<?php
/**
Plugin Name: WPAdverts Snippets - Search by category
Version: 1.0
Author: Greg Winiarski
Description: Adds categories dropdown input to [adverts_list] search bar.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter( 'adverts_form_load', 'search_by_category_form_load' );
add_filter( 'adverts_list_query', 'search_by_category_query' );

/**
 * Adds category dropdown into search form in [adverts_list].
 * 
 * @param array $form Search form scheme
 * @return array Customized search form scheme 
 */
function search_by_category_form_load( $form ) {
    
    if( $form['name'] != 'search' ) {
        return $form;
    }

    $form['field'][] = array(
        "name" => "advert_category",
        "type" => "adverts_field_select",
        "order" => 20,
        "label" => __("Category", "adverts"),
        "max_choices" => 10,
        "options" => array(),
        "options_callback" => "adverts_taxonomies",
        "meta" => array(
            "search_group" => "visible",
            "search_type" => "full" 
        )
    );

    return $form;
}

/**
 * Adds tax_query param to WP_Query
 * 
 * The tax_query is added only if it is in $_GET['advert_category']
 * 
 * @param array $args WP_Query args
 * @return array Modified WP_Query args
 */
function search_by_category_query( $args ) {
    
    if( ! adverts_request( "advert_category" ) ) {
        return $args;
    }
    
    $args["tax_query"] = array(
        array(
            'taxonomy' => 'advert_category',
            'field'    => 'term_id',
            'terms'    => adverts_request( "advert_category" ),
        ),
    );
    
    return $args;
}

