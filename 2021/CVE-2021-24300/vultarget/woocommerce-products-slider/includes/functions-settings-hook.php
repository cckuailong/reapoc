<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_action('wcps_settings_content_general', 'wcps_settings_content_general');

function wcps_settings_content_general(){
    $settings_tabs_field = new settings_tabs_field();

    $wcps_settings = get_option('wcps_settings');

    $font_aw_version = isset($wcps_settings['font_aw_version']) ? $wcps_settings['font_aw_version'] : 'none';
    $wcps_preview = isset($wcps_settings['wcps_preview']) ? $wcps_settings['wcps_preview'] : 'yes';

    //echo '<pre>'.var_export($wcps_settings, true).'</pre>';

    ?>
    <div class="section">
        <div class="section-title"><?php echo __('General', 'woocommerce-products-slider'); ?></div>
        <p class="description section-description"><?php echo __('Choose some general options.', 'woocommerce-products-slider'); ?></p>

        <?php



        $args = array(
            'id'		=> 'font_aw_version',
            'parent'		=> 'wcps_settings',
            'title'		=> __('Font-awesome version','woocommerce-products-slider'),
            'details'	=> __('Choose font awesome version you want to load.','woocommerce-products-slider'),
            'type'		=> 'select',
            'value'		=> $font_aw_version,
            'default'		=> '',
            'args'		=> array('v_5'=>__('Version 5+','woocommerce-products-slider'), 'v_4'=>__('Version 4+','woocommerce-products-slider'), 'none'=>__('None','woocommerce-products-slider')  ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'wcps_preview',
            'parent'		=> 'wcps_settings',
            'title'		=> __('Enable WCPS preview','woocommerce-products-slider'),
            'details'	=> __('You can enable preview WCPS.','woocommerce-products-slider'),
            'type'		=> 'select',
            'value'		=> $wcps_preview,
            'default'		=> 'yes',
            'args'		=> array('yes'=>__('Yes','woocommerce-products-slider'), 'no'=>__('No','woocommerce-products-slider')  ),
        );

        $settings_tabs_field->generate_field($args);





        ?>

    </div>

    <?php





}


add_action('wcps_settings_content_help_support', 'wcps_settings_content_help_support');

if(!function_exists('wcps_settings_content_help_support')) {
    function wcps_settings_content_help_support($tab){

        $settings_tabs_field = new settings_tabs_field();

        ?>
        <div class="section">
            <div class="section-title"><?php echo __('Get support', 'woocommerce-products-slider'); ?></div>
            <p class="description section-description"><?php echo __('Use following to get help and support from our expert team.', 'woocommerce-products-slider'); ?></p>

            <?php


            ob_start();
            ?>

            <p><?php echo __('Ask question for free on our forum and get quick reply from our expert team members.', 'woocommerce-products-slider'); ?></p>
            <a class="button" href="https://www.pickplugins.com/create-support-ticket/"><?php echo __('Create support ticket', 'woocommerce-products-slider'); ?></a>

            <p><?php echo __('Read our documentation before asking your question.', 'woocommerce-products-slider'); ?></p>
            <a class="button" href="https://www.pickplugins.com/documentation/woocommerce-products-slider/"><?php echo __('Documentation', 'woocommerce-products-slider'); ?></a>

            <p><?php echo __('Watch video tutorials.', 'woocommerce-products-slider'); ?></p>
            <a class="button" href="https://www.youtube.com/playlist?list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f"><i class="fab fa-youtube"></i> <?php echo __('All tutorials', 'woocommerce-products-slider'); ?></a>

            <ul>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=kn3skEwh5t4&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=2">Data migration</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">Customize Layouts</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=UVa0kfo9oI4&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=3&t=4s">Query product by categories</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=qJWCizg5res&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=4&t=0s">Exclude featured products</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=d_KZg_cghow&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=5&t=0s">Exclude on sale products</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=HbpNaqrlppk&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=6&t=0s">Exclude out of stock products</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=Ss5wkHoyzFE&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=7&t=0s">Query product by tags</a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=SSIfHT2UK0Y&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=9&t=0s">Display latest products</a></li>
            </ul>



            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'get_support',
                //'parent'		=> '',
                'title'		=> __('Ask question','woocommerce-products-slider'),
                'details'	=> '',
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>

            <p class="">We wish your 2 minutes to write your feedback about the <b>PickPlugins Product Slider</b> plugin. give us <span style="color: #ffae19"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span></p>

            <a target="_blank" href="https://wordpress.org/plugins/woocommerce-products-slider/#reviews" class="button"><i class="fab fa-wordpress"></i> Write a review</a>


            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'reviews',
                //'parent'		=> '',
                'title'		=> __('Submit reviews','woocommerce-products-slider'),
                'details'	=> '',
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            $wcps_plugin_info = get_option('wcps_plugin_info');

            //delete_option('wcps_plugin_info');
            //var_dump($wcps_plugin_info);

            $migration_reset_stats = isset($wcps_plugin_info['migration_reset']) ? $wcps_plugin_info['migration_reset'] : '';


            $actionurl = admin_url().'edit.php?post_type=wcps&page=settings&tab=help_support';
            $actionurl = wp_nonce_url( $actionurl,  'wcps_reset_migration' );

            $nonce = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';

            if ( wp_verify_nonce( $nonce, 'wcps_reset_migration' )  ){

                $wcps_plugin_info['migration_reset'] = 'processing';
                update_option('wcps_plugin_info', $wcps_plugin_info);

                wp_schedule_event(time(), '1minute', 'wcps_cron_reset_migrate');


                $migration_reset_stats = 'processing';
            }

            if($migration_reset_stats == 'processing'){

                $url = admin_url().'edit.php?post_type=wcps&page=settings&tab=help_support';

                ?>
                <p style="color: #f00;"><i class="fas fa-spin fa-spinner"></i> Migration reset on process, please wait until complete.</p>
                <p><a href="<?php echo $url; ?>">Refresh</a> to check Migration reset stats</p>

                <script>
                    setTimeout(function(){
                        window.location.href = '<?php echo $url; ?>';
                    }, 1000*30);

                </script>


                <?php
            }elseif($migration_reset_stats == 'done'){
                ?>
                <p style="color: #22631a;font-weight: bold;"><i class="fas fa-check"></i> Migration reset completed.</p>
                <?php
            }else{

            }



            ?>

            <p class="">Please click the button bellow to reset migration data, you can start over, Please use with caution, your new migrate data will deleted. you can use default <a href="<?php echo admin_url().'export.php'; ?>">export</a> menu to take your wcps, wcps layouts data saved.</p>

            <p class="reset-migration"><a class="button  button-primary" href="<?php echo $actionurl; ?>">Reset migration</a> <span style="display: none; color: #f2433f; margin: 0 5px"> Click again to confirm!</span></p>

            <script>
                jQuery(document).ready(function($){
                    $(document).on('click','.reset-migration a',function(event){

                        event.preventDefault();

                        is_confirm = $(this).attr('confirm');
                        url = $(this).attr('href');

                        if(is_confirm == 'ok'){
                            window.location.href = url;
                        }else{
                            $(this).attr('confirm', 'ok');


                        }
                        $('.reset-migration span').fadeIn();

                    })
                })
            </script>

            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'reset_migrate',
                //'parent'		=> '',
                'title'		=> __('Reset migration','woocommerce-products-slider'),
                'details'	=> '',
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);










            ?>


        </div>
        <?php


    }
}






