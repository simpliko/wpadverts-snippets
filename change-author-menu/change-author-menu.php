<?php
/**
Plugin Name: WPAdverts Snippets - Change Author Menu
Version: 1.0
Author: Greg Winiarski
Description: This plugin allows to manage Author dashboard. 
*/

// Change menu in Author dashboard
add_filter( "adverts_authors_dashboard_menu", "change_author_dashboard_menu" );
function change_author_dashboard_menu( $menu ) {
    
    // Remove delete account link from menu
    unset( $menu['delete-account'] );
    
    // Change icon for My Ads option to bell
    $menu['my-ads']['icon'] = 'adverts-icon-bell';

    // Add new custom menu option
    $page_slug = "custom-page"; // use small letters and - symbol only! There can be two option with same slug!
    $menu[$page_slug] = array(
        "label" => __("Custom Menu", "wpadverts-authors"),
        "href"  => get_the_permalink( adverts_config( "authors.author_dashboard_page_id" ) ) . "?author-panel=" . $page_slug,
        "icon"  => "adverts-icon-box",
    );
    
    // Always return $menu!
    return $menu; 
}

// Define content for custom panel
add_filter( "adverts_authors_dashboard_content", "define_custom_panel_content", 10, 2 );
function define_custom_panel_content( $content, $panel ) {
    
    // if $panel is different from our $page_slug we return content
    // user is accessing one of the default pages and we do not want to change this
    if($panel != 'custom-page') {
        return $content;
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

// Change existing panel
add_filter( "adverts_authors_dashboard_content", "redefine_default_panel_content", 10, 2 );
function redefine_default_panel_content( $content, $panel ) {
    
    // we want to change only default home page
    if($panel != 'home') {
        return $content;
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