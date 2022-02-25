<?php if(!defined('ABSPATH')) exit;

class PerfectSurveyPostTypeMeta extends PerfectSurveyCore
{
  /**
  * Current post options loaded
  *
  * @var array
  */
  protected $post_meta = array();


  /**
  * Return all post type options schema
  *
  * @var array
  */
  public static function get_all_post_meta_fields()
  {

    return array(
      

      PRSV_PLUGIN_CODE . '_survey_turn_on'  => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => __('Activate the survey', 'perfect-survey'),
        'description' => __('Activate the survey to start collecting data. If the poll is off, you will not see the frontend and the statistics will not be saved', 'perfect-survey'),
        'input'       => array(
          'type'      => 'checkbox',
          'value'     => 'survey_on',
          'value_two' => 'survey_off',
        ),
      ),

      PRSV_PLUGIN_CODE . '_anonymize_ip'  => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => '<i class="pswp_set_icon-star pswp_set_icon-hilight"></i> '.__('Anonymize IP', 'perfect-survey'),
        'description' => __('Do you wanto to anonymize users\'s remote IP Adress?', 'perfect-survey'),
        'input'       => array(
          'type'        => 'checkbox',
          'validation'  => 'required',
          'value'      => 'anonymize_ip_on',
          'value_two'  => 'anonymize_ip_off',
        )
      ),

