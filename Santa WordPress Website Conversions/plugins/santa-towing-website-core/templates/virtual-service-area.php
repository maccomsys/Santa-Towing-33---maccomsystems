<?php
/**
 * Virtual Service Area Detail Template (for un-imported districts/towns & all 232 Ghana localities)
 *
 * Mirrors theme `template-parts/service-area-template.php` cleanly when no WP CPT row exists yet.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$route_data = get_query_var( 'stwc_virtual_route', array() );
$area       = $route_data['area'] ?? array();
$slug       = $area['slug'] ?? '';
$area_label = $area['area_label'] ?? 'Ghana';
$heading    = $area['heading'] ?? '24/7 Towing Service in ' . $area_label;
$desc       = $area['description'] ?? '';
$region     = $area['region'] ?? 'Ghana';
$eta        = $area['eta'] ?? '30-45 mins';
$landmarks  = $area['landmarks'] ?? array();

$phone_tel  = stwc_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp   = stwc_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
$sub_links  = stwc_get_area_sub_services();
?>
<main class="pt-24 pb-16 md:pb-20 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <div class="mb-8">
            <nav class="text-sm text-dark/60 flex flex-wrap items-center gap-2 mb-6" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-primary"><?php esc_html_e( 'Home', 'santa-towing-website-core' ); ?></a>
                <span>/</span>
                <a href="<?php echo esc_url( home_url( '/service-areas/' ) ); ?>" class="hover:text-primary"><?php esc_html_e( 'Service Areas', 'santa-towing-website-core' ); ?></a>
                <span>/</span>
                <span class="font-bold text-dark"><?php echo esc_html( $area_label ); ?></span>
            </nav>

            <span class="inline-block bg-accent text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-[1px] mb-3">
                <?php echo esc_html( $region . ' • ETA ' . $eta ); ?>
            </span>
            <h1 class="text-3xl md:text-5xl font-bold text-dark mb-4"><?php echo esc_html( $heading ); ?></h1>
        </div>

        <div class="bg-white p-6 sm:p-8 md:p-12 rounded-[1px] border border-dark/10 shadow-sm mb-10">
            <div class="text-dark/80 mb-6 leading-relaxed text-lg">
                <?php echo wp_kses_post( wpautop( $desc ) ); ?>
            </div>

            <?php if ( ! empty( $landmarks ) ) : ?>
                <div class="mb-8">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-dark/60 mb-3"><?php esc_html_e( 'Key Coverage Landmarks & Corridors', 'santa-towing-website-core' ); ?></h2>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ( $landmarks as $lm ) : ?>
                            <span class="bg-slate-100 text-dark px-3 py-1.5 text-sm font-semibold rounded-[1px]"><?php echo esc_html( $lm ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-8">
                <h2 class="text-lg font-bold text-dark mb-3"><?php esc_html_e( 'Specialized Towing Services in This Area', 'santa-towing-website-core' ); ?></h2>
                <div class="grid sm:grid-cols-2 gap-3">
                    <?php foreach ( $sub_links as $s_slug => $s_meta ) : ?>
                        <a href="<?php echo esc_url( home_url( '/service-areas/' . $slug . '/' . $s_slug . '/' ) ); ?>" class="p-4 border border-dark/10 hover:border-accent rounded-[1px] bg-slate-50 hover:bg-white transition-colors block">
                            <div class="font-bold text-primary"><?php echo esc_html( $s_meta['title'] . ' — ' . $area_label ); ?></div>
                            <div class="text-xs text-dark/70 mt-1"><?php echo esc_html( $s_meta['tagline'] ); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="p-6 bg-slate-50 rounded-[1px] border border-dark/5">
                <h2 class="text-xl font-bold text-dark mb-2"><?php echo esc_html( stwc_get_content( 'serviceAreas.template.ctaHeading', 'Need Immediate Assistance?' ) ); ?></h2>
                <p class="text-dark/70 mb-6"><?php echo esc_html( stwc_get_content( 'serviceAreas.template.ctaSubtext', 'Call or WhatsApp us now to book your tow.' ) ); ?></p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <a href="<?php echo esc_url( $phone_tel ); ?>" class="flex items-center justify-center gap-3 bg-primary hover:bg-dark text-white px-6 py-4 font-bold transition-colors w-full rounded-full">
                        <?php echo stwc_icon( 'phone', 'w-5 h-5' ); ?>
                        <span><?php echo esc_html( stwc_get_content( 'serviceAreas.template.callBtnLabel', 'Call Now' ) ); ?></span>
                    </a>
                    <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-3 bg-accent hover:bg-dark text-white px-6 py-4 font-bold transition-colors w-full rounded-full">
                        <?php echo stwc_icon( 'message-circle', 'w-5 h-5' ); ?>
                        <span><?php echo esc_html( stwc_get_content( 'serviceAreas.template.whatsappBtnLabel', 'WhatsApp Us' ) ); ?></span>
                    </a>
                </div>
            </div>

            <?php if ( ! empty( $area['prev_path'] ) || ! empty( $area['next_path'] ) ) : ?>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 pt-8 border-t border-dark/10">
                    <div>
                        <?php if ( ! empty( $area['prev_path'] ) ) : ?>
                            <a href="<?php echo esc_url( stwc_route_url( $area['prev_path'] ) ); ?>" class="text-sm font-bold text-dark hover:text-accent">
                                &larr; <?php echo esc_html( $area['prev_label'] ?: __( 'Previous Area', 'santa-towing-website-core' ) ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ( ! empty( $area['next_path'] ) ) : ?>
                            <a href="<?php echo esc_url( stwc_route_url( $area['next_path'] ) ); ?>" class="text-sm font-bold text-dark hover:text-accent">
                                <?php echo esc_html( $area['next_label'] ?: __( 'Next Area', 'santa-towing-website-core' ) ); ?> &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php
get_footer();
