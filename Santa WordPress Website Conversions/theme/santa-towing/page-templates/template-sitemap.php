<?php
/**
 * Template Name: Website Sitemap Directory
 * Template Post Type: page
 *
 * Visual HTML sitemap directory matching React src/pages/Sitemap.tsx.
 * Dynamically aggregates WordPress Pages, CPTs (Cars, Rentals, Parts, Service Areas), and Blog Posts.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$main_links = array(
    array( 'label' => 'Home', 'path' => '/' ),
    array( 'label' => 'About / Services Overview', 'path' => '/services' ),
    array( 'label' => 'Gallery Showcase', 'path' => '/gallery' ),
    array( 'label' => 'Blog & Automotive Hub', 'path' => '/blog' ),
    array( 'label' => 'Frequently Asked Questions', 'path' => '/faqs' ),
    array( 'label' => 'Contact Us', 'path' => '/contact' ),
    array( 'label' => 'Careers', 'path' => '/careers' ),
    array( 'label' => 'Search Website', 'path' => '/search' ),
);

$service_links = array(
    array( 'label' => 'Emergency Towing Services', 'path' => '/towing' ),
    array( 'label' => 'Haulage & Heavy Transport', 'path' => '/haulage' ),
    array( 'label' => 'Auto Repairs & Diagnostics', 'path' => '/repairs' ),
    array( 'label' => 'Body Works & Spray Painting', 'path' => '/body-works' ),
    array( 'label' => 'Corporate Fleet Services', 'path' => '/fleet' ),
);

$policy_links = array(
    array( 'label' => 'Privacy Policy', 'path' => '/privacy-policy' ),
    array( 'label' => 'Terms & Conditions', 'path' => '/terms-conditions' ),
    array( 'label' => 'Towing Service Terms', 'path' => '/towing-service-terms' ),
    array( 'label' => 'Cancellation & Refund Policy', 'path' => '/cancellation-refund-policy' ),
    array( 'label' => 'Pricing & Payment Policy', 'path' => '/pricing-payment-policy' ),
    array( 'label' => 'Disclaimer', 'path' => '/disclaimer' ),
    array( 'label' => 'Cookie Policy', 'path' => '/cookie-policy' ),
    array( 'label' => 'Service Area Policy', 'path' => '/service-area-policy' ),
    array( 'label' => 'Emergency Service Policy', 'path' => '/emergency-service-policy' ),
    array( 'label' => 'Complaints Policy', 'path' => '/complaints-policy' ),
    array( 'label' => 'Car Damage & Liability Policy', 'path' => '/car-damage-liability-policy' ),
    array( 'label' => 'Accessibility Statement', 'path' => '/accessibility-statement' ),
);

// Query CPTs safely if registered, with graceful static fallback
$car_posts   = post_type_exists( 'st_car' ) ? get_posts( array( 'post_type' => 'st_car', 'numberposts' => -1, 'post_status' => 'publish' ) ) : array();
$rent_posts  = post_type_exists( 'st_rental' ) ? get_posts( array( 'post_type' => 'st_rental', 'numberposts' => -1, 'post_status' => 'publish' ) ) : array();
$part_posts  = post_type_exists( 'st_part' ) ? get_posts( array( 'post_type' => 'st_part', 'numberposts' => -1, 'post_status' => 'publish' ) ) : array();
$area_posts  = post_type_exists( 'st_service_area' ) ? get_posts( array( 'post_type' => 'st_service_area', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) ) : array();
$blog_posts  = get_posts( array( 'post_type' => 'post', 'numberposts' => 50, 'post_status' => 'publish' ) );

$inventory_links = array(
    array( 'label' => 'Car Sales (Overview)', 'url' => st_route_url( '/sales' ) ),
);
if ( ! empty( $car_posts ) ) {
    foreach ( $car_posts as $p ) {
        $price             = get_post_meta( $p->ID, '_st_price', true );
        $inventory_links[] = array(
            'label' => $p->post_title . ( $price ? ' — ' . $price : '' ),
            'url'   => get_permalink( $p ),
        );
    }
} elseif ( function_exists( 'stwc_get_cars' ) ) {
    foreach ( stwc_get_cars( array( 'posts_per_page' => 24 ) ) as $car ) {
        $inventory_links[] = array(
            'label' => ( $car['name'] ?? '' ) . ( ! empty( $car['price'] ) ? ' — ' . $car['price'] : '' ),
            'url'   => $car['url'] ?? st_route_url( '/sales/' . ( $car['id'] ?? '' ) ),
        );
    }
}

$inventory_links[] = array( 'label' => 'Car Rentals (Overview)', 'url' => st_route_url( '/rental' ) );
if ( ! empty( $rent_posts ) ) {
    foreach ( $rent_posts as $p ) {
        $inventory_links[] = array( 'label' => $p->post_title, 'url' => get_permalink( $p ) );
    }
} elseif ( function_exists( 'stwc_get_rentals' ) ) {
    foreach ( stwc_get_rentals( array( 'posts_per_page' => 24 ) ) as $rent ) {
        $inventory_links[] = array(
            'label' => ( $rent['name'] ?? '' ) . ( ! empty( $rent['daily'] ) ? ' (' . $rent['daily'] . '/day)' : '' ),
            'url'   => $rent['url'] ?? st_route_url( '/rental/' . ( $rent['id'] ?? '' ) ),
        );
    }
}

$inventory_links[] = array( 'label' => 'Auto Parts Store (Overview)', 'url' => st_route_url( '/parts' ) );
if ( ! empty( $part_posts ) ) {
    foreach ( $part_posts as $p ) {
        $inventory_links[] = array( 'label' => $p->post_title, 'url' => get_permalink( $p ) );
    }
} elseif ( function_exists( 'stwc_get_parts' ) ) {
    foreach ( stwc_get_parts( array( 'posts_per_page' => 24 ) ) as $part ) {
        $inventory_links[] = array(
            'label' => ( $part['name'] ?? '' ) . ( ! empty( $part['price'] ) ? ' — ' . $part['price'] : '' ),
            'url'   => $part['url'] ?? st_route_url( '/parts/' . ( $part['id'] ?? '' ) ),
        );
    }
}

$blog_links = array( array( 'label' => 'Blog & Automotive Hub', 'url' => st_route_url( '/blog' ) ) );
foreach ( $blog_posts as $p ) {
    $blog_links[] = array( 'label' => $p->post_title, 'url' => get_permalink( $p ) );
}

$area_links = array( array( 'label' => 'Service Areas Directory', 'url' => st_route_url( '/service-areas' ) ) );
$seen_areas = array( st_route_url( '/service-areas' ) => true );
if ( ! empty( $area_posts ) ) {
    foreach ( $area_posts as $p ) {
        $u                = get_permalink( $p );
        $seen_areas[ $u ] = true;
        $area_links[]     = array( 'label' => $p->post_title, 'url' => $u );
    }
} elseif ( function_exists( 'stwc_get_towing_adverts' ) ) {
    foreach ( stwc_get_towing_adverts() as $adv ) {
        $u                = st_route_url( '/service-areas/' . $adv['slug'] );
        $seen_areas[ $u ] = true;
        $area_links[]     = array( 'label' => 'Towing in ' . $adv['areaName'] . ' (' . $adv['region'] . ')', 'url' => $u );
    }
}
if ( function_exists( 'stwc_get_ghana_regions_data' ) ) {
    foreach ( stwc_get_ghana_regions_data() as $reg ) {
        foreach ( $reg['areas'] as $town ) {
            $u = st_route_url( '/service-areas/' . sanitize_title( $town ) );
            if ( ! isset( $seen_areas[ $u ] ) ) {
                $seen_areas[ $u ] = true;
                $area_links[]     = array( 'label' => $town . ' (' . $reg['region'] . ')', 'url' => $u );
            }
        }
    }
}

$categories = array(
    array(
        'title' => 'Main Pages',
        'icon'  => 'home',
        'links' => array_map(
            function ( $l ) {
                return array( 'label' => $l['label'], 'url' => st_route_url( $l['path'] ) );
            },
            $main_links
        ),
    ),
    array(
        'title' => 'Services',
        'icon'  => 'wrench',
        'links' => array_map(
            function ( $l ) {
                return array( 'label' => $l['label'], 'url' => st_route_url( $l['path'] ) );
            },
            $service_links
        ),
    ),
    array(
        'title' => 'Inventory (Cars, Rentals, Parts)',
        'icon'  => 'car',
        'links' => $inventory_links,
    ),
    array(
        'title' => 'Blog',
        'icon'  => 'book-open',
        'links' => $blog_links,
    ),
    array(
        'title' => 'Service Areas',
        'icon'  => 'map',
        'links' => $area_links,
    ),
    array(
        'title' => 'Policies',
        'icon'  => 'shield-check',
        'links' => array_map(
            function ( $l ) {
                return array( 'label' => $l['label'], 'url' => st_route_url( $l['path'] ) );
            },
            $policy_links
        ),
    ),
);

$total_count = 0;
foreach ( $categories as $c ) {
    $total_count += count( $c['links'] );
}
?>
<main class="min-h-screen bg-slate-50 pt-24 pb-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="mb-6">
            <?php st_breadcrumbs( array( array( 'label' => 'Website Sitemap' ) ) ); ?>
        </div>

        <div class="bg-white rounded-[1px] p-6 sm:p-8 md:p-10 border border-slate-200 shadow-sm mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-accent/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-3.5 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-accent text-white flex items-center justify-center shrink-0 shadow-md shadow-accent/20">
                            <?php echo st_icon( 'folder-tree', 'w-6 h-6' ); ?>
                        </div>
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                                <?php esc_html_e( 'Website Sitemap', 'santa-towing' ); ?>
                            </h1>
                            <p id="st-sitemap-count" data-total-routes="<?php echo (int) $total_count; ?>" class="text-xs text-slate-500 font-mono mt-0.5">
                                <?php
                                printf(
                                    esc_html__( '%d Total Verified Routes', 'santa-towing' ),
                                    (int) $total_count
                                );
                                ?>
                            </p>
                        </div>
                    </div>
                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed mt-2">
                        <?php esc_html_e( 'A complete architectural directory of Santa Towing. Navigate directly to any core service, inventory item, legal policy, or service coverage route.', 'santa-towing' ); ?>
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="relative max-w-xl">
                    <span class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <?php echo st_icon( 'search', 'w-4 h-4' ); ?>
                    </span>
                    <input
                        type="text"
                        id="st-sitemap-filter"
                        placeholder="<?php esc_attr_e( 'Filter pages by keyword or route (e.g. rental, kumasi, policy)...', 'santa-towing' ); ?>"
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                    />
                </div>
            </div>
        </div>

        <div id="st-sitemap-empty" class="hidden bg-white rounded-[1px] border border-dashed border-slate-300 p-10 text-center text-slate-600 mb-8">
            <p class="font-bold text-slate-900 mb-1"><?php esc_html_e( 'No matching pages found', 'santa-towing' ); ?></p>
            <p class="text-sm"><?php esc_html_e( 'Try clearing or shortening your keyword filter.', 'santa-towing' ); ?></p>
        </div>

        <div class="space-y-8" id="st-sitemap-sections">
            <?php
            $counter = 1;
            foreach ( $categories as $category ) :
                ?>
                <section class="st-sitemap-section bg-white rounded-[1px] border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-7 border-b bg-slate-50/70 border-slate-100 flex items-center gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-900 text-accent flex items-center justify-center shrink-0 shadow-sm">
                            <?php echo st_icon( $category['icon'], 'w-5 h-5 sm:w-6 sm:h-6' ); ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?php echo esc_html( $category['title'] ); ?></h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <?php echo count( $category['links'] ) . ' ' . ( 1 === count( $category['links'] ) ? 'Page' : 'Pages' ); ?>
                            </p>
                        </div>
                    </div>
                    <div class="p-5 sm:p-8">
                        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ( $category['links'] as $link ) : ?>
                                <li class="st-sitemap-item" data-st-filter-text="<?php echo esc_attr( strtolower( $link['label'] . ' ' . $link['url'] ) ); ?>">
                                    <a
                                        href="<?php echo esc_url( $link['url'] ); ?>"
                                        class="group flex items-start gap-2 p-4 rounded-[1px] border bg-slate-50 border-slate-100 hover:border-accent/40 hover:bg-slate-50/90 transition-all h-full"
                                    >
                                        <span class="mt-0.5 shrink-0 text-slate-300 group-hover:text-accent transition-colors">
                                            <?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?>
                                        </span>
                                        <div>
                                            <span class="font-bold block text-slate-800 group-hover:text-accent transition-colors leading-snug">
                                                <span class="text-slate-400 mr-1"><?php echo (int) $counter++; ?>.</span>
                                                <?php echo esc_html( $link['label'] ); ?>
                                            </span>
                                            <span class="text-xs text-slate-400 block mt-1 font-mono group-hover:text-slate-500 transition-colors break-all">
                                                <?php echo esc_html( wp_make_link_relative( $link['url'] ) ?: '/' ); ?>
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php
get_footer();
