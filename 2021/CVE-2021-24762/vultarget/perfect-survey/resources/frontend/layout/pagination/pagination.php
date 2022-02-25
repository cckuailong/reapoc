<?php
switch ($ps_post_meta['ps_pagination_style']) {
  case 'dots':
  ?>
  <div class="ps_paginator_step">
    <ul>
      <?php foreach ($ps_all_questions as $question) { ?>
        <li class="<?php echo !empty($question['question_data']) ? '' : 'ps_checked'; ?> <?php echo $question['question_id'] == $ps_post->current_question_id ? 'ps_current' : ''; ?>"></li>
      <?php } ?>
    </ul>
  </div>
  <?php
  break;
  case 'number':
  ?>
  <div class="ps_paginator_step_number">
    <ul>
      <li class="current_step"><?php echo sprintf(__('Step %s of %s', 'perfect-survey'), $ps_post->current_question_number, $ps_post->total_questions); ?></li>
    </ul>
  </div>
  <?php break; ?>
<?php } ?>
