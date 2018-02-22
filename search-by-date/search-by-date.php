<?php
/**
Plugin Name: WPAdverts Snippets - Search by date
Version: 1.0
Author: Greg Winiarski
Description: Adds date field to the [adverts_list] search bar.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter( 'adverts_form_load', 'search_by_date_form_load' );
add_filter( 'adverts_list_query', 'search_by_date_query' );

/**
 * Adds posted_date field into search form in [adverts_list].
 * 
 * @param array $form Search form scheme
 * @return array Customized search form scheme 
 */
function search_by_date_form_load( $form ) {
    
    if( $form['name'] != 'search' ) {
        return $form;
    }
    
    $form['field'][] = array(
        "name" => "posted_range",
        "type" => "adverts_field_select",
        "class" => "",
        "order" => 20,
        "label" => "",
        "options" => array(
            array( "value" => "today", "text" => "Today"),
            array( "value" => "since-yesterday", "text" => "Since Yesterday"),
            array( "value" => "less-than-7-days-ago", "text" => "Less than 7 days ago"),
            array( "value" => "less-than-30-days-ago", "text" => "Less than 30 days ago"),
        ),
        "empty_option" => true,
        "empty_option_text" => "Select date range ...",
        "meta" => array(
            "search_group" => "visible",
            "search_type" => "full" 
        )
    );
    
    return $form;
}

/**
 * Adds search by date params to WP_Query
 * 
 * The query is modified only if $_GET['posted_range'] is set and has non empty value
 * 
 * @param array $args WP_Query args
 * @return array Modified WP_Query args
 */
function search_by_date_query( $args ) {
    
    if( adverts_request( 'posted_range' ) ) {
        
        $date_query = null;
        $ct = current_time( "timestamp", 1 );
        
        switch( adverts_request( 'posted_range' ) ) {
            case "today":
                $date_query = array( 
                    "after" => date("Y-m-d 00:00:00", current_time( "timestamp", 1 ) )
                );
                break;
            case "since-yesterday":
                $date_query = array( 
                    "after" => date("Y-m-d 00:00:00",  strtotime( "yesterday", $ct ) )
                );
                break;
            case "less-than-7-days-ago":
                $date_query = array( 
                    "after" => date("Y-m-d 00:00:00",  strtotime( "today -7 days", $ct ) )
                );
                break;
            case "less-than-30-days-ago":
                $date_query = array( 
                    "after" => date("Y-m-d 00:00:00",  strtotime( "today -30 days", $ct ) )
                );
                break;
                
        }
        
        if($date_query) {
            $args["date_query"] = $date_query;
        }
    }
    
    return $args;
}
