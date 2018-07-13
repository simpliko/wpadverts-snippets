<?php
/*
 * Plugin Name: Customize Adverts slugs.
 * Plugin URI: http://wpadverts.com/
 * Description: This code snippet/plugin explains how to customize Adverts slugs.
 * Author: Greg Winiarski
 */

add_action("adverts_post_type", "customize_adverts_post_type", 10, 2);
add_action("adverts_register_taxonomy", "customize_adverts_taxonomy", 10, 2 );

/**
 * Changes ads url from "advert" to "classified"
 * 
 * This will basically change URL from for example 
 * http://example.com/advert/hello-world/ to http://example.com/classified/hello-world/
 * 
 * Note you can use this function to further customize Adverts Custom Post Type the
 * $args argument is "$args" used in register_post_type function.  In other words
 * this function (or rather "customize_post_type" filter) gives you an opportunity
 * to customize adverts Custom Post Type parameters before 
 * register_post_type function will be called.
 * 
 * @see register_post_type()
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 * 
 * @param array     $args   Custom Post Type init params
 * @param string    $type   Custom Post Type name
 * @return array
 */
function customize_adverts_post_type( $args, $type = null ) {
    if( $type != "advert" ) {
        return $args;
    }
    
    if(!isset($args["rewrite"])) {
        $args["rewrite"] = array();
    }
   
    $args["rewrite"]["slug"] = "classified";
    return $args;
}

/**
 * Customizes Adverts Categories slug
 * 
 * This functions changes adverts categories slug from for example
 * http://example.com/advert-category/toys/ to http://example.com/ad-category/toys/
 * 
 * Note you can use this function to further customize Adverts Taxonomy
 * $args argument is "$args" used in register_taxonomy function. In other words
 * this function (or rather "customize_adverts_taxonomy" filter) gives you an opportunity
 * to customize adverts_category taxonomy parameters before register_taxonomy function will be called.
 * 
 * @see register_taxonomy()
 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
 * 
 * @param array     $args   Parameters that will passed to register_taxonomy function
 * @param string    $type   Custom taxonomy name
 * @return array
 */
function customize_adverts_taxonomy( $args, $type = null ) {
    if( $type != "advert_category" ) {
        return $args;
    }
    
    if(!isset($args["rewrite"])) {
        $args["rewrite"] = array();
    }
   
    $args["rewrite"]["slug"] = "ad-category";
    return $args;
}
