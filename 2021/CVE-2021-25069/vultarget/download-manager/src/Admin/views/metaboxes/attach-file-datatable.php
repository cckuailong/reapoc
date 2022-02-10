<div id="ftabs">
<ul>
    <li><a href="#upload"><?php echo __( "Upload" , "download-manager" ); ?></a></li>
    <?php  if(current_user_can('access_server_browser')){ ?>
    <li><a href="#browse"><?php echo __( "Browse" , "download-manager" ); ?></a></li>
    <?php } ?>
    <li><a href="#remote"><?php echo __( "URL" , "download-manager" ); ?></a></li>
</ul>

<div id="upload">
<div id="plupload-upload-ui" class="hide-if-no-js">
        <div id="drag-drop-area">
            <div class="drag-drop-inside">
                <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
            </div>
        </div>
    </div>

    <?php

    $plupload_init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'plupload-browse-button',
        'container'           => 'plupload-upload-ui',
        'drop_element'        => 'drag-drop-area',
        'file_data_name'      => 'package_file',
        'multiple_queues'     => true,
        'max_file_size'       => wp_max_upload_size().'b',
        'url'                 => admin_url('admin-ajax.php'),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
        'multipart'           => true,
        'urlstream_upload'    => true,

        // additional post data to send to our ajax hook
        'multipart_params'    => array(
            '_ajax_nonce' => wp_create_nonce('wpdm_admin_upload_file'),
            'action'      => 'wpdm_admin_upload_file',            // the ajax action name
        ),
    );

    // we should probably not apply this filter, plugins may expect wp's media uploader...
    $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

    <script type="text/javascript">

        jQuery(document).ready(function($){

            // create the uploader and pass the config from above
            var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

            // checks if browser supports drag and drop upload, makes some css adjustments if necessary
            uploader.bind('Init', function(up){
                var uploaddiv = jQuery('#plupload-upload-ui');

                if(up.features.dragdrop){
                    uploaddiv.addClass('drag-drop');
                    jQuery('#drag-drop-area')
                        .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                        .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                }else{
                    uploaddiv.removeClass('drag-drop');
                    jQuery('#drag-drop-area').unbind('.wp-uploader');
                }
            });

            uploader.init();

            // a file was added in the queue
            uploader.bind('FilesAdded', function(up, files){
                //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);



                plupload.each(files, function(file){
                    jQuery('#filelist').append(
                        '<div class="file" id="' + file.id + '"><b>' +

                            file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                            '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
                });

                up.refresh();
                up.start();
            });

            uploader.bind('UploadProgress', function(up, file) {

                jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });


            // a file was uploaded
            uploader.bind('FileUploaded', function(up, file, response) {

                // this is your ajax response, update the DOM with it or something...
                //console.log(response);
                //response
                jQuery('#' + file.id ).remove();
                var d = new Date();
                var ID = d.getTime();
                response = response.response;
                var nm = response;
                if(response.length>20) nm = response.substring(0,7)+'...'+response.substring(response.length-10);
                //jQuery('#currentfiles table.widefat').append("<tr id='"+ID+"' class='cfile'><td><input type='hidden' id='in_"+ID+"' name='files[]' value='"+response+"' /><img id='del_"+ID+"' src='<?php echo plugins_url(); ?>/download-manager/images/minus.png' rel='del' align=left /></td><td>"+response+"</td><td width='40%'><input style='width:99%' type='text' name='file[fileinfo]["+response+"][title]' value='"+response+"' onclick='this.select()'></td><td><input size='10' type='text' id='indpass_"+ID+"' name='file[fileinfo]["+response+"][password]' value=''> <img style='cursor: pointer;float: right;margin-top: -3px' class='genpass' onclick=\"return generatepass('indpass_"+ID+"')\" title='Generate Password' src=\"<?php echo plugins_url('download-manager/images/generate-pass.png'); ?>\" /></td></tr>");
                jQuery('#wpdm-files').dataTable().fnAddData( [
                    "<input type='hidden' id='in_"+ID+"' name='file[files]["+ID+"]' value='"+response+"' /><i id='del_"+ID+"' class='fa fa-trash-o action-ico text-danger' rel='del'></i>",
                    response,
                    "<input class='form-control input-sm' type='text' name='file[fileinfo]["+ID+"][title]' value='"+response+"' onclick='this.select()'>",
                    "<div class='input-group'><input size='10' class='form-control input-sm' type='text' id='indpass_"+ID+"' name='file[fileinfo]["+ID+"][password]' value=''><span class='input-group-btn'><button class='genpass btn btn-secondary btn-sm' type='button' onclick=\"return generatepass('indpass_"+ID+"')\" title='Generate Password'><i class='fa fa-key'></i></button>"
                ] );



                jQuery('#wpdm-files tbody tr:last-child').attr('id',ID).addClass('cfile');

                jQuery("#wpdm-files tbody").sortable();

                jQuery('#'+ID).fadeIn();




            });

        });

    </script>
    <div id="filelist"></div>

    <div class="clear"></div>
</div>

<div id="browse">
    <?php if(current_user_can('access_server_browser')) wpdm_file_browser(); ?>
</div>
<div id="remote" class="w3eden">
    <div class="input-group"><input type="url" id="rurl" class="form-control" placeholder="Insert URL"><span class="input-group-btn"><button type="button" id="rmta" class="btn btn-secondary"><i class="fa fa-plus-circle"></i></button></span></div>
</div>
</div>

<script>
jQuery(function(){
        jQuery( "#ftabs" ).tabs();

        jQuery('#rmta').click(function(){
            var d = new Date();
            var ID = d.getTime();
        var file = jQuery('#rurl').val();
        var filename = file;
            jQuery('#rurl').val('');
            if(file == ''){
                alert("Invalid url");
                return false;
            }

            jQuery('#wpdm-files').dataTable().fnAddData( [
                "<input type='hidden' id='in_"+ID+"' class='fa' name='file[files]["+ID+"]' value='"+file+"' /><i id='del_"+ID+"' class='fa fa-trash-o action-ico text-danger' rel='del'></i>",
                file,
                "<input class='form-control input-sm' type='text' name='file[fileinfo]["+ID+"][title]' value='"+file+"' onclick='this.select()'>",
                "<div class='input-group'><input size='10' class='form-control input-sm' type='text' id='indpass_"+ID+"' name='file[fileinfo]["+ID+"][password]' value=''><span class='input-group-btn'><button class='genpass btn btn-secondary btn-sm' type='button' onclick=\"return generatepass('indpass_"+ID+"')\" title='Generate Password'><i class='fa fa-key'></i></button>"
            ] );
            jQuery('#wpdm-files tbody tr:not(.dfile):not(.cfile)').attr('id',ID).addClass('cfile');


            jQuery("#wpdm-files tbody").sortable();

        jQuery('#'+ID).fadeIn();



    });

});

</script>
