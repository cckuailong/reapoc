<tr>
  <?php require 'settings_layout/label.php'; ?>
  <td>
    <div class="survey-wrap-input">
      <ul class='survey_select_all_export'>
        <li>
          <label>
            <input name="ps_global_options[<?php echo $composer; ?>]"  type="<?php echo $row['input']['type']; ?>" class="survey-input-regular <?php echo!empty($row['input']['class']) ? $row['input']['class'] : ''; ?>" value="<?php echo prsv_global_options_get($composer); ?>" <?php if (!empty($row['input']['placeholder'])) { ?>placeholder="<?php echo $row['input']['placeholder']; ?>" <?php } ?> <?php echo isset($row['input']['attributes']) ? $row['input']['attributes'] : ''; ?>>
            </label>
          </li>
        </ul>
      </div>
    </td>
  </tr>
