<?php
/**
Plugin Name: WPAdverts Snippets - Ads By Author
Version: 1.0
Author: Greg Winiarski
Description: Links "Posted By" on Ad details page, to page which displays all active ads posted by this author. Note this plugin requires WPAdverts 1.0.5 in order to work.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( "wp", "ads_by_author_init", 50 );

/**
 * Registers "Ads By Author" filter and actions.
 *
 * It does two things:
 * - replaces "by John Doe" on Ad details with link to all user Ads
 * - if current page is default Ads list and GET param "posted_by" is provided
 *   then this function will modify [adverts_list] shortcode to include user
 *   info above Ads list.
 * 
 * @since 1.0
 */ 
function ads_by_author_init() {

    add_filter( "adverts_tpl_single_posted_by", "ads_by_author_tpl_single_posted_by", 10, 2 );

    if( is_page( adverts_config( 'ads_list_id' ) ) && is_numeric( adverts_request( "posted_by" ) ) ) {
    
        remove_shortcode( "adverts_list" );
        add_shortcode( "adverts_list", "ads_by_author_list" );
        
        add_filter( "adverts_list_pagination_base", "ads_by_author_pagination_base" );
        add_filter( "adverts_list_query", "ads_by_author_query" );
    }
}

/**
 * Generates HTML for [adverts_list] shortcode
 * 
 * This function replaces default [adverts_list] callback and
 * adds author header before the listings.
 *
 * @param array $atts Shorcode attributes
 * @since 1.0
 * @return string Fully formatted HTML for adverts list
 */
function ads_by_author_list( $params ) {
    $html = "";
    $params["search_bar"] = "disabled";
    $author = get_user_by( "id", adverts_request( "posted_by" ) );
    ob_start();
    ?>
    <style type="text/css">
        .ads-by-author.adverts-options {
            padding: 0.5em 1em;
        }
        .ads-by-author.adverts-options .author-logo {
            float: left; 
            height: 48px;
            width: 64px;
        }
        .ads-by-author.adverts-options .author-logo > img {
            border-radius: 50%;
        }
        .ads-by-author.adverts-options .author-data {
            line-height: 24px;
        }
    </style>
    <div class="ads-by-author adverts-options">
        <div class="author-logo">
            <?php echo get_avatar( $author->user_email, 48 ) ?>
        </div>
        <div class="author-data">
            <strong><?php esc_html_e( $author->display_name ) ?></strong><br/>
            <?php esc_html_e( date_i18n( get_option( "date_format" ), strtotime( $author->user_registered ) ) ) ?>
        </div>
    </div>        
    <?php
    $html.= ob_get_clean();
    $html.= shortcode_adverts_list( $params );
    
    return $html;
}

/**
 * Changes pagination base.
 * 
 * This function adds "posted_by" param to the default
 * pagination URL so it is possible to paginate over
 * user submitted ads.
 *
 * @see adverts_list_pagination_base filter in wpadverts/includes/shortcodes.php
 *
 * @param string $pbase Default pagination base
 * @since 1.0
 * @return string Updated pagination base
 */
function ads_by_author_pagination_base( $pbase ) {
    $pbase = get_the_permalink();
    $glue = stripos( $pbase, '?' ) ? '&' : '?';
    
    return $pbase . $glue . "posted_by=" . intval( adverts_request( "posted_by" ) ) . '%_%';
}

/**
 * Adds "author" param to [adverts_list] WP_Query.
 * 
 * This function filters Ads by "author" param (provided in $_GET['posted_by']),
 * it is applied using "adverts_list_query" filter
 *
 * @see adverts_list_query filter in wpadverts/includes/shortcodes.php
 *
 * @param string $pbase Default pagination base
 * @since 1.0
 * @return string Updated pagination base
 */
function ads_by_author_query( $args ) {
    $args["author"] = intval( adverts_request( "posted_by" ) );
    return $args;
}

/**
 * Replaces user name with link to all user ads.
 * 
 * If user who posted an Ad is registered WP user, then the "By John Doe" text
 * on Ad details page is replaced with link to all user Ads.
 *
 * This change is applied using adverts_tpl_single_posted_by filter.
 *
 * @see adverts_tpl_single_posted_by filter in wpadverts/templates/single.php
 *
 * @param string $pbase Default pagination base
 * @since 1.0
 * @return string Updated pagination base
 */
function ads_by_author_tpl_single_posted_by( $name, $post_id ) {
    
    $post = get_post( $post_id );
    $person = get_post_meta($post_id, 'adverts_person', true);
    
    if( $post->post_author ) {
        include_once ADVERTS_PATH . "/includes/class-html.php";
        
        $link = get_permalink( adverts_config( 'ads_list_id' ) );
        $glue = stripos( $link, '?' ) ? '&' : '?';
    
        $person = new Adverts_Html( "a", array(
            "href" => $link . $glue . "posted_by=" . $post->post_author
        ), $person);
    }
    
    return sprintf( __("by <strong>%s</strong>", "adverts"), $person );
}