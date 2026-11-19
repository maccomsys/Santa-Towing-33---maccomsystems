<?php
/**
 * Custom MySQL Database Schema, Billing Calculations, Relational Guards, and Image Studio Storage.
 *
 * Tables created via dbDelta():
 * - {$wpdb->prefix}stbm_customers
 * - {$wpdb->prefix}stbm_invoices
 * - {$wpdb->prefix}stbm_receipts
 * - {$wpdb->prefix}stbm_activity_logs
 * - {$wpdb->prefix}stbm_studio_images
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_DB {

	/**
	 * Table name helper.
	 *
	 * @param string $suffix Short suffix.
	 * @return string
	 */
	public static function table( $suffix ) {
		global $wpdb;
		return $wpdb->prefix . 'stbm_' . $suffix;
	}

	/**
	 * Create or update custom tables using WordPress dbDelta().
	 */
	public static function create_tables() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$t_cust          = self::table( 'customers' );
		$t_inv           = self::table( 'invoices' );
		$t_rec           = self::table( 'receipts' );
		$t_logs          = self::table( 'activity_logs' );
		$t_studio        = self::table( 'studio_images' );

		$sql_cust = "CREATE TABLE {$t_cust} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			uid varchar(64) NOT NULL,
			name varchar(191) NOT NULL,
			company varchar(191) DEFAULT '' NOT NULL,
			phone varchar(64) NOT NULL,
			email varchar(191) DEFAULT '' NOT NULL,
			address text NOT NULL,
			notes text NOT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uid (uid),
			KEY name (name)
		) {$charset_collate};";

		$sql_inv = "CREATE TABLE {$t_inv} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			uid varchar(64) NOT NULL,
			doc_number varchar(64) NOT NULL,
			doc_title varchar(128) DEFAULT 'Pro Forma Invoice' NOT NULL,
			issue_date date NOT NULL,
			valid_until date NOT NULL,
			customer_uid varchar(64) DEFAULT '' NOT NULL,
			customer_name varchar(191) NOT NULL,
			customer_company varchar(191) DEFAULT '' NOT NULL,
			customer_phone varchar(64) NOT NULL,
			customer_email varchar(191) DEFAULT '' NOT NULL,
			customer_address text NOT NULL,
			items_json longtext NOT NULL,
			subtotal decimal(12,2) NOT NULL DEFAULT 0.00,
			discount_type varchar(20) NOT NULL DEFAULT 'percentage',
			discount_value decimal(12,2) NOT NULL DEFAULT 0.00,
			discount_amount decimal(12,2) NOT NULL DEFAULT 0.00,
			discount_enabled tinyint(1) NOT NULL DEFAULT 1,
			discount_label varchar(64) DEFAULT 'Discount' NOT NULL,
			tax_enabled tinyint(1) NOT NULL DEFAULT 1,
			tax_label varchar(64) DEFAULT 'Tax / VAT' NOT NULL,
			tax_rate decimal(5,2) NOT NULL DEFAULT 15.00,
			tax_amount decimal(12,2) NOT NULL DEFAULT 0.00,
			shipping_enabled tinyint(1) NOT NULL DEFAULT 0,
			shipping_label varchar(64) DEFAULT 'Dispatch / Towing Fee' NOT NULL,
			shipping_amount decimal(12,2) NOT NULL DEFAULT 0.00,
			additional_charges_json longtext NOT NULL,
			grand_total decimal(12,2) NOT NULL DEFAULT 0.00,
			amount_paid decimal(12,2) NOT NULL DEFAULT 0.00,
			balance_due decimal(12,2) NOT NULL DEFAULT 0.00,
			payment_instructions text NOT NULL,
			terms_and_conditions text NOT NULL,
			notes text NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'Draft',
			custom_fields_json longtext NOT NULL,
			custom_sections_json longtext NOT NULL,
			hidden_fields_json longtext NOT NULL,
			field_labels_json longtext NOT NULL,
			section_titles_json longtext NOT NULL,
			section_order_json longtext NOT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uid (uid),
			UNIQUE KEY doc_number (doc_number),
			KEY status (status),
			KEY customer_uid (customer_uid)
		) {$charset_collate};";

		$sql_rec = "CREATE TABLE {$t_rec} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			uid varchar(64) NOT NULL,
			receipt_number varchar(64) NOT NULL,
			receipt_date date NOT NULL,
			customer_uid varchar(64) DEFAULT '' NOT NULL,
			customer_name varchar(191) NOT NULL,
			customer_phone varchar(64) NOT NULL,
			customer_email varchar(191) DEFAULT '' NOT NULL,
			customer_address text NOT NULL,
			invoice_uid varchar(64) DEFAULT '' NOT NULL,
			invoice_number varchar(64) DEFAULT '' NOT NULL,
			amount_received decimal(12,2) NOT NULL DEFAULT 0.00,
			invoice_total decimal(12,2) DEFAULT NULL,
			remaining_balance decimal(12,2) NOT NULL DEFAULT 0.00,
			payment_method varchar(64) NOT NULL,
			payment_reference varchar(128) NOT NULL,
			notes text NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'Issued',
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uid (uid),
			UNIQUE KEY receipt_number (receipt_number),
			KEY invoice_uid (invoice_uid),
			KEY customer_uid (customer_uid),
			KEY status (status)
		) {$charset_collate};";

		$sql_logs = "CREATE TABLE {$t_logs} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			uid varchar(64) NOT NULL,
			log_timestamp datetime NOT NULL,
			action varchar(64) NOT NULL,
			document_type varchar(32) NOT NULL,
			document_number varchar(128) DEFAULT '' NOT NULL,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			user_name varchar(128) NOT NULL,
			details text NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uid (uid),
			KEY log_timestamp (log_timestamp)
		) {$charset_collate};";

		$sql_studio = "CREATE TABLE {$t_studio} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			uid varchar(64) NOT NULL,
			attachment_id bigint(20) unsigned NOT NULL DEFAULT 0,
			name varchar(191) NOT NULL,
			image_url text NOT NULL,
			width int(11) NOT NULL DEFAULT 0,
			height int(11) NOT NULL DEFAULT 0,
			format varchar(32) NOT NULL DEFAULT 'image/jpeg',
			size_bytes bigint(20) NOT NULL DEFAULT 0,
			category varchar(32) NOT NULL DEFAULT 'General',
			is_edited tinyint(1) NOT NULL DEFAULT 0,
			original_uid varchar(64) DEFAULT '' NOT NULL,
			original_name varchar(191) DEFAULT '' NOT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uid (uid),
			KEY category (category)
		) {$charset_collate};";

		dbDelta( $sql_cust );
		dbDelta( $sql_inv );
		dbDelta( $sql_rec );
		dbDelta( $sql_logs );
		dbDelta( $sql_studio );

		update_option( 'stbm_db_version', STBM_DB_VERSION );
	}

	/**
	 * Seed initial customers, invoices, receipts, logs, and studio images if tables are empty.
	 *
	 * @param bool $force Force re-seed even if rows exist (clears billing/studio tables first).
	 */
	public static function seed_initial_data( $force = false ) {
		global $wpdb;
		$t_cust   = self::table( 'customers' );
		$t_inv    = self::table( 'invoices' );
		$t_rec    = self::table( 'receipts' );
		$t_logs   = self::table( 'activity_logs' );
		$t_studio = self::table( 'studio_images' );

		if ( $force ) {
			$wpdb->query( "TRUNCATE TABLE {$t_rec}" );
			$wpdb->query( "TRUNCATE TABLE {$t_inv}" );
			$wpdb->query( "TRUNCATE TABLE {$t_cust}" );
			$wpdb->query( "TRUNCATE TABLE {$t_logs}" );
			$wpdb->query( "TRUNCATE TABLE {$t_studio}" );
			delete_option( 'stbm_billing_seeded_v1' );
			delete_option( 'stbm_studio_seeded_v1' );
		}

		$settings = STBM_Defaults::get_doc_settings();

		// 1. Billing Seed (Customers, Invoices, Receipts, Logs).
		$cust_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_cust}" );
		if ( 0 === $cust_count && ! get_option( 'stbm_billing_seeded_v1' ) ) {
			$customers = array(
				array(
					'uid' => 'cust-1', 'name' => 'Kwame Mensah', 'company' => 'Golden Transport Logistics Ltd', 'phone' => '0244123456',
					'email' => 'kwame@goldentransport.com.gh', 'address' => 'Plot 44, Heavy Industrial Area, Tema, Ghana',
					'notes' => 'Regular logistics fleet partner. Prefers MoMo payments.', 'created_at' => '2026-10-15 08:30:00', 'updated_at' => '2026-10-15 08:30:00',
				),
				array(
					'uid' => 'cust-2', 'name' => 'Akosua Boakye', 'company' => 'Apex Consult Ltd', 'phone' => '0208987654',
					'email' => 'akosua.b@apexconsult.com', 'address' => 'Airport Residential Area, Accra, Ghana',
					'notes' => 'Toyota Prado recovery customer.', 'created_at' => '2026-10-18 11:15:00', 'updated_at' => '2026-10-18 11:15:00',
				),
				array(
					'uid' => 'cust-3', 'name' => 'Emmanuel Osei', 'company' => 'Prime Haulage & Mining Supplies', 'phone' => '0553456789',
					'email' => 'e.osei@primemining.gh', 'address' => 'Tarkwa Highway Depot, Western Region, Ghana',
					'notes' => 'Excavator and flatbed low-bed equipment transport.', 'created_at' => '2026-10-20 14:40:00', 'updated_at' => '2026-10-20 14:40:00',
				),
			);
			foreach ( $customers as $c ) {
				$wpdb->insert( $t_cust, $c );
			}

			$invoices = array(
				array(
					'uid' => 'inv-1', 'docNumber' => 'PFI-202610-0001', 'issueDate' => '2026-10-22', 'validUntil' => '2026-11-05',
					'customerId' => 'cust-1', 'customerName' => 'Kwame Mensah', 'customerCompany' => 'Golden Transport Logistics Ltd',
					'customerPhone' => '0244123456', 'customerEmail' => 'kwame@goldentransport.com.gh', 'customerAddress' => 'Plot 44, Heavy Industrial Area, Tema, Ghana',
					'items' => array(
						array( 'id' => 'item-1', 'description' => 'Heavy Duty 30-Ton Low-Bed Haulage (Tema Port to Spintex Depot)', 'quantity' => 1, 'unitPrice' => 4500, 'total' => 4500 ),
						array( 'id' => 'item-2', 'description' => 'Accident Scene Winching & Crane Lifting Assistance', 'quantity' => 1, 'unitPrice' => 1200, 'total' => 1200 ),
					),
					'discountType' => 'percentage', 'discountValue' => 5, 'taxRate' => 15, 'amountPaid' => 6227.25,
					'paymentInstructions' => $settings['defaultPaymentInstructions'], 'termsAndConditions' => $settings['defaultTerms'],
					'notes' => 'Equipment secured and transported with hydraulic locking rig.', 'status' => 'Accepted',
					'createdAt' => '2026-10-22 09:00:00', 'updatedAt' => '2026-10-23 15:30:00',
				),
				array(
					'uid' => 'inv-2', 'docNumber' => 'PFI-202610-0002', 'issueDate' => '2026-10-24', 'validUntil' => '2026-11-07',
					'customerId' => 'cust-2', 'customerName' => 'Akosua Boakye', 'customerCompany' => 'Apex Consult Ltd',
					'customerPhone' => '0208987654', 'customerEmail' => 'akosua.b@apexconsult.com', 'customerAddress' => 'Airport Residential Area, Accra, Ghana',
					'items' => array(
						array( 'id' => 'item-3', 'description' => 'Flatbed Emergency Towing (Airport Residential to Spintex Garage)', 'quantity' => 1, 'unitPrice' => 850, 'total' => 850 ),
						array( 'id' => 'item-4', 'description' => 'Complete Vehicle Diagnostic Scan & Alternator Inspection', 'quantity' => 1, 'unitPrice' => 350, 'total' => 350 ),
					),
					'discountType' => 'fixed', 'discountValue' => 0, 'taxRate' => 15, 'amountPaid' => 700,
					'paymentInstructions' => $settings['defaultPaymentInstructions'], 'termsAndConditions' => $settings['defaultTerms'],
					'notes' => 'Partial payment received. Balance due upon garage repair completion.', 'status' => 'Issued',
					'createdAt' => '2026-10-24 10:15:00', 'updatedAt' => '2026-10-25 12:00:00',
				),
				array(
					'uid' => 'inv-3', 'docNumber' => 'PFI-202610-0003', 'issueDate' => '2026-10-26', 'validUntil' => '2026-11-09',
					'customerId' => 'cust-3', 'customerName' => 'Emmanuel Osei', 'customerCompany' => 'Prime Haulage & Mining Supplies',
					'customerPhone' => '0553456789', 'customerEmail' => 'e.osei@primemining.gh', 'customerAddress' => 'Tarkwa Highway Depot, Western Region, Ghana',
					'items' => array(
						array( 'id' => 'item-5', 'description' => 'Inter-City Long Distance Towing & Haulage (Accra to Kumasi Suame)', 'quantity' => 1, 'unitPrice' => 6800, 'total' => 6800 ),
						array( 'id' => 'item-6', 'description' => 'Highway Security Escort & Toll Permitting', 'quantity' => 1, 'unitPrice' => 950, 'total' => 950 ),
					),
					'discountType' => 'percentage', 'discountValue' => 10, 'taxRate' => 15, 'amountPaid' => 0,
					'paymentInstructions' => $settings['defaultPaymentInstructions'], 'termsAndConditions' => $settings['defaultTerms'],
					'notes' => 'Draft awaiting final equipment serial confirmation from client.', 'status' => 'Draft',
					'createdAt' => '2026-10-26 16:00:00', 'updatedAt' => '2026-10-26 16:00:00',
				),
			);
			foreach ( $invoices as $inv ) {
				self::save_invoice( $inv, false );
			}

			$receipts = array(
				array(
					'uid' => 'rec-1', 'receiptNumber' => 'REC-202610-0001', 'receiptDate' => '2026-10-23',
					'customerId' => 'cust-1', 'customerName' => 'Kwame Mensah', 'customerPhone' => '0244123456',
					'customerEmail' => 'kwame@goldentransport.com.gh', 'customerAddress' => 'Plot 44, Heavy Industrial Area, Tema, Ghana',
					'invoiceId' => 'inv-1', 'invoiceNumber' => 'PFI-202610-0001', 'amountReceived' => 6227.25,
					'paymentMethod' => 'Bank Transfer', 'paymentReference' => 'ECO-TX-994827104',
					'notes' => 'Full payment cleared via Ecobank corporate transfer.', 'status' => 'Issued',
					'createdAt' => '2026-10-23 15:30:00', 'updatedAt' => '2026-10-23 15:30:00',
				),
				array(
					'uid' => 'rec-2', 'receiptNumber' => 'REC-202610-0002', 'receiptDate' => '2026-10-25',
					'customerId' => 'cust-2', 'customerName' => 'Akosua Boakye', 'customerPhone' => '0208987654',
					'customerEmail' => 'akosua.b@apexconsult.com', 'customerAddress' => 'Airport Residential Area, Accra, Ghana',
					'invoiceId' => 'inv-2', 'invoiceNumber' => 'PFI-202610-0002', 'amountReceived' => 700,
					'paymentMethod' => 'Mobile Money (MoMo)', 'paymentReference' => 'MM-ID-8839201948',
					'notes' => 'Initial 50% deposit received via MTN Mobile Money.', 'status' => 'Issued',
					'createdAt' => '2026-10-25 12:00:00', 'updatedAt' => '2026-10-25 12:00:00',
				),
			);
			foreach ( $receipts as $rec ) {
				self::save_receipt( $rec, false );
			}

			self::log_activity( 'create', 'invoice', 'PFI-202610-0003', 'Created draft pro forma invoice for Emmanuel Osei (GH₵ 8,021.25)', 'Operations Manager', '2026-10-26 16:00:00' );
			self::log_activity( 'create', 'receipt', 'REC-202610-0002', 'Recorded MTN MoMo payment of GH₵ 700.00 from Akosua Boakye', 'Operations Manager', '2026-10-25 12:00:00' );
			self::log_activity( 'create', 'receipt', 'REC-202610-0001', 'Recorded full bank transfer payment of GH₵ 6,227.25 from Kwame Mensah', 'Operations Manager', '2026-10-23 15:30:00' );

			update_option( 'stbm_billing_seeded_v1', 1 );
		}

		// 2. Studio Images Seed.
		$studio_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_studio}" );
		if ( 0 === $studio_count && ! get_option( 'stbm_studio_seeded_v1' ) ) {
			$studio_seeds = array(
				array(
					'uid' => 'seed-img-1', 'name' => 'Santa Heavy Duty Towing Truck', 'image_url' => stbm_asset_image( 'image-1-towing-vehicle.jpg' ),
					'width' => 1200, 'height' => 800, 'format' => 'image/jpeg', 'size_bytes' => 245000, 'category' => 'Fleet', 'is_edited' => 0,
					'created_at' => '2026-10-20 10:00:00', 'updated_at' => '2026-10-20 10:00:00',
				),
				array(
					'uid' => 'seed-img-2', 'name' => 'Excavator Heavy Equipment Haulage', 'image_url' => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ),
					'width' => 1200, 'height' => 800, 'format' => 'image/jpeg', 'size_bytes' => 312000, 'category' => 'Haulage', 'is_edited' => 0,
					'created_at' => '2026-10-21 11:30:00', 'updated_at' => '2026-10-21 11:30:00',
				),
				array(
					'uid' => 'seed-img-3', 'name' => 'Accident Scene Hydraulic Recovery', 'image_url' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
					'width' => 1200, 'height' => 800, 'format' => 'image/png', 'size_bytes' => 280000, 'category' => 'Towing', 'is_edited' => 0,
					'created_at' => '2026-10-22 08:15:00', 'updated_at' => '2026-10-22 08:15:00',
				),
				array(
					'uid' => 'seed-img-4', 'name' => 'Santa Official Company Wordmark Logo', 'image_url' => stbm_asset_image( 'santa-logo.png' ),
					'width' => 600, 'height' => 200, 'format' => 'image/png', 'size_bytes' => 48000, 'category' => 'Documents', 'is_edited' => 0,
					'created_at' => '2026-10-23 14:00:00', 'updated_at' => '2026-10-23 14:00:00',
				),
			);
			foreach ( $studio_seeds as $s ) {
				$wpdb->insert( $t_studio, $s );
			}
			update_option( 'stbm_studio_seeded_v1', 1 );
		}
	}

	/**
	 * Round money consistently to 2 decimals.
	 */
	public static function round_money( $val ) {
		return round( (float) $val, 2 );
	}

	/**
	 * Deterministic server-side invoice financial math matching React `documentStorage.ts`.
	 *
	 * @param array $inv Raw invoice payload.
	 * @return array Calculated totals & normalized items.
	 */
	public static function calculate_invoice_totals( $inv ) {
		$items      = is_array( $inv['items'] ?? null ) ? $inv['items'] : array();
		$norm_items = array();
		$subtotal   = 0.0;

		foreach ( $items as $idx => $item ) {
			$qty        = max( 0, (float) ( $item['quantity'] ?? 1 ) );
			$unit_price = max( 0, (float) ( $item['unitPrice'] ?? 0 ) );
			$line_total = self::round_money( $qty * $unit_price );
			$subtotal  += $line_total;
			$norm_items[] = array(
				'id'          => sanitize_text_field( $item['id'] ?? ( 'item-' . ( $idx + 1 ) ) ),
				'description' => sanitize_text_field( $item['description'] ?? '' ),
				'quantity'    => $qty,
				'unitPrice'   => $unit_price,
				'total'       => $line_total,
			);
		}
		$subtotal = self::round_money( $subtotal );

		$discount_enabled = array_key_exists( 'discountEnabled', $inv ) ? (bool) $inv['discountEnabled'] : true;
		$discount_type    = ( isset( $inv['discountType'] ) && 'fixed' === $inv['discountType'] ) ? 'fixed' : 'percentage';
		$discount_val     = max( 0, (float) ( $inv['discountValue'] ?? 0 ) );
		$discount_amount  = 0.0;
		if ( $discount_enabled && $discount_val > 0 ) {
			$discount_amount = ( 'percentage' === $discount_type )
				? ( $subtotal * ( $discount_val / 100.0 ) )
				: $discount_val;
		}
		$discount_amount = self::round_money( min( $subtotal, $discount_amount ) );
		$after_discount  = max( 0.0, $subtotal - $discount_amount );

		$tax_enabled = array_key_exists( 'taxEnabled', $inv ) ? (bool) $inv['taxEnabled'] : true;
		$tax_rate    = max( 0, min( 100, (float) ( $inv['taxRate'] ?? 0 ) ) );
		$tax_amount  = $tax_enabled ? self::round_money( $after_discount * ( $tax_rate / 100.0 ) ) : 0.0;

		$shipping_enabled = ! empty( $inv['shippingEnabled'] );
		$shipping_amount  = $shipping_enabled ? self::round_money( max( 0, (float) ( $inv['shippingAmount'] ?? 0 ) ) ) : 0.0;

		$extra_charges     = array();
		$extra_charges_sum = 0.0;
		if ( ! empty( $inv['additionalCharges'] ) && is_array( $inv['additionalCharges'] ) ) {
			foreach ( $inv['additionalCharges'] as $c_idx => $ch ) {
				$enabled = array_key_exists( 'enabled', $ch ) ? (bool) $ch['enabled'] : true;
				$amt     = self::round_money( max( 0, (float) ( $ch['amount'] ?? 0 ) ) );
				$extra_charges[] = array(
					'id'      => sanitize_text_field( $ch['id'] ?? ( 'chg-' . ( $c_idx + 1 ) ) ),
					'label'   => sanitize_text_field( $ch['label'] ?? 'Fee' ),
					'amount'  => $amt,
					'enabled' => $enabled,
				);
				if ( $enabled ) {
					$extra_charges_sum += $amt;
				}
			}
		}

		$grand_total = self::round_money( $after_discount + $tax_amount + $shipping_amount + $extra_charges_sum );
		$amount_paid = self::round_money( max( 0, (float) ( $inv['amountPaid'] ?? 0 ) ) );
		$balance_due = self::round_money( max( 0.0, $grand_total - $amount_paid ) );

		return array(
			'items'             => $norm_items,
			'subtotal'          => $subtotal,
			'discountEnabled'   => $discount_enabled,
			'discountType'      => $discount_type,
			'discountValue'     => $discount_val,
			'discountAmount'    => $discount_amount,
			'taxEnabled'        => $tax_enabled,
			'taxRate'           => $tax_rate,
			'taxAmount'         => $tax_amount,
			'shippingEnabled'   => $shipping_enabled,
			'shippingAmount'    => $shipping_amount,
			'additionalCharges' => $extra_charges,
			'grandTotal'        => $grand_total,
			'amountPaid'        => $amount_paid,
			'balanceDue'        => $balance_due,
		);
	}

	/**
	 * Generate sequential document number (`PFI-YYYYMM-0001` or `REC-YYYYMM-0001`).
	 *
	 * @param string $prefix `PFI` or `REC`.
	 * @return string
	 */
	public static function next_document_number( $prefix = 'PFI' ) {
		global $wpdb;
		$ym = gmdate( 'Ym' );
		if ( 'REC' === $prefix ) {
			$t_rec = self::table( 'receipts' );
			$like  = 'REC-' . $ym . '-%';
			$last  = $wpdb->get_var( $wpdb->prepare( "SELECT receipt_number FROM {$t_rec} WHERE receipt_number LIKE %s ORDER BY id DESC LIMIT 1", $like ) );
		} else {
			$t_inv = self::table( 'invoices' );
			$like  = 'PFI-' . $ym . '-%';
			$last  = $wpdb->get_var( $wpdb->prepare( "SELECT doc_number FROM {$t_inv} WHERE doc_number LIKE %s ORDER BY id DESC LIMIT 1", $like ) );
		}
		$seq = 1;
		if ( $last && preg_match( '/-(\d+)$/', (string) $last, $m ) ) {
			$seq = (int) $m[1] + 1;
		}
		return sprintf( '%s-%s-%04d', $prefix, $ym, $seq );
	}

	/**
	 * Hydrate Customer DB row to JS-friendly array.
	 */
	private static function format_customer( $row ) {
		return array(
			'id'        => $row['uid'],
			'name'      => $row['name'],
			'company'   => $row['company'],
			'phone'     => $row['phone'],
			'email'     => $row['email'],
			'address'   => $row['address'],
			'notes'     => $row['notes'],
			'createdAt' => mysql2date( 'c', $row['created_at'] ),
			'updatedAt' => mysql2date( 'c', $row['updated_at'] ),
		);
	}

	/**
	 * List all customers.
	 *
	 * @return array[]
	 */
	public static function get_customers() {
		global $wpdb;
		$t    = self::table( 'customers' );
		$rows = $wpdb->get_results( "SELECT * FROM {$t} ORDER BY created_at DESC", ARRAY_A );
		return array_map( array( __CLASS__, 'format_customer' ), is_array( $rows ) ? $rows : array() );
	}

	/**
	 * Create or update a customer.
	 *
	 * @param array $data Customer fields.
	 * @param bool  $log  Write activity log.
	 * @return array|WP_Error
	 */
	public static function save_customer( $data, $log = true ) {
		global $wpdb;
		$t   = self::table( 'customers' );
		$uid = sanitize_text_field( $data['id'] ?? $data['uid'] ?? '' );
		$now = current_time( 'mysql' );

		$name = sanitize_text_field( $data['name'] ?? '' );
		if ( '' === trim( $name ) ) {
			return new WP_Error( 'missing_name', __( 'Customer name is required.', 'santa-towing-business-manager' ) );
		}

		$record = array(
			'name'       => $name,
			'company'    => sanitize_text_field( $data['company'] ?? '' ),
			'phone'      => sanitize_text_field( $data['phone'] ?? '' ),
			'email'      => sanitize_email( $data['email'] ?? '' ),
			'address'    => sanitize_textarea_field( $data['address'] ?? '' ),
			'notes'      => sanitize_textarea_field( $data['notes'] ?? '' ),
			'updated_at' => $now,
		);

		$existing = $uid ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A ) : null;
		if ( $existing ) {
			$wpdb->update( $t, $record, array( 'uid' => $uid ) );
			if ( $log ) {
				self::log_activity( 'edit', 'customer', $name, "Updated customer record for {$name}" );
			}
		} else {
			$uid                  = $uid ?: ( 'cust-' . wp_generate_uuid4() );
			$record['uid']        = $uid;
			$record['created_at'] = ! empty( $data['created_at'] ) ? $data['created_at'] : $now;
			$wpdb->insert( $t, $record );
			if ( $log ) {
				self::log_activity( 'create', 'customer', $name, "Added new customer {$name}" );
			}
		}

		$saved = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A );
		return self::format_customer( $saved );
	}

	/**
	 * Delete customer only if NO invoices or receipts reference them (Safe Delete Guard).
	 *
	 * @param string $uid Customer UID.
	 * @return true|WP_Error
	 */
	public static function delete_customer( $uid ) {
		global $wpdb;
		$t_cust = self::table( 'customers' );
		$t_inv  = self::table( 'invoices' );
		$t_rec  = self::table( 'receipts' );

		$cust = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_cust} WHERE uid = %s", $uid ), ARRAY_A );
		if ( ! $cust ) {
			return new WP_Error( 'not_found', __( 'Customer not found.', 'santa-towing-business-manager' ) );
		}

		$inv_cnt = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t_inv} WHERE customer_uid = %s", $uid ) );
		$rec_cnt = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t_rec} WHERE customer_uid = %s", $uid ) );
		if ( $inv_cnt > 0 || $rec_cnt > 0 ) {
			return new WP_Error(
				'has_linked_docs',
				sprintf(
					/* translators: 1: invoice count, 2: receipt count */
					__( 'Cannot delete customer: %1$d pro forma invoice(s) and %2$d receipt(s) are linked to this customer.', 'santa-towing-business-manager' ),
					$inv_cnt,
					$rec_cnt
				)
			);
		}

		$wpdb->delete( $t_cust, array( 'uid' => $uid ) );
		self::log_activity( 'delete', 'customer', $cust['name'], "Deleted customer {$cust['name']}" );
		return true;
	}

	/**
	 * Hydrate Invoice DB row to React-parity object.
	 */
	private static function format_invoice( $row ) {
		$items      = json_decode( $row['items_json'], true );
		$charges    = json_decode( $row['additional_charges_json'], true );
		$c_fields   = json_decode( $row['custom_fields_json'], true );
		$c_sections = json_decode( $row['custom_sections_json'], true );
		$hidden_f   = json_decode( $row['hidden_fields_json'], true );
		$field_lbls = json_decode( $row['field_labels_json'], true );
		$sec_titles = json_decode( $row['section_titles_json'], true );
		$sec_order  = json_decode( $row['section_order_json'], true );

		return array(
			'id'                  => $row['uid'],
			'docNumber'           => $row['doc_number'],
			'docTitle'            => $row['doc_title'],
			'issueDate'           => $row['issue_date'],
			'validUntil'          => $row['valid_until'],
			'customerId'          => $row['customer_uid'],
			'customerName'        => $row['customer_name'],
			'customerCompany'     => $row['customer_company'],
			'customerPhone'       => $row['customer_phone'],
			'customerEmail'       => $row['customer_email'],
			'customerAddress'     => $row['customer_address'],
			'items'               => is_array( $items ) ? $items : array(),
			'subtotal'            => (float) $row['subtotal'],
			'discountType'        => $row['discount_type'],
			'discountValue'       => (float) $row['discount_value'],
			'discountAmount'      => (float) $row['discount_amount'],
			'discountEnabled'     => (bool) $row['discount_enabled'],
			'discountLabel'       => $row['discount_label'],
			'taxEnabled'          => (bool) $row['tax_enabled'],
			'taxLabel'            => $row['tax_label'],
			'taxRate'             => (float) $row['tax_rate'],
			'taxAmount'           => (float) $row['tax_amount'],
			'shippingEnabled'     => (bool) $row['shipping_enabled'],
			'shippingLabel'       => $row['shipping_label'],
			'shippingAmount'      => (float) $row['shipping_amount'],
			'additionalCharges'   => is_array( $charges ) ? $charges : array(),
			'grandTotal'          => (float) $row['grand_total'],
			'amountPaid'          => (float) $row['amount_paid'],
			'balanceDue'          => (float) $row['balance_due'],
			'paymentInstructions' => $row['payment_instructions'],
			'termsAndConditions'  => $row['terms_and_conditions'],
			'notes'               => $row['notes'],
			'status'              => $row['status'],
			'customFields'        => is_array( $c_fields ) ? $c_fields : array(),
			'customSections'      => is_array( $c_sections ) ? $c_sections : array(),
			'hiddenFields'        => is_array( $hidden_f ) ? $hidden_f : array(),
			'fieldLabels'         => is_array( $field_lbls ) ? $field_lbls : new stdClass(),
			'sectionTitles'       => is_array( $sec_titles ) ? $sec_titles : new stdClass(),
			'sectionOrder'        => is_array( $sec_order ) ? $sec_order : array(),
			'createdAt'           => mysql2date( 'c', $row['created_at'] ),
			'updatedAt'           => mysql2date( 'c', $row['updated_at'] ),
		);
	}

	/**
	 * Fetch all invoices ordered newest first.
	 *
	 * @return array[]
	 */
	public static function get_invoices() {
		global $wpdb;
		$t    = self::table( 'invoices' );
		$rows = $wpdb->get_results( "SELECT * FROM {$t} ORDER BY created_at DESC", ARRAY_A );
		return array_map( array( __CLASS__, 'format_invoice' ), is_array( $rows ) ? $rows : array() );
	}

	/**
	 * Fetch single invoice by UID.
	 *
	 * @param string $uid Invoice UID.
	 * @return array|null
	 */
	public static function get_invoice( $uid ) {
		global $wpdb;
		$t   = self::table( 'invoices' );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A );
		return $row ? self::format_invoice( $row ) : null;
	}

	/**
	 * Create or update a Pro Forma Invoice with deterministic total math.
	 *
	 * @param array $data Invoice payload.
	 * @param bool  $log  Write activity log.
	 * @return array|WP_Error
	 */
	public static function save_invoice( $data, $log = true ) {
		global $wpdb;
		$t        = self::table( 'invoices' );
		$uid      = sanitize_text_field( $data['id'] ?? $data['uid'] ?? '' );
		$existing = $uid ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A ) : null;

		$settings = STBM_Defaults::get_doc_settings();
		$calc     = self::calculate_invoice_totals( $data );
		$now      = current_time( 'mysql' );

		$doc_number = sanitize_text_field( $data['docNumber'] ?? '' );
		if ( '' === $doc_number ) {
			$doc_number = $existing ? $existing['doc_number'] : self::next_document_number( 'PFI' );
		}

		// Preserve calculated paid balance from active receipts when editing an existing invoice.
		$amount_paid = $calc['amountPaid'];
		if ( $existing ) {
			$t_rec      = self::table( 'receipts' );
			$linked_sum = (float) $wpdb->get_var(
				$wpdb->prepare( "SELECT COALESCE(SUM(amount_received), 0) FROM {$t_rec} WHERE invoice_uid = %s AND status != 'Cancelled'", $uid )
			);
			if ( $linked_sum > 0 ) {
				$amount_paid = self::round_money( $linked_sum );
			}
		}
		$balance_due = self::round_money( max( 0.0, $calc['grandTotal'] - $amount_paid ) );

		$allowed_statuses = array( 'Draft', 'Issued', 'Accepted', 'Cancelled' );
		$status           = in_array( $data['status'] ?? 'Draft', $allowed_statuses, true ) ? $data['status'] : 'Draft';

		$record = array(
			'doc_number'              => $doc_number,
			'doc_title'               => sanitize_text_field( $data['docTitle'] ?? 'Pro Forma Invoice' ),
			'issue_date'              => sanitize_text_field( $data['issueDate'] ?? current_time( 'Y-m-d' ) ),
			'valid_until'             => sanitize_text_field( $data['validUntil'] ?? gmdate( 'Y-m-d', strtotime( '+' . (int) $settings['defaultValidityDays'] . ' days' ) ) ),
			'customer_uid'            => sanitize_text_field( $data['customerId'] ?? '' ),
			'customer_name'           => sanitize_text_field( $data['customerName'] ?? '' ),
			'customer_company'        => sanitize_text_field( $data['customerCompany'] ?? '' ),
			'customer_phone'          => sanitize_text_field( $data['customerPhone'] ?? '' ),
			'customer_email'          => sanitize_email( $data['customerEmail'] ?? '' ),
			'customer_address'        => sanitize_textarea_field( $data['customerAddress'] ?? '' ),
			'items_json'              => wp_json_encode( $calc['items'] ),
			'subtotal'                => $calc['subtotal'],
			'discount_type'           => $calc['discountType'],
			'discount_value'          => $calc['discountValue'],
			'discount_amount'         => $calc['discountAmount'],
			'discount_enabled'        => $calc['discountEnabled'] ? 1 : 0,
			'discount_label'          => sanitize_text_field( $data['discountLabel'] ?? 'Discount' ),
			'tax_enabled'             => $calc['taxEnabled'] ? 1 : 0,
			'tax_label'               => sanitize_text_field( $data['taxLabel'] ?? 'Tax / VAT' ),
			'tax_rate'                => $calc['taxRate'],
			'tax_amount'              => $calc['taxAmount'],
			'shipping_enabled'        => $calc['shippingEnabled'] ? 1 : 0,
			'shipping_label'          => sanitize_text_field( $data['shippingLabel'] ?? 'Dispatch / Towing Fee' ),
			'shipping_amount'         => $calc['shippingAmount'],
			'additional_charges_json' => wp_json_encode( $calc['additionalCharges'] ),
			'grand_total'             => $calc['grandTotal'],
			'amount_paid'             => $amount_paid,
			'balance_due'             => $balance_due,
			'payment_instructions'    => sanitize_textarea_field( $data['paymentInstructions'] ?? $settings['defaultPaymentInstructions'] ),
			'terms_and_conditions'    => sanitize_textarea_field( $data['termsAndConditions'] ?? $settings['defaultTerms'] ),
			'notes'                   => sanitize_textarea_field( $data['notes'] ?? '' ),
			'status'                  => $status,
			'custom_fields_json'      => wp_json_encode( is_array( $data['customFields'] ?? null ) ? $data['customFields'] : array() ),
			'custom_sections_json'    => wp_json_encode( is_array( $data['customSections'] ?? null ) ? $data['customSections'] : array() ),
			'hidden_fields_json'      => wp_json_encode( is_array( $data['hiddenFields'] ?? null ) ? array_values( $data['hiddenFields'] ) : array() ),
			'field_labels_json'       => wp_json_encode( is_array( $data['fieldLabels'] ?? null ) ? $data['fieldLabels'] : new stdClass() ),
			'section_titles_json'     => wp_json_encode( is_array( $data['sectionTitles'] ?? null ) ? $data['sectionTitles'] : new stdClass() ),
			'section_order_json'      => wp_json_encode( is_array( $data['sectionOrder'] ?? null ) ? array_values( $data['sectionOrder'] ) : array() ),
			'updated_at'              => ! empty( $data['updatedAt'] ) ? $data['updatedAt'] : $now,
		);

		if ( $existing ) {
			$wpdb->update( $t, $record, array( 'uid' => $uid ) );
			if ( $log ) {
				self::log_activity( 'edit', 'invoice', $doc_number, "Updated invoice {$doc_number} for {$record['customer_name']}" );
			}
		} else {
			$uid                  = $uid ?: ( 'inv-' . wp_generate_uuid4() );
			$record['uid']        = $uid;
			$record['created_at'] = ! empty( $data['createdAt'] ) ? $data['createdAt'] : $now;
			$wpdb->insert( $t, $record );
			if ( $log ) {
				self::log_activity( 'create', 'invoice', $doc_number, "Created {$status} pro forma invoice {$doc_number} for {$record['customer_name']}" );
			}
		}

		return self::get_invoice( $uid );
	}

	/**
	 * Delete an invoice if NO receipts reference it (Safe Delete Guard).
	 *
	 * @param string $uid Invoice UID.
	 * @return true|WP_Error
	 */
	public static function delete_invoice( $uid ) {
		global $wpdb;
		$t_inv = self::table( 'invoices' );
		$t_rec = self::table( 'receipts' );

		$inv = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_inv} WHERE uid = %s", $uid ), ARRAY_A );
		if ( ! $inv ) {
			return new WP_Error( 'not_found', __( 'Invoice not found.', 'santa-towing-business-manager' ) );
		}

		$rec_cnt = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t_rec} WHERE invoice_uid = %s", $uid ) );
		if ( $rec_cnt > 0 ) {
			return new WP_Error(
				'has_linked_receipts',
				sprintf(
					/* translators: %d: receipt count */
					__( 'Cannot delete invoice %1$s: %2$d receipt(s) are linked to it. Cancel or remove linked receipts first.', 'santa-towing-business-manager' ),
					$inv['doc_number'],
					$rec_cnt
				)
			);
		}

		$wpdb->delete( $t_inv, array( 'uid' => $uid ) );
		self::log_activity( 'delete', 'invoice', $inv['doc_number'], "Deleted invoice {$inv['doc_number']}" );
		return true;
	}

	/**
	 * Duplicate an existing invoice as a fresh Draft.
	 *
	 * @param string $uid Source invoice UID.
	 * @return array|WP_Error
	 */
	public static function duplicate_invoice( $uid ) {
		$src = self::get_invoice( $uid );
		if ( ! $src ) {
			return new WP_Error( 'not_found', __( 'Source invoice not found.', 'santa-towing-business-manager' ) );
		}
		unset( $src['id'], $src['createdAt'], $src['updatedAt'] );
		$src['docNumber']  = self::next_document_number( 'PFI' );
		$src['issueDate']  = current_time( 'Y-m-d' );
		$settings          = STBM_Defaults::get_doc_settings();
		$src['validUntil'] = gmdate( 'Y-m-d', strtotime( '+' . (int) $settings['defaultValidityDays'] . ' days' ) );
		$src['amountPaid'] = 0;
		$src['status']     = 'Draft';
		$dup               = self::save_invoice( $src, false );
		self::log_activity( 'duplicate', 'invoice', $dup['docNumber'], "Duplicated {$uid} as {$dup['docNumber']}" );
		return $dup;
	}

	/**
	 * Recalculate linked invoice `amount_paid` and `balance_due` from non-cancelled receipts.
	 *
	 * @param string $invoice_uid Invoice UID.
	 * @return float Remaining balance.
	 */
	public static function recalculate_invoice_balance( $invoice_uid ) {
		global $wpdb;
		if ( '' === $invoice_uid ) {
			return 0.0;
		}
		$t_inv = self::table( 'invoices' );
		$t_rec = self::table( 'receipts' );

		$inv = $wpdb->get_row( $wpdb->prepare( "SELECT id, grand_total FROM {$t_inv} WHERE uid = %s", $invoice_uid ), ARRAY_A );
		if ( ! $inv ) {
			return 0.0;
		}

		$paid = (float) $wpdb->get_var(
			$wpdb->prepare( "SELECT COALESCE(SUM(amount_received), 0) FROM {$t_rec} WHERE invoice_uid = %s AND status != 'Cancelled'", $invoice_uid )
		);
		$paid    = self::round_money( $paid );
		$balance = self::round_money( max( 0.0, (float) $inv['grand_total'] - $paid ) );

		$wpdb->update(
			$t_inv,
			array(
				'amount_paid' => $paid,
				'balance_due' => $balance,
				'updated_at'  => current_time( 'mysql' ),
			),
			array( 'uid' => $invoice_uid )
		);
		return $balance;
	}

	/**
	 * Hydrate Receipt DB row to React-parity object.
	 */
	private static function format_receipt( $row ) {
		return array(
			'id'               => $row['uid'],
			'receiptNumber'    => $row['receipt_number'],
			'receiptDate'      => $row['receipt_date'],
			'customerId'       => $row['customer_uid'],
			'customerName'     => $row['customer_name'],
			'customerPhone'    => $row['customer_phone'],
			'customerEmail'    => $row['customer_email'],
			'customerAddress'  => $row['customer_address'],
			'invoiceId'        => $row['invoice_uid'],
			'invoiceNumber'    => $row['invoice_number'],
			'amountReceived'   => (float) $row['amount_received'],
			'invoiceTotal'     => null !== $row['invoice_total'] ? (float) $row['invoice_total'] : null,
			'remainingBalance' => (float) $row['remaining_balance'],
			'paymentMethod'    => $row['payment_method'],
			'paymentReference' => $row['payment_reference'],
			'notes'            => $row['notes'],
			'status'           => $row['status'],
			'createdAt'        => mysql2date( 'c', $row['created_at'] ),
			'updatedAt'        => mysql2date( 'c', $row['updated_at'] ),
		);
	}

	/**
	 * List all receipts ordered newest first.
	 *
	 * @return array[]
	 */
	public static function get_receipts() {
		global $wpdb;
		$t    = self::table( 'receipts' );
		$rows = $wpdb->get_results( "SELECT * FROM {$t} ORDER BY created_at DESC", ARRAY_A );
		return array_map( array( __CLASS__, 'format_receipt' ), is_array( $rows ) ? $rows : array() );
	}

	/**
	 * Get single receipt by UID.
	 *
	 * @param string $uid Receipt UID.
	 * @return array|null
	 */
	public static function get_receipt( $uid ) {
		global $wpdb;
		$t   = self::table( 'receipts' );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A );
		return $row ? self::format_receipt( $row ) : null;
	}

	/**
	 * Save (create or edit) Official Receipt and atomically sync linked invoice balance.
	 *
	 * @param array $data Receipt payload.
	 * @param bool  $log  Write activity log.
	 * @return array|WP_Error
	 */
	public static function save_receipt( $data, $log = true ) {
		global $wpdb;
		$t        = self::table( 'receipts' );
		$uid      = sanitize_text_field( $data['id'] ?? $data['uid'] ?? '' );
		$existing = $uid ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A ) : null;
		$now      = current_time( 'mysql' );

		$rec_number = sanitize_text_field( $data['receiptNumber'] ?? '' );
		if ( '' === $rec_number ) {
			$rec_number = $existing ? $existing['receipt_number'] : self::next_document_number( 'REC' );
		}

		$allowed_statuses = array( 'Issued', 'Corrected', 'Cancelled' );
		$status           = in_array( $data['status'] ?? 'Issued', $allowed_statuses, true ) ? $data['status'] : 'Issued';

		$invoice_uid    = sanitize_text_field( $data['invoiceId'] ?? '' );
		$invoice_number = sanitize_text_field( $data['invoiceNumber'] ?? '' );
		$invoice_total  = null;
		if ( '' !== $invoice_uid ) {
			$linked_inv = self::get_invoice( $invoice_uid );
			if ( $linked_inv ) {
				$invoice_number = $linked_inv['docNumber'];
				$invoice_total  = (float) $linked_inv['grandTotal'];
			}
		}

		$amount_received = self::round_money( max( 0, (float) ( $data['amountReceived'] ?? 0 ) ) );

		$record = array(
			'receipt_number'    => $rec_number,
			'receipt_date'      => sanitize_text_field( $data['receiptDate'] ?? current_time( 'Y-m-d' ) ),
			'customer_uid'      => sanitize_text_field( $data['customerId'] ?? '' ),
			'customer_name'     => sanitize_text_field( $data['customerName'] ?? '' ),
			'customer_phone'    => sanitize_text_field( $data['customerPhone'] ?? '' ),
			'customer_email'    => sanitize_email( $data['customerEmail'] ?? '' ),
			'customer_address'  => sanitize_textarea_field( $data['customerAddress'] ?? '' ),
			'invoice_uid'       => $invoice_uid,
			'invoice_number'    => $invoice_number,
			'amount_received'   => $amount_received,
			'invoice_total'     => $invoice_total,
			'remaining_balance' => 0.00,
			'payment_method'    => sanitize_text_field( $data['paymentMethod'] ?? 'Mobile Money (MoMo)' ),
			'payment_reference' => sanitize_text_field( $data['paymentReference'] ?? '' ),
			'notes'             => sanitize_textarea_field( $data['notes'] ?? '' ),
			'status'            => $status,
			'updated_at'        => ! empty( $data['updatedAt'] ) ? $data['updatedAt'] : $now,
		);

		$prev_inv_uid = $existing ? $existing['invoice_uid'] : '';
		if ( $existing ) {
			$wpdb->update( $t, $record, array( 'uid' => $uid ) );
			if ( $log ) {
				self::log_activity( 'edit', 'receipt', $rec_number, "Updated receipt {$rec_number} ({$status})" );
			}
		} else {
			$uid                  = $uid ?: ( 'rec-' . wp_generate_uuid4() );
			$record['uid']        = $uid;
			$record['created_at'] = ! empty( $data['createdAt'] ) ? $data['createdAt'] : $now;
			$wpdb->insert( $t, $record );
			if ( $log ) {
				self::log_activity( 'create', 'receipt', $rec_number, "Issued receipt {$rec_number} for {$record['customer_name']} ({$amount_received})" );
			}
		}

		if ( '' !== $prev_inv_uid && $prev_inv_uid !== $invoice_uid ) {
			self::recalculate_invoice_balance( $prev_inv_uid );
		}
		if ( '' !== $invoice_uid ) {
			$remaining = self::recalculate_invoice_balance( $invoice_uid );
			$wpdb->update( $t, array( 'remaining_balance' => $remaining ), array( 'uid' => $uid ) );
		}

		return self::get_receipt( $uid );
	}

	/**
	 * Delete a receipt and recalculate any linked invoice balance.
	 *
	 * @param string $uid Receipt UID.
	 * @return true|WP_Error
	 */
	public static function delete_receipt( $uid ) {
		global $wpdb;
		$t   = self::table( 'receipts' );
		$rec = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A );
		if ( ! $rec ) {
			return new WP_Error( 'not_found', __( 'Receipt not found.', 'santa-towing-business-manager' ) );
		}

		$wpdb->delete( $t, array( 'uid' => $uid ) );
		if ( ! empty( $rec['invoice_uid'] ) ) {
			self::recalculate_invoice_balance( $rec['invoice_uid'] );
		}
		self::log_activity( 'delete', 'receipt', $rec['receipt_number'], "Deleted receipt {$rec['receipt_number']}" );
		return true;
	}

	/**
	 * Write audit activity log record (capped at latest 500 rows for tidy cPanel MySQL tables).
	 */
	public static function log_activity( $action, $entity_type, $entity_title, $details, $user_name = '', $timestamp = '' ) {
		global $wpdb;
		$t    = self::table( 'activity_logs' );
		$user = wp_get_current_user();
		if ( '' === $user_name ) {
			$user_name = ( $user && $user->exists() ) ? $user->display_name : 'System';
		}

		$wpdb->insert(
			$t,
			array(
				'uid'             => 'log-' . wp_generate_uuid4(),
				'log_timestamp'   => $timestamp ?: current_time( 'mysql' ),
				'action'          => sanitize_text_field( $action ),
				'document_type'   => sanitize_text_field( $entity_type ),
				'document_number' => sanitize_text_field( $entity_title ),
				'user_id'         => ( $user && $user->exists() ) ? (int) $user->ID : 0,
				'user_name'       => sanitize_text_field( $user_name ),
				'details'         => sanitize_textarea_field( $details ),
			)
		);
	}

	/**
	 * Fetch recent audit activity logs.
	 *
	 * @param int $limit Max logs.
	 * @return array[]
	 */
	public static function get_activity_logs( $limit = 100 ) {
		global $wpdb;
		$t    = self::table( 'activity_logs' );
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t} ORDER BY log_timestamp DESC, id DESC LIMIT %d", (int) $limit ), ARRAY_A );
		$out  = array();
		foreach ( is_array( $rows ) ? $rows : array() as $r ) {
			$out[] = array(
				'id'          => $r['uid'],
				'timestamp'   => mysql2date( 'c', $r['log_timestamp'] ),
				'action'      => $r['action'],
				'entityType'  => $r['document_type'],
				'entityTitle' => $r['document_number'],
				'user'        => $r['user_name'],
				'details'     => $r['details'],
			);
		}
		return $out;
	}

	/**
	 * List Image Studio assets.
	 *
	 * @return array[]
	 */
	public static function get_studio_images() {
		global $wpdb;
		$t    = self::table( 'studio_images' );
		$rows = $wpdb->get_results( "SELECT * FROM {$t} ORDER BY created_at DESC, id DESC", ARRAY_A );
		$out  = array();
		foreach ( is_array( $rows ) ? $rows : array() as $r ) {
			$out[] = array(
				'id'           => $r['uid'],
				'attachmentId' => (int) $r['attachment_id'],
				'name'         => $r['name'],
				'dataUrl'      => stbm_resolve_media_url( $r['image_url'] ),
				'width'        => (int) $r['width'],
				'height'       => (int) $r['height'],
				'format'       => $r['format'],
				'size'         => (int) $r['size_bytes'],
				'category'     => $r['category'],
				'isEdited'     => (bool) $r['is_edited'],
				'originalId'   => $r['original_uid'],
				'originalName' => $r['original_name'],
				'createdAt'    => mysql2date( 'c', $r['created_at'] ),
				'updatedAt'    => mysql2date( 'c', $r['updated_at'] ),
			);
		}
		return $out;
	}

	/**
	 * Save a studio image asset (creating WP Media attachment when uploading base64 dataURL).
	 *
	 * @param array $data Studio image payload.
	 * @return array|WP_Error
	 */
	public static function save_studio_image( $data ) {
		global $wpdb;
		$t         = self::table( 'studio_images' );
		$uid       = sanitize_text_field( $data['id'] ?? '' );
		$existing  = $uid ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A ) : null;
		$now       = current_time( 'mysql' );
		$name      = sanitize_text_field( $data['name'] ?? 'Untitled Image' );
		$format    = sanitize_text_field( $data['format'] ?? 'image/jpeg' );
		$allowed_f = array( 'image/jpeg', 'image/png', 'image/webp' );
		if ( ! in_array( $format, $allowed_f, true ) ) {
			$format = 'image/jpeg';
		}

		$allowed_cats = array( 'Fleet', 'Towing', 'Haulage', 'Garage', 'Documents', 'General' );
		$category     = in_array( $data['category'] ?? 'General', $allowed_cats, true ) ? $data['category'] : 'General';

		$raw_image_url = (string) ( $data['dataUrl'] ?? $data['imageUrl'] ?? '' );
		$attachment_id = (int) ( $data['attachmentId'] ?? ( $existing['attachment_id'] ?? 0 ) );
		$image_url     = $existing ? $existing['image_url'] : '';
		$size_bytes    = (int) ( $data['size'] ?? ( $existing['size_bytes'] ?? 0 ) );

		// Decode data:image/* payload into wp_upload_bits + WordPress Media Library attachment.
		if ( preg_match( '/^data:(image\/(?:jpeg|jpg|png|webp));base64,(.+)$/i', $raw_image_url, $m ) ) {
			$mime    = strtolower( $m[1] );
			$ext     = 'image/png' === $mime ? 'png' : ( 'image/webp' === $mime ? 'webp' : 'jpg' );
			$decoded = base64_decode( str_replace( ' ', '+', $m[2] ), true );
			if ( false === $decoded || strlen( $decoded ) < 16 ) {
				return new WP_Error( 'invalid_image', __( 'Invalid image payload supplied.', 'santa-towing-business-manager' ) );
			}
			// Guard maximum 12MB upload.
			if ( strlen( $decoded ) > 12 * 1024 * 1024 ) {
				return new WP_Error( 'image_too_large', __( 'Image exceeds 12MB maximum limit.', 'santa-towing-business-manager' ) );
			}

			$slug_file = sanitize_file_name( strtolower( preg_replace( '/[^a-zA-Z0-9_-]+/', '-', $name ) ) ?: 'studio-asset' );
			$filename  = $slug_file . '-' . time() . '.' . $ext;
			$upload    = wp_upload_bits( $filename, null, $decoded );
			if ( ! empty( $upload['error'] ) ) {
				return new WP_Error( 'upload_failed', $upload['error'] );
			}

			$image_url  = $upload['url'];
			$size_bytes = strlen( $decoded );
			$format     = 'jpg' === $ext ? 'image/jpeg' : $mime;

			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_id = wp_insert_attachment(
				array(
					'guid'           => $upload['url'],
					'post_mime_type' => $format,
					'post_title'     => $name,
					'post_content'   => '',
					'post_status'    => 'inherit',
				),
				$upload['file']
			);
			if ( ! is_wp_error( $attach_id ) ) {
				$meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
				wp_update_attachment_metadata( $attach_id, $meta );
				$attachment_id = (int) $attach_id;
			}
		} elseif ( '' !== $raw_image_url && 0 !== strpos( $raw_image_url, 'data:' ) ) {
			$image_url = esc_url_raw( $raw_image_url );
		}

		$record = array(
			'attachment_id' => $attachment_id,
			'name'          => $name,
			'image_url'     => $image_url,
			'width'         => (int) ( $data['width'] ?? ( $existing['width'] ?? 0 ) ),
			'height'        => (int) ( $data['height'] ?? ( $existing['height'] ?? 0 ) ),
			'format'        => $format,
			'size_bytes'    => $size_bytes,
			'category'      => $category,
			'is_edited'     => ! empty( $data['isEdited'] ) ? 1 : 0,
			'original_uid'  => sanitize_text_field( $data['originalId'] ?? ( $existing['original_uid'] ?? '' ) ),
			'original_name' => sanitize_text_field( $data['originalName'] ?? ( $existing['original_name'] ?? '' ) ),
			'updated_at'    => $now,
		);

		if ( $existing ) {
			$wpdb->update( $t, $record, array( 'uid' => $uid ) );
			self::log_activity( 'edit', 'image', $name, "Updated studio asset {$name}" );
		} else {
			$uid                  = $uid ?: ( 'studio-' . wp_generate_uuid4() );
			$record['uid']        = $uid;
			$record['created_at'] = $now;
			$wpdb->insert( $t, $record );
			self::log_activity( 'create', 'image', $name, "Saved studio image {$name} ({$category})" );
		}

		$saved = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A );
		return array(
			'id'           => $saved['uid'],
			'attachmentId' => (int) $saved['attachment_id'],
			'name'         => $saved['name'],
			'dataUrl'      => stbm_resolve_media_url( $saved['image_url'] ),
			'width'        => (int) $saved['width'],
			'height'       => (int) $saved['height'],
			'format'       => $saved['format'],
			'size'         => (int) $saved['size_bytes'],
			'category'     => $saved['category'],
			'isEdited'     => (bool) $saved['is_edited'],
			'originalId'   => $saved['original_uid'],
			'originalName' => $saved['original_name'],
			'createdAt'    => mysql2date( 'c', $saved['created_at'] ),
			'updatedAt'    => mysql2date( 'c', $saved['updated_at'] ),
		);
	}

	/**
	 * Delete Studio Image row.
	 *
	 * @param string $uid Image UID.
	 * @return bool
	 */
	public static function delete_studio_image( $uid ) {
		global $wpdb;
		$t   = self::table( 'studio_images' );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t} WHERE uid = %s", $uid ), ARRAY_A );
		if ( ! $row ) {
			return false;
		}
		$wpdb->delete( $t, array( 'uid' => $uid ) );
		self::log_activity( 'delete', 'image', $row['name'], "Removed studio image {$row['name']}" );
		return true;
	}

	/**
	 * Build full Billing JSON backup object matching React `exportBillingBackup()`.
	 *
	 * @return array
	 */
	public static function export_billing_backup() {
		return array(
			'backupType' => 'santa_towing_billing_v1',
			'exportedAt' => current_time( 'c' ),
			'settings'   => STBM_Defaults::get_doc_settings(),
			'customers'  => self::get_customers(),
			'invoices'   => self::get_invoices(),
			'receipts'   => self::get_receipts(),
			'logs'       => self::get_activity_logs( 250 ),
		);
	}

	/**
	 * Restore full Billing JSON backup (transactional upsert / reset).
	 *
	 * @param array $backup Parsed JSON backup.
	 * @return true|WP_Error
	 */
	public static function restore_billing_backup( $backup ) {
		if ( ! is_array( $backup ) || ( ! isset( $backup['invoices'] ) && ! isset( $backup['customers'] ) && ! isset( $backup['receipts'] ) ) ) {
			return new WP_Error( 'invalid_backup', __( 'Invalid billing backup JSON structure.', 'santa-towing-business-manager' ) );
		}

		global $wpdb;
		$wpdb->query( 'TRUNCATE TABLE ' . self::table( 'receipts' ) );
		$wpdb->query( 'TRUNCATE TABLE ' . self::table( 'invoices' ) );
		$wpdb->query( 'TRUNCATE TABLE ' . self::table( 'customers' ) );

		if ( ! empty( $backup['settings'] ) && is_array( $backup['settings'] ) ) {
			STBM_Defaults::save_doc_settings( $backup['settings'] );
		}
		foreach ( (array) ( $backup['customers'] ?? array() ) as $c ) {
			self::save_customer( $c, false );
		}
		foreach ( (array) ( $backup['invoices'] ?? array() ) as $inv ) {
			self::save_invoice( $inv, false );
		}
		foreach ( (array) ( $backup['receipts'] ?? array() ) as $rec ) {
			self::save_receipt( $rec, false );
		}

		self::log_activity( 'import', 'settings', 'Billing Backup', 'Restored billing backup JSON archive' );
		return true;
	}
}
