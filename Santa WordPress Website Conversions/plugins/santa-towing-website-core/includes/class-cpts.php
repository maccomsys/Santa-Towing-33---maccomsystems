<?php
/**
 * Custom Post Types, Taxonomies, Post Meta & Admin Meta Boxes
 *
 * Mirrors the React source data schemas and maintains 1:1 compatibility with the
 * Santa Towing theme templates and migration scripts.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class STWC_CPTs {

    /**
     * Singleton instance.
     *
     * @var STWC_CPTs|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Hook registration.
     */
    private function __construct() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 6 );
        add_action( 'init', array( __CLASS__, 'register_meta_fields' ), 7 );

        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );

        // Admin table columns
        add_filter( 'manage_st_car_posts_columns', array( $this, 'columns_car' ) );
        add_action( 'manage_st_car_posts_custom_column', array( $this, 'render_column_car' ), 10, 2 );

        add_filter( 'manage_st_rental_posts_columns', array( $this, 'columns_rental' ) );
        add_action( 'manage_st_rental_posts_custom_column', array( $this, 'render_column_rental' ), 10, 2 );

        add_filter( 'manage_st_part_posts_columns', array( $this, 'columns_part' ) );
        add_action( 'manage_st_part_posts_custom_column', array( $this, 'render_column_part' ), 10, 2 );

        add_filter( 'manage_st_service_area_posts_columns', array( $this, 'columns_service_area' ) );
        add_action( 'manage_st_service_area_posts_custom_column', array( $this, 'render_column_service_area' ), 10, 2 );

        add_filter( 'manage_st_career_posts_columns', array( $this, 'columns_career' ) );
        add_action( 'manage_st_career_posts_custom_column', array( $this, 'render_column_career' ), 10, 2 );
    }

    /**
     * Register all 7 Custom Post Types.
     */
    public static function register_post_types() {
        // 1. Cars for Sale (st_car -> /sales/{id})
        register_post_type(
            'st_car',
            array(
                'labels'       => array(
                    'name'               => __( 'Cars for Sale', 'santa-towing-website-core' ),
                    'singular_name'      => __( 'Car', 'santa-towing-website-core' ),
                    'add_new'            => __( 'Add New Car', 'santa-towing-website-core' ),
                    'add_new_item'       => __( 'Add New Car for Sale', 'santa-towing-website-core' ),
                    'edit_item'          => __( 'Edit Car', 'santa-towing-website-core' ),
                    'all_items'          => __( 'All Cars for Sale', 'santa-towing-website-core' ),
                    'search_items'       => __( 'Search Cars', 'santa-towing-website-core' ),
                    'not_found'          => __( 'No cars found.', 'santa-towing-website-core' ),
                ),
                'public'       => true,
                'has_archive'  => 'sales',
                'rewrite'      => array(
                    'slug'       => 'sales',
                    'with_front' => false,
                ),
                'menu_icon'    => 'dashicons-car',
                'menu_position'=> 27,
                'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
                'show_in_rest' => true,
            )
        );

        // 2. Rental Fleet (st_rental -> /rental/{id})
        register_post_type(
            'st_rental',
            array(
                'labels'       => array(
                    'name'          => __( 'Rental Fleet', 'santa-towing-website-core' ),
                    'singular_name' => __( 'Rental Vehicle', 'santa-towing-website-core' ),
                    'add_new'       => __( 'Add Rental Vehicle', 'santa-towing-website-core' ),
                    'add_new_item'  => __( 'Add New Rental Vehicle', 'santa-towing-website-core' ),
                    'edit_item'     => __( 'Edit Rental Vehicle', 'santa-towing-website-core' ),
                    'all_items'     => __( 'All Rental Vehicles', 'santa-towing-website-core' ),
                ),
                'public'       => true,
                'has_archive'  => 'rental',
                'rewrite'      => array(
                    'slug'       => 'rental',
                    'with_front' => false,
                ),
                'menu_icon'    => 'dashicons-admin-network',
                'menu_position'=> 28,
                'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
                'show_in_rest' => true,
            )
        );

        // 3. Auto Parts (st_part -> /parts/{id})
        register_post_type(
            'st_part',
            array(
                'labels'       => array(
                    'name'          => __( 'Auto Parts', 'santa-towing-website-core' ),
                    'singular_name' => __( 'Auto Part', 'santa-towing-website-core' ),
                    'add_new'       => __( 'Add Auto Part', 'santa-towing-website-core' ),
                    'add_new_item'  => __( 'Add New Auto Part', 'santa-towing-website-core' ),
                    'edit_item'     => __( 'Edit Auto Part', 'santa-towing-website-core' ),
                    'all_items'     => __( 'All Auto Parts', 'santa-towing-website-core' ),
                ),
                'public'       => true,
                'has_archive'  => 'parts',
                'rewrite'      => array(
                    'slug'       => 'parts',
                    'with_front' => false,
                ),
                'menu_icon'    => 'dashicons-admin-tools',
                'menu_position'=> 29,
                'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
                'show_in_rest' => true,
            )
        );

        // 4. Service Area Routes (st_service_area -> /service-areas/{slug})
        register_post_type(
            'st_service_area',
            array(
                'labels'       => array(
                    'name'          => __( 'Service Areas', 'santa-towing-website-core' ),
                    'singular_name' => __( 'Service Area Route', 'santa-towing-website-core' ),
                    'add_new'       => __( 'Add Service Area', 'santa-towing-website-core' ),
                    'add_new_item'  => __( 'Add New Service Area Route', 'santa-towing-website-core' ),
                    'edit_item'     => __( 'Edit Service Area Route', 'santa-towing-website-core' ),
                    'all_items'     => __( 'All Service Areas', 'santa-towing-website-core' ),
                ),
                'public'       => true,
                'has_archive'  => false,
                'rewrite'      => array(
                    'slug'       => 'service-areas',
                    'with_front' => false,
                ),
                'menu_icon'    => 'dashicons-location-alt',
                'menu_position'=> 30,
                'supports'     => array( 'title', 'editor', 'excerpt' ),
                'show_in_rest' => true,
            )
        );

        // 5. Gallery Showcase (st_gallery -> displayed via shortcode / Elementor widget)
        register_post_type(
            'st_gallery',
            array(
                'labels'              => array(
                    'name'          => __( 'Gallery Showcase', 'santa-towing-website-core' ),
                    'singular_name' => __( 'Gallery Item', 'santa-towing-website-core' ),
                    'add_new'       => __( 'Add Gallery Item', 'santa-towing-website-core' ),
                    'add_new_item'  => __( 'Add New Gallery Item', 'santa-towing-website-core' ),
                    'edit_item'     => __( 'Edit Gallery Item', 'santa-towing-website-core' ),
                    'all_items'     => __( 'All Gallery Items', 'santa-towing-website-core' ),
                ),
                'public'              => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-format-gallery',
                'menu_position'       => 31,
                'supports'            => array( 'title', 'thumbnail' ),
                'show_in_rest'        => true,
            )
        );

        // 6. FAQs (st_faq)
        register_post_type(
            'st_faq',
            array(
                'labels'              => array(
                    'name'          => __( 'FAQs', 'santa-towing-website-core' ),
                    'singular_name' => __( 'FAQ', 'santa-towing-website-core' ),
                    'add_new'       => __( 'Add FAQ', 'santa-towing-website-core' ),
                    'add_new_item'  => __( 'Add New FAQ', 'santa-towing-website-core' ),
                    'edit_item'     => __( 'Edit FAQ', 'santa-towing-website-core' ),
                    'all_items'     => __( 'All FAQs', 'santa-towing-website-core' ),
                ),
                'public'              => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => false,
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-editor-help',
                'menu_position'       => 32,
                'supports'            => array( 'title', 'editor', 'page-attributes' ),
                'show_in_rest'        => true,
            )
        );

        // 7. Careers / Job Openings (st_career)
        register_post_type(
            'st_career',
            array(
                'labels'              => array(
                    'name'          => __( 'Careers', 'santa-towing-website-core' ),
                    'singular_name' => __( 'Job Opening', 'santa-towing-website-core' ),
                    'add_new'       => __( 'Add Job Opening', 'santa-towing-website-core' ),
                    'add_new_item'  => __( 'Add New Job Opening', 'santa-towing-website-core' ),
                    'edit_item'     => __( 'Edit Job Opening', 'santa-towing-website-core' ),
                    'all_items'     => __( 'All Job Openings', 'santa-towing-website-core' ),
                ),
                'public'              => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => false,
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-businessperson',
                'menu_position'       => 33,
                'supports'            => array( 'title', 'editor' ),
                'show_in_rest'        => true,
            )
        );
    }

    /**
     * Register all 9 Taxonomies.
     */
    public static function register_taxonomies() {
        register_taxonomy(
            'st_car_category',
            'st_car',
            array(
                'label'        => __( 'Vehicle Condition / Type', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite'      => array( 'slug' => 'car-category' ),
            )
        );

        register_taxonomy(
            'st_car_make',
            'st_car',
            array(
                'label'        => __( 'Vehicle Make', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite'      => array( 'slug' => 'car-make' ),
            )
        );

        register_taxonomy(
            'st_rental_category',
            'st_rental',
            array(
                'label'        => __( 'Rental Category', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite'      => array( 'slug' => 'rental-category' ),
            )
        );

        register_taxonomy(
            'st_part_category',
            'st_part',
            array(
                'label'        => __( 'Part Category', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite'      => array( 'slug' => 'part-category' ),
            )
        );

        register_taxonomy(
            'st_part_brand',
            'st_part',
            array(
                'label'        => __( 'Part Brand', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite'      => array( 'slug' => 'part-brand' ),
            )
        );

        register_taxonomy(
            'st_gallery_category',
            'st_gallery',
            array(
                'label'        => __( 'Gallery Category', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'st_faq_category',
            'st_faq',
            array(
                'label'        => __( 'FAQ Topic', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'st_department',
            'st_career',
            array(
                'label'        => __( 'Department', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'st_region',
            'st_service_area',
            array(
                'label'        => __( 'Ghana Region', 'santa-towing-website-core' ),
                'hierarchical' => true,
                'show_in_rest' => true,
            )
        );
    }

    /**
     * Register post meta keys for REST & sanitization parity.
     */
    public static function register_meta_fields() {
        $meta_map = array(
            'post' => array(
                '_st_legacy_id',
                '_st_read_time',
                '_st_author',
                '_st_author_role',
            ),
            'st_car' => array(
                '_st_legacy_id',
                '_st_car_type',
                '_st_price',
                '_st_fuel',
                '_st_transmission',
                '_st_year',
                '_st_mileage',
                '_st_make',
                '_st_model',
                '_st_status',
                '_st_featured',
                '_st_gallery',
                '_st_specs',
            ),
            'st_rental' => array(
                '_st_legacy_id',
                '_st_rental_type',
                '_st_daily_rate',
                '_st_weekly_rate',
                '_st_monthly_rate',
                '_st_seats',
                '_st_transmission',
                '_st_fuel',
                '_st_deposit',
                '_st_with_driver',
                '_st_status',
                '_st_gallery',
            ),
            'st_part' => array(
                '_st_legacy_id',
                '_st_brand',
                '_st_category',
                '_st_price',
                '_st_sku',
                '_st_rating',
                '_st_reviews',
                '_st_in_stock',
                '_st_stock_status',
                '_st_warranty',
                '_st_condition',
                '_st_compatible_models',
                '_st_installation',
                '_st_specs_json',
                '_st_gallery',
            ),
            'st_gallery' => array(
                '_st_legacy_id',
                '_st_media_type',
                '_st_span',
                '_st_location',
                '_st_date_label',
                '_st_video_url',
            ),
            'st_faq' => array(
                '_st_legacy_id',
                '_st_sort_order',
            ),
            'st_career' => array(
                '_st_legacy_id',
                '_st_location',
                '_st_employment_type',
                '_st_responsibilities_json',
                '_st_requirements_json',
                '_st_benefits_json',
            ),
            'st_service_area' => array(
                '_st_area_label',
                '_st_origin',
                '_st_destination',
                '_st_heading',
                '_st_description',
                '_st_seo_title',
                '_st_seo_description',
                '_st_keywords_json',
                '_st_prev_path',
                '_st_prev_label',
                '_st_next_path',
                '_st_next_label',
            ),
        );

        foreach ( $meta_map as $post_type => $keys ) {
            foreach ( $keys as $key ) {
                register_post_meta(
                    $post_type,
                    $key,
                    array(
                        'single'            => true,
                        'type'              => 'string',
                        'show_in_rest'      => true,
                        'sanitize_callback' => 'sanitize_textarea_field',
                        'auth_callback'     => function () {
                            return current_user_can( 'edit_posts' );
                        },
                    )
                );
            }
        }
    }

    /**
     * Register Custom Admin Meta Boxes.
     */
    public function add_meta_boxes() {
        add_meta_box( 'stwc_car_meta', __( 'Vehicle Specifications & Pricing', 'santa-towing-website-core' ), array( $this, 'render_car_metabox' ), 'st_car', 'normal', 'high' );
        add_meta_box( 'stwc_rental_meta', __( 'Rental Rates & Configuration', 'santa-towing-website-core' ), array( $this, 'render_rental_metabox' ), 'st_rental', 'normal', 'high' );
        add_meta_box( 'stwc_part_meta', __( 'Part Inventory, Specs & Compatibility', 'santa-towing-website-core' ), array( $this, 'render_part_metabox' ), 'st_part', 'normal', 'high' );
        add_meta_box( 'stwc_gallery_meta', __( 'Gallery Display Settings', 'santa-towing-website-core' ), array( $this, 'render_gallery_metabox' ), 'st_gallery', 'normal', 'default' );
        add_meta_box( 'stwc_faq_meta', __( 'FAQ Display Options', 'santa-towing-website-core' ), array( $this, 'render_faq_metabox' ), 'st_faq', 'side', 'default' );
        add_meta_box( 'stwc_career_meta', __( 'Job Details, Responsibilities & Requirements', 'santa-towing-website-core' ), array( $this, 'render_career_metabox' ), 'st_career', 'normal', 'high' );
        add_meta_box( 'stwc_service_area_meta', __( 'Service Area Route & SEO Details', 'santa-towing-website-core' ), array( $this, 'render_service_area_metabox' ), 'st_service_area', 'normal', 'high' );
        add_meta_box( 'stwc_blog_meta', __( 'Article Author & Read Time', 'santa-towing-website-core' ), array( $this, 'render_blog_metabox' ), 'post', 'side', 'default' );
    }

    /**
     * Render Car Meta Box.
     */
    public function render_car_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $legacy_id    = get_post_meta( $post->ID, '_st_legacy_id', true );
        $car_type     = get_post_meta( $post->ID, '_st_car_type', true ) ?: 'New';
        $price        = get_post_meta( $post->ID, '_st_price', true );
        $fuel         = get_post_meta( $post->ID, '_st_fuel', true );
        $transmission = get_post_meta( $post->ID, '_st_transmission', true );
        $year         = get_post_meta( $post->ID, '_st_year', true );
        $mileage      = get_post_meta( $post->ID, '_st_mileage', true );
        $make         = get_post_meta( $post->ID, '_st_make', true );
        $model        = get_post_meta( $post->ID, '_st_model', true );
        $status       = get_post_meta( $post->ID, '_st_status', true ) ?: 'Available';
        $featured     = get_post_meta( $post->ID, '_st_featured', true );
        $gallery      = get_post_meta( $post->ID, '_st_gallery', true );
        ?>
        <div class="stwc-metabox-grid">
            <p><label><strong><?php esc_html_e( 'Condition / Type', 'santa-towing-website-core' ); ?></strong><br>
                <select name="stwc_meta[_st_car_type]" class="widefat">
                    <?php foreach ( array( 'New', 'Foreign Used', 'Ghana Used' ) as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $car_type, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
                    <?php endforeach; ?>
                </select></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Price (e.g. GH₵ 980,000)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_price]" value="<?php echo esc_attr( $price ); ?>" class="widefat" placeholder="GH₵ 980,000"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Year', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_year]" value="<?php echo esc_attr( $year ); ?>" class="widefat" placeholder="2024"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Fuel Type', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_fuel]" value="<?php echo esc_attr( $fuel ); ?>" class="widefat" placeholder="Petrol / Hybrid / Diesel"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Transmission', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_transmission]" value="<?php echo esc_attr( $transmission ); ?>" class="widefat" placeholder="Automatic"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Mileage', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_mileage]" value="<?php echo esc_attr( $mileage ); ?>" class="widefat" placeholder="0 km"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Make', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_make]" value="<?php echo esc_attr( $make ); ?>" class="widefat" placeholder="Toyota"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Model', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_model]" value="<?php echo esc_attr( $model ); ?>" class="widefat" placeholder="Corolla"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Availability Status', 'santa-towing-website-core' ); ?></strong><br>
                <select name="stwc_meta[_st_status]" class="widefat">
                    <?php foreach ( array( 'Available', 'Reserved', 'Sold' ) as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $status, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
                    <?php endforeach; ?>
                </select></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Legacy Route ID (e.g. /sales/1)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="number" name="stwc_meta[_st_legacy_id]" value="<?php echo esc_attr( $legacy_id ); ?>" class="widefat"></label>
            </p>
            <p class="stwc-col-span-2">
                <label><input type="checkbox" name="stwc_meta[_st_featured]" value="1" <?php checked( $featured, '1' ); ?>> <strong><?php esc_html_e( 'Feature in Homepage Cars Showcase', 'santa-towing-website-core' ); ?></strong></label>
            </p>
            <div class="stwc-col-span-2">
                <?php $this->render_gallery_picker_field( '_st_gallery', $gallery, __( 'Vehicle Photo Gallery (Additional Images)', 'santa-towing-website-core' ) ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Rental Meta Box.
     */
    public function render_rental_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $legacy_id    = get_post_meta( $post->ID, '_st_legacy_id', true );
        $rental_type  = get_post_meta( $post->ID, '_st_rental_type', true );
        $daily_rate   = get_post_meta( $post->ID, '_st_daily_rate', true );
        $weekly_rate  = get_post_meta( $post->ID, '_st_weekly_rate', true );
        $monthly_rate = get_post_meta( $post->ID, '_st_monthly_rate', true );
        $seats        = get_post_meta( $post->ID, '_st_seats', true );
        $transmission = get_post_meta( $post->ID, '_st_transmission', true );
        $fuel         = get_post_meta( $post->ID, '_st_fuel', true );
        $deposit      = get_post_meta( $post->ID, '_st_deposit', true );
        $with_driver  = get_post_meta( $post->ID, '_st_with_driver', true );
        $status       = get_post_meta( $post->ID, '_st_status', true ) ?: 'Available';
        $gallery      = get_post_meta( $post->ID, '_st_gallery', true );
        ?>
        <div class="stwc-metabox-grid">
            <p><label><strong><?php esc_html_e( 'Rental Category / Type', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_rental_type]" value="<?php echo esc_attr( $rental_type ); ?>" class="widefat" placeholder="SUV / Sedan / Pickup / Luxury"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Daily Rate (e.g. GH₵ 900)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_daily_rate]" value="<?php echo esc_attr( $daily_rate ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Weekly Rate (e.g. GH₵ 5,800)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_weekly_rate]" value="<?php echo esc_attr( $weekly_rate ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Monthly Rate (e.g. GH₵ 21,000)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_monthly_rate]" value="<?php echo esc_attr( $monthly_rate ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Seating Capacity', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_seats]" value="<?php echo esc_attr( $seats ); ?>" class="widefat" placeholder="5 Seats"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Transmission', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_transmission]" value="<?php echo esc_attr( $transmission ); ?>" class="widefat" placeholder="Automatic"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Fuel Type', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_fuel]" value="<?php echo esc_attr( $fuel ); ?>" class="widefat" placeholder="Petrol"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Security Deposit', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_deposit]" value="<?php echo esc_attr( $deposit ); ?>" class="widefat" placeholder="GH₵ 2,500 (Refundable)"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Availability Status', 'santa-towing-website-core' ); ?></strong><br>
                <select name="stwc_meta[_st_status]" class="widefat">
                    <?php foreach ( array( 'Available', 'Booked', 'Maintenance' ) as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $status, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
                    <?php endforeach; ?>
                </select></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Legacy Route ID (e.g. /rental/1)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="number" name="stwc_meta[_st_legacy_id]" value="<?php echo esc_attr( $legacy_id ); ?>" class="widefat"></label>
            </p>
            <p class="stwc-col-span-2">
                <label><input type="checkbox" name="stwc_meta[_st_with_driver]" value="1" <?php checked( $with_driver, '1' ); ?>> <strong><?php esc_html_e( 'Optional Chauffeur / Driver Available', 'santa-towing-website-core' ); ?></strong></label>
            </p>
            <div class="stwc-col-span-2">
                <?php $this->render_gallery_picker_field( '_st_gallery', $gallery, __( 'Rental Photo Gallery', 'santa-towing-website-core' ) ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Auto Part Meta Box.
     */
    public function render_part_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $legacy_id    = get_post_meta( $post->ID, '_st_legacy_id', true );
        $brand        = get_post_meta( $post->ID, '_st_brand', true );
        $category     = get_post_meta( $post->ID, '_st_category', true );
        $price        = get_post_meta( $post->ID, '_st_price', true );
        $sku          = get_post_meta( $post->ID, '_st_sku', true );
        $rating       = get_post_meta( $post->ID, '_st_rating', true ) ?: '5.0';
        $reviews      = get_post_meta( $post->ID, '_st_reviews', true ) ?: '12';
        $in_stock     = get_post_meta( $post->ID, '_st_in_stock', true );
        $warranty     = get_post_meta( $post->ID, '_st_warranty', true );
        $condition    = get_post_meta( $post->ID, '_st_condition', true ) ?: 'Brand New OEM';
        $compat       = get_post_meta( $post->ID, '_st_compatible_models', true );
        $installation = get_post_meta( $post->ID, '_st_installation', true );
        $specs_json   = get_post_meta( $post->ID, '_st_specs_json', true );
        $gallery      = get_post_meta( $post->ID, '_st_gallery', true );
        ?>
        <div class="stwc-metabox-grid">
            <p><label><strong><?php esc_html_e( 'Brand', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_brand]" value="<?php echo esc_attr( $brand ); ?>" class="widefat" placeholder="Bosch / Toyota Genuine / Amaron"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Category', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_category]" value="<?php echo esc_attr( $category ); ?>" class="widefat" placeholder="Batteries / Brakes / Filters"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Price', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_price]" value="<?php echo esc_attr( $price ); ?>" class="widefat" placeholder="GH₵ 1,450"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'SKU / Part Code', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_sku]" value="<?php echo esc_attr( $sku ); ?>" class="widefat" placeholder="ST-BAT-075"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Condition', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_condition]" value="<?php echo esc_attr( $condition ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Warranty', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_warranty]" value="<?php echo esc_attr( $warranty ); ?>" class="widefat" placeholder="24 Months Replacement Warranty"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Rating (1.0 - 5.0)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_rating]" value="<?php echo esc_attr( $rating ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Review Count', 'santa-towing-website-core' ); ?></strong><br>
                <input type="number" name="stwc_meta[_st_reviews]" value="<?php echo esc_attr( $reviews ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Legacy Route ID (e.g. /parts/1)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="number" name="stwc_meta[_st_legacy_id]" value="<?php echo esc_attr( $legacy_id ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Workshop Installation Fee', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_installation]" value="<?php echo esc_attr( $installation ); ?>" class="widefat" placeholder="Available at Spintex Garage"></label>
            </p>
            <p class="stwc-col-span-2">
                <label><input type="checkbox" name="stwc_meta[_st_in_stock]" value="1" <?php checked( '' === $in_stock ? '1' : $in_stock, '1' ); ?>> <strong><?php esc_html_e( 'In Stock (Ready for Dispatch / Pickup)', 'santa-towing-website-core' ); ?></strong></label>
            </p>
            <p class="stwc-col-span-2"><label><strong><?php esc_html_e( 'Compatible Vehicle Models (comma-separated)', 'santa-towing-website-core' ); ?></strong><br>
                <textarea name="stwc_meta[_st_compatible_models]" rows="2" class="widefat"><?php echo esc_textarea( $compat ); ?></textarea></label>
            </p>
            <p class="stwc-col-span-2"><label><strong><?php esc_html_e( 'Technical Specs JSON (Optional Key/Value Object)', 'santa-towing-website-core' ); ?></strong><br>
                <textarea name="stwc_meta[_st_specs_json]" rows="3" class="widefat"><?php echo esc_textarea( $specs_json ); ?></textarea></label>
            </p>
            <div class="stwc-col-span-2">
                <?php $this->render_gallery_picker_field( '_st_gallery', $gallery, __( 'Part Photo Gallery', 'santa-towing-website-core' ) ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Gallery Meta Box.
     */
    public function render_gallery_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $media_type = get_post_meta( $post->ID, '_st_media_type', true ) ?: 'photo';
        $span       = get_post_meta( $post->ID, '_st_span', true ) ?: 'col-span-1 row-span-1';
        $location   = get_post_meta( $post->ID, '_st_location', true );
        $date_label = get_post_meta( $post->ID, '_st_date_label', true );
        $video_url  = get_post_meta( $post->ID, '_st_video_url', true );
        ?>
        <div class="stwc-metabox-grid">
            <p><label><strong><?php esc_html_e( 'Media Type', 'santa-towing-website-core' ); ?></strong><br>
                <select name="stwc_meta[_st_media_type]" class="widefat">
                    <option value="photo" <?php selected( $media_type, 'photo' ); ?>><?php esc_html_e( 'Photo', 'santa-towing-website-core' ); ?></option>
                    <option value="video" <?php selected( $media_type, 'video' ); ?>><?php esc_html_e( 'Video', 'santa-towing-website-core' ); ?></option>
                </select></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Masonry Grid Span', 'santa-towing-website-core' ); ?></strong><br>
                <select name="stwc_meta[_st_span]" class="widefat">
                    <option value="col-span-1 row-span-1" <?php selected( $span, 'col-span-1 row-span-1' ); ?>>1x1 Standard</option>
                    <option value="col-span-2 row-span-1" <?php selected( $span, 'col-span-2 row-span-1' ); ?>>2x1 Wide</option>
                    <option value="col-span-1 row-span-2" <?php selected( $span, 'col-span-1 row-span-2' ); ?>>1x2 Tall</option>
                    <option value="col-span-2 row-span-2" <?php selected( $span, 'col-span-2 row-span-2' ); ?>>2x2 Featured Large</option>
                </select></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Location Label', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_location]" value="<?php echo esc_attr( $location ); ?>" class="widefat" placeholder="Spintex Workshop"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Date Label', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_date_label]" value="<?php echo esc_attr( $date_label ); ?>" class="widefat" placeholder="October 2024"></label>
            </p>
            <p class="stwc-col-span-2"><label><strong><?php esc_html_e( 'Video URL (MP4 or Embed URL if Media Type is Video)', 'santa-towing-website-core' ); ?></strong><br>
                <input type="url" name="stwc_meta[_st_video_url]" value="<?php echo esc_attr( $video_url ); ?>" class="widefat"></label>
            </p>
        </div>
        <?php
    }

    /**
     * Render FAQ Meta Box.
     */
    public function render_faq_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $order = get_post_meta( $post->ID, '_st_sort_order', true ) ?: '10';
        ?>
        <p><label><strong><?php esc_html_e( 'Display Priority Order', 'santa-towing-website-core' ); ?></strong><br>
            <input type="number" name="stwc_meta[_st_sort_order]" value="<?php echo esc_attr( $order ); ?>" class="widefat"></label>
        </p>
        <?php
    }

    /**
     * Render Career Meta Box.
     */
    public function render_career_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $location  = get_post_meta( $post->ID, '_st_location', true ) ?: 'Accra Main Workshop';
        $emp_type  = get_post_meta( $post->ID, '_st_employment_type', true ) ?: 'Full-time';
        $resp_json = get_post_meta( $post->ID, '_st_responsibilities_json', true );
        $req_json  = get_post_meta( $post->ID, '_st_requirements_json', true );
        $ben_json  = get_post_meta( $post->ID, '_st_benefits_json', true );

        $resp_lines = is_array( json_decode( (string) $resp_json, true ) ) ? implode( "\n", json_decode( (string) $resp_json, true ) ) : $resp_json;
        $req_lines  = is_array( json_decode( (string) $req_json, true ) ) ? implode( "\n", json_decode( (string) $req_json, true ) ) : $req_json;
        $ben_lines  = is_array( json_decode( (string) $ben_json, true ) ) ? implode( "\n", json_decode( (string) $ben_json, true ) ) : $ben_json;
        ?>
        <div class="stwc-metabox-grid">
            <p><label><strong><?php esc_html_e( 'Work Location', 'santa-towing-website-core' ); ?></strong><br>
                <input type="text" name="stwc_meta[_st_location]" value="<?php echo esc_attr( $location ); ?>" class="widefat"></label>
            </p>
            <p><label><strong><?php esc_html_e( 'Employment Type', 'santa-towing-website-core' ); ?></strong><br>
                <select name="stwc_meta[_st_employment_type]" class="widefat">
                    <?php foreach ( array( 'Full-time', 'Part-time', 'Contract', 'Apprenticeship' ) as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $emp_type, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
                    <?php endforeach; ?>
                </select></label>
            </p>
            <p class="stwc-col-span-2"><label><strong><?php esc_html_e( 'Key Responsibilities (one per line)', 'santa-towing-website-core' ); ?></strong><br>
                <textarea name="stwc_career_lists[responsibilities]" rows="4" class="widefat"><?php echo esc_textarea( $resp_lines ); ?></textarea></label>
            </p>
            <p class="stwc-col-span-2"><label><strong><?php esc_html_e( 'Requirements & Qualifications (one per line)', 'santa-towing-website-core' ); ?></strong><br>
                <textarea name="stwc_career_lists[requirements]" rows="4" class="widefat"><?php echo esc_textarea( $req_lines ); ?></textarea></label>
            </p>
            <p class="stwc-col-span-2"><label><strong><?php esc_html_e( 'Employee Benefits (one per line)', 'santa-towing-website-core' ); ?></strong><br>
                <textarea name="stwc_career_lists[benefits]" rows="3" class="widefat"><?php echo esc_textarea( $ben_lines ); ?></textarea></label>
            </p>
        </div>
        <?php
    }

    /**
     * Render Service Area Meta Box.
     */
    public function render_service_area_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $fields = array(
            '_st_area_label'      => __( 'Area / District Label (e.g. Tema)', 'santa-towing-website-core' ),
            '_st_origin'          => __( 'Route Origin (if Intercity)', 'santa-towing-website-core' ),
            '_st_destination'     => __( 'Route Destination (if Intercity)', 'santa-towing-website-core' ),
            '_st_heading'         => __( 'H1 Page Heading', 'santa-towing-website-core' ),
            '_st_seo_title'       => __( 'SEO Meta Title', 'santa-towing-website-core' ),
            '_st_seo_description' => __( 'SEO Meta Description', 'santa-towing-website-core' ),
            '_st_prev_path'       => __( 'Previous Area Path (e.g. /service-areas/accra)', 'santa-towing-website-core' ),
            '_st_prev_label'      => __( 'Previous Area Label', 'santa-towing-website-core' ),
            '_st_next_path'       => __( 'Next Area Path (e.g. /service-areas/kumasi)', 'santa-towing-website-core' ),
            '_st_next_label'      => __( 'Next Area Label', 'santa-towing-website-core' ),
        );
        echo '<div class="stwc-metabox-grid">';
        foreach ( $fields as $key => $label ) {
            $val = get_post_meta( $post->ID, $key, true );
            echo '<p><label><strong>' . esc_html( $label ) . '</strong><br>';
            echo '<input type="text" name="stwc_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( $val ) . '" class="widefat"></label></p>';
        }
        $desc = get_post_meta( $post->ID, '_st_description', true );
        echo '<p class="stwc-col-span-2"><label><strong>' . esc_html__( 'Service Area Intro Description', 'santa-towing-website-core' ) . '</strong><br>';
        echo '<textarea name="stwc_meta[_st_description]" rows="3" class="widefat">' . esc_textarea( $desc ) . '</textarea></label></p>';
        echo '</div>';
    }

    /**
     * Render Blog Meta Box.
     */
    public function render_blog_metabox( $post ) {
        wp_nonce_field( 'stwc_save_meta_nonce', 'stwc_meta_nonce' );
        $legacy_id   = get_post_meta( $post->ID, '_st_legacy_id', true );
        $read_time   = get_post_meta( $post->ID, '_st_read_time', true );
        $author      = get_post_meta( $post->ID, '_st_author', true ) ?: 'Santa Towing Editorial Team';
        $author_role = get_post_meta( $post->ID, '_st_author_role', true ) ?: 'Automotive Operations';
        ?>
        <p><label><strong><?php esc_html_e( 'Legacy Article ID (e.g. /blog/1)', 'santa-towing-website-core' ); ?></strong><br>
            <input type="number" name="stwc_meta[_st_legacy_id]" value="<?php echo esc_attr( $legacy_id ); ?>" class="widefat"></label></p>
        <p><label><strong><?php esc_html_e( 'Read Time Label', 'santa-towing-website-core' ); ?></strong><br>
            <input type="text" name="stwc_meta[_st_read_time]" value="<?php echo esc_attr( $read_time ); ?>" class="widefat" placeholder="5 min read"></label></p>
        <p><label><strong><?php esc_html_e( 'Display Author Name', 'santa-towing-website-core' ); ?></strong><br>
            <input type="text" name="stwc_meta[_st_author]" value="<?php echo esc_attr( $author ); ?>" class="widefat"></label></p>
        <p><label><strong><?php esc_html_e( 'Author Role / Title', 'santa-towing-website-core' ); ?></strong><br>
            <input type="text" name="stwc_meta[_st_author_role]" value="<?php echo esc_attr( $author_role ); ?>" class="widefat"></label></p>
        <?php
    }

    /**
     * Reusable Media Library Gallery Picker Field for CPT Meta Boxes.
     */
    private function render_gallery_picker_field( $meta_key, $value, $label ) {
        ?>
        <div class="stwc-gallery-field" data-target-input="<?php echo esc_attr( $meta_key ); ?>">
            <label><strong><?php echo esc_html( $label ); ?></strong></label>
            <p class="description"><?php esc_html_e( 'JSON array or comma-separated image URLs/IDs used in detail page image switchers.', 'santa-towing-website-core' ); ?></p>
            <input type="hidden" name="stwc_meta[<?php echo esc_attr( $meta_key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="stwc-gallery-input" />
            <div class="stwc-gallery-thumbs" style="display:flex;flex-wrap:wrap;gap:8px;margin:8px 0;"></div>
            <button type="button" class="button stwc-add-gallery-images"><?php esc_html_e( 'Select Gallery Images', 'santa-towing-website-core' ); ?></button>
            <button type="button" class="button stwc-clear-gallery-images"><?php esc_html_e( 'Clear Gallery', 'santa-towing-website-core' ); ?></button>
        </div>
        <?php
    }

    /**
     * Save Meta Boxes cleanly with nonce, autosave & capability verification.
     */
    public function save_meta_boxes( $post_id, $post ) {
        if ( ! isset( $_POST['stwc_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stwc_meta_nonce'] ) ), 'stwc_save_meta_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['stwc_meta'] ) && is_array( $_POST['stwc_meta'] ) ) {
            $raw_meta = wp_unslash( $_POST['stwc_meta'] );
            foreach ( $raw_meta as $key => $val ) {
                if ( 0 !== strpos( (string) $key, '_st_' ) ) {
                    continue;
                }
                if ( in_array( $key, array( '_st_description', '_st_compatible_models' ), true ) ) {
                    $clean = sanitize_textarea_field( (string) $val );
                } elseif ( '_st_video_url' === $key ) {
                    $clean = esc_url_raw( (string) $val );
                } else {
                    $clean = sanitize_text_field( (string) $val );
                }
                update_post_meta( $post_id, $key, $clean );
            }
        }

        // Handle explicit checkboxes so unchecking clears state
        if ( 'st_car' === $post->post_type ) {
            $featured = isset( $_POST['stwc_meta']['_st_featured'] ) ? '1' : '0';
            update_post_meta( $post_id, '_st_featured', $featured );
        }
        if ( 'st_rental' === $post->post_type ) {
            $with_driver = isset( $_POST['stwc_meta']['_st_with_driver'] ) ? '1' : '0';
            update_post_meta( $post_id, '_st_with_driver', $with_driver );
        }
        if ( 'st_part' === $post->post_type ) {
            $in_stock = isset( $_POST['stwc_meta']['_st_in_stock'] ) ? '1' : '0';
            update_post_meta( $post_id, '_st_in_stock', $in_stock );
            update_post_meta( $post_id, '_st_stock_status', '1' === $in_stock ? 'In Stock' : 'Out of Stock' );
        }

        // Career newline lists -> JSON arrays
        if ( 'st_career' === $post->post_type && isset( $_POST['stwc_career_lists'] ) && is_array( $_POST['stwc_career_lists'] ) ) {
            $lists = wp_unslash( $_POST['stwc_career_lists'] );
            foreach ( array( 'responsibilities', 'requirements', 'benefits' ) as $list_key ) {
                if ( isset( $lists[ $list_key ] ) ) {
                    $lines = array_values(
                        array_filter(
                            array_map( 'sanitize_text_field', preg_split( '/\r\n|\r|\n/', (string) $lists[ $list_key ] ) ),
                            'strlen'
                        )
                    );
                    update_post_meta( $post_id, '_st_' . $list_key . '_json', wp_json_encode( $lines ) );
                }
            }
        }
    }

    /**
     * Admin list columns.
     */
    public function columns_car( $cols ) {
        return array(
            'cb'        => $cols['cb'],
            'title'     => __( 'Vehicle', 'santa-towing-website-core' ),
            'st_type'   => __( 'Condition', 'santa-towing-website-core' ),
            'st_price'  => __( 'Price', 'santa-towing-website-core' ),
            'st_year'   => __( 'Year', 'santa-towing-website-core' ),
            'st_status' => __( 'Status', 'santa-towing-website-core' ),
            'date'      => $cols['date'],
        );
    }

    public function render_column_car( $col, $post_id ) {
        $map = array(
            'st_type'   => '_st_car_type',
            'st_price'  => '_st_price',
            'st_year'   => '_st_year',
            'st_status' => '_st_status',
        );
        if ( isset( $map[ $col ] ) ) {
            echo esc_html( get_post_meta( $post_id, $map[ $col ], true ) ?: '—' );
        }
    }

    public function columns_rental( $cols ) {
        return array(
            'cb'          => $cols['cb'],
            'title'       => __( 'Rental Vehicle', 'santa-towing-website-core' ),
            'st_category' => __( 'Category', 'santa-towing-website-core' ),
            'st_daily'    => __( 'Daily Rate', 'santa-towing-website-core' ),
            'st_status'   => __( 'Status', 'santa-towing-website-core' ),
            'date'        => $cols['date'],
        );
    }

    public function render_column_rental( $col, $post_id ) {
        $map = array(
            'st_category' => '_st_rental_type',
            'st_daily'    => '_st_daily_rate',
            'st_status'   => '_st_status',
        );
        if ( isset( $map[ $col ] ) ) {
            echo esc_html( get_post_meta( $post_id, $map[ $col ], true ) ?: '—' );
        }
    }

    public function columns_part( $cols ) {
        return array(
            'cb'       => $cols['cb'],
            'title'    => __( 'Part Name', 'santa-towing-website-core' ),
            'st_brand' => __( 'Brand', 'santa-towing-website-core' ),
            'st_price' => __( 'Price', 'santa-towing-website-core' ),
            'st_sku'   => __( 'SKU', 'santa-towing-website-core' ),
            'date'     => $cols['date'],
        );
    }

    public function render_column_part( $col, $post_id ) {
        $map = array(
            'st_brand' => '_st_brand',
            'st_price' => '_st_price',
            'st_sku'   => '_st_sku',
        );
        if ( isset( $map[ $col ] ) ) {
            echo esc_html( get_post_meta( $post_id, $map[ $col ], true ) ?: '—' );
        }
    }

    public function columns_service_area( $cols ) {
        return array(
            'cb'       => $cols['cb'],
            'title'    => __( 'Service Area Route', 'santa-towing-website-core' ),
            'st_area'  => __( 'Area Label', 'santa-towing-website-core' ),
            'st_route' => __( 'Origin / Destination', 'santa-towing-website-core' ),
            'date'     => $cols['date'],
        );
    }

    public function render_column_service_area( $col, $post_id ) {
        if ( 'st_area' === $col ) {
            echo esc_html( get_post_meta( $post_id, '_st_area_label', true ) ?: '—' );
        } elseif ( 'st_route' === $col ) {
            $o = get_post_meta( $post_id, '_st_origin', true );
            $d = get_post_meta( $post_id, '_st_destination', true );
            echo esc_html( ( $o || $d ) ? trim( $o . ' → ' . $d, ' →' ) : 'District / Town' );
        }
    }

    public function columns_career( $cols ) {
        return array(
            'cb'          => $cols['cb'],
            'title'       => __( 'Job Title', 'santa-towing-website-core' ),
            'st_location' => __( 'Location', 'santa-towing-website-core' ),
            'st_type'     => __( 'Type', 'santa-towing-website-core' ),
            'date'        => $cols['date'],
        );
    }

    public function render_column_career( $col, $post_id ) {
        if ( 'st_location' === $col ) {
            echo esc_html( get_post_meta( $post_id, '_st_location', true ) ?: '—' );
        } elseif ( 'st_type' === $col ) {
            echo esc_html( get_post_meta( $post_id, '_st_employment_type', true ) ?: 'Full-time' );
        }
    }
}
