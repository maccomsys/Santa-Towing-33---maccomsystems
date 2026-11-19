<?php
/**
 * Single Auto Part CPT Template (`st_part`)
 *
 * 1:1 visual parity with React src/pages/PartDetails.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$phone_raw = st_get_content( 'global.links.phone', '0244753849' );
$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp  = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );

while ( have_posts() ) :
    the_post();
    $post_id  = get_the_ID();
    $name     = get_the_title();
    $brand    = get_post_meta( $post_id, '_st_brand', true ) ?: 'Genuine OEM';
    $category = get_post_meta( $post_id, '_st_category', true ) ?: 'Auto Parts';
    $price    = get_post_meta( $post_id, '_st_price', true ) ?: 'GH₵ 550';
    $sku      = get_post_meta( $post_id, '_st_sku', true ) ?: ( 'ST-PART-' . $post_id );
    $rating   = (float) ( get_post_meta( $post_id, '_st_rating', true ) ?: 5.0 );
    $reviews  = (int) ( get_post_meta( $post_id, '_st_reviews', true ) ?: 42 );
    $in_stock = get_post_meta( $post_id, '_st_in_stock', true );
    $in_stock = ( '' === $in_stock || '1' === $in_stock || true === $in_stock );
    $main_img = get_the_post_thumbnail_url( $post_id, 'full' ) ?: st_theme_image( 'image-9-car-battery-maintenance.jpg' );
    ?>
    <main class="pt-24 lg:pt-[104px] min-h-screen bg-white pb-24">
        <!-- Breadcrumb Navigation -->
        <div class="bg-white border-b border-dark/20 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center text-sm text-dark/70 overflow-x-auto no-scrollbar whitespace-nowrap">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Home', 'santa-towing' ); ?></a>
                    <span class="mx-2 shrink-0"><?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?></span>
                    <a href="<?php echo esc_url( st_route_url( '/parts' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Auto Parts', 'santa-towing' ); ?></a>
                    <span class="mx-2 shrink-0"><?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?></span>
                    <span class="text-dark font-medium truncate"><?php echo esc_html( $name ); ?></span>
                </div>
                <div class="mt-4">
                    <a href="<?php echo esc_url( st_route_url( '/parts' ) ); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-accent hover:text-dark transition-colors">
                        &larr; <?php esc_html_e( 'Back to Auto Parts', 'santa-towing' ); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 relative">

                <!-- Main Left Area -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-[1px] p-4 border border-dark/10 shadow-sm mb-12">
                        <div class="aspect-[4/3] md:aspect-video rounded-[1px] overflow-hidden bg-white mb-4 relative group">
                            <img src="<?php echo esc_url( $main_img ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                        </div>
                    </div>

                    <!-- Product Details Tabs -->
                    <div class="bg-white rounded-[1px] border border-dark/10 shadow-sm mb-12 overflow-hidden" data-st-tabs>
                        <div class="flex overflow-x-auto no-scrollbar border-b border-dark/10 bg-white/50">
                            <button type="button" data-st-tab-btn="specs" class="px-6 py-4 font-bold text-sm md:text-base whitespace-nowrap border-b-2 text-primary border-primary bg-white"><?php esc_html_e( 'Specifications', 'santa-towing' ); ?></button>
                            <button type="button" data-st-tab-btn="compat" class="px-6 py-4 font-bold text-sm md:text-base whitespace-nowrap border-b-2 text-dark/70 border-transparent hover:text-dark"><?php esc_html_e( 'Compatibility', 'santa-towing' ); ?></button>
                            <button type="button" data-st-tab-btn="warranty" class="px-6 py-4 font-bold text-sm md:text-base whitespace-nowrap border-b-2 text-dark/70 border-transparent hover:text-dark"><?php esc_html_e( 'Warranty', 'santa-towing' ); ?></button>
                            <button type="button" data-st-tab-btn="install" class="px-6 py-4 font-bold text-sm md:text-base whitespace-nowrap border-b-2 text-dark/70 border-transparent hover:text-dark"><?php esc_html_e( 'Installation Notes', 'santa-towing' ); ?></button>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div data-st-tab-panel="specs" class="prose max-w-none text-dark/80">
                                <?php the_content(); ?>
                            </div>
                            <div data-st-tab-panel="compat" class="hidden text-dark/70 leading-relaxed">
                                <?php esc_html_e( 'OEM and aftermarket fitment verified across major Toyota, Honda, Hyundai, Kia, Nissan, Mercedes-Benz, and Ford models. Call with your VIN for guaranteed compatibility check.', 'santa-towing' ); ?>
                            </div>
                            <div data-st-tab-panel="warranty" class="hidden flex items-start gap-4">
                                <?php echo st_icon( 'shield-check', 'w-8 h-8 text-accent shrink-0' ); ?>
                                <div>
                                    <h4 class="font-bold text-dark mb-2"><?php esc_html_e( 'Coverage Details', 'santa-towing' ); ?></h4>
                                    <p class="text-dark/70 leading-relaxed"><?php esc_html_e( 'All genuine parts come with manufacturer warranty protection and 14-day unused exchange policy.', 'santa-towing' ); ?></p>
                                </div>
                            </div>
                            <div data-st-tab-panel="install" class="hidden flex items-start gap-4">
                                <?php echo st_icon( 'wrench', 'w-8 h-8 text-primary shrink-0' ); ?>
                                <div>
                                    <h4 class="font-bold text-dark mb-2"><?php esc_html_e( 'Important Instructions', 'santa-towing' ); ?></h4>
                                    <p class="text-dark/70 leading-relaxed"><?php esc_html_e( 'Professional installation is available on-site at our Spintex Road diagnostic workshop.', 'santa-towing' ); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Installation Promo -->
                    <div class="bg-primary rounded-[1px] p-6 sm:p-8 md:p-12 text-white mb-12 relative overflow-hidden">
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                            <div>
                                <h3 class="text-2xl font-bold mb-2"><?php esc_html_e( 'Need Professional Installation?', 'santa-towing' ); ?></h3>
                                <p class="text-white/70"><?php esc_html_e( 'Get this part fitted by our certified mechanics at our workshop.', 'santa-towing' ); ?></p>
                            </div>
                            <a href="<?php echo esc_url( $phone_tel ); ?>" class="w-full md:w-auto bg-accent hover:bg-accent/90 text-white font-bold px-6 sm:px-8 py-4 transition-all whitespace-nowrap shadow-lg rounded-full text-center">
                                <?php esc_html_e( 'Call for Installation', 'santa-towing' ); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Sticky Summary Sidebar -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-[1px] p-6 sm:p-8 border border-dark/10 shadow-xl lg:sticky lg:top-32">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-sm font-bold text-accent tracking-wider uppercase"><?php echo esc_html( $brand ); ?></span>
                            <button type="button" data-st-copy-url="<?php echo esc_url( get_permalink() ); ?>" class="text-dark/70 hover:text-accent transition-colors rounded-full" aria-label="Share part">
                                <?php echo st_icon( 'share-2', 'w-5 h-5' ); ?>
                            </button>
                        </div>

                        <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2 leading-tight"><?php echo esc_html( $name ); ?></h1>

                        <div class="flex items-center gap-2 mb-6 text-sm">
                            <div class="flex text-accent">
                                <?php for ( $s = 1; $s <= 5; $s++ ) { echo st_icon( 'star-filled', 'w-4 h-4 text-accent' ); } ?>
                            </div>
                            <span class="text-dark/70">(<?php echo (int) $reviews; ?> reviews)</span>
                            <span class="mx-1 text-dark/30">|</span>
                            <span class="text-dark/70">SKU: <?php echo esc_html( $sku ); ?></span>
                        </div>

                        <div class="text-3xl font-black text-dark mb-4"><?php echo esc_html( $price ); ?></div>

                        <div class="flex items-center gap-2 mb-8">
                            <?php if ( $in_stock ) : ?>
                                <div class="flex items-center gap-1 text-accent font-bold text-sm bg-primary/5 px-3 py-1">
                                    <?php echo st_icon( 'check-circle', 'w-4 h-4' ); ?>
                                    <span><?php esc_html_e( 'In Stock & Ready to Ship', 'santa-towing' ); ?></span>
                                </div>
                            <?php else : ?>
                                <div class="flex items-center gap-1 text-accent font-bold text-sm bg-accent/5 px-3 py-1">
                                    <?php echo st_icon( 'info', 'w-4 h-4' ); ?>
                                    <span><?php esc_html_e( 'Order on Request', 'santa-towing' ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="space-y-3 mb-6">
                            <a href="<?php echo esc_url( $phone_tel ); ?>" class="w-full bg-accent hover:bg-accent/90 text-white font-bold py-4 transition-all shadow-md flex items-center justify-center gap-2 rounded-full">
                                <?php echo st_icon( 'phone', 'w-5 h-5' ); ?>
                                <span><?php echo esc_html( 'Call to Order: ' . $phone_raw ); ?></span>
                            </a>
                            <a href="<?php echo esc_url( $whatsapp . '?text=' . rawurlencode( "Hello Santa Towing, I would like to order {$name} (SKU: {$sku})." ) ); ?>" target="_blank" rel="noopener noreferrer" class="w-full bg-primary hover:bg-black text-white font-bold py-4 transition-all shadow-md flex items-center justify-center gap-2 rounded-full">
                                <?php echo st_icon( 'message-circle', 'w-5 h-5' ); ?>
                                <span><?php esc_html_e( 'Order via WhatsApp', 'santa-towing' ); ?></span>
                            </a>
                        </div>

                        <div class="space-y-3 text-sm text-dark/70">
                            <div class="flex items-center gap-3"><?php echo st_icon( 'shield-check', 'w-5 h-5 text-accent shrink-0' ); ?> <span><?php esc_html_e( 'Genuine Quality Guarantee', 'santa-towing' ); ?></span></div>
                            <div class="flex items-center gap-3"><?php echo st_icon( 'rotate-ccw', 'w-5 h-5 text-primary shrink-0' ); ?> <span><?php esc_html_e( '14-Day Easy Returns', 'santa-towing' ); ?></span></div>
                            <div class="flex items-center gap-3"><?php echo st_icon( 'truck', 'w-5 h-5 text-primary shrink-0' ); ?> <span><?php esc_html_e( 'Fast Delivery / Free Pickup', 'santa-towing' ); ?></span></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <?php
endwhile;

get_footer();
