<?php
/**
Plugin Name: WPAdverts Snippets - Capabilities
Version: 1.0
Author: Greg Winiarski
Description: Allows managing capabilities in for wp-admin / Classifieds panel.
*/

/*
 * <strong>How to use this snippet?</strong>
 * 
 * After installation and activation there will be the following changes in wp-admin panel:
 * 
 * - Users with Subscriber role will see wp-admin / Classifieds section and will be able to create a new Ad (submit it for review)
 *   and edit the Ads they own.
 *   
 *   @TIP: If you do not want to enable this functionality comment out or remove line: add_action( "admin_init", "cpt_caps_init_subscriber" );
 * 
 * - A new role Moderator will be created, the Moderators are users like Subscribers but can manage ALL the ads in wp-admin / Classifieds 
 *   section.
 *   
 *   @TIP: If you want to have a moderator, you can create a user with this role from wp-admin / Users / Add New panel.
 * 
 */

// Initialization action and filters
add_action( "admin_init", "cpt_caps_init" );
add_filter( "adverts_post_type", "cpt_caps_register", 10, 2 );
add_filter( "adverts_register_taxonomy", "cpt_caps_register_taxonomy" );

// Comment out the filter below if you do not want Subscribers
// to be able to edit their own Ads from wp-admin / Classifieds panel
add_action( "wp_loaded", "cpt_caps_loaded" );

/**
 * Allows subscribers to edit their own Ads
 * 
 * The function will:
 * - allow subscriber to use only title and editor when editing classified
 * - allow subscriber to see and edit his own ads in wp-admin / Classifieds
 * - hide in wp-admin / Classifieds all ads not owned by currently logged in user
 * - remove meta boes the user should not see (by default it hides wpseo-metabox)
 * 
 * @since 1.0
 * @return void
 */
function cpt_caps_loaded() {
    $user = wp_get_current_user();
    if ( in_array( 'subscriber', (array) $user->roles ) ) {

        add_filter( "adverts_post_type", "cpt_caps_register_subscriber", 20, 2 );
        add_action( "admin_init", "cpt_caps_init_subscriber" );
        add_filter( 'pre_get_posts', 'cpt_caps_pre_get_posts' );
        add_action( 'add_meta_boxes', 'cpt_caps_remove_meta_boxes', 1000 );
    }
}

/**
 * Registers custom capabilities for editing Adverts
 * 
 * @since   1.0
 * 
 * @param   array   $args           register_post_type() arguments
 * @param   string  $post_type      post type
 * @return  array
 */
function cpt_caps_register( $args, $post_type = null ) {
    if( $post_type != "advert" ) {
        return $args;
    }
    
    $args["capabilities"] = array(
        'edit_post' => 'edit_advert',
        'edit_posts' => 'edit_adverts',
        'edit_others_posts' => 'edit_other_adverts',
        'publish_posts' => 'publish_adverts',
        'read_post' => 'read_advert',
        'read_private_posts' => 'read_private_adverts',
        'delete_post' => 'delete_advert',
        'delete_posts' => 'delete_adverts',
        
        'delete_private_posts' => 'delete_private_adverts',
        'delete_published_posts' => 'delete_published_adverts',
        'delete_others_posts' => 'delete_others_adverts',
        'edit_private_posts' => 'edit_private_adverts',
        'edit_published_posts' => 'edit_published_adverts',
        
    );
    $args['map_meta_cap'] = true;
    
    return $args;
}

/**
 * Change capability required to assign terms.
 * 
 * Changes capability required to modify selected ters when editing an advert
 * from wp-admin / Classifieds to "edit_adverts" (originally "edit_posts")
 * 
 * @since   1.0
 * 
 * @param   array     $args   register_taxonomy arguments
 * @return  array
 */
function cpt_caps_register_taxonomy( $args ) {
    $args["capabilities"] = array(
        'assign_terms' => 'edit_adverts'
    );
    
    return $args;
}

/**
 * Allow administrators and editors to manage posts.
 * 
 * This function allows Administrators and Editors to manage Ads in 
 * wp-admin / Classifieds panel.
 * 
 * The function adds new capabilities to these two roles.
 * 
 * @since 1.0
 * @return void;
 */
