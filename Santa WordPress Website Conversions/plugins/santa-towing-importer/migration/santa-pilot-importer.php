<?php
/**
 * Santa Towing — Stage 4 Pilot Page & Template Importer
 *
 * Imports the 5 verified Pilot routes cleanly into WordPress:
 *   1. Home (/)                          — Page + Elementor Free (`pilot-home.json`)
 *   2. Privacy Policy (/privacy-policy/) — Page (`template-policy.php` + Elementor JSON export available)
 *   3. Our Services (/services/)         — Page + Elementor Free (`pilot-services.json`)
 *   4. Our Fleet (/fleet/)               — Interactive Page + Elementor Free (`pilot-fleet.json`)
 *   5. Accra (/service-areas/accra/)     — CPT `st_service_area` + Elementor Free (`pilot-service-area-accra.json`)
 *
 * Supports BOTH:
 *   - WP-CLI execution: `wp eval-file "Santa WordPress Website Conversions/migration/santa-pilot-importer.php"`
 *   - WP Admin Tools UI (when required by theme/mu-plugin): Tools -> Santa Pilot Importer
 *
 * Idempotent: looks up existing posts by post_type + post_name (`get_page_by_path`) and updates
 * cleanly without duplicating records or overwriting unrelated posts.
 *
 * @package Santa_Towing_Migration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-st-media-sync.php';

class ST_Pilot_Importer {

    /**
     * Hook Admin Tools UI inside WordPress when loaded as an admin helper.
     */
    public static function register_admin_page() {
        add_action(
            'admin_menu',
            function () {
                add_management_page(
                    __( 'Santa Pilot Importer', 'santa-towing' ),
                    __( 'Santa Pilot Importer', 'santa-towing' ),
                    'manage_options',
                    'st-pilot-importer',
                    array( __CLASS__, 'render_admin_page' )
                );
            }
        );
    }

    /**
     * Execute the 5-page Pilot import idempotently.
     *
     * @param bool $dry_run When true, parses data and returns exact plan without writing DB changes.
     * @return array{ok:bool,dry_run:bool,results:array,media_count:int,errors:array}
     */
    public static function run( $dry_run = false ) {
        $json_path = __DIR__ . '/pilot-pages.json';
        if ( ! file_exists( $json_path ) ) {
            return array(
                'ok'          => false,
                'dry_run'     => (bool) $dry_run,
                'results'     => array(),
                'media_count' => 0,
                'errors'      => array( 'Missing pilot-pages.json at ' . $json_path ),
            );
        }

        $payload = json_decode( (string) file_get_contents( $json_path ), true );
        if ( ! is_array( $payload ) || empty( $payload['pilot_routes'] ) ) {
            return array(
                'ok'          => false,
                'dry_run'     => (bool) $dry_run,
                'results'     => array(),
                'media_count' => 0,
                'errors'      => array( 'Invalid JSON structure in pilot-pages.json.' ),
            );
        }

        $tpl_dir     = dirname( __DIR__ ) . '/elementor-templates/';
        $media_cache = array();
        $results     = array();
        $errors      = array();

        // 1. Pre-sync required media assets idempotently
        foreach ( $payload['pilot_routes'] as $route ) {
            foreach ( (array) ( $route['media_assets'] ?? array() ) as $filename ) {
                if ( isset( $media_cache[ $filename ] ) ) {
                    continue;
                }
                if ( $dry_run ) {
                    $media_cache[ $filename ] = array(
                        'id'  => 0,
                        'url' => trailingslashit( get_template_directory_uri() ) . 'assets/images/' . rawurlencode( $filename ),
                    );
                } else {
                    $res = ST_Migration_Media_Sync::ensure_media_attachment( $filename );
                    if ( is_wp_error( $res ) ) {
                        $errors[] = $filename . ': ' . $res->get_error_message();
                    } else {
                        $media_cache[ $filename ] = $res;
                    }
                }
            }
        }

        // 2. Import each of the 5 Pilot routes
        foreach ( $payload['pilot_routes'] as $route ) {
            $post_type = sanitize_key( $route['post_type'] );
            $slug      = sanitize_title( $route['post_name'] );
            $existing  = get_page_by_path( $slug, OBJECT, $post_type );
            $action    = $existing ? 'update' : 'create';

            if ( ! post_type_exists( $post_type ) ) {
                $errors[] = sprintf( 'Post type "%s" is not registered. Activate "Santa Towing Website Core" plugin first.', $post_type );
                continue;
            }

            if ( $dry_run ) {
                $results[] = array(
                    'key'       => $route['key'],
                    'action'    => 'dry_run_' . $action,
                    'post_id'   => $existing ? (int) $existing->ID : 0,
                    'post_type' => $post_type,
                    'slug'      => $slug,
                    'target'    => $route['target_wp_path'],
                    'elementor' => $route['elementor_template'] ?? '',
                );
                continue;
            }

            $post_content = isset( $route['post_content'] )
                ? $route['post_content']
                : ( $route['post_content_fallback'] ?? '' );

            $postarr = array(
                'post_type'    => $post_type,
                'post_status'  => sanitize_key( $route['post_status'] ?? 'publish' ),
                'post_title'   => wp_strip_all_tags( $route['post_title'] ),
                'post_name'    => $slug,
                'post_content' => wp_kses_post( $post_content ),
                'post_excerpt' => isset( $route['seo']['description'] ) ? wp_strip_all_tags( $route['seo']['description'] ) : '',
            );

            if ( $existing ) {
                $postarr['ID'] = (int) $existing->ID;
                $post_id       = wp_update_post( $postarr, true );
            } else {
                $post_id = wp_insert_post( $postarr, true );
            }

            if ( is_wp_error( $post_id ) ) {
                $errors[] = $route['key'] . ': ' . $post_id->get_error_message();
                continue;
            }

            // Template assignment
            if ( ! empty( $route['wp_template'] ) ) {
                update_post_meta( $post_id, '_wp_page_template', sanitize_text_field( $route['wp_template'] ) );
            }

            // Custom SEO + Policy + Service Area Meta
            if ( ! empty( $route['seo']['title'] ) ) {
                update_post_meta( $post_id, '_st_seo_title', sanitize_text_field( $route['seo']['title'] ) );
            }
            if ( ! empty( $route['seo']['description'] ) ) {
                update_post_meta( $post_id, '_st_seo_description', sanitize_textarea_field( $route['seo']['description'] ) );
            }
            if ( ! empty( $route['seo']['keywords'] ) && is_array( $route['seo']['keywords'] ) ) {
                update_post_meta( $post_id, '_st_keywords_json', wp_json_encode( array_values( $route['seo']['keywords'] ) ) );
            }
            if ( ! empty( $route['meta'] ) && is_array( $route['meta'] ) ) {
                foreach ( $route['meta'] as $mkey => $mval ) {
                    update_post_meta( $post_id, sanitize_key( $mkey ), is_string( $mval ) ? $mval : wp_json_encode( $mval ) );
                }
            }

            // Assign Taxonomy Terms if configured (e.g. Greater Accra on Accra route)
            if ( ! empty( $route['taxonomy'] ) && is_array( $route['taxonomy'] ) ) {
                foreach ( $route['taxonomy'] as $tax => $terms ) {
                    if ( taxonomy_exists( $tax ) ) {
                        wp_set_object_terms( $post_id, (array) $terms, $tax, false );
                    }
                }
            }

            // Apply Elementor Free data when enabled for this pilot page
            $use_elementor = ! isset( $route['use_elementor_on_import'] ) || (bool) $route['use_elementor_on_import'];
            if ( $use_elementor && ! empty( $route['elementor_template'] ) ) {
                $tpl_file = $tpl_dir . basename( $route['elementor_template'] );
                if ( file_exists( $tpl_file ) ) {
                    $tpl_json = json_decode( (string) file_get_contents( $tpl_file ), true );
                    if ( is_array( $tpl_json ) && isset( $tpl_json['content'] ) ) {
                        $hydrated = ST_Migration_Media_Sync::hydrate_elementor_tokens( $tpl_json['content'], $media_cache );
                        update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $hydrated ) ) );
                        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
                        update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
                        update_post_meta( $post_id, '_elementor_version', '3.24.0' );
                        if ( ! empty( $tpl_json['page_settings'] ) ) {
                            update_post_meta( $post_id, '_elementor_page_settings', $tpl_json['page_settings'] );
                        }
                    }
                }
            }

            // Set static front page option for Home
            if ( ! empty( $route['set_as_front_page'] ) ) {
                update_option( 'show_on_front', 'page' );
                update_option( 'page_on_front', (int) $post_id );
            }

            $results[] = array(
                'key'       => $route['key'],
                'action'    => $action,
                'post_id'   => (int) $post_id,
                'post_type' => $post_type,
                'slug'      => $slug,
                'permalink' => get_permalink( $post_id ),
                'elementor' => $use_elementor ? ( $route['elementor_template'] ?? '' ) : 'theme-template',
            );
        }

        if ( ! $dry_run ) {
            flush_rewrite_rules( false );
        }

        return array(
            'ok'          => empty( $errors ),
            'dry_run'     => (bool) $dry_run,
            'results'     => $results,
            'media_count' => count( $media_cache ),
            'errors'      => $errors,
        );
    }

    /**
     * Render WP-Admin Tools Page for secure nonced Pilot import execution.
     */
    public static function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'santa-towing' ) );
        }

        $report = null;
        if ( isset( $_POST['st_pilot_action'] ) && check_admin_referer( 'st_pilot_import_nonce' ) ) {
            $is_dry = ( 'dry_run' === sanitize_key( wp_unslash( $_POST['st_pilot_action'] ) ) );
            $report = self::run( $is_dry );
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Santa Towing — Stage 4 Pilot Page Importer (5 Pilot Routes)', 'santa-towing' ); ?></h1>
            <p><?php esc_html_e( 'Imports Home (/), Privacy Policy (/privacy-policy/), Our Services (/services/), Our Fleet (/fleet/), and Accra (/service-areas/accra/) idempotently with Elementor Free JSON templates and verified SEO metadata.', 'santa-towing' ); ?></p>
            <form method="post" style="margin: 20px 0; display:flex; gap:12px;">
                <?php wp_nonce_field( 'st_pilot_import_nonce' ); ?>
                <button type="submit" name="st_pilot_action" value="dry_run" class="button button-secondary"><?php esc_html_e( 'Preview Dry Run', 'santa-towing' ); ?></button>
                <button type="submit" name="st_pilot_action" value="import" class="button button-primary"><?php esc_html_e( 'Run Pilot Import (5 Routes)', 'santa-towing' ); ?></button>
            </form>

            <?php if ( is_array( $report ) ) : ?>
                <div class="notice <?php echo $report['ok'] ? 'notice-success' : 'notice-error'; ?>" style="padding:16px;">
                    <p><strong><?php echo $report['dry_run'] ? 'Dry Run Preview Complete' : 'Pilot Import Executed'; ?></strong> — Media Processed: <?php echo (int) $report['media_count']; ?></p>
                    <?php if ( ! empty( $report['errors'] ) ) : ?>
                        <ul>
                            <?php foreach ( $report['errors'] as $err ) : ?>
                                <li><?php echo esc_html( $err ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <table class="widefat striped">
                        <thead><tr><th>Key</th><th>Action</th><th>Post ID</th><th>Type</th><th>Slug</th><th>Handler</th></tr></thead>
                        <tbody>
                            <?php foreach ( $report['results'] as $row ) : ?>
                                <tr>
                                    <td><?php echo esc_html( $row['key'] ); ?></td>
                                    <td><?php echo esc_html( $row['action'] ); ?></td>
                                    <td><?php echo (int) $row['post_id']; ?></td>
                                    <td><?php echo esc_html( $row['post_type'] ); ?></td>
                                    <td><?php echo esc_html( $row['slug'] ); ?></td>
                                    <td><?php echo esc_html( $row['elementor'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Auto-run when invoked directly via WP-CLI (`wp eval-file`)
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    $result = ST_Pilot_Importer::run( false );
    WP_CLI::log( wp_json_encode( $result, JSON_PRETTY_PRINT ) );
    if ( ! $result['ok'] ) {
        WP_CLI::error( 'Pilot import finished with errors.' );
    } else {
        WP_CLI::success( 'Imported ' . count( $result['results'] ) . ' pilot routes and synced ' . $result['media_count'] . ' media assets.' );
    }
} elseif ( is_admin() ) {
    ST_Pilot_Importer::register_admin_page();
}
