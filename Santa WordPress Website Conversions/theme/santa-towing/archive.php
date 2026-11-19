<?php
/**
 * Archive / Blog Index Template
 *
 * 1:1 visual parity with React src/pages/Blog.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$hero_image = st_theme_image( 'image-15-car-lighting-maintenance.jpg' );
$archive_title = is_archive() ? get_the_archive_title() : __( 'Blog & Resources', 'santa-towing' );
?>
<main class="pt-24 lg:pt-[104px] min-h-screen bg-white">
    <!-- Hero Banner -->
    <section class="relative bg-primary overflow-hidden pt-16 pb-24 md:pt-24 md:pb-32">
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"></div>
        <div class="absolute inset-0 bg-dark/80 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="mb-4 inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 border border-white/20 text-white text-sm font-semibold backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-accent animate-ping"></span>
                <span><?php echo esc_html( st_get_content( 'blog.badge', 'Insights & Guides' ) ); ?></span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
                <?php echo wp_kses_post( $archive_title ); ?>
            </h1>
            <p class="text-white/80 text-lg max-w-2xl mx-auto mb-10">
                <?php echo esc_html( st_get_content( 'blog.description', 'Expert advice, maintenance tips, safety guidelines, and company news to keep you moving safely on the road.' ) ); ?>
            </p>

            <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="max-w-2xl mx-auto bg-white p-2 rounded-[1px] flex items-center shadow-2xl">
                <div class="pl-4 pr-2 text-dark/70"><?php echo st_icon( 'search', 'w-6 h-6' ); ?></div>
                <input type="text" name="s" placeholder="<?php esc_attr_e( 'Search articles by keyword...', 'santa-towing' ); ?>" class="flex-1 py-3 px-2 text-dark focus:outline-none bg-transparent" />
                <button type="submit" class="bg-accent hover:bg-accent/90 text-white px-6 py-3 font-bold transition-all rounded-full">
                    <?php esc_html_e( 'Search', 'santa-towing' ); ?>
                </button>
            </form>
        </div>
    </section>

    <!-- Categories Bar -->
    <?php
    $blog_cats = get_categories( array( 'hide_empty' => false ) );
    if ( ! empty( $blog_cats ) ) :
        ?>
        <section class="bg-white border-b border-dark/20 sticky top-[72px] lg:top-[104px] z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex overflow-x-auto no-scrollbar py-4 gap-2">
                    <a href="<?php echo esc_url( st_route_url( '/blog' ) ); ?>" class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all <?php echo ! is_category() ? 'bg-primary text-white shadow-md' : 'bg-slate-100 text-dark/70 hover:bg-slate-200 hover:text-dark'; ?>">
                        <?php esc_html_e( 'All', 'santa-towing' ); ?>
                    </a>
                    <?php foreach ( $blog_cats as $cat ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all <?php echo is_category( $cat->term_id ) ? 'bg-primary text-white shadow-md' : 'bg-slate-100 text-dark/70 hover:bg-slate-200 hover:text-dark'; ?>">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 lg:py-20">
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
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <div class="bg-slate-50 p-10 rounded-[1px] border border-dark/10 text-center">
                <h2 class="text-2xl font-bold text-dark mb-2"><?php esc_html_e( 'No Articles Found', 'santa-towing' ); ?></h2>
                <p class="text-dark/70"><?php esc_html_e( 'New automotive tips and guides are published regularly.', 'santa-towing' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
