<?php
/** no direct access **/
defined('MECEXEC') or die();
?>
<div id="webnus-dashboard" class="wrap about-wrap mec-addons">
    <div class="welcome-head w-clearfix">
        <div class="w-row">
            <div class="w-col-sm-9">
                <h1> <?php echo __('Go Pro', 'modern-events-calendar-lite'); ?> </h1>
            </div>
            <div class="w-col-sm-3">
                <?php $styling = $this->main->get_styling(); $darkadmin_mode = ( isset($styling['dark_mode']) ) ? $styling['dark_mode'] : ''; if ($darkadmin_mode == 1): $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w2.png'; else: $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w.png'; endif; ?>
                <img src="<?php echo $darklogo; ?>" />
                <span class="w-theme-version"><?php echo __('Version', 'modern-events-calendar-lite'); ?> <?php echo MEC_VERSION; ?></span>
            </div>
        </div>
    </div>
    <div class="mec-custom-msg-notification-set-box extra">
        <div class="w-row mec-custom-msg-notification-wrap">
            <div class="w-col-sm-12">
                <div class="w-clearfix w-box mec-cmsg-notification-box-wrap mec-go-pro-features-wrap">
                    <div class="w-box-head">Pro Features<span></span></div>
                    <div class="w-box-content">
                        <div class="mec-addons-notification-box-image"> <img src="<?php echo plugin_dir_url(__FILE__ ); ?>../../../assets/img/mec-lite-backend-go-pro.png" /></div>
                        <div class="mec-addons-notification-box-content mec-new-addons">
                            <div class="w-box-content">
                                <div class="mec-go-pro-content-title">Modern Events Calendar Pro</div>
                                <p>Modern Events Calendar has been created according to trending design methods and latest technology with attractive design.
                                We gathered all features of event calendar as you desire with advanced booking system and many other useful options which are practical in real world! Below you can see a list of some of the pro features:</p>
                                <div class="mec-addons-key-features">
                                    <ul>
                                        <li>Booking System</li>
                                        <li>Reporting Dashboard</li>
                                        <li>Timetable View</li>
                                        <li>Map view + Directions</li>
                                        <li>Reminders Notification</li>
                                        <li>Weather module</li>
                                        <li>PDF invoice</li>
                                        <li>Mailchimp Integration</li>
                                        <li>Campaign Monitor Integration</li>
                                    </ul>
                                    <ul>
                                        <li>Cancellation Booking</li>
                                        <li>Attendee list</li>
                                        <li>Organizer payment (Commission System)</li>
                                        <li>Advanced Tax/Fee system</li>
                                        <li>Ticket Variations</li>
                                        <li>Coupons/Discount system</li>
                                        <li>Synchronization</li>
                                        <li>MailerLite Integration</li>
                                        <li>Constant Contact Integration</li>
                                    </ul>
                                </div>
                                <div style="clear:both"></div><a href="https://webnus.net/modern-events-calendar/features/?ref=17" target="_blank">Find out more</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="welcome-content w-clearfix extra">
        <div class="w-row mec-pro-notice">
            <div class="w-col-sm-12">
                <div class="info-msg">
                    <p>
                        <?php echo sprintf(__("You're using %s version of Modern Events Calendar. To use advanced booking system, modern skins like Agenda, Timetable, Masonry, Yearly View, Available Spots, etc you should upgrade to the Pro version.", 'modern-events-calendar-lite'), '<strong>'.__('lite', 'modern-events-calendar-lite').'</strong>'); ?>
                    </p>
                    <a class="info-msg-link" href="<?php echo $this->main->get_pro_link(); ?>" target="_blank">
                        <?php esc_html_e('GO PREMIUM', 'modern-events-claendar-lite'); ?>
                    </a>
                    <div class="info-msg-coupon">
                        <?php echo sprintf(__("Easily get a discount coupon by rating us on %s or following and reposting us on social media. Just send a screenshot to %s and you'll receive the %s", 'modern-events-calendar-lite'), '<a href="https://wordpress.org/plugins/modern-events-calendar-lite/#reviews" target="_blank">'.__('WordPress', 'modern-events-calendar-lite').'</a>', '<a href="mailto:sales@webnus.net" target="_blank">sales@webnus.net</a>','<strong>'.__('Copouns!', 'modern-events-calendar-lite').'</strong>'); ?>
                    </div>
                    <div class="socialfollow">
                        <a target="_blank" href="https://www.facebook.com/WebnusCo/" class="facebook">
                            <i class="mec-sl-social-facebook"></i>
                        </a>
                        <a target="_blank" href="https://twitter.com/webnus" class="twitter">
                            <i class="mec-sl-social-twitter"></i>
                        </a>
                        <a target="_blank" href="https://www.instagram.com/webnus/" class="instagram">
                            <i class="mec-sl-social-instagram"></i>
                        </a>
                        <a target="_blank" href="https://www.youtube.com/channel/UCmQ-VeVK7nLR3bGpAkSYB1Q" class="youtube">
                            <i class="mec-sl-social-youtube"></i>
                        </a>
                        <a target="_blank" href="https://dribbble.com/Webnus" class="dribbble">
                            <i class="mec-sl-social-dribbble"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>