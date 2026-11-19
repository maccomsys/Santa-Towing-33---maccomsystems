<?php
/**
 * Template Name: Policy & Legal Page
 * Template Post Type: page
 *
 * 1:1 visual parity with React src/pages/PrivacyPolicy.tsx & legal policy templates.
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$phone_raw = st_get_content( 'global.links.phone', '0244753849' );
$phone_tel = st_get_content( 'global.links.phoneTel', 'tel:0244753849' );
$whatsapp  = st_get_content( 'global.links.whatsapp', 'https://wa.me/233244753849' );
$address   = st_get_content( 'global.links.address', '123 Spintex Road, Accra, Ghana' );

while ( have_posts() ) :
    the_post();
    $page_title    = get_the_title();
    $page_subtitle = get_post_meta( get_the_ID(), '_st_policy_subtitle', true );
    if ( empty( $page_subtitle ) ) {
        $page_subtitle = get_the_excerpt() ?: __( 'Official company policy, terms, and customer protection standards at Santa Towing & Garage Services Ltd.', 'santa-towing' );
    }
    ?>
    <main class="pt-24 lg:pt-[104px] bg-slate-50 min-h-screen">
        <!-- Page Header -->
        <section class="bg-dark text-white py-10 md:py-16 lg:py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-primary/20"></div>
            <div class="max-w-4xl mx-auto px-4 relative z-10 text-center flex flex-col items-center">
                <?php st_breadcrumbs( array( array( 'label' => $page_title ) ), 'light', 'mb-6 justify-center' ); ?>
                <div class="text-accent mx-auto mb-6">
                    <?php echo st_icon( 'shield-check', 'w-16 h-16' ); ?>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4"><?php echo esc_html( $page_title ); ?></h1>
                <p class="text-lg text-white/85 max-w-2xl mx-auto">
                    <?php echo esc_html( $page_subtitle ); ?>
                </p>
            </div>
        </section>

        <!-- Content Section -->
        <section class="py-10 md:py-16 lg:py-24">
            <div class="max-w-4xl mx-auto px-4">
                <div data-st-reveal class="bg-white p-6 sm:p-8 md:p-12 rounded-[1px] shadow-sm border border-dark/10">
                    <div class="prose prose-slate max-w-none prose-headings:text-dark prose-p:text-dark/80 prose-a:text-primary">
                        <p class="text-sm text-dark/50 mb-8 font-bold">
                            <?php
                            printf(
                                /* translators: %s: last modified date */
                                esc_html__( 'Last Updated: %s', 'santa-towing' ),
                                esc_html( get_the_modified_date( 'F j, Y' ) )
                            );
                            ?>
                        </p>

                        <?php the_content(); ?>

                        <div class="bg-slate-50 p-6 rounded-[1px] mt-10 border border-dark/10 flex flex-col sm:flex-row gap-6 not-prose">
                            <a
                                href="<?php echo esc_url( $whatsapp ); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 text-dark/80 hover:text-accent transition-colors"
                            >
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                    <?php echo st_icon( 'message-circle', 'w-5 h-5' ); ?>
                                </div>
                                <span class="font-medium"><?php esc_html_e( 'Chat on WhatsApp', 'santa-towing' ); ?></span>
                            </a>
                            <a
                                href="<?php echo esc_url( $phone_tel ); ?>"
                                class="flex items-center gap-3 text-dark/80 hover:text-accent transition-colors"
                            >
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                    <?php echo st_icon( 'phone', 'w-5 h-5' ); ?>
                                </div>
                                <span class="font-medium"><?php echo esc_html( $phone_raw ); ?></span>
                            </a>
                            <div class="flex items-center gap-3 text-dark/80">
                                <div class="w-10 h-10 rounded-xl bg-secondary flex items-center justify-center text-white shrink-0">
                                    <?php echo st_icon( 'map-pin', 'w-5 h-5' ); ?>
                                </div>
                                <span class="font-medium"><?php echo esc_html( $address ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
endwhile;

get_footer();
