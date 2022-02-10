<script type="text/javascript" >
jQuery(function($) {
	var messageEditorCurrentMessageContent;
	var messageEditorCurrentMessageName;

	$(".wpam-js-edit-message").click(function() {
		messageEditorCurrentMessageContent = $(this).siblings('input[type=hidden][name*=content]');
		messageEditorCurrentMessageName = $(this).siblings('input[type=hidden][name*=name]');

		$("#messageEditorTextArea").text(messageEditorCurrentMessageContent.val());
		$("#messageEditorUse").text($(this).siblings('input[type=hidden][name*=use]').val());
		$("#messageEditorStatus").html('Editing: '+messageEditorCurrentMessageName.val());
		$("#messageEditorDialog").dialog('open');
	});

	$("#messageEditorDialog").dialog({
		resizable: true,
		height: 480,
		width: 640,
		modal: true,
		draggable: true,
		autoOpen: false,
		resize: function (event, ui) {
			$("#messageEditorTextArea").css('height', ui.size.height-(480-225));
		},
		buttons: [ {
				  text : 'Cancel',
				  click : function() { jQuery(this).dialog('close'); }
			 }, {
				  text : 'OK',
				  click : function() {
				$(this).dialog('close');
				messageEditorCurrentMessageContent.val($("#messageEditorTextArea").val());
				messageEditorCurrentMessageContent
					.closest('tr')
					.children('td')
					.eq(1)
					.css('color', 'red')
					.css('font-weight', 'bold')
					.children('span')
					.show();
				$("#messageEditorModifiedWarning").slideDown(500);
			}
		} ]
	});
});
</script>

<style type="text/css">
	.dragging {
		background-color: #efe;
	}
	.dragging td {

	}
	.showDragHandle {
		background-image: url(<?php echo WPAM_URL."/images/move_grip_icon.jpg"?>);
		background-repeat: no-repeat;
		background-position: center center;
		cursor: move;
	}
</style>


<p>
	<?php _e( 'These settings allow you to control text that is displayed to affiliates at various points in the affiliate plug-in, as well as e-mails that are sent.', 'affiliates-manager' ) ?>
</p>

<div id="messageEditorDialog" style="display: none">
	<div id="messageEditorStatus" style="font-weight: bold"></div>
	<div style="margin-top: 6px"><?php _e( 'Use:', 'affiliates-manager' ) ?></div>
	<div id="messageEditorUse" style="border: 1px solid #e0e0e0; height: 50px; overflow:hidden;">

	</div>
	<div style="margin-top: 6px"><?php _e( 'Content:', 'affiliates-manager' ) ?></div>
	<textarea id="messageEditorTextArea" name="messageEditorTextArea" style="width: 100%; height: 225px"></textarea>

</div>
<div class="postbox">
<div class="inside">
<div>
	<table class="widefat">
		<thead>
		<tr>
			<th width="50"></th>
			<th width="250"><?php _e( 'Name', 'affiliates-manager' ) ?></th>
			<th width="85"><?php _e( 'Type', 'affiliates-manager' ) ?></th>
			<th width="300"><?php _e( 'Use', 'affiliates-manager' ) ?></th>
			<th><?php _e( 'Content', 'affiliates-manager' ) ?></th>
		</tr>
		</thead>
		<?php foreach ($this->viewData['messages'] as $message) { ?>
		<tr>
			<td>
				<img class="wpam-js-edit-message wpam-action-icon wpam-edit-icon" src="<?php echo $ICON_EDIT?>" />
				<input type="hidden" name="messages[<?php echo $message->name?>][content]" value="<?php echo htmlentities($message->content, ENT_COMPAT, 'UTF-8')?>" />
				<input type="hidden" name="messages[<?php echo $message->name?>][name]" value="<?php echo $message->name?>" />
				<input type="hidden" name="messages[<?php echo $message->name?>][modified]" value="0" />
				<input type="hidden" name="messages[<?php echo $message->name?>][use]" value="<?php echo $message->use?>" />
			</td>
			<td style="white-space: nowrap"><?php echo $message->name?><span style="display: none"> *</span>
			</td>
			<td style="white-space: nowrap;">
				<img src="<?php
					if ($message->type == 'web')
						echo WPAM_URL . "/images/icon_world.gif";
					else if ($message->type == 'email')
						echo WPAM_URL . "/images/icon_mail.gif";
				?>" style="float:left; margin-right: 4px;" />
				<?php echo strtoupper($message->type)?>
			</td>
			<td><?php echo $message->use?></td>

			<td><em><?php
				$encodedContent = htmlentities($message->content, ENT_COMPAT, 'UTF-8');
				if (strlen($encodedContent) < 520)
					echo $encodedContent;
				else
					echo sprintf( __( '%s(More)', 'affiliates-manager' ), substr($encodedContent, 0, 500) );
			?></em></td>
		</tr>
		<?php } ?>
	</table>
