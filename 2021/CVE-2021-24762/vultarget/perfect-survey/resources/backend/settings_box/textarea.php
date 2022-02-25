<tr>
  <?php require 'settings_layout/label.php'; ?>
  <td>
    <div class="survey-wrap-input">
      <ul class='survey_select_all_export'>
        <li>
          <label>
            <textarea class="<?php echo $row['input']['class'] ?>" rows="<?php echo $row['input']['rows']; ?>"  name="ps_global_options[<?php echo $composer; ?>]"><?php echo prsv_global_options_get($composer); ?></textarea>
          </label>
        </li>
      </ul>
    </div>
  </td>
</tr>
