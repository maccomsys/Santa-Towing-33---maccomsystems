<?php
/**
 * Elementor Free Widget: Rental Fleet Cards & Category Filters
 *
 * Replicates React `Rental.tsx` & `CarsAndRentals.tsx`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Rental_Fleet extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-rental-fleet';
    }

    public function get_title() {
        return __( 'Santa Rental Fleet', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-price-list';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            array(
                'label' => __( 'Rental Fleet Options', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Section Eyebrow', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Self-Drive & Chauffeur Driven',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Available Rental Fleet',
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label'   => __( 'Max Vehicles', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min'     => 1,
                'max'     => 24,
            )
        );

        $this->add_control(
            'show_filters',
            array(
                'label'        => __( 'Show Vehicle Class Tabs', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'fallback_image',
            array(
                'label'   => __( 'Fallback Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ) ),
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
                'options'        => array( '1' => '1', '2' => '2', '3' => '3' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-rental-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-rental-section', '#ffffff', '#131514' );

        $this->start_controls_section(
            'section_rental_card_style',
            array(
                'label' => __( 'Rental Card Style', 'santa-towing-website-core' ),
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
                    '{{WRAPPER}} .stwc-rental-grid article' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .stwc-rental-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s       = $this->get_settings_for_display();
        $limit   = absint( $s['posts_per_page'] ?? 6 );
        $rentals = array_slice( stwc_get_rentals( array( 'posts_per_page' => $limit ) ), 0, $limit );
        $tel     = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
        $cats    = array( 'All' );
        foreach ( $rentals as $r ) {
            if ( ! empty( $r['category'] ) && ! in_array( $r['category'], $cats, true ) ) {
                $cats[] = $r['category'];
            }
        }
        ?>
        <section class="stwc-widget-section stwc-rental-section py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-stwc-filter-scope="rentals">
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

                <div class="stwc-rental-grid grid gap-6">
                    <?php foreach ( $rentals as $rental ) : ?>
                        <?php
                        $img = ! empty( $rental['image'] ) ? $rental['image'] : ( $s['fallback_image']['url'] ?? stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ) );
                        $url = ! empty( $rental['url'] ) ? $rental['url'] : stwc_route_url( '/rental/' . ( $rental['id'] ?? 1 ) );
                        $wa  = stwc_build_whatsapp_url( 'Hello Santa Towing, I would like to book the ' . ( $rental['name'] ?? 'rental car' ) . ' (' . ( $rental['daily'] ?? '' ) . '/day).' );
                        ?>
                        <article class="stwc-filter-item bg-white rounded-[1px] overflow-hidden shadow-sm hover:shadow-xl border border-gray-200/80 hover:border-accent/30 transition-all duration-300 group flex flex-col hover:-translate-y-1.5" data-category="<?php echo esc_attr( $rental['category'] ?? '' ); ?>">
                            <div class="aspect-[16/10] relative overflow-hidden bg-gray-100">
                                <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $rental['name'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                                <div class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm">
                                    <?php echo esc_html( $rental['category'] ?? 'Rental' ); ?>
                                </div>
                                <h3 class="absolute bottom-4 left-6 text-xl font-extrabold text-white"><?php echo esc_html( $rental['name'] ); ?></h3>
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex justify-between items-end mb-5 pb-4 border-b border-gray-100">
                                    <div>
                                        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider"><?php esc_html_e( 'Daily Rate', 'santa-towing-website-core' ); ?></span>
                                        <div class="flex items-baseline gap-1 mt-0.5">
                                            <span class="text-2xl font-extrabold text-dark"><?php echo esc_html( $rental['daily'] ); ?></span>
                                            <span class="text-gray-500 text-xs">/day</span>
                                        </div>
                                    </div>
                                    <div class="text-right text-xs font-semibold text-slate-600 space-y-0.5">
                                        <div><?php esc_html_e( 'Weekly:', 'santa-towing-website-core' ); ?> <strong class="text-dark"><?php echo esc_html( $rental['weekly'] ); ?></strong></div>
                                        <div><?php esc_html_e( 'Monthly:', 'santa-towing-website-core' ); ?> <strong class="text-dark"><?php echo esc_html( $rental['monthly'] ); ?></strong></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mb-6 bg-slate-50 rounded-[1px] p-3 text-center text-xs font-bold text-dark">
                                    <div><?php echo esc_html( $rental['seats'] ?? '5 Seats' ); ?></div>
                                    <div class="border-x border-gray-200"><?php echo esc_html( $rental['trans'] ?? 'Automatic' ); ?></div>
                                    <div><?php echo esc_html( $rental['fuel'] ?? 'Petrol' ); ?></div>
                                </div>
                                <div class="flex flex-col gap-2 mt-auto">
                                    <a href="<?php echo esc_url( $url ); ?>" class="w-full text-center bg-white hover:bg-dark/5 text-dark border border-dark/20 py-3 font-bold transition-colors text-sm rounded-full"><?php esc_html_e( 'View Details', 'santa-towing-website-core' ); ?></a>
                                    <div class="flex gap-2">
                                        <a href="<?php echo esc_url( $tel ); ?>" class="flex-1 text-center bg-primary hover:bg-black text-white py-3 font-bold transition-colors text-sm rounded-full flex items-center justify-center gap-1">
                                            <?php echo $this->render_icon( 'phone', 'w-4 h-4' ); ?><span><?php esc_html_e( 'Call', 'santa-towing-website-core' ); ?></span>
                                        </a>
                                        <a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener noreferrer" class="flex-1 text-center bg-accent hover:bg-black text-white py-3 font-bold transition-colors text-sm rounded-full flex items-center justify-center gap-1">
                                            <?php echo $this->render_icon( 'message-circle', 'w-4 h-4' ); ?><span><?php esc_html_e( 'Book', 'santa-towing-website-core' ); ?></span>
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
