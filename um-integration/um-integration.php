<?php
/**
Plugin Name: WPAdverts Snippets - UM Integration
Version: 1.0
Author: Greg Winiarski
Description: Basic integration with Ultimate Members plugin. Adds a tab with user Ads to the user profile
*/

class UM_Integration {
    
    public function __construct() {
        add_filter( 'um_profile_tabs', array( $this, "add_tab" ), 1000 );
        add_action( 'um_profile_content_classifieds_default', array( $this, "content" ) );
        
    }
    
    public function add_tab( $tabs ) {
        $tabs[ 'classifieds' ] = array(
            'name'   => 'Classifieds',
            'icon'   => 'um-faicon-pencil',
            'custom' => true
	);

	UM()->options()->options[ 'profile_tab_' . 'classifieds' ] = true;

	return $tabs;
    }
    
    public function content( $args ){
	?>
	<div class="um-field">
            <?php 
                // more params for [adverts_list] shortcode you will find here
                // https://wpadverts.com/doc/creating-ads-list-adverts_list/
                echo shortcode_adverts_list(array(
                    "author" => um_profile_id()
                ))
            ?>
	</div>
	<?php
	
    }
}

$um_integration = new UM_Integration();
