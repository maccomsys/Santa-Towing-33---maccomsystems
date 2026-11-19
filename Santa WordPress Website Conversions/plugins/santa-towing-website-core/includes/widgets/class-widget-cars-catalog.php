<?php
/**
 * Elementor Free Widget: Certified Cars for Sale Catalog & Filter Tabs
 *
 * Replicates React `FeaturedCars.tsx` & `Sales.tsx` car grid.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Cars_Catalog extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-cars-catalog';
    }

    public function get_title() {
        return __( 'Santa Cars Catalog', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-products';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            array(
                'label' => __( 'Catalog Query & Filters', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Section Eyebrow', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Inspected Dealership Inventory',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Cars for Sale',
            )
        );

        $this->add_control(
            'subheading',
            array(
                'label'   => __( 'Subheading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Every vehicle undergoes a 150-point mechanical and structural inspection before listing.',
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label'   => __( 'Max Vehicles Shown', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min'     => 1,
                'max'     => 24,
            )
        );

        $this->add_control(
            'show_filters',
            array(
                'label'        => __( 'Show Condition Filter Tabs', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'fallback_image',
            array(
                'label'   => __( 'Fallback Vehicle Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ) ),
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label'          => __( 'Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-cars-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-cars-section', '#f8fafc', '#131514' );

        $this->start_controls_section(
            'section_card_style',
            array(
                'label' => __( 'Car Card Styling', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'price_color',
            array(
                'label'     => __( 'Price Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#131514',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-car-price' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'card_title_typography',
                'label'    => __( 'Card Title Typography', 'santa-towing-website-core' ),
                'selector' => '{{WRAPPER}} .stwc-car-title',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s        = $this->get_settings_for_display();
        $limit    = absint( $s['posts_per_page'] ?? 6 );
        $cars     = array_slice( stwc_get_cars( array( 'posts_per_page' => $limit ) ), 0, $limit );
        $fallback = ! empty( $s['fallback_image']['url'] ) ? $s['fallback_image']['url'] : stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' );
        $tel      = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
        ?>
        <section class="stwc-widget-section stwc-cars-section bg-slate-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-stwc-filter-scope="cars">
                <div class="stwc-section-header max-w-3xl mx-auto mb-10 text-center">
                    <?php if ( ! empty( $s['badge'] ) ) : ?>
                        <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                    <?php endif; ?>
                    <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-dark mb-3"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
                    <?php if ( ! empty( $s['subheading'] ) ) : ?>
                        <p class="text-dark/70"><?php echo esc_html( $s['subheading'] ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( 'yes' === ( $s['show_filters'] ?? 'yes' ) ) : ?>
                    <div class="flex flex-wrap justify-center gap-2 mb-10">
                        <?php foreach ( array( 'All', 'New', 'Foreign Used', 'Ghana Used' ) as $idx => $tab ) : ?>
                            <button type="button" class="stwc-filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition-colors <?php echo 0 === $idx ? 'bg-primary text-white is-active' : 'bg-white text-dark border border-dark/15 hover:border-primary'; ?>" data-filter-value="<?php echo esc_attr( $tab ); ?>">
                                <?php echo esc_html( $tab ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="stwc-cars-grid grid gap-6">
                    <?php foreach ( $cars as $car ) : ?>
                        <?php
                        $img = ! empty( $car['image'] ) ? $car['image'] : $fallback;
                        $url = ! empty( $car['url'] ) ? $car['url'] : stwc_route_url( '/sales/' . ( $car['id'] ?? 1 ) );
                        $wa  = stwc_build_whatsapp_url( 'Hello Santa Towing, I am interested in buying the ' . ( $car['name'] ?? 'vehicle' ) . ' (' . ( $car['price'] ?? '' ) . ').' );
                        ?>
                        <article class="stwc-filter-item bg-white text-dark rounded-[1px] overflow-hidden border border-gray-200 hover:border-accent/50 transition-all duration-300 group flex flex-col hover:-translate-y-1.5 hover:shadow-2xl" data-category="<?php echo esc_attr( $car['type'] ?? 'New' ); ?>">
                            <div class="aspect-[4/3] relative overflow-hidden bg-black/40">
                                <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $car['name'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <div class="stwc-accent-bg absolute top-3 left-3 bg-accent text-white px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-md">
                                    <?php echo esc_html( $car['type'] ?? 'For Sale' ); ?>
                                </div>
                                <div class="absolute top-3 right-3 bg-dark/80 px-2.5 py-1 rounded-[1px] text-xs font-bold text-white border border-white/20">
                                    <?php echo esc_html( $car['year'] ?? '2024' ); ?>
                                </div>
                            </div>
                            <div class="p-5 sm:p-6 flex-1 flex flex-col">
                                <h3 class="stwc-car-title text-lg font-bold mb-1.5 group-hover:text-accent transition-colors">
                                    <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $car['name'] ); ?></a>
                                </h3>
                                <div class="stwc-car-price text-2xl font-extrabold text-dark mb-5 tracking-tight"><?php echo esc_html( $car['price'] ); ?></div>
                                <div class="grid grid-cols-2 gap-3 mb-6 flex-1 text-xs text-dark/80 border-t border-b border-gray-100 py-3.5">
                                    <div class="flex items-center gap-2"><?php echo $this->render_icon( 'settings', 'w-4 h-4 text-accent shrink-0' ); ?><span><?php echo esc_html( $car['transmission'] ?? 'Automatic' ); ?></span></div>
                                    <div class="flex items-center gap-2"><?php echo $this->render_icon( 'map-pin', 'w-4 h-4 text-accent shrink-0' ); ?><span><?php echo esc_html( $car['mileage'] ?? '0 km' ); ?></span></div>
                                    <div class="flex items-center gap-2"><?php echo $this->render_icon( 'car', 'w-4 h-4 text-accent shrink-0' ); ?><span><?php echo esc_html( $car['fuel'] ?? 'Petrol' ); ?></span></div>
                                    <div class="flex items-center gap-2"><?php echo $this->render_icon( 'shield-check', 'w-4 h-4 text-accent shrink-0' ); ?><span><?php esc_html_e( 'Inspected', 'santa-towing-website-core' ); ?></span></div>
                                </div>
                                <div class="flex flex-col gap-2 mt-auto">
                                    <a href="<?php echo esc_url( $url ); ?>" class="w-full text-center bg-white hover:bg-dark/5 text-dark border border-dark/20 py-3 font-bold transition-colors text-sm rounded-full"><?php esc_html_e( 'View Details', 'santa-towing-website-core' ); ?></a>
                                    <div class="flex gap-2">
                                        <a href="<?php echo esc_url( $tel ); ?>" class="flex-1 text-center bg-primary hover:bg-black text-white py-3 font-bold transition-colors text-sm shadow-md rounded-full flex items-center justify-center gap-1">
                                            <?php echo $this->render_icon( 'phone', 'w-4 h-4' ); ?>
                                            <span><?php esc_html_e( 'Call', 'santa-towing-website-core' ); ?></span>
                                        </a>
                                        <a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener noreferrer" class="flex-1 text-center bg-primary hover:bg-primary/90 text-white py-3 font-bold transition-colors text-sm shadow-md rounded-full flex items-center justify-center gap-1">
                                            <?php echo $this->render_icon( 'message-circle', 'w-4 h-4' ); ?>
                                            <span><?php esc_html_e( 'WhatsApp', 'santa-towing-website-core' ); ?></span>
                                        </a>
                                    </div>
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
