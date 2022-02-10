<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

?>
<h3><?php _e('Settings', 'tutor') ?></h3>

<div class="tutor-dashboard-content-inner">

    <div class="tutor-dashboard-inline-links">
        <?php
            tutor_load_template('dashboard.settings.nav-bar', ['active_setting_nav'=>'skill']);
        ?>
    </div>

</div>