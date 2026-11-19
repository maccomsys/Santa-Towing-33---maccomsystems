<?php
/**
 * URL Rewrites, 301 Redirects, Virtual Service-Area Routes, Sitemap & SEO Meta
 *
 * Implements:
 * - Legacy numeric ID routing (/sales/1, /rental/1, /parts/1, /blog/1)
 * - Service-Area sub-routes (/service-areas/{areaSlug}/{serviceSlug})
 * - Fallback 200 rendering for 32 districts + 232 Ghana towns even pre-import
 * - All 14 React <Navigate replace> 301 redirects + vehicle-recovery -> car-recovery
 * - Dynamic /sitemap.xml & robots.txt integration
 * - Server-side OpenGraph, Twitter Cards & Schema.org AutoRepair JSON-LD
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class STWC_Rewrites {

    /**
     * Singleton instance.
     *
     * @var STWC_Rewrites|null
     */
    private static $instance = null;

    /**
     * Virtual service-area data payload when serving a non-CPT locality route.
     *
     * @var array|null
     */
    private $virtual_route_data = null;

    /**
     * Get singleton instance.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Hook WordPress rewrite & SEO lifecycle.
     */
    private function __construct() {
        add_action( 'init', array( __CLASS__, 'add_rewrite_rules' ), 10 );
        add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
        add_filter( 'request', array( $this, 'resolve_legacy_id_request' ) );
        add_action( 'template_redirect', array( $this, 'handle_redirects_and_sitemap' ), 1 );
        add_filter( 'template_include', array( $this, 'maybe_load_virtual_template' ), 99 );
        add_filter( 'robots_txt', array( $this, 'filter_robots_txt' ), 10, 2 );
        add_filter( 'document_title_parts', array( $this, 'filter_document_title' ), 20 );
        add_action( 'wp_head', array( $this, 'output_seo_and_schema' ), 3 );
    }

    /**
     * Register all custom rewrite rules.
     */
    public static function add_rewrite_rules() {
        // Legacy numeric detail routes (/sales/1, /rental/1, /parts/1, /blog/1)
        add_rewrite_rule( '^sales/([0-9]+)/?$', 'index.php?post_type=st_car&st_legacy_id=$matches[1]', 'top' );
        add_rewrite_rule( '^rental/([0-9]+)/?$', 'index.php?post_type=st_rental&st_legacy_id=$matches[1]', 'top' );
        add_rewrite_rule( '^parts/([0-9]+)/?$', 'index.php?post_type=st_part&st_legacy_id=$matches[1]', 'top' );
        add_rewrite_rule( '^blog/([0-9]+)/?$', 'index.php?post_type=post&st_legacy_id=$matches[1]', 'top' );

        // Service Area sub-service routes (/service-areas/tema/emergency-towing)
        add_rewrite_rule( '^service-areas/([^/]+)/([^/]+)/?$', 'index.php?st_area_slug=$matches[1]&st_service_slug=$matches[2]', 'top' );

        // Dynamic XML Sitemap
        add_rewrite_rule( '^sitemap\.xml$', 'index.php?st_sitemap=1', 'top' );
    }

    /**
     * Whitelist custom query variables.
     *
     * @param array $vars Existing query vars.
     * @return array
     */
    public function register_query_vars( $vars ) {
        $vars[] = 'st_legacy_id';
        $vars[] = 'st_area_slug';
        $vars[] = 'st_service_slug';
        $vars[] = 'st_sitemap';
        return $vars;
    }

    /**
     * Resolve legacy numeric IDs (/sales/1, /rental/1, /parts/1, /blog/1) prior to parse_query().
     *
     * @param array $vars Parsed WP request query vars.
     * @return array
     */
    public function resolve_legacy_id_request( $vars ) {
        if ( is_admin() || empty( $vars['st_legacy_id'] ) ) {
            return $vars;
        }

        $legacy_id = absint( $vars['st_legacy_id'] );
        $post_type = ! empty( $vars['post_type'] ) ? sanitize_key( $vars['post_type'] ) : 'post';

        $matches = get_posts(
            array(
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => '_st_legacy_id',
                'meta_value'     => $legacy_id,
            )
        );

        if ( ! empty( $matches ) ) {
            return array(
                'post_type' => $post_type,
                'p'         => (int) $matches[0],
            );
        }

        $vars['error'] = '404';
        unset( $vars['st_legacy_id'] );
        return $vars;
    }

    /**
     * Handle 301 Redirects (React <Navigate replace>), vehicle-recovery alias, and XML Sitemap.
     */
    public function handle_redirects_and_sitemap() {
        if ( (int) get_query_var( 'st_sitemap' ) === 1 ) {
            $this->render_sitemap_xml();
            exit;
        }

        $req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
        $req_path = trim( (string) wp_parse_url( $req_uri, PHP_URL_PATH ), '/' );

        // Exact React <Navigate replace> routes (src/App.tsx lines 699-712) + legacy aliases
        $redirect_map = array(
            'vehicle-damage-liability-policy' => '/car-damage-liability-policy/',
            'vehicles'                        => '/sales/',
            'cars'                            => '/sales/',
            'car-sales'                       => '/sales/',
            'vehicle-sales'                   => '/sales/',
            'car-rentals'                     => '/rental/',
            'vehicle-rentals'                 => '/rental/',
            'car-rental'                      => '/rental/',
            'vehicle-rental'                  => '/rental/',
            'about'                           => '/#about',
            'services-areas'                  => '/service-areas/',
            'rentals'                         => '/rental/',
            'privacy'                         => '/privacy-policy/',
            'terms'                           => '/terms-conditions/',
            'terms-and-conditions'            => '/terms-conditions/',
            'cookies'                         => '/cookie-policy/',
            'repair'                          => '/repairs/',
            'bodyworks'                       => '/body-works/',
            'part'                            => '/parts/',
            'towing-services'                 => '/towing/',
            'haulage-services'                => '/haulage/',
            'faq'                             => '/faqs/',
            'career'                          => '/careers/',
        );

        $lower_path = strtolower( $req_path );
        if ( isset( $redirect_map[ $lower_path ] ) ) {
            wp_safe_redirect( home_url( $redirect_map[ $lower_path ] ), 301 );
            exit;
        }

        // Pattern redirects from src/App.tsx lines 704-707 (/vehicles/:id, /cars/:id, /vehicle-details/:id, /car-details/:id)
        if ( preg_match( '#^(?:vehicles|cars|vehicle-details|car-details)/[^/]+$#i', $lower_path ) ) {
            wp_safe_redirect( home_url( '/sales/' ), 301 );
            exit;
        }

        // Sub-service alias: /service-areas/{area}/vehicle-recovery -> /service-areas/{area}/car-recovery
        $area_slug    = sanitize_title( (string) get_query_var( 'st_area_slug' ) );
        $service_slug = sanitize_title( (string) get_query_var( 'st_service_slug' ) );

        if ( $area_slug && 'vehicle-recovery' === $service_slug ) {
            wp_safe_redirect( home_url( '/service-areas/' . $area_slug . '/car-recovery/' ), 301 );
            exit;
        }
    }

    /**
     * Serve virtual templates for /service-areas/{area}/{service} and un-imported locality slugs.
     *
     * @param string $template Resolved WP template path.
     * @return string
     */
    public function maybe_load_virtual_template( $template ) {
        $req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
        $req_path = trim( (string) wp_parse_url( $req_uri, PHP_URL_PATH ), '/' );

        // Case 0: /search or /search?q=... renders the theme search.php template directly
        if ( 'search' === strtolower( $req_path ) || is_page( 'search' ) ) {
            $q_param = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
            if ( '' === $q_param && isset( $_GET['s'] ) ) {
                $q_param = sanitize_text_field( wp_unslash( $_GET['s'] ) );
            }
            if ( '' !== $q_param ) {
                set_query_var( 's', $q_param );
            }
            $search_tpl = get_query_template( 'search' );
            if ( $search_tpl ) {
                status_header( 200 );
                global $wp_query;
                $wp_query->is_404 = false;
                return $search_tpl;
            }
        }

        $area_slug    = sanitize_title( (string) get_query_var( 'st_area_slug' ) );
        $service_slug = sanitize_title( (string) get_query_var( 'st_service_slug' ) );

        // Case 1: Localized Area + Specific Towing Sub-Service (/service-areas/tema/emergency-towing)
        if ( $area_slug && $service_slug ) {
            $area_obj     = $this->lookup_area_data( $area_slug );
            $service_list = stwc_get_area_sub_services();

            if ( $area_obj && isset( $service_list[ $service_slug ] ) ) {
                status_header( 200 );
                global $wp_query;
                $wp_query->is_404 = false;

                $this->virtual_route_data = array(
                    'mode'         => 'area_service',
                    'area'         => $area_obj,
                    'service_slug' => $service_slug,
                    'service'      => $service_list[ $service_slug ],
                );
                set_query_var( 'stwc_virtual_route', $this->virtual_route_data );

                $virtual_tpl = STWC_DIR . 'templates/virtual-area-service.php';
                if ( file_exists( $virtual_tpl ) ) {
                    return $virtual_tpl;
                }
            }
        }

        // Case 2: 404 under /service-areas/{slug} matching one of the 32 adverts or 232 Ghana localities
        if ( is_404() ) {
            if ( preg_match( '#^service-areas/([^/]+)$#i', $req_path, $m ) ) {
                $candidate_slug = sanitize_title( $m[1] );
                $area_obj       = $this->lookup_area_data( $candidate_slug );

                if ( $area_obj ) {
                    status_header( 200 );
                    global $wp_query;
                    $wp_query->is_404 = false;

                    $this->virtual_route_data = array(
                        'mode' => 'area_detail',
                        'area' => $area_obj,
                    );
                    set_query_var( 'stwc_virtual_route', $this->virtual_route_data );

                    $virtual_tpl = STWC_DIR . 'templates/virtual-service-area.php';
                    if ( file_exists( $virtual_tpl ) ) {
                        return $virtual_tpl;
                    }
                }
            }
        }

        return $template;
    }

    /**
     * Find Service Area details from CPT, 32 District Adverts, or 232 Ghana Towns.
     *
     * @param string $slug Route slug.
     * @return array|null
     */
    public function lookup_area_data( $slug ) {
        $slug = sanitize_title( $slug );

        // 1. Check WP CPT st_service_area
        $cpt = get_page_by_path( $slug, OBJECT, 'st_service_area' );
        if ( $cpt && 'publish' === $cpt->post_status ) {
            $terms  = wp_get_post_terms( $cpt->ID, 'st_region', array( 'fields' => 'names' ) );
            $region = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : 'Ghana';
            return array(
                'slug'        => $slug,
                'area_label'  => get_post_meta( $cpt->ID, '_st_area_label', true ) ?: get_the_title( $cpt ),
                'heading'     => get_post_meta( $cpt->ID, '_st_heading', true ) ?: get_the_title( $cpt ),
                'description' => get_post_meta( $cpt->ID, '_st_description', true ) ?: wp_strip_all_tags( $cpt->post_content ),
                'region'      => $region,
                'prev_path'   => get_post_meta( $cpt->ID, '_st_prev_path', true ),
                'prev_label'  => get_post_meta( $cpt->ID, '_st_prev_label', true ),
                'next_path'   => get_post_meta( $cpt->ID, '_st_next_path', true ),
                'next_label'  => get_post_meta( $cpt->ID, '_st_next_label', true ),
            );
        }

        // 2. Check 32 Towing Adverts
        $adverts = stwc_get_towing_adverts();
        $total   = count( $adverts );
        foreach ( $adverts as $idx => $adv ) {
            if ( sanitize_title( $adv['slug'] ) === $slug ) {
                $prev = $idx > 0 ? $adverts[ $idx - 1 ] : null;
                $next = $idx < $total - 1 ? $adverts[ $idx + 1 ] : null;
                return array(
                    'slug'        => $adv['slug'],
                    'area_label'  => $adv['areaName'],
                    'heading'     => '24/7 Towing Service in ' . $adv['areaName'],
                    'description' => $adv['description'],
                    'region'      => $adv['region'],
                    'eta'         => $adv['eta'] ?? '30-45 mins',
                    'landmarks'   => $adv['landmarks'] ?? array(),
                    'prev_path'   => $prev ? '/service-areas/' . $prev['slug'] : '',
                    'prev_label'  => $prev ? $prev['areaName'] : '',
                    'next_path'   => $next ? '/service-areas/' . $next['slug'] : '',
                    'next_label'  => $next ? $next['areaName'] : '',
                );
            }
        }

        // 3. Check 232 Ghana Locations
        foreach ( stwc_get_ghana_regions_data() as $reg ) {
            foreach ( $reg['areas'] as $town ) {
                if ( sanitize_title( $town ) === $slug ) {
                    return array(
                        'slug'        => $slug,
                        'area_label'  => $town,
                        'heading'     => 'Towing Services in ' . $town . ' (' . $reg['region'] . ')',
                        'description' => sprintf(
                            'Fast, reliable 24/7 emergency towing, flatbed recovery, and roadside assistance across %1$s and surrounding corridors in the %2$s.',
                            $town,
                            $reg['region']
                        ),
                        'region'      => $reg['region'],
                        'eta'         => '30-60 mins',
                        'landmarks'   => array(),
                        'prev_path'   => '/service-areas',
                        'prev_label'  => 'All Service Areas',
                        'next_path'   => '/towing',
                        'next_label'  => 'Request Emergency Tow',
                    );
                }
            }
        }

        // 4. Intercity pattern ({origin}-to-{destination}) fallback
        if ( preg_match( '/^([a-z0-9-]+)-to-([a-z0-9-]+)$/', $slug, $parts ) ) {
            $origin = ucwords( str_replace( '-', ' ', $parts[1] ) );
            $dest   = ucwords( str_replace( '-', ' ', $parts[2] ) );
            return array(
                'slug'        => $slug,
                'area_label'  => "{$origin} to {$dest}",
                'heading'     => "Towing & Haulage from {$origin} to {$dest}",
                'description' => "Reliable 24/7 long-distance vehicle towing, flatbed recovery, and heavy haulage between {$origin} and {$dest} with GPS-tracked trucks.",
                'region'      => 'Nationwide Intercity Corridor',
                'eta'         => 'Rapid Dispatch',
                'landmarks'   => array(),
                'prev_path'   => '/service-areas',
                'prev_label'  => 'All Corridors',
                'next_path'   => '/towing',
                'next_label'  => 'Request Dispatch',
            );
        }

        return null;
    }

    /**
     * Render /sitemap.xml dynamically.
     */
    private function render_sitemap_xml() {
        header( 'Content-Type: application/xml; charset=utf-8' );
        $home = untrailingslashit( home_url( '/' ) );

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $static_paths = array( '', 'towing', 'haulage', 'sales', 'rental', 'parts', 'repairs', 'body-works', 'fleet', 'services', 'service-areas', 'gallery', 'blog', 'faqs', 'careers', 'contact', 'sitemap' );
        foreach ( $static_paths as $sp ) {
            $loc = $sp ? "{$home}/{$sp}/" : "{$home}/";
            echo "  <url><loc>" . esc_url( $loc ) . "</loc><changefreq>weekly</changefreq><priority>" . ( '' === $sp ? '1.0' : '0.8' ) . "</priority></url>\n";
        }

        // Published CPTs & Posts
        $posts = get_posts(
            array(
                'post_type'      => array( 'page', 'post', 'st_car', 'st_rental', 'st_part', 'st_service_area' ),
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )
        );
        $seen = array();
        foreach ( $posts as $pid ) {
            $url = get_permalink( $pid );
            if ( $url && ! isset( $seen[ $url ] ) ) {
                $seen[ $url ] = true;
                echo "  <url><loc>" . esc_url( $url ) . "</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>\n";
            }
        }

        // Include the 32 Towing Districts & their 4 Sub-Services
        $sub_services = array_keys( stwc_get_area_sub_services() );
        foreach ( stwc_get_towing_adverts() as $adv ) {
            $base = "{$home}/service-areas/{$adv['slug']}/";
            if ( ! isset( $seen[ $base ] ) ) {
                $seen[ $base ] = true;
                echo "  <url><loc>" . esc_url( $base ) . "</loc><changefreq>monthly</changefreq><priority>0.7</priority></url>\n";
            }
            foreach ( $sub_services as $ss ) {
                $sub_url = "{$base}{$ss}/";
                if ( ! isset( $seen[ $sub_url ] ) ) {
                    $seen[ $sub_url ] = true;
                    echo "  <url><loc>" . esc_url( $sub_url ) . "</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>\n";
                }
            }
        }

        echo "</urlset>\n";
    }

    /**
     * Append Sitemap URL cleanly to WordPress virtual robots.txt.
     */
    public function filter_robots_txt( $output, $public ) {
        if ( $public ) {
            $output .= "\nSitemap: " . esc_url( home_url( '/sitemap.xml' ) ) . "\n";
        }
        return $output;
    }

    /**
     * Adjust <title> tag for virtual service area routes & SEO consistency.
     *
     * @param array $parts WP title parts.
     * @return array
     */
    public function filter_document_title( $parts ) {
        if ( is_front_page() ) {
            $home_title = stwc_get_content( 'seo.home.title', '' );
            if ( $home_title ) {
                $parts['title'] = $home_title;
                unset( $parts['tagline'], $parts['site'] );
            }
        } elseif ( is_singular() ) {
            $custom_title = get_post_meta( get_queried_object_id(), '_st_seo_title', true );
            if ( $custom_title ) {
                $parts['title'] = $custom_title;
                unset( $parts['tagline'], $parts['site'] );
            }
        } elseif ( ! empty( $this->virtual_route_data ) ) {
            $v = $this->virtual_route_data;
            if ( 'area_service' === $v['mode'] ) {
                $parts['title'] = $v['service']['title'] . ' in ' . $v['area']['area_label'] . ' | Santa Towing';
            } elseif ( 'area_detail' === $v['mode'] ) {
                $parts['title'] = $v['area']['heading'] . ' | Santa Towing';
            }
        }
        return $parts;
    }

    /**
     * Output OpenGraph, Meta Description & AutoRepair Schema JSON-LD on frontend pages.
     */
    public function output_seo_and_schema() {
        if ( is_admin() ) {
            return;
        }

        $title = wp_get_document_title();
        $desc  = stwc_get_content( 'seo.home.description', '24/7 emergency towing, nationwide haulage, auto repairs, car sales & rentals in Ghana.' );

        $desc_is_custom = false;
        if ( ! empty( $this->virtual_route_data['area']['description'] ) ) {
            $desc           = $this->virtual_route_data['area']['description'];
            $desc_is_custom = true;
        } elseif ( is_singular() ) {
            $custom_desc = get_post_meta( get_queried_object_id(), '_st_seo_description', true );
            if ( $custom_desc ) {
                $desc           = $custom_desc;
                $desc_is_custom = true;
            } elseif ( has_excerpt() ) {
                $desc = wp_strip_all_tags( get_the_excerpt() );
            }
        }

        $req_uri   = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
        $canonical = home_url( $req_uri );
        $image     = stwc_get_content( 'global.logo.url', stwc_asset_image( 'santa-logo.png' ) );
        if ( is_singular() && has_post_thumbnail() ) {
            $thumb = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
            if ( $thumb ) {
                $image = $thumb;
            }
        }

        $keywords = '';
        if ( ! empty( $this->virtual_route_data['area']['area_label'] ) ) {
            $area_kw  = $this->virtual_route_data['area']['area_label'];
            $keywords = "towing {$area_kw}, emergency towing {$area_kw}, car recovery {$area_kw}, Santa Towing Ghana";
        } elseif ( is_singular() ) {
            $kw_json = json_decode( (string) get_post_meta( get_queried_object_id(), '_st_keywords_json', true ), true );
            if ( is_array( $kw_json ) && ! empty( $kw_json ) ) {
                $keywords = implode( ', ', array_map( 'sanitize_text_field', $kw_json ) );
            }
        }

        $meta_desc = $desc_is_custom ? wp_strip_all_tags( $desc ) : wp_trim_words( $desc, 30, '' );

        echo '<meta name="description" content="' . esc_attr( $meta_desc ) . "\" />\n";
        if ( '' !== $keywords ) {
            echo '<meta name="keywords" content="' . esc_attr( $keywords ) . "\" />\n";
        }
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . "\" />\n";
        echo '<meta property="og:type" content="' . ( is_single() ? 'article' : 'website' ) . "\" />\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . "\" />\n";
        echo '<meta property="og:description" content="' . esc_attr( $meta_desc ) . "\" />\n";
        echo '<meta property="og:url" content="' . esc_url( $canonical ) . "\" />\n";
        echo '<meta property="og:image" content="' . esc_url( $image ) . "\" />\n";
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";

        $schema = array(
            '@context'  => 'https://schema.org',
            '@type'     => 'AutoRepair',
            'name'      => stwc_get_content( 'global.brand.name', 'Santa Towing & Garage Services Ltd.' ),
            'url'       => home_url( '/' ),
            'telephone' => stwc_get_content( 'global.links.phone', '0244753849' ),
            'email'     => stwc_get_content( 'global.links.email', 'info@santatowing.com' ),
            'address'   => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => stwc_get_content( 'global.links.address', '123 Spintex Road, Accra, Ghana' ),
                'addressLocality' => 'Accra',
                'addressCountry'  => 'GH',
            ),
            'areaServed' => 'Ghana',
            'openingHoursSpecification' => array(
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
                'opens'     => '00:00',
                'closes'    => '23:59',
            ),
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . "</script>\n";
    }
}
