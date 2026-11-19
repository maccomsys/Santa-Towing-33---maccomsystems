<?php
/**
 * Uninstall handler for Santa Towing Business Manager.
 *
 * Intentionally preserves `santa_towing_site_content` and `st_*` CPT posts so
 * public website content is not destroyed on plugin uninstall. Only cleans
 * transient caches and rewrite flush state.
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_transient( 'stbm_stats_cache' );
flush_rewrite_rules();
