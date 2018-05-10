<?php
/**
Plugin Name: WPAdverts Snippets - Attachment Uploaded
Version: 1.0
Author: Greg Winiarski
Description: This plugin executes some action after a file is uploaded using WPAdverts upload interface.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( "adverts_attachment_uploaded", "attachment_uploaded_action" );

/**
 * Customizes attachment after upload.
 * 
 * This function is executed when WPAdverts file upload finishes, you can use 
 * it to customize the uploaded file or add some term or meta value to it.
 * 
 * In the example below will add 'wpadverts' term to 'my-taxonomy', if
 * the my-taxonomy exists (that is if it was registered).
 * 
 * We will also add is_wpadverts_attachment meta value to the attachment.
 * 
 * @param int $attach_id Attachment ID
 * @return void
 */
function attachment_uploaded_action( $attach_id ) {
    
    // Set Attachment Terms
    // 
    // This is useful if you are using Enhanced Media Library plugin
    // and want to mark the WPAdverts attachments with some taxonomy term.
    
    if( taxonomy_exists( 'my-taxonomy' ) ) {
        wp_set_post_terms( $attach_id, 'wpadverts', 'my-taxonomy' );
    }
    
    // Set Attachment Meta
    //
    // This is useful if you would like to store some additional data about the
    // attachement.
    //
    // For example you can add boolean meta value which will allow to tell if the 
    // attachment was uploaded using WPAdverts uploader. Using this information
    // you will be able to filter the attachments in Media Library.
    //
    // Also see the attachment_uploaded_args() function which actually hides the
    // WPAdverts items in wp-admin / Media panel.
    
    add_post_meta( $attach_id, 'is_wpadverts_attachment', '1' );
}

#add_filter( "ajax_query_attachments_args", "attachment_uploaded_args" );

/**
 * Hides the WPAdverts attachments in media library
 * 
 * This function is executed by ajax_query_attachments_args filter, it allows
 * customizing the query args and hide the WPAdverts attachments in the media
 * library.
 * 
 * @see ajax_query_attachments_args filter
 * 
 * @param   array $query Media Library attachments query args.
 * @return  array
 */
function attachment_uploaded_args( $query ) {
    if( ! isset( $query['meta_query'] ) ) {
        $query['meta_query'] = array();
    }
    
    $query['meta_query'][] = array(
        'key' => 'is_wpadverts_attachment',
        'compare' => 'NOT EXISTS'
    );
    
    return $query;
}

add_action( "before_delete_post", "attachment_uploaded_cleanup" );

/**
 * Deletes Advert attachments
 * 
 * This function is executed using delete_post filter, it deletes all 
 * of the attachments assigned to the Advert.
 * 
 * @param   int     $post_id    ID of an Advert being deleted
 * @return  void
 */
function attachment_uploaded_cleanup( $post_id ) {
    $advert = get_post( $post_id );

    if( $advert->post_type != 'advert' ) {
        return;
    }

    $param = array( 
        'post_parent' => $post_id, 
        'post_type' => 'attachment' 
    );
    
    $children = get_posts( $param );

    // Delete all uploaded files
    if( is_array( $children ) ) {
        foreach( $children as $attch) {
            wp_delete_attachment( $attch->ID, true );
        }
    } 
    
}