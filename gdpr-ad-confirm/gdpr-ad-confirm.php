<?php
/*
 * Plugin Name: GDPR Ad Confirm.
 * Plugin URI: http://wpadverts.com/
 * Description: Compliance for German GDPR rules, the extension requires user to confirm Ad via email.
 * Author: Greg Winiarski
 */

 add_action( "plugins_loaded", function() {

    if( ! defined( "ADVERTS_PATH" ) ) {
        return;
    }
    
    if( function_exists( "wpadverts_snippet_run") ) {
        $basedir = dirname( __FILE__ );
        $baseurl = plugins_url()  .'/wpadverts-snippets/gdpr-ad-confirm/';
    } else {
        $basedir = dirname( __FILE__ );
        $baseurl = plugins_url( __FILE__ ).'/gdpr-ad-confirm/';
    }

    //echo "[$basedir|$baseurl]";

    require_once $basedir . "/class-gdpr-ad-confirm.php";

    $gdpr_ad_confirm = new \WPAdverts\Snippets\Gdpr\AdConfirm;
 });