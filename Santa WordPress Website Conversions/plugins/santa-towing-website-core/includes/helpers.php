<?php
/**
 * Plugin Helpers & Theme-Agnostic Fallbacks
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Resolve bundled image URL (prefers active santa-towing theme image if available, else plugin/uploads).
 *
 * @param string $filename Asset filename.
 * @return string
 */
function stwc_asset_image( $filename ) {
    $filename = ltrim( (string) $filename, '/' );

    if ( function_exists( 'st_theme_image' ) ) {
        $theme_path = get_template_directory() . '/assets/images/' . $filename;
        if ( file_exists( $theme_path ) ) {
            return st_theme_image( $filename );
        }
    }

    $plugin_img = STWC_DIR . 'assets/images/' . $filename;
    if ( file_exists( $plugin_img ) ) {
        return STWC_URL . 'assets/images/' . $filename;
    }

    return get_template_directory_uri() . '/assets/images/' . $filename;
}

/**
 * Retrieve dot-notation content from `santa_towing_site_content` option with complete React defaults fallback.
 *
 * @param string $path     Dot-notation path e.g. 'global.links.phone'.
 * @param mixed  $fallback Fallback value.
 * @return mixed
 */
function stwc_get_content( $path, $fallback = '' ) {
    if ( function_exists( 'st_get_content' ) ) {
        $val = st_get_content( $path, '__STWC_UNSET__' );
        if ( '__STWC_UNSET__' !== $val ) {
            return $val;
        }
    }

    $opt    = get_option( 'santa_towing_site_content', array() );
    $stored = is_array( $opt ) ? $opt : array();

    $segments = explode( '.', (string) $path );
    $cur      = $stored;
    $found    = true;
    foreach ( $segments as $seg ) {
        if ( is_array( $cur ) && array_key_exists( $seg, $cur ) && null !== $cur[ $seg ] && '' !== $cur[ $seg ] ) {
            $cur = $cur[ $seg ];
        } else {
            $found = false;
            break;
        }
    }
    if ( $found ) {
        return $cur;
    }

    $cur   = stwc_get_default_site_content();
    $found = true;
    foreach ( $segments as $seg ) {
        if ( is_array( $cur ) && array_key_exists( $seg, $cur ) ) {
            $cur = $cur[ $seg ];
        } else {
            $found = false;
            break;
        }
    }

    if ( $found && null !== $cur ) {
        return $cur;
    }

    return $fallback;
}

/**
 * Safe internal route URL formatter.
 *
 * @param string $path Path or URL.
 * @return string
 */
function stwc_route_url( $path ) {
    if ( function_exists( 'st_route_url' ) ) {
        return st_route_url( $path );
    }
    $path = trim( (string) $path );
    if ( '' === $path || '/' === $path ) {
        return home_url( '/' );
    }
    if ( preg_match( '#^(https?://|tel:|mailto:|#)#i', $path ) ) {
        return $path;
    }
    return home_url( '/' . ltrim( $path, '/' ) );
}

/**
 * Render clean inline Lucide SVG icon matching the original React icon strokes.
 *
 * @param string $name  Icon identifier.
 * @param string $class CSS classes.
 * @return string
 */
