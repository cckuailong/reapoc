<?php	


/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access



$post_grid_settings_tab = array();

$post_grid_settings_tab[] = array(
    'id' => 'general',
    'title' => __('<i class="fas fa-laptop-code"></i> General','post-grid'),
    'priority' => 1,
    'active' => true,
);







$post_grid_settings_tabs = apply_filters('post_grid_settings', $post_grid_settings_tab);


$tabs_sorted = array();
foreach ($post_grid_settings_tabs as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
array_multisort($tabs_sorted, SORT_ASC, $post_grid_settings_tabs);













?>





<div class="wrap">


    <h2><?php echo __('Post Grid - Settings','post-grid')?></h2><br>

    <?php

    if(empty($_POST['post_grid_hidden'])) {

        $post_grid_license = get_option('post_grid_license');
        $license_key = isset($post_grid_license['license_key']) ? $post_grid_license['license_key'] : '';


    }
    else{

        $nonce = sanitize_text_field($_POST['_wpnonce']);

        if(wp_verify_nonce( $nonce, 'post_grid_nonce' ) && $_POST['post_grid_hidden'] == 'Y') {

            $license_key = sanitize_text_field($_POST['license_key']);
            $post_grid_license = array(
                'license_key'=>$license_key,
                'license_status'=>'pending',

            );

            update_option('post_grid_license', $post_grid_license);





            ?>
            <div class="updated notice  is-dismissible"><p><strong><?php _e('Changes Saved.', 'post-grid' ); ?></strong></p></div>

            <?php
        }
    }



    //var_dump($post_grid_1);

    ?>


    <form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="post_grid_hidden" value="Y">


        <div class="clear clearfix"></div>
        <div class="settings-tabs vertical">
            <ul class="tab-navs">
                <?php
                foreach ($post_grid_settings_tabs as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                    $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                    ?>
                    <li class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                    <?php
                }
                ?>
            </ul>
            <?php
            foreach ($post_grid_settings_tabs as $tab){
                $id = $tab['id'];
                $title = $tab['title'];
                $active = $tab['active'];


                ?>

                <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                    <?php
                    do_action('post_grid_settings_'.$id, $tab);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="clear clearfix"></div>



        <p class="submit">
            <?php wp_nonce_field( 'post_grid_nonce' ); ?>
            <input type="submit" name="submit" value="<?php _e('Update', 'post-grid'); ?>" class="button-primary" />

        </p>
    </form>





















</div>
