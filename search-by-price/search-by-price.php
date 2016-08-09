<?php
/**
Plugin Name: WPAdverts Snippets - Search by price
Version: 1.0
Author: Greg Winiarski
Description: Adds price min and max fields into [adverts_list] search bar.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter( 'adverts_form_load', 'search_by_price_form_load' );
add_filter( 'adverts_list_query', 'search_by_price_query' );

/**
 * Adds price min and max fields into search form in [adverts_list].
 * 
 * @param array $form Search form scheme
 * @return array Customized search form scheme 
 */
function search_by_price_form_load( $form ) {
    
    if( $form['name'] != 'search' ) {
        return $form;
    }
    
    wp_enqueue_script( 'adverts-auto-numeric' );

    $form['field'][] = array(
        "name" => "price_min",
        "type" => "adverts_field_text",
        "class" => "adverts-filter-money",
        "order" => 20,
        "label" => "",
        "placeholder" => "Price min.",
        "meta" => array(
            "search_group" => "visible",
            "search_type" => "half" 
        )
    );
    
    $form['field'][] = array(
        "name" => "price_max",
        "type" => "adverts_field_text",
        "class" => "adverts-filter-money",
        "order" => 20,
        "label" => "",
        "placeholder" => "Price max.",
        "meta" => array(
            "search_group" => "visible",
            "search_type" => "half" 
        )
    );
    
    return $form;
}

/**
 * Adds search by price params to WP_Query
 * 
 * The query is modified only if $_GET['price_min'] or $_GET['price_max']
 * is greater than 0.
 * 
 * @param array $args WP_Query args
 * @return array Modified WP_Query args
 */
function search_by_price_query( $args ) {
    
    if( adverts_request( 'price_min' ) ) {
        
        $args["meta_query"][] = array( 
            'key' => 'adverts_price', 
            'value' => adverts_filter_money( adverts_request( 'price_min' ) ), 
            'compare' => '>=',
            'type' => 'DECIMAL(12,2)'
        );
    }

    if( adverts_request( 'price_max' ) ) {
        $args["meta_query"][] = array( 
            'key' => 'adverts_price', 
            'value' => adverts_filter_money( adverts_request( 'price_max' ) ), 
            'compare' => '<=',
            'type' => 'DECIMAL(12,2)'
        );
    }
    
    return $args;
}