function stwc_icon( $name, $class = 'w-5 h-5' ) {
    if ( function_exists( 'st_icon' ) ) {
        $svg = st_icon( $name, $class );
        if ( '' !== $svg ) {
            return $svg;
        }
    }

    $attrs = 'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="' . esc_attr( $class ) . '"';

    switch ( $name ) {
        case 'phone':
            return "<svg {$attrs}><path d=\"M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z\"/></svg>";
        case 'message-circle':
            return "<svg {$attrs}><path d=\"M7.9 20A9 9 0 1 0 4 16.1L2 22Z\"/></svg>";
        case 'truck':
            return "<svg {$attrs}><path d=\"M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2\"/><path d=\"M15 18H9\"/><path d=\"M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14\"/><circle cx=\"17\" cy=\"18\" r=\"2\"/><circle cx=\"7\" cy=\"18\" r=\"2\"/></svg>";
        case 'car':
            return "<svg {$attrs}><path d=\"M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2\"/><circle cx=\"7\" cy=\"17\" r=\"2\"/><path d=\"M9 17h6\"/><circle cx=\"17\" cy=\"17\" r=\"2\"/></svg>";
        case 'wrench':
            return "<svg {$attrs}><path d=\"M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z\"/></svg>";
        case 'shield':
            return "<svg {$attrs}><path d=\"M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z\"/></svg>";
        case 'shield-check':
            return "<svg {$attrs}><path d=\"M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z\"/><path d=\"m9 12 2 2 4-4\"/></svg>";
        case 'settings':
            return "<svg {$attrs}><path d=\"M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z\"/><circle cx=\"12\" cy=\"12\" r=\"3\"/></svg>";
        case 'clock':
            return "<svg {$attrs}><circle cx=\"12\" cy=\"12\" r=\"10\"/><polyline points=\"12 6 12 12 16 14\"/></svg>";
        case 'map-pin':
            return "<svg {$attrs}><path d=\"M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z\"/><circle cx=\"12\" cy=\"10\" r=\"3\"/></svg>";
        case 'check-circle':
            return "<svg {$attrs}><path d=\"M22 11.08V12a10 10 0 1 1-5.93-9.14\"/><polyline points=\"22 4 12 14.01 9 11.01\"/></svg>";
        case 'chevron-down':
            return "<svg {$attrs}><path d=\"m6 9 6 6 6-6\"/></svg>";
        case 'chevron-left':
            return "<svg {$attrs}><path d=\"m15 18-6-6 6-6\"/></svg>";
        case 'chevron-right':
            return "<svg {$attrs}><path d=\"m9 18 6-6-6-6\"/></svg>";
        case 'arrow-right':
            return "<svg {$attrs}><path d=\"M5 12h14\"/><path d=\"m12 5 7 7-7 7\"/></svg>";
        case 'arrow-down':
            return "<svg {$attrs}><path d=\"M12 5v14\"/><path d=\"m19 12-7 7-7-7\"/></svg>";
        case 'search':
            return "<svg {$attrs}><circle cx=\"11\" cy=\"11\" r=\"8\"/><path d=\"m21 21-4.3-4.3\"/></svg>";
        case 'camera':
            return "<svg {$attrs}><path d=\"M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z\"/><circle cx=\"12\" cy=\"13\" r=\"3\"/></svg>";
        case 'navigation':
            return "<svg {$attrs}><polygon points=\"3 11 22 2 13 21 11 13 3 11\"/></svg>";
        case 'briefcase':
            return "<svg {$attrs}><rect width=\"20\" height=\"14\" x=\"2\" y=\"7\" rx=\"2\" ry=\"2\"/><path d=\"M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16\"/></svg>";
        case 'award':
            return "<svg {$attrs}><circle cx=\"12\" cy=\"8\" r=\"6\"/><path d=\"M15.477 12.89 17 22l-5-3-5 3 1.523-9.11\"/></svg>";
        case 'paperclip':
            return "<svg {$attrs}><path d=\"m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48\"/></svg>";
        case 'send':
            return "<svg {$attrs}><path d=\"m22 2-7 20-4-9-9-4Z\"/><path d=\"M22 2 11 13\"/></svg>";
        case 'calendar':
            return "<svg {$attrs}><rect width=\"18\" height=\"18\" x=\"3\" y=\"4\" rx=\"2\" ry=\"2\"/><line x1=\"16\" x2=\"16\" y1=\"2\" y2=\"6\"/><line x1=\"8\" x2=\"8\" y1=\"2\" y2=\"6\"/><line x1=\"3\" x2=\"21\" y1=\"10\" y2=\"10\"/></svg>";
        case 'route':
            return "<svg {$attrs}><circle cx=\"6\" cy=\"19\" r=\"3\"/><path d=\"M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15\"/><circle cx=\"18\" cy=\"5\" r=\"3\"/></svg>";
        case 'star-filled':
            $fattrs = str_replace( 'fill="none"', 'fill="currentColor"', $attrs );
            return "<svg {$fattrs}><polygon points=\"12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2\"/></svg>";
        case 'building':
            return "<svg {$attrs}><path d=\"M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z\"/><path d=\"M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2\"/><path d=\"M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2\"/><path d=\"M10 6h4\"/><path d=\"M10 10h4\"/><path d=\"M10 14h4\"/><path d=\"M10 18h4\"/></svg>";
        case 'bar-chart':
            return "<svg {$attrs}><path d=\"M3 3v18h18\"/><path d=\"M18 17V9\"/><path d=\"M13 17V5\"/><path d=\"M8 17v-3\"/></svg>";
        case 'hard-hat':
            return "<svg {$attrs}><path d=\"M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5\"/><path d=\"M14 6a6 6 0 0 1 6 6v3\"/><path d=\"M4 15v-3a6 6 0 0 1 6-6\"/><rect x=\"2\" y=\"15\" width=\"20\" height=\"4\" rx=\"1\"/></svg>";
        case 'mail':
            return "<svg {$attrs}><rect width=\"20\" height=\"16\" x=\"2\" y=\"4\" rx=\"2\"/><path d=\"m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7\"/></svg>";
        case 'tag':
            return "<svg {$attrs}><path d=\"M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z\"/><circle cx=\"7.5\" cy=\"7.5\" r=\".5\" fill=\"currentColor\"/></svg>";
        case 'zap':
            return "<svg {$attrs}><path d=\"M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z\"/></svg>";
        case 'users':
            return "<svg {$attrs}><path d=\"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2\"/><circle cx=\"9\" cy=\"7\" r=\"4\"/><path d=\"M22 21v-2a4 4 0 0 0-3-3.87\"/><path d=\"M16 3.13a4 4 0 0 1 0 7.75\"/></svg>";
        case 'check':
            return "<svg {$attrs}><path d=\"M20 6 9 17l-5-5\"/></svg>";
        default:
            return "<svg {$attrs}><circle cx=\"12\" cy=\"12\" r=\"9\"/></svg>";
    }
}

