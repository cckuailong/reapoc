<?php if(!defined('ABSPATH')) exit; // Exit if accessed directlys

/**
* Class for access data of post type
*/
class PerfectSurveyPostTypeModel extends PerfectSurveyPostType
{
  /* Questions types */

  const PS_QUESTION_TYPE_SINGLE_CHOICE   = 'single_choice';

  const PS_QUESTION_TYPE_MULTIPLE_CHOICE = 'multiple_choice';

  const PS_QUESTION_TYPE_TEXT            = 'text';

  const PS_QUESTION_TYPE_IMAGE           = 'image';

  const PS_QUESTION_TYPE_RATING          = 'rating';

  const PS_QUESTION_TYPE_SLIDER          = 'slider';

  const PS_QUESTION_TYPE_TEXT_SPAN       = 'text_span';

  const PS_QUESTION_TYPE_MATRIX          = 'matrix';

  /* Questions show types */

  const PS_ANSWER_SHOW_TYPE_ROW    = 'survey_row_settings_layout';

  const PS_ANSWER_SHOW_TYPE_COLUMN = 'survey_column_settings_layout';

  const PS_ANSWER_SHOW_TYPE_SELECT = 'select';

  /* Question logic condition type */

  const PS_LOGIC_CONDITION_TYPE_EQ   = 'eq';

  const PS_LOGIC_CONDITION_TYPE_DIFF = 'diff';

  protected $sql_table_survey            = 'ps';

  protected $sql_table_answers           = 'ps_answers';

  protected $sql_table_answers_values    = 'ps_answers_values';

  protected $sql_table_questions         = 'ps_questions';

  protected $sql_table_data              = 'ps_data';

  protected $sql_table_logic_conditions  = 'ps_logic_conditions';

  protected $sql_table_users             = 'users';

  public function wp_init()
  {
    $this->sql_table_survey            = $this->get_table_name($this->sql_table_survey);
    $this->sql_table_answers           = $this->get_table_name($this->sql_table_answers);
    $this->sql_table_questions         = $this->get_table_name($this->sql_table_questions);
    $this->sql_table_answers_values    = $this->get_table_name($this->sql_table_answers_values);
    $this->sql_table_data              = $this->get_table_name($this->sql_table_data);
    $this->sql_table_users             = $this->get_table_name($this->sql_table_users);
    $this->sql_table_logic_conditions  = $this->get_table_name($this->sql_table_logic_conditions);

    add_action('wp_head', array($this,'init_current_post'));

    add_action('admin_head', array($this,'init_current_post'));

    add_action('init',array($this,'init_session'));

    return $this;
  }

