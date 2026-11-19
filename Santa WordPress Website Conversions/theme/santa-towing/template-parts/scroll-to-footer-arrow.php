<?php
/**
 * Template Part: Scroll To Footer Arrow
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$extra_class = isset( $args['class'] ) ? $args['class'] : '';
?>
<button
    type="button"
    data-st-scroll-footer
    aria-label="<?php esc_attr_e( 'Scroll to footer', 'santa-towing' ); ?>"
    class="flex flex-col items-center justify-center group z-20 focus:outline-none <?php echo esc_attr( $extra_class ); ?>"
>
    <span class="st-animate-bob w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white transition-all shadow-lg hover:shadow-xl hover:scale-110">
        <?php echo st_icon( 'arrow-down', 'w-5 h-5 md:w-6 md:h-6 group-hover:text-white' ); ?>
    </span>
</button>
