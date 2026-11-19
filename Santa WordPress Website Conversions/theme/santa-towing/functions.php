<?php
/**
 * Santa Towing Theme Functions & Definitions
 *
 * @package Santa_Towing
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SANTA_TOWING_VERSION', '1.0.0' );
define( 'SANTA_TOWING_DIR', get_template_directory() );
define( 'SANTA_TOWING_URI', get_template_directory_uri() );

if ( ! isset( $content_width ) ) {
    $content_width = 1280;
}

/**
 * 1. Theme Setup
 */
function santa_towing_setup() {
    load_theme_textdomain( 'santa-towing', SANTA_TOWING_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support(
        'html5',
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
    );
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 80,
            'width'       => 260,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    // Elementor Free Compatibility
    add_theme_support( 'elementor' );

    register_nav_menus(
        array(
            'primary'         => __( 'Primary Navigation (Desktop & Mobile)', 'santa-towing' ),
            'footer_quick'    => __( 'Footer Quick Links', 'santa-towing' ),
            'footer_services' => __( 'Footer Services Links', 'santa-towing' ),
            'footer_legal'    => __( 'Footer Legal & Policy Links', 'santa-towing' ),
        )
    );
}
add_action( 'after_setup_theme', 'santa_towing_setup' );

/**
 * 2. Enqueue Styles & Scripts (Zero Node.js, Zero React, Pre-compiled Static Tailwind CSS)
 */
function santa_towing_scripts() {
    wp_enqueue_style(
        'santa-towing-tailwind',
        SANTA_TOWING_URI . '/assets/css/santa-styles.css',
        array(),
        SANTA_TOWING_VERSION
    );

    wp_enqueue_style(
        'santa-towing-style',
        get_stylesheet_uri(),
        array( 'santa-towing-tailwind' ),
        SANTA_TOWING_VERSION
    );

    wp_enqueue_script(
        'santa-towing-motion',
        SANTA_TOWING_URI . '/assets/js/santa-motion.js',
        array(),
        SANTA_TOWING_VERSION,
        true
    );

    wp_enqueue_script(
        'santa-towing-main',
        SANTA_TOWING_URI . '/assets/js/santa-main.js',
        array(),
        SANTA_TOWING_VERSION,
        true
    );

    wp_localize_script(
        'santa-towing-main',
        'santaTowingConfig',
        array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'santa_towing_public_nonce' ),
            'homeUrl'      => home_url( '/' ),
            'searchUrl'    => home_url( '/search/' ),
            'isFrontPage'  => is_front_page(),
            'whatsappUrl'  => function_exists( 'st_build_whatsapp_url' ) ? st_build_whatsapp_url() : st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' ),
            'phoneDisplay' => st_get_content( 'global.links.phone', '0244753849' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'santa_towing_scripts' );

/**
 * 3. Inject Dynamic Theme Color Tokens (:root CSS Vars) + Favicon + Meta Fallback
 */
function santa_towing_head_tokens() {
    $primary   = sanitize_hex_color( st_get_content( 'theme.primary', '#015557' ) ) ?: '#015557';
    $secondary = sanitize_hex_color( st_get_content( 'theme.secondary', '#192e2e' ) ) ?: '#192e2e';
    $accent    = sanitize_hex_color( st_get_content( 'theme.accent', '#d80000' ) ) ?: '#d80000';
    $dark      = sanitize_hex_color( st_get_content( 'theme.dark', '#131514' ) ) ?: '#131514';
    $white     = sanitize_hex_color( st_get_content( 'theme.white', '#ffffff' ) ) ?: '#ffffff';
    $black     = sanitize_hex_color( st_get_content( 'theme.black', '#000000' ) ) ?: '#000000';

    echo "<style id=\"santa-towing-theme-tokens\">\n:root {\n";
    echo "  --color-primary: {$primary};\n";
    echo "  --color-secondary: {$secondary};\n";
    echo "  --color-accent: {$accent};\n";
    echo "  --color-dark: {$dark};\n";
    echo "  --color-white: {$white};\n";
    echo "  --color-black: {$black};\n";
    echo "}\n</style>\n";

    // Favicon sync if WP site_icon not already set
    if ( ! has_site_icon() ) {
        $favicon = st_get_logo_url();
        echo '<link rel="icon" type="image/png" href="' . esc_url( $favicon ) . "\" />\n";
    }
}
add_action( 'wp_head', 'santa_towing_head_tokens', 5 );

/**
 * 4. Disable Elementor Default Google Fonts to Guarantee Exact System UI Typography
 */
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/**
 * Ensure full-width container behavior on Elementor templates
 */
function santa_towing_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'st-is-home';
    } else {
        $classes[] = 'st-is-inner';
    }
    if ( is_page_template( 'page-templates/template-elementor-canvas.php' ) ) {
        $classes[] = 'st-elementor-canvas';
    }
    return $classes;
}
add_filter( 'body_class', 'santa_towing_body_classes' );

