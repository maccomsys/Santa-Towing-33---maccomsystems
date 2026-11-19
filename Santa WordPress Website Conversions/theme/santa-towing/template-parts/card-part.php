<?php
/**
 * Template Part: Auto Part Card
 *
 * Exact visual parity with Parts catalog product card.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$part     = isset( $args['part'] ) ? $args['part'] : array();
$name     = $part['name'] ?? get_the_title();
$brand    = $part['brand'] ?? 'OEM Genuine';
$category = $part['category'] ?? 'Auto Parts';
$price    = $part['price'] ?? 'GH₵ 450';
$rating   = isset( $part['rating'] ) ? (float) $part['rating'] : 5.0;
$image    = $part['image'] ?? st_theme_image( 'image-9-car-battery-maintenance.jpg' );
$url      = $part['url'] ?? st_route_url( '/parts/' . ( $part['id'] ?? get_the_ID() ) );

$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
?>
<article
    data-st-reveal
    data-st-part-card
    data-st-category="<?php echo esc_attr( $category ); ?>"
    class="bg-white rounded-[1px] overflow-hidden border border-dark/10 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col hover:-translate-y-1"
>
    <div class="aspect-[4/3] relative overflow-hidden bg-slate-50">
        <img
            src="<?php echo esc_url( $image ); ?>"
            alt="<?php echo esc_attr( $name ); ?>"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        <div class="absolute top-3 left-3 bg-secondary/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-[1px]">
            <?php echo esc_html( $category ); ?>
        </div>
    </div>

    <div class="p-6 flex-1 flex flex-col">
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider text-accent"><?php echo esc_html( $brand ); ?></span>
            <div class="flex items-center gap-1 text-xs font-bold text-dark">
                <?php echo st_icon( 'star-filled', 'w-3.5 h-3.5 text-accent' ); ?>
                <span><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
            </div>
        </div>

        <h3 class="font-bold text-dark text-lg mb-3 leading-snug group-hover:text-accent transition-colors">
            <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a>
        </h3>

        <div class="text-2xl font-extrabold text-dark mb-5 mt-auto">
            <?php echo esc_html( $price ); ?>
        </div>

        <div class="flex gap-2">
            <a
                href="<?php echo esc_url( $url ); ?>"
                class="flex-1 text-center bg-primary hover:bg-black text-white py-2.5 px-4 font-bold text-sm transition-colors rounded-full"
            >
                <?php esc_html_e( 'View Part', 'santa-towing' ); ?>
            </a>
            <a
                href="<?php echo esc_url( $phone_tel ); ?>"
                class="bg-accent hover:bg-accent/90 text-white py-2.5 px-4 font-bold text-sm transition-colors rounded-full flex items-center justify-center"
                aria-label="<?php esc_attr_e( 'Call to order part', 'santa-towing' ); ?>"
            >
                <?php echo st_icon( 'phone', 'w-4 h-4' ); ?>
            </a>
        </div>
    </div>
</article>
