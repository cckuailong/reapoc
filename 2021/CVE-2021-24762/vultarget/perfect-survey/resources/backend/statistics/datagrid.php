<?php
$data = prsv_get_post_type_model()->get_survey_data_groupped($ID);
?>
<div class="survey_ip_container_stat">
  <h2 class="survey_title_ip"><?php _e('Result of statistics for ip', 'perfect-survey') ?></h2>
  <p class="survey_title_ip"><?php _e('In this section it is possible to analyze data for a single user. By clicking on the user name, if registered, you can go to view his profile. By clicking on its IP address you can analyze only its answers isolating them from the rest.', 'perfect-survey') ?></p>
  <div class="ps_resposive_table">
    <table class="widefat survey_settings survey_input reset_survey_tablestyle" cellspacing="0">
      <tbody>
        <tr>
          <td class='survey_container_table'>
            <table class="introdatabyip display" cellspacing="0">
              <thead>
                <tr>
                  <th><?php _e('Starting date', 'perfect-survey') ?></th>
                  <th><?php _e('User', 'perfect-survey') ?></th>
                  <th><?php _e('User IP', 'perfect-survey') ?></th>
                  <th><?php _e('User', 'perfect-survey') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach($data as $record){
                  $user = $record['user_id'];
                  ?>
                  <tr>
                    <td><?php echo $record['date'];?></td>
                    <td><a href="<?php echo admin_url('edit.php?post_type='.PRSV_PLUGIN_CODE.'&page=single_statistic&id='.$ID.'&filters[user_id]='.$record['user_id'].'&filters[session_id]='.$record['session_id']);?>"><?php _e('View this survey', 'perfect-survey') ?></a></td>
                    <td><?php echo $record['ip'] ? $record['ip'] : _e('IP not available'); ?></td>
                    <?php if(!$user) { ?>
                      <td><?php echo __('User', 'perfect-survey') . ' ' . __('not registered', 'perfect-survey') ;?></td>
                    <?php } else { ?>
                      <td><a href="<?php echo get_edit_user_link($user);?>"><?php echo $record['username'];?></a></td>
                    <?php } ?>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
