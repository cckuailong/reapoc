<?php


// create custom plugin settings menu
add_action('admin_menu', 'request_list_menu');

function request_list_menu() {

    //create new top-level menu

    add_submenu_page('edit.php?post_type=carsellers', 'Requests List', 'Requests List', 'manage_options', 'request_list', 'request_lists_page');

    // add_action( 'admin_init', 'register_request_lists' );
}

function request_lists_page() {

    $request_list_url = site_url() . '/wp-admin/edit.php?post_type=packages&page=request_list';

    wp_enqueue_script( 'datatable', plugins_url('js/jquery.dataTables.min.js', __FILE__) );
    ?>




   
    <?php $concat = get_option("permalink_structure") ? "?" : "&"; ?> 
    <style type="text/css">
        .dataTables_length{
            float: left;
            margin-bottom: 10px;
        }
        .dataTables_filter{
            float: right;
            margin-bottom: 10px;
        }
        #user_post_list_paginate a{
            padding:3px 5px;
            font-size:10pt;
            background: #eee;
            margin: 2px;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        #user_post_list_paginate a:hover{
            border: 1px solid #999;
        }
        .title.sorting_asc:after{
            content: "\25bc";
        }
        .title.sorting_desc:after{
            content: "\25b2";
        }
        .title{
            cursor: pointer;
            text-decoration: underline;
        }
        #wpfooter{
            display: none;
        }
        .dataTables_info{
            height: 30px
        }
        .sorting_desc .sorting-indicator,.sorting_desc .sorting-indicator:before{
            content: '\f140';
            display: block;
            margin-left: 0px;
            float: right;width: 10%;
        }
        .sorting_asc .sorting-indicator,.sorting_asc .sorting-indicator:before{
            content: '\f142';
            display: block;
            margin-left: 0px;
            float: right;width: 10%;
        }
        span.label{
            width: 90%; float: left;

        }
        #overlay {filter:alpha(opacity=0); -moz-opacity:0; -khtml-opacity: 0; opacity: 0;  background-color: #fff; position: absolute; top: 0px; left: 0px; z-index: 50; width: 100%; height: 100%; display:none;}
        #order_detail{margin:auto; padding:20px; width:400px; background:#fff; box-shadow: 0 4px 16px #999; z-index: 51; border:1px solid #acacac;  position:absolute; display:none;position: fixed;top:30% !important;}
        .order_detail td{border: solid 1px #999;

        }
        .subscription_expire_available{
            background: green;
            color: #fff
        }
        .subscription_expire_very_soon{
            background: yellow;
            color:#fff
        }
        .subscription_expired{
            background: red;
            color:#fff
        }
    </style>

    <div class="wrap">
        <h2 style="">Requests List</h2>
        

        <div class="wrap_tab"> 
            <div class="table_device">
                <table class="post wp-list-table widefat fixed posts" id="user_post_list" border="0" width="100%" cellspacing="0" style="clear: both;">

                    <thead>
                        <tr>
                            <th class="order_id" style="width:53px;">
                                <span class="label"> Request ID </span><span class="sorting-indicator"></span>
                            </th>
                            <th class="car_title">
                                Car Title
                                <span class="sorting-indicator"></span>
                            </th>

                            <th class="user_name">
                                Name 
                                <span class="sorting-indicator"></span>
                            </th>
                            <th class="email">
                               Email  
                                <span class="sorting-indicator"></span>
                            </th>
                            <th class="nosort">
                                Phone
                            </th>
                            <th class="date">
                                Date
                                <span class="sorting-indicator"></span>
                            </th>
                            
                        </tr>
                    </thead> 
                    <tbody>
    <?php
    global $wpdb;
    $tablename = $wpdb->prefix . 'carsellers_requests';
    $results = $wpdb->get_results("SELECT * FROM $tablename");
    
    // echo '<pre>';
    // print_r($result);
    $i = 1;
    foreach ($results as $result) {
        if ($i % 2 != 0)
            $alternate = 'alternate';
        else
            $alternate = '';
        ?>
                            <tr class="<?php echo $alternate;
                    $i++; ?>">
                                <td><?php echo $result->id; ?></td>
                                <td><?php 
                      echo  get_the_title($result->carseller_id);
                    ?>

                                    <div class="row-actions"><span class="view"><a href="javascript:void(0);" title="View “Calculus”" rel="permalink" onclick="view_order(<?php echo $result->id; ?>);">View Order</a></span></div>



                                </td>
                                <td><?php echo $result->first_name.' '.$result->last_name; ?></td>
                                <td><?php echo $result->email; ?></td>

                                <td><?php
                                    echo $result->phone;
                                    ?>
                                </td>
                                <td><?php
                                    echo $result->created;
                                    ?>
                                </td>

                            </tr><?php } ?>


                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- ending div for wrapper-->  
    <div id="overlay"></div>
    <div id="order_detail" style="display:none"></div>
    <script language="JavaScript">
    <!--
        jQuery(document).ready(function() {
            jQuery('#user_post_list').dataTable({
                "sPaginationType": "full_numbers"
            });

            jQuery('.nosort').unbind('click');




            jQuery('#close').live('click', function() {

                jQuery('#overlay, #order_detail').animate({'opacity': '0'}, 300, 'linear', function() {
                    jQuery('#overlay, #order_detail').css('display', 'none');
                });
            });


        });



        function view_order(order_id) {
            // alert(order_id)

            jQuery.ajax({
                type: "POST",
                url: "<?php echo site_url(); ?>/wp-admin/admin-ajax.php",
                data: {
                    action: 'request_list_request',
                    order_id: order_id, //this is the $_REQUEST['fn'] from above
                },
                success: function(data) {

                    jQuery('#order_detail').html(data);

                    jQuery('#overlay, #order_detail').animate({'opacity': '0.7'}, 300, 'linear');
                    jQuery('#order_detail').animate({'opacity': '1.00'}, 300, 'linear');
                    jQuery('#overlay, #order_detail').css('display', 'block');
                    jQuery('#order_detail').css({'left': ((jQuery(document).width() / 2) - (jQuery('#order_detail').width() / 2))});
                    jQuery('#order_detail').css({'top': ((jQuery(document).height() / 2) - (jQuery('#order_detail').height() / 2) - 50)});
                }
            });
        }
    //-->
    </script>

    </div>




    <?php
}

