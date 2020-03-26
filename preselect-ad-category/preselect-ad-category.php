<?php
/*
 * Plugin Name: Preselect Ad Category
 * Plugin URI: https://wpadverts.com/
 * Description: Allows preselcting category in [adverts_add] shortcode, this allows using some custom form per category.
 * Author: Greg Winiarski
 * Version: 1.1.0
 */

if(is_admin()) {
    add_action( "init", "preselect_ad_category_init_admin" );
} 

add_action( "init", "preselect_ad_category_init", 20 );

/**
 * Replace the [adverts_add] shortcode with a custom shortcode
 * 
 * The custom shortcode will first require user to select a category 
 * and then show the normal [adverts_add]
 * 
 * @since 1.0
 * @return void
 */
function preselect_ad_category_init() {
    remove_shortcode( "adverts_add" );
    add_shortcode( "adverts_add", "preselect_ad_category_shortcode" );
    add_filter( "shortcode_atts_adverts_list", "preselect_ad_category_atts", 9000 );
    
    if( defined( "PRESELECT_AD_CATEGORY_USE_DTD" ) && PRESELECT_AD_CATEGORY_USE_DTD ) {
        add_filter( "template_redirect", "preselect_ad_category_dtd_redirect" );
    }
}

/**
 * Admin Init
 * 
 * Registers edit and save actions in the Advert Category edition.
 * 
 * @since 1.0
 * @return void
 */
function preselect_ad_category_init_admin() {
    add_action( 'advert_category_edit_form_fields', 'preselect_ad_category_edit_meta_fields', 10, 2 );
    add_action( 'edited_advert_category', 'preselect_ad_category_save', 10, 2 );  
}

/**
 * Displays form scheme field in advert category edition.
 * 
 * This function displays form scheme selection dropdown in the term edition
 * panel (wp-admin / Classifieds / Categories).
 * 
 * IMPORTANT: To have some form schemes listed you need to have the Custom Fields
 * add-on activated and some [adverts_add] form schemes created.
 * 
 * @param WP_Term $term     Term being edited
 */
