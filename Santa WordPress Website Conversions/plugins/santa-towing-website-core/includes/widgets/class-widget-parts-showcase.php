<?php
/**
 * Elementor Free Widget: Genuine Auto Parts Catalog Showcase
 *
 * Replicates React `Parts.tsx` & `theme/template-parts/card-part.php`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Parts_Showcase extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-parts-showcase';
    }

    public function get_title() {
        return __( 'Santa Auto Parts Catalog', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-cogs';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            array(
                'label' => __( 'Auto Parts Settings', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Eyebrow', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '100% OEM & Certified Aftermarket',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Genuine Auto Parts & Accessories',
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label'   => __( 'Max Parts Shown', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 8,
                'min'     => 1,
                'max'     => 24,
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

        $this->add_control(
            'fallback_image',
            array(
                'label'   => __( 'Fallback Part Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-9-car-battery-maintenance.jpg' ) ),
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label'          => __( 'Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '4',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-parts-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-parts-section', '#ffffff', '#131514' );

        $this->start_controls_section(
            'section_part_card_style',
            array(
                'label' => __( 'Part Card & Grid Style', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg_color',
            array(
                'label'     => __( 'Card Background Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-parts-grid article' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .stwc-parts-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s        = $this->get_settings_for_display();
        $limit    = absint( $s['posts_per_page'] ?? 8 );
        $parts    = array_slice( stwc_get_parts( array( 'posts_per_page' => $limit ) ), 0, $limit );
        $fallback = ! empty( $s['fallback_image']['url'] ) ? $s['fallback_image']['url'] : stwc_asset_image( 'image-9-car-battery-maintenance.jpg' );
        $tel      = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
        $cats  = array( 'All' );
        foreach ( $parts as $p ) {
            if ( ! empty( $p['category'] ) && ! in_array( $p['category'], $cats, true ) ) {
                $cats[] = $p['category'];
            }
        }
        ?>
        <section class="stwc-widget-section stwc-parts-section py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-stwc-filter-scope="parts">
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

                <div class="stwc-parts-grid grid gap-6">
                    <?php foreach ( $parts as $part ) : ?>
                        <?php
                        $url    = ! empty( $part['url'] ) ? $part['url'] : stwc_route_url( '/parts/' . ( $part['id'] ?? 1 ) );
                        $img    = ! empty( $part['image'] ) ? $part['image'] : $fallback;
                        $rating = isset( $part['rating'] ) ? (float) $part['rating'] : 5.0;
                        ?>
                        <article class="stwc-filter-item bg-white rounded-[1px] overflow-hidden border border-dark/10 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col hover:-translate-y-1" data-category="<?php echo esc_attr( $part['category'] ?? 'Auto Parts' ); ?>">
                            <div class="aspect-[4/3] relative overflow-hidden bg-slate-50">
                                <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $part['name'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <div class="absolute top-3 left-3 bg-secondary text-white text-xs font-bold px-3 py-1 rounded-[1px]">
                                    <?php echo esc_html( $part['category'] ?? 'Auto Parts' ); ?>
                                </div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="stwc-accent-text text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( $part['brand'] ?? 'OEM Genuine' ); ?></span>
                                    <div class="flex items-center gap-1 text-xs font-bold text-dark">
                                        <?php echo $this->render_icon( 'star-filled', 'w-3.5 h-3.5 text-accent' ); ?>
                                        <span><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-dark text-lg mb-3 leading-snug group-hover:text-accent transition-colors">
                                    <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $part['name'] ); ?></a>
                                </h3>
                                <div class="text-2xl font-extrabold text-dark mb-5 mt-auto"><?php echo esc_html( $part['price'] ); ?></div>
                                <div class="flex gap-2">
                                    <a href="<?php echo esc_url( $url ); ?>" class="flex-1 text-center bg-primary hover:bg-black text-white py-2.5 px-4 font-bold text-sm transition-colors rounded-full"><?php esc_html_e( 'View Part', 'santa-towing-website-core' ); ?></a>
                                    <a href="<?php echo esc_url( $tel ); ?>" class="stwc-accent-bg bg-accent hover:bg-black text-white py-2.5 px-4 font-bold text-sm transition-colors rounded-full flex items-center justify-center" aria-label="<?php esc_attr_e( 'Call to order part', 'santa-towing-website-core' ); ?>">
                                        <?php echo $this->render_icon( 'phone', 'w-4 h-4' ); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
