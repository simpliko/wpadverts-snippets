<?php
/**
Plugin Name: WPAdverts Snippets - Custom Fields Taxonomies
Version: 1.0
Author: Greg Winiarski
Description: Handle new taxonomy in Custom Fields add-on
*/

add_action( "init", function() {
    new Custom_Fields_Taxonomies_Handler( "hospital", "advert_category");
}, 100 );

//add_action( "init", "custom_fields_taxonomies_init" );

/**
 * Init new taxonomy and register data source.
 *
 * @since 1.0
 * @return void
 */
function custom_fields_taxonomies_init() {

    // Register new taxonomy and assign it to WPAdverts
    $args = array(
        'label' => __( "Taxonomy Example" ),
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'taxonomy-example')
    );

    register_taxonomy( 'taxonomy_example', 'advert', $args );

    if( ! function_exists( "wpadverts_custom_fields_register_data_source" ) ) {
        // Custom Fields add-on disabled, nothing to do here
        return;
    }
    
    // Register new data source and connect it to previously created taxonomy
    wpadverts_custom_fields_register_data_source(array(
        "name" => "taxonomy-example",           
        "title" => "Taxonomy Example",                   
        "callback" => "custom_fields_taxonomies_data_source",
        "taxonomy" => "taxonomy_example"
    ));

    // Connect taxonomy to a field in [adverts_add] form.
    new Custom_Fields_Taxonomies_Handler( "taxonomy_test", "taxonomy_example");
}

/**
 * Data source function for taxonomy-example data source.
 * 
 * This function will return all options created for taxonomy_example, note 
 * this will work with WPAdverts 1.1.2 or newer only.
 * 
 * @return array    List of taxonomy taxes
 */
function custom_fields_taxonomies_data_source() {
    return adverts_taxonomies( 'taxonomy_example' );
}

if(class_exists("Custom_Fields_Taxonomies_Handler" ) ) {
    // register this class only once, if it is already registered return
    return;
}

class Custom_Fields_Taxonomies_Handler
{
    /**
     * Field name in the custom form
     * 
     * @var string
     */
    protected $_field_name = null;
    
    /**
     * Taxonomy name to which the _field_name should be assigned
     *
     * @var string
     */
    protected $_taxonomy_name = null;
    
    /**
     * Class constructor
     * 
     * @param string $field_name        Field name to which taxonomy will be connected
     * @param string $taxonomy_name     Taxonomy name
     */
    public function __construct( $field_name, $taxonomy_name ) 
    {
        $this->_field_name = $field_name;
        $this->_taxonomy_name = $taxonomy_name;
        
        add_filter( "adverts_data_box_content_exclude", array( $this, "exclude" ) );
        add_filter( "adverts_post_save", array( $this, "save" ), 10, 2 );
        add_filter( "adverts_form_bind", array( $this, "bind" ), 10, 2 );
    }
    
    /**
     * Excludes a field from a form in wp-admin / Classifieds panel.
     * 
     * The field is being excluded because since this is a taxonomy field 
     * WordPress will handle it in a sidebar.
     * 
     * @param array $exclude    List of fields to exclude
     * @return array
     */
    public function exclude( $exclude ) {
        $exclude[] = $this->_field_name;
        return $exclude;
    }
    
    /**
     * Save field data as taxonomy not meta
     * 
     * @param Adverts_Form $form    Currently proccessed form
     * @param int $post_id          Post ID
     * @return void
     */
    public function save( $form, $post_id ) {
        wp_set_post_terms( $post_id, $form->get_value( $this->_field_name ), $this->_taxonomy_name );
        delete_post_meta( $post_id, $this->_field_name );
    }
    
    /**
     * Bind data to form fields
     * 
     * @param Adverts_Form $form    Currently proccessed form
     * @param array $data           Form data that will be assigned to fields
     * @return Adverts_Form
     */
    public function bind( $form, $data ) {
        if( isset( $data[ $this->_taxonomy_name ] ) ) {
            $form->set_value( $this->_field_name, (array)$data[ $this->_taxonomy_name ] );
        }
        return $form;
    }
}