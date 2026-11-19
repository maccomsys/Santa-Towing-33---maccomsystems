<?php
/**
 * Auto Parts Archive Template (`st_part`)
 *
 * 1:1 visual parity with React src/pages/Parts.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$hero_image = st_theme_image( 'image-12-tyre-maintenance.jpg' );
?>
<main class="pt-24 lg:pt-[104px] min-h-screen bg-slate-50">
    <div class="bg-slate-50 border-b border-dark/10 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php st_breadcrumbs( array( array( 'label' => 'Auto Parts' ) ) ); ?>
        </div>
    </div>

    <section class="relative bg-primary overflow-hidden pt-12 pb-24 md:pt-20 md:pb-28">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                <?php echo esc_html( st_get_content( 'parts.title', 'Original Auto Parts & Batteries' ) ); ?>
            </h1>
            <p class="text-lg md:text-xl text-white/70 mb-10 max-w-3xl mx-auto">
                <?php echo esc_html( st_get_content( 'parts.description', 'Quality batteries, filters, brake pads, tires, and accessories for every make and model.' ) ); ?>
            </p>
        </div>
    </section>

    <section class="py-12 md:py-16" data-st-filter-group="part">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-dark"><?php esc_html_e( 'Spare Parts Catalog', 'santa-towing' ); ?></h2>
                <div class="flex bg-white p-1.5 rounded-[1px] border border-dark/10 shadow-sm overflow-x-auto no-scrollbar">
                    <?php foreach ( array( 'All', 'Car Batteries', 'Engine Oil & Fluids', 'Brake Pads & Discs', 'Tires', 'Filters', 'Suspension Parts', 'Lighting' ) as $idx => $tab ) : ?>
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
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        $pid = get_the_ID();
                        get_template_part(
                            'template-parts/card-part',
                            null,
                            array(
                                'part' => array(
                                    'id'       => $pid,
                                    'name'     => get_the_title(),
                                    'brand'    => get_post_meta( $pid, '_st_brand', true ) ?: 'Genuine OEM',
                                    'category' => get_post_meta( $pid, '_st_category', true ) ?: 'Auto Parts',
                                    'price'    => get_post_meta( $pid, '_st_price', true ) ?: 'GH₵ 450',
                                    'rating'   => get_post_meta( $pid, '_st_rating', true ) ?: 5.0,
                                    'image'    => get_the_post_thumbnail_url( $pid, 'large' ) ?: st_theme_image( 'image-9-car-battery-maintenance.jpg' ),
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
                    <p class="text-dark/70"><?php esc_html_e( 'No auto parts found.', 'santa-towing' ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