/**
 * 5. Content & Site Settings Accessor (`santa_towing_site_content` WP Option)
 */
function st_get_default_site_content() {
    static $defaults = null;
    if ( null !== $defaults ) {
        return $defaults;
    }

    $defaults = array(
        'theme'  => array(
            'primary'   => '#015557',
            'secondary' => '#192e2e',
            'accent'    => '#d80000',
            'dark'      => '#131514',
            'white'     => '#ffffff',
            'black'     => '#000000',
        ),
        'global' => array(
            'brand'           => array(
                'name'      => 'Santa Towing & Garage Services Ltd.',
                'shortName' => 'Santa Towing',
                'tagline'   => '24/7 Emergency Towing, Haulage & Automotive Services in Ghana',
            ),
            'logo'            => array(
                'url' => SANTA_TOWING_URI . '/assets/images/santa-logo.png',
                'alt' => 'Santa Towing & Garage Services Ltd. Logo',
            ),
            'links'           => array(
                'phone'           => '0244753849',
                'phoneDisplay'    => '0244753849',
                'phoneFormatted'  => '024 475 3849',
                'phoneTel'        => 'tel:0244753849',
                'whatsapp'        => 'https://wa.me/233244753849',
                'whatsappNumber'  => '0244753849',
                'whatsappDisplay' => 'WhatsApp: 0244753849',
                'email'           => 'info@santatowing.com',
                'address'         => '123 Spintex Road, Accra, Ghana',
                'hours'           => '24/7 Towing Service',
                'coverage'        => 'Nationwide Coverage',
            ),
            'social'          => array(
                'facebook'  => 'https://facebook.com/santatowing',
                'twitter'   => 'https://twitter.com/santatowing',
                'instagram' => 'https://instagram.com/santatowing',
                'linkedin'  => 'https://linkedin.com/company/santatowing',
            ),
            'nav'             => array(
                'home'            => 'Home',
                'towingDropdown'  => 'Towing Services',
                'emergencyTowing' => 'Emergency Towing',
                'serviceAreas'    => 'Service Areas Directory',
                'haulage'         => 'Haulage Services',
                'carSales'        => 'Car Sales',
                'carRentals'      => 'Car Rentals',
                'garageDropdown'  => 'Garage Services',
                'repairs'         => 'Auto Repairs & Diagnostics',
                'bodyWorks'       => 'Auto Body Works & Spraying',
                'parts'           => 'Genuine Auto Parts',
                'fleet'           => 'Corporate Fleet Services',
                'allServices'     => 'All Services Overview',
                'companyDropdown' => 'Company',
                'gallery'         => 'Gallery',
                'blog'            => 'Blog & Advice',
                'faqs'            => 'FAQs',
                'sitemap'         => 'Website Sitemap',
                'contact'         => 'Contact',
                'search'          => 'Search',
                'callNow'         => 'Call Now: 0244753849',
                'menuLinks'       => null,
            ),
            'footer'          => array(
                'about'                 => "Ghana's premier automotive partner for 24/7 rapid emergency towing, heavy car recovery, certified garage repairs, car sales, and rental fleet solutions.",
                'quickLinksTitle'       => 'Quick Links',
                'servicesTitle'         => 'Our Services',
                'legalTitle'            => 'Legal and Policies',
                'stayConnectedTitle'    => 'Stay Connected',
                'newsletterText'        => 'Subscribe for roadside safety tips, car maintenance reminders, and exclusive service discounts.',
                'newsletterPlaceholder' => 'Enter your email',
                'newsletterButton'      => 'Subscribe Now',
                'newsletterSuccess'     => 'Thank you! You have successfully subscribed to Santa Towing updates.',
                'copyright'             => 'Santa Towing & Garage Services Ltd. All rights reserved.',
            ),
            'floatingButtons' => array(
                'towTooltip'      => 'Request Emergency Tow',
                'whatsappTooltip' => 'Chat on WhatsApp',
            ),
        ),
        'serviceAreas' => array(
            'template' => array(
                'ctaHeading'       => 'Need Immediate Assistance?',
                'ctaSubtext'       => 'Call or WhatsApp us now to book your tow.',
                'callBtnLabel'     => 'Call Now',
                'whatsappBtnLabel' => 'WhatsApp Us',
                'prevAreaLabel'    => 'Previous Area',
                'nextAreaLabel'    => 'Next Area',
            ),
        ),
    );

    return $defaults;
}

