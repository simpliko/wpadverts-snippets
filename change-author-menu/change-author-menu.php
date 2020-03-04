<?php
/**
Plugin Name: WPAdverts Snippets - Change Author Menu
Version: 1.0
Author: Greg Winiarski
Description: This plugin allows modifying links and content in the [adverts_authors_manage] shortcode.
*/

// Change menu in Author dashboard
add_filter( "adverts_authors_dashboard_menu", "change_author_menu_init", 10, 2 );

/**
 * Customizes menu in the [adverts_authors_manage] shortcode
 * 
 * This function is being called by the adverts_authors_dashboard_menu filter
 * 
 * Menu array keys available by default:
 * 
 * - home               Dashboard
 * - my-ads             My Ads
 * - edit-account       Edit Profile
 * - change-password    Change Password
 * - delete-account     Delete Account
 * - logout             Logout
 * 
 * @see adverts_authors_dashboard_menu filter
 * 
 * @param array     $menu               [adverts_authors_manage] menu items
 * @param array     $shortcode_atts     [adverts_authors_manage] shortcode parameters
 * @return array                        Updated $menu
 */
function change_author_menu_init( $menu, $shortcode_atts ) {

    // Remove delete account link from menu
    unset( $menu['delete-account'] );
    
    // Change icon for My Ads option to bell
    $menu['my-ads']['icon'] = 'adverts-icon-bell';

    // Change function called to render Dashboard menu
    $menu['home']['callback'] = 'change_author_menu_redefine_default';
    
    // Add new custom menu option
    $url = get_the_permalink( adverts_config( "authors.author_dashboard_page_id" ) );
    
    // Set custom page slug
    // Slug may contain lowercase a-z, 0-9 and "-" characters only
    // The slug needs to be unique
    $page_slug = "custom-page"; 
    
    $page_arr = array(
        "label" => __("Custom Menu", "wpadverts-authors"),
        "href"  =>  add_query_arg( "author-panel", $page_slug, $url ),
        "icon"  => "adverts-icon-box",
        "callback" => "change_author_menu_custom_content",
        "order" => 90
    );
    
    // no longer needed since Authors 1.1.2
    // the $menu is sorted using "order" param (default value 100)
    // $menu = array_slice($menu, 0, 4, true) + array( $page_slug => $page_arr ) + array_slice( $menu, 3, count($menu) - 1, true) ;
    
    $menu[$page_slug] = $page_arr;
    
    // Always return $menu!
    return $menu; 
}

/**
 * Define content for custom panel
 * 
 * This function will be called when [adverts_authors_manage] will want to render
 * content for your "custom-page"
 * 
 * @param   string $panel   Name of the panel that will be rendered
 * @return  string          HTML for the panel
 */
function change_author_menu_custom_content( $panel ) {
    
    // if $panel is different from our $page_slug we return content
    // user is accessing one of the default pages and we do not want to change this
    if($panel != 'custom-page') {
        return "";
    }
    
    // Prepare variables for content - do the PHP logic here
    $msg = __( "Click me!" );
    $url = "http://wpadverts.com";
    
    // Prepare new content - you can use HTML or include existing PHP file using include_once function
    ob_start();
    ?>
        <a href="<?php echo $url; ?>"> <?php echo esc_html( $msg ); ?> </a>
    <?php 
    $content = ob_get_clean();
    
    // Always return $content!
    return $content;
}

/**
 * Custom content for [adverts_authors_manage] dashboard
 * 
 * This function is being assigned in change_author_menu_init() function 
 * 
 * @see change_author_menu_init()
 * 
 * @param   string $panel   Name of the panel that will be rendered
 * @return  string          HTML for the panel
 */
function change_author_menu_redefine_default( $panel ) {
    // we want to change only default home page
    if($panel != 'home') {
        return "";
    }
    
    return "Custom Dashboard!";
}