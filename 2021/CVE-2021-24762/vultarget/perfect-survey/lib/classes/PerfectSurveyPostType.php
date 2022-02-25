<?php if(!defined('ABSPATH')) exit;

/**
* Description of PerfectSurveyPostType
*
* @author andrea.namici
*/
class PerfectSurveyPostType extends PerfectSurveyDB
{
  /**
  * Current post
  *
  * @var array
  */
  public $current_post = null;

  /**
  * Current post id
  *
  * @var int
  */
  public $current_post_id = null;


  /**
  * Current Session ID
  *
  * @var string
  */
  public $current_session_id = null;


 /**
  * Current cookie name
  *
  * @var string
  */
  public $current_cookie_name = null;

 /**
  * Current cookie value
  *
  * @var string
  */
  public $current_cookie_id   = null;

  /**
   * Current cookie exp
   */
  public $current_cookie_exp  = null;

  /**
  * Current user ID
  *
  * @var int
  */
  public $current_user_id;

  public function wp_init()
  {
    add_action('admin_head', array($this,'init_current_post'));

    add_action('wp_head', array($this,'init_current_post'));

    add_action('init', array($this, 'init'));
  }

  public function init()
  {
    // Create post type
    $labels = array(
      'name' => __('All survey', 'perfect-survey'),
      'singular_name' => __('Survey', 'perfect-survey'),
      'add_new' => __('Add New', 'perfect-survey'),
      'add_new_item' => __('Add New Survey', 'perfect-survey'),
      'edit_item' => __('Edit Survey', 'perfect-survey'),
      'new_item' => __('New Survey', 'perfect-survey'),
      'view_item' => __('View Survey', 'perfect-survey'),
      'search_items' => __('Search Survey', 'perfect-survey'),
      'not_found' => __('No Survey found', 'perfect-survey'),
      'not_found_in_trash' => __('No Survey found in Trash', 'perfect-survey'),
    );

    register_post_type(PRSV_PLUGIN_CODE, array(
      'labels' => $labels,
      'public' => false,
      'show_ui' => true,
      '_builtin' => false,
      'capability_type' => 'page',
      'hierarchical' => true,
      'register_meta_box_cb' => array($this,'ps_include_post_edit'),
      'rewrite' => false,
      'query_var' => PRSV_PLUGIN_CODE,
      'supports' => array(
        'title',
        'author'
      ),
      'show_in_menu' => true,
      'menu_icon' => 'dashicons-clipboard'
    ));

    $this->init_session();

    add_filter('enter_title_here', array($this, 'ps_new_title'));
    add_filter('post_updated_messages', array($this, 'ps_post_updated_messages'));
    add_filter('manage_ps_posts_columns', array($this,'ps_add_columns'));
    add_filter('manage_ps_posts_columns', array($this,'ps_set_columns'));
    add_filter('manage_ps_posts_columns', array($this,'ps_set_custom_edit_columns'));

    add_shortcode(PRSV_SHORTCODE_NAME, array($this,'ps_survey_view'));

    add_action('manage_ps_posts_custom_column', array($this, 'ps_manage_custom_columns'), 10, 2);

    add_action( 'post_submitbox_misc_actions', function($post){
      if($post->post_type != PRSV_POST_TYPE)
      {
        return false;
      }
      $html  = '<div class="misc-pub-section">';
      $html .= '<a href="#" class="button button-large btn-copy-survey" data-id="'.$post->ID.'" data-confirm-text="'.__('Are you sure to copy entire survey?', 'perfect-survey').'">'.__('Copy this survey', 'perfect-survey').'</a>';
      $html .= '</div>';
      echo $html;
    });
  }

  /**
  * Init session
  *
  * @return PerfectSurveyPostType
  */
  public function init_session()
  {
    if(!@session_id())
    {
      @session_set_cookie_params(0, '/');
      @session_name('wp-ps-session');
      @session_start();
    }

    $_SESSION['survey_session_id'] = empty($_SESSION['survey_session_id']) ? uniqid() : $_SESSION['survey_session_id'];
    
    $this->current_session_id = $_SESSION['survey_session_id'];
    $this->current_user_id    = get_current_user_id();

    return $this;
  }


  public function generate_session_id()
  {
    $sessid = uniqid();
    
    $_SESSION['survey_session_id'] = $sessid;
    $this->current_session_id = $sessid;

    return $sessid;
  }


