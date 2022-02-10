<script type="text/javascript">
	jQuery(function($) {
		function updateTypeDivs()
		{
			var setting = $("#ddType").val();
			if (setting == 'image')
			{
				$("#imageDiv").show();
				$("#textLinkDiv").hide();
			}
			else if (setting == 'text')
			{
				$("#imageDiv").hide();
				$("#textLinkDiv").show();
			}
			else
			{
				$("#imageDiv").hide();
				$("#textLinkDiv").hide();
			}
		}
		
		$("#ddType").change(updateTypeDivs);

		updateTypeDivs();

		var dialog = {
		  resizable: false,
		  height: 300,
		  width: 500,
		  autoOpen: false,
		  modal: true,
		  draggable: false,
		  buttons: [ {
			  text : '<?php _e( 'OK', 'affiliates-manager' ) ?>',
			  click : function() { $(this).dialog('close'); }
			} ]
		};
	
		$("#image_help").dialog(dialog);

		$("#imageInfo").click(function() {
			$("#image_help").dialog('open');
		});
                
                //image upload handler
                var wpam_media_prev_setting = wp.media.controller.Library.prototype.defaults.contentUserSetting;
                function wpam_attach_media_uploader(key) {
                    var wpam_frame;
                    var libType = 'image';
                    jQuery('#' + key + '_button').click(function () {
                        wp.media.controller.Library.prototype.defaults.contentUserSetting = false;
                        wpam_frame = wp.media({
                            title: '<?php _e( 'Upload a File or Select from Media Library', 'affiliates-manager' ) ?>',
                            button: {
                                text: '<?php _e( 'Insert', 'affiliates-manager' ) ?>',
                            },
                            multiple: false,
                            library: {type: libType},
                        });
                        text_element = jQuery('#' + key).attr('name');
                        button_element = jQuery('#' + key + '_button').attr('name');

                        wpam_frame.open();
                        wp.media.controller.Library.prototype.defaults.contentUserSetting = wpam_media_prev_setting;
                        wpam_frame.on('select', function () {
                            var attachment = wpam_frame.state().get('selection').first().toJSON();
                            jQuery('#' + text_element).val(attachment.url);

                        });
                        return false;
                    });
                }
                wpam_attach_media_uploader('image_url');
	});
</script>

<div id="image_help" style="display: none;">
        <p>
<?php _e('This list contains images from the media library. If you upload a new image it is added to the media library and you can reuse images on multiple creatives by selecting it from this list. If a new image file is added, it will be uploaded and will override the currently selected media library image for this creative link. However, the old image will still remain in the media library for future use.', 'affiliates-manager' ) ?>
       </p>
</div>

<div class="wrap">
	 <h2><?php echo $this->viewData['request']['action'] == 'edit' ? __( 'Edit', 'affiliates-manager' ) : __( 'New', 'affiliates-manager' ) ?> <?php _e( 'Creative', 'affiliates-manager' ) ?></h2>

<?php
require_once WPAM_BASE_DIRECTORY . "/html/widget_form_errors_panel.php";
$home_url = home_url('/');
$aff_landing_page = get_option(WPAM_PluginConfig::$AffLandingPageURL);
if(isset($aff_landing_page) && !empty($aff_landing_page)){
    $home_url = trailingslashit($aff_landing_page);
}
?>
	
