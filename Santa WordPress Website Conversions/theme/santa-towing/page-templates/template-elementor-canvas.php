<?php
/**
 * Template Name: Elementor Full-Bleed Canvas (With Theme Header & Footer)
 * Template Post Type: page, post, st_car, st_rental, st_part, st_service_area
 *
 * Full-bleed edge-to-edge layout compatible with Elementor Free while retaining
 * the Santa Towing TopBar, NavBar, Footer, and Emergency Floating Buttons.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main id="primary" class="st-elementor-fullbleed min-h-screen bg-white">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>
<?php
get_footer();
