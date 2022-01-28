<style>
	.supsystic-plugin .bundleMessageShell {
		background-color: #ffffff;
		padding: 10px 10px 0 10px;
	}
	.supsystic-plugin .bundleMessage {
		border: 3px solid #52bac5;
		text-align: center;
		padding: 10px;
		vertical-align: middle;
	}
	.supsystic-plugin .bundleMessage .text {
		font-size: 21px;
		line-height: 28px;
		vertical-align: middle;
	}
	.supsystic-plugin .bundleMessage .text a {
		color: #52bac5;
	}
	.supsystic-plugin .bundleMessage .button {
		height: 40px !important;
		line-height: 40px !important;
		font-size: 17px !important;
		background-color: #52bac5 !important;
		border: none !important;
		color: #fff !important;
		padding: 0 26px !important;
		margin-left: 20px;
		vertical-align: middle;
	}
	.supsystic-plugin .bundleMessage .button:hover {
		background: #4ae8ea !important;
	}
</style>
<div class="bundleMessageShell">
	<div class="bundleMessage">
		<span class="text"><?php echo sprintf(__('Upgrade to bundle and get an access to <a href="%s" target="_blank">all 14 plugins</a> more than 80%% off!', UMS_LANG_CODE), $this->bundlePageLink);?></span>
		<a href="<?php echo $this->buyLink; ?>" class="button" target="_blank"><?php _e('Buy Now', UMS_LANG_CODE)?></a>
	</div>
</div>