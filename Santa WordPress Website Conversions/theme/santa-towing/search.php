<?php
/**
 * Search Results Template
 *
 * 1:1 visual & functional parity with React src/pages/Search.tsx
 * Combines live WP_Query posts/CPTs with core services, policies, 32 district adverts,
 * 232 Ghana localities, and default CPT catalog fallbacks.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$query = trim( (string) get_search_query( false ) );
if ( '' === $query && isset( $_GET['q'] ) ) {
    $query = trim( sanitize_text_field( wp_unslash( $_GET['q'] ) ) );
}
if ( '' === $query && isset( $_GET['s'] ) ) {
    $query = trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) );
}

/**
 * Highlight matched query substrings safely without breaking HTML escaping.
 */
function st_search_highlight( $text, $needle ) {
    $safe = esc_html( (string) $text );
    $q    = trim( (string) $needle );
    if ( '' === $q ) {
        return $safe;
    }
    $pattern = '/' . preg_quote( esc_html( $q ), '/' ) . '/i';
    return preg_replace(
        $pattern,
        '<mark class="bg-accent/20 text-dark font-semibold rounded-[1px] px-0.5">$0</mark>',
        $safe
    );
}

$results = array();

if ( '' !== $query ) {
    $lower_q = function_exists( 'mb_strtolower' ) ? mb_strtolower( $query ) : strtolower( $query );
    $seen    = array();

    $norm_key = function ( $url ) {
        $p = (string) wp_parse_url( $url, PHP_URL_PATH );
        return '/' . trim( $p, '/' );
    };

    $score_item = function ( $title, $excerpt, $keywords = '' ) use ( $lower_q ) {
        $score = 0;
        $t     = strtolower( (string) $title );
        $e     = strtolower( (string) $excerpt );
        $k     = strtolower( (string) $keywords );
        if ( false !== strpos( $t, $lower_q ) ) {
            $score += 3;
        }
        if ( '' !== $k && false !== strpos( $k, $lower_q ) ) {
            $score += 2;
        }
        if ( false !== strpos( $e, $lower_q ) ) {
            $score += 1;
        }
        return $score;
    };

    // 1. Live WordPress query across pages, blog posts & CPTs
    $wp_search = new WP_Query(
        array(
            's'              => $query,
            'post_type'      => array( 'page', 'post', 'st_car', 'st_rental', 'st_part', 'st_service_area' ),
            'post_status'    => 'publish',
            'posts_per_page' => 30,
        )
    );

    if ( $wp_search->have_posts() ) {
        foreach ( $wp_search->posts as $p ) {
            $url   = get_permalink( $p );
            $key   = $norm_key( $url );
            $ptype = $p->post_type;
            $icon  = 'file-text';
            $badge = 'Page';
            if ( 'st_car' === $ptype ) {
                $icon  = 'car';
                $badge = 'Car Sales';
            } elseif ( 'st_rental' === $ptype ) {
                $icon  = 'car';
                $badge = 'Car Rental';
            } elseif ( 'st_part' === $ptype ) {
                $icon  = 'wrench';
                $badge = 'Auto Part';
            } elseif ( 'st_service_area' === $ptype ) {
                $icon  = 'map-pin';
                $badge = 'Service Area';
            } elseif ( 'post' === $ptype ) {
                $icon  = 'book-open';
                $badge = 'Blog';
            }
            $title   = get_the_title( $p );
            $excerpt = wp_trim_words( wp_strip_all_tags( $p->post_excerpt ?: $p->post_content ), 28 );
            $score   = max( 1, $score_item( $title, $excerpt ) );
            $seen[ $key ] = true;
            $results[] = array(
                'title'   => $title,
                'excerpt' => $excerpt ?: __( 'View full details on Santa Towing.', 'santa-towing' ),
                'url'     => $url,
                'icon'    => $icon,
                'badge'   => $badge,
                'score'   => $score + 1,
            );
        }
    }

    // 2. Core static & service directory matching Search.tsx SEARCH_INDEX
    $core_index = array(
        array( 'title' => 'Home', 'path' => '/', 'icon' => 'file-text', 'badge' => 'Page', 'excerpt' => 'Santa Towing offers 24/7 towing, auto repairs, body works, car sales, and car rentals in Ghana.', 'kw' => 'home santa towing auto services ghana accra' ),
        array( 'title' => 'Emergency Towing', 'path' => '/towing', 'icon' => 'wrench', 'badge' => 'Service', 'excerpt' => 'Rapid response 24/7 towing services. We safely transport your car to any destination or our secure facility.', 'kw' => 'towing emergency breakdown recovery flatbed tow truck' ),
        array( 'title' => 'Haulage Services', 'path' => '/haulage', 'icon' => 'wrench', 'badge' => 'Service', 'excerpt' => 'Specialized heavy machinery transport, flatbed haulage, Tema harbour freight transfer, and oversize plant relocation.', 'kw' => 'haulage heavy transport machinery freight cargo tema harbour lowbed excavator' ),
        array( 'title' => 'Auto Repairs', 'path' => '/repairs', 'icon' => 'wrench', 'badge' => 'Service', 'excerpt' => 'Comprehensive mechanical repairs, computer diagnostics, and regular maintenance by certified mechanics.', 'kw' => 'repairs mechanic engine diagnostics maintenance oil change brakes' ),
        array( 'title' => 'Body Works & Spray Painting', 'path' => '/body-works', 'icon' => 'wrench', 'badge' => 'Service', 'excerpt' => 'Professional dent repair, collision restoration, and premium oven-baked spray painting services.', 'kw' => 'body works spray painting dent repair collision paint restoration' ),
        array( 'title' => 'Auto Parts', 'path' => '/parts', 'icon' => 'wrench', 'badge' => 'Service', 'excerpt' => 'Genuine OEM and high-quality aftermarket spare parts for all major car makes and models.', 'kw' => 'parts spares oem accessories batteries tires engine parts' ),
        array( 'title' => 'Fleet Services', 'path' => '/fleet', 'icon' => 'wrench', 'badge' => 'Service', 'excerpt' => 'Specialized maintenance and repair programs designed to keep corporate and commercial fleets running efficiently.', 'kw' => 'fleet commercial corporate maintenance program logistics' ),
        array( 'title' => 'Cars for Sale', 'path' => '/sales', 'icon' => 'car', 'badge' => 'Car Sales', 'excerpt' => 'Browse our inventory of quality inspected new and pre-owned cars.', 'kw' => 'sales buy car used cars new cars dealership cars' ),
        array( 'title' => 'Rental Fleet', 'path' => '/rental', 'icon' => 'car', 'badge' => 'Car Rental', 'excerpt' => 'Premium and affordable rental cars for daily, weekly, or monthly use.', 'kw' => 'rental hire rent car suv sedan daily rental' ),
        array( 'title' => 'Service Areas Directory', 'path' => '/service-areas', 'icon' => 'map-pin', 'badge' => 'Location', 'excerpt' => 'View all areas we serve across Greater Accra and nationwide Ghana for towing and roadside assistance.', 'kw' => 'locations areas accra tema coverage map ghana' ),
        array( 'title' => 'Contact Us', 'path' => '/contact', 'icon' => 'file-text', 'badge' => 'Page', 'excerpt' => 'Get in touch with Santa Towing for inquiries, quotes, or emergency assistance.', 'kw' => 'contact phone email location address help' ),
        array( 'title' => 'Blog & News', 'path' => '/blog', 'icon' => 'book-open', 'badge' => 'Page', 'excerpt' => 'Read the latest automotive tips, company news, and industry updates from Santa Towing.', 'kw' => 'blog news tips articles guide' ),
        array( 'title' => 'Gallery Showcase', 'path' => '/gallery', 'icon' => 'file-text', 'badge' => 'Page', 'excerpt' => 'Photos and highlights of our towing trucks, heavy haulage fleet, and garage restorations.', 'kw' => 'gallery photos images trucks recovery workshop' ),
        array( 'title' => 'Frequently Asked Questions', 'path' => '/faqs', 'icon' => 'shield', 'badge' => 'Policy & Help', 'excerpt' => 'Find answers to common questions about our towing, repairs, and rental services.', 'kw' => 'faq questions answers help support' ),
        array( 'title' => 'Privacy Policy', 'path' => '/privacy-policy', 'icon' => 'shield', 'badge' => 'Policy', 'excerpt' => 'Learn how Santa Towing collects, uses, and protects your personal information.', 'kw' => 'privacy policy data terms security' ),
        array( 'title' => 'Terms & Conditions', 'path' => '/terms-conditions', 'icon' => 'shield', 'badge' => 'Policy', 'excerpt' => 'Read our general terms and conditions for using Santa Towing.', 'kw' => 'terms conditions legal agreement' ),
        array( 'title' => 'Careers', 'path' => '/careers', 'icon' => 'file-text', 'badge' => 'Page', 'excerpt' => 'Join the Santa Towing team. Explore open positions for mechanics, drivers, and admin staff.', 'kw' => 'careers jobs hiring work employment' ),
        array( 'title' => 'Website Sitemap', 'path' => '/sitemap', 'icon' => 'file-text', 'badge' => 'Page', 'excerpt' => 'Explore the full directory of Santa Towing services, inventory, policies, and Ghana service areas.', 'kw' => 'sitemap directory all pages routes' ),
    );

    foreach ( $core_index as $item ) {
        $url = st_route_url( $item['path'] );
        $key = $norm_key( $url );
        if ( isset( $seen[ $key ] ) ) {
            continue;
        }
        $sc = $score_item( $item['title'], $item['excerpt'], $item['kw'] );
        if ( $sc > 0 ) {
            $seen[ $key ] = true;
            $results[]    = array(
                'title'   => $item['title'],
                'excerpt' => $item['excerpt'],
                'url'     => $url,
                'icon'    => $item['icon'],
                'badge'   => $item['badge'],
                'score'   => $sc,
            );
        }
    }

    // 3. Cars, Rentals & Parts catalogs (handles unseeded CPT defaults too)
    if ( function_exists( 'stwc_get_cars' ) ) {
        foreach ( stwc_get_cars( array( 'posts_per_page' => 24 ) ) as $car ) {
            $url   = $car['url'] ?? st_route_url( '/sales/' . ( $car['id'] ?? '' ) );
            $key   = $norm_key( $url );
            $title = $car['name'] ?? '';
            $exc   = sprintf( '%s • %s • %s • %s', $car['price'] ?? '', $car['year'] ?? '', $car['fuel'] ?? '', $car['transmission'] ?? '' );
            $sc    = $score_item( $title, $exc, ( $car['type'] ?? '' ) . ' car sales' );
            if ( ! isset( $seen[ $key ] ) && $sc > 0 ) {
                $seen[ $key ] = true;
                $results[]    = array( 'title' => $title, 'excerpt' => $exc, 'url' => $url, 'icon' => 'car', 'badge' => 'Car Sales', 'score' => $sc );
            }
        }
    }

    if ( function_exists( 'stwc_get_rentals' ) ) {
        foreach ( stwc_get_rentals( array( 'posts_per_page' => 24 ) ) as $rent ) {
            $url   = $rent['url'] ?? st_route_url( '/rental/' . ( $rent['id'] ?? '' ) );
            $key   = $norm_key( $url );
            $title = $rent['name'] ?? '';
            $exc   = sprintf( 'Daily %s • Weekly %s • %s • %s', $rent['daily'] ?? '', $rent['weekly'] ?? '', $rent['trans'] ?? '', $rent['fuel'] ?? '' );
            $sc    = $score_item( $title, $exc, ( $rent['category'] ?? '' ) . ' rental hire' );
            if ( ! isset( $seen[ $key ] ) && $sc > 0 ) {
                $seen[ $key ] = true;
                $results[]    = array( 'title' => $title, 'excerpt' => $exc, 'url' => $url, 'icon' => 'car', 'badge' => 'Car Rental', 'score' => $sc );
            }
        }
    }

    if ( function_exists( 'stwc_get_parts' ) ) {
        foreach ( stwc_get_parts( array( 'posts_per_page' => 24 ) ) as $part ) {
            $url   = $part['url'] ?? st_route_url( '/parts/' . ( $part['id'] ?? '' ) );
            $key   = $norm_key( $url );
            $title = $part['name'] ?? '';
            $exc   = sprintf( '%s • %s • %s', $part['brand'] ?? '', $part['category'] ?? '', $part['price'] ?? '' );
            $sc    = $score_item( $title, $exc, ( $part['category'] ?? '' ) . ' auto parts' );
            if ( ! isset( $seen[ $key ] ) && $sc > 0 ) {
                $seen[ $key ] = true;
                $results[]    = array( 'title' => $title, 'excerpt' => $exc, 'url' => $url, 'icon' => 'wrench', 'badge' => 'Auto Part', 'score' => $sc );
            }
        }
    }

    // 4. 32 Towing Districts & 232 Ghana Locations
    if ( function_exists( 'stwc_get_towing_adverts' ) ) {
        foreach ( stwc_get_towing_adverts() as $adv ) {
            $url   = st_route_url( '/service-areas/' . $adv['slug'] );
            $key   = $norm_key( $url );
            $title = $adv['areaName'] . ' Towing Services';
            $exc   = $adv['description'] ?? '';
            $kw    = implode( ' ', array_merge( array( $adv['areaName'], $adv['region'] ), $adv['landmarks'] ?? array() ) );
            $sc    = $score_item( $title, $exc, $kw );
            if ( ! isset( $seen[ $key ] ) && $sc > 0 ) {
                $seen[ $key ] = true;
                $results[]    = array( 'title' => $title, 'excerpt' => $exc, 'url' => $url, 'icon' => 'map-pin', 'badge' => 'Location', 'score' => $sc );
            }
        }
    }

    if ( function_exists( 'stwc_get_ghana_regions_data' ) ) {
        foreach ( stwc_get_ghana_regions_data() as $reg ) {
            foreach ( $reg['areas'] as $town ) {
                $slug  = sanitize_title( $town );
                $url   = st_route_url( '/service-areas/' . $slug );
                $key   = $norm_key( $url );
                if ( isset( $seen[ $key ] ) ) {
                    continue;
                }
                $title = $town . ' Towing Services';
                $exc   = sprintf( '24/7 emergency towing & roadside recovery in %s (%s).', $town, $reg['region'] );
                $sc    = $score_item( $title, $exc, $reg['region'] . ' ' . $town );
                if ( $sc > 0 ) {
                    $seen[ $key ] = true;
                    $results[]    = array( 'title' => $title, 'excerpt' => $exc, 'url' => $url, 'icon' => 'map-pin', 'badge' => 'Location', 'score' => $sc );
                }
            }
        }
    }

    usort(
        $results,
        function ( $a, $b ) {
            return $b['score'] <=> $a['score'];
        }
    );
}
?>
<main class="min-h-screen bg-slate-50 pt-24 pb-16 md:pb-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <?php st_breadcrumbs( array( array( 'label' => 'Search Results' ) ), 'dark', 'mb-6' ); ?>

        <div class="bg-white rounded-[1px] shadow-sm border border-dark/10 p-6 sm:p-8 md:p-10 mb-8">
            <h1 class="text-3xl md:text-4xl font-black text-dark mb-6 tracking-tight">
                <?php esc_html_e( 'Search', 'santa-towing' ); ?>
            </h1>

            <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="relative" role="search">
                <div class="relative">
                    <input
                        type="text"
                        name="s"
                        value="<?php echo esc_attr( $query ); ?>"
                        placeholder="<?php esc_attr_e( 'Search services, locations, cars...', 'santa-towing' ); ?>"
                        class="w-full bg-slate-50 border-2 border-dark/10 focus:border-primary py-4 pl-14 pr-28 sm:pr-32 text-dark font-medium outline-none transition-all"
                        autofocus
                    />
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 text-dark/40 pointer-events-none">
                        <?php echo st_icon( 'search', 'w-6 h-6' ); ?>
                    </span>
                    <button
                        type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-primary hover:bg-black text-white px-4 sm:px-6 py-2 sm:py-2.5 text-sm sm:text-base font-bold transition-colors rounded-full"
                    >
                        <?php esc_html_e( 'Search', 'santa-towing' ); ?>
                    </button>
                </div>
            </form>
        </div>

        <?php if ( '' !== $query ) : ?>
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-dark">
                        <?php
                        printf(
                            esc_html__( 'Results for "%s"', 'santa-towing' ),
                            esc_html( $query )
                        );
                        ?>
                    </h2>
                    <span class="text-dark/60 font-medium bg-dark/5 px-3 py-1 rounded-full text-sm">
                        <?php
                        printf(
                            esc_html__( '%d found', 'santa-towing' ),
                            count( $results )
                        );
                        ?>
                    </span>
                </div>

                <?php if ( ! empty( $results ) ) : ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ( $results as $res ) : ?>
                            <a
                                href="<?php echo esc_url( $res['url'] ); ?>"
                                class="block bg-white p-6 rounded-[1px] shadow-sm border border-dark/5 hover:border-primary/30 hover:shadow-md transition-all group"
                            >
                                <div class="flex items-start gap-4">
                                    <div class="bg-slate-50 p-3 rounded-[1px] shrink-0 group-hover:bg-primary/10 transition-colors">
                                        <?php echo st_icon( $res['icon'], 'w-5 h-5 text-accent' ); ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-xs font-bold uppercase tracking-wider text-accent/80">
                                                <?php echo esc_html( $res['badge'] ); ?>
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-bold text-dark mb-2 group-hover:text-primary transition-colors line-clamp-1">
                                            <?php echo wp_kses( st_search_highlight( $res['title'], $query ), array( 'mark' => array( 'class' => true ) ) ); ?>
                                        </h3>
                                        <p class="text-dark/70 text-sm leading-relaxed line-clamp-2">
                                            <?php echo wp_kses( st_search_highlight( $res['excerpt'], $query ), array( 'mark' => array( 'class' => true ) ) ); ?>
                                        </p>
                                    </div>
                                    <div class="hidden sm:flex shrink-0 items-center justify-center w-10 h-10 rounded-full bg-slate-50 group-hover:bg-primary text-dark/40 group-hover:text-white transition-colors">
                                        <?php echo st_icon( 'arrow-right', 'w-5 h-5' ); ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="bg-white p-6 sm:p-8 md:p-12 rounded-[1px] text-center border border-dark/10 border-dashed">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <?php echo st_icon( 'search', 'w-8 h-8 text-dark/30' ); ?>
                        </div>
                        <h3 class="text-2xl font-bold text-dark mb-3"><?php esc_html_e( 'No results found', 'santa-towing' ); ?></h3>
                        <p class="text-dark/60 max-w-md mx-auto mb-8">
                            <?php
                            printf(
                                esc_html__( 'We couldn\'t find anything matching "%s". Try searching with a different keyword or check out our popular sections below.', 'santa-towing' ),
                                esc_html( $query )
                            );
                            ?>
                        </p>

                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="<?php echo esc_url( st_route_url( '/towing' ) ); ?>" class="bg-slate-50 hover:bg-primary/10 hover:text-primary text-dark px-5 py-2.5 font-semibold transition-colors text-sm rounded-full"><?php esc_html_e( 'Towing Services', 'santa-towing' ); ?></a>
                            <a href="<?php echo esc_url( st_route_url( '/repairs' ) ); ?>" class="bg-slate-50 hover:bg-primary/10 hover:text-primary text-dark px-5 py-2.5 font-semibold transition-colors text-sm rounded-full"><?php esc_html_e( 'Auto Repairs', 'santa-towing' ); ?></a>
                            <a href="<?php echo esc_url( st_route_url( '/service-areas' ) ); ?>" class="bg-slate-50 hover:bg-primary/10 hover:text-primary text-dark px-5 py-2.5 font-semibold transition-colors text-sm rounded-full"><?php esc_html_e( 'Service Areas', 'santa-towing' ); ?></a>
                            <a href="<?php echo esc_url( st_route_url( '/contact' ) ); ?>" class="bg-slate-50 hover:bg-primary/10 hover:text-primary text-dark px-5 py-2.5 font-semibold transition-colors text-sm rounded-full"><?php esc_html_e( 'Contact Us', 'santa-towing' ); ?></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <!-- Suggestion quick-cards when no search query (1:1 Search.tsx) -->
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                <a href="<?php echo esc_url( st_route_url( '/towing' ) ); ?>" class="flex flex-col items-center justify-center p-6 bg-white rounded-[1px] border border-dark/5 hover:border-primary/50 hover:shadow-md transition-all group text-center gap-3">
                    <div class="w-12 h-12 bg-secondary group-hover:bg-primary rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <?php echo st_icon( 'truck', 'w-6 h-6' ); ?>
                    </div>
                    <span class="font-bold text-dark group-hover:text-primary transition-colors"><?php esc_html_e( 'Towing', 'santa-towing' ); ?></span>
                </a>
                <a href="<?php echo esc_url( st_route_url( '/repairs' ) ); ?>" class="flex flex-col items-center justify-center p-6 bg-white rounded-[1px] border border-dark/5 hover:border-primary/50 hover:shadow-md transition-all group text-center gap-3">
                    <div class="w-12 h-12 bg-secondary group-hover:bg-accent rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <?php echo st_icon( 'wrench', 'w-6 h-6' ); ?>
                    </div>
                    <span class="font-bold text-dark group-hover:text-accent transition-colors"><?php esc_html_e( 'Repairs', 'santa-towing' ); ?></span>
                </a>
                <a href="<?php echo esc_url( st_route_url( '/service-areas' ) ); ?>" class="flex flex-col items-center justify-center p-6 bg-white rounded-[1px] border border-dark/5 hover:border-primary/50 hover:shadow-md transition-all group text-center gap-3">
                    <div class="w-12 h-12 bg-secondary group-hover:bg-primary rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <?php echo st_icon( 'map-pin', 'w-6 h-6' ); ?>
                    </div>
                    <span class="font-bold text-dark group-hover:text-primary transition-colors"><?php esc_html_e( 'Locations', 'santa-towing' ); ?></span>
                </a>
                <a href="<?php echo esc_url( st_route_url( '/faqs' ) ); ?>" class="flex flex-col items-center justify-center p-6 bg-white rounded-[1px] border border-dark/5 hover:border-primary/50 hover:shadow-md transition-all group text-center gap-3">
                    <div class="w-12 h-12 bg-secondary group-hover:bg-primary rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <?php echo st_icon( 'shield', 'w-6 h-6' ); ?>
                    </div>
                    <span class="font-bold text-dark group-hover:text-primary transition-colors"><?php esc_html_e( 'Help & FAQs', 'santa-towing' ); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
