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
add_filter( "adverts_form_load", "dependant_taxonomy_dropdown_form_load", 9999 );
add_action( "save_post_advert", "dependant_taxonomy_dropdown_save_advert", 20, 3 );

function dependant_taxonomy_dropdown_get( $type = null ) {
    $arr = array(
        "advert" => array(
            "advert_category" => array( "taxonomy" => "advert_category" ),
            "adverts_location" => array( "taxonomy" => "advert_location", "rename_to" => "advert_location" )
        ),
        "search" => array(
            "adverts_category" => array( "taxonomy" => "advert_category" ),
            "location" => array( "taxonomy" => "advert_location" )
        )
    );
    
    $filtered = apply_filters( "dependant_taxonomy_dropdown_get", $arr );
    
    if( $type === null ) {
        return $filtered;
    }
    
    if( isset( $filtered[$type] ) ) {
        return $filtered[$type];
    } else {
        return null;
    }
}

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
        "2", 
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
function dependant_taxonomy_dropdown( $field, $form ) {
    
    wp_enqueue_script( 'dependant-taxonomy-dropdown' );
    $value = 0;

    include_once ADVERTS_PATH . '/includes/class-html.php';
    
    $post_id = null;
    $terms = array();
    $field_name = $field["name"];
    $term_ids = array();
    
    if( is_admin() && isset( $_GET['post'] ) ) {
        $post_id = $_GET['post'];
    } elseif( adverts_request( "_post_id" ) ) {
        $post_id = adverts_request( "_post_id" );
    } elseif( adverts_request( "advert_id" ) ) {
        $post_id = adverts_request( "advert_id" );
    } elseif( isset( $field["meta"]["object_id"] ) ) {
        $post_id = $field["meta"]["object_id"];
    }
    
    if( $post_id !== null ) {
        $terms_array = wp_get_post_terms( $post_id, $field["dtd_use_taxonomy"] );
        
        if( is_array( $terms_array ) ) {
            foreach( $terms_array as $l ) {
                $term_ids[] = $l->term_id;
            }
        }
    }
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' && adverts_request( $field["dtd_use_taxonomy"] ) ) {
        $terms_ids = adverts_request( $field["dtd_use_taxonomy"] );
    }
    
    if( isset( $term_ids[0] ) ) {
        $value = $term_ids[0];
    } else {
        $value = "";
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
 * Updates the [adverts_add] and [adverts_list] scheme
 * 
 * Registers the adverts_field_select_dependant field in forms.
 * This function is executed by the adverts_form_load filter.
 * 
 * @see adverts_form_load filter
 * 
 * @since   1.0
 * @param   array $form     Form scheme
 * @return  array           Updated form scheme
 */
function dependant_taxonomy_dropdown_form_load( $form ) {
    
    $fields = dependant_taxonomy_dropdown_get( $form["name"] );
    
    if($fields === null ) {
        return $form;
    }
    
    foreach( $form["field"] as $key => $field ) {
        if( isset( $fields[ $field["name"] ] ) ) {
            if( isset( $fields[ $field["name"] ]["rename_to"] ) ) {
                $form["field"][$key]["name"] = $fields[ $field["name"] ]["rename_to"];
            }
            $form["field"][$key]["type"] = "adverts_field_select_dependant";
            $form["field"][$key]["options_callback"] = null;
            $form["field"][$key]["options"] = array();
            $form["field"][$key]["dtd_use_taxonomy"] = $fields[ $field["name"] ]["taxonomy"];
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
    
    $is_last = $i != $total;
    $is_enabled = $is_last;
    
    if( $id > 0 && get_term_meta( $id, "wpadvert_is_restricted", true ) == "1" ) {
        $is_enabled = false;
    }
    
    $response = new stdClass();
    $response->selected = $id;
    $response->is_enabled = apply_filters( "dependant_taxonomy_dropdown_is_enabled", $is_enabled, $id, $is_last );
    $response->html = $html;

    echo json_encode( $response );
    exit;
}



function dependant_taxonomy_dropdown_save_advert( $post_ID, $post, $update ) {
        
    if( ! isset( $_POST ) || empty( $_POST ) ) {
        return;
    }

    $fields = dependant_taxonomy_dropdown_get( "advert" );
    
    //echo "<pre>"; print_r($fields); print_r($_POST);
    
    foreach( $fields as $k => $field ) {
        if( isset( $_POST[$k] ) ) {
            
            if( ! empty( $_POST[$k] ) ) {
                $location = array( intval( $_POST[$k] ) );
            } else {
                $location = null;
            }
            //echo "$post_ID, ".print_r($location,true).", {$field['taxonomy']}";
            $result = wp_set_post_terms( $post_ID, $location, $field['taxonomy'] );
            //print_r($result);
        }
    }
}

add_action( "admin_footer", function() {
    ?>
<script type="text/javascript">
    if( typeof adverts_frontend_lang === 'undefined' ) {
        var adverts_frontend_lang = { ajaxurl: '<?php echo admin_url( 'admin-ajax.php' ) ?>' };
    }
</script>
    <?php
});

