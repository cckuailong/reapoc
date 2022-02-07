<?php
/**
 * Template for wizard top tabs
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/wizard/wizard-tabs
 */
?>

<!-- Tittle -->
<div class="section-title">
    <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . '/admin/images/simple-job-board-logo.png' ?>">
    <h2><?php echo esc_html__('Simple Job Board', 'simple-job-board'); ?></h2>
    <p><?php echo esc_html__('by ', 'simple-job-board'); ?><a href="http://presstigers.com" target="_blank"><?php echo esc_html__('PressTigers', 'simple-job-board'); ?></a></p>
</div>
<!-- progressbar -->
<ul id="sjb-inactive">
    <li class="<?php echo $get_started ?>">
        <strong><?php echo esc_html__('Get Started', 'simple-job-board'); ?></strong>
        <span><?php echo esc_html__('Select Plugin', 'simple-job-board'); ?></span>
    </li>
    <li>
        <strong><?php echo esc_html__('Fill Job Data', 'simple-job-board'); ?></strong>
        <span><?php echo esc_html__('Required Information', 'simple-job-board'); ?></span>
    </li>
    <li>
        <strong><?php echo esc_html__('Application Form', 'simple-job-board'); ?></strong>
        <span><?php echo esc_html__('Required Information', 'simple-job-board'); ?></span>
    </li>
    <li class="<?php echo $get_ready ?>">
        <strong><?php echo esc_html__('Get Ready', 'simple-job-board'); ?></strong>
        <span><?php echo esc_html__('Required Information', 'simple-job-board'); ?> </span>
    </li>
</ul>