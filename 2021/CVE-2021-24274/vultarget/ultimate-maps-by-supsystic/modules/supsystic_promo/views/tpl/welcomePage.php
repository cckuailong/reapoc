<style type="text/css">
	.spacer {width:100%; height:25px;}
	.container {
		position: relative;
		max-width: 1050px;
	}
	.container h1 {text-align:center; font-size: 30px; margin:10px 0; float: left;}
    .container h2 {font-size:24px; margin:10px 0;}
    .container h3 {font-size:20px; margin:10px 0;}
    .container p {line-height:20px; margin-bottom:10px;}
    .container ul {margin-bottom:10px; margin-left:20px;}
    .container ul li {list-style-type:disc;}
	
	.about-message {
		font-size: 21px;
		line-height: 30px;
		float: left;
	}
	
	.plug-icon-shell {
		position: absolute;
		right: 0;
		top: 50px;
		width: 15%;
	}
	.plug-icon-shell a {
		font-size: 14px;
		color: grey;
		text-decoration: none;
		text-align: right;
		float: right;
	}
	
	.video-wrapper {
		margin:0 auto; 
		width:640px;
		float: left;
	}
    .clear {clear:both;}
    
    .col-3 {
		float:left; 
		padding-right: 20px;
		width:29%;
	}
	
	#toeWelcomePageFindUsForm label {
		line-height: 24px;
		margin-left: 20px;
		font-size: 14px;
		display: block;
		max-width: 200px;
	}
</style>
<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#toeWelcomePageFindUsForm input[type=radio][name=where_find_us]').change(function(){
		jQuery('#toeFindUsUrlShell, #toeOtherWayTextShell').hide();
		switch(parseInt(jQuery(this).val())) {
			case 4 /*Find on the web*/ :
				jQuery('#toeFindUsUrlShell').show('slow');
				break;
			case 5 /*Other way*/ :
				jQuery('#toeOtherWayTextShell').show('slow');
				break;
		}
	});
});
// -->
</script>
<div class="supContainer supsystic-plugin supsystic-welcome-page-content" id="containerWrapper">
	<div class="supsystic-item supsystic-panel">
		<form id="toeWelcomePageFindUsForm" method="GET">
			<h1>
				<?php _e('Welcome to', UMS_LANG_CODE)?>
				<?php echo UMS_WP_PLUGIN_NAME?>
				<?php _e('Version', UMS_LANG_CODE)?>
				<?php echo UMS_VERSION_PLUGIN?>!
			</h1>
			<div class="clear"></div>
			<hr />
			<div class="about-message">
				<?php printf(__('This is first start up of the %s plugin.', UMS_LANG_CODE), UMS_WP_PLUGIN_NAME)?><br />
				<?php _e('If you are newbie - check all features on that page, if you are guru - please correct us.', UMS_LANG_CODE)?> 
			</div>
			<div class="clear"></div>
			<div class="spacer"></div>

			<h2>Where did you find us?</h2>
			<?php foreach($this->askOptions as $askId => $askOpt) { ?>
				<label><?php echo htmlUms::radiobutton('where_find_us', array('value' => $askId))?>&nbsp;<?php echo $askOpt['label']?></label>
				<?php if($askId == 4 /*Find on the web*/) { ?>
					<label id="toeFindUsUrlShell" style="display: none;"><?php _e('Please, post url', UMS_LANG_CODE)?>: <?php echo htmlUms::text('find_on_web_url')?></label>
				<?php } elseif($askId == 5 /*Other way*/) { ?>
					<label style="display: none;" id="toeOtherWayTextShell"><?php echo htmlUms::textarea('other_way_desc')?></label>
				<?php }?>
			<?php }?>

			<div class="spacer"></div>

			<h2><?php _e('Video tutorial', UMS_LANG_CODE)?></h2>
			<div class="video-wrapper">
				<iframe width="640" height="360" src="//www.youtube.com/v/Vqp7EJ2inQ4%26feature" frameborder="0" allowfullscreen></iframe>
			</div>
			<div class="clear"></div>

			<div class="about-message"><?php _e('What to do next? Check below section', UMS_LANG_CODE)?>:</div>
			<div class="clear"></div>

			<div class="col-3">
				<h3><?php _e('Boost us', UMS_LANG_CODE)?>:</h3>
				<p><?php printf(__("It's amazing when you boost development with your feedback and ratings. So we create special <a target='_blank' href='%s'>boost page</a> to help you to help us.", UMS_LANG_CODE), 'http://supsystic.com/boost-our-plugins/')?></p>
			</div>

			<div class="col-3">
				<h3><?php _e('Documentation', UMS_LANG_CODE)?>:</h3>
				<p><?php printf(__("Check <a target='_blank' href='%s'>documentation</a> and FAQ section. If you can't solve your problems - <a target='_blank' href='%s'>contact us</a>.", UMS_LANG_CODE), 'http://supsystic.com/product/supsystic-secure/', 'http://supsystic.com/contacts/')?></p>
			</div>

			<div class="col-3">
				<h3><?php _e('Full Features List', UMS_LANG_CODE)?>:</h3>
				<p><?php _e("There are so many features, so we can't post it here. Like", UMS_LANG_CODE)?>:</p>
				<ul>
					<li><?php _e('Capcha for admin login', UMS_LANG_CODE)?></li>
					<li><?php _e('htaccess admin protect', UMS_LANG_CODE)?></li>
					<li><?php _e('Hide directory files listing', UMS_LANG_CODE)?></li>
					<li><?php _e('Check files and directories write permissions', UMS_LANG_CODE)?></li>
				</ul>
				<p><?php printf(__("So check full features list <a target='_blank' href='%s'>here</a>.", UMS_LANG_CODE), 'http://worumsess.org/plugins/supsystic-secure/')?></p>
			</div>
			<div class="clear"></div>

			<?php echo htmlUms::hidden('pl', array('value' => UMS_CODE))?>
			<?php echo htmlUms::hidden('page', array('value' => 'supsystic_promo'))?>
			<?php echo htmlUms::hidden('action', array('value' => 'welcomePageSaveInfo'))?>
			<?php echo htmlUms::submit('gonext', array('value' => 'Thank for check info. Start using plugin.', 'attrs' => 'class="button button-primary button-hero"'))?>
			<?php echo htmlUms::hidden('original_page', array('value' => $this->originalPage))?>

			<span id="toeWelcomePageFindUsMsg"></span>
		</form>
	</div>
</div>