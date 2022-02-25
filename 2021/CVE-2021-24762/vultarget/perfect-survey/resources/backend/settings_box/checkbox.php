<tr>
  <?php require 'settings_layout/label.php'; ?>
  <td>
    <div class="survey-wrap-input">
      <ul class='survey_select_all_export'>
        <li>
          <div class="survey-wrap-input">
            <label class="psswitch">
              <input type="hidden" name="ps_global_options[<?php echo $composer; ?>]" value="<?php echo $row['input']['value_two']; ?>">
              <input name="ps_global_options[<?php echo $composer; ?>]" type="<?php echo $row['input']['type']; ?>"  value="<?php echo $row['input']['value']; ?>"  <?php checked(esc_attr(prsv_global_options_get($composer)), $row['input']['value']); ?>>
              <span class="psslider psround"></span>
            </label>
          </div>
        </li>
      </ul>
    </div>
  </td>
</tr>
