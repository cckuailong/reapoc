<?php
/**
 * Template for get started wizard page.
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/wizard/wizard-get-started
 */
?>

<div class="sjb-wiz-option-simple-job-board">
    <h4><?php echo esc_html__('Simple Job Board', 'simple-job-board'); ?></h4>
    <p><?php echo esc_html__('Simple Job Board by PressTigers is an easy, light weight plugin that adds a job board to your WordPress website.', 'simple-job-board'); ?></p>
    <a href="<?php echo $settings_url; ?>"><?php echo esc_html__('Explore Settings', 'simple-job-board'); ?></a>
</div>
<div class="sjb-stripe"></div>
<button type="button" class="next action-button"><?php echo esc_html__('Next', 'simple-job-board'); ?></button>