<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class post_grid_meta_boxs{
	
	public function __construct(){


		// meta box for post_grid
        add_action('add_meta_boxes', array($this, 'post_grid'));
        add_action('save_post', array($this, 'post_grid_save'));

        // Post options
        add_action('add_meta_boxes', array($this, 'post_options'));
        add_action('save_post', array($this, 'post_options_save'));


        //meta box for "post_grid_layout"
        add_action('add_meta_boxes', array($this, 'post_grid_layout'));
        add_action('save_post', array($this, 'post_grid_layout_save'));

		}


	public function post_grid_layout($post_type){

            add_meta_box('post-grid-layout',__('Layout data', 'post-grid'), array($this, 'post_grid_layout_display'), 'post_grid_layout', 'normal', 'high');
	}

    public function post_grid($post_type){

        add_meta_box('post-grid',__('Post Grid Options', 'post-grid'), array($this, 'post_grid_display'), 'post_grid', 'normal', 'high');
        add_meta_box('post-grid-side',__('Post Grid Info', 'post-grid'), array($this, 'post_grid_side'), 'post_grid', 'side', 'low');

    }

    public function post_options($post_type){

        $post_grid_settings = get_option('post_grid_settings');
        $post_options_post_types = isset($post_grid_settings['post_options_post_types']) ? $post_grid_settings['post_options_post_types'] : array('post');


        add_meta_box('post-grid-post-option',__('Post Grid - Post Options', 'post-grid'), array($this, 'post_options_display'), $post_options_post_types, 'normal', 'high');
    }





	public function post_grid_layout_display($post) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field('post_grid_nonce_check', 'post_grid_nonce_check_value');

        $post_id = $post->ID;


        $settings_tabs_field = new settings_tabs_field();

        $post_grid_settings_tab = array();

        $post_grid_settings_tab[] = array(
            'id' => 'layout_builder',
            'title' => sprintf(__('%s Layout editor','post-grid'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 4,
            'active' => true,
        );


        $post_grid_settings_tab[] = array(
            'id' => 'custom_scripts',
            'title' => sprintf(__('%s Custom scripts','post-grid'),'<i class="far fa-building"></i>'),
            'priority' => 5,
            'active' => false,
        );



        $post_grid_settings_tab = apply_filters('post_grid_layout_metabox_navs', $post_grid_settings_tab);

        $tabs_sorted = array();
        foreach ($post_grid_settings_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
        array_multisort($tabs_sorted, SORT_ASC, $post_grid_settings_tab);



        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');


        wp_enqueue_style( 'jquery-ui');
        wp_enqueue_style( 'font-awesome-5' );
        wp_enqueue_style( 'settings-tabs' );
        wp_enqueue_script( 'settings-tabs' );


		?>


        <div class="settings-tabs vertical">
            <ul class="tab-navs">
                <?php
                foreach ($post_grid_settings_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                    $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                    ?>
                    <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                    <?php
                }
                ?>
            </ul>
            <?php
            foreach ($post_grid_settings_tab as $tab){
                $id = $tab['id'];
                $title = $tab['title'];
                $active = $tab['active'];
                ?>

                <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                    <?php
                    do_action('post_grid_layout_metabox_content_'.$id, $post_id);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="clear clearfix"></div>

        <?php

   		}




	public function post_grid_layout_save($post_id){

        /*
         * We need to verify this came from the our screen and with
         * proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['post_grid_nonce_check_value']))
            return $post_id;

        $nonce = $_POST['post_grid_nonce_check_value'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'post_grid_nonce_check'))
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id))
                return $post_id;

        } else {

            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize the user input.
        //$grid_item_layout = stripslashes_deep($_POST['grid_item_layout']);


        // Update the meta field.
        //update_post_meta($post_id, 'grid_item_layout', $grid_item_layout);

        do_action('post_grid_layout_metabox_save', $post_id);


					
		}






    function post_grid_display( $post ) {

        global $post;
        wp_nonce_field( 'meta_boxes_post_grid_input', 'meta_boxes_post_grid_input_nonce' );

        $post_id = $post->ID;
        $post_grid_meta_options = get_post_meta($post_id, 'post_grid_meta_options', true);

        $grid_type =     $post_types = !empty($post_grid_meta_options['grid_type']) ? $post_grid_meta_options['grid_type'] : 'grid';

        $current_tab = isset($post_grid_meta_options['current_tab']) ? $post_grid_meta_options['current_tab'] : 'query_post';

        $settings_tabs_field =  new settings_tabs_field();

        $settings_tabs = array();



        $settings_tabs[] = array(
            'id' => 'shortcode',
            'title' => sprintf(__('%s Shortcode','post-grid'), '<i class="fas fa-laptop-code"></i>'),
            'priority' => 5,
            'active' => ($current_tab == 'shortcode') ? true : false,
        );

        $settings_tabs[] = array(
            'id' => 'general',
            'title' => sprintf(__('%s General','post-grid'), '<i class="fas fa-cogs"></i>'),
            'priority' => 10,
            'active' => ($current_tab == 'general') ? true : false,
        );

        $settings_tabs[] = array(
            'id' => 'query_post',
            'title' => sprintf(__('%s Query Post','post-grid'), '<i class="fas fa-cubes"></i>'),
            'priority' => 15,
            'active' => ($current_tab == 'query_post') ? true : false,
        );

//        $settings_tabs[] = array(
//            'id' => 'skin_layout',
//            'title' => sprintf(__('%s Skin & Layout (Old)','post-grid'), '<i class="fas fa-magic"></i>'),
//            'priority' => 20,
//            'active' => ($current_tab == 'skin_layout') ? true : false,
//        );

        $settings_tabs[] = array(
            'id' => 'layouts',
            'title' => sprintf(__('%s Layouts','post-grid'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 30,
            'active' => ($current_tab == 'layouts') ? true : false,
        );



        $settings_tabs[] = array(
            'id' => 'grid_settings',
            'title' => sprintf(__('%s Grid settings','post-grid'), '<i class="fas fa-th"></i>'),
            'priority' => 35,
            'active' => ($current_tab == 'grid_settings') ? true : false,
            'data_visible' => 'grid filterable',
            'hidden' => ($grid_type == 'grid')? false : true ,
        );

        $settings_tabs[] = array(
            'id' => 'item_style',
            'title' => sprintf(__('%s Item style','post-grid'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 38,
            'active' => ($current_tab == 'item_style') ? true : false,
        );



        $settings_tabs[] = array(
            'id' => 'masonry',
            'title' => sprintf(__('%s Masonry','post-grid'), '<i class="fas fa-th-large"></i>'),
            'priority' => 40,
            'active' => ($current_tab == 'masonry') ? true : false,
            'data_visible' => 'grid glossary timeline filterable',
            'hidden' => ($grid_type == 'slider')? true : false ,
        );

        $settings_tabs[] = array(
            'id' => 'pagination',
            'title' => sprintf(__('%s Pagination','post-grid'), '<i class="fas fa-pager"></i>'),
            'priority' => 45,
            'active' => ($current_tab == 'pagination') ? true : false,
            'data_visible' => ' grid glossary timeline filterable collapsible',
            'hidden' => ($grid_type == 'slider')? true : false ,
        );

        $settings_tabs[] = array(
            'id' => 'custom_scripts',
            'title' => sprintf(__('%s Custom Scripts','post-grid'), '<i class="fas fa-code"></i>'),
            'priority' => 50,
            'active' => ($current_tab == 'custom_scripts') ? true : false,
        );

        $settings_tabs[] = array(
            'id' => 'search',
            'title' => sprintf(__('%s Search','post-grid'), '<i class="fas fa-search"></i>'),
            'priority' => 55,
            'active' => ($current_tab == 'search') ? true : false,
        );

        $settings_tabs = apply_filters('post_grid_metabox_tabs', $settings_tabs);

        //var_dump($settings_tabs);


        $tabs_sorted = array();
        foreach ($settings_tabs as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
        array_multisort($tabs_sorted, SORT_ASC, $settings_tabs);


        ?>

        <div class="post-grid-meta-box">

            <script>
                jQuery(document).ready(function($){
                    $(document).on('click', '.settings-tabs input[name="post_grid_meta_options[grid_type]"]', function(){
                        var val = $(this).val();
                        console.log( val );
                        $('.settings-tabs .tab-navs li').each(function( index ) {
                            data_visible = $( this ).attr('data_visible');
                            if(typeof data_visible != 'undefined'){
                                n = data_visible.indexOf(val);
                                if(n<0){
                                    $( this ).hide();
                                }else{
                                    $( this ).show();
                                }
                            }else{
                                console.log('Not matched: '+ data_visible );
                            }
                        });
                    })
                })
            </script>

            <div class="settings-tabs vertical">
                <input class="current_tab" type="hidden" name="post_grid_meta_options[current_tab]" value="<?php echo $current_tab; ?>">

                <?php


                $args = array(
                    'id'		=> 'grid_type',
                    'parent'		=> 'post_grid_meta_options',
                    'title'		=> __('View Type','post-grid'),
                    'details'	=> '',
                    'type'		=> 'radio',
                    'value'		=> $grid_type,
                    'default'		=> '',
                    'args'		=> apply_filters('post_grid_view_types', array('grid' => 'Normal grid' )),
                );

                $settings_tabs_field->generate_field($args);

                ?>


                <ul class="tab-navs">
                    <?php
                    foreach ($settings_tabs as $tab){
                        $id = $tab['id'];
                        $title = $tab['title'];
                        $active = $tab['active'];
                        $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                        $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                        ?>
                        <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                foreach ($settings_tabs as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];


                    ?>

                    <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                        <?php
                        do_action('post_grid_metabox_tabs_content_'.$id, $tab, $post_id);
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="clear clearfix"></div>

        </div>










        <?php



    }


    function post_grid_save( $post_id ) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['meta_boxes_post_grid_input_nonce'] ) )
            return $post_id;

        $nonce = $_POST['meta_boxes_post_grid_input_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'meta_boxes_post_grid_input' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;



        /* OK, its safe for us to save the data now. */

        // Sanitize user input.
        //$post_grid_collapsible = sanitize_text_field( $_POST['post_grid_collapsible'] );


        $post_grid_meta_options = stripslashes_deep( $_POST['post_grid_meta_options'] );
        update_post_meta( $post_id, 'post_grid_meta_options', $post_grid_meta_options );





    }


    function post_grid_side( $post ) {

        ?>
        <div class="plugin-help-search">
            <input type="search" value="" placeholder="Start typing">

            <ul>

                <?php
                $class_post_grid_support = new class_post_grid_support();

                $video_tutorials =  $class_post_grid_support->video_tutorials();

                foreach($video_tutorials as $item){
                    $url = isset($item['url']) ?$item['url'] : '';
                    $title = isset($item['title']) ?$item['title'] : '';
                    $keywords = isset($item['keywords']) ? $item['keywords'] : '';

                    ?>
                    <li keywords="<?php echo $keywords; ?>" class="item">
                        <a target="_blank" href="<?php echo $url; ?>"><i class="far fa-dot-circle"></i> <?php echo $title; ?></a>

                    </li>
                    <?php

                }

                ?>


            </ul>
        </div>



        <style type="text/css">
            .plugin-help-search{}
            .plugin-help-search input[type=search]{
                width: 100%;
            }
        </style>

        <script>
            jQuery(document).ready(function($){
                jQuery(document).on('keyup', '.plugin-help-search input', function(){
                    keyword = jQuery(this).val().toLowerCase();
                    content_body = [];

                    console.log(keyword);

                    $('.plugin-help-search li').each(function( index ) {
                        $( this ).hide();
                        content = $( this ).text().toLowerCase();
                        content_body[index] = content;
                        n = content_body[index].indexOf(keyword);
                        if(n<0){
                            $( this ).hide();
                        }else{
                            $( this ).show();
                        }
                    });
                })
            })
        </script>




        <div class="post-grid-meta-box">






            <ul>
                <li>Post Grid Version: <?php echo post_grid_version; ?></li>
                <li>Tested WP: 5.4</li>

            </ul>

            <h3>Try Pro</h3>
            <a class="button" href="https://www.pickplugins.com/item/post-grid-create-awesome-grid-from-any-post-type-for-wordpress/?ref=dashboard" target="_blank">Buy Pro</a><p class="description">If you are looking some extra feature you may try our premium version.</p>

            <h3>Documentation</h3>
            <a class="button" href="https://www.pickplugins.com/documentation/post-grid/?ref=dashboard" target="_blank">Documentation</a><p class="description">Before asking, submitting reviews please take a look on our documentation, may help your issue fast.</p>

            <h3>Looking for support?</h3>
            <a class="button" href="https://www.pickplugins.com/forum/?ref=dashboard" target="_blank">Create Support Ticket</a><p class="description">Its free and you can ask any question about our plugins and get support fast.</p>

            <h3>Provide your feedback</h3>

            <a class="button" href="https://wordpress.org/support/plugin/post-grid/reviews/#new-post" target="_blank">Submit Reviews</a> <a class="button" href="https://wordpress.org/support/plugin/post-grid/#new-topic-0" target="_blank">Ask wordpress.org</a><p>We spent thousand+ hours to development on this plugin, please submit your reviews wisely.</p><p>If you have any issue with this plugin please submit our forums or contact our support first.</p><p class="description">Your feedback and reviews are most important things to keep our development on track. If you have time please submit us five star <a href="https://wordpress.org/support/plugin/post-grid/reviews/"><span style="color: orange"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span></a> reviews.</p>


        </div>
        <?php

    }



    function post_options_display( $post ) {

        global $post;
        wp_nonce_field( 'post_grid_post_settings_input', 'post_grid_post_settings_input_nonce' );

        $post_id = $post->ID;
        $post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);


        $post_grid_settings_tab = array();
        $current_tab = isset($post_grid_post_settings['current_tab']) ? $post_grid_post_settings['current_tab'] : 'options';


        $post_grid_settings_tab[] = array(
            'id' => 'options',
            'title' => sprintf(__('%s Options','post-grid'), '<i class="fas fas fa-tools"></i>'),
            'priority' => 1,
            'active' => ($current_tab == 'options') ? true : false,
        );


        $post_grid_settings_tabs = apply_filters('post_grid_post_options_tabs', $post_grid_settings_tab);


        $tabs_sorted = array();
        foreach ($post_grid_settings_tabs as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
        array_multisort($tabs_sorted, SORT_ASC, $post_grid_settings_tabs);

        $settings_tabs_field = new settings_tabs_field();
        $settings_tabs_field->admin_scripts();

        ?>

        <div class="settings-tabs vertical">
            <input class="current_tab" type="hidden" name="post_grid_post_settings[current_tab]" value="<?php echo $current_tab; ?>">

            <ul class="tab-navs">
                <?php
                foreach ($post_grid_settings_tabs as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                    $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                    ?>
                    <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
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
                    do_action('post_grid_post_options_content_'.$id, $tab, $post_id);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="clear clearfix"></div>


        <?php

    }


    function post_options_save( $post_id ) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['post_grid_post_settings_input_nonce'] ) )
            return $post_id;

        $nonce = $_POST['post_grid_post_settings_input_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'post_grid_post_settings_input' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        /* OK, its safe for us to save the data now. */

        // Sanitize user input.
        $post_grid_post_settings = stripslashes_deep( $_POST['post_grid_post_settings'] );
        update_post_meta( $post_id, 'post_grid_post_settings', $post_grid_post_settings );


    }


}


new post_grid_meta_boxs();