<?php
/**
 * Single Rental CPT Template (`st_rental`)
 *
 * 1:1 visual parity with React src/pages/RentalDetails.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp  = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );

while ( have_posts() ) :
    the_post();
    $post_id      = get_the_ID();
    $name         = get_the_title();
    $daily        = get_post_meta( $post_id, '_st_daily_rate', true ) ?: 'GH₵ 600 / day';
    $type         = get_post_meta( $post_id, '_st_rental_type', true ) ?: 'Sedan';
    $seats        = get_post_meta( $post_id, '_st_seats', true ) ?: '5 Seats';
    $transmission = get_post_meta( $post_id, '_st_transmission', true ) ?: 'Automatic';
    $fuel         = get_post_meta( $post_id, '_st_fuel', true ) ?: 'Petrol';
    $main_img     = get_the_post_thumbnail_url( $post_id, 'full' ) ?: st_theme_image( 'image-3-sedan-toyota-corolla.jpg' );
    $gallery      = array(
        $main_img,
        st_theme_image( 'image-6-luxury-mercedes-benz.jpg' ),
        st_theme_image( 'image-2-suv-kia-sportage-2024.png' ),
        st_theme_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
    );

    $inclusions = array(
        'Comprehensive Fleet Insurance Coverage',
        'Unlimited Mileage within Greater Accra Metropolitan Area',
        'Optional Professional Uniformed Chauffeur / Driver',
        'Full Air Conditioning & Climate Control',
        'Bluetooth Audio & Hands-Free Connectivity',
        '24/7 Nationwide Emergency Roadside Assistance & Breakdown Replacement',
        'Clean, Sanitized & Full Tank on Pickup',
        'Baby / Child Safety Seat Available on Request',
    );
    ?>
    <main class="pt-24 lg:pt-[104px] bg-slate-50 min-h-screen">
        <div class="bg-white border-b border-gray-200/80 py-3.5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php
                st_breadcrumbs(
                    array(
                        array( 'label' => 'Car Rental', 'path' => '/rental' ),
                        array( 'label' => $type, 'path' => '/rental' ),
                        array( 'label' => $name ),
                    )
                );
                ?>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

                <!-- Main Column -->
                <div class="lg:col-span-8 space-y-8">
                    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200/80 shadow-sm space-y-4" data-st-gallery>
                        <div class="aspect-[16/10] relative rounded-lg overflow-hidden bg-slate-100 border border-gray-100">
                            <img src="<?php echo esc_url( $gallery[0] ); ?>" alt="<?php echo esc_attr( $name ); ?>" data-st-gallery-main class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" />
                            <div class="absolute top-4 left-4 bg-dark/85 backdrop-blur-md text-white text-xs font-bold px-3.5 py-1.5 rounded-full shadow-md">
                                <?php esc_html_e( 'Santa Towing Verified Fleet', 'santa-towing' ); ?>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 sm:gap-3">
                            <?php foreach ( $gallery as $idx => $gimg ) : ?>
                                <button type="button" data-st-gallery-thumb="<?php echo esc_url( $gimg ); ?>" class="aspect-video rounded-lg overflow-hidden border-2 transition-all bg-slate-100 <?php echo 0 === $idx ? 'border-accent ring-2 ring-accent/30 shadow-md' : 'border-transparent opacity-75 hover:opacity-100'; ?>">
                                    <img src="<?php echo esc_url( $gimg ); ?>" alt="<?php echo esc_attr( 'View ' . ( $idx + 1 ) ); ?>" class="w-full h-full object-cover" />
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-accent/10 text-accent flex items-center justify-center shrink-0"><?php echo st_icon( 'users', 'w-5 h-5' ); ?></div>
                            <div>
                                <div class="text-xs text-slate-500"><?php esc_html_e( 'Capacity', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $seats ); ?></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0"><?php echo st_icon( 'settings', 'w-5 h-5' ); ?></div>
                            <div>
                                <div class="text-xs text-slate-500"><?php esc_html_e( 'Transmission', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $transmission ); ?></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><?php echo st_icon( 'fuel', 'w-5 h-5' ); ?></div>
                            <div>
                                <div class="text-xs text-slate-500"><?php esc_html_e( 'Fuel', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $fuel ); ?></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><?php echo st_icon( 'shield-check', 'w-5 h-5' ); ?></div>
                            <div>
                                <div class="text-xs text-slate-500"><?php esc_html_e( 'Insurance', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php esc_html_e( 'Full Comprehensive', 'santa-towing' ); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 sm:p-8 space-y-6">
                        <div class="prose max-w-none text-slate-600">
                            <?php the_content(); ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-dark mb-3"><?php esc_html_e( 'Rental Inclusions & Benefits', 'santa-towing' ); ?></h2>
                            <div class="grid sm:grid-cols-2 gap-3.5">
                                <?php foreach ( $inclusions as $item ) : ?>
                                    <div class="flex items-start gap-3 bg-slate-50/80 p-3 rounded-lg border border-slate-100">
                                        <?php echo st_icon( 'check-circle', 'w-5 h-5 text-accent shrink-0 mt-0.5' ); ?>
                                        <span class="text-sm font-medium text-slate-700"><?php echo esc_html( $item ); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sticky Sidebar -->
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-gray-200 p-6 sm:p-7 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-accent via-red-500 to-primary"></div>

                            <div class="mb-4">
                                <h1 class="text-xl sm:text-2xl font-extrabold text-dark leading-tight mb-2"><?php echo esc_html( $name ); ?></h1>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500"><?php esc_html_e( 'Daily Rental Rate', 'santa-towing' ); ?></span>
                                <div class="text-3xl sm:text-4xl font-black text-accent mt-1 tracking-tight"><?php echo esc_html( $daily ); ?></div>
                                <div class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'Includes Comprehensive Insurance', 'santa-towing' ); ?></div>
                            </div>

                            <div class="space-y-3 mt-6">
                                <a href="<?php echo esc_url( $phone_tel ); ?>" class="w-full bg-accent hover:bg-accent/90 text-white font-bold py-3.5 px-4 rounded-full transition-all shadow-md flex justify-center items-center gap-2 text-sm sm:text-base">
                                    <?php echo st_icon( 'phone', 'w-5 h-5' ); ?>
                                    <span><?php esc_html_e( 'Call Hotline for Booking', 'santa-towing' ); ?></span>
                                </a>
                                <a href="<?php echo esc_url( $whatsapp . '?text=' . rawurlencode( "Hello Santa Towing, I would like to reserve {$name}." ) ); ?>" target="_blank" rel="noopener noreferrer" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 rounded-full transition-all shadow-md flex justify-center items-center gap-2 text-sm sm:text-base">
                                    <?php echo st_icon( 'message-circle', 'w-5 h-5' ); ?>
                                    <span><?php esc_html_e( 'WhatsApp Instant Booking', 'santa-towing' ); ?></span>
                                </a>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
                            <h3 class="font-bold text-dark text-base mb-3 flex items-center gap-2">
                                <?php echo st_icon( 'map-pin', 'w-4 h-4 text-accent' ); ?>
                                <span><?php esc_html_e( 'Fleet Station & Pickup', 'santa-towing' ); ?></span>
                            </h3>
                            <p class="text-xs text-slate-600 mb-3">
                                <?php esc_html_e( 'Airport pickup / drop-off available at Kotoka International Airport (ACC) on request.', 'santa-towing' ); ?>
                            </p>
                            <div class="text-xs text-slate-500 font-medium">
                                <?php esc_html_e( 'Main Depot: Spintex Road, Accra • 24/7 Dispatch', 'santa-towing' ); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <?php
endwhile;

get_footer();
