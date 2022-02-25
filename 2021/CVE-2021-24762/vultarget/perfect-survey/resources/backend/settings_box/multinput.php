<tr>
  <?php require 'settings_layout/label.php'; ?>
  <td>
    <div class="survey-wrap-input">
      <ul class='survey_select_all_export'>
        <li>
          <div class="survey-wrap-input">
            <table class="widefat survey_settings survey_input">
              <tbody>
                <tr>
                  <?php foreach ($row['inputs'] as $multiple => $single) { ?>
                    <td class="ps_multiple_form">
                      <label>
                        <?php echo $single['label'] ?>
                        <?php
                        switch ($single['type']) {
                          case 'select':
                          ?>
                          <select name="ps_global_options[<?php echo $multiple; ?>]">
                            <?php foreach ($single['option'] as $opt => $o) { ?>
                              <option value="<?php echo $o ?>" <?php selected(esc_attr(prsv_global_options_get($multiple)), $o); ?>><?php echo $opt ?> </option>
                            <?php } ?>
                          </select>
                          <?php
                          break;
                          default:
                          ?>
                          <input class="<?php echo!empty($single['class']) ? $single['class'] : ''; ?>" type="<?php echo $single['type'] ?>" name="ps_global_options[<?php echo $multiple ?>]" value="<?php echo prsv_global_options_get($multiple); ?>">
                          <?php
                          break;
                        }
                        ?>
                      </label>
                    </td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </div>
        </li>
      </ul>
    </div>
  </td>
</tr>
