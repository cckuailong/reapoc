


<div id="package-icons" class="tab-pane">
    <?php /* if(current_user_can('manage_options')){ ?>
    <div id="icon-plupload-upload-ui" class="hide-if-no-js">
        <div id="icon-drag-drop-area">
            <div class="icon-drag-drop-inside">
                <input id="icon-plupload-browse-button" type="button" class="button-secondary" value="<?php echo __( "Upload New Icon" , "download-manager" ); ?>" class="btn" />
            </div>
        </div>
    </div>

    <?php

    $plupload_init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'icon-plupload-browse-button',
        'container'           => 'icon-plupload-upload-ui',
        'drop_element'        => 'icon-drag-drop-area',
        'file_data_name'      => 'icon-async-upload',
        'multiple_queues'     => true,
        'url'                 => admin_url('admin-ajax.php'),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __( "Allowed Files" , "download-manager" ), 'extensions' => 'png, jpg, gif')),
        'multipart'           => true,
        'urlstream_upload'    => true,

        // additional post data to send to our ajax hook
        'multipart_params'    => array(
            '_ajax_nonce' => wp_create_nonce('icon-upload'),
            'action'      => 'icon_upload',            // the ajax action name
        ),
    );

    // we should probably not apply this filter, plugins may expect wp's media uploader...
    $plupload_init = apply_filters('plupload_init', $plupload_init);

    ?>

    <script type="text/javascript">

        jQuery(document).ready(function($){

            // create the uploader and pass the config from above
            var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

            // checks if browser supports drag and drop upload, makes some css adjustments if necessary
            uploader.bind('Init', function(up){
                var uploaddiv = jQuery('#icon-plupload-upload-ui');

                if(up.features.dragdrop){
                    uploaddiv.addClass('drag-drop');
                    jQuery('#icon-drag-drop-area')
                        .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                        .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                }else{
                    uploaddiv.removeClass('drag-drop');
                    jQuery('#icon-drag-drop-area').unbind('.wp-uploader');
                }
            });

            uploader.init();

            // a file was added in the queue
            uploader.bind('FilesAdded', function(up, files){
                //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

                jQuery('#icon-loading').slideDown();


                plupload.each(files, function(file){
                    jQuery('#icon-filelist').html(
                        '<div class="file" id="' + file.id + '"><b>' +

                            file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                            '<div class="fileprogress"></div></div>');
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
                var jres = jQuery.parseJSON(response.response);
                console.log(jres);
                //response
                jQuery('#' + file.id ).remove();
                var d = new Date();
                var ID = d.getTime();
                jQuery('#icon-loading').hide();
                jQuery('#w-icons').prepend("<img class='wdmiconfile' id='"+jres.fid+"' src='"+jres.url+"' style='padding:5px; margin:1px; float:left; border:#fff 2px solid;height: 32px;width:auto; ' /><input rel='wdmiconfile' style='display:none' type='radio'  name='file[icon]'  class='checkbox'  value='"+jres.rpath+"' ></label>");


            });

        });

    </script>
    <?php } */ ?>
    <div class="w3eden"><input style="background: url(<?php echo esc_url(get_post_meta($post->ID,'__wpdm_icon', true)); ?>) no-repeat;background-size: 24px;padding-left: 40px;background-position:8px center;" id="wpdmiconurl" placeholder="<?php _e( "Icon URL" , "download-manager" ); ?>" value="<?php echo esc_url(get_post_meta($post->ID,'__wpdm_icon', true)); ?>" type="text"  name="file[icon]"  class="form-control input-lg" ></div>
    <br clear="all" />
    <?php
    $path = WPDM_BASE_DIR."assets/file-type-icons/";
    $_upload_dir = wp_upload_dir();
    $_upload_basedir = $_upload_dir['basedir'];
    $c_path = $_upload_basedir.'/wpdm-file-type-icons/';
    $c_url = $_upload_dir['baseurl'].'/wpdm-file-type-icons/';
    $scan = scandir( $path );
    $k = 0;
    $fileinfo = array();
    foreach( $scan as $v )
    {
        if( $v=='.' or $v=='..' or is_dir($path.$v) ) continue;

        $fileinfo[$k]['file'] = 'download-manager/assets/file-type-icons/'.$v;
        $fileinfo[$k]['name'] = $v;
        $k++;
    }

    if(file_exists($c_path)) {
        $c_scan = scandir( $c_path );
        if(is_array($c_scan)) {
            foreach ($c_scan as $v) {
                if ($v == '.' or $v == '..' or is_dir($path . $v)) continue;

                $fileinfo[$k]['file'] = $c_url . $v;
                $fileinfo[$k]['name'] = $v;
                $k++;
            }
        }
    }



    ?>
    <div id="w-icons">

        <?php
        $img = array('jpg','gif','jpeg','png', 'svg');
        foreach($fileinfo as $index=>$value): $tmpvar = explode(".",$value['file']); $ext = strtolower(end($tmpvar)); if(in_array($ext,$img)): ?>
            <label>
                <img class="wdmiconfile" id="<?php echo !strstr($value['file'], '://')?md5(plugins_url().'/'.$value['file']):md5($value['file']); ?>" src="<?php  echo !strstr($value['file'], '://')?plugins_url().'/'.esc_attr($value['file']):esc_url($value['file']); ?>" alt="<?php echo $value['name'] ?>" style="padding:5px; margin:1px; float:left; border:#fff 2px solid;height: 32px;width:auto; " />
                </label>
        <?php endif; endforeach; ?>
    </div>
    <script type="text/javascript">
        //border:#CCCCCC 2px solid

        <?php if(isset($_GET['action'])&&$_GET['action']=='edit'){ ?>
        jQuery('#<?php echo md5(get_post_meta($post->ID,'__wpdm_icon', true)) ?>').addClass("iactive");
        <?php } ?>
        jQuery('body').on('click', 'img.wdmiconfile',function(){
            jQuery('#wpdmiconurl').val(jQuery(this).attr('src'));
            jQuery('#wpdmiconurl').css('background-image','url('+jQuery(this).attr('src')+')');
            jQuery('img.wdmiconfile').removeClass('iactive');
            jQuery(this).addClass('iactive');



        });
        jQuery('#wpdmiconurl').on('change', function(){
            jQuery('#wpdmiconurl').css('background-image','url('+jQuery(this).val()+')');
        });




    </script>
    <style>

        .iactive{
            -moz-box-shadow:    inset 0 0 10px #5FAC4F;
            -webkit-box-shadow: inset 0 0 10px #5FAC4F;
            box-shadow:         inset 0 0 10px #5FAC4F;
            background: #D9FCD1;
        }
    </style>

    <div class="clear"></div>
</div>

