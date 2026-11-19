<?php
/**
 * Template Part: Service Area Page Template
 *
 * 1:1 visual parity with React src/components/sections/ServiceAreaPageTemplate.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$area_slug           = isset( $args['slug'] ) ? $args['slug'] : get_post_field( 'post_name', get_the_ID() );
$area_label          = isset( $args['area_label'] ) ? $args['area_label'] : get_the_title();
$default_heading     = isset( $args['heading'] ) ? $args['heading'] : get_the_title();
$default_description = isset( $args['description'] ) ? $args['description'] : wp_strip_all_tags( get_the_content() );
$prev_area           = isset( $args['prev_area'] ) ? $args['prev_area'] : null;
$next_area           = isset( $args['next_area'] ) ? $args['next_area'] : null;

$heading            = st_get_content( "serviceAreas.pages.{$area_slug}.heading", $default_heading );
$description        = st_get_content( "serviceAreas.pages.{$area_slug}.description", $default_description );
$cta_heading        = st_get_content( 'serviceAreas.template.ctaHeading', 'Need Immediate Assistance?' );
$cta_subtext        = st_get_content( 'serviceAreas.template.ctaSubtext', 'Call or WhatsApp us now to book your tow.' );
$call_btn_label     = st_get_content( 'serviceAreas.template.callBtnLabel', 'Call Now' );
$whatsapp_btn_label = st_get_content( 'serviceAreas.template.whatsappBtnLabel', 'WhatsApp Us' );
$prev_area_label    = st_get_content( 'serviceAreas.template.prevAreaLabel', 'Previous Area' );
$next_area_label    = st_get_content( 'serviceAreas.template.nextAreaLabel', 'Next Area' );
$phone_tel          = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp           = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
?>
<main class="pt-24 pb-16 md:pb-20 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <div class="mb-12">
            <?php
            st_breadcrumbs(
                array(
                    array( 'label' => 'Towing Services', 'path' => '/towing' ),
                    array( 'label' => 'Service Areas', 'path' => '/service-areas' ),
                    array( 'label' => $area_label ),
                )
            );
            ?>

            <div class="mt-8">
                <h1 class="text-3xl md:text-5xl font-bold text-dark mb-4">
                    <?php echo esc_html( $heading ); ?>
                </h1>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-8 md:p-12 rounded-[1px] border border-dark/10 shadow-sm mb-12">
            <?php if ( ! empty( $description ) ) : ?>
                <div class="text-dark/80 mb-6 leading-relaxed text-lg space-y-4">
                    <?php echo wp_kses_post( wpautop( $description ) ); ?>
                </div>
            <?php endif; ?>

            <div class="mt-8 p-6 bg-slate-50 rounded-[1px] border border-dark/5">
                <h2 class="text-xl font-bold text-dark mb-4"><?php echo esc_html( $cta_heading ); ?></h2>
                <p class="text-dark/70 mb-6"><?php echo esc_html( $cta_subtext ); ?></p>

                <div class="grid sm:grid-cols-2 gap-4">
                    <a
                        href="<?php echo esc_url( $phone_tel ); ?>"
                        class="flex items-center justify-center gap-3 bg-primary hover:bg-dark text-white px-6 py-4 font-bold transition-colors w-full rounded-full"
                    >
                        <?php echo st_icon( 'phone', 'w-5 h-5' ); ?>
                        <span><?php echo esc_html( $call_btn_label ); ?></span>
                    </a>
                    <a
                        href="<?php echo esc_url( $whatsapp ); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center gap-3 bg-primary hover:bg-dark text-white px-6 py-4 font-bold transition-colors w-full rounded-full"
                    >
                        <?php echo st_icon( 'message-circle', 'w-5 h-5' ); ?>
                        <span><?php echo esc_html( $whatsapp_btn_label ); ?></span>
                    </a>
                </div>
            </div>

            <?php if ( $prev_area || $next_area ) : ?>
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 pt-8 border-t border-dark/10">
                    <div class="w-full sm:w-1/2 flex justify-start">
                        <?php if ( $prev_area ) : ?>
                            <a
                                href="<?php echo esc_url( $prev_area['url'] ); ?>"
                                class="group flex items-center gap-3 hover:bg-slate-50 p-3 rounded-[1px] transition-colors max-w-full"
                            >
                                <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-full group-hover:bg-accent group-hover:text-white transition-colors">
                                    <?php echo st_icon( 'chevron-left', 'w-5 h-5' ); ?>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-xs text-dark/50 font-bold uppercase tracking-wider mb-0.5">
                                        <?php echo esc_html( $prev_area_label ); ?>
                                    </div>
                                    <div class="text-sm font-bold text-dark group-hover:text-accent truncate">
                                        <?php echo esc_html( $prev_area['label'] ); ?>
                                    </div>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="w-full sm:w-1/2 flex justify-end">
                        <?php if ( $next_area ) : ?>
                            <a
                                href="<?php echo esc_url( $next_area['url'] ); ?>"
                                class="group flex items-center gap-3 hover:bg-slate-50 p-3 rounded-[1px] transition-colors max-w-full text-right"
                            >
                                <div class="overflow-hidden">
                                    <div class="text-xs text-dark/50 font-bold uppercase tracking-wider mb-0.5">
                                        <?php echo esc_html( $next_area_label ); ?>
                                    </div>
                                    <div class="text-sm font-bold text-dark group-hover:text-accent truncate">
                                        <?php echo esc_html( $next_area['label'] ); ?>
                                    </div>
                                </div>
                                <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-full group-hover:bg-accent group-hover:text-white transition-colors">
                                    <?php echo st_icon( 'chevron-right', 'w-5 h-5' ); ?>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