add_action('wcps_settings_content_buy_pro', 'wcps_settings_content_buy_pro');

if(!function_exists('wcps_settings_content_buy_pro')) {
    function wcps_settings_content_buy_pro($tab){

        $settings_tabs_field = new settings_tabs_field();


        ?>
        <div class="section">
            <div class="section-title"><?php echo __('Get Premium', 'woocommerce-products-slider'); ?></div>
            <p class="description section-description"><?php echo __('Thanks for using our plugin, if you looking for some advance feature please buy premium version.', 'woocommerce-products-slider'); ?></p>

            <?php


            ob_start();
            ?>

            <p><?php echo __('If you love our plugin and want more feature please consider to buy pro version.', 'woocommerce-products-slider'); ?></p>
            <a class="button" href="https://www.pickplugins.com/item/woocommerce-products-slider-for-wordpress/?ref=dashobard"><?php echo __('Buy premium', 'woocommerce-products-slider'); ?></a>
            <a class="button" href="https://www.pickplugins.com/demo/woocommerce-products-slider/?ref=dashobard"><?php echo __('See all demo', 'woocommerce-products-slider'); ?></a>

            <h2><?php echo __('See the differences','woocommerce-products-slider'); ?></h2>

            <table class="pro-features">
                <thead>
                <tr>
                    <th class="col-features"><?php echo __('Features','woocommerce-products-slider'); ?></th>
                    <th class="col-free"><?php echo __('Free','woocommerce-products-slider'); ?></th>
                    <th class="col-pro"><?php echo __('Premium','woocommerce-products-slider'); ?></th>
                </tr>
                </thead>

                <tr>
                    <td class="col-features"><?php echo __('Query by product taxonomies','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Query by recently viewed products','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Query by SKU','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Related product query(Single product page)','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Upsells cross-sells query(Single product page)','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Order by Best selling','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Order by Top rated','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Advance meta fields query','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Featured products at first','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Query by product attributes','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Stock status','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Stock quantity','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Product Weight','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Layout element - Product Dimensions','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Layout element - Share button','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Recently viewed text','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Meta fields','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Layout element - Price','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Rating','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Add to cart','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - On sale icon','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Featured icon','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Sale Count','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Layout element - Product Tag','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Layout element - Product Category','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Layout element - Content/Excerpt','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Product Title','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Layout element - Thumbnail','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Layout element - Wrapper start','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Layout element - Wrapper end','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Layout builder','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider column count','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider autoplay','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider rewind & loop','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Slider stop on hover','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider center','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider navigations','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider dots','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider RTL','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider lazy load','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider touch & mouse drag','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Query products limit','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Query by product categories & tags','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Hide out of stock products','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Featured product','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('On-sale products','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Product by ids','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Query order & orderby','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Custom ribbons','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Slider item style','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Slider container style','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td colspan="3" class="col-features"><?php echo __('3rd party','woocommerce-products-slider'); ?> </td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('YITH WooCommerce Badge Management','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('YITH WooCommerce Quick View','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('YITH WooCommerce Wishlist','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('YITH WooCommerce Brands','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('YITH WooCommerce Compare','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('WooCommerce Wholesale Prices','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('TI WooCommerce Wishlist','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('WPC Countdown Timer','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('WPC Smart Compare','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('WPC Smart Quick View','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('WPC Smart Wishlist','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Wishlist by PickPlugins','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Wish List for WooCommerce','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('WooCommerce Wholesale Prices','woocommerce-products-slider'); ?> </td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>



                <tr>
                    <th class="col-features"><?php echo __('Features','woocommerce-products-slider'); ?></th>
                    <th class="col-free"><?php echo __('Free','woocommerce-products-slider'); ?></th>
                    <th class="col-pro"><?php echo __('Premium','woocommerce-products-slider'); ?></th>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Buy now','woocommerce-products-slider'); ?></td>
                    <td> </td>
                    <td><a class="button" href="https://www.pickplugins.com/item/woocommerce-products-slider-for-wordpress/?ref=dashobard"><?php echo __('Buy premium', 'woocommerce-products-slider'); ?></a></td>
                </tr>

            </table>



            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'get_pro',
                'title'		=> __('Get pro version','woocommerce-products-slider'),
                'details'	=> '',
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>


        </div>

        <style type="text/css">
            .pro-features{
                margin: 30px 0;
                border-collapse: collapse;
                border: 1px solid #ddd;
            }
            .pro-features th{
                width: 120px;
                background: #ddd;
                padding: 10px;
            }
            .pro-features tr{
            }
            .pro-features td{
                border-bottom: 1px solid #ddd;
                padding: 10px 10px;
                text-align: center;
            }
            .pro-features .col-features{
                width: 230px;
                text-align: left;
            }

            .pro-features .col-free{
            }
            .pro-features .col-pro{
            }

            .pro-features i.fas.fa-check {
                color: #139e3e;
                font-size: 16px;
            }
            .pro-features i.fas.fa-times {
                color: #f00;
                font-size: 17px;
            }
        </style>
        <?php


    }
}









add_action('wcps_settings_save', 'wcps_settings_save');

function wcps_settings_save(){

    $wcps_settings = isset($_POST['wcps_settings']) ?  stripslashes_deep($_POST['wcps_settings']) : array();
    update_option('wcps_settings', $wcps_settings);
}
