<script type="text/javascript">
jQuery(function($) {

	jQuery(".edit-icon-button").click(editIconClicked);
	jQuery(".delete-icon-button").click(deleteIconClicked);

	var rowEditDialogMode = 'ADD';
	var rowEditDialogEditTarget = null;
	
	$('input[name$="[enabled]"][type=checkbox]').each(toggleRow);
	$('input[name$="[enabled]"][type=checkbox]').click(toggleRow);

	function toggleRow()
	{

		if (jQuery(this).filter(':checked').length > 0) {
			enableRow(jQuery(this).parent());
		} else {
			disableRow(jQuery(this).parent());
		}
	}
	function enableRow(cell)
	{
		cell.siblings()
			.css("font-style", "normal")
			.css("color", "#000")
			.children(":checkbox")
			.removeAttr('disabled');
	}
	function disableRow(cell)
	{
		cell.siblings()
			.css("font-style", "italic")
			.css("color", "#bbb")
			.children(":checkbox")
			.attr('disabled',true);
	}

	function addRow(enabled, type, fieldFormat, fieldName, displayName, maxLength, required)
	{
		var newRow = jQuery('<tr>');

		setRowValues(newRow, enabled, type, fieldFormat, fieldName, displayName, maxLength, required);

		jQuery("#fields_table").append(newRow);
		jQuery("#fields_table").tableDnD({
			onDragClass: 'dragging',
			dragHandle: 'dragHandle'
		});
	}

	function updateRow(rowObject, type, fieldFormat, fieldName, displayName, maxLength)
	{
		var enabledField = jQuery('[id="field['+fieldName+'][enabled]"]').attr('checked');
		var requiredField = jQuery('[id="field['+fieldName+'][required]"]').attr('checked');

		setRowValues(
			rowObject,
			enabledField,
			type,
			fieldFormat,
			fieldName,
			displayName,
			maxLength,
			requiredField
		);

		jQuery("#fields_table").tableDnD({
			onDragClass: 'dragging',
			dragHandle: 'dragHandle'
		});
	}

	function setRowValues(rowObject, enabled, type, fieldFormat, fieldName, displayName, maxLength, required)
	{
		var fieldPrefix = 'field['+fieldName+']';

		rowObject.empty();
		rowObject.append(jQuery('<td>').addClass('dragHandle').hover(doDragHandleHover, doDragHandleHoverLeave));

		var enabledTd = jQuery('<td>').append(
			jQuery('<input type="checkbox" id="'+fieldPrefix+'[enabled]">')
				.attr('name', fieldPrefix+'[enabled]')
				.click(toggleRow)
		);

		rowObject.append(enabledTd);
		rowObject.append(jQuery('<td>').html(type).append(
			jQuery('<input type="hidden">')
				.attr('name', fieldPrefix+'[type]')
				.val(type)
		));
		rowObject.append(jQuery('<td>').html(fieldFormat).append(
			jQuery('<input type="hidden">')
				.attr('name', fieldPrefix+'[fieldType]')
				.val(fieldFormat)
		));
		rowObject.append(jQuery('<td>').html(displayName).append(
			jQuery('<input type="hidden">')
				.attr('name', fieldPrefix + '[displayName]')
				.val(displayName)
		));
		rowObject.append(jQuery('<td>').html(fieldName).append(
			jQuery('<input type="hidden">')
				.attr('name', fieldPrefix + '[fieldName]')
				.val(fieldName)
		));

		if (fieldFormat == 'string')
		{
			rowObject.append(jQuery('<td>').html(maxLength).append(
				jQuery('<input type="hidden">')
					.attr('name', fieldPrefix + '[maxLength]')
					.val(maxLength)
			));
		}
		else
		{
			rowObject.append(jQuery('<td>').html('n/a').append(
				jQuery('<input type="hidden">')
					.attr('name', fieldPrefix + '[maxLength]')
					.val(maxLength)
			));
		}


		var requiredTd = jQuery('<td>').append(
			jQuery('<input type="checkbox" id="'+fieldPrefix+'[required]">')
				.attr('name', fieldPrefix + '[required]')
		);

		if (required)
			requiredTd.find(':checkbox').attr('checked', 'checked');

		rowObject.append(requiredTd);

		if (type=='base')
		{
			rowObject.append(jQuery('<td>'));
		}
		else
		{
			rowObject.append(jQuery('<td>').append(
				jQuery('<img>')
					.attr('src', '<?php echo $ICON_EDIT?>')
					.addClass('wpam-action-icon')
					.addClass('edit-icon-button')
					.click(editIconClicked),
				jQuery('<img>')
					.attr('src', '<?php echo $ICON_DELETE?>')
					.addClass('wpam-action-icon')
					.addClass('delete-icon-button')
					.click(deleteIconClicked)
			).css('white-space', 'nowrap'));
		}

		if (enabled)
			enabledTd.find(':checkbox').attr('checked', 'checked');
		else
			disableRow(enabledTd);

	}
	function deleteIconClicked()
	{
		jQuery(this).closest('tr').remove();
	}

	function editIconClicked()
	{
		rowEditDialogMode = 'EDIT';
		rowEditDialogEditTarget = jQuery(this).closest('tr');
		var fieldName = rowEditDialogEditTarget.find('input[name$="[fieldName]"]').val();
		var displayName = rowEditDialogEditTarget.find('input[name$="[displayName]"]').val();
		var maxLength = rowEditDialogEditTarget.find('input[name$="[maxLength]"]').val();
		var type = rowEditDialogEditTarget.find('input[name$="[fieldType]"]').val();

		showCustomFieldEditor(fieldName, displayName, maxLength, type);
	}
	function markLabelBad(inputId, errorMsg)
	{
		jQuery("label[for=" + inputId + "]").addClass('wpam_form_error');
		jQuery("#"+inputId).addClass('ui-state-error');
		jQuery("#"+inputId).parent().next().html(errorMsg).css('color','red');
	}
	function markLabelOk(inputId)
	{
		jQuery("label[for=" + inputId + "]").removeClass('wpam_form_error');
		jQuery("#"+inputId).removeClass('ui-state-error');
		jQuery("#"+inputId).parent().next().html("");
	}

	function showCustomFieldEditor(fieldName, displayName, fieldLength, type)
	{
		markLabelOk("txtFieldName");
		markLabelOk("txtDisplayName");
		markLabelOk("txtFieldLength");

		jQuery("#txtFieldName").val(fieldName);
		jQuery("#txtDisplayName").val(displayName);
		jQuery("#txtFieldLength").val(fieldLength);
		jQuery("#ddFieldTypes").val(type);

		updateFieldLengthVisibility();

		jQuery("#dialogCustomFieldEditor").dialog('open');
	}

	function validateCustomFieldEditor()
	{
		var isValid = true;

		var displayName = jQuery("#txtDisplayName").val();
		var fieldName = jQuery("#txtFieldName").val();
		var fieldLength = jQuery("#txtFieldLength").val();
		var fieldType = jQuery("#ddFieldTypes").val();

		if (jQuery.trim(displayName).length == 0) {
			markLabelBad('txtDisplayName', "<?php _e( 'Display name is required.', 'affiliates-manager' ) ?>");
			isValid = false;
		} else {
			markLabelOk('txtDisplayName');
		}

		if (jQuery.trim(fieldName).length == 0) {
			markLabelBad('txtFieldName', "<?php _e( 'Field name is required.', 'affiliates-manager' ) ?>");
			isValid = false;
		}else if (!fieldName.match(/^[a-zA-Z][a-zA-Z0-9_-]*$/)) {
			markLabelBad('txtFieldName', "<?php _e( 'Field name may only contains letters, numbers, underscores and hyphens, and must begin with a letter.', 'affiliates-manager' ) ?>");
			isValid = false;
		}
		else {
			var currentFieldName = rowEditDialogMode == 'EDIT' ? rowEditDialogEditTarget.find('input[name$="[fieldName]"]').val() : null;
			if (currentFieldName != fieldName && jQuery('input[name$="[fieldName]"][value="'+fieldName+'"]').length != 0)
			{
				markLabelBad('txtFieldName', '<?php _e( 'This field name is already in use.', 'affiliates-manager' ) ?>');
				isValid = false;
			}
			else
			{
				markLabelOk('txtFieldName');
			}
		}

		if (fieldType == 'string')
		{
			if (isNaN(fieldLength) || jQuery.trim(fieldLength).length == 0 || fieldLength <= 0) {
				markLabelBad('txtFieldLength',"<?php _e( 'Field length must be an non-0 positive number.', 'affiliates-manager' ) ?>");
				isValid = false;
			} else {
				markLabelOk('txtFieldLength');
			}
		}

		return isValid;
	}

	jQuery("#dialogCustomFieldEditor").dialog({
		resizable: false,
		height: 380,
		width: 640,
		modal: true,
		draggable: true,
		autoOpen: false,
		buttons: [ {
				  text : 'Cancel',
				  click : function() { jQuery(this).dialog('close'); }
				}, {
				  text : 'OK',
				  click : function() {
				if (validateCustomFieldEditor())
				{
					if (rowEditDialogMode == 'ADD')
					{
						addRow(
							true,
							'custom',
							jQuery("#ddFieldTypes").val(),
							jQuery("#txtFieldName").val(),
							jQuery("#txtDisplayName").val(),
							jQuery("#txtFieldLength").val(),
							true
						);
					}
					else
					{
						updateRow(
							rowEditDialogEditTarget,
							'custom',
							jQuery("#ddFieldTypes").val(),
							jQuery("#txtFieldName").val(),
							jQuery("#txtDisplayName").val(),
							jQuery("#txtFieldLength").val()
						);
					}
					jQuery(this).dialog('close');
				}					
			}
		} ]
	});

	function updateFieldLengthVisibility()
	{
		var fieldType = jQuery("#ddFieldTypes").val();
		if (fieldType == 'string')
		{
			jQuery("#row_txtFieldLength").show();
		}
		else
		{
			jQuery("#row_txtFieldLength").hide();
		}

	}
	jQuery("#ddFieldTypes").change(updateFieldLengthVisibility);

	jQuery("#btnAddCustomField").click(function() {
		rowEditDialogMode = 'ADD';
		rowEditDialogEditTarget = null;
		showCustomFieldEditor('','','', 'string');
	});

	jQuery("#fields_table").tableDnD({
		onDragClass: 'dragging',
		dragHandle: 'dragHandle'
	});

	function doDragHandleHover() {
		jQuery(this).addClass('showDragHandle');
	}
	function doDragHandleHoverLeave() {
		jQuery(this).removeClass('showDragHandle');
	}
	jQuery(".dragHandle").hover(doDragHandleHover, doDragHandleHoverLeave);

	<?php foreach ($this->viewData['affiliateRegisterFields'] as $affiliateField): ?>
	addRow(
		<?php echo $affiliateField->enabled?'true':'false'?>,
		"<?php echo $affiliateField->type?>",
		"<?php echo $affiliateField->fieldType?>",
		"<?php echo $affiliateField->databaseField?>",
		"<?php echo __($affiliateField->name, 'affiliates-manager')?>",
		"<?php echo $affiliateField->length?>",
		<?php echo $affiliateField->required?'true':'false'?>
	);
	<?php endforeach; ?>

	//#43 email is required
	jQuery('[id="field[email][enabled]"]').attr('checked', 'checked').attr('disabled',true);
	jQuery('[id="field[email][required]"]').attr('checked', 'checked').attr('disabled',true);
	
	
});
</script>

