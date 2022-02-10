<?php
/** no direct access **/
defined('MECEXEC') or die();

// MEC Settings
$settings = $this->get_settings();

// Social networds on single page is disabled
if(!isset($settings['social_network_status']) or (isset($settings['social_network_status']) and !$settings['social_network_status'])) return;

$url = isset($event->data->permalink) ? $event->data->permalink : '';
if(trim($url) == '') return;

$socials = $this->get_social_networks();
?>
<div class="mec-event-social mec-frontbox">
     <h3 class="mec-social-single mec-frontbox-title"><?php _e('Share this event', 'modern-events-calendar-lite'); ?></h3>
     <div class="mec-event-sharing">
        <div class="mec-links-details">
            <ul>
                <?php
                foreach($socials as $social)
                {
                    if(!isset($settings['sn'][$social['id']]) or (isset($settings['sn'][$social['id']]) and !$settings['sn'][$social['id']])) continue;
                    if(is_callable($social['function'])) echo call_user_func($social['function'], $url, $event);
                }
                ?>
            </ul>
        </div>
    </div>
</div>