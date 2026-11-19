<?php
/**
 * Single Blog Post Template
 *
 * 1:1 visual parity with React src/pages/BlogPost.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$phone_raw = st_get_content( 'global.links.phone', '0244753849' );
$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );

while ( have_posts() ) :
    the_post();
    $post_id    = get_the_ID();
    $title      = get_the_title();
    $excerpt    = get_the_excerpt();
    $cats       = get_the_category();
    $category   = ! empty( $cats ) ? $cats[0]->name : __( 'Automotive Guides', 'santa-towing' );
    $cat_link   = ! empty( $cats ) ? get_category_link( $cats[0]->term_id ) : st_route_url( '/blog' );
    $thumb_url  = get_the_post_thumbnail_url( $post_id, 'full' ) ?: st_theme_image( 'image-1-towing-vehicle.jpg' );
    $author     = get_the_author() ?: 'Santa Towing Editorial';
    $author_img = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 128 ) ) ?: st_theme_image( 'avatar-dispatch-director-256.jpg' );
    $read_time  = get_post_meta( $post_id, '_st_read_time', true ) ?: st_reading_time( $post_id );
    $tags       = get_the_tags();
    $permalink  = get_permalink();
    $prev_post  = get_previous_post();
    $next_post  = get_next_post();

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => $title,
        'image'         => array( $thumb_url ),
        'datePublished' => get_the_date( 'c' ),
        'author'        => array(
            array(
                '@type' => 'Person',
                'name'  => $author,
            ),
        ),
    );
    ?>
    <main class="pt-24 lg:pt-[104px] min-h-screen bg-white">
        <div id="st-reading-progress" class="fixed top-0 left-0 h-1 bg-accent z-[60] transition-all duration-75" style="width:0%"></div>
        <script type="application/ld+json"><?php echo wp_json_encode( $schema ); ?></script>

        <!-- Breadcrumb Navigation -->
        <div class="bg-white border-b border-dark/20 py-4 sticky top-[72px] lg:top-[104px] z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center text-sm text-dark/70 overflow-x-auto no-scrollbar whitespace-nowrap">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Home', 'santa-towing' ); ?></a>
                <span class="mx-2 shrink-0"><?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?></span>
                <a href="<?php echo esc_url( st_route_url( '/blog' ) ); ?>" class="hover:text-accent transition-colors"><?php esc_html_e( 'Blog', 'santa-towing' ); ?></a>
                <span class="mx-2 shrink-0"><?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?></span>
                <a href="<?php echo esc_url( $cat_link ); ?>" class="hover:text-accent transition-colors"><?php echo esc_html( $category ); ?></a>
                <span class="mx-2 shrink-0"><?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?></span>
                <span class="text-dark font-medium truncate"><?php echo esc_html( $title ); ?></span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="flex flex-col lg:flex-row gap-8 md:gap-12 relative">

                <!-- Main Article Content -->
                <div class="w-full lg:w-2/3">
                    <div class="mb-8">
                        <span class="inline-block bg-accent/10 text-accent font-bold px-3 py-1 text-sm mb-4">
                            <?php echo esc_html( $category ); ?>
                        </span>
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-dark mb-6 leading-tight">
                            <?php echo esc_html( $title ); ?>
                        </h1>

                        <div class="flex flex-wrap items-center gap-6 text-sm text-dark/70 py-4 border-y border-dark/10">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo esc_url( $author_img ); ?>" alt="<?php echo esc_attr( $author ); ?>" class="w-10 h-10 rounded-[1px] border border-dark/10 object-cover" />
                                <span class="font-bold text-dark"><?php echo esc_html( $author ); ?></span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1.5"><?php echo st_icon( 'calendar', 'w-4 h-4' ); ?> <?php echo esc_html( get_the_date() ); ?></span>
                                <span class="flex items-center gap-1.5"><?php echo st_icon( 'clock', 'w-4 h-4' ); ?> <?php echo esc_html( $read_time ); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1px] overflow-hidden mb-12 shadow-md">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="w-full h-auto max-h-[500px] object-cover" />
                    </div>

                    <article id="st-article-body" class="prose prose-lg prose-slate max-w-none mb-12 prose-headings:font-bold prose-headings:text-dark prose-a:text-accent hover:prose-a:text-red-700">
                        <?php the_content(); ?>
                    </article>

                    <!-- Tags & Share -->
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 py-6 border-y border-dark/10 mb-12">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-bold text-dark mr-2"><?php esc_html_e( 'Tags:', 'santa-towing' ); ?></span>
                            <?php if ( $tags ) : ?>
                                <?php foreach ( $tags as $tag ) : ?>
                                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="bg-slate-100 hover:bg-slate-200 text-dark/70 hover:text-dark text-xs font-bold px-3 py-1.5 transition-colors rounded-full">
                                        <?php echo esc_html( $tag->name ); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <a href="<?php echo esc_url( st_route_url( '/blog' ) ); ?>" class="bg-slate-100 hover:bg-slate-200 text-dark/70 hover:text-dark text-xs font-bold px-3 py-1.5 transition-colors rounded-full">
                                    <?php echo esc_html( $category ); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-dark"><?php esc_html_e( 'Share:', 'santa-towing' ); ?></span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $permalink ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-100 flex items-center justify-center hover:bg-primary hover:text-white transition-colors text-dark/60 rounded-full" aria-label="Share on Facebook">
                                <?php echo st_icon( 'facebook', 'w-4 h-4' ); ?>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( $permalink ); ?>&text=<?php echo rawurlencode( $title ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-100 flex items-center justify-center hover:bg-primary hover:text-white transition-colors text-dark/60 rounded-full" aria-label="Share on Twitter">
                                <?php echo st_icon( 'twitter', 'w-4 h-4' ); ?>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode( $permalink ); ?>&title=<?php echo rawurlencode( $title ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-100 flex items-center justify-center hover:bg-primary hover:text-white transition-colors text-dark/60 rounded-full" aria-label="Share on LinkedIn">
                                <?php echo st_icon( 'linkedin', 'w-4 h-4' ); ?>
                            </a>
                            <button type="button" data-st-copy-url="<?php echo esc_url( $permalink ); ?>" class="w-9 h-9 bg-slate-100 flex items-center justify-center hover:bg-dark hover:text-white transition-colors text-dark/60 rounded-full relative" aria-label="Copy link">
                                <?php echo st_icon( 'link-2', 'w-4 h-4' ); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Prev / Next Post Navigation -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 py-6 mb-12">
                        <div class="w-full sm:w-1/2 flex justify-start">
                            <?php if ( $prev_post ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="group flex items-center gap-3 hover:bg-slate-50 p-3 rounded-[1px] transition-colors max-w-full">
                                    <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-xl group-hover:bg-accent group-hover:text-white transition-colors">
                                        <?php echo st_icon( 'chevron-left', 'w-5 h-5' ); ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="text-xs text-dark/50 font-bold uppercase tracking-wider mb-0.5"><?php esc_html_e( 'Previous Article', 'santa-towing' ); ?></div>
                                        <div class="text-sm font-bold text-dark group-hover:text-accent truncate"><?php echo esc_html( get_the_title( $prev_post ) ); ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="w-full sm:w-1/2 flex justify-end">
                            <?php if ( $next_post ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="group flex items-center gap-3 hover:bg-slate-50 p-3 rounded-[1px] transition-colors max-w-full text-right">
                                    <div class="overflow-hidden">
                                        <div class="text-xs text-dark/50 font-bold uppercase tracking-wider mb-0.5"><?php esc_html_e( 'Next Article', 'santa-towing' ); ?></div>
                                        <div class="text-sm font-bold text-dark group-hover:text-accent truncate"><?php echo esc_html( get_the_title( $next_post ) ); ?></div>
                                    </div>
                                    <div class="w-10 h-10 shrink-0 bg-slate-100 flex items-center justify-center rounded-xl group-hover:bg-accent group-hover:text-white transition-colors">
                                        <?php echo st_icon( 'chevron-right', 'w-5 h-5' ); ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- CTA Box -->
                    <div class="bg-primary text-white rounded-[1px] p-6 sm:p-8 shadow-xl relative overflow-hidden mb-12">
                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-white opacity-5 rounded-[1px] blur-2xl"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div>
                                <h3 class="text-2xl font-bold mb-2 flex items-center gap-2">
                                    <?php echo st_icon( 'alert-triangle', 'w-6 h-6 text-accent' ); ?>
                                    <span><?php esc_html_e( 'Need Towing Assistance?', 'santa-towing' ); ?></span>
                                </h3>
                                <p class="text-white/80 max-w-md"><?php esc_html_e( "Our expert mechanics and towing fleet are available 24/7 across Greater Accra. Don't stay stranded.", 'santa-towing' ); ?></p>
                            </div>
                            <div class="flex flex-col gap-3 w-full md:w-auto shrink-0">
                                <a href="<?php echo esc_url( $phone_tel ); ?>" class="bg-accent hover:bg-accent/90 text-white font-bold px-6 py-3 text-center transition-all shadow-md rounded-full">
                                    <?php echo esc_html( 'Call ' . $phone_raw ); ?>
                                </a>
                                <a href="<?php echo esc_url( st_route_url( '/towing' ) ); ?>" class="bg-white/10 hover:bg-white/20 text-white font-bold px-6 py-3 text-center transition-all rounded-full">
                                    <?php esc_html_e( 'View Towing Services', 'santa-towing' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="w-full lg:w-1/3">
                    <div class="sticky top-[160px] space-y-8">
                        <div class="bg-slate-50 rounded-[1px] p-6 border border-dark/5 shadow-sm text-center">
                            <img src="<?php echo esc_url( $author_img ); ?>" alt="<?php echo esc_attr( $author ); ?>" class="w-20 h-20 rounded-[1px] mx-auto mb-4 border-2 border-white shadow-sm object-cover" />
                            <h3 class="font-bold text-dark text-lg mb-1"><?php echo esc_html( $author ); ?></h3>
                            <p class="text-accent text-sm font-bold mb-4 uppercase tracking-wider"><?php esc_html_e( 'Automotive Expert', 'santa-towing' ); ?></p>
                            <p class="text-sm text-dark/70 mb-4"><?php esc_html_e( 'Sharing industry insights and actionable advice to keep drivers safe and their cars running perfectly.', 'santa-towing' ); ?></p>
                            <a href="<?php echo esc_url( st_route_url( '/contact' ) ); ?>" class="text-primary hover:text-accent text-sm font-bold inline-flex items-center gap-1 transition-colors">
                                <span><?php esc_html_e( 'Contact Author', 'santa-towing' ); ?></span>
                                <?php echo st_icon( 'arrow-right', 'w-4 h-4' ); ?>
                            </a>
                        </div>

                        <?php
                        $related = get_posts(
                            array(
                                'post_type'    => 'post',
                                'numberposts'  => 3,
                                'post__not_in' => array( $post_id ),
                                'post_status'  => 'publish',
                            )
                        );
                        if ( $related ) :
                            ?>
                            <div class="bg-white rounded-[1px] border border-dark/10 shadow-sm overflow-hidden">
                                <div class="p-6 border-b border-dark/5 bg-slate-50">
                                    <h3 class="font-bold text-dark text-lg"><?php esc_html_e( 'Related Articles', 'santa-towing' ); ?></h3>
                                </div>
                                <div class="p-6 space-y-6">
                                    <?php
                                    foreach ( $related as $rel ) :
                                        $rcats = get_the_category( $rel->ID );
                                        $rcat  = ! empty( $rcats ) ? $rcats[0]->name : __( 'Tips', 'santa-towing' );
                                        $rimg  = get_the_post_thumbnail_url( $rel->ID, 'medium' ) ?: st_theme_image( 'image-1-towing-vehicle.jpg' );
                                        ?>
                                        <a href="<?php echo esc_url( get_permalink( $rel ) ); ?>" class="group flex gap-4">
                                            <div class="w-20 h-20 shrink-0 rounded-[1px] overflow-hidden bg-slate-100">
                                                <img src="<?php echo esc_url( $rimg ); ?>" alt="<?php echo esc_attr( get_the_title( $rel ) ); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <span class="text-xs font-bold uppercase tracking-wider text-accent mb-1"><?php echo esc_html( $rcat ); ?></span>
                                                <h4 class="font-bold text-dark text-sm group-hover:text-accent transition-colors line-clamp-2 leading-tight">
                                                    <?php echo esc_html( get_the_title( $rel ) ); ?>
                                                </h4>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="bg-white rounded-[1px] border border-dark/10 shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-dark/5 bg-slate-50">
                                <h3 class="font-bold text-dark text-lg"><?php esc_html_e( 'Our Service Areas', 'santa-towing' ); ?></h3>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-dark/70 mb-4"><?php esc_html_e( 'We provide 24/7 towing and recovery across Greater Accra.', 'santa-towing' ); ?></p>
                                <a href="<?php echo esc_url( st_route_url( '/service-areas' ) ); ?>" class="text-accent hover:text-primary font-bold text-sm inline-flex items-center gap-1 transition-colors">
                                    <span><?php esc_html_e( 'View All Locations', 'santa-towing' ); ?></span>
                                    <?php echo st_icon( 'chevron-right', 'w-4 h-4' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </main>
    <?php
endwhile;

get_footer();
