<?php
/*
 * Plugin Name: Dependant Taxonomy Dropdown
 * Plugin URI: http://wpadverts.com/
 * Description: Replaces taxonomy dropdown (for example Category) in [adverts_add] form with dependend AJAX drop-downs.
 * Author: Greg Winiarski
 */

/**
 * DOWNLOAD:
 * You can download this extension using the link below:
 * https://wpadverts.com/snippets/dependant-taxonomy-dropdown.zip
 * 
 * INSTALLATION:
 * In order to install downloaded extension go to wp-admin / Plugins / Add New / Upload panel
 * and upload and activate it from there.
 */

add_action( "init", "dependant_taxonomy_dropdown_init", 1000 );
add_filter( "adverts_form_load", "dependant_taxonomy_dropdown_form_load" );

/**
 * Initiates taxonomy dropdown
 * 
 * This function is executed by the "init" action.
 * 
 * @since 1.0
 * @return void
 */
function dependant_taxonomy_dropdown_init() {
   
    if( ! defined( "ADVERTS_PATH" ) ) {
        return;
    }
    
    if( function_exists( "wpadverts_snippet_run") ) {
        $url = plugins_url()  .'/wpadverts-snippets';
    } else {
        $url = plugins_url();
    }
    
    wp_register_script( 
        'dependant-taxonomy-dropdown', 
        $url  .'/dependant-taxonomy-dropdown/dependant-taxonomy-dropdown.js', 
        array( 'jquery' ), 
        "1", 
        true
    );
    
    adverts_form_add_field("adverts_field_select_dependant", array(
        "renderer" => "dependant_taxonomy_dropdown",
        "callback_save" => "adverts_save_multi",
        "callback_bind" => "adverts_bind_multi",
    ));
    
    add_action('wp_ajax_dependant_taxonomy_dropdown', 'dependant_taxonomy_dropdown_ajax');
    add_action('wp_ajax_nopriv_dependant_taxonomy_dropdown', 'dependant_taxonomy_dropdown_ajax');
}

/**
 * Dependant Taxonomy Dropdown Field HTML
 * 
 * This function renders the dependend taxonomy dropdown in the form.
 * 
 * @since 1.0
 * @param array $field
 * @return void
 */
function dependant_taxonomy_dropdown( $field ) {
    
    wp_enqueue_script( 'dependant-taxonomy-dropdown' );
    $value = 0;
    
    if( isset( $field["value"][0] ) ) {
        $value = $field["value"][0];
    } else if( isset( $field["value"] ) ) {
        $value = $field["value"];
    }
    
    echo '<style type="text/css">
    label[for="'.$field["name"].'"] { float: left !important }
    .dependant-taxonomy-dropdown > select { width: 92% !important; margin: 0 0 5px 0 }
    </style>';
    echo '<div class="dependant-taxonomy-dropdown-ui" data-taxonomy="'.$field["dtd_use_taxonomy"].'">';
    echo '<div class="dependant-taxonomy-dropdown"></div>';
    adverts_field_hidden( array(
        "name" => $field["name"],
        "value" => $value,
        "class" => "dependant-taxonomy-dropdown-value"
    ) );
    echo '<div class="dependant-taxonomy-loader"><span class="adverts-loader adverts-icon-spinner animate-spin"></span></div>';
    echo '</div>';
}

/**
 * Returns options for the taxonomy dropdown (<select>)
 * 
 * This function is used to get options for a dropdown, it can generate the options
 * for any taxonomy
 * 
 * @since   1.0
 * @param   string    $taxonomy   Taxonomy Name
 * @param   int       $parent     Id of a parent taxonomy
 * @return  array                 array of array( "value" => "", "text" => "", "depth" => 0 )
 */
function dependant_taxonomy_dropdown_taxonomies( $taxonomy = "advert_category", $parent = 0 ) {
    $args = array(
        'taxonomy'      => $taxonomy,
        'hierarchical'  => true,
        'orderby'       => 'name',
        'order'         => 'ASC',
        'hide_empty'    => false,
        'depth'         => 0,
        'selected'      => 0,
        'show_count'    => 0,
        'parent'        => $parent
    );
    
    $terms = get_terms( $taxonomy, $args );
    $array = array();
    
    foreach( $terms as $term ) {
        $array[] = array(                    
            "value" => esc_attr( $term->term_id ),
            "text" => apply_filters( 'list_cats', $term->name, $term ),
            "depth" => 0
        );
    }
    
    return $array;
}

/**
 * Updates the [adverts_form] scheme
 * 
 * Registers the adverts_field_select_dependant field in [adverts_add] form.
 * This function is executed by the adverts_form_load filter.
 * 
 * @see adverts_form_load filter
 * 
 * @since   1.0
 * @param   array $form     Form scheme
 * @return  array           Updated form scheme
 */
function dependant_taxonomy_dropdown_form_load( $form ) {
    if( $form["name"] != "advert" ) {
        return $form;
    }
    
    foreach( $form["field"] as $key => $field ) {
        if( $field["name"] === "advert_category" ) {
            $form["field"][$key]["type"] = "adverts_field_select_dependant";
            $form["field"][$key]["options_callback"] = null;
            $form["field"][$key]["options"] = array();
            $form["field"][$key]["dtd_use_taxonomy"] = "advert_category";
        }
    }
    
    return $form;
}

/**
 * AJAX dependant_taxonomy_dropdown action
 * 
 * This function is executed when server requests 
 * /wp-admin/admin-ajax.php?action=dependant_taxonomy_dropdown
 * 
 * The function returns dependant dropdowns HTML based on "id" of currently
 * selected category.
 * 
 * Note the function returns HTML for all the dropdowns.
 * 
 * @since 1.0
 * @return void
 */
function dependant_taxonomy_dropdown_ajax() {
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), array() );
    $taxonomy = adverts_request( 'taxonomy' );
    
    $id = absint( adverts_request( "id" ) );
    $ancestors = get_ancestors( $id, $taxonomy );
    $tax_ids = array_merge( array( 0 ), array_reverse( $ancestors ) );
    
    if( $id > 0 ) {
        $tax_ids[] = $id;
    }
    
    $total = count( $tax_ids );
    
    ob_start();
    for( $i = 0; $i < $total; $i++ ) {
        $tax_id = $tax_ids[$i];
        
        if( isset( $tax_ids[ $i + 1 ] ) ) {
            $selected = $tax_ids[ $i + 1 ];
        } else {
            $selected = null;
        }
        
        $options = dependant_taxonomy_dropdown_taxonomies( $taxonomy, $tax_id );
        
        if( empty( $options ) ) {
            break;
        }
        
        adverts_field_select( array(
            "name" => $taxonomy . '_' . $i,
            "empty_option" => true,
            "empty_option_text" => "",
            "options" => $options,
            "class" => "",
            "value" => $selected
        ) );
    }
    $html = ob_get_clean();
    
    $response = new stdClass();
    $response->selected = $id;
    $response->html = $html;

    echo json_encode( $response );
    exit;
}