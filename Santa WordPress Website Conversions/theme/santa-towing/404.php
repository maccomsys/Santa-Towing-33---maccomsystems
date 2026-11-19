<?php
/**
 * 404 Not Found Template
 *
 * 1:1 visual parity with React src/pages/NotFound.tsx
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main class="pt-24 pb-16 md:pb-20 bg-slate-50 min-h-[80vh] flex flex-col items-center justify-center">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-2xl text-center">

        <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-8 text-primary">
            <?php echo st_icon( 'map-pin-off', 'w-10 h-10 text-primary' ); ?>
        </div>

        <h1 class="text-7xl md:text-9xl font-black text-dark tracking-tighter mb-4">
            404
        </h1>

        <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
            <?php esc_html_e( 'Page Not Found', 'santa-towing' ); ?>
        </h2>

        <p class="text-lg text-dark/70 mb-8 max-w-lg mx-auto">
            <?php esc_html_e( 'Sorry, the page you are looking for does not exist, has been moved, or may no longer be available.', 'santa-towing' ); ?>
        </p>

        <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="max-w-md mx-auto mb-12 relative">
            <input
                type="text"
                name="s"
                placeholder="<?php esc_attr_e( 'Search our website...', 'santa-towing' ); ?>"
                class="w-full bg-white border-2 border-dark/10 focus:border-primary py-3 pl-12 pr-4 text-dark font-medium outline-none transition-all"
            />
            <span class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-dark/40 pointer-events-none">
                <?php echo st_icon( 'search', 'w-5 h-5' ); ?>
            </span>
            <button type="submit" class="hidden rounded-full"><?php esc_html_e( 'Search', 'santa-towing' ); ?></button>
        </form>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button
                type="button"
                onclick="window.history.length > 1 ? window.history.back() : (window.location.href = '<?php echo esc_url( home_url( '/' ) ); ?>')"
                class="w-full sm:w-auto bg-white border border-dark/20 hover:border-dark text-dark px-4 sm:px-8 py-4 font-bold flex items-center justify-center gap-3 transition-colors"
            >
                <?php echo st_icon( 'arrow-left', 'w-5 h-5' ); ?>
                <span><?php esc_html_e( 'Go Back', 'santa-towing' ); ?></span>
            </button>

            <a
                href="<?php echo esc_url( home_url( '/' ) ); ?>"
                class="w-full sm:w-auto bg-primary hover:bg-black text-white px-4 sm:px-8 py-4 font-bold flex items-center justify-center gap-3 transition-colors rounded-full"
            >
                <?php echo st_icon( 'home', 'w-5 h-5' ); ?>
                <span><?php esc_html_e( 'Go to Homepage', 'santa-towing' ); ?></span>
            </a>

            <a
                href="<?php echo esc_url( st_route_url( '/contact' ) ); ?>"
                class="w-full sm:w-auto bg-white border border-dark/20 hover:border-dark text-dark px-4 sm:px-8 py-4 font-bold flex items-center justify-center gap-3 transition-colors"
            >
                <?php echo st_icon( 'phone', 'w-5 h-5' ); ?>
                <span><?php esc_html_e( 'Contact Us', 'santa-towing' ); ?></span>
            </a>
        </div>

    </div>
</main>
<?php
get_footer();