  public function get_all_questions_types()
  {
    return array(

      self::PS_QUESTION_TYPE_SINGLE_CHOICE   => array(
        'name' => __('Single choice','perfect-survey'),
        'btn_add_title' => __('add answer','perfect-survey'),
        'answer_input' => 'text',
        'answer_input_type' => 'radio',
        'answer_input_label' => false,
        'add_answer' => true,
        'multiple_answers' => true,
        'answer_show_type_default' => self::PS_ANSWER_SHOW_TYPE_COLUMN,
        'answer_show_types' =>  self::get_all_answers_show_types(),
        'icon_class' => 'pswp_set_icon-list2'
      ),

      self::PS_QUESTION_TYPE_MULTIPLE_CHOICE => array(
        'name' => __('Multiple choice', 'perfect-survey'),
        'btn_add_title' => __('add answer','perfect-survey'),
        'answer_input' => 'text',
        'answer_input_type' => 'checkbox',
        'answer_input_label' => true,
        'add_answer' => true,
        'multiple_answers' => true,
        'answer_show_type_default' => self::PS_ANSWER_SHOW_TYPE_COLUMN,
        'answer_show_types' =>  self::get_all_answers_show_types(),
        'icon_class' => 'pswp_set_icon-list'
      ),

      self::PS_QUESTION_TYPE_TEXT            => array(
        'name' =>__('Text answer', 'perfect-survey'),
        'btn_add_title' => __('add answer','perfect-survey'),
        'answer_input' => 'input_custom',
        'add_answer' => false,
        'multiple_answers' => false,
        'answer_field_types' => array(
          'textarea' => __('Text area', 'perfect-survey'),
          'date' => __('Date field', 'perfect-survey'),
          'number' => __('Number', 'perfect-survey'),
          'email' => __('E-mail','perfect-survey'),
          'text' => __('Text','perfect-survey'),
        ),
        'icon_class' => 'pswp_set_icon-stack'
      ),

      self::PS_QUESTION_TYPE_TEXT_SPAN       => array(
        'name' =>__('Custom text','perfect-survey'),
        'answer_input' => 'textarea',
        'add_answer' => false,
        'multiple_answers' => false,
        'icon_class' => 'pswp_set_icon-paragraph-left'
      ),

      self::PS_QUESTION_TYPE_MATRIX          => array(
        'name' => __('Matrix','perfect-survey'),
        'btn_add_title' => __('add answer','perfect-survey'),
        'answer_input' => 'text',
        'answer_input_type' => 'checkbox',
        'answer_input_label' => true,
        'add_answer' => true,
        'multiple_answers' => true,
        'icon_class' => 'pswp_set_icon-ungroup'
      ),

      self::PS_QUESTION_TYPE_RATING          => array(
        'name' => __('Rating', 'perfect-survey'),
        'btn_add_title' => __('add answer','perfect-survey'),
        'answer_input' => 'rating',
        'add_answer' => true,
        'multiple_answers' => false,
        'icon_class' => 'pswp_set_icon-star',
        'answer_max_values_default' => 10,
        'answer_max_values' => array(
          1   => '1',
          2   => '2',
          3   => '3',
          4   => '4',
          5   => '5',
          6   => '6',
          7   => '7',
          8   => '8',
          9   => '9',
          10  => '10'
        ),
        'answer_css_classes_default' => 'pswp_set_icon-star',
        'answer_css_classes' => array(
          'pswp_set_icon-star' => __('Star', 'perfect-survey'),
          'pswp_set_icon-heart' => __('Like', 'perfect-survey'),
          'pswp_set_icon-map' => __('Map', 'perfect-survey'),
          'pswp_set_icon-location' => __('Location', 'perfect-survey'),
          'pswp_set_icon-music'  => __('Music', 'perfect-survey'),
          'pswp_set_icon-clock' => __('Clock', 'perfect-survey'),
          'pswp_set_icon-trophy' => __('Trophy', 'perfect-survey'),
          'pswp_set_icon-glass' => __('Glass', 'perfect-survey'),
          'pswp_set_icon-glass2' => __('Martini', 'perfect-survey'),
          'pswp_set_icon-mug' => __('Beer', 'perfect-survey'),
          'pswp_set_icon-leaf' => __('Leaf', 'perfect-survey'),
          'pswp_set_icon-rocket' => __('Rocket', 'perfect-survey'),
          'pswp_set_icon-spoon-knife' => __('Spoon e knife', 'perfect-survey'),
          'pswp_set_icon-fire' => __('Fire', 'perfect-survey'),
          'pswp_set_icon-power' => __('Power', 'perfect-survey'),
          'pswp_set_icon-cloud' => __('Cloud', 'perfect-survey'),
          'pswp_set_icon-flag' => __('Flag', 'perfect-survey'),
          'pswp_set_icon-bubble2' => __('Buble', 'perfect-survey'),
          'pswp_set_icon-checkmark2' => __('Checkmark', 'perfect-survey'),
          'pswp_set_icon-smile' => __('Smiley', 'perfect-survey'),
          'pswp_set_icon-happy' => __('Happy', 'perfect-survey'),
          'pswp_set_icon-crying' => __('Crying', 'perfect-survey'),
          'pswp_set_icon-frustrated' => __('Frustated', 'perfect-survey'),
          'pswp_set_icon-sleepy' => __('Sleepy', 'perfect-survey'),
          'pswp_set_icon-wondering' => __('Wondering', 'perfect-survey'),
          'pswp_set_icon-hipster' => __('Hipster', 'perfect-survey'),
          'pswp_set_icon-neutral' => __('Neutral', 'perfect-survey'),
          'pswp_set_icon-confused' => __('Confused', 'perfect-survey'),
          'pswp_set_icon-evil' => __('Evil', 'perfect-survey'),
          'pswp_set_icon-cool' => __('Cool', 'perfect-survey'),
          'pswp_set_icon-grin' => __('Grin', 'perfect-survey')
        )
      ),

      self::PS_QUESTION_TYPE_SLIDER          => array(
        'name' => __('Rating scale', 'perfect-survey'),
        'btn_add_title' => __('add answer','perfect-survey'),
        'answer_input' => 'slider',
        'add_answer' => true,
        'multiple_answers' => true,
        'icon_class' => 'pswp_set_icon-equalizer',
        'answer_max_values_default' => 10,
        'answer_max_values' => array(
          1   => '1',
          2   => '2',
          3   => '3',
          4   => '4',
          5   => '5',
          6   => '6',
          7   => '7',
          8   => '8',
          9   => '9',
          10  => '10'
        ),
        'answer_css_classes_default' => 'pswp_set_icon-star',
        'answer_css_classes' => array(
          'pswp_set_icon-star' => __('Star', 'perfect-survey'),
          'pswp_set_icon-heart' => __('Like', 'perfect-survey'),
          'pswp_set_icon-map' => __('Map', 'perfect-survey'),
          'pswp_set_icon-location' => __('Location', 'perfect-survey'),
          'pswp_set_icon-music'  => __('Music', 'perfect-survey'),
          'pswp_set_icon-clock' => __('Clock', 'perfect-survey'),
          'pswp_set_icon-trophy' => __('Trophy', 'perfect-survey'),
          'pswp_set_icon-glass' => __('Glass', 'perfect-survey'),
          'pswp_set_icon-glass2' => __('Martini', 'perfect-survey'),
          'pswp_set_icon-mug' => __('Beer', 'perfect-survey'),
          'pswp_set_icon-leaf' => __('Leaf', 'perfect-survey'),
          'pswp_set_icon-rocket' => __('Rocket', 'perfect-survey'),
          'pswp_set_icon-spoon-knife' => __('Spoon e knife', 'perfect-survey'),
          'pswp_set_icon-fire' => __('Fire', 'perfect-survey'),
          'pswp_set_icon-power' => __('Power', 'perfect-survey'),
          'pswp_set_icon-cloud' => __('Cloud', 'perfect-survey'),
          'pswp_set_icon-flag' => __('Flag', 'perfect-survey'),
          'pswp_set_icon-bubble2' => __('Buble', 'perfect-survey'),
          'pswp_set_icon-checkmark2' => __('Checkmark', 'perfect-survey'),
          'pswp_set_icon-smile' => __('Smiley', 'perfect-survey'),
          'pswp_set_icon-happy' => __('Happy', 'perfect-survey'),
          'pswp_set_icon-crying' => __('Crying', 'perfect-survey'),
          'pswp_set_icon-frustrated' => __('Frustated', 'perfect-survey'),
          'pswp_set_icon-sleepy' => __('Sleepy', 'perfect-survey'),
          'pswp_set_icon-wondering' => __('Wondering', 'perfect-survey'),
          'pswp_set_icon-hipster' => __('Hipster', 'perfect-survey'),
          'pswp_set_icon-neutral' => __('Neutral', 'perfect-survey'),
          'pswp_set_icon-confused' => __('Confused', 'perfect-survey'),
          'pswp_set_icon-evil' => __('Evil', 'perfect-survey'),
          'pswp_set_icon-cool' => __('Cool', 'perfect-survey'),
          'pswp_set_icon-grin' => __('Grin', 'perfect-survey')
        )
      ),

      self::PS_QUESTION_TYPE_IMAGE           => array(
        'name' => __('Image choice','perfect-survey'),
        'btn_add_title' => __('Add image','perfect-survey'),
        'answer_input' => 'image',
        'answer_show_type_default' => self::PS_ANSWER_SHOW_TYPE_COLUMN,
        'answer_show_types' => self::get_all_answers_show_types(self::PS_QUESTION_TYPE_IMAGE),
        'add_answer' => true,
        'multiple_answers' => true,
        'icon_class' => 'pswp_set_icon-images',
        'answer_field_types' => array(
          'checkbox' => __('Multiple choice', 'perfect-survey'),
          'radio'    => __('Single choice', 'perfect-survey')
        ),
      )
    );
  }

