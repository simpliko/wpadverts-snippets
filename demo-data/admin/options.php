<div class="wrap">
    <h1><?php _e("Import WPAdverts Demo Data", "wpadverts-snippet-demo-data") ?></h1>

    <?php if( $show == "welcome" ): ?>
    <p>
        <?php _e("This import tool will add the following data to your database:", "wpadverts-snippet-demo-data" ) ?>
        <ul>
            <li><?php _e("10 classified ads (each with 2 images uploaded to the Media Library", "wpadverts-snippet-demo-data") ?></li>
            <li><?php _e("72 categories", "wpadverts-snippet-demo-data") ?></li>
        </ul> 
    </p>

    <p>
        <?php _e("The import should take no longer than 30 seconds to complete.", "wpadverts-snippet-demo-data") ?>
    </p>

	<p>
        <form action="" method="post">
            <input type="hidden" name="wpadverts-snp-demo-data-start", value="1" />
            <input type="hidden" name="wpadverts-snp-demo-data-nonce", value="<?php echo $nonce ?>" />
            <input type="submit" class="button-primary" value="<?php _e("Import data now","wpadverts-snippet-demo-data") ?>" />
        </form>
    </p>



    <?php elseif( $show == 1 ): ?>
        <p>
            <?php _e('<strong>Success!</strong> All data was imported correctly.', 'wpadverts-snippet-demo-data'); ?>
        </p>

        <p>
            <?php 
                echo sprintf(
                    __('Manage your <a href="%s">Classifieds</a> and <a href="%s">Categories</a>.', 'wpadverts-snippet-demo-data'),
                    admin_url('edit.php?post_type=advert'),
                    admin_url('edit-tags.php?taxonomy=advert_category&post_type=advert')
                );
            ?>
        </p>
    <?php else: ?>
        <p>
            <?php _('<strong>Ooops!</strong> Some or all data could not be imported into the database, please check your server error_log file for problems.', 'wpadverts-snippet-demo-data' ) ?>
        </p>
    <?php endif; ?>
</div>