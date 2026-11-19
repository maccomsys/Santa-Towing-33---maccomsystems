<?php
/**
 * Admin Site Settings, Color & Elementor Kit Sync, Inquiries Manager & Diagnostics
 *
 * Stores all nested site configuration inside `santa_towing_site_content` WP Option
 * matching theme `st_get_content()` dot-notation contract cleanly.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class STWC_Settings {

    /**
     * Singleton instance.
     *
     * @var STWC_Settings|null
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
     * Register hooks.
     */
    private function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
        add_action( 'admin_post_stwc_save_settings', array( $this, 'handle_save_settings' ) );
        add_action( 'admin_post_stwc_reset_defaults', array( $this, 'handle_reset_defaults' ) );
        add_action( 'admin_post_stwc_flush_rewrites', array( $this, 'handle_flush_rewrites' ) );
        add_action( 'admin_post_stwc_inquiry_action', array( $this, 'handle_inquiry_action' ) );
        add_action( 'admin_post_stwc_export_inquiries', array( $this, 'handle_export_inquiries' ) );
        add_action( 'wp_head', array( $this, 'inject_css_tokens_fallback' ), 6 );
    }

    /**
     * Register Santa Towing Core Admin Menu & Submenus.
     */
    public function register_admin_menus() {
        add_menu_page(
            __( 'Santa Towing Settings', 'santa-towing-website-core' ),
            __( 'Santa Towing', 'santa-towing-website-core' ),
            'manage_options',
            'santa-towing-settings',
            array( $this, 'render_settings_page' ),
            'dashicons-car',
            26
        );

        add_submenu_page(
            'santa-towing-settings',
            __( 'Site Settings & Theme Tokens', 'santa-towing-website-core' ),
            __( 'Site Settings', 'santa-towing-website-core' ),
            'manage_options',
            'santa-towing-settings',
            array( $this, 'render_settings_page' )
        );

        add_submenu_page(
            'santa-towing-settings',
            __( 'Inquiries & Dispatch Leads', 'santa-towing-website-core' ),
            __( 'Inquiries & Leads', 'santa-towing-website-core' ),
            'manage_options',
            'santa-towing-inquiries',
            array( $this, 'render_inquiries_page' )
        );

        add_submenu_page(
            'santa-towing-settings',
            __( 'Routes, Permalinks & Diagnostics', 'santa-towing-website-core' ),
            __( 'Routes & System', 'santa-towing-website-core' ),
            'manage_options',
            'santa-towing-diagnostics',
            array( $this, 'render_diagnostics_page' )
        );
    }

    /**
     * Inject dynamic CSS variables fallback when active theme doesn't print them.
     */
    public function inject_css_tokens_fallback() {
        if ( function_exists( 'santa_towing_head_tokens' ) ) {
            return;
        }
        $primary   = sanitize_hex_color( stwc_get_content( 'theme.primary', '#015557' ) ) ?: '#015557';
        $secondary = sanitize_hex_color( stwc_get_content( 'theme.secondary', '#192e2e' ) ) ?: '#192e2e';
        $accent    = sanitize_hex_color( stwc_get_content( 'theme.accent', '#d80000' ) ) ?: '#d80000';
        $dark      = sanitize_hex_color( stwc_get_content( 'theme.dark', '#131514' ) ) ?: '#131514';
        $white     = sanitize_hex_color( stwc_get_content( 'theme.white', '#ffffff' ) ) ?: '#ffffff';
        $black     = sanitize_hex_color( stwc_get_content( 'theme.black', '#000000' ) ) ?: '#000000';

        echo "<style id=\"stwc-theme-tokens\">\n:root {\n";
        echo "  --color-primary: {$primary};\n";
        echo "  --color-secondary: {$secondary};\n";
        echo "  --color-accent: {$accent};\n";
        echo "  --color-dark: {$dark};\n";
        echo "  --color-white: {$white};\n";
        echo "  --color-black: {$black};\n";
        echo "}\n</style>\n";
    }

    /**
     * Render Tabbed Site Settings Screen.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'santa-towing-website-core' ) );
        }

        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'brand';
        $tabs       = array(
            'brand'   => __( 'Brand & Logo', 'santa-towing-website-core' ),
            'colors'  => __( 'Theme Colors & Elementor Sync', 'santa-towing-website-core' ),
            'contact' => __( 'Contact & WhatsApp', 'santa-towing-website-core' ),
            'social'  => __( 'Social, Footer & Floating UI', 'santa-towing-website-core' ),
            'seo'     => __( 'SEO & Service Area Copy', 'santa-towing-website-core' ),
        );
        if ( ! isset( $tabs[ $active_tab ] ) ) {
            $active_tab = 'brand';
        }
        ?>
        <div class="wrap stwc-admin-wrap">
            <h1><?php esc_html_e( 'Santa Towing Website Core — Site Settings', 'santa-towing-website-core' ); ?></h1>

            <?php if ( isset( $_GET['updated'] ) ) : ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved successfully.', 'santa-towing-website-core' ); ?></p></div>
            <?php endif; ?>
            <?php if ( isset( $_GET['reset'] ) ) : ?>
                <div class="notice notice-warning is-dismissible"><p><?php esc_html_e( 'Site content restored to React source defaults.', 'santa-towing-website-core' ); ?></p></div>
            <?php endif; ?>

            <nav class="nav-tab-wrapper wp-clearfix">
                <?php foreach ( $tabs as $tab_key => $label ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=santa-towing-settings&tab=' . $tab_key ) ); ?>" class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="stwc-settings-card">
                <?php wp_nonce_field( 'stwc_save_settings_nonce', 'stwc_nonce' ); ?>
                <input type="hidden" name="action" value="stwc_save_settings" />
                <input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>" />

                <?php if ( 'brand' === $active_tab ) : ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="stwc_brand_name"><?php esc_html_e( 'Company Full Legal Name', 'santa-towing-website-core' ); ?></label></th>
                            <td><input id="stwc_brand_name" type="text" name="content[global][brand][name]" value="<?php echo esc_attr( stwc_get_content( 'global.brand.name' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="stwc_brand_short"><?php esc_html_e( 'Brand Short Name', 'santa-towing-website-core' ); ?></label></th>
                            <td><input id="stwc_brand_short" type="text" name="content[global][brand][shortName]" value="<?php echo esc_attr( stwc_get_content( 'global.brand.shortName' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="stwc_brand_tagline"><?php esc_html_e( 'Brand Tagline', 'santa-towing-website-core' ); ?></label></th>
                            <td><input id="stwc_brand_tagline" type="text" name="content[global][brand][tagline]" value="<?php echo esc_attr( stwc_get_content( 'global.brand.tagline' ) ); ?>" class="large-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="stwc_contact_person"><?php esc_html_e( 'Operations Contact Person', 'santa-towing-website-core' ); ?></label></th>
                            <td><input id="stwc_contact_person" type="text" name="content[global][brand][contactPerson]" value="<?php echo esc_attr( stwc_get_content( 'global.brand.contactPerson' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="stwc_logo_url"><?php esc_html_e( 'Site Logo URL', 'santa-towing-website-core' ); ?></label></th>
                            <td>
                                <?php $logo_url = stwc_get_content( 'global.logo.url' ); ?>
                                <div class="stwc-media-picker">
                                    <input id="stwc_logo_url" type="url" name="content[global][logo][url]" value="<?php echo esc_attr( $logo_url ); ?>" class="regular-text stwc-media-input" />
                                    <button type="button" class="button stwc-media-select"><?php esc_html_e( 'Choose from Media Library', 'santa-towing-website-core' ); ?></button>
                                    <div style="margin-top:10px;">
                                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="" class="stwc-media-preview" style="max-height:60px;background:#015557;padding:6px;border-radius:4px;" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="stwc_logo_alt"><?php esc_html_e( 'Logo Alt Text', 'santa-towing-website-core' ); ?></label></th>
                            <td><input id="stwc_logo_alt" type="text" name="content[global][logo][alt]" value="<?php echo esc_attr( stwc_get_content( 'global.logo.alt' ) ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>

                <?php elseif ( 'colors' === $active_tab ) : ?>
                    <table class="form-table">
                        <?php
                        $color_fields = array(
                            'primary'   => array( __( 'Primary Brand Color (Deep Teal)', 'santa-towing-website-core' ), '#015557' ),
                            'secondary' => array( __( 'Secondary Surface Color (Deep Pine)', 'santa-towing-website-core' ), '#192e2e' ),
                            'accent'    => array( __( 'Emergency / CTA Accent Color (Red)', 'santa-towing-website-core' ), '#d80000' ),
                            'dark'      => array( __( 'Dark Text Color', 'santa-towing-website-core' ), '#131514' ),
                            'white'     => array( __( 'Light Surface Color', 'santa-towing-website-core' ), '#ffffff' ),
                            'black'     => array( __( 'Pure Black Token', 'santa-towing-website-core' ), '#000000' ),
                        );
                        foreach ( $color_fields as $ckey => $cmeta ) :
                            ?>
                            <tr>
                                <th><label for="stwc_color_<?php echo esc_attr( $ckey ); ?>"><?php echo esc_html( $cmeta[0] ); ?></label></th>
                                <td><input id="stwc_color_<?php echo esc_attr( $ckey ); ?>" type="text" name="content[theme][<?php echo esc_attr( $ckey ); ?>]" value="<?php echo esc_attr( stwc_get_content( 'theme.' . $ckey, $cmeta[1] ) ); ?>" class="stwc-color-field" data-default-color="<?php echo esc_attr( $cmeta[1] ); ?>" /></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th><?php esc_html_e( 'Elementor Global Colors Sync', 'santa-towing-website-core' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="sync_elementor_kit" value="1" checked="checked" />
                                    <?php esc_html_e( 'Also sync Primary, Secondary, Dark, and Accent colors into active Elementor Default Kit system colors.', 'santa-towing-website-core' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                <?php elseif ( 'contact' === $active_tab ) : ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e( 'Hotline Number (Raw)', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][phone]" value="<?php echo esc_attr( stwc_get_content( 'global.links.phone' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Hotline Display Label', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][phoneDisplay]" value="<?php echo esc_attr( stwc_get_content( 'global.links.phoneDisplay' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Hotline Formatted', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][phoneFormatted]" value="<?php echo esc_attr( stwc_get_content( 'global.links.phoneFormatted' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Tel URI Link', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][phoneTel]" value="<?php echo esc_attr( stwc_get_content( 'global.links.phoneTel' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'WhatsApp Base URL (wa.me)', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="url" name="content[global][links][whatsapp]" value="<?php echo esc_attr( stwc_get_content( 'global.links.whatsapp' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'Example: https://wa.me/233244753849 — used for Towing, Haulage, Sales & Rental WhatsApp deep-links.', 'santa-towing-website-core' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'WhatsApp Number', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][whatsappNumber]" value="<?php echo esc_attr( stwc_get_content( 'global.links.whatsappNumber' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'WhatsApp Display Label', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][whatsappDisplay]" value="<?php echo esc_attr( stwc_get_content( 'global.links.whatsappDisplay' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Contact & Leads Email', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="email" name="content[global][links][email]" value="<?php echo esc_attr( stwc_get_content( 'global.links.email' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'Form submissions (Towing, Haulage, Careers, Inquiries) also notify this address via wp_mail().', 'santa-towing-website-core' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Headquarters Address', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][address]" value="<?php echo esc_attr( stwc_get_content( 'global.links.address' ) ); ?>" class="large-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Operating Hours Label', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][hours]" value="<?php echo esc_attr( stwc_get_content( 'global.links.hours' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Coverage Label', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][links][coverage]" value="<?php echo esc_attr( stwc_get_content( 'global.links.coverage' ) ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>

                <?php elseif ( 'social' === $active_tab ) : ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e( 'Facebook URL', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="url" name="content[global][social][facebook]" value="<?php echo esc_attr( stwc_get_content( 'global.social.facebook' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Twitter / X URL', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="url" name="content[global][social][twitter]" value="<?php echo esc_attr( stwc_get_content( 'global.social.twitter' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Instagram URL', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="url" name="content[global][social][instagram]" value="<?php echo esc_attr( stwc_get_content( 'global.social.instagram' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'LinkedIn URL', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="url" name="content[global][social][linkedin]" value="<?php echo esc_attr( stwc_get_content( 'global.social.linkedin' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Footer About Text', 'santa-towing-website-core' ); ?></label></th>
                            <td><textarea name="content[global][footer][about]" rows="3" class="large-text"><?php echo esc_textarea( stwc_get_content( 'global.footer.about' ) ); ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Footer Copyright Text', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][footer][copyright]" value="<?php echo esc_attr( stwc_get_content( 'global.footer.copyright' ) ); ?>" class="large-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Newsletter Success Message', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][footer][newsletterSuccess]" value="<?php echo esc_attr( stwc_get_content( 'global.footer.newsletterSuccess' ) ); ?>" class="large-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Floating Tow Button Tooltip', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][floatingButtons][towTooltip]" value="<?php echo esc_attr( stwc_get_content( 'global.floatingButtons.towTooltip' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Floating WhatsApp Tooltip', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[global][floatingButtons][whatsappTooltip]" value="<?php echo esc_attr( stwc_get_content( 'global.floatingButtons.whatsappTooltip' ) ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>

                <?php elseif ( 'seo' === $active_tab ) : ?>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e( 'Canonical Production Domain', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="url" name="content[seo][siteUrl]" value="<?php echo esc_attr( stwc_get_content( 'seo.siteUrl' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Homepage SEO Title', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[seo][home][title]" value="<?php echo esc_attr( stwc_get_content( 'seo.home.title' ) ); ?>" class="large-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Homepage Meta Description', 'santa-towing-website-core' ); ?></label></th>
                            <td><textarea name="content[seo][home][description]" rows="2" class="large-text"><?php echo esc_textarea( stwc_get_content( 'seo.home.description' ) ); ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Service Area Template CTA Heading', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[serviceAreas][template][ctaHeading]" value="<?php echo esc_attr( stwc_get_content( 'serviceAreas.template.ctaHeading' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Service Area Template CTA Subtext', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[serviceAreas][template][ctaSubtext]" value="<?php echo esc_attr( stwc_get_content( 'serviceAreas.template.ctaSubtext' ) ); ?>" class="large-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Service Area Call Button Label', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[serviceAreas][template][callBtnLabel]" value="<?php echo esc_attr( stwc_get_content( 'serviceAreas.template.callBtnLabel' ) ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Service Area WhatsApp Button Label', 'santa-towing-website-core' ); ?></label></th>
                            <td><input type="text" name="content[serviceAreas][template][whatsappBtnLabel]" value="<?php echo esc_attr( stwc_get_content( 'serviceAreas.template.whatsappBtnLabel' ) ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>
                <?php endif; ?>

                <p class="submit">
                    <?php submit_button( __( 'Save Settings', 'santa-towing-website-core' ), 'primary', 'submit', false ); ?>
                </p>
            </form>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:20px;">
                <?php wp_nonce_field( 'stwc_reset_defaults_nonce', 'stwc_reset_nonce' ); ?>
                <input type="hidden" name="action" value="stwc_reset_defaults" />
                <button type="submit" class="button button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Restore all Santa Towing site content fields back to original React defaults?', 'santa-towing-website-core' ) ); ?>');">
                    <?php esc_html_e( 'Restore Default React Source Content', 'santa-towing-website-core' ); ?>
                </button>
            </form>
        </div>
        <?php
    }

    /**
     * Handle Site Settings Save cleanly preserving sibling tabs inside `santa_towing_site_content`.
     */
    public function handle_save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }
        check_admin_referer( 'stwc_save_settings_nonce', 'stwc_nonce' );

        $active_tab = isset( $_POST['active_tab'] ) ? sanitize_key( wp_unslash( $_POST['active_tab'] ) ) : 'brand';
        $existing   = get_option( 'santa_towing_site_content', array() );
        if ( ! is_array( $existing ) || empty( $existing ) ) {
            $existing = stwc_get_default_site_content();
        }

        $posted = isset( $_POST['content'] ) && is_array( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : array();
        $clean  = $this->sanitize_nested_content( $posted );
        $merged = array_replace_recursive( $existing, $clean );

        update_option( 'santa_towing_site_content', $merged, false );

        if ( ! empty( $_POST['sync_elementor_kit'] ) ) {
            $this->sync_elementor_kit_colors( $merged['theme'] ?? array() );
        }

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page'    => 'santa-towing-settings',
                    'tab'     => $active_tab,
                    'updated' => '1',
                ),
                admin_url( 'admin.php' )
            )
        );
        exit;
    }

    /**
     * Recursively sanitize nested site content values by field intent.
     *
     * @param mixed  $data Input value.
     * @param string $key  Key name.
     * @return mixed
     */
    private function sanitize_nested_content( $data, $key = '' ) {
        if ( is_array( $data ) ) {
            $out = array();
            foreach ( $data as $k => $v ) {
                $safe_k         = sanitize_key( (string) $k );
                $out[ $safe_k ] = $this->sanitize_nested_content( $v, (string) $k );
            }
            return $out;
        }

        $val = (string) $data;
        if ( in_array( $key, array( 'primary', 'secondary', 'accent', 'dark', 'white', 'black' ), true ) ) {
            return sanitize_hex_color( $val ) ?: '#015557';
        }
        if ( in_array( $key, array( 'url', 'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'siteUrl' ), true ) ) {
            return esc_url_raw( $val );
        }
        if ( 'email' === $key ) {
            return sanitize_email( $val );
        }
        if ( in_array( $key, array( 'about', 'description' ), true ) ) {
            return sanitize_textarea_field( $val );
        }
        return sanitize_text_field( $val );
    }

    /**
     * Sync plugin Theme Colors into active Elementor Default Kit system colors (Elementor Free compatible).
     *
     * @param array $theme Theme hex color array.
     */
    public function sync_elementor_kit_colors( $theme ) {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }
        $kit_id = (int) get_option( 'elementor_active_kit', 0 );
        if ( ! $kit_id ) {
            return;
        }
        $settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
        if ( ! is_array( $settings ) ) {
            $settings = array();
        }

        $settings['system_colors'] = array(
            array(
                '_id'   => 'primary',
                'title' => 'Primary (Santa Teal)',
                'color' => sanitize_hex_color( $theme['primary'] ?? '#015557' ) ?: '#015557',
            ),
            array(
                '_id'   => 'secondary',
                'title' => 'Secondary (Deep Pine)',
                'color' => sanitize_hex_color( $theme['secondary'] ?? '#192e2e' ) ?: '#192e2e',
            ),
            array(
                '_id'   => 'text',
                'title' => 'Text (Santa Dark)',
                'color' => sanitize_hex_color( $theme['dark'] ?? '#131514' ) ?: '#131514',
            ),
            array(
                '_id'   => 'accent',
                'title' => 'Accent (Emergency Red)',
                'color' => sanitize_hex_color( $theme['accent'] ?? '#d80000' ) ?: '#d80000',
            ),
        );

        update_post_meta( $kit_id, '_elementor_page_settings', $settings );
    }

    /**
     * Restore default option content.
     */
    public function handle_reset_defaults() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }
        check_admin_referer( 'stwc_reset_defaults_nonce', 'stwc_reset_nonce' );
        update_option( 'santa_towing_site_content', stwc_get_default_site_content(), false );

        wp_safe_redirect( admin_url( 'admin.php?page=santa-towing-settings&reset=1' ) );
        exit;
    }

    /**
     * Render Inquiries & Dispatch Leads Table.
     */
    public function render_inquiries_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }
        global $wpdb;
        $table = $wpdb->prefix . 'santa_inquiries';

        $type_filter   = isset( $_GET['form_type'] ) ? sanitize_key( wp_unslash( $_GET['form_type'] ) ) : '';
        $status_filter = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
        $paged         = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
        $per_page      = 20;
        $offset        = ( $paged - 1 ) * $per_page;

        $where  = array( '1=1' );
        $params = array();
        if ( $type_filter ) {
            $where[]  = 'form_type = %s';
            $params[] = $type_filter;
        }
        if ( $status_filter ) {
            $where[]  = 'status = %s';
            $params[] = $status_filter;
        }
        $where_sql = implode( ' AND ', $where );

        $total_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        $total     = ! empty( $params ) ? (int) $wpdb->get_var( $wpdb->prepare( $total_sql, $params ) ) : (int) $wpdb->get_var( $total_sql );

        $rows_sql    = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $rows_params = array_merge( $params, array( $per_page, $offset ) );
        $rows        = $wpdb->get_results( $wpdb->prepare( $rows_sql, $rows_params ), ARRAY_A );

        $export_url = wp_nonce_url(
            admin_url( 'admin-post.php?action=stwc_export_inquiries' ),
            'stwc_export_inquiries_nonce'
        );
        ?>
        <div class="wrap stwc-admin-wrap">
            <h1>
                <?php esc_html_e( 'Santa Towing — Inquiries, Towing Requests & Applications', 'santa-towing-website-core' ); ?>
                <a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action"><?php esc_html_e( 'Export CSV', 'santa-towing-website-core' ); ?></a>
            </h1>

            <form method="get" style="margin: 15px 0;">
                <input type="hidden" name="page" value="santa-towing-inquiries" />
                <select name="form_type">
                    <option value=""><?php esc_html_e( 'All Form Types', 'santa-towing-website-core' ); ?></option>
                    <?php foreach ( array( 'towing', 'haulage', 'contact', 'fleet', 'car_inquiry', 'rental_booking', 'part_inquiry', 'career' ) as $ft ) : ?>
                        <option value="<?php echo esc_attr( $ft ); ?>" <?php selected( $type_filter, $ft ); ?>><?php echo esc_html( ucwords( str_replace( '_', ' ', $ft ) ) ); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status">
                    <option value=""><?php esc_html_e( 'All Statuses', 'santa-towing-website-core' ); ?></option>
                    <?php foreach ( array( 'new', 'contacted', 'resolved', 'archived' ) as $st ) : ?>
                        <option value="<?php echo esc_attr( $st ); ?>" <?php selected( $status_filter, $st ); ?>><?php echo esc_html( ucfirst( $st ) ); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button"><?php esc_html_e( 'Filter', 'santa-towing-website-core' ); ?></button>
            </form>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Type', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Customer', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Contact', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Subject & Details', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'santa-towing-website-core' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'santa-towing-website-core' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $rows ) ) : ?>
                        <tr><td colspan="8"><?php esc_html_e( 'No inquiries recorded yet.', 'santa-towing-website-core' ); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ( $rows as $row ) : ?>
                            <?php $payload = json_decode( (string) $row['payload_json'], true ); ?>
                            <tr>
                                <td>#<?php echo (int) $row['id']; ?></td>
                                <td><span class="stwc-badge"><?php echo esc_html( ucwords( str_replace( '_', ' ', $row['form_type'] ) ) ); ?></span></td>
                                <td><strong><?php echo esc_html( $row['customer_name'] ); ?></strong></td>
                                <td>
                                    <?php if ( $row['phone'] ) : ?>
                                        <div><a href="tel:<?php echo esc_attr( $row['phone'] ); ?>"><?php echo esc_html( $row['phone'] ); ?></a></div>
                                    <?php endif; ?>
                                    <?php if ( $row['email'] ) : ?>
                                        <div><a href="mailto:<?php echo esc_attr( $row['email'] ); ?>"><?php echo esc_html( $row['email'] ); ?></a></div>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $row['whatsapp_url'] ) ) : ?>
                                        <div><a href="<?php echo esc_url( $row['whatsapp_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open WhatsApp', 'santa-towing-website-core' ); ?></a></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo esc_html( $row['subject'] ); ?></strong>
                                    <?php if ( is_array( $payload ) ) : ?>
                                        <details style="margin-top:4px;">
                                            <summary style="cursor:pointer;color:#015557;"><?php esc_html_e( 'View Submitted Fields', 'santa-towing-website-core' ); ?></summary>
                                            <ul style="margin:6px 0 0 14px;font-size:12px;">
                                                <?php foreach ( $payload as $pk => $pv ) : ?>
                                                    <li><strong><?php echo esc_html( ucwords( str_replace( '_', ' ', (string) $pk ) ) ); ?>:</strong> <?php echo esc_html( is_scalar( $pv ) ? (string) $pv : wp_json_encode( $pv ) ); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </details>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $row['attachment_url'] ) ) : ?>
                                        <div style="margin-top:4px;"><a href="<?php echo esc_url( $row['attachment_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="button button-small"><?php esc_html_e( 'View Uploaded CV / Document', 'santa-towing-website-core' ); ?></a></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo esc_html( ucfirst( $row['status'] ) ); ?></strong></td>
                                <td><?php echo esc_html( $row['created_at'] ); ?></td>
                                <td>
                                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;gap:4px;flex-wrap:wrap;">
                                        <?php wp_nonce_field( 'stwc_inquiry_action_nonce', 'stwc_inq_nonce' ); ?>
                                        <input type="hidden" name="action" value="stwc_inquiry_action" />
                                        <input type="hidden" name="inquiry_id" value="<?php echo (int) $row['id']; ?>" />
                                        <button type="submit" name="sub_action" value="mark_contacted" class="button button-small"><?php esc_html_e( 'Contacted', 'santa-towing-website-core' ); ?></button>
                                        <button type="submit" name="sub_action" value="mark_resolved" class="button button-small"><?php esc_html_e( 'Resolved', 'santa-towing-website-core' ); ?></button>
                                        <button type="submit" name="sub_action" value="delete" class="button button-small button-link-delete" onclick="return confirm('Delete this inquiry?');"><?php esc_html_e( 'Delete', 'santa-towing-website-core' ); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php
            $total_pages = max( 1, (int) ceil( $total / $per_page ) );
            if ( $total_pages > 1 ) {
                echo '<div class="tablenav"><div class="tablenav-pages">';
                echo wp_kses_post(
                    paginate_links(
                        array(
                            'base'    => add_query_arg( 'paged', '%#%' ),
                            'format'  => '',
                            'current' => $paged,
                            'total'   => $total_pages,
                        )
                    )
                );
                echo '</div></div>';
            }
            ?>
        </div>
        <?php
    }

    /**
     * Handle inquiry status update or delete.
     */
    public function handle_inquiry_action() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }
        check_admin_referer( 'stwc_inquiry_action_nonce', 'stwc_inq_nonce' );

        global $wpdb;
        $table      = $wpdb->prefix . 'santa_inquiries';
        $id         = isset( $_POST['inquiry_id'] ) ? absint( $_POST['inquiry_id'] ) : 0;
        $sub_action = isset( $_POST['sub_action'] ) ? sanitize_key( wp_unslash( $_POST['sub_action'] ) ) : '';

        if ( $id > 0 ) {
            if ( 'delete' === $sub_action ) {
                $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
            } elseif ( 'mark_contacted' === $sub_action ) {
                $wpdb->update( $table, array( 'status' => 'contacted' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
            } elseif ( 'mark_resolved' === $sub_action ) {
                $wpdb->update( $table, array( 'status' => 'resolved' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
            }
        }

        wp_safe_redirect( admin_url( 'admin.php?page=santa-towing-inquiries' ) );
        exit;
    }

    /**
     * Export inquiries to CSV cleanly.
     */
    public function handle_export_inquiries() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }
        check_admin_referer( 'stwc_export_inquiries_nonce' );

        global $wpdb;
        $table = $wpdb->prefix . 'santa_inquiries';
        $rows  = $wpdb->get_results( "SELECT id, form_type, customer_name, phone, email, subject, payload_json, attachment_url, status, created_at FROM {$table} ORDER BY created_at DESC", ARRAY_A );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="santa-towing-inquiries-' . gmdate( 'Y-m-d' ) . '.csv"' );

        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, array( 'ID', 'Form Type', 'Name', 'Phone', 'Email', 'Subject', 'Details JSON', 'Attachment', 'Status', 'Created At' ) );
        foreach ( (array) $rows as $row ) {
            fputcsv( $out, $row );
        }
        fclose( $out );
        exit;
    }

    /**
     * Render Routes, Permalinks & Diagnostics page.
     */
    public function render_diagnostics_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }

        $cpts = array(
            'st_car'          => __( 'Cars for Sale', 'santa-towing-website-core' ),
            'st_rental'       => __( 'Rental Vehicles', 'santa-towing-website-core' ),
            'st_part'         => __( 'Auto Parts', 'santa-towing-website-core' ),
            'st_service_area' => __( 'Service Area Routes', 'santa-towing-website-core' ),
            'st_gallery'      => __( 'Gallery Items', 'santa-towing-website-core' ),
            'st_faq'          => __( 'FAQs', 'santa-towing-website-core' ),
            'st_career'       => __( 'Careers', 'santa-towing-website-core' ),
            'post'            => __( 'Blog Articles', 'santa-towing-website-core' ),
        );
        ?>
        <div class="wrap stwc-admin-wrap">
            <h1><?php esc_html_e( 'Routes, Rewrites & System Diagnostics', 'santa-towing-website-core' ); ?></h1>

            <?php if ( isset( $_GET['flushed'] ) ) : ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Permalinks and rewrite rules flushed successfully.', 'santa-towing-website-core' ); ?></p></div>
            <?php endif; ?>

            <div class="stwc-settings-card" style="padding:20px;margin-top:16px;">
                <h2><?php esc_html_e( 'Environment & Plugin Health', 'santa-towing-website-core' ); ?></h2>
                <p><strong><?php esc_html_e( 'Elementor Free Status:', 'santa-towing-website-core' ); ?></strong>
                    <?php echo did_action( 'elementor/loaded' ) ? esc_html__( 'Active (13 Custom Widgets Registered)', 'santa-towing-website-core' ) : esc_html__( 'Not Active (CPTs, Shortcodes, Settings & Forms remain active)', 'santa-towing-website-core' ); ?>
                </p>
                <p><strong><?php esc_html_e( 'Ghana Locations Loaded:', 'santa-towing-website-core' ); ?></strong> <?php echo (int) count( stwc_get_ghana_regions_data() ); ?> Regions | <?php echo (int) count( stwc_get_towing_adverts() ); ?> District Adverts</p>
                <p><strong><?php esc_html_e( 'Dynamic XML Sitemap:', 'santa-towing-website-core' ); ?></strong> <a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( home_url( '/sitemap.xml' ) ); ?></a></p>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:16px 0;">
                    <?php wp_nonce_field( 'stwc_flush_rewrites_nonce', 'stwc_flush_nonce' ); ?>
                    <input type="hidden" name="action" value="stwc_flush_rewrites" />
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Flush Rewrite Rules & Permalinks Now', 'santa-towing-website-core' ); ?></button>
                </form>

                <h3><?php esc_html_e( 'Custom Post Type Record Counts', 'santa-towing-website-core' ); ?></h3>
                <table class="widefat striped" style="max-width:540px;">
                    <thead><tr><th><?php esc_html_e( 'Content Type', 'santa-towing-website-core' ); ?></th><th><?php esc_html_e( 'Published Count', 'santa-towing-website-core' ); ?></th></tr></thead>
                    <tbody>
                        <?php foreach ( $cpts as $pt => $label ) : $counts = wp_count_posts( $pt ); ?>
                            <tr>
                                <td><?php echo esc_html( $label ); ?> (<code><?php echo esc_html( $pt ); ?></code>)</td>
                                <td><strong><?php echo isset( $counts->publish ) ? (int) $counts->publish : 0; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Flush rewrites handler.
     */
    public function handle_flush_rewrites() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'santa-towing-website-core' ) );
        }
        check_admin_referer( 'stwc_flush_rewrites_nonce', 'stwc_flush_nonce' );
        STWC_CPTs::register_post_types();
        STWC_Rewrites::add_rewrite_rules();
        flush_rewrite_rules();

        wp_safe_redirect( admin_url( 'admin.php?page=santa-towing-diagnostics&flushed=1' ) );
        exit;
    }
}
