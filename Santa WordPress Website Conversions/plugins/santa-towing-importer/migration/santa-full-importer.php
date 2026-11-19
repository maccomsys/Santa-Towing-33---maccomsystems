<?php
/**
 * Santa Towing — Stage 4 Phase B Full-Site & 261 Service Area Bulk Importer
 *
 * Idempotently imports the complete Santa Towing website into WordPress + Elementor FREE:
 *   1. Media Library Sync      — All 42 verified images (`_st_source_filename` deduped)
 *   2. Theme & Plugin Settings — `santa_towing_site_content` + Elementor Kit colors + logo
 *   3. Navigation Menus        — Primary (w/ dropdowns), Footer Quick, Footer Services, Footer Legal
 *   4. CPT & Blog Seeds        — 12 Cars, 6 Rentals, 6 Parts, 6 Blog Posts, 28 FAQs, 8 Gallery, 4 Careers
 *   5. Core + Policy Pages     — 5 Pilot routes + 22 Phase B pages (11 Marketing/Utility + 11 Policies)
 *   6. 261 Service Area Pages  — All 35 Local Districts + 226 Intercity Corridors (`st_service_area`)
 *                                with unique SEO titles, localized copy, keywords, prev/next links,
 *                                `st_region` taxonomy, and Elementor Free `service-area-single-template.json`
 *
 * Supports BOTH:
 *   - WP-CLI: `wp eval-file "Santa WordPress Website Conversions/migration/santa-full-importer.php"`
 *     Optional args after script or env vars:
 *       `ST_IMPORT_SECTION=all|media|settings|menus|cpts|pages|areas`
 *       `ST_IMPORT_OFFSET=0` `ST_IMPORT_LIMIT=261` `ST_IMPORT_DRY_RUN=0|1`
 *   - WP Admin Tools UI (when required by theme/mu-plugin): Tools -> Santa Full Importer
 *
 * Safe for standard cPanel hosting (chunked execution support, wp_defer_term_counting, idempotent upserts).
 *
 * @package Santa_Towing_Migration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-st-media-sync.php';

class ST_Full_Importer {

    /**
     * Hook Admin Tools UI inside WordPress.
     */
    public static function register_admin_page() {
        add_action(
            'admin_menu',
            function () {
                add_management_page(
                    __( 'Santa Full Importer', 'santa-towing' ),
                    __( 'Santa Full Importer', 'santa-towing' ),
                    'manage_options',
                    'st-full-importer',
                    array( __CLASS__, 'render_admin_page' )
                );
            }
        );
    }

    /**
     * Load JSON file safely.
     *
     * @param string $path Absolute JSON file path.
     * @return array|WP_Error
     */
    private static function read_json( $path ) {
        if ( ! file_exists( $path ) ) {
            return new WP_Error( 'st_missing_json', 'Missing migration data file: ' . $path );
        }
        $decoded = json_decode( (string) file_get_contents( $path ), true );
        if ( ! is_array( $decoded ) ) {
            return new WP_Error( 'st_invalid_json', 'Invalid JSON format in: ' . $path );
        }
        return $decoded;
    }

    /**
     * Find existing post idempotently by legacy ID first, then slug + post_type.
     *
     * @param string   $post_type Post type slug.
     * @param string   $slug      Desired post_name.
     * @param int|null $legacy_id Optional `_st_legacy_id`.
     * @return int Post ID or 0.
     */
    private static function find_existing_post_id( $post_type, $slug, $legacy_id = null ) {
        if ( null !== $legacy_id && (int) $legacy_id > 0 ) {
            $by_meta = get_posts(
                array(
                    'post_type'      => $post_type,
                    'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'meta_key'       => '_st_legacy_id',
                    'meta_value'     => (int) $legacy_id,
                )
            );
            if ( ! empty( $by_meta ) ) {
                return (int) $by_meta[0];
            }
        }

        if ( '' !== $slug ) {
            $by_path = get_page_by_path( $slug, OBJECT, $post_type );
            if ( $by_path && isset( $by_path->ID ) ) {
                return (int) $by_path->ID;
            }
        }

        return 0;
    }

    /**
     * Hydrate media placeholders and resolve home URL placeholders recursively.
     *
     * @param mixed $value       String or nested array.
     * @param array $media_cache Filename => ['id'=>int,'url'=>string].
     * @return mixed
     */
    private static function hydrate_value( $value, $media_cache ) {
        return ST_Migration_Media_Sync::hydrate_elementor_tokens( $value, $media_cache );
    }

    /**
     * Execute full-site or targeted migration step idempotently.
     *
     * @param array $opts Options: section, offset, limit, dry_run, areas_with_elementor.
     * @return array{ok:bool,dry_run:bool,section:string,summary:array,errors:array}
     */
    public static function run( $opts = array() ) {
        $section              = sanitize_key( $opts['section'] ?? 'all' );
        $offset               = max( 0, (int) ( $opts['offset'] ?? 0 ) );
        $limit                = max( 1, (int) ( $opts['limit'] ?? 261 ) );
        $dry_run              = ! empty( $opts['dry_run'] );
        $areas_with_elementor = ! isset( $opts['areas_with_elementor'] ) || (bool) $opts['areas_with_elementor'];

        if ( ! $dry_run && function_exists( 'set_time_limit' ) ) {
            @set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
        }

        $seeds    = self::read_json( __DIR__ . '/cpt-seeds.json' );
        $pilot    = self::read_json( __DIR__ . '/pilot-pages.json' );
        $phase    = self::read_json( __DIR__ . '/phase-b-pages.json' );
        $areas    = self::read_json( __DIR__ . '/service-areas-261.json' );
        $defaults = file_exists( __DIR__ . '/business-manager-defaults.json' ) ? self::read_json( __DIR__ . '/business-manager-defaults.json' ) : array();

        $errors  = array();
        $report_details = array(
            'successful'           => array(),
            'failed'               => array(),
            'pending'              => array(),
            'duplicates'           => array(),
            'missing_images'       => array(),
            'broken_relationships' => array(),
        );
        $summary = array(
            'media_synced'   => 0,
            'media_imported' => 0,
            'media_reused'   => 0,
            'settings'       => 'skipped',
            'menus_synced'   => 0,
            'cpts_synced'    => array(),
            'pages_synced'   => 0,
            'areas_synced'   => 0,
            'areas_offset'   => $offset,
            'areas_total'    => is_array( $areas ) && ! empty( $areas['routes'] ) ? count( $areas['routes'] ) : 0,
            'defaults_synced'=> array(),
        );

        foreach ( array( $seeds, $pilot, $phase, $areas ) as $check ) {
            if ( is_wp_error( $check ) ) {
                $errors[]                   = $check->get_error_message();
                $report_details['failed'][] = $check->get_error_message();
            }
        }
        if ( ! empty( $errors ) ) {
            return array(
                'ok'      => false,
                'dry_run' => $dry_run,
                'section' => $section,
                'summary' => $summary,
                'report'  => $report_details,
                'errors'  => $errors,
            );
        }

        $media_cache = array();
        $home_clean  = untrailingslashit( home_url( '/' ) );
        $tpl_dir     = dirname( __DIR__ ) . '/elementor-templates/';

        // Defer term counting during bulk writes for fast cPanel execution
        if ( ! $dry_run ) {
            wp_defer_term_counting( true );
        }

        // ------------------------------------------------------------------
        // 1. Media Library Sync (Always resolve URL + Alt map needed by later steps)
        // ------------------------------------------------------------------
        $run_media = in_array( $section, array( 'all', 'media', 'settings', 'cpts', 'pages', 'defaults' ), true );
        if ( $run_media ) {
            $do_upload_now = ( ! $dry_run && in_array( $section, array( 'all', 'media', 'cpts', 'pages', 'defaults' ), true ) );
            $media_report  = ST_Migration_Media_Sync::sync_all_verified_images( ! $do_upload_now );
            $media_cache   = $media_report['cache'];

            $summary['media_synced']   = count( $media_cache );
            $summary['media_imported'] = (int) $media_report['imported'];
            $summary['media_reused']   = (int) $media_report['duplicates_reused'];

            foreach ( $media_report['missing'] as $miss ) {
                $report_details['missing_images'][] = $miss;
            }
            foreach ( $media_report['failed'] as $fail ) {
                $errors[]                   = 'Media ' . $fail;
                $report_details['failed'][] = 'Media ' . $fail;
            }
            if ( $media_report['duplicates_reused'] > 0 ) {
                $report_details['duplicates'][] = $media_report['duplicates_reused'] . ' media assets already in Media Library were deduplicated & reused cleanly via _st_source_filename.';
            }
            $report_details['successful'][] = 'Media Library verified/synced: ' . count( $media_cache ) . ' assets (' . $media_report['imported'] . ' newly imported, ' . $media_report['duplicates_reused'] . ' reused, alt text populated).';
        }

        // ------------------------------------------------------------------
        // 2. Site Content Options, All 30 SITE_IMAGES Alt Keys & Elementor Kit Sync
        // ------------------------------------------------------------------
        if ( in_array( $section, array( 'all', 'settings' ), true ) && ! empty( $seeds['site_content'] ) ) {
            if ( $dry_run ) {
                $summary['settings'] = 'dry_run_ready';
            } else {
                $hydrated_content = self::hydrate_value( $seeds['site_content'], $media_cache );

                // Seed all 30 SITE_IMAGES defaultAlt strings into images.alt.* / section *Alt slots.
                if ( is_array( $defaults ) && ! empty( $defaults['site_images_registry'] ) ) {
                    foreach ( $defaults['site_images_registry'] as $slot ) {
                        if ( ! empty( $slot['altStorageKey'] ) && ! empty( $slot['defaultAlt'] ) ) {
                            self::set_by_dot_path( $hydrated_content, $slot['altStorageKey'], $slot['defaultAlt'], false );
                        }
                    }
                }

                $existing_content = get_option( 'santa_towing_site_content', array() );
                $merged_content   = is_array( $existing_content )
                    ? array_replace_recursive( $hydrated_content, $existing_content )
                    : $hydrated_content;

                update_option( 'santa_towing_site_content', $merged_content, true );

                if ( ! empty( $media_cache['santa-logo.png']['id'] ) ) {
                    set_theme_mod( 'custom_logo', (int) $media_cache['santa-logo.png']['id'] );
                }
                if ( class_exists( 'STWC_Settings' ) && method_exists( 'STWC_Settings', 'sync_elementor_kit_colors' ) && ! empty( $merged_content['theme'] ) ) {
                    STWC_Settings::sync_elementor_kit_colors( $merged_content['theme'] );
                }
                $summary['settings']            = 'updated';
                $report_details['successful'][] = 'Site content & all 30 image alt slots migrated into santa_towing_site_content.';
            }
        }

        // ------------------------------------------------------------------
        // 3. WordPress Navigation Menus (Primary + 3 Footer Columns)
        // ------------------------------------------------------------------
        if ( in_array( $section, array( 'all', 'menus' ), true ) && ! empty( $seeds['menus'] ) ) {
            if ( $dry_run ) {
                $summary['menus_synced'] = count( $seeds['menus'] );
            } else {
                $locations = get_theme_mod( 'nav_menu_locations', array() );
                if ( ! is_array( $locations ) ) {
                    $locations = array();
                }

                foreach ( $seeds['menus'] as $loc => $menu_def ) {
                    $menu_name = sanitize_text_field( $menu_def['name'] ?? $loc );
                    $menu_obj  = wp_get_nav_menu_object( $menu_name );
                    $menu_id   = $menu_obj ? (int) $menu_obj->term_id : (int) wp_create_nav_menu( $menu_name );

                    if ( is_wp_error( $menu_id ) || $menu_id <= 0 ) {
                        $errors[] = 'Menu create failed for ' . $loc;
                        continue;
                    }

                    // Replace menu items idempotently to mirror exact order & dropdown hierarchy
                    $existing_items = wp_get_nav_menu_items( $menu_id );
                    if ( is_array( $existing_items ) ) {
                        foreach ( $existing_items as $ei ) {
                            wp_delete_post( (int) $ei->ID, true );
                        }
                    }

                    $pos = 1;
                    foreach ( (array) ( $menu_def['items'] ?? array() ) as $item ) {
                        $url       = home_url( '/' . ltrim( (string) ( $item['path'] ?? '/' ), '/' ) );
                        $parent_id = wp_update_nav_menu_item(
                            $menu_id,
                            0,
                            array(
                                'menu-item-title'    => sanitize_text_field( $item['title'] ),
                                'menu-item-url'      => esc_url_raw( $url ),
                                'menu-item-type'     => 'custom',
                                'menu-item-status'   => 'publish',
                                'menu-item-position' => $pos++,
                            )
                        );

                        if ( ! is_wp_error( $parent_id ) && ! empty( $item['children'] ) && is_array( $item['children'] ) ) {
                            foreach ( $item['children'] as $child ) {
                                $child_url = home_url( '/' . ltrim( (string) ( $child['path'] ?? '/' ), '/' ) );
                                wp_update_nav_menu_item(
                                    $menu_id,
                                    0,
                                    array(
                                        'menu-item-title'     => sanitize_text_field( $child['title'] ),
                                        'menu-item-url'       => esc_url_raw( $child_url ),
                                        'menu-item-type'      => 'custom',
                                        'menu-item-status'    => 'publish',
                                        'menu-item-parent-id' => (int) $parent_id,
                                        'menu-item-position'  => $pos++,
                                    )
                                );
                            }
                        }
                    }

                    $locations[ $loc ] = $menu_id;
                    $summary['menus_synced']++;
                }

                set_theme_mod( 'nav_menu_locations', $locations );
            }
        }

        // ------------------------------------------------------------------
        // 4. CPT & Blog Seeds (12 Cars, 6 Rentals, 6 Parts, 6 Posts, 28 FAQs, 8 Gallery, 4 Careers)
        // ------------------------------------------------------------------
        if ( in_array( $section, array( 'all', 'cpts' ), true ) ) {
            // 4a. Cars for Sale (`st_car`)
            $summary['cpts_synced']['st_car'] = self::import_cars( $seeds['st_car'] ?? array(), $media_cache, $dry_run, $errors );

            // 4b. Rental Fleet (`st_rental`)
            $summary['cpts_synced']['st_rental'] = self::import_rentals( $seeds['st_rental'] ?? array(), $media_cache, $dry_run, $errors );

            // 4c. Auto Parts (`st_part`)
            $summary['cpts_synced']['st_part'] = self::import_parts( $seeds['st_part'] ?? array(), $media_cache, $dry_run, $errors );

            // 4d. Blog Posts (`post`)
            $summary['cpts_synced']['post'] = self::import_blog_posts( $seeds['post'] ?? array(), $media_cache, $dry_run, $errors );

            // 4e. FAQs (`st_faq`)
            $summary['cpts_synced']['st_faq'] = self::import_faqs( $seeds['st_faq'] ?? array(), $dry_run, $errors );

            // 4f. Gallery Items (`st_gallery`)
            $summary['cpts_synced']['st_gallery'] = self::import_gallery( $seeds['st_gallery'] ?? array(), $media_cache, $dry_run, $errors );

            // 4g. Careers (`st_career`)
            $summary['cpts_synced']['st_career'] = self::import_careers( $seeds['st_career'] ?? array(), $dry_run, $errors );
        }

        // ------------------------------------------------------------------
        // 5. Pages: Pilot (4 non-area pages) + Phase B (22 Marketing/Utility/Policy pages)
        // ------------------------------------------------------------------
        if ( in_array( $section, array( 'all', 'pages' ), true ) ) {
            $all_pages = array();
            foreach ( (array) ( $pilot['pilot_routes'] ?? array() ) as $pr ) {
                if ( 'page' === ( $pr['post_type'] ?? '' ) ) {
                    $all_pages[] = $pr;
                }
            }
            foreach ( (array) ( $phase['pages'] ?? array() ) as $pb ) {
                $all_pages[] = $pb;
            }

            foreach ( $all_pages as $pg ) {
                $slug = sanitize_title( $pg['post_name'] );
                $existing_id = self::find_existing_post_id( 'page', $slug, null );

                if ( $dry_run ) {
                    $summary['pages_synced']++;
                    continue;
                }

                $raw_content = isset( $pg['post_content'] ) ? $pg['post_content'] : ( $pg['post_content_fallback'] ?? '' );
                $raw_content = str_replace( '__ST_HOME_URL__', $home_clean, (string) $raw_content );

                $postarr = array(
                    'post_type'    => 'page',
                    'post_status'  => sanitize_key( $pg['post_status'] ?? 'publish' ),
                    'post_title'   => wp_strip_all_tags( $pg['post_title'] ),
                    'post_name'    => $slug,
                    'post_content' => wp_kses_post( $raw_content ),
                    'post_excerpt' => ! empty( $pg['seo']['description'] ) ? wp_strip_all_tags( $pg['seo']['description'] ) : '',
                );

                if ( $existing_id > 0 ) {
                    $postarr['ID'] = $existing_id;
                    $pid           = wp_update_post( $postarr, true );
                } else {
                    $pid = wp_insert_post( $postarr, true );
                }

                if ( is_wp_error( $pid ) ) {
                    $errors[] = 'Page ' . $slug . ': ' . $pid->get_error_message();
                    continue;
                }

                if ( isset( $pg['wp_template'] ) ) {
                    if ( '' !== $pg['wp_template'] ) {
                        update_post_meta( $pid, '_wp_page_template', sanitize_text_field( $pg['wp_template'] ) );
                    } else {
                        delete_post_meta( $pid, '_wp_page_template' );
                    }
                }

                // SEO & Policy Meta
                if ( ! empty( $pg['seo']['title'] ) ) {
                    update_post_meta( $pid, '_st_seo_title', sanitize_text_field( $pg['seo']['title'] ) );
                }
                if ( ! empty( $pg['seo']['description'] ) ) {
                    update_post_meta( $pid, '_st_seo_description', sanitize_textarea_field( $pg['seo']['description'] ) );
                }
                if ( ! empty( $pg['seo']['keywords'] ) && is_array( $pg['seo']['keywords'] ) ) {
                    update_post_meta( $pid, '_st_keywords_json', wp_json_encode( array_values( $pg['seo']['keywords'] ) ) );
                }
                if ( ! empty( $pg['meta'] ) && is_array( $pg['meta'] ) ) {
                    foreach ( $pg['meta'] as $mkey => $mval ) {
                        update_post_meta( $pid, sanitize_key( $mkey ), is_string( $mval ) ? $mval : wp_json_encode( $mval ) );
                    }
                }

                // Elementor Free Template Injection
                $use_elementor = ! isset( $pg['use_elementor_on_import'] ) || (bool) $pg['use_elementor_on_import'];
                if ( $use_elementor && ! empty( $pg['elementor_template'] ) ) {
                    $tpl_path = $tpl_dir . basename( $pg['elementor_template'] );
                    if ( file_exists( $tpl_path ) ) {
                        $tpl_json = json_decode( (string) file_get_contents( $tpl_path ), true );
                        if ( is_array( $tpl_json ) && isset( $tpl_json['content'] ) ) {
                            $hydrated = self::hydrate_value( $tpl_json['content'], $media_cache );
                            update_post_meta( $pid, '_elementor_data', wp_slash( wp_json_encode( $hydrated ) ) );
                            update_post_meta( $pid, '_elementor_edit_mode', 'builder' );
                            update_post_meta( $pid, '_elementor_template_type', 'wp-page' );
                            update_post_meta( $pid, '_elementor_version', '3.24.0' );
                            if ( ! empty( $tpl_json['page_settings'] ) ) {
                                update_post_meta( $pid, '_elementor_page_settings', $tpl_json['page_settings'] );
                            }
                        }
                    }
                } else {
                    delete_post_meta( $pid, '_elementor_edit_mode' );
                }

                // Reading Settings Assignment (Home & Blog)
                if ( ! empty( $pg['set_as_front_page'] ) ) {
                    update_option( 'show_on_front', 'page' );
                    update_option( 'page_on_front', (int) $pid );
                }
                if ( ! empty( $pg['set_as_posts_page'] ) ) {
                    update_option( 'page_for_posts', (int) $pid );
                }

                $summary['pages_synced']++;
            }
        }

        // ------------------------------------------------------------------
        // 6. All 261 Service Area Pages (`st_service_area` CPT Generator)
        // ------------------------------------------------------------------
        if ( in_array( $section, array( 'all', 'areas' ), true ) && ! empty( $areas['routes'] ) ) {
            if ( ! post_type_exists( 'st_service_area' ) ) {
                $errors[] = 'Post type st_service_area is not registered. Activate "Santa Towing Website Core" first.';
            } else {
                $area_tpl_json = null;
                if ( $areas_with_elementor && file_exists( $tpl_dir . 'service-area-single-template.json' ) ) {
                    $area_tpl_json = json_decode( (string) file_get_contents( $tpl_dir . 'service-area-single-template.json' ), true );
                }

                $slice = array_slice( $areas['routes'], $offset, $limit );
                foreach ( $slice as $route ) {
                    $slug = sanitize_title( $route['slug'] );
                    if ( '' === $slug ) {
                        continue;
                    }

                    if ( $dry_run ) {
                        $summary['areas_synced']++;
                        continue;
                    }

                    $existing_id = self::find_existing_post_id( 'st_service_area', $slug, null );
                    $postarr     = array(
                        'post_type'    => 'st_service_area',
                        'post_status'  => 'publish',
                        'post_title'   => wp_strip_all_tags( $route['heading'] ),
                        'post_name'    => $slug,
                        'post_content' => wp_kses_post( $route['description'] ),
                        'post_excerpt' => wp_strip_all_tags( $route['seo_description'] ?? $route['description'] ),
                    );

                    if ( $existing_id > 0 ) {
                        $postarr['ID'] = $existing_id;
                        $aid           = wp_update_post( $postarr, true );
                    } else {
                        $aid = wp_insert_post( $postarr, true );
                    }

                    if ( is_wp_error( $aid ) ) {
                        $errors[] = 'Service Area ' . $slug . ': ' . $aid->get_error_message();
                        continue;
                    }

                    update_post_meta( $aid, '_st_area_label', sanitize_text_field( $route['area_label'] ) );
                    update_post_meta( $aid, '_st_origin', sanitize_text_field( $route['origin'] ?? '' ) );
                    update_post_meta( $aid, '_st_destination', sanitize_text_field( $route['destination'] ?? '' ) );
                    update_post_meta( $aid, '_st_heading', sanitize_text_field( $route['heading'] ) );
                    update_post_meta( $aid, '_st_description', wp_kses_post( $route['description'] ) );
                    update_post_meta( $aid, '_st_seo_title', sanitize_text_field( $route['seo_title'] ) );
                    update_post_meta( $aid, '_st_seo_description', sanitize_textarea_field( $route['seo_description'] ) );
                    update_post_meta( $aid, '_st_keywords_json', wp_json_encode( array_values( (array) ( $route['keywords'] ?? array() ) ) ) );
                    update_post_meta( $aid, '_st_prev_path', sanitize_text_field( $route['prev_path'] ?? '' ) );
                    update_post_meta( $aid, '_st_prev_label', sanitize_text_field( $route['prev_label'] ?? '' ) );
                    update_post_meta( $aid, '_st_next_path', sanitize_text_field( $route['next_path'] ?? '' ) );
                    update_post_meta( $aid, '_st_next_label', sanitize_text_field( $route['next_label'] ?? '' ) );

                    if ( ! empty( $route['region'] ) && taxonomy_exists( 'st_region' ) ) {
                        wp_set_object_terms( $aid, array( sanitize_text_field( $route['region'] ) ), 'st_region', false );
                    }

                    if ( is_array( $area_tpl_json ) && isset( $area_tpl_json['content'] ) ) {
                        update_post_meta( $aid, '_elementor_data', wp_slash( wp_json_encode( $area_tpl_json['content'] ) ) );
                        update_post_meta( $aid, '_elementor_edit_mode', 'builder' );
                        update_post_meta( $aid, '_elementor_template_type', 'wp-post' );
                        update_post_meta( $aid, '_elementor_version', '3.24.0' );
                        if ( ! empty( $area_tpl_json['page_settings'] ) ) {
                            update_post_meta( $aid, '_elementor_page_settings', $area_tpl_json['page_settings'] );
                        }
                    }

                    $summary['areas_synced']++;
                }
            }
        }

        // ------------------------------------------------------------------
        // 7. Data Files & Business Manager Defaults (Adverts, Regions, Billing & Studio Seeds)
        // ------------------------------------------------------------------
        if ( in_array( $section, array( 'all', 'defaults' ), true ) && is_array( $defaults ) && ! empty( $defaults ) ) {
            $summary['defaults_synced'] = self::import_defaults_and_bm( $defaults, $media_cache, $dry_run, $errors, $report_details );
        }

        if ( ! $dry_run ) {
            wp_defer_term_counting( false );
            flush_rewrite_rules( false );
        }

        $report_details['pending'][] = 'Browser runtime localStorage / IndexedDB data (if any live browser edits exist) must be manually exported via legacy-browser-export-snippet.js and uploaded in Santa Manager -> Backup & Export.';

        return array(
            'ok'      => empty( $errors ),
            'dry_run' => $dry_run,
            'section' => $section,
            'summary' => $summary,
            'report'  => $report_details,
            'errors'  => $errors,
        );
    }

    /**
     * Write nested value onto array by dot path.
     */
    private static function set_by_dot_path( &$target, $path, $value, $overwrite = true ) {
        $parts = explode( '.', (string) $path );
        $curr  =& $target;
        $len   = count( $parts );
        for ( $i = 0; $i < $len - 1; $i++ ) {
            $part = $parts[ $i ];
            if ( ! isset( $curr[ $part ] ) || ! is_array( $curr[ $part ] ) ) {
                $curr[ $part ] = array();
            }
            $curr =& $curr[ $part ];
        }
        $last = $parts[ $len - 1 ];
        if ( $overwrite || ! isset( $curr[ $last ] ) || '' === $curr[ $last ] ) {
            $curr[ $last ] = $value;
        }
    }

    /**
     * Import Data Files (`towingAdverts.ts`, `ghanaLocations.ts`, `mockData.ts`) and Business Manager Seed Defaults.
     */
    private static function import_defaults_and_bm( $defaults, $media_cache, $dry_run, &$errors, &$report_details ) {
        $counts = array(
            'towing_adverts' => count( (array) ( $defaults['towing_adverts'] ?? array() ) ),
            'ghana_regions'  => count( (array) ( $defaults['ghana_regions'] ?? array() ) ),
            'doc_settings'   => ! empty( $defaults['doc_settings'] ) ? 1 : 0,
            'customers'      => 0,
            'invoices'       => 0,
            'receipts'       => 0,
            'studio_images'  => 0,
        );

        if ( $dry_run ) {
            $counts['customers']     = count( (array) ( $defaults['customers'] ?? array() ) );
            $counts['invoices']      = count( (array) ( $defaults['invoices'] ?? array() ) );
            $counts['receipts']      = count( (array) ( $defaults['receipts'] ?? array() ) );
            $counts['studio_images'] = count( (array) ( $defaults['studio_images'] ?? array() ) );
            return $counts;
        }

        if ( ! empty( $defaults['towing_adverts'] ) ) {
            update_option( 'stwc_towing_adverts', $defaults['towing_adverts'], false );
        }
        if ( ! empty( $defaults['ghana_regions'] ) ) {
            update_option( 'stwc_ghana_locations', $defaults['ghana_regions'], false );
        }
        if ( ! empty( $defaults['mock_highlights'] ) ) {
            update_option( 'stwc_mock_highlights', $defaults['mock_highlights'], false );
        }

        // Sync Business Manager Document Settings cleanly without clobbering existing customizations.
        if ( ! empty( $defaults['doc_settings'] ) ) {
            $doc = $defaults['doc_settings'];
            if ( ! empty( $doc['logoFilename'] ) && ! empty( $media_cache[ $doc['logoFilename'] ]['url'] ) ) {
                $doc['logoUrl'] = $media_cache[ $doc['logoFilename'] ]['url'];
            }
            unset( $doc['logoFilename'] );
            $existing_doc = get_option( 'stbm_doc_settings_v1', array() );
            $merged_doc   = is_array( $existing_doc ) ? array_merge( $doc, $existing_doc ) : $doc;
            update_option( 'stbm_doc_settings_v1', $merged_doc, false );
        }

        // Seed Business Manager CRM / Billing / Studio DB tables if plugin is active.
        if ( class_exists( 'STBM_DB' ) ) {
            STBM_DB::create_tables();
            foreach ( (array) ( $defaults['customers'] ?? array() ) as $c ) {
                $res = STBM_DB::save_customer( $c, false );
                if ( is_wp_error( $res ) ) {
                    $errors[] = 'BM Customer ' . ( $c['uid'] ?? '' ) . ': ' . $res->get_error_message();
                } else {
                    $counts['customers']++;
                }
            }
            foreach ( (array) ( $defaults['invoices'] ?? array() ) as $inv ) {
                $res = STBM_DB::save_invoice( $inv, false );
                if ( is_wp_error( $res ) ) {
                    $errors[] = 'BM Invoice ' . ( $inv['docNumber'] ?? '' ) . ': ' . $res->get_error_message();
                } else {
                    $counts['invoices']++;
                }
            }
            foreach ( (array) ( $defaults['receipts'] ?? array() ) as $rec ) {
                $res = STBM_DB::save_receipt( $rec, false );
                if ( is_wp_error( $res ) ) {
                    $errors[] = 'BM Receipt ' . ( $rec['receiptNumber'] ?? '' ) . ': ' . $res->get_error_message();
                } else {
                    $counts['receipts']++;
                }
            }
            foreach ( (array) ( $defaults['studio_images'] ?? array() ) as $st ) {
                $fn = $st['filename'] ?? '';
                if ( $fn && ! empty( $media_cache[ $fn ] ) ) {
                    $st['attachmentId'] = (int) ( $media_cache[ $fn ]['id'] ?? 0 );
                    $st['imageUrl']     = (string) ( $media_cache[ $fn ]['url'] ?? '' );
                }
                $st['id'] = $st['uid'] ?? '';
                $res      = STBM_DB::save_studio_image( $st );
                if ( is_wp_error( $res ) ) {
                    $errors[] = 'BM Studio Image ' . ( $st['uid'] ?? '' ) . ': ' . $res->get_error_message();
                } else {
                    $counts['studio_images']++;
                }
            }
            $report_details['successful'][] = "Business Manager defaults imported ({$counts['customers']} customers, {$counts['invoices']} invoices, {$counts['receipts']} receipts, {$counts['studio_images']} studio images).";
        } else {
            $report_details['pending'][] = 'Business Manager tables (wp_stbm_*) deferred: activate Santa Towing Business Manager plugin, then re-run Scope = "defaults" or plugin activation will seed them cleanly.';
        }

        return $counts;
    }

    /**
     * Import 12 Cars for Sale (`st_car`).
     */
    private static function import_cars( $items, $media_cache, $dry_run, &$errors ) {
        if ( ! post_type_exists( 'st_car' ) ) {
            $errors[] = 'CPT st_car not registered.';
            return 0;
        }
        $count = 0;
        foreach ( $items as $car ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $car['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $car['slug'] );
            $existing_id = self::find_existing_post_id( 'st_car', $slug, $legacy_id );

            $postarr = array(
                'post_type'    => 'st_car',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $car['title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $car['description'] ?? '' ),
                'post_excerpt' => wp_strip_all_tags( $car['description'] ?? '' ),
                'menu_order'   => (int) ( $car['menu_order'] ?? $legacy_id ),
            );
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'Car ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            if ( ! empty( $car['image'] ) && ! empty( $media_cache[ $car['image'] ] ) ) {
                if ( ! empty( $media_cache[ $car['image'] ]['id'] ) ) {
                    set_post_thumbnail( $pid, (int) $media_cache[ $car['image'] ]['id'] );
                }
                if ( ! empty( $media_cache[ $car['image'] ]['url'] ) ) {
                    update_post_meta( $pid, '_st_image_url', esc_url_raw( $media_cache[ $car['image'] ]['url'] ) );
                }
            }

            $gallery_urls = array();
            foreach ( (array) ( $car['gallery'] ?? array() ) as $gf ) {
                if ( ! empty( $media_cache[ $gf ]['url'] ) ) {
                    $gallery_urls[] = $media_cache[ $gf ]['url'];
                }
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_car_type', sanitize_text_field( $car['car_type'] ?? 'New' ) );
            update_post_meta( $pid, '_st_price', sanitize_text_field( $car['price'] ?? '' ) );
            update_post_meta( $pid, '_st_year', sanitize_text_field( $car['year'] ?? '2024' ) );
            update_post_meta( $pid, '_st_fuel', sanitize_text_field( $car['fuel'] ?? 'Petrol' ) );
            update_post_meta( $pid, '_st_transmission', sanitize_text_field( $car['transmission'] ?? 'Automatic' ) );
            update_post_meta( $pid, '_st_mileage', sanitize_text_field( $car['mileage'] ?? '0 km' ) );
            update_post_meta( $pid, '_st_status', sanitize_text_field( $car['status'] ?? 'Available' ) );
            update_post_meta( $pid, '_st_featured', sanitize_text_field( $car['featured'] ?? 'no' ) );
            update_post_meta( $pid, '_st_gallery', $gallery_urls );
            update_post_meta( $pid, '_st_sort_order', (int) ( $car['menu_order'] ?? $legacy_id ) );
            update_post_meta( $pid, '_st_hidden', '0' );
            if ( ! empty( $car['specs'] ) && is_array( $car['specs'] ) ) {
                update_post_meta( $pid, '_st_specs', $car['specs'] );
            }
            update_post_meta( $pid, '_st_seo_title', sanitize_text_field( ! empty( $car['seo_title'] ) ? $car['seo_title'] : ( $car['title'] . ' for Sale | Santa Towing' ) ) );
            update_post_meta( $pid, '_st_seo_description', sanitize_textarea_field( ! empty( $car['seo_description'] ) ? $car['seo_description'] : ( $car['description'] ?? '' ) ) );

            if ( ! empty( $car['car_type'] ) && taxonomy_exists( 'st_car_category' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $car['car_type'] ) ), 'st_car_category', false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Import 6 Rental Vehicles (`st_rental`).
     */
    private static function import_rentals( $items, $media_cache, $dry_run, &$errors ) {
        if ( ! post_type_exists( 'st_rental' ) ) {
            $errors[] = 'CPT st_rental not registered.';
            return 0;
        }
        $count = 0;
        foreach ( $items as $rent ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $rent['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $rent['slug'] );
            $existing_id = self::find_existing_post_id( 'st_rental', $slug, $legacy_id );

            $postarr = array(
                'post_type'    => 'st_rental',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $rent['title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $rent['description'] ?? '' ),
                'post_excerpt' => wp_strip_all_tags( $rent['description'] ?? '' ),
                'menu_order'   => (int) ( $rent['menu_order'] ?? $legacy_id ),
            );
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'Rental ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            if ( ! empty( $rent['image'] ) && ! empty( $media_cache[ $rent['image'] ] ) ) {
                if ( ! empty( $media_cache[ $rent['image'] ]['id'] ) ) {
                    set_post_thumbnail( $pid, (int) $media_cache[ $rent['image'] ]['id'] );
                }
                if ( ! empty( $media_cache[ $rent['image'] ]['url'] ) ) {
                    update_post_meta( $pid, '_st_image_url', esc_url_raw( $media_cache[ $rent['image'] ]['url'] ) );
                }
            }

            $gallery_urls = array();
            foreach ( (array) ( $rent['gallery'] ?? array() ) as $gf ) {
                if ( ! empty( $media_cache[ $gf ]['url'] ) ) {
                    $gallery_urls[] = $media_cache[ $gf ]['url'];
                }
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_rental_type', sanitize_text_field( $rent['rental_type'] ?? 'Sedan' ) );
            update_post_meta( $pid, '_st_daily_rate', sanitize_text_field( $rent['daily_rate'] ?? '' ) );
            update_post_meta( $pid, '_st_weekly_rate', sanitize_text_field( $rent['weekly_rate'] ?? '' ) );
            update_post_meta( $pid, '_st_monthly_rate', sanitize_text_field( $rent['monthly_rate'] ?? '' ) );
            update_post_meta( $pid, '_st_seats', sanitize_text_field( $rent['seats'] ?? '5 Seats' ) );
            update_post_meta( $pid, '_st_transmission', sanitize_text_field( $rent['transmission'] ?? 'Automatic' ) );
            update_post_meta( $pid, '_st_fuel', sanitize_text_field( $rent['fuel'] ?? 'Petrol' ) );
            update_post_meta( $pid, '_st_status', sanitize_text_field( $rent['status'] ?? 'Available' ) );
            update_post_meta( $pid, '_st_gallery', $gallery_urls );
            update_post_meta( $pid, '_st_sort_order', (int) ( $rent['menu_order'] ?? $legacy_id ) );
            update_post_meta( $pid, '_st_hidden', '0' );
            if ( ! empty( $rent['features'] ) && is_array( $rent['features'] ) ) {
                $clean_features = array_map( 'sanitize_text_field', $rent['features'] );
                update_post_meta( $pid, '_st_rental_features', $clean_features );
                update_post_meta( $pid, '_st_features_json', wp_json_encode( $clean_features ) );
            }
            update_post_meta( $pid, '_st_seo_title', sanitize_text_field( ! empty( $rent['seo_title'] ) ? $rent['seo_title'] : ( $rent['title'] . ' Rental in Ghana | Santa Towing' ) ) );
            update_post_meta( $pid, '_st_seo_description', sanitize_textarea_field( ! empty( $rent['seo_description'] ) ? $rent['seo_description'] : ( $rent['description'] ?? '' ) ) );

            $cat = $rent['category'] ?? ( $rent['rental_type'] ?? '' );
            if ( $cat && taxonomy_exists( 'st_rental_category' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $cat ) ), 'st_rental_category', false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Import 6 Auto Parts (`st_part`).
     */
    private static function import_parts( $items, $media_cache, $dry_run, &$errors ) {
        if ( ! post_type_exists( 'st_part' ) ) {
            $errors[] = 'CPT st_part not registered.';
            return 0;
        }
        $count = 0;
        foreach ( $items as $part ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $part['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $part['slug'] );
            $existing_id = self::find_existing_post_id( 'st_part', $slug, $legacy_id );

            $desc    = $part['description'] ?? ( $part['compatible_models'] ?? '' );
            $postarr = array(
                'post_type'    => 'st_part',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $part['title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $desc ),
                'post_excerpt' => wp_strip_all_tags( $desc ),
            );
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'Part ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            if ( ! empty( $part['image'] ) && ! empty( $media_cache[ $part['image'] ] ) ) {
                if ( ! empty( $media_cache[ $part['image'] ]['id'] ) ) {
                    set_post_thumbnail( $pid, (int) $media_cache[ $part['image'] ]['id'] );
                }
                if ( ! empty( $media_cache[ $part['image'] ]['url'] ) ) {
                    update_post_meta( $pid, '_st_image_url', esc_url_raw( $media_cache[ $part['image'] ]['url'] ) );
                }
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_sort_order', $legacy_id );
            update_post_meta( $pid, '_st_hidden', '0' );
            update_post_meta( $pid, '_st_brand', sanitize_text_field( $part['brand'] ?? 'OEM Genuine' ) );
            update_post_meta( $pid, '_st_category', sanitize_text_field( $part['category'] ?? 'Auto Parts' ) );
            update_post_meta( $pid, '_st_price', sanitize_text_field( $part['price'] ?? '' ) );
            update_post_meta( $pid, '_st_sku', sanitize_text_field( $part['sku'] ?? '' ) );
            update_post_meta( $pid, '_st_rating', sanitize_text_field( $part['rating'] ?? '5' ) );
            update_post_meta( $pid, '_st_reviews', sanitize_text_field( $part['reviews'] ?? '42' ) );
            update_post_meta( $pid, '_st_in_stock', sanitize_text_field( $part['in_stock'] ?? '1' ) );
            update_post_meta( $pid, '_st_stock_status', sanitize_text_field( $part['stock_status'] ?? 'In Stock' ) );
            update_post_meta( $pid, '_st_warranty', sanitize_text_field( $part['warranty'] ?? '' ) );
            update_post_meta( $pid, '_st_compatible_models', sanitize_textarea_field( $part['compatible_models'] ?? '' ) );
            update_post_meta( $pid, '_st_installation', sanitize_textarea_field( $part['installation'] ?? '' ) );
            if ( ! empty( $part['specs'] ) ) {
                update_post_meta( $pid, '_st_specs_json', wp_json_encode( $part['specs'] ) );
            }
            update_post_meta( $pid, '_st_seo_title', sanitize_text_field( ! empty( $part['seo_title'] ) ? $part['seo_title'] : ( $part['title'] . ' (' . ( $part['sku'] ?? '' ) . ') | Santa Towing' ) ) );
            update_post_meta( $pid, '_st_seo_description', sanitize_textarea_field( ! empty( $part['seo_description'] ) ? $part['seo_description'] : $desc ) );

            if ( ! empty( $part['category'] ) && taxonomy_exists( 'st_part_category' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $part['category'] ) ), 'st_part_category', false );
            }
            if ( ! empty( $part['brand'] ) && taxonomy_exists( 'st_part_brand' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $part['brand'] ) ), 'st_part_brand', false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Import 6 Blog Posts (`post`).
     */
    private static function import_blog_posts( $items, $media_cache, $dry_run, &$errors ) {
        $count = 0;
        foreach ( $items as $bp ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $bp['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $bp['slug'] );
            $existing_id = self::find_existing_post_id( 'post', $slug, $legacy_id );

            $postarr = array(
                'post_type'    => 'post',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $bp['title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $bp['content'] ?? '' ),
                'post_excerpt' => wp_strip_all_tags( $bp['excerpt'] ?? '' ),
            );
            if ( ! empty( $bp['date_gmt'] ) ) {
                $postarr['post_date_gmt'] = sanitize_text_field( $bp['date_gmt'] );
                $postarr['post_date']     = get_date_from_gmt( $postarr['post_date_gmt'] );
            }
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'Blog ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            if ( ! empty( $bp['image'] ) && ! empty( $media_cache[ $bp['image'] ]['id'] ) ) {
                set_post_thumbnail( $pid, (int) $media_cache[ $bp['image'] ]['id'] );
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_read_time', sanitize_text_field( $bp['read_time'] ?? '5 min read' ) );
            update_post_meta( $pid, '_st_author', sanitize_text_field( $bp['author'] ?? 'Santa Editorial Team' ) );
            if ( ! empty( $bp['author_img'] ) && ! empty( $media_cache[ $bp['author_img'] ]['url'] ) ) {
                update_post_meta( $pid, '_st_author_img', esc_url_raw( $media_cache[ $bp['author_img'] ]['url'] ) );
            }
            update_post_meta( $pid, '_st_seo_title', sanitize_text_field( ! empty( $bp['seo_title'] ) ? $bp['seo_title'] : ( $bp['title'] . ' | Santa Towing Blog' ) ) );
            update_post_meta( $pid, '_st_seo_description', sanitize_textarea_field( ! empty( $bp['seo_description'] ) ? $bp['seo_description'] : ( $bp['excerpt'] ?? '' ) ) );

            if ( ! empty( $bp['category'] ) ) {
                $term = term_exists( $bp['category'], 'category' );
                if ( ! $term ) {
                    $term = wp_insert_term( sanitize_text_field( $bp['category'] ), 'category' );
                }
                if ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) {
                    wp_set_post_categories( $pid, array( (int) $term['term_id'] ), false );
                }
            }
            if ( ! empty( $bp['tags'] ) && is_array( $bp['tags'] ) ) {
                wp_set_post_tags( $pid, array_map( 'sanitize_text_field', $bp['tags'] ), false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Import 28 FAQs (`st_faq`).
     */
    private static function import_faqs( $items, $dry_run, &$errors ) {
        if ( ! post_type_exists( 'st_faq' ) ) {
            $errors[] = 'CPT st_faq not registered.';
            return 0;
        }
        $count = 0;
        foreach ( $items as $faq ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $faq['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $faq['slug'] );
            $existing_id = self::find_existing_post_id( 'st_faq', $slug, $legacy_id );

            $postarr = array(
                'post_type'    => 'st_faq',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $faq['question'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $faq['answer'] ),
                'menu_order'   => (int) ( $faq['sort_order'] ?? $legacy_id ),
            );
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'FAQ ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_sort_order', (int) ( $faq['sort_order'] ?? $legacy_id ) );
            if ( ! empty( $faq['category'] ) && taxonomy_exists( 'st_faq_category' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $faq['category'] ) ), 'st_faq_category', false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Import 8 Gallery Showcase Items (`st_gallery`).
     */
    private static function import_gallery( $items, $media_cache, $dry_run, &$errors ) {
        if ( ! post_type_exists( 'st_gallery' ) ) {
            $errors[] = 'CPT st_gallery not registered.';
            return 0;
        }
        $count = 0;
        foreach ( $items as $gal ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $gal['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $gal['slug'] );
            $existing_id = self::find_existing_post_id( 'st_gallery', $slug, $legacy_id );

            $postarr = array(
                'post_type'    => 'st_gallery',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $gal['title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $gal['description'] ?? '' ),
                'menu_order'   => (int) ( $gal['menu_order'] ?? $legacy_id ),
            );
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'Gallery ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            if ( ! empty( $gal['image'] ) && ! empty( $media_cache[ $gal['image'] ] ) ) {
                if ( ! empty( $media_cache[ $gal['image'] ]['id'] ) ) {
                    set_post_thumbnail( $pid, (int) $media_cache[ $gal['image'] ]['id'] );
                }
                if ( ! empty( $media_cache[ $gal['image'] ]['url'] ) ) {
                    update_post_meta( $pid, '_st_image_url', esc_url_raw( $media_cache[ $gal['image'] ]['url'] ) );
                }
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_sort_order', (int) ( $gal['menu_order'] ?? $legacy_id ) );
            update_post_meta( $pid, '_st_hidden', '0' );
            update_post_meta( $pid, '_st_span', sanitize_text_field( $gal['span'] ?? 'col-span-1 row-span-1' ) );
            update_post_meta( $pid, '_st_media_type', sanitize_text_field( $gal['media_type'] ?? 'image' ) );
            update_post_meta( $pid, '_st_location', sanitize_text_field( $gal['location'] ?? 'Ghana' ) );
            if ( ! empty( $gal['alt'] ) ) {
                $clean_alt = sanitize_text_field( $gal['alt'] );
                update_post_meta( $pid, '_st_alt', $clean_alt );
                update_post_meta( $pid, '_st_alt_text', $clean_alt );
            }
            if ( ! empty( $gal['category'] ) && taxonomy_exists( 'st_gallery_category' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $gal['category'] ) ), 'st_gallery_category', false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Import 4 Career Job Openings (`st_career`).
     */
    private static function import_careers( $items, $dry_run, &$errors ) {
        if ( ! post_type_exists( 'st_career' ) ) {
            $errors[] = 'CPT st_career not registered.';
            return 0;
        }
        $count = 0;
        foreach ( $items as $job ) {
            if ( $dry_run ) {
                $count++;
                continue;
            }
            $legacy_id   = (int) ( $job['legacy_id'] ?? 0 );
            $slug        = sanitize_title( $job['slug'] );
            $existing_id = self::find_existing_post_id( 'st_career', $slug, $legacy_id );

            $postarr = array(
                'post_type'    => 'st_career',
                'post_status'  => 'publish',
                'post_title'   => wp_strip_all_tags( $job['title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $job['description'] ?? '' ),
            );
            if ( $existing_id > 0 ) {
                $postarr['ID'] = $existing_id;
                $pid           = wp_update_post( $postarr, true );
            } else {
                $pid = wp_insert_post( $postarr, true );
            }
            if ( is_wp_error( $pid ) ) {
                $errors[] = 'Career ' . $slug . ': ' . $pid->get_error_message();
                continue;
            }

            update_post_meta( $pid, '_st_legacy_id', $legacy_id );
            update_post_meta( $pid, '_st_location', sanitize_text_field( $job['location'] ?? 'Accra Main Workshop' ) );
            update_post_meta( $pid, '_st_employment_type', sanitize_text_field( $job['employment_type'] ?? 'Full-time' ) );
            update_post_meta( $pid, '_st_responsibilities_json', wp_json_encode( (array) ( $job['responsibilities'] ?? array() ) ) );
            update_post_meta( $pid, '_st_requirements_json', wp_json_encode( (array) ( $job['requirements'] ?? array() ) ) );
            update_post_meta( $pid, '_st_benefits_json', wp_json_encode( (array) ( $job['benefits'] ?? array() ) ) );
            update_post_meta( $pid, '_st_seo_title', sanitize_text_field( ! empty( $job['seo_title'] ) ? $job['seo_title'] : ( $job['title'] . ' | Santa Towing Careers' ) ) );
            update_post_meta( $pid, '_st_seo_description', sanitize_textarea_field( ! empty( $job['seo_description'] ) ? $job['seo_description'] : ( $job['description'] ?? '' ) ) );

            if ( ! empty( $job['department'] ) && taxonomy_exists( 'st_department' ) ) {
                wp_set_object_terms( $pid, array( sanitize_text_field( $job['department'] ) ), 'st_department', false );
            }
            $count++;
        }
        return $count;
    }

    /**
     * Render WP-Admin Tools Page with one-click full import & chunked cPanel service-area controls.
     */
    public static function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'santa-towing' ) );
        }

        $report = null;
        if ( isset( $_POST['st_full_section'] ) && check_admin_referer( 'st_full_import_nonce' ) ) {
            $report = self::run(
                array(
                    'section'              => sanitize_key( wp_unslash( $_POST['st_full_section'] ) ),
                    'offset'               => isset( $_POST['st_area_offset'] ) ? absint( $_POST['st_area_offset'] ) : 0,
                    'limit'                => isset( $_POST['st_area_limit'] ) ? absint( $_POST['st_area_limit'] ) : 261,
                    'dry_run'              => ! empty( $_POST['st_dry_run'] ),
                    'areas_with_elementor' => true,
                )
            );
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Santa Towing — Phase B Full Site & 261 Service Area Importer', 'santa-towing' ); ?></h1>
            <p><?php esc_html_e( 'Synchronizes all 42 media assets, site settings, 4 nav menus, 70 CPT/blog seeds, 26 pages (marketing + all 12 policies + sitemap + blog), and all 261 localized service-area routes cleanly and idempotently.', 'santa-towing' ); ?></p>

            <form method="post" style="background:#fff;padding:20px;border:1px solid #ccd0d4;max-width:860px;margin:20px 0;">
                <?php wp_nonce_field( 'st_full_import_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="st_full_section"><?php esc_html_e( 'Import Scope', 'santa-towing' ); ?></label></th>
                        <td>
                            <select name="st_full_section" id="st_full_section">
                                <option value="all"><?php esc_html_e( 'Complete Site (Media + Settings + Menus + 70 CPTs + 26 Pages + 261 Areas + BM Defaults)', 'santa-towing' ); ?></option>
                                <option value="media"><?php esc_html_e( 'Step 1: 42 Media Library Assets Only (Exact Filenames + Alt Text)', 'santa-towing' ); ?></option>
                                <option value="settings"><?php esc_html_e( 'Step 2: Site Settings, 30 Image Alts & Elementor Kit Colors', 'santa-towing' ); ?></option>
                                <option value="menus"><?php esc_html_e( 'Step 3: Navigation Menus (4 Locations)', 'santa-towing' ); ?></option>
                                <option value="cpts"><?php esc_html_e( 'Step 4: Inventory, Blog, FAQs, Gallery & Careers (70 Items)', 'santa-towing' ); ?></option>
                                <option value="pages"><?php esc_html_e( 'Step 5: All Core, Directory, Utility & 12 Policy Pages', 'santa-towing' ); ?></option>
                                <option value="areas"><?php esc_html_e( 'Step 6: 261 Service Area Pages Only (Supports cPanel Batch Offset)', 'santa-towing' ); ?></option>
                                <option value="defaults"><?php esc_html_e( 'Step 7: Data Files (Adverts/Regions) & Business Manager Seeds', 'santa-towing' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="st_area_offset"><?php esc_html_e( 'Service Area Batch (cPanel Timeout Safe)', 'santa-towing' ); ?></label></th>
                        <td>
                            Offset: <input type="number" name="st_area_offset" id="st_area_offset" value="0" min="0" max="260" style="width:90px;" />
                            Limit: <input type="number" name="st_area_limit" value="261" min="1" max="261" style="width:90px;" />
                            <p class="description"><?php esc_html_e( 'Leave 0 / 261 on normal servers, or run batches (e.g. offset 0 limit 90, offset 90 limit 90, offset 180 limit 90) on strict cPanel timeouts.', 'santa-towing' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Mode', 'santa-towing' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="st_dry_run" value="1" /> <?php esc_html_e( 'Dry Run (Validate files and preview counts without writing to MySQL)', 'santa-towing' ); ?></label>
                        </td>
                    </tr>
                </table>
                <p><button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Execute Full Migration', 'santa-towing' ); ?></button></p>
            </form>

            <?php if ( is_array( $report ) ) : ?>
                <div class="notice <?php echo $report['ok'] ? 'notice-success' : 'notice-error'; ?>" style="padding:16px;max-width:860px;">
                    <h3><?php echo $report['dry_run'] ? 'Dry Run Preview Complete' : 'Migration Execution Complete'; ?> (Scope: <?php echo esc_html( $report['section'] ); ?>)</h3>
                    <pre style="background:#f6f7f7;padding:12px;overflow:auto;"><?php echo esc_html( wp_json_encode( $report['summary'], JSON_PRETTY_PRINT ) ); ?></pre>
                    <?php if ( ! empty( $report['report'] ) ) : ?>
                        <h4><?php esc_html_e( 'Migration Audit Breakdown', 'santa-towing' ); ?></h4>
                        <pre style="background:#f6f7f7;padding:12px;overflow:auto;"><?php echo esc_html( wp_json_encode( $report['report'], JSON_PRETTY_PRINT ) ); ?></pre>
                    <?php endif; ?>
                    <?php if ( ! empty( $report['errors'] ) ) : ?>
                        <ul>
                            <?php foreach ( $report['errors'] as $err ) : ?>
                                <li><?php echo esc_html( $err ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Auto-run when invoked directly via WP-CLI (`wp eval-file`)
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    $section = getenv( 'ST_IMPORT_SECTION' ) ?: 'all';
    $offset  = getenv( 'ST_IMPORT_OFFSET' ) !== false ? (int) getenv( 'ST_IMPORT_OFFSET' ) : 0;
    $limit   = getenv( 'ST_IMPORT_LIMIT' ) !== false ? (int) getenv( 'ST_IMPORT_LIMIT' ) : 261;
    $dry_run = getenv( 'ST_IMPORT_DRY_RUN' ) === '1';

    $result = ST_Full_Importer::run(
        array(
            'section' => $section,
            'offset'  => $offset,
            'limit'   => $limit,
            'dry_run' => $dry_run,
        )
    );
    WP_CLI::log( wp_json_encode( $result, JSON_PRETTY_PRINT ) );
    if ( ! $result['ok'] ) {
        WP_CLI::error( 'Full site import finished with errors.' );
    } else {
        WP_CLI::success( 'Full site import completed cleanly.' );
    }
} elseif ( is_admin() ) {
    ST_Full_Importer::register_admin_page();
}
