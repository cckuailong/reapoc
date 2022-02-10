<div class="cmplz-service-field" data-service_id="{service_id}">
    <div class="{disabledClass}">
        <div><label><?php _e('Name', 'complianz-gdpr')?></label></div>

        <input type="text" {disabled}
               class="cmplz_name" name="cmplz_name"
               value="{name}">
    </div>
    <div class="{disabledClass}">
        <div><label><?php _e('Service type', 'complianz-gdpr')?></label></div>
        <select class="cmplz-select2-no-additions cmplz_serviceType" type="text" {disabled} name="cmplz_serviceType">
            {serviceTypes}
        </select>
    </div>

    <label class="cmplz-checkbox-container {disabledClass}"><?php _e( 'Data is shared with this service', 'complianz-gdpr' ) ?>
        <input
                name="cmplz_sharesData"
                class="cmplz_sharesData"
                type="checkbox"
                {sharesData}
        >
        <div class="checkmark"><?php echo cmplz_icon('check', 'success', '', 10) ?></div>
    </label>

    <div class="{disabledClass}">
        <div><label><?php _e('Privacy Statement URL', 'complianz-gdpr')?></label></div>

        <input type="text" {disabled}
               class="cmplz_privacyStatementURL" name="cmplz_privacyStatementURL"
               value="{privacyStatementURL}">
    </div>

    <label class="cmplz-checkbox-container {syncDisabled}"><?php _e( 'Sync service info with cookiedatabase.org', 'complianz-gdpr' ) ?>
        <input
                name="cmplz_sync"
                class="cmplz_sync"
                type="checkbox"
                {sync}
        >
        <div class="checkmark"><?php echo cmplz_icon('check', 'success', '', 10) ?></div>
    </label>

    <div>
        {link}
    </div>

    <div class="cmplz-multiple-field-button-footer">
        <button class="button cmplz-edit-item button-primary" type="button" data-action="save" data-type="service" name="cmplz-save-item" ><?php _e('Save','complianz-gdpr')?></button>

        <button class="button cmplz-edit-item button-primary button-red" type="button" data-action="delete" data-type="service"
                name="cmplz_remove_item"><?php _e("Delete", 'complianz-gdpr') ?></button>
    </div>

</div>
