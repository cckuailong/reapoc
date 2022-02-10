<?php

$profile_completion = tutils()->user_profile_completion();

if ($profile_completion->progress < 100) { ?>
    <div class="tutor-col-12">
        <div class="tutor-profile-completion-warning">
            <div class="profile-completion-warning-icon">
                <span class="tutor-icon-warning-2"></span>
            </div>
            <div class="profile-completion-warning-content">
                <h4><?php _e('Complete Your Profile', 'tutor'); ?></h4>
                <div class="profile-completion-warning-details">
                    <p><?php _e('Complete your profile so people can know more about you! Go to Profile', 'tutor'); ?> <a href="<?php echo tutils()->tutor_dashboard_url('settings'); ?>"><?php _e('Settings', 'tutor'); ?></a></p>
                    <ul>
                        <?php 
                        foreach ($profile_completion->empty_fields as $empty_field) {
                            echo '<li>'.__('Set Your', 'tutor').'<span> '. $empty_field.'</span></li>';
                        } ?>
                    </ul>
                </div>
                <div class="profile-completion-warning-status">
                    <p><span><?php echo $profile_completion->progress.__('% Complete', 'tutor'); ?>,</span> <?php _e('You are almost done!', 'tutor'); ?></p>
                    <div class="tutor-progress-bar-wrap">
                        <div class="tutor-progress-bar">
                            <div class="tutor-progress-filled" style="--tutor-progress-left: <?php echo $profile_completion->progress; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    } 
?>