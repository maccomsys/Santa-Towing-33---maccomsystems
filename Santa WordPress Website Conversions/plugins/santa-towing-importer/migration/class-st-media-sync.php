<?php
/**
 * Idempotent Media Library Synchronizer
 *
 * Imports verified local assets from Santa WordPress Website Conversions/assets/images/
 * (exact copies of src/assets/images/) into the WordPress Media Library once,
 * preserving exact filenames and setting `_wp_attachment_image_alt` alt text, title,
 * and caption from `media-manifest.json`. Deduplicates via `_st_source_filename` and
 * existing upload filename lookups.
 *
 * @package Santa_Towing_Migration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ST_Migration_Media_Sync {

    /**
     * Cached manifest indexed by filename.
     *
     * @var array|null
     */
    private static $manifest_cache = null;

    /**
     * Resolve path to the local images folder inside the migration workspace or active theme.
     *
     * @return string
     */
    public static function resolve_assets_dir() {
        $candidates = array(
            dirname( __DIR__ ) . '/assets/images',
            trailingslashit( get_template_directory() ) . 'assets/images',
            WP_CONTENT_DIR . '/themes/santa-towing/assets/images',
        );

        foreach ( $candidates as $dir ) {
            if ( is_dir( $dir ) ) {
                return trailingslashit( $dir );
            }
        }

        return '';
    }

    /**
     * Load media-manifest.json indexed by filename.
     *
     * @return array<string, array>
     */
    public static function get_manifest_map() {
        if ( null !== self::$manifest_cache ) {
            return self::$manifest_cache;
        }

        self::$manifest_cache = array();
        $manifest_path        = __DIR__ . '/media-manifest.json';
        if ( file_exists( $manifest_path ) ) {
            $decoded = json_decode( (string) file_get_contents( $manifest_path ), true );
            if ( is_array( $decoded ) && ! empty( $decoded['assets'] ) && is_array( $decoded['assets'] ) ) {
                foreach ( $decoded['assets'] as $entry ) {
                    if ( ! empty( $entry['filename'] ) ) {
                        self::$manifest_cache[ $entry['filename'] ] = $entry;
                    }
                }
            }
        }

        return self::$manifest_cache;
    }

    /**
     * Find an existing attachment ID matching either `_st_source_filename` or upload basename.
     *
     * @param string $filename Exact asset filename.
     * @return int Attachment post ID or 0.
     */
    public static function find_existing_attachment_id( $filename ) {
        $filename = sanitize_file_name( $filename );
        if ( '' === $filename ) {
            return 0;
        }

        // 1. Primary idempotent lookup via `_st_source_filename`.
        $by_source = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => '_st_source_filename',
                'meta_value'     => $filename,
            )
        );
        if ( ! empty( $by_source ) ) {
            return (int) $by_source[0];
        }

        // 2. Secondary deduplication by `_wp_attached_file` filename suffix.
        global $wpdb;
        $like = '%/' . $wpdb->esc_like( $filename );
        $aid  = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT p.ID FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                 WHERE p.post_type = 'attachment'
                   AND p.post_status = 'inherit'
                   AND pm.meta_key = '_wp_attached_file'
                   AND ( pm.meta_value = %s OR pm.meta_value LIKE %s )
                 ORDER BY p.ID ASC
                 LIMIT 1",
                $filename,
                $like
            )
        );

        if ( $aid > 0 ) {
            update_post_meta( $aid, '_st_source_filename', $filename );
            return $aid;
        }

        return 0;
    }

    /**
     * Find an existing imported attachment by original filename, or import it once
     * while preserving exact filename and alt text (`_wp_attachment_image_alt`).
     *
     * @param string $filename Exact asset filename (e.g. "welcome-towing-image.jpg").
     * @param int    $post_id  Optional post ID to attach media to.
     * @param string $alt_text Optional explicit alt text (defaults to media-manifest.json).
     * @return array{id:int,url:string,alt:string,status:string}|WP_Error
     */
    public static function ensure_media_attachment( $filename, $post_id = 0, $alt_text = '' ) {
        $raw_filename = basename( (string) $filename );
        $filename     = sanitize_file_name( $raw_filename );
        if ( '' === $filename ) {
            return new WP_Error( 'st_empty_filename', 'Empty media filename supplied.' );
        }

        $manifest     = self::get_manifest_map();
        $meta         = $manifest[ $raw_filename ] ?? ( $manifest[ $filename ] ?? array() );
        $resolved_alt = '' !== trim( (string) $alt_text )
            ? sanitize_text_field( (string) $alt_text )
            : sanitize_text_field( (string) ( $meta['alt_text'] ?? '' ) );
        $resolved_title = sanitize_text_field(
            (string) ( $meta['title'] ?? ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $filename, PATHINFO_FILENAME ) ) ) )
        );
        $resolved_caption = sanitize_text_field( (string) ( $meta['caption'] ?? '' ) );

        if ( '' === $resolved_alt ) {
            $resolved_alt = $resolved_title;
        }

        // 1. Reuse existing imported attachment if present (deduplicated).
        $existing_id = self::find_existing_attachment_id( $filename );
        if ( $existing_id > 0 ) {
            $url = wp_get_attachment_url( $existing_id );
            if ( $url ) {
                $cur_alt = (string) get_post_meta( $existing_id, '_wp_attachment_image_alt', true );
                if ( '' === $cur_alt && '' !== $resolved_alt ) {
                    update_post_meta( $existing_id, '_wp_attachment_image_alt', $resolved_alt );
                }
                return array(
                    'id'     => $existing_id,
                    'url'    => $url,
                    'alt'    => '' !== $cur_alt ? $cur_alt : $resolved_alt,
                    'status' => 'duplicate_reused',
                );
            }
        }

        // 2. Locate physical file inside Santa WordPress Website Conversions/assets/images/ or theme assets/images/.
        $assets_dir  = self::resolve_assets_dir();
        $source_file = '' !== $assets_dir && file_exists( $assets_dir . $raw_filename )
            ? $assets_dir . $raw_filename
            : ( '' !== $assets_dir && file_exists( $assets_dir . $filename ) ? $assets_dir . $filename : '' );

        if ( '' === $source_file ) {
            return array(
                'id'     => 0,
                'url'    => trailingslashit( get_template_directory_uri() ) . 'assets/images/' . rawurlencode( $raw_filename ),
                'alt'    => $resolved_alt,
                'status' => 'missing_file_fallback',
            );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $upload_dir = wp_upload_dir();
        if ( ! empty( $upload_dir['error'] ) ) {
            return new WP_Error( 'st_upload_dir_error', $upload_dir['error'] );
        }

        // Preserve exact filename when destination does not already collide on disk.
        $dest_dir    = trailingslashit( $upload_dir['path'] );
        $target_name = file_exists( $dest_dir . $filename ) ? wp_unique_filename( $upload_dir['path'], $filename ) : $filename;
        $dest_file   = $dest_dir . $target_name;

        if ( ! @copy( $source_file, $dest_file ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
            return new WP_Error( 'st_copy_failed', 'Could not copy asset ' . $filename . ' to uploads.' );
        }

        $filetype = wp_check_filetype( $dest_file, null );

        $attachment = array(
            'guid'           => trailingslashit( $upload_dir['url'] ) . $target_name,
            'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/jpeg',
            'post_title'     => $resolved_title,
            'post_excerpt'   => $resolved_caption,
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $attach_id = wp_insert_attachment( $attachment, $dest_file, absint( $post_id ), true );
        if ( is_wp_error( $attach_id ) ) {
            return $attach_id;
        }

        $attach_data = wp_generate_attachment_metadata( $attach_id, $dest_file );
        if ( ! is_wp_error( $attach_data ) && ! empty( $attach_data ) ) {
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }

        update_post_meta( $attach_id, '_st_source_filename', $filename );
        update_post_meta( $attach_id, '_st_source_sha256', (string) ( $meta['sha256'] ?? '' ) );
        if ( '' !== $resolved_alt ) {
            update_post_meta( $attach_id, '_wp_attachment_image_alt', $resolved_alt );
        }

        return array(
            'id'     => (int) $attach_id,
            'url'    => wp_get_attachment_url( $attach_id ),
            'alt'    => $resolved_alt,
            'status' => 'imported',
        );
    }

    /**
     * Synchronize all 42 verified local images in media-manifest.json and produce a detailed status report.
     *
     * @param bool $dry_run Preview mode without writing to disk or MySQL.
     * @return array{total:int,imported:int,duplicates_reused:int,missing:string[],failed:string[],cache:array}
     */
    public static function sync_all_verified_images( $dry_run = false ) {
        $manifest = self::get_manifest_map();
        $assets_dir = self::resolve_assets_dir();
        $report   = array(
            'total'             => count( $manifest ),
            'imported'          => 0,
            'duplicates_reused' => 0,
            'missing'           => array(),
            'failed'            => array(),
            'cache'             => array(),
        );

        foreach ( $manifest as $filename => $meta ) {
            if ( $dry_run ) {
                $exists_on_disk = ( '' !== $assets_dir && file_exists( $assets_dir . $filename ) );
                if ( ! $exists_on_disk ) {
                    $report['missing'][] = $filename;
                }
                $existing_id = self::find_existing_attachment_id( $filename );
                if ( $existing_id > 0 ) {
                    $report['duplicates_reused']++;
                    $report['cache'][ $filename ] = array(
                        'id'  => $existing_id,
                        'url' => wp_get_attachment_url( $existing_id ),
                        'alt' => $meta['alt_text'] ?? '',
                    );
                } else {
                    $report['cache'][ $filename ] = array(
                        'id'  => 0,
                        'url' => trailingslashit( get_template_directory_uri() ) . 'assets/images/' . rawurlencode( $filename ),
                        'alt' => $meta['alt_text'] ?? '',
                    );
                }
                continue;
            }

            $res = self::ensure_media_attachment( $filename, 0, $meta['alt_text'] ?? '' );
            if ( is_wp_error( $res ) ) {
                $report['failed'][] = $filename . ': ' . $res->get_error_message();
            } else {
                $report['cache'][ $filename ] = $res;
                if ( 'imported' === ( $res['status'] ?? '' ) ) {
                    $report['imported']++;
                } elseif ( 'duplicate_reused' === ( $res['status'] ?? '' ) ) {
                    $report['duplicates_reused']++;
                } elseif ( 'missing_file_fallback' === ( $res['status'] ?? '' ) ) {
                    $report['missing'][] = $filename;
                }
            }
        }

        return $report;
    }

    /**
     * Resolve all `__ST_MEDIA_URL__filename__`, `__ST_MEDIA_ID__filename__`, and `__ST_HOME_URL__`
     * placeholders recursively across an Elementor template array or content tree.
     *
     * @param mixed $data        Decoded Elementor data tree.
     * @param array $media_cache Filename => ['id'=>int,'url'=>string].
     * @return mixed
     */
    public static function hydrate_elementor_tokens( $data, $media_cache = array() ) {
        $home = untrailingslashit( home_url( '/' ) );

        if ( is_string( $data ) ) {
            $data = str_replace( '__ST_HOME_URL__', $home, $data );

            $data = preg_replace_callback(
                '/__ST_MEDIA_URL__([a-zA-Z0-9._-]+)__/',
                function ( $m ) use ( $media_cache ) {
                    $fn = $m[1];
                    if ( isset( $media_cache[ $fn ]['url'] ) ) {
                        return $media_cache[ $fn ]['url'];
                    }
                    $res = self::ensure_media_attachment( $fn );
                    return is_wp_error( $res ) ? '' : $res['url'];
                },
                $data
            );

            $data = preg_replace_callback(
                '/__ST_MEDIA_ID__([a-zA-Z0-9._-]+)__/',
                function ( $m ) use ( $media_cache ) {
                    $fn = $m[1];
                    if ( isset( $media_cache[ $fn ]['id'] ) ) {
                        return (string) $media_cache[ $fn ]['id'];
                    }
                    $res = self::ensure_media_attachment( $fn );
                    return is_wp_error( $res ) ? '0' : (string) $res['id'];
                },
                $data
            );

            return $data;
        }

        if ( is_array( $data ) ) {
            foreach ( $data as $k => $v ) {
                $data[ $k ] = self::hydrate_elementor_tokens( $v, $media_cache );
            }
        }

        return $data;
    }
}
