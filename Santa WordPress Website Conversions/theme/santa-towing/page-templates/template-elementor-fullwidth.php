<?php
/**
 * Template Name: Elementor Full-Width Inner Page (Header Clearance)
 * Template Post Type: page, post, st_car, st_rental, st_part, st_service_area
 *
 * Full-width Elementor Free layout with top padding matching the fixed Santa Towing header.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main id="primary" class="pt-24 lg:pt-[104px] min-h-screen bg-white">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>
<?php
get_footer();