  /**
  * Get question type info
  *
  * @param string $type question type name
  *
  * @return array
  */
  public function get_question_type($type)
  {
    $question_types =  $this->get_all_questions_types();
    return $question_types[$type];
  }

  /**
  * Return all logic conditions types
  * @return array
  */
  public function get_logic_condition_types()
  {
    return array(
      self::PS_LOGIC_CONDITION_TYPE_EQ   => __('Equal', 'perfect-survey'),
      self::PS_LOGIC_CONDITION_TYPE_DIFF => __('Different','perfect-survey'),
    );
  }

  /**
  * Get all answers show types
  *
  * @param string $question_type  question type, default null (all)
  *
  * @return array
  */
  public static function get_all_answers_show_types($question_type = null)
  {
    $answers_show_types =  array(


      self::PS_ANSWER_SHOW_TYPE_COLUMN => array(
        'value' => 'column',
        'name'  => __('In column', 'perfect-survey')
      ),


      self::PS_ANSWER_SHOW_TYPE_ROW => array(
        'value' => 'row',
        'name'  => __('In row', 'perfect-survey')
      ),


      self::PS_ANSWER_SHOW_TYPE_SELECT => array(
        'value' => 'select',
        'name'  => __('Select field', 'perfect-survey')
      )
    );

    if($question_type == self::PS_QUESTION_TYPE_IMAGE)
    {
      unset($answers_show_types[self::PS_ANSWER_SHOW_TYPE_SELECT]);
    }

    return $answers_show_types;
  }

  /**
  * Get survey by postID
  *
  * @param int $postID
  *
  * @return WP_Post
  */
  public function get_survey($postID)
  {
    return get_post($postID);
  }

  /**
  * Get all survey info
  *
  * @param int $postID
  *
  * @return array
  */
  public function get_survey_complete($postID)
  {
    $survey    = (array) $this->get_survey($postID);
    $questions = $this->get_questions($postID);

    if($questions)
    {
      foreach($questions as $key => $question)
      {
        if($question['image'])
        {
          $question['image_post'] = (array) get_post($question['image']);
          $question['image_url']  = get_post($question['image'])->guid;
        }

        $answers = $this->get_answers($question['question_id']);

        if($answers)
        {
          foreach($answers as $k => $answer)
          {
            if($answer['image'])
            {
              $answer['image_post'] = (array) get_post($answer['image']);
              $answer['image_url']  = get_post($answer['image'])->guid;
              $answers[$k] = $answer;
            }
          }
        }

        $answers_values = $this->get_answers_values($question['question_id']);

        $question['answers_values']   = $answers_values;
        $question['answers']          = $answers;

        $questions[$key] = $question;
      }
    }

    $survey['logic_conditions'] = $this->get_logic_conditions($postID);
    $survey['questions'] = $questions;
    $survey['postmeta']  = prsv_post_meta_get_all($postID);

    return $survey;
  }


  public function import_survey(array $survey)
  {
    $postmeta  = isset($survey['postmeta'])  ? $survey['postmeta']  : array();
    $questions = isset($survey['questions']) ? $survey['questions'] : array();
    unset($survey['questions']);
    unset($survey['postmeta']);

    $survey['ID']          = null;
    $survey['post_author'] = get_current_user_id();
    $postID                = wp_insert_post($survey);

    if($postID)
    {
      $survey['ID'] = $postID;

      foreach($postmeta as $postmeta => $value)
      {
        add_post_meta($postID, $postmeta, $value);
      }

      $import_questions = array();
      $import_answers   = array();

      foreach($questions as $question)
      {
        $question['ID']          = $postID;
        $old_question_id         = $question['question_id'];

        $question['question_id'] = null;

        if($question['image_post']) //question has an image, download from url, and create relative post type
        {
          $question['image'] = $this->_build_post_type_image($question['image_post']);
        }

        $question_id = $this->add_question($postID, $question['type'],$question, false);
        $question['question_id'] = $question_id;
        $import_questions[$old_question_id] = $question_id;

        if($question_id)
        {
          if(!empty($question['answers']))
          {
            foreach($question['answers'] as $key => $answer)
            {
              $old_answer_id = $answer['answer_id'];
              if($answer['image_post'])
              {
                $answer['image'] = $this->_build_post_type_image($answer['image_post']);
              }
              $answer['question_id']  = $question_id;
              $answer['answer_id']    = null;
              $answer_id              = $this->add_answer($question_id, $answer);
              // $question['answers'][$key]['answer_id'] = $answer_id;
              // $import_answers[$old_answer_id]         = $answer_id;
            }
          }

          if(!empty($question['answers_values']))
          {
            foreach($question['answers_values'] as $answer_value)
            {
              if($answer_value['image_post'])
              {
                $answer_value['image'] = $this->_build_post_type_image($answer_value['image_post']);
              }

              $answer_value['question_id']        = $question_id;
              $answer_value['answer_value_id']    = null;
              $answer_id = $this->add_answer_value($question_id, $answer_value);
              // $question['answers'][$key]['answer_value_id'] = $answer_id;
            }
          }
        }
      }

      if(!empty($survey['logic_conditions']))
      {
        foreach($survey['logic_conditions'] as $key => $logic_condition)
        {
          $logic_condition['question_id']        = $import_questions[$logic_condition['question_id']];
          $logic_condition['answer_id']          = $logic_condition['answer_id'] > 0 ? $import_answers[$logic_condition['answer_id']] : $logic_condition['answer_id'];
          $logic_condition['logic_condition_id'] = null;
          $logic_condition_id = $this->add_logic_condition($postID, $logic_condition);
          // $question['logic_conditions'][$key]['logic_condition_id'] = $logic_condition_id;
        }
      }

    }

    return $postID;
  }


