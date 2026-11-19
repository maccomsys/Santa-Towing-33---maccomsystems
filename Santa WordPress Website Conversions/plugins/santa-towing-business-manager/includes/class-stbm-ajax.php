<?php
/**
 * Granular Nonce-Protected & Capability-Checked AJAX Handlers for all 14 Modules.
 *
 * Every endpoint enforces `STBM_Roles::verify_ajax( $cap )` prior to reading or mutating state.
 * Colors & Logo endpoints strictly enforce `STBM_Roles::CAP_BRAND`.
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_Ajax {

	/**
	 * Register WordPress admin AJAX routes.
	 */
	public static function init() {
		$routes = array(
			'stbm_save_content'         => 'save_content',
			'stbm_reset_content_key'    => 'reset_content_key',
			'stbm_save_brand'           => 'save_brand',
			'stbm_reset_brand'          => 'reset_brand',
			'stbm_save_inventory_item'  => 'save_inventory_item',
			'stbm_delete_inventory_item'=> 'delete_inventory_item',
			'stbm_reorder_inventory'    => 'reorder_inventory',
			'stbm_reset_inventory'      => 'reset_inventory',
			'stbm_save_customer'        => 'save_customer',
			'stbm_delete_customer'      => 'delete_customer',
			'stbm_save_invoice'         => 'save_invoice',
			'stbm_update_invoice_status'=> 'update_invoice_status',
			'stbm_duplicate_invoice'    => 'duplicate_invoice',
			'stbm_delete_invoice'       => 'delete_invoice',
			'stbm_save_receipt'         => 'save_receipt',
			'stbm_delete_receipt'       => 'delete_receipt',
			'stbm_save_doc_settings'    => 'save_doc_settings',
			'stbm_reset_doc_settings'   => 'reset_doc_settings',
			'stbm_save_studio_image'    => 'save_studio_image',
			'stbm_delete_studio_image'  => 'delete_studio_image',
			'stbm_restore_backup'       => 'restore_backup',
			'stbm_import_legacy_bundle' => 'import_legacy_bundle',
			'stbm_reset_billing_seed'   => 'reset_billing_seed',
			'stbm_factory_reset'        => 'factory_reset',
			'stbm_update_profile'       => 'update_profile',
			'stbm_update_password'      => 'update_password',
		);

		foreach ( $routes as $action => $method ) {
			add_action( 'wp_ajax_' . $action, array( __CLASS__, $method ) );
		}
	}

	/**
	 * Parse JSON `payload` sent in POST body.
	 *
	 * @return array
	 */
	private static function read_payload() {
		$raw = isset( $_POST['payload'] ) ? wp_unslash( $_POST['payload'] ) : '';
		if ( is_array( $raw ) ) {
			return $raw;
		}
		if ( '' === $raw ) {
			return array();
		}
		$decoded = json_decode( (string) $raw, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Recursively sanitize generic content dictionary values while allowing safe URLs.
	 *
	 * @param mixed $val Raw value.
	 * @return mixed
	 */
	private static function sanitize_content_value( $val ) {
		if ( is_bool( $val ) || is_int( $val ) || is_float( $val ) || null === $val ) {
			return $val;
		}
		if ( is_string( $val ) ) {
			if ( false !== strpos( $val, "\n" ) ) {
				return sanitize_textarea_field( $val );
			}
			return wp_kses_post( $val );
		}
		if ( is_array( $val ) ) {
			$out = array();
			foreach ( $val as $k => $v ) {
				$clean_k         = is_int( $k ) ? $k : sanitize_text_field( (string) $k );
				$out[ $clean_k ] = self::sanitize_content_value( $v );
			}
			return $out;
		}
		return '';
	}

	/**
	 * Save generic `santa_towing_site_content` dot-path updates (Pages Text, Links, Images, Dashboard Quick Config, Blog).
	 * Blocks `theme.*` and `global.logo.*` unless caller holds `CAP_BRAND`.
	 */
	public static function save_content() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_CONTENT );
		$payload = self::read_payload();
		$updates = $payload['updates'] ?? array();
		if ( ! is_array( $updates ) || empty( $updates ) ) {
			wp_send_json_error( array( 'message' => __( 'No updates supplied.', 'santa-towing-business-manager' ) ), 400 );
		}

		$clean_updates = array();
		foreach ( $updates as $key => $val ) {
			$key_str = sanitize_text_field( (string) $key );
			if ( 0 === strpos( $key_str, 'theme.' ) || 0 === strpos( $key_str, 'global.logo.' ) ) {
				if ( ! current_user_can( STBM_Roles::CAP_BRAND ) && ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( array( 'message' => __( 'Only Administrators / Business Managers can modify Brand Colors or Logo.', 'santa-towing-business-manager' ) ), 403 );
				}
			}
			$clean_updates[ $key_str ] = self::sanitize_content_value( $val );
		}

		$updated = STBM_Defaults::save_content_paths( $clean_updates );
		STBM_DB::log_activity( 'edit', 'settings', 'Website Content', sprintf( 'Updated %d content field(s)', count( $clean_updates ) ) );

		wp_send_json_success(
			array(
				'siteContent' => $updated,
				'logs'        => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Reset a single dot-path key in `santa_towing_site_content`.
	 */
	public static function reset_content_key() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_CONTENT );
		$payload = self::read_payload();
		$key     = sanitize_text_field( (string) ( $payload['key'] ?? '' ) );
		if ( '' === $key ) {
			wp_send_json_error( array( 'message' => __( 'Missing key to reset.', 'santa-towing-business-manager' ) ), 400 );
		}
		if ( 0 === strpos( $key, 'theme.' ) || 0 === strpos( $key, 'global.logo.' ) ) {
			if ( ! current_user_can( STBM_Roles::CAP_BRAND ) && ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Brand reset restricted to Administrator / Business Manager.', 'santa-towing-business-manager' ) ), 403 );
			}
		}

		$updated = STBM_Defaults::reset_content_path( $key );
		wp_send_json_success( array( 'siteContent' => $updated ) );
	}

	/**
	 * Save Brand Colors or Logo (`CAP_BRAND` exclusively).
	 */
	public static function save_brand() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BRAND );
		$payload = self::read_payload();
		$updates = $payload['updates'] ?? array();
		if ( ! is_array( $updates ) || empty( $updates ) ) {
			wp_send_json_error( array( 'message' => __( 'No brand settings supplied.', 'santa-towing-business-manager' ) ), 400 );
		}

		$clean_updates = array();
		foreach ( $updates as $k => $v ) {
			$k_str = sanitize_text_field( (string) $k );
			if ( 0 === strpos( $k_str, 'theme.' ) ) {
				$hex = sanitize_hex_color( (string) $v );
				if ( ! $hex ) {
					wp_send_json_error( array( 'message' => __( 'Invalid HEX color value provided.', 'santa-towing-business-manager' ) ), 400 );
				}
				$clean_updates[ $k_str ] = $hex;
			} elseif ( 'global.logo.url' === $k_str || 'global.images.logo' === $k_str ) {
				$clean_updates[ $k_str ] = esc_url_raw( (string) $v );
			} else {
				$clean_updates[ $k_str ] = sanitize_text_field( (string) $v );
			}
		}

		$updated = STBM_Defaults::save_content_paths( $clean_updates );
		STBM_DB::log_activity( 'edit', 'settings', 'Brand & Theme', 'Updated Brand Colors / Logo settings' );

		wp_send_json_success(
			array(
				'siteContent' => $updated,
				'logs'        => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Reset Colors or Logo back to defaults (`CAP_BRAND` exclusively).
	 */
	public static function reset_brand() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BRAND );
		$payload = self::read_payload();
		$scope   = sanitize_text_field( (string) ( $payload['scope'] ?? 'colors' ) );

		if ( 'logo' === $scope ) {
			STBM_Defaults::reset_content_path( 'global.logo.url' );
			STBM_Defaults::reset_content_path( 'global.logo.alt' );
			$updated = STBM_Defaults::reset_content_path( 'global.images.logo' );
		} else {
			$updated = STBM_Defaults::reset_content_path( 'theme' );
		}

		STBM_DB::log_activity( 'edit', 'settings', 'Brand Reset', "Reset {$scope} to factory defaults" );
		wp_send_json_success(
			array(
				'siteContent' => $updated,
				'logs'        => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Create/update Inventory CPT item (`cars`, `rentals`, `parts`, `gallery`, `faqs`, `careers`).
	 */
	public static function save_inventory_item() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_INVENTORY );
		$payload = self::read_payload();
		$type    = sanitize_text_field( (string) ( $payload['type'] ?? '' ) );
		$item    = $payload['item'] ?? array();

		$saved = STBM_Defaults::save_inventory_item( $type, $item );
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error( array( 'message' => $saved->get_error_message() ), 400 );
		}

		STBM_DB::log_activity( ! empty( $item['id'] ) ? 'edit' : 'create', 'settings', strtoupper( $type ), 'Saved item: ' . ( $saved['name'] ?? $saved['title'] ?? $saved['q'] ?? '' ) );

		wp_send_json_success(
			array(
				'item'  => $saved,
				'items' => STBM_Defaults::get_inventory_items( $type ),
				'logs'  => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Delete Inventory CPT item permanently.
	 */
	public static function delete_inventory_item() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_INVENTORY );
		$payload = self::read_payload();
		$type    = sanitize_text_field( (string) ( $payload['type'] ?? '' ) );
		$id      = (int) ( $payload['id'] ?? 0 );
		$pt      = STBM_Defaults::map_inventory_cpt( $type );

		if ( ! $pt || $id <= 0 || get_post_type( $id ) !== $pt ) {
			wp_send_json_error( array( 'message' => __( 'Inventory item not found.', 'santa-towing-business-manager' ) ), 404 );
		}

		$title = get_the_title( $id );
		wp_delete_post( $id, true );
		STBM_DB::log_activity( 'delete', 'settings', strtoupper( $type ), "Deleted item: {$title}" );

		wp_send_json_success(
			array(
				'items' => STBM_Defaults::get_inventory_items( $type ),
				'logs'  => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Persist drag/button sort order for an inventory category.
	 */
	public static function reorder_inventory() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_INVENTORY );
		$payload = self::read_payload();
		$type    = sanitize_text_field( (string) ( $payload['type'] ?? '' ) );
		$ids     = is_array( $payload['ids'] ?? null ) ? $payload['ids'] : array();
		$pt      = STBM_Defaults::map_inventory_cpt( $type );

		if ( ! $pt ) {
			wp_send_json_error( array( 'message' => __( 'Invalid inventory category.', 'santa-towing-business-manager' ) ), 400 );
		}

		foreach ( $ids as $idx => $raw_id ) {
			$pid = (int) $raw_id;
			if ( $pid > 0 && get_post_type( $pid ) === $pt ) {
				update_post_meta( $pid, '_st_sort_order', $idx + 1 );
			}
		}

		wp_send_json_success( array( 'items' => STBM_Defaults::get_inventory_items( $type ) ) );
	}

	/**
	 * Restore factory default dataset for an inventory category.
	 */
	public static function reset_inventory() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_INVENTORY );
		$payload = self::read_payload();
		$type    = sanitize_text_field( (string) ( $payload['type'] ?? '' ) );
		$items   = STBM_Defaults::reset_inventory_type( $type );

		STBM_DB::log_activity( 'import', 'settings', strtoupper( $type ), "Restored default {$type} inventory" );
		wp_send_json_success(
			array(
				'items' => $items,
				'logs'  => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Create or edit CRM Customer.
	 */
	public static function save_customer() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$saved   = STBM_DB::save_customer( $payload );
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error( array( 'message' => $saved->get_error_message() ), 400 );
		}
		wp_send_json_success(
			array(
				'customer'  => $saved,
				'customers' => STBM_DB::get_customers(),
				'logs'      => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Delete CRM Customer (enforcing linked Invoice/Receipt guard).
	 */
	public static function delete_customer() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$uid     = sanitize_text_field( (string) ( $payload['id'] ?? '' ) );
		$res     = STBM_DB::delete_customer( $uid );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ), 409 );
		}
		wp_send_json_success(
			array(
				'customers' => STBM_DB::get_customers(),
				'logs'      => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Create or update Pro Forma Invoice.
	 */
	public static function save_invoice() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$saved   = STBM_DB::save_invoice( $payload );
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error( array( 'message' => $saved->get_error_message() ), 400 );
		}
		wp_send_json_success(
			array(
				'invoice'  => $saved,
				'invoices' => STBM_DB::get_invoices(),
				'logs'     => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Inline Invoice status update (`Draft`, `Issued`, `Accepted`, `Cancelled`).
	 */
	public static function update_invoice_status() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$uid     = sanitize_text_field( (string) ( $payload['id'] ?? '' ) );
		$status  = sanitize_text_field( (string) ( $payload['status'] ?? '' ) );
		$inv     = STBM_DB::get_invoice( $uid );
		if ( ! $inv ) {
			wp_send_json_error( array( 'message' => __( 'Invoice not found.', 'santa-towing-business-manager' ) ), 404 );
		}
		$inv['status'] = $status;
		$saved         = STBM_DB::save_invoice( $inv, false );
		STBM_DB::log_activity( 'status_change', 'invoice', $saved['docNumber'], "Changed status of {$saved['docNumber']} to {$saved['status']}" );

		wp_send_json_success(
			array(
				'invoice'  => $saved,
				'invoices' => STBM_DB::get_invoices(),
				'logs'     => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Duplicate an Invoice as a fresh Draft.
	 */
	public static function duplicate_invoice() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$uid     = sanitize_text_field( (string) ( $payload['id'] ?? '' ) );
		$dup     = STBM_DB::duplicate_invoice( $uid );
		if ( is_wp_error( $dup ) ) {
			wp_send_json_error( array( 'message' => $dup->get_error_message() ), 404 );
		}
		wp_send_json_success(
			array(
				'invoice'  => $dup,
				'invoices' => STBM_DB::get_invoices(),
				'logs'     => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Delete an Invoice (enforcing linked Receipt guard).
	 */
	public static function delete_invoice() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$uid     = sanitize_text_field( (string) ( $payload['id'] ?? '' ) );
		$res     = STBM_DB::delete_invoice( $uid );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ), 409 );
		}
		wp_send_json_success(
			array(
				'invoices' => STBM_DB::get_invoices(),
				'logs'     => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Create or update Official Receipt and sync linked Invoice balances.
	 */
	public static function save_receipt() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$saved   = STBM_DB::save_receipt( $payload );
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error( array( 'message' => $saved->get_error_message() ), 400 );
		}
		wp_send_json_success(
			array(
				'receipt'  => $saved,
				'receipts' => STBM_DB::get_receipts(),
				'invoices' => STBM_DB::get_invoices(),
				'logs'     => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Delete Official Receipt and recalculate linked Invoice balances.
	 */
	public static function delete_receipt() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$uid     = sanitize_text_field( (string) ( $payload['id'] ?? '' ) );
		$res     = STBM_DB::delete_receipt( $uid );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ), 404 );
		}
		wp_send_json_success(
			array(
				'receipts' => STBM_DB::get_receipts(),
				'invoices' => STBM_DB::get_invoices(),
				'logs'     => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Save Document Settings (letterhead, currency, TIN, terms, remittance).
	 */
	public static function save_doc_settings() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$payload = self::read_payload();
		$saved   = STBM_Defaults::save_doc_settings( $payload );
		STBM_DB::log_activity( 'edit', 'settings', 'Document Settings', 'Updated Invoice & Receipt Company Letterhead Settings' );
		wp_send_json_success(
			array(
				'docSettings' => $saved,
				'logs'        => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Reset Document Settings back to defaults.
	 */
	public static function reset_doc_settings() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BILLING );
		$defaults = STBM_Defaults::get_default_doc_settings();
		update_option( STBM_Defaults::OPTION_DOC_SETTINGS, $defaults );
		STBM_DB::log_activity( 'edit', 'settings', 'Document Settings', 'Reset Document Settings to factory defaults' );
		wp_send_json_success(
			array(
				'docSettings' => $defaults,
				'logs'        => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Save / Upload / Edit Image Studio Asset (syncs to WP Media Library & `stbm_studio_images` table).
	 */
	public static function save_studio_image() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_STUDIO );
		$payload = self::read_payload();
		$saved   = STBM_DB::save_studio_image( $payload );
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error( array( 'message' => $saved->get_error_message() ), 400 );
		}
		wp_send_json_success(
			array(
				'image'        => $saved,
				'studioImages' => STBM_DB::get_studio_images(),
				'logs'         => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Delete Image Studio asset record.
	 */
	public static function delete_studio_image() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_STUDIO );
		$payload = self::read_payload();
		$uid     = sanitize_text_field( (string) ( $payload['id'] ?? '' ) );
		if ( ! STBM_DB::delete_studio_image( $uid ) ) {
			wp_send_json_error( array( 'message' => __( 'Studio asset not found.', 'santa-towing-business-manager' ) ), 404 );
		}
		wp_send_json_success(
			array(
				'studioImages' => STBM_DB::get_studio_images(),
				'logs'         => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Restore Billing JSON or Content JSON backup (Merge or Replace mode).
	 */
	public static function restore_backup() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BACKUPS );
		$payload = self::read_payload();
		$scope   = sanitize_text_field( (string) ( $payload['scope'] ?? 'billing' ) );
		$mode    = sanitize_text_field( (string) ( $payload['mode'] ?? 'replace' ) );
		$backup  = $payload['data'] ?? array();

		if ( 'content' === $scope ) {
			if ( ! is_array( $backup ) || empty( $backup['content'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Invalid site content backup JSON.', 'santa-towing-business-manager' ) ), 400 );
			}
			$incoming = self::sanitize_content_value( $backup['content'] );
			if ( 'merge' === $mode ) {
				$merged = STBM_Defaults::deep_merge_content( STBM_Defaults::get_site_content(), $incoming );
				update_option( STBM_Defaults::OPTION_SITE_CONTENT, $merged );
			} else {
				update_option( STBM_Defaults::OPTION_SITE_CONTENT, STBM_Defaults::deep_merge_content( STBM_Defaults::get_default_site_content(), $incoming ) );
			}
			if ( ! empty( $backup['inventory'] ) && is_array( $backup['inventory'] ) ) {
				foreach ( array( 'cars', 'rentals', 'parts', 'gallery', 'faqs', 'careers' ) as $type ) {
					if ( isset( $backup['inventory'][ $type ] ) && is_array( $backup['inventory'][ $type ] ) ) {
						if ( 'replace' === $mode ) {
							$pt = STBM_Defaults::map_inventory_cpt( $type );
							foreach ( get_posts( array( 'post_type' => $pt, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $pid ) {
								wp_delete_post( $pid, true );
							}
						}
						foreach ( $backup['inventory'][ $type ] as $idx => $it ) {
							unset( $it['id'] );
							$it['order'] = $idx + 1;
							STBM_Defaults::save_inventory_item( $type, $it );
						}
					}
				}
			}
			STBM_DB::log_activity( 'import', 'settings', 'Content Restore', "Restored site content JSON ({$mode})" );
			wp_send_json_success(
				array(
					'siteContent' => STBM_Defaults::get_site_content(),
					'inventory'   => array(
						'cars'    => STBM_Defaults::get_inventory_items( 'cars' ),
						'rentals' => STBM_Defaults::get_inventory_items( 'rentals' ),
						'parts'   => STBM_Defaults::get_inventory_items( 'parts' ),
						'gallery' => STBM_Defaults::get_inventory_items( 'gallery' ),
						'faqs'    => STBM_Defaults::get_inventory_items( 'faqs' ),
						'careers' => STBM_Defaults::get_inventory_items( 'careers' ),
					),
					'logs'        => STBM_DB::get_activity_logs( 100 ),
				)
			);
		} else {
			$res = STBM_DB::restore_billing_backup( $backup );
			if ( is_wp_error( $res ) ) {
				wp_send_json_error( array( 'message' => $res->get_error_message() ), 400 );
			}
			wp_send_json_success(
				array(
					'docSettings' => STBM_Defaults::get_doc_settings(),
					'customers'   => STBM_DB::get_customers(),
					'invoices'    => STBM_DB::get_invoices(),
					'receipts'    => STBM_DB::get_receipts(),
					'logs'        => STBM_DB::get_activity_logs( 100 ),
				)
			);
		}
	}

	/**
	 * Import unified `santa-legacy-browser-export-v1` (or legacy JSON backups) with Dry-Run & Audit Report.
	 */
	public static function import_legacy_bundle() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BACKUPS );
		$payload = self::read_payload();
		$bundle  = $payload['bundle'] ?? array();
		$mode    = sanitize_key( (string) ( $payload['mode'] ?? 'merge' ) );
		$dry_run = ! empty( $payload['dryRun'] );

		if ( ! is_array( $bundle ) || empty( $bundle ) ) {
			wp_send_json_error( array( 'message' => __( 'Uploaded JSON archive is empty or invalid.', 'santa-towing-business-manager' ) ), 400 );
		}

		$report = STBM_Legacy_Importer::import_bundle(
			$bundle,
			array(
				'mode'         => 'replace' === $mode ? 'replace' : 'merge',
				'dry_run'      => $dry_run,
				'enforce_caps' => true,
			)
		);

		if ( ! $report['ok'] && in_array( $report['schema'], array( 'invalid', 'unknown' ), true ) ) {
			wp_send_json_error( array( 'message' => implode( ' ', $report['failed'] ), 'report' => $report ), 400 );
		}

		wp_send_json_success(
			array(
				'report'       => $report,
				'siteContent'  => STBM_Defaults::get_site_content(),
				'docSettings'  => STBM_Defaults::get_doc_settings(),
				'customers'    => STBM_DB::get_customers(),
				'invoices'     => STBM_DB::get_invoices(),
				'receipts'     => STBM_DB::get_receipts(),
				'studioImages' => STBM_DB::get_studio_images(),
				'inventory'    => array(
					'cars'    => STBM_Defaults::get_inventory_items( 'cars' ),
					'rentals' => STBM_Defaults::get_inventory_items( 'rentals' ),
					'parts'   => STBM_Defaults::get_inventory_items( 'parts' ),
					'gallery' => STBM_Defaults::get_inventory_items( 'gallery' ),
					'faqs'    => STBM_Defaults::get_inventory_items( 'faqs' ),
					'careers' => STBM_Defaults::get_inventory_items( 'careers' ),
				),
				'logs'         => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Reset billing tables back to clean starter seed records.
	 */
	public static function reset_billing_seed() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BACKUPS );
		STBM_DB::seed_initial_data( true );
		STBM_DB::log_activity( 'import', 'settings', 'Seed Reset', 'Reset billing ledger & studio seeds to factory state' );
		wp_send_json_success(
			array(
				'customers'    => STBM_DB::get_customers(),
				'invoices'     => STBM_DB::get_invoices(),
				'receipts'     => STBM_DB::get_receipts(),
				'studioImages' => STBM_DB::get_studio_images(),
				'logs'         => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Factory reset site content (and optionally inventory CPTs).
	 */
	public static function factory_reset() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_BACKUPS );
		$payload = self::read_payload();
		$with_inv = ! empty( $payload['includeInventory'] );
		$content  = STBM_Defaults::factory_reset_content( $with_inv );
		STBM_DB::log_activity( 'import', 'settings', 'Factory Reset', 'Restored website content to factory defaults' );
		wp_send_json_success(
			array(
				'siteContent' => $content,
				'inventory'   => array(
					'cars'    => STBM_Defaults::get_inventory_items( 'cars' ),
					'rentals' => STBM_Defaults::get_inventory_items( 'rentals' ),
					'parts'   => STBM_Defaults::get_inventory_items( 'parts' ),
					'gallery' => STBM_Defaults::get_inventory_items( 'gallery' ),
					'faqs'    => STBM_Defaults::get_inventory_items( 'faqs' ),
					'careers' => STBM_Defaults::get_inventory_items( 'careers' ),
				),
				'logs'        => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Update Current WordPress User Display Name, Email, Bio, and Avatar Meta.
	 */
	public static function update_profile() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_CONSOLE );
		$payload = self::read_payload();
		$user    = wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			wp_send_json_error( array( 'message' => __( 'User session not found.', 'santa-towing-business-manager' ) ), 401 );
		}

		$display_name = sanitize_text_field( (string) ( $payload['displayName'] ?? $user->display_name ) );
		$email        = sanitize_email( (string) ( $payload['email'] ?? $user->user_email ) );
		$avatar       = esc_url_raw( (string) ( $payload['avatar'] ?? '' ) );
		$job_title    = sanitize_text_field( (string) ( $payload['jobTitle'] ?? '' ) );

		$update_args = array(
			'ID'           => $user->ID,
			'display_name' => $display_name ?: $user->display_name,
		);
		if ( is_email( $email ) && ( $email === $user->user_email || ! email_exists( $email ) ) ) {
			$update_args['user_email'] = $email;
		}
		$res = wp_update_user( $update_args );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ), 400 );
		}

		update_user_meta( $user->ID, 'stbm_avatar_url', $avatar );
		update_user_meta( $user->ID, 'stbm_job_title', $job_title );

		STBM_DB::log_activity( 'edit', 'settings', 'User Profile', "Updated WP profile for {$display_name}" );

		$refreshed = get_userdata( $user->ID );
		wp_send_json_success(
			array(
				'user' => array(
					'id'          => $refreshed->ID,
					'username'    => $refreshed->user_login,
					'displayName' => $refreshed->display_name,
					'email'       => $refreshed->user_email,
					'avatar'      => $avatar ?: stbm_asset_image( 'avatar-operations-lead-256.jpg' ),
					'jobTitle'    => $job_title ?: STBM_Roles::get_user_role_info( $refreshed )['label'],
				),
				'logs' => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}

	/**
	 * Securely verify current WP password and update password for current user.
	 */
	public static function update_password() {
		STBM_Roles::verify_ajax( STBM_Roles::CAP_CONSOLE );
		$payload = self::read_payload();
		$user    = wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			wp_send_json_error( array( 'message' => __( 'User session not found.', 'santa-towing-business-manager' ) ), 401 );
		}

		$current = (string) ( $payload['currentPassword'] ?? '' );
		$new_pwd = (string) ( $payload['newPassword'] ?? '' );

		if ( '' === $current || ! wp_check_password( $current, $user->user_pass, $user->ID ) ) {
			wp_send_json_error( array( 'message' => __( 'Current WordPress password is incorrect.', 'santa-towing-business-manager' ) ), 403 );
		}
		if ( strlen( trim( $new_pwd ) ) < 8 ) {
			wp_send_json_error( array( 'message' => __( 'New password must be at least 8 characters long.', 'santa-towing-business-manager' ) ), 400 );
		}

		wp_set_password( $new_pwd, $user->ID );
		// Re-authenticate session immediately so the admin isn't logged out unexpectedly.
		wp_set_auth_cookie( $user->ID, true );

		STBM_DB::log_activity( 'edit', 'settings', 'Security', "Changed WordPress password for {$user->user_login}" );
		wp_send_json_success(
			array(
				'message' => __( 'WordPress password updated successfully.', 'santa-towing-business-manager' ),
				'logs'    => STBM_DB::get_activity_logs( 100 ),
			)
		);
	}
}
