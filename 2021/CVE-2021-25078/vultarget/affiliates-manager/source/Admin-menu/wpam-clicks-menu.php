<?php

//display clicks menu
function wpam_display_clicks_menu()
{
    ?>
    <div class="wrap">
    <h2><?php _e('Click Tracking', 'affiliates-manager');?></h2>
    <?php
    $wpam_clicktracking_tabs = array(
        'wpam-clicktracking' => __('Unique Click Tracking', 'affiliates-manager'),
    ); 

    if(isset($_GET['page'])){
        $current = sanitize_text_field($_GET['page']);
        if(isset($_GET['action'])){
            $current .= "&action=".sanitize_text_field($_GET['action']);
        }
    }
    $content = '';
    $content .= '<h2 class="nav-tab-wrapper">';
    foreach($wpam_clicktracking_tabs as $location => $tabname)
    {
        if($current == $location){
            $class = ' nav-tab-active';
        } else{
            $class = '';    
        }
        $content .= '<a class="nav-tab'.$class.'" href="?page='.$location.'">'.$tabname.'</a>';
    }
    $content .= '</h2>';
    echo $content;
    ?>
    <p><?php _e('This tab shows unique referrals to your website from your affiliates', 'affiliates-manager');?></p>
    <div id="poststuff"><div id="post-body">
    <?php        
    
    include_once(WPAM_BASE_DIRECTORY . '/classes/ListClicksTable.php');
    //Create an instance of our package class...
    $clicks_list_table = new WPAM_List_Clicks_Table();
    //Fetch, prepare, sort, and filter our data...
    $clicks_list_table->prepare_items();
    ?>
    <style type="text/css">
        .column-trackingTokenId {width:6%;}
        .column-dateCreated {width:20%;}
        .column-sourceAffiliateId {width:6%;}
        .column-trackingKey {width:25%;}
        .column-sourceCreativeId {width:6%;}
        .column-referer {width:25%;}
    </style>
    <div class="wpam-click-throughs">

        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="wpam-click-throughs-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <!-- Now we can render the completed list table -->
            <?php $clicks_list_table->display() ?>
        </form>

    </div>

    </div></div>
    </div>
    <?php
}
