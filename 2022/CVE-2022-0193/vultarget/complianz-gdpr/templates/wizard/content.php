<div class="cmplz-section-content">

    <form class="cmplz-form" action="{page_url}" method="POST">
		<input type="hidden" value="{page}" name="wizard_type">
		<input type="hidden" value="{step}" name="step">
		<input type="hidden" value="{section}" name="section">
		<?php wp_nonce_field( 'complianz_save', 'cmplz_nonce' ); ?>

        <div class="cmplz-wizard-title cmplz-section-content-title-header">{title}{flags}</div>
        <div class="cmplz-wizard-title cmplz-section-content-notifications-header">
			<?php _e("Notifications", "complianz-gdpr")?>
		</div>
	    {learn_notice}
	    {intro}
		{post_id}

		{fields}

        <div class="cmplz-section-footer">
            {save_as_notice}
            <div class="cmplz-buttons-container">
                {previous_button}
                {save_button}
                {next_button}
                {cookie_or_finish_button}
            </div>
        </div>

    </form>

</div>