      PRSV_PLUGIN_CODE . '_multiple_submit'  => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => '<i class="pswp_set_icon-star pswp_set_icon-hilight"></i> '.__('Multiple submit', 'perfect-survey'),
        'description' => __('Allow users to do the same survey multiple times', 'perfect-survey'),
        'input'       => array(
          'type'        => 'checkbox',
          'validation'  => 'required',
          'value'      => 'multiple_submit_on',
          'value_two'  => 'multiple_submit_off',
        )
      ),

      PRSV_PLUGIN_CODE . '_question_submit_complete' => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => __('Submit one question or all?', 'perfect-survey'),
        'description' => __('Please select if survey must be submit complete or one question at once', 'perfect-survey'),
        'input'       => array(
          'placeholder' => __('Submit survey', 'perfect-survey'),
          'type'        => 'select',
          'validation'  => 'required',
          'options'     => array(
            'complete' => __('Complete', 'perfect-survey'),
            'one'      => __('One question at once', 'perfect-survey')
          ),
        ),
      ),

      PRSV_PLUGIN_CODE . '_question_submit_type' => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => __('How to submite the survey?', 'perfect-survey'),
        'description' => __('You can choose how the form must be submit, if normal or asyncronouse mode, without reload all page', 'perfect-survey'),
        'input'       => array(
          'placeholder' => __('Submit survey', 'perfect-survey'),
          'type'        => 'select',
          'validation'  => 'required',
          'options'     => array(
            'normal'  => __('Normal', 'perfect-survey'),
            'ajax'    => __('Asyncronous', 'perfect-survey'),
          ),
        ),
      ),

      PRSV_PLUGIN_CODE . '_pagination_style'  => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => __('Pagination style','perfect-survey'),
        'description' => __('You can create two different type of pagination, with number or with dots', 'perfect-survey'),
        'input'       => array(
          'type'    => 'select',
          'options' => array(
            'nopager'   =>  __('Without paginator', 'perfect-survey'),
            'dots'   =>  __('Pagination with dots', 'perfect-survey'),
            'number'      =>  __('Pagination with number (1 of 10)', 'perfect-survey'),
          )
        )
      ),

      PRSV_PLUGIN_CODE . '_boxed_questions'  => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'       => __('Questions style','perfect-survey'),
        'description' => __('You can choose if the questions have a border or not', 'perfect-survey'),
        'input'       => array(
          'type'    => 'select',
          'options' => array(
            'no-boxed'   =>  __('No external box', 'perfect-survey'),
            'boxed'      =>  __('With external box', 'perfect-survey'),
          )
        )
      ),

      PRSV_PLUGIN_CODE. '_survey_main_color_frontend_single' => array(
        'cat_field'   => 'psrv_pager psrv_global_settings',
        'label'        => __('Main color', 'perfect-survey'),
        'description'  => __('Select main color of your survey for best integration with your theme. Applicate a color at buttons, title, borders of inputs and UI interface.', 'perfect-survey'),
        'input'        => array(
          'type'       => 'text',
          'class'      => 'color-field',
          'value'      => '#0f9bd4'
        )
      ),

      PRSV_PLUGIN_CODE . '_btn_submit_text' => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'label'       => __('Text button submit', 'perfect-survey'),
        'description' => __('Insert the text of the button for submitting a survey', 'perfect-survey'),
        'input'       => array(
          'placeholder' => __('Submit survey', 'perfect-survey'),
          'value' => __('Send survey', 'perfect-survey'),
          'type'        => 'text',
          'validation'  => 'required'
        ),
      ),

      PRSV_PLUGIN_CODE . '_btn_submit_next_text' => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'label'       => __('Text button next', 'perfect-survey'),
        'description' => __('Insert the text of the button for submitting a single survey question (when survey is submit one question at once)', 'perfect-survey'),
        'input'       => array(
          'placeholder' => __('Submit single question', 'perfect-survey'),
          'value' => __('Go next question', 'perfect-survey'),
          'type'        => 'text',
          'validation'  => 'required'
        ),
      ),

      PRSV_PLUGIN_CODE . '_survey_turn_off_message'  => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'label'       => __('Thank you message (only for disabled survey)','perfect-survey'),
        'description' => __('This field will show a courtesy message if the survey is not active, change the message to show to the public', 'perfect-survey'),
        'input'       => array(
          'value' => __('We are sorry, the survey is currently suspended or being created, please try again later ;)', 'perfect-survey'),
          'type'        => 'text',
          'placeholder' => '',
        )
      ),

      PRSV_PLUGIN_CODE . '_success_message_complete'  => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'rowspan'     => 2,
        'label'       => __('Thank you message (multiple question mode)','perfect-survey'),
        'description' => __('Message to show after complete survey', 'perfect-survey'),
        'allowempty'  => true,
        'input'       => array(
          'value'       => __('Thank you for submitting the questionnaire, your data has been successfully saved.', 'perfect-survey'),
          'type'        => 'text',
          'placeholder' => '',
        )
      ),

      PRSV_PLUGIN_CODE . '_success_message_complete_on'  => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'label'       => '',
        'description' => '',
        'input'       => array(
          'type'      => 'checkbox',
          'value'     => 'message_complete_on',
          'value_two' => 'message_complete_off',
        ),
      ),

      PRSV_PLUGIN_CODE . '_success_message_one'  => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'rowspan'     => 2,
        'label'       => __('Thank you message (single question mode)','perfect-survey'),
        'description' => __('Message to show after complete each question (only in question single mode)', 'perfect-survey'),
        'allowempty'  => true,
        'input'       => array(
          'value'       => __('Thank you for submitting this question, your data has been successfully saved.', 'perfect-survey'),
          'type'        => 'text',
          'placeholder' => '',
        )
      ),

      PRSV_PLUGIN_CODE . '_success_message_one_on'  => array(
        'cat_field'   => 'psrv_pager psrv_messages_settings',
        'label'       => '',
        'description' => '',
        'input'       => array(
          'type'      => 'checkbox',
          'value'     => 'message_one_on',
          'value_two' => 'message_one_off',
        ),
      ),

      PRSV_PLUGIN_CODE . '_survey_stats_frontend'  => array(
        'cat_field'   => 'psrv_pager psrv_stats_settings',
        'label'       => __('Public statistics in Front-end', 'perfect-survey'),
        'description' => __('Thanks to this field, you can choose whether to display statistics in the front end. They will appear at the bottom of the survey.', 'perfect-survey'),
        'input'       => array(
          'type'      => 'checkbox',
          'value'     => 'statistics_frontend_on',
          'value_two' => 'statistics_frontend_off',
        ),
      ),


      PRSV_PLUGIN_CODE . '_survey_tables_frontend'  => array(
        'cat_field'   => 'psrv_pager psrv_stats_settings',
        'label'       => __('Statistics table in Front-end', 'perfect-survey'),
        'description' => __('You can decide whether to show or hide the tables in the frontend statistics.', 'perfect-survey'),
        'input'       => array(
          'type'      => 'checkbox',
          'value'     => 'statistics_table_on',
          'value_two' => 'statistics_table_off',
        ),
      ),

      PRSV_PLUGIN_CODE . '_survey_stats_frontend_end_survey'  => array(
        'cat_field'   => 'psrv_pager psrv_stats_settings',
        'label'       => __('When do you want to publish the statistics?'),
        'description' => __('Thanks to this field, you can choose whether to display statistics in the front end. They will appear at the bottom of the survey.', 'perfect-survey'),
        'input'       => array(
          'type'    => 'select',
          'options' => array(
            'statistics_frontend_always'      =>  __('Always show statistics in the front end', 'perfect-survey'),
            'statistics_frontend_on_end'   =>  __('Show statistics in the front end only when users finish the survey', 'perfect-survey'),
          )
        )
      ),

      PRSV_PLUGIN_CODE . '_success_page_link'  => array(
        'cat_field'   => 'psrv_pager psrv_stats_settings',
        'label'       => __('Thank you screen', 'perfect-survey'),
        'description' => __('Choose your thank you page, will be visible after sending a survey', 'perfect-survey'),
        'input'       => array(
          'type'        => 'url',
          'placeholder' => 'http://',
          'value' => ''
        )
      ),
      
    );
  }


  public function get_defaults_post_meta()
  {
    $post_meta_fields =$this->get_all_post_meta_fields();

    foreach($post_meta_fields as $meta_name => $post_meta_field)
    {
      if(isset($post_meta_field['input']) && isset($post_meta_field['input']['value']))
      {
        $defaults_post_meta[$meta_name] = $post_meta_field['input']['value'];
      }
    }

    return $defaults_post_meta;
  }

  public function wp_init()
  {
    //Nothing to do on wp init :-)
  }


  /**
  * Return post meta by name for post id or current post
  *
  * @param string $name       name of config
  * @param mixed  $default    default value to return, default false
  * @param int    $post_id    id of post
  *
  * @return mixed
  */
  public function get($name, $default = false, $post_id = null)
  {
    $name    =  preg_match('/^'.PRSV_PLUGIN_CODE.'_(.*)$/',$name, $matches) ? $name : PRSV_PLUGIN_CODE.'_'.$name;

    $post_id = $post_id ? $post_id : prsv_get('post_type')->current_post_id;

    $this->load($post_id);

    $post_meta_fields = $this->get_all_post_meta_fields();

    if($post_id && array_key_exists($name, $this->post_meta[$post_id]) && !empty($post_meta_fields[$name]['allowempty']))
    {
      return $this->post_meta[$post_id][$name];
    }

    return $post_id && !empty($this->post_meta[$post_id][$name]) ? $this->post_meta[$post_id][$name] : $default;
  }

  /**
  * Return all post meta
  *
  * @param int $ID  the post ID, default null, current post id
  *
  * @return array
  */
  public function get_all($ID = null)
  {
    $ID = $ID ? $ID : prsv_get('post_type')->current_post;

    return $this->load($ID);
  }

  /**
  * Load all configurations in post meta
  *
  * @param int $ID  the post ID, default null, current post id
  *
  * @return array
  */
  public function load($ID = null)
  {
    $post_id = $ID ? $ID : prsv_get('post_type')->current_post_id;

    if(isset($this->post_meta[$post_id]))
    {
      return $this->post_meta[$post_id];
    }

    if(empty($this->post_meta[$post_id]))
    {
      foreach(static::get_all_post_meta_fields() as $name => $value)
      {
        $this->post_meta[$post_id][$name] = get_post_meta($post_id, $name, true);
      }
    }

    return $this->post_meta[$post_id];
  }
}
