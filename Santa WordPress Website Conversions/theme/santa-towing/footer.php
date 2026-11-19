<?php
/**
 * Theme Footer Template
 * Closes #main-content, renders 5-column Footer + Emergency Floating Buttons
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
    </div><!-- #main-content -->

    <?php
    if ( ! is_page_template( 'page-templates/template-elementor-blank.php' ) ) {
        get_template_part( 'template-parts/footer-content' );
        get_template_part( 'template-parts/floating-buttons' );
    }
    ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
