<?php
/*
 * Plugin Name: Limit Active Listings.
 * Plugin URI: http://wpadverts.com/
 * Description: Limit the number of concurrent active listings user can have on site.
 * Author: Greg Winiarski
 */

add_action( "init", "limit_user_active_listings_init", 20 );

/**
 * Replace the [adverts_add] shortcode with a custom shortcode
 * 
 * The custom shortcode will not render the [adverts_add] if the user reached
 * maximum number of concurrent active listings
 * 
 * @since 1.0
 * @return void
 */
function limit_user_active_listings_init() {
    
    remove_shortcode( "adverts_add" );
    add_shortcode( "adverts_add", "limit_user_active_listings_shortcode" );
}

/**
 * New [adverts_add] shortcode
 * 
 * Shows the error if user reached max number of active listings otherwise shows 
 * [adverts_add] shortcode.
 * 
 * @see shortcode_adverts_add()
 * 
 * @param array $atts   Shortcode attributes
 * @return string       HTML for the shortcode
 */
function limit_user_active_listings_shortcode( $atts ) {

    // Change Maximum number of allowed active ads below
    $max = 5;
    
    $flash = array(
        "error" => array( ),
        "info" => array( )
    );

    $args = array(
        'post_type' => 'advert',
        'post_status' => 'publish',
        'author' => get_current_user_id(),
    ); 
    
    $query = new WP_Query( $args );

    if( $query->found_posts >= $max ) {
        $message = __( 'You reached maximum active ads limit. You cannot have more than %d active Ads at once.', "limit-user-active-listings" );
        
        $flash["error"][] = array(
            "message" => sprintf( $message, $max ),
            "icon" => "adverts-icon-attention-alt"
        );
        
        ob_start();
        adverts_flash( $flash );
        return ob_get_clean();
    }

    return shortcode_adverts_add( $atts );
}
