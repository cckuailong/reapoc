<?php
add_meta_box('survey_shortcode', __('Copy a shortcode', 'perfect-survey'), function(){
  global $post;
  ?>
  <table class="survey_settings survey_input">
    <tbody>
      <tr>
        <td class="label">
          <label><?php _e('Shortcode', 'perfect-survey') ?></label>
          <p><?php _e('Copy this shortcode and enter it in your page, article or widget', 'perfect-survey') ?></p>
        </td>
      </tr>
      <tr>
        <td>
          <div class="survey-wrap-input">
            <input type="text" onfocus="this.select();" readonly="readonly" value='[perfect_survey id="<?php echo $post->ID ?>"]' class="large-text code">
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <?php
}, PRSV_PLUGIN_CODE, 'side', 'default');
