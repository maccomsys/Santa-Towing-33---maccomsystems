<?php
/**
 * Elementor Free Widget: Feature Cards, Process Steps, Packages, Feed & Link Pills
 *
 * Covers Quick Action Cards, Stand-Out Benefits, Process Steps, Service Capabilities,
 * Haulage Photo Cards, Fleet Industries, Maintenance Packages, Gallery Highlights Feed,
 * and Featured Hub Pills with zero Pro dependencies or raw HTML blocks.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Feature_Cards extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-feature-cards';
    }

    public function get_title() {
        return __( 'Santa Feature & Cards Grid', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-info-box';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_cards_header',
            array(
                'label' => __( 'Layout Variant & Header', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'variant',
            array(
                'label'   => __( 'Card Display Variant', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'service-cards',
                'options' => array(
                    'quick-actions-overlap' => __( 'Home 5-Card Hero Overlap Strip', 'santa-towing-website-core' ),
                    'icon-center-white'     => __( 'White Icon Cards (Stand Out)', 'santa-towing-website-core' ),
                    'icon-top-light'        => __( 'Top Icon Cards (Fleet / Careers / Support)', 'santa-towing-website-core' ),
                    'icon-dark-glass'       => __( 'Dark Glass Icon Cards', 'santa-towing-website-core' ),
                    'process-steps'         => __( 'Process Steps Grid', 'santa-towing-website-core' ),
                    'service-cards'         => __( 'Capabilities / Contact Cards Grid', 'santa-towing-website-core' ),
                    'photo-cards'           => __( 'Photo Cards Grid (Haulage Services)', 'santa-towing-website-core' ),
                    'photo-overlay'         => __( 'Photo Gradient Overlay Cards (Industries)', 'santa-towing-website-core' ),
                    'pricing-packages'      => __( 'Maintenance Package Cards', 'santa-towing-website-core' ),
                    'photo-feed'            => __( 'Square Linked Photo Feed', 'santa-towing-website-core' ),
                    'link-pills'            => __( 'Quick Route & District Link Pills', 'santa-towing-website-core' ),
                ),
            )
        );

        $this->add_control(
            'step_style',
            array(
                'label'     => __( 'Process Step Card Style', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'card',
                'options'   => array(
                    'centered-square' => __( 'Centered Number Tile + Title', 'santa-towing-website-core' ),
                    'card'            => __( 'Bordered Number Card + Description', 'santa-towing-website-core' ),
                    'minimal-num'     => __( 'Minimal Accent Number Card', 'santa-towing-website-core' ),
                ),
                'condition' => array( 'variant' => 'process-steps' ),
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
                'default'   => '',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'     => __( 'Heading', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Our Key Capabilities',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_control(
            'subheading',
            array(
                'label'     => __( 'Subheading', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => '',
                'condition' => array( 'show_header' => 'yes' ),
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label'          => __( 'Grid Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-feature-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_cards_items',
            array(
                'label' => __( 'Cards / Steps / Pills Repeater', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'title',
            array(
                'label'   => __( 'Title / Pill Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Card Title',
            )
        );

        $repeater->add_control(
            'subtitle',
            array(
                'label'   => __( 'Eyebrow / Meta Subtitle', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $repeater->add_control(
            'step_number',
            array(
                'label'   => __( 'Step Number (Process Steps)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '01',
            )
        );

        $repeater->add_control(
            'description',
            array(
                'label'   => __( 'Description', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
            )
        );

        $repeater->add_control(
            'features_list',
            array(
                'label'       => __( 'Checklist Lines (One per line, for Packages)', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => '',
                'description' => __( 'Enter one bullet point per line.', 'santa-towing-website-core' ),
            )
        );

        $repeater->add_control(
            'footer_tag',
            array(
                'label'   => __( 'Footer Uppercase Tag', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $repeater->add_control(
            'icon_name',
            array(
                'label'   => __( 'Icon', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => array(
                    'none'         => 'None',
                    'truck'        => 'Truck / Towing',
                    'car'          => 'Car / Rentals',
                    'shield'       => 'Shield',
                    'shield-check' => 'Shield Check',
                    'wrench'       => 'Wrench / Repairs',
                    'settings'     => 'Settings / Parts',
                    'clock'        => 'Clock / 24-7',
                    'tag'          => 'Tag / Pricing',
                    'map-pin'      => 'Map Pin',
                    'zap'          => 'Zap / Fast',
                    'users'        => 'Users / Management',
                    'bar-chart'    => 'Bar Chart / Analytics',
                    'building'     => 'Building / Corporate',
                    'hard-hat'     => 'Hard Hat / Construction',
                    'phone'        => 'Phone',
                    'message-circle' => 'WhatsApp',
                    'mail'         => 'Email',
                    'award'        => 'Award',
                ),
            )
        );

        $repeater->add_control(
            'image',
            array(
                'label'   => __( 'Photo (Photo Cards / Overlay / Feed)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => '' ),
            )
        );

        $repeater->add_control(
            'link',
            array(
                'label'   => __( 'Link (Card, Title or Button)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '' ),
            )
        );

        $repeater->add_control(
            'link_label',
            array(
                'label'   => __( 'Button / Link Label (Optional)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $repeater->add_control(
            'button_style',
            array(
                'label'   => __( 'Button / Pill Highlight Style', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => array(
                    'default' => 'Default / Primary Link',
                    'accent'  => 'Accent Red',
                    'primary' => 'Primary Teal',
                    'dark'    => 'Dark Charcoal',
                ),
            )
        );

        $repeater->add_control(
            'is_featured',
            array(
                'label'        => __( 'Highlight Border / Active Pill', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'items',
            array(
                'label'       => __( 'Items', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default'     => array(),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_bottom_callout',
            array(
                'label' => __( 'Optional Bottom Callout Bar', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_callout',
            array(
                'label'        => __( 'Show Bottom Callout Bar', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'callout_title',
            array(
                'label'     => __( 'Callout Heading', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => '',
                'condition' => array( 'show_callout' => 'yes' ),
            )
        );

        $this->add_control(
            'callout_text',
            array(
                'label'     => __( 'Callout Description', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => '',
                'condition' => array( 'show_callout' => 'yes' ),
            )
        );

        $cb_repeater = new \Elementor\Repeater();
        $cb_repeater->add_control(
            'label',
            array(
                'label'   => __( 'Link Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Accra',
            )
        );
        $cb_repeater->add_control(
            'link',
            array(
                'label'   => __( 'Destination URL', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => home_url( '/service-areas/accra/' ) ),
            )
        );
        $cb_repeater->add_control(
            'is_accent',
            array(
                'label'        => __( 'Accent CTA Pill', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'callout_links',
            array(
                'label'       => __( 'Callout Quick Links', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $cb_repeater->get_controls(),
                'title_field' => '{{{ label }}}',
                'condition'   => array( 'show_callout' => 'yes' ),
                'default'     => array(),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-cards-section', '#ffffff', '#131514' );

        $this->start_controls_section(
            'section_cards_appearance',
            array(
                'label' => __( 'Cards Spacing & Subtitle Color', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'subheading_color',
            array(
                'label'     => __( 'Subheading Text Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-cards-subheading' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'grid_gap',
            array(
                'label'      => __( 'Grid Gap', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
                'default'    => array( 'size' => 24, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-feature-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s            = $this->get_settings_for_display();
        $variant      = $s['variant'] ?? 'service-cards';
        $items        = $s['items'] ?? array();
        $show_header  = 'yes' === ( $s['show_header'] ?? 'yes' );
        $is_dark_bg   = in_array( $variant, array( 'icon-center-white', 'icon-dark-glass' ), true );
        $wrapper_cls  = 'quick-actions-overlap' === $variant
            ? 'stwc-widget-section stwc-cards-section stwc-overlap-strip relative -mt-14 sm:-mt-20 z-30 pb-8 md:pb-16'
            : 'stwc-widget-section stwc-cards-section py-16';
        ?>
        <section class="<?php echo esc_attr( $wrapper_cls ); ?>">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php if ( $show_header && 'quick-actions-overlap' !== $variant && ( ! empty( $s['badge'] ) || ! empty( $s['heading'] ) || ! empty( $s['subheading'] ) ) ) : ?>
                    <div class="stwc-section-header max-w-3xl mx-auto mb-12 text-center">
                        <?php if ( ! empty( $s['badge'] ) ) : ?>
                            <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['heading'] ) ) : ?>
                            <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold <?php echo $is_dark_bg ? 'text-white' : 'text-dark'; ?> mb-4">
                                <?php echo esc_html( $s['heading'] ); ?>
                            </h2>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['subheading'] ) ) : ?>
                            <p class="stwc-cards-subheading <?php echo $is_dark_bg ? 'text-white/80' : 'text-dark/70'; ?> text-base sm:text-lg leading-relaxed">
                                <?php echo esc_html( $s['subheading'] ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( 'link-pills' === $variant ) : ?>
                    <div class="flex flex-wrap justify-center gap-2.5">
                        <?php foreach ( $items as $item ) : ?>
                            <?php
                            $href   = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#';
                            $bstyle = $item['button_style'] ?? 'default';
                            if ( 'primary' === $bstyle || 'yes' === ( $item['is_featured'] ?? 'no' ) ) {
                                $pcls = 'bg-primary text-white hover:bg-accent';
                            } elseif ( 'accent' === $bstyle ) {
                                $pcls = 'bg-accent/10 text-accent hover:bg-accent hover:text-white';
                            } else {
                                $pcls = 'bg-slate-100 text-dark hover:bg-primary hover:text-white';
                            }
                            ?>
                            <a href="<?php echo esc_url( $href ); ?>" class="px-4 py-2 text-xs font-bold rounded-full transition-colors <?php echo esc_attr( $pcls ); ?>">
                                <?php echo esc_html( $item['title'] ?? '' ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="stwc-feature-grid grid gap-6">
                        <?php foreach ( $items as $idx => $item ) : ?>
                            <?php
                            $title      = $item['title'] ?? '';
                            $sub        = $item['subtitle'] ?? '';
                            $desc       = $item['description'] ?? '';
                            $icon       = $item['icon_name'] ?? 'none';
                            $href       = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '';
                            $ext        = ! empty( $item['link']['is_external'] );
                            $link_label = $item['link_label'] ?? '';
                            $img_url    = ! empty( $item['image']['url'] ) ? $item['image']['url'] : '';
                            $feat       = 'yes' === ( $item['is_featured'] ?? 'no' );
                            $ext_attr   = $ext ? 'target="_blank" rel="noopener noreferrer"' : '';

                            if ( 'quick-actions-overlap' === $variant ) :
                                ?>
                                <a href="<?php echo esc_url( $href ?: '#' ); ?>" class="flex flex-col h-full bg-white rounded-[1px] p-4 sm:p-5 lg:p-6 shadow-xl shadow-black/5 border border-gray-100/90 hover:-translate-y-1.5 transition-all duration-300 group hover:shadow-2xl hover:border-accent/30 min-w-0">
                                    <?php if ( 'none' !== $icon ) : ?>
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-secondary rounded-full flex items-center justify-center mb-3 sm:mb-4 group-hover:bg-white group-hover:shadow-md transition-all duration-300 shrink-0 text-white group-hover:text-secondary">
                                            <?php echo $this->render_icon( $icon, 'w-5 h-5 sm:w-6 sm:h-6' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="font-bold text-dark text-sm sm:text-base lg:text-lg mb-1 leading-snug group-hover:text-accent transition-colors"><?php echo esc_html( $title ); ?></h3>
                                    <?php if ( $desc ) : ?>
                                        <p class="text-xs sm:text-sm text-gray-500 line-clamp-2 leading-relaxed mb-3 flex-1"><?php echo esc_html( $desc ); ?></p>
                                    <?php endif; ?>
                                    <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-bold text-primary group-hover:text-accent transition-all duration-200 mt-auto">
                                        <?php echo esc_html( $link_label ?: 'Explore →' ); ?>
                                    </span>
                                </a>

                            <?php elseif ( 'icon-center-white' === $variant ) : ?>
                                <div class="bg-white rounded-[1px] p-6 text-center shadow-sm hover:shadow-md transition-all min-w-0">
                                    <?php if ( 'none' !== $icon ) : ?>
                                        <div class="w-10 h-10 mx-auto text-primary mb-4 flex items-center justify-center">
                                            <?php echo $this->render_icon( $icon, 'w-10 h-10' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="font-bold text-primary text-base sm:text-lg"><?php echo esc_html( $title ); ?></h3>
                                    <?php if ( $desc ) : ?>
                                        <p class="text-sm text-dark/70 mt-2 leading-relaxed"><?php echo esc_html( $desc ); ?></p>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ( 'icon-dark-glass' === $variant ) : ?>
                                <div class="flex flex-col items-center text-center p-6 bg-white/5 border border-white/10 rounded-[1px] backdrop-blur-sm min-w-0">
                                    <?php if ( 'none' !== $icon ) : ?>
                                        <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center mb-4 text-accent">
                                            <?php echo $this->render_icon( $icon, 'w-8 h-8' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="font-bold text-lg text-white"><?php echo esc_html( $title ); ?></h3>
                                    <?php if ( $desc ) : ?>
                                        <p class="text-sm text-white/75 mt-2"><?php echo esc_html( $desc ); ?></p>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ( 'icon-top-light' === $variant ) : ?>
                                <div class="bg-white rounded-[1px] p-8 border border-dark/10 hover:shadow-xl transition-all flex flex-col min-w-0">
                                    <?php if ( 'none' !== $icon ) : ?>
                                        <div class="w-14 h-14 bg-secondary rounded-xl flex items-center justify-center mb-6 shadow-sm text-white">
                                            <?php echo $this->render_icon( $icon, 'w-7 h-7' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="text-xl font-bold text-dark mb-3"><?php echo esc_html( $title ); ?></h3>
                                    <?php if ( $desc ) : ?>
                                        <p class="text-dark/70 leading-relaxed flex-1"><?php echo esc_html( $desc ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( $href && $link_label ) : ?>
                                        <?php
                                        $bstyle = $item['button_style'] ?? 'primary';
                                        $bcls   = array(
                                            'accent'  => 'bg-accent text-white hover:bg-accent/90',
                                            'primary' => 'bg-primary text-white hover:bg-secondary',
                                            'dark'    => 'bg-dark text-white hover:bg-black',
                                            'default' => 'bg-primary text-white hover:bg-accent',
                                        );
                                        ?>
                                        <div class="mt-5">
                                            <a href="<?php echo esc_url( $href ); ?>" <?php echo $ext_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="inline-block <?php echo esc_attr( $bcls[ $bstyle ] ?? $bcls['primary'] ); ?> font-bold px-6 py-2.5 rounded-full text-sm transition-colors">
                                                <?php echo esc_html( $link_label ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ( 'process-steps' === $variant ) : ?>
                                <?php
                                $s_style  = $s['step_style'] ?? 'card';
                                $step_num = ! empty( $item['step_number'] ) ? $item['step_number'] : (string) ( $idx + 1 );
                                if ( 'centered-square' === $s_style ) :
                                    ?>
                                    <div class="flex flex-col items-center text-center min-w-0">
                                        <div class="w-16 h-16 bg-slate-50 rounded-xl shadow-md flex items-center justify-center text-xl font-bold text-dark"><?php echo esc_html( $step_num ); ?></div>
                                        <h4 class="mt-4 font-bold text-dark text-base"><?php echo esc_html( $title ); ?></h4>
                                        <?php if ( $desc ) : ?>
                                            <p class="text-xs text-dark/70 mt-2 leading-relaxed"><?php echo esc_html( $desc ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ( 'minimal-num' === $s_style ) : ?>
                                    <div class="bg-slate-50 p-6 rounded-[1px] border border-dark/10 min-w-0">
                                        <span class="text-accent font-extrabold text-xl block"><?php echo esc_html( $step_num ); ?></span>
                                        <h4 class="font-bold text-dark mt-2 mb-1 text-base"><?php echo esc_html( $title ); ?></h4>
                                        <?php if ( $desc ) : ?>
                                            <p class="text-xs text-dark/70 leading-relaxed"><?php echo esc_html( $desc ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="bg-slate-50 p-7 rounded-[1px] border border-dark/10 shadow-sm min-w-0">
                                        <div class="w-12 h-12 rounded-xl <?php echo $feat ? 'bg-primary' : 'bg-accent'; ?> text-white font-extrabold flex items-center justify-center mb-4 text-lg"><?php echo esc_html( $step_num ); ?></div>
                                        <h3 class="text-lg font-bold text-dark mb-2"><?php echo esc_html( $title ); ?></h3>
                                        <?php if ( $desc ) : ?>
                                            <p class="text-sm text-dark/70 leading-relaxed"><?php echo esc_html( $desc ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            <?php elseif ( 'photo-cards' === $variant ) : ?>
                                <article class="bg-white border border-gray-200 rounded-[1px] overflow-hidden shadow-sm flex flex-col min-w-0">
                                    <div class="aspect-[16/10] w-full overflow-hidden bg-slate-100">
                                        <?php if ( $img_url ) : ?>
                                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="w-full h-full object-cover" loading="lazy" />
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-5 flex-1">
                                        <h3 class="font-bold text-dark text-lg mb-2"><?php echo esc_html( $title ); ?></h3>
                                        <?php if ( $desc ) : ?>
                                            <p class="text-xs text-slate-600 leading-relaxed"><?php echo esc_html( $desc ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </article>

                            <?php elseif ( 'photo-overlay' === $variant ) : ?>
                                <div class="relative rounded-[1px] overflow-hidden h-64 min-w-0">
                                    <?php if ( $img_url ) : ?>
                                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="w-full h-full object-cover" loading="lazy" />
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent p-6 sm:p-8 flex flex-col justify-end">
                                        <?php if ( 'none' !== $icon ) : ?>
                                            <div class="text-accent mb-3"><?php echo $this->render_icon( $icon, 'w-8 h-8' ); ?></div>
                                        <?php endif; ?>
                                        <h3 class="text-2xl font-bold text-white"><?php echo esc_html( $title ); ?></h3>
                                    </div>
                                </div>

                            <?php elseif ( 'pricing-packages' === $variant ) : ?>
                                <?php
                                $lines = array_filter( array_map( 'trim', explode( "\n", (string) ( $item['features_list'] ?? '' ) ) ) );
                                ?>
                                <div class="bg-white p-8 rounded-[1px] <?php echo $feat ? 'border-2 border-primary shadow-md' : 'border border-dark/10 shadow-sm'; ?> flex flex-col min-w-0">
                                    <h3 class="text-xl font-bold text-dark mb-2"><?php echo esc_html( $title ); ?></h3>
                                    <?php if ( $sub ) : ?>
                                        <p class="text-xs text-accent font-bold uppercase mb-4"><?php echo esc_html( $sub ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $lines ) ) : ?>
                                        <ul class="space-y-2 text-sm text-dark/75 mb-6 flex-1">
                                            <?php foreach ( $lines as $line ) : ?>
                                                <li class="flex items-start gap-2">
                                                    <span class="text-primary font-bold shrink-0">&#10003;</span>
                                                    <span><?php echo esc_html( ltrim( $line, "✓•- \t" ) ); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <?php if ( $href || $link_label ) : ?>
                                        <a href="<?php echo esc_url( $href ?: '#' ); ?>" <?php echo $ext_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="inline-block font-bold <?php echo $feat ? 'text-accent' : 'text-primary hover:text-accent'; ?> text-sm mt-auto">
                                            <?php echo esc_html( $link_label ?: 'Book Service →' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ( 'photo-feed' === $variant ) : ?>
                                <a href="<?php echo esc_url( $href ?: '#' ); ?>" class="aspect-square rounded-[1px] overflow-hidden block shadow-sm group min-w-0">
                                    <?php if ( $img_url ) : ?>
                                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                                    <?php endif; ?>
                                </a>

                            <?php else : /* 'service-cards' */ ?>
                                <div class="bg-slate-50 border border-dark/10 p-7 rounded-[1px] flex flex-col min-w-0 <?php echo $feat ? 'text-center' : ''; ?>">
                                    <?php if ( 'none' !== $icon ) : ?>
                                        <div class="w-10 h-10 bg-secondary text-white rounded-xl flex items-center justify-center mb-4 <?php echo $feat ? 'mx-auto' : ''; ?>">
                                            <?php echo $this->render_icon( $icon, 'w-5 h-5' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $sub ) : ?>
                                        <div class="text-xs font-bold uppercase tracking-wider text-accent mb-2"><?php echo esc_html( $sub ); ?></div>
                                    <?php endif; ?>
                                    <h3 class="text-xl font-bold text-dark mb-2">
                                        <?php if ( $href && ! $link_label ) : ?>
                                            <a href="<?php echo esc_url( $href ); ?>" <?php echo $ext_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="hover:text-accent transition-colors"><?php echo esc_html( $title ); ?></a>
                                        <?php else : ?>
                                            <?php echo esc_html( $title ); ?>
                                        <?php endif; ?>
                                    </h3>
                                    <?php if ( $desc ) : ?>
                                        <p class="text-sm text-dark/70 leading-relaxed <?php echo ( $item['footer_tag'] || $link_label ) ? 'mb-4' : ''; ?> flex-1">
                                            <?php echo esc_html( $desc ); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $item['footer_tag'] ) ) : ?>
                                        <span class="text-xs font-bold text-accent uppercase mt-auto"><?php echo esc_html( $item['footer_tag'] ); ?></span>
                                    <?php endif; ?>
                                    <?php if ( $href && $link_label ) : ?>
                                        <?php
                                        $bstyle = $item['button_style'] ?? 'accent';
                                        $bcls   = array(
                                            'accent'  => 'bg-accent text-white hover:bg-accent/90',
                                            'primary' => 'bg-primary text-white hover:bg-secondary',
                                            'dark'    => 'bg-dark text-white hover:bg-black',
                                            'default' => 'bg-primary text-white hover:bg-accent',
                                        );
                                        ?>
                                        <div class="mt-4">
                                            <a href="<?php echo esc_url( $href ); ?>" <?php echo $ext_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="inline-block <?php echo esc_attr( $bcls[ $bstyle ] ?? $bcls['accent'] ); ?> font-bold px-6 py-2.5 rounded-full text-sm transition-colors">
                                                <?php echo esc_html( $link_label ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( 'yes' === ( $s['show_callout'] ?? 'no' ) && ! empty( $s['callout_title'] ) ) : ?>
                    <div class="bg-primary text-white rounded-[1px] p-8 mt-12 flex flex-col lg:flex-row items-center justify-between gap-6">
                        <div class="min-w-0">
                            <h3 class="text-2xl font-bold text-white mb-2"><?php echo esc_html( $s['callout_title'] ); ?></h3>
                            <?php if ( ! empty( $s['callout_text'] ) ) : ?>
                                <p class="text-white/80 text-sm max-w-2xl"><?php echo esc_html( $s['callout_text'] ); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ( ! empty( $s['callout_links'] ) ) : ?>
                            <div class="flex flex-wrap gap-3 shrink-0">
                                <?php foreach ( $s['callout_links'] as $cl ) : ?>
                                    <?php
                                    $c_url = ! empty( $cl['link']['url'] ) ? $cl['link']['url'] : '#';
                                    $c_cls = 'yes' === ( $cl['is_accent'] ?? 'no' )
                                        ? 'bg-accent hover:bg-accent/90 text-white px-5 py-2.5'
                                        : 'bg-white/10 hover:bg-white/20 text-white px-4 py-2.5';
                                    ?>
                                    <a href="<?php echo esc_url( $c_url ); ?>" class="<?php echo esc_attr( $c_cls ); ?> rounded-full text-xs font-bold transition-colors">
                                        <?php echo esc_html( $cl['label'] ?? '' ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
