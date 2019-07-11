<?php
/**
Plugin Name: WPAdverts Snippets - Related Ads Carousel
Version: 1.0
Author: Greg Winiarski
Description: Shows a list of related Ads on the Ad details page.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_action( "adverts_tpl_single_bottom", "related_ads_carousel_tpl_single_bottom", 9000 );

/**
 * Displays "Related Ads" on the Ad details page.
 * 
 * This function is executed by adverts_tpl_single_bottom filter.
 * 
 * @param int $post_id  Post ID
 * @return void
 */
function related_ads_carousel_tpl_single_bottom( $post_id ) {
    
    $postcat = get_the_category( $post_id );
    $terms = array();
    foreach( $postcat as $cat ) {
        $terms[] = $cat->term_id;
    }
    
    $args = array(
        'post__not_in' => array( $post_id ),
        'post_type' => 'advert', 
        'post_status' => 'publish',
        'posts_per_page' => 10, 
        'paged' => -1,
        '_tax_query' => array(
            array(
                'taxonomy' => 'advert_category',
                'field'    => 'term_id',
                'terms'    => $terms,
            ),
	),
    );
    
    $loop = new WP_Query( $args );
    echo '<h3>' . __( "Related Ads") . '</h3>';


    
    
    ?>
    <div class="wpadverts-slick-carousel">
        <?php foreach( $loop->posts as $post ): ?>
        <a href="<?php echo esc_attr( get_permalink( $post->ID ) ) ?>">
            
            
            <?php $price = get_post_meta( $post->ID, "adverts_price", true ) ?>
            <?php if( $price ): ?>
            <span class=""><?php echo esc_html( adverts_get_the_price( get_the_ID(), $price ) ) ?></span>
            <?php elseif( adverts_config( 'empty_price' ) ): ?>
            <span class=""><?php echo esc_html( adverts_empty_price( get_the_ID() ) ) ?></span>
            <?php endif; ?>
            
            <?php $image_id = adverts_get_main_image_id( $post->ID ) ?>
            <div class="advert-img">
                <?php if($image_id): ?>
                    <?php $image = get_post( $image_id ) ?>
                    <img src="<?php echo esc_attr( adverts_get_main_image( $post->ID ) ) ?>" class="advert-item-grow" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
                <?php endif; ?>
            </div>
            
        </a>
        <?php endforeach; ?>
    </div>
    <?php
    
    add_action( "wp_footer", "related_ads_carousel_footer", 2000 );
}

function related_ads_carousel_footer() {
    ?>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('.wpadverts-slick-carousel').slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4
      });
    });
    </script>
    <style type="text/css">
        
    </style>
    <?php
}
