<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_wcps_metabox{
	
	public function __construct(){

		//meta box action for "wcps"
		add_action('add_meta_boxes', array($this, 'wcps_post_meta_wcps'));
		add_action('save_post', array($this, 'meta_boxes_wcps_save'), 99);



		}


	public function wcps_post_meta_wcps($post_type){

            add_meta_box('metabox-wcps',__('WCPS data', 'woocommerce-products-slider'), array($this, 'meta_box_wcps_data'), 'wcps', 'normal', 'high');
        add_meta_box('metabox-wcps-side',__('WCPS Help', 'woocommerce-products-slider'), array($this, 'meta_box_wcps_side'), 'wcps', 'side', 'low');

		}



    public function meta_box_wcps_side($post){

	    ?>
        <div class="plugin-help-search">
            <input type="search" value="" placeholder="Start typing">

            <ul>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=hEmggd6pDFw&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=9">Version 1 13 10 overview</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=kn3skEwh5t4&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=2">Data migration</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=lQuacOHKp5U&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=15">Custom thumbnail size</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">Customize layouts</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=UVa0kfo9oI4&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=3&t=4s">Query product by categories</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=qJWCizg5res&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=4&t=0s">Exclude featured products</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=d_KZg_cghow&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=5&t=0s">Exclude on sale products</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=HbpNaqrlppk&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=6&t=0s">Exclude out of stock products</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=Ss5wkHoyzFE&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=7&t=0s">Query product by tags</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=SSIfHT2UK0Y&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=9&t=0s">Display latest products</a></li>

                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=bWVvTFbSups&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=13&t=0s">Dokan vendors slider</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=dy68CuFe51w&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=11">Create categories slider</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=RLMXZmb_9_g&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=10">Create slider for customer orders</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=0XTVH09YuIo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=13">Easy digital downloads slider</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.pickplugins.com/documentation/woocommerce-products-slider/faq/how-to-activate-license/">How to activate license?</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.pickplugins.com/documentation/woocommerce-products-slider/faq/install-pro-version/">How to install pro version?</a></li>



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

        <?php

    }

	public function meta_box_wcps_data($post) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field('wcps_nonce_check', 'wcps_nonce_check_value');
 
        // Use get_post_meta to retrieve an existing value from the database.
       // $wcps_data = get_post_meta($post -> ID, 'wcps_data', true);

        $post_id = $post->ID;



        $settings_tabs_field = new settings_tabs_field();

        $wcps_options = get_post_meta($post_id,'wcps_options', true);
        $current_tab = isset($wcps_options['current_tab']) ? $wcps_options['current_tab'] : 'layouts';
        $slider_for = !empty($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';


        $wcps_settings_tab = array();

        $wcps_settings_tabs[] = array(
            'id' => 'shortcode',
            'title' => sprintf(__('%s Shortcode','woocommerce-products-slider'),'<i class="fas fa-laptop-code"></i>'),
            'priority' => 1,
            'active' => ($current_tab == 'shortcode') ? true : false,
        );

        $wcps_settings_tabs[] = array(
            'id' => 'slider_options',
            'title' => sprintf(__('%s Slider options','woocommerce-products-slider'),'<i class="fa fa-cogs"></i>'),
            'priority' => 2,
            'active' => ($current_tab == 'slider_options') ? true : false,
        );

        $wcps_settings_tabs[] = array(
            'id' => 'query_product',
            'title' => sprintf(__('%s Query product','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 3,
            'active' => ($current_tab == 'query_product') ? true : false,
            'data_visible' => 'products',
            'hidden' => (($slider_for == 'categories') ? true : false) || (($slider_for == 'orders') ? true : false) || (($slider_for == 'dokan_vendors') ? true : false) || (($slider_for == 'edd_downloads') ? true : false),
        );

        $wcps_settings_tabs[] = array(
            'id' => 'query_orders',
            'title' => sprintf(__('%s Query orders','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 3,
            'active' => ($current_tab == 'query_orders') ? true : false,
            'data_visible' => 'orders',
            'hidden' => (($slider_for == 'products') ? true : false) || (($slider_for == 'categories') ? true : false) || (($slider_for == 'dokan_vendors') ? true : false) || (($slider_for == 'edd_downloads') ? true : false),
        );

        $wcps_settings_tabs[] = array(
            'id' => 'query_categories',
            'title' => sprintf(__('%s Query categories','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 3,
            'active' => ($current_tab == 'query_categories') ? true : false,
            'data_visible' => 'categories',
            'hidden' => (($slider_for == 'products') ? true : false) || (($slider_for == 'orders') ? true : false) || (($slider_for == 'dokan_vendors') ? true : false) || (($slider_for == 'edd_downloads') ? true : false),
        );

        $wcps_settings_tabs[] = array(
            'id' => 'style',
            'title' => sprintf(__('%s Style','woocommerce-products-slider'),'<i class="fas fa-palette"></i>'),
            'priority' => 4,
            'active' => ($current_tab == 'style') ? true : false,
        );

        $wcps_settings_tabs[] = array(
            'id' => 'layouts',
            'title' => sprintf(__('%s Layouts','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
            'priority' => 5,
            'active' => ($current_tab == 'layouts') ? true : false,
        );


        $wcps_settings_tabs[] = array(
            'id' => 'custom_scripts',
            'title' => sprintf(__('%s Custom scripts','woocommerce-products-slider'),'<i class="far fa-file-code"></i>'),
            'priority' => 6,
            'active' => ($current_tab == 'custom_scripts') ? true : false,
        );

        $wcps_settings_tabs[] = array(
            'id' => 'help_support',
            'title' => sprintf(__('%s Help support','woocommerce-products-slider'),'<i class="fas fa-hands-helping"></i>'),
            'priority' => 80,
            'active' => ($current_tab == 'help_support') ? true : false,
        );

        $wcps_settings_tabs[] = array(
            'id' => 'buy_pro',
            'title' => sprintf(__('%s Buy pro','woocommerce-products-slider'),'<i class="fas fa-store"></i>'),
            'priority' => 90,
            'active' => ($current_tab == 'buy_pro') ? true : false,
        );

        $wcps_settings_tabs = apply_filters('wcps_metabox_navs', $wcps_settings_tabs);

        $tabs_sorted = array();

        if(!empty($wcps_settings_tabs))
        foreach ($wcps_settings_tabs as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
        array_multisort($tabs_sorted, SORT_ASC, $wcps_settings_tabs);



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
        <script>
            jQuery(document).ready(function($){
                $(document).on('click', '.settings-tabs input[name="wcps_options[slider_for]"]', function(){
                    var val = $(this).val();

                    console.log( val );

                    $('.settings-tabs .tab-navs li').each(function( index ) {
                        data_visible = $( this ).attr('data_visible');

                        if(typeof data_visible != 'undefined'){
                            //console.log('undefined '+ data_visible );

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
            <input class="current_tab" type="hidden" name="wcps_options[current_tab]" value="<?php echo $current_tab; ?>">
            <div class="view-types">

                <?php


                $args = array(
                    'id'		=> 'slider_for',
                    'parent'		=> 'wcps_options',
                    'title'		=> __('Slider for','woocommerce-products-slider'),
                    'details'	=> '',
                    'type'		=> 'radio',
                    'value'		=> $slider_for,
                    'default'		=> '',
                    'args'		=> apply_filters('wcps_slider_for_args', array('products' => 'Products','orders' => 'Orders', 'categories' => 'Categories' )),
                );

                $settings_tabs_field->generate_field($args);

                ?>
            </div>

            <ul class="tab-navs">
                <?php
                foreach ($wcps_settings_tabs as $tab){
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
            foreach ($wcps_settings_tabs as $tab){
                $id = $tab['id'];
                $title = $tab['title'];
                $active = $tab['active'];
                ?>

                <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                    <?php
                    do_action('wcps_metabox_content_'.$id, $post_id);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="clear clearfix"></div>

        <?php


   		}




	public function meta_boxes_wcps_save($post_id){

        /*
         * We need to verify this came from the our screen and with
         * proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['wcps_nonce_check_value']))
            return $post_id;

        $nonce = sanitize_text_field($_POST['wcps_nonce_check_value']);

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'wcps_nonce_check'))
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

        do_action('wcps_metabox_save', $post_id);


					
		}
	
	}


new class_wcps_metabox();