/**
 * Format phone / WhatsApp base URL safely and build a pre-filled WhatsApp click-to-chat URL.
 *
 * @param string $message Text message.
 * @param string $custom_base Optional override WhatsApp URL.
 * @return string
 */
function stwc_build_whatsapp_url( $message = '', $custom_base = '' ) {
    if ( function_exists( 'st_build_whatsapp_url' ) && '' === trim( (string) $custom_base ) ) {
        return st_build_whatsapp_url( $message );
    }
    $raw = trim( (string) $custom_base );
    if ( '' === $raw ) {
        $raw = trim( (string) stwc_get_content( 'global.links.whatsapp', '' ) );
    }
    if ( '' === $raw ) {
        $raw = trim( (string) stwc_get_content( 'global.links.whatsappNumber', '0244753849' ) );
    }

    if ( preg_match( '#^https?://#i', $raw ) ) {
        $base = strtok( $raw, '?' );
    } else {
        $digits = preg_replace( '/\D+/', '', $raw );
        if ( 0 === strpos( $digits, '0' ) && strlen( $digits ) >= 9 ) {
            $digits = '233' . substr( $digits, 1 );
        } elseif ( strlen( $digits ) === 9 ) {
            $digits = '233' . $digits;
        }
        if ( '' === $digits ) {
            $digits = '233244753849';
        }
        $base = 'https://wa.me/' . $digits;
    }

    if ( '' === trim( (string) $message ) ) {
        return esc_url_raw( $base );
    }
    return esc_url_raw( $base . '?text=' . rawurlencode( $message ) );
}

