<?php

    $progress_bar_type  = get_option('niteoCS_progress_bar_type', 'manual');
    
    if ( $progress_bar_type === 'manual' ) {
        $progress = get_option('niteoCS_progress_bar_percentage', '0');
    }

    if ( $progress_bar_type === 'auto' ) {
        $progress_bar_start_date	= (int)get_option('niteoCS_progress_start_bar_date', time());
        $progress_bar_end_date		= (int)get_option('niteoCS_progress_end_bar_date', time() + 86400);
        $current_date = time();
        $progress = round(($current_date - $progress_bar_start_date) / ( $progress_bar_end_date - $progress_bar_start_date) * 100);
    }

    ob_start();

    ?>
    <div id="progress-bar" class="<?php echo esc_attr( $wrapper_class );?>" data-width="<?php echo esc_attr( $progress );?>">
        <div class="bar-wrapper">
            <div class="growing-bar"></div>
        </div>
        <div class="bar-percentage"><span class="bar-percentage-number">0</span>%</div>
    </div>

    <script>
        const progressBar = document.getElementById('progress-bar');
        const growingBar = document.querySelector('.growing-bar');
        const percentageNumber = document.querySelector('.bar-percentage-number');
        const progress = progressBar.getAttribute('data-width');
        let counter = 0;
        setTimeout(() => {
            growingBar.style.width = progress + '%';

            let countUp = setInterval(() => {
                percentageNumber.innerHTML = counter;
                if (counter >= progress) {
                    clearInterval(countUp);
                }
                counter++;
            }, 1000 / progress);

        }, <?php echo esc_attr($timeout);?>);
    </script>
    <?php


$html = ob_get_clean();