  /**
  * Return question
  *
  * @param int $question_id question id
  *
  * @return array
  */
  public function get_question($question_id)
  {
    $question =  $this->wpdb->get_row('SELECT * FROM ' . $this->sql_table_questions.' WHERE question_id = '. $question_id, ARRAY_A);

    return  $this->_build_questions_data($question);
  }

  /**
  * Return all questions
  *
  * @return array
  */
  public function get_all_surveys($published = true)
  {
    $where = '';

    if($published)
    {
      $where.=" p.post_status = 'publish'";
    }

    return  $this->wpdb->get_results('SELECT * FROM '.$this->get_table_name('posts').' p WHERE post_type="'.PRSV_POST_TYPE.'" AND '.$where, ARRAY_A);
  }

  /**
  * Return answer
  *
  * @param int $answer_id answer id
  *
  * @return array
  */
  public function get_answer($answer_id)
  {
    $answer =  $this->wpdb->get_row('SELECT * FROM ' . $this->sql_table_answers.' WHERE answer_id = '. $answer_id, ARRAY_A);

    $answer = $this->_build_answers_data($answer);

    return $answer;
  }

  /**
  * Return answer value
  *
  * @param int $answer_value_id answer id
  *
  * @return array
  */
  public function get_answer_value($answer_value_id)
  {
    return $this->wpdb->get_row('SELECT * FROM ' . $this->sql_table_answers_values.' WHERE answer_value_id = '. $answer_value_id, ARRAY_A);
  }

  /**
  * Add a question to post ID
  *
  * @param int $ID  post ID
  * @param string $type question type
  *
  * @return int
  */
  public function add_question($ID, $type, array $data = array(), $add_answer = true)
  {
    $all_question_type_data = $this->get_all_questions_types();
    $question_type_data     = $all_question_type_data[$type];

    $data = array_merge($data, array(
      'ID'                =>  $ID,
      'type'              =>  $type,
      'position'          =>  $this->get_last_position_for_question($ID) + 1,
      'answer_show_type'  =>  isset($question_type_data['answer_show_type_default'])   ? $question_type_data['answer_show_type_default']   : null,
      'answer_max_value'  =>  isset($question_type_data['answer_max_values_default'])  ? $question_type_data['answer_max_values_default']  : null,
      'answer_css_class'  =>  isset($question_type_data['answer_css_classes_default']) ? $question_type_data['answer_css_classes_default'] : null
    ));

    $res         = $this->wpdb->insert($this->sql_table_questions, $this->filter_data_by_table($this->sql_table_questions,$data));
    $question_id = $res ? $this->wpdb->insert_id : null;

    if($question_id > 0 && $add_answer)
    {
      switch($type)
      {
        case self::PS_QUESTION_TYPE_SLIDER:
        case self::PS_QUESTION_TYPE_RATING:
        case self::PS_QUESTION_TYPE_TEXT:
          $this->add_answer($question_id);
        break;
      }
    }

    return $res ? $question_id : false;
  }

  /**
  * Add logic condition
  *
  * @param int $ID
  *
  * @return int
  */
  public function add_logic_condition($ID, array $data = array())
  {
    $data        = array_merge($data,array(
      'ID'       => $ID,
      'position' => $this->get_last_position_for_logic_condition($ID) + 1,
    ));

    $res         = $this->wpdb->insert($this->sql_table_logic_conditions, $this->filter_data_by_table($this->sql_table_logic_conditions,$data));
    return $this->wpdb->insert_id;
  }

  /**
  * Add logic condition
  *
  * @param int       $logic_condition_id
  * @param array     $data
  *
  * @return bool
  */
  public function update_logic_condition($logic_condition_id, array $data)
  {
    $data = $this->filter_data_by_table($this->sql_table_logic_conditions,$data);
    return  $this->wpdb->update($this->sql_table_logic_conditions,$data, array('logic_condition_id' => $logic_condition_id));
  }

  /**
  * Update question
  *
  * @param int   $question_id    question id
  * @param array $data           data
  *
  * @return bool
  */
  public function update_question($question_id, $data)
  {
    $data = $this->filter_data_by_table($this->sql_table_questions,$data);

    $question = $this->get_question($question_id);

    $question_type_info = $this->get_question_type($question['type']);

    if(!empty($question_type_info['answer_css_classes_default']) && empty($data['answer_css_class']))
    {
      $data['answer_css_class'] = $question_type_info['answer_css_classes_default'];
    }

    if(!empty($question_type_info['answer_max_values_default']) && empty($data['answer_max_value']))
    {
      $data['answer_max_value'] = $question_type_info['answer_max_values_default'];
    }

    if(!empty($question_type_info['answer_show_type_default']) && empty($data['answer_show_type']))
    {
      $data['answer_show_type'] = $question_type_info['answer_show_type_default'];
    }

    if(!empty($data['image_properties']) && is_array($data['image_properties']))
    {
      $data['image_properties'] = json_encode($data['image_properties']);
    }

    $res =  $this->wpdb->update($this->sql_table_questions,$data, array('question_id' => $question_id));

    /*
    * If answer type has a range value
    */
    if(!empty($question_type_info['answer_max_values']) && $data['answer_max_value'] > 0)
    {
      $answers_values = $this->get_answers_values($question_id);

      if($answers_values)
      {
        foreach($answers_values as $answer_value) // Delete all answer values greater than max value
        {
          if($answer_value['value'] > $data['answer_max_value'])
          {
            $this->delete_answer_value($answer_value['answer_value_id']);
          }
        }
      }

      for($i=1;$i<=$data['answer_max_value'];$i++)
      {
        $found = false;

        if($answers_values)
        {
          foreach($answers_values as $answer_value)
          {
            if($answer_value['value'] == $i)
            {
              $found = true;
            }
          }
        }

        if(!$found)
        {
          $this->add_answer_value($question_id, array('value' => $i, 'position' => $i));
        }
      }
    }

    return $res;
  }

