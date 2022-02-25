<?php add_meta_box('survey_settings', __('Settings', 'perfect-survey'),function(){  ?>

  <div class="psrv_tabs_nav_container">
    <ul class="psrv_tabs_nav">
      <li id="psrv_global_settings" class="psrv_active_tab_clicked"><i class="pswp_set_icon-wrench psrv_icon_hidden"></i> <?php _e('Global settings', 'perfect-survey') ?></li>
      <li id="psrv_messages_settings"><i class="pswp_set_icon-file-text  psrv_icon_hidden"></i> <?php _e('Text', 'perfect-survey') ?></li>
      <li id="psrv_stats_settings"><i class="pswp_set_icon-pie-chart  psrv_icon_hidden"></i> <?php _e('Statistics', 'perfect-survey') ?></li>
    </ul>
  </div>
  <table class="survey_settings survey_input">
    <tbody>
      <?php
      foreach(prsv_get('post_type_meta')->get_all_post_meta_fields() as $meta_name => $meta_field)
      {
        ?>
        <tr class="<?php echo $meta_field['cat_field']; ?>">
          <?php if(!empty($meta_field['label'])){ ?>
            <td <?php if(!empty($meta_field['rowspan'])){ ?>rowspan="<?php echo $meta_field['rowspan'];?>"<?php } ?> class="label">
              <label><?php echo $meta_field['label'];?></label>
              <?php if(!empty($meta_field['description'])) { ?>
                <p><?php echo $meta_field['description']; ?></p>
              <?php } ?>
            </td>
          <?php } ?>
          <td>
            <div class="survey-wrap-input">
              <?php
              switch($meta_field['input']['type'])
              {
                case 'select':
                ?>
                <select name="ps_post_meta[<?php echo $meta_name;?>]" <?php  echo isset($meta_field['input']['attributes']) ? $meta_field['input']['attributes'] : '' ;?>>
                  <?php foreach($meta_field['input']['options'] as $value => $label) { ?>
                    <option value="<?php echo $value;?>" <?php selected(esc_attr(prsv_post_meta_get($meta_name)), $value); ?>><?php echo $label;?></option>
                  <?php } ?>
                </select>
                <?php
                break;
                case 'checkbox':
                ?>
                <label class="psswitch">
                  <input type="hidden" name="ps_post_meta[<?php echo $meta_name; ?>]" value="<?php echo $meta_field['input']['value_two'];?>">
                  <input name="ps_post_meta[<?php echo $meta_name; ?>]" type="<?php echo $meta_field['input']['type'];?>"  value="<?php echo $meta_field['input']['value'];?>"  <?php checked(esc_attr(prsv_post_meta_get($meta_name)), $meta_field['input']['value']); ?>>
                  <span class="psslider psround"></span>
                </label>
                <?php
                break;
                default:
                ?>
                <input name="ps_post_meta[<?php echo $meta_name; ?>]"  type="<?php echo $meta_field['input']['type'];?>" class="survey-input-regular <?php echo !empty($meta_field['input']['class']) ? $meta_field['input']['class'] : '';?>" value="<?php echo prsv_post_meta_get($meta_name,!empty($meta_field['input']['value']) ? $meta_field['input']['value'] : '');?>" <?php if(!empty($meta_field['input']['placeholder'])){ ?>placeholder="<?php echo $meta_field['input']['placeholder'];?>" <?php } ?> <?php echo isset($meta_field['input']['attributes']) ? $meta_field['input']['attributes'] : '' ;?>>

                  <?php
                  break;
                }

                ?>
                <?php if(!empty($meta_field['allowempty'])){ ?>
                  <a href="#" data-meta="<?php echo $meta_name;?>" class="ps-meta-restore"><?php _e('Restore default value', 'perfect-survey');?></a>
                <?php } ?>
              </div>
            </td>
          </tr>
        <?php }  ?>
      </tbody>
    </table>
    <!-- LOAD TEMPLATE EDITOR FOR AJAX CALL -->
    <div style="display: none;">
      <?php wp_editor('', 'editor_template'); ?>
    </div>
    <!-- END -->
    <?php if(defined("PRSV_NOUNCE_FIELD_NAME") && PRSV_NOUNCE_FIELD_NAME) { wp_nonce_field(PRSV_NOUNCE_FIELD_NAME, PRSV_NOUNCE_FIELD_VALUE); } }, PRSV_PLUGIN_CODE, 'normal', 'high'); ?>
