<?php
/**
 * Elementor Free Widget: Page Hero Banner (Breadcrumbs, Badge, CTAs, Optional Stats / Feature Card / FAQ Search)
 *
 * Strictly Elementor Free APIs only. Replaces inline HTML heroes cleanly across inner pages.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Page_Hero extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-page-hero';
    }

    public function get_title() {
        return __( 'Santa Page Hero Banner', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-banner';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_hero_content',
            array(
                'label' => __( 'Hero Content & Navigation', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $bc_repeater = new \Elementor\Repeater();
        $bc_repeater->add_control(
            'label',
            array(
                'label'   => __( 'Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Home',
            )
        );
        $bc_repeater->add_control(
            'link',
            array(
                'label'   => __( 'Link (Leave blank for current page)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '' ),
            )
        );

        $this->add_control(
            'breadcrumbs',
            array(
                'label'       => __( 'Breadcrumbs', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $bc_repeater->get_controls(),
                'title_field' => '{{{ label }}}',
                'default'     => array(
                    array( 'label' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
                    array( 'label' => 'Our Services', 'link' => array( 'url' => '' ) ),
                ),
            )
        );

        $this->add_control(
            'badge_icon',
            array(
                'label'   => __( 'Badge / Hero Icon', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => array(
                    'none'         => __( 'None', 'santa-towing-website-core' ),
                    'building'     => 'Building / Corporate',
                    'shield-check' => 'Shield Check',
                    'truck'        => 'Truck / Towing',
                    'wrench'       => 'Wrench / Repairs',
                    'car'          => 'Car / Fleet',
                    'map-pin'      => 'Map Pin / Coverage',
                    'briefcase'    => 'Briefcase / Careers',
                    'camera'       => 'Camera / Gallery',
                    'phone'        => 'Phone / Dispatch',
                ),
            )
        );

        $this->add_control(
            'badge_icon_above',
            array(
                'label'        => __( 'Show Large Icon Above Title (Policy Style)', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Eyebrow Badge Text', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $this->add_control(
            'badge_style',
            array(
                'label'   => __( 'Badge Style', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'glass',
                'options' => array(
                    'glass'  => __( 'Translucent Glass (Accent Text)', 'santa-towing-website-core' ),
                    'accent' => __( 'Solid Accent Pill (White Text)', 'santa-towing-website-core' ),
                ),
            )
        );

        $this->add_control(
            'title',
            array(
                'label'       => __( 'Hero Heading (H1)', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => 'Complete Automotive Solutions',
                'description' => __( 'Optional: wrap highlighted words in <span style="color:#d80000;">...</span>.', 'santa-towing-website-core' ),
            )
        );

        $this->add_control(
            'subtitle',
            array(
                'label'   => __( 'Subtitle / Lead Paragraph', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
            )
        );

        $this->add_control(
            'layout_align',
            array(
                'label'   => __( 'Alignment', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left'   => array( 'title' => __( 'Left', 'santa-towing-website-core' ), 'icon' => 'eicon-text-align-left' ),
                    'center' => array( 'title' => __( 'Center', 'santa-towing-website-core' ), 'icon' => 'eicon-text-align-center' ),
                ),
                'default' => 'left',
            )
        );

        $cta_repeater = new \Elementor\Repeater();
        $cta_repeater->add_control(
            'label',
            array(
                'label'   => __( 'Button Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Call Dispatch: 0244753849',
            )
        );
        $cta_repeater->add_control(
            'link',
            array(
                'label'   => __( 'Button Link', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => 'tel:0244753849' ),
            )
        );
        $cta_repeater->add_control(
            'style',
            array(
                'label'   => __( 'Button Variant', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'accent',
                'options' => array(
                    'accent'  => 'Accent Red',
                    'white'   => 'Solid White',
                    'primary' => 'Primary Teal (Bordered)',
                    'glass'   => 'Translucent Glass',
                ),
            )
        );

        $this->add_control(
            'ctas',
            array(
                'label'       => __( 'CTA Buttons', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $cta_repeater->get_controls(),
                'title_field' => '{{{ label }}}',
                'default'     => array(),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_hero_extras',
            array(
                'label' => __( 'Optional Right Card, Stats Strip & FAQ Search', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_side_card',
            array(
                'label'        => __( 'Show Right Dispatch Feature Card', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'side_image',
            array(
                'label'     => __( 'Right Card Image', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => array( 'url' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ) ),
                'condition' => array( 'show_side_card' => 'yes' ),
            )
        );

        $this->add_control(
            'side_metric_label',
            array(
                'label'     => __( 'Metric Label', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Average Urban Arrival',
                'condition' => array( 'show_side_card' => 'yes' ),
            )
        );

        $this->add_control(
            'side_metric_value',
            array(
                'label'     => __( 'Metric Value', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => '30 - 45 Minutes',
                'condition' => array( 'show_side_card' => 'yes' ),
            )
        );

        $this->add_control(
            'side_cta_label',
            array(
                'label'     => __( 'Card Button Label', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'WhatsApp Pin',
                'condition' => array( 'show_side_card' => 'yes' ),
            )
        );

        $this->add_control(
            'side_cta_link',
            array(
                'label'     => __( 'Card Button Link', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::URL,
                'default'   => array( 'url' => 'https://wa.me/233244753849', 'is_external' => true ),
                'condition' => array( 'show_side_card' => 'yes' ),
            )
        );

        $stat_repeater = new \Elementor\Repeater();
        $stat_repeater->add_control(
            'value',
            array(
                'label'   => __( 'Stat Value', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '20 - 100+ Tons',
            )
        );
        $stat_repeater->add_control(
            'label',
            array(
                'label'   => __( 'Stat Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Lowbed Load Capacity',
            )
        );
        $stat_repeater->add_control(
            'is_accent',
            array(
                'label'        => __( 'Accent Value Color', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'stats',
            array(
                'label'       => __( 'Bottom Highlight Stats Strip (Optional)', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $stat_repeater->get_controls(),
                'title_field' => '{{{ value }}}',
                'default'     => array(),
            )
        );

        $this->add_control(
            'show_faq_search',
            array(
                'label'        => __( 'Show Live FAQ Search Input', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'search_placeholder',
            array(
                'label'     => __( 'Search Placeholder', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Search FAQs (e.g. flatbed, deposit, warranty, MoMo, Kumasi)...',
                'condition' => array( 'show_faq_search' => 'yes' ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_hero_background',
            array(
                'label' => __( 'Background & Visual Style', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'bg_image',
            array(
                'label'   => __( 'Background Image (Optional)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => '' ),
            )
        );

        $this->add_control(
            'bg_color',
            array(
                'label'     => __( 'Base Background Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#015557',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-page-hero' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'overlay_color',
            array(
                'label'     => __( 'Overlay Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(1, 85, 87, 0.86)',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-page-hero-overlay' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'heading_color',
            array(
                'label'     => __( 'Heading Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-page-hero h1' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'hero_title_typography',
                'label'    => __( 'Heading Typography', 'santa-towing-website-core' ),
                'selector' => '{{WRAPPER}} .stwc-page-hero h1',
            )
        );

        $this->add_responsive_control(
            'hero_padding',
            array(
                'label'      => __( 'Section Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem' ),
                'default'    => array(
                    'top'    => '88',
                    'right'  => '16',
                    'bottom' => '88',
                    'left'   => '16',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-page-hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s            = $this->get_settings_for_display();
        $breadcrumbs  = $s['breadcrumbs'] ?? array();
        $ctas         = $s['ctas'] ?? array();
        $stats        = $s['stats'] ?? array();
        $has_side     = 'yes' === ( $s['show_side_card'] ?? 'no' );
        $is_center    = 'center' === ( $s['layout_align'] ?? 'left' ) && ! $has_side;
        $bg_url       = ! empty( $s['bg_image']['url'] ) ? $s['bg_image']['url'] : '';
        $badge_icon   = $s['badge_icon'] ?? 'none';
        $allowed_h1   = array(
            'span'   => array( 'style' => true, 'class' => true ),
            'strong' => array( 'class' => true ),
            'br'     => array(),
        );
        ?>
        <section class="stwc-widget-section stwc-page-hero relative overflow-hidden bg-primary text-white">
            <?php if ( $bg_url ) : ?>
                <img src="<?php echo esc_url( $bg_url ); ?>" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover z-0" />
            <?php endif; ?>
            <div class="stwc-page-hero-overlay absolute inset-0 z-0" aria-hidden="true"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 <?php echo $is_center ? 'text-center' : ''; ?>">
                <?php if ( ! empty( $breadcrumbs ) ) : ?>
                    <nav class="text-sm text-white/75 mb-6 flex flex-wrap items-center gap-1.5 <?php echo $is_center ? 'justify-center' : ''; ?>" aria-label="Breadcrumb">
                        <?php
                        $total_bc = count( $breadcrumbs );
                        foreach ( $breadcrumbs as $idx => $bc ) :
                            $label = $bc['label'] ?? '';
                            $url   = ! empty( $bc['link']['url'] ) ? $bc['link']['url'] : '';
                            $last  = ( $idx === $total_bc - 1 );
                            if ( $idx > 0 ) :
                                ?>
                                <span aria-hidden="true">/</span>
                            <?php endif; ?>
                            <?php if ( $url && ! $last ) : ?>
                                <a href="<?php echo esc_url( $url ); ?>" class="hover:text-white transition-colors"><?php echo esc_html( $label ); ?></a>
                            <?php else : ?>
                                <span class="font-bold text-white"><?php echo esc_html( $label ); ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>

                <div class="<?php echo $has_side ? 'grid lg:grid-cols-12 gap-10 items-center' : ( $is_center ? 'max-w-4xl mx-auto' : 'max-w-3xl' ); ?>">
                    <div class="<?php echo $has_side ? 'lg:col-span-7 min-w-0' : 'min-w-0'; ?>">
                        <?php if ( 'yes' === ( $s['badge_icon_above'] ?? 'no' ) && 'none' !== $badge_icon ) : ?>
                            <div class="mb-4 inline-flex items-center justify-center text-accent <?php echo $is_center ? 'mx-auto' : ''; ?>">
                                <?php echo $this->render_icon( $badge_icon, 'w-16 h-16' ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['badge'] ) ) : ?>
                            <?php
                            $badge_cls = 'accent' === ( $s['badge_style'] ?? 'glass' )
                                ? 'bg-accent text-white'
                                : 'bg-white/10 border border-white/20 text-accent';
                            ?>
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 <?php echo esc_attr( $badge_cls ); ?> font-bold text-xs tracking-wider uppercase rounded-full mb-5">
                                <?php if ( 'none' !== $badge_icon && 'yes' !== ( $s['badge_icon_above'] ?? 'no' ) ) : ?>
                                    <?php echo $this->render_icon( $badge_icon, 'w-4 h-4 shrink-0' ); ?>
                                <?php endif; ?>
                                <span><?php echo esc_html( $s['badge'] ); ?></span>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['title'] ) ) : ?>
                            <h1 class="stwc-section-heading text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 text-white break-words">
                                <?php echo wp_kses( $s['title'], $allowed_h1 ); ?>
                            </h1>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                            <p class="text-lg text-white/85 mb-8 leading-relaxed <?php echo $is_center ? 'max-w-2xl mx-auto' : 'max-w-2xl'; ?>">
                                <?php echo esc_html( $s['subtitle'] ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( ! empty( $ctas ) ) : ?>
                            <div class="flex flex-wrap gap-4 <?php echo $is_center ? 'justify-center' : ''; ?> <?php echo ! empty( $stats ) ? 'mb-10' : ''; ?>">
                                <?php foreach ( $ctas as $btn ) : ?>
                                    <?php
                                    $b_url   = ! empty( $btn['link']['url'] ) ? $btn['link']['url'] : '#';
                                    $b_ext   = ! empty( $btn['link']['is_external'] );
                                    $b_style = $btn['style'] ?? 'accent';
                                    $cls_map = array(
                                        'accent'  => 'bg-accent hover:bg-accent/90 text-white shadow-lg',
                                        'white'   => 'bg-white text-dark hover:bg-slate-100 shadow-lg',
                                        'primary' => 'bg-primary hover:bg-primary/90 border border-white/25 text-white shadow-xl',
                                        'glass'   => 'bg-white/10 hover:bg-white/20 text-white border border-white/25',
                                    );
                                    $b_cls = $cls_map[ $b_style ] ?? $cls_map['accent'];
                                    ?>
                                    <a href="<?php echo esc_url( $b_url ); ?>"
                                       <?php echo $b_ext ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                                       class="<?php echo esc_attr( $b_cls ); ?> px-7 py-4 font-bold rounded-full inline-flex items-center gap-2 transition-colors">
                                        <?php echo esc_html( $btn['label'] ?? 'Learn More' ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( 'yes' === ( $s['show_faq_search'] ?? 'no' ) ) : ?>
                            <div class="stwc-faq-search-wrap max-w-xl mx-auto">
                                <input type="search"
                                       data-stwc-faq-search="1"
                                       placeholder="<?php echo esc_attr( $s['search_placeholder'] ?? 'Search FAQs...' ); ?>"
                                       class="w-full px-5 py-4 rounded-full text-dark bg-white shadow-xl border-0 focus:outline-none text-sm" />
                                <p class="stwc-faq-search-status text-xs text-white/75 mt-2 hidden"></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( $has_side ) : ?>
                        <?php
                        $side_img   = ! empty( $s['side_image']['url'] ) ? $s['side_image']['url'] : stwc_asset_image( 'image-1-towing-vehicle.jpg' );
                        $side_url   = ! empty( $s['side_cta_link']['url'] ) ? $s['side_cta_link']['url'] : stwc_build_whatsapp_url();
                        $side_ext   = ! empty( $s['side_cta_link']['is_external'] );
                        ?>
                        <div class="lg:col-span-5 min-w-0">
                            <div class="rounded-[1px] overflow-hidden border border-white/15 shadow-2xl relative bg-dark">
                                <div class="aspect-[4/3] w-full overflow-hidden">
                                    <img src="<?php echo esc_url( $side_img ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ?? 'Santa Towing' ) ); ?>" class="w-full h-full object-cover" />
                                </div>
                                <div class="bg-black/80 backdrop-blur-sm p-5 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <?php if ( ! empty( $s['side_metric_label'] ) ) : ?>
                                            <div class="text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( $s['side_metric_label'] ); ?></div>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $s['side_metric_value'] ) ) : ?>
                                            <div class="text-xl font-extrabold text-white"><?php echo esc_html( $s['side_metric_value'] ); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ( ! empty( $s['side_cta_label'] ) ) : ?>
                                        <a href="<?php echo esc_url( $side_url ); ?>"
                                           <?php echo $side_ext ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                                           class="bg-primary hover:bg-primary/90 text-white text-xs font-bold px-4 py-2.5 rounded-full transition-colors">
                                            <?php echo esc_html( $s['side_cta_label'] ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $stats ) ) : ?>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-6 mt-8 border-t border-white/15">
                        <?php foreach ( $stats as $st ) : ?>
                            <div class="bg-white/5 border border-white/10 p-4 rounded-[1px]">
                                <div class="text-2xl font-extrabold <?php echo 'yes' === ( $st['is_accent'] ?? 'yes' ) ? 'text-accent' : 'text-white'; ?>">
                                    <?php echo esc_html( $st['value'] ?? '' ); ?>
                                </div>
                                <div class="text-xs text-white/75 mt-1"><?php echo esc_html( $st['label'] ?? '' ); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