  /**
  * Add an answer to question
  *
  * @param int   $question_id  question_id
  * @param array $data         dati, default array() vuoto
  *
  * @return int
  */
  public function add_answer($question_id, array $data = array())
  {
    $last_position = $this->get_last_position_for_answers($question_id);

    $data  = $this->filter_data_by_table($this->sql_table_answers,array_merge($data, array('question_id' => $question_id, 'position' => $last_position + 1)));

    $res   = $this->wpdb->insert($this->sql_table_answers, $data);

    return $res ? $this->wpdb->insert_id : false;
  }

  /**
  * Update answer data
  *
  * @param int   $answer_id      answer id
  * @param array $data           data
  *
  * @return bool
  */
  public function update_answer($answer_id, $data)
  {
    $data = $this->filter_data_by_table($this->sql_table_answers,$data);

    return $this->wpdb->update($this->sql_table_answers,$data, array('answer_id' => $answer_id));
  }

  /**
  * Add an answer value to a question
  *
  * @param int   $question_id  question_id
  * @param array $data         question value data
  *
  * @return int
  */
  public function add_answer_value($question_id,array $data = array())
  {
    $last_position = $this->get_last_position_for_answers_values($question_id);

    $data = array_merge($data,array('question_id' => $question_id, 'position' => $last_position + 1));

    $data = $this->filter_data_by_table($this->sql_table_answers_values,$data);

    $res = $this->wpdb->insert($this->sql_table_answers_values,$data);

    return $res ? $this->wpdb->insert_id : false;
  }

  /**
  * Update answer value data
  *
  * @param int   $answer_value_id  answer value id
  * @param array $data             data
  *
  * @return bool
  */
  public function update_answer_value($answer_value_id, $data)
  {
    $data = $this->filter_data_by_table($this->sql_table_answers_values,$data);

    return $this->wpdb->update($this->sql_table_answers_values,$data, array('answer_value_id' => $answer_value_id));
  }

  /**
  * Delete all meta fields
  *
  * @param int $ID post id
  *
  * @return bool
  */
  public function delete_meta_field($ID, $meta)
  {
    return delete_post_meta($ID,$meta);
  }

  /**
  * Delete all meta fields
  *
  * @param int $ID post id
  *
  * @return int
  */
  public function delete_meta_fields($ID)
  {
    $deleted = 0;
    $meta_fields = prsv_get('post_type_meta')->get_all_post_meta_fields();

    foreach($meta_fields as $meta => $meta_info)
    {
      $deleted+= $this->delete_meta_field($ID, $meta) ? 1 : 0;
    }

    return $deleted;
  }

  /**
  * Delete an answer
  *
  * @param int $answer_id
  *
  * @return boolean
  */
  public function delete_answer($answer_id)
  {
    $res = $this->wpdb->delete($this->sql_table_answers, array('answer_id' => $answer_id));

    return $res > 0;
  }

  /**
  * Delete an answer value
  *
  * @param int $answer_value_id
  *
  * @return boolean
  */
  public function delete_answer_value($answer_value_id)
  {
    $res = $this->wpdb->delete($this->sql_table_answers_values, array('answer_value_id' => $answer_value_id));

    return $res > 0;
  }

  /**
  * Delete all answer for a question
  *
  * @param int $question_id
  *
  * @return boolean
  */
  public function delete_answers($question_id)
  {
    $res = $this->wpdb->delete($this->sql_table_answers, array('question_id' => $question_id));

    return $res > 0;
  }

  /**
  * Delete all answers values for a question
  *
  * @param int $question_id
  *
  * @return boolean
  */
  public function delete_answers_values($question_id)
  {
    $res = $this->wpdb->delete($this->sql_table_answers_values, array('question_id' => $question_id));

    return $res > 0;
  }

  /**
  * Delete all data for question
  *
  * @param int $question_id
  *
  * @return boolean
  */
  public function delete_question_data($question_id)
  {
    $res = $this->wpdb->delete($this->sql_table_data, array('question_id' => $question_id));

    return $res > 0;
  }

  /**
  * Delete a question
  *
  * @param int $question_id
  *
  * @return boolean
  */
  public function delete_question($question_id)
  {
    $res = $this->wpdb->delete($this->sql_table_questions, array('question_id' => $question_id));

    if($res > 0)
    {
      $this->delete_answers($question_id);
    }

    return $res > 0;
  }

  /**
  * Delete logic condition by id
  *
  * @param int $logic_condition_id
  *
  * @return bool
  */
  public function delete_logic_condition($logic_condition_id)
  {
    return  $this->wpdb->delete($this->sql_table_logic_conditions, array('logic_condition_id' => $logic_condition_id));
  }


  public function delete_all_questions_data($ID)
  {
     $questions = $this->get_questions($ID);

     if(!$questions){
         return false;
     }

     foreach($questions as $question){
         $this->delete_question_data($question['question_id']);
     }

     return true;
  }

  /**
  * Copy question, answers and all answers values associated
  *
  * @param int $question_id copied question
  *
  * @return int new question id
  */
  public function copy_survey($postID)
  {
    $survey       = $this->get_survey($postID);

    if(!$survey || $survey->post_type != PRSV_POST_TYPE)
    {
      return false;
    }

    $survey->ID   = null;
    $survey->post_title = $survey->post_title.' ('.__('copy','perfect-survey').')';
    $newID        = wp_insert_post((array) $survey);

    if($newID)
    {
      $questions = $this->get_questions($postID);

      foreach($questions as $question)
      {
        $this->copy_question($question['question_id'], $newID, false);
      }
    }

    return $newID;
  }