/**
 * Retrieve dot-notation value from `santa_towing_site_content` WP option with exact defaults
 */
function st_get_content( $path, $fallback = '' ) {
    static $stored = null;
    if ( null === $stored ) {
        $opt    = get_option( 'santa_towing_site_content', array() );
        $stored = is_array( $opt ) ? $opt : array();
    }

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

    // Fallback to theme defaults array
    $cur   = st_get_default_site_content();
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
 * Normalize Ghana / international phone or wa.me URL and optionally append a pre-filled UTF-8 message.
 *
 * @param string $message     Optional pre-filled WhatsApp message.
 * @param string $custom_base Optional override phone or wa.me URL.
 * @return string
 */
function st_build_whatsapp_url( $message = '', $custom_base = '' ) {
    $raw = trim( (string) $custom_base );
    if ( '' === $raw ) {
        $raw = trim( (string) st_get_content( 'global.links.whatsapp', '' ) );
    }
    if ( '' === $raw ) {
        $raw = trim( (string) st_get_content( 'global.links.whatsappNumber', '0244753849' ) );
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
        $default_msg = trim( (string) st_get_content( 'global.links.whatsappMessage', '' ) );
        if ( '' !== $default_msg && '' === trim( (string) $custom_base ) && false === strpos( $raw, '?text=' ) ) {
            return esc_url_raw( $base . '?text=' . rawurlencode( $default_msg ) );
        }
        return esc_url_raw( $base );
    }

    return esc_url_raw( $base . '?text=' . rawurlencode( $message ) );
}

/**
 * Resolve Logo URL (Custom Logo > Admin Option > Theme Asset)
 */
function st_get_logo_url() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $img = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        if ( $img ) {
            return $img;
        }
    }
    $opt_logo = st_get_content( 'global.logo.url', '' );
    if ( $opt_logo && 0 !== strpos( $opt_logo, '/src/' ) ) {
        return $opt_logo;
    }
    return SANTA_TOWING_URI . '/assets/images/santa-logo.png';
}

/**
 * Helper to resolve local bundled theme image assets
 */
function st_theme_image( $filename ) {
    return SANTA_TOWING_URI . '/assets/images/' . ltrim( $filename, '/' );
}

/**
 * Format safe internal route link supporting WordPress Permalinks cleanly
 */
function st_route_url( $path ) {
    $path = trim( (string) $path );
    if ( empty( $path ) || '/' === $path ) {
        return home_url( '/' );
    }
    if ( 0 === strpos( $path, 'http://' ) || 0 === strpos( $path, 'https://' ) || 0 === strpos( $path, 'tel:' ) || 0 === strpos( $path, 'mailto:' ) || 0 === strpos( $path, '#' ) ) {
        return $path;
    }
    return home_url( '/' . ltrim( $path, '/' ) );
}

