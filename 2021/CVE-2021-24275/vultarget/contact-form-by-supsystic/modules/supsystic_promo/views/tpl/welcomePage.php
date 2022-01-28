<section>
	<div class="supsystic-item supsystic-panel supsystic-plugin">
		<div id="containerWrapper">
			<div class="supRow">
				<div class="supSm12">
					<h2>
						<?php printf(__('Welcome to the %s v %s', CFS_LANG_CODE), CFS_WP_PLUGIN_NAME, CFS_VERSION)?>
						<a href="<?php echo $this->skipTutorLink;?>" class="button"><?php _e('Skip tutorial', CFS_LANG_CODE)?></a>
					</h2>
					<p>
						<?php _e('The best way to collect subscribers and show notifications.<br />We are trying to make our plugin work in most comfortable way for you. Here is some base information about it.', CFS_LANG_CODE)?>
					</p>
				</div>
			</div>
			<div class="supRow">
				<div class="supSm8">
					<div class="supRow">
						<div class="supSm6">
							<h3><?php _e('Step-by-step tutorial', CFS_LANG_CODE)?></h3>
							<p>
								<?php _e("There're really many options of forms customization. So as soon as you close that page, I'll show you step-by-step tutorial of how to use plugin. Hope it will be usefull for you :)", CFS_LANG_CODE)?>
							</p>
							<p>
								<?php _e('As an option we can install and setup plugin for you.', CFS_LANG_CODE)?>
							</p>
						</div>
						<div class="supSm6">
							<h3><?php _e('Support', CFS_LANG_CODE)?></h3>
							<p>
								<?php printf(__("We love our plugin and do the best to improve all features for You. But sometimes issues happened, or you can't find required feature that you need. Don't worry, just <a href='%s' target='_blank'>contact us</a> and we will help you!", CFS_LANG_CODE), $this->getModule()->getContactLink())?>
							</p>
						</div>
					</div>
					<div class="supRow">
						<div class="supSm12">
							<h3><?php _e('Video Tutorial', CFS_LANG_CODE)?></h3>
							<iframe type="text/html"
									width="90%"
									height="330px"
									src="https://www.youtube.com/embed/v8h2k3vvpdM"
									frameborder="0">
							</iframe>
						</div>
					</div>
				</div>
				<div class="supSm4">
					<h3>
						<?php _e('Frequently Asked Questions', CFS_LANG_CODE)?>
						<a target="_blank" href="<?php echo $this->mainLink?>#faq" style="float: right; font-size: 16px; padding-right: 15px; white-space: nowrap; font-weight: normal;">
							<i class="fa fa-info-circle"></i>
							<?php _e('Check all FAQs', CFS_LANG_CODE)?>
						</a>
					</h3>
					<?php foreach($this->faqList as $fHead => $fDesc) { ?>
					<h4><?php echo $fHead;?></h4>
					<p><?php echo $fDesc;?></p>
					<?php }?>
					<div style="clear: both;"></div>
					<a href="<?php echo $this->createNewLink;?>" class="button button-primary button-hero"><?php _e("Let's Start!", CFS_LANG_CODE)?></a>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</section>