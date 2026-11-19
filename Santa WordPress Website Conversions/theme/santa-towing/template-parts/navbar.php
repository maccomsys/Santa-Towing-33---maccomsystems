<?php
/**
 * NavBar Partial (Matches src/components/layout/NavBar.tsx 1:1)
 * Supports Desktop (xl:flex), Tablet/Laptop (lg:flex xl:hidden), and Mobile Accordions Drawer (lg:hidden)
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$logo_url  = st_get_logo_url();
$logo_alt  = st_get_content( 'global.logo.alt', 'Santa Towing & Garage Services Ltd. Logo' );
$phone     = st_get_content( 'global.links.phone', '0244753849' );
$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$call_now  = st_get_content( 'global.nav.callNow', 'Call Now: ' . $phone );

$nav_home             = st_get_content( 'global.nav.home', 'Home' );
$nav_towing_dropdown  = st_get_content( 'global.nav.towingDropdown', 'Towing Services' );
$nav_emergency_towing = st_get_content( 'global.nav.emergencyTowing', 'Emergency Towing' );
$nav_service_areas    = st_get_content( 'global.nav.serviceAreas', 'Service Areas Directory' );
$nav_haulage          = st_get_content( 'global.nav.haulage', 'Haulage Services' );
$nav_car_sales        = st_get_content( 'global.nav.carSales', 'Car Sales' );
$nav_car_rentals      = st_get_content( 'global.nav.carRentals', 'Car Rentals' );
$nav_garage_dropdown  = st_get_content( 'global.nav.garageDropdown', 'Garage Services' );
$nav_repairs          = st_get_content( 'global.nav.repairs', 'Auto Repairs & Diagnostics' );
$nav_body_works       = st_get_content( 'global.nav.bodyWorks', 'Auto Body Works & Spraying' );
$nav_parts            = st_get_content( 'global.nav.parts', 'Genuine Auto Parts' );
$nav_fleet            = st_get_content( 'global.nav.fleet', 'Corporate Fleet Services' );
$nav_all_services     = st_get_content( 'global.nav.allServices', 'All Services Overview' );
$nav_company_dropdown = st_get_content( 'global.nav.companyDropdown', 'Company' );
$nav_gallery          = st_get_content( 'global.nav.gallery', 'Gallery' );
$nav_blog             = st_get_content( 'global.nav.blog', 'Blog & Advice' );
$nav_faqs             = st_get_content( 'global.nav.faqs', 'FAQs' );
$nav_sitemap          = st_get_content( 'global.nav.sitemap', 'Website Sitemap' );
$nav_contact          = st_get_content( 'global.nav.contact', 'Contact' );
$nav_search           = st_get_content( 'global.nav.search', 'Search' );
$raw_menu_links = st_get_content( 'global.links.menuLinks', null );
if ( ! is_array( $raw_menu_links ) || empty( $raw_menu_links ) ) {
    $raw_menu_links = st_get_content( 'global.nav.menuLinks', null );
}
$custom_menu_links = is_array( $raw_menu_links )
    ? array_values(
        array_filter(
            $raw_menu_links,
            function ( $item ) {
                return is_array( $item ) && ( ! isset( $item['visible'] ) || false !== $item['visible'] ) && ! empty( $item['label'] );
            }
        )
    )
    : array();

$towing_links = array(
    array( 'title' => $nav_emergency_towing, 'path' => '/towing' ),
    array( 'title' => $nav_service_areas, 'path' => '/service-areas' ),
);

$garage_links = array(
    array( 'title' => $nav_repairs, 'path' => '/repairs' ),
    array( 'title' => $nav_body_works, 'path' => '/body-works' ),
    array( 'title' => $nav_parts, 'path' => '/parts' ),
    array( 'title' => $nav_fleet, 'path' => '/fleet' ),
    array( 'title' => $nav_all_services, 'path' => '/services' ),
);

$company_links = array(
    array( 'title' => $nav_gallery, 'path' => '/gallery' ),
    array( 'title' => $nav_blog, 'path' => '/blog' ),
    array( 'title' => $nav_faqs, 'path' => '/faqs' ),
    array( 'title' => $nav_sitemap, 'path' => '/sitemap' ),
);

$is_home_hero_top = is_front_page();
$header_classes   = $is_home_hero_top
    ? 'st-fixed-header fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out bg-secondary/90 backdrop-blur-md text-white py-4 translate-y-0 lg:translate-y-9 border-b border-white/10'
    : 'st-fixed-header fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100 py-3.5 text-dark translate-y-0';
?>
<header
    id="st-main-header"
    data-is-home="<?php echo $is_home_hero_top ? '1' : '0'; ?>"
    class="<?php echo esc_attr( $header_classes ); ?>"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
            <img
                src="<?php echo esc_url( $logo_url ); ?>"
                alt="<?php echo esc_attr( $logo_alt ); ?>"
                class="h-10 md:h-11 w-auto object-contain bg-white p-1 rounded-[1px] shadow-sm"
            />
        </a>

        <!-- Desktop Navigation (xl screens >= 1280px) -->
        <?php if ( is_array( $custom_menu_links ) && ! empty( $custom_menu_links ) ) : ?>
            <nav class="hidden xl:flex items-center gap-5 font-medium text-sm" aria-label="<?php esc_attr_e( 'Primary Navigation', 'santa-towing' ); ?>">
                <?php foreach ( $custom_menu_links as $it ) : ?>
                    <?php if ( isset( $it['visible'] ) && false === $it['visible'] ) { continue; } ?>
                    <a
                        href="<?php echo esc_url( st_route_url( $it['path'] ?? '/' ) ); ?>"
                        class="hover:text-accent transition-colors py-2 whitespace-nowrap"
                    >
                        <?php echo esc_html( $it['label'] ?? '' ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php else : ?>
            <nav class="hidden xl:flex items-center gap-6 font-medium text-sm" aria-label="<?php esc_attr_e( 'Primary Navigation', 'santa-towing' ); ?>">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $nav_home ); ?></a>

                <!-- Towing Dropdown -->
                <div class="relative group cursor-pointer st-dropdown">
                    <button type="button" class="flex items-center gap-1 hover:text-accent transition-colors py-2 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                        <span><?php echo esc_html( $nav_towing_dropdown ); ?></span>
                        <?php echo st_icon( 'chevron-down', 'w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200' ); ?>
                    </button>
                    <div class="st-dropdown-menu absolute top-full left-1/2 -translate-x-1/2 mt-1 w-56 bg-white text-dark rounded-[1px] shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 overflow-hidden border border-gray-100 p-1.5 z-50">
                        <?php foreach ( $towing_links as $s ) : ?>
                            <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="block px-3.5 py-2.5 hover:bg-slate-50 hover:text-accent transition-colors text-sm font-medium rounded-full">
                                <?php echo esc_html( $s['title'] ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="<?php echo esc_url( st_route_url( '/haulage' ) ); ?>" class="hover:text-accent transition-colors font-semibold flex items-center gap-1">
                    <?php echo esc_html( $nav_haulage ); ?>
                </a>

                <a href="<?php echo esc_url( st_route_url( '/sales' ) ); ?>" class="hover:text-accent transition-colors font-medium">
                    <?php echo esc_html( $nav_car_sales ); ?>
                </a>

                <a href="<?php echo esc_url( st_route_url( '/rental' ) ); ?>" class="hover:text-accent transition-colors font-medium">
                    <?php echo esc_html( $nav_car_rentals ); ?>
                </a>

                <!-- Garage Services Dropdown -->
                <div class="relative group cursor-pointer st-dropdown">
                    <button type="button" class="flex items-center gap-1 hover:text-accent transition-colors py-2 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                        <span><?php echo esc_html( $nav_garage_dropdown ); ?></span>
                        <?php echo st_icon( 'chevron-down', 'w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200' ); ?>
                    </button>
                    <div class="st-dropdown-menu absolute top-full left-1/2 -translate-x-1/2 mt-1 w-60 bg-white text-dark rounded-[1px] shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 overflow-hidden border border-gray-100 p-1.5 z-50">
                        <?php foreach ( $garage_links as $s ) : ?>
                            <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="block px-3.5 py-2.5 hover:bg-slate-50 hover:text-accent transition-colors text-sm font-medium rounded-full">
                                <?php echo esc_html( $s['title'] ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Company Dropdown -->
                <div class="relative group cursor-pointer st-dropdown">
                    <button type="button" class="flex items-center gap-1 hover:text-accent transition-colors py-2 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                        <span><?php echo esc_html( $nav_company_dropdown ); ?></span>
                        <?php echo st_icon( 'chevron-down', 'w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200' ); ?>
                    </button>
                    <div class="st-dropdown-menu absolute top-full left-1/2 -translate-x-1/2 mt-1 w-52 bg-white text-dark rounded-[1px] shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 overflow-hidden border border-gray-100 p-1.5 z-50">
                        <?php foreach ( $company_links as $s ) : ?>
                            <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="block px-3.5 py-2.5 hover:bg-slate-50 hover:text-accent transition-colors text-sm font-medium rounded-full">
                                <?php echo esc_html( $s['title'] ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="<?php echo esc_url( st_route_url( '/contact' ) ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $nav_contact ); ?></a>
            </nav>
        <?php endif; ?>

        <!-- Medium Navigation (Tablet/Laptop 1024px–1279px) -->
        <nav class="hidden lg:flex xl:hidden items-center gap-4 font-medium text-sm" aria-label="<?php esc_attr_e( 'Compact Navigation', 'santa-towing' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $nav_home ); ?></a>

            <div class="relative group cursor-pointer st-dropdown">
                <button type="button" class="flex items-center gap-1 hover:text-accent transition-colors py-2 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    <span><?php esc_html_e( 'Towing', 'santa-towing' ); ?></span>
                    <?php echo st_icon( 'chevron-down', 'w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200' ); ?>
                </button>
                <div class="st-dropdown-menu absolute top-full left-1/2 -translate-x-1/2 mt-1 w-56 bg-white text-dark rounded-[1px] shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 overflow-hidden border border-gray-100 p-1.5 z-50">
                    <?php foreach ( $towing_links as $s ) : ?>
                        <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="block px-3.5 py-2.5 hover:bg-slate-50 hover:text-accent transition-colors text-sm font-medium rounded-full">
                            <?php echo esc_html( $s['title'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="<?php echo esc_url( st_route_url( '/haulage' ) ); ?>" class="hover:text-accent transition-colors font-semibold"><?php echo esc_html( $nav_haulage ); ?></a>
            <a href="<?php echo esc_url( st_route_url( '/sales' ) ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $nav_car_sales ); ?></a>
            <a href="<?php echo esc_url( st_route_url( '/rental' ) ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $nav_car_rentals ); ?></a>

            <div class="relative group cursor-pointer st-dropdown">
                <button type="button" class="flex items-center gap-1 hover:text-accent transition-colors py-2 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    <span><?php esc_html_e( 'Garage', 'santa-towing' ); ?></span>
                    <?php echo st_icon( 'chevron-down', 'w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200' ); ?>
                </button>
                <div class="st-dropdown-menu absolute top-full left-1/2 -translate-x-1/2 mt-1 w-60 bg-white text-dark rounded-[1px] shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 overflow-hidden border border-gray-100 p-1.5 z-50">
                    <?php foreach ( $garage_links as $s ) : ?>
                        <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="block px-3.5 py-2.5 hover:bg-slate-50 hover:text-accent transition-colors text-sm font-medium rounded-full">
                            <?php echo esc_html( $s['title'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="relative group cursor-pointer st-dropdown">
                <button type="button" class="flex items-center gap-1 hover:text-accent transition-colors py-2 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    <span><?php echo esc_html( $nav_company_dropdown ); ?></span>
                    <?php echo st_icon( 'chevron-down', 'w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200' ); ?>
                </button>
                <div class="st-dropdown-menu absolute top-full left-1/2 -translate-x-1/2 mt-1 w-48 bg-white text-dark rounded-[1px] shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 overflow-hidden border border-gray-100 p-1.5 z-50">
                    <?php foreach ( $company_links as $s ) : ?>
                        <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="block px-3.5 py-2.5 hover:bg-slate-50 hover:text-accent transition-colors text-sm font-medium rounded-full">
                            <?php echo esc_html( $s['title'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="<?php echo esc_url( st_route_url( '/contact' ) ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $nav_contact ); ?></a>
        </nav>

        <!-- Search, Call CTA & Mobile Toggle -->
        <div class="flex items-center gap-3">
            <a
                href="<?php echo esc_url( st_route_url( '/search' ) ); ?>"
                class="hidden sm:flex items-center justify-center p-2.5 rounded-full hover:bg-white/10 transition-colors text-inherit"
                aria-label="<?php echo esc_attr( $nav_search ); ?>"
            >
                <?php echo st_icon( 'search', 'w-5 h-5' ); ?>
            </a>

            <a
                href="<?php echo esc_url( $phone_tel ); ?>"
                class="hidden sm:inline-flex items-center gap-2 bg-accent hover:bg-accent/90 text-white px-5 py-2.5 font-semibold text-sm transition-all shadow-md shadow-accent/25 hover:shadow-accent/40 hover:-translate-y-0.5 rounded-full"
            >
                <?php echo st_icon( 'phone', 'w-4 h-4' ); ?>
                <span><?php echo esc_html( $call_now ); ?></span>
            </a>

            <button
                id="st-mobile-menu-toggle"
                type="button"
                class="lg:hidden p-2 text-inherit rounded-full hover:bg-white/10 transition-colors"
                aria-label="<?php esc_attr_e( 'Toggle Menu', 'santa-towing' ); ?>"
                aria-expanded="false"
                aria-controls="st-mobile-drawer"
            >
                <span class="st-icon-open"><?php echo st_icon( 'menu', 'w-6 h-6' ); ?></span>
                <span class="st-icon-close hidden"><?php echo st_icon( 'x', 'w-6 h-6' ); ?></span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Drawer (< 1024px) -->
    <div
        id="st-mobile-drawer"
        class="hidden lg:hidden bg-white text-dark shadow-2xl absolute top-full left-0 right-0 border-t border-gray-100 overflow-hidden"
    >
        <div class="flex flex-col p-4 font-medium divide-y divide-gray-100 max-h-[calc(100vh-80px)] overflow-y-auto">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 rounded-full">
                <?php echo esc_html( $nav_home ); ?>
            </a>

            <a href="<?php echo esc_url( st_route_url( '/search' ) ); ?>" class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 flex items-center gap-2 rounded-full">
                <?php echo st_icon( 'search', 'w-4 h-4' ); ?>
                <span><?php echo esc_html( $nav_search ); ?></span>
            </a>

            <?php if ( is_array( $custom_menu_links ) && ! empty( $custom_menu_links ) ) : ?>
                <?php foreach ( $custom_menu_links as $it ) : ?>
                    <?php if ( isset( $it['visible'] ) && false === $it['visible'] ) { continue; } ?>
                    <a
                        href="<?php echo esc_url( st_route_url( $it['path'] ?? '/' ) ); ?>"
                        class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 rounded-full font-medium"
                    >
                        <?php echo esc_html( $it['label'] ?? '' ); ?>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- Towing Services Mobile Accordion -->
                <div class="flex flex-col shrink-0">
                    <button
                        type="button"
                        data-st-accordion-toggle="st-mob-towing"
                        aria-expanded="false"
                        class="flex justify-between items-center py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors w-full text-left rounded-full"
                    >
                        <span><?php echo esc_html( $nav_towing_dropdown ); ?></span>
                        <?php echo st_icon( 'chevron-down', 'w-4 h-4 transition-transform duration-200 st-chevron' ); ?>
                    </button>
                    <div id="st-mob-towing" class="st-accordion-panel">
                        <div class="flex flex-col bg-slate-50 rounded-[1px] my-1 py-1">
                            <?php foreach ( $towing_links as $s ) : ?>
                                <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="py-2.5 px-6 hover:text-accent text-sm font-medium">
                                    <?php echo esc_html( $s['title'] ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Haulage Highlight Mobile Link -->
                <a href="<?php echo esc_url( st_route_url( '/haulage' ) ); ?>" class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 flex items-center justify-between font-bold text-accent rounded-full">
                    <span><?php echo esc_html( $nav_haulage ); ?></span>
                    <span class="text-xs bg-accent/10 text-accent px-2 py-0.5 font-bold uppercase"><?php esc_html_e( 'Heavy Transport', 'santa-towing' ); ?></span>
                </a>

                <a href="<?php echo esc_url( st_route_url( '/sales' ) ); ?>" class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 rounded-full">
                    <?php echo esc_html( $nav_car_sales ); ?>
                </a>

                <a href="<?php echo esc_url( st_route_url( '/rental' ) ); ?>" class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 rounded-full">
                    <?php echo esc_html( $nav_car_rentals ); ?>
                </a>

                <!-- Garage Services Mobile Accordion -->
                <div class="flex flex-col shrink-0">
                    <button
                        type="button"
                        data-st-accordion-toggle="st-mob-garage"
                        aria-expanded="false"
                        class="flex justify-between items-center py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors w-full text-left rounded-full"
                    >
                        <span><?php echo esc_html( $nav_garage_dropdown ); ?></span>
                        <?php echo st_icon( 'chevron-down', 'w-4 h-4 transition-transform duration-200 st-chevron' ); ?>
                    </button>
                    <div id="st-mob-garage" class="st-accordion-panel">
                        <div class="flex flex-col bg-slate-50 rounded-[1px] my-1 py-1">
                            <?php foreach ( $garage_links as $s ) : ?>
                                <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="py-2.5 px-6 hover:text-accent text-sm font-medium">
                                    <?php echo esc_html( $s['title'] ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Company Mobile Accordion -->
                <div class="flex flex-col shrink-0">
                    <button
                        type="button"
                        data-st-accordion-toggle="st-mob-company"
                        aria-expanded="false"
                        class="flex justify-between items-center py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors w-full text-left rounded-full"
                    >
                        <span><?php echo esc_html( $nav_company_dropdown ); ?></span>
                        <?php echo st_icon( 'chevron-down', 'w-4 h-4 transition-transform duration-200 st-chevron' ); ?>
                    </button>
                    <div id="st-mob-company" class="st-accordion-panel">
                        <div class="flex flex-col bg-slate-50 rounded-[1px] my-1 py-1">
                            <?php foreach ( $company_links as $s ) : ?>
                                <a href="<?php echo esc_url( st_route_url( $s['path'] ) ); ?>" class="py-2.5 px-6 hover:text-accent text-sm font-medium">
                                    <?php echo esc_html( $s['title'] ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <a href="<?php echo esc_url( st_route_url( '/contact' ) ); ?>" class="py-3 px-4 hover:text-accent hover:bg-slate-50 transition-colors shrink-0 rounded-full">
                    <?php echo esc_html( $nav_contact ); ?>
                </a>
            <?php endif; ?>

            <div class="pt-4 pb-2 px-2 shrink-0">
                <a
                    href="<?php echo esc_url( $phone_tel ); ?>"
                    class="w-full bg-accent hover:bg-accent/90 text-white px-6 py-3.5 font-semibold transition-all text-center flex items-center justify-center gap-2 shadow-md rounded-full"
                >
                    <?php echo st_icon( 'phone', 'w-4 h-4' ); ?>
                    <span><?php echo esc_html( $call_now ); ?></span>
                </a>
            </div>
        </div>
    </div>
</header>
