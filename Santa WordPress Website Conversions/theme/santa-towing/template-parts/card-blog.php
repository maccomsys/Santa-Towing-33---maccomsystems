<?php
/**
 * Template Part: Blog Post Card
 *
 * Exact visual parity with BlogSection / Blog.tsx card.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_item = isset( $args['post'] ) ? $args['post'] : null;

if ( $post_item ) {
    $title    = $post_item['title'];
    $excerpt  = $post_item['excerpt'];
    $category = $post_item['category'];
    $image    = $post_item['image'];
    $url      = $post_item['url'];
    $date     = $post_item['date'] ?? '';
    $read     = $post_item['readTime'] ?? '';
} else {
    $cats     = get_the_category();
    $category = ! empty( $cats ) ? $cats[0]->name : __( 'Automotive Guides', 'santa-towing' );
    $title    = get_the_title();
    $excerpt  = get_the_excerpt();
    $image    = get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: st_theme_image( 'image-1-towing-vehicle.jpg' );
    $url      = get_permalink();
    $date     = get_the_date();
    $read     = st_reading_time( get_the_ID() );
}
?>
<article data-st-reveal class="group bg-white rounded-[1px] overflow-hidden shadow-sm hover:shadow-xl border border-gray-200/80 hover:border-accent/30 transition-all duration-300 flex flex-col hover:-translate-y-1.5">
    <a href="<?php echo esc_url( $url ); ?>" class="aspect-[16/10] relative overflow-hidden bg-gray-100 block">
        <img
            src="<?php echo esc_url( $image ); ?>"
            alt="<?php echo esc_attr( $title ); ?>"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        <div class="absolute top-3.5 left-3.5 bg-dark/80 backdrop-blur-md px-3 py-1 rounded-[1px] text-xs font-bold text-white border border-white/20">
            <?php echo esc_html( $category ); ?>
        </div>
    </a>

    <div class="p-6 sm:p-7 flex flex-col flex-1">
        <?php if ( $date || $read ) : ?>
            <div class="flex items-center gap-4 text-xs text-dark/50 font-semibold mb-3">
                <?php if ( $date ) : ?>
                    <span class="flex items-center gap-1"><?php echo st_icon( 'calendar', 'w-3.5 h-3.5' ); ?> <?php echo esc_html( $date ); ?></span>
                <?php endif; ?>
                <?php if ( $read ) : ?>
                    <span class="flex items-center gap-1"><?php echo st_icon( 'clock', 'w-3.5 h-3.5' ); ?> <?php echo esc_html( $read ); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h3 class="text-lg sm:text-xl font-bold text-dark mb-3 group-hover:text-accent transition-colors leading-snug">
            <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
        </h3>
        <p class="text-slate-600 text-sm sm:text-base mb-6 flex-1 leading-relaxed">
            <?php echo esc_html( $excerpt ); ?>
        </p>
        <a
            href="<?php echo esc_url( $url ); ?>"
            class="mt-auto inline-flex items-center gap-2 text-sm font-bold text-primary group-hover:text-accent transition-colors pt-4 border-t border-gray-100 group/link"
        >
            <span><?php esc_html_e( 'Read Full Article', 'santa-towing' ); ?></span>
            <?php echo st_icon( 'arrow-right', 'w-4 h-4 group-hover/link:translate-x-1.5 transition-transform' ); ?>
        </a>
    </div>
</article>