  /**
  * Copy question, answers and all answers values associated
  *
  * @param int $question_id copied question
  *
  * @return int new question id
  */
  public function copy_question($question_id, $postID = null, $label = true)
  {
    $question      = $this->get_question($question_id);
    $answers       = $this->get_answers($question_id);
    $answer_values = $this->get_answers_values($question_id);

    $question        = $this->filter_data_by_table($this->sql_table_questions, $question);

    $question['question_id'] = null;
    if($label == true) {
      $label = '('.__('copy','perfect-survey').')';
    }
    $question['text'] = $question['text'].' '.$label;
    $question['position']  = $this->get_last_position_for_question($question['ID']) + 1;
    if($postID){
      $question['ID'] = $postID;
    }

    $this->wpdb->insert($this->sql_table_questions,$question);
    $new_question_id = $this->wpdb->insert_id;

    if($new_question_id > 0)
    {
      if($answers)
      {
        foreach($answers as $answer)
        {
          $answer                = $this->filter_data_by_table($this->sql_table_answers, $answer);
          $answer['answer_id']   = null;
          $answer['question_id'] = $new_question_id;
          $this->wpdb->insert($this->sql_table_answers,$answer);
        }
      }

      if($answer_values)
      {
        foreach($answer_values as $answer_value)
        {
          $answer_value                      = $this->filter_data_by_table($this->sql_table_answers_values, $answer_value);
          $answer_value['answer_value_id']   = null;
          $answer_value['question_id']       = $new_question_id;
          $this->wpdb->insert($this->sql_table_answers_values,$answer_value);
        }
      }
    }

    return $new_question_id;
  }

  /**
  * Return last position defined for question's
  *
  * @param int $ID post ID
  *
  * @return int
  */
  public function get_last_position_for_question($ID)
  {
    $res = $this->wpdb->get_row('SELECT COALESCE(MAX(position),0) as last_position FROM ' . $this->sql_table_questions.' WHERE ID = '. $ID, ARRAY_A);

    if(!empty($res))
    {
      return $res['last_position'];
    }

    return 0;
  }

  /**
  * Return last position defined for question's
  *
  * @param int $ID post ID
  *
  * @return int
  */
  public function get_last_position_for_logic_condition($ID)
  {
    $res = $this->wpdb->get_row('SELECT COALESCE(MAX(position),0) as last_position FROM ' . $this->sql_table_logic_conditions.' WHERE ID = '. $ID, ARRAY_A);

    if(!empty($res))
    {
      return $res['last_position'];
    }

    return 0;
  }

  /**
  * Return last position defined for question's answers
  *
  * @param int $question_id question ID
  *
  * @return int
  */
  public function get_last_position_for_answers($question_id)
  {
    $res = $this->wpdb->get_row('SELECT COALESCE(MAX(position),0) as last_position FROM ' . $this->sql_table_answers.' WHERE question_id = '. $question_id, ARRAY_A);

    if(!empty($res))
    {
      return $res['last_position'];
    }

    return 0;
  }

  /**
  * Return last position defined for question's answers values
  *
  * @param int $question_id question ID
  *
  * @return int
  */
  public function get_last_position_for_answers_values($question_id)
  {
    $res = $this->wpdb->get_row('SELECT COALESCE(MAX(position),0) as last_position FROM ' . $this->sql_table_answers_values.' WHERE question_id = '. $question_id, ARRAY_A);

    if(!empty($res))
    {
      return $res['last_position'];
    }

    return 0;
  }

  /**
  * Get post questions
  *
  * @param int   $ID         id of post
  * @param array $options    options
  *
  * @return array all questions
  */
  public function get_questions($ID = null, array $options = array())
  {
    $ID = $ID ? $ID : $this->current_post_id;

    $questions = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_questions.' WHERE ID = '. $ID.' ORDER BY position ASC', ARRAY_A);

    if($questions)
    {
      foreach($questions as $key => $question)
      {
        $questions[$key] =  $this->_build_questions_data($question, $options);
      }
    }

