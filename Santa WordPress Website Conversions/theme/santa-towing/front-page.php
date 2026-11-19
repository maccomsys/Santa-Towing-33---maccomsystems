<?php
/**
 * Front Page Template
 *
 * If the front page is built with Elementor Free, renders full-bleed Elementor content.
 * Otherwise renders 1:1 visual parity with React src/pages/Home.tsx immediately upon theme activation.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

if ( have_posts() && st_is_elementor_page( get_queried_object_id() ) ) :
    echo '<main id="primary" class="min-h-screen">';
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    echo '</main>';
else :
    $phone     = st_get_content( 'global.links.phone', '0244753849' );
    $phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
    $whatsapp  = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );

    // 1. Hero Slides
    $slides = array(
        array(
            'headline'    => '24/7 Emergency Towing Services',
            'subheadline' => 'Fast response, safe car transport, affordability, and reliability. Call us anytime, anywhere.',
            'image'       => st_theme_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ),
            'buttons'     => array(
                array( 'label' => 'Call Now: ' . $phone, 'href' => $phone_tel, 'type' => 'phone' ),
                array( 'label' => 'WhatsApp Us', 'href' => $whatsapp, 'type' => 'whatsapp' ),
            ),
        ),
        array(
            'headline'    => 'Accident Recovery & Roadside Assistance',
            'subheadline' => 'Professional winch-outs, flat tire changes, jump starts, and safe off-road recovery.',
            'image'       => st_theme_image( 'image-9-car-battery-maintenance.jpg' ),
            'buttons'     => array(
                array( 'label' => 'Request Assistance', 'href' => $phone_tel, 'type' => 'primary' ),
            ),
        ),
        array(
            'headline'    => 'Flatbed Towing & Car Transport',
            'subheadline' => 'Secure and damage-free transport for luxury cars, SUVs, and specialized cars.',
            'image'       => st_theme_image( 'image-10-engine-oil-fluids-maintenance.jpg' ),
            'buttons'     => array(
                array( 'label' => 'Call for Transport', 'href' => st_route_url( '/contact' ), 'type' => 'primary' ),
            ),
        ),
        array(
            'headline'    => 'Premium Car Rentals',
            'subheadline' => 'Choose from our wide range of well-maintained cars for business or leisure.',
            'image'       => st_theme_image( 'image-11-brake-parts-disk-maintenance.jpg' ),
            'buttons'     => array(
                array( 'label' => 'Browse Cars', 'href' => st_route_url( '/rental' ), 'type' => 'primary' ),
            ),
        ),
        array(
            'headline'    => 'Professional Auto Repairs',
            'subheadline' => 'Expert diagnostics and repairs by certified mechanics you can trust.',
            'image'       => st_theme_image( 'image-12-tyre-maintenance.jpg' ),
            'buttons'     => array(
                array( 'label' => 'Call for Repair', 'href' => $phone_tel, 'type' => 'phone' ),
                array( 'label' => 'WhatsApp for Repair', 'href' => $whatsapp, 'type' => 'whatsapp' ),
            ),
        ),
    );

    // 2. Quick Actions
    $quick_actions = array(
        array( 'title' => 'Request Towing', 'desc' => '24/7 emergency response', 'icon' => 'truck', 'url' => st_route_url( '/towing' ) ),
        array( 'title' => 'Rent a Car', 'desc' => 'Flexible daily & weekly rates', 'icon' => 'car', 'url' => st_route_url( '/rental' ) ),
        array( 'title' => 'Buy a Car', 'desc' => 'New & premium used cars', 'icon' => 'shield', 'url' => st_route_url( '/sales' ) ),
        array( 'title' => 'Call for Repairs', 'desc' => 'Certified expert mechanics', 'icon' => 'wrench', 'url' => st_route_url( '/repairs' ) ),
        array( 'title' => 'Shop Auto Parts', 'desc' => 'Genuine replacement parts', 'icon' => 'settings', 'url' => st_route_url( '/parts' ) ),
    );

    // 3. Services Grid
    $services = array(
        array( 'title' => 'Emergency Towing', 'desc' => 'Fast response towing anywhere, anytime.', 'img' => st_theme_image( 'image-1-towing-vehicle.jpg' ), 'url' => st_route_url( '/towing' ) ),
        array( 'title' => 'Car Recovery', 'desc' => 'Safe off-road and accident recovery.', 'img' => st_theme_image( 'image-1-towing-vehicle.jpg' ), 'url' => st_route_url( '/towing' ) ),
        array( 'title' => 'Car Rental', 'desc' => 'Wide range of cars for rent.', 'img' => st_theme_image( 'image-13-filters-maintenance.jpg' ), 'url' => st_route_url( '/rental' ) ),
        array( 'title' => 'Car Sales', 'desc' => 'Quality new and used cars.', 'img' => st_theme_image( 'image-14-suspension-parts-maintenance.jpg' ), 'url' => st_route_url( '/sales' ) ),
        array( 'title' => 'Auto Repairs', 'desc' => 'Comprehensive mechanical repairs. Buy Batteries available.', 'img' => st_theme_image( 'image-15-car-lighting-maintenance.jpg' ), 'url' => st_route_url( '/repairs' ) ),
        array( 'title' => 'Preventive Maintenance', 'desc' => 'Keep your car running smoothly, including advanced Car Diagnostics.', 'img' => st_theme_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'url' => st_route_url( '/services' ) ),
        array( 'title' => 'Spray Painting', 'desc' => 'Premium automotive painting.', 'img' => st_theme_image( 'image-6-luxury-mercedes-benz.jpg' ), 'url' => st_route_url( '/body-works' ) ),
        array( 'title' => 'Genuine Auto Parts', 'desc' => 'High-quality OEM parts.', 'img' => st_theme_image( 'image-16-accessories.jpg' ), 'url' => st_route_url( '/parts' ) ),
        array( 'title' => 'Fleet Services', 'desc' => 'Commercial car and fleet management.', 'img' => st_theme_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'url' => st_route_url( '/fleet' ) ),
    );

    // 4. Haulage Highlights
    $haulage_items = array(
        array( 'title' => 'Heavy Machinery Transport', 'desc' => 'Excavators, bulldozers, graders, loaders, and road-building plant machinery on specialized lowbeds.', 'img' => st_theme_image( 'haulage-heavy-logistics-600.jpg' ) ),
        array( 'title' => 'Tema Harbour Haulage', 'desc' => 'Rapid gate-out port clearance, container transfers, and car deliveries directly from Tema Port.', 'img' => st_theme_image( 'haulage-container-port-600.jpg' ) ),
        array( 'title' => 'Container & Industrial Cargo', 'desc' => '20ft & 40ft shipping containers, factory equipment, high-output generators, and industrial power units.', 'img' => st_theme_image( 'haulage-shipping-containers-600.jpg' ) ),
        array( 'title' => 'Intercity & Fleet Haulage', 'desc' => 'Commercial fleet distribution and long-haul transportation connecting Accra, Kumasi, Takoradi & Tamale.', 'img' => st_theme_image( 'haulage-semi-truck-600.jpg' ) ),
    );

    // 5. Featured Cars (CPT first, fallback to exact defaults)
    $car_items = array();
    if ( post_type_exists( 'st_car' ) ) {
        $cp = get_posts( array( 'post_type' => 'st_car', 'numberposts' => 12, 'post_status' => 'publish' ) );
        foreach ( $cp as $p ) {
            $car_items[] = array(
                'id'           => $p->ID,
                'name'         => $p->post_title,
                'type'         => get_post_meta( $p->ID, '_st_car_type', true ) ?: 'New',
                'price'        => get_post_meta( $p->ID, '_st_price', true ) ?: 'Contact',
                'fuel'         => get_post_meta( $p->ID, '_st_fuel', true ) ?: 'Hybrid',
                'transmission' => get_post_meta( $p->ID, '_st_transmission', true ) ?: 'Automatic',
                'year'         => get_post_meta( $p->ID, '_st_year', true ) ?: '2024',
                'mileage'      => get_post_meta( $p->ID, '_st_mileage', true ) ?: '0 km',
                'image'        => get_the_post_thumbnail_url( $p->ID, 'large' ) ?: st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ),
                'url'          => get_permalink( $p->ID ),
            );
        }
    }
    if ( empty( $car_items ) ) {
        $car_items = array(
            array( 'id' => 1, 'type' => 'New', 'name' => '2024 Luxury Sedan', 'price' => 'GH₵ 540,000', 'fuel' => 'Hybrid', 'transmission' => 'Automatic', 'year' => '2024', 'mileage' => '0 km', 'image' => st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => st_route_url( '/sales/1' ) ),
            array( 'id' => 2, 'type' => 'New', 'name' => '2024 Premium SUV', 'price' => 'GH₵ 780,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2024', 'mileage' => '25 km', 'image' => st_theme_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => st_route_url( '/sales/2' ) ),
            array( 'id' => 3, 'type' => 'New', 'name' => '2024 Executive Pickup', 'price' => 'GH₵ 620,000', 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'year' => '2024', 'mileage' => '15 km', 'image' => st_theme_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => st_route_url( '/sales/3' ) ),
            array( 'id' => 4, 'type' => 'New', 'name' => '2024 Passenger Van', 'price' => 'GH₵ 690,000', 'fuel' => 'Diesel', 'transmission' => 'Manual', 'year' => '2024', 'mileage' => '40 km', 'image' => st_theme_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'url' => st_route_url( '/sales/4' ) ),
            array( 'id' => 5, 'type' => 'Foreign Used', 'name' => '2020 Midsize Sedan', 'price' => 'GH₵ 264,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2020', 'mileage' => '72,000 km', 'image' => st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => st_route_url( '/sales/5' ) ),
            array( 'id' => 6, 'type' => 'Foreign Used', 'name' => '2019 Luxury SUV', 'price' => 'GH₵ 450,000', 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'year' => '2019', 'mileage' => '65,000 km', 'image' => st_theme_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => st_route_url( '/sales/6' ) ),
            array( 'id' => 7, 'type' => 'Foreign Used', 'name' => '2021 Premium Hatchback', 'price' => 'GH₵ 210,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2021', 'mileage' => '48,000 km', 'image' => st_theme_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'url' => st_route_url( '/sales/7' ) ),
            array( 'id' => 8, 'type' => 'Foreign Used', 'name' => '2018 Heavy Duty Pickup', 'price' => 'GH₵ 340,000', 'fuel' => 'Diesel', 'transmission' => 'Manual', 'year' => '2018', 'mileage' => '92,000 km', 'image' => st_theme_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => st_route_url( '/sales/8' ) ),
            array( 'id' => 9, 'type' => 'Ghana Used', 'name' => '2018 Compact Hatchback', 'price' => 'GH₵ 144,000', 'fuel' => 'Petrol', 'transmission' => 'Manual', 'year' => '2018', 'mileage' => '136,000 km', 'image' => st_theme_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'url' => st_route_url( '/sales/9' ) ),
            array( 'id' => 10, 'type' => 'Ghana Used', 'name' => '2015 Family SUV', 'price' => 'GH₵ 185,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2015', 'mileage' => '185,000 km', 'image' => st_theme_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => st_route_url( '/sales/10' ) ),
        );
    }

    // 6. Rental Fleet
    $rental_items = array(
        array( 'id' => 1, 'name' => 'Economy', 'daily' => 'GH₵ 400', 'weekly' => 'GH₵ 2,500', 'monthly' => 'GH₵ 9,000', 'seats' => '4', 'trans' => 'Auto/Manual', 'fuel' => 'Petrol', 'image' => st_theme_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'url' => st_route_url( '/rental/1' ) ),
        array( 'id' => 2, 'name' => 'Sedan', 'daily' => 'GH₵ 600', 'weekly' => 'GH₵ 3,800', 'monthly' => 'GH₵ 14,000', 'seats' => '5', 'trans' => 'Automatic', 'fuel' => 'Petrol/Hybrid', 'image' => st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => st_route_url( '/rental/2' ) ),
        array( 'id' => 3, 'name' => 'SUV', 'daily' => 'GH₵ 900', 'weekly' => 'GH₵ 5,800', 'monthly' => 'GH₵ 21,000', 'seats' => '7', 'trans' => 'Automatic', 'fuel' => 'Petrol/Diesel', 'image' => st_theme_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => st_route_url( '/rental/3' ) ),
        array( 'id' => 4, 'name' => 'Pickup', 'daily' => 'GH₵ 800', 'weekly' => 'GH₵ 5,000', 'monthly' => 'GH₵ 18,000', 'seats' => '5', 'trans' => 'Automatic', 'fuel' => 'Diesel', 'image' => st_theme_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => st_route_url( '/rental/4' ) ),
        array( 'id' => 5, 'name' => 'Luxury', 'daily' => 'GH₵ 1,500', 'weekly' => 'GH₵ 9,500', 'monthly' => 'GH₵ 35,000', 'seats' => '5', 'trans' => 'Automatic', 'fuel' => 'Premium', 'image' => st_theme_image( 'image-6-luxury-mercedes-benz.jpg' ), 'url' => st_route_url( '/rental/5' ) ),
        array( 'id' => 6, 'name' => 'Van', 'daily' => 'GH₵ 1,100', 'weekly' => 'GH₵ 7,000', 'monthly' => 'GH₵ 25,000', 'seats' => '12', 'trans' => 'Auto/Manual', 'fuel' => 'Diesel', 'image' => st_theme_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'url' => st_route_url( '/rental/6' ) ),
    );

    // 7. Parts Categories
    $parts_cats = array(
        array( 'name' => 'Car Batteries', 'icon' => 'wrench' ),
        array( 'name' => 'Engine Oil', 'icon' => 'battery' ),
        array( 'name' => 'Brake Pads', 'icon' => 'disc' ),
        array( 'name' => 'Tires', 'icon' => 'settings' ),
        array( 'name' => 'Filters', 'icon' => 'zap' ),
        array( 'name' => 'Suspension Parts', 'icon' => 'settings' ),
        array( 'name' => 'Lighting', 'icon' => 'shield-check' ),
        array( 'name' => 'Accessories', 'icon' => 'check-circle' ),
    );

    // 8. Why Choose Us
    $why_features = array(
        array( 'title' => '24/7 Emergency Support', 'icon' => 'clock', 'desc' => 'Always on-call dispatch team ready for day and night roadside recoveries.' ),
        array( 'title' => 'Rapid Response Time', 'icon' => 'truck', 'desc' => 'Average arrival time within 20-30 minutes across Greater Accra and highways.' ),
        array( 'title' => 'Certified Technicians', 'icon' => 'shield', 'desc' => 'ASE-certified mechanics and master auto-electricians with years of field mastery.' ),
        array( 'title' => 'Transparent Pricing', 'icon' => 'settings', 'desc' => 'Clear, upfront quotes with zero hidden charges or unexpected surprise fees.' ),
        array( 'title' => '100% Genuine OEM Parts', 'icon' => 'wrench', 'desc' => 'Direct factory imports backed by manufacturer warranties for maximum durability.' ),
        array( 'title' => 'State-of-the-Art Equipment', 'icon' => 'settings', 'desc' => 'Modern hydraulic flatbeds, specialized wheel lifts, and computer diagnostic rigs.' ),
        array( 'title' => 'Nationwide Network', 'icon' => 'map-pin', 'desc' => 'Comprehensive coverage across Accra, Tema, Kumasi, Takoradi, and regional corridors.' ),
        array( 'title' => 'Guaranteed Satisfaction', 'icon' => 'check-circle', 'desc' => 'Thousands of satisfied private motorists, fleet managers, and corporate partners.' ),
    );

    // 9. FAQs
    $faqs = array(
        array( 'q' => 'How fast can you respond to a towing emergency?', 'a' => 'Our average response time is under 30 minutes within city limits.' ),
        array( 'q' => 'What documents do I need to rent a car?', 'a' => 'You will need a valid driver\'s license, proof of identity, and a valid credit card for the security deposit.' ),
        array( 'q' => 'Do you offer warranties on auto repairs?', 'a' => 'Yes, we offer a 6-month or 6,000-mile warranty on all our repair services and parts.' ),
        array( 'q' => 'Can I finance a car purchase?', 'a' => 'Yes, we partner with several major banks to offer flexible financing options.' ),
    );
    ?>
    <main>
        <!-- 1. HERO SLIDER -->
        <section id="st-hero-slider" class="relative min-h-[640px] md:min-h-[700px] lg:h-[88vh] w-full overflow-hidden bg-dark">
            <?php foreach ( $slides as $sidx => $slide ) : ?>
                <div data-st-slide="<?php echo (int) $sidx; ?>" class="absolute inset-0 transition-opacity duration-700 <?php echo 0 === $sidx ? 'opacity-100 z-10' : 'opacity-0 pointer-events-none z-0'; ?>">
                    <div class="absolute inset-0">
                        <img src="<?php echo esc_url( $slide['image'] ); ?>" alt="<?php echo esc_attr( $slide['headline'] ); ?>" class="w-full h-full object-cover" />
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-dark/95 via-dark/80 to-dark/40 pointer-events-none"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/90 via-transparent to-black/30 pointer-events-none"></div>

                    <div class="absolute inset-0 flex items-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-20 pb-28 md:py-0">
                            <div class="max-w-2xl lg:max-w-3xl">
                                <?php if ( 0 === $sidx ) : ?>
                                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-5 tracking-tight leading-[1.1]">
                                        <?php echo esc_html( $slide['headline'] ); ?>
                                    </h1>
                                <?php else : ?>
                                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-5 tracking-tight leading-[1.1]">
                                        <?php echo esc_html( $slide['headline'] ); ?>
                                    </h2>
                                <?php endif; ?>

                                <p class="text-base sm:text-lg md:text-xl text-white/85 mb-8 leading-relaxed max-w-2xl font-normal">
                                    <?php echo esc_html( $slide['subheadline'] ); ?>
                                </p>

                                <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-8">
                                    <?php
                                    foreach ( $slide['buttons'] as $bidx => $btn ) :
                                        $is_wa   = 'whatsapp' === $btn['type'];
                                        $is_ph   = 'phone' === $btn['type'];
                                        $btn_cls = ( 0 === $bidx )
                                            ? 'bg-accent hover:bg-accent/90 text-white shadow-accent/30 hover:shadow-accent/50'
                                            : ( $is_wa ? 'bg-primary hover:bg-primary/90 text-white shadow-primary/30' : 'bg-white/15 hover:bg-white/25 text-white backdrop-blur-md border border-white/20' );
                                        ?>
                                        <a
                                            href="<?php echo esc_url( $btn['href'] ); ?>"
                                            <?php echo $is_wa ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                                            class="px-6 sm:px-8 py-3.5 sm:py-4 rounded-full font-bold text-sm sm:text-base transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:-translate-y-0.5 <?php echo esc_attr( $btn_cls ); ?>"
                                        >
                                            <?php if ( $is_ph ) { echo st_icon( 'phone', 'w-4 h-4' ); } ?>
                                            <?php if ( $is_wa ) { echo st_icon( 'message-circle', 'w-4 h-4' ); } ?>
                                            <span><?php echo esc_html( $btn['label'] ); ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Navigation Arrows -->
            <div class="absolute top-1/2 -translate-y-1/2 w-full px-4 sm:px-6 lg:px-8 flex justify-between pointer-events-none z-20">
                <button type="button" data-st-slide-prev class="pointer-events-auto w-11 h-11 sm:w-12 sm:h-12 bg-dark/60 hover:bg-accent text-white flex items-center justify-center backdrop-blur-md transition-all border border-white/15 shadow-lg hover:scale-105 rounded-full" aria-label="<?php esc_attr_e( 'Previous Slide', 'santa-towing' ); ?>">
                    <?php echo st_icon( 'chevron-left', 'w-5 h-5 sm:w-6 sm:h-6' ); ?>
                </button>
                <button type="button" data-st-slide-next class="pointer-events-auto w-11 h-11 sm:w-12 sm:h-12 bg-dark/60 hover:bg-accent text-white flex items-center justify-center backdrop-blur-md transition-all border border-white/15 shadow-lg hover:scale-105 rounded-full" aria-label="<?php esc_attr_e( 'Next Slide', 'santa-towing' ); ?>">
                    <?php echo st_icon( 'chevron-right', 'w-5 h-5 sm:w-6 sm:h-6' ); ?>
                </button>
            </div>

            <!-- Slide Indicators -->
            <div class="absolute bottom-28 md:bottom-24 left-1/2 -translate-x-1/2 flex items-center gap-4 z-20">
                <button type="button" data-st-slide-pause class="text-white/60 hover:text-white transition-colors flex items-center justify-center" aria-label="<?php esc_attr_e( 'Pause slider', 'santa-towing' ); ?>">
                    <span data-st-icon-pause><?php echo st_icon( 'pause', 'w-4 h-4' ); ?></span>
                    <span data-st-icon-play class="hidden"><?php echo st_icon( 'play', 'w-4 h-4' ); ?></span>
                </button>
                <div class="flex items-center gap-2">
                    <?php foreach ( $slides as $sidx => $slide ) : ?>
                        <button type="button" data-st-slide-dot="<?php echo (int) $sidx; ?>" aria-label="<?php echo esc_attr( 'Go to slide ' . ( $sidx + 1 ) ); ?>" class="h-2 rounded-xl transition-all duration-300 <?php echo 0 === $sidx ? 'w-8 bg-accent shadow-sm' : 'w-2.5 bg-white/40 hover:bg-white/70'; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="absolute bottom-36 left-1/2 -translate-x-1/2 z-40">
                <?php get_template_part( 'template-parts/scroll-to-footer-arrow' ); ?>
            </div>
        </section>

        <!-- 2. QUICK ACTIONS -->
        <div class="relative -mt-14 sm:-mt-20 z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 md:mb-24">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4 lg:gap-5">
                <?php foreach ( $quick_actions as $qa ) : ?>
                    <div data-st-reveal class="relative w-full">
                        <a href="<?php echo esc_url( $qa['url'] ); ?>" class="flex flex-col h-full bg-white rounded-[1px] p-4 sm:p-5 lg:p-6 shadow-xl shadow-black/5 border border-gray-100/90 hover:-translate-y-1.5 transition-all duration-300 group hover:shadow-2xl hover:border-accent/30">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-secondary rounded-full flex items-center justify-center mb-3 sm:mb-4 group-hover:bg-white group-hover:shadow-md group-hover:shadow-accent/30 transition-all duration-300 shrink-0 text-white group-hover:text-secondary">
                                <?php echo st_icon( $qa['icon'], 'w-5 h-5 sm:w-6 sm:h-6' ); ?>
                            </div>
                            <h3 class="font-bold text-dark text-sm sm:text-base lg:text-lg mb-1 leading-snug group-hover:text-accent transition-colors"><?php echo esc_html( $qa['title'] ); ?></h3>
                            <p class="text-xs sm:text-sm text-gray-500 line-clamp-2 leading-relaxed mb-3 flex-1"><?php echo esc_html( $qa['desc'] ); ?></p>
                            <div class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-bold text-primary group-hover:text-accent group-hover:translate-x-1 transition-all duration-200 mt-auto">
                                <span><?php esc_html_e( 'Explore', 'santa-towing' ); ?></span>
                                <?php echo st_icon( 'arrow-right', 'w-3 h-3 sm:w-3.5 sm:h-3.5' ); ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3. ABOUT SECTION -->
        <section id="about" class="py-12 md:py-16 lg:py-20 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                    <div data-st-reveal class="relative">
                        <div class="aspect-[4/3] rounded-[1px] overflow-hidden shadow-2xl shadow-black/10 border border-gray-100">
                            <img src="<?php echo esc_url( st_theme_image( 'welcome-towing-image.jpg' ) ); ?>" alt="Santa Towing &amp; Garage Workshop" class="w-full h-full object-cover" />
                        </div>
                        <div class="absolute bottom-3 right-3 sm:bottom-4 sm:right-4 md:-bottom-6 md:-right-6 lg:-bottom-8 lg:-right-8 bg-primary text-white p-4 sm:p-6 max-w-[280px] sm:max-w-xs rounded-[1px] shadow-2xl border border-white/10">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-3 aspect-square rounded-full bg-accent flex items-center justify-center font-extrabold text-white text-lg shrink-0">24/7</div>
                                <div>
                                    <div class="font-bold text-base leading-tight"><?php esc_html_e( 'Emergency Towing', 'santa-towing' ); ?></div>
                                    <div class="text-xs text-white/80"><?php esc_html_e( 'Rapid Dispatch Team', 'santa-towing' ); ?></div>
                                </div>
                            </div>
                            <p class="text-white/80 text-xs sm:text-sm mt-2 leading-relaxed">
                                <?php esc_html_e( 'Nationwide roadside assistance with guaranteed fast arrival.', 'santa-towing' ); ?>
                            </p>
                        </div>
                    </div>

                    <div data-st-reveal>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-5 tracking-tight leading-[1.15]">
                            <?php echo esc_html( st_get_content( 'home.about.heading', 'Leading Towing & Premium Automotive Care' ) ); ?>
                        </h2>
                        <p class="text-slate-600 mb-6 text-base sm:text-lg leading-relaxed">
                            <?php echo esc_html( st_get_content( 'home.about.p1', 'With over 15 years of industry excellence, Santa Towing & Garage Services is your premier partner for 24/7 emergency towing, heavy car recovery, certified mechanical repairs, and premium car rentals.' ) ); ?>
                        </p>
                        <div class="flex flex-wrap items-center gap-4">
                            <a href="<?php echo esc_url( st_route_url( '/services' ) ); ?>" class="bg-primary hover:bg-secondary text-white px-7 py-3.5 font-bold transition-all duration-200 flex items-center gap-2 shadow-md hover:-translate-y-0.5 rounded-full">
                                <span><?php esc_html_e( 'Discover Our Services', 'santa-towing' ); ?></span>
                                <?php echo st_icon( 'arrow-right', 'w-4 h-4' ); ?>
                            </a>
                            <a href="<?php echo esc_url( $phone_tel ); ?>" class="bg-accent hover:bg-accent/90 text-white px-7 py-3.5 font-bold transition-all duration-200 shadow-md shadow-accent/25 hover:-translate-y-0.5 rounded-full">
                                <?php echo esc_html( 'Call Hotline: ' . $phone ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. SERVICES SECTION -->
        <section class="py-12 md:py-16 lg:py-20 bg-slate-50/70 relative border-y border-gray-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-10 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4 tracking-tight">
                        <?php echo esc_html( st_get_content( 'home.servicesSection.heading', 'Complete Towing & Automotive Solutions' ) ); ?>
                    </h2>
                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                        <?php echo esc_html( st_get_content( 'home.servicesSection.description', 'From emergency towing to scheduled maintenance, our specialized team delivers reliable, high-standard automotive support across Ghana.' ) ); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php foreach ( $services as $srv ) : ?>
                        <div data-st-reveal class="group bg-white rounded-[1px] overflow-hidden shadow-sm hover:shadow-xl border border-gray-200/80 hover:border-accent/30 transition-all duration-300 flex flex-col h-full hover:-translate-y-1.5">
                            <div class="aspect-[16/10] overflow-hidden relative bg-gray-100">
                                <img src="<?php echo esc_url( $srv['img'] ); ?>" alt="<?php echo esc_attr( $srv['title'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-t from-dark/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                            </div>
                            <div class="p-6 sm:p-7 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-dark mb-2.5 mt-1 group-hover:text-accent transition-colors"><?php echo esc_html( $srv['title'] ); ?></h3>
                                <p class="text-slate-600 text-sm sm:text-base mb-6 flex-1 leading-relaxed"><?php echo esc_html( $srv['desc'] ); ?></p>
                                <div class="mt-auto pt-4 border-t border-gray-100 space-y-3">
                                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                        <a href="<?php echo esc_url( $phone_tel ); ?>" class="flex-1 bg-accent hover:bg-accent/90 text-white text-center py-2.5 px-3 text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center justify-center gap-1 min-h-[40px] rounded-full">
                                            <?php echo st_icon( 'phone', 'w-3.5 h-3.5' ); ?> <span><?php esc_html_e( 'Call for Repair', 'santa-towing' ); ?></span>
                                        </a>
                                        <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="flex-1 bg-primary hover:bg-primary/90 text-white text-center py-2.5 px-3 text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 min-h-[40px] rounded-full">
                                            <?php echo st_icon( 'message-circle', 'w-3.5 h-3.5' ); ?> <span><?php esc_html_e( 'WhatsApp', 'santa-towing' ); ?></span>
                                        </a>
                                    </div>
                                    <a href="<?php echo esc_url( $srv['url'] ); ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-accent transition-colors group/link">
                                        <span><?php esc_html_e( 'Service Details', 'santa-towing' ); ?></span>
                                        <?php echo st_icon( 'arrow-right', 'w-3.5 h-3.5 group-hover/link:translate-x-1 transition-transform' ); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-14 text-center">
                    <a href="<?php echo esc_url( st_route_url( '/services' ) ); ?>" class="bg-accent hover:bg-accent/90 text-white px-6 sm:px-10 py-4 font-bold transition-all shadow-md shadow-accent/25 hover:-translate-y-0.5 inline-flex items-center gap-2 rounded-full">
                        <span><?php echo esc_html( st_get_content( 'home.servicesSection.viewAllBtn', 'View All Automotive Services' ) ); ?></span>
                        <?php echo st_icon( 'arrow-right', 'w-4 h-4' ); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- 5. HAULAGE SECTION -->
        <section class="py-12 md:py-16 lg:py-20 bg-slate-900 text-white relative overflow-hidden border-y border-slate-800">
            <div class="absolute inset-0 bg-cover bg-center opacity-10" style="background-image: url('<?php echo esc_url( st_theme_image( 'haulage-highway-freight-1920.jpg' ) ); ?>');"></div>
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-8 items-end mb-14">
                    <div class="lg:col-span-8">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight mb-4 leading-tight">
                            <?php esc_html_e( 'Reliable Haulage & Heavy Transport Services', 'santa-towing' ); ?>
                        </h2>
                        <p class="text-slate-300 text-base sm:text-lg leading-relaxed max-w-3xl">
                            <?php esc_html_e( 'Beyond standard towing, Santa Towing provides heavy-duty haulage for cars, construction machinery, shipping containers, industrial generators, and oversized cargo across all 16 regions of Ghana.', 'santa-towing' ); ?>
                        </p>
                    </div>
                    <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col lg:items-end gap-3">
                        <a href="<?php echo esc_url( st_route_url( '/haulage' ) ); ?>" class="bg-accent hover:bg-accent/90 text-white px-7 py-3.5 font-bold transition-all shadow-md shadow-accent/25 inline-flex items-center justify-center gap-2 text-sm rounded-full">
                            <span><?php esc_html_e( 'View All Haulage Services', 'santa-towing' ); ?></span>
                            <?php echo st_icon( 'arrow-right', 'w-4 h-4' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $phone_tel ); ?>" class="bg-white/10 hover:bg-white/20 text-white px-7 py-3.5 font-bold transition-all border border-white/15 inline-flex items-center justify-center gap-2 text-sm rounded-full">
                            <?php echo st_icon( 'phone', 'w-4 h-4 text-accent' ); ?>
                            <span><?php echo esc_html( 'Haulage Dispatch: ' . $phone ); ?></span>
                        </a>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ( $haulage_items as $hl ) : ?>
                        <div data-st-reveal class="bg-slate-800/80 rounded-[1px] overflow-hidden border border-slate-700/80 hover:border-accent/50 transition-all duration-300 flex flex-col group hover:-translate-y-1 shadow-lg">
                            <div class="aspect-[16/10] relative overflow-hidden bg-slate-950">
                                <img src="<?php echo esc_url( $hl['img'] ); ?>" alt="<?php echo esc_attr( $hl['title'] ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent pointer-events-none"></div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-white mb-2 group-hover:text-accent transition-colors"><?php echo esc_html( $hl['title'] ); ?></h3>
                                <p class="text-slate-400 text-xs sm:text-sm leading-relaxed mb-4 flex-1"><?php echo esc_html( $hl['desc'] ); ?></p>
                                <a href="<?php echo esc_url( st_route_url( '/haulage' ) ); ?>" class="mt-auto inline-flex items-center gap-1.5 text-xs font-bold text-accent hover:text-white transition-colors">
                                    <span><?php esc_html_e( 'Learn More', 'santa-towing' ); ?></span>
                                    <?php echo st_icon( 'arrow-right', 'w-3.5 h-3.5' ); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 6. FEATURED CARS FOR SALE -->
        <section class="py-12 md:py-16 lg:py-20 bg-secondary text-white relative overflow-hidden" data-st-filter-group="car">
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary/20 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-accent/10 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 tracking-tight leading-[1.15]">
                            <?php echo esc_html( st_get_content( 'home.featuredCars.heading', 'Featured Cars for Sale' ) ); ?>
                        </h2>
                        <p class="text-white/75 text-base sm:text-lg leading-relaxed">
                            <?php echo esc_html( st_get_content( 'home.featuredCars.description', 'Explore our vetted inventory of premium brand-new and certified pre-owned cars with complete mechanical inspection reports.' ) ); ?>
                        </p>
                    </div>
                    <div class="flex bg-white/10 p-1.5 rounded-[1px] backdrop-blur-md self-start md:self-end border border-white/15 overflow-x-auto no-scrollbar max-w-full">
                        <?php foreach ( array( 'New', 'Foreign Used', 'Ghana Used' ) as $tidx => $tab ) : ?>
                            <button
                                type="button"
                                data-st-filter-btn="<?php echo esc_attr( $tab ); ?>"
                                class="px-5 py-2 rounded-full font-bold text-sm transition-all duration-200 whitespace-nowrap <?php echo 0 === $tidx ? 'bg-accent text-white shadow-md shadow-accent/30' : 'text-white/80 hover:text-white hover:bg-white/10'; ?>"
                            >
                                <?php echo esc_html( $tab ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php
                    foreach ( $car_items as $car ) :
                        get_template_part( 'template-parts/card-car', null, array( 'car' => $car ) );
                    endforeach;
                    ?>
                </div>

                <div class="mt-12 text-center">
                    <a href="<?php echo esc_url( st_route_url( '/sales' ) ); ?>" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 sm:px-8 py-3.5 font-bold transition-all border border-white/20 hover:border-white/40 group rounded-full">
                        <span><?php echo esc_html( st_get_content( 'home.featuredCars.viewAllBtn', 'View Full Car Inventory' ) ); ?></span>
                        <?php echo st_icon( 'arrow-right', 'w-4 h-4 group-hover:translate-x-1 transition-transform' ); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- 7. RENTAL FLEET -->
        <section class="py-12 md:py-16 lg:py-20 bg-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-10 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4 tracking-tight">
                        <?php echo esc_html( st_get_content( 'home.rentalFleet.heading', 'Premium Car Rental Fleet' ) ); ?>
                    </h2>
                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                        <?php echo esc_html( st_get_content( 'home.rentalFleet.description', 'Clean, fully insured cars available for daily, weekly, or corporate monthly rentals with airport pickup options.' ) ); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php
                    foreach ( $rental_items as $ritem ) :
                        get_template_part( 'template-parts/card-rental', null, array( 'rental' => $ritem ) );
                    endforeach;
                    ?>
                </div>
            </div>
        </section>

        <!-- 8. AUTO PARTS CATEGORIES -->
        <section class="py-12 md:py-16 lg:py-20 bg-slate-50/70 relative border-t border-gray-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-14">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4 tracking-tight leading-[1.15]">
                            <?php echo esc_html( st_get_content( 'home.autoParts.heading', 'Genuine Auto Parts & Accessories' ) ); ?>
                        </h2>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            <?php echo esc_html( st_get_content( 'home.autoParts.description', 'We stock thousands of high-quality OEM and verified aftermarket parts, batteries, and accessories to keep your car running at peak performance.' ) ); ?>
                        </p>
                    </div>
                    <a href="<?php echo esc_url( st_route_url( '/parts' ) ); ?>" class="bg-primary hover:bg-secondary text-white px-7 py-3.5 font-bold transition-all inline-flex items-center gap-2 whitespace-nowrap shadow-md hover:-translate-y-0.5 rounded-full">
                        <span><?php echo esc_html( st_get_content( 'home.autoParts.browseBtn', 'Browse Full Catalog' ) ); ?></span>
                        <?php echo st_icon( 'arrow-right', 'w-4 h-4' ); ?>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    <?php foreach ( $parts_cats as $pcat ) : ?>
                        <div data-st-reveal class="relative">
                            <a href="<?php echo esc_url( st_route_url( '/parts' ) ); ?>" class="bg-white border border-gray-200/80 hover:border-accent/40 hover:shadow-xl rounded-[1px] p-6 text-center transition-all duration-300 group flex flex-col items-center h-full hover:-translate-y-1.5">
                                <div class="w-14 h-14 bg-secondary rounded-full flex items-center justify-center mb-4 group-hover:bg-white group-hover:shadow-md transition-all duration-300 text-white group-hover:text-secondary">
                                    <?php echo st_icon( $pcat['icon'], 'w-7 h-7' ); ?>
                                </div>
                                <h3 class="font-bold text-dark text-base group-hover:text-accent transition-colors mb-1"><?php echo esc_html( $pcat['name'] ); ?></h3>
                                <span class="text-xs text-gray-500 font-medium"><?php esc_html_e( 'OEM & Aftermarket', 'santa-towing' ); ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 9. WHY CHOOSE US -->
        <section class="py-12 md:py-16 lg:py-20 bg-dark text-white relative overflow-hidden">
            <div class="absolute top-1/4 left-0 w-80 h-80 bg-accent/15 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-0 w-80 h-80 bg-primary/20 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-10 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 tracking-tight leading-[1.15]">
                        <?php esc_html_e( 'Why Choose Santa Towing & Garage?', 'santa-towing' ); ?>
                    </h2>
                    <p class="text-white/75 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                        <?php esc_html_e( "We don't just fix cars; we build lasting relationships based on trust, swift emergency response, quality workmanship, and transparent pricing.", 'santa-towing' ); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 md:mb-16">
                    <?php foreach ( $why_features as $feat ) : ?>
                        <div data-st-reveal class="bg-white border border-white/10 p-7 rounded-[1px] hover:border-accent/40 transition-all duration-300 group flex flex-col hover:-translate-y-1.5">
                            <div class="w-14 h-14 rounded-full bg-secondary flex items-center justify-center mb-6 group-hover:bg-white group-hover:shadow-lg transition-all duration-300 text-white group-hover:text-secondary">
                                <?php echo st_icon( $feat['icon'], 'w-7 h-7' ); ?>
                            </div>
                            <h3 class="text-xl font-bold mb-3 tracking-tight text-secondary"><?php echo esc_html( $feat['title'] ); ?></h3>
                            <p class="text-secondary text-sm leading-relaxed flex-1"><?php echo esc_html( $feat['desc'] ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-gradient-to-r from-primary to-secondary p-6 sm:p-8 md:p-10 rounded-[1px] border border-white/10 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div class="max-w-2xl text-center lg:text-left">
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-2 tracking-tight">
                            <?php esc_html_e( 'Need Immediate Roadside Assistance or Repairs?', 'santa-towing' ); ?>
                        </h3>
                        <p class="text-white/85 text-sm sm:text-base">
                            <?php esc_html_e( 'Our 24/7 emergency dispatch and expert mechanical teams are standing by right now.', 'santa-towing' ); ?>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-4 shrink-0">
                        <a href="<?php echo esc_url( $phone_tel ); ?>" class="bg-accent hover:bg-accent/90 text-white px-7 py-3.5 font-bold transition-all shadow-lg shadow-accent/30 hover:-translate-y-0.5 flex items-center gap-2 text-sm sm:text-base rounded-full">
                            <?php echo st_icon( 'phone', 'w-4 h-4' ); ?>
                            <span><?php echo esc_html( 'Call Now: ' . $phone ); ?></span>
                        </a>
                        <a href="<?php echo esc_url( st_route_url( '/towing' ) ); ?>" class="bg-white/15 hover:bg-white/25 text-white px-7 py-3.5 font-bold transition-all border border-white/20 hover:-translate-y-0.5 text-sm sm:text-base rounded-full">
                            <?php esc_html_e( 'Request Tow Online', 'santa-towing' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. BLOG SECTION -->
        <section class="py-12 md:py-16 lg:py-20 bg-slate-50/70 relative border-t border-gray-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-14">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark tracking-tight leading-[1.15]">
                        <?php echo esc_html( st_get_content( 'home.blogSection.heading', 'Latest News & Maintenance Insights' ) ); ?>
                    </h2>
                    <a href="<?php echo esc_url( st_route_url( '/blog' ) ); ?>" class="text-primary hover:text-accent font-bold transition-colors inline-flex items-center gap-2 group text-sm sm:text-base">
                        <span><?php echo esc_html( st_get_content( 'home.blogSection.viewAllBtn', 'View All Articles' ) ); ?></span>
                        <?php echo st_icon( 'arrow-right', 'w-4 h-4 group-hover:translate-x-1 transition-transform' ); ?>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php
                    $wp_posts = get_posts( array( 'post_type' => 'post', 'numberposts' => 6, 'post_status' => 'publish' ) );
                    if ( ! empty( $wp_posts ) ) :
                        foreach ( $wp_posts as $p ) :
                            $cats = get_the_category( $p->ID );
                            get_template_part(
                                'template-parts/card-blog',
                                null,
                                array(
                                    'post' => array(
                                        'title'    => $p->post_title,
                                        'excerpt'  => wp_trim_words( get_the_excerpt( $p ), 24 ),
                                        'category' => ! empty( $cats ) ? $cats[0]->name : 'Guides',
                                        'image'    => get_the_post_thumbnail_url( $p->ID, 'large' ) ?: st_theme_image( 'image-1-towing-vehicle.jpg' ),
                                        'url'      => get_permalink( $p->ID ),
                                        'date'     => get_the_date( '', $p ),
                                        'readTime' => st_reading_time( $p->ID ),
                                    ),
                                )
                            );
                        endforeach;
                    else :
                        $default_posts = array(
                            array( 'title' => 'What to Do If Your Car Breaks Down on a Busy Highway', 'excerpt' => 'Step-by-step instructions to keep yourself safe while waiting for emergency assistance or a tow truck on high-speed roads.', 'category' => 'Emergency Assistance', 'image' => st_theme_image( 'image-1-towing-vehicle.jpg' ), 'url' => st_route_url( '/blog/1' ), 'date' => 'Oct 15, 2023', 'readTime' => '6 min read' ),
                            array( 'title' => '5 Essential Summer Car Care Tips', 'excerpt' => 'Keep your car running cool during the hot summer months with these simple engine, coolant, and tire checks.', 'category' => 'Car Care', 'image' => st_theme_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'url' => st_route_url( '/blog/2' ), 'date' => 'Sep 28, 2023', 'readTime' => '4 min read' ),
                            array( 'title' => 'How to Prevent Battery Drain in Older Cars', 'excerpt' => 'Learn the common causes of parasitic battery drain and how to ensure your car starts reliably every morning.', 'category' => 'Automotive Guides', 'image' => st_theme_image( 'image-9-car-battery-maintenance.jpg' ), 'url' => st_route_url( '/blog/3' ), 'date' => 'Sep 15, 2023', 'readTime' => '5 min read' ),
                        );
                        foreach ( $default_posts as $dp ) {
                            get_template_part( 'template-parts/card-blog', null, array( 'post' => $dp ) );
                        }
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <!-- 11. FAQ SECTION -->
        <section class="py-12 md:py-16 lg:py-20 bg-white relative" data-st-faq-group>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4 tracking-tight">
                        <?php echo esc_html( st_get_content( 'home.faqSection.heading', 'Frequently Asked Questions' ) ); ?>
                    </h2>
                    <p class="text-slate-600 text-base sm:text-lg max-w-xl mx-auto">
                        <?php echo esc_html( st_get_content( 'home.faqSection.description', 'Quick answers regarding our 24/7 towing response times, repair warranties, car rentals, and pricing.' ) ); ?>
                    </p>
                </div>

                <div class="space-y-3.5">
                    <?php foreach ( $faqs as $fidx => $faq ) : ?>
                        <div class="border border-gray-200/90 rounded-[1px] overflow-hidden shadow-sm transition-colors hover:border-gray-300">
                            <button type="button" data-st-accordion-toggle aria-expanded="<?php echo 0 === $fidx ? 'true' : 'false'; ?>" class="w-full px-6 py-5 text-left flex justify-between items-center bg-white hover:bg-slate-50 transition-colors gap-4">
                                <span class="font-bold text-dark text-base sm:text-lg"><?php echo esc_html( $faq['q'] ); ?></span>
                                <span class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 transition-transform duration-200 st-accordion-icon">
                                    <?php echo st_icon( 'chevron-down', 'w-4 h-4' ); ?>
                                </span>
                            </button>
                            <div data-st-accordion-panel class="st-accordion-panel <?php echo 0 === $fidx ? 'is-open' : ''; ?>">
                                <div class="px-6 pb-6 pt-2 text-slate-600 text-sm sm:text-base leading-relaxed border-t border-gray-100">
                                    <?php echo esc_html( $faq['a'] ); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
    <?php
endif;

get_footer();
