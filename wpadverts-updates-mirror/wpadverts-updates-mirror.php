<?php
/*
 * Plugin Name: WP Adverts - Updates Mirror
 * Plugin URI: http://wpadverts.com/
 * Description: This plugin changes the default updates URL to a mirror site. Use this plugin only if you are having a problem with activating licenses for extensions.
 * Author: Greg Winiarski
 */

add_action( "plugins_loaded", function() {
    if( defined( "ADVERTS_PATH" ) ) {
        include_once ADVERTS_PATH . "/includes/class-updates-manager.php";
        Adverts_Updates_Manager::$url = "https://mirror.wpadverts.com/";
    }
} );