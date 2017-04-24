<?php
/*
 * Plugin Name: Moderate Once
 * Plugin URI: https://wpadverts.com/
 * Description: Disable moderation for users who already have at least one ad with status 'publish' or 'expired'
 * Author: Greg Winiarski
 */

add_filter( "shortcode_atts_adverts_add", "moderate_once_loggedin", 10, 3 );
add_filter( "shortcode_atts_adverts_add", "moderate_once_loggedout", 10, 3 );

/** 
 * Extends allowed [adverts_add] attributes.
 *
 * Uses 'shortcode_atts_adverts_add' filter to inject new parameters.
 * 
 * @since 1.0.0
 *
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 * @return array            The output array of shortcode attributes.
 */
function moderate_once_loggedin( $out, $pairs, $atts ) {

    // 1. check if user is logged in, if not return $out;
    if( get_current_user_id() < 1 ) {
        return $out;
    }
    
    $out["moderate"] = 1;

    // 2. check if user has at least one Advert with status publish or expired, if not return $out;
    $posts = get_posts( array(
        'author' => get_current_user_id(),
        'post_type' => 'advert',
        'post_status' => array("publish", "expired"),
        'posts_per_page' => 1
    ) );
    
    if( empty( $posts ) ) {
        return $out;
    }


    $out["moderate"] = 0;
    return $out;
}

/**
 * Extends allowed [adverts_add] attributes.
 *
 * Uses 'shortcode_atts_adverts_add' filter to inject new parameters.
 * 
 * @since 1.0.0
 *
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 * @return array            The output array of shortcode attributes.
 */
function moderate_once_loggedout( $out, $pairs, $atts ) {
    
    // 1. check if user is logged out, if not return $out;
    if( get_current_user_id() > 0 ) {
        return $out;
    }
    
    // 2. check if action is equal to "save"
    $action = apply_filters( 'adverts_action', adverts_request("_adverts_action", ""), "shortcode_adverts_add" );
    if( $action !== "save" ) {
        return $out;
    }
    
    $out["moderate"] = 1;
    
    $post_id = absint( adverts_request( "_post_id", null ) );
    $adverts_email = get_post_meta( $post_id, "adverts_email", true );
    
    
    // 3. sprawdź czy istnieje ogłoszenie:
    // - typu (custom post type) advert
    // - ze statusem publish lub expired
    // - które ma pole meta o nazwie adverts_email == $adverts_email    https://codex.wordpress.org/Template_Tags/get_posts#Custom_Field_Parameters
    // - to można zrobić używając funkcji get_posts()
    // https://codex.wordpress.org/Template_Tags/get_posts
    if( false ) {
        return $out;
    }
    
    $out["moderate"] = 0;
    return $out;
}