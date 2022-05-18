<?php
/*
 * Plugin Name: Post View Counter Integration
 * Plugin URI: https://wpadverts.com/
 * Description: Displays the Post View Counter views in the Ad details pages and the [adverts_manage].
 * Author: Greg Winiarski
 * Version: 1.0.0
 */

 add_action( "init", "pvc_integration_init" );

 function pvc_integration_init() {
     if( ! class_exists( "Post_Views_Counter" ) ) {
         return;
     }

     // Ad details
     add_action( "adverts_tpl_single_details", "pvc_integration_tpl_single_details" );

     // [adverts_manage] possible actions: adverts_sh_manage_actions_left, adverts_sh_manage_actions_right, adverts_sh_manage_actions_more
     add_action( "adverts_sh_manage_actions_left", "pvc_integration_manage_views", 1000 );
 }

 function pvc_integration_tpl_single_details( $post_id ) {
     ?>

    <div class="adverts-grid-row">
            <div class="adverts-grid-col adverts-col-30">
                <span class="adverts-round-icon adverts-icon-chart-bar"></span>
                <span class="adverts-row-title"><?php _e("Post Views", "wpadverts") ?></span>
            </div>
            <div class="adverts-grid-col adverts-col-65">
                <?php echo pvc_get_post_views( $post_id ) ?>
            </div>
    </div>

    <?php
 }

 function pvc_integration_manage_views( $post_id ) {
     echo sprintf( '<span class="adverts-manage-action" style="border-color: transparent">Views %s</span>', pvc_get_post_views( $post_id ) );
 }