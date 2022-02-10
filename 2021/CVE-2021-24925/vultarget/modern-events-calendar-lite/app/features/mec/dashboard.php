<?php
/** no direct access **/
defined('MECEXEC') or die();

// get screen id
$current_user = wp_get_current_user();

// user event created
$count_events = wp_count_posts($this->get_main_post_type());
$user_post_count = isset($count_events->publish) ? $count_events->publish : '0';

// user calendar created
$count_calendars = wp_count_posts('mec_calendars');
$user_post_count_c = isset($count_calendars->publish) ? $count_calendars->publish : '0';

// mec location
$user_location_count_l = wp_count_terms('mec_location', array(
    'hide_empty'=>false,
    'parent'=>0
));

// mec orgnizer
$user_organizer_count_l = wp_count_terms('mec_organizer', array(
    'hide_empty'=>false,
    'parent'=>0
));

$version = $verify = NULL;

if($this->getPRO())
{
    // Get MEC New Update
    $envato = $this->getEnvato();

    $v = $envato->get_MEC_info('version');
    $version = isset($v->version) ? $v->version : NULL;
    //$verify = $envato->get_MEC_info('activate');

    $mec_license_status = get_option( 'mec_license_status');
}

// MEC Database
$db = $this->getDB();

// MEC Settings
$settings = $this->get_settings();

// MEC Booking Status
$booking_status = ($this->getPRO() and isset($settings['booking_status']) and $settings['booking_status']) ? true : false;

// Add ChartJS library
if($booking_status) wp_enqueue_script('mec-chartjs-script', $this->asset('js/chartjs.min.js'));

