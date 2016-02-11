<?php
/**
Plugin Name: WPAdverts Snippets - Assets In Head
Version: 1.0
Author: Greg Winiarski
Description: This plugin will load all WPAdverts scripts and styles in <code>&lt;head&gt;</code> section, instead of footer.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( "init", "assets_in_head_init", 20 );

function assets_in_head_init() {
    if( is_admin() ) {
        return;
    }
    
    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-icons' );
    wp_enqueue_style( 'adverts-icons-animate' );

}