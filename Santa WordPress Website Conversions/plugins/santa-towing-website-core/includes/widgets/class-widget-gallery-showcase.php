<?php
/**
 * Elementor Free Widget: Gallery Masonry Showcase & Lightbox
 *
 * Replicates React `src/pages/Gallery.tsx`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Gallery_Showcase extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-gallery-showcase';
    }

    public function get_title() {
        return __( 'Santa Gallery Showcase', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-gallery-masonry';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_gallery_content',
            array(
                'label' => __( 'Gallery Settings & Source', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Badge', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Our Fleet & Workshop In Action',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Santa Towing Gallery',
            )
        );

        $this->add_control(
            'source_mode',
            array(
                'label'   => __( 'Image Source', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'cpt',
                'options' => array(
                    'cpt'    => __( 'WordPress Gallery CPT (with Defaults)', 'santa-towing-website-core' ),
                    'custom' => __( 'Custom Elementor Gallery Picker', 'santa-towing-website-core' ),
                ),
            )
        );

        $this->add_control(
            'custom_gallery',
            array(
                'label'     => __( 'Select Custom Images', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::GALLERY,
                'condition' => array( 'source_mode' => 'custom' ),
            )
        );

        $this->add_control(
            'show_filters',
            array(
                'label'        => __( 'Show Category Filter Pills', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
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
                'options'        => array( '2' => '2', '3' => '3', '4' => '4' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-gallery-masonry' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->add_responsive_control(
            'row_height',
            array(
                'label'      => __( 'Row Height (px)', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 160, 'max' => 360 ) ),
                'default'    => array( 'size' => 230, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-gallery-masonry' => 'grid-auto-rows: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-gallery-section', '#ffffff', '#131514' );

        $this->start_controls_section(
            'section_gallery_item_style',
            array(
                'label' => __( 'Gallery Tile Style', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'gallery_gap',
            array(
                'label'      => __( 'Tile Gap', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 4, 'max' => 36 ) ),
                'default'    => array( 'size' => 16, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-gallery-masonry' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'tile_radius',
            array(
                'label'      => __( 'Tile Border Radius', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
                'default'    => array( 'size' => 1, 'unit' => 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-gallery-masonry .stwc-gallery-card' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $mode = $s['source_mode'] ?? 'cpt';

        if ( 'custom' === $mode && ! empty( $s['custom_gallery'] ) ) {
            $items = array();
            foreach ( $s['custom_gallery'] as $img ) {
                $items[] = array(
                    'title'    => get_the_title( $img['id'] ?? 0 ) ?: 'Santa Towing Operations',
                    'category' => 'Operations',
                    'url'      => $img['url'],
                    'span'     => 'col-span-1 row-span-1',
                    'location' => 'Ghana',
                );
            }
        } else {
            $items = stwc_get_gallery_items();
        }

        $cats = array( 'All' );
        foreach ( $items as $it ) {
            if ( ! empty( $it['category'] ) && ! in_array( $it['category'], $cats, true ) ) {
                $cats[] = $it['category'];
            }
        }
        ?>
        <section class="stwc-widget-section stwc-gallery-section py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-stwc-filter-scope="gallery">
                <div class="stwc-section-header max-w-3xl mx-auto mb-10 text-center">
                    <?php if ( ! empty( $s['badge'] ) ) : ?>
                        <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                    <?php endif; ?>
                    <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-dark mb-3"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
                </div>

                <?php if ( 'yes' === ( $s['show_filters'] ?? 'yes' ) && count( $cats ) > 2 ) : ?>
                    <div class="flex flex-wrap justify-center gap-2 mb-10">
                        <?php foreach ( $cats as $idx => $cat ) : ?>
                            <button type="button" class="stwc-filter-btn px-4 py-2 rounded-full text-xs sm:text-sm font-bold transition-colors <?php echo 0 === $idx ? 'bg-primary text-white is-active' : 'bg-slate-100 text-dark hover:bg-primary hover:text-white'; ?>" data-filter-value="<?php echo esc_attr( $cat ); ?>">
                                <?php echo esc_html( $cat ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="stwc-gallery-masonry grid gap-4">
                    <?php foreach ( $items as $item ) : ?>
                        <div class="stwc-filter-item relative rounded-[1px] overflow-hidden group cursor-pointer <?php echo esc_attr( $item['span'] ?? 'col-span-1 row-span-1' ); ?>" data-category="<?php echo esc_attr( $item['category'] ?? 'Workshop' ); ?>" data-stwc-lightbox="<?php echo esc_url( $item['url'] ); ?>" data-stwc-caption="<?php echo esc_attr( $item['title'] ); ?>">
                            <img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-5 flex flex-col justify-end text-white">
                                <span class="text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( ( $item['category'] ?? '' ) . ( ! empty( $item['location'] ) ? ' • ' . $item['location'] : '' ) ); ?></span>
                                <h4 class="font-bold text-lg"><?php echo esc_html( $item['title'] ); ?></h4>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
