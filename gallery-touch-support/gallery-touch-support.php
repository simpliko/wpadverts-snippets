<?php
/*
 * Plugin Name: Gallery Touch Support
 * Plugin URI: https://wpadverts.com/
 * Description: This code snippet enables drag and drop support for gallery image sorting on the mobile devices
 * Author: Greg Winiarski
 * Version: 1.0.0
 */


add_action( "wp_footer", function() {

    if( function_exists( "wpadverts_snippet_run") ) {
        $url = plugins_url()  .'/wpadverts-snippets';
    } else {
        $url = plugins_url();
    }

    echo sprintf( '<script src="%s/gallery-touch-support/jquery.ui.touch-punch.min.js"></script>', $url );
},90000);