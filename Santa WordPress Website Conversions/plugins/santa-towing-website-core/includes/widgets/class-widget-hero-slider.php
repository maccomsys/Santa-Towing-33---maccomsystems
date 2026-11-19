<?php
/**
 * Elementor Free Widget: Hero Carousel Slider & Emergency Dispatch CTA
 *
 * Replicates React `src/components/sections/HeroSlider.tsx`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Hero_Slider extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-hero-slider';
    }

    public function get_title() {
        return __( 'Santa Hero Slider', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-slides';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_slides',
            array(
                'label' => __( 'Hero Slides & CTAs', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'badge',
            array(
                'label'   => __( 'Top Badge', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '24/7 Rapid Response Ghana',
            )
        );
        $repeater->add_control(
            'title',
            array(
                'label'   => __( 'Slide Headline', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Fast 24/7 Emergency Towing & Recovery',
            )
        );
        $repeater->add_control(
            'subtitle',
            array(
                'label'   => __( 'Slide Subtitle', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Stranded anywhere in Accra, Tema, Kumasi, or nationwide? Our GPS-tracked flatbed tow trucks arrive in 30 minutes.',
            )
        );
        $repeater->add_control(
            'image',
            array(
                'label'   => __( 'Background Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ) ),
            )
        );
        $repeater->add_control(
            'cta_label',
            array(
                'label'   => __( 'Primary CTA Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Request Emergency Tow',
            )
        );
        $repeater->add_control(
            'cta_link',
            array(
                'label'   => __( 'Primary CTA Link', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => home_url( '/towing/' ) ),
            )
        );

        $this->add_control(
            'slides',
            array(
                'label'       => __( 'Slides', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default'     => array(
                    array(
                        'badge'     => '24/7 Emergency Roadside Dispatch',
                        'title'     => 'Fast 24/7 Emergency Towing & Car Recovery in Ghana',
                        'subtitle'  => 'Rapid flatbed towing, accident extraction, and roadside support across Greater Accra, Tema, Kumasi & all 16 regions.',
                        'image'     => array( 'url' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ) ),
                        'cta_label' => 'Request Emergency Tow',
                        'cta_link'  => array( 'url' => home_url( '/towing/' ) ),
                    ),
                    array(
                        'badge'     => 'Heavy Haulage & Tema Port Logistics',
                        'title'     => 'Heavy Machinery, Container & Vehicle Haulage',
                        'subtitle'  => 'Lowbed trailers, ISO container chassis, and multi-car carriers from Tema Harbour to anywhere in Ghana.',
                        'image'     => array( 'url' => stwc_asset_image( 'haulage-semi-truck-1920.jpg' ) ),
                        'cta_label' => 'Get Haulage Quote',
                        'cta_link'  => array( 'url' => home_url( '/haulage/' ) ),
                    ),
                    array(
                        'badge'     => 'Certified Garage, Sales & Rentals',
                        'title'     => 'Auto Repairs, Inspected Cars & Executive Rentals',
                        'subtitle'  => 'Diagnostic servicing, genuine OEM spare parts, quality pre-owned vehicles, and corporate rental fleets.',
                        'image'     => array( 'url' => stwc_asset_image( 'image-6-luxury-mercedes-benz.jpg' ) ),
                        'cta_label' => 'Explore Inventory',
                        'cta_link'  => array( 'url' => home_url( '/sales/' ) ),
                    ),
                ),
            )
        );

        $this->add_control(
            'autoplay',
            array(
                'label'        => __( 'Enable Autoplay', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'autoplay_interval',
            array(
                'label'     => __( 'Autoplay Speed (ms)', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 6000,
                'min'       => 2000,
                'max'       => 15000,
                'condition' => array( 'autoplay' => 'yes' ),
            )
        );

        $this->add_control(
            'show_call_btn',
            array(
                'label'        => __( 'Show Direct Call Button', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();

        // Style & Responsive Controls
        $this->start_controls_section(
            'section_style_hero',
            array(
                'label' => __( 'Hero Style & Responsive Sizing', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'hero_min_height',
            array(
                'label'      => __( 'Minimum Height', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'vh' ),
                'range'      => array(
                    'px' => array( 'min' => 380, 'max' => 920 ),
                    'vh' => array( 'min' => 50, 'max' => 100 ),
                ),
                'default'    => array( 'size' => 600, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-hero-slider' => 'min-height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'overlay_bg',
            array(
                'label'     => __( 'Overlay Tint Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(1, 85, 87, 0.84)',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-hero-overlay' => 'background: linear-gradient(90deg, {{VALUE}} 0%, rgba(19,21,20,0.65) 100%);',
                ),
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => __( 'Headline Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-hero-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => __( 'Headline Typography', 'santa-towing-website-core' ),
                'selector' => '{{WRAPPER}} .stwc-hero-title',
            )
        );

        $this->add_control(
            'cta_bg_color',
            array(
                'label'     => __( 'Primary CTA Background', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#d80000',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-hero-primary-cta' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'hero_content_padding',
            array(
                'label'      => __( 'Content Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-hero-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $slides    = $settings['slides'] ?? array();
        $autoplay  = ( 'yes' === ( $settings['autoplay'] ?? 'yes' ) ) ? '1' : '0';
        $interval  = absint( $settings['autoplay_interval'] ?? 6000 );
        $phone_tel = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
        $phone_raw = stwc_get_content( 'global.links.phone', '0244753849' );

        if ( empty( $slides ) ) {
            return;
        }
        ?>
        <section class="stwc-hero-slider relative overflow-hidden bg-primary text-white flex items-center" data-stwc-hero-slider data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>">
            <?php foreach ( $slides as $idx => $slide ) : ?>
                <?php
                $img_url = ! empty( $slide['image']['url'] ) ? $slide['image']['url'] : stwc_asset_image( 'image-1-towing-vehicle.jpg' );
                $cta_url = ! empty( $slide['cta_link']['url'] ) ? $slide['cta_link']['url'] : home_url( '/towing/' );
                ?>
                <div class="stwc-hero-slide absolute inset-0 transition-opacity duration-700 <?php echo 0 === $idx ? 'opacity-100 z-10' : 'opacity-0 pointer-events-none z-0'; ?>">
                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $slide['title'] ?? '' ); ?>" class="w-full h-full object-cover" />
                    <div class="stwc-hero-overlay absolute inset-0"></div>
                </div>
            <?php endforeach; ?>

            <div class="stwc-hero-inner relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
                <?php foreach ( $slides as $idx => $slide ) : ?>
                    <?php $cta_url = ! empty( $slide['cta_link']['url'] ) ? $slide['cta_link']['url'] : home_url( '/towing/' ); ?>
                    <div class="stwc-hero-copy max-w-3xl <?php echo 0 === $idx ? 'block' : 'hidden'; ?>">
                        <?php if ( ! empty( $slide['badge'] ) ) : ?>
                            <span class="inline-block bg-accent text-white text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-5 shadow-md">
                                <?php echo esc_html( $slide['badge'] ); ?>
                            </span>
                        <?php endif; ?>

                        <h1 class="stwc-hero-title text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                            <?php echo esc_html( $slide['title'] ?? '' ); ?>
                        </h1>

                        <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                            <p class="text-lg sm:text-xl text-white/85 leading-relaxed mb-8 max-w-2xl">
                                <?php echo esc_html( $slide['subtitle'] ); ?>
                            </p>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-4">
                            <a href="<?php echo esc_url( $cta_url ); ?>" class="stwc-hero-primary-cta inline-flex items-center gap-2 bg-accent hover:bg-black text-white font-bold px-8 py-4 rounded-full shadow-lg transition-colors">
                                <span><?php echo esc_html( $slide['cta_label'] ?? 'Request Dispatch' ); ?></span>
                                <?php echo $this->render_icon( 'arrow-right', 'w-5 h-5' ); ?>
                            </a>
                            <?php if ( 'yes' === ( $settings['show_call_btn'] ?? 'yes' ) ) : ?>
                                <a href="<?php echo esc_url( $phone_tel ); ?>" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-white font-bold px-7 py-4 rounded-full backdrop-blur-md transition-colors">
                                    <?php echo $this->render_icon( 'phone', 'w-5 h-5 text-accent' ); ?>
                                    <span><?php echo esc_html( 'Call ' . $phone_raw ); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ( count( $slides ) > 1 ) : ?>
                    <div class="flex items-center gap-3 mt-10">
                        <button type="button" class="stwc-hero-prev w-10 h-10 rounded-full bg-white/15 hover:bg-accent text-white flex items-center justify-center transition-colors" aria-label="<?php esc_attr_e( 'Previous slide', 'santa-towing-website-core' ); ?>">
                            <?php echo $this->render_icon( 'chevron-left', 'w-5 h-5' ); ?>
                        </button>
                        <div class="flex gap-2">
                            <?php foreach ( $slides as $idx => $s ) : ?>
                                <button type="button" class="stwc-hero-dot w-3 h-3 rounded-full transition-all <?php echo 0 === $idx ? 'bg-accent w-8' : 'bg-white/40'; ?>" data-slide-index="<?php echo (int) $idx; ?>" aria-label="<?php echo esc_attr( 'Slide ' . ( $idx + 1 ) ); ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="stwc-hero-next w-10 h-10 rounded-full bg-white/15 hover:bg-accent text-white flex items-center justify-center transition-colors" aria-label="<?php esc_attr_e( 'Next slide', 'santa-towing-website-core' ); ?>">
                            <?php echo $this->render_icon( 'chevron-right', 'w-5 h-5' ); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
