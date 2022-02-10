<?php
$model = $this->viewData['creative'];

?>


<script type="text/javascript">

	jQuery(document).ready(function() {
		jQuery("#dialog-preview").dialog({
			resizable: true,
			width: 640,
			height: 480,
			closeOnEscape: true,
			modal: true,
			draggable: true,
			autoOpen: false,
			buttons: [ {
				text : '<?php _e( 'OK', 'affiliates-manager' ) ?>',
			  	click : function() { jQuery(this).dialog('close'); }
			} ]
		});

		jQuery("#previewButton").click(function() {
			jQuery("#dialog-preview").dialog('open');
		});

	});
</script>


<div id="dialog-preview" title="<?php _e( 'Preview', 'affiliates-manager' ) ?>" style="display: none">
	<?php echo $this->viewData['htmlPreview']?>
</div>

<?php
echo '<div class="aff-wrap">';
include WPAM_BASE_DIRECTORY . "/html/affiliate_cp_nav.php";
?>

<div class="wrap">

	 <h2><?php _e( 'Creative:', 'affiliates-manager' ) ?> <?php echo $model->name?></h2>

	 <p align="center"><button type="button" name="preview" id="previewButton" class="button-secondary"><?php _e( 'Preview', 'affiliates-manager' ) ?></button></p>

	 <h2><?php _e( 'Your Affiliate-Specific HTML snippet', 'affiliates-manager' ) ?></h2>
         <textarea rows="5" class="wpam-creative-code"><?php echo htmlspecialchars($this->viewData['htmlSnippet']); ?></textarea>

<p/>
	<?php if ($model->type == 'text') { ?>

	<table class="pure-table wpam-creative-detail">
		<thead>
		<tr>
			<th colspan="2"><?php _e( 'Text Link Properties', 'affiliates-manager' ) ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td width="150"><?php _e( 'Link Text', 'affiliates-manager' ) ?></td>
			<td><?php echo $model->linkText?></td>
		</tr>
		<tr>
			<td><?php _e( 'Alt Text', 'affiliates-manager' ) ?></td>
			<td>
				<?php echo $model->altText?>
			</td>
		</tr>
		</tbody>
	</table>

	<?php } else if ($model->type == 'image') { ?>
	<table class="pure-table wpam-creative-detail">
		<thead>
		<tr>
			<th colspan="2"><?php _e( 'Image Properties', 'affiliates-manager' ) ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td width="150"><?php _e( 'Image', 'affiliates-manager' ) ?></td>
			<td><img src="<?php
                                $img_url = '';
                                if(isset($model->image) && !empty($model->image)){  //new way of retrieving an image URL
                                    $img_url = $model->image;
                                }
                                else if(isset($model->imagePostId) && !empty($model->imagePostId)){  //old way for backwards compatiblity
                                    $img_url = wp_get_attachment_url($model->imagePostId);
                                }
				//$url = wp_get_attachment_image_src($model->imagePostId);
				echo $img_url;?>" style="max-width: 200px; max-height: 200px;"/></td>
		</tr>
		<tr>
			<td><?php _e( 'Alt Text', 'affiliates-manager' ) ?></td>
			<td><?php echo $model->altText?></td>
		</tr>
		</tbody>
	</table>
	<?php } ?>
</div>
</div>