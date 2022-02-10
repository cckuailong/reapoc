<?php 
wp_enqueue_style('mec-lity-style', $this->main->asset('packages/lity/lity.min.css'));
wp_enqueue_script('mec-lity-script', $this->main->asset('packages/lity/lity.min.js'));
?>
<div id="webnus-dashboard" class="wrap about-wrap">
    <div class="welcome-head w-clearfix">
        <div class="w-row">
            <div class="w-col-sm-9">
                <h1> <?php echo __('Support', 'modern-events-calendar-lite'); ?> </h1>
                <div class="w-welcome">
                    <div class="support-page-links link-to-doc"><a href="https://webnus.net/dox/modern-events-calendar/" target="_blank"><?php esc_html_e('Documentation' , 'modern-events-calendar-lite'); ?></a></div>
                    <div class="support-page-links link-to-videos"><a href="https://webnus.net/dox/modern-events-calendar/video-tutorials/" target="_blank"><?php esc_html_e('All videos' , 'modern-events-calendar-lite'); ?></a></div>
                    <div class="support-page-links link-to-articles"><a href="https://webnus.net/dox/modern-events-calendar/category/knowledge/" target="_blank"><?php esc_html_e('All Articles' , 'modern-events-calendar-lite'); ?></a></div>
                    <p>
                        <?php esc_html_e('If you have any questions regarding Modern Events Calendar and how to use it, you can use the following four methods we have prepared in this page. The detailed documentations of MEC along with its instructional videos will help you have a great experience working with it.So, if  you need futher instructions using the plugin, please first refer to the following to find your answers.' , 'modern-events-calendar-lite'); ?>
                    </p>
                </div>
            </div>
            <div class="w-col-sm-3">
                <img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w.png'; ?>" />
                <span class="w-theme-version"><?php echo __('Version', 'modern-events-calendar-lite'); ?> <?php echo MEC_VERSION; ?></span>
            </div>
        </div>
    </div>
    <div class="welcome-content w-clearfix extra">

    <?php if(!$this->getPRO()): ?>
        <div class="w-row mec-pro-notice">
            <div class="w-col-sm-12">
                <div class="info-msg support-box">
                    <p><?php echo sprintf(__("%s, if you need support, you can purchase our Extra Support feature through links below:", 'modern-events-calendar-lite'), '<strong>'.__('Dear user', 'modern-events-calendar-lite').'</strong>'); ?></p>
                    <a target="_blank" href="https://webnus.net/checkout?edd_action=add_to_cart&download_id=960896"> Get 12 Month Premium Support </a>
                    <a target="_blank" href="https://webnus.net/checkout?edd_action=add_to_cart&download_id=960724"> Get 6 Month Premium Support </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($this->getPRO()): ?>
        <div class="w-row mec-pro-notice">
            <div class="w-col-sm-12">
                <div class="info-msg support-box">
                    <p><?php echo sprintf(__("%s, we won't charge you for any extra price after a year for using MEC or receiving updates, but you will need to renew your license if you needed support by then. You can use links below in order to do that:", 'modern-events-calendar-lite'), '<strong>'.__('Dear user', 'modern-events-calendar-lite').'</strong>'); ?></p>
                    <a target="_blank" href="https://webnus.net/checkout?edd_action=add_to_cart&download_id=960896"> Get 12 Month Premium Support </a>
                    <a target="_blank" href="https://webnus.net/checkout?edd_action=add_to_cart&download_id=960724"> Get 6 Month Premium Support </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(current_user_can('read')): ?>
        <script src='https://webnus.freshsales.io/web_forms/8dd552ab6041bd25d23d8a8467819f701f9196106be0e25edc6870c9cc922bdc/form.js' crossorigin='anonymous' id='fs_8dd552ab6041bd25d23d8a8467819f701f9196106be0e25edc6870c9cc922bdc'></script>
        <div class="w-row">
            <div class="w-col-sm-12">
                <div class="w-box support-page searchbox">
                    <div class="w-box-content">
                        <p><?php esc_html_e('Advice and answers from the Webnus Team'); ?></p>  
                        <div class="search-form">
                            <form role="search" action="https://webnus.net/dox/modern-events-calendar/" method="get">
                                <div>
                                    <input name="s" type="text" placeholder="Enter Keywords..." class="search-side">
                                    <button type="submit" id="searchsubmit" value="Search" class="btn" formtarget="_blank"><i class="mec-sl-magnifier"></i></button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>       
        </div>

        <div class="w-row">
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox articles">
                    <div class="w-box-head">
                        <?php _e('Setting Up Event', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/category/setup-features/" target="_blank"><?php echo esc_html__('Setup Features', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/add-event/" target="_blank"><?php echo esc_html__('Add Event In MEC Plugin', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/date-and-time/" target="_blank"><?php echo esc_html__('Repeating, Date & Time', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/tickets-and-taxes-fees/" target="_blank"><?php echo esc_html__('Add Tickets & Fee', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/total-booking-limits/" target="_blank"><?php echo esc_html__('Total Booking & User Limits', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/create-events-with-your-page-builder/" target="_blank"><?php echo esc_html__('Create Events With Your Page Builder', 'modern-events-calendar-lite'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('MEC Settings', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php echo esc_html__('General Options', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/frontend-event-submission/" target="_blank"><?php echo esc_html__('Frontend Event Submission', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php echo esc_html__('Single Event', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/google-maps-options/" target="_blank"><?php echo esc_html__('Google Maps Options', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/making-advance-shortcodes-in-modern-event-calendar/" target="_blank"><?php echo esc_html__('Making Advance Shortcodes', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/messages/" target="_blank"><?php echo esc_html__('Messages', 'modern-events-calendar-lite'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div> 
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Booking', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/booking/" target="_blank"><?php echo esc_html__('Booking', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/payment-gateways/" target="_blank"><?php echo esc_html__('Payment Gateways', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/tickets-and-taxes-fees/" target="_blank"><?php echo esc_html__('Add Tickets & Fee', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php echo esc_html__('Notifications', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/organizer-payment/" target="_blank"><?php echo esc_html__('Organizer Payment', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/bookings/" target="_blank"><?php echo esc_html__('Manage The Bookings', 'modern-events-calendar-lite'); ?></a></li>
                        </ul>
                    </div>
            </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Other Articles', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/import-and-export-events/" target="_blank"><?php echo esc_html__('Import & Export Events', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/single-events-sidebar/" target="_blank"><?php echo esc_html__('Single Events Sidebar', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/translate-mec/" target="_blank"><?php echo esc_html__('Translate MEC', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/category/developer-document/" target="_blank"><?php echo esc_html__('Developer Documentation', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/category/knowledge/" target="_blank"><?php echo esc_html__('Knowledge', 'modern-events-calendar-lite'); ?></a></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><a href="https://webnus.net/dox/modern-events-calendar/category/troubleshooting/" target="_blank"><?php echo esc_html__('Troubleshooting', 'modern-events-calendar-lite'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>    
        </div>

        <div class="w-row">
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Quick Setup Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Download the Plugin', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Install and Activate the Plugin', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Add a New Event', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/ZDzcAEtdkC0" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Activate License Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Login to Dashboard', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Get the License Key', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Activate the plugin', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/V8DAZXuVxrQ" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Add New Event Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Add New Events, Date and Time', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Tags, Categories, Organizer, Location', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Hourly Schedule,  Set Up Shortcodes', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/cnVy2YzDMOk" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Enable Booking Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Enable Booking', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Customize the Booking Form', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Set Up a Payment Gateway', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/7X58lpjFDh8" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div>    
        </div>

        <div class="w-row">
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Add Booking Form Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Enable Booking from Settings', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Set Up a Booking Form', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Customize the Booking Form', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/difbDGz6blU" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Create Shortcodes Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Create a New Shortcode', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Customize Shortcodes', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Use Filters in Shortcodes', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/iRIBZZlYq54" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('WooCommerce Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Install and Activate WooCommerce', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Integrate with MEC', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Set Up WooCommerce Gateway', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/ZI9aWMtwYWE" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
            </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box support-page videobox">
                    <div class="w-box-head">
                        <?php _e('Settings Video', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('An Overview of the Settings', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Set Up Different Settings', 'modern-events-calendar-lite'); ?></li>
                            <li><i class="mec-sl-arrow-right-circle"></i><?php echo esc_html__('Use Different Options', 'modern-events-calendar-lite'); ?></li>
                        </ul>
                        <div class="w-button">
                            <a href="https://youtu.be/mdXWngl4Lso" data-lity><i class="mec-sl-control-play"></i><?php echo esc_html__('Watch Video', 'modern-events-calendar-lite'); ?></a>
                        </div>
                    </div>
                </div>
            </div>    
        </div>

        <div class="w-row">
            <div class="w-col-sm-6">
                <div class="w-box support-page faq-box">
                    <div class="w-box-head">
                        <?php _e('FAQ', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <div class="mec-faq-accordion">
                            <div class="mec-faq-accordion-trigger"><a href="" class="active"><?php echo esc_html__('How should I update plugin?', 'modern-events-calendar-lite'); ?></a></div>
                            <div class="mec-faq-accordion-content active">
                            <?php echo sprintf(__('You have two options:<br>
                            1-	Uploading the plugin file using FTP. For more information, please <a href="%s" target="_blank">click here</a>.<br>
                            2-	Using the auto-update feature which needs the adding of the purchase code in the corresponding section in the plugin. For more information, please <a href="%s" target="_blank">click here</a>.', 'modern-events-calendar-lite') , 'https://webnus.net/dox/modern-events-calendar/how-to-update-manually-plugin/', 'https://webnus.net/dox/modern-events-calendar/how-to-auto-update-plugin/' ); ?>
                            </div>

                            <div class="mec-faq-accordion-trigger"><a href=""><?php echo esc_html__('Do I lose all my data or customization if I update MEC?', 'modern-events-calendar-lite'); ?></a></div>
                            <div class="mec-faq-accordion-content"><?php esc_html_e('If you’ve added a file to the main folder of MEC, this file will be removed after the update. Therefore, please do get a full back up before proceeding with the update process.', 'modern-events-calendar-lite'); ?>
                            </div>

                            <div class="mec-faq-accordion-trigger"><a href=""><?php echo esc_html__('Can I customize the event pages?', 'modern-events-calendar-lite'); ?></a></div>
                            <div class="mec-faq-accordion-content">
                            <?php echo sprintf(__('Yes, it is possible. In order to see the related documentations, please <a href="%s" target="_blank">click here</a>.', 'modern-events-calendar-lite') , 'https://webnus.net/dox/modern-events-calendar/mec-theme-integration-guide/' ); ?>
                            </div>

                            <div class="mec-faq-accordion-trigger"><a href=""><?php echo esc_html__('Does MEC have default languages or it needs to be translated?', 'modern-events-calendar-lite'); ?></a></div>
                            <div class="mec-faq-accordion-content">
                            <?php echo sprintf(__('Yes, for a number of languages MEC has the translation file as default. However, since these translations have been done by the users, they may be incomplete, hence updating them might be required. For more information, please <a href="%s" target="_blank">click here</a>.', 'modern-events-calendar-lite') , 'https://webnus.net/dox/modern-events-calendar/translate-mec/' ); ?>
                            </div>

                            <div class="mec-faq-accordion-trigger"><a href=""><?php echo esc_html__('Can I have more than one calendar in one website?', 'modern-events-calendar-lite'); ?></a></div>
                            <div class="mec-faq-accordion-content"><?php esc_html_e( 'Unfortunately, MEC does not support more than 1 calendar in a single website, however, it will be added in its upcoming updates.' , 'modern-events-calendar-lite' ); ?></div>

                            <div class="mec-faq-accordion-trigger"><a href=""><?php echo esc_html__('Can I import/export from/to MEC?', 'modern-events-calendar-lite'); ?></a></div>
                            <div class="mec-faq-accordion-content"><?php esc_html_e( 'Yes, you can get an XML export from MEC data or import the file you\'ve exported to MEC. Also, if you are using one of the following plugins (The event calendar, calendarize it, EventOn, Events Schedule WP Plugin), then you can easily transfer your events to MEC.', 'modern-events-calendar-lite' ); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-6">
                <div class="w-box support-page articles-box">
                    <div class="w-box-head">
                        <?php _e('Articles', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <a href="https://webnus.net/dox/modern-events-calendar/woocommerce/" target="_blank"><?php esc_html_e('MEC And Integrate With WooCommerce' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/create-events-with-your-page-builder/" target="_blank"><?php esc_html_e('Create Events With Your Page Builder' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/how-to-remove-comment-box-on-single-event/" target="_blank"><?php esc_html_e('How to remove comment box on single event?' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/setup-date-option-on-shortcodes/" target="_blank"><?php esc_html_e('Setup Date Option On Shortcodes' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/no-event-found/" target="_blank"><?php esc_html_e('No Event Found, what should I fix?' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/image-size-in-shortcodes/" target="_blank"><?php esc_html_e('How Can I change the image size in shortcodes?' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/register-button-booking-system/" target="_blank"><?php esc_html_e('Booking Module Not Working' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/translate-mec/" target="_blank"><?php esc_html_e('Translate MEC' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/i-want-to-export-booking-what-should-i-do/" target="_blank"><?php esc_html_e('I want to export booking, what should I do?' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/make-advance-shortcode/" target="_blank"><?php esc_html_e('Making Advance Shortcodes' , 'modern-events-calendar-lite'); ?></a>
                        <a href="https://webnus.net/dox/modern-events-calendar/category/developer-document/" target="_blank"><?php esc_html_e('MEC developer documentation' , 'modern-events-calendar-lite'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php if($this->getPRO()) : ?>
        <div class="w-row">
            <div class="w-col-sm-12">
                <div class="w-box support-page mec-ticksy">
                    <div class="w-box-content">
                        <center><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/wn-ms-icon-17-n.svg'; ?>" style="width:100px; margin-top:30px;" /></center>
                        <p style="margin-top:20px;"><?php esc_html_e('You don’t need to register anywhere for support anymore.You can click the following button, and the chat box will open up to ask all your different questions using our various channels.' , 'modern-events-calendar-lite'); ?><?php echo sprintf(__("<br><small style=\"color: #8a8a8a;\">Only enter your email address and the answers will be sent over to your mail box.</small>", 'modern-events-calendar-lite'));?></p>
                        <a href="#" class="support-button"><?php esc_html_e('Create a Support Ticket','modern-events-calendar-lite'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>
<script>
(function($) {

    var allPanels = $('.mec-faq-accordion > .mec-faq-accordion-content');
    $('.mec-faq-accordion>.mec-faq-accordion-content.active').show();

    $('.mec-faq-accordion > .mec-faq-accordion-trigger > a').click(function() {
        $this = $(this);
        $target =  $this.parent().next();
        
        if(!$target.hasClass('active')){
            allPanels.removeClass('active').slideUp();
            $target.addClass('active').slideDown();
            $('.mec-faq-accordion > .mec-faq-accordion-trigger > a').removeClass('active')
            $this.addClass('active');
        } else {
            $this.removeClass('active');
            $target.removeClass('active').slideUp();
        }
    return false;
    });

})(jQuery);
jQuery('.support-button').on('click',function(event, data) {
    event.preventDefault();
    if (window.fcWidget.isOpen() != true) {
        window.fcWidget.open();
    }
});
</script>
<?php if($this->getPRO()) : ?>
<script>
  function initFreshChat() {
    window.fcWidget.init({
      token: "1be9e2ea-febf-4835-b290-5bd097dc2e02",
      host: "https://wchat.freshchat.com"
    });
  }
  function initialize(i,t){var e;i.getElementById(t)?initFreshChat():((e=i.createElement("script")).id=t,e.async=!0,e.src="https://wchat.freshchat.com/js/widget.js",e.onload=initFreshChat,i.head.appendChild(e))}function initiateCall(){initialize(document,"freshchat-js-sdk")}window.addEventListener?window.addEventListener("load",initiateCall,!1):window.attachEvent("load",initiateCall,!1);
</script>
<?php endif; ?>