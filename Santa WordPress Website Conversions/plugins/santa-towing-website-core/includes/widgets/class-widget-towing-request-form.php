<?php
/**
 * Elementor Free Widget: Emergency Towing Request Form (16 Ghana Regions -> 232 Areas + WhatsApp Dispatch)
 *
 * Replicates React `src/pages/Towing.tsx` dependent dropdown & WhatsApp dispatch form.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Towing_Request_Form extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-towing-request-form';
    }

    public function get_title() {
        return __( 'Santa Emergency Towing Form', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_form_content',
            array(
                'label' => __( 'Towing Dispatch Banner & Form', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_hero_split',
            array(
                'label'        => __( 'Use Two-Column Emergency Banner Layout', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'hero_heading',
            array(
                'label'     => __( 'Banner Heading', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Stranded? Help Is On The Way.',
                'condition' => array( 'show_hero_split' => 'yes' ),
            )
        );

        $this->add_control(
            'hero_subtext',
            array(
                'label'     => __( 'Banner Subtext', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => 'Fast dispatch, GPS-tracked recovery trucks, and certified operators ready 24/7 across all 16 regions of Ghana.',
                'condition' => array( 'show_hero_split' => 'yes' ),
            )
        );

        $this->add_control(
            'bg_image',
            array(
                'label'     => __( 'Banner Background Image', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => array( 'url' => stwc_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ) ),
                'condition' => array( 'show_hero_split' => 'yes' ),
            )
        );

        $this->add_control(
            'form_title',
            array(
                'label'   => __( 'Form Title', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Request a Tow Now',
            )
        );

        $this->add_control(
            'form_subtitle',
            array(
                'label'   => __( 'Form Subtitle', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Complete your location for instant dispatch alert & WhatsApp handoff.',
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-towing-form-section', '#015557', '#ffffff' );

        $this->start_controls_section(
            'section_form_box_style',
            array(
                'label' => __( 'Form Card Styling', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'btn_bg_color',
            array(
                'label'     => __( 'Submit Button Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#d80000',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-towing-form .stwc-submit-btn' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'form_card_padding',
            array(
                'label'      => __( 'Form Box Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-towing-form-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s          = $this->get_settings_for_display();
        $split      = ( 'yes' === ( $s['show_hero_split'] ?? 'yes' ) );
        $bg_url     = ! empty( $s['bg_image']['url'] ) ? $s['bg_image']['url'] : stwc_asset_image( 'image-4-van-hyundai-h1-2022.jpg' );
        $phone_raw  = stwc_get_content( 'global.links.phone', '0244753849' );
        $phone_tel  = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );

        $form_html = STWC_Elementor::render_towing_form_markup(
            array(
                'title'    => $s['form_title'] ?? 'Request a Tow Now',
                'subtitle' => $s['form_subtitle'] ?? '',
            )
        );

        if ( ! $split ) {
            echo '<section class="stwc-widget-section stwc-towing-form-section py-12"><div class="max-w-3xl mx-auto px-4">' . $form_html . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return;
        }
        ?>
        <section class="stwc-widget-section stwc-towing-form-section relative bg-primary overflow-hidden py-16 lg:py-24">
            <img src="<?php echo esc_url( $bg_url ); ?>" alt="" class="absolute inset-0 w-full h-full object-cover opacity-25" />
            <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-primary/60"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-10 items-center">
                    <div class="lg:col-span-5 text-white space-y-6 stwc-section-header text-left">
                        <span class="inline-block bg-accent text-white text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full">
                            <?php esc_html_e( '24/7 Emergency Dispatch', 'santa-towing-website-core' ); ?>
                        </span>
                        <h1 class="stwc-section-heading text-4xl md:text-5xl font-extrabold leading-tight text-white">
                            <?php echo esc_html( $s['hero_heading'] ?? 'Stranded? Help Is On The Way.' ); ?>
                        </h1>
                        <p class="text-lg text-white/80 leading-relaxed">
                            <?php echo esc_html( $s['hero_subtext'] ?? '' ); ?>
                        </p>
                        <div class="pt-2">
                            <a href="<?php echo esc_url( $phone_tel ); ?>" class="inline-flex items-center gap-3 bg-white text-dark hover:bg-accent hover:text-white px-7 py-4 rounded-full font-bold shadow-lg transition-colors">
                                <?php echo $this->render_icon( 'phone', 'w-5 h-5 text-accent' ); ?>
                                <span><?php echo esc_html( 'Hotline: ' . $phone_raw ); ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="lg:col-span-7">
                        <?php echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
