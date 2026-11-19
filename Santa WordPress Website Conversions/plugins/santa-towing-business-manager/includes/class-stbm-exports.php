<?php
/**
 * Protected File Downloads: CSV Exports, Full JSON Backups, and Native A4 PDF Generation.
 *
 * Every endpoint uses `admin-post.php` and validates both nonce (`_wpnonce`) and granular WP capability.
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_Exports {

	/**
	 * Register admin-post hooks.
	 */
	public static function init() {
		add_action( 'admin_post_stbm_download_pdf', array( __CLASS__, 'handle_pdf' ) );
		add_action( 'admin_post_stbm_export_csv', array( __CLASS__, 'handle_csv' ) );
		add_action( 'admin_post_stbm_export_json', array( __CLASS__, 'handle_json' ) );
	}

	/**
	 * Download A4 Pro Forma Invoice or Official Receipt PDF.
	 */
	public static function handle_pdf() {
		STBM_Roles::verify_admin_post( STBM_Roles::CAP_BILLING, 'stbm_export_nonce' );

		$type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'invoice';
		$uid  = isset( $_GET['uid'] ) ? sanitize_text_field( wp_unslash( $_GET['uid'] ) ) : '';
		if ( '' === $uid ) {
			wp_die( esc_html__( 'Missing document ID.', 'santa-towing-business-manager' ), 400 );
		}

		$settings = STBM_Defaults::get_doc_settings();
		$pdf_gen  = new STBM_PDF();

		if ( 'receipt' === $type ) {
			$rec = STBM_DB::get_receipt( $uid );
			if ( ! $rec ) {
				wp_die( esc_html__( 'Receipt not found.', 'santa-towing-business-manager' ), 404 );
			}
			$bytes    = $pdf_gen->render_receipt( $rec, $settings );
			$filename = sanitize_file_name( $rec['receiptNumber'] . '-' . $rec['customerName'] ) . '.pdf';
			STBM_DB::log_activity( 'export', 'receipt', $rec['receiptNumber'], 'Downloaded Official Receipt PDF' );
		} else {
			$inv = STBM_DB::get_invoice( $uid );
			if ( ! $inv ) {
				wp_die( esc_html__( 'Invoice not found.', 'santa-towing-business-manager' ), 404 );
			}
			$bytes    = $pdf_gen->render_invoice( $inv, $settings );
			$filename = sanitize_file_name( $inv['docNumber'] . '-' . $inv['customerName'] ) . '.pdf';
			STBM_DB::log_activity( 'export', 'invoice', $inv['docNumber'], 'Downloaded Pro Forma Invoice PDF' );
		}

		nocache_headers();
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Length: ' . strlen( $bytes ) );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Binary PDF byte stream.
		echo $bytes;
		exit;
	}

	/**
	 * CSV Export for Invoices, Receipts, or Customers.
	 */
	public static function handle_csv() {
		STBM_Roles::verify_admin_post( STBM_Roles::CAP_BILLING, 'stbm_export_nonce' );

		$dataset = isset( $_GET['dataset'] ) ? sanitize_text_field( wp_unslash( $_GET['dataset'] ) ) : 'invoices';
		$date    = current_time( 'Y-m-d' );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="santa-towing-' . sanitize_file_name( $dataset ) . '-' . $date . '.csv"' );

		$out = fopen( 'php://output', 'w' );
		if ( false === $out ) {
			exit;
		}
		// UTF-8 BOM for clean Excel opening.
		fwrite( $out, "\xEF\xBB\xBF" );

		if ( 'receipts' === $dataset ) {
			fputcsv( $out, array( 'Receipt Number', 'Date', 'Customer Name', 'Phone', 'Linked Invoice', 'Method', 'Reference', 'Amount Received', 'Remaining Balance', 'Status' ) );
			foreach ( STBM_DB::get_receipts() as $r ) {
				fputcsv(
					$out,
					array(
						$r['receiptNumber'],
						$r['receiptDate'],
						$r['customerName'],
						$r['customerPhone'],
						$r['invoiceNumber'] ?: 'Direct',
						$r['paymentMethod'],
						$r['paymentReference'],
						$r['amountReceived'],
						$r['remainingBalance'],
						$r['status'],
					)
				);
			}
			STBM_DB::log_activity( 'export', 'receipt', 'CSV Export', 'Exported Official Receipts CSV ledger' );
		} elseif ( 'customers' === $dataset ) {
			fputcsv( $out, array( 'ID', 'Name', 'Company', 'Phone', 'Email', 'Address', 'Notes', 'Created At' ) );
			foreach ( STBM_DB::get_customers() as $c ) {
				fputcsv(
					$out,
					array(
						$c['id'],
						$c['name'],
						$c['company'],
						$c['phone'],
						$c['email'],
						$c['address'],
						$c['notes'],
						$c['createdAt'],
					)
				);
			}
			STBM_DB::log_activity( 'export', 'customer', 'CSV Export', 'Exported Customers CRM CSV directory' );
		} else {
			fputcsv( $out, array( 'Invoice No', 'Issue Date', 'Valid Until', 'Customer', 'Company', 'Phone', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Amount Paid', 'Balance Due', 'Status' ) );
			foreach ( STBM_DB::get_invoices() as $inv ) {
				fputcsv(
					$out,
					array(
						$inv['docNumber'],
						$inv['issueDate'],
						$inv['validUntil'],
						$inv['customerName'],
						$inv['customerCompany'],
						$inv['customerPhone'],
						$inv['subtotal'],
						$inv['discountAmount'],
						$inv['taxAmount'],
						$inv['grandTotal'],
						$inv['amountPaid'],
						$inv['balanceDue'],
						$inv['status'],
					)
				);
			}
			STBM_DB::log_activity( 'export', 'invoice', 'CSV Export', 'Exported Pro Forma Invoices CSV ledger' );
		}

		fclose( $out );
		exit;
	}

	/**
	 * JSON Backup Export for Billing or Full Site Content + Inventory.
	 */
	public static function handle_json() {
		STBM_Roles::verify_admin_post( STBM_Roles::CAP_BACKUPS, 'stbm_export_nonce' );

		$scope = isset( $_GET['scope'] ) ? sanitize_text_field( wp_unslash( $_GET['scope'] ) ) : 'billing';
		$date  = current_time( 'Y-m-d' );

		if ( 'content' === $scope ) {
			$payload = array(
				'backupType' => 'santa_towing_site_content_v1',
				'exportedAt' => current_time( 'c' ),
				'content'    => STBM_Defaults::get_site_content(),
				'inventory'  => array(
					'cars'    => STBM_Defaults::get_inventory_items( 'cars' ),
					'rentals' => STBM_Defaults::get_inventory_items( 'rentals' ),
					'parts'   => STBM_Defaults::get_inventory_items( 'parts' ),
					'gallery' => STBM_Defaults::get_inventory_items( 'gallery' ),
					'faqs'    => STBM_Defaults::get_inventory_items( 'faqs' ),
					'careers' => STBM_Defaults::get_inventory_items( 'careers' ),
				),
			);
			$fname = 'santa-towing-content-backup-' . $date . '.json';
			STBM_DB::log_activity( 'export', 'settings', 'Content Backup', 'Exported full site content & inventory JSON backup' );
		} else {
			$payload = STBM_DB::export_billing_backup();
			$fname   = 'santa-towing-billing-backup-' . $date . '.json';
			STBM_DB::log_activity( 'export', 'settings', 'Billing Backup', 'Exported full billing & CRM JSON backup' );
		}

		$json = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $fname ) . '"' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download response.
		echo $json;
		exit;
	}
}