<style type="text/css">
	#fields_table th {
		white-space: nowrap;
	}
</style>

<div id="dialogCustomFieldEditor" style="display: none" title="<?php _e( 'Custom Field', 'affiliates-manager' ) ?>">
	<div id="dialogCustomFieldEditorErrors">

	</div>
	<table class="wpam-form-table">
		<tbody>
		<tr>
	  		<th><label for="txtDisplayName"><?php _e( 'Display Name', 'affiliates-manager' ) ?></label></th>
			<td>
				<input id="txtDisplayName" type="text" name="txtDisplayName" size="20" />
			</td>
			<td></td>
		</tr>
		<tr>
			<th><label for="txtFieldName"><?php _e( 'Field Name', 'affiliates-manager' ) ?></label></th>
			<td>
				<input id="txtFieldName" type="text" name="txtFieldName" size="20" />
			</td>
			<td></td>
		</tr>
		<tr>
				<th><label for="ddFieldTypes"><?php _e( 'Field Type', 'affiliates-manager' ) ?></label></th>
			<td>
				<select id="ddFieldTypes">
					<option value="string"><?php _e( 'Text', 'affiliates-manager' ) ?></option>
                                        <option value="textarea"><?php _e( 'Textarea', 'affiliates-manager' ) ?></option>
					<option value="number"><?php _e( 'Number', 'affiliates-manager' ) ?></option>
					<option value="email"><?php _e( 'E-Mail Address', 'affiliates-manager' ) ?></option>
					<option value="phoneNumber"><?php _e( 'Phone Number', 'affiliates-manager' ) ?></option>
					<option value="stateCode"><?php _e( 'State Code (US)', 'affiliates-manager' ) ?></option>
					<option value="zipCode"><?php _e( 'Zip Code (US)', 'affiliates-manager' ) ?></option>
					<option value="countryCode"><?php _e( 'Country Code', 'affiliates-manager' ) ?></option>
					<option value="ssn"><?php _e( 'Social Security Number (US)', 'affiliates-manager' ) ?></option>
				</select>
			</td>
			<td></td>
		</tr>
		<tr id="row_txtFieldLength">
			<!-- only for strings -->
			<th><label for="txtFieldLength"><?php _e( 'Max Length', 'affiliates-manager' ) ?></label></th>
			<td>
				<input type="text" id="txtFieldLength" name="txtFieldLength" size="5" />
			</td>
			<td></td>
		</tr>
		</tbody>
	</table>
