<?php
/**
 * TopBar Partial (Matches src/components/layout/TopBar.tsx 1:1)
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$phone     = st_get_content( 'global.links.phone', '0244753849' );
$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp  = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
$wa_disp   = st_get_content( 'global.links.whatsappDisplay', 'WhatsApp: 0244753849' );
$hours     = st_get_content( 'global.links.hours', '24/7 Towing Service' );
$coverage  = st_get_content( 'global.links.coverage', 'Nationwide Coverage' );
$facebook  = st_get_content( 'global.social.facebook', 'https://facebook.com/santatowing' );
$twitter   = st_get_content( 'global.social.twitter', 'https://twitter.com/santatowing' );
$instagram = st_get_content( 'global.social.instagram', 'https://instagram.com/santatowing' );
$linkedin  = st_get_content( 'global.social.linkedin', 'https://linkedin.com/company/santatowing' );
?>
<div id="st-topbar" class="bg-secondary text-white/80 py-2.5 text-xs md:text-sm border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-2">
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 md:gap-6 font-medium">
            <a href="<?php echo esc_url( $phone_tel ); ?>" class="flex items-center gap-2 text-white hover:text-accent transition-colors font-semibold">
                <?php echo st_icon( 'phone', 'w-3.5 h-3.5 text-accent animate-pulse' ); ?>
                <span><?php esc_html_e( '24/7 Hotline:', 'santa-towing' ); ?> <?php echo esc_html( $phone ); ?></span>
            </a>

            <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="hidden sm:flex items-center gap-2 hover:text-accent transition-colors">
                <?php echo st_icon( 'message-circle', 'w-3.5 h-3.5 text-white/60' ); ?>
                <span><?php echo esc_html( $wa_disp ); ?></span>
            </a>

            <div class="hidden md:flex items-center gap-2 text-white/70">
                <?php echo st_icon( 'clock', 'w-3.5 h-3.5 text-white/60' ); ?>
                <span><?php echo esc_html( $hours ); ?></span>
            </div>

            <div class="hidden lg:flex items-center gap-2 text-white/70">
                <?php echo st_icon( 'map-pin', 'w-3.5 h-3.5 text-accent' ); ?>
                <span><?php echo esc_html( $coverage ); ?></span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-white/80">
            <span class="text-xs uppercase tracking-wider text-white/80 hidden sm:inline font-semibold"><?php esc_html_e( 'Follow Us', 'santa-towing' ); ?></span>
            <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors p-1" aria-label="Facebook"><?php echo st_icon( 'facebook', 'w-3.5 h-3.5' ); ?></a>
            <a href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors p-1" aria-label="Twitter"><?php echo st_icon( 'twitter', 'w-3.5 h-3.5' ); ?></a>
            <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors p-1" aria-label="Instagram"><?php echo st_icon( 'instagram', 'w-3.5 h-3.5' ); ?></a>
            <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-accent transition-colors p-1" aria-label="LinkedIn"><?php echo st_icon( 'linkedin', 'w-3.5 h-3.5' ); ?></a>
        </div>
    </div>
</div>
