<?php
/*
 * Plugin Name: Cleanup Args
 * Plugin URI: http://wpadverts.com/
 * Description: Removes empty $_GET params from the search, so for example URL <code>/adverts/?query=&location=London</code> will become <code>/adverts/?location=London</code>.
 * Author: Greg Winiarski
 */

add_filter( "template_redirect", "cleanup_args" );

/**
 * Removes empty $_GET params from current URL.
 * 
 * The function checks if current URL has empty ('query or 'location') params,
 * if it has then empty params are removed from URL and user is redirected to 
 * page with this params removed.
 * 
 * @uses is_page() Checks if user is on main Ads list.
 * @uses remove_query_arg() Modifies current URL.
 * @uses wp_redirect() Redirects user to page with empty params removed.
 * @return void
 */
function cleanup_args() {
    if( ! is_page( adverts_config( 'config.ads_list_id' ) ) ) {
        return;
    }
    
    $args = array( 'query', 'location' );
    $redirect = false;
    
    foreach( $args as $arg ) {
        if( isset( $_GET[$arg] ) && empty( $_GET[$arg] ) ) {
            $redirect = remove_query_arg( $arg, $redirect );
        }
    }
    
    if( $redirect ) {
        wp_redirect( $redirect );
        exit;
    }
}