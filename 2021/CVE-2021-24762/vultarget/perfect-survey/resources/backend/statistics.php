<?php
if (!defined('ABSPATH'))  exit; // Exit if accessed directly
$surveys = prsv_get_post_type_model()->get_all_surveys(true);
?>
<div class='wrap'>
  <h2><?php _e('Analyze Results', 'perfect-survey') ?></h2>
  <div class="ps_respons_data">
    <div class="ps_block_stats">
      <div class="ps_inner_stats_box">
        <p><?php _e('Total Response', 'perfect-survey');?></p>
        <p class="ps_number_stats"><?php echo prsv_get_post_type_model()->count_total_response(true);?></p>
        <span class="ps_icon_stats"><i class="pswp_set_icon-pie-chart"></i></span>
      </div>
    </div>
    <div class="ps_block_stats">
      <div class="ps_inner_stats_box">
        <p><?php _e('Total Surveys', 'perfect-survey');?></p>
        <p class="ps_number_stats"><?php echo prsv_get_post_type_model()->count_total_surveys(true);?></p>
        <span class="ps_icon_stats"><i class="pswp_set_icon-stats-dots"></i></span>
      </div>
    </div>
    <div class="ps_block_stats">
      <div class="ps_inner_stats_box">
        <p><?php _e('Total Questions', 'perfect-survey');?></p>
        <p class="ps_number_stats"><?php echo prsv_get_post_type_model()->count_total_questions(true);?></p>
        <span class="ps_icon_stats"><i class="pswp_set_icon-stats-bars"></i></span>
      </div>
    </div>
  </div>
  <p>
    <?php _e('In this area you will be able to view the statistics of the surveys in progress.', 'perfect-survey') ?>
  </p>
  <?php if($surveys) { ?>
    <table class="wp-list-table widefat fixed striped " cellspacing="0">
      <thead>
        <tr>
          <td id="columnname" class="manage-column column-columnname" scope="col"><?php _e('Survey name', 'perfect-survey') ?></td>
          <td id="columnname" class="manage-column column-columnname" scope="col"><?php _e('Status', 'perfect-survey') ?></td>
        </tr>
      </thead>
      <tbody id="the-list">
        <?php foreach ($surveys as $survey) { ?>
          <tr valign="top">
            <td class="column-columnname">
              <strong>
                <a class="row-title" href="edit.php?post_type=ps&page=single_statistic&id=<?php echo $survey['ID'] ?>">
                  <?php echo get_post($survey['ID'])->post_title ? get_post($survey['ID'])->post_title : __('(no title)'); ?>
                </a>
              </strong>
              <div class="row-actions">
                <span><a href="edit.php?post_type=ps&page=single_statistic&id=<?php echo $survey['ID'] ?>"><?php _e('View', 'perfect-survey') ?></a></span>
              </div>
            </td>
            <?php if(get_post($survey['ID'])->ps_survey_turn_on === 'survey_on') { ?>
              <td class="column-columnname ps_normal_style_font"><i class="pswp_set_icon-play2"></i> <?php _e('In progress', 'perfect-survey') ?></td>
            <?php } else { ?>
              <td class="column-columnname ps_alert_style_font"><i class="pswp_set_icon-pause"></i> <?php _e('Suspended', 'perfect-survey') ?></td>
            <?php } ?>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td id="columnname" class="manage-column column-columnname" scope="col"><?php _e('Survey name', 'perfect-survey') ?></td>
          <td id="columnname" class="manage-column column-columnname" scope="col"><?php _e('Status', 'perfect-survey') ?></td>
        </tr>
      </tfoot>
    </table>
  <?php } else { ?>
    <table class="wp-list-table widefat fixed striped  survey_settings survey_input">
      <tbody>
        <tr>
          <td>
            <div class='survey-empty'>
              <div class="psrv_nosurvey_yet_container">
                <img class="psrv_nosurvey_yet" src="<?php echo esc_url( plugins_url( 'backend/assets/img/psrv_no_survey_found.png', dirname(__FILE__) ) ); ?>">
              </div>
              <p class="survey-empty-message"><?php _e('This page is empty because there are no active surveys', 'perfect-survey') ?></p>
              <a href="post-new.php?post_type=ps" class="button button-primary button-large"><i class="pswp_set_icon-plus"></i> <?php _e('Create a new survey', 'perfect-survey') ?></a>
            </div>
          </td>
        </tr>
        <tbody>
        </table>
      <?php } ?>
    </div>
