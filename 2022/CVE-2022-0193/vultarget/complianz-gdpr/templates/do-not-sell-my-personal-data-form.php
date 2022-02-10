<?php
//firstname is honeypot
?>
<div class="cmplz-dnsmpd alert">
    <span class="close">&times;</span>
    <span id="message"></span>
</div>
<style>
	.cmplz-first-name {
		position: absolute !important;
		left: -5000px !important;
	}
</style>
<div id="cmplz-dnsmpd-form">
    <label for="cmplz_dnsmpd_firstname" class="cmplz-first-name"><?php echo __('Name','complianz-gdpr')?><input type="search" class="dnsmpd-firstname" value="" placeholder="your first name" id="cmplz_dnsmpd_firstname"></label>
	<div>
		<label for="cmplz_dnsmpd_name"><?php echo __('Name','complianz-gdpr')?></label><input type="text" required value="" placeholder="<?php echo __('Your name','complianz-gdpr')?>" id="cmplz_dnsmpd_name">
	</div><div>
		<label for="cmplz_dnsmpd_email"><?php echo __('Email','complianz-gdpr')?></label><input type="email" required value="" placeholder="<?php echo __('email@email.com','complianz-gdpr')?>" id="cmplz_dnsmpd_email">
	</div>
	<input type="button" id="cmplz-dnsmpd-submit"  value="<?php echo __('Send','complianz-gdpr')?>">
</div>


