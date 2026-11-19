<?php
/**
 * Template Name: Elementor Blank Canvas (No Header / Footer)
 * Template Post Type: page
 *
 * Clean 100% blank canvas for Elementor Free pages without theme header or footer.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main id="primary" class="min-h-screen bg-white">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>
<?php
get_footer();
