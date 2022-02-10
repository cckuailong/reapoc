<?php if(get_post_meta($post->ID,'__wpdm_legacy_id',true)){ ?>
<input type="hidden" name="file[legacy_id]" value="<?php echo get_post_meta($post->ID,'__wpdm_legacy_id',true); ?>" />
<?php } ?>
<div class="w3eden" id="all-package-settings">
<ul class="nav nav-tabs">
    <li class="active"><a href="#package-settings" role="tab" data-toggle="tab"><?php echo __( "Package Settings" , "download-manager" ); ?></a></li>
    <li><a href="#lock-options" role="tab" data-toggle="tab"><?php echo __( "Lock Options" , "download-manager" ); ?></a></li>
    <li><a href="#package-icons" role="tab" data-toggle="tab"><?php echo __( "Icons" , "download-manager" ); ?></a></li>
    <?php

    $etabs = apply_filters('wpdm_package_settings_tabs',array());

    foreach($etabs as $id=>$tab){
        echo "<li><a href='#{$id}' role=\"tab\" data-toggle=\"tab\">{$tab['name']}</a></li>";

    } ?>
</ul>

    <div class="tab-content">
<div id="package-settings" class="tab-pane active">
    <table cellpadding="5" id="file_settings_table" cellspacing="0" width="100%" class="table table-bordered table-hover">
        <tr id="version_row">
            <td width="150px"><?php echo __( "Version:" , "download-manager" ); ?></td>
            <td><input size="10" type="text" class="form-control input-sm" style="width: 100px" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_version',true)); ?>" name="file[version]" />
            </td></tr>

        <tr id="link_label_row">
            <td><?php echo __( "Link Label:" , "download-manager" ); ?></td>
            <td><input size="10" type="text" class="form-control input-sm" style="width: 200px" value="<?php echo htmlspecialchars(get_post_meta($post->ID,'__wpdm_link_label',true)); ?>" name="file[link_label]" />
            </td></tr>

        <tr id="stock_row">
            <td><?php echo __( "Stock&nbsp;Limit:" , "download-manager" ); ?></td>
            <td><input size="10" class="form-control input-sm" style="width: 80px" type="number" name="file[quota]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_quota',true)); ?>" /></td>
        </tr>

        <tr id="view_count_row">
            <td><?php echo __( "View&nbsp;Count:" , "download-manager" ); ?></td>
            <td><input size="10" style="width: 80px" type="number" class="form-control input-sm" name="file[view_count]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_view_count',true)); ?>" /> <i class="info fa fa-info" title="Set/Reset Download Count for this package"></i></td>
        </tr>

       <tr id="download_count_row">
            <td><?php echo __( "Download&nbsp;Count:" , "download-manager" ); ?></td>
            <td><input size="10" style="width: 80px" type="number" class="form-control input-sm" name="file[download_count]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_download_count',true)); ?>" /> <i class="info fa fa-info" title="Set/Reset Download Count for this package"></i></td>
        </tr>

       <tr id="package_size_row">
            <td><?php echo __( "Package&nbsp;Size:" , "download-manager" ); ?></td>
            <td><input size="10" style="width: 80px" class="form-control input-sm" type="text" name="file[package_size]" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_package_size',true)); ?>" /> <i class="info fa fa-info" title="Total size of included files with this package."></i></td>
        </tr>

        <tr id="access_row">
            <td valign="top"><?php echo __( "Allow Access:" , "download-manager" ); ?></td>
            <td>
                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                $currentAccess = get_post_meta($post->ID, '__wpdm_access', true);
                $currentAccess = maybe_unserialize($currentAccess);
                ?>
                <select name="file[access][]"  data-placeholder="<?php _e( "Who should be able to download?" , "download-manager" ); ?>"  class="chzn-select role" multiple="multiple" id="access" style="min-width: 350px">
                    <?php
                    $selz = '';
                    if(  is_array($currentAccess) ) $selz = (in_array('guest',$currentAccess))?'selected=selected':'';
                    if(!isset($_GET['post']) && !$currentAccess) $selz = 'selected=selected';
                    ?>

                    <option value="guest" <?php echo $selz  ?>><?php echo __( "All Visitors" , "download-manager" ); ?></option>
                    <?php
                    foreach( $roles as $role => $name ) {



                        if(  is_array($currentAccess) ) $sel = (in_array($role,$currentAccess))?'selected=selected':'';
                        else $sel = '';



                        ?>
                        <option value="<?php echo $role; ?>" <?php echo $sel  ?>> <?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </td></tr>


        <tr id="page_template_row">
            <td><?php echo __( "Page Template:" , "download-manager" ); ?></td>
            <td><?php
                echo WPDM()->packageTemplate->dropdown(array('type'=>'page','name' => 'file[page_template]', 'id'=>'pge_tpl', 'selected' => get_post_meta($post->ID,'__wpdm_page_template',true)), true);
                ?>

            </td>
        </tr>
        <?php if(isset($_GET['post'])&&$_GET['post']!=''){ ?>
            <tr>
                <td><?php echo __( "Master Key" , "download-manager" ); ?></td>
                <td><input class="form-control" style="width: 200px;display: inline-block;font-family: monospace;letter-spacing: 1px" type="text" readonly="readonly" value="<?php echo get_post_meta($post->ID, '__wpdm_masterkey', true); ?>"> <label><input type="checkbox" value="1" name="reset_key" /> <?php echo __( "Regenerate Master Key for Download" , "download-manager" ); ?></label> <i class="info fa fa-info" data-placement="top" title="<?php echo __( "This key can be used for direct download" , "download-manager" ); ?>"></i></td>
            </tr>
        <?php } ?>
        <?php do_action("wpdm_package_settings_tr", $post->ID); ?>
    </table>
    <div class="clear"></div>
</div>

<?php include("lock-options.php"); ?>

<?php include("icons.php"); ?>

<?php foreach($etabs as $id=>$tab){
     echo "<div class='tab-pane' id='{$id}'>";
     call_user_func($tab['callback']);
     echo "</div>";
} ?>
    </div>
<?php if(!file_exists(dirname(WPDM_BASE_DIR).'/wpdm-premium-packages/wpdm-premium-packages.php')){  ?>
    <div class="w3eden" id="wpdm-activate-shop">
        <div  class="alert alert-warning" id="wpdm-activate-shop-info" style="background-image: none !important;border-radius:0 !important;background:#d7b75d33;border:0;text-align:center;margin: 0 10px 10px">

            Planning to sell your digital products? <a style="font-weight: 900" href="#" id="wpdm-activate-shop-link">Activate Digital Store Option</a>

        </div>

        <script>
            jQuery(function(){
                jQuery('#wpdm-activate-shop-link').on('click', function(){
                    jQuery(this).html('Activating...')
                    jQuery.post(ajaxurl,{action:'wpdm-activate-shop'}, function(res){
                        jQuery('#wpdm-activate-shop-info').html(res);
                    });
                    return false;
                });
            });
        </script>

    </div>
<?php } ?>

</div>








<!-- all js ------>

<script type="text/javascript">

    jQuery(document).ready(function() {

        // Uploading files
        var file_frame;
        jQuery( "#tabs" ).tabs();
        //jQuery( "#eid" ).buttonset();



        jQuery('body').on('click','#img', function( event ){

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery( this ).data( 'uploader_title' ),
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' ),
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                jQuery('#fpvw').val(attachment.url);
                jQuery('#rmvp').remove();
                jQuery('#img').html("<img src='"+attachment.url+"' style='max-width:100%'/><input type='hidden' name='file[preview]' value='"+attachment.url+"' >");
                jQuery('#img').after('<a href="#"  id="rmvp"> <img style="width:16px;height:16px" align="left" src="<?php echo plugins_url('/download-manager/images/delete.svg'); ?>" /> Remove Preview Image</a>');
                file_frame.close();
                // Do something with attachment.id and/or attachment.url here
            });

            // Finally, open the modal
            file_frame.open();
        });


        jQuery('body').on('click', ".cb-enable",function(){
            var parent = jQuery(this).parents('.switch');
            jQuery('.cb-disable',parent).removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery('.checkbox',parent).attr('checked', true);
        });
        jQuery('body').on('click', ".cb-disable",function(){
            var parent = jQuery(this).parents('.switch');
            jQuery('.cb-enable',parent).removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery('.checkbox',parent).attr('checked', false);
        });

        var n = 0;

        jQuery('body').on('click', '.wpdm-label',function(){
            //alert(jQuery(this).attr('class'));
            if(jQuery(this).hasClass('wpdm-checked')) jQuery(this).addClass('wpdm-unchecked').removeClass('wpdm-checked');
            else jQuery(this).addClass('wpdm-checked').removeClass('wpdm-unchecked');

        });



        jQuery("#wpdm-settings select").select2({no_results_text: ""});

        jQuery('.handlediv').click(function(){
            jQuery(this).parent().find('.inside').slideToggle();
        });




        jQuery('.nopro').click(function(){
            if(this.checked) jQuery('.wpdmlock').removeAttr('checked');
        });

        jQuery('.wpdmlock').click(function(){
            if(this.checked) {
                jQuery('#'+jQuery(this).attr('rel')).slideDown();
                jQuery('.nopro').removeAttr('checked');
            } else {
                jQuery('#'+jQuery(this).attr('rel')).slideUp();
            }
        });

        jQuery('.w3eden .info.fa').tooltip({html:true, placement: 'right'});



    });


    function generatepass(id){
        wpdm_pass_target = '#'+id;
        jQuery('#generatepass').modal('show');
    }

    function wpdm_view_package(){

    }




    <?php /* if(is_array($file)&&get_post_meta($file['id'],'__wpdm_lock',true)!='') { ?>
    jQuery('#<?php echo get_post_meta($file['id'],'__wpdm_lock',true); ?>').show();
    <?php } */ ?>
