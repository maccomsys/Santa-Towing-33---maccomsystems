<?php
/**
 * Zero-Dependency Pure-PHP A4 PDF 1.4 Generator for Pro Forma Invoices & Official Receipts.
 *
 * Designed specifically for cPanel shared hosting without requiring Composer,
 * Dompdf, TCPDF, or headless Chrome/Node.js. Uses standard built-in Helvetica /
 * Helvetica-Bold Type1 PDF fonts with proportional glyph width metrics and crisp
 * vector primitives matching the React DocumentPreviewModal layout (#015557 primary strip).
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_PDF {

	/** @var float A4 Width in points (210mm). */
	const PAGE_W = 595.28;

	/** @var float A4 Height in points (297mm). */
	const PAGE_H = 841.89;

	/** @var array<int,string> Stream pages. */
	private $pages = array();

	/** @var string Active page drawing commands. */
	private $stream = '';

	/** @var float Current top-down Y position (in mm from top). */
	private $y_mm = 15.0;

	/** @var float[] Primary RGB normalized 0..1 */
	private $primary_rgb = array( 0.004, 0.333, 0.341 ); // #015557

	/**
	 * Convert millimeter X coordinate to PDF point X.
	 *
	 * @param float $mm Millimeters from left edge.
	 * @return float
	 */
	private function x( $mm ) {
		return round( $mm * 2.83464567, 2 );
	}

	/**
	 * Convert top-down millimeter Y coordinate to bottom-up PDF point Y.
	 *
	 * @param float $mm Millimeters from top edge.
	 * @return float
	 */
	private function y( $mm ) {
		return round( self::PAGE_H - ( $mm * 2.83464567 ), 2 );
	}

	/**
	 * Convert mm distance to points.
	 *
	 * @param float $mm Distance in mm.
	 * @return float
	 */
	private function pt( $mm ) {
		return round( $mm * 2.83464567, 2 );
	}

	/**
	 * Set primary hex color.
	 *
	 * @param string $hex Hex color string.
	 */
	private function set_primary_hex( $hex ) {
		$clean = ltrim( trim( (string) $hex ), '#' );
		if ( 3 === strlen( $clean ) ) {
			$clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
		}
		if ( 6 === strlen( $clean ) && ctype_xdigit( $clean ) ) {
			$this->primary_rgb = array(
				round( hexdec( substr( $clean, 0, 2 ) ) / 255, 3 ),
				round( hexdec( substr( $clean, 2, 2 ) ) / 255, 3 ),
				round( hexdec( substr( $clean, 4, 2 ) ) / 255, 3 ),
			);
		}
	}

	/**
	 * Transliterate / normalize UTF-8 string (including GH₵) to WinAnsi-safe ASCII text.
	 *
	 * @param string $text Raw string.
	 * @return string
	 */
	private function clean_text( $text ) {
		$str          = (string) $text;
		$replacements = array(
			'GH₵' => 'GHS',
			'₵'   => 'GHS ',
			'•'   => '-',
			'–'   => '-',
			'—'   => '-',
			'’'   => "'",
			'‘'   => "'",
			'“'   => '"',
			'”'   => '"',
			'…'   => '...',
			"\r"  => '',
		);
		$str = strtr( $str, $replacements );
		if ( function_exists( 'iconv' ) ) {
			$converted = @iconv( 'UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $str );
			if ( false !== $converted ) {
				$str = $converted;
			}
		}
		return preg_replace( '/[^\x20-\x7E\n]/', '', $str );
	}

	/**
	 * Measure rendered text width in mm using standard Helvetica / Helvetica-Bold glyph widths.
	 *
	 * @param string $clean Clean ASCII string.
	 * @param int    $size  Font size pt.
	 * @param bool   $bold  Bold variant.
	 * @return float
	 */
	private function measure_text_mm( $clean, $size, $bold = false ) {
		// Helvetica Type1 metric widths (1/1000 em) grouped by narrow/medium/wide glyphs.
		static $narrow = array( ' ' => 278, '!' => 278, "'" => 191, ',' => 278, '.' => 278, ':' => 278, ';' => 278, 'i' => 222, 'j' => 222, 'l' => 222, 'I' => 278, 't' => 278, 'r' => 333, 'f' => 278, '(' => 333, ')' => 333, '[' => 278, ']' => 278, '/' => 278, '-' => 333 );
		static $wide   = array( 'm' => 833, 'w' => 722, 'M' => 833, 'W' => 944, 'G' => 778, 'O' => 778, 'Q' => 778, 'C' => 722, 'D' => 722, 'H' => 722, 'N' => 722, 'R' => 722, 'U' => 722, 'A' => 667, 'B' => 667, 'K' => 667, 'S' => 667, 'X' => 667, 'Y' => 667, 'V' => 667, '%' => 889, '@' => 1015, '&' => 667 );
		$len           = strlen( $clean );
		$units         = 0;
		for ( $i = 0; $i < $len; $i++ ) {
			$ch = $clean[ $i ];
			if ( isset( $narrow[ $ch ] ) ) {
				$w = $narrow[ $ch ];
			} elseif ( isset( $wide[ $ch ] ) ) {
				$w = $wide[ $ch ];
			} elseif ( $ch >= 'A' && $ch <= 'Z' ) {
				$w = 667;
			} elseif ( $ch >= '0' && $ch <= '9' ) {
				$w = 556;
			} else {
				$w = 520;
			}
			$units += $bold ? (int) round( $w * 1.05 ) : $w;
		}
		$pts = ( $units / 1000.0 ) * (float) $size;
		return $pts / 2.83464567;
	}

	/**
	 * Escape text for PDF string literal.
	 *
	 * @param string $text Clean ASCII string.
	 * @return string
	 */
	private function pdf_str( $text ) {
		$s = $this->clean_text( $text );
		$s = str_replace( array( '\\', '(', ')' ), array( '\\\\', '\\(', '\\)' ), $s );
		return '(' . $s . ')';
	}

	/**
	 * Start a new A4 page.
	 */
	private function new_page() {
		if ( '' !== $this->stream ) {
			$this->pages[] = $this->stream;
		}
		$this->stream = '';
		$this->y_mm   = 18.0;
	}

	/**
	 * Check if vertical space remains, otherwise insert a page break.
	 *
	 * @param float $needed_mm Millimeters required.
	 */
	private function ensure_space( $needed_mm ) {
		if ( ( $this->y_mm + $needed_mm ) > 274.0 ) {
			$this->new_page();
		}
	}

	/**
	 * Draw filled rectangle in top-down mm coordinates.
	 */
	private function rect_fill( $x_mm, $y_mm, $w_mm, $h_mm, $rgb ) {
		$rx = $this->x( $x_mm );
		$ry = $this->y( $y_mm + $h_mm );
		$rw = $this->pt( $w_mm );
		$rh = $this->pt( $h_mm );
		$this->stream .= sprintf( "%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f\n", $rgb[0], $rgb[1], $rgb[2], $rx, $ry, $rw, $rh );
	}

	/**
	 * Draw stroked line in top-down mm coordinates.
	 */
	private function line( $x1_mm, $y1_mm, $x2_mm, $y2_mm, $rgb = array( 0.88, 0.90, 0.92 ), $width = 0.6 ) {
		$this->stream .= sprintf(
			"%.2F w %.3F %.3F %.3F RG %.2F %.2F m %.2F %.2F l S\n",
			$width,
			$rgb[0],
			$rgb[1],
			$rgb[2],
			$this->x( $x1_mm ),
			$this->y( $y1_mm ),
			$this->x( $x2_mm ),
			$this->y( $y2_mm )
		);
	}

	/**
	 * Draw single line of text.
	 *
	 * @param float  $x_mm X in mm.
	 * @param float  $y_mm Baseline Y in mm from top.
	 * @param string $text Text string.
	 * @param float  $size Font size pt.
	 * @param bool   $bold Use F2 (bold) instead of F1.
	 * @param array  $rgb  RGB 0..1 array.
	 * @param string $align 'left'|'right'|'center'.
	 */
	private function text( $x_mm, $y_mm, $text, $size = 9, $bold = false, $rgb = array( 0.10, 0.14, 0.18 ), $align = 'left' ) {
		$clean = $this->clean_text( $text );
		$font  = $bold ? 'F2' : 'F1';
		$tx    = $x_mm;
		if ( 'left' !== $align ) {
			$approx_w_mm = $this->measure_text_mm( $clean, $size, $bold );
			if ( 'right' === $align ) {
				$tx = $x_mm - $approx_w_mm;
			} elseif ( 'center' === $align ) {
				$tx = $x_mm - ( $approx_w_mm / 2 );
			}
		}
		$this->stream .= sprintf(
			"BT /%s %.1F Tf %.3F %.3F %.3F rg %.2F %.2F Td %s Tj ET\n",
			$font,
			(float) $size,
			$rgb[0],
			$rgb[1],
			$rgb[2],
			$this->x( $tx ),
			$this->y( $y_mm ),
			$this->pdf_str( $clean )
		);
	}

	/**
	 * Word-wrap text into lines fitting a given mm width.
	 *
	 * @param string $text      Input text.
	 * @param float  $max_w_mm  Max width in mm.
	 * @param float  $font_size Font size pt.
	 * @param bool   $bold      Bold variant.
	 * @return string[]
	 */
	private function wrap_lines( $text, $max_w_mm, $font_size = 9, $bold = false ) {
		$clean      = $this->clean_text( $text );
		$paragraphs = explode( "\n", $clean );
		$lines      = array();
		foreach ( $paragraphs as $p ) {
			$p = trim( $p );
			if ( '' === $p ) {
				$lines[] = '';
				continue;
			}
			$words   = preg_split( '/\s+/', $p );
			$cur     = '';
			foreach ( $words as $word ) {
				$candidate = '' === $cur ? $word : $cur . ' ' . $word;
				if ( $this->measure_text_mm( $candidate, $font_size, $bold ) <= $max_w_mm ) {
					$cur = $candidate;
				} else {
					if ( '' !== $cur ) {
						$lines[] = $cur;
					}
					$cur = $word;
				}
			}
			if ( '' !== $cur ) {
				$lines[] = $cur;
			}
		}
		return $lines;
	}

	/**
	 * Format currency string safely for PDF.
	 *
	 * @param float  $amount   Amount.
	 * @param string $currency Currency prefix.
	 * @return string
	 */
	private function money( $amount, $currency = 'GHS' ) {
		$cur = $this->clean_text( $currency );
		if ( '' === trim( $cur ) ) {
			$cur = 'GHS';
		}
		return trim( $cur ) . ' ' . number_format( (float) $amount, 2 );
	}

	/**
	 * Normalize hiddenFields array supporting both React & PHP short aliases.
	 */
	private function is_field_visible( $hidden_fields, $keys ) {
		foreach ( (array) $keys as $k ) {
			if ( in_array( $k, $hidden_fields, true ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Generate PDF binary bytes from pages.
	 *
	 * @return string
	 */
	private function output_bytes() {
		if ( '' !== $this->stream ) {
			$this->pages[] = $this->stream;
			$this->stream  = '';
		}
		if ( empty( $this->pages ) ) {
			$this->pages[] = '';
		}

		$objects    = array();
		$page_count = count( $this->pages );

		// Obj 1: Catalog, Obj 2: Pages, Obj 3: F1 Helvetica, Obj 4: F2 Helvetica-Bold.
		$objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
		$objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

		$kids   = array();
		$obj_id = 5;
		foreach ( $this->pages as $page_stream ) {
			$page_obj_id    = $obj_id++;
			$content_obj_id = $obj_id++;
			$kids[]         = $page_obj_id . ' 0 R';

			$objects[ $page_obj_id ] = sprintf(
				'<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
				self::PAGE_W,
				self::PAGE_H,
				$content_obj_id
			);
			$len                      = strlen( $page_stream );
			$objects[ $content_obj_id ] = "<< /Length {$len} >>\nstream\n{$page_stream}\nendstream";
		}

		$objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
		$objects[2] = '<< /Type /Pages /Kids [' . implode( ' ', $kids ) . '] /Count ' . $page_count . ' >>';
		ksort( $objects );

		$pdf     = "%PDF-1.4\n";
		$offsets = array( 0 => 0 );
		foreach ( $objects as $id => $body ) {
			$offsets[ $id ] = strlen( $pdf );
			$pdf           .= "{$id} 0 obj\n{$body}\nendobj\n";
		}

		$xref_pos = strlen( $pdf );
		$total    = count( $objects ) + 1;
		$pdf     .= "xref\n0 {$total}\n";
		$pdf     .= "0000000000 65535 f \n";
		for ( $i = 1; $i < $total; $i++ ) {
			$pdf .= sprintf( "%010d 00000 n \n", $offsets[ $i ] );
		}
		$pdf .= "trailer\n<< /Size {$total} /Root 1 0 R >>\nstartxref\n{$xref_pos}\n%%EOF";

		return $pdf;
	}

	/**
	 * Render Pro Forma Invoice to raw PDF bytes.
	 *
	 * @param array $inv      Invoice record.
	 * @param array $settings Document settings.
	 * @return string
	 */
	public function render_invoice( $inv, $settings ) {
		$this->pages  = array();
		$this->stream = '';
		$this->set_primary_hex( $settings['primaryColor'] ?? '#015557' );
		$dark_rgb  = array( 0.08, 0.11, 0.15 );
		$muted_rgb = array( 0.38, 0.44, 0.52 );
		$light_bg  = array( 0.965, 0.975, 0.985 );
		$white_rgb = array( 1, 1, 1 );
		$currency  = $settings['currency'] ?? 'GHS';
		$hidden    = is_array( $inv['hiddenFields'] ?? null ) ? $inv['hiddenFields'] : array();
		$titles    = is_array( $inv['sectionTitles'] ?? null ) ? $inv['sectionTitles'] : array();

		$this->new_page();

		// Top Brand Strip.
		$this->rect_fill( 0, 0, 210, 4.5, $this->primary_rgb );

		// Company Letterhead (Left).
		$phones_line = 'Tel: ' . ( $settings['phone'] ?? '' );
		if ( ! empty( $settings['phoneSecondary'] ) ) {
			$phones_line .= ' / ' . $settings['phoneSecondary'];
		}
		$phones_line .= '  |  Email: ' . ( $settings['email'] ?? '' );

		$this->text( 15, 16, strtoupper( $settings['companyName'] ?? 'Santa Towing & Garage Services Ltd.' ), 13, true, $this->primary_rgb );
		$this->text( 15, 21, $settings['tagline'] ?? '', 8, false, $muted_rgb );
		$this->text( 15, 26.5, $settings['address'] ?? '', 8, false, $dark_rgb );
		$this->text( 15, 31, $phones_line, 8, false, $dark_rgb );
		$this->text( 15, 35.5, ( $settings['taxRegistrationNumber'] ?? '' ) . '  |  ' . ( $settings['website'] ?? '' ), 8, false, $muted_rgb );

		// Document Title Badge (Right).
		$doc_title = ! empty( $inv['docTitle'] ) ? $inv['docTitle'] : 'PRO FORMA INVOICE';
		$this->text( 195, 16.5, strtoupper( $doc_title ), 16, true, $this->primary_rgb, 'right' );
		$this->text( 195, 23, 'Document No: ' . ( $inv['docNumber'] ?? '' ), 9, true, $dark_rgb, 'right' );
		$this->text( 195, 28, 'Issue Date: ' . ( $inv['issueDate'] ?? '' ), 8.5, false, $muted_rgb, 'right' );
		$this->text( 195, 32.5, 'Valid Until: ' . ( $inv['validUntil'] ?? '' ), 8.5, false, $muted_rgb, 'right' );
		$this->text( 195, 37, 'Status: ' . strtoupper( $inv['status'] ?? 'DRAFT' ), 8.5, true, $this->primary_rgb, 'right' );

		$this->line( 15, 42, 195, 42, $this->primary_rgb, 1.1 );
		$this->y_mm = 46.0;

		// Normalize sectionOrder supporting both React ('customer','calculations','payment') and legacy ('billTo','totals','instructions') keys.
		$raw_order = ( ! empty( $inv['sectionOrder'] ) && is_array( $inv['sectionOrder'] ) )
			? $inv['sectionOrder']
			: array( 'billTo', 'items', 'totals', 'instructions', 'terms', 'notes' );

		$rendered = array();
		foreach ( $raw_order as $sec_key ) {
			$canonical = $sec_key;
			if ( 'customer' === $sec_key ) {
				$canonical = 'billTo';
			} elseif ( 'calculations' === $sec_key ) {
				$canonical = 'totals';
			} elseif ( 'payment' === $sec_key ) {
				$canonical = 'instructions';
			}
			if ( isset( $rendered[ $canonical ] ) ) {
				continue;
			}
			$rendered[ $canonical ] = true;

			if ( 'billTo' === $canonical ) {
				if ( ! $this->is_field_visible( $hidden, array( 'billTo', 'customer' ) ) ) {
					continue;
				}
				$this->ensure_space( 40 );
				$box_y = $this->y_mm;
				$this->rect_fill( 15, $box_y, 108, 32, $light_bg );
				$bill_heading = strtoupper( $titles['billTo'] ?? $titles['customer'] ?? 'BILL TO / CLIENT DETAILS' );
				$this->text( 19, $box_y + 6, $bill_heading, 8, true, $this->primary_rgb );
				$this->text( 19, $box_y + 12, $inv['customerName'] ?? '', 10.5, true, $dark_rgb );
				$cur_y = $box_y + 17;
				if ( ! empty( $inv['customerCompany'] ) && $this->is_field_visible( $hidden, array( 'company', 'customerCompany' ) ) ) {
					$this->text( 19, $cur_y, $inv['customerCompany'], 8.5, false, $dark_rgb );
					$cur_y += 4.5;
				}
				$show_email = ! empty( $inv['customerEmail'] ) && $this->is_field_visible( $hidden, array( 'email', 'customerEmail' ) );
				if ( ! empty( $inv['customerPhone'] ) || $show_email ) {
					$contact_str = ! empty( $inv['customerPhone'] ) ? 'Phone: ' . $inv['customerPhone'] : '';
					if ( $show_email ) {
						$contact_str .= ( '' !== $contact_str ? '  |  ' : 'Email: ' ) . $inv['customerEmail'];
					}
					$this->text( 19, $cur_y, $contact_str, 8, false, $muted_rgb );
					$cur_y += 4.5;
				}
				if ( ! empty( $inv['customerAddress'] ) && $this->is_field_visible( $hidden, array( 'address', 'customerAddress' ) ) ) {
					$this->text( 19, $cur_y, $inv['customerAddress'], 8, false, $muted_rgb );
				}

				$this->rect_fill( 128, $box_y, 67, 32, $this->primary_rgb );
				$this->text( 133, $box_y + 7, 'ESTIMATED TOTAL DUE', 8, true, $white_rgb );
				$this->text( 133, $box_y + 16.5, $this->money( $inv['grandTotal'] ?? 0, $currency ), 15, true, $white_rgb );
				$this->text( 133, $box_y + 23.5, 'Paid: ' . $this->money( $inv['amountPaid'] ?? 0, $currency ), 8, false, $white_rgb );
				$this->text( 133, $box_y + 28, 'Balance Due: ' . $this->money( $inv['balanceDue'] ?? 0, $currency ), 8, true, $white_rgb );

				$this->y_mm = $box_y + 40.0;
			} elseif ( 'items' === $canonical ) {
				if ( ! $this->is_field_visible( $hidden, array( 'items' ) ) ) {
					continue;
				}
				$this->ensure_space( 22 );
				$this->rect_fill( 15, $this->y_mm, 180, 8, $this->primary_rgb );
				$this->text( 18, $this->y_mm + 5.3, '#', 8.5, true, $white_rgb );
				$this->text( 26, $this->y_mm + 5.3, strtoupper( $titles['items'] ?? 'SERVICE / ITEM DESCRIPTION' ), 8.5, true, $white_rgb );
				$this->text( 134, $this->y_mm + 5.3, 'QTY', 8.5, true, $white_rgb, 'right' );
				$this->text( 163, $this->y_mm + 5.3, 'UNIT PRICE', 8.5, true, $white_rgb, 'right' );
				$this->text( 192, $this->y_mm + 5.3, 'TOTAL', 8.5, true, $white_rgb, 'right' );
				$this->y_mm += 10.0;

				$items = is_array( $inv['items'] ?? null ) ? $inv['items'] : array();
				foreach ( $items as $idx => $item ) {
					$desc_lines = $this->wrap_lines( $item['description'] ?? '', 94, 8.5 );
					$row_h      = max( 8.0, ( count( $desc_lines ) * 4.2 ) + 3.0 );
					$this->ensure_space( $row_h + 4 );

					if ( 1 === ( $idx % 2 ) ) {
						$this->rect_fill( 15, $this->y_mm - 2, 180, $row_h, $light_bg );
					}
					$this->text( 18, $this->y_mm + 2.5, (string) ( $idx + 1 ), 8.5, false, $muted_rgb );
					foreach ( $desc_lines as $l_idx => $dl ) {
						$this->text( 26, $this->y_mm + 2.5 + ( $l_idx * 4.2 ), $dl, 8.5, false, $dark_rgb );
					}
					$this->text( 134, $this->y_mm + 2.5, number_format( (float) ( $item['quantity'] ?? 1 ), 2 ), 8.5, false, $dark_rgb, 'right' );
					$this->text( 163, $this->y_mm + 2.5, $this->money( $item['unitPrice'] ?? 0, $currency ), 8.5, false, $dark_rgb, 'right' );
					$this->text( 192, $this->y_mm + 2.5, $this->money( $item['total'] ?? 0, $currency ), 8.5, true, $dark_rgb, 'right' );

					$this->y_mm += $row_h;
					$this->line( 15, $this->y_mm - 1.5, 195, $this->y_mm - 1.5, array( 0.90, 0.92, 0.94 ), 0.4 );
				}
				$this->y_mm += 2.0;
			} elseif ( 'totals' === $canonical ) {
				if ( ! $this->is_field_visible( $hidden, array( 'totals', 'calculations' ) ) ) {
					continue;
				}
				$this->ensure_space( 48 );
				$this->y_mm += 2.0;
				$sum_x_lbl   = 124;
				$sum_x_val   = 193;

				$this->text( $sum_x_lbl, $this->y_mm, 'Subtotal:', 9, false, $muted_rgb );
				$this->text( $sum_x_val, $this->y_mm, $this->money( $inv['subtotal'] ?? 0, $currency ), 9, true, $dark_rgb, 'right' );
				$this->y_mm += 5.5;

				if ( ! empty( $inv['discountAmount'] ) && (float) $inv['discountAmount'] > 0 ) {
					$disc_lbl = ! empty( $inv['discountLabel'] ) ? $inv['discountLabel'] : 'Discount';
					$this->text( $sum_x_lbl, $this->y_mm, $disc_lbl . ':', 9, false, $muted_rgb );
					$this->text( $sum_x_val, $this->y_mm, '-' . $this->money( $inv['discountAmount'], $currency ), 9, false, array( 0.80, 0.15, 0.15 ), 'right' );
					$this->y_mm += 5.5;
				}

				if ( ! empty( $inv['taxAmount'] ) && (float) $inv['taxAmount'] > 0 ) {
					$tax_lbl = ! empty( $inv['taxLabel'] ) ? $inv['taxLabel'] : 'Tax / VAT';
					$this->text( $sum_x_lbl, $this->y_mm, $tax_lbl . ' (' . (float) ( $inv['taxRate'] ?? 0 ) . '%):', 9, false, $muted_rgb );
					$this->text( $sum_x_val, $this->y_mm, $this->money( $inv['taxAmount'], $currency ), 9, false, $dark_rgb, 'right' );
					$this->y_mm += 5.5;
				}

				if ( ! empty( $inv['shippingEnabled'] ) && (float) ( $inv['shippingAmount'] ?? 0 ) > 0 ) {
					$ship_lbl = ! empty( $inv['shippingLabel'] ) ? $inv['shippingLabel'] : 'Logistics / Dispatch Fee';
					$this->text( $sum_x_lbl, $this->y_mm, $ship_lbl . ':', 9, false, $muted_rgb );
					$this->text( $sum_x_val, $this->y_mm, $this->money( $inv['shippingAmount'], $currency ), 9, false, $dark_rgb, 'right' );
					$this->y_mm += 5.5;
				}

				if ( ! empty( $inv['additionalCharges'] ) && is_array( $inv['additionalCharges'] ) ) {
					foreach ( $inv['additionalCharges'] as $ch ) {
						if ( ( $ch['enabled'] ?? true ) && (float) ( $ch['amount'] ?? 0 ) > 0 ) {
							$this->text( $sum_x_lbl, $this->y_mm, ( $ch['label'] ?? 'Fee' ) . ':', 9, false, $muted_rgb );
							$this->text( $sum_x_val, $this->y_mm, $this->money( $ch['amount'], $currency ), 9, false, $dark_rgb, 'right' );
							$this->y_mm += 5.5;
						}
					}
				}

				$this->rect_fill( 121, $this->y_mm - 1, 74, 8.5, $this->primary_rgb );
				$this->text( 124, $this->y_mm + 4.8, 'GRAND TOTAL:', 9.5, true, $white_rgb );
				$this->text( 192, $this->y_mm + 4.8, $this->money( $inv['grandTotal'] ?? 0, $currency ), 10.5, true, $white_rgb, 'right' );
				$this->y_mm += 12.5;

				$amt_paid = (float) ( $inv['amountPaid'] ?? 0 );
				if ( $amt_paid > 0 ) {
					$this->text( $sum_x_lbl, $this->y_mm, 'Amount Paid:', 9, false, $muted_rgb );
					$this->text( $sum_x_val, $this->y_mm, '-' . $this->money( $amt_paid, $currency ), 9, true, array( 0.02, 0.45, 0.30 ), 'right' );
					$this->y_mm += 5.0;
					$this->text( $sum_x_lbl, $this->y_mm, 'Balance Due:', 9.5, true, $dark_rgb );
					$this->text( $sum_x_val, $this->y_mm, $this->money( $inv['balanceDue'] ?? 0, $currency ), 9.5, true, $this->primary_rgb, 'right' );
					$this->y_mm += 7.0;
				}
			} elseif ( 'instructions' === $canonical ) {
				$body = (string) ( $inv['paymentInstructions'] ?? '' );
				if ( '' !== trim( $body ) && $this->is_field_visible( $hidden, array( 'instructions', 'paymentSection', 'paymentInstructions' ) ) ) {
					$heading = strtoupper( $titles['payment'] ?? $titles['instructions'] ?? 'PAYMENT & REMITTANCE INSTRUCTIONS' );
					$lines   = $this->wrap_lines( $body, 175, 8 );
					$this->ensure_space( 10 + ( count( $lines ) * 4.0 ) );
					$this->text( 15, $this->y_mm, $heading, 8.5, true, $this->primary_rgb );
					$this->y_mm += 4.5;
					foreach ( $lines as $line ) {
						$this->text( 15, $this->y_mm, $line, 8, false, $muted_rgb );
						$this->y_mm += 4.0;
					}
					$this->y_mm += 3.0;
				}
			} elseif ( 'terms' === $canonical ) {
				$body = (string) ( $inv['termsAndConditions'] ?? '' );
				if ( '' !== trim( $body ) && $this->is_field_visible( $hidden, array( 'terms', 'termsSection', 'termsAndConditions' ) ) ) {
					$heading = strtoupper( $titles['terms'] ?? 'TERMS & CONDITIONS' );
					$lines   = $this->wrap_lines( $body, 175, 8 );
					$this->ensure_space( 10 + ( count( $lines ) * 4.0 ) );
					$this->text( 15, $this->y_mm, $heading, 8.5, true, $this->primary_rgb );
					$this->y_mm += 4.5;
					foreach ( $lines as $line ) {
						$this->text( 15, $this->y_mm, $line, 8, false, $muted_rgb );
						$this->y_mm += 4.0;
					}
					$this->y_mm += 3.0;
				}
			} elseif ( 'notes' === $canonical ) {
				$body = (string) ( $inv['notes'] ?? '' );
				if ( '' !== trim( $body ) && $this->is_field_visible( $hidden, array( 'notes', 'notesSection' ) ) ) {
					$heading = strtoupper( $titles['notes'] ?? 'NOTES' );
					$lines   = $this->wrap_lines( $body, 175, 8 );
					$this->ensure_space( 10 + ( count( $lines ) * 4.0 ) );
					$this->text( 15, $this->y_mm, $heading, 8.5, true, $this->primary_rgb );
					$this->y_mm += 4.5;
					foreach ( $lines as $line ) {
						$this->text( 15, $this->y_mm, $line, 8, false, $muted_rgb );
						$this->y_mm += 4.0;
					}
					$this->y_mm += 3.0;
				}
			}
		}

		// Custom Fields (if present).
		if ( ! empty( $inv['customFields'] ) && is_array( $inv['customFields'] ) ) {
			$visible_fields = array_filter(
				$inv['customFields'],
				function ( $f ) {
					return ( $f['visible'] ?? true ) && '' !== trim( (string) ( $f['value'] ?? '' ) );
				}
			);
			if ( ! empty( $visible_fields ) ) {
				$this->ensure_space( 20 );
				$this->text( 15, $this->y_mm, 'ADDITIONAL DOCUMENT DETAILS', 8.5, true, $this->primary_rgb );
				$this->y_mm += 5;
				foreach ( $visible_fields as $cf ) {
					$this->ensure_space( 6 );
					$this->text( 15, $this->y_mm, ( $cf['label'] ?? '' ) . ': ' . ( $cf['value'] ?? '' ), 8.5, false, $dark_rgb );
					$this->y_mm += 4.5;
				}
				$this->y_mm += 3;
			}
		}

		// Custom Text Sections (if present).
		if ( ! empty( $inv['customSections'] ) && is_array( $inv['customSections'] ) ) {
			foreach ( $inv['customSections'] as $cs ) {
				if ( false === ( $cs['visible'] ?? true ) || '' === trim( (string) ( $cs['content'] ?? '' ) ) ) {
					continue;
				}
				$lines = $this->wrap_lines( $cs['content'], 175, 8 );
				$this->ensure_space( 12 + ( count( $lines ) * 4.0 ) );
				$this->text( 15, $this->y_mm, strtoupper( $cs['title'] ?? 'ADDITIONAL SECTION' ), 8.5, true, $this->primary_rgb );
				$this->y_mm += 4.5;
				foreach ( $lines as $line ) {
					$this->text( 15, $this->y_mm, $line, 8, false, $muted_rgb );
					$this->y_mm += 4.0;
				}
				$this->y_mm += 3.0;
			}
		}

		// Signature & Footer.
		$this->ensure_space( 26 );
		$sig_y = max( $this->y_mm + 10, 255.0 );
		$this->line( 15, $sig_y, 75, $sig_y, $muted_rgb, 0.6 );
		$this->text( 15, $sig_y + 4.5, 'Authorized Dispatch / Management Signature', 8, false, $muted_rgb );
		$this->line( 130, $sig_y, 195, $sig_y, $muted_rgb, 0.6 );
		$this->text( 130, $sig_y + 4.5, 'Client Acceptance & Date', 8, false, $muted_rgb );

		$this->line( 15, 281, 195, 281, array( 0.85, 0.88, 0.90 ), 0.5 );
		$this->text( 105, 286, $settings['footerText'] ?? '', 7.5, false, $muted_rgb, 'center' );

		return $this->output_bytes();
	}

	/**
	 * Render Official Receipt to raw PDF bytes.
	 *
	 * @param array $rec      Receipt record.
	 * @param array $settings Document settings.
	 * @return string
	 */
	public function render_receipt( $rec, $settings ) {
		$this->pages  = array();
		$this->stream = '';
		$this->set_primary_hex( $settings['primaryColor'] ?? '#015557' );
		$dark_rgb  = array( 0.08, 0.11, 0.15 );
		$muted_rgb = array( 0.38, 0.44, 0.52 );
		$light_bg  = array( 0.965, 0.975, 0.985 );
		$white_rgb = array( 1, 1, 1 );
		$currency  = $settings['currency'] ?? 'GHS';

		$this->new_page();
		$this->rect_fill( 0, 0, 210, 4.5, $this->primary_rgb );

		// Letterhead.
		$phones_line = 'Tel: ' . ( $settings['phone'] ?? '' );
		if ( ! empty( $settings['phoneSecondary'] ) ) {
			$phones_line .= ' / ' . $settings['phoneSecondary'];
		}
		$phones_line .= '  |  Email: ' . ( $settings['email'] ?? '' );

		$this->text( 15, 16, strtoupper( $settings['companyName'] ?? 'Santa Towing & Garage Services Ltd.' ), 13, true, $this->primary_rgb );
		$this->text( 15, 21, $settings['tagline'] ?? '', 8, false, $muted_rgb );
		$this->text( 15, 26.5, $settings['address'] ?? '', 8, false, $dark_rgb );
		$this->text( 15, 31, $phones_line, 8, false, $dark_rgb );
		$this->text( 15, 35.5, ( $settings['taxRegistrationNumber'] ?? '' ) . '  |  ' . ( $settings['website'] ?? '' ), 8, false, $muted_rgb );

		$this->text( 195, 16.5, 'OFFICIAL PAYMENT RECEIPT', 16, true, $this->primary_rgb, 'right' );
		$this->text( 195, 23, 'Receipt No: ' . ( $rec['receiptNumber'] ?? '' ), 9.5, true, $dark_rgb, 'right' );
		$this->text( 195, 28, 'Receipt Date: ' . ( $rec['receiptDate'] ?? '' ), 8.5, false, $muted_rgb, 'right' );
		$this->text( 195, 33, 'Status: ' . strtoupper( $rec['status'] ?? 'ISSUED' ), 8.5, true, $this->primary_rgb, 'right' );

		$this->line( 15, 42, 195, 42, $this->primary_rgb, 1.1 );

		// Received From & Amount Box.
		$this->rect_fill( 15, 48, 108, 34, $light_bg );
		$this->text( 19, 54, 'RECEIVED WITH THANKS FROM', 8, true, $this->primary_rgb );
		$this->text( 19, 61, $rec['customerName'] ?? '', 11, true, $dark_rgb );
		$this->text( 19, 67, 'Phone: ' . ( $rec['customerPhone'] ?? '' ), 8.5, false, $muted_rgb );
		if ( ! empty( $rec['customerEmail'] ) ) {
			$this->text( 19, 72, 'Email: ' . $rec['customerEmail'], 8, false, $muted_rgb );
		}
		if ( ! empty( $rec['customerAddress'] ) ) {
			$this->text( 19, 77, $rec['customerAddress'], 8, false, $muted_rgb );
		}

		$this->rect_fill( 128, 48, 67, 34, $this->primary_rgb );
		$this->text( 133, 55, 'AMOUNT RECEIVED', 8, true, $white_rgb );
		$this->text( 133, 66, $this->money( $rec['amountReceived'] ?? 0, $currency ), 16, true, $white_rgb );
		$this->text( 133, 74, 'Method: ' . ( $rec['paymentMethod'] ?? '' ), 8.5, false, $white_rgb );

		// Transaction Details Table.
		$y = 92;
		$this->rect_fill( 15, $y, 180, 8, $this->primary_rgb );
		$this->text( 19, $y + 5.3, 'PAYMENT & TRANSACTION BREAKDOWN', 9, true, $white_rgb );
		$y += 12;

		$rows = array(
			array( 'Payment Method', $rec['paymentMethod'] ?? '-' ),
			array( 'Payment Reference / Transaction ID', $rec['paymentReference'] ?? '-' ),
			array( 'Linked Pro Forma Invoice', ! empty( $rec['invoiceNumber'] ) ? $rec['invoiceNumber'] : 'Direct Payment / Unlinked' ),
			array( 'Amount Paid', $this->money( $rec['amountReceived'] ?? 0, $currency ) ),
			array( 'Remaining Invoice Balance', isset( $rec['remainingBalance'] ) && null !== $rec['remainingBalance'] ? $this->money( $rec['remainingBalance'], $currency ) : $this->money( 0, $currency ) ),
		);

		foreach ( $rows as $i => $row ) {
			if ( 1 === ( $i % 2 ) ) {
				$this->rect_fill( 15, $y - 3, 180, 9, $light_bg );
			}
			$this->text( 19, $y + 3, $row[0], 9, true, $muted_rgb );
			$this->text( 191, $y + 3, $row[1], 9.5, true, $dark_rgb, 'right' );
			$this->line( 15, $y + 6, 195, $y + 6, array( 0.90, 0.92, 0.94 ), 0.4 );
			$y += 10;
		}

		if ( ! empty( $rec['notes'] ) ) {
			$y += 6;
			$this->text( 15, $y, 'OFFICIAL REMARKS / NOTES', 8.5, true, $this->primary_rgb );
			$y += 5;
			foreach ( $this->wrap_lines( $rec['notes'], 175, 8.5 ) as $line ) {
				$this->text( 15, $y, $line, 8.5, false, $muted_rgb );
				$y += 4.5;
			}
		}

		// Stamp & Signature.
		$sig_y = max( $y + 22, 235.0 );
		$this->line( 15, $sig_y, 75, $sig_y, $muted_rgb, 0.6 );
		$this->text( 15, $sig_y + 4.5, 'Authorized Cashier / Accounts Officer', 8, false, $muted_rgb );
		$this->line( 130, $sig_y, 195, $sig_y, $muted_rgb, 0.6 );
		$this->text( 130, $sig_y + 4.5, 'Official Company Stamp & Date', 8, false, $muted_rgb );

		$this->line( 15, 281, 195, 281, array( 0.85, 0.88, 0.90 ), 0.5 );
		$this->text( 105, 286, $settings['footerText'] ?? '', 7.5, false, $muted_rgb, 'center' );

		return $this->output_bytes();
	}
}
