<?php
$elements_widgets = get_option( 'mystickyelements-widgets' );//get_option( 'mystickyelements-contact-field' );
//$stickyelements_widgets = get_option( 'mystickyelements-widgets' );

if ( !isset( $stickyelements_widgets[0]['status'])) {
	$widget_status = 1;
}
if ( isset( $stickyelements_widgets[0]['status']) ) {
	$widget_status = $stickyelements_widgets[0]['status'];
}
?>
<div class="wrap mystickyelement-wrap">
	<div class="mystickyelement-dashboard mystickyelement-dashboard-free">
		<div class="container">
			<?php if( !empty($elements_widgets) ):?>
				<div class="mystickyelement-widgets">
					<table class="mystickyelement-widgets-lists">
						<thead>
							<tr>
								<th><?php esc_html_e( 'My Sticky Elements', 'mystickyelement');?></th>
								<th><?php esc_html_e( 'Edit', 'mystickyelement');?></th>
								<th><?php esc_html_e( 'Status', 'mystickyelement');?></th>
								<th><?php esc_html_e( 'Quick Action', 'mystickyelement');?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><a href="<?php echo admin_url('admin.php?page=my-sticky-elements&widget=0'  )?>"><?php echo ( isset($elements_widgets[0])) ? $elements_widgets[0]: 'MyStickyElements #1'; ?></a></td>
								<td>
									<a href="<?php echo admin_url('admin.php?page=my-sticky-elements&widget=0'  )?>" class="mystickyelement-widgets-lists-edit-btn">
										<i class="fas fa-pencil-alt"></i>
									</a>
								</td>
								<td>
									<label class="myStickyelements-switch">
										<input type="checkbox" data-id="0" class="mystickyelement-widgets-lists-enabled" name="" value="1" <?php checked( $widget_status, 1); ?> />
										<span class="slider round"></span>
									</label>

									<div class="mystickyelements-action-popup-open mystickyelements-action-popup-status" id="widget-status-popup-0" style="display:none;">
										<div class="popup-ui-widget-header">
											<span id="ui-id-1" class="ui-dialog-title"><?php echo esc_html_e( 'Are you sure?', 'mystickyelement');?></span><span class="close-dialog" data-id="0" data-from ='widget-status'> &#10006 </span>
										</div>	
										<div id="widget-delete-confirm" class="ui-widget-content"><p><?php 
											echo esc_html_e( "You're about to turn off the widget. By turning it off, this widget won't appear on your website. Are you sure?", "mystickyelement");
										?></p></div>
										<div class="popup-ui-dialog-buttonset"><button type="button" class="btn-disable-cancel" data-id="0"><?php echo esc_html_e('Disable anyway','mystickyelement');?></button><button type="button" class="mystickyelement-keep-widget-btn" data-id="0"><?php echo esc_html_e('Keep using','mystickyelement');?></button></div>
									</div>
									<div id="mystickyelement-status-popup-overlay-0" class="stickyelement-overlay" style="display:none;" data-id="0" data-from="widget-status"></div>
								</td>
								<td>
									<!--<i class="fas fa-ellipsis-h stickyelement-action-popup" data-id="0"</i> -->
									<!-- <?php //echo admin_url('admin.php?page=my-sticky-elements-upgrade');?> -->
									<div class="mystickyelement-quick-action-wrap">
										<div class="mystickyelements-custom-fields-tooltip"><a class="mystickyelements-tooltip dashboard mystickyelemt-rename-widget" href="#" data-id="0"><svg width="20" height="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.93754 1.83337H12.6024C12.7766 1.83343 12.9442 1.8996 13.0715 2.01852C13.1988 2.13743 13.2762 2.30023 13.2881 2.47401C13.2999 2.6478 13.2454 2.81961 13.1355 2.95473C13.0256 3.08985 12.8684 3.17821 12.6959 3.20196L12.6024 3.20837H11.4584V18.7917H12.6005C12.7667 18.7917 12.9272 18.8519 13.0524 18.9611C13.1776 19.0703 13.259 19.2211 13.2816 19.3857L13.288 19.4792C13.288 19.6453 13.2279 19.8059 13.1187 19.9311C13.0095 20.0563 12.8586 20.1377 12.694 20.1603L12.6005 20.1667H8.93754C8.76335 20.1667 8.59568 20.1005 8.4684 19.9816C8.34112 19.8626 8.26372 19.6999 8.25185 19.5261C8.23998 19.3523 8.29451 19.1805 8.40444 19.0454C8.51436 18.9102 8.67148 18.8219 8.84404 18.7981L8.93754 18.7917H10.0825V3.20837H8.93754C8.77141 3.20837 8.61089 3.1482 8.48569 3.039C8.36048 2.92981 8.27905 2.77896 8.25646 2.61437L8.25004 2.52087C8.25005 2.35474 8.31021 2.19423 8.41941 2.06902C8.52861 1.94381 8.67945 1.86238 8.84404 1.83979L8.93754 1.83337ZM16.7255 4.58062C17.5154 4.58135 18.2728 4.89546 18.8313 5.45401C19.3899 6.01255 19.704 6.76989 19.7047 7.55979L19.7084 14.4385C19.7089 15.1993 19.4182 15.9316 18.8959 16.4849C18.3736 17.0382 17.6594 17.3706 16.8997 17.414L16.7292 17.4185H12.3796V4.57971H16.7246L16.7255 4.58062ZM9.16396 4.58062L9.15937 17.4167H4.81254C4.02258 17.4167 3.26495 17.103 2.70628 16.5445C2.1476 15.986 1.83362 15.2284 1.83337 14.4385V7.55979C1.83337 6.76967 2.14725 6.0119 2.70595 5.4532C3.26465 4.8945 4.02242 4.58062 4.81254 4.58062H9.16396V4.58062Z" fill="#97A6BA"/></svg></a><p><?php echo esc_html_e('Rename','mystickyelement');?></p></div>
											
										<div class="mystickyelements-custom-fields-tooltip">
										<a class="mystickyelements-tooltip dashboard" href="<?php echo admin_url('admin.php?copy-from=0&page=my-sticky-elements-new-widget');?>"><i class="fas fa-copy"></i></a><p><?php echo esc_html_e('Duplicate','mystickyelement');?></p></div>
										
										<div class="mystickyelement-delete-widget mystickyelements-custom-fields-tooltip" data-id="0">
										<a class="mystickyelements-tooltip dashboard" href="#"><i class="fas fa-trash-alt"></i></a><p><?php echo esc_html_e('Delete','mystickyelement');?></p></div>
										
										<div class="mystickyelements-action-popup-open" id="stickyelement-action-popup-0" style="display:none;">
											<div class="popup-ui-widget-header">
												<span id="ui-id-1" class="ui-dialog-title"><?php echo esc_html_e( 'Are you sure about deleting the widget?', 'mystickyelement');?></span><span class="close-dialog" data-id="0" data-from ='widget-delete'> &#10006 </span>
											</div>	
											<div id="widget-delete-confirm" class="ui-widget-content">
												<p>
												<?php 
													echo esc_html_e( "Are you sure want to delete the widget? By doing this, you'll delete your saved settings, channels, & information within the widget. You will lose the widget permanently and will not be able to retrieve it.", "mystickyelement");
												?>
												</p>
											</div>
											<div class="popup-ui-dialog-buttonset"><button type="button" class="btn-cancel" data-id="0"><?php echo esc_html_e('Cancel','mystickyelement');?></button><button type="button" class="mystickyelement-delete-widget-btn" data-id="0"><?php echo esc_html_e('Delete anyway','mystickyelement');?> </button></div>
										</div>
										<div id="mystickyelement-action-popup-overlay-0" class="stickyelement-overlay" style="display:none;" data-id="0" data-from="widget-action"></div>
										
										<div class="mystickyelements-action-popup-open mystickyelements-action-popup-rename mystickyelements-action-popup-status" id="stickyelement-widget-rename-popup-0" style="display:none;">
											
											<div class="popup-ui-widget-header">
												<span id="ui-id-1" class="ui-dialog-title"><?php esc_html_e('Rename widget','mystickyelement')?></span>
												<span class="close-dialog" data-id="0" data-from='widget-rename'>&#10006</span>
											</div>
											<div id="widget-delete-confirm" class="ui-widget-content">
												<input type="text" name="widget_rename" value="<?php echo ( isset($elements_widgets[0])) ? $elements_widgets[0]: 'MyStickyElements #1'; ?>" id="widget_rename_0"/>
											</div>
											<div class="popup-ui-dialog-buttonset"><button type="button" class="mystickyelement-cancel-without-color-widget-btn" data-id="0"><?php esc_html_e('Cancel','mystickyelement');?></button><button type="button" class="mystickyelement-btn-rename" data-id="0"><?php esc_html_e('Rename','mystickyelement');?></button></div>
										</div>
										<div id="mystickyelement-rename-popup-overlay-0" class="stickyelement-overlay" style="display:none;" data-id="0" data-from="widget-rename"></div>
									</div>
								</td>
							</tr>								
						</tbody>
					</table>
				</div>	
				<div class="mystickyelement-widgets-btn-wrap">
					<a href="<?php echo admin_url('admin.php?page=my-sticky-elements-new-widget')?>" class="btn"><i class="fas fa-plus-circle"></i>&nbsp; Create a New Widget</a>
				</div>	
				
				<?php 
					global $wpdb;
					$table_name = $wpdb->prefix . "mystickyelement_contact_lists";
					$result = $wpdb->get_results ( "SELECT * FROM ".$table_name ." ORDER BY ID DESC LIMIT 5" );
					
					function dateDiffInDays($date1, $date2)  
					{ 
						$diff = strtotime($date2) - strtotime($date1); 
						return abs(round($diff / 86400)); 
					} 
					
					function set_lead_message($message_date,$contact_name){
						$messageDate = date_format($message_date,"d-M-Y");
						$messageTime = date_format($message_date,"h:i A");
						$currentDate = date("d-M-Y");
						//$diff = abs(strtotime($currentDate) - strtotime($messageDate));
						//$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
						$days = dateDiffInDays($currentDate, $messageDate);
						
						if($days == 0){
							$message = "<p><span>".$contact_name."</span> has left message on today on ". $messageTime."</p>";
						}
						else if($days == 1){
							$message = "<p><span>".$contact_name."</span> has left message on 1 day ago on ". $messageTime."</p>";
						}
						else if($days == 2){
							$message = "<p><span>".$contact_name."</span> has left message on 2 days ago on ". $messageTime."</p>";
						}
						else if($days == 3){
							$message = "<p><span>".$contact_name."</span> has left message on 3 days ago on ". $messageTime."</p>";
						}
						else if($days == 4){
							$message = "<p><span>".$contact_name."</span> has left message on 4 days ago on ". $messageTime."</p>";
						}
						else if($days == 5){
							$message = "<p><span>".$contact_name."</span> has left message on 5 days ago on ". $messageTime."</p>";
						}
						else if($days == 6){
							$message = "<p><span>".$contact_name."</span> has left message on 6 days ago on ". $messageTime."</p>";
						}
						else if($days == 7){
							$message = "<p><span>".$contact_name."</span> has left message on 7 days ago on ". $messageTime."</p>";
						}
						else if($days == 8){
							$message = "<p><span>".$contact_name."</span> has left message on 8 days ago on ". $messageTime."</p>";
						}
						else if($days == 9){
							$message = "<p><span>".$contact_name."</span> has left message on 9 days ago on ". $messageTime."</p>";
						}
						else if($days == 10){
							$message = "<p><span>".$contact_name."</span> has left message on 10 days ago on ". $messageTime."</p>";
						}
						else{
							$message = "<p><span>".$contact_name."</span> has left message on ".$messageDate." on ". $messageTime."</p>";
						}
						return $message;		
					}
				?>
				<div class="mystickyelement-tab-boxes">
					<div class="mystickyelement-tab-box-documentation">
						<div class="mystickyelement-tab-boxes-wrap">
							<div class="mystickyelement-tab-box title-box">
								<label><i class="far fa-edit"></i>&nbsp;&nbsp; <?php esc_html_e('DOCUMENTATION','mystickyelements');?></label>
							</div>
							<div class="mystickyelement-tab-box-content">
								<ul class="documents-wrap-list">
									<li><a href="https://premio.io/help/mystickyelements/how-to-use-my-sticky-elements/" target="_blank"><?php esc_html_e('How to use MyStickyElements like a pro?','mystickyelements');?></a></li>
									<li><a href="https://premio.io/help/mystickyelements/how-to-use-my-sticky-elements/" target="_blank"><?php esc_html_e('How do I change or translate My Sticky Elements placeholders?','mystickyelements');?></a></li>
									<li><a href="https://premio.io/help/mystickyelements/how-to-use-my-sticky-elements/" target="_blank"><?php esc_html_e('How do I send my contact form leads to email?','mystickyelements');?></a></li>
									<li><a href="https://premio.io/help/mystickyelements/how-to-use-my-sticky-elements/" target="_blank"><?php esc_html_e('How do I create a custom link or JavaScript channel?','mystickyelements');?></a></li>
									<li><a href="https://premio.io/help/mystickyelements/how-to-use-my-sticky-elements/" target="_blank"><?php esc_html_e('How do I create a custom shortcode/HTML channel to your widget?','mystickyelements');?></a></li>
								</ul>	
								<div class="mystickyelement-tab-boxes-btn-wrap">
									<a href="https://premio.io/help/mystickyelements/" target="_blank" class="btn"><?php esc_html_e('Explore all docs','mystickyelements');?><i class="fas fa-arrow-circle-right"></i></a>
								</div>
							</div>
						</div>
					</div>	
					<div class="mystickyelement-tab-box-form-leads">
						<div class="mystickyelement-tab-boxes-wrap">
							<div class="mystickyelement-tab-box title-box">
								<label><i class="fas fa-history"></i></i>&nbsp;&nbsp;<?php esc_html_e('RECENT FORM LEADS','mystickyelements');?></label>
							</div>
							<?php 
							
								if(count($result)>0){
									?>
									<div class="mystickyelement-tab-box-content">
										<ul class="leads-wrap-list">
											<?php 
												foreach($result as $lead){
													$messageDate = date_create($lead->message_date);
													$contact_name = $lead->contact_name;
													$message = set_lead_message($messageDate,$contact_name);
													echo "<li>".$message."</li>";
												}
											?>
										</ul>
										<div class="mystickyelement-tab-boxes-btn-wrap">
										
											<a href="<?php echo admin_url('admin.php?page=my-sticky-elements-leads')?>" class="btn"> <?php esc_html_e('View all leads','mystickyelements');?>&nbsp;<i class="fas fa-arrow-circle-right"></i></a>
										</div>
									</div>
									<?php	
								}
								else{
									?>
									<div class="mystickyelement-tab-box-content mystickyelement-tab-box-content-empty-leads">
										<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/no_lead.svg" width="269" height="184"/>	
										<p><strong><?php esc_html_e("You're all caught up.","mystickyelements");?></strong>&nbsp;<?php esc_html_e("When you receive a new lead, it'll show up here.","mystickyelements");?></p>
									</div>
									<?php
								}
							?>
						</div>
					</div>
					<div class="mystickyelement-tab-box-integration">
						<div class="mystickyelement-tab-boxes-wrap">
							<div class="mystickyelement-tab-box title-box">
								<label><i class="fas fa-link"></i>&nbsp;&nbsp;<?php esc_html_e('INTEGRATION','mystickyelements');?></label>
							</div>
							<div class="mystickyelement-tab-box-content mystickyelement-tab-box-content-integration">
								<div class="mystickyelement-tab-integration-row">
									<div class="mystickyelement-tab-integration-col integration-logo">
										<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/mailchimp.png" width="25px" height="25px">
									</div>
									<div class="mystickyelement-tab-integration-col-title">
										<h3><?php esc_html_e('Mailchimp','mystickyelements');?></h3>
										<p><?php esc_html_e(' Sync your leads automatically to your Mailchimp list','mystickyelements'); ?></p>
									</div>
									<div class="mystickyelement-tab-integration-action">
										<a href="<?php echo admin_url('admin.php?page=my-sticky-elements-integration')?>"><?php esc_html_e('Connect','mystickyelements');?></a>		
									</div>
									<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
								</div>
								<div class="mystickyelement-tab-integration-row">
									<div class="mystickyelement-tab-integration-col integration-logo">
										<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/mailpoet.png" width="25px" height="25px">
									</div>
									<div class="mystickyelement-tab-integration-col-title">
										<h3><?php esc_html_e('Mailpoet','mystickyelements');?></h3>
										<p><?php esc_html_e('Sync your leads automatically to your Mailpoet list','mystickyelements');?></p>
									</div>
									<div class="mystickyelement-tab-integration-action">
										<a href="<?php echo admin_url('admin.php?page=my-sticky-elements-integration')?>"><?php esc_html_e('Connect','mystickyelements');?></a>
									</div>
									<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
								</div>
							</div>
						</div>
					</div>
				</div>			
			<?php else :?>
				<div class="mystickyelement-welcome-wrap">
					<div class="mystickyelement-welcome-img">
						<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/welcome_img.svg" width="438" height="317" alt="Welcome Image" />
					</div>
					<div class="mystickyelement-heading">
						<h3><?php esc_html_e( 'Welcome to My Sticky Elements', 'mystickyelement'); ?> 🎉</h3>
					</div>
					
					<div class="mystickyelement-content">
						<ul>
							<li><?php esc_html_e( 'Add different elements like forms, chat icons, social media icon channels, custom fields, and combine them together into 1 element', 'mystickyelement'); ?></li>
							<li><?php esc_html_e( 'Customize your form, chat, and social icons as you see fit', 'mystickyelement'); ?></li>
							<li><?php esc_html_e( 'Configure triggers & targeting rules for the behavior of the widget. Explore advanced settings for fine tuning even the smallest detail', 'mystickyelement'); ?></li>
							
							<li>Discover more on our <a href="https://premio.io/help/mystickyelements" target="_blank">Help Center</a> for video tutorials and documentation</li>
						</ul>
					</div>
					
					<div class="create-mystickyelement mystickyelement-widgets-btn-wrap">
						<a href="<?php echo admin_url( 'admin.php?page=my-sticky-elements&widget=0' )?>" class="btn"><span><i class="fas fa-plus"></i></span> Create Your First Widget</a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>	
</div>