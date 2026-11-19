<?php
/**
 * Elementor Free Widget: Blog & Automotive Advice Grid
 *
 * Replicates React `BlogSection.tsx` & `Blog.tsx`.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class STWC_Widget_Blog_Grid extends STWC_Widget_Base {

    public function get_name() {
        return 'stwc-blog-grid';
    }

    public function get_title() {
        return __( 'Santa Blog & Guides Grid', 'santa-towing-website-core' );
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            array(
                'label' => __( 'Blog Posts Query', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'badge',
            array(
                'label'   => __( 'Eyebrow', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Roadside Tips & Auto Maintenance',
            )
        );

        $this->add_control(
            'heading',
            array(
                'label'   => __( 'Heading', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Latest From Santa Towing Blog',
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label'   => __( 'Posts Count', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min'     => 1,
                'max'     => 12,
            )
        );

        $this->add_control(
            'fallback_image',
            array(
                'label'   => __( 'Default Article Thumbnail', 'santa-towing-website-core' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => stwc_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ) ),
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
                    '{{WRAPPER}} .stwc-blog-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->end_controls_section();

        $this->register_common_section_style_controls( '.stwc-blog-section', '#f8fafc', '#131514' );

        $this->start_controls_section(
            'section_blog_card_style',
            array(
                'label' => __( 'Article Card Style', 'santa-towing-website-core' ),
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
                    '{{WRAPPER}} .stwc-blog-grid article' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .stwc-blog-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $s       = $this->get_settings_for_display();
        $limit   = absint( $s['posts_per_page'] ?? 3 );
        $fallback = ! empty( $s['fallback_image']['url'] ) ? $s['fallback_image']['url'] : stwc_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' );

        $query = new WP_Query(
            array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
            )
        );

        $articles = array();
        if ( $query->have_posts() ) {
            foreach ( $query->posts as $p ) {
                $cats       = get_the_category( $p->ID );
                $cat_name   = ! empty( $cats ) ? $cats[0]->name : 'Guides';
                $articles[] = array(
                    'title'     => get_the_title( $p->ID ),
                    'excerpt'   => wp_trim_words( wp_strip_all_tags( $p->post_excerpt ?: $p->post_content ), 20 ),
                    'url'       => get_permalink( $p->ID ),
                    'image'     => get_the_post_thumbnail_url( $p->ID, 'medium_large' ) ?: $fallback,
                    'category'  => $cat_name,
                    'read_time' => get_post_meta( $p->ID, '_st_read_time', true ) ?: '4 min read',
                    'date'      => get_the_date( 'M j, Y', $p->ID ),
                );
            }
        } else {
            $articles = array(
                array( 'title' => 'What to Do Immediately After a Roadside Breakdown in Accra', 'excerpt' => 'Stay safe inside or off the shoulder, activate hazards, and share exact landmarks with our 24/7 dispatchers.', 'url' => home_url( '/towing/' ), 'image' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ), 'category' => 'Roadside Safety', 'read_time' => '5 min read', 'date' => 'Featured Guide' ),
                array( 'title' => 'Flatbed vs Wheel-Lift Towing: Which Does Your Vehicle Need?', 'excerpt' => 'Why AWD SUVs, luxury sedans, and accident-damaged vehicles require zero-clearance hydraulic flatbed transport.', 'url' => home_url( '/towing/' ), 'image' => stwc_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'category' => 'Towing Guide', 'read_time' => '4 min read', 'date' => 'Featured Guide' ),
                array( 'title' => 'Essential Preventive Maintenance Before Long Up-Country Trips', 'excerpt' => 'Check battery cranking amps, brake pad thickness, cooling hoses, and spare tyre pressure before travelling.', 'url' => home_url( '/repairs/' ), 'image' => $fallback, 'category' => 'Maintenance', 'read_time' => '6 min read', 'date' => 'Featured Guide' ),
            );
            $articles = array_slice( $articles, 0, $limit );
        }
        ?>
        <section class="stwc-widget-section stwc-blog-section bg-slate-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="stwc-section-header max-w-3xl mx-auto mb-10 text-center">
                    <?php if ( ! empty( $s['badge'] ) ) : ?>
                        <span class="stwc-accent-text text-accent font-bold uppercase tracking-wider text-xs block mb-2"><?php echo esc_html( $s['badge'] ); ?></span>
                    <?php endif; ?>
                    <h2 class="stwc-section-heading text-3xl md:text-4xl font-extrabold text-dark mb-3"><?php echo esc_html( $s['heading'] ?? '' ); ?></h2>
                </div>

                <div class="stwc-blog-grid grid gap-6">
                    <?php foreach ( $articles as $art ) : ?>
                        <article class="bg-white rounded-[1px] overflow-hidden border border-dark/10 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                            <a href="<?php echo esc_url( $art['url'] ); ?>" class="aspect-[16/10] relative overflow-hidden block bg-slate-100">
                                <img src="<?php echo esc_url( $art['image'] ); ?>" alt="<?php echo esc_attr( $art['title'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <span class="stwc-accent-bg absolute top-3 left-3 bg-accent text-white text-xs font-bold px-3 py-1 rounded-[1px]"><?php echo esc_html( $art['category'] ); ?></span>
                            </a>
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="text-xs text-dark/60 font-semibold mb-2"><?php echo esc_html( $art['date'] . ' • ' . $art['read_time'] ); ?></div>
                                <h3 class="font-bold text-lg text-dark mb-2 leading-snug group-hover:text-accent transition-colors">
                                    <a href="<?php echo esc_url( $art['url'] ); ?>"><?php echo esc_html( $art['title'] ); ?></a>
                                </h3>
                                <p class="text-sm text-dark/70 leading-relaxed mb-5 flex-1"><?php echo esc_html( $art['excerpt'] ); ?></p>
                                <a href="<?php echo esc_url( $art['url'] ); ?>" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-accent mt-auto">
                                    <span><?php esc_html_e( 'Read Article', 'santa-towing-website-core' ); ?></span>
                                    <?php echo $this->render_icon( 'arrow-right', 'w-4 h-4' ); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
