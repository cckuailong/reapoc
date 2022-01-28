<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC envato class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_contextual extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;
    public $settings;

    public function __construct()
    {
        // Import MEC Main
        $this->main = $this->getMain();

        // Import MEC Factory
        $this->factory = $this->getFactory();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }

    /**
     * Initialize the auto update class
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // updating checking
        //$this->factory->filter('contextual_help', array($this, 'contextual'), 10, 2);
    }

    public function contextual( $contextual_help, $screen_id)
    {
        $screen = get_current_screen();
        switch($screen_id)
        {
            case 'm-e-calendar_page_MEC-settings':

                // To add a whole tab group
                $screen->add_help_tab(array
                (
                    'id' => 'mec-settings',
                    'title' => __('Settings', 'modern-events-calendar-lite'),
                    'callback' => array($this, 'settings')
                ));

                $screen->add_help_tab(array
                (
                    'id' => 'mec-form',
                    'title' => __('Booking Form', 'modern-events-calendar-lite'),
                    'content' => __('<h2 class="dark-text">Booking Form<hr></h2>
                        <strong>Build your booking registration form (This form will be used for every attendee).</strong>                   <iframe width="600" height="300" src="https://www.youtube.com/embed/YM8cCOvgpk0" frameborder="0" allowfullscreen></iframe>', 'modern-events-calendar-lite')
                ));

                $screen->add_help_tab(array
                (
                    'id' => 'mec-gateways',
                    'title' => __('Payment Gateways', 'modern-events-calendar-lite'),
                    'content' => __('<h2 class="dark-text">Payment Gateways <hr></h2><iframe width="600" height="300" src="https://www.youtube.com/embed/Hpg4chWlxoQ" frameborder="0" allowfullscreen></iframe>', 'modern-events-calendar-lite')
                ));

                $screen->add_help_tab(array
                (
                    'id' => 'mec-notifications',
                    'title' => __('Notifications', 'modern-events-calendar-lite'),
                    'content' => __('<h2 class="dark-text">Notifications <hr></h2><strong>You can edit your messages in there.</strong><strong>MEC Notificatoin Module</strong><iframe width="600" height="300" src="https://www.youtube.com/embed/ZAA8zVewOj0" frameborder="0" allowfullscreen></iframe>', 'modern-events-calendar-lite')
                ));

                break;

            case 'm-e-calendar_page_MEC-ix':

                $screen->add_help_tab(array
                (
                    'id' => 'mec-importexport',
                    'title' => __('Google Cal. Import', 'modern-events-calendar-lite'),
                    'content' => __('<h2 class="dark-text">Import/Export<hr></h2><div class="big-title2"><strong>Google Cal. Import:</strong></div><iframe width="854" height="420" src="https://www.youtube.com/embed/vxHC7NVbmuc" frameborder="0" allowfullscreen></iframe>', 'modern-events-calendar-lite')
                ));

                $screen->add_help_tab(array
                (
                    'id' => 'mec-importexportg',
                    'title' => __('Google Cal. Export', 'modern-events-calendar-lite'),
                    'content' => __('<h2 class="dark-text">Import/Export<hr></h2><div class="big-title2"><strong>Google Cal. Export:</strong></div><iframe width="854" height="420" src="https://www.youtube.com/embed/DdeNazxbLyo" frameborder="0" allowfullscreen></iframe>', 'modern-events-calendar-lite')
                ));

                $screen->add_help_tab(array
                (
                    'id' => 'mec-importexportf',
                    'title' => __('Facebook Cal. Import', 'modern-events-calendar-lite'),
                    'content' => __('<h2 class="dark-text">Import/Export<hr></h2><div class="big-title2"><strong>Facebook Cal. Import:</strong></div><iframe width="854" height="420" src="https://www.youtube.com/embed/dqgt1b8X8hs" frameborder="0" allowfullscreen></iframe>', 'modern-events-calendar-lite')
                ));

                break;
        }

        return $contextual_help;
    }

    public function settings()
    {
        ?>
        <div class="mec-form-row" id="mec_setting_contextual">
            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="genral_setting" data-status="close"><?php _e('General Options','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-genral_setting" style="display: none;">
                    <h2 class="dark-text">GENERAL OPTIONS<hr></h2>
                    <ol class="list-w">
                        <li>Archive Page Title </li>
                        <li>Archive Page Skin </li>
                        <iframe width="600" height="300" src="https://www.youtube.com/embed/QXvIhrazZTg" frameborder="0" allowfullscreen></iframe>
                        <li>Manage View Categories </li>
                        <iframe width="600" height="300" src="https://www.youtube.com/embed/iu6GTZQG9pY" frameborder="0" allowfullscreen></iframe>
                        <li>Time Format (Change time format to 24H or AM/PM) </li>
                        <li>You can set your events as non expired at 1 or 2 hours after the Start Date Events or the End Date Event.</li>
                        <iframe width="600" height="300" src="https://www.youtube.com/embed/gZ9xp2xFUz8" frameborder="0" allowfullscreen></iframe>
                        <li>If your events start date, start frm ex 2016-10-03 to 2016-10-18 if you choose "Show only first day" in all of skins event show time will be only 2016-10-03 but if you select "Show all days" it will be show event from start date to end date in skins.</li>
                        <iframe width="600" height="300" src="https://www.youtube.com/embed/4tRTj43F85s" frameborder="0" allowfullscreen></iframe>
                        <li>If you disable it, then you should create a page as archive page of MEC. Page's slug must equal to "Main Slug" of MEC. Also it will disable all of MEC rewrite rules. </li>
                        <li> If this option is Enable, by deleting the MEC, all the data would be deleted from database. If it is Disable, by deleting MEC, you can still keep your data in database. </li>
                        <li>You can remove Suffixes by this option. </li>
                    </ol>
                </ul>
            </ul>            

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="slugs" data-status="close"><?php _e('Slugs/Permalinks','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-slugs" style="display: none;">
                <h2 class="dark-text">SLUG OPTIONS<hr></h2>
                    <strong>Setup slug of category and main events slug.</strong>
                    <div class="vertical-space1"></div>
                    <ol class="list-w">
                        <li>Main Slug <span class="size10">The post slug is the user friendly and URL valid name of a post. Most common usage of this feature is to create a permalink for each post <a href="http://www.wpbeginner.com/glossary/post-slug/">see more</a></span></li>
                        <li>Change category slug</li>
                    </ol>
                </ul>
            </ul>                  

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="event_detail" data-status="close"><?php _e('Event Details/Single Event Page','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-event_detail" style="display: none;">
                <h2 class="dark-text">Event Details/Single Event Page<hr></h2>
                <ol class="list-w">
                    <li>In single events, you can change the date format. For more information please refer to <a href="#line10_4">All Date Format</a></li>
                    <li>For the events which are on repeat, by choosing Next Occurrence Date, your site user would be able to view Ongoing Events Date, and if it is on Referred Date, they can see the date of the event which they have selected. </li>
                    <iframe width="600" height="300" src="https://www.youtube.com/embed/x4834_oZerU" frameborder="0" allowfullscreen></iframe>
                    <li>We have two styles for Singe Events which you can select.</li>
                    <iframe width="600" height="300" src="https://www.youtube.com/embed/EC2VFK_Bdgs" frameborder="0" allowfullscreen></iframe>
                </ol>
                </ul>
            </ul>            

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="currency" data-status="close"><?php _e('Currency Options','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-currency" style="display: none;">
                <h2 class="dark-text">CURRENCY OPTIONS <hr></h2>
                    <strong>Setup your event calendar currency option.</strong>
                    <ol class="list-w">
                        <li>Currency</li>
                        <li>Currency Sign</li>
                        <li>Position currency sign on front end</li>
                        <li>Thousand Separator</li>
                        <li>Decimal Separator</li>
                        <li>No decimal</li>
                    </ol>
                </ul>
            </ul>            

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="g_map" data-status="close"><?php _e('Google Maps Options','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-g_map" style="display: none;">
                <h2 class="dark-text">GOOGLE MAPS OPTIONS<hr></h2>
                <p><strong>Modern Event Calendar is Google map integrated for feature access, modern view calendar needs api key from Google to contract with Google and fetch your requested map. For activation, check the "Show Google Maps On Event Page".</strong>
                    <div class="vertical-space1"></div>
                    <div class="big-title2"><strong>1- Get API Key</strong></div>
                    <ul class="gd">
                        <li>
                            A. <a href="https://developers.google.com/maps/web/"><b>Click On This</b></a> to start get key from google when page is fully loaded scroll down and click on "GET A KEY" like this :
                            <div class="row">
                                <div class="col-md-10">
                                    <!-- <img src="images/settings/get_key.png" width="100%" alt="Get Google Key"> -->
                                </div>
                                <div class="col-md-2"></div>
                            </div>
                        </li>
                        <div class="vertical-space2"></div>
                        <li>B. Sign in with your google account if you dont have google account yet and need help for register please <a href="http://www.wikihow.com/Make-a-Google-Account">check out this link </a> before continue.
                        </li>
                        <div class="vertical-space1"></div>
                        <li>C. After login user will be directed to blew page set drop down to create new project then click on continue .
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- <img src="images/settings/continu.png" width="100%" alt="Get Google Key"> -->
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </li>
                        <div class="vertical-space2"></div>
                        <!--<li>D. Enter your name for google map project and set your domain then click on create!
                                <div class="row">
                                    <div class="col-md-6">
                                        <img src="images/settings/creat.png" width="100%" alt="Create Google Key">
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                            </li>-->
                        <li>D. Congratulation you got key!
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- <img src="images/settings/Congratulation.png" width="100%" alt="Congratulation"> -->
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </li>
                        <li>E. Paste your key here now Modern Event calendar can contract with google map!
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- <img src="images/settings/paste_key.png" width="100%" alt="Congratulation"> -->
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </li>
                    </ul>
                    <div class="big-title2"><strong>2- Zoom level</strong></div>
                    <div class="big-title2"><strong>3- Google Maps Style</strong></div>
                    <div class="big-title2"><strong>4- Direction on single event</strong></div>
                    <div class="big-title2"><strong>5- If you active google api on your wordpress plugin or theme by check this link mec will not load api again.</strong></div>
                </ul>
            </ul>            

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="g_recaptcha" data-status="close"><?php _e('Google Recaptcha Options','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-g_recaptcha" style="display: none;">
                    <h2 class="dark-text">GOOGLE RECAPTCHA OPTIONS<hr></h2>
                    <strong>Easily setup your captcha and security on mec by google captcha.</strong>
                    <ol class="list-w">
                        <li>Enable google recaptcha will run module for connection with site key and secret key that user must create it with google recaptcha.</li>
                        <li>Enable google recaptcha on booking module simply with check this option</li>
                        <li>Enable google recaptcha on front event submision for both registred and guest</li>
                    </ol>
                    <iframe width="600" height="300" src="https://www.youtube.com/embed/3E7i1FQ4t0I" frameborder="0" allowfullscreen></iframe>
                </ul>
            </ul>              

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="countdown" data-status="close"><?php _e('Countdown Options','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-countdown" style="display: none;">
                <h2 class="dark-text">COUNTDOWN OPTIONS<hr></h2>
                <strong>1- Shows Event Countdown in event single.</strong><br />
                <strong>2- You can change the Countdown style.</strong>
                <iframe width="600" height="300" src="https://www.youtube.com/embed/irctuuyv_sQ" frameborder="0" allowfullscreen></iframe>
                </ul>
            </ul>           

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="social" data-status="close"><?php _e('Social Networks','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-social" style="display: none;">
                    <h2 class="dark-text">Social Networks <hr></h2>
                    <strong>Show social networks in event single. (For better displaying in grid view, you should just select 3 of them)</strong>
                    <iframe width="600" height="420" src="https://www.youtube.com/embed/TnG9vd2XLg0" frameborder="0" allowfullscreen></iframe>

                </ul>
            </ul>
            
            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="next_event" data-status="close"><?php _e('Next Event Module','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-next_event" style="display: none;">
                <h2 class="dark-text">Next Event Module<hr></h2>
                <iframe width="600" height="420" src="https://www.youtube.com/embed/2CsOdgkBIms" frameborder="0" allowfullscreen></iframe>
                </ul>
            </ul>    
                    
            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="fes" data-status="close"><?php _e('Frontend Event Submission','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-fes" style="display: none;">
                    <h2 class="dark-text">Front Event Submission<hr></h2>
                    <ol class="list-w">
                        <li>By selecting this option ‘Enable event submission by guest (Not Logged-in) users’, you let anyone (even not logged-in) add events</li>
                        <li>By selecting this option ‘Enable mandatory email and name for guest user’ a box would appear which receives the non-logged in user Name and Email</li>
                    </ol>
                    <iframe width="600" height="300" src="https://www.youtube.com/embed/iNnboG_rQBQ" frameborder="0" allowfullscreen></iframe>
                </ul>
            </ul>                         

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="exceptional" data-status="close"><?php _e('Exceptional Days','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-exceptional" style="display: none;">
                <h2 class="dark-text">Exceptional Days<hr></h2>
                <strong>Show exceptional days option on Add/Edit events page</strong>
                <iframe width="600" height="300" src="https://www.youtube.com/embed/tZpqW3MQ7QM" frameborder="0" allowfullscreen></iframe>

                </ul>
            </ul>            

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="booking" data-status="close"><?php _e('Booking','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-booking" style="display: none;">
                <h2 class="dark-text">Booking<hr></h2>
                <strong>By enabling booking module service, Modern Event calendar will be able to handle ticket service with registration form you can easily build up your own form to get information you need before processing payment.</strong>
                <iframe width="600" height="300" src="https://www.youtube.com/embed/YM8cCOvgpk0" frameborder="0" allowfullscreen></iframe>

                </ul>
            </ul>            
            
            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="coupon" data-status="close"><?php _e('Coupons','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-coupon" style="display: none;">
                    <h2 class="dark-text">Coupons<hr></h2>
                    <strong>When you enable the option Coupons in Dashboard > Booking, an option would be added ‘Coupons’ which you can add coupons<br />Show coupons in event single.</strong>
                </ul>
            </ul>
                  
            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="buddy" data-status="close"><?php _e('BuddyPress Integration','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-buddy" style="display: none;">
                    <h2 class="dark-text">Buddypress<hr></h2>
                    <iframe width="600" height="300" src="https://www.youtube.com/embed/ZNZOSgXO16o" frameborder="0" allowfullscreen></iframe>
                </ul>
            </ul>  

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="mailchimp" data-status="close"><?php _e('Mailchimp Integration','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-mailchimp" style="display: none;">
                <h2 class="dark-text">Mailchimp Integration<hr></h2>
                <strong>API Key : Mailchimp account > Extras > API Key > Copy APIKey</strong>
                <br>
                <strong>List ID : Mailchimp List > Setting > Copy Unique id for list </strong>
                <iframe width="600" height="300" src="https://www.youtube.com/embed/CDY-EEZhwK8" frameborder="0" allowfullscreen></iframe>

                </ul>
            </ul>   

            <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                <li class="mec-acc-label" data-key="activation" data-status="close"><?php _e('MEC Activation','modern-events-calendar-lite'); ?></li>
                <ul id="mec-acc-activation" style="display: none;">
                    <h2 class="dark-text">How to Activate Plugin<hr></h2>
                    <p>To activate the M.E.Calendar, you should enter the exact purchase code without using any space in <strong>M.E.Calendar > Setting > Settings (bottom of the page)</strong>, then Save Changes. By refreshing the page, your purchase code would be verified. </p>
                    <p><strong>Please Note: If you activate MEC on localhost (or another domain), you should delete the plugin completely to activate it with the same purchase code, then you can activate it on a new domain. </strong></p>
                    <iframe width="600" height="300" src="https://www.youtube.com/embed/c9DKvsrxD9I" frameborder="0" allowfullscreen></iframe>
                </ul>
            </ul>
        </div>
        <?php
    }
}