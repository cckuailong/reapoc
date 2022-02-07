<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access

wp_enqueue_style( 'post-grid-addons' );


$class_post_grid_functions = new class_post_grid_functions();
$addons_list = $class_post_grid_functions->addons_list();

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s - Extensions', 'post-grid'), post_grid_plugin_name)?></h2>


    <div class="addon-list">

        <?php

        if(!empty($addons_list)):
            foreach ($addons_list as $addon):
                $addon_title = isset($addon['title']) ? $addon['title'] : '';
                $item_link = isset($addon['item_link']) ? $addon['item_link'] : '';
                $addon_thumb = isset($addon['thumb']) ? $addon['thumb'] : '';
                $zip_link = isset($addon['zip_link']) ? $addon['zip_link'] : '';
                $wp_org_slug = isset($addon['wp_org_slug']) ? $addon['wp_org_slug'] : '';


                ?>
                <div class="item">
                    <div class="thumb-wrap">
                        <a href="<?php echo $item_link;?>"><img src="<?php echo $addon_thumb;?>"></a>
                    </div>
                    <div class="addon-title"><a class="addon-link" href="<?php echo $item_link;?>"><?php echo $addon_title;?></a></div>
                    <?php if(!empty($zip_link)): ?>
                        <div class="addon-link button"><a href="<?php echo $zip_link;?>">Download</a> </div>
                    <?php endif; ?>


                </div>
            <?php
            endforeach;
        endif;

        ?>


    </div>


</div>