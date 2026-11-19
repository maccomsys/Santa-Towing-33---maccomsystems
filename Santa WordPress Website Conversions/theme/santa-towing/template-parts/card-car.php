<?php
/**
 * Template Part: Car Card
 *
 * Exact visual parity with FeaturedCars / Sales car card.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$car          = isset( $args['car'] ) ? $args['car'] : array();
$name         = $car['name'] ?? get_the_title();
$price        = $car['price'] ?? 'Contact for Price';
$year         = $car['year'] ?? '2024';
$fuel         = $car['fuel'] ?? 'Petrol';
$transmission = $car['transmission'] ?? 'Automatic';
$mileage      = $car['mileage'] ?? '0 km';
$type         = $car['type'] ?? 'New';
$image        = $car['image'] ?? st_theme_image( 'image-3-sedan-toyota-corolla.jpg' );
$url          = $car['url'] ?? st_route_url( '/sales/' . ( $car['id'] ?? get_the_ID() ) );

$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$wa_msg    = sprintf( "Hello Santa Towing, I'm interested in buying the %s (%s). Is it still available?", $name, $price );
$whatsapp  = function_exists( 'st_build_whatsapp_url' ) ? st_build_whatsapp_url( $wa_msg ) : st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
?>
<article
    data-st-reveal
    data-st-car-card
    data-st-category="<?php echo esc_attr( $type ); ?>"
    class="bg-white text-dark rounded-[1px] overflow-hidden border border-gray-200 hover:border-accent/50 transition-all duration-300 group flex flex-col hover:-translate-y-1.5 hover:shadow-2xl"
>
    <div class="aspect-[4/3] relative overflow-hidden bg-black/40">
        <img
            src="<?php echo esc_url( $image ); ?>"
            alt="<?php echo esc_attr( $name ); ?>"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        <div class="absolute top-3 left-3 bg-accent text-white px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-md flex items-center gap-1">
            <?php echo st_icon( 'tag', 'w-3 h-3' ); ?>
            <span><?php esc_html_e( 'For Sale', 'santa-towing' ); ?></span>
        </div>
        <div class="absolute top-3 right-3 bg-dark/80 backdrop-blur-md px-2.5 py-1 rounded-[1px] text-xs font-bold text-white border border-white/20">
            <?php echo esc_html( $year ); ?>
        </div>
    </div>

    <div class="p-5 sm:p-6 flex-1 flex flex-col">
        <h3 class="text-lg font-bold mb-1.5 group-hover:text-accent transition-colors">
            <a href="<?php echo esc_url( $url ); ?>" class="hover:text-accent transition-colors">
                <?php echo esc_html( $name ); ?>
            </a>
        </h3>
        <div class="text-2xl font-extrabold text-dark mb-5 tracking-tight">
            <?php echo esc_html( $price ); ?>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-6 flex-1 text-xs text-dark/80 border-t border-b border-gray-100 py-3.5">
            <div class="flex items-center gap-2">
                <?php echo st_icon( 'fuel', 'w-4 h-4 text-accent shrink-0' ); ?>
                <span><?php echo esc_html( $fuel ); ?></span>
            </div>
            <div class="flex items-center gap-2">
                <?php echo st_icon( 'settings', 'w-4 h-4 text-accent shrink-0' ); ?>
                <span><?php echo esc_html( $transmission ); ?></span>
            </div>
            <div class="flex items-center gap-2">
                <?php echo st_icon( 'map-pin', 'w-4 h-4 text-accent shrink-0' ); ?>
                <span><?php echo esc_html( $mileage ); ?></span>
            </div>
            <div class="flex items-center gap-2">
                <?php echo st_icon( 'shield-check', 'w-4 h-4 text-accent shrink-0' ); ?>
                <span><?php esc_html_e( 'Inspected', 'santa-towing' ); ?></span>
            </div>
        </div>

        <div class="flex flex-col gap-2 mt-auto">
            <a
                href="<?php echo esc_url( $url ); ?>"
                class="w-full text-center bg-white hover:bg-dark/5 text-dark border border-dark/20 py-3 font-bold transition-colors text-sm rounded-full"
            >
                <?php esc_html_e( 'View Details', 'santa-towing' ); ?>
            </a>
            <div class="flex gap-2">
                <a
                    href="<?php echo esc_url( $phone_tel ); ?>"
                    class="flex-1 text-center bg-primary hover:bg-black text-white py-3 font-bold transition-colors text-sm shadow-md rounded-full flex items-center justify-center gap-1"
                >
                    <?php echo st_icon( 'phone', 'w-4 h-4' ); ?>
                    <span><?php esc_html_e( 'Call', 'santa-towing' ); ?></span>
                </a>
                <a
                    href="<?php echo esc_url( $whatsapp ); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex-1 text-center bg-primary hover:bg-primary/90 text-white py-3 font-bold transition-colors text-sm shadow-md rounded-full flex items-center justify-center gap-1"
                >
                    <?php echo st_icon( 'message-circle', 'w-4 h-4' ); ?>
                    <span><?php esc_html_e( 'WhatsApp', 'santa-towing' ); ?></span>
                </a>
            </div>
        </div>
    </div>
</article>
