<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */
/** @var MEC_factory $factory */

// MEC Settings
$settings = $this->get_settings();

// Countdown on single page is disabled
if(!isset($settings['countdown_status']) or (isset($settings['countdown_status']) and !$settings['countdown_status'])) return;

$event = $event[0];
$date = $event->date;

$start_date = (isset($date['start']) and isset($date['start']['date'])) ? $date['start']['date'] : current_time('Y-m-d');
$end_date = (isset($date['end']) and isset($date['end']['date'])) ? $date['end']['date'] : current_time('Y-m-d');

$s_time = '';
if(!empty($date))
{
    $s_hour = $date['start']['hour'];
    if(strtoupper($date['start']['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

    $s_time .= sprintf("%02d", $s_hour).':';
    $s_time .= sprintf("%02d", $date['start']['minutes']);
    $s_time .= ' '.trim($date['start']['ampm']);
}

$e_time = '';
if(!empty($date))
{
    $e_hour = $date['end']['hour'];
    if(strtoupper($date['end']['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

    $e_time .= sprintf("%02d", $e_hour).':';
    $e_time .= sprintf("%02d", $date['end']['minutes']);
    $e_time .= ' '.trim($date['end']['ampm']);
}

$start_time = date('D M j Y G:i:s', strtotime($start_date.' '.$s_time));
$end_time = date('D M j Y G:i:s', strtotime($end_date.' '.$e_time));

$d1 = new DateTime($start_time);
$d2 = new DateTime(current_time("D M j Y G:i:s"));
$d3 = new DateTime($end_time);

$countdown_method = get_post_meta($event->ID, 'mec_countdown_method', true);
if(trim($countdown_method) == '') $countdown_method = 'global';

if($countdown_method == 'global') $ongoing = (isset($settings['hide_time_method']) and trim($settings['hide_time_method']) == 'end') ? true : false;
else $ongoing = ($countdown_method == 'end') ? true : false;

$disable_for_ongoing = (isset($settings['countdown_disable_for_ongoing_events']) and $settings['countdown_disable_for_ongoing_events']);

if($d3 < $d2)
{
    echo '<div class="mec-end-counts"><h3>'.__('The event is finished.', 'modern-events-calendar-lite').'</h3></div>';
    return;
}
elseif(($d1 < $d2 and !$ongoing) or $disable_for_ongoing)
{
    echo '<div class="mec-end-counts"><h3>'.__('The event is ongoing.', 'modern-events-calendar-lite').'</h3></div>';
    return;
}

$gmt_offset = $this->get_gmt_offset($event, strtotime($start_date.' '.$s_time));
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') === false) $gmt_offset = ' : '.$gmt_offset;
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == true) $gmt_offset = substr(trim($gmt_offset), 0 , 3);
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') == true) $gmt_offset = substr(trim($gmt_offset), 2 , 3);

$datetime = $ongoing ? $end_time : $start_time;

// Generating javascript code of countdown default module
$defaultjs = '<script type="text/javascript">
jQuery(document).ready(function($)
{
    jQuery.each(jQuery(".mec-countdown-details"),function(i,el){
        var datetime = jQuery(el).data("datetime");
        var gmt_offset = jQuery(el).data("gmt_offset");
        jQuery(el).mecCountDown(
            {
                date: datetime+""+gmt_offset,
                format: "off"
            },
            function(){}
        );
    });
});
</script>';

// Generating javascript code of countdown flip module
$flipjs = '<script type="text/javascript">
var clock;
jQuery(document).ready(function()
{
    var futureDate = new Date("'.($datetime).$gmt_offset.'");
    var currentDate = new Date();
    var diff = parseInt((futureDate.getTime() / 1000 - currentDate.getTime() / 1000));

    function dayDiff(first, second)
    {
        return (second-first)/(1000*3600*24);
    }

    if(dayDiff(currentDate, futureDate) < 100) jQuery(".clock").addClass("twodaydigits");
    else jQuery(".clock").addClass("threedaydigits");

    if(diff < 0)
    {
        diff = 0;
        jQuery(".countdown-message").html();
    }

    clock = jQuery(".clock").FlipClock(diff,
    {
        clockFace: "DailyCounter",
        countdown: true,
        autoStart: true,
            callbacks: {
            stop: function() {
                jQuery(".countdown-message").html()
            }
        }
    });

    jQuery(".mec-wrap .flip-clock-wrapper ul li, a .shadow, a .inn").on("click", function(event)
    {
        event.preventDefault();
    });
});
</script>';
$flipjsDivi = '<script type="text/javascript">
var clock;
jQuery(document).ready(function()
{
    var futureDate = new Date("'.($datetime).$gmt_offset.'");
    var currentDate = new Date();
    var diff = parseInt((futureDate.getTime() / 1000 - currentDate.getTime() / 1000));

    function dayDiff(first, second)
    {
        return (second-first)/(1000*3600*24);
    }

    if(dayDiff(currentDate, futureDate) < 100) jQuery(".clock").addClass("twodaydigits");
    else jQuery(".clock").addClass("threedaydigits");

    if(diff < 0)
    {
        diff = 0;
        jQuery(".countdown-message").html();
    }

    clock = $(".clock").FlipClock(diff,
    {
        clockFace: "DailyCounter",
        countdown: true,
        autoStart: true,
            callbacks: {
            stop: function() {
                jQuery(".countdown-message").html()
            }
        }
    });

    jQuery(".mec-wrap .flip-clock-wrapper ul li, a .shadow, a .inn").on("click", function(event)
    {
        event.preventDefault();
    });
});
</script>';
if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php');
?>
<?php if(!isset($settings['countdown_list']) or (isset($settings['countdown_list']) and $settings['countdown_list'] === 'default')): ?>
<?php
    if($this->is_ajax()) echo $defaultjs;
    elseif (is_plugin_active( 'mec-single-builder/mec-single-builder.php')) echo $defaultjs;
    else $factory->params('footer', $defaultjs);
?>
<div class="mec-countdown-details" id="mec_countdown_details" data-datetime="<?php echo $datetime; ?>" data-gmt_offset="<?php echo $gmt_offset ?>">
    <div class="countdown-w ctd-simple">
        <ul class="clockdiv" id="countdown">
            <div class="days-w block-w">
                <li>
                    <i class="icon-w mec-li_calendar"></i>
                    <span class="mec-days">00</span>
                    <p class="mec-timeRefDays label-w"><?php _e('days', 'modern-events-calendar-lite'); ?></p>
                </li>
            </div>
            <div class="hours-w block-w">
                <li>
                    <i class="icon-w mec-fa-clock-o"></i>
                    <span class="mec-hours">00</span>
                    <p class="mec-timeRefHours label-w"><?php _e('hours', 'modern-events-calendar-lite'); ?></p>
                </li>
            </div>
            <div class="minutes-w block-w">
                <li>
                    <i class="icon-w mec-li_clock"></i>
                    <span class="mec-minutes">00</span>
                    <p class="mec-timeRefMinutes label-w"><?php _e('minutes', 'modern-events-calendar-lite'); ?></p>
                </li>
            </div>
            <div class="seconds-w block-w">
                <li>
                    <i class="icon-w mec-li_heart"></i>
                    <span class="mec-seconds">00</span>
                    <p class="mec-timeRefSeconds label-w"><?php _e('seconds', 'modern-events-calendar-lite'); ?></p>
                </li>
            </div>
        </ul>
    </div>
</div>
<?php elseif(isset($settings['countdown_list']) and $settings['countdown_list'] === 'flip'): ?>
<?php
    if($this->is_ajax()) echo $flipjs;
    elseif(is_plugin_active( 'mec-single-builder/mec-single-builder.php'))
    {
        wp_enqueue_script('mec-flipcount-script', $this->asset('js/flipcount.js'));
        echo $flipjs;
    }
    elseif(is_plugin_active( 'divi-single-builder/divi-single-builder.php'))
    {
        wp_enqueue_script('mec-flipcount-script', $this->asset('js/flipcount-divi.js'));
        $factory->params('footer', $flipjsDivi);
    }
    else
    {
        // Include FlipCount library
        wp_enqueue_script('mec-flipcount-script', $this->asset('js/flipcount.js'));

        // Include the JS code
        $factory->params('footer', $flipjs);
    }
?>
<div class="clock"></div>
<div class="countdown-message"></div>
<?php endif;