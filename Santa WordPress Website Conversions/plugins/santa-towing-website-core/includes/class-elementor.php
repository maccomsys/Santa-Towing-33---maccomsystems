<?php
/**
 * Elementor Free Integration, Widget Category & Shortcode Bridges
 *
 * Registers the `santa-towing` Elementor category and all 17 custom
 * `\Elementor\Widget_Base` widgets (strictly Elementor Free APIs only — zero Pro hooks).
 * Also registers companion shortcodes so forms/catalogs work outside Elementor too.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class STWC_Elementor {

    /**
     * Singleton instance.
     *
     * @var STWC_Elementor|null
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
     * Hook Elementor Free lifecycle and fallback shortcodes.
     */
    private function __construct() {
        add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_category' ) );
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_elementor_scripts' ) );

        // Register companion shortcodes for Classic/Block or theme usage
        add_action( 'init', array( $this, 'register_shortcodes' ) );
    }

    /**
     * Register Santa Towing custom Elementor widget category.
     *
     * @param \Elementor\Elements_Manager $elements_manager Elementor Elements Manager.
     */
    public function register_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'santa-towing',
            array(
                'title' => __( 'Santa Towing', 'santa-towing-website-core' ),
                'icon'  => 'eicon-car',
            )
        );
    }

    /**
     * Load & register all 17 Elementor Free custom widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor Widgets Manager.
     */
    public function register_widgets( $widgets_manager ) {
        $base_dir = STWC_DIR . 'includes/widgets/';
        require_once $base_dir . 'class-widget-base.php';

        $widgets = array(
            'class-widget-hero-slider.php'         => 'STWC_Widget_Hero_Slider',
            'class-widget-page-hero.php'           => 'STWC_Widget_Page_Hero',
            'class-widget-feature-cards.php'       => 'STWC_Widget_Feature_Cards',
            'class-widget-spotlight-split.php'     => 'STWC_Widget_Spotlight_Split',
            'class-widget-services-grid.php'       => 'STWC_Widget_Services_Grid',
            'class-widget-haulage-showcase.php'    => 'STWC_Widget_Haulage_Showcase',
            'class-widget-cars-catalog.php'        => 'STWC_Widget_Cars_Catalog',
            'class-widget-rental-fleet.php'        => 'STWC_Widget_Rental_Fleet',
            'class-widget-parts-showcase.php'      => 'STWC_Widget_Parts_Showcase',
            'class-widget-why-choose-us.php'       => 'STWC_Widget_Why_Choose_Us',
            'class-widget-blog-grid.php'           => 'STWC_Widget_Blog_Grid',
            'class-widget-faq-accordion.php'       => 'STWC_Widget_FAQ_Accordion',
            'class-widget-gallery-showcase.php'    => 'STWC_Widget_Gallery_Showcase',
            'class-widget-service-area-route.php'  => 'STWC_Widget_Service_Area_Route',
            'class-widget-towing-request-form.php' => 'STWC_Widget_Towing_Request_Form',
            'class-widget-enquiry-forms.php'       => 'STWC_Widget_Enquiry_Forms',
            'class-widget-careers-board.php'       => 'STWC_Widget_Careers_Board',
        );

        foreach ( $widgets as $file => $class_name ) {
            $path = $base_dir . $file;
            if ( file_exists( $path ) ) {
                require_once $path;
                if ( class_exists( $class_name ) ) {
                    $widgets_manager->register( new $class_name() );
                }
            }
        }
    }

    /**
     * Ensure frontend JS/CSS run cleanly in Elementor Editor Preview & Frontend.
     */
    public function enqueue_elementor_scripts() {
        wp_enqueue_style( 'santa-towing-core' );
        wp_enqueue_script( 'santa-towing-core' );
    }

    /**
     * Register helpful shortcodes rendering key interactive sections cleanly.
     */
    public function register_shortcodes() {
        add_shortcode( 'stwc_towing_form', array( $this, 'sc_towing_form' ) );
        add_shortcode( 'stwc_enquiry_form', array( $this, 'sc_enquiry_form' ) );
        add_shortcode( 'stwc_faqs', array( $this, 'sc_faqs' ) );
        add_shortcode( 'stwc_gallery', array( $this, 'sc_gallery' ) );
        add_shortcode( 'stwc_careers', array( $this, 'sc_careers' ) );
    }

    /**
     * Shortcode: [stwc_towing_form]
     */
    public function sc_towing_form( $atts ) {
        $atts = shortcode_atts(
            array(
                'title'    => __( 'Request a Tow Now', 'santa-towing-website-core' ),
                'subtitle' => __( 'Select your Ghana region & town for immediate WhatsApp + dispatch response.', 'santa-towing-website-core' ),
            ),
            $atts,
            'stwc_towing_form'
        );
        return self::render_towing_form_markup( $atts );
    }

    /**
     * Shortcode: [stwc_enquiry_form mode="haulage|contact|fleet|car_inquiry|rental_booking|part_inquiry"]
     */
    public function sc_enquiry_form( $atts ) {
        $atts = shortcode_atts(
            array(
                'mode'       => 'contact',
                'title'      => '',
                'item_id'    => 0,
                'item_title' => '',
            ),
            $atts,
            'stwc_enquiry_form'
        );
        return self::render_enquiry_form_markup( $atts );
    }

    /**
     * Shortcode: [stwc_faqs category="All"]
     */
    public function sc_faqs( $atts ) {
        $atts = shortcode_atts( array( 'category' => 'All' ), $atts, 'stwc_faqs' );
        $faqs = stwc_get_faqs( $atts['category'] );
        ob_start();
        ?>
        <div class="stwc-faq-wrapper space-y-4">
            <?php foreach ( $faqs as $idx => $faq ) : ?>
                <div class="bg-white rounded-[1px] border border-dark/10 overflow-hidden shadow-sm">
                    <button type="button" class="stwc-faq-trigger w-full px-6 py-5 text-left flex items-center justify-between font-bold text-dark hover:text-accent transition-colors" aria-expanded="<?php echo 0 === $idx ? 'true' : 'false'; ?>">
                        <span><?php echo esc_html( $faq['q'] ); ?></span>
                        <span class="stwc-faq-chevron transition-transform duration-300 <?php echo 0 === $idx ? 'rotate-180' : ''; ?>">
                            <?php echo stwc_icon( 'chevron-down', 'w-5 h-5 text-accent' ); ?>
                        </span>
                    </button>
                    <div class="stwc-faq-panel px-6 pb-5 text-dark/70 leading-relaxed <?php echo 0 === $idx ? '' : 'hidden'; ?>">
                        <?php echo wp_kses_post( wpautop( $faq['a'] ) ); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: [stwc_gallery]
     */
    public function sc_gallery() {
        $items = stwc_get_gallery_items();
        ob_start();
        ?>
        <div class="stwc-gallery-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 auto-rows-[240px]">
            <?php foreach ( $items as $item ) : ?>
                <div class="relative rounded-[1px] overflow-hidden group cursor-pointer <?php echo esc_attr( $item['span'] ?? 'col-span-1 row-span-1' ); ?>" data-stwc-lightbox="<?php echo esc_url( $item['url'] ); ?>" data-stwc-caption="<?php echo esc_attr( $item['title'] ); ?>">
                    <img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-5 flex flex-col justify-end text-white">
                        <span class="text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( $item['category'] ); ?></span>
                        <h4 class="font-bold text-lg"><?php echo esc_html( $item['title'] ); ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: [stwc_careers]
     */
    public function sc_careers() {
        $jobs = stwc_get_careers();
        ob_start();
        ?>
        <div class="stwc-careers-board space-y-6">
            <?php foreach ( $jobs as $job ) : ?>
                <div class="bg-white rounded-[1px] p-6 sm:p-8 border border-dark/10 shadow-sm">
                    <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( $job['department'] ); ?></span>
                            <h3 class="text-2xl font-bold text-dark mt-1"><?php echo esc_html( $job['title'] ); ?></h3>
                            <p class="text-sm text-dark/60 mt-1"><?php echo esc_html( $job['location'] . ' • ' . $job['type'] ); ?></p>
                        </div>
                        <button type="button" class="stwc-open-apply bg-accent hover:bg-black text-white px-6 py-3 font-bold rounded-full text-sm transition-colors" data-job-title="<?php echo esc_attr( $job['title'] ); ?>">
                            <?php esc_html_e( 'Apply Now', 'santa-towing-website-core' ); ?>
                        </button>
                    </div>
                    <p class="text-dark/80 mb-4"><?php echo esc_html( $job['description'] ); ?></p>
                </div>
            <?php endforeach; ?>
            <div class="stwc-career-form-box hidden bg-slate-50 border border-dark/10 p-6 sm:p-8 rounded-[1px]">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h4 class="text-xl font-bold text-dark"><?php esc_html_e( 'Submit Job Application', 'santa-towing-website-core' ); ?>: <span class="stwc-selected-job-title text-accent"></span></h4>
                    <button type="button" class="stwc-close-apply px-3 py-1.5 text-sm font-bold text-dark/70 hover:text-dark bg-white border border-dark/15 rounded-full transition-colors" aria-label="<?php esc_attr_e( 'Close Application Form', 'santa-towing-website-core' ); ?>">&times;</button>
                </div>
                <?php echo self::render_enquiry_form_markup( array( 'mode' => 'career' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shared markup renderer for Towing Request Form (used by Widget + Shortcode).
     *
     * @param array $opts Widget/shortcode attributes.
     * @return string
     */
    public static function render_towing_form_markup( $opts = array() ) {
        $title    = $opts['title'] ?? __( 'Request a Tow Now', 'santa-towing-website-core' );
        $subtitle = $opts['subtitle'] ?? '';
        $regions  = stwc_get_ghana_regions_data();

        ob_start();
        ?>
        <div class="stwc-towing-form-card bg-white rounded-[1px] p-6 sm:p-8 shadow-2xl border border-dark/10">
            <?php if ( $title ) : ?>
                <h3 class="text-2xl font-bold text-dark mb-2 stwc-form-heading"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="text-sm text-dark/70 mb-5"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>

            <form class="stwc-ajax-form stwc-towing-form space-y-4" data-action="stwc_towing_request" novalidate>
                <input type="text" name="website_hp" value="" class="stwc-hp-field" tabindex="-1" autocomplete="off" aria-hidden="true" />

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-dark/70 mb-1.5"><?php esc_html_e( 'Your Name *', 'santa-towing-website-core' ); ?></label>
                        <input type="text" name="name" required placeholder="<?php esc_attr_e( 'Kwame Mensah', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] text-dark focus:outline-none focus:border-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-dark/70 mb-1.5"><?php esc_html_e( 'Phone Number *', 'santa-towing-website-core' ); ?></label>
                        <input type="tel" name="phone" required placeholder="024 475 3849" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] text-dark focus:outline-none focus:border-primary" />
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="stwc-region-wrap relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-dark/70 mb-1.5"><?php esc_html_e( 'Region in Ghana *', 'santa-towing-website-core' ); ?></label>
                        <select name="region" required class="stwc-region-select w-full px-4 py-3 border border-gray-300 rounded-[1px] text-dark bg-white focus:outline-none focus:border-primary">
                            <option value=""><?php esc_html_e( 'Select Region...', 'santa-towing-website-core' ); ?></option>
                            <?php foreach ( $regions as $reg ) : ?>
                                <option value="<?php echo esc_attr( $reg['region'] ); ?>"><?php echo esc_html( $reg['region'] ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="stwc-area-wrap relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-dark/70 mb-1.5"><?php esc_html_e( 'Area / Town *', 'santa-towing-website-core' ); ?></label>
                        <select name="area" required class="stwc-area-select w-full px-4 py-3 border border-gray-300 rounded-[1px] text-dark bg-white focus:outline-none focus:border-primary" disabled>
                            <option value=""><?php esc_html_e( 'Select Region First...', 'santa-towing-website-core' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-dark/70 mb-1.5"><?php esc_html_e( 'Car Make & Model', 'santa-towing-website-core' ); ?></label>
                        <input type="text" name="carMakeModel" placeholder="<?php esc_attr_e( 'e.g. Toyota Corolla 2020', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] text-dark focus:outline-none focus:border-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-dark/70 mb-1.5"><?php esc_html_e( 'Issue / Situation', 'santa-towing-website-core' ); ?></label>
                        <select name="issue" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] text-dark bg-white focus:outline-none focus:border-primary">
                            <option value="breakdown"><?php esc_html_e( 'Car Breakdown', 'santa-towing-website-core' ); ?></option>
                            <option value="accident"><?php esc_html_e( 'Accident Recovery', 'santa-towing-website-core' ); ?></option>
                            <option value="flat-tire"><?php esc_html_e( 'Flat Tire', 'santa-towing-website-core' ); ?></option>
                            <option value="fuel"><?php esc_html_e( 'Out of Fuel', 'santa-towing-website-core' ); ?></option>
                            <option value="lockout"><?php esc_html_e( 'Car Lockout', 'santa-towing-website-core' ); ?></option>
                            <option value="other"><?php esc_html_e( 'Other Roadside Issue', 'santa-towing-website-core' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="stwc-form-feedback hidden p-4 rounded-[1px] text-sm font-medium"></div>

                <button type="submit" class="w-full bg-accent hover:bg-black text-white font-bold py-4 px-6 rounded-full transition-colors flex items-center justify-center gap-2 shadow-lg cursor-pointer stwc-submit-btn">
                    <?php echo stwc_icon( 'message-circle', 'w-5 h-5' ); ?>
                    <span><?php esc_html_e( 'Request Emergency Tow via WhatsApp & Dispatch', 'santa-towing-website-core' ); ?></span>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shared markup renderer for Haulage Quote, Contact, Fleet, Inventory & Career Forms.
     *
     * @param array $opts Widget/shortcode attributes.
     * @return string
     */
    public static function render_enquiry_form_markup( $opts = array() ) {
        $mode       = $opts['mode'] ?? 'contact';
        $title      = $opts['title'] ?? '';
        $item_id    = absint( $opts['item_id'] ?? 0 );
        $item_title = $opts['item_title'] ?? '';

        $action_map = array(
            'haulage'        => 'stwc_haulage_quote',
            'contact'        => 'stwc_contact_enquiry',
            'fleet'          => 'stwc_fleet_enquiry',
            'car_inquiry'    => 'stwc_inventory_inquiry',
            'rental_booking' => 'stwc_inventory_inquiry',
            'part_inquiry'   => 'stwc_inventory_inquiry',
            'career'         => 'stwc_career_apply',
        );
        $ajax_action = $action_map[ $mode ] ?? 'stwc_contact_enquiry';

        ob_start();
        ?>
        <div class="stwc-enquiry-form-card bg-white rounded-[1px] p-6 sm:p-8 border border-dark/10 shadow-sm">
            <?php if ( $title ) : ?>
                <h3 class="text-2xl font-bold text-dark mb-5 stwc-form-heading"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>

            <form class="stwc-ajax-form space-y-4" data-action="<?php echo esc_attr( $ajax_action ); ?>" <?php echo 'career' === $mode ? 'enctype="multipart/form-data"' : ''; ?> novalidate>
                <input type="text" name="website_hp" value="" class="stwc-hp-field" tabindex="-1" autocomplete="off" aria-hidden="true" />

                <?php if ( 'haulage' === $mode ) : ?>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="customerName" required placeholder="<?php esc_attr_e( 'Your Full Name *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="tel" name="phoneNumber" required placeholder="<?php esc_attr_e( 'Phone / WhatsApp Number *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="pickupLocation" required placeholder="<?php esc_attr_e( 'Pickup Location (e.g. Tema Harbour) *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="text" name="deliveryLocation" required placeholder="<?php esc_attr_e( 'Delivery Location (e.g. Kumasi) *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <select name="cargoType" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] bg-white">
                            <option value="Heavy Machinery Transport">Heavy Machinery Transport</option>
                            <option value="Car Haulage">Car Haulage</option>
                            <option value="Container Haulage">Container Haulage (20ft/40ft)</option>
                            <option value="Tema Harbour Haulage">Tema Harbour Haulage</option>
                            <option value="Generator & Industrial Equipment">Generator & Industrial Equipment</option>
                            <option value="Construction Equipment Haulage">Construction Equipment</option>
                            <option value="Forklift Transportation">Forklift Transportation</option>
                            <option value="Oversized & Heavy Load">Oversized & Heavy Load</option>
                        </select>
                        <input type="text" name="estimatedWeight" placeholder="<?php esc_attr_e( 'Est. Weight (e.g. 25 Tons)', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="date" name="preferredDate" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <textarea name="cargoDescription" rows="3" placeholder="<?php esc_attr_e( 'Cargo description, dimensions or port gate details...', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]"></textarea>

                <?php elseif ( 'fleet' === $mode ) : ?>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="companyName" required placeholder="<?php esc_attr_e( 'Company / Organization Name *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="text" name="contactName" required placeholder="<?php esc_attr_e( 'Contact Person Name *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <input type="tel" name="phone" required placeholder="<?php esc_attr_e( 'Phone Number *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="email" name="email" placeholder="<?php esc_attr_e( 'Corporate Email', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="text" name="fleetSize" placeholder="<?php esc_attr_e( 'Fleet Size (e.g. 15 vehicles)', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <textarea name="notes" rows="3" placeholder="<?php esc_attr_e( 'Tell us about your servicing, VIP towing, or corporate rental needs...', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]"></textarea>

                <?php elseif ( in_array( $mode, array( 'car_inquiry', 'rental_booking', 'part_inquiry' ), true ) ) : ?>
                    <input type="hidden" name="inquiryKind" value="<?php echo esc_attr( $mode ); ?>" />
                    <input type="hidden" name="itemId" value="<?php echo (int) $item_id; ?>" />
                    <input type="text" name="itemTitle" value="<?php echo esc_attr( $item_title ); ?>" placeholder="<?php esc_attr_e( 'Vehicle / Part Interested In', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] bg-slate-50 font-semibold" />
                    <div class="grid sm:grid-cols-3 gap-4">
                        <input type="text" name="name" required placeholder="<?php esc_attr_e( 'Full Name *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="tel" name="phone" required placeholder="<?php esc_attr_e( 'Phone / WhatsApp *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="email" name="email" placeholder="<?php esc_attr_e( 'Email Address', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <?php if ( 'rental_booking' === $mode ) : ?>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <input type="date" name="pickupDate" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                            <input type="text" name="duration" placeholder="<?php esc_attr_e( 'Duration (e.g. 5 days)', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                            <select name="withDriver" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] bg-white">
                                <option value="no"><?php esc_html_e( 'Self-Drive', 'santa-towing-website-core' ); ?></option>
                                <option value="yes"><?php esc_html_e( 'With Professional Driver', 'santa-towing-website-core' ); ?></option>
                            </select>
                        </div>
                    <?php elseif ( 'part_inquiry' === $mode ) : ?>
                        <input type="text" name="vinNumber" placeholder="<?php esc_attr_e( 'Car VIN / Chassis Number (for 100% compatibility check)', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    <?php endif; ?>
                    <textarea name="notes" rows="3" placeholder="<?php esc_attr_e( 'Additional notes or preferred viewing time...', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]"></textarea>

                <?php elseif ( 'career' === $mode ) : ?>
                    <input type="text" name="jobTitle" class="stwc-job-title-input w-full px-4 py-3 border border-gray-300 rounded-[1px] font-semibold" value="<?php echo esc_attr( $item_title ); ?>" placeholder="<?php esc_attr_e( 'Position Applying For *', 'santa-towing-website-core' ); ?>" required />
                    <div class="grid sm:grid-cols-3 gap-4">
                        <input type="text" name="fullName" required placeholder="<?php esc_attr_e( 'Full Name *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="email" name="email" required placeholder="<?php esc_attr_e( 'Email Address *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="tel" name="phone" required placeholder="<?php esc_attr_e( 'Phone Number *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="url" name="portfolioUrl" placeholder="<?php esc_attr_e( 'LinkedIn / Portfolio URL (Optional)', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <div>
                            <label class="block text-xs font-bold uppercase text-dark/70 mb-1"><?php esc_html_e( 'Upload Resume / CV (PDF, DOC, DOCX, Max 5MB)', 'santa-towing-website-core' ); ?></label>
                            <input type="file" name="resumeFile" accept=".pdf,.doc,.docx" class="w-full text-sm text-dark" />
                        </div>
                    </div>
                    <textarea name="coverLetter" rows="4" placeholder="<?php esc_attr_e( 'Tell us about your relevant automotive or logistics experience...', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]"></textarea>

                <?php else : ?>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="name" required placeholder="<?php esc_attr_e( 'Your Full Name *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <input type="tel" name="phone" required placeholder="<?php esc_attr_e( 'Phone Number *', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="email" name="email" placeholder="<?php esc_attr_e( 'Email Address', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]" />
                        <select name="serviceType" class="w-full px-4 py-3 border border-gray-300 rounded-[1px] bg-white">
                            <option value="24/7 Emergency Towing">24/7 Emergency Towing</option>
                            <option value="Haulage & Freight Quote">Haulage & Freight Quote</option>
                            <option value="Auto Repairs & Diagnostics">Auto Repairs & Diagnostics</option>
                            <option value="Auto Body Works & Spraying">Auto Body Works & Spraying</option>
                            <option value="Car Sales Inquiry">Car Sales Inquiry</option>
                            <option value="Car Rental Booking">Car Rental Booking</option>
                            <option value="Genuine Auto Parts">Genuine Auto Parts</option>
                        </select>
                    </div>
                    <textarea name="message" rows="4" placeholder="<?php esc_attr_e( 'How can Santa Towing assist you today?', 'santa-towing-website-core' ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-[1px]"></textarea>
                <?php endif; ?>

                <div class="stwc-form-feedback hidden p-4 rounded-[1px] text-sm font-medium"></div>

                <button type="submit" class="w-full bg-primary hover:bg-black text-white font-bold py-4 px-6 rounded-full transition-colors flex items-center justify-center gap-2 shadow-md cursor-pointer stwc-submit-btn">
                    <?php echo stwc_icon( 'send', 'w-4 h-4' ); ?>
                    <span><?php echo 'career' === $mode ? esc_html__( 'Submit Application', 'santa-towing-website-core' ) : esc_html__( 'Submit Request', 'santa-towing-website-core' ); ?></span>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