  public function init_cookie($ID)
  {
    $this->current_cookie_name = 'wp-'.PRSV_PLUGIN_CODE.'-survey-'.$ID;
    $cookie_id = prsv_get_cookie_id();
    $this->current_cookie_id   = $cookie_id ? $cookie_id :  md5($ID.uniqid());
    $this->current_cookie_exp  = strtotime(prsv_post_meta_get('cookie_id_exp',PRSV_COOKIE_EXP_NEVER, $this->current_cookie_id));
    
    return $this;
  }


  /**
  * Init current post
  *
  * @return PerfectSurveyPostType
  */
  public function init_current_post()
  {
    $this->current_post    = get_post();
    $this->current_post_id = $this->current_post ? $this->current_post->ID : null;

    return $this;
  }


  /**
  * Configure post type messages
  *
  * @global array $post
  * @global int   $post_ID
  * @param array  $messages
  *
  * @return array
  */
  public function ps_post_updated_messages($messages)
  {
    global $post, $post_ID;

    $messages[PRSV_PLUGIN_CODE] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __('Survey updated.', 'perfect-survey'),
      2 => __('Survey updated.', 'perfect-survey'),
      3 => __('Survey deleted.', 'perfect-survey'),
      4 => __('Survey updated.', 'perfect-survey'),
      /* translators: %s: date and time of the revision */
      5 => prsv_input_get('revision') ? sprintf(__('Survey restored to revision from %s', 'perfect-survey'), wp_post_revision_title((int)  prsv_input_get('revision'), false)) : false,
      6 => __('Survey published.', 'perfect-survey'),
      7 => __('Survey saved.', 'perfect-survey'),
      8 => __('Survey submitted.', 'perfect-survey'),
      9 => __('Survey scheduled for.', 'perfect-survey'),
      10 => __('Survey draft updated.', 'perfect-survey'),
    );

