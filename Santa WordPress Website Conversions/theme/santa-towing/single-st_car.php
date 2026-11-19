<?php
/**
 * Single Car Sales CPT Template (`st_car`)
 *
 * 1:1 visual parity with React src/pages/CarDetails.tsx
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
    $post_id      = get_the_ID();
    $car_name     = get_the_title();
    $price        = get_post_meta( $post_id, '_st_price', true ) ?: 'Contact for Price';
    $car_type     = get_post_meta( $post_id, '_st_car_type', true ) ?: 'New';
    $year         = get_post_meta( $post_id, '_st_year', true ) ?: '2024';
    $mileage      = get_post_meta( $post_id, '_st_mileage', true ) ?: '0 km';
    $fuel         = get_post_meta( $post_id, '_st_fuel', true ) ?: 'Hybrid';
    $transmission = get_post_meta( $post_id, '_st_transmission', true ) ?: 'Automatic';
    $main_img     = get_the_post_thumbnail_url( $post_id, 'full' ) ?: st_theme_image( 'image-6-luxury-mercedes-benz.jpg' );
    $gallery_meta = get_post_meta( $post_id, '_st_gallery', true );
    $gallery      = is_array( $gallery_meta ) && ! empty( $gallery_meta ) ? $gallery_meta : array(
        $main_img,
        st_theme_image( 'image-2-suv-kia-sportage-2024.png' ),
        st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ),
        st_theme_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
        st_theme_image( 'image-4-van-hyundai-h1-2022.jpg' ),
    );

    $specs_meta = get_post_meta( $post_id, '_st_specs', true );
    $features   = ( is_array( $specs_meta ) && ! empty( $specs_meta['features'] ) ) ? $specs_meta['features'] : array(
        'Apple CarPlay & Android Auto Smartphone Integration',
        'Surround Audio System',
        'Heated & Power Front Seats',
        'Active Blind Spot Assist & Lane Keeping Assist',
        '360-Degree Surround Camera & Parking Sensors',
        'LED Headlamps & Daytime Running Lights',
        'Dual-Zone Automatic Climate Control System',
        'Keyless-GO Push Button Start',
    );

    $history_items = array(
        array( 'label' => '150-Point Technical Inspection', 'value' => 'Passed All Mechanical & Electronic Tests' ),
        array( 'label' => 'Accident & Damage History', 'value' => 'Zero Reported Accidents & No Structural Damage' ),
        array( 'label' => 'Service & Maintenance Records', 'value' => 'Full Comprehensive Dealer Service History' ),
        array( 'label' => 'Ownership Verification', 'value' => 'Verified Clean Ownership History' ),
        array( 'label' => 'Title & Documentation Check', 'value' => 'Clean Title • Free of Financial Encumbrances' ),
        array( 'label' => 'Ghana Customs Duty & Clearance', 'value' => 'Fully Cleared & Validated at Tema Port' ),
    );

    $car_faqs = array(
        array( 'q' => 'Can I arrange a physical viewing and test drive?', 'a' => 'Yes! Test drives and physical car viewings can be scheduled Monday through Saturday at our Spintex Road showroom in Accra. Simply call our sales team or click "WhatsApp Us" to reserve your preferred viewing time.' ),
        array( 'q' => 'Do you offer financing or flexible payment plans?', 'a' => 'Yes, we collaborate with leading partner financial institutions in Ghana to provide competitive car financing options. We also offer customized installment arrangements for verified corporate clients and fleet buyers.' ),
        array( 'q' => 'What warranty is included with this car?', 'a' => 'Every car sold through Santa Towing & Garage Services comes with a standard 3-month or 5,000 km warranty covering major engine and transmission components.' ),
        array( 'q' => 'Do you deliver cars to other regions in Ghana?', 'a' => 'Yes, we provide nationwide door-to-door car delivery across all 16 regions of Ghana using our secure hydraulic flatbed carriers.' ),
    );

    $prev_car = get_previous_post();
    $next_car = get_next_post();
    ?>
    <main class="pt-24 lg:pt-[104px] bg-slate-50 min-h-screen">
        <!-- Breadcrumb Bar -->
        <div class="bg-white border-b border-gray-200/80 py-3.5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php
                st_breadcrumbs(
                    array(
                        array( 'label' => 'Car Sales', 'path' => '/sales' ),
                        array( 'label' => $car_type, 'path' => '/sales' ),
                        array( 'label' => $car_name ),
                    )
                );
                ?>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

                <!-- LEFT COLUMN -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- Gallery Showcase -->
                    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200/80 shadow-sm space-y-4" data-st-gallery>
                        <div class="aspect-[16/10] relative rounded-lg overflow-hidden bg-slate-100 group border border-gray-100">
                            <img
                                src="<?php echo esc_url( $gallery[0] ); ?>"
                                alt="<?php echo esc_attr( $car_name ); ?>"
                                data-st-gallery-main
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="bg-dark/85 backdrop-blur-md text-white text-xs font-bold px-3.5 py-1.5 rounded-full shadow-md flex items-center gap-1.5">
                                    <?php echo st_icon( 'eye', 'w-3.5 h-3.5 text-accent' ); ?>
                                    <span><?php esc_html_e( 'HD Gallery', 'santa-towing' ); ?></span>
                                </span>
                            </div>
                            <div class="absolute bottom-4 right-4 bg-dark/80 backdrop-blur-md text-white/90 text-xs font-medium px-3 py-1.5 rounded-full">
                                <?php esc_html_e( 'Santa Towing Verified Car', 'santa-towing' ); ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                <?php printf( esc_html__( 'Photo Gallery (%d Views)', 'santa-towing' ), count( $gallery ) ); ?>
                            </div>
                            <div class="grid grid-cols-5 gap-2 sm:gap-3">
                                <?php foreach ( $gallery as $idx => $gimg ) : ?>
                                    <button
                                        type="button"
                                        data-st-gallery-thumb="<?php echo esc_url( $gimg ); ?>"
                                        class="aspect-video rounded-lg overflow-hidden border-2 transition-all duration-200 relative bg-slate-100 focus:outline-none <?php echo 0 === $idx ? 'border-accent ring-2 ring-accent/30 shadow-md' : 'border-transparent opacity-75 hover:opacity-100'; ?>"
                                        aria-label="<?php echo esc_attr( 'View photo ' . ( $idx + 1 ) ); ?>"
                                    >
                                        <img src="<?php echo esc_url( $gimg ); ?>" alt="<?php echo esc_attr( 'Thumbnail ' . ( $idx + 1 ) ); ?>" class="w-full h-full object-cover" loading="lazy" />
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick-Spec Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-accent/10 flex items-center justify-center text-accent shrink-0">
                                <?php echo st_icon( 'calendar', 'w-5 h-5' ); ?>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 font-medium"><?php esc_html_e( 'Model Year', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $year ); ?></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                <?php echo st_icon( 'clock', 'w-5 h-5' ); ?>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 font-medium"><?php esc_html_e( 'Mileage', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $mileage ); ?></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                                <?php echo st_icon( 'fuel', 'w-5 h-5' ); ?>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 font-medium"><?php esc_html_e( 'Fuel System', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $fuel ); ?></div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                                <?php echo st_icon( 'settings', 'w-5 h-5' ); ?>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 font-medium"><?php esc_html_e( 'Transmission', 'santa-towing' ); ?></div>
                                <div class="font-extrabold text-dark text-sm sm:text-base"><?php echo esc_html( $transmission ); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Tabs -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden" data-st-tabs>
                        <div class="flex border-b border-gray-200 overflow-x-auto no-scrollbar bg-slate-50/50">
                            <button type="button" data-st-tab-btn="overview" class="px-5 sm:px-7 py-4 font-bold text-sm sm:text-base whitespace-nowrap flex items-center gap-2 border-b-2 text-accent border-accent bg-white shadow-sm">
                                <?php echo st_icon( 'file-text', 'w-4 h-4' ); ?>
                                <span><?php esc_html_e( 'Car Overview', 'santa-towing' ); ?></span>
                            </button>
                            <button type="button" data-st-tab-btn="specs" class="px-5 sm:px-7 py-4 font-bold text-sm sm:text-base whitespace-nowrap flex items-center gap-2 border-b-2 text-slate-600 border-transparent hover:text-dark">
                                <?php echo st_icon( 'settings', 'w-4 h-4' ); ?>
                                <span><?php esc_html_e( 'Technical Specifications', 'santa-towing' ); ?></span>
                            </button>
                            <button type="button" data-st-tab-btn="history" class="px-5 sm:px-7 py-4 font-bold text-sm sm:text-base whitespace-nowrap flex items-center gap-2 border-b-2 text-slate-600 border-transparent hover:text-dark">
                                <?php echo st_icon( 'shield-check', 'w-4 h-4' ); ?>
                                <span><?php esc_html_e( '150-Pt Inspection & History', 'santa-towing' ); ?></span>
                            </button>
                            <button type="button" data-st-tab-btn="calculator" class="px-5 sm:px-7 py-4 font-bold text-sm sm:text-base whitespace-nowrap flex items-center gap-2 border-b-2 text-slate-600 border-transparent hover:text-dark">
                                <?php echo st_icon( 'calculator', 'w-4 h-4' ); ?>
                                <span><?php esc_html_e( 'Financing Calculator', 'santa-towing' ); ?></span>
                            </button>
                        </div>

                        <div class="p-6 sm:p-8">
                            <div data-st-tab-panel="overview" class="space-y-6">
                                <div>
                                    <h2 class="text-xl font-bold text-dark mb-3"><?php esc_html_e( 'Car Overview & Description', 'santa-towing' ); ?></h2>
                                    <div class="text-slate-600 leading-relaxed text-base prose max-w-none">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                                <div class="border-t border-gray-100 pt-6">
                                    <h3 class="text-lg font-bold text-dark mb-4"><?php esc_html_e( 'Key Features & Equipment', 'santa-towing' ); ?></h3>
                                    <div class="grid sm:grid-cols-2 gap-3.5">
                                        <?php foreach ( $features as $feat ) : ?>
                                            <div class="flex items-start gap-3 bg-slate-50/80 p-3 rounded-lg border border-slate-100">
                                                <?php echo st_icon( 'check-circle', 'w-5 h-5 text-accent shrink-0 mt-0.5' ); ?>
                                                <span class="text-sm font-medium text-slate-700"><?php echo esc_html( $feat ); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="bg-primary/5 rounded-xl p-5 sm:p-6 border border-primary/15 flex flex-col sm:flex-row gap-4 items-start">
                                    <div class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center shrink-0">
                                        <?php echo st_icon( 'award', 'w-6 h-6' ); ?>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-dark text-base"><?php esc_html_e( 'Santa Towing Peace of Mind Guarantee', 'santa-towing' ); ?></h4>
                                        <p class="text-slate-600 text-sm leading-relaxed">
                                            <?php esc_html_e( 'Every car purchased includes a 3-month mechanical warranty, free engine oil service upon delivery, and 1 full year of 24/7 complimentary roadside assistance across Ghana.', 'santa-towing' ); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div data-st-tab-panel="specs" class="hidden space-y-4">
                                <h2 class="text-xl font-bold text-dark"><?php esc_html_e( 'Detailed Technical Specifications', 'santa-towing' ); ?></h2>
                                <div class="bg-slate-50/70 rounded-xl p-5 border border-slate-200/70 grid sm:grid-cols-2 gap-x-8 gap-y-3">
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-200/70">
                                        <span class="text-sm text-slate-600 font-medium">Condition</span>
                                        <span class="text-sm font-bold text-dark"><?php echo esc_html( $car_type ); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-200/70">
                                        <span class="text-sm text-slate-600 font-medium">Model Year</span>
                                        <span class="text-sm font-bold text-dark"><?php echo esc_html( $year ); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-200/70">
                                        <span class="text-sm text-slate-600 font-medium">Fuel System</span>
                                        <span class="text-sm font-bold text-dark"><?php echo esc_html( $fuel ); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-200/70">
                                        <span class="text-sm text-slate-600 font-medium">Transmission</span>
                                        <span class="text-sm font-bold text-dark"><?php echo esc_html( $transmission ); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-200/70">
                                        <span class="text-sm text-slate-600 font-medium">Mileage</span>
                                        <span class="text-sm font-bold text-dark"><?php echo esc_html( $mileage ); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 border-b border-gray-200/70">
                                        <span class="text-sm text-slate-600 font-medium">Warranty</span>
                                        <span class="text-sm font-bold text-dark">3 Months / 5,000 km</span>
                                    </div>
                                </div>
                            </div>

                            <div data-st-tab-panel="history" class="hidden space-y-6">
                                <div>
                                    <h2 class="text-xl font-bold text-dark mb-2"><?php esc_html_e( '150-Point Inspection & Car History', 'santa-towing' ); ?></h2>
                                    <p class="text-slate-600 text-sm"><?php esc_html_e( 'Full inspection conducted at Santa Towing Diagnostic Center. All systems verified and certified ready for delivery.', 'santa-towing' ); ?></p>
                                </div>
                                <div class="space-y-3">
                                    <?php foreach ( $history_items as $hitem ) : ?>
                                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-3.5">
                                                <div class="w-9 h-9 rounded-full bg-accent/10 text-accent flex items-center justify-center shrink-0">
                                                    <?php echo st_icon( 'shield-check', 'w-5 h-5' ); ?>
                                                </div>
                                                <span class="font-bold text-dark text-sm sm:text-base"><?php echo esc_html( $hitem['label'] ); ?></span>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60">
                                                <?php echo st_icon( 'check-circle', 'w-3.5 h-3.5 text-emerald-600' ); ?>
                                                <span><?php echo esc_html( $hitem['value'] ); ?></span>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div data-st-tab-panel="calculator" class="hidden space-y-6">
                                <div>
                                    <h2 class="text-xl font-bold text-dark mb-2"><?php esc_html_e( 'Car Financing Estimate', 'santa-towing' ); ?></h2>
                                    <p class="text-slate-600 text-sm"><?php esc_html_e( 'Calculate estimated monthly installments based on partner bank lending rates in Ghana.', 'santa-towing' ); ?></p>
                                </div>
                                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 space-y-4">
                                    <div class="grid sm:grid-cols-3 gap-4">
                                        <div class="bg-white p-4 rounded-lg border border-slate-200">
                                            <div class="text-xs text-slate-500 font-medium mb-1"><?php esc_html_e( 'Car Price', 'santa-towing' ); ?></div>
                                            <div class="text-lg font-bold text-dark"><?php echo esc_html( $price ); ?></div>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg border border-slate-200">
                                            <div class="text-xs text-slate-500 font-medium mb-1"><?php esc_html_e( 'Down Payment (30%)', 'santa-towing' ); ?></div>
                                            <div class="text-lg font-bold text-dark"><?php esc_html_e( 'Estimated 30% Deposit', 'santa-towing' ); ?></div>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg border border-slate-200">
                                            <div class="text-xs text-slate-500 font-medium mb-1"><?php esc_html_e( 'Flexible Loan Tenor', 'santa-towing' ); ?></div>
                                            <div class="text-lg font-bold text-accent"><?php esc_html_e( '12 to 48 Months', 'santa-towing' ); ?></div>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-primary/5 rounded-lg border border-primary/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="text-sm text-slate-700">
                                            <span class="font-bold text-dark"><?php esc_html_e( 'Want a customized finance pre-approval?', 'santa-towing' ); ?></span>
                                            <?php esc_html_e( 'Our car financing specialist can assist with bank paperwork within 24 hours.', 'santa-towing' ); ?>
                                        </div>
                                        <a href="<?php echo esc_url( $whatsapp . '?text=' . rawurlencode( 'Hello Santa Towing, I would like financing information for the car: ' . $car_name ) ); ?>" target="_blank" rel="noopener noreferrer" class="px-5 py-2.5 bg-primary text-white font-bold text-xs sm:text-sm rounded-full whitespace-nowrap shadow-sm hover:bg-primary/90">
                                            <?php esc_html_e( 'Request Loan Quote', 'santa-towing' ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buyer FAQs Accordion -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 sm:p-8" data-st-faq-group>
                        <div class="flex items-center gap-2.5 mb-6">
                            <div class="w-9 h-9 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                                <?php echo st_icon( 'info', 'w-5 h-5' ); ?>
                            </div>
                            <h2 class="text-xl font-bold text-dark"><?php esc_html_e( 'Buyer Questions & Answers', 'santa-towing' ); ?></h2>
                        </div>
                        <div class="space-y-3">
                            <?php foreach ( $car_faqs as $fidx => $faq ) : ?>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <button type="button" data-st-accordion-toggle aria-expanded="<?php echo 0 === $fidx ? 'true' : 'false'; ?>" class="w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 transition-colors">
                                        <span class="font-bold text-dark text-sm sm:text-base pr-4"><?php echo esc_html( $faq['q'] ); ?></span>
                                        <span class="st-accordion-icon text-accent shrink-0 transition-transform"><?php echo st_icon( 'chevron-down', 'w-5 h-5' ); ?></span>
                                    </button>
                                    <div data-st-accordion-panel class="st-accordion-panel <?php echo 0 === $fidx ? 'is-open' : ''; ?>">
                                        <div class="px-5 pb-5 pt-1 bg-slate-50/70 text-slate-600 text-sm leading-relaxed border-t border-gray-100">
                                            <?php echo esc_html( $faq['a'] ); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Prev / Next Navigation -->
                    <div class="bg-white rounded-xl p-5 border border-gray-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="w-full sm:w-1/2">
                            <?php if ( $prev_car ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $prev_car ) ); ?>" class="group flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                    <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-full group-hover:bg-accent group-hover:text-white transition-colors">
                                        <?php echo st_icon( 'chevron-left', 'w-5 h-5' ); ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider"><?php esc_html_e( 'Previous Listing', 'santa-towing' ); ?></div>
                                        <div class="text-sm font-bold text-dark group-hover:text-accent truncate"><?php echo esc_html( get_the_title( $prev_car ) ); ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="w-full sm:w-1/2 flex justify-end">
                            <?php if ( $next_car ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $next_car ) ); ?>" class="group flex items-center justify-end gap-3 p-3 rounded-lg hover:bg-slate-50 transition-colors text-right">
                                    <div class="overflow-hidden">
                                        <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider"><?php esc_html_e( 'Next Listing', 'santa-towing' ); ?></div>
                                        <div class="text-sm font-bold text-dark group-hover:text-accent truncate"><?php echo esc_html( get_the_title( $next_car ) ); ?></div>
                                    </div>
                                    <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-full group-hover:bg-accent group-hover:text-white transition-colors">
                                        <?php echo st_icon( 'chevron-right', 'w-5 h-5' ); ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- RIGHT STICKY SIDEBAR -->
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-gray-200 p-6 sm:p-7 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-accent via-red-500 to-primary"></div>

                            <div class="mb-4">
                                <div class="flex justify-between items-start gap-3 mb-2">
                                    <h1 class="text-xl sm:text-2xl font-extrabold text-dark leading-tight"><?php echo esc_html( $car_name ); ?></h1>
                                    <button type="button" data-st-copy-url="<?php echo esc_url( get_permalink() ); ?>" class="p-2 rounded-full bg-slate-50 border border-gray-200 text-slate-600 hover:bg-slate-100 hover:text-dark shrink-0" aria-label="Share listing">
                                        <?php echo st_icon( 'share-2', 'w-4 h-4' ); ?>
                                    </button>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500"><?php esc_html_e( 'Drive-Away Price', 'santa-towing' ); ?></span>
                                <div class="text-3xl sm:text-4xl font-black text-accent mt-1 tracking-tight"><?php echo esc_html( $price ); ?></div>
                                <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500 font-medium">
                                    <?php echo st_icon( 'check-circle', 'w-3.5 h-3.5 text-emerald-600' ); ?>
                                    <span><?php esc_html_e( 'Includes Port Clearance & Duty', 'santa-towing' ); ?></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5 py-4 my-4 border-y border-gray-100 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                    <span class="text-slate-500 block">Condition</span>
                                    <span class="font-bold text-dark"><?php echo esc_html( $car_type ); ?></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                    <span class="text-slate-500 block">Transmission</span>
                                    <span class="font-bold text-dark"><?php echo esc_html( $transmission ); ?></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                    <span class="text-slate-500 block">Fuel Type</span>
                                    <span class="font-bold text-dark"><?php echo esc_html( $fuel ); ?></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                    <span class="text-slate-500 block">Mileage</span>
                                    <span class="font-bold text-dark"><?php echo esc_html( $mileage ); ?></span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <a href="<?php echo esc_url( $phone_tel ); ?>" class="w-full bg-accent hover:bg-accent/90 text-white font-bold py-3.5 px-4 rounded-full transition-all shadow-md flex justify-center items-center gap-2 text-sm sm:text-base">
                                    <?php echo st_icon( 'phone', 'w-5 h-5' ); ?>
                                    <span><?php esc_html_e( 'Call Sales Hotline', 'santa-towing' ); ?></span>
                                </a>
                                <a href="<?php echo esc_url( $whatsapp . '?text=' . rawurlencode( "Hello Santa Towing, I am interested in the {$car_name} priced at {$price}. Is it still available?" ) ); ?>" target="_blank" rel="noopener noreferrer" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 rounded-full transition-all shadow-md flex justify-center items-center gap-2 text-sm sm:text-base">
                                    <?php echo st_icon( 'message-circle', 'w-5 h-5' ); ?>
                                    <span><?php esc_html_e( 'Chat on WhatsApp', 'santa-towing' ); ?></span>
                                </a>
                            </div>

                            <div class="mt-4 pt-3 text-center">
                                <div class="text-[11px] text-slate-500 flex items-center justify-center gap-1">
                                    <?php echo st_icon( 'shield', 'w-3.5 h-3.5 text-accent' ); ?>
                                    <span><?php esc_html_e( 'Official Santa Towing & Garage Services Dealership', 'santa-towing' ); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-100/80 rounded-xl p-5 border border-slate-200/80 text-xs text-slate-600 space-y-2.5">
                            <div class="flex items-center gap-2 font-bold text-dark text-sm mb-1">
                                <?php echo st_icon( 'shield-check', 'w-4 h-4 text-accent' ); ?>
                                <span><?php esc_html_e( 'Buyer Protection Guaranteed', 'santa-towing' ); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php echo st_icon( 'check', 'w-3.5 h-3.5 text-emerald-600 shrink-0' ); ?>
                                <span><?php esc_html_e( 'Standard 3-Month Mechanical Warranty', 'santa-towing' ); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php echo st_icon( 'check', 'w-3.5 h-3.5 text-emerald-600 shrink-0' ); ?>
                                <span><?php esc_html_e( 'Full GRA Customs Port Clearance', 'santa-towing' ); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php echo st_icon( 'check', 'w-3.5 h-3.5 text-emerald-600 shrink-0' ); ?>
                                <span><?php esc_html_e( 'Nationwide Flatbed Delivery Available', 'santa-towing' ); ?></span>
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
