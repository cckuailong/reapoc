<?php

function wpvivid_add_tab_storage_list()
{
    ?>
    <a href="#" id="wpvivid_tab_storage_list" class="nav-tab storage-nav-tab nav-tab-active" onclick="switchstorageTabs(event,'page-storage-list','page-storage-list')"><?php _e('Storages', 'wpvivid-backuprestore'); ?></a>
    <?php
}

function wpvivid_add_tab_storage_edit()
{
    ?>
    <a href="#" id="wpvivid_tab_storage_edit" class="nav-tab storage-nav-tab delete" onclick="switchstorageTabs(event,'page-storage_edit','page-storage_edit')" style="display: none;">
        <div id="wpvivid_tab_storage_edit_text" style="margin-right: 15px;"><?php _e('Storage Edit', 'wpvivid-backuprestore'); ?></div>
        <div class="nav-tab-delete-img">
            <img src="<?php echo esc_url(plugins_url( 'images/delete-tab.png', __FILE__ )); ?>" style="vertical-align:middle; cursor:pointer;" onclick="wpvivid_close_tab(event, 'wpvivid_tab_storage_edit', 'storage', 'wpvivid_tab_storage_list');" />
        </div>
    </a>
    <?php
}

function wpvivid_add_page_storage_list()
{
    ?>
    <div class="storage-tab-content wpvivid_tab_storage_list" id="page-storage-list">
        <div style="margin-top:10px;"><p><strong><?php _e('Please choose one storage to save your backups (remote storage)', 'wpvivid-backuprestore'); ?></strong></p></div>
        <div class="schedule-tab-block"></div>
        <div class="">
            <table class="widefat">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th><?php _e( 'Storage Provider', 'wpvivid-backuprestore' ); ?></th>
                    <th class="row-title"><?php _e( 'Remote Storage Alias', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Actions', 'wpvivid-backuprestore' ); ?></th>
                </tr>
                </thead>
                <tbody class="wpvivid-remote-storage-list" id="wpvivid_remote_storage_list">
                <?php
                $html = '';
                $html = apply_filters('wpvivid_add_remote_storage_list', $html);
                echo $html;
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5" class="row-title"><input class="button-primary" id="wpvivid_set_default_remote_storage" type="submit" name="choose-remote-storage" value="<?php esc_attr_e( 'Save Changes', 'wpvivid-backuprestore' ); ?>" /></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script>
        jQuery('input[option=add-remote]').click(function(){
            var storage_type = jQuery(".storage-providers-active").attr("remote_type");
            wpvivid_add_remote_storage(storage_type);
            wpvivid_settings_changed = false;
        });

        jQuery('#wpvivid_set_default_remote_storage').click(function(){
            wpvivid_set_default_remote_storage();
            wpvivid_settings_changed = false;
        });

        /**
         * Add remote storages to the list
         *
         * @param action        - The action to add or test a remote storage
         * @param storage_type  - Remote storage types (Amazon S3, SFTP and FTP server)
         */
        function wpvivid_add_remote_storage(storage_type)
        {
            var remote_from = wpvivid_ajax_data_transfer(storage_type);
            var ajax_data;
            ajax_data = {
                'action': 'wpvivid_add_remote',
                'remote': remote_from,
                'type': storage_type
            };
            jQuery('input[option=add-remote]').css({'pointer-events': 'none', 'opacity': '0.4'});
            jQuery('#wpvivid_remote_notice').html('');
            wpvivid_post_request(ajax_data, function (data)
            {
                try
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        jQuery('input[option=add-remote]').css({'pointer-events': 'auto', 'opacity': '1'});
                        jQuery('input:text[option='+storage_type+']').each(function(){
                            jQuery(this).val('');
                        });
                        jQuery('input:password[option='+storage_type+']').each(function(){
                            jQuery(this).val('');
                        });
                        wpvivid_handle_remote_storage_data(data);
                    }
                    else if (jsonarray.result === 'failed')
                    {
                        jQuery('#wpvivid_remote_notice').html(jsonarray.notice);
                        jQuery('input[option=add-remote]').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }
                catch (err)
                {
                    alert(err);
                    jQuery('input[option=add-remote]').css({'pointer-events': 'auto', 'opacity': '1'});
                }

            }, function (XMLHttpRequest, textStatus, errorThrown)
            {
                var error_message = wpvivid_output_ajaxerror('adding the remote storage', textStatus, errorThrown);
                alert(error_message);
                jQuery('input[option=add-remote]').css({'pointer-events': 'auto', 'opacity': '1'});
            });
        }

        function wpvivid_edit_remote_storage() {
            var data_tran = 'edit-'+wpvivid_editing_storage_type;
            var remote_data = wpvivid_ajax_data_transfer(data_tran);
            var ajax_data;
            ajax_data = {
                'action': 'wpvivid_edit_remote',
                'remote': remote_data,
                'id': wpvivid_editing_storage_id,
                'type': wpvivid_editing_storage_type
            };
            jQuery('#wpvivid_remote_notice').html('');
            wpvivid_post_request(ajax_data, function(data){
                try {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success') {
                        jQuery('#wpvivid_tab_storage_edit').hide();
                        wpvivid_click_switch_page('storage', 'wpvivid_tab_storage_list', true);
                        wpvivid_handle_remote_storage_data(data);
                    }
                    else if (jsonarray.result === 'failed') {
                        jQuery('#wpvivid_remote_notice').html(jsonarray.notice);
                    }
                }
                catch(err){
                    alert(err);
                }
            },function(XMLHttpRequest, textStatus, errorThrown) {
                var error_message = wpvivid_output_ajaxerror('editing the remote storage', textStatus, errorThrown);
                alert(error_message);
            });
        }

        /**
         * Set a default remote storage for backups.
         */
        function wpvivid_set_default_remote_storage(){
            var remote_storage = new Array();
            //remote_storage[0] = jQuery("input[name='remote_storage']:checked").val();
            jQuery.each(jQuery("input[name='remote_storage']:checked"), function()
            {
                remote_storage.push(jQuery(this).val());
            });

            var ajax_data = {
                'action': 'wpvivid_set_default_remote_storage',
                'remote_storage': remote_storage
            };
            jQuery('#wpvivid_remote_notice').html('');
            wpvivid_post_request(ajax_data, function(data){
                wpvivid_handle_remote_storage_data(data);
            }, function(XMLHttpRequest, textStatus, errorThrown) {
                var error_message = wpvivid_output_ajaxerror('setting up the default remote storage', textStatus, errorThrown);
                alert(error_message);
            });
        }

        jQuery('#wpvivid_remote_storage_list').on("click", "input", function(){
            var check_status = true;
            if(jQuery(this).prop('checked') === true){
                check_status = true;
            }
            else {
                check_status = false;
            }
            jQuery('input[name="remote_storage"]').prop('checked', false);
            if(check_status === true){
                jQuery(this).prop('checked', true);
            }
            else {
                jQuery(this).prop('checked', false);
            }
        });

        function wpvivid_delete_remote_storage(storage_id){
            var descript = 'Deleting a remote storage will make it unavailable until it is added again. Are you sure to continue?';
            var ret = confirm(descript);
            if(ret === true){
                var ajax_data = {
                    'action': 'wpvivid_delete_remote',
                    'remote_id': storage_id
                };
                wpvivid_post_request(ajax_data, function(data){
                    wpvivid_handle_remote_storage_data(data);
                },function(XMLHttpRequest, textStatus, errorThrown) {
                    var error_message = wpvivid_output_ajaxerror('deleting the remote storage', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        }

        function wpvivid_handle_remote_storage_data(data){
            var i = 0;
            try {
                var jsonarray = jQuery.parseJSON(data);
                if (jsonarray.result === 'success') {
                    jQuery('#wpvivid_remote_storage_list').html('');
                    jQuery('#wpvivid_remote_storage_list').append(jsonarray.html);
                    jQuery('#upload_storage').html(jsonarray.pic);
                    jQuery('#schedule_upload_storage').html(jsonarray.pic);
                    jQuery('#wpvivid_out_of_date_remote_path').html(jsonarray.dir);
                    jQuery('#wpvivid_schedule_backup_local_remote').html(jsonarray.local_remote);
                    wpvivid_control_remote_storage(jsonarray.remote_storage);
                    jQuery('#wpvivid_remote_notice').html(jsonarray.notice);
                }
                else if(jsonarray.result === 'failed'){
                    alert(jsonarray.error);
                }
            }
            catch(err){
                alert(err);
            }
        }

        function wpvivid_control_remote_storage(has_remote){
            if(!has_remote){
                if(jQuery("input:radio[name='save_local_remote'][value='remote']").prop('checked')) {
                    alert("There is no default remote storage configured. Please set it up first.");
                    jQuery("input:radio[name='save_local_remote'][value='local']").prop('checked', true);
                }
            }
        }

        function click_retrieve_remote_storage(id,type,name)
        {
            wpvivid_editing_storage_id = id;
            jQuery('.remote-storage-edit').hide();
            jQuery('#wpvivid_tab_storage_edit').show();
            jQuery('#wpvivid_tab_storage_edit_text').html(name);
            wpvivid_editing_storage_type=type;
            jQuery('#remote_storage_edit_'+wpvivid_editing_storage_type).fadeIn();
            wpvivid_click_switch_page('storage', 'wpvivid_tab_storage_edit', true);

            var ajax_data = {
                'action': 'wpvivid_retrieve_remote',
                'remote_id': id
            };
            wpvivid_post_request(ajax_data, function(data)
            {
                try
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        jQuery('input:text[option=edit-'+jsonarray.type+']').each(function(){
                            var key = jQuery(this).prop('name');
                            jQuery(this).val(jsonarray[key]);
                        });
                        jQuery('input:password[option=edit-'+jsonarray.type+']').each(function(){
                            var key = jQuery(this).prop('name');
                            jQuery(this).val(jsonarray[key]);
                        });
                        jQuery('input:checkbox[option=edit-'+jsonarray.type+']').each(function() {
                            var key = jQuery(this).prop('name');
                            var value;
                            if(jsonarray[key] == '0'){
                                value = false;
                            }
                            else{
                                value = true;
                            }
                            jQuery(this).prop('checked', value);
                        });
                    }
                    else
                    {
                        alert(jsonarray.error);
                    }
                }
                catch(err)
                {
                    alert(err);
                }
            },function(XMLHttpRequest, textStatus, errorThrown)
            {
                var error_message = wpvivid_output_ajaxerror('retrieving the remote storage', textStatus, errorThrown);
                alert(error_message);
            });
        }
    </script>
    <?php
}

function wpvivid_add_page_storage_edit()
{
    ?>
    <div class="storage-tab-content wpvivid_tab_storage_edit" id="page-storage_edit" style="display:none;">
        <div><?php do_action('wpvivid_edit_remote_page'); ?></div>
    </div>
    <script>
        jQuery('input[option=edit-remote]').click(function(){
            wpvivid_edit_remote_storage();
        });
    </script>
    <?php
}

function wpvivid_storage_list($html)
{
    $html='<h2 class="nav-tab-wrapper" style="padding-bottom:0!important;">';
    $html.='<a href="#" id="wpvivid_tab_storage_list" class="nav-tab storage-nav-tab nav-tab-active" onclick="switchstorageTabs(event,\'page-storage-list\',\'page-storage-list\')">'. __('Storages', 'wpvivid-backuprestore').'</a>';
    $html.='<a href="#" id="wpvivid_tab_storage_edit" class="nav-tab storage-nav-tab delete" onclick="switchstorageTabs(event,\'page-storage_edit\',\'page-storage_edit\')" style="display: none;">
        <div id="wpvivid_tab_storage_edit_text" style="margin-right: 15px;">'.__('Storage Edit', 'wpvivid-backuprestore').'</div>
        <div class="nav-tab-delete-img">
            <img src="'.esc_url(plugins_url( 'images/delete-tab.png', __FILE__ )).'" style="vertical-align:middle; cursor:pointer;" onclick="wpvivid_close_tab(event, \'wpvivid_tab_storage_edit\', \'storage\', \'wpvivid_tab_storage_list\');" />
        </div>
    </a>';
    $html.='</h2>';
    $html.='<div class="storage-tab-content wpvivid_tab_storage_list" id="page-storage-list">
        <div style="margin-top:10px;"><p><strong>'.__('Please choose one storage to save your backups (remote storage)', 'wpvivid-backuprestore').'</strong></p></div>
        <div class="schedule-tab-block"></div>
        <div class="">
            <table class="widefat">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>'. __( 'Storage Provider', 'wpvivid-backuprestore' ).'</th>
                    <th class="row-title">'. __( 'Remote Storage Alias', 'wpvivid-backuprestore' ).'</th>
                    <th>'. __( 'Actions', 'wpvivid-backuprestore' ).'</th>
                </tr>
                </thead>
                <tbody class="wpvivid-remote-storage-list" id="wpvivid_remote_storage_list">
                ';
    $html_list='';
    $html.= apply_filters('wpvivid_add_remote_storage_list', $html_list);
    $html.='</tbody><tfoot><tr>
            <th colspan="5" class="row-title"><input class="button-primary" id="wpvivid_set_default_remote_storage" type="submit" name="choose-remote-storage" value="'.esc_attr__( 'Save Changes', 'wpvivid-backuprestore' ).'" /></th>
            </tr></tfoot></table></div></div>';

    $html .= '<script>
            jQuery(\'#wpvivid_remote_storage_list\').on("click", "input", function(){
                var check_status = true;
                if(jQuery(this).prop(\'checked\') === true){
                     check_status = true;
                }
                else {
                    check_status = false;
                }
                jQuery(\'input[name = "remote_storage"]\').prop(\'checked\', false);
                if(check_status === true){
                    jQuery(this).prop(\'checked\', true);
                 }
                else {
                    jQuery(this).prop(\'checked\', false);
                }
            });
            </script>';
    return $html;
}

add_action('wpvivid_storage_add_tab', 'wpvivid_add_tab_storage_list', 10);
add_action('wpvivid_storage_add_tab', 'wpvivid_add_tab_storage_edit', 11);
add_action('wpvivid_storage_add_page', 'wpvivid_add_page_storage_list', 10);
add_action('wpvivid_storage_add_page', 'wpvivid_add_page_storage_edit', 11);
//add_filter('wpvivid_storage_list','wpvivid_storage_list',10);
?>



<script>
    function select_remote_storage(evt, storage_page_id)
    {
        var i, tablecontent, tablinks;
        tablinks = document.getElementsByClassName("storage-providers");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace("storage-providers-active", "");
        }
        evt.currentTarget.className += " storage-providers-active";

        jQuery(".storage-account-page").hide();
        jQuery("#"+storage_page_id).show();
    }
    function switchstorageTabs(evt,contentName,storage_page_id) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="table-list-content" and hide them
        tabcontent = document.getElementsByClassName("storage-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="table-nav-tab" and remove the class "nav-tab-active"
        tablinks = document.getElementsByClassName("storage-nav-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
        }

        // Show the current tab, and add an "storage-menu-active" class to the button that opened the tab
        document.getElementById(contentName).style.display = "block";
        evt.currentTarget.className += " nav-tab-active";

        var top = jQuery('#'+storage_page_id).offset().top-jQuery('#'+storage_page_id).height();
        jQuery('html, body').animate({scrollTop:top}, 'slow');
    }
</script>