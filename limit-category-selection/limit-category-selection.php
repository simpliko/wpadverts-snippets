<?php
/**
Plugin Name: WPAdverts Snippets - Limit Category Selection
Version: 1.0
Author: Greg Winiarski
Description: This addon allows to selecto ne category only when submitting an Ad in the frontend.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter("adverts_form_load", "limit_category_selection");

function limit_category_selection( $form ) {
    if($form["name"] != 'advert' || is_admin()) {
        return $form;
    }

    $count = count( $form["field"] );
    
    for( $i = 0; $i < $count; $i++ ) {
        if($form["field"][$i]["name"] == "advert_category") {
            $form["field"][$i]["max_choices"] = 1;
        }
    }
    

    return $form;
}
