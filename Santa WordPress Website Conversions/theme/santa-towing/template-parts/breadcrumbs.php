<?php
/**
 * Breadcrumbs Partial (Matches src/components/ui/Breadcrumbs.tsx 1:1)
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$items   = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$variant = isset( $args['variant'] ) && 'light' === $args['variant'] ? 'light' : 'dark';
$extra   = isset( $args['class'] ) ? $args['class'] : '';

$is_light     = 'light' === $variant;
$nav_color    = $is_light ? 'text-white/60' : 'text-dark/60';
$hover_color  = $is_light ? 'hover:text-white' : 'hover:text-accent';
$current_text = $is_light ? 'text-white' : 'text-dark';
?>
<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'santa-towing' ); ?>" class="flex items-center flex-wrap gap-2 text-sm font-medium mb-3 <?php echo esc_attr( trim( $nav_color . ' ' . $extra ) ); ?>">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="transition-colors flex items-center gap-1 <?php echo esc_attr( $hover_color ); ?>">
        <?php echo st_icon( 'home', 'w-3.5 h-3.5' ); ?>
        <span><?php esc_html_e( 'Home', 'santa-towing' ); ?></span>
    </a>

    <?php foreach ( $items as $item ) : ?>
        <?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?>
        <?php if ( ! empty( $item['path'] ) ) : ?>
            <a href="<?php echo esc_url( st_route_url( $item['path'] ) ); ?>" class="transition-colors <?php echo esc_attr( $hover_color ); ?>">
                <?php echo esc_html( $item['label'] ); ?>
            </a>
        <?php else : ?>
            <span class="<?php echo esc_attr( $current_text ); ?>">
                <?php echo esc_html( $item['label'] ); ?>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
