<?php
/**
 * Plugin Name: WPAdverts Snippets - Search by Form Scheme
 * Version: 1.0
 * Author: Laurie Greysky with guidence from Greg Winiarski
 * Description: Adds Form Scheme dropdown input to [adverts_list] search bar. Requires "WPAdverts Snippets - Return Form Scheme"
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.


if ( function_exists('rfs_get_wpadverts_forms') ) {
	add_filter( 'adverts_form_load', 'search_by_form_scheme_form_load' );
	add_filter( 'adverts_list_query', 'search_by_form_scheme_query' );
	
	function rfs_search_by_form_scheme_options() {

		$form_scheme = rfs_get_wpadverts_forms();
		
		$options = array();
		
		foreach ($form_scheme as $value => $form) {
			
			$options[]	= array('value' => $value,
								'text' => $form['title'],
								'depth' => 0);
		}
		
		return $options;
	}
}

/**
 * Adds category dropdown into search form in [adverts_list].
 * 
 * @param array $form Search form scheme
 * @return array Customized search form scheme 
 */
function search_by_form_scheme_form_load( $form ) {

    if( $form['name'] != 'search' ) {
        return $form;
    }

    $form['field'][] = array(
        "name" => "advert_form_scheme",
        "type" => "adverts_field_select",
        "order" => 10,
        "label" => __("Form Scheme", "adverts"),
        "max_choices" => 10,
        "options" => array(),
        "options_callback" => "rfs_search_by_form_scheme_options",
        "meta" => array(
            "search_group" => "visible",
            "search_type" => "full" 
        )
    );
    
    return $form;
}

/**
 * Adds meta_query param to WP_Query
 * 
 * The meta_query is added only if it is in $_GET['advert_category_form_scheme']
 * 
 * @param array $args WP_Query args
 * @return array Modified WP_Query args
 */
function search_by_form_scheme_query( $args ) {
    
    if( ! adverts_request( "advert_form_scheme" ) ) {
        return $args;
    }
    	
    $args["meta_query"] = array(
        array(
            'key' 	=> '_wpacf_form_scheme_id',
            'value'	=> adverts_request( "advert_form_scheme" ),
            'compare'   => 'IN',
        )
    );
    
    return $args;
}
