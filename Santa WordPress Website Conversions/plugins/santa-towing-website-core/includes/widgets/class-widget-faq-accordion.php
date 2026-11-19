<?php
/**
 * Elementor Free Widget: FAQ Accordion & Topic Filter
 *
 * Replicates React `Faqs.tsx` & section FAQ accordions.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_FAQ_Accordion extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-faq-accordion';
    }

    public function get_title() {
        return __( 'Santa FAQ Accordion', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_faq_content',
            array(
                'label' => __( 'FAQ Source & Display', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'faq_source',
            array(
                'label'   => __( 'FAQ Data Source', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'cpt',
                'options' => array(
                    'cpt'    => __( 'Dynamic FAQ Posts (CPT)', 'santa-towing-website-core' ),
                    'custom' => __( 'Custom Editable Items (Repeater)', 'santa-towing-website-core' ),
                ),
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
                'default'   => 'Frequently Asked Questions',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'     => __( 'Heading', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Answers Before We Dispatch',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'category_filter',
            array(
                'label'     => __( 'Filter by Topic', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'All',
                'options'   => array(
                    'All'      => 'All Topics',
                    'Towing'   => 'Towing',
                    'Rentals'  => 'Rentals',
                    'Repairs'  => 'Repairs',
                    'Sales'    => 'Sales',
                    'Parts'    => 'Parts',
                    'Fleet'    => 'Fleet',
                    'Payments' => 'Payments',
                ),
                'condition' => array( 'faq_source' => 'cpt' ),
            )
        );

        $faq_repeater = new \Elementor\Repeater();
        $faq_repeater->add_control(
            'q',
            array(
                'label'   => __( 'Question', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'How fast is your emergency response?',
            )
        );
        $faq_repeater->add_control(
            'a',
            array(
                'label'   => __( 'Answer', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Urban dispatch typically arrives within 30 to 45 minutes depending on distance and traffic.',
            )
        );
        $faq_repeater->add_control(
            'category',
            array(
                'label'   => __( 'Category', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'General',
            )
        );

        $this->add_control(
            'custom_faqs',
            array(
                'label'       => __( 'Custom FAQ Items', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $faq_repeater->get_controls(),
                'title_field' => '{{{ q }}}',
                'condition'   => array( 'faq_source' => 'custom' ),
                'default'     => array(),
            )
        );

        $this->add_control(
            'compact_mode',
            array(
                'label'        => __( 'Compact Column Layout (No Outer Section Padding)', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'show_topic_tabs',
            array(
                'label'        => __( 'Show Interactive Topic Tabs', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_banner_image',
            array(
                'label'        => __( 'Show Header Image', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'banner_image',
            array(
                'label'     => __( 'FAQ Support Image', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => array( 'url' => stwc_asset_image( 'welcome-towing-image.jpg' ) ),
                'condition' => array( 'show_banner_image' => 'yes' ),
            )
        );

        $this->add_control(
            'max_items',
            array(
                'label'     => __( 'Max Questions', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 11,
                'min'       => 1,
                'max'       => 50,
                'condition' => array( 'faq_source' => 'cpt' ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-faq-section', '#ffffff', '#131514' );

        $this->start_controls_section(
            'section_faq_item_style',
            array(
                'label' => __( 'Accordion Item Style', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'question_color',
            array(
                'label'     => __( 'Question Text Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#131514',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-faq-trigger' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'item_bg_color',
            array(
                'label'     => __( 'Item Background Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-faq-list .stwc-filter-item' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_spacing',
            array(
                'label'      => __( 'Gap Between Questions', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 4, 'max' => 32 ) ),
                'default'    => array( 'size' => 14, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-faq-list' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'banner_height',
            array(
                'label'      => __( 'Support Image Max Height', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 120, 'max' => 460 ) ),
                'default'    => array( 'size' => 240, 'unit' => 'px' ),
                'condition'  => array( 'show_banner_image' => 'yes' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-faq-banner img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s       = $this->get_settings_for_display();
        $source  = $s['faq_source'] ?? 'cpt';
        $compact = 'yes' === ( $s['compact_mode'] ?? 'no' );

        if ( 'custom' === $source && ! empty( $s['custom_faqs'] ) ) {
            $faqs = $s['custom_faqs'];
        } else {
            $topic = $s['category_filter'] ?? 'All';
            $limit = absint( $s['max_items'] ?? 11 );
            $faqs  = array_slice( stwc_get_faqs( $topic ), 0, $limit );
        }

        $topics = array( 'All' );
        foreach ( $faqs as $f ) {
            if ( ! empty( $f['category'] ) && ! in_array( $f['category'], $topics, true ) ) {
                $topics[] = $f['category'];
            }
        }
        $wrap_cls  = $compact ? 'stwc-widget-section stwc-faq-section stwc-faq-compact' : 'stwc-widget-section stwc-faq-section py-16';
        $inner_cls = $compact ? 'w-full' : 'max-w-4xl mx-auto px-4 sm:px-6 lg:px-8';
        ?>
        <section class="<?php echo esc_attr( $wrap_cls ); ?>">
            <div class="<?php echo esc_attr( $inner_cls ); ?>" data-stwc-filter-scope="faqs">
                <?php if ( 'yes' === ( $s['show_header'] ?? 'yes' ) ) : ?>
                    <div class="stwc-section-header <?php echo $compact ? 'text-left mb-6' : 'text-center mb-10'; ?>">
                        <?php if ( ! empty( $s['badge'] ) ) : ?>
                            <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['heading'] ) ) : ?>
                            <h2 class="stwc-section-heading <?php echo $compact ? 'text-2xl font-extrabold text-dark mb-0' : 'text-3xl md:text-4xl font-extrabold text-dark mb-3'; ?>">
                                <?php echo esc_html( $s['heading'] ); ?>
                            </h2>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( 'yes' === ( $s['show_banner_image'] ?? 'no' ) && ! empty( $s['banner_image']['url'] ) ) : ?>
                    <div class="stwc-faq-banner mb-8 rounded-[1px] overflow-hidden border border-dark/10 shadow-sm">
                        <img src="<?php echo esc_url( $s['banner_image']['url'] ); ?>" alt="<?php echo esc_attr( $s['heading'] ?? 'Santa Towing FAQs' ); ?>" class="w-full object-cover" loading="lazy" />
                    </div>
                <?php endif; ?>

                <?php if ( 'yes' === ( $s['show_topic_tabs'] ?? 'yes' ) && count( $topics ) > 2 ) : ?>
                    <div class="flex flex-wrap justify-center gap-2 mb-8">
                        <?php foreach ( $topics as $idx => $t ) : ?>
                            <button type="button" class="stwc-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-bold transition-colors <?php echo 0 === $idx ? 'bg-primary text-white is-active' : 'bg-slate-100 text-dark hover:bg-primary hover:text-white'; ?>" data-filter-value="<?php echo esc_attr( $t ); ?>">
                                <?php echo esc_html( $t ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="stwc-faq-list flex flex-col">
                    <?php foreach ( $faqs as $idx => $faq ) : ?>
                        <div class="stwc-filter-item bg-white rounded-[1px] border border-dark/10 overflow-hidden shadow-sm" data-category="<?php echo esc_attr( $faq['category'] ?? 'General' ); ?>">
                            <button type="button" class="stwc-faq-trigger w-full px-6 <?php echo $compact ? 'py-4' : 'py-5'; ?> text-left flex items-center justify-between gap-4 font-bold text-dark hover:text-accent transition-colors" aria-expanded="<?php echo 0 === $idx ? 'true' : 'false'; ?>">
                                <span><?php echo esc_html( $faq['q'] ?? '' ); ?></span>
                                <span class="stwc-faq-chevron shrink-0 transition-transform duration-300 <?php echo 0 === $idx ? 'rotate-180' : ''; ?>">
                                    <?php echo $this->render_icon( 'chevron-down', 'w-5 h-5 text-accent' ); ?>
                                </span>
                            </button>
                            <div class="stwc-faq-panel px-6 pb-5 text-dark/75 leading-relaxed <?php echo 0 === $idx ? '' : 'hidden'; ?>">
                                <?php echo wp_kses_post( wpautop( $faq['a'] ?? '' ) ); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