// Whether to show dashboard boxes or not!
$box_support = apply_filters('mec_dashboard_box_support', true);
$box_stats = apply_filters('mec_dashboard_box_stats', true);
?>
<div id="webnus-dashboard" class="wrap about-wrap">
    <div class="welcome-head w-clearfix">
        <div class="w-row">
            <div class="w-col-sm-9">
                <h1> <?php echo sprintf(__('Welcome %s', 'modern-events-calendar-lite'), $current_user->user_firstname); ?> </h1>
                <div class="w-welcome">
                    <!-- <p> -->
                        <?php echo sprintf(__('%s - Most Powerful & Easy to Use Events Management System', 'modern-events-calendar-lite'), '<strong>'.($this->getPRO() ? __('Modern Events Calendar', 'modern-events-calendar-lite') : __('Modern Events Calendar (Lite)', 'modern-events-calendar-lite')).'</strong>'); ?>
                        <?php if(!$this->getPRO()): ?>
                        <span><a href="https://wordpress.org/support/plugin/modern-events-calendar-lite/reviews/#new-post" target="_blank"><?php echo _x('Rate the plugin ★★★★★', 'plugin rate', 'modern-events-calendar-lite'); ?></a></span>
                        <?php endif; ?>
                        <?php if(version_compare(MEC_VERSION, $version, '<')): ?>
                        <span class="mec-tooltip">
                            <div class="box">
                                <h5 class="title"><?php _e('Update', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php esc_attr_e("Update $version is ready for download.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/category/installation/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                        <?php endif; ?>
                    <!-- </p> -->
                </div>
            </div>
            <div class="w-col-sm-3">
                <?php $styling = $this->get_styling(); $darkadmin_mode = ( isset($styling['dark_mode']) ) ? $styling['dark_mode'] : ''; if ($darkadmin_mode == 1): $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w2.png'; else: $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w.png'; endif; ?>
                <img src="<?php echo $darklogo; ?>" />
                <span class="w-theme-version"><?php echo __('Version', 'modern-events-calendar-lite'); ?> <?php echo MEC_VERSION; ?></span>
            </div>
        </div>
    </div>
    <div class="welcome-content w-clearfix extra">
        <div class="w-row">
            <div class="w-col-sm-12">
                <script src='https://webnus.freshsales.io/web_forms/8dd552ab6041bd25d23d8a8467819f701f9196106be0e25edc6870c9cc922bdc/form.js' crossorigin='anonymous' id='fs_8dd552ab6041bd25d23d8a8467819f701f9196106be0e25edc6870c9cc922bdc'></script>
            </div>
        </div>                                   
        <?php if(!$this->getPRO()): ?>
        <div class="w-row mec-pro-notice">
            <div class="w-col-sm-12">
                <div class="info-msg">
                    <p>
                        <?php echo sprintf(__("You're using %s version of Modern Events Calendar. To use advanced booking system, modern skins like Agenda, Timetable, Masonry, Yearly View, Available Spots, etc you should upgrade to the Pro version.", 'modern-events-calendar-lite'), '<strong>'.__('lite', 'modern-events-calendar-lite').'</strong>'); ?>
                    </p>
                    <a class="info-msg-link" href="<?php echo $this->get_pro_link(); ?>" target="_blank">
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
        <?php endif; ?>
        <?php echo $this->mec_custom_msg_2('yes', 'yes'); ?>
        <?php echo $this->mec_custom_msg('yes', 'yes'); ?>
        <?php //echo $this->addons_msg(); ?>
        <div class="w-row">
            <div class="w-col-sm-12">
                <div class="w-box mec-intro-section">
                    <div class="w-box-content mec-intro-section-welcome">
                        <h3><?php _e('Getting started with Modern Events Calendar' , 'modern-events-calendar-lite'); ?></h3>
                        <p><?php _e('In this short video, you can learn how to make an event and put a calendar on your website. Please watch this 2 minutes video to the end.' , 'modern-events-calendar-lite'); ?></p>
                    </div>
                    <div class="w-box-content mec-intro-section-ifarme">
                        <iframe width="784" height="441" src="https://www.youtube.com/embed/FV_X341oyiw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>   
                    </div>
                    <div class="w-box-content mec-intro-section-links wp-core-ui">
                        <a class="mec-intro-section-link-tag button button-primary button-hero" href="<?php esc_html_e(admin_url( 'post-new.php?post_type=mec-events' )); ?>" target="_blank"><?php esc_html_e('Add New Event' , 'modern-events-calendar-lite'); ?>
                        <a class="mec-intro-section-link-tag button button-secondary button-hero" href="<?php esc_html_e(admin_url( 'admin.php?page=MEC-settings' )); ?>" target="_blank"><?php esc_html_e('Settings' , 'modern-events-calendar-lite'); ?>
                        <a class="mec-intro-section-link-tag button button-secondary button-hero" href="https://webnus.net/dox/modern-events-calendar/" target="_blank"><?php esc_html_e('Documentation' , 'modern-events-calendar-lite'); ?></a>
                    </div>
                </div>
            </div>
            <?php if(!$this->getPRO() && has_action('addons_activation') ) : ?>
            <div class="w-col-sm-12">
                <div class="w-box mec-activation">
                    <div class="w-box-head">
                        <?php _e('License Activation', 'modern-events-calendar-lite'); ?>
                    </div>
                    <?php if (current_user_can( 'administrator' )): ?>
                    <div class="w-box-content">
                        <div class="box-addons-activation">
                            <?php $mec_options = get_option('mec_options'); ?>
                            <div class="box-addon-activation-toggle-head"><i class="mec-sl-plus"></i><span><?php _e('Activate Addons', 'modern-events-calendar-lite'); ?></span></div>
                            <div class="box-addon-activation-toggle-content">
                                <?php do_action('addons_activation'); ?>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="w-box-content">
                        <p style="background: #f7f7f7f7;display: inline-block;padding: 17px 35px;border-radius: 3px;/* box-shadow: 0 1px 16px rgba(0,0,0,.034); */"><?php echo esc_html__('You cannot access this section.', 'modern-events-calendar-lite'); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if($this->getPRO()) : ?>
            <div class="w-col-sm-12">
                <div class="w-box mec-activation">
                    <div class="w-box-head">
                        <?php _e('License Activation', 'modern-events-calendar-lite'); ?>
                    </div>
                    <?php 
                        if (current_user_can( 'administrator' )):
                    ?>                    
                    <div class="w-box-content">
                        <p><?php echo esc_html__('In order to use all plugin features and options, please enter your purchase code.', 'modern-events-calendar-lite'); ?></p>
                        <div class="box-mec-avtivation">
                            <?php 
                                $mec_options = get_option('mec_options');
                                $one_license = $five_license = $ten_license = $appsumo =  $product_license = '';

                                if(!empty($mec_options) && isset($mec_options['product_name']))
                                {
                                    if($mec_options['product_name'] == '1 License for MEC Plugin')
                                    {
                                        $one_license = 'checked';
                                    }
                                    elseif($mec_options['product_name'] == '5 License for MEC Plugin')
                                    {
                                        $five_license = 'checked';
                                    }
                                    elseif($mec_options['product_name'] == '10 License for MEC Plugin')
                                    {
                                        $ten_license = 'checked';
                                    }
                                    elseif($mec_options['product_name'] == 'appsumo')
                                    {
                                        $appsumo = 'checked';
                                    }

                                    if($mec_options['product_name'] != '')
                                    {
                                        $product_license = $mec_options['purchase_code'];
                                    }
                                }
                            ?>
                            <form id="MECActivation" action="#" method="post">
                                <div class="LicenseType">
                                    <input type="radio" id="OneLicense" name="MECLicense" value="1 License for MEC Plugin" class="<?php echo esc_html($one_license); ?>" />
                                    <label for="OneLicense"><span></span>1 License</label>
                                    <input type="radio" id="FiveLicense" value="5 License for MEC Plugin" name="MECLicense" class="<?php echo esc_html($five_license); ?>" />
                                    <label for="FiveLicense"><span></span>5 License</label>
                                    <input type="radio" id="TenLicense" value="10 License for MEC Plugin" name="MECLicense" class="<?php echo esc_html($ten_license); ?>" />
                                    <label for="TenLicense"><span></span>10 License</label>
                                    <input type="radio" id="Appsumo" value="appsumo" name="MECLicense" class="<?php echo esc_html($appsumo); ?>" />
                                    <label for="Appsumo"><span></span>AppSumo</label>
                                </div>
                                <div class="LicenseField">
                                    <input type="password" placeholder="Put your purchase code here" name="MECPurchaseCode" value="<?php echo esc_html($product_license); ?>">
                                    <input type="submit">
                                    <?php
                                    $license_status = '';
                                    if(!empty($mec_options['purchase_code']) && $mec_license_status == 'active')
                                    {
                                        $license_status = 'PurchaseSuccess';
                                    } 
                                    elseif(!empty($mec_options['purchase_code']) && $mec_license_status == 'faild')
                                    {
                                        $license_status = 'PurchaseError';
                                    }
                                    ?>
                                    <div class="MECPurchaseStatus <?php echo esc_html($license_status); ?>"></div>
                                </div>
                                <div class="MECLicenseMessage"></div>
                            </form>
                        </div>

                        <div class="box-addons-activation">
                            <?php $mec_options = get_option('mec_options'); ?>
                            <div class="box-addon-activation-toggle-head"><i class="mec-sl-plus"></i><span><?php _e('Activate Addons', 'modern-events-calendar-lite'); ?></span></div>
                            <div class="box-addon-activation-toggle-content">
                                <?php do_action('addons_activation'); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        else: ?>
                        <div class="w-box-content">
                            <p style="background: #f7f7f7f7;display: inline-block;padding: 17px 35px;border-radius: 3px;/* box-shadow: 0 1px 16px rgba(0,0,0,.034); */"><?php echo esc_html__('You cannot access this section.', 'modern-events-calendar-lite'); ?></p>
                        </div>
                            <?php                            
                        endif;
                    ?>                    
                </div>
            </div>
            <?php endif; ?>
            <?php if(current_user_can('read')): ?>
            <div class="w-col-sm-3">
                <div class="w-box doc">
                    <div class="w-box-child mec-count-child">
                        <p><?php echo '<p class="mec_dash_count">'.$user_post_count.'</p> '.__('Events', 'modern-events-calendar-lite'); ?></p>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box doc">
                    <div class="w-box-child mec-count-child">
                        <p><?php echo '<p class="mec_dash_count">'.$user_post_count_c.'</p> '.__('Shortcodes', 'modern-events-calendar-lite'); ?></p>
                    </div>
                </div>
            </div>
            <div class="w-col-sm-3">
                <div class="w-box doc">
                    <div class="w-box-child mec-count-child">
                        <p><?php echo '<p class="mec_dash_count">'.$user_location_count_l.'</p> '.__('Locations', 'modern-events-calendar-lite'); ?></p>
                    </div>
                </div>
            </div>            
            <div class="w-col-sm-3">
                <div class="w-box doc">
                    <div class="w-box-child mec-count-child">
                        <p><?php echo '<p class="mec_dash_count">'.$user_organizer_count_l.'</p> '. __('Organizers', 'modern-events-calendar-lite'); ?></p>
                    </div>
                </div>
            </div>           
            <?php endif; ?>
        </div>
        <?php if($box_stats): ?>
        <div class="w-row">
            <div class="w-col-sm-<?php echo $booking_status ? 6 : 12; ?>">
                <div class="w-box upcoming-events">
                    <div class="w-box-head">
                        <?php _e('Upcoming Events', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <?php
                            $render = $this->getRender();
                            echo $render->skin('list', array
                            (
                                'sk-options'=>array('list'=>array
                                (
                                    'style'=>'minimal',
                                    'start_date_type'=>'today',
                                    'load_more_button'=>'0',
                                    'limit'=>'6',
                                    'month_divider'=>'0'
                                ))
                            ));
                        ?>
                    </div>
                </div>
            </div>
            <?php if($booking_status): ?>
            <div class="w-col-sm-6">
                <div class="w-box gateways">
                    <div class="w-box-head">
                        <?php echo esc_html__('Popular Gateways', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <?php
                            $results = $db->select("SELECT COUNT(`meta_id`) AS count, `meta_value` AS gateway FROM `#__postmeta` WHERE `meta_key`='mec_gateway' GROUP BY `meta_value`", 'loadAssocList');

                            $labels = '';
                            $data = '';
                            $bg_colors = '';

                            foreach($results as $result)
                            {
                                if (!class_exists($result['gateway'])) {
                                    continue;
                                }

                                $gateway = new $result['gateway'];
                                $stats[] = array('label'=>$gateway->title(), 'count'=>$result['count']);

                                $labels .= '"'.$gateway->title().'",';
                                $data .= ((int) $result['count']).',';
                                $bg_colors .= "'".$gateway->color()."',";
                            }
                            echo '<canvas id="mec_gateways_chart" width="300" height="300"></canvas>';
                            echo '<script type="text/javascript">
                                jQuery(document).ready(function()
                                {
                                    var ctx = document.getElementById("mec_gateways_chart");
                                    var mecGatewaysChart = new Chart(ctx,
                                    {
                                        type: "doughnut",
                                        data:
                                        {
                                            labels: ['.trim($labels, ', ').'],
                                            datasets: [
                                            {
                                                data: ['.trim($data, ', ').'],
                                                backgroundColor: ['.trim($bg_colors, ', ').']
                                            }]
                                        }
                                    });
                                });
                            </script>';
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php if($booking_status and current_user_can('mec_settings')) echo (new MEC_feature_mec())->widget_total_bookings(); ?>
        <?php endif; ?>

        <?php if($this->getPRO()) echo (new MEC_feature_mec())->widget_print(); ?>

        <div class="w-row">
            <div class="w-col-sm-12">
                <div class="w-box change-log">
                    <div class="w-box-head">
                        <?php echo esc_html__('Change Log', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <pre><?php echo file_get_contents(plugin_dir_path(__FILE__ ).'../../../changelog.txt'); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>