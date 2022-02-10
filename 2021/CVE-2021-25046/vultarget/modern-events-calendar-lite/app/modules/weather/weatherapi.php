<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */

$date = current_time('Y-m-d H:i:s');

$weather = $this->get_weather_wa($weatherapi, $lat, $lng, $date);
$imperial = (isset($settings['weather_module_imperial_units']) and $settings['weather_module_imperial_units']) ? true : false;

// Weather not found!
if(!is_array($weather) or (is_array($weather) and !count($weather))) return;
?>
<div class="mec-weather-details mec-frontbox" id="mec_weather_details">
    <h3 class="mec-weather mec-frontbox-title"><?php _e('Weather', 'modern-events-calendar-lite'); ?></h3>

    <!-- mec weather start -->
    <div class="mec-weather-box">

        <div class="mec-weather-head">

            <?php if(isset($weather['condition']) and isset($weather['condition']['icon'])): ?>
            <div class="mec-weather-icon-box">
                <span class="mec-weather-icon"><img src="<?php echo $weather['condition']['icon']; ?>" alt="<?php echo $weather['condition']['text']; ?>"></span>
            </div>
            <?php endif; ?>

            <div class="mec-weather-summary">

                <?php if(isset($weather['condition']) and isset($weather['condition']['text'])): ?>
                <div class="mec-weather-summary-report"><?php echo $weather['condition']['text']; ?></div>
                <?php endif; ?>

                <?php if(isset($weather['temp_c'])): ?>
                    <div class="mec-weather-summary-temp" data-c="<?php _e( ' °C', 'modern-events-calendar-lite' ); ?>" data-f="<?php _e( ' °F', 'modern-events-calendar-lite' ); ?>">
                    <?php if(!$imperial): echo round($weather['temp_c']); ?>
                    <var><?php _e(' °C', 'modern-events-calendar-lite'); ?></var>
                    <?php else: echo round($weather['temp_f']); ?>
                    <var><?php _e(' °F', 'modern-events-calendar-lite'); ?></var>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
            
            <?php if(isset($settings['weather_module_change_units_button']) and $settings['weather_module_change_units_button']): ?>
            <span data-imperial="<?php _e('°Imperial', 'modern-events-calendar-lite'); ?>" data-metric="<?php _e('°Metric', 'modern-events-calendar-lite'); ?>" class="degrees-mode"><?php if(!$imperial) _e('°Imperial', 'modern-events-calendar-lite'); else _e('°Metric', 'modern-events-calendar-lite'); ?></span>
            <?php endif ?>
            
            <div class="mec-weather-extras">

                <?php if(isset($weather['wind_kph']) and isset($weather['wind_mph'])): ?>
                <div class="mec-weather-wind" data-kph="<?php _e(' KPH', 'modern-events-calendar-lite'); ?>" data-mph="<?php _e(' MPH', 'modern-events-calendar-lite'); ?>"><span><?php _e('Wind', 'modern-events-calendar-lite'); ?>: </span><?php if(!$imperial) echo round($weather['wind_kph']); else echo round($weather['wind_mph']); ?><var><?php if(!$imperial) _e(' KPH', 'modern-events-calendar-lite'); else _e(' MPH', 'modern-events-calendar-lite'); ?></var></div>
                <?php endif; ?>

                <?php if(isset($weather['humidity'])): ?>
                    <div class="mec-weather-humidity"><span><?php _e('Humidity', 'modern-events-calendar-lite'); ?>:</span> <?php echo round($weather['humidity']); ?><var><?php _e(' %','modern-events-calendar-lite'); ?></var></div>
                <?php endif; ?>

                <?php if(isset($weather['feelslike_c']) and isset($weather['feelslike_f'])): ?>
                    <div class="mec-weather-feels-like" data-c="<?php _e( ' °C', 'modern-events-calendar-lite' ); ?>" data-f="<?php _e( ' °F', 'modern-events-calendar-lite' ); ?>"><span><?php _e('Feels like', 'modern-events-calendar-lite'); ?>: </span><?php if(!$imperial) echo round($weather['feelslike_c']); else echo round($weather['feelslike_f']); ?><var><?php if(!$imperial) _e(' °C','modern-events-calendar-lite'); else _e(' °F','modern-events-calendar-lite'); ?></var></div>
                <?php endif; ?>
        
            </div>
        </div>

    </div><!--  mec weather end -->

</div>