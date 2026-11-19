<?php
/**
 * Theme Header Template
 * Matches App.tsx global wrapper + TopBar + Fixed/Sticky NavBar
 *
 * @package Santa_Towing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen bg-white text-dark font-sans antialiased flex flex-col' ); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:bg-primary focus:text-white focus:px-4 focus:py-2 focus:rounded-full focus:font-bold">
    <?php esc_html_e( 'Skip to main content', 'santa-towing' ); ?>
</a>

<div id="page" class="min-h-screen bg-white text-dark font-sans flex flex-col w-full">
    <?php
    if ( ! is_page_template( 'page-templates/template-elementor-blank.php' ) ) {
        get_template_part( 'template-parts/top-bar' );
        get_template_part( 'template-parts/navbar' );
    }
    ?>
    <div id="main-content" class="flex-1 w-full">
