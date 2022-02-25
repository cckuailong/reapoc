<tr>
  <?php require 'settings_layout/label.php'; ?>
  <td>
    <div class="survey-wrap-input">
      <ul class='survey_select_all_export'>
        <li>
          <label>
            <select name="ps_global_options[<?php echo $composer; ?>]" <?php echo isset($composer['input']['attributes']) ? $composer['input']['attributes'] : ''; ?>>
              <?php foreach ($row['input']['options'] as $value => $label) { ?>
                <option value="<?php echo $value; ?>" <?php selected(esc_attr(prsv_global_options_get($composer)), $value); ?>><?php echo $label; ?></option>
              <?php } ?>
            </select>
          </label>
        </li>
      </ul>
    </div>
  </td>
</tr>