    return $questions;
  }

  /**
  * Get all questions beetween two questions
  *
  * @param int   $question_id_from
  * @param int   $question_id_to
  * @param array $options
  *
  * @return array
  */
  public function get_questions_between($question_id_from, $question_id_to = null, array $options = array())
  {
    $question_from  = $this->get_question($question_id_from);
    $question_to    = $question_id_to > 0 ? $this->get_question($question_id_to) : null;
    $questions      = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_questions.' WHERE ID = '. $question_from['ID'].' AND position > '.$question_from['position'] .' '.( $question_to > 0 ? ' AND position < '.$question_to['position'] : '' ).' ORDER BY position ASC', ARRAY_A);

    if($questions)
    {
      foreach($questions as $key => $question)
      {
        $questions[$key] =  $this->_build_questions_data($question, $options);
      }
    }

    return $questions;
  }

  public function get_logic_conditions_questions($ID = null, array $options = array())
  {
    $ID = $ID ? $ID : $this->current_post_id;

    $questions = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_questions.' WHERE ID = '. $ID.' AND type IN("'.self::PS_QUESTION_TYPE_MULTIPLE_CHOICE.'","'.self::PS_QUESTION_TYPE_SINGLE_CHOICE.'") ORDER BY position ASC', ARRAY_A);

    if($questions)
    {
      foreach($questions as $key => $question)
      {
        $questions[$key] =  $this->_build_questions_data($question, $options);
      }
    }

    return $questions;
  }

  public function get_logic_condition_by_question($question_id, array $options = array())
  {
    $logic_conditions = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_logic_conditions.' WHERE question_id = '. $question_id.' ORDER BY position ASC', ARRAY_A);

    if($logic_conditions)
    {
      foreach($logic_conditions as $key => $logic_condition)
      {
        $logic_conditions[$key] =  $this->_build_logic_condition_data($logic_condition, $options);
      }
    }

    return $logic_conditions;
  }

  /**
  * Get question answers
  *
  * @param int   $question_id    question id
  * @param array $options        options
  *
  * @return array
  */
  public function get_answers($question_id, array $options = array())
  {
    $answers = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_answers.' WHERE question_id = '. $question_id .' ORDER BY position ASC', ARRAY_A);

    if($answers)
    {
      foreach($answers as $key => $answer)
      {
        $answers[$key] =  $this->_build_answers_data($answer);
      }
    }

    return $answers;
  }

  /**
  * Get question answers values
  *
  * @param int   $question_id    question id
  * @param array $options        options
  *
  * @return array
  */
  public function get_answers_values($question_id, array $options = array())
  {
    $answers_values = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_answers_values.' WHERE question_id = '. $question_id .' ORDER BY position ASC', ARRAY_A);

    return $answers_values;
  }


  public function get_logic_condition($logic_condition_id)
  {
    $question =  $this->wpdb->get_row('SELECT * FROM ' . $this->sql_table_logic_conditions.' WHERE logic_condition_id = '. $logic_condition_id, ARRAY_A);

    return  $this->_build_logic_condition_data($question);
  }

  /**
  * Get survey logic conditions
  *
  * @param int   $question_id    question id
  * @param array $options        options
  *
  * @return array
  */
  public function get_logic_conditions($ID = null, array $options = array())
  {
    $ID = $ID ? $ID : $this->current_post_id;
    $logic_conditions = $this->wpdb->get_results('SELECT * FROM ' . $this->sql_table_logic_conditions.' WHERE ID = '. $ID .' ORDER BY position ASC', ARRAY_A);

    if($logic_conditions)
    {
      foreach($logic_conditions as $key => $logic_condition)
      {
        $logic_conditions[$key] =  $this->_build_logic_condition_data($logic_condition);
      }
    }

    return $logic_conditions;
  }

  /**
  * Get question data answered by current session / user
  *
  * @param int    $question_id
  * @param string $session_id
  * @param int    $user_id
  *
  * @return array
  */
  public function get_question_data($question_id, $answer_id = null, $my = true, $session_id = null, $user_id = null, $cookie_id = null)
  {
    $where = '1=1';

    if($my)
    {
      $session_id = $session_id ? $session_id : $this->current_session_id;
      $user_id    = $user_id    ? $user_id    : $this->current_user_id;
      $cookie_id  = $cookie_id  ? $cookie_id  : prsv_get_cookie_id();
    }

    if($answer_id > 0)
    {
      $where.=" AND answer_id=".$answer_id;
    }

    if($user_id > 0 && (!$session_id || !$cookie_id))
    {
      $where.= ' AND user_id='.$user_id;
    }
    
    if(!empty($cookie_id))
    {
      $where.= ' AND cookie_id = "'.$cookie_id.'"';
    }
    else if(!empty($session_id))
    {
      $where.= ' AND session_id = "'.$session_id.'"';
    } 

    $sql = 'SELECT * FROM ' . $this->sql_table_data.' WHERE question_id = '. $question_id .' AND '.$where;

    return $this->wpdb->get_results($sql, ARRAY_A);
  }

  /**
  * Get all survey data by post ID
  *
  * @param int $ID
  * @param int $limit
  * @param int $offset
  *
  * @return array
  */
  public function get_survey_data($ID,array $filters = array(),  $limit = 1000,$offset = 0)
  {
    $sql = "SELECT q.question_id as id, q.text \"question_text\", a.text \"answer_text\", COALESCE(d.value,av.value) \"value\", d.ip, d.session_id, d.user_id, DATE_FORMAT(d.creation_datetime,'%Y-%m-%d %H:%i') as \"date\" FROM ".$this->sql_table_questions." q ";
    $sql.= "INNER JOIN ".$this->sql_table_data." d ON(d.question_id = q.question_id) ";
    $sql.= "LEFT JOIN ".$this->sql_table_answers." a ON(a.answer_id = d.answer_id) ";
    $sql.= "LEFT JOIN ".$this->sql_table_answers_values." av ON(av.answer_value_id = d.answer_value_id) ";
    $sql.= "WHERE q.ID = ".$ID." ";

    if($filters)
    {
      foreach($filters as $field => $value)
      {
        $sql.=" AND ".$field." = \"".$value."\"";
      }
    }

    $sql.= "ORDER BY q.question_id ASC LIMIT ".$offset.",".$limit;

    return $this->wpdb->get_results($sql, ARRAY_A);
  }

  /**
  * Get all survey data by post ID
  *
  * @param int $ID
  * @param int $limit
  * @param int $offset
  *
  * @return array
  */
  public function get_survey_data_groupped($ID, array $filters = array())
  {
    $sql = "SELECT q.question_id as id, q.text \"question_text\", a.text \"answer_text\", COALESCE(d.value,av.value) \"value\", d.ip, d.user_id, d.session_id, COALESCE(IF(d.user_id >0,u.display_name,d.user_id), d.session_id) as username, DATE_FORMAT(d.creation_datetime,'%Y-%m-%d %H:%i') as \"date\" FROM ".$this->sql_table_questions." q ";
    $sql.= "INNER JOIN ".$this->sql_table_data." d ON(d.question_id = q.question_id) ";
    $sql.= "LEFT JOIN ".$this->sql_table_answers." a ON(a.answer_id = d.answer_id) ";
    $sql.= "LEFT JOIN ".$this->sql_table_users." u ON(u.ID = d.user_id) ";
    $sql.= "LEFT JOIN ".$this->sql_table_answers_values." av ON(av.answer_value_id = d.answer_value_id) ";
    $sql.= "WHERE q.ID = ".$ID." ";

    if($filters)
    {
      foreach($filters as $field => $value)
      {
        $sql.=" AND ".$field." = \"".$value."\"";
      }
    }

    $sql.= " GROUP BY username, q.ID ORDER BY d.creation_datetime ASC";

    return $this->wpdb->get_results($sql, ARRAY_A);
  }

  /**
  * Count questions
  *
  * @param int    $ID          post type ID
  * @param bool   $answered    check if answered, default false
  * @param string $session_id  session id
  * @param int    $user_id     user id
  *
  * @return int
  */
  public function count_questions($ID, $answered = false, $session_id = null, $user_id = null)
  {
    $where = '1=1';

    if($answered)
    {
      $session_id = $session_id ? $session_id : $this->current_session_id;
      $user_id    = $user_id    ? $user_id    : $this->current_user_id;
      $subwhere   = [];

      if($user_id > 0)
      {
        $subwhere[] = 'user_id='.$user_id;
      }

      if(!empty($session_id))
      {
        $subwhere[] = 'session_id = "'.$session_id.'"';
      }

      $where = 'q.question_id IN(SELECT DISTINCT question_id FROM '.$this->sql_table_data.' WHERE '.implode(' AND ',$subwhere).')';
    }

    $result = $this->wpdb->get_row('SELECT COALESCE(COUNT(q.question_id),0) as tot FROM ' . $this->sql_table_questions.' q WHERE ID = '. $ID .' AND '.$where, ARRAY_A);

    return $result['tot'];
  }


  public function count_total_response($published = true)
  {
    $where = '';

    if($published)
    {
      $where.=" AND p.post_status = 'publish'";
    }

    $result = $this->wpdb->get_row('SELECT COALESCE(COUNT(d.id),0) as tot FROM ' . $this->sql_table_data.' d '.
    'INNER JOIN '.$this->sql_table_questions.' q ON(q.question_id = d.question_id) '.
    'INNER JOIN '.$this->get_table_name('posts').' p ON(p.ID = q.ID) WHERE 1 '.$where, ARRAY_A);
    return $result['tot'];
  }

  public function count_total_surveys($published = true)
  {
    $where = '';

    if($published)
    {
      $where.=" AND p.post_status = 'publish'";
    }

    $result = $this->wpdb->get_row('SELECT COUNT(ID) as tot FROM '.$this->get_table_name('posts').' p WHERE p.post_type="'.PRSV_POST_TYPE.'" '.$where, ARRAY_A);
    return $result['tot'];
  }

  public function count_total_questions($published = true)
  {
    $where = '';

    if($published)
    {
      $where.=" AND p.post_status = 'publish'";
    }

    $result = $this->wpdb->get_row('SELECT COALESCE(COUNT(question_id),0) as tot FROM ' . $this->sql_table_questions.' q '.
    'INNER JOIN  '.$this->get_table_name('posts').' p ON(p.ID = q.ID) WHERE 1 '.$where, ARRAY_A);
    return $result['tot'];
  }

  public function count_answers($answer_id, $filters = array())
  {
    $where  = '';

    if($filters)
    {
      foreach($filters as $field => $value)
      {
        if($value){
          $where.=" AND d.".$field."='".$value."'";
        }
      }
    }

    $result = $this->wpdb->get_row('SELECT COALESCE(COUNT(d.answer_id),0) as tot FROM ' . $this->sql_table_data.' d WHERE answer_id = '. $answer_id.' '.$where, ARRAY_A);
    return $result['tot'];
  }

  public function count_answers_values($answer_value_id, $answer_id = null, $filters = array())
  {
    $where  = $answer_id ? 'AND answer_id = '.$answer_id : '';

    if($filters)
    {
      foreach($filters as $field => $value)
      {
        if($value){
          $where.=" AND d.".$field."='".$value."'";
        }
      }
    }

    $result = $this->wpdb->get_row('SELECT COALESCE(COUNT(d.answer_value_id),0) as tot FROM ' . $this->sql_table_data.' d WHERE answer_value_id = '. $answer_value_id.' '.$where, ARRAY_A);
    return $result['tot'];
  }

  /**
  * Registra la risposta ad una domanda per un utente / sessione
  *
  * @param array $data dati
  *
  * @return int|boolean
  */
  public function add_question_data(array $data, $session_id = null, $user_id = null, $cookie_id = null)
  {
    $session_id = $session_id ? $session_id : $this->current_session_id;
    $user_id    = $user_id    ? $user_id    : $this->current_user_id;

    $cookie_id = prsv_get_cookie_id() ? prsv_get_cookie_id() : prsv_set_cookie_id();

    $data['user_id']    = $user_id ? $user_id : null;
    $data['session_id'] = $session_id;
    $data['cookie_id']  = $cookie_id ? $cookie_id : null;
    $question = $this->get_question($data['question_id']);

    $question_data = $this->get_question_data($data['question_id'],isset($data['answer_id']) ? $data['answer_id'] : null,null, $session_id, $user_id,$cookie_id);

    if($question_data)
    {
      return false;
    }

    $anonymize = strstr(prsv_post_meta_get('anonymize_ip', '', $question['ID']), 'anonymize_ip_on') !== false;

    $data['creation_datetime'] = date('Y-m-d H:i:s');
    $data['ip']                = prsv_get_ip($anonymize);

    $data = $this->filter_data_by_table($this->sql_table_data,$data);
    $res = $this->wpdb->insert($this->sql_table_data, $data);
    $question_id = $res ? $this->wpdb->insert_id : null;

    return $res;
  }

  public function update_activation_data(array $data)
  {
    $data['version'] = PRSV_PLUGIN_VERSION;
    return $this->wpdb->update($this->sql_table_survey,$this->filter_data_by_table($this->sql_table_survey, $data), array('version' => PRSV_PLUGIN_VERSION));
  }

  protected function _build_questions_data($question, array $options = array())
  {
    if($question)
    {
      $question['image_properties'] = !empty($question['image_properties']) ? json_decode($question['image_properties'],true) : null;
      $question['question_type']    = $this->get_question_type($question['type']);
      $question['answers']          = array();

      if(!empty($options['answers']))
      {
        $question['answers']        = $this->get_answers($question['question_id']);
        $question['answers_values'] = $this->get_answers_values($question['question_id']);
      }
    }

    return $question;
  }

  protected function _build_answers_data($answer)
  {
    if($answer)
    {
      $answer['image_properties'] = !empty($answer['image_properties']) ? json_decode($answer['image_properties'],true) : null;
    }

    return $answer;
  }


  protected function _build_logic_condition_data($logic_condition)
  {
    return $logic_condition;
  }


  protected function _build_post_type_image(array $post)
  {
    $post['ID']       = null;
    $attachment_id    = media_sideload_image($post['guid'], null, $post['post_content'], 'id');

    return $attachment_id;
  }
}
