<?php
/**
 * Plugin Name:       Santa Towing Full Site Importer
 * Plugin URI:        https://santatowingservices.com
 * Description:       Beginner-friendly WordPress Admin importer for the complete Santa Towing website: syncs 42 verified media assets, 26 pages (with 15 Elementor Free templates), 261 Ghana service areas, 70 CPT/blog seeds, 4 navigation menus, site settings, Business Manager defaults, and optional Legacy React Browser JSON exports. Safe to deactivate and delete after migration finishes.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.1
 * Author:            Santa Towing Services Ghana
 * Text Domain:       santa-towing
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Santa_Towing_Importer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ST_IMPORTER_VERSION', '1.0.0' );
define( 'ST_IMPORTER_DIR', plugin_dir_path( __FILE__ ) );

// Load migration engines bundled inside this plugin.
require_once ST_IMPORTER_DIR . 'migration/class-st-media-sync.php';
require_once ST_IMPORTER_DIR . 'migration/santa-full-importer.php';
require_once ST_IMPORTER_DIR . 'migration/santa-pilot-importer.php';
require_once ST_IMPORTER_DIR . 'migration/class-stbm-legacy-importer.php';

/**
 * Show quick-access importer links in WP Admin -> Plugins list.
 *
 * @param string[] $links Existing plugin action links.
 * @return string[]
 */
function st_importer_action_links( $links ) {
	$custom = array(
		'<a href="' . esc_url( admin_url( 'tools.php?page=st-full-importer' ) ) . '"><strong>' . esc_html__( 'Full Site Importer', 'santa-towing' ) . '</strong></a>',
		'<a href="' . esc_url( admin_url( 'tools.php?page=stbm-legacy-importer' ) ) . '">' . esc_html__( 'Legacy Browser Importer', 'santa-towing' ) . '</a>',
	);
	return array_merge( $custom, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'st_importer_action_links' );

/**
 * Show helpful setup checklist notice in WP Admin until migration is run or dismissed.
 */
function st_importer_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$missing_prereqs = array();
	$active_theme    = wp_get_theme();
	if ( 'santa-towing' !== $active_theme->get_stylesheet() && 'santa-towing' !== $active_theme->get_template() ) {
		$missing_prereqs[] = __( 'Activate the <strong>Santa Towing</strong> custom theme (<code>Appearance &rarr; Themes</code>).', 'santa-towing' );
	}
	if ( ! post_type_exists( 'st_service_area' ) ) {
		$missing_prereqs[] = __( 'Activate the <strong>Santa Towing Website Core</strong> plugin (<code>Plugins &rarr; Installed Plugins</code>) so all custom post types are registered before importing.', 'santa-towing' );
	}
	if ( '' === ST_Migration_Media_Sync::resolve_assets_dir() ) {
		$missing_prereqs[] = __( 'Verify the <strong>Santa Towing</strong> theme (<code>wp-content/themes/santa-towing/assets/images/</code>) is uploaded so all 42 media assets can sync into the Media Library.', 'santa-towing' );
	}

	if ( ! empty( $missing_prereqs ) ) {
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Santa Towing Full Site Importer — Setup Prerequisites:', 'santa-towing' ) . '</strong></p><ul style="list-style:disc;margin-left:20px;">';
		foreach ( $missing_prereqs as $item ) {
			echo '<li>' . wp_kses_post( $item ) . '</li>';
		}
		echo '</ul></div>';
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && in_array( $screen->id, array( 'plugins', 'dashboard', 'tools_page_st-full-importer' ), true ) ) {
		if ( 'tools_page_st-full-importer' !== $screen->id ) {
			echo '<div class="notice notice-info is-dismissible"><p>'
				. wp_kses_post(
					sprintf(
						/* translators: %s: admin URL to Tools -> Santa Full Importer */
						__( '<strong>Santa Towing Full Site Importer is ready.</strong> Go to <a href="%s">Tools &rarr; Santa Full Importer</a> to import all pages, 261 service areas, CPTs, menus, and 42 media assets. You may safely deactivate &amp; delete this importer plugin once migration completes.', 'santa-towing' ),
						esc_url( admin_url( 'tools.php?page=st-full-importer' ) )
					)
				)
				. '</p></div>';
		}
	}
}
add_action( 'admin_notices', 'st_importer_admin_notices' );