add_action('wp_ajax_nopriv_request_list_request', 'view_request_list');
add_action('wp_ajax_request_list_request', 'view_request_list');

function view_request_list() {

    global $wpdb;
    $tablename = $wpdb->prefix . 'carsellers_requests';
    $result = $wpdb->get_results("SELECT * FROM $tablename WHERE id=" . $_POST['order_id']);
    if (!empty($result)) {
        // echo '<pre>';
        // print_r($result[0]);
        ?>
        <h1>Request Details</h1>
        <table style=" border: 1px solid #999;width:96%" class="order_detail">
            <tr>
                <td>Request Id</td><td><?php echo $result[0]->id ?></td>
            </tr>
            <tr>
                <td>Car Title</td><td><?php echo  get_the_title($result[0]->carseller_id);
        ?></td>
            </tr>
            <tr>
                <td>Name</td><td><?php echo $result[0]->first_name.' '.$result[0]->last_name; ?></td>
            </tr>
            <tr>
                <td>Email</td><td><?php echo $result[0]->email; ?></td>
            </tr>
            <tr>
                <td>Phone</td><td><?php echo $result[0]->phone; ?></td>
            </tr>
            <tr>
                <td>Message</td><td><?php echo $result[0]->message; ?></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><a href="mailto:<?php echo $result[0]->email; ?>" style="background: #2ea2cc;border-color: #0074a2;-webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);color: #fff;text-decoration: none;vertical-align: baseline;display: inline-block;text-decoration: none;font-size: 13px;line-height: 26px;height: 28px;margin: 0;padding: 0 10px 1px;cursor: pointer;border-width: 1px;
border-style: solid;-webkit-appearance: none;-webkit-border-radius: 3px;border-radius: 3px;white-space: nowrap;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">Reply</a></td>

            </tr>
                        
        </table>
        <p style="margin-top:20px; text-align:right;"><a href="#" id="close">Close</a></p>
            <?php
        } else {
            echo 'No record found';
        }
        die(); //this makes sure you don't get a "1" or "0" appended to the end of your request.
    }

    add_action('wp_ajax_nopriv_check_cart_empty', 'check_cart_empty');
    add_action('wp_ajax_check_cart_empty', 'check_cart_empty');

    function check_cart_empty() {
        if (!session_id())
            session_start();

        if (isset($_SESSION['refer_package_id'])) {
            echo 'true';
        } else {
            echo 'false';
        }
        die();
    }
    