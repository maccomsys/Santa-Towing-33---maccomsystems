<?php
/**
 * Elementor Free Widget: Service Area Route Card & Interactive Coverage Directory
 *
 * Replicates React `ServiceAreaPageTemplate.tsx` & `ServiceAreas.tsx` searchable directory
 * of 32 Rapid Districts and all 16 Ghana Regions / 232 Areas.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Service_Area_Route extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-service-area-route';
    }

    public function get_title() {
        return __( 'Santa Service Area Route / Directory', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-map-pin';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_area_content',
            array(
                'label' => __( 'Service Area Configuration', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'display_mode',
            array(
                'label'   => __( 'Display Mode', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'directory',
                'options' => array(
                    'directory'    => __( 'Searchable 16-Region / 232-Town Directory', 'santa-towing-website-core' ),
                    'single_route' => __( 'Single Service Area Route Template', 'santa-towing-website-core' ),
                ),
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'     => __( 'Heading (Override)', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Nationwide Towing Coverage Across All 16 Regions',
            )
        );

        $this->add_control(
            'description',
            array(
                'label'     => __( 'Description (Override)', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'default'   => 'Search any town, highway corridor, or district across Ghana to view rapid dispatch ETA and localized recovery options.',
            )
        );

        $advert_options = array( '' => __( 'Auto-Detect Current Route / First District', 'santa-towing-website-core' ) );
        foreach ( stwc_get_towing_adverts() as $adv ) {
            $advert_options[ $adv['slug'] ] = $adv['areaName'] . ' (' . $adv['region'] . ')';
        }

        $this->add_control(
            'area_slug',
            array(
                'label'     => __( 'Preview District Route', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => '',
                'options'   => $advert_options,
                'condition' => array( 'display_mode' => 'single_route' ),
            )
        );

        $this->add_control(
            'show_route_image',
            array(
                'label'        => __( 'Show Coverage Truck Banner Image', 'santa-towing-website-core' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'route_image',
            array(
                'label'     => __( 'Route / Coverage Image', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'default'   => array( 'url' => stwc_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ) ),
                'condition' => array( 'show_route_image' => 'yes' ),
            )
        );

        $this->add_responsive_control(
            'directory_columns',
            array(
                'label'          => __( 'Region Card Columns', 'santa-towing-website-core' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
                'condition'      => array( 'display_mode' => 'directory' ),
                'selectors'      => array(
                    '{{WRAPPER}} .stwc-regions-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-service-area-section', '#f8fafc', '#131514' );

        $this->start_controls_section(
            'section_area_card_style',
            array(
                'label' => __( 'Area Card & Typography Style', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'area_card_bg',
            array(
                'label'     => __( 'Region / Route Card Background', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .stwc-region-card, {{WRAPPER}} .stwc-route-card' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'area_card_padding',
            array(
                'label'      => __( 'Card Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .stwc-region-card, {{WRAPPER}} .stwc-route-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $mode = $s['display_mode'] ?? 'directory';

        if ( 'single_route' === $mode ) {
            $slug = $s['area_slug'] ?? '';
            if ( '' === $slug && is_singular( 'st_service_area' ) ) {
                $slug = get_post_field( 'post_name', get_the_ID() );
            }
            $area_data = $slug ? STWC_Rewrites::instance()->lookup_area_data( $slug ) : null;
            if ( ! $area_data ) {
                $adverts   = stwc_get_towing_adverts();
                $first     = $adverts[0];
                $area_data = array(
                    'slug'        => $first['slug'],
                    'area_label'  => $first['areaName'],
                    'heading'     => ! empty( $s['heading'] ) ? $s['heading'] : '24/7 Towing Service in ' . $first['areaName'],
                    'description' => ! empty( $s['description'] ) ? $s['description'] : $first['description'],
                    'region'      => $first['region'],
                    'eta'         => $first['eta'],
                );
            }
            $phone_tel = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
            $wa_url    = stwc_build_whatsapp_url( 'Hello Santa Towing, I need towing assistance in ' . $area_data['area_label'] );
            $heading   = ! empty( $s['heading'] ) && 'Nationwide Towing Coverage Across All 16 Regions' !== $s['heading'] ? $s['heading'] : $area_data['heading'];
            $desc      = ! empty( $s['description'] ) && false === strpos( $s['description'], 'Search any town, highway corridor' ) ? $s['description'] : $area_data['description'];
            $prev_path = ! empty( $area_data['prev_path'] ) ? $area_data['prev_path'] : '';
            $prev_lbl  = ! empty( $area_data['prev_label'] ) ? $area_data['prev_label'] : '';
            $next_path = ! empty( $area_data['next_path'] ) ? $area_data['next_path'] : '';
            $next_lbl  = ! empty( $area_data['next_label'] ) ? $area_data['next_label'] : '';
            ?>
            <section class="stwc-widget-section stwc-service-area-section pt-10 pb-16 md:pb-20 bg-slate-50">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-10">
                        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-dark/60 mb-6">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Home', 'santa-towing-website-core' ); ?></a>
                            <span>/</span>
                            <a href="<?php echo esc_url( home_url( '/towing/' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Towing Services', 'santa-towing-website-core' ); ?></a>
                            <span>/</span>
                            <a href="<?php echo esc_url( home_url( '/service-areas/' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Service Areas', 'santa-towing-website-core' ); ?></a>
                            <span>/</span>
                            <span class="font-bold text-dark"><?php echo esc_html( $area_data['area_label'] ); ?></span>
                        </nav>

                        <h1 class="stwc-section-heading text-3xl md:text-5xl font-bold text-dark mb-4"><?php echo esc_html( $heading ); ?></h1>
                    </div>

                    <div class="stwc-route-card bg-white p-6 sm:p-8 md:p-12 rounded-[1px] border border-dark/10 shadow-sm">
                        <?php if ( 'yes' === ( $s['show_route_image'] ?? 'no' ) && ! empty( $s['route_image']['url'] ) ) : ?>
                            <div class="mb-6 rounded-[1px] overflow-hidden aspect-video">
                                <img src="<?php echo esc_url( $s['route_image']['url'] ); ?>" alt="<?php echo esc_attr( $heading ); ?>" class="w-full h-full object-cover" loading="lazy" />
                            </div>
                        <?php endif; ?>

                        <p class="text-dark/80 text-lg leading-relaxed mb-6"><?php echo esc_html( $desc ); ?></p>

                        <div class="mt-8 p-6 bg-slate-50 rounded-[1px] border border-dark/5">
                            <h3 class="text-xl font-bold text-dark mb-4"><?php esc_html_e( 'Need Immediate Assistance?', 'santa-towing-website-core' ); ?></h3>
                            <p class="text-dark/70 mb-6"><?php esc_html_e( 'Call or WhatsApp us now to book your tow.', 'santa-towing-website-core' ); ?></p>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <a href="<?php echo esc_url( $phone_tel ); ?>" class="flex items-center justify-center gap-3 bg-primary hover:bg-dark text-white px-6 py-4 font-bold rounded-full transition-colors w-full">
                                    <?php echo $this->render_icon( 'phone', 'w-5 h-5' ); ?>
                                    <span><?php esc_html_e( 'Call Now', 'santa-towing-website-core' ); ?></span>
                                </a>
                                <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-3 bg-primary hover:bg-dark text-white px-6 py-4 font-bold rounded-full transition-colors w-full">
                                    <?php echo $this->render_icon( 'message-circle', 'w-5 h-5' ); ?>
                                    <span><?php esc_html_e( 'WhatsApp Us', 'santa-towing-website-core' ); ?></span>
                                </a>
                            </div>
                        </div>

                        <?php if ( $prev_path || $next_path ) : ?>
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 pt-8 border-t border-dark/10">
                                <div class="w-full sm:w-1/2 flex justify-start">
                                    <?php if ( $prev_path ) : ?>
                                        <a href="<?php echo esc_url( home_url( trailingslashit( $prev_path ) ) ); ?>" class="group flex items-center gap-3 hover:bg-slate-50 p-3 rounded-[1px] transition-colors max-w-full">
                                            <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-full group-hover:bg-accent group-hover:text-white transition-colors">
                                                <?php echo $this->render_icon( 'chevron-left', 'w-5 h-5' ); ?>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="text-xs text-dark/50 font-bold uppercase tracking-wider mb-0.5"><?php esc_html_e( 'Previous Area', 'santa-towing-website-core' ); ?></div>
                                                <div class="text-sm font-bold text-dark group-hover:text-accent truncate"><?php echo esc_html( $prev_lbl ); ?></div>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="w-full sm:w-1/2 flex justify-end">
                                    <?php if ( $next_path ) : ?>
                                        <a href="<?php echo esc_url( home_url( trailingslashit( $next_path ) ) ); ?>" class="group flex items-center gap-3 hover:bg-slate-50 p-3 rounded-[1px] transition-colors max-w-full text-right">
                                            <div class="overflow-hidden">
                                                <div class="text-xs text-dark/50 font-bold uppercase tracking-wider mb-0.5"><?php esc_html_e( 'Next Area', 'santa-towing-website-core' ); ?></div>
                                                <div class="text-sm font-bold text-dark group-hover:text-accent truncate"><?php echo esc_html( $next_lbl ); ?></div>
                                            </div>
                                            <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-full group-hover:bg-accent group-hover:text-white transition-colors">
                                                <?php echo $this->render_icon( 'chevron-right', 'w-5 h-5' ); ?>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php
            return;
        }

        // Interactive Directory Mode
        $regions = stwc_get_ghana_regions_data();
        ?>
        <section class="stwc-widget-section stwc-service-area-section py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-stwc-area-directory>
                <div class="stwc-section-header max-w-3xl mx-auto text-center mb-10">
                    <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-dark mb-3"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
                    <p class="text-dark/70 mb-6"><?php echo esc_html( $s['description'] ?? '' ); ?></p>
                    <div class="max-w-md mx-auto relative">
                        <input type="search" class="stwc-area-search w-full px-5 py-3.5 rounded-full border border-dark/20 bg-white text-dark shadow-sm focus:outline-none focus:border-primary" placeholder="<?php esc_attr_e( 'Search town or region (e.g. Tema, Kumasi, Spintex)...', 'santa-towing-website-core' ); ?>" />
                    </div>
                </div>

                <div class="stwc-regions-grid grid gap-6">
                    <?php foreach ( $regions as $reg ) : ?>
                        <div class="stwc-region-card bg-white p-6 rounded-[1px] border border-dark/10 shadow-sm" data-region="<?php echo esc_attr( strtolower( $reg['region'] ) ); ?>">
                            <h3 class="text-lg font-extrabold text-primary mb-3 pb-2 border-b border-gray-100 flex items-center justify-between">
                                <span><?php echo esc_html( $reg['region'] ); ?></span>
                                <span class="text-xs font-bold text-accent"><?php echo (int) count( $reg['areas'] ); ?></span>
                            </h3>
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ( $reg['areas'] as $town ) : ?>
                                    <a href="<?php echo esc_url( home_url( '/service-areas/' . sanitize_title( $town ) . '/' ) ); ?>" class="stwc-town-chip text-xs bg-slate-100 hover:bg-accent hover:text-white text-dark px-2.5 py-1 rounded-[1px] transition-colors" data-town="<?php echo esc_attr( strtolower( $town ) ); ?>">
                                        <?php echo esc_html( $town ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
