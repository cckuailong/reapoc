<div class="cmplz-wizard-menu">
    <div class="cmplz-wizard-title">
		{title}
	</div>
    <div class="cmplz-wizard-progress-bar">
        <div class="cmplz-wizard-progress-bar-value" style="width: {percentage-complete}%"></div>
    </div>
    <div class="cmplz-wizard-menu-menus">
        {steps}
    </div>
</div>
<?php $hide = isset( $_POST['cmplz-save']) ? 'cmplz-settings-saved--fade-in': ''; ?>
<div class="cmplz-settings-saved <?php echo $hide?>">
	<div class="cmplz-settings-saved__text_and_icon">
		<?php echo cmplz_icon('check', 'success', '', 18); ?>
		<span><?php _e('Changes saved successfully', 'complianz-gdpr') ?> </span>
	</div>
</div>
