
<style type="text/css">
	.ui-progressbar-value {
		background-image: url(<?php echo WPAM_URL . "/images/pbar-ani.gif"?>);
		height: 22px;
	}

</style>
<script type="text/javascript">
	jQuery(function(){

		var deleteIdClicked;

		function updateFilters()
		{
			var statusVal = jQuery("#ddFilterStatus").val();
			window.location = '<?php echo admin_url( 'admin.php?page=wpam-creatives&statusFilter=' ); ?>' + statusVal;
		}

		jQuery("#dialog-loading").dialog({
			resizable: false,
			height: 20,
			width: 500,
			closeOnEscape: false,

			modal:true,
			draggable:false,
			autoOpen:false
		});

		jQuery("#dialog-error").dialog({
			resizable: false,
			height: 100,
			autoOpen: false,
			modal: true,
			draggable: false,
			buttons: [ {
				  text : '<?php _e( 'OK', 'affiliates-manager' ) ?>',
				  click : function() { jQuery(this).dialog('close'); }
			} ]
		});

		function doJsonRequest(args, successCallback)
		{
			args.action = 'wpam-ajax_request';
			jQuery.getJSON( ajaxurl, args, successCallback);
			showLoad();
		}
		function showLoad()
		{
			jQuery("#dialog-loading").dialog("open");
			jQuery("#progressbar").show();

			jQuery(".ui-dialog-titlebar").hide();
		}

		jQuery("#dialog-confirm-delete").dialog({
			autoOpen: false,
			resizable: false,
			height: 200,
			modal: true,
			buttons: [
				{
				  text : '<?php _e( 'Yes, delete this creative', 'affiliates-manager' ) ?>',
				  click : function() {
					jQuery(this).dialog('close');
					jQuery("#dialog-loading").dialog('open');
					jQuery(".ui-dialog-titlebar").hide();
					jQuery("#progressbar").show();

					doJsonRequest({
						handler: 'deleteCreative',
						creativeId: deleteIdClicked
						},
						function(data) {
							if (data['status'] == 'OK')
							{
								location.reload();
							}
							else
							{
								jQuery("#errorMsg").html(data['message']);
								jQuery("#dialog-error").dialog('open');
								jQuery("#dialog-loading").dialog('close');
							}
						})
					}
				},
				{
				  text : '<?php _e( 'Cancel', 'affiliates-manager' ) ?>',
				  click : function() {
					jQuery(this).dialog('close');
				  }
				}
			] }
		);

		jQuery("[id^=delete-button]").click(function() {
			deleteIdClicked = jQuery(this).attr('id').split('-')[2];
			jQuery("#dialog-confirm-delete").dialog('open');
		});

		jQuery("#ddFilterStatus").change(function(){
			updateFilters();
		});
	});
</script>
<div id="dialog-error" title="Error" style="display: none">
	 <p><?php _e( 'ERROR:', 'affiliates-manager' ) ?> <span id="errorMsg"></span></p>
</div>

<div id="dialog-loading" style="display:none">
	<div style="text-align: center"><?php _e( 'Updating, please wait ...', 'affiliates-manager' ) ?></div><br />
	<div id="progressbar" class="ui-progressbar-value">

	</div>
</div>


<div id="dialog-confirm-delete" title="<?php _e( 'Delete Creative?', 'affiliates-manager' ) ?>">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php _e( 'This creative will be deleted. Are you sure?', 'affiliates-manager' ) ?></p>
</div>


<div class="wrap">
<h2><?php _e( 'My Creatives', 'affiliates-manager' ) ?></h2>
<h3></h3>

<table class="widefat">
	<thead>
	<tr>
		<th><?php _e( 'Filter', 'affiliates-manager' ) ?></th>
	</tr>
	</thead>
	<tr>
		<td>
			<label for="ddFilterStatus"><?php _e( 'Status:', 'affiliates-manager' ) ?></label>
			<select id="ddFilterStatus">

				<?php foreach ($this->viewData['statusFilters'] as $key => $val) { ?>

			<option value="<?php echo $key?>" <?php echo (isset($this->viewData['request']['statusFilter']) && $this->viewData['request']['statusFilter'] === $key ? 'selected="selected"' : '')?>><?php echo $val?></option>

				<?php } ?>

			</select>
		</td>
	</tr>
</table>

	<br/>


	<div id="buttonsDiv" style="margin-bottom: 10px; margin-top: 10px;">
		<a href="<?php echo admin_url( 'admin.php?page=wpam-creatives&action=new' ) ?>" class="button-primary"><?php _e( 'Create New', 'affiliates-manager' ) ?></a>
	</div>

	<br/>

	<table class="widefat">
		<thead>
			<tr>
				<th width="150"><?php _e( 'Actions', 'affiliates-manager' ) ?></th>
				<th width="50"><?php _e( 'ID', 'affiliates-manager' ) ?></th>
				<th width="75"><?php _e( 'Status', 'affiliates-manager' ) ?></th>
				<th width="100"><?php _e( 'Created', 'affiliates-manager' ) ?></th>
				<th width="75"><?php _e( 'Type', 'affiliates-manager' ) ?></th>
				<th><?php _e( 'Name', 'affiliates-manager' ) ?></th>

			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->viewData['creatives'] as $creative) {?>
			<tr>
				<td style="white-space: nowrap;">
					<a class="button-secondary" href="<?php echo admin_url( "admin.php?page=wpam-creatives&action=viewDetail&creativeId={$creative->creativeId}" ) ?>"><?php _e( 'View', 'affiliates-manager' ) ?></a>
					&nbsp; <a id="delete-button-<?php echo $creative->creativeId?>" class="button-secondary delete-button"><?php _e( 'Delete', 'affiliates-manager' ) ?></a>
				</td>
				<td><?php echo $creative->creativeId?></td>
				<td><?php echo $creative->status?></td>
				<td><?php echo date("m/d/Y", $creative->dateCreated)?></td>
				<td><?php echo $creative->type?></td>
				<td><?php echo $creative->name?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>