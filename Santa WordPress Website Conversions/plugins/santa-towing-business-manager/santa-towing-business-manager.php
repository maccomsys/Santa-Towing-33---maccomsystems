<?php
/**
 * Plugin Name:       Santa Towing Business Manager
 * Plugin URI:        https://santatowingservices.com
 * Description:       Standalone WordPress Operations, Billing, Pro Forma Invoices, Official Receipts, CRM, Image Studio, Inventory, and Site Content Manager for Santa Towing & Garage Services Ltd. Zero Elementor dependency.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Santa Towing & Garage Services Ltd.
 * License:           GPL-2.0-or-later
 * Text Domain:       santa-towing-business-manager
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STBM_VERSION', '1.0.0' );
define( 'STBM_PLUGIN_FILE', __FILE__ );
define( 'STBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STBM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STBM_DB_VERSION', '1.0.0' );

require_once STBM_PLUGIN_DIR . 'includes/class-stbm-roles.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-defaults.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-db.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-pdf.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-exports.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-legacy-importer.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-ajax.php';
require_once STBM_PLUGIN_DIR . 'includes/class-stbm-admin-ui.php';

/**
 * Resolve URL for a bundled plugin image asset.
 *
 * @param string $filename Image filename inside assets/images/.
 * @return string
 */
function stbm_asset_image( $filename ) {
	$clean = ltrim( basename( (string) $filename ), '/' );
	return STBM_PLUGIN_URL . 'assets/images/' . $clean;
}

/**
 * Resolve any legacy `/src/assets/images/...` or relative asset reference to an actual plugin asset URL or existing URL.
 *
 * @param string $value Raw image URL or path.
 * @return string
 */
function stbm_resolve_media_url( $value ) {
	$val = trim( (string) $value );
	if ( '' === $val ) {
		return '';
	}
	if ( 0 === strpos( $val, 'data:image/' ) ) {
		return $val;
	}
	if ( 0 === strpos( $val, 'http://' ) || 0 === strpos( $val, 'https://' ) ) {
		return $val;
	}
	$base = basename( wp_parse_url( $val, PHP_URL_PATH ) ?: $val );
	if ( $base && file_exists( STBM_PLUGIN_DIR . 'assets/images/' . $base ) ) {
		return stbm_asset_image( $base );
	}
	return $val;
}

/**
 * Plugin activation: register roles/capabilities, install DB tables, seed initial billing/studio/content defaults, and register CPTs if standalone.
 */
function stbm_activate_plugin() {
	STBM_Roles::register_roles_and_caps();
	STBM_DB::create_tables();
	STBM_DB::seed_initial_data();
	STBM_Defaults::ensure_site_content_seeded();
	STBM_Defaults::maybe_register_standalone_cpts();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'stbm_activate_plugin' );

/**
 * Plugin deactivation cleanup.
 */
function stbm_deactivate_plugin() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'stbm_deactivate_plugin' );

/**
 * Bootstrap plugin components cleanly.
 */
function stbm_init_plugin() {
	STBM_Roles::ensure_admin_caps();

	// Priority 20 ensures compatibility if Core plugin registers CPTs at priority 5.
	add_action( 'init', array( 'STBM_Defaults', 'maybe_register_standalone_cpts' ), 20 );

	// Ensure schema upgrades run gracefully when updated without reactivation.
	if ( get_option( 'stbm_db_version' ) !== STBM_DB_VERSION ) {
		STBM_DB::create_tables();
		STBM_DB::seed_initial_data();
	}

	STBM_Ajax::init();
	STBM_Exports::init();
	STBM_Admin_UI::init();
}
add_action( 'plugins_loaded', 'stbm_init_plugin', 15 );
