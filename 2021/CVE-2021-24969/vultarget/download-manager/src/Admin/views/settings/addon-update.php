<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right" style="margin-top: -1px"><a class="btn btn-xs btn-primary" href="edit.php?post_type=wpdmpro&page=settings&tab=plugin-update&newpurchase=1"><i class="fa fa-sync"></i> <?php _e( "Check For Updates" , "download-manager" ); ?></a>   <a class="btn btn-warning btn-xs" href="edit.php?post_type=wpdmpro&page=settings&tab=plugin-update&__lononce=<?=wp_create_nonce(NONCE_KEY); ?>"><?php _e( "Logout" , "download-manager" ); ?></a></div>
        <i class="fa fa-sync"></i> &nbsp; <?php _e( "Add-on Update" , "download-manager" ) ?></div>
    <div class="panel-body-x">

        <?php if(get_option('__wpdm_suname') =='') { ?>
            <div class="panel-body">
                <div class="log-req" style="width: 350px;margin: 30px auto;padding: 30px 30px 40px;">
                <div class="form-group text-center">
                    <img style="width: 128px;margin-bottom: 20px" src="<?php echo WPDM_BASE_URL.'assets/images/wpdm-icon.png'; ?>" />
                </div>
                    <div class="form-group text-center" style="font-weight: 300;text-transform: capitalize;font-size: 9pt;">
                        <?php echo sprintf(__( "Enter your %s login info" , "download-manager" ), '<a href="https://www.wpdownloadmanager.com/signup/" target="_blank">WPDownloadManager.com</a>'); ?>
                    </div>

                    <div class="form-group">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon" style="border-radius: 3px 0 0 3px" id="sizing-addon1"><i class="fa fa-user"></i></span>
                        <input style="border-radius: 0 3px 3px 0"  placeholder="Username" name="__wpdm_suname" id="user_login"
                               class="form-control required text" value="" size="20" tabindex="38" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-lg">
                        <span style="border-radius: 3px 0 0 3px"  class="input-group-addon" id="sizing-addon1"><i class="fa fa-key"></i></span>
                        <input style="border-radius: 0 3px 3px 0"  placeholder="Password" name="__wpdm_supass" id="user_pass"
                               class="form-control required password" value="" size="20" tabindex="39" type="password">
                    </div>
                </div>
                    <div class="form-group">
                        <div class="well text-center" style="border-radius: 3px;background: #ffffff;padding: 10px;box-shadow:none;color: #999;font-family: Montserrat, serif;font-weight: 300;font-size:10px;letter-spacing:0.6px;">
                        <?php _e('Click Save Settings Button To Login', 'download-manager'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .well.log-req *:not(.btn){
                    border-radius: 0 !important;
                }
            </style>


        <?php
        } else {
        //precho($purchased_items);
        ?>
            <ul id="plugin-updates-nav" class="nav nav-pills nav-justified">
                <li class="active"><a href="#pro-add-ons" data-toggle="tab"><?php _e( "Purchased Add-ons" , "download-manager" ); ?></a></li>
                <li><a href="#free-add-ons" data-toggle="tab"><?php _e( "Free Add-ons" , "download-manager" ); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pro-add-ons">
                    <table class="table" style="margin: 0;">
                        <thead>
                        <tr>
                            <th><?php _e( "Product Name" , "download-manager" ); ?></th>
                            <th><?php _e( "Active(v)" , "download-manager" ); ?></th>
                            <th><?php _e( "Latest(v)" , "download-manager" ); ?></th>
                            <th><?php _e( "Download" , "download-manager" ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $latest = WPDM()->updater->getLatestVersions();

                        if(isset($purchased_items) && is_array($purchased_items)){
                            foreach($purchased_items as $item){
                                if(isset($item->download_url)){
                                    foreach($item->download_url as $file => $dlu){
                                        $plugin_name = str_replace(".zip", "", basename($file));

                                        if(!strstr($plugin_name, "download-manager-")){

                                        $plugin_data = wpdm_plugin_data($plugin_name);
                                        ?>
                                        <tr class="<?php if($item->order_status == 'Expired'){  ?>bg-danger<?php } else { ?><?php echo version_compare(wpdm_valueof($latest, $plugin_name), $plugin_data['Version'], '>')?'bg-warning':(!$plugin_data?'':'bg-success'); ?><?php } ?>">
                                            <td><a href="https://www.wpdownloadmanager.com/?p=<?php echo $item->pid; ?>" target="_blank"><?php echo $item->post_title; ?> ( <?php echo basename($file); ?> )</a></td>
                                            <td><?php echo isset($plugin_data['Version'])?$plugin_data['Version']:'NA'; ?></td>
                                            <td><?php echo isset($latest[$plugin_name])?$latest[$plugin_name]:'NA'; ?></td>
                                            <td style="width: 100px">
                                                <?php if($item->order_status == 'Completed'){  ?>
                                                    <?php if(!$plugin_data){ ?>
                                                        <a href="#" data-url="<?php echo $dlu; ?>" data-action="installaddon" data-plugin="<?php echo $plugin_name; ?>" class="btn btn-xs btn-success btn-block btn-update"><i class="fa fa-plus"></i> <?php _e( "Install" , "download-manager" ); ?></a>
                                                    <?php } else if(isset($latest[$plugin_name]) && version_compare($latest[$plugin_name], $plugin_data['Version'], '>')){ ?>
                                                        <a href="#" data-url="<?php echo $dlu; ?>" data-action="updateaddon" data-plugin="<?php echo $plugin_name; ?>" class="btn btn-xs btn-warning btn-block btn-update"><i class="fa fa-sync"></i> <?php _e( "Update" , "download-manager" ); ?></a>
                                                    <?php } else echo "<span class='text-success'><i class='fa fa-check-circle'></i> ". __( "Updated" , "download-manager" )."</span>"; ?>
                                                <?php } else { ?>
                                                    <a href="https://www.wpdownloadmanager.com/user-dashboard/?udb_page=purchases/order/<?php echo $item->oid; ?>/" target="_blank" class="btn btn-xs btn-danger btn-block"><?php _e( "Expired" , "download-manager" ); ?></a>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                    <?php }}}}} ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="free-add-ons">
                    <table class="table" style="margin: 0;">
                        <thead>
                        <tr>
                            <th><?php _e( "Product Name" , "download-manager" ); ?></th>
                            <th><?php _e( "Active(v)" , "download-manager" ); ?></th>
                            <th><?php _e( "Latest(v)" , "download-manager" ); ?></th>
                            <th><?php _e( "Download" , "download-manager" ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        foreach($freeaddons as $addon){
                            $addon->files = (array)$addon->files;
                            $file = array_shift($addon->files);
                            $plugin_name = str_replace(".zip", "", basename($file));
                            $plugin_data = wpdm_plugin_data($plugin_name);
                            ?>

                            <tr class="<?php if(isset($latest[$plugin_name])) { echo version_compare($latest[$plugin_name], $plugin_data['Version'], '>')?'bg-warning':(!$plugin_data?'':'bg-success'); } ?>">
                                <td><a href="<?php echo $addon->link; ?>" target="_blank"><?php echo $addon->post_title; ?></a></td>
                                <td><?php echo isset($plugin_data['Version'])?$plugin_data['Version']:'NA'; ?></td>
                                <td><?php echo isset($latest[$plugin_name])?$latest[$plugin_name]:'NA'; ?></td>
                                <td style="width: 100px">

                                    <?php if(!$plugin_data){ ?>
                                        <a href="#" data-url="https://www.wpdownloadmanager.com/?wpdmdl=<?php echo $addon->ID; ?>" data-action="installaddon" data-plugin="<?php echo $plugin_name; ?>" class="btn btn-xs btn-success btn-block btn-update"><i class="fa fa-plus"></i> <?php _e( "Install" , "download-manager" ); ?></a>
                                    <?php } else if(isset($latest[$plugin_name]) && version_compare($latest[$plugin_name], $plugin_data['Version'], '>')){ ?>
                                        <a href="#" data-url="https://www.wpdownloadmanager.com/?wpdmdl=<?php echo $addon->ID; ?>" data-action="updateaddon" data-plugin="<?php echo $plugin_name; ?>" class="btn btn-xs btn-warning btn-block btn-update"><i class="fa fa-sync"></i> <?php _e( "Update" , "download-manager" ); ?></a>
                                    <?php } else echo "<span class='text-success'><i class='fa fa-check-circle'></i> ". __( "Updated" , "download-manager" )."</span>"; ?>

                                </td>
                            </tr>

                        <?php }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                jQuery(function($){
                    $('.btn-update').on('click', function (res) {
                        var bhtml = $(this).html(), btn = $(this);
                        btn.html('<i class="fa fa-sync fa-spin"></i> <?php _e( "Please Wait..." , "download-manager" ); ?>');
                        $.post('admin-ajax.php?action='+$(this).data('action'), {updateurl: $(this).data('url'),  plugin: $(this).data('plugin')}, function (res) {
                            btn.html('<i class="fa fa-check-circle"></i> <?php _e( "Success!" , "download-manager" ); ?>');
                        });
                        return false;
                    })
                });
            </script>

        <?php } ?>

    </div>
</div>
