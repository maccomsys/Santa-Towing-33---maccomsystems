<?php
/**
 * Virtual Service-Area Sub-Service Template
 * Handles /service-areas/{areaSlug}/{serviceSlug} (emergency-towing, scheduled-towing, car-recovery, long-distance-towing).
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$route_data   = get_query_var( 'stwc_virtual_route', array() );
$area         = $route_data['area'] ?? array();
$service      = $route_data['service'] ?? array();
$area_label   = $area['area_label'] ?? 'Ghana';
$area_slug    = $area['slug'] ?? '';
$service_name = $service['title'] ?? 'Emergency Towing';
$tagline      = $service['tagline'] ?? 'Fast, reliable 24/7 roadside assistance.';
$eta          = $area['eta'] ?? '30-45 mins';

$phone_tel = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$wa_url    = stwc_build_whatsapp_url( "Hello Santa Towing, I need {$service_name} in {$area_label}." );
?>
<main class="pt-24 pb-16 md:pb-20 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <nav class="text-sm text-dark/60 flex flex-wrap items-center gap-2 mb-6" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-primary"><?php esc_html_e( 'Home', 'santa-towing-website-core' ); ?></a>
            <span>/</span>
            <a href="<?php echo esc_url( home_url( '/service-areas/' ) ); ?>" class="hover:text-primary"><?php esc_html_e( 'Service Areas', 'santa-towing-website-core' ); ?></a>
            <span>/</span>
            <a href="<?php echo esc_url( home_url( '/service-areas/' . $area_slug . '/' ) ); ?>" class="hover:text-primary"><?php echo esc_html( $area_label ); ?></a>
            <span>/</span>
            <span class="font-bold text-dark"><?php echo esc_html( $service_name ); ?></span>
        </nav>

        <div class="bg-white p-6 sm:p-8 md:p-12 rounded-[1px] border border-dark/10 shadow-sm">
            <span class="inline-block bg-primary text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-[1px] mb-3">
                <?php echo esc_html( $area_label . ' • ETA ' . $eta ); ?>
            </span>
            <h1 class="text-3xl md:text-5xl font-bold text-dark mb-4">
                <?php echo esc_html( "{$service_name} in {$area_label}" ); ?>
            </h1>
            <p class="text-lg text-dark/80 leading-relaxed mb-8">
                <?php echo esc_html( $tagline . ' ' . ( $area['description'] ?? '' ) ); ?>
            </p>

            <div class="p-6 bg-slate-50 rounded-[1px] border border-dark/10 mb-8">
                <h2 class="text-xl font-bold text-dark mb-2"><?php echo esc_html( "Book {$service_name} in {$area_label}" ); ?></h2>
                <p class="text-dark/70 mb-6"><?php esc_html_e( 'Our GPS-tracked trucks are stationed nearby for rapid, damage-free dispatch.', 'santa-towing-website-core' ); ?></p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <a href="<?php echo esc_url( $phone_tel ); ?>" class="flex items-center justify-center gap-3 bg-primary hover:bg-dark text-white px-6 py-4 font-bold transition-colors w-full rounded-full">
                        <?php echo stwc_icon( 'phone', 'w-5 h-5' ); ?>
                        <span><?php esc_html_e( 'Call Dispatch Now', 'santa-towing-website-core' ); ?></span>
                    </a>
                    <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-3 bg-accent hover:bg-dark text-white px-6 py-4 font-bold transition-colors w-full rounded-full">
                        <?php echo stwc_icon( 'message-circle', 'w-5 h-5' ); ?>
                        <span><?php esc_html_e( 'Send Location on WhatsApp', 'santa-towing-website-core' ); ?></span>
                    </a>
                </div>
            </div>

            <a href="<?php echo esc_url( home_url( '/service-areas/' . $area_slug . '/' ) ); ?>" class="text-sm font-bold text-primary hover:underline">
                &larr; <?php echo esc_html( sprintf( __( 'Back to all towing services in %s', 'santa-towing-website-core' ), $area_label ) ); ?>
            </a>
        </div>
    </div>
</main>
<?php
get_footer();
