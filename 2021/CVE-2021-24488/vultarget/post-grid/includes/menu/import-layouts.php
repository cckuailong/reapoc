<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

if(!current_user_can('manage_options')) return;

$keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';
$paged = isset($_GET['paged']) ? sanitize_text_field($_GET['paged']) : '';
$tabs = isset($_GET['tabs']) ? sanitize_text_field($_GET['tabs']) : 'latest';

$post_grid_settings = get_option('post_grid_license');
$license_key = isset($post_grid_settings['license_key']) ? $post_grid_settings['license_key'] : '';

$max_num_pages = 0;

wp_enqueue_script('post_grid_layouts');


//var_dump($_SERVER);

?>
<div class="wrap">
    <h2><?php _e('Post Grid - Layouts library', 'post-grid'); ?></h2>

    <div class="wpblockhub-search">

        <div class="wp-filter">
            <ul class="filter-links">
                <li class=""><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&tabs=latest" class="<?php if($tabs == 'latest') echo 'current'; ?>" aria-current="page"><?php _e('Latest', 'post-grid'); ?></a> </li>
                <li class=""><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&tabs=free" class="<?php if($tabs == 'free') echo 'current'; ?>" aria-current="page"><?php _e('Free', 'post-grid'); ?></a> </li>
                <li class=""><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&tabs=pro" class="<?php if($tabs == 'pro') echo 'current'; ?>" aria-current="page"><?php _e('Premium', 'post-grid'); ?></a> </li>
            </ul>
            <form class="block-search-form">
                <span class="loading"></span>
                <input id="block-keyword" type="search" placeholder="<?php _e('Start typing...', 'wp-block-hub'); ?>"
                       value="<?php echo $keyword; ?>">
            </form>
        </div>

        <?php

        $api_params = array(
            'post_grid_remote_action' => 'layoutSearch',
            'keyword' => $keyword,
            'paged' => $paged,
            'tabs' => $tabs,
        );

        // Send query to the license manager server
        $response = wp_remote_get(add_query_arg($api_params, post_grid_server_url), array('timeout' => 20, 'sslverify' => false));


        //echo '<pre>'.var_export($response, true).'</pre>';

        /*
         * Check is there any server error occurred
         *
         * */
        if (is_wp_error($response)){

            ?>
            <div class="return-empty">
                <ul>
                    <li><?php echo __("Unexpected Error! The query returned with an error.", 'post-grid'); ?></li>
                    <li><?php echo __("Make sure your internet connection is up.", 'post-grid'); ?></li>
                </ul>
            </div>
            <?php


        }
        else{

            $response_data = json_decode(wp_remote_retrieve_body($response));
            $post_data = isset($response_data->posts) ? $response_data->posts : array();
            $post_found = isset($response_data->post_found) ? sanitize_text_field($response_data->post_found) : array();
            $max_num_pages = isset($response_data->max_num_pages) ? sanitize_text_field($response_data->max_num_pages) : 0;

            //echo '<pre>'.var_export($response_data, true).'</pre>';
            //var_dump($response_data->ajax_nonce);
        }

        ?>

        <div class="block-list-items">
            <?php

            if(!empty($post_data)):

                foreach ($post_data as $item_index=>$item):


                    //var_dump($item);

                    $post_id      = isset($item->post_id) ? $item->post_id : '';
                    $block_title        = isset($item->title) ? $item->title : __('No title', 'post-grid');
                    $post_url           = isset($item->post_url) ? $item->post_url : '';
                    $download_count           = isset($item->download_count) ? $item->download_count : 0;

                    $layout_options           = isset($item->layout_options) ? unserialize($item->layout_options) : '';
                    $is_pro           = isset($item->is_pro) ? $item->is_pro : '';

                    $layout_preview_img           = isset($layout_options['layout_preview_img']) ? $layout_options['layout_preview_img'] : '';


                    //echo '<pre>'.var_export($is_pro, true).'</pre>';


                    ?>

                    <div class="item">
                        <div class="item-top-area">

                            <?php if(!empty($layout_preview_img)):?>
                                <div class="block-thumb">
                                    <img src="<?php echo $layout_preview_img; ?>">
                                </div>
                            <?php endif; ?>


                            <div class="block-content">
                                <div class="block-name"><?php echo $block_title; ?></div>

                            </div>
                            <div class="actions">

                                <?php
                                if ($is_pro == 'yes' && empty($license_key)) {

                                }else{
                                    ?>
                                    <span class="button  import-layout"  post_id="<?php echo $post_id; ?>"><i class="fas fa-download"></i> Import (<?php echo $download_count; ?>)</span>
                                    <?php
                                }

                                ?>


                                <?php if($is_pro == 'yes'): ?>
                                    <span title="Enter license key to import" class="is_pro button"><i class="fas fa-crown"></i> Pro</span>
                                <?php else: ?>
                                    <span class="is_free button"><i class="far fa-lightbulb"></i> Free</span>
                                <?php endif; ?>



                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                <?php
                endforeach;

            else:

                echo 'Server return empty. please try again later.';
            endif;

            ?>



        </div>

        <div class="paginate">
            <?php


            $big = 999999999; // need an unlikely integer
            //$max_num_pages = 4;


            //var_dump(get_pagenum_link( $big ));

            echo paginate_links(
                array(
                    'base' => preg_replace('/\?.*/', '', get_pagenum_link()) . '%_%',
                    //'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, $paged ),
                    'total' => $max_num_pages ,
                    'prev_text'          => '« Previous',
                    'next_text'          => 'Next »',



                ));
            ?>
        </div>



    </div>

</div>

