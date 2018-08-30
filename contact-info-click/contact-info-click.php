<?php
/*
 * Plugin Name: Contact Info Click
 * Plugin URI: https://wpadverts.com/
 * Description: This plugin will automatically click on the "Show contact information" button on Ad details pages.
 * Author: Greg Winiarski
 */

add_action( "init", "contact_info_click_init", 20 );

function contact_info_click_init() {
    if( ! defined( "ADVERTS_PATH" ) ) {
        return;
    }
    
    if( function_exists( "wpadverts_snippet_run") ) {
        $url = plugins_url()  .'/wpadverts-snippets';
    } else {
        $url = plugins_url();
    }
    
    wp_register_script( 
        'contact-info-click', 
        $url  .'/contact-info-click/contact-info-click.js', 
        array( 'jquery', 'adverts-frontend' ), 
        "1", 
        true
    );
}

add_action( "wp", "contact_info_click_wp", 20 );

function contact_info_click_wp() {
    if( is_singular( 'advert' ) ) {
        wp_enqueue_script( 'contact-info-click' );
    }
}