/**
 * Helper to fetch CPT records in normalized array format matching theme templates.
 */
function stwc_get_cars( $args = array() ) {
    $defaults = array(
        'post_type'      => 'st_car',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_st_legacy_id',
        'order'          => 'ASC',
    );
    $query = new WP_Query( wp_parse_args( $args, $defaults ) );
    $items = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $id    = $post->ID;
            $type  = get_post_meta( $id, '_st_car_type', true ) ?: 'New';
            $image = get_the_post_thumbnail_url( $id, 'large' ) ?: stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' );
            $items[] = array(
                'id'           => get_post_meta( $id, '_st_legacy_id', true ) ?: $id,
                'post_id'      => $id,
                'name'         => get_the_title( $id ),
                'type'         => $type,
                'price'        => get_post_meta( $id, '_st_price', true ) ?: 'Contact for Price',
                'fuel'         => get_post_meta( $id, '_st_fuel', true ) ?: 'Petrol',
                'transmission' => get_post_meta( $id, '_st_transmission', true ) ?: 'Automatic',
                'year'         => get_post_meta( $id, '_st_year', true ) ?: '2024',
                'mileage'      => get_post_meta( $id, '_st_mileage', true ) ?: '0 km',
                'image'        => $image,
                'url'          => get_permalink( $id ),
            );
        }
    }
    return ! empty( $items ) ? $items : stwc_get_default_cars();
}

/**
 * Helper to fetch Rentals from CPT with exact fallback defaults.
 */
function stwc_get_rentals( $args = array() ) {
    $defaults = array(
        'post_type'      => 'st_rental',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_st_legacy_id',
        'order'          => 'ASC',
    );
    $query = new WP_Query( wp_parse_args( $args, $defaults ) );
    $items = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $id    = $post->ID;
            $type  = get_post_meta( $id, '_st_rental_type', true ) ?: get_the_title( $id );
            $image = get_the_post_thumbnail_url( $id, 'large' ) ?: stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' );
            $items[] = array(
                'id'       => get_post_meta( $id, '_st_legacy_id', true ) ?: $id,
                'post_id'  => $id,
                'name'     => get_the_title( $id ),
                'category' => $type,
                'daily'    => get_post_meta( $id, '_st_daily_rate', true ) ?: 'GH₵ 600',
                'weekly'   => get_post_meta( $id, '_st_weekly_rate', true ) ?: 'GH₵ 3,800',
                'monthly'  => get_post_meta( $id, '_st_monthly_rate', true ) ?: 'GH₵ 14,000',
                'seats'    => get_post_meta( $id, '_st_seats', true ) ?: '5 Seats',
                'trans'    => get_post_meta( $id, '_st_transmission', true ) ?: 'Automatic',
                'fuel'     => get_post_meta( $id, '_st_fuel', true ) ?: 'Petrol',
                'image'    => $image,
                'url'      => get_permalink( $id ),
            );
        }
    }
    return ! empty( $items ) ? $items : stwc_get_default_rentals();
}

/**
 * Helper to fetch Auto Parts from CPT with exact fallback defaults.
 */
function stwc_get_parts( $args = array() ) {
    $defaults = array(
        'post_type'      => 'st_part',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_st_legacy_id',
        'order'          => 'ASC',
    );
    $query = new WP_Query( wp_parse_args( $args, $defaults ) );
    $items = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $id    = $post->ID;
            $image = get_the_post_thumbnail_url( $id, 'large' ) ?: stwc_asset_image( 'image-9-car-battery-maintenance.jpg' );
            $items[] = array(
                'id'       => get_post_meta( $id, '_st_legacy_id', true ) ?: $id,
                'post_id'  => $id,
                'name'     => get_the_title( $id ),
                'brand'    => get_post_meta( $id, '_st_brand', true ) ?: 'OEM Genuine',
                'category' => get_post_meta( $id, '_st_category', true ) ?: 'Auto Parts',
                'price'    => get_post_meta( $id, '_st_price', true ) ?: 'GH₵ 450',
                'sku'      => get_post_meta( $id, '_st_sku', true ) ?: 'ST-' . $id,
                'rating'   => (float) ( get_post_meta( $id, '_st_rating', true ) ?: 5.0 ),
                'image'    => $image,
                'url'      => get_permalink( $id ),
            );
        }
    }
    return ! empty( $items ) ? $items : stwc_get_default_parts();
}