</script>

<style>

.w3eden .tooltip-inner{ border-radius: 2px !important; font-family: courier, monospace; padding: 8px 13px;  text-align: left; font-size: 12px; max-width: 250px; }
.w3eden input[type=radio]{ margin-top: 0; }
 .form-control.input-sm{ display: inline; }

    .ui-tabs .ui-tabs-nav li a{
        font-size: 10pt !important;
        outline: none !important;

    }
    .ui-tabs .ui-tabs-nav li{
        margin-bottom: 0 !important;
        border-bottom: 1px solid #dddddd !important;
    }

    .ui-tabs .ui-tabs-nav li.ui-state-active{
        border-bottom: 1px solid #ffffff !important;
    }
    .wdmiconfile{
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
    }


    .w3eden input[type=checkbox], .w3eden input[type=radio], .w3eden label{ margin: 0 !important;}

#wpdm-files_length{
    display: none;
}
#wpdm-files_filter{
    margin-bottom:10px !important;
}
.adp-ui-state-highlight{
    width:50px;
    height:50px;
    background: #fff;
    float:left;
    padding: 4px;
    border:1px solid #aaa;
}
#wpdm-files tbody .ui-sortable-helper{
    background: transparent;

}
#wpdm-files tbody .ui-sortable-helper td{
    vertical-align: middle;
    background: #eeeeee;
}
input[type=text]{
    padding: 4px 7px;
    border-radius: 3px;
}


