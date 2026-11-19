<?php
/**
 * Elementor Free Widget: Careers Board & CV Application Form
 *
 * Replaces `[stwc_careers]` shortcode inside Elementor pages cleanly.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Careers_Board extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-careers-board';
    }

    public function get_title() {
        return __( 'Santa Careers & CV Application', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-user-circle-o';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_careers_content',
            array(
                'label' => __( 'Vacancies Header & Labels', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_header',
            array(
                'label'        => __( 'Show Section Header', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'     => __( 'Eyebrow', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Active Recruitment',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'     => __( 'Heading', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Open Positions',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'description',
            array(
                'label'     => __( 'Intro Text', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => 'Select any open vacancy below and click Apply Now to submit your details and upload your CV/Resume (PDF, DOC, DOCX up to 5MB).',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'apply_button_label',
            array(
                'label'   => __( 'Apply Button Text', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Apply Now',
            )
        );

        $this->add_control(
            'empty_message',
            array(
                'label'   => __( 'No Open Roles Message', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'No active vacancies right now — please check back soon or contact our office.',
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-careers-section', '#f8fafc', '#131514' );
    }

    protected function render() {
        $s          = $this->get_settings_for_display();
        $jobs       = stwc_get_careers();
        $apply_text = ! empty( $s['apply_button_label'] ) ? $s['apply_button_label'] : __( 'Apply Now', 'santa-towing-website-core' );
        ?>
        <section id="open-positions" class="stwc-widget-section stwc-careers-section py-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php if ( 'yes' === ( $s['show_header'] ?? 'yes' ) ) : ?>
                    <div class="stwc-section-header text-center mb-10">
                        <?php if ( ! empty( $s['badge'] ) ) : ?>
                            <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['heading'] ) ) : ?>
                            <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-dark mb-3"><?php echo esc_html( $s['heading'] ); ?></h2>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['description'] ) ) : ?>
                            <p class="text-dark/70 max-w-2xl mx-auto"><?php echo esc_html( $s['description'] ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="stwc-careers-board space-y-6">
                    <?php if ( empty( $jobs ) ) : ?>
                        <div class="bg-white rounded-[1px] p-8 border border-dark/10 text-center text-dark/70">
                            <?php echo esc_html( $s['empty_message'] ?? '' ); ?>
                        </div>
                    <?php else : ?>
                        <?php foreach ( $jobs as $job ) : ?>
                            <div class="bg-white rounded-[1px] p-6 sm:p-8 border border-dark/10 shadow-sm">
                                <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( $job['department'] ); ?></span>
                                        <h3 class="text-2xl font-bold text-dark mt-1"><?php echo esc_html( $job['title'] ); ?></h3>
                                        <p class="text-sm text-dark/60 mt-1"><?php echo esc_html( $job['location'] . ' • ' . $job['type'] ); ?></p>
                                    </div>
                                    <button type="button" class="stwc-open-apply bg-accent hover:bg-black text-white px-6 py-3 font-bold rounded-full text-sm transition-colors shrink-0" data-job-title="<?php echo esc_attr( $job['title'] ); ?>">
                                        <?php echo esc_html( $apply_text ); ?>
                                    </button>
                                </div>
                                <p class="text-dark/80 mb-2 leading-relaxed"><?php echo esc_html( $job['description'] ); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="stwc-career-form-box hidden bg-white border border-dark/10 p-6 sm:p-8 rounded-[1px] shadow-sm">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <h4 class="text-xl font-bold text-dark"><?php esc_html_e( 'Submit Job Application', 'santa-towing-website-core' ); ?>: <span class="stwc-selected-job-title text-accent"></span></h4>
                            <button type="button" class="stwc-close-apply px-3 py-1.5 text-sm font-bold text-dark/70 hover:text-dark bg-slate-50 border border-dark/15 rounded-full transition-colors" aria-label="<?php esc_attr_e( 'Close Application Form', 'santa-towing-website-core' ); ?>">&times;</button>
                        </div>
                        <?php echo STWC_Elementor::render_enquiry_form_markup( array( 'mode' => 'career' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
