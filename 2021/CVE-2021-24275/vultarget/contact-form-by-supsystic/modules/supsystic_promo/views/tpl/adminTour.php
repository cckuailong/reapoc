<div id="supsystic-admin-tour" class="">
	<div id="supsystic-welcome-first_welcome">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Welcome to %s plugin!', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('Thank you for choosing our %s plugin. Just click here to start using it - and we will show you it\'s possibilities and powerfull features.', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="<?php echo frameCfs::_()->getModule('options')->getTabUrl('forms');?>" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-create_first-create_bar_btn">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Create your firs Form', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('Click on "Add New Form" button to create your firs Form. Just try - this is really simple!', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="<?php echo frameCfs::_()->getModule('options')->getTabUrl('forms_add_new');?>" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-create_first-enter_title">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Enter name for your Form', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('This will be name of your Form. You can change it latter.', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointer('create_first', 'select_tpl'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-create_first-select_tpl">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Selecte template for your Form', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('Choose any templates from this list. You will be able to customize it after creation, and also - you will be able to change it latter if you will need this.', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointer('create_first', 'save_first_forms'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-create_first-save_first_forms">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Save first Form', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('After you entered name of your Form and selected it\'s template - just save it, and you will be redirected to Form edit screen - where you will be able to customize your Form.', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="jQuery('#cfsCreateFormForm').submit(); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-first_edit-forms_main_opts">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Main Settings', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('Here you can setup main display settings for your Form - when it should be visible for your user, when it need to be closed, if required - select specific pages/posts where you need to show your Form.', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointerAndFormTab('first_edit', 'forms_design_opts', '#cfsFormTpl'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-first_edit-forms_design_opts">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Design Settings', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('One of our most powerfull features - possibility to <strong>customize</strong> design for each Form window for your needs. In this section you can select your Form colors and images, enter required texts that will describe your neds for your visitors, setup social settings (if required), select Form location, and in the end - select Animation style for your Form from list of more then 20 different animation styles!', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointerAndFormTab('first_edit', 'forms_subscribe_opts', '#cfsFormSubscribe'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-first_edit-forms_subscribe_opts">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Subscribe Settings', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('Setup your Subscription settings here - select Subscribers destination in "Subscribe to" option - it allow to flow your subscribers not only to WordPress Users, but to other popular subscribe services. With other subscription options you will be able to easily customize your subscribe form in Form window.', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointerAndFormTab('first_edit', 'forms_statistics_opts', '#cfsFormStatistics'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-first_edit-forms_statistics_opts">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Form Statistics', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('After you will setup your Form - it will start displaying to your site visitors. And now - you need to check it\'s displaying statistics. Here, in Statistics tab, you will be able to see how many times Form was shown to your visitors, how many times visitors subscribed to it (if subscription is enabled), how many times visitors shared your site using Social Share Form functionality and what social networks for share is most popular (if it was enabled). If you will use AB Testing feature to increase your site popularity - you will see here all your main and tested Forms statistics - in one graph or diagramm, - and this will provide you with all required information about your Form popularity!', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointerAndFormTab('first_edit', 'forms_code_opts', '#cfsFormEditors'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-first_edit-forms_code_opts">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Form CSS / HTML Code', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('In case you will need modify source CSS / HTML code of your Form - you can easily do this here. Just make sure that you know what you are doing - don\'t break Form. You can also find additional information about editing source code <a href="%s" target="_blank">here</a>.', CFS_LANG_CODE), 'http://supsystic.com/edit-forms-html-css-code/')?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="close"><?php _e('Close', CFS_LANG_CODE)?></a>
			<a href="#" onclick="_cfsOpenPointer('first_edit', 'final'); return false;" class="button button-primary supsystic-tour-next-btn"><?php _e('Next', CFS_LANG_CODE)?></a>
		</div>
	</div>
	<div id="supsystic-first_edit-final">
		<div class="supsystic-tour-content">
			<h3><?php printf(__('Well Done!', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME)?></h3>
			<p><?php printf(__('That\'s it! Now you know how to use our %s. Just save your Form after you will setup it - and you will see results. You can also check our site - <a href="%s" target="_blank">supsystic.com</a> to find out more about our %s plugin. If you will have any questions - you can always contact us on <a href="%s" target="_blank">WordPress plugin forum</a> or in <a href="%s" target="_blank">our support system</a>. We really hope that our solution will be helpful for you. Good luck!', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME, $this->finishSiteLink, CFS_WP_PLUGIN_NAME, 'https://wordpress.org/support/plugin/contact-form-by-supsystic', $this->contactFormLink)?></p>
		</div>
		<div class="supsystic-tour-btns">
			<a href="#" class="button-primary supsystic-tour-finish-btn"><?php _e('Finish', CFS_LANG_CODE)?></a>
		</div>
	</div>
</div>