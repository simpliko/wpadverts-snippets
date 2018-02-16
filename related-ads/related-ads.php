<?php
/**
Plugin Name: WPAdverts Snippets - Related Ads
Version: 1.0
Author: Greg Winiarski
Description: Shows a list of related Ads on the Ad details page.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( "adverts_tpl_single_bottom", "related_ads_tpl_single_bottom", 1000 );

/**
 * Displays "Related Ads" on the Ad details page.
 * 
 * This function is executed by adverts_tpl_single_bottom filter.
 * 
 * @param int $post_id  Post ID
 * @return void
 */
function related_ads_tpl_single_bottom( $post_id ) {
    
    add_filter( "adverts_list_query", "related_ads_list_query" );
    
    echo '<h3>' . __( "Related Ads") . '</h3>';
    echo shortcode_adverts_list( array(
        "redirect_to" => "",
        "search_bar" => "disabled",
        "show_pagination" => 0,
        "posts_per_page" => 5,
        "display" => "list"
    ) );
}

/**
 * Does a related posts search
 * 
 * This function is executed on Ad details pages (/advert/*) by the adverts_list_query
 * filter.
 * 
 * @param array $args   WP_Query arguments
 * @return array        Modified list of arguments
 */
function related_ads_list_query( $args ) {
    $post = get_post( get_the_ID() );
    
    $args["post__not_in"] = array($post->ID);
    
    // search by keyword
    $args["s"] = $post->post_title;
    
    remove_filter( "adverts_list_query", "related_ads_list_query" );
    return $args;
}