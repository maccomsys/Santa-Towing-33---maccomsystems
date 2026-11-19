<?php
/**
 * Elementor Free Widget: Haulage & Tema Harbour Logistics Showcase
 *
 * Replicates React `src/components/sections/HaulageShowcase.tsx` & corridor matrix.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Haulage_Showcase extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-haulage-showcase';
    }

    public function get_title() {
        return __( 'Santa Haulage Showcase', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Haulage Content & Corridors', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Badge', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Heavy Haulage & Tema Harbour Logistics',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Heavy Machinery, Container & Vehicle Haulage',
            )
        );

        $this->add_control(
            'description',
            array(
                'label'   => __( 'Description', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'We move excavators, 20ft/40ft shipping containers, generators, industrial machinery, and dealership fleets from Tema Port & Accra to Kumasi, Takoradi, Tamale & nationwide.',
            )
        );

        $this->add_control(
            'feature_image',
            array(
                'label'   => __( 'Showcase Image', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'haulage-container-port-1920.jpg' ) ),
            )
        );

        $this->add_control(
            'cta_label',
            array(
                'label'   => __( 'Button Label', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Explore Haulage & Request Quote',
            )
        );

        $this->add_control(
            'cta_link',
            array(
                'label'   => __( 'Button URL', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => home_url( '/haulage/' ) ),
            )
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'destination',
            array(
                'label'   => __( 'Route / Destination', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Accra / Tema Metro',
            )
        );
        $repeater->add_control(
            'distance',
            array(
                'label'   => __( 'Distance', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '15 – 35 km',
            )
        );
        $repeater->add_control(
            'eta',
            array(
                'label'   => __( 'Transit ETA', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '45 – 90 mins',
            )
        );
        $repeater->add_control(
            'highlight',
            array(
                'label'   => __( 'Highlight Note', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Express daily port shuttles',
            )
        );

        $this->add_control(
            'corridors',
            array(
                'label'       => __( 'Tema & Intercity Corridors', 'santa-towing-website-core' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ destination }}} ({{{ eta }}})',
                'default'     => array(
                    array( 'destination' => 'Accra / Tema Metro', 'distance' => '15 – 35 km', 'eta' => '45 – 90 mins', 'highlight' => 'Express daily port shuttles' ),
                    array( 'destination' => 'Kumasi & Ashanti Region', 'distance' => '280 km', 'eta' => '4 – 6 hours', 'highlight' => 'Direct N6 dual-carriage transit' ),
                    array( 'destination' => 'Takoradi & Western Region', 'distance' => '240 km', 'eta' => '4 – 5 hours', 'highlight' => 'Coastal industrial corridor' ),
                    array( 'destination' => 'Tamale & Northern Ghana', 'distance' => '640 km', 'eta' => '12 – 16 hours', 'highlight' => 'Cross-country heavy transport' ),
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-haulage-section', '#192e2e', '#ffffff' );

        $this->start_controls_section(
            'section_haulage_responsive',
            array(
                'label' => __( 'Corridor Grid & Typography', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'corridor_columns',
            array(
                'label'          => __( 'Corridor Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '2',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array( '1' => '1', '2' => '2', '4' => '4' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-corridor-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s         = $this->get_settings_for_display();
        $img_url   = ! empty( $s['feature_image']['url'] ) ? $s['feature_image']['url'] : stwc_asset_image( 'haulage-container-port-1920.jpg' );
        $cta_url   = ! empty( $s['cta_link']['url'] ) ? $s['cta_link']['url'] : home_url( '/haulage/' );
        $corridors = $s['corridors'] ?? array();
        ?>
        <section class="stwc-widget-section stwc-haulage-section bg-secondary text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-10 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <div class="stwc-section-header text-left">
                            <?php if ( ! empty( $s['badge'] ) ) : ?>
                                <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                            <?php endif; ?>
                            <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-white mb-4"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
                            <p class="text-white/80 leading-relaxed"><?php echo esc_html( $s['description'] ?? '' ); ?></p>
                        </div>

                        <?php if ( ! empty( $corridors ) ) : ?>
                            <div class="stwc-corridor-grid grid gap-3 pt-2">
                                <?php foreach ( $corridors as $cor ) : ?>
                                    <div class="bg-white/5 border border-white/10 p-4 rounded-[1px]">
                                        <div class="font-bold text-white text-sm"><?php echo esc_html( $cor['destination'] ?? '' ); ?></div>
                                        <div class="text-xs text-accent font-bold mt-1"><?php echo esc_html( ( $cor['distance'] ?? '' ) . ' • ' . ( $cor['eta'] ?? '' ) ); ?></div>
                                        <div class="text-xs text-white/70 mt-1"><?php echo esc_html( $cor['highlight'] ?? '' ); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="pt-3">
                            <a href="<?php echo esc_url( $cta_url ); ?>" class="stwc-accent-bg inline-flex items-center gap-2 bg-accent hover:bg-black text-white font-bold px-7 py-3.5 rounded-full transition-colors">
                                <span><?php echo esc_html( $s['cta_label'] ?? 'Explore Haulage' ); ?></span>
                                <?php echo $this->render_icon( 'arrow-right', 'w-4 h-4' ); ?>
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="aspect-[16/10] rounded-[1px] overflow-hidden border border-white/15 shadow-2xl">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $s['heading'] ?? 'Haulage Showcase' ); ?>" class="w-full h-full object-cover" loading="lazy" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
