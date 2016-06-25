<?php
/*
 * Plugin Name: WP Adverts MAL - Sync
 * Plugin URI: http://wpadverts.com/
 * Description: Allows to synchornize wp_mal_latlng table with rest of WPAdverts data.
 * Author: Greg Winiarski
 * Text Domain: mal-sync
 * Version: 1.0
 */

if( is_admin() ) {
    add_action( "admin_menu", "mal_sync_admin_init" );
    add_action( "wp_ajax_mal_sync", 'mal_sync_ajax' );
}

function mal_sync_admin_init() {
    add_management_page(
        __( 'MAL Sync', 'mal-sync' ),
        __( 'MAL Sync', 'mal-sync' ),
        'manage_options',
        'mal-sync',
        'mal_sync_page'
    );
}

function mal_sync_page() {
    global $wpdb;

    $tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}mal_latlng'" );
    $error = null;
    
    if( isset( $tables[0] ) ) {
        $exists = true;
    } else {
        $exists = false;
    }
    $exists = false;
    if( adverts_request( "create-table") == "1" && ! $exists ) {
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta("
        CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mal_latlng` (  
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `post_id` bigint(20) unsigned NOT NULL,
            `lat` double(18,15) NOT NULL,
            `lng` double(18,15) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `post_id` (`post_id`),
            KEY `post_lat_lng` (`post_id`,`lat`,`lng`)
        ) ENGINE=InnoDB;" );
        
        if( ! empty( $wpdb->last_error ) ) {
            $error = $wpdb->last_error;
        } else {
            $exists = true;
        }
    }
    
    wp_enqueue_script( 'jquery' );

    ?>
    <div class="wrap">
    
    <h1><?php echo __( "Maps and Locations | Sync", "mal-sync" ) ?></h1>
    
    <?php if( ! $exists ): ?>
    <div>
        <p class="mal-sync-table-missing">
            <?php _e( "Geolocation table does not exist!", "mal-sync" ) ?>
        </p>
        
        <a href="<?php echo add_query_arg("create-table", "1") ?>" class="button mal-sync-table-create"><?php _e( "Create NOW", "mal-sync" ) ?></a>
    
        <?php if( $error ): ?>
        <p class="mal-sync-error">
            <?php echo esc_html( $error ) ?>
        </p>
        <?php endif; ?>
        
    </div>
    <?php else: ?>
    
    <p>
        <a href="#" class="button mal-sync-init"><?php esc_html_e( "Sync Now!", "mal-sync" ) ?></a>
        <span class="mal-sync-progress">

            <span class="mal-sync-preloader">
                <span class="mal-sync-preloader-progress"></span>
            </span>

            <span class="mal-sync-completed"></span>
        </span>
    </p>



    <table class="widefat mal-sync-data striped">
        <tbody>

        </tbody>
    </table>
    <?php endif; ?>

    </div>

    <script type="text/javascript">
    var MAL = MAL || {};

    MAL.sync = {};
    MAL.sync.init = function() {
        var $ = jQuery;
        $(".mal-sync-preloader-progress").css("width", "0%");
        $(".mal-sync-completed").html("");
        $(".mal-sync-data tbody").empty();
        MAL.sync.run({
            offset: 0
        });
    };
    MAL.sync.run = function(data) {
        var $ = jQuery;
        var data = {
            action: "mal_sync",
            offset: data.offset
        };
        $.ajax({
            type: "POST",
            data: data,
            url: ajaxurl,
            dataType: "json",
            success: MAL.sync.success
        }); // end $.ajax
    };
    MAL.sync.success = function(response) {
        var $ = jQuery;

        $.each(response.posts, function(index, item) {

            var link = $("<a></a>").attr("href", item.admin_url).html(item.post_title);

            var tr = $("<tr></tr>");
            var td1 = $("<td></td>").addClass("import-system row-title").html(link);
            var td2 = $("<td></td>").addClass("desc").html(item.update_message);

            tr.append(td1);
            tr.append(td2);

            $(".mal-sync-data tbody").append(tr);
        });

        if(response.completed == 0) {
            // continue
            MAL.sync.run({
                offset: response.offset
            });
            var percent = (response.offset/response.total*100).toString() + "%";
            $(".mal-sync-preloader-progress").css("width", percent);
            $(".mal-sync-completed").html(percent);

        } else {
            // finish
            var percent = "100%";
            $(".mal-sync-preloader-progress").css("width", percent);
            $(".mal-sync-completed").html(percent);
            
            $(".mal-sync-init").show();
            $(".mal-sync-progress").hide();
            
        }
    };

    jQuery(function($) {
        $(".mal-sync-init").click(function(e) {
            e.preventDefault();

            $(this).hide();
            $(".mal-sync-progress").show();

            MAL.sync.init();
        });
    });
    </script>

    <style type="text/css">
        .mal-sync-table-missing {
            font-size: 16px;
            line-height: 28px;
            font-weight: bold;
            color: darkred;
        }
        .mal-sync-error {
            font-size: 16px;
            line-height: 28px;
            font-weight: bold;
            color: darkred;
            background-color: pink;
        }
        .mal-sync-progress {
            display: none;
        }
        .mal-sync-preloader {
            display: inline-block;
            border: 1px solid silver;
            padding: 4px;
            width: 50%;
            height: 20px
        }
        .mal-sync-preloader-progress {
            display: inline-block;
            height: 20px;
            width:10%;
            background-color: #0073aa;
        }

        .mal-sync-completed {
            line-height: 20px;
            font-size: 16px;
            vertical-align: super;
        }
    </style>

    <?php
}

function mal_sync_ajax() {
    global $wpdb;

    $x = 4;
    $offset = absint( adverts_request( "offset" ) );
    $completed = 0;

    $query = new WP_Query( array(
        'post_type' => 'advert',
        'posts_per_page' => $x,
        'offset' => $offset
    ) );

    $total = $query->found_posts;
    $posts = array();

    if( $total <= $offset + $x ) {
        $completed = 1;
    }

    foreach( $query->posts as $post ) {
        $geo_type = null;

        $meta_lat = number_format( get_post_meta( $post->ID, "_adverts_mal_latitude", true ), "15", ".", "" );
        $meta_lng = number_format( get_post_meta( $post->ID, "_adverts_mal_longitude", true ), "15", ".", "" );

        $terms = wp_get_post_terms( $post->ID, "advert_location" );

        $term_lat = null;
        $term_lng = null;

        if( isset( $terms[0] ) ) {
            $term_lat = number_format( get_term_meta( $terms[0]->term_id, "_wpadverts_mal_geo_lat", true ), "15", ".", "" );
            $term_lng = number_format( get_term_meta( $terms[0]->term_id, "_wpadverts_mal_geo_lng", true ), "15", ".", "" );
        }

        $latlng = wpadverts_mal_get_latlng( $post->ID );

        if( $meta_lat && $meta_lng ) {
            $geo_type = "autocomplete";
            $geo_update = mal_sync_ajax_update( $post->ID, $meta_lat, $meta_lng, $latlng, "post" );
        } elseif( $term_lat && $term_lng ) {
            $geo_type = "dropdown";
            $geo_update = mal_sync_ajax_update( $post->ID, $term_lat, $term_lng, $latlng, "term" );
        } else {
            $geo_type = "none";
            $geo_update = mal_sync_ajax_delete( $post->ID );
        }

        $posts[] = array(
            "ID" => $post->ID,
            "post_title" => $post->post_title,
            "admin_url" => admin_url( sprintf( "post.php?post=%d&action=edit", $post->ID ) ),
            "update_type" => $geo_update["type"],
            "update_message" => $geo_update["message"]
        );
    }
    
    if( $completed ) {
        // cleanup DB
        $wpdb->query( "DELETE FROM {$wpdb->prefix}mal_latlng WHERE (SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_id` = `ID`) IS NULL" );
    }

    echo json_encode( array(
        "total" => $total,
        "offset" => $offset + $x,
        "completed" => $completed,
        "posts" => $posts
    ));

    exit;
}

function mal_sync_ajax_update( $post_id, $lat, $lng, $geo, $type ) {

    if( $geo === null ) {
        wpadverts_mal_save_latlng( $post_id, $lat, $lng );
        return array(
            "type" => "notice",
            "message" => sprintf( __( "INSERTED from %s meta.", "mal-sync" ), $type )
        );
    }

    if( $lat != $geo->lat || $lng != $geo->lng ) {
        wpadverts_mal_save_latlng( $post_id, $lat, $lng );
        return array(
            "type" => "notice",
            "message" => sprintf( __( "UPDATED from %s meta.", "mal-sync" ), $type )
        );
    }

    return array(
        "type" => "info",
        "message" => __( "OK.", "mal-sync" )
    );
}

function mal_sync_ajax_delete( $post_id, $lat, $lng, $geo ) {
    if( $geo === null ) {
        return array(
            "type" => "info",
            "message" => __( "NOT SET.", "mal-sync" )
        );
    } else {
        wpadverts_mal_delete_latlng( $post_id );
        return array(
            "type" => "info",
            "message" => __( "DELETED.", "mal-sync" )
        );
    }
}