</div>	

<table class="form-table">
	<tr>
		<th width="200">
			<?php _e( 'Available Payout Methods', 'affiliates-manager' ) ?>
		</th>
		<td>
			<input type="checkbox" id="chkPayoutMethodCheck" name="chkPayoutMethodCheck" <?php
			if ($this->viewData['request']['chkPayoutMethodCheck'])
				echo 'checked="checked"';
			?>/>&nbsp;&nbsp;<label for="chkPayoutMethodCheck"><?php _e( 'Check', 'affiliates-manager') ?></label>
			<br><input type="checkbox" id="chkPayoutMethodPaypal" name="chkPayoutMethodPaypal" <?php
			if ($this->viewData['request']['chkPayoutMethodPaypal'])
				echo 'checked="checked"';
			?>/>&nbsp;&nbsp;<label for="chkPayoutMethodPaypal"><?php _e( 'PayPal', 'affiliates-manager' ) ?></label>
                        <br><input type="checkbox" id="chkPayoutMethodManual" name="chkPayoutMethodManual" <?php
			if ($this->viewData['request']['chkPayoutMethodManual'])
				echo 'checked="checked"';
			?>/>&nbsp;&nbsp;<label for="chkPayoutMethodManual"><?php _e( 'Manual', 'affiliates-manager' ) ?></label>
		</td>
	</tr>
	<tr>
		<th width="200">
			<?php _e( 'Affiliate Register Fields', 'affiliates-manager' ) ?>
		</th>
		<td>
			<button id="btnAddCustomField" class="button-secondary" type="button" name="add"><?php _e( 'Add Custom Field', 'affiliates-manager' ) ?></button>
			<table class="widefat" style="max-width: 950px;" id="fields_table">
				<thead>
				<tr>
					<th style="width: 50px">&lt; - &gt;</th>
					<th style="width: 50px;"><?php _e( 'Enabled', 'affiliates-manager' ) ?></th>
					<th style="width: 150px;"><?php _e( 'Type', 'affiliates-manager' ) ?></th>
					<th style="width: 150px"><?php _e( 'Field Format', 'affiliates-manager' ) ?></th>
					<th style="width: 150px"><?php _e( 'Display Name', 'affiliates-manager' ) ?></th>
					<th style="width: 150px"><?php _e( 'Field Name', 'affiliates-manager' ) ?></th>
					<th style="width: 150px;"><?php _e( 'Max Length', 'affiliates-manager' ) ?></th>
					<th style="width: 50px;"><?php _e( 'Required', 'affiliates-manager' ) ?></th>
					<th style="width: 50px"></th>
				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</td>
	</tr>
        <tr>
		<th width="200">
			<label for="affhomemsg"><?php _e('Affiliates Homepage Message', 'affiliates-manager');?></label>
		</th>
		<td>
                        <?php wp_editor($this->viewData['request']['affhomemsg'], 'affhomemsg', array('textarea_name' => 'affhomemsg')); ?>
                        <p class="description"><?php _e('This message is shown to a normal visitor who is not logged into WordPress', 'affiliates-manager')?></p>
		</td>
	</tr>
        <tr>
		<th width="200">
			<label for="affhomemsgnotregistered"><?php _e('Affiliates Homepage Message (Logged in but not an affiliate)', 'affiliates-manager');?></label>
		</th>
		<td>
                        <?php wp_editor($this->viewData['request']['affhomemsgnotregistered'], 'affhomemsgnotregistered', array('textarea_name' => 'affhomemsgnotregistered')); ?>
                        <p class="description"><?php _e('This message is shown to a user who is logged into WordPress but not an affiliate', 'affiliates-manager')?></p>
		</td>
	</tr>
</table>

