<div class="cmplz-cookie-field {ignored}"
     data-cookie_id="{cookie_id}">
	<div class="{disabledClass}">
		<div><label><?php _e( 'Name', 'complianz-gdpr' ) ?></label></div>

		<input type="text" {disabled}
		       class="cmplz_name" name="cmplz_name"
		       value="{name}">
	</div>
	<div class="{disabledClass}">
		<div><label><?php _e( 'Service', 'complianz-gdpr' ) ?></label></div>

		<select class="cmplz-select2 cmplz_service" type="text" {disabled}
		        name="cmplz_service">
			{services}
		</select>
	</div>
	<div class="{disabledClass}">
		<div><label><?php _e( 'Expiration', 'complianz-gdpr' ) ?></label></div>

		<input type="text" {disabled}
		       class="cmplz_retention" name="cmplz_retention"
		       value="{retention}">
	</div>
	<div class="{disabledClass}">
		<div><label><?php _e( 'Cookie function', 'complianz-gdpr' ) ?></label>
		</div>

		<input type="text" {disabled}
		       class="cmplz_cookieFunction" name="cmplz_cookieFunction"
		       value="{cookieFunction}">
	</div>
	<div class="{disabledClass}">
		<div><label><?php _e( 'Purpose', 'complianz-gdpr' ) ?></label></div>

		<select class="cmplz-select2-no-additions cmplz_purpose" type="text"
		        {disabled} name="cmplz_purpose">
			{purposes}
		</select>
	</div>

    <label class="cmplz-checkbox-container {disabledClass}"><?php _e( 'Stores personal data', 'complianz-gdpr' ) ?>
        <input
                name="cmplz_isPersonalData"
                class="cmplz_isPersonalData"
                type="checkbox"
                {isPersonalData}
        >
        <div class="checkmark"><?php echo cmplz_icon('check', 'success', '', 10) ?></div>
    </label>

    <div class="{disabledClass}">
        <div><label><?php _e( 'Collected Personal Data', 'complianz-gdpr' ) ?></label></div>

        <input type="text" {disabled}
               class="cmplz_collectedPersonalData"
               name="cmplz_collectedPersonalData"
               value="{collectedPersonalData}">
    </div>

    <label class="cmplz-checkbox-container {syncDisabled}"><?php _e( 'Sync cookie info with cookiedatabase.org', 'complianz-gdpr' ) ?>
        <input
                name="cmplz_sync"
                class="cmplz_sync"
                type="checkbox"
                {sync}
        >
        <div class="checkmark"><?php echo cmplz_icon('check', 'success', '', 10) ?></div>
    </label>

    <label class="cmplz-checkbox-container"><?php _e( 'Show cookie on Cookie Policy', 'complianz-gdpr' ) ?>
        <input
                name="cmplz_showOnPolicy"
                class="cmplz_showOnPolicy"
                type="checkbox"
                {showOnPolicy}
        >
        <div class="checkmark"><?php echo cmplz_icon('check', 'success', '', 10) ?></div>
    </label>

	<div>
		{link}
	</div>

    <div class="cmplz-multiple-field-button-footer">
        <button class="button cmplz-edit-item button-primary" type="button" data-action="save"
                data-type="cookie" name="cmplz-save-item"><?php _e( 'Save',
                'complianz-gdpr' ) ?></button>
        <button class="button cmplz-edit-item button-primary button-red" type="button" data-action="delete"
                data-type="cookie" name="cmplz_remove_item"><?php _e( "Delete",
                'complianz-gdpr' ) ?></button>
        <button class="button cmplz-edit-item button-primary" type="button" data-action="restore"
                data-type="cookie" name="cmplz_restore_item"><?php _e( "Restore",
                'complianz-gdpr' ) ?></button>
    </div>
</div>
