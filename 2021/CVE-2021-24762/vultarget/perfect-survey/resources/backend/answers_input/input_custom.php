<tr>
  <td class="surgey_repeater_field">
    <table class="survey_inner_settings">
      <tbody class="reorder-single-answer">
        <?php foreach($question_type['answer_field_types'] as $field_type => $field_name) { ?>
          <tr style='display:<?php echo $question['answer_field_type'] == $field_type ? 'block' : 'none';?>' class="input-<?php echo $field_type;?>">
            <td class="ps-rower-expanded">
              <div class="survey-wrap-input">
                <?php
                switch($field_type)
                {
                  case 'textarea': ?>
                  <h4><?php echo $field_name;?></h4>
                  <textarea rows="3" class="survey_textarea" placeholder="<?php _e('The user will see this type of field', 'perfect-survey') ?>" disabled></textarea> <?php break;
                  default: ?>
                  <h4><?php echo $field_name;?></h4>
                  <input type="<?php echo $field_type;?>" placeholder="<?php _e('The user will see this type of field', 'perfect-survey') ?>" disabled> <?php break;
                }
                ?>
              </div>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </td>
</tr>
