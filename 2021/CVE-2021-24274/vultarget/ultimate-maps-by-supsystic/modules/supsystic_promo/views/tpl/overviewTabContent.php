<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<div class="supsistic-half-side-box">
				<?php /* ?><div class="faq-list">
 					<h3><?php _e('FAQ and Documentation', UMS_LANG_CODE)?></h3>
					<?php foreach($this->faqList as $title => $desc) { ?>
						<div class="faq-title">
							 <i class="fa fa-info-circle"></i>
							 <?php echo $title;?>
							 <div class="description" style="display: none;"><?php echo $desc;?></div>
						</div>
					<?php }?>
					<div style="clear: both;"></div>
					<a target="_blank" class="button button-primary button-hero" href="<?php echo $this->mainLink?>#faq" style="float: right;">
						<i class="fa fa-info-circle"></i>
						<?php _e('Check all FAQs', UMS_LANG_CODE)?>
					</a>
					<div style="clear: both;"></div>
				</div>
				<div class="video">
					<h3><?php _e('Video tutorial', UMS_LANG_CODE)?></h3>
					<iframe type="text/html"
							width="80%"
							height="240px"
							src="https://www.youtube.com/embed/Ej8EtuLcLZk"
							frameborder="0">
					</iframe>
				</div>
				<div class="video">
					<h3><?php _e('PRO Features', UMS_LANG_CODE)?></h3>
					<iframe type="text/html"
							width="80%"
							height="240px"
							src="https://www.youtube.com/embed/0wl0p_m8HEE"
							frameborder="0">
					</iframe>
				</div><?php */ ?>
				<div class="banner">
					<div class="text-block"><?php _e('If you want to host a business site or a blog, Kinsta managed WordPress hosting is the best place to stop on. Without any hesitation, we can say Kinsta is incredible when it comes to uptime and speed.', UMS_LANG_CODE)?></div>
					<a href="https://kinsta.com?kaid=MNRQQASUYJRT">
						<img src="<?php echo frameUms::_()->getModule('supsystic_promo')->getModPath()?>img/kinsta_banner.png" style="width: 300px;height: 250px;" />
					</a>
				</div>
				<div class="server-settings">
					<h3><?php _e('Server Settings', UMS_LANG_CODE)?></h3>
					<ul class="settings-list">
						<?php foreach($this->serverSettings as $title => $element) {?>
							<li class="settings-line">
								<div class="settings-title"><?php echo $title?>:</div>
								<span><?php echo $element['value']?></span>
							</li>
						<?php }?>
					</ul>
				</div>
			</div>
			<div class="supsistic-half-side-box" style="border-left: 1px solid rgba(164, 170, 172, 0.28);padding-left: 20px;">
				<div class="supsystic-overview-news">
					<h3><?php _e('News', UMS_LANG_CODE)?></h3>
					<div class="supsystic-overview-news-content">
						<?php echo $this->news?>
					</div>
					<a href="<?php echo $this->mainLink?>" class="button button-primary button-hero" style="float: right; margin-top: 10px;">
						<i class="fa fa-info-circle"></i>
						<?php _e('All news and info', UMS_LANG_CODE)?>
					</a>
					<div style="clear: both;"></div>
				</div>
				<div class="overview-contact-form">
					<h3><?php _e('Contact form', UMS_LANG_CODE)?></h3>
					<form id="form-settings">
						<table class="contact-form-table">
							<?php foreach($this->contactFields as $fName => $fData) { ?>
								<?php
									$htmlType = $fData['html'];
									$id = 'contact_form_'. $fName;
									$htmlParams = array('attrs' => 'id="'. $id. '"');
									if(isset($fData['placeholder']))
										$htmlParams['placeholder'] = $fData['placeholder'];
									if(isset($fData['options']))
										$htmlParams['options'] = $fData['options'];
									if(isset($fData['def']))
										$htmlParams['value'] = $fData['def'];
									if(isset($fData['valid']) && in_array('notEmpty', $fData['valid']))
										$htmlParams['required'] = true;
								?>
							<tr>
								<th scope="row">
									<label for="<?php echo $id?>"><?php echo $fData['label']?></label>
								</th>
								<td>
									<?php echo htmlUms::$htmlType($fName, $htmlParams)?>
								</td>
							</tr>
							<?php }?>
							<tr>
								<th scope="row" colspan="2">
									<?php echo htmlUms::hidden('mod', array('value' => 'supsystic_promo'))?>
									<?php echo htmlUms::hidden('action', array('value' => 'sendContact'))?>
									<button class="button button-primary button-hero" style="float: right;">
										<i class="fa fa-upload"></i>
										<?php _e('Send email', UMS_LANG_CODE)?>
									</button>
									<div style="clear: both;"></div>
								</th>
							</tr>
						</table>
					</form>
					<div id="form-settings-send-msg" style="display: none;">
						<i class="fa fa-envelope-o"></i>
						<?php _e('Your email was send, we will try to respond to you as soon as possible. Thank you for support!', UMS_LANG_CODE)?>
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</section>