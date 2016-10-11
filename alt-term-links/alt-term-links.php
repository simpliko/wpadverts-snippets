<?php
/**
Plugin Name: WPAdverts Snippets - Alt Terms Links
Version: 1.0
Author: Greg Winiarski
Description: Display Adverts category (taxonomy) pages on default [adverts_list] page.
*/

###### START COPYING BELOW ######

add_filter( 'term_link', 'alt_term_links_urls', 10, 3 );
add_filter( 'adverts_list_query', 'alt_term_links_search', 1000 );

/**
 * Registry of taxonomies to which custom permalinks can be applied.
 * 
 * By default the list has WPAdverts 'advert_category' and 'advert_location'
 * taxonomies, additional taxonomies can be added using "alt_term_links_config"
 * filter.
 * 
 * @uses alt_term_links_config filter
 * 
 * @since 1.0
 * @access public
 * @return array    List of supported taxonomies
 */
function alt_term_links_config() {
    
    $apply = array(
        "advert_category" => array(
            "param" => "advert-category",
            "taxonomy" => "advert_category"
        ),
        "advert_location" => array(
            "param" => "advert-location",
            "taxonomy" => "advert_location"
        )
    );
    
    return apply_filters( "alt_term_links_config", $apply );
}

/**
 * Replaces taxonomy URL with custom URL.
 * 
 * Replaces URLs for taxonomies registered in alt_term_links_config(), the new
 * URL points to default page with [adverts_list] shortcode
 * 
 * @uses alt_term_links_config()

 * @since 1.0
 * @access public
 * @param string $termlink      Default link to taxonomy page
 * @param WP_Term $term         Term for which the URL is generated
 * @param string $taxonomy      Taxonomy name
 * @return string               Custom link to taxonomy page
 */
function alt_term_links_urls($termlink, $term, $taxonomy = null) {

    $apply = alt_term_links_config();

    if( ! array_key_exists( $taxonomy, $apply ) ) {
        return $termlink;
    }
    
    $tax = $apply[ $taxonomy ];
    
    return add_query_arg( $tax["param"], $term->slug, get_permalink( adverts_config( "ads_list_id" ) ) );
    
}

/**
 * Applies search by taxonomy if taxonomy param is present in $_GET.
 * 
 * This function uses alt_term_links_config() to check if any of the registered
 * taxonomies param is in the current ($_GET] url. If it is then search by
 * this taxonomy is applied to [adverts_list] WP_Query.
 * 
 * @since 1.0
 * @access public
 * @param array $args   [adverts_list] WP_Query arguments
 * @return array        Customized WP_Query args.
 */
function alt_term_links_search( $args ) {
    
    foreach( alt_term_links_config() as $conf ) {
        $var = adverts_request( $conf["param"] );
        if( ! empty( $var ) ) {
            $args["tax_query"] = _alt_term_links_search_apply( $conf, $var,  $args["tax_query"] ); 
        }
    }
    
    return $args;
}

/**
 * Modifies $tax_query variable and returns it.
 * 
 * This functon modifies $tax_query array with additional search params.
 * 
 * @since 1.0
 * @access private
 * @param array $param      Information about taxonomy
 * @param string $var       Value for this taxonomy provided in URL ($_GET)
 * @param array $tax_query  WP_Query::$tax_query data
 * @return array            Customized $tax_query data
 */
function _alt_term_links_search_apply( $param, $var, $tax_query ) {
    
    if( ! is_array( $tax_query ) ) {
        $tax_query = array();
    }
    
    foreach( $tax_query as $tax ) {
        
        // Already doing search by this taxonomy, skip.
        if( $tax["taxonomy"] == $param["taxonomy"] ) {
            return $tax_query;
        }
    }
    
    $tax_query[] = array(
        'taxonomy' => $param["taxonomy"],
        'field'    => 'slug',
        'terms'    => $var,
    );
    
    return $tax_query;
}