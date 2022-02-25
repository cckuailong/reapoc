<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

$ID         = prsv_input_get('id',array());
$filters    = prsv_input_get('filters',array());
$usermeta   = !empty($filters) ? get_user_meta($filters['user_id']): array();
$show_chart = empty($filters);

if(!$ID)
{
  wp_die('Survey ID not valid!');
}

$post      = prsv_get_post_type_model()->get_survey($ID);
$questions = prsv_get_post_type_model()->get_questions($ID, array('answers' => true));

foreach($questions as $key => $question)
{
  if(in_array($question['type'], array(PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_TEXT_SPAN)))
  {
    unset($questions[$key]);
  }
}

?>
<div class='wrap'>
  <h2><?php _e('Statistics for', 'perfect-survey') ?> <?php echo $post->post_title ? $post->post_title : __('(no title)'); ?></h2>

    <h3>
      <?php
      if(!empty($filters['user_id'])){
        echo empty($filters) ? '' : ' ' . __('User', 'perfect-survey') . ' <a href="'.get_edit_user_link($filters['user_id']).'">' . $usermeta['nickname'][0].'</a>';
      } elseif (!empty($filters['session_id'])){
        echo empty($filters) ? '' : ' ' . __('User', 'perfect-survey') . ' ' . __('not registered', 'perfect-survey') . ' ' . __('session', 'perfect-survey') . ' ' . $filters['session_id'];
      } else {
        echo empty($filters) ? '' : ' ' . __('User', 'perfect-survey') . ' ' . __('not registered', 'perfect-survey');
      }
      ?>
    </h3>

    <?php if(prsv_input_get('message')) { ?>
      <div class="survey_alert_banner survey_positive_color">
        <p class="survey_alert_p">
          <?php _e(prsv_input_get('message'), 'perfect-survey') ?>
        </p>
      </div>
      <br/><br/>
    <?php } ?>
    <?php if(get_post($post)->ps_survey_turn_on === 'survey_on') { ?>
      <div class="survey_alert_banner survey_positive_color">
        <p class="survey_alert_p"><strong><?php _e('Survey', 'perfect-survey') ?></strong>
          <?php _e('In progress', 'perfect-survey') ?>
        </p>
      </div>
    <?php } else { ?>
      <div class="survey_alert_banner survey_alert_color">
        <p class="survey_alert_p"><strong><?php _e('Survey', 'perfect-survey') ?></strong>
          <?php _e('Suspended', 'perfect-survey') ?>. <?php _e('When the survey is suspended, the system does not record the statistics. Enable the survey to start collecting data.', 'perfect-survey') ?> <a href="post.php?post=<?php echo $ID; ?>&action=edit"><?php _e('Go to survey', 'perfect-survey') ?>.</a>
        </p>
      </div>
    <?php } ?>

    <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content" style="position: relative;">
          <div class='survey-block'>
            <div class="ps_sfe_statistics">
              <?php foreach($questions as $question) { ?>
                <div class="ps_sfe_statistics_body">
                  <div class="ps_sfe_questions">
                    <p class="ps_sfe_question"><?php echo esc_html($question['text']); ?></p>
                  </div>
                  <div class="ps_sfe_result">
                    <?php prsv_resource_include_backend('statistics/'.$question['type'], array('show_chart'=>$show_chart, 'filters'=>$filters,'question' => $question, 'question_type' => $question['question_type'],'answers' => $question['answers'], 'answers_values' => $question['answers_values'])); ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
          <?php if(empty($filters)){  prsv_resource_include_backend('statistics/datagrid', array('ID' => $ID, 'questions' => $questions, 'post' => $post));  } ?>
        </div>
        <div id="postbox-container-1" class="postbox-container">
          <div class='survey-block'>
            <table class="widefat survey_settings survey_input" cellspacing="0">
              <thead>
                <tr>
                  <th colspan="3"><?php _e('View this survey', 'perfect-survey') ?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="survey-wrap-input">
                      <p class='survey_code'>
                        <?php _e('You can click on this link to go to the edit page of this survey', 'perfect-survey') ?>
                      </p>
                      <p class='survey_code'>
                        <a href="post.php?post=<?php echo $ID; ?>&action=edit" class="button button-primary button-large"><?php _e('Go to survey', 'perfect-survey') ?></a>
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class='survey-block'>
            <table class="widefat survey_settings survey_input" cellspacing="0">
              <thead>
                <tr>
                  <th colspan="3"><?php _e('Download area', 'perfect-survey') ?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="survey-wrap-input">
                      <p><?php _e('Create', 'perfect-survey') ?>: <?php echo get_the_time('F j, Y', $ID ); ?> at <?php echo get_the_time('g:i a', $ID ); ?></p>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="survey-wrap-input">
                      <p class='survey_code'>
                        <?php if(isset($filters['user_id']) && $filters['user_id'] != '') { ?>
                          <a href="<?php echo admin_url('admin-ajax.php?post_type='.PRSV_PLUGIN_CODE.'&id='.$ID.'&action=download_csv&'.http_build_query(array('filters' => $filters)));?>" target="_blank" class="button button-primary button-large"><?php _e('Download CSV', 'perfect-survey') ?></a>
                          <?php
                        } else {
                          unset($filters['user_id']);
                          ?>
                          <a href="<?php echo admin_url('admin-ajax.php?post_type='.PRSV_PLUGIN_CODE.'&id='.$ID.'&action=download_csv&'.http_build_query(array('filters' => $filters)));?>" target="_blank" class="button button-primary button-large"><?php _e('Download CSV', 'perfect-survey') ?></a>
                        <?php } ?>
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class='survey-block'>
            <table class="widefat survey_settings survey_input" cellspacing="0">
              <thead>
                <tr>
                  <th colspan="3">
                    <?php _e('Reset all data', 'perfect-survey') ?>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="survey-wrap-input">
                      <p class='survey_code'>
                        <?php _e('Reset the collected data, the operation will not be reversible and the data will be lost forever.', 'perfect-survey') ?>
                      </p>
                      <p class='survey_code'>
                        <a onclick="return confirm('<?php _e('Reset the collected data, the operation will not be reversible and the data will be lost forever. Are you sure?', 'perfect-survey') ?>')" href="<?php echo admin_url('admin-ajax.php?post_type='.PRSV_PLUGIN_CODE.'&id='.$ID.'&action=delete_stats'); ?>" class="button button-large"><?php _e('Reset all data', 'perfect-survey') ?></a>
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
  jQuery(window).load(function () {
    jQuery('.introdata').DataTable({
      "paging": false,
      "info": false,
      "searching": false,
      "search": false
    });
    jQuery('.introdatabyip').DataTable({
      "paging": true,
      "info": true,
      "searching": true,
      "search": true
    });
  });
</script>
