<?php
/**
Plugin Name: WPAdverts Snippets - Change Author Details
Version: 1.0.0
Author: Greg Winiarski
Description: Adds a new tab to the Author details page
*/

add_action( "adverts_authors_single_tabs", function( $tabs, $author_id ) {
    $tabs["custom-tab"] = array(
        "title"     => "Custom Tab",
        "hint"      => null,
        "callback"  => "change_author_details_tab",
        "order"     => 15
    );
    return $tabs;
}, 10, 2 );

function change_author_details_tab( $post_id, $author_id, $action ) {
    echo "[Custom Tab Content Here]";
}