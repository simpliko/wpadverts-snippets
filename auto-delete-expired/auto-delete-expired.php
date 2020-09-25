<?php
/**
Plugin Name: WPAdverts Snippets - Auto delete expired
Version: 1.0
Author: Greg Winiarski
Description: Automatically deletes ads which expired over 15 days ago.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( 'wp', 'auto_delete_expired_setup_schedule' );
add_action( 'auto_delete_expired', 'auto_delete_expired' );

/**
 * Schedules delete event
 * 
 * This function schedules (in wp-cron) default events.
 * 
 * @since 1.0
 * @return void
 */
function auto_delete_expired_setup_schedule() {
    
    // Schedule ad deletion, if not already scheduled
    if ( ! wp_next_scheduled( 'auto_delete_expired' ) ) {
        wp_schedule_event( time(), 'daily', 'auto_delete_expired');
    }

}

/**
 * Deletes the Ads which expired over 15 days ago.
 * 
 * If $dry_run is set to true the function will not delete anything, it will
 * only print the IDs and Ad expiration dates of the Ads which should be deleted.
 * 
 * You can customize the deletion proccess using following constants:
 * - AUTO_DELETE_EXPIRED_DELTA          number of days after the expiration when the Ad should be removed (default: 15)
 * - AUTO_DELETE_EXPIRED_FORCE_DELETE   should the Ads be removed permanently or only trashed (default: false)
 * - AUTO_DELETE_EXPIRED_ATTACHMENTS    should the attachments be also deleted
 * 
 * @example define( "AUTO_DELETE_EXPIRED_DELTA", 30 );
 * 
 * The constants you can put in your wp-config.php file.
 * 
 * @param   boolean   $dry_run  
 * @return  void
 */
function auto_delete_expired( $dry_run = false ) {
    
    if( defined( "AUTO_DELETE_EXPIRED_DELTA" ) ) {
        $delta = AUTO_DELETE_EXPIRED_DELTA;
    } else {
        $delta = 15;
    }
    
    if( defined( "AUTO_DELETE_EXPIRED_FORCE_DELETE" ) ) {
        $force_delete = AUTO_DELETE_EXPIRED_FORCE_DELETE;
    } else {
        $force_delete = false;
    }
    
    if( defined( "AUTO_DELETE_EXPIRED_ATTACHMENTS" ) ) {
        $delete_attachments = AUTO_DELETE_EXPIRED_ATTACHMENTS;
    } else {
        $delete_attachments = false;
    }

    if( ! $force_delete ) {
        $delete_attachments = false;
    }
    
    $ads = new WP_Query( array(
        "post_type" => "advert",
        "post_status" => "expired",
        "suppress_filters" => true,
        "posts_per_page" => -1,
        "meta_key" => "_expiration_date",
        "meta_value" => current_time( "timestamp", true ) - $delta * 24 * 3600,
        "meta_compare" => "<="
    ) );
    
    if( $dry_run ) {
        auto_delete_expired_dry_run( $ads );
        return;
    }
    
    foreach( $ads->posts as $ad ) {
        
        if( $delete_attachments ) {
            auto_delete_expired_attachments( $ad->ID, $force_delete );
        }
        
        if( $force_delete ) {
            wp_delete_post( $ad->ID, $force_delete );
        } else {
            wp_trash_post( $ad->ID );
        }
    }
    
}

/**
 * Deletes attachment assigned to post identified with $parent_id
 * 
 * The attachments will be deleted permanently if $force_delete is set to true, 
 * othherwise the attachments will be only trashed.
 * 
 * @since   1.0
 * @param   int       $parent_id
 * @param   boolean   $force_delete
 * @return  null
 */
function auto_delete_expired_attachments( $parent_id, $force_delete ) {

    $param = array( 
        'post_parent' => $parent_id, 
        "suppress_filters" => true,
        'post_type' => 'attachment',
        'posts_per_page' => -1
    );
    
    $children = get_posts( $param );
    
    // also delete all uploaded files
    if( is_array( $children ) ) {
        foreach( $children as $attch) {
            if( $force_delete ) {
                wp_delete_attachment( $attch->ID, $force_delete );
            } else {
                wp_trash_post( $attch->ID );
            }
        }
    } 
}

/**
 * Prints IDs and Ad expiration date
 * 
 * @since   1.0
 * @param   WP_Query $ads     Ads found when searching for list of expired Ads
 * @return  void
 */
function auto_delete_expired_dry_run( $ads ) {
    echo "<pre>";
    foreach( $ads->posts as $post ) {
        $expired = date_i18n( "Y-m-d H:i:s", get_post_meta( $post->ID, "_expiration_date", true ) );
        echo sprintf( "%d / %s\r\n", $post->ID, $expired );
    }
    echo "</pre>";
}