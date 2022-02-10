<?php
    //display My Affiliates menu
    $wpam_plugin_tabs = array(
        'wpam-affiliates' => __('Affiliate Details', 'affiliates-manager'),
        'wpam-affiliates&tab=export_data' => __('Export Data', 'affiliates-manager')
    );
    
    echo '<div class="wrap"><h1>'.__('My Affiliates', 'affiliates-manager').'</h1>'; 
    
    if(isset($_GET['page'])){
        $current = $_GET['page'];
        if(isset($_GET['action'])){
            $current .= "&action=".$_GET['action'];
        }
    }
    $content = '';
    $content .= '<h2 class="nav-tab-wrapper">';
    foreach($wpam_plugin_tabs as $location => $tabname)
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
    echo '<div id="poststuff"><div id="post-body">';
    if(isset($_GET['tab']))
    { 
        switch ($_GET['tab'])
        {
           case 'export_data':
               wpam_my_affiliates_export();
               break;
        }
    }
    else
    {
        wpam_my_affiliates_list();
    }

    echo '</div></div>';
    echo '</div>';

    function wpam_my_affiliates_list()
    {
        
        $status_array = array(
            'all_active' => __('All Active', 'affiliates-manager'),
            'all' => __('All (Including Closed)', 'affiliates-manager'),
            'active' => __('Active', 'affiliates-manager'),
            'applied' => __('Applied', 'affiliates-manager'),
            'approved' => __('Approved', 'affiliates-manager'),
            'confirmed' => __('Confirmed', 'affiliates-manager'),
            'declined' => __('Declined', 'affiliates-manager'),
            'blocked' => __('Blocked', 'affiliates-manager'),
            'inactive' => __('Inactive', 'affiliates-manager')
        );
        $current_class = "";
        if (isset($_REQUEST['statusFilter'])) {
            $status_text = esc_sql($_REQUEST['statusFilter']);
            if (!empty($status_text)) {
                $current_class = $status_text;
            }
        }
        ?>
        <ul class="subsubsub"> 
            <?php
            $count = 1;
            foreach ($status_array as $key => $status) {
                ?>
                <li><a href="admin.php?page=wpam-affiliates&statusFilter=<?php echo $key; ?>"<?php echo ($current_class == $key) ? ' class="current"' : ''; ?>><?php echo $status; ?></a><?php echo ($count == 9) ? '' : ' |'; ?></li>
                <?php
                $count = $count + 1;
            }
            ?>
        </ul>
        <!--<div id="poststuff"><div id="post-body">-->
                <?php
                include_once(WPAM_BASE_DIRECTORY . '/classes/ListAffiliatesTable.php');
                //Create an instance of our package class...
                $affiliates_list_table = new WPAM_List_Affiliates_Table();
                //Fetch, prepare, sort, and filter our data...
                $affiliates_list_table->prepare_items();
                ?>
                <!--        
                <style type="text/css">
                    .column-affiliateId {width:6%;}
                    .column-status {width:6%;}
                    .column-balance {width:6%;}
                    .column-earnings {width:6%;}
                    .column-firstName {width:6%;}
                    .column-lastName {width:6%;}
                    .column-email {width:10%;}
                    .column-companyName {width:10%;}
                    .column-dateCreated {width:10%;}
                    .column-websiteUrl {width:10%;}
                </style>
                -->
                <div class="wpam-click-throughs">

                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                    <form id="wpam-click-throughs-filter" method="get">
                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                        <!-- Now we can render the completed list table -->
                        <?php $affiliates_list_table->display() ?>
                    </form>

                </div>
        <?php
    }
    
    function wpam_my_affiliates_export()
    {
        ?>
        <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('Export Affiliates Record', 'affiliates-manager'); ?></label></h3>
            <div class="inside">
            <form method="POST">
                <?php wp_nonce_field('wpam-export-affiliates-to-csv-nonce'); ?>
                <p>
                    <input type="submit" name="wpam-export-affiliates-to-csv" value="<?php _e('Export to CSV', 'affiliates-manager') ?>" class="button-primary"/>
                </p>
            </form>
            </div>
        </div>
        <?php        
    }
    
