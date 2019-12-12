<?php
/**
 * Theme functions and definitions.
 */
function sweatytheme_child_enqueue_styles() {

    wp_enqueue_style( 'SweatyTheme-style' , get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'SweatyTheme-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'SweatyTheme-style' ),
        wp_get_theme()->get('Version')
    );

    if ( is_rtl() ) {
        wp_enqueue_style( 'SweatyTheme-rtl', get_template_directory_uri() . '/rtl.css' );
    }

}

add_action(  'wp_enqueue_scripts', 'sweatytheme_child_enqueue_styles' );


/* =========== Sparrow Custom Functions =========== */
include_once get_stylesheet_directory() . '/assets/include/sparrow-mods.php';