<?php
/*
 * Plugin Name: Override Templates.
 * Plugin URI: http://wpadverts.com/
 * Description: This code snippet/plugin explains how to use different frontend templates instead of default ones.
 * Author: Greg Winiarski
 */

add_action("adverts_template_load", "override_templates");

/**
 * Loads WPAdverts templates from current theme or child-theme directory.
 *
 * By default WPAdverts loads templates from wpadverts/templates directory,
 * this function tries to load files from your current theme 'wpadverts'
 * directory for example wp-content/themes/twentytwelve/wpadverts.
 *
 * The function will look for templates in three places, if the template will
 * be found in fist one the other places are not being checked.
 
 * @param string $tpl Absolute path to template file
 * @return string
 */
function override_templates( $tpl ) {
     
    $dirs = array();
    // first check in child-theme directory
    $dirs[] = get_stylesheet_directory() . "/wpadverts/";
    // next check in parent theme directory
    $dirs[] = get_template_directory() . "/wpadverts/";
    // if nothing else use default template
    $dirs[] = ADVERTS_PATH . "/templates/";
    // use absolute path in case the full path to the file was passed
    $dirs[] = dirname( $tpl ) . '/';
    
    $basename = basename( $tpl );
     
    foreach($dirs as $dir) {
        if( file_exists( $dir . $basename ) ) {
            return $dir . $basename;
        }
    }
}