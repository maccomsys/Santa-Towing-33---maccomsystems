<?php
/**
 * Uninstall cleanup for Santa Towing Website Core.
 * Runs only when plugin is deleted via WP Admin.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Preserve CPT posts & inquiries by default; remove transient caches and rewrite flag.
delete_transient( 'stwc_diagnostics_cache' );
flush_rewrite_rules();