function preselect_ad_category_edit_meta_fields( $term ) {
    
    $loop = new WP_Query(array(
        'post_type' => 'wpadverts-form',
        'post_status' => array('wpad-form-add'),
        'posts_per_page' => -1
    ));

    $term_id = get_term_meta( $term->term_id, "category_form_scheme", true );
    
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="category_form_scheme">
                <?php _e( 'Form Scheme', 'preselect_ad_category' ); ?>
            </label>
        </th>
        <td>
            <select name="category_form_scheme">
                <option value=""><?php _e( "Default", "preselect_ad_category" ) ?></option>
                <?php foreach( $loop->posts as $scheme ): ?>
                <option value="<?php echo esc_attr($scheme->ID) ?>" <?php selected($scheme->ID, $term_id) ?>><?php echo esc_html($scheme->post_title) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php
    
    $loop = new WP_Query(array(
        'post_type' => 'wpadverts-form',
        'post_status' => array('wpad-form-search'),
        'posts_per_page' => -1
    ));

    $term_id = get_term_meta( $term->term_id, "category_form_scheme_search", true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="category_form_scheme_search">
                <?php _e( 'Form Scheme Search', 'preselect_ad_category' ); ?>
            </label>
        </th>
        <td>
            <select name="category_form_scheme_search">
                <option value=""><?php _e( "Default", "preselect_ad_category" ) ?></option>
                <?php foreach( $loop->posts as $scheme ): ?>
                <option value="<?php echo esc_attr($scheme->ID) ?>" <?php selected($scheme->ID, $term_id) ?>><?php echo esc_html($scheme->post_title) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php
}

/**
 * Saves the category_form_scheme in meta table.
 * 
 * This function is executed when edition form in wp-admin / Classifieds / Categories
 * panel is submitted.
 * 
 * @param int $term_id      Edited Term ID
 * @return void
 */
function preselect_ad_category_save( $term_id ) {
    
    $cfs = intval( adverts_request( "category_form_scheme" ) );
    
    if ( $cfs > 0 ) {
        update_term_meta( $term_id, "category_form_scheme", $cfs );
    } else {
        delete_term_meta( $term_id, "category_form_scheme" ); 
    }
    
    $cfss = intval( adverts_request( "category_form_scheme_search" ) );
    
    if ( $cfss > 0 ) {
        update_term_meta( $term_id, "category_form_scheme_search", $cfss );
    } else {
        delete_term_meta( $term_id, "category_form_scheme_search" ); 
    }
}

/**
 * New [adverts_add] shortcode
 * 
 * Shows the preselection interface or runs shortcode_adverts_add() if the
 * category is already preselected
 * 
 * @see shortcode_adverts_add()
 * 
 * @param array $atts   Shortcode attributes
 * @return string       HTML for the shortcode
 */
function preselect_ad_category_shortcode( $atts ) {
    
    if( ! is_array( $atts ) ) {
        $atts = array();
    }
    
    $term_slug =  trim( adverts_request( "preselected_category" ) );
    $is_preselected = false;
    $flash = array(
        "error" => array( ),
        "info" => array( )
    );
    
    if( ! empty( $term_slug ) ) {
        $term = get_term_by( "slug", adverts_request( "preselected_category" ), "advert_category" );
        
        if( ! is_wp_error( $term ) && is_object( $term ) ) {
            $is_preselected = true;
        } 
    }
    
    if( ! $is_preselected ) {
        return preselect_ad_category_display( $atts );
    }

    if( get_term_meta( $term->term_id, "wpadvert_is_restricted", true ) ) {
        $message = __( 'This category is disabled. <a href="%s">Go Back</a>.', "preselect-ad-category" );
        
        $flash["error"][] = array(
            "message" => sprintf( $message, remove_query_arg( "preselected_category" ) ),
            "icon" => "adverts-icon-no"
        );
        
        ob_start();
        adverts_flash( $flash );
        return ob_get_clean();
    }
    
    add_filter( "adverts_form_load", "preselect_ad_category_form_load" );
    add_filter( "adverts_form_bind", "preselect_ad_category_form_bind", 10, 2 );

    $scheme_id = intval( get_term_meta( $term->term_id, "category_form_scheme", true ) );

    if( $scheme_id ) {
        $form_scheme = get_post( $scheme_id );
        if( $form_scheme ) {
            $atts["form_scheme"] = $form_scheme->post_name;
            $atts["form_scheme_id"] = $form_scheme->ID;
        }
    }

    return shortcode_adverts_add( $atts );
}

/**
 * Renders the category selection interface
 * 
 * This function is based on Adverts_Widget_Categories
 * 
 * @see Adverts_Widget_Categories::widget_multi_level()
 * 
 * @param array $atts   Shortcode attributes
 * @return string       HTML for the shortcode
 */
function preselect_ad_category_display( $atts ) {
    
    $terms = get_terms( 'advert_category', array( 
        'hide_empty' => 0,
        'parent' => 0,
    ) );
        
    ob_start();
    
    include_once ADVERTS_PATH . 'includes/shortcodes.php';
    
    $adverts_flash = array(
        "error" => array( ),
        "info" => array(            
            array( 
                "message" => __( "Select category before creating an Advert", "preselect-ad-category" ),
                "icon" => "adverts-icon-tag"
            )
        )
    );
    adverts_flash($adverts_flash);
    
    ?>

    <div class="wpjb adverts-widget adverts-widget-categories adverts-widget-multi-level-categories">
        <div class="adverts-grid adverts-grid-compact">

            <?php
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ):
                    if( defined( "PRESELECT_AD_CATEGORY_USE_DTD" ) && PRESELECT_AD_CATEGORY_USE_DTD ) {
                        preselect_ad_category_dtd();
                    } else {
                        preselect_ad_category_display_inner( $terms, 0 );
                    }
                else:
            ?>
            <div class="adverts-grid-row">
                <div class="adverts-col-100">
                    <span><?php _e("No categories found.", "adverts") ?></span>
                </div>
            </div>
            <?php endif; ?> 
        </div>
    </div>
    

    <?php

    return ob_get_clean();
}

function preselect_ad_category_dtd() {
    $field = array(
        "name" => "preselected_dtd_category",
        "value" => "",
        "dtd_use_taxonomy" => "advert_category"
    );
    
    wp_enqueue_script( "adverts-frontend" );
    wp_enqueue_style( "adverts-frontend" );
    wp_enqueue_style( "adverts-icons" );
    
    ?>
    <form action="" method="post">
        <?php wp_nonce_field( "preselected-dtd-category" ) ?>
        <?php dependant_taxonomy_dropdown( $field ) ?>
        <input type="submit" value="Select" class="button preselect-ad-category-dtd-submit" />
    </form>

    <?php
}

