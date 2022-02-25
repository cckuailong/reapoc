<tr>
  <?php require 'settings_layout/label.php'; ?>
  <td>
    <div class="survey-wrap-input">
      <ul class='survey_select_all_export_image'>
        <?php foreach ($row['input']['options'] as $value => $label) { ?>
          <li>
            <label class="survey_radio_image">
              <input type="radio" name="ps_global_options[<?php echo $composer; ?>]" value="<?php echo $value ?>" <?php checked(esc_attr(prsv_global_options_get($composer)), $value); ?>>
              <img src="<?php echo $label['image'] ?>">
              <p><?php echo $label['label'] ?></p>
            </label>
          </li>
        <?php } ?>
      </ul>
    </div>
  </td>
</tr>