<form method="post" action="admin.php?page=wpam-creatives" enctype="multipart/form-data">
        <?php wp_nonce_field('wpam_save_creatives_nonce'); ?>
	<input type="hidden" name="action" value="<?php echo $this->viewData['request']['action']?>" />
	<input type="hidden" name="post" value="true"/>
	<?php if ($this->viewData['request']['action'] === 'edit') { ?>
		<input type="hidden" name="creativeId" value="<?php echo $this->viewData['request']['creativeId']?>" />
	<?php } ?>

	<div id="mainForm">
		<table class="widefat">
			<thead>
			<tr>
				<th colspan="2"><?php _e( 'General', 'affiliates-manager' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					<label for="txtName">
						<?php _e( 'Name', 'affiliates-manager' ) ?>
					</label>
				</td>
				<td>
					<input type="text" id="txtName" name="txtName" size="30" value="<?php echo isset($this->viewData['request']['txtName']) ? $this->viewData['request']['txtName'] : ''; ?>" />
				</td>
			</tr>

			<tr>
				<td>
					<label for="txtSlug"><?php _e( 'Landing Page', 'affiliates-manager' ) ?></label>
				</td>
				<td id="landing-page-slug">
					<?php echo esc_url($home_url) ?><input type="text" id="txtSlug" name="txtSlug" size="30" value="<?php echo isset($this->viewData['request']['txtSlug']) ? $this->viewData['request']['txtSlug'] : ''; ?>" />
				</td>
			</tr>			
			<tr>
				<td width="200"><label for="ddType"><?php _e( 'Type', 'affiliates-manager' ) ?></label></td>
				<td>
					<select id="ddType" name="ddType" style="width:150px">
						<?php foreach ($this->viewData['creativeTypes'] as $value => $name) { ?>
				<option value="<?php echo $value?>" <?php echo isset($this->viewData['request']['ddType']) && $this->viewData['request']['ddType'] === $value ? 'selected="selected"' : ''; ?>><?php echo $name?></option>
						<?php } ?>
					</select>
				</td>
			</tr>

			</tbody>
		</table>
	</div>

	<br/>

	<div id="imageDiv" style="display: none;">
		<table class="widefat">
			<theaD>
			<tr>
				<th colspan="2"><?php _e( 'Image Parameters', 'affiliates-manager' ) ?></th>
			</tr>
			</theaD>
			<tbody>
                        <?php
                            $img_url = '';
                            if(isset($this->viewData['request']['image_url']) && !empty($this->viewData['request']['image_url'])){  //new way of retrieving an image URL
                                $img_url = $this->viewData['request']['image_url'];
                            }
                            else if(isset($this->viewData['request']['ddFileImage']) && !empty($this->viewData['request']['ddFileImage'])){  //old way for backwards compatiblity
                                $img_url = wp_get_attachment_url($this->viewData['request']['ddFileImage']);
                            } 
                        ?>
                        <tr valign="top">
                            <th scope="row"><label for="image_url"><?php _e( 'Image URL', 'affiliates-manager' ) ?></label></th>
                            <td><input name="image_url" type="text" id="image_url" value="<?php echo $img_url?>" size="100" />
                                <input type="button" id="image_url_button" name="image_url_button" class="button rbutton" value="<?php _e( 'Upload File', 'affiliates-manager' ) ?>" />
                                <p class="description"><?php _e( 'The URL of the image to be used for the creative.', 'affiliates-manager' ) ?></p>
                            </td>
                        </tr>
			<tr>
				<td width="200">
					<label for="txtImageAltText">
						<?php _e( 'Alt Text', 'affiliates-manager' ) ?>
					</label>
				</td>
				<td>
					<input id="txtImageAltText" name="txtImageAltText" type="text" size="40" value="<?php echo isset($this->viewData['request']['txtImageAltText']) ? $this->viewData['request']['txtImageAltText'] : ''; ?>" />
				</td>
			</tr>
			</tbody>
		</table>

	</div>

	<div id="textLinkDiv" style="display: none;">
		<table class="widefat">
			<thead>
			<tr>
				<th colspan="2"><?php _e( 'Text Link Parameters', 'affiliates-manager' ) ?></th>
			</tr>
			</thead>

			<tbody>
			<tr>
				<td width="200">
					<label for="txtLinkText">
						<?php _e( 'Link Text', 'affiliates-manager' ) ?>
					</label>
				</td>
				<td>
					<input id="txtLinkText" name="txtLinkText" type="text" size="30" value="<?php echo isset($this->viewData['request']['txtLinkText']) ? $this->viewData['request']['txtLinkText'] : ''; ?>"/>
				</td>
			</tr>

			<tr>
				<td width="200">
					<label for="txtAltText">
						<?php _e( 'Alt Text', 'affiliates-manager' ) ?>
					</label>
				</td>
				<td>
					<input id="txtAltText" name="txtAltText" type="text" size="40" value="<?php echo isset($this->viewData['request']['txtAltText']) ? $this->viewData['request']['txtAltText'] : ''; ?>"/>
				</td>
			</tr>
			</tbody>
		</table>

	</div>
<br />
	<div>
	<input class="button-primary" type="submit" id="btnSubmit" name="btnSubmit" value="<?php _e( 'Save Creative', 'affiliates-manager' ) ?>" />	
	</div>


</form>

</div>