function cpt_caps_init() {
    
    // allow administrators and editors to edit all Ads
    $allowed = array( "administrator", "editor" );
    foreach( $allowed as $r ) {
        $role = get_role( $r );
        $role->add_cap( "edit_advert", true );
        $role->add_cap( "edit_adverts", true );
        $role->add_cap( "edit_other_adverts", true );
        $role->add_cap( "publish_adverts", true );
        $role->add_cap( "read_advert", true );
        $role->add_cap( "read_private_adverts", true );
        $role->add_cap( "delete_advert", true );
        $role->add_cap( "delete_adverts", true );
        
        $role->add_cap( "delete_private_adverts", true );
        $role->add_cap( "delete_published_adverts", true );
        $role->add_cap( "delete_others_adverts", true );
        $role->add_cap( "edit_private_adverts", true );
        $role->add_cap( "edit_published_adverts", true );
        
    } 
    
    // Create role "moderator" - moderators will be able to manage their own profiles
    // in wp-admin panel and moderate Ads.
    add_role( "moderator", "Moderator", array(
        'read',
        'edit_advert',
        'edit_adverts',
        'edit_other_adverts',
        'publish_adverts',
        'read_advert',
        'read_private_adverts',
        'delete_advert',
        'delete_adverts'
    ) );
}

// Subscriber related functions
// The functions below allow the user with role Subscriber to edit his own
// ads from wp-admin / Classifieds section.

/**
 * Customize the register_post_type() args for subscribers.
 * 
 * The function allows customizing the register_post_type() args for subscribers.
 * 
 * You can use it to remove some features from the wp-admin / Classifieds / Edit 
 * page. By default it remove the user ability to change the author.
 * 
 * @param   array   $args
 * @param   type    $post_type
 * @return  array
 */
function cpt_caps_register_subscriber( $args, $post_type = null ) {
    if( $post_type != "advert" ) {
        return $args;
    }
    
    $args["support"] = array( 'title', 'editor' );

    return $args;
}

/**
 * Remove meta boxes the Subscriber should not see
 * 
 * In this function body you can add multiple remove_meta_box() calls
 * to hide the meta boxes the Subscriber should not see.
 * 
 * The function is executed via add_meta_boxes action
 * 
 * @see add_meta_boxes
 * 
 * @since 1.0
 * @return void;
 */
function cpt_caps_remove_meta_boxes() {
    remove_meta_box( 'wpseo_meta', 'advert', 'normal' );
}

/**
 * Add advert edit capabilities to subscriber
 * 
 * This function is executed via admin_init action.
 * 
 * @see admin_init action
 * 
 * @since 1.0
 * @return void;
 */
function cpt_caps_init_subscriber() {
    
    // Allow subscribers to edit their own Ads from wp-admin panel
    $role = get_role( 'subscriber' );
    $role->add_cap( "edit_advert", true );
    $role->add_cap( "edit_adverts", true );
    $role->add_cap( "edit_other_adverts", false );
    $role->add_cap( "publish_adverts", false );
    $role->add_cap( "read_advert", true );
    //$role->add_cap( "read_private_adverts", true );
    //$role->add_cap( "delete_advert", true );
    //$role->add_cap( "delete_adverts", true );
    
    //$role->add_cap( "delete_private_posts", true );
    //$role->add_cap( "delete_published_posts", true );
    //$role->add_cap( "delete_others_posts", true );
    $role->add_cap( "edit_private_adverts", true );
    $role->add_cap( "edit_published_adverts", true );
    $role->add_cap( "assign_terms", true );
}

/**
 * Hide posts not owned by current user
 * 
 * This function is executed via pre_get_posts filter
 * 
 * @see pre_get_posts filter
 * 
 * @global      string      $pagenow    Current page
 * @global      int         $user_ID    Currently logged in user ID
 * @param       WP_Query    $query      Main WP Query
 * @return      WP_Query
 */
function cpt_caps_pre_get_posts($query) {
    global $pagenow;
 
    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;
 
    if( ! current_user_can( 'edit_others_adverts' ) ) {
        global $user_ID;
        $query->set('author', $user_ID );
    }
    return $query;
}
