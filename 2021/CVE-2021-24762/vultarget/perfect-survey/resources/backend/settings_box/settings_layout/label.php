<?php if (!empty($row['label'])) { ?>
  <td class="label">
    <label><?php echo $row['label']; ?></label>
    <p class="ps_setting_description_field"><?php echo!empty($row['label']) ? $row['description'] : ''; ?></p>
  </td>
<?php } ?>
