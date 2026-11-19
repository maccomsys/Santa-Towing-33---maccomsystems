<?php
/**
 * Single Service Area CPT Template (`st_service_area`)
 *
 * Delegates to template-parts/service-area-template.php with prev/next area navigation.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    if ( st_is_elementor_page( get_the_ID() ) ) {
        echo '<main id="primary" class="pt-20 lg:pt-[76px] min-h-screen bg-white">';
        the_content();
        echo '</main>';
    } else {
        $post_id     = get_the_ID();
        $area_label  = get_post_meta( $post_id, '_st_area_label', true ) ?: get_the_title();
        $heading     = get_post_meta( $post_id, '_st_heading', true ) ?: get_the_title();
        $description = get_post_meta( $post_id, '_st_description', true ) ?: get_the_content();
        $prev_path   = get_post_meta( $post_id, '_st_prev_path', true );
        $prev_label  = get_post_meta( $post_id, '_st_prev_label', true );
        $next_path   = get_post_meta( $post_id, '_st_next_path', true );
        $next_label  = get_post_meta( $post_id, '_st_next_label', true );

        if ( ! $prev_path ) {
            $prev_post = get_previous_post();
            if ( $prev_post ) {
                $prev_path  = get_permalink( $prev_post );
                $prev_label = get_the_title( $prev_post );
            }
        } else {
            $prev_path = st_route_url( $prev_path );
        }

        if ( ! $next_path ) {
            $next_post = get_next_post();
            if ( $next_post ) {
                $next_path  = get_permalink( $next_post );
                $next_label = get_the_title( $next_post );
            }
        } else {
            $next_path = st_route_url( $next_path );
        }

        get_template_part(
            'template-parts/service-area-template',
            null,
            array(
                'slug'        => get_post_field( 'post_name', $post_id ),
                'area_label'  => $area_label,
                'heading'     => $heading,
                'description' => $description,
                'prev_area'   => $prev_path ? array(
                    'url'   => $prev_path,
                    'label' => $prev_label ?: __( 'Previous Area', 'santa-towing' ),
                ) : null,
                'next_area'   => $next_path ? array(
                    'url'   => $next_path,
                    'label' => $next_label ?: __( 'Next Area', 'santa-towing' ),
                ) : null,
            )
        );
    }
endwhile;

get_footer();
