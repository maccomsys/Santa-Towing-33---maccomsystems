<?php
/**
 * Main Index Fallback Template
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main class="pt-24 lg:pt-[104px] min-h-screen bg-slate-50 pb-20">
    <div class="bg-white border-b border-dark/10 py-4 mb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php st_breadcrumbs( array( array( 'label' => single_post_title( '', false ) ?: __( 'Latest Updates', 'santa-towing' ) ) ) ); ?>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ( have_posts() ) : ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <?php
                while ( have_posts() ) :
                    the_post();
                    get_template_part( 'template-parts/card-blog' );
                endwhile;
                ?>
            </div>

            <div class="mt-12 flex justify-center">
                <?php
                the_posts_pagination(
                    array(
                        'mid_size'  => 2,
                        'prev_text' => esc_html__( 'Previous', 'santa-towing' ),
                        'next_text' => esc_html__( 'Next', 'santa-towing' ),
                    )
                );
                ?>
            </div>
        <?php else : ?>
            <div class="bg-white p-10 rounded-[1px] border border-dark/10 text-center max-w-2xl mx-auto">
                <h1 class="text-2xl font-bold text-dark mb-3"><?php esc_html_e( 'Nothing Found', 'santa-towing' ); ?></h1>
                <p class="text-dark/70 mb-6"><?php esc_html_e( 'No content matched this request. Explore our services or search below.', 'santa-towing' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 bg-primary hover:bg-black text-white px-6 py-3 font-bold rounded-full transition-colors">
                    <?php echo st_icon( 'home', 'w-4 h-4' ); ?>
                    <span><?php esc_html_e( 'Back to Home', 'santa-towing' ); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
