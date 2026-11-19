<?php
/**
 * Car Rentals Archive Template (`st_rental`)
 *
 * 1:1 visual parity with React src/pages/Rental.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$hero_image = st_theme_image( 'image-15-car-lighting-maintenance.jpg' );
$phone_tel  = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp   = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
?>
<main class="pt-24 lg:pt-[104px] bg-slate-50 min-h-screen">
    <div class="bg-slate-50 border-b border-dark/10 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php st_breadcrumbs( array( array( 'label' => 'Car Rentals' ) ) ); ?>
        </div>
    </div>

    <section class="relative bg-primary overflow-hidden pt-12 pb-20 md:pt-20 md:pb-28">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/80 to-transparent"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    <?php echo esc_html( st_get_content( 'rental.title', 'Drive Your Way - Premium Car Rentals for Every Journey' ) ); ?>
                </h1>
                <p class="text-lg md:text-xl text-white/70 mb-8">
                    <?php echo esc_html( st_get_content( 'rental.description', 'Flexible daily, weekly, and monthly rentals with a modern, well-maintained fleet.' ) ); ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#fleet" class="bg-accent hover:bg-accent/90 text-white px-6 sm:px-8 py-4 font-bold transition-all shadow-lg shadow-accent/30 rounded-full"><?php esc_html_e( 'Browse Fleet', 'santa-towing' ); ?></a>
                    <a href="<?php echo esc_url( $phone_tel ); ?>" class="bg-white/10 hover:bg-white/20 text-white backdrop-blur-md px-6 sm:px-8 py-4 font-bold transition-all rounded-full flex items-center gap-2"><?php echo st_icon( 'phone', 'w-5 h-5' ); ?> <?php esc_html_e( 'Call Now', 'santa-towing' ); ?></a>
                    <a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="bg-white hover:bg-white/90 text-primary px-6 sm:px-8 py-4 font-bold transition-all shadow-lg flex items-center gap-2 rounded-full"><?php echo st_icon( 'message-circle', 'w-5 h-5' ); ?> <?php esc_html_e( 'WhatsApp', 'santa-towing' ); ?></a>
                </div>
            </div>
        </div>
    </section>

    <section id="fleet" class="py-12 md:py-16" data-st-filter-group="rental">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-dark"><?php esc_html_e( 'Rental Fleet', 'santa-towing' ); ?></h2>
                <div class="flex bg-white p-1.5 rounded-[1px] border border-dark/10 shadow-sm overflow-x-auto no-scrollbar">
                    <?php foreach ( array( 'All', 'Economy', 'Sedan', 'SUV', 'Pickup', 'Luxury', 'Van' ) as $idx => $tab ) : ?>
                        <button
                            type="button"
                            data-st-filter-btn="<?php echo esc_attr( $tab ); ?>"
                            class="px-4 py-2 rounded-full font-bold text-sm transition-all whitespace-nowrap <?php echo 0 === $idx ? 'bg-accent text-white shadow-md' : 'text-dark/70 hover:text-dark hover:bg-slate-100'; ?>"
                        >
                            <?php echo esc_html( $tab ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ( have_posts() ) : ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        $pid = get_the_ID();
                        get_template_part(
                            'template-parts/card-rental',
                            null,
                            array(
                                'rental' => array(
                                    'id'       => $pid,
                                    'name'     => get_the_title(),
                                    'category' => get_post_meta( $pid, '_st_rental_type', true ) ?: 'Sedan',
                                    'daily'    => get_post_meta( $pid, '_st_daily_rate', true ) ?: 'GH₵ 600',
                                    'weekly'   => get_post_meta( $pid, '_st_weekly_rate', true ) ?: 'GH₵ 3,800',
                                    'monthly'  => get_post_meta( $pid, '_st_monthly_rate', true ) ?: 'GH₵ 14,000',
                                    'seats'    => get_post_meta( $pid, '_st_seats', true ) ?: '5',
                                    'trans'    => get_post_meta( $pid, '_st_transmission', true ) ?: 'Automatic',
                                    'fuel'     => get_post_meta( $pid, '_st_fuel', true ) ?: 'Petrol',
                                    'image'    => get_the_post_thumbnail_url( $pid, 'large' ) ?: st_theme_image( 'image-3-sedan-toyota-corolla.jpg' ),
                                    'url'      => get_permalink( $pid ),
                                ),
                            )
                        );
                    endwhile;
                    ?>
                </div>
                <div class="mt-12 flex justify-center"><?php the_posts_pagination(); ?></div>
            <?php else : ?>
                <div class="bg-white p-10 rounded-[1px] border border-dark/10 text-center">
                    <p class="text-dark/70"><?php esc_html_e( 'No rental vehicles found.', 'santa-towing' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
