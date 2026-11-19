<?php
/**
 * Footer Content Partial (Matches src/components/layout/Footer.tsx 1:1)
 * 5-column responsive grid + 12 Legal/Policy links + Newsletter form + Social circles
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$logo_url       = st_get_logo_url();
$brand_name     = st_get_content( 'global.brand.shortName', 'Santa Towing' );
$brand_tagline  = st_get_content( 'global.brand.tagline', '24/7 Emergency Towing, Haulage & Automotive Services in Ghana' );
$about_text     = st_get_content( 'global.footer.about', "Ghana's premier automotive partner for 24/7 rapid emergency towing, heavy car recovery, certified garage repairs, car sales, and rental fleet solutions." );
$phone          = st_get_content( 'global.links.phone', '0244753849' );
$phone_tel      = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp       = function_exists( 'st_build_whatsapp_url' ) ? st_build_whatsapp_url() : st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
$wa_display     = st_get_content( 'global.links.whatsappDisplay', 'WhatsApp: 0244753849' );
$address        = st_get_content( 'global.links.address', '123 Spintex Road, Accra, Ghana' );

$quick_title    = st_get_content( 'global.footer.quickLinksTitle', 'Quick Links' );
$services_title = st_get_content( 'global.footer.servicesTitle', 'Our Services' );
$legal_title    = st_get_content( 'global.footer.legalTitle', 'Legal and Policies' );
$stay_title     = st_get_content( 'global.footer.stayConnectedTitle', 'Stay Connected' );
$nl_text        = st_get_content( 'global.footer.newsletterText', 'Subscribe for roadside safety tips, car maintenance reminders, and exclusive service discounts.' );
$nl_placeholder = st_get_content( 'global.footer.newsletterPlaceholder', 'Enter your email' );
$nl_button      = st_get_content( 'global.footer.newsletterButton', 'Subscribe Now' );
$nl_success     = st_get_content( 'global.footer.newsletterSuccess', 'Thank you! You have successfully subscribed to Santa Towing updates.' );
$copyright_text = st_get_content( 'global.footer.copyright', 'Santa Towing & Garage Services Ltd. All rights reserved.' );

$facebook  = st_get_content( 'global.social.facebook', 'https://facebook.com/santatowing' );
$twitter   = st_get_content( 'global.social.twitter', 'https://twitter.com/santatowing' );
$instagram = st_get_content( 'global.social.instagram', 'https://instagram.com/santatowing' );
$linkedin  = st_get_content( 'global.social.linkedin', 'https://linkedin.com/company/santatowing' );

$default_quick_links = array(
    array( 'name' => 'Home', 'path' => '/' ),
    array( 'name' => 'Haulage Services', 'path' => '/haulage' ),
    array( 'name' => '24/7 Emergency Towing', 'path' => '/towing' ),
    array( 'name' => 'Service Areas Directory', 'path' => '/service-areas' ),
    array( 'name' => 'Auto Repair & Maintenance', 'path' => '/repairs' ),
    array( 'name' => 'Car Rentals', 'path' => '/rental' ),
    array( 'name' => 'Car Sales', 'path' => '/sales' ),
    array( 'name' => 'Auto Parts & Batteries', 'path' => '/parts' ),
    array( 'name' => 'Blog & Resources', 'path' => '/blog' ),
    array( 'name' => 'Contact & Dispatch', 'path' => '/contact' ),
    array( 'name' => 'Sitemap', 'path' => '/sitemap' ),
);

$default_service_links = array(
    array( 'name' => 'Haulage & Heavy Transport', 'path' => '/haulage' ),
    array( 'name' => 'Tema Harbour Haulage', 'path' => '/haulage' ),
    array( 'name' => 'Heavy Machinery Transport', 'path' => '/haulage' ),
    array( 'name' => 'Emergency Roadside Towing', 'path' => '/towing' ),
    array( 'name' => 'Heavy Duty Recovery', 'path' => '/towing' ),
    array( 'name' => 'Fleet Maintenance', 'path' => '/fleet' ),
    array( 'name' => 'Engine & Gearbox Repairs', 'path' => '/repairs' ),
    array( 'name' => 'Brake & Suspension Service', 'path' => '/repairs' ),
    array( 'name' => 'Corporate Car Rentals', 'path' => '/rental' ),
    array( 'name' => 'OEM Battery Replacement', 'path' => '/parts' ),
);

$legal_links = array(
    array( 'name' => 'Privacy Policy', 'path' => '/privacy-policy' ),
    array( 'name' => 'Terms & Conditions', 'path' => '/terms-conditions' ),
    array( 'name' => 'Towing Service Terms', 'path' => '/towing-service-terms' ),
    array( 'name' => 'Cancellation & Refund Policy', 'path' => '/cancellation-refund-policy' ),
    array( 'name' => 'Pricing & Payment Policy', 'path' => '/pricing-payment-policy' ),
    array( 'name' => 'Disclaimer', 'path' => '/disclaimer' ),
    array( 'name' => 'Cookie Policy', 'path' => '/cookie-policy' ),
    array( 'name' => 'Service Area Policy', 'path' => '/service-area-policy' ),
    array( 'name' => 'Emergency Service Policy', 'path' => '/emergency-service-policy' ),
    array( 'name' => 'Complaints Policy', 'path' => '/complaints-policy' ),
    array( 'name' => 'Car Damage & Liability Policy', 'path' => '/car-damage-liability-policy' ),
    array( 'name' => 'Accessibility Statement', 'path' => '/accessibility-statement' ),
);

$raw_quick = st_get_content( 'global.links.quickLinks', null );
if ( ! is_array( $raw_quick ) || empty( $raw_quick ) ) {
    $raw_quick = st_get_content( 'global.footer.quickLinks', null );
}
$raw_service = st_get_content( 'global.links.serviceLinks', null );
if ( ! is_array( $raw_service ) || empty( $raw_service ) ) {
    $raw_service = st_get_content( 'global.footer.serviceLinks', null );
}

$quick_links   = ( is_array( $raw_quick ) && ! empty( $raw_quick ) ) ? $raw_quick : $default_quick_links;
$service_links = ( is_array( $raw_service ) && ! empty( $raw_service ) ) ? $raw_service : $default_service_links;
?>
<footer class="bg-dark text-white/70 pt-16 sm:pt-20 pb-10 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-10 lg:gap-8 mb-14">

            <!-- Brand Column -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block">
                        <img
                            src="<?php echo esc_url( $logo_url ); ?>"
                            alt="<?php echo esc_attr( $brand_name ); ?>"
                            class="h-12 w-auto object-contain bg-white p-1.5 rounded-[1px] shadow-md"
                        />
                    </a>
                    <p class="text-xs text-white/60 italic font-medium">
                        <?php echo esc_html( $brand_tagline ); ?>
                    </p>
                </div>

                <p class="text-white/85 text-sm sm:text-base leading-relaxed">
                    <?php echo esc_html( $about_text ); ?>
                </p>

                <div class="space-y-3 text-sm">
                    <a href="<?php echo esc_url( $phone_tel ); ?>" class="flex items-center gap-3 text-white hover:text-accent transition-colors font-bold">
                        <div class="w-8 h-8 rounded-xl bg-accent/20 flex items-center justify-center text-accent shrink-0">
                            <?php echo st_icon( 'phone', 'w-4 h-4' ); ?>
                        </div>
                        <span><?php echo esc_html( $phone ); ?></span>
                    </a>

                    <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-white/80 hover:text-accent transition-colors break-all sm:break-normal">
                        <div class="w-8 h-8 rounded-xl bg-primary/30 flex items-center justify-center text-white shrink-0">
                            <?php echo st_icon( 'message-circle', 'w-4 h-4' ); ?>
                        </div>
                        <span><?php echo esc_html( $wa_display ); ?></span>
                    </a>

                    <div class="flex items-center gap-3 text-white/80">
                        <div class="w-8 h-8 rounded-xl bg-primary/30 flex items-center justify-center text-white shrink-0">
                            <?php echo st_icon( 'map-pin', 'w-4 h-4' ); ?>
                        </div>
                        <span><?php echo esc_html( $address ); ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-white text-base sm:text-lg font-bold mb-5 relative inline-block">
                    <?php echo esc_html( $quick_title ); ?>
                    <span class="absolute -bottom-1.5 left-0 w-8 h-0.5 bg-accent rounded-full"></span>
                </h4>
                <ul class="space-y-2.5 text-sm">
                    <?php foreach ( $quick_links as $item ) : ?>
                        <?php if ( isset( $item['visible'] ) && false === $item['visible'] ) { continue; } ?>
                        <?php
                        $item_label = $item['label'] ?? ( $item['name'] ?? '' );
                        $item_path  = $item['path'] ?? ( $item['href'] ?? '/' );
                        ?>
                        <li>
                            <a href="<?php echo esc_url( st_route_url( $item_path ) ); ?>" class="hover:text-accent transition-colors flex items-center gap-2 text-white/80 hover:translate-x-1 duration-200">
                                <?php echo st_icon( 'arrow-right', 'w-3 h-3 text-accent shrink-0' ); ?>
                                <span><?php echo esc_html( $item_label ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h4 class="text-white text-base sm:text-lg font-bold mb-5 relative inline-block">
                    <?php echo esc_html( $services_title ); ?>
                    <span class="absolute -bottom-1.5 left-0 w-8 h-0.5 bg-accent rounded-full"></span>
                </h4>
                <ul class="space-y-2.5 text-sm">
                    <?php foreach ( $service_links as $item ) : ?>
                        <?php if ( isset( $item['visible'] ) && false === $item['visible'] ) { continue; } ?>
                        <?php
                        $item_label = $item['label'] ?? ( $item['name'] ?? '' );
                        $item_path  = $item['path'] ?? ( $item['href'] ?? '/' );
                        ?>
                        <li>
                            <a href="<?php echo esc_url( st_route_url( $item_path ) ); ?>" class="hover:text-accent transition-colors flex items-center gap-2 text-white/80 hover:translate-x-1 duration-200">
                                <?php echo st_icon( 'arrow-right', 'w-3 h-3 text-accent shrink-0' ); ?>
                                <span><?php echo esc_html( $item_label ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Legal and Policies -->
            <div>
                <h4 class="text-white text-base sm:text-lg font-bold mb-5 relative inline-block">
                    <?php echo esc_html( $legal_title ); ?>
                    <span class="absolute -bottom-1.5 left-0 w-8 h-0.5 bg-accent rounded-full"></span>
                </h4>
                <ul class="space-y-2.5 text-sm">
                    <?php foreach ( $legal_links as $item ) : ?>
                        <li>
                            <a href="<?php echo esc_url( st_route_url( $item['path'] ) ); ?>" class="hover:text-accent transition-colors flex items-center gap-2 text-white/80 hover:translate-x-1 duration-200">
                                <?php echo st_icon( 'arrow-right', 'w-3 h-3 text-accent shrink-0' ); ?>
                                <span><?php echo esc_html( $item['name'] ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Newsletter & Socials -->
            <div class="sm:col-span-2 lg:col-span-1">
                <h4 class="text-white text-base sm:text-lg font-bold mb-5 relative inline-block">
                    <?php echo esc_html( $stay_title ); ?>
                    <span class="absolute -bottom-1.5 left-0 w-8 h-0.5 bg-accent rounded-full"></span>
                </h4>
                <p class="text-white/70 text-sm mb-4 leading-relaxed">
                    <?php echo esc_html( $nl_text ); ?>
                </p>

                <div id="st-footer-newsletter-success" class="hidden bg-primary/40 border border-primary text-white p-4 rounded-lg text-sm items-center gap-2">
                    <span class="font-bold"><?php esc_html_e( 'Thank you!', 'santa-towing' ); ?></span>
                    <span><?php echo esc_html( $nl_success ); ?></span>
                </div>

                <form id="st-footer-newsletter-form" class="space-y-3">
                    <input
                        required
                        type="email"
                        name="email"
                        placeholder="<?php echo esc_attr( $nl_placeholder ); ?>"
                        class="w-full bg-white/10 border border-white/15 rounded-lg px-4 py-3 text-white text-sm placeholder-white/50 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                    />
                    <button
                        type="submit"
                        class="w-full bg-accent hover:bg-accent/90 text-white font-bold px-4 py-3 text-sm transition-all shadow-md shadow-accent/25 hover:-translate-y-0.5 rounded-full"
                    >
                        <?php echo esc_html( $nl_button ); ?>
                    </button>
                </form>

                <div class="flex items-center gap-3 mt-6">
                    <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-white/80" aria-label="Facebook"><?php echo st_icon( 'facebook', 'w-4 h-4' ); ?></a>
                    <a href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-white/80" aria-label="Twitter"><?php echo st_icon( 'twitter', 'w-4 h-4' ); ?></a>
                    <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-white/80" aria-label="Instagram"><?php echo st_icon( 'instagram', 'w-4 h-4' ); ?></a>
                    <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-accent hover:text-white transition-all text-white/80" aria-label="LinkedIn"><?php echo st_icon( 'linkedin', 'w-4 h-4' ); ?></a>
                </div>
            </div>

        </div>

        <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-white/80 font-medium">
            <p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $copyright_text ); ?></p>
        </div>
    </div>
</footer>
