<?php
/**
 * Default Singular Page Template
 *
 * Automatically adapts to Elementor Free (seamless full-width) vs standard Gutenberg/Classic pages.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    if ( st_is_elementor_page( get_the_ID() ) ) :
        ?>
        <main id="primary" class="pt-20 lg:pt-[76px] min-h-screen bg-white">
            <?php the_content(); ?>
        </main>
        <?php
    else :
        ?>
        <main id="primary" class="pt-24 lg:pt-[104px] bg-slate-50 min-h-screen pb-16 md:pb-20">
            <div class="bg-white border-b border-dark/10 py-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <?php st_breadcrumbs( array( array( 'label' => get_the_title() ) ) ); ?>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
                <article class="bg-white p-6 sm:p-8 md:p-12 rounded-[1px] border border-dark/10 shadow-sm">
                    <h1 class="text-3xl md:text-5xl font-bold text-dark mb-6 leading-tight">
                        <?php the_title(); ?>
                    </h1>
                    <div class="prose prose-slate max-w-none prose-headings:text-dark prose-p:text-dark/80 prose-a:text-primary">
                        <?php the_content(); ?>
                    </div>
                </article>
            </div>
        </main>
        <?php
    endif;
endwhile;

get_footer();
