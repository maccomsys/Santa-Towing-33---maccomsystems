<?php
/**
 * Floating Emergency Action Buttons (Matches src/components/layout/FloatingButtons.tsx 1:1)
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$whatsapp_url     = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
$tow_tooltip      = st_get_content( 'global.floatingButtons.towTooltip', 'Request Emergency Tow' );
$whatsapp_tooltip = st_get_content( 'global.floatingButtons.whatsappTooltip', 'Chat on WhatsApp' );
?>
<div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex flex-col gap-2.5 sm:gap-3 no-print">
    <!-- Request Emergency Tow Button -->
    <a href="<?php echo esc_url( st_route_url( '/towing' ) ); ?>" class="block" aria-label="<?php echo esc_attr( $tow_tooltip ); ?>">
        <div class="group relative flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 bg-accent hover:bg-accent/95 hover:scale-108 active:scale-95 text-white rounded-full shadow-xl shadow-red-600/30 transition-all border-2 border-white/20 cursor-pointer">
            <?php echo st_icon( 'truck', 'w-5 h-5 sm:w-6 sm:h-6 animate-pulse' ); ?>
            <span class="hidden sm:block absolute right-full mr-3.5 bg-dark text-white text-xs font-semibold px-3 py-2 rounded-[1px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap shadow-xl pointer-events-none">
                <?php echo esc_html( $tow_tooltip ); ?>
                <span class="absolute top-1/2 -right-1 -translate-y-1/2 border-y-4 border-y-transparent border-l-4 border-l-dark"></span>
            </span>
        </div>
    </a>

    <!-- WhatsApp Chat Button -->
    <a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer" class="block" aria-label="<?php echo esc_attr( $whatsapp_tooltip ); ?>">
        <div class="group relative flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 bg-primary hover:bg-primary/90 hover:scale-108 active:scale-95 text-white rounded-full shadow-xl shadow-primary/30 transition-all border-2 border-white/20 cursor-pointer">
            <?php echo st_icon( 'message-circle', 'w-6 h-6 sm:w-7 sm:h-7' ); ?>
            <span class="hidden sm:block absolute right-full mr-3.5 bg-dark text-white text-xs font-semibold px-3 py-2 rounded-[1px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap shadow-xl pointer-events-none">
                <?php echo esc_html( $whatsapp_tooltip ); ?>
                <span class="absolute top-1/2 -right-1 -translate-y-1/2 border-y-4 border-y-transparent border-l-4 border-l-dark"></span>
            </span>
        </div>
    </a>
</div>
