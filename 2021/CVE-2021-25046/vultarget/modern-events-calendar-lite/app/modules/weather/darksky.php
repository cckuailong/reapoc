<?php
/** no direct access **/
defined('MECEXEC') or die();

$occurrence = isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '';
$date = (trim($occurrence) ? $occurrence : $event->date['start']['date']).' '.sprintf("%02d", $event->date['start']['hour']).':'.sprintf("%02d", $event->date['start']['minutes']).' '.$event->date['start']['ampm'];

$weather = $this->get_weather_darksky($darksky, $lat, $lng, $date);
$imperial = (isset($settings['weather_module_imperial_units']) and $settings['weather_module_imperial_units']) ? true : false;

// Weather not found!
if(!is_array($weather) or (is_array($weather) and !count($weather))) return;
?>
<div class="mec-weather-details mec-frontbox" id="mec_weather_details">
    <h3 class="mec-weather mec-frontbox-title"><?php _e('Weather', 'modern-events-calendar-lite'); ?></h3>

    <!-- mec weather start -->
    <div class="mec-weather-box">

        <div class="mec-weather-head">
            <div class="mec-weather-icon-box">
                <span class="mec-weather-icon <?php echo $weather['icon']; ?>"></span>
            </div>
            <div class="mec-weather-summary">

                <?php if(isset($weather['summary'])): ?>
                <div class="mec-weather-summary-report"><?php echo $weather['summary']; ?></div>
                <?php endif; ?>

                <?php if(isset($weather['temperature'])): ?>
                    <div class="mec-weather-summary-temp" data-c="<?php _e( ' °C', 'modern-events-calendar-lite' ); ?>" data-f="<?php _e( ' °F', 'modern-events-calendar-lite' ); ?>">
                    <?php if(!$imperial): echo round($weather['temperature']); ?>
                    <var><?php _e(' °C', 'modern-events-calendar-lite'); ?></var>
                    <?php else: echo $this->weather_unit_convert($weather['temperature'], 'C_TO_F'); ?>
                    <var><?php _e(' °F', 'modern-events-calendar-lite'); ?></var>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
            
            <?php if(isset($settings['weather_module_change_units_button']) and $settings['weather_module_change_units_button']): ?>
            <span data-imperial="<?php _e('°Imperial', 'modern-events-calendar-lite'); ?>" data-metric="<?php _e('°Metric', 'modern-events-calendar-lite'); ?>" class="degrees-mode"><?php if(!$imperial) _e('°Imperial', 'modern-events-calendar-lite'); else _e('°Metric', 'modern-events-calendar-lite'); ?></span>
            <?php endif ?>
            
            <div class="mec-weather-extras">

                <?php if(isset($weather['windSpeed'])): ?>
                <div class="mec-weather-wind" data-kph="<?php _e(' KPH', 'modern-events-calendar-lite'); ?>" data-mph="<?php _e(' MPH', 'modern-events-calendar-lite'); ?>"><span><?php _e('Wind', 'modern-events-calendar-lite'); ?>: </span><?php if(!$imperial) echo round($weather['windSpeed']); else  echo $this->weather_unit_convert($weather['windSpeed'], 'KM_TO_M');?><var><?php if(!$imperial) _e(' KPH', 'modern-events-calendar-lite'); else _e(' MPH', 'modern-events-calendar-lite'); ?></var></div>
                <?php endif; ?>

                <?php if(isset($weather['humidity'])): ?>
                    <div class="mec-weather-humidity"><span><?php _e('Humidity', 'modern-events-calendar-lite'); ?>:</span> <?php echo round($weather['humidity']); ?><var><?php _e(' %','modern-events-calendar-lite'); ?></var></div>
                <?php endif; ?>

                <?php if(isset($weather['visibility'])): ?>
                    <div class="mec-weather-visibility" data-kph="<?php _e(' KM', 'modern-events-calendar-lite'); ?>" data-mph="<?php _e(' Miles', 'modern-events-calendar-lite'); ?>"><span><?php _e('Visibility', 'modern-events-calendar-lite'); ?>: </span><?php if(!$imperial) echo round($weather['visibility']); else  echo $this->weather_unit_convert($weather['visibility'], 'KM_TO_M');?><var><?php if(!$imperial) _e(' KM','modern-events-calendar-lite'); else _e(' Miles','modern-events-calendar-lite'); ?></var></div>
                <?php endif; ?>
        
            </div>
        </div>

    </div><!--  mec weather end -->

</div>