/**
 * Helper to fetch FAQs from CPT with exact fallback defaults.
 */
function stwc_get_faqs( $category = '' ) {
    $args = array(
        'post_type'      => 'st_faq',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    );
    if ( $category && 'All' !== $category ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'st_faq_category',
                'field'    => 'slug',
                'terms'    => sanitize_title( $category ),
            ),
        );
    }
    $query = new WP_Query( $args );
    $items = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'st_faq_category', array( 'fields' => 'names' ) );
            $cat   = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : 'General';
            $items[] = array(
                'id'       => $post->ID,
                'category' => $cat,
                'q'        => get_the_title( $post->ID ),
                'a'        => wp_strip_all_tags( $post->post_content ),
            );
        }
    }
    if ( ! empty( $items ) ) {
        return $items;
    }
    $defaults = stwc_get_default_faqs();
    if ( $category && 'All' !== $category ) {
        return array_values(
            array_filter(
                $defaults,
                function ( $f ) use ( $category ) {
                    return strcasecmp( $f['category'], $category ) === 0;
                }
            )
        );
    }
    return $defaults;
}

/**
 * Helper to fetch Gallery items from CPT with fallback defaults.
 */
function stwc_get_gallery_items() {
    $query = new WP_Query(
        array(
            'post_type'      => 'st_gallery',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    $items = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'st_gallery_category', array( 'fields' => 'names' ) );
            $cat   = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : 'Workshop';
            $url   = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: stwc_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' );
            $items[] = array(
                'id'         => $post->ID,
                'title'      => get_the_title( $post->ID ),
                'category'   => $cat,
                'url'        => $url,
                'span'       => get_post_meta( $post->ID, '_st_span', true ) ?: 'col-span-1 row-span-1',
                'media_type' => get_post_meta( $post->ID, '_st_media_type', true ) ?: 'photo',
                'location'   => get_post_meta( $post->ID, '_st_location', true ) ?: 'Accra',
            );
        }
    }
    return ! empty( $items ) ? $items : stwc_get_default_gallery_items();
}

/**
 * Helper to fetch Career job openings from CPT with fallback defaults.
 */
function stwc_get_careers() {
    $query = new WP_Query(
        array(
            'post_type'      => 'st_career',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    $items = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'st_department', array( 'fields' => 'names' ) );
            $dept  = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : 'Towing & Recovery';
            $resp  = json_decode( (string) get_post_meta( $post->ID, '_st_responsibilities_json', true ), true );
            $reqs  = json_decode( (string) get_post_meta( $post->ID, '_st_requirements_json', true ), true );
            $bens  = json_decode( (string) get_post_meta( $post->ID, '_st_benefits_json', true ), true );

            $items[] = array(
                'id'               => $post->ID,
                'title'            => get_the_title( $post->ID ),
                'department'       => $dept,
                'location'         => get_post_meta( $post->ID, '_st_location', true ) ?: 'Accra Main Workshop',
                'type'             => get_post_meta( $post->ID, '_st_employment_type', true ) ?: 'Full-time',
                'description'      => wp_strip_all_tags( $post->post_content ),
                'responsibilities' => is_array( $resp ) ? $resp : array(),
                'requirements'     => is_array( $reqs ) ? $reqs : array(),
                'benefits'         => is_array( $bens ) ? $bens : array(),
            );
        }
    }
    return ! empty( $items ) ? $items : stwc_get_default_careers();
}
