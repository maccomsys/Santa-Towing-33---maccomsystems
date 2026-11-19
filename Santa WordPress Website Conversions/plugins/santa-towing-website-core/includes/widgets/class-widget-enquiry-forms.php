<?php
/**
 * Elementor Free Widget: Multi-Mode Enquiry, Haulage Quote, Fleet & Career Form
 *
 * Supports: Haulage Quote, Contact Enquiry, Corporate Fleet, Car Inquiry,
 * Rental Booking, Auto Part Inquiry, and Career Application (w/ CV Upload).
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Enquiry_Forms extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-enquiry-forms';
    }

    public function get_title() {
        return __( 'Santa Enquiry / Quote / Application Form', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-mail';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_form_settings',
            array(
                'label' => __( 'Form Type & Labels', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'form_mode',
            array(
                'label'   => __( 'Form Mode', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'haulage',
                'options' => array(
                    'haulage'        => __( 'Haulage Quote Request', 'santa-towing-website-core' ),
                    'contact'        => __( 'General Contact Enquiry', 'santa-towing-website-core' ),
                    'fleet'          => __( 'Corporate Fleet Consultation', 'santa-towing-website-core' ),
                    'car_inquiry'    => __( 'Car Sales Purchase Inquiry', 'santa-towing-website-core' ),
                    'rental_booking' => __( 'Car Rental Reservation', 'santa-towing-website-core' ),
                    'part_inquiry'   => __( 'Auto Part Availability Check', 'santa-towing-website-core' ),
                    'career'         => __( 'Career Job Application (CV Upload)', 'santa-towing-website-core' ),
                ),
            )
        );

        $this->add_control(
            'form_title',
            array(
                'label'   => __( 'Form Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Request a Haulage Quote or Dispatch',
            )
        );

        $this->add_control(
            'item_title',
            array(
                'label'       => __( 'Pre-filled Vehicle / Part / Job Title (Optional)', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'description' => __( 'Leave blank on singular pages to auto-use the current post title.', 'santa-towing-website-core' ),
            )
        );

        $this->add_control(
            'side_image',
            array(
                'label'   => __( 'Optional Side Accent Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => '' ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-enquiry-section', '#f8fafc', '#131514' );

        $this->start_controls_section(
            'section_enquiry_btn_style',
            array(
                'label' => __( 'Form Card & Submit Styling', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'submit_btn_bg',
            array(
                'label'     => __( 'Submit Button Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#015557',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-enquiry-form-card .stwc-submit-btn' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_padding',
            array(
                'label'      => __( 'Card Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-enquiry-form-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s          = $this->get_settings_for_display();
        $mode       = $s['form_mode'] ?? 'haulage';
        $item_title = $s['item_title'] ?? '';
        $item_id    = 0;
        if ( '' === $item_title && is_singular( array( 'st_car', 'st_rental', 'st_part', 'st_career' ) ) ) {
            $item_id    = get_queried_object_id();
            $item_title = get_the_title( $item_id );
        }

        $form_html = STWC_Elementor::render_enquiry_form_markup(
            array(
                'mode'       => $mode,
                'title'      => $s['form_title'] ?? '',
                'item_id'    => $item_id,
                'item_title' => $item_title,
            )
        );

        $side_img = ! empty( $s['side_image']['url'] ) ? $s['side_image']['url'] : '';
        ?>
        <section class="stwc-widget-section stwc-enquiry-section bg-slate-50 py-14">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php if ( $side_img ) : ?>
                    <div class="grid lg:grid-cols-12 gap-8 items-stretch">
                        <div class="lg:col-span-5 rounded-[1px] overflow-hidden shadow-md">
                            <img src="<?php echo esc_url( $side_img ); ?>" alt="<?php echo esc_attr( $s['form_title'] ?? '' ); ?>" class="w-full h-full object-cover min-h-[260px]" loading="lazy" />
                        </div>
                        <div class="lg:col-span-7">
                            <?php echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </div>
                <?php else : ?>
                    <?php echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