<script>
    jQuery(document).ready(function($) {

        var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();


        $(document).on('keyup','#block-keyword',function(){
            _this = this;
            keyword = $(this).val();

            url = window.location.href
            //console.log();
            var url = new URL(url);


            delay(function(){
                $(_this).parent().children('.loading').addClass('button updating-message');

                url.searchParams.append('keyword', keyword);
                url.searchParams.delete('paged');
                window.location.href = url.href;

            }, 1000 );


        })
    })
</script>

<style type="text/css">

    .block-search-form{
        float: right;
        padding: 10px;
    }
    .block-search-form input[type="search"]{
        width: 225px;
        padding: 0 10px;
    }
    .block-list-items{}
    .block-list-items a{ text-decoration: none}
    .block-list-items .item{
        display: inline-block;
        vertical-align: top;
        width: 18%;
        background: #fff;
        margin: 10px;
    }

    @media (max-width: 1199.98px) {

        .block-list-items .item{
            width: 46%;

        }
    }

    @media (max-width: 767.98px) {

        .block-list-items .item{
            width: 46%;

        }

    }

    @media (max-width: 575.98px) {
        .block-list-items .item{
            width: 95%;

        }
    }





    .block-list-items .item-top-area{}
    .block-list-items .block-thumb{
        /* float: left; */
        overflow: hidden;
        /* margin-right: 15px; */
        height: 280px;
        border-bottom: 1px solid #ddd;
    }
    .block-list-items .block-thumb img{
        width: 100%;
    }

    .block-list-items .block-name{
        font-weight: 600;
        font-size: 18px;
    }
    .block-list-items .block-content{
        padding: 15px;
    }
    .item .actions{
        margin: 10px;
    }

    .item .is_pro{
        background: #3f51b5;
        color: #fff;
    }
    .item .is_free{
        background: #449862;
        color: #fff;
    }

    .block-save{}
    .block-save.saved{
        color: #00a04f;
    }

    .block-save span{
        line-height: normal;
        display: inline-block;
    }

    .block-list-items .demo-wrap{}
    .block-list-items .block-action{
        float: right;
        display: inline-block;
        padding: 15px;
        text-align: right;
    }
    .plugin-required{}
    .plugin-required a{
        text-decoration: none;
    }


    .plugin-required .installed {
        color: #00a04f;
    }

    .plugin-required .not-installed {
        color: #e02102;
    }



    .block-list-items .item-bottom-area{
        padding: 10px;
        background: #f7f7f7;
        border-top: 1px solid #ddd;
    }
    .item-bottom-area .col-left{
        width: 49%;
        display: inline-block;
        vertical-align: top;
    }
    .item-bottom-area .col-right{
        width: 49%;
        display: inline-block;
        text-align: right;
    }
    .item-bottom-area .col-left .star-rate{
        margin-bottom: 10px;
    }
    .item-bottom-area .col-left .star-rate .dashicons{
        color: #ffb900;
    }
    .item-bottom-area .col-left .download-count{}

    .item-bottom-area .col-right .author-link{
        margin-bottom: 10px;
    }



    .paginate{
        text-align: center;
        margin: 40px;
    }

    .paginate .page-numbers{
        background: #f7f7f7;
        padding: 10px 15px;
        margin: 5px;
        text-decoration: none;
    }
    .paginate .page-numbers.current{
        background: #e4e4e4;
    }











    /*wpblockhub-import-container*/

    .wpblockhub-import-container{
        position: relative;
    }
    .wpblockhub-import-btn{}
    .wpblockhub-import-container button {
        background: #3f51b5;
        color: #fff;
    }
    .wpblockhub-import-container .item-list-wrap.active{
        display: block;
    }

    .item-list-wrap{
        position: absolute;
        width: 300px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 10px;
        box-shadow: 0px 4px 6px 0px rgba(210, 210, 210, 0.4);
        left: -188px;
        max-height: 400px;
        overflow: hidden;
        overflow-y: scroll;
        margin-top: 9px;
        display: none;
    }





    .item-list-wrap .item {
        position: relative;
        transition: ease all 1s;
    }

    .item-list-wrap .item img{}
    .item-list-wrap .item:hover img{}

    /*.item-list-wrap .item img:before {*/
    /*    transition: ease all 1s;*/
    /*    content: "";*/
    /*    width: 100%;*/
    /*    height: 100%;*/
    /*    background: #1d1d1d78;*/
    /*    position: absolute;*/
    /*    top: 0;*/
    /*    left: 0;*/
    /*    display: none;*/
    /*    transform: scale(.5);*/
    /*}*/

    /*.item-list-wrap .item:hover img:before {*/

    /*    display: block;*/
    /*    transform: scale(1);*/
    /*}*/

    .item-list-wrap .item .item-import {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: none;
        z-index: 99999;
    }

    .item-list-wrap .item:hover .item-import{

        display: block;
    }

    .item-list-wrap .item img{
        transition: ease all 1s;
    }
    .item-list-wrap .item:hover img{
        opacity: 0.3;
    }

    .item-list-wrap .item.loading{}
    .item-list-wrap .item.loading:before {
        content: "Loading...";
    }
    .item-list-wrap .categories{
        width: 100%;
        margin-bottom: 10px;
    }
    .item-list-wrap .keyword, .item-list-wrap .loading{
        width: 100%;
    }



    .item-list-wrap .load-more{
        width: 100%;
        text-align: center;
    }


    .item-list-wrap .plugins-required{}
    .item-list-wrap .plugins-required a{
        text-decoration: none;
    }



    /*Sidebar .wpblockhub-import-wrap*/

    .wpblockhub-import-wrap{}
    .wpblockhub-import-header-wrap{
        padding: 15px;
    }

    .wpblockhub-import-header{

    }
</style>