<div class="ps_survey_suspended">
  <p><?php _e('This page is empty.<br>Add a question now!', 'perfect-survey') ?></p>
  <?php
  if (is_user_logged_in()) {
    echo '<a href="'.get_edit_post_link($ps_post->ID).'">'.__('Add a question now', 'perfect-survey').'</a>';
  }
  ?>
</div>
