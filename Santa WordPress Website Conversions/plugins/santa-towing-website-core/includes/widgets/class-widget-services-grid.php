<?php
/**
 * Elementor Free Widget: Core Automotive Services Grid
 *
 * Replicates React `src/components/sections/ServicesSection.tsx`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Services_Grid extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-services-grid';
    }

    public function get_title() {
        return __( 'Santa Services Grid', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Section & Service Cards', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Section Eyebrow', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Our Automotive Services',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Section Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Complete Care For Every Mile',
            )
        );

        $this->add_control(
            'subheading',
            array(
                'label'   => __( 'Subheading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'From rapid 24/7 emergency towing and heavy haulage to certified mechanical repairs, genuine parts, car sales, and corporate fleet rentals.',
            )
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'title',
            array(
                'label'   => __( 'Service Title', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '24/7 Emergency Towing',
            )
        );
        $repeater->add_control(
            'description',
            array(
                'label'   => __( 'Service Description', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Rapid flatbed towing, accident extraction, and roadside assistance across Ghana.',
            )
        );
        $repeater->add_control(
            'image',
            array(
                'label'   => __( 'Service Card Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ) ),
            )
        );
        $repeater->add_control(
            'icon_name',
            array(
                'label'   => __( 'Badge Icon', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'truck',
                'options' => array(
                    'truck'        => 'Truck / Towing',
                    'wrench'       => 'Wrench / Repairs',
                    'car'          => 'Car / Sales & Rentals',
                    'settings'     => 'Settings / Parts',
                    'shield-check' => 'Shield / Fleet',
                    'navigation'   => 'Navigation / Haulage',
                ),
            )
        );
        $repeater->add_control(
            'link',
            array(
                'label'   => __( 'Destination Link', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => home_url( '/towing/' ) ),
            )
        );

        $this->add_control(
            'services',
            array(
                'label'       => __( 'Service Items', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default'     => array(
                    array( 'title' => '24/7 Emergency Towing', 'description' => 'Fast flatbed dispatch, breakdown recovery, and accident clearance nationwide.', 'image' => array( 'url' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ) ), 'icon_name' => 'truck', 'link' => array( 'url' => home_url( '/towing/' ) ) ),
                    array( 'title' => 'Haulage & Freight', 'description' => 'Lowbed machinery transport, container chassis & port haulage across Ghana.', 'image' => array( 'url' => stwc_asset_image( 'haulage-semi-truck-800.jpg' ) ), 'icon_name' => 'navigation', 'link' => array( 'url' => home_url( '/haulage/' ) ) ),
                    array( 'title' => 'Auto Repairs & Diagnostics', 'description' => 'Certified workshop mechanics, computer fault scanning & engine overhauls.', 'image' => array( 'url' => stwc_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ) ), 'icon_name' => 'wrench', 'link' => array( 'url' => home_url( '/repairs/' ) ) ),
                    array( 'title' => 'Body Works & Spraying', 'description' => 'Oven-baked factory paint finishing, dent repair & chassis alignment.', 'image' => array( 'url' => stwc_asset_image( 'image-14-suspension-parts-maintenance.jpg' ) ), 'icon_name' => 'shield-check', 'link' => array( 'url' => home_url( '/body-works/' ) ) ),
                    array( 'title' => 'Car Sales & Imports', 'description' => '150-point inspected new, foreign used, and Ghana used vehicles.', 'image' => array( 'url' => stwc_asset_image( 'image-6-luxury-mercedes-benz.jpg' ) ), 'icon_name' => 'car', 'link' => array( 'url' => home_url( '/sales/' ) ) ),
                    array( 'title' => 'Car Rentals & Fleet', 'description' => 'Self-drive and chauffeur sedans, SUVs, pickups & executive vans.', 'image' => array( 'url' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ) ), 'icon_name' => 'car', 'link' => array( 'url' => home_url( '/rental/' ) ) ),
                    array( 'title' => 'Genuine Auto Parts', 'description' => 'OEM batteries, brake pads, filters, lubricants & workshop fitting.', 'image' => array( 'url' => stwc_asset_image( 'image-9-car-battery-maintenance.jpg' ) ), 'icon_name' => 'settings', 'link' => array( 'url' => home_url( '/parts/' ) ) ),
                    array( 'title' => 'Corporate Fleet Care', 'description' => 'B2B fleet SLAs, consolidated invoicing & priority breakdown dispatch.', 'image' => array( 'url' => stwc_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ) ), 'icon_name' => 'shield-check', 'link' => array( 'url' => home_url( '/fleet/' ) ) ),
                ),
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label'          => __( 'Grid Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '4',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-services-grid-wrap' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-services-section', '#ffffff', '#131514' );

        $this->start_controls_section(
            'section_card_style',
            array(
                'label' => __( 'Card Appearance', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg',
            array(
                'label'     => __( 'Card Background', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-service-card' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_padding',
            array(
                'label'      => __( 'Card Body Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-service-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s        = $this->get_settings_for_display();
        $services = $s['services'] ?? array();
        ?>
        <section class="stwc-widget-section stwc-services-section py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="stwc-section-header max-w-3xl mx-auto mb-12 text-center">
                    <?php if ( ! empty( $s['badge'] ) ) : ?>
                        <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['heading'] ) ) : ?>
                        <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-dark mb-4"><?php echo esc_html( $s['heading'] ); ?></h2>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['subheading'] ) ) : ?>
                        <p class="text-dark/70 text-base sm:text-lg leading-relaxed"><?php echo esc_html( $s['subheading'] ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="stwc-services-grid-wrap grid gap-6">
                    <?php foreach ( $services as $item ) : ?>
                        <?php
                        $img_url = ! empty( $item['image']['url'] ) ? $item['image']['url'] : stwc_asset_image( 'image-1-towing-vehicle.jpg' );
                        $href    = ! empty( $item['link']['url'] ) ? $item['link']['url'] : home_url( '/services/' );
                        ?>
                        <article class="stwc-service-card bg-white rounded-[1px] overflow-hidden border border-dark/10 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                            <div class="aspect-[16/10] relative overflow-hidden bg-slate-100">
                                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $item['title'] ?? '' ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <div class="stwc-accent-bg absolute top-3 left-3 w-10 h-10 rounded-[1px] bg-accent text-white flex items-center justify-center shadow-md">
                                    <?php echo $this->render_icon( $item['icon_name'] ?? 'truck', 'w-5 h-5' ); ?>
                                </div>
                            </div>
                            <div class="stwc-service-card-body p-6 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-dark mb-2 group-hover:text-accent transition-colors">
                                    <a href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $item['title'] ?? '' ); ?></a>
                                </h3>
                                <p class="text-sm text-dark/70 leading-relaxed mb-5 flex-1"><?php echo esc_html( $item['description'] ?? '' ); ?></p>
                                <a href="<?php echo esc_url( $href ); ?>" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-accent transition-colors mt-auto">
                                    <span><?php esc_html_e( 'Learn More', 'santa-towing-website-core' ); ?></span>
                                    <?php echo $this->render_icon( 'arrow-right', 'w-4 h-4' ); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