    return $messages;
  }

  public function ps_manage_custom_columns($column, $post_id)
  {
    switch ($column)
    {
      case 'shortcode' :
      echo "<input type='text' onfocus='this.select();' readonly='readonly' value='" . sprintf(PRSV_SHORTCODE, $post_id) . "' class='large-text code'>";
      break;

      case 'author' :

      $terms = get_the_term_list($post_id, 'author', '', ',', '');
      if (is_string($terms))
      {
        echo $terms;
      }
      break;

      case 'publisher' :
      echo get_post_meta($post_id, 'publisher', true);
      break;
    }
  }


  /**
  * Add columns
  *
  * @param array $columns array columns
  *
  * @return bool
  */
  public function ps_add_columns($columns)
  {
    unset($columns['author']);
    return array_merge($columns, array('author' => __('Author', 'perfect-survey')));
  }


  /**
  * Post type colums order
  */
  public function ps_set_columns($columns)
  {
    return array(
      'cb'           => '<input type="checkbox" />',
      'title'        => __('Title', 'perfect-survey'),
      'shortcode'    => __('Shortcode', 'perfect-survey'),
      'author'       => __('Author', 'perfect-survey'),
      'date'         => __('Date', 'perfect-survey')
    );
  }

  /**
  * SEt custom column edit
  *
  * @param array $columns
  *
  * @return array
  */
  public function ps_set_custom_edit_columns($columns)
  {
    $columns['author'] = __('Author', 'perfect-survey');
    return $columns;
  }

  /**
  * custom placeholder
  */
  public function ps_new_title()
  {
    $title = '';
    $screen = get_current_screen();

    if ($screen->post_type == PRSV_POST_TYPE)
    {
      $title = __('Insert new survey title', 'perfect-survey');
    }

    return $title;
  }

  /**
  * include post type edit page
  *
  */
  public function ps_include_post_edit()
  {
    require_once PRSV_BASE_PATH_RESOURCES_BACKEND. '/post-edit.php';
  }

  /**
  * Create survey in front-end post
  *
  * @param array $attrs (id, etc..)
  *
  * @return string survey HTML
  */
  public function ps_survey_view($atts = array())
  {
    global $ps_post;
    global $ps_post_atts;
    global $ps_questions;
    global $ps_all_questions;
    global $ps_answers;
    global $ps_answers_values;
    global $ps_post_meta;
    global $ps_atts;
    global $ps_logic_conditions;

    $ps_atts = $atts;

    $settings = prsv_get('settings');

    if(!$settings['valid_purchase'])
    {
      return '<!-- '.PRSV_PLUGIN_NAME.': plugin not registered -->';
    }

    if(empty($atts) || !isset($atts['id']))
    {
      return '<!-- '.PRSV_PLUGIN_NAME.': invalid tag attributes, field "id" is missing on shorttag -->';
    }

    $ID = $atts['id'];/*Survey post ID*/

    $ps_post_atts = $atts;
    $ps_post      = get_post($ID);

    if(!$ps_post || empty($ps_post))
    {
      return '<!-- '.PRSV_PLUGIN_NAME.': invalid tag attributes, field "id" is not a valid post ID -->';
    }
    if($ps_post->post_status != 'publish')
    {
      return '<!-- '.PRSV_PLUGIN_NAME.': invalid tag attributes, this post of type "'.PRSV_PLUGIN_CODE.'" is not publish -->';
    }

    $ps_questions = $ps_all_questions =  prsv_get_post_type_model()->get_questions($ID);
    $ps_post_meta = prsv_post_meta_get_all($ID);
    $ps_logic_conditions = prsv_get_post_type_model()->get_logic_conditions($ID);

    if($ps_questions)
    {
      $first_question    = null;
      $last_question     = $ps_questions[count($ps_questions)-1];

      $ps_post->submit_btn_text = $ps_post_meta['ps_btn_submit_text'];
      $ps_post->complete        = true;
    
      $ps_post->total_questions = count($ps_questions);

      foreach($ps_questions as $key => $question)
      {
        if(!$question['text'])
        {
          unset($ps_questions[$key]);
          continue;
        }

        if(!empty($question['question_data']))
        {
          unset($ps_questions[$key]);
          continue;
        }
        $question['question_data']   = prsv_get_post_type_model()->get_question_data($question['question_id']);
        $question_type               = prsv_get_post_type_model()->get_question_type($question['type']);
        $answers                     = prsv_get_post_type_model()->get_answers($question['question_id']);
        $answers_values              = prsv_get_post_type_model()->get_answers_values($question['question_id']);

        $question['question_type']     = $question_type;
        $question['frontend_template'] = 'questions_types/'.$question['type'];

        if(!empty($answers))
        {
          foreach($answers as $answer_key => $answer)
          {
            $answer['frontend_template'] = 'answers_input/'. $question_type['answer_input'];
            $answers[$answer_key] = $answer;
          }
        }

        $ps_answers[$question['question_id']]        = $answers;
        $ps_answers_values[$question['question_id']] = $answers_values;

        $question['ID']             = $ID;
        $question['answers']        = $answers;
        $question['answers_values'] = $answers_values;

        $question['css_class'] = $ps_post_meta['ps_boxed_questions'].' '.$question['type']. ' '.(empty($question['question_data']) && $question['answers'] ? 'answerable' : 'not-answerable').' '.($question['question_data'] ? 'answered' : 'not-answered');

        if(empty($question['question_data']))
        {
          $ps_post->complete = false;
        }

        if($ps_post_meta['ps_question_submit_complete'] == 'one')
        {
          if(!$first_question && empty($question['question_data']))
          {
            $first_question = $question;
            $ps_post->current_question_id       = $question['question_id'];
            $ps_post->current_question_number   = $key + 1;
          }
          else
          {
            $question['css_class'].= ' hidden_ps_questions_com';
          }
        }
        else if(!empty($question['question_data']))
        {
          $question['css_class'].= ' hidden_ps_questions_com';
        }

        $ps_questions[$key]      = $question;
        $ps_all_questions[$key]  = $question;
      }

      if($ps_post_meta['ps_question_submit_complete'] == 'one')
      {
        if($ps_post->current_question_number != $ps_post->total_questions)
        {
          $ps_post->submit_btn_text = $ps_post_meta['ps_btn_submit_next_text'];
        }
      }

      if($ps_post->complete && $ps_post_meta['ps_multiple_submit'] == 'multiple_submit_on')
      {
         //Permetti nuovo loop
         $this->generate_session_id();
      }

    } else {
      require_once PRSV_BASE_PATH_RESOURCES_FRONTEND. '/questions_types/no-questions.php';
    }

    return prsv_resource_render_frontend('survey');
  }
}
