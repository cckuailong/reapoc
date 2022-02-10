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
            tutor_load_template('dashboard.settings.nav-bar', ['active_setting_nav'=>'profile']);
        ?>
    </div>

</div>

<?php
    if(isset($GLOBALS['tutor_setting_nav']['profile'])){
        tutor_load_template('dashboard.settings.profile');
    }
    else{
        foreach($GLOBALS['tutor_setting_nav'] as $page){
            echo '<script>window.location.replace("',$page['url'],'");</script>';
            break;
        }
    }
?>