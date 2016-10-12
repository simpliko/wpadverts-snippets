<?php
/**
Plugin Name: WPAdverts Snippets - Custom Taxonomies
Version: 1.0
Author: Greg Winiarski
Description: Adds custom taxonomy to [adverts_add] and displays values (as links) in Ad details page.
*/

add_action( "init", "custom_taxonomies_init", 50 );
add_filter( "adverts_form_load", "custom_taxonomies_form_load" );
add_filter( "adverts_list_query", "custom_taxonomies_query" );
add_action( "adverts_tpl_single_details", "custom_taxonomies_tpl_single_details" );

/**
 * Custom Taxonomy Init 
 * 
 * Registers new custom taxonomy
 * 
 * @since 1.0
 * @return void
 */
function custom_taxonomies_init() {
    
    $args = array(
        'label' => __( "Taxonomy Example" ),
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'advert-example'),
    );
    
    register_taxonomy( 'advert_example', 'advert', $args );
}

/**
 * Returns options for advert_example field
 * 
 * This function is being used when generating category field in the (for example 
 * "post ad" form).
 * 
 * @uses adverts_walk_category_dropdown_tree()
 * @since 1.0
 * @return array
 */
function custom_taxonomies_options() {
    
    $args = array(
        'taxonomy'      => 'advert_example',
        'hierarchical'  => true,
        'orderby'       => 'name',
        'order'         => 'ASC',
        'hide_empty'    => false,
        'depth'         => 0,
        'selected'      => 0,
        'show_count'    => 0,
    );

    include_once ADVERTS_PATH . '/includes/class-walker-category-options.php';
    
    $walker = new Adverts_Walker_Category_Options;
    $params = array(
        get_terms( 'advert_example', $args ),
        0,
        $args
    );
    
    return call_user_func_array(array( &$walker, 'walk' ), $params );
}

/**
 * Adds "Taxonomy Example" field into [adverts_add] form.
 * 
 * @since 1.0
 * @access public
 * @param array $form   Adverts Form structure
 * @return array        Customized form structure
 */
function custom_taxonomies_form_load( $form ) {
    global $pagenow;
    
    if( $form['name'] == "advert" && $pagenow != "post.php" ) {
        $form["field"][] = array(
            "name" => "advert_example",
            "type" => "adverts_field_select",
            "order" => 20,
            "label" => __( "Taxonomy Example", "adverts" ),
            "max_choices" => 10,
            "options" => array(),
            "options_callback" => "custom_taxonomies_options"
        );
    }
    
    if( $form['name'] == "search" ) {
        $form['field'][] = array(
            "name" => "advert_example",
            "type" => "adverts_field_select",
            "order" => 20,
            "label" => "",
            "max_choices" => 10,
            "options" => array(),
            "options_callback" => "custom_taxonomies_options",
            "meta" => array(
                "search_group" => "visible",
                "search_type" => "full" 
            )
        );
    }
    
    return $form;
}

/**
 * Displays selected terms on Ad details page.
 * 
 * This functions is executed by adverts_tpl_single_details filter
 * 
 * @since 1.0
 * @access public
 * @param int $post_id Currently displayed post ID
 */
function custom_taxonomies_tpl_single_details( $post_id ) {

    $terms = get_the_terms( $post_id, 'advert_example' );
    
    ?>
    
    <?php if(! empty( $terms ) ): ?>
    <div class="adverts-grid-row">
        <div class="adverts-grid-col adverts-col-30">
            <span class="adverts-round-icon adverts-icon-wordpress"></span>
            <span class="adverts-row-title">Taxonomy Example</span>
        </div>
        <div class="adverts-grid-col adverts-col-65">
            <?php foreach( $terms as $term ): ?>
            <a href="<?php echo esc_attr( get_term_link( $term ) ) ?>"><?php echo join( " / ", advert_category_path( $term ) ) ?></a><br/>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php
}

/**
 * Adds tax_query param to WP_Query
 * 
 * The tax_query is added only if it is in $_GET['advert_example']
 * 
 * @param array $args WP_Query args
 * @return array Modified WP_Query args
 */
function custom_taxonomies_query( $args ) {
    
    if( ! adverts_request( "advert_example" ) ) {
        return $args;
    }
    
    $args["tax_query"] = array(
        array(
            'taxonomy' => 'advert_example',
            'field'    => 'term_id',
            'terms'    => adverts_request( "advert_example" ),
        ),
    );
    
    return $args;
}