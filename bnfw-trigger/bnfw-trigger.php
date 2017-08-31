<?php
/**
Plugin Name: WPAdverts Snippets - BNFW Trigger
Version: 1.0
Author: Greg Winiarski
Description: Triggers "draft_to_pending" and "draft_to_publish" actions for BNFW.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( "advert_tmp_to_publish", "bnfw_trigger_draft_to_publish" );
add_action( "advert_tmp_to_pending", "bnfw_trigger_draft_to_publish" );

/**
 * Triggers draft_to_publish action
 * 
 * @param WP_Post $post
 */
function bnfw_trigger_draft_to_publish( $post ) {
    if( $post->post_type === "advert" ) {
        do_action( "draft_to_publish", $post );
    }
}

/**
 * Triggers draft_to_pending action
 * 
 * @param WP_Post $post
 */
function bnfw_trigger_draft_to_pending( $post ) {
    if( $post->post_type === "advert" ) {
        do_action( "draft_to_pending", $post );
    }
}