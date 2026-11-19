<?php
/**
 * Car Sales Archive Template (`st_car`)
 *
 * 1:1 visual parity with React src/pages/Sales.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$hero_image = st_theme_image( 'image-1-towing-vehicle.jpg' );
?>
<main class="pt-24 lg:pt-[104px] bg-slate-50 min-h-screen">
    <div class="bg-white border-b border-dark/10 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php st_breadcrumbs( array( array( 'label' => 'Car Sales' ) ) ); ?>
        </div>
    </div>

    <!-- Hero Banner -->
    <section class="relative bg-primary overflow-hidden pt-12 pb-20 md:pt-20 md:pb-28">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                <?php esc_html_e( 'Find Your Next Car With', 'santa-towing' ); ?> <span class="text-accent"><?php esc_html_e( 'Confidence', 'santa-towing' ); ?></span>
            </h1>
            <p class="text-lg md:text-xl text-white/70 mb-10 max-w-3xl mx-auto">
                <?php echo esc_html( st_get_content( 'sales.description', 'Explore certified new, foreign-used, and inspected Ghana-used cars with flexible financing and warranty options.' ) ); ?>
            </p>
        </div>
    </section>

    <section class="py-12 md:py-16" data-st-filter-group="car">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-dark"><?php esc_html_e( 'Available Inventory', 'santa-towing' ); ?></h2>
                <div class="flex bg-white p-1.5 rounded-[1px] border border-dark/10 shadow-sm overflow-x-auto no-scrollbar">
                    <?php foreach ( array( 'All', 'New', 'Foreign Used', 'Ghana Used' ) as $idx => $tab ) : ?>
                        <button
                            type="button"
                            data-st-filter-btn="<?php echo esc_attr( $tab ); ?>"
                            class="px-5 py-2 rounded-full font-bold text-sm transition-all whitespace-nowrap <?php echo 0 === $idx ? 'bg-accent text-white shadow-md' : 'text-dark/70 hover:text-dark hover:bg-slate-100'; ?>"
                        >
                            <?php echo esc_html( $tab ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ( have_posts() ) : ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        $pid = get_the_ID();
                        get_template_part(
                            'template-parts/card-car',
                            null,
                            array(
                                'car' => array(
                                    'id'           => $pid,
                                    'name'         => get_the_title(),
                                    'price'        => get_post_meta( $pid, '_st_price', true ) ?: 'Contact for Price',
                                    'year'         => get_post_meta( $pid, '_st_year', true ) ?: '2024',
                                    'fuel'         => get_post_meta( $pid, '_st_fuel', true ) ?: 'Petrol',
                                    'transmission' => get_post_meta( $pid, '_st_transmission', true ) ?: 'Automatic',
                                    'mileage'      => get_post_meta( $pid, '_st_mileage', true ) ?: '0 km',
                                    'type'         => get_post_meta( $pid, '_st_car_type', true ) ?: 'New',
                                    'image'        => get_the_post_thumbnail_url( $pid, 'large' ) ?: st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ),
                                    'url'          => get_permalink( $pid ),
                                ),
                            )
                        );
                    endwhile;
                    ?>
                </div>
                <div class="mt-12 flex justify-center"><?php the_posts_pagination(); ?></div>
            <?php else : ?>
                <div class="bg-white p-10 rounded-[1px] border border-dark/10 text-center">
                    <p class="text-dark/70"><?php esc_html_e( 'No cars currently match your query.', 'santa-towing' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
