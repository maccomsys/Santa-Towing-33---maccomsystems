<?php
/**
 * Plugin Name:       Santa Towing Website Core
 * Plugin URI:        https://santatowingservices.com/
 * Description:       Core engine for Santa Towing & Garage Services Ltd. — registers Custom Post Types, Taxonomies, Post Meta, URL Rewrites & 301 Redirects, Site Settings & SEO, Secure AJAX Forms with WhatsApp Dispatch, and Elementor Free Custom Widgets.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.1
 * Author:            Santa Towing & Garage Services Ltd.
 * Author URI:        https://santatowingservices.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       santa-towing-website-core
 * Domain Path:       /languages
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'STWC_VERSION', '1.0.0' );
define( 'STWC_FILE', __FILE__ );
define( 'STWC_DIR', plugin_dir_path( __FILE__ ) );
define( 'STWC_URL', plugin_dir_url( __FILE__ ) );
define( 'STWC_BASENAME', plugin_basename( __FILE__ ) );
define( 'STWC_TEXT_DOMAIN', 'santa-towing-website-core' );

// Core Modules
require_once STWC_DIR . 'includes/helpers.php';
require_once STWC_DIR . 'includes/data-defaults.php';
require_once STWC_DIR . 'includes/class-cpts.php';
require_once STWC_DIR . 'includes/class-settings.php';
require_once STWC_DIR . 'includes/class-rewrites.php';
require_once STWC_DIR . 'includes/class-forms.php';
require_once STWC_DIR . 'includes/class-elementor.php';

/**
 * Main Plugin Coordinator Class (Singleton).
 */
final class Santa_Towing_Website_Core {

    /**
     * Singleton instance.
     *
     * @var Santa_Towing_Website_Core|null
     */
    private static $instance = null;

    /**
     * Return singleton instance.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize plugin subsystems.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'load_textdomain' ), 1 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ), 20 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_notices', array( $this, 'elementor_admin_notice' ) );
        add_filter( 'plugin_action_links_' . STWC_BASENAME, array( $this, 'add_plugin_action_links' ) );

        // Instantiate sub-modules.
        STWC_CPTs::instance();
        STWC_Settings::instance();
        STWC_Rewrites::instance();
        STWC_Forms::instance();
        STWC_Elementor::instance();
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            STWC_TEXT_DOMAIN,
            false,
            dirname( STWC_BASENAME ) . '/languages'
        );
    }

    /**
     * Enqueue plugin CSS & JS on frontend & Elementor preview.
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'santa-towing-core',
            STWC_URL . 'assets/css/santa-core.css',
            array(),
            STWC_VERSION
        );

        wp_enqueue_script(
            'santa-towing-core',
            STWC_URL . 'assets/js/santa-core.js',
            array(),
            STWC_VERSION,
            true
        );

        wp_localize_script(
            'santa-towing-core',
            'stwcConfig',
            array(
                'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
                'nonce'           => wp_create_nonce( 'stwc_form_nonce' ),
                'whatsappUrl'     => stwc_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' ),
                'whatsappNumber'  => stwc_get_content( 'global.links.whatsappNumber', '0244753849' ),
                'phoneDisplay'    => stwc_get_content( 'global.links.phone', '0244753849' ),
                'phoneTel'        => stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' ),
                'brandName'       => stwc_get_content( 'global.brand.shortName', 'Santa Towing' ),
                'ghanaRegions'    => stwc_get_ghana_regions_data(),
                'i18n'            => array(
                    'submitting'    => __( 'Sending request...', 'santa-towing-website-core' ),
                    'networkError'  => __( 'Connection error. Please try WhatsApp or call us directly.', 'santa-towing-website-core' ),
                    'selectRegion'  => __( 'Please select a Region in Ghana first.', 'santa-towing-website-core' ),
                    'selectArea'    => __( 'Please select an Area / Town.', 'santa-towing-website-core' ),
                    'fileTooLarge'  => __( 'File must be 5MB or smaller (PDF, DOC, DOCX).', 'santa-towing-website-core' ),
                ),
            )
        );
    }

    /**
     * Enqueue admin assets on Santa Towing admin screens & CPT editors.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        $is_stwc_screen = ( false !== strpos( $hook, 'santa-towing' ) );
        $is_st_cpt      = ( $screen && in_array( $screen->post_type, array( 'st_car', 'st_rental', 'st_part', 'st_gallery', 'st_faq', 'st_career', 'st_service_area' ), true ) );

        if ( ! $is_stwc_screen && ! $is_st_cpt ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style(
            'santa-towing-core-admin',
            STWC_URL . 'assets/css/santa-core-admin.css',
            array( 'wp-color-picker' ),
            STWC_VERSION
        );
        wp_enqueue_script(
            'santa-towing-core-admin',
            STWC_URL . 'assets/js/santa-core-admin.js',
            array( 'jquery', 'wp-color-picker' ),
            STWC_VERSION,
            true
        );
    }

    /**
     * Show gentle non-blocking notice if Elementor Free is not active.
     */
    public function elementor_admin_notice() {
        if ( ! current_user_can( 'install_plugins' ) ) {
            return;
        }
        if ( did_action( 'elementor/loaded' ) ) {
            return;
        }
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( ! $screen || ! in_array( $screen->id, array( 'plugins', 'dashboard', 'toplevel_page_santa-towing-settings' ), true ) ) {
            return;
        }
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php esc_html_e( 'Santa Towing Website Core:', 'santa-towing-website-core' ); ?></strong>
                <?php esc_html_e( 'All Custom Post Types, URL Rewrites, Settings, and Forms are active. Install & activate the free Elementor plugin to unlock the 17 custom Santa Towing Elementor Free widgets.', 'santa-towing-website-core' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Add quick action links in Plugins list.
     *
     * @param array $links Existing links.
     * @return array
     */
    public function add_plugin_action_links( $links ) {
        $settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=santa-towing-settings' ) ) . '">' . esc_html__( 'Site Settings', 'santa-towing-website-core' ) . '</a>';
        $inquiries_link = '<a href="' . esc_url( admin_url( 'admin.php?page=santa-towing-inquiries' ) ) . '">' . esc_html__( 'Inquiries', 'santa-towing-website-core' ) . '</a>';
        array_unshift( $links, $settings_link, $inquiries_link );
        return $links;
    }

    /**
     * Plugin activation callback: create DB tables, register CPTs/rewrites, flush rules.
     */
    public static function activate() {
        STWC_Forms::create_table();
        STWC_CPTs::register_post_types();
        STWC_CPTs::register_taxonomies();
        STWC_Rewrites::add_rewrite_rules();

        // Seed default options only if missing so existing edits are preserved.
        if ( false === get_option( 'santa_towing_site_content', false ) ) {
            update_option( 'santa_towing_site_content', stwc_get_default_site_content(), false );
        }

        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation callback: flush rewrite rules cleanly.
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}

register_activation_hook( __FILE__, array( 'Santa_Towing_Website_Core', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Santa_Towing_Website_Core', 'deactivate' ) );

// Boot plugin.
add_action(
    'plugins_loaded',
    function () {
        Santa_Towing_Website_Core::instance();
    },
    5
);
