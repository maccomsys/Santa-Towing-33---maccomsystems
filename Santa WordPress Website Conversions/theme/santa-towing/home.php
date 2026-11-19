<?php
/**
 * Blog Index Template (`is_home()` when /blog/ is assigned as Posts Page)
 *
 * Delegates directly to archive.php to maintain 1:1 visual parity with React src/pages/Blog.tsx.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require __DIR__ . '/archive.php';