</div>
</div></div>
<div id="messageEditorModifiedWarning" style="display: none; padding: 20px;">
	<span style="color: red"><?php _e( "* Some messages have been modified. These will <strong>NOT</strong> be saved to the database until you click 'Save Settings' button below.", 'affiliates-manager' ) ?></span>
</div>

<div class="postbox">
<h3 class="hndle"><label for="title"><?php _e('Messaging Related Options', 'affiliates-manager' ) ?></label></h3>
<div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

        <tbody>
        <tr valign="top">
            <td width="25%" align="left">
            <strong><?php _e('Email Type:', 'affiliates-manager' ) ?></strong>
            </td>
            <td align="left">
                <select id="emailType" name="emailType">
                        <option value="plain" <?php echo ($this->viewData['request']['emailType'] == 'plain' ? 'selected="selected"' : '')?>><?php _e( 'Plain Text', 'affiliates-manager' ) ?></option>
                        <option value="html" <?php echo ($this->viewData['request']['emailType'] == 'html' ? 'selected="selected"' : '')?>><?php _e( 'HTML', 'affiliates-manager' ) ?></option>
                </select>
                <br><i><?php _e("The content type for email sent through the wp_mail() function. The default is 'text/plain' which does not allow using HTML.", 'affiliates-manager' ) ?></i><br>    
            </td>
        </tr>    
        <tr valign="top">
            <td width="25%" align="left">
            <strong><?php _e('Send Registration Notification:', 'affiliates-manager' ) ?></strong>
            </td>
            <td align="left">
                <input type="checkbox" id="sendAdminRegNotification" name="sendAdminRegNotification" <?php
			if ($this->viewData['request']['sendAdminRegNotification'])
				echo 'checked="checked"';
			?>/>
                <?php _e('Send Notification to Admin', 'affiliates-manager' ) ?>
                <br><i><?php _e('Check this box if you want the admin of this site to get notified via email after a new affiliate signup.', 'affiliates-manager' ) ?></i>
                <br><br>
                <?php _e('Admin Email Address', 'affiliates-manager' ) ?>
                <input type="text" size="30" name="adminRegNotificationEmail" id="adminRegNotificationEmail" value="<?php echo $this->viewData['request']['adminRegNotificationEmail']?>" />
                <br><i><?php _e('The email address to which the admin will get notified after a new affiliate signup.', 'affiliates-manager' ) ?></i><br>    
            </td>
        </tr>
        <tr valign="top">
            <td width="25%" align="left">
            <strong><?php _e('Send Commission Notification:', 'affiliates-manager' ) ?></strong>
            </td>
            <td align="left">
                <input type="checkbox" id="sendAffCommissionNotification" name="sendAffCommissionNotification" <?php
			if ($this->viewData['request']['sendAffCommissionNotification'])
				echo 'checked="checked"';
			?>/>
                <?php _e('Send Notification to Affiliate', 'affiliates-manager' ) ?>
                <br><i><?php _e('Check this box if you want your affiliates to get notified via email when they earn a commission.', 'affiliates-manager' ) ?></i>
                <br><br>
                <input type="checkbox" id="sendAdminAffCommissionNotification" name="sendAdminAffCommissionNotification" <?php
			if ($this->viewData['request']['sendAdminAffCommissionNotification'])
				echo 'checked="checked"';
			?>/>
                <?php _e('Send Notification to Admin', 'affiliates-manager' ) ?>
                <br><i><?php _e('Check this box if you want the admin of this site to get notified via email when an affiliate earns a commission.', 'affiliates-manager' ) ?></i>
                <br><br>
                <?php _e('Admin Email Address', 'affiliates-manager' ) ?>
                <input type="text" size="30" name="adminAffCommissionNotificationEmail" id="adminAffCommissionNotificationEmail" value="<?php echo $this->viewData['request']['adminAffCommissionNotificationEmail']?>" />
                <br><i><?php _e('The email address to which the admin will get notified when an affiliate earns a commission.', 'affiliates-manager' ) ?></i><br>    
            </td>
        </tr>
    </tbody></table>
</div></div>