/**
 * Render Breadcrumbs Helper
 */
function st_breadcrumbs( $items = array(), $variant = 'dark', $class = '' ) {
    get_template_part(
        'template-parts/breadcrumbs',
        null,
        array(
            'items'   => $items,
            'variant' => $variant,
            'class'   => $class,
        )
    );
}

/**
 * Check if current page is built with Elementor Free
 */
function st_is_elementor_page( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_queried_object_id();
    }
    if ( ! $post_id || ! class_exists( '\Elementor\Plugin' ) ) {
        return false;
    }
    try {
        $document = \Elementor\Plugin::$instance->documents->get( $post_id );
        return $document && $document->is_built_with_elementor();
    } catch ( \Exception $e ) {
        return false;
    }
}

/**
 * Compute estimated reading time for blog posts
 */
function st_reading_time( $post_id = 0 ) {
    $content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
    $words   = str_word_count( wp_strip_all_tags( (string) $content ) );
    $minutes = max( 1, (int) ceil( $words / 200 ) );
    return sprintf( _n( '%d min read', '%d min read', $minutes, 'santa-towing' ), $minutes );
}

/**
 * Inline Lucide SVG Helper (Exact parity with React lucide-react strokes, zero external dependencies)
 */
function st_icon( $name, $class = 'w-4 h-4' ) {
    $attrs = 'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="' . esc_attr( $class ) . '"';

    switch ( $name ) {
        case 'phone':
            return "<svg {$attrs}><path d=\"M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z\"/></svg>";
        case 'message-circle':
            return "<svg {$attrs}><path d=\"M7.9 20A9 9 0 1 0 4 16.1L2 22Z\"/></svg>";
        case 'clock':
            return "<svg {$attrs}><circle cx=\"12\" cy=\"12\" r=\"10\"/><polyline points=\"12 6 12 12 16 14\"/></svg>";
        case 'map-pin':
            return "<svg {$attrs}><path d=\"M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z\"/><circle cx=\"12\" cy=\"10\" r=\"3\"/></svg>";
        case 'map-pin-off':
            return "<svg {$attrs}><path d=\"M12.75 7.09a3 3 0 0 1 2.16 2.16\"/><path d=\"M17.072 17.072c-1.634 2.17-3.527 3.912-4.471 4.727a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 1.432-4.568\"/><path d=\"m2 2 20 20\"/><path d=\"M8.475 2.818A8 8 0 0 1 20 10c0 1.183-.31 2.377-.81 3.533\"/><path d=\"M9.13 9.13a2.78 2.78 0 0 0-.13.87 3 3 0 0 0 3.87 2.87\"/></svg>";
        case 'facebook':
            return "<svg {$attrs}><path d=\"M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z\"/></svg>";
        case 'twitter':
            return "<svg {$attrs}><path d=\"M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z\"/></svg>";
        case 'instagram':
            return "<svg {$attrs}><rect width=\"20\" height=\"20\" x=\"2\" y=\"2\" rx=\"5\" ry=\"5\"/><path d=\"M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z\"/><line x1=\"17.5\" x2=\"17.51\" y1=\"6.5\" y2=\"6.5\"/></svg>";
        case 'linkedin':
            return "<svg {$attrs}><path d=\"M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z\"/><rect width=\"4\" height=\"12\" x=\"2\" y=\"9\"/><circle cx=\"4\" cy=\"4\" r=\"2\"/></svg>";
        case 'chevron-down':
            return "<svg {$attrs}><path d=\"m6 9 6 6 6-6\"/></svg>";
        case 'chevron-right':
            return "<svg {$attrs}><path d=\"m9 18 6-6-6-6\"/></svg>";
        case 'chevron-left':
            return "<svg {$attrs}><path d=\"m15 18-6-6 6-6\"/></svg>";
        case 'arrow-right':
            return "<svg {$attrs}><path d=\"M5 12h14\"/><path d=\"m12 5 7 7-7 7\"/></svg>";
        case 'arrow-left':
            return "<svg {$attrs}><path d=\"m12 19-7-7 7-7\"/><path d=\"M19 12H5\"/></svg>";
        case 'arrow-down':
            return "<svg {$attrs}><path d=\"M12 5v14\"/><path d=\"m19 12-7 7-7-7\"/></svg>";
        case 'search':
            return "<svg {$attrs}><circle cx=\"11\" cy=\"11\" r=\"8\"/><path d=\"m21 21-4.3-4.3\"/></svg>";
        case 'menu':
            return "<svg {$attrs}><line x1=\"4\" x2=\"20\" y1=\"12\" y2=\"12\"/><line x1=\"4\" x2=\"20\" y1=\"6\" y2=\"6\"/><line x1=\"4\" x2=\"20\" y1=\"18\" y2=\"18\"/></svg>";
        case 'x':
            return "<svg {$attrs}><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg>";
        case 'truck':
            return "<svg {$attrs}><path d=\"M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2\"/><path d=\"M15 18H9\"/><path d=\"M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14\"/><circle cx=\"17\" cy=\"18\" r=\"2\"/><circle cx=\"7\" cy=\"18\" r=\"2\"/></svg>";
        case 'home':
            return "<svg {$attrs}><path d=\"m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z\"/><polyline points=\"9 22 9 12 15 12 15 22\"/></svg>";
        case 'calendar':
            return "<svg {$attrs}><path d=\"M8 2v4\"/><path d=\"M16 2v4\"/><rect width=\"18\" height=\"18\" x=\"3\" y=\"4\" rx=\"2\"/><path d=\"M3 10h18\"/></svg>";
        case 'wrench':
            return "<svg {$attrs}><path d=\"M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z\"/></svg>";
        case 'car':
            return "<svg {$attrs}><path d=\"M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2\"/><circle cx=\"7\" cy=\"17\" r=\"2\"/><path d=\"M9 17h6\"/><circle cx=\"17\" cy=\"17\" r=\"2\"/></svg>";
        case 'shield':
            return "<svg {$attrs}><path d=\"M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z\"/></svg>";
        case 'shield-check':
            return "<svg {$attrs}><path d=\"M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z\"/><path d=\"m9 12 2 2 4-4\"/></svg>";
        case 'file-text':
            return "<svg {$attrs}><path d=\"M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z\"/><path d=\"M14 2v4a2 2 0 0 0 2 2h4\"/><path d=\"M10 9H8\"/><path d=\"M16 13H8\"/><path d=\"M16 17H8\"/></svg>";
        case 'check-circle':
            return "<svg {$attrs}><path d=\"M22 11.08V12a10 10 0 1 1-5.93-9.14\"/><polyline points=\"22 4 12 14.01 9 11.01\"/></svg>";
        case 'check':
            return "<svg {$attrs}><path d=\"M20 6 9 17l-5-5\"/></svg>";
        case 'tag':
            return "<svg {$attrs}><path d=\"M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z\"/><circle cx=\"7.5\" cy=\"7.5\" r=\".5\" fill=\"currentColor\"/></svg>";
        case 'fuel':
            return "<svg {$attrs}><line x1=\"3\" x2=\"15\" y1=\"22\" y2=\"22\"/><line x1=\"4\" x2=\"14\" y1=\"9\" y2=\"9\"/><path d=\"M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18\"/><path d=\"M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5\"/></svg>";
        case 'settings':
            return "<svg {$attrs}><path d=\"M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z\"/><circle cx=\"12\" cy=\"12\" r=\"3\"/></svg>";
        case 'users':
            return "<svg {$attrs}><path d=\"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2\"/><circle cx=\"9\" cy=\"7\" r=\"4\"/><path d=\"M22 21v-2a4 4 0 0 0-3-3.87\"/><path d=\"M16 3.13a4 4 0 0 1 0 7.75\"/></svg>";
        case 'eye':
            return "<svg {$attrs}><path d=\"M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0\"/><circle cx=\"12\" cy=\"12\" r=\"3\"/></svg>";
        case 'heart':
            return "<svg {$attrs}><path d=\"M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z\"/></svg>";
        case 'share-2':
            return "<svg {$attrs}><circle cx=\"18\" cy=\"5\" r=\"3\"/><circle cx=\"6\" cy=\"12\" r=\"3\"/><circle cx=\"18\" cy=\"19\" r=\"3\"/><line x1=\"8.59\" x2=\"15.42\" y1=\"13.51\" y2=\"17.49\"/><line x1=\"15.41\" x2=\"8.59\" y1=\"6.51\" y2=\"10.49\"/></svg>";
        case 'star':
            return "<svg {$attrs}><polygon points=\"12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2\"/></svg>";
        case 'star-filled':
            $fattrs = str_replace( 'fill="none"', 'fill="currentColor"', $attrs );
            return "<svg {$fattrs}><polygon points=\"12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2\"/></svg>";
        case 'shopping-cart':
            return "<svg {$attrs}><circle cx=\"8\" cy=\"21\" r=\"1\"/><circle cx=\"19\" cy=\"21\" r=\"1\"/><path d=\"M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12\"/></svg>";
        case 'rotate-ccw':
            return "<svg {$attrs}><path d=\"M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8\"/><path d=\"M3 3v5h5\"/></svg>";
        case 'alert-triangle':
            return "<svg {$attrs}><path d=\"m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3\"/><path d=\"M12 9v4\"/><path d=\"M12 17h.01\"/></svg>";
        case 'alert-circle':
            return "<svg {$attrs}><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"12\" x2=\"12\" y1=\"8\" y2=\"12\"/><line x1=\"12\" x2=\"12.01\" y1=\"16\" y2=\"16\"/></svg>";
        case 'award':
            return "<svg {$attrs}><path d=\"m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526\"/><circle cx=\"12\" cy=\"8\" r=\"6\"/></svg>";
        case 'info':
            return "<svg {$attrs}><circle cx=\"12\" cy=\"12\" r=\"10\"/><path d=\"M12 16v-4\"/><path d=\"M12 8h.01\"/></svg>";
        case 'calculator':
            return "<svg {$attrs}><rect width=\"16\" height=\"20\" x=\"4\" y=\"2\" rx=\"2\"/><line x1=\"8\" x2=\"16\" y1=\"6\" y2=\"6\"/><line x1=\"16\" x2=\"16\" y1=\"14\" y2=\"18\"/><path d=\"M16 10h.01\"/><path d=\"M12 10h.01\"/><path d=\"M8 10h.01\"/><path d=\"M12 14h.01\"/><path d=\"M8 14h.01\"/><path d=\"M12 18h.01\"/><path d=\"M8 18h.01\"/></svg>";
        case 'link-2':
            return "<svg {$attrs}><path d=\"M9 17H7A5 5 0 0 1 7 7h2\"/><path d=\"M15 7h2a5 5 0 1 1 0 10h-2\"/><line x1=\"8\" x2=\"16\" y1=\"12\" y2=\"12\"/></svg>";
        case 'folder-tree':
            return "<svg {$attrs}><path d=\"M20 10a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2.5a1 1 0 0 1-.8-.4l-.9-1.2A1 1 0 0 0 15 3h-2a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1Z\"/><path d=\"M20 21a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1h-2.9a1 1 0 0 1-.88-.55l-.42-.85a1 1 0 0 0-.9-.6H13a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1Z\"/><path d=\"M3 5a2 2 0 0 0 2 2h3\"/><path d=\"M3 3v13a2 2 0 0 0 2 2h3\"/></svg>";
        case 'lock':
            return "<svg {$attrs}><rect width=\"18\" height=\"11\" x=\"3\" y=\"11\" rx=\"2\" ry=\"2\"/><path d=\"M7 11V7a5 5 0 0 1 10 0v4\"/></svg>";
        case 'book-open':
            return "<svg {$attrs}><path d=\"M12 7v14\"/><path d=\"M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z\"/></svg>";
        case 'map':
            return "<svg {$attrs}><path d=\"M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z\"/><path d=\"M15 5.764v15\"/><path d=\"M9 3.236v15\"/></svg>";
        case 'play':
            $fattrs = str_replace( 'fill="none"', 'fill="currentColor"', $attrs );
            return "<svg {$fattrs}><polygon points=\"6 3 20 12 6 21 6 3\"/></svg>";
        case 'pause':
            $fattrs = str_replace( 'fill="none"', 'fill="currentColor"', $attrs );
            return "<svg {$fattrs}><rect x=\"14\" y=\"4\" width=\"4\" height=\"16\" rx=\"1\"/><rect x=\"6\" y=\"4\" width=\"4\" height=\"16\" rx=\"1\"/></svg>";
        case 'battery':
            return "<svg {$attrs}><rect width=\"16\" height=\"10\" x=\"2\" y=\"7\" rx=\"2\" ry=\"2\"/><line x1=\"22\" x2=\"22\" y1=\"11\" y2=\"13\"/></svg>";
        case 'disc':
            return "<svg {$attrs}><circle cx=\"12\" cy=\"12\" r=\"10\"/><circle cx=\"12\" cy=\"12\" r=\"2\"/></svg>";
        case 'zap':
            return "<svg {$attrs}><path d=\"M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z\"/></svg>";
        case 'anchor':
            return "<svg {$attrs}><path d=\"M12 22V8\"/><path d=\"M5 12H2a10 10 0 0 0 20 0h-3\"/><circle cx=\"12\" cy=\"5\" r=\"3\"/></svg>";
        case 'package':
            return "<svg {$attrs}><path d=\"m7.5 4.27 9 5.15\"/><path d=\"M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z\"/><path d=\"m3.3 7 8.7 5 8.7-5\"/><path d=\"M12 22V12\"/></svg>";
        case 'building':
            return "<svg {$attrs}><path d=\"M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z\"/><path d=\"M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2\"/><path d=\"M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2\"/><path d=\"M10 6h4\"/><path d=\"M10 10h4\"/><path d=\"M10 14h4\"/><path d=\"M10 18h4\"/></svg>";
        case 'bar-chart':
            return "<svg {$attrs}><path d=\"M3 3v18h18\"/><path d=\"M18 17V9\"/><path d=\"M13 17V5\"/><path d=\"M8 17v-3\"/></svg>";
        case 'hard-hat':
            return "<svg {$attrs}><path d=\"M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5\"/><path d=\"M14 6a6 6 0 0 1 6 6v3\"/><path d=\"M4 15v-3a6 6 0 0 1 6-6\"/><rect x=\"2\" y=\"15\" width=\"20\" height=\"4\" rx=\"1\"/></svg>";
        case 'mail':
            return "<svg {$attrs}><rect width=\"20\" height=\"16\" x=\"2\" y=\"4\" rx=\"2\"/><path d=\"m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7\"/></svg>";
        default:
            return '';
    }
}

/**
 * 6. Footer Newsletter AJAX Handler (Standalone Theme Support + Stage 5 Table Sync)
 */
function santa_towing_ajax_newsletter() {
    check_ajax_referer( 'santa_towing_public_nonce', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'santa-towing' ) ), 400 );
    }

    $subscribers = get_option( 'santa_towing_newsletter_subscribers', array() );
    if ( ! is_array( $subscribers ) ) {
        $subscribers = array();
    }
    if ( ! in_array( $email, $subscribers, true ) ) {
        $subscribers[] = $email;
        update_option( 'santa_towing_newsletter_subscribers', $subscribers, false );
    }

    wp_send_json_success(
        array(
            'message' => st_get_content( 'global.footer.newsletterSuccess', 'Thank you! You have successfully subscribed to Santa Towing updates.' ),
        )
    );
}
add_action( 'wp_ajax_st_newsletter_subscribe', 'santa_towing_ajax_newsletter' );
add_action( 'wp_ajax_nopriv_st_newsletter_subscribe', 'santa_towing_ajax_newsletter' );
