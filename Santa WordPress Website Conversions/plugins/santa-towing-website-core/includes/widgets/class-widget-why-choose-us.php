<?php
/**
 * Elementor Free Widget: Why Choose Santa Towing Trust Pillars & Stats
 *
 * Replicates React `src/components/sections/WhyChooseUs.tsx`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Why_Choose_Us extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-why-choose-us';
    }

    public function get_title() {
        return __( 'Santa Why Choose Us', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-shield';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Trust Section Content', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Badge', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Why Drivers Across Ghana Trust Us',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Built For Rapid Rescue & Certified Care',
            )
        );

        $this->add_control(
            'description',
            array(
                'label'   => __( 'Description', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'With GPS-dispatched trucks, certified master technicians, transparent pricing, and comprehensive goods-in-transit insurance, we deliver peace of mind on every call.',
            )
        );

        $this->add_control(
            'side_image',
            array(
                'label'   => __( 'Workshop / Recovery Photo', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'welcome-towing-image.jpg' ) ),
            )
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'title',
            array(
                'label'   => __( 'Pillar Title', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Under 30-Min Rapid Dispatch',
            )
        );
        $repeater->add_control(
            'desc',
            array(
                'label'   => __( 'Pillar Text', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Strategically stationed recovery units across Accra, Tema, and Kumasi.',
            )
        );
        $repeater->add_control(
            'icon_name',
            array(
                'label'   => __( 'Icon', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'clock',
                'options' => array(
                    'clock'        => 'Clock',
                    'shield-check' => 'Shield Check',
                    'map-pin'      => 'Map Pin',
                    'award'        => 'Award',
                    'wrench'       => 'Wrench',
                ),
            )
        );

        $this->add_control(
            'pillars',
            array(
                'label'       => __( 'Trust Pillars', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default'     => array(
                    array( 'title' => '24/7 Emergency Readiness', 'desc' => 'Night or day, weekend or holiday — our dispatchers answer within seconds.', 'icon_name' => 'clock' ),
                    array( 'title' => 'Damage-Free Flatbed Fleet', 'desc' => 'Hydraulic tilt flatbeds & soft straps protect luxury, AWD, and commercial vehicles.', 'icon_name' => 'shield-check' ),
                    array( 'title' => 'All 16 Regions Covered', 'desc' => 'Fast local rescue and long-distance recovery corridors across Ghana.', 'icon_name' => 'map-pin' ),
                    array( 'title' => 'Certified Mechanics & OEM Parts', 'desc' => 'Backed by our Spintex diagnostic garage and genuine parts inventory.', 'icon_name' => 'award' ),
                ),
            )
        );

        $this->add_responsive_control(
            'pillar_columns',
            array(
                'label'          => __( 'Pillar Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '2',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array( '1' => '1', '2' => '2' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-pillars-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-why-section', '#015557', '#ffffff' );

        $this->start_controls_section(
            'section_why_pillar_style',
            array(
                'label' => __( 'Pillar Cards & Image Style', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'pillar_card_bg',
            array(
                'label'     => __( 'Pillar Card Background', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#192e2e',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-pillars-grid > div' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'pillars_gap',
            array(
                'label'      => __( 'Pillar Grid Gap', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
                'default'    => array( 'size' => 20, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-pillars-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s       = $this->get_settings_for_display();
        $img_url = ! empty( $s['side_image']['url'] ) ? $s['side_image']['url'] : stwc_asset_image( 'welcome-towing-image.jpg' );
        $pillars = $s['pillars'] ?? array();
        ?>
        <section class="stwc-widget-section stwc-why-section bg-primary text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-10 items-center">
                    <div class="lg:col-span-5">
                        <div class="aspect-[4/3] rounded-[1px] overflow-hidden border border-white/15 shadow-2xl">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $s['heading'] ?? 'Why Choose Us' ); ?>" class="w-full h-full object-cover" loading="lazy" />
                        </div>
                    </div>
                    <div class="lg:col-span-7 space-y-6">
                        <div class="stwc-section-header text-left">
                            <?php if ( ! empty( $s['badge'] ) ) : ?>
                                <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                            <?php endif; ?>
                            <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-white mb-4"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
                            <p class="text-white/80 leading-relaxed"><?php echo esc_html( $s['description'] ?? '' ); ?></p>
                        </div>

                        <div class="stwc-pillars-grid grid gap-4">
                            <?php foreach ( $pillars as $p ) : ?>
                                <div class="bg-white/10 border border-white/15 p-5 rounded-[1px]">
                                    <div class="w-10 h-10 bg-accent text-white rounded-[1px] flex items-center justify-center mb-3">
                                        <?php echo $this->render_icon( $p['icon_name'] ?? 'shield-check', 'w-5 h-5' ); ?>
                                    </div>
                                    <h3 class="font-bold text-white text-base mb-1"><?php echo esc_html( $p['title'] ?? '' ); ?></h3>
                                    <p class="text-xs sm:text-sm text-white/75 leading-relaxed"><?php echo esc_html( $p['desc'] ?? '' ); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