/**
 * Prints category interface items
 * 
 * This function renders one category level and calls itself recursively
 * to render inner levels.
 * 
 * @see Adverts_Widget_Categories::print_terms_multi_level()
 * 
 * @param array $terms      
 * @param int $level
 */
function preselect_ad_category_display_inner( $terms, $level = 0 ) {
    
    foreach ( $terms as $term_item ):
        $default_icon = "folder";
        $icon = adverts_taxonomy_get( "advert_category", $term_item->term_id, "advert_category_icon", $default_icon );
        
        if ( $icon == "" ) {
            $icon = $default_icon;
        }

        ?>
        <div class="adverts-grid-row">
            <div class="adverts-col-100">
                <span class="adverts-widget-grid-link <?php echo "adverts-icon-".$icon ?>">
                    <?php if( get_term_meta( $term_item->term_id, "wpadvert_is_restricted", true ) ): ?>
                    <?php echo esc_html($term_item->name) ?>
                    <?php else: ?>
                    <a href="<?php echo esc_attr(add_query_arg("preselected_category", $term_item->slug)) ?>"><?php echo esc_html($term_item->name) ?></a>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <?php

        $child_terms = get_terms( 'advert_category', array(
            'hide_empty' => 0,
            'parent' => (int)$term_item->term_id,
        ) );

        if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ):
            ?>
            <div class="adverts-multi-level <?php echo 'adverts-multi-level-'.$level ?>">
            <?php preselect_ad_category_display_inner( $child_terms, $level+1 ); ?>
            </div>
            <?php
        endif;

    endforeach;
}

/**
 * Locks the advert_cateogry field
 * 
 * This function makes the category field a single select field, sets value
 * to the preselected category and disables users ability to change the category.
 * 
 * @param array $form   Form scheme
 * @return array        Updated form scheme
 */
function preselect_ad_category_form_load( $form ) {
    if( $form['name'] != "advert" ) {
        return $form;
    }
    
    $term = get_term_by( "slug", adverts_request( "preselected_category" ), "advert_category" );

    if( is_wp_error( $term ) || $term === null ) {
        return $form;
    }
    
    foreach( $form["field"] as $key => $field ) {
        if( $field["name"] == "advert_category" ) {
            $form["field"][$key]["attr"]["readonly"] = "readonly";
            $form["field"][$key]["value"] = $term->term_id;
            $form["field"][$key]["max_choices"] = 1;
        }
    }
    return $form;
}

/**
 * Sets value for the advert_category field
 * 
 * This function makes sure that the preselected category was not changed.
 * 
 * @param Adverts_Form $form    Form Object
 * @return void
 */
function preselect_ad_category_form_bind( $form ) {

    $term = get_term_by( "slug", adverts_request( "preselected_category" ), "advert_category" );

    if( is_wp_error( $term ) || $term === null ) {
        return null;
    }

    $form->set_value( "advert_category", $term->term_id );
}


/**
 * Applies form_scheme_id param to [adverts_list] shortcode
 * 
 * This function applies form_scheme_id param to the [adverts_list] shortcode
 * when displaying it on the advert-category pages
 * 
 * @since   1.1.0
 * @param   array   $out    Shortcode atts
 * @return  array
 */
function preselect_ad_category_atts( $out ) {
    if( is_tax( 'advert_category' ) ) {
        $term_id = get_queried_object_id();
        $form_id = get_term_meta( $term_id, "category_form_scheme_search", true );
        
        if( intval( $form_id ) > 0 ) {
            
            $form_scheme = get_post( $form_id );
            
            $out["form_scheme_id"] = $form_id;
            $out["form_scheme"] = $form_scheme->post_name;
        }
    }
    return $out;
}

function preselect_ad_category_dtd_redirect( $template ) {
    
    if( ! wp_verify_nonce( adverts_request( '_wpnonce' ), 'preselected-dtd-category' ) ) {
        return $template;
    }
    
    $dtd_category = absint( adverts_request( 'preselected_dtd_category' ) );
    
    if( ! is_numeric( $dtd_category ) ) {
        return $template;
    }
    
    $term = get_term( $dtd_category );
    
    if( is_wp_error( $term ) ) {
        wp_die( __( "Incorrect category ID provided", 'preselect-ad-category' ) );
    }
    
    wp_redirect( add_query_arg( array( "preselected_category" => $term->slug ), get_permalink( get_the_ID() ) ) );
    exit;
}