<?php add_meta_box('survey_logic', __('Logic conditions', 'perfect-survey'), function() { ?>
  <table id="survey-empty-questions-box" class="survey_settings logic-conditions survey_input">
    <tbody>
      <tr>
        <td>
          <div class='survey-empty survey_yellow_class'>
            <p class="survey-empty-message">
            <i class="pswp_set_icon-star pswp_set_icon-hilight"></i>  <?php _e('Create now your logic conditions', 'perfect-survey');?>
            </p>
            <p>
              <?php _e('All conditions will be active only when survey is one question for page. Only answer of type multiple answers can be used here', 'perfect-survey') ?>
            </p>
            <br>
            <a id="survey_add_condition" class="button button-primary button-large" href="#"><i class="pswp_set_icon-plus"></i> <?php _e('Add logic conditions', 'perfect-survey') ?></a>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Logic conditions container -->
  <div id="logic-conditions-container" class="survey_logic_conditions survey_sortable-item">
    <?php
    $logic_conditions = prsv_get_post_type_model()->get_logic_conditions();
    if(!empty($logic_conditions))
    {
      foreach($logic_conditions as $logic_condition)
      {
        prsv_resource_include_backend('wp_ps_metaboxes/logic_condition_item' , array('logic_condition' => $logic_condition ));
      }
    }
    ?>
  </div>
  <!-- end -->
<?php }, PRSV_PLUGIN_CODE, 'normal', 'high'); ?>