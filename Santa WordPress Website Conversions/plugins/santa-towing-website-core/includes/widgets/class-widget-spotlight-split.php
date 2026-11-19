<?php
/**
 * Elementor Free Widget: Spotlight Split, Diagnostics/Portal Showcase, Body Works Tabs & Comparison Slider
 *
 * Strictly Elementor Free APIs only. Zero inline <script> tags (delegated cleanly via santa-core.js).
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Spotlight_Split extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-spotlight-split';
    }

    public function get_title() {
        return __( 'Santa Spotlight & Before/After Showcase', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-image-before-after';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_spotlight_mode',
            array(
                'label' => __( 'Showcase Mode & Copy', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'layout_mode',
            array(
                'label'   => __( 'Showcase Layout Mode', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'checklist-feature',
                'options' => array(
                    'primary-banner-split' => __( 'Primary Towing Banner Split', 'santa-towing-website-core' ),
                    'checklist-feature'    => __( 'Split Feature (Checklist / Numbered Points + Photo)', 'santa-towing-website-core' ),
                    'before-after-tabs'    => __( 'Before & After Tabbed Showcase', 'santa-towing-website-core' ),
                    'before-after-slider'  => __( 'Before & After Interactive Range Slider', 'santa-towing-website-core' ),
                ),
            )
        );

        $this->add_control(
            'theme_tone',
            array(
                'label'     => __( 'Visual Tone', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'light',
                'options'   => array(
                    'light'        => __( 'Light Background / Dark Text', 'santa-towing-website-core' ),
                    'primary'      => __( 'Primary Teal Background / White Text', 'santa-towing-website-core' ),
                    'primary-card' => __( 'White Section with Primary Boxed Card', 'santa-towing-website-core' ),
                ),
                'condition' => array( 'layout_mode' => 'checklist-feature' ),
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Eyebrow', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Rapid Emergency Towing',
            )
        );

        $this->add_control(
            'description',
            array(
                'label'   => __( 'Description', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
            )
        );

        $this->add_control(
            'cta_label',
            array(
                'label'     => __( 'CTA Button Label', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Request a Tow Now',
                'condition' => array( 'layout_mode' => 'primary-banner-split' ),
            )
        );

        $this->add_control(
            'cta_link',
            array(
                'label'     => __( 'CTA Button Link', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::URL,
                'default'   => array( 'url' => home_url( '/towing/' ) ),
                'condition' => array( 'layout_mode' => 'primary-banner-split' ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_checklist_media',
            array(
                'label'     => __( 'Feature Points, Photo & Optional Overlay', 'santa-towing-website-core' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => array( 'layout_mode!' => array( 'before-after-tabs', 'before-after-slider' ) ),
            )
        );

        $this->add_control(
            'feature_image',
            array(
                'label'   => __( 'Showcase Photo', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-9-car-battery-maintenance.jpg' ) ),
            )
        );

        $this->add_control(
            'feature_image_alt',
            array(
                'label'   => __( 'Image Alt Text', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Santa Towing Showcase',
            )
        );

        $pt_repeater = new \Elementor\Repeater();
        $pt_repeater->add_control(
            'step_num',
            array(
                'label'   => __( 'Number Badge (Optional, e.g. 01 — leave empty for Checkmark)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            )
        );
        $pt_repeater->add_control(
            'title',
            array(
                'label'   => __( 'Point Title / Bullet Text', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Comprehensive Transit Insurance',
            )
        );
        $pt_repeater->add_control(
            'desc',
            array(
                'label'   => __( 'Point Description (Optional)', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
            )
        );
        $pt_repeater->add_control(
            'is_accent',
            array(
                'label'        => __( 'Accent Number Badge Color', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'points',
            array(
                'label'       => __( 'Checklist / Feature Points', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $pt_repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'condition'   => array( 'layout_mode' => 'checklist-feature' ),
                'default'     => array(),
            )
        );

        $this->add_control(
            'overlay_type',
            array(
                'label'     => __( 'Image Overlay Badge Type', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'none',
                'options'   => array(
                    'none'            => 'None',
                    'fleet-health'    => __( 'Fleet Health Progress Card (98% Good)', 'santa-towing-website-core' ),
                    'dispatch-callout'=> __( 'Floating 24/7 Dispatch Card + Phone', 'santa-towing-website-core' ),
                ),
                'condition' => array( 'layout_mode' => 'checklist-feature' ),
            )
        );

        $this->add_control(
            'overlay_title',
            array(
                'label'     => __( 'Overlay Title / Metric', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => '24/7 Dispatch',
                'condition' => array( 'layout_mode' => 'checklist-feature', 'overlay_type!' => 'none' ),
            )
        );

        $this->add_control(
            'overlay_text',
            array(
                'label'     => __( 'Overlay Description / Metric Value', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => '98% Good',
                'condition' => array( 'layout_mode' => 'checklist-feature', 'overlay_type!' => 'none' ),
            )
        );

        $this->add_control(
            'overlay_cta_label',
            array(
                'label'     => __( 'Overlay CTA Label (Optional)', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => '024 475 3849',
                'condition' => array( 'layout_mode' => 'checklist-feature', 'overlay_type' => 'dispatch-callout' ),
            )
        );

        $this->add_control(
            'overlay_cta_link',
            array(
                'label'     => __( 'Overlay CTA Link', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::URL,
                'default'   => array( 'url' => 'tel:0244753849' ),
                'condition' => array( 'layout_mode' => 'checklist-feature', 'overlay_type' => 'dispatch-callout' ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_before_after_tabs',
            array(
                'label'     => __( 'Before & After Comparison Sets', 'santa-towing-website-core' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => array( 'layout_mode' => 'before-after-tabs' ),
            )
        );

        $tab_repeater = new \Elementor\Repeater();
        $tab_repeater->add_control(
            'tab_label',
            array(
                'label'   => __( 'Tab Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Collision Restoration',
            )
        );
        $tab_repeater->add_control(
            'before_image',
            array(
                'label'   => __( 'Before Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-14-suspension-parts-maintenance.jpg' ) ),
            )
        );
        $tab_repeater->add_control(
            'before_caption',
            array(
                'label'   => __( 'Before Caption', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Front-end impact damage requiring panel alignment, support bracket straightening, and primer prep.',
            )
        );
        $tab_repeater->add_control(
            'after_image',
            array(
                'label'   => __( 'After Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ) ),
            )
        );
        $tab_repeater->add_control(
            'after_caption',
            array(
                'label'   => __( 'After Caption', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Restored chassis alignment, OEM paint color code match, and high-gloss 2K clear coat finish.',
            )
        );

        $this->add_control(
            'tab_sets',
            array(
                'label'       => __( 'Transformation Sets', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $tab_repeater->get_controls(),
                'title_field' => '{{{ tab_label }}}',
                'default'     => array(),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_before_after_slider',
            array(
                'label'     => __( 'Comparison Slider Images', 'santa-towing-website-core' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => array( 'layout_mode' => 'before-after-slider' ),
            )
        );

        $this->add_control(
            'slider_before_image',
            array(
                'label'   => __( 'Before Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-14-suspension-parts-maintenance.jpg' ) ),
            )
        );

        $this->add_control(
            'slider_after_image',
            array(
                'label'   => __( 'After Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ) ),
            )
        );

        $this->add_control(
            'slider_before_label',
            array(
                'label'   => __( 'Before Badge Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'BEFORE',
            )
        );

        $this->add_control(
            'slider_after_label',
            array(
                'label'   => __( 'After Badge Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'AFTER',
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-spotlight-section', '#ffffff', '#131514' );
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $mode = $s['layout_mode'] ?? 'checklist-feature';

        if ( 'primary-banner-split' === $mode ) :
            $img_url  = ! empty( $s['feature_image']['url'] ) ? $s['feature_image']['url'] : stwc_asset_image( 'image-9-car-battery-maintenance.jpg' );
            $cta_url  = ! empty( $s['cta_link']['url'] ) ? $s['cta_link']['url'] : home_url( '/towing/' );
            ?>
            <section class="stwc-widget-section stwc-spotlight-section py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-primary rounded-[1px] overflow-hidden flex flex-col md:flex-row relative">
                        <div class="md:w-1/2 p-8 md:p-16 flex flex-col justify-center relative z-10 text-white min-w-0">
                            <?php if ( ! empty( $s['heading'] ) ) : ?>
                                <h2 class="stwc-section-heading text-3xl md:text-4xl font-bold mb-4 text-white"><?php echo esc_html( $s['heading'] ); ?></h2>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['description'] ) ) : ?>
                                <p class="text-white/80 mb-8 text-lg leading-relaxed"><?php echo esc_html( $s['description'] ); ?></p>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['cta_label'] ) ) : ?>
                                <a href="<?php echo esc_url( $cta_url ); ?>" class="bg-accent hover:bg-accent/90 text-white px-8 py-4 font-bold shadow-lg self-start rounded-full transition-colors">
                                    <?php echo esc_html( $s['cta_label'] ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="md:w-1/2 aspect-video md:aspect-auto relative overflow-hidden">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $s['feature_image_alt'] ?? 'Emergency Towing' ); ?>" class="w-full h-full object-cover" loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-r from-primary to-transparent md:w-1/2 pointer-events-none"></div>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ( 'before-after-tabs' === $mode ) : ?>
            <?php
            $sets = $s['tab_sets'] ?? array();
            if ( empty( $sets ) ) {
                return;
            }
            $first = $sets[0];
            $b_img = ! empty( $first['before_image']['url'] ) ? $first['before_image']['url'] : stwc_asset_image( 'image-14-suspension-parts-maintenance.jpg' );
            $a_img = ! empty( $first['after_image']['url'] ) ? $first['after_image']['url'] : stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' );
            ?>
            <section class="stwc-widget-section stwc-spotlight-section py-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-slate-50 border border-dark/10 p-6 sm:p-10 rounded-[1px]" data-stwc-bw-showcase="1">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                            <div class="stwc-section-header text-left min-w-0">
                                <?php if ( ! empty( $s['badge'] ) ) : ?>
                                    <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-1"><?php echo esc_html( $s['badge'] ); ?></span>
                                <?php endif; ?>
                                <?php if ( ! empty( $s['heading'] ) ) : ?>
                                    <h3 class="stwc-section-heading text-2xl md:text-3xl font-extrabold text-dark"><?php echo esc_html( $s['heading'] ); ?></h3>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-wrap gap-2" role="tablist">
                                <?php foreach ( $sets as $idx => $set ) : ?>
                                    <?php
                                    $sb = ! empty( $set['before_image']['url'] ) ? $set['before_image']['url'] : '';
                                    $sa = ! empty( $set['after_image']['url'] ) ? $set['after_image']['url'] : '';
                                    ?>
                                    <button type="button"
                                            role="tab"
                                            aria-selected="<?php echo 0 === $idx ? 'true' : 'false'; ?>"
                                            data-stwc-bw-tab="<?php echo (int) $idx; ?>"
                                            data-before-img="<?php echo esc_url( $sb ); ?>"
                                            data-before-cap="<?php echo esc_attr( $set['before_caption'] ?? '' ); ?>"
                                            data-after-img="<?php echo esc_url( $sa ); ?>"
                                            data-after-cap="<?php echo esc_attr( $set['after_caption'] ?? '' ); ?>"
                                            class="px-4 py-2 rounded-full text-xs font-bold transition-colors <?php echo 0 === $idx ? 'bg-primary text-white is-active' : 'bg-white text-dark border border-dark/15'; ?>">
                                        <?php echo esc_html( $set['tab_label'] ?? '' ); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="rounded-[1px] overflow-hidden border border-dark/10 bg-white min-w-0">
                                <div class="relative h-64 overflow-hidden">
                                    <img src="<?php echo esc_url( $b_img ); ?>" alt="Before body work repair" class="stwc-bw-before-img w-full h-full object-cover" />
                                    <span class="absolute top-3 left-3 bg-black/80 text-white text-xs font-bold px-3 py-1 rounded-full">BEFORE</span>
                                </div>
                                <div class="stwc-bw-before-cap p-4 text-sm text-dark/75 leading-relaxed"><?php echo esc_html( $first['before_caption'] ?? '' ); ?></div>
                            </div>
                            <div class="rounded-[1px] overflow-hidden border border-dark/10 bg-white min-w-0">
                                <div class="relative h-64 overflow-hidden">
                                    <img src="<?php echo esc_url( $a_img ); ?>" alt="After factory finish paint" class="stwc-bw-after-img w-full h-full object-cover" />
                                    <span class="absolute top-3 left-3 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">AFTER (FACTORY FINISH)</span>
                                </div>
                                <div class="stwc-bw-after-cap p-4 text-sm text-dark/75 leading-relaxed"><?php echo esc_html( $first['after_caption'] ?? '' ); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <?php elseif ( 'before-after-slider' === $mode ) : ?>
            <?php
            $b_img = ! empty( $s['slider_before_image']['url'] ) ? $s['slider_before_image']['url'] : stwc_asset_image( 'image-14-suspension-parts-maintenance.jpg' );
            $a_img = ! empty( $s['slider_after_image']['url'] ) ? $s['slider_after_image']['url'] : stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' );
            ?>
            <section class="stwc-widget-section stwc-spotlight-section py-12">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="stwc-section-header text-center mb-10">
                        <?php if ( ! empty( $s['badge'] ) ) : ?>
                            <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['heading'] ) ) : ?>
                            <h2 class="stwc-section-heading text-3xl font-extrabold text-dark mb-3"><?php echo esc_html( $s['heading'] ); ?></h2>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['description'] ) ) : ?>
                            <p class="text-dark/70 text-sm"><?php echo esc_html( $s['description'] ); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="relative h-80 sm:h-96 rounded-[1px] overflow-hidden border border-dark/15 shadow-lg select-none" data-stwc-before-after="1">
                        <img src="<?php echo esc_url( $a_img ); ?>" alt="After workshop paint restoration" class="absolute inset-0 w-full h-full object-cover" />
                        <div class="stwc-before-clip absolute inset-0 overflow-hidden" style="width:50%;">
                            <img src="<?php echo esc_url( $b_img ); ?>" alt="Before mechanical and panel repair" class="w-full h-full object-cover" style="max-width:none;" />
                        </div>
                        <div class="stwc-compare-divider" style="left:50%;" aria-hidden="true"></div>
                        <span class="absolute top-4 left-4 bg-black/80 text-white text-xs font-bold px-3 py-1 rounded-full z-10"><?php echo esc_html( $s['slider_before_label'] ?? 'BEFORE' ); ?></span>
                        <span class="absolute top-4 right-4 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full z-10"><?php echo esc_html( $s['slider_after_label'] ?? 'AFTER' ); ?></span>
                        <input type="range" min="5" max="95" value="50" aria-label="Compare before and after image" class="stwc-compare-range relative z-20 cursor-ew-resize" style="position:absolute;bottom:16px;left:16px;right:16px;width:calc(100% - 32px);" />
                    </div>
                </div>
            </section>

        <?php else : /* 'checklist-feature' */ ?>
            <?php
            $tone    = $s['theme_tone'] ?? 'light';
            $is_dark = in_array( $tone, array( 'primary', 'primary-card' ), true );
            $img_url = ! empty( $s['feature_image']['url'] ) ? $s['feature_image']['url'] : stwc_asset_image( 'welcome-towing-image.jpg' );
            $points  = $s['points'] ?? array();
            $ov_type = $s['overlay_type'] ?? 'none';

            $inner_box_cls = 'primary-card' === $tone
                ? 'bg-primary text-white rounded-[1px] p-8 md:p-12'
                : '';
            $grid_cls = 'primary-card' === $tone
                ? 'grid lg:grid-cols-12 gap-10 items-center'
                : 'grid lg:grid-cols-2 gap-12 items-center';
            $left_cls  = 'primary-card' === $tone ? 'lg:col-span-6 min-w-0' : 'min-w-0';
            $right_cls = 'primary-card' === $tone ? 'lg:col-span-6 min-w-0' : 'relative min-w-0';
            ?>
            <section class="stwc-widget-section stwc-spotlight-section py-16 <?php echo 'primary' === $tone ? 'bg-primary text-white' : ''; ?>">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="<?php echo esc_attr( $inner_box_cls ); ?>">
                        <div class="<?php echo esc_attr( $grid_cls ); ?>">
                            <div class="<?php echo esc_attr( $left_cls ); ?>">
                                <div class="stwc-section-header text-left">
                                    <?php if ( ! empty( $s['badge'] ) ) : ?>
                                        <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $s['heading'] ) ) : ?>
                                        <h2 class="stwc-section-heading text-2xl sm:text-3xl md:text-4xl font-extrabold <?php echo $is_dark ? 'text-white' : 'text-dark'; ?> mb-4">
                                            <?php echo esc_html( $s['heading'] ); ?>
                                        </h2>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $s['description'] ) ) : ?>
                                        <p class="<?php echo $is_dark ? 'text-white/80' : 'text-slate-600'; ?> text-base sm:text-lg leading-relaxed mb-8">
                                            <?php echo esc_html( $s['description'] ); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <?php if ( ! empty( $points ) ) : ?>
                                    <div class="space-y-4">
                                        <?php foreach ( $points as $pt ) : ?>
                                            <?php if ( ! empty( $pt['step_num'] ) || ! empty( $pt['desc'] ) ) : ?>
                                                <div class="flex items-start gap-4 p-4 rounded-[1px] bg-slate-50 border border-gray-100">
                                                    <div class="w-10 h-10 rounded-xl <?php echo 'yes' === ( $pt['is_accent'] ?? 'yes' ) ? 'bg-accent' : 'bg-primary'; ?> text-white flex items-center justify-center shrink-0 font-bold">
                                                        <?php echo esc_html( $pt['step_num'] ?: '✓' ); ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h4 class="font-bold text-dark text-base"><?php echo esc_html( $pt['title'] ?? '' ); ?></h4>
                                                        <?php if ( ! empty( $pt['desc'] ) ) : ?>
                                                            <p class="text-slate-600 text-sm mt-1 leading-relaxed"><?php echo esc_html( $pt['desc'] ); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else : ?>
                                                <div class="flex items-center gap-3 <?php echo $is_dark ? 'text-white/90' : 'text-dark/80'; ?>">
                                                    <span class="text-accent shrink-0"><?php echo $this->render_icon( 'check-circle', 'w-5 h-5' ); ?></span>
                                                    <span class="text-sm sm:text-base"><?php echo esc_html( $pt['title'] ?? '' ); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="<?php echo esc_attr( $right_cls ); ?>">
                                <?php if ( 'fleet-health' === $ov_type ) : ?>
                                    <div class="bg-white/10 border border-white/20 rounded-[1px] p-4">
                                        <div class="bg-white rounded-[1px] overflow-hidden aspect-video relative">
                                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $s['feature_image_alt'] ?? 'Dashboard Preview' ); ?>" class="w-full h-full object-cover" loading="lazy" />
                                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent flex items-end p-6">
                                                <div class="bg-white/95 p-4 rounded-[1px] w-full max-w-sm shadow-md">
                                                    <div class="flex justify-between items-center mb-3 gap-2">
                                                        <span class="font-bold text-dark"><?php echo esc_html( $s['overlay_title'] ?? 'Fleet Health' ); ?></span>
                                                        <span class="text-accent font-bold text-sm"><?php echo esc_html( $s['overlay_text'] ?? '98% Good' ); ?></span>
                                                    </div>
                                                    <div class="w-full bg-slate-200 h-2 rounded-[1px]">
                                                        <div class="bg-primary h-2 rounded-[1px]" style="width:98%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ( 'dispatch-callout' === $ov_type ) : ?>
                                    <div class="relative">
                                        <div class="aspect-[4/3] rounded-[1px] overflow-hidden shadow-2xl border border-gray-200">
                                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $s['feature_image_alt'] ?? '' ); ?>" class="w-full h-full object-cover" loading="lazy" />
                                        </div>
                                        <div class="mt-4 sm:mt-0 sm:absolute sm:bottom-6 sm:-left-6 bg-dark text-white p-6 rounded-[1px] shadow-xl border border-white/10 max-w-xs">
                                            <div class="text-accent font-extrabold text-2xl mb-1"><?php echo esc_html( $s['overlay_title'] ?? '24/7 Dispatch' ); ?></div>
                                            <p class="text-xs text-white/80 leading-relaxed"><?php echo esc_html( $s['overlay_text'] ?? '' ); ?></p>
                                            <?php if ( ! empty( $s['overlay_cta_label'] ) ) : ?>
                                                <?php $o_url = ! empty( $s['overlay_cta_link']['url'] ) ? $s['overlay_cta_link']['url'] : 'tel:0244753849'; ?>
                                                <a href="<?php echo esc_url( $o_url ); ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-white bg-accent px-3 py-1.5 rounded-full">
                                                    <?php echo esc_html( $s['overlay_cta_label'] ); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="aspect-[4/3] rounded-[1px] overflow-hidden shadow-xl">
                                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $s['feature_image_alt'] ?? '' ); ?>" class="w-full h-full object-cover" loading="lazy" />
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php
        endif;
    }
}
