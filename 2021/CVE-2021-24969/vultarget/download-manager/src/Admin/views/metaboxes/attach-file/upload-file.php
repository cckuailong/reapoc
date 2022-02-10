<?php
if(!defined("ABSPATH")) die("Shit happens!");
$afiles = maybe_unserialize(get_post_meta(get_the_ID(), "__wpdm_files", true));
if(!is_array($afiles)) $afiles = [];
$afiles = array_values($afiles);
$afile = wpdm_valueof($afiles, 0);
?>
<div class="w3eden">

    <input type="hidden" name="file[files][]" value="<?php  echo $afile; ?>" id="wpdmfile" />

    <div class="cfile" id="cfl" style="padding: 10px;border:2px solid #ddd;background: #ffffff;margin-bottom: 10px">
		<?php
		$filesize = "<em style='color: darkred'>( ".__("attached file is missing/deleted",'download-manager')." )</em>";
		$afile = trim($afile);
		$mfz = get_post_meta(get_the_ID(), '__wpdm_package_size', true);
		$url = false;
		if($afile !=''){
			if(substr_count($afile, "://") > 0){
				$fparts = parse_url($afile);
				$url = true;
				$hurl = strlen($fparts['host']) > 20 ? substr($fparts['host'], 0, 20)."..." : $fparts['host'];
				$filesize = "<span class='w3eden'><span class='text-primary ellipsis ttip' title='{$afile}'><i class='fa fa-link'></i> {$hurl}</span></span>";
			}
			else {
				$filesize = wpdm_file_size($afile);
			}

			if(strpos($afile, "#")) {
				$afile = explode("#", $afile);
				$afile = $afile[1];
			}


			?>
            <div class="media">
                <a href="#" id="dcf" title="Delete Current File" class="pull-right" style="font-size:24px">
                    <i class="fa fa-trash color-red"></i>
                </a>
                <div class="media-body"><strong><?php echo  basename($afile); ?></strong><br><span class="text-success"><?php echo (double)$mfz && !$url ?$mfz:$filesize; ?></span></div>
            </div>

		<?php } else echo "<span style='font-weight:bold;color:#ddd'>". __('No file uploaded yet!', 'download-manager')."</span>"; ?>
        <div style="clear: both;"></div>
    </div>


    <div id="upload">
        <div id="plupload-upload-ui" class="hide-if-no-js">
            <div id="drag-drop-area">
                <div class="drag-drop-inside" style="margin-top: 40px">
                    <p class="drag-drop-info" style="letter-spacing: 1px;font-size: 10pt"><?php _e('Drop file here'); ?><p>
                    <p>&mdash; <?php _ex('or', 'Uploader: Drop file here - or - Select File'); ?> &mdash;</p>
                    <p class="drag-drop-buttons">
                        <button id="plupload-browse-button" type="button" class="btn btn-sm btn-success wpdm-whatsapp"><i class="fa fa-file"></i> <?php esc_attr_e('Select File'); ?></button><br/>
                        <small style="margin-top: 15px;display: block">[ Max: <?php echo get_option('__wpdm_chunk_upload',0) == 1?'No Limit':(int)(wp_max_upload_size()/1048576).' MB'; ?> ]</small>
                    </p>
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
			'url'                 => admin_url('admin-ajax.php'),
			'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
			'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
			'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => WPDM()->fileSystem->getAllowedFileTypes(false) )),
			'multipart'           => true,
			'urlstream_upload'    => true,
			// additional post data to send to our ajax hook
			'multipart_params'    => array(
				'_ajax_nonce' => wp_create_nonce(NONCE_KEY),
				'type'          => 'package_attachment',
				'package_id'          => get_the_ID(),
				'action'      => 'wpdm_admin_upload_file',            // the ajax action name
			),
		);

		if(get_option('__wpdm_chunk_upload',0) == 1){
			$plupload_init['chunk_size'] = (int)get_option('__wpdm_chunk_size', 1024).'kb';
			$plupload_init['max_retries'] = 3;
		} else
			$plupload_init['max_file_size'] = wp_max_upload_size().'b';

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

                            file.name.replace(/</ig, "&lt;") + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
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

                    jQuery('#' + file.id ).remove();
                    var d = new Date();
                    var ID = d.getTime();
                    response = response.response;
                    if(response == -3)
                        jQuery('#cfl').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> &nbsp; <?php _e('Invalid File Type!','download-manager');?></span>');
                    else if(response == -2)
                        jQuery('#cfl').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> &nbsp; <?php _e('Unauthorized Access!','download-manager');?></span>');
                    else {
                        var data = response.split("|||");
                        jQuery('#wpdmfile').val(data[1]);
                        jQuery('#cfl').html('<div class="media"><a href="#" class="pull-right ttip" id="dcf" title="<?php _e('Delete Current File', 'download-manager');?>" style="font-size: 24px"><i class="fa fa-trash color-red"></i></a><div class="media-body"><strong>' + data[1] + '</strong><br/>'+data[2]+' </div></div>').slideDown();
                    }
                });
            });

        </script>
        <div id="filelist"></div>

        <div class="clear"></div>
    </div>


    <script>
        function wpdm_html_compile(html, dataset){
            return html.replace(/{{(.*?)}}/g,
                function (...match) {
                    return dataset[match[1]];
                });
        }
        function wpdm_attach_file(file)
        {
            jQuery('#wpdmfile').val(file.filepath);
            jQuery('#cfl').html('<div class="media"><a href="#" class="pull-right ttip" id="dcf" title="<?php _e('Delete Current File', 'download-manager');?>" style="font-size: 24px"><i class="fa fa-trash color-red"></i></a><div class="media-body"><strong>' + file.filetitle + '</strong><br/>&mdash;</div></div>').slideDown();


        }

        jQuery(function($){


            $('body').on('click', '#dcf', function(){
                if(!confirm('<?php _e('Are you sure?','download-manager'); ?>')) return false;
                $('#wpdmfile').val('');
                $('#cfl').html('<?php _e('<div class="w3eden"><div class="text-danger"><i class="fa fa-check-circle"></i> Removed!</div></div>','download-manager'); ?>');
            });




        });

    </script>


</div>
