<?php

// display counter
    $translation    = $this->cmp_wpml_niteoCS_translation();
    $seconds_label  = $translation[0]['translation'];
    $minutes_label  = $translation[1]['translation'];
    $hours_label    = $translation[2]['translation'];
    $days_label     = $translation[3]['translation'];
    $date           = get_option('niteoCS_counter_date', time() + 86400);
    $counter_date   = $days_only ? abs(time() - $date)/60/60/24 : $date;
    ob_start();
    ?>
    <div id="counter" class="<?php echo esc_attr( $wrapper_class );?>" data-date="<?php echo esc_attr( $counter_date );?>">
        <div class="counter-box">
            <div class="counter-inner">
                <span id="counter-day" class="counter-number"><?php echo $days_only ? round($counter_date) + 50 : '00';?></span>
                <p class="counter-label"><?php echo esc_html( $days_label );?></p>
            </div>
        </div>    
        <?php if ( !$days_only ) : ?>
        <div class="counter-box">
            <div class="counter-inner">
                <span id="counter-hour" class="counter-number">00</span>
                <p class="counter-label"><?php echo esc_html( $hours_label );?></p>
            </div>
        </div>
        <?php endif; ?>
        <?php if ( !$days_only ) : ?>
        <div class="counter-box">
            <div class="counter-inner">
                <span id="counter-minute" class="counter-number">00</span>
                <p class="counter-label"><?php echo esc_html( $minutes_label );?></p>
            </div>
        </div>  
        <?php endif; ?>
        <?php if ( !$days_only ) : ?>
        <div class="counter-box">
            <div class="counter-inner">
                <span id="counter-second" class="counter-number">00</span>
                <p class="counter-label"><?php echo esc_html( $seconds_label );?></p>
            </div>
        </div> 
        <?php endif; ?>
    </div>
    <?php


$html = ob_get_clean();