.dfile{background: #ffdfdf;}
.cfile{
    cursor: move;
}
.cfile img, .dfile img{cursor: pointer;}

#editorcontainer textarea{border:0px;width:99.9%;}
#icon_uploadUploader,#file_uploadUploader {background: transparent url('<?php echo plugins_url(); ?>/download-manager/images/browse.png') left top no-repeat; }
#icon_uploadUploader:hover,#file_uploadUploader:hover {background-position: left bottom; }
.frm td{line-height: 30px; border-bottom: 1px solid #EEEEEE; padding:5px; font-size:9pt;font-family: Tahoma;}

.fwpdmlock td{
    border:0px !important;
    vertical-align: middle !important;
}
#filelist {
    margin-top: 10px;
}
#filelist .file{
    margin-top: 5px;
    padding: 0px 10px;
    color:#444;
    display: block;
    margin-bottom: 5px;
    font-weight: normal;
}

table.widefat{
    border-bottom:0px;
}

.genpass{
    cursor: pointer;
}

h3,
h3.handle{
    cursor: default !important;
}


@-webkit-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@-moz-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@-ms-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@-o-keyframes progress-bar-stripes {
    from {
        background-position: 0 0;
    }
    to {
        background-position: 40px 0;
    }
}

@keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

.progress {
    height: 15px;
    margin-bottom: 10px;
    overflow: hidden;
    background-color: #f7f7f7;
    background-image: -moz-linear-gradient(top, #f5f5f5, #f9f9f9);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f5f5f5), to(#f9f9f9));
    background-image: -webkit-linear-gradient(top, #f5f5f5, #f9f9f9);
    background-image: -o-linear-gradient(top, #f5f5f5, #f9f9f9);
    background-image: linear-gradient(to bottom, #f5f5f5, #f9f9f9);
    background-repeat: repeat-x;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff5f5f5', endColorstr='#fff9f9f9', GradientType=0);
    -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress .bar {
    float: left;
    width: 0;
    height: 100%;
    font-size: 12px;
    color: #ffffff;
    text-align: center;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    background-color: #0e90d2;
    background-image: -moz-linear-gradient(top, #149bdf, #0480be);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#149bdf), to(#0480be));
    background-image: -webkit-linear-gradient(top, #149bdf, #0480be);
    background-image: -o-linear-gradient(top, #149bdf, #0480be);
    background-image: linear-gradient(to bottom, #149bdf, #0480be);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff149bdf', endColorstr='#ff0480be', GradientType=0);
    -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    -moz-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-transition: width 0.6s ease;
    -moz-transition: width 0.6s ease;
    -o-transition: width 0.6s ease;
    transition: width 0.6s ease;
}

.progress .bar + .bar {
    -webkit-box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    -moz-box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
    box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
}

.progress-striped .bar {
    background-color: #149bdf;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    -webkit-background-size: 40px 40px;
    -moz-background-size: 40px 40px;
    -o-background-size: 40px 40px;
    background-size: 40px 40px;
}

.progress.active .bar {
    -webkit-animation: progress-bar-stripes 2s linear infinite;
    -moz-animation: progress-bar-stripes 2s linear infinite;
    -ms-animation: progress-bar-stripes 2s linear infinite;
    -o-animation: progress-bar-stripes 2s linear infinite;
    animation: progress-bar-stripes 2s linear infinite;
}

.progress-danger .bar,
.progress .bar-danger {
    background-color: #dd514c;
    background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#c43c35));
    background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
    background-image: linear-gradient(to bottom, #ee5f5b, #c43c35);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffc43c35', GradientType=0);
}

.progress-danger.progress-striped .bar,
.progress-striped .bar-danger {
    background-color: #ee5f5b;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}

.progress-success .bar,
.progress .bar-success {
    background-color: #5eb95e;
    background-image: -moz-linear-gradient(top, #62c462, #57a957);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#57a957));
    background-image: -webkit-linear-gradient(top, #62c462, #57a957);
    background-image: -o-linear-gradient(top, #62c462, #57a957);
    background-image: linear-gradient(to bottom, #62c462, #57a957);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff62c462', endColorstr='#ff57a957', GradientType=0);
}

.progress-success.progress-striped .bar,
.progress-striped .bar-success {
    background-color: #62c462;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}

.progress-info .bar,
.progress .bar-info {
    background-color: #4bb1cf;
    background-image: -moz-linear-gradient(top, #5bc0de, #339bb9);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#339bb9));
    background-image: -webkit-linear-gradient(top, #5bc0de, #339bb9);
    background-image: -o-linear-gradient(top, #5bc0de, #339bb9);
    background-image: linear-gradient(to bottom, #5bc0de, #339bb9);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de', endColorstr='#ff339bb9', GradientType=0);
}

.progress-info.progress-striped .bar,
.progress-striped .bar-info {
    background-color: #5bc0de;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}

.progress-warning .bar,
.progress .bar-warning {
    background-color: #faa732;
    background-image: -moz-linear-gradient(top, #fbb450, #f89406);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fbb450), to(#f89406));
    background-image: -webkit-linear-gradient(top, #fbb450, #f89406);
    background-image: -o-linear-gradient(top, #fbb450, #f89406);
    background-image: linear-gradient(to bottom, #fbb450, #f89406);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffbb450', endColorstr='#fff89406', GradientType=0);
}

.progress-warning.progress-striped .bar,
.progress-striped .bar-warning {
    background-color: #fbb450;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
}
#access{
    width: 250px;
}

#nxt{
    background-color: #C1F4C1;
    background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
    -webkit-background-size: 40px 40px;
    -moz-background-size: 40px 40px;
    -o-background-size: 40px 40px;
    background-size: 40px 40px;
    display: none;
    border-bottom:1px solid #008000;
    color: #0C490C;font-family:'Courier New';padding:5px 10px;text-align: center;
}

#serr{
    display: none;margin-top: 5px;border:1px solid #800000;background: #FFEDED;color: #000;font-family:'Courier New';padding:5px 10px;text-align: left;
}
.action #nxt{
    width:100%;
    position: fixed;
    top:0px;left:0px;z-index:999999;
}
#nxt a{
    font-weight: bold;
    color:#0C490C;
}

.action-float{
    position:fixed;top:-33px;left:0px;width:100%;z-index:999999;text-align:right;
    background: rgba(0,0,0,0.9);
}

.action .inside,
.action-float .inside{
    margin: 0px;
}

.action-float #serr{
    width:500px;
    float: left;
    margin: 4px;
    z-index:999999;
    margin-top:-50px;
    border:1px solid #800000;
}
.action-float #nxt{
    width:500px;
    float: left;
    margin: 4px;
    z-index:999999;
    margin-top:-40px;
    border:1px solid #008000;
}

.wpdm-accordion > div{
    padding:10px;
}

/*.wpdmlock {*/
    /*opacity:0;*/
/*}*/
/*.wpdmlock+label {*/

    /*width:16px;*/
    /*height:16px;*/
    /*vertical-align:middle;*/
/*}*/

.w3eden .panel{
    padding: 0 !important;
}
.w3eden .wpdmlock{
    margin: 0 5px  0 0 !important;
}
.wpdm-unchecked{
    display: inline-block;
    float: left;
    width: 21px;
    height: 21px;
    padding: 0px;
    margin: 0px;
    cursor: hand;
    padding: 3px;
    margin-top: -4px !important;
    background-image: url('<?php echo plugins_url('/download-manager/assets/images/CheckBox.png'); ?>');
    background-position: -21px 0px;
}
.wpdm-checked{
    display: inline-block;
    float: left;
    width: 21px;
    height: 21px;
    padding: 0px;
    margin: 0px;
    cursor: hand;
    padding: 3px;
    margin-top: -4px !important;
    background-image: url('<?php echo plugins_url('/download-manager/assets/images/CheckBox.png'); ?>');
    background-position: 0px 0px;
}
.cb-enable, .cb-disable, .cb-enable span, .cb-disable span { background: url(<?php echo plugins_url('/download-manager/images/switch.gif'); ?>) repeat-x; display: block; float: left; }
.cb-enable span, .cb-disable span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
.cb-enable span { background-position: left -90px; padding: 0 10px; }
.cb-disable span { background-position: right -180px;padding: 0 10px; }
.cb-disable.selected { background-position: 0 -30px; }
.cb-disable.selected span { background-position: right -210px; color: #fff; }
.cb-enable.selected { background-position: 0 -60px; }
.cb-enable.selected span { background-position: left -150px; color: #fff; }
.switch label { cursor: pointer; }
/*.switch input { display: none; }*/
p.field.switch{
    margin:0px;display:block;float:left;
}
.wpdm-accordion.w3eden .panel-default{
    margin-bottom: -2px !important;
    border-radius: 0;
}
    .wpdm-accordion.w3eden .panel-default .panel-heading{
        border-radius: 0;
    }
.w3eden .chzn-choices{
    background-image: none !important;
    border-radius: 3px;
}
 .w3eden .chzn-choices input{
     padding: 0;
     line-height: 10px;
 }
.w3eden .chzn-container-multi .chzn-drop{
    margin-top: 3px;
    border: 1px solid #5897fb !important;
    overflow: hidden;
    border-radius: 3px;
    padding: 10px 5px 10px 10px !important;
}
 .w3eden .chzn-container-multi .chzn-drop li{
     border-radius: 2px !important;
     margin-right: 10px !important;
 }
.w3eden .info.fa{
    border-radius: 500px;
    border: 0.05em solid #5897fb;
    color: #5897fb;
    width: 20px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    font-size: 10px;
}
.w3eden .table td{
    vertical-align: middle !important;
}
</style>
