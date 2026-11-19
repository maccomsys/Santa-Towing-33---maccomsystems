<?php
/**
 * Template Part: Rental Card
 *
 * Exact visual parity with RentalFleet card in CarsAndRentals.tsx.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$rental  = isset( $args['rental'] ) ? $args['rental'] : array();
$name    = $rental['name'] ?? get_the_title();
$daily   = $rental['daily'] ?? 'GH₵ 600';
$weekly  = $rental['weekly'] ?? 'GH₵ 3,800';
$monthly = $rental['monthly'] ?? 'GH₵ 14,000';
$seats   = $rental['seats'] ?? '5';
$trans   = $rental['trans'] ?? ( $rental['transmission'] ?? 'Automatic' );
$fuel    = $rental['fuel'] ?? 'Petrol';
$cat     = $rental['category'] ?? $name;
$image   = $rental['image'] ?? st_theme_image( 'image-3-sedan-toyota-corolla.jpg' );
$url     = $rental['url'] ?? st_route_url( '/rental/' . ( $rental['id'] ?? get_the_ID() ) );

$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$wa_msg    = sprintf( "Hello Santa Towing, I'd like to book the %s (%s/day) for rental. Please share availability.", $name, $daily );
$whatsapp  = function_exists( 'st_build_whatsapp_url' ) ? st_build_whatsapp_url( $wa_msg ) : st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
?>
<article
    data-st-reveal
    data-st-rental-card
    data-st-category="<?php echo esc_attr( $cat ); ?>"
    class="bg-white rounded-[1px] overflow-hidden shadow-sm hover:shadow-xl border border-gray-200/80 hover:border-accent/30 transition-all duration-300 group flex flex-col hover:-translate-y-1.5"
>
    <div class="aspect-[16/10] relative overflow-hidden bg-gray-100">
        <img
            src="<?php echo esc_url( $image ); ?>"
            alt="<?php echo esc_attr( $name ); ?>"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
        <div class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm">
            <?php esc_html_e( 'Rental', 'santa-towing' ); ?>
        </div>
        <h3 class="absolute bottom-4 left-6 text-2xl font-extrabold text-white tracking-wide">
            <?php echo esc_html( $name ); ?>
        </h3>
    </div>

    <div class="p-6 sm:p-7 flex-1 flex flex-col">
        <div class="flex justify-between items-end mb-6 pb-5 border-b border-gray-100">
            <div>
                <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider"><?php esc_html_e( 'Daily Rate', 'santa-towing' ); ?></span>
                <div class="flex items-baseline gap-1 mt-0.5">
                    <span class="text-2xl sm:text-3xl font-extrabold text-dark"><?php echo esc_html( $daily ); ?></span>
                    <span class="text-gray-500 font-medium text-sm">/day</span>
                </div>
            </div>
            <div class="text-right text-xs font-semibold text-slate-600 space-y-0.5">
                <div><?php esc_html_e( 'Weekly:', 'santa-towing' ); ?> <span class="text-dark font-bold"><?php echo esc_html( $weekly ); ?></span></div>
                <div><?php esc_html_e( 'Monthly:', 'santa-towing' ); ?> <span class="text-dark font-bold"><?php echo esc_html( $monthly ); ?></span></div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 mb-6 py-2 bg-slate-50 rounded-[1px] p-3 text-center">
            <div class="flex flex-col items-center gap-1">
                <?php echo st_icon( 'users', 'w-4 h-4 text-primary' ); ?>
                <span class="text-xs font-bold text-dark">
                    <?php
                    echo esc_html( stripos( (string) $seats, 'seat' ) !== false ? $seats : $seats . ' Seats' );
                    ?>
                </span>
            </div>
            <div class="flex flex-col items-center gap-1 border-x border-gray-200">
                <?php echo st_icon( 'settings', 'w-4 h-4 text-primary' ); ?>
                <span class="text-xs font-bold text-dark"><?php echo esc_html( $trans ); ?></span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <?php echo st_icon( 'fuel', 'w-4 h-4 text-primary' ); ?>
                <span class="text-xs font-bold text-dark"><?php echo esc_html( $fuel ); ?></span>
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
