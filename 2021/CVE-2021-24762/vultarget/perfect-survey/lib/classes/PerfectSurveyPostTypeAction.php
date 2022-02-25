<?php

/**
* Class for ajax actions
*/
class PerfectSurveyPostTypeAction extends PerfectSurveyCore
{
  /**
  * Model of PerfectSurvey
  *
  * @var PerfectSurveyPostTypeModel
  */
  protected $post_type_model;

  public function wp_init()
  {
    $this->wp_actions_register('wp_ajax');
    $this->wp_actions_register('wp_ajax_nopriv');

    add_action('save_post', array($this, 'save_post'));

    add_action( 'wp_insert_post',  array($this, 'add_post'));

    add_action( 'delete_post',   array($this, 'delete_post'));

    $this->post_type_model = prsv_get_post_type_model();
  }

  /**
  * Fetch a question
  *
  * @param int $question_id
  */
  public function get_question($question_id = null)
  {
    $question_id = $question_id > 0 ? $question_id : prsv_input_get('question_id',0);

    $question    = $this->post_type_model->get_question($question_id);

    $html = '';

    if($question)
    {
      $question_type =  $this->post_type_model->get_question_type($question['type']);
      $html          =  prsv_resource_render_backend('questions_types/'.$question['type'], array(
        'question'      => $question,
        'question_type' => $question_type
      ));
    }

    return wp_send_json(array('question_id' => $question_id, 'html' => $html), $question ? 200 : 404);
  }

  /**
  * Fetch an answer
  *
  * @param int $answer_id  answer_id
  *
  */
  public function get_answer($answer_id = null)
  {
    $answer_id = $answer_id > 0 ? $answer_id : prsv_input_get('answer_id',0);
    $html      = '';

    $answer    = $this->post_type_model->get_answer($answer_id);

    if($answer)
    {
      $question      = $this->post_type_model->get_question($answer['question_id']);
      $question_type = $this->post_type_model->get_question_type($question['type']);

      $html          = prsv_resource_render_backend('answers_input/'.$question_type['answer_input'], array(
        'question_type' => $question_type,
        'question'      => $question,
        'answer'        => $answer
      ));
    }


    return wp_send_json(array('answer_id' => $answer_id, 'html' => $html), $answer ? 200 : 404);
  }

  /**
  * Fetch an answer value
  *
  * @param int $answer_value_id  answer_value_id
  *
  */
  public function get_answer_value($answer_value_id = null)
  {
    $answer_value_id = $answer_value_id > 0 ? $answer_value_id : prsv_input_get('answer_value_id',0);
    $html      = '';

    $answer_value   = $this->post_type_model->get_answer_value($answer_value_id);

    if($answer_value)
    {
      $question      = $this->post_type_model->get_question($answer_value['question_id']);
      $question_type = $this->post_type_model->get_question_type($question['type']);

      $html          = prsv_resource_render_backend('answers_input/'.$question['type'].'_values', array(
        'answer'        => $answer,
        'answer_value'  => $answer_value,
        'question'      => $question,
        'question_type' => $question_type,
      ));
    }


    return wp_send_json(array('answer_id' => $answer_value_id, 'html' => $html), $answer_value ? 200 : 404);
  }

  /**
  * Fetch Survey Front-end box HTML
  *
  * @param int $ID post id
  *
  */
  public function get_survey()
  {
    $ID   = prsv_input_get('ID');

    $html = prsv_get('post_type')->ps_survey_view(array('id' => $ID));

    return wp_send_json(array('ID' => $ID, 'html' => $html), 200);
  }

  /**
  * Fetch a logic condition
  *
  * @param int $question_id
  */
  public function get_logic_condition($logic_condition_id = null)
  {
    $logic_condition_id = $logic_condition_id > 0 ? $logic_condition_id : prsv_input_get('logic_condition_id',0);

    $logic_condition = $this->post_type_model->get_logic_condition($logic_condition_id);

    $html = prsv_resource_render_backend('wp_ps_metaboxes/logic_condition_item', array(
      'logic_condition'  => $logic_condition
    ));

    return wp_send_json(array('logic_condition_id' => $logic_condition_id, 'html' => $html), $logic_condition ? 200 : 404);
  }

  /**
  * Add a question to post ID
  */
  public function add_question()
  {
    $question_id = $this->post_type_model->add_question(prsv_input_post('ID'), prsv_input_post('type'));

    return $this->get_question($question_id);
  }

  /**
  * Edit question
  *
  * @return array
  */
  public function edit_question_box()
  {
    $question_id = prsv_input_post('question_id',0);
    $questions   = prsv_input_post('ps_questions');

    if(!empty($questions))
    {
      $this->save_questions($questions);
    }

    $answers = prsv_input_post('ps_answers');
    if(!empty($answers))
    {
      $this->save_answers($answers);
    }

    $answers_values = prsv_input_post('ps_answers_values');
    if(!empty($answers_values))
    {
      $this->save_answers_values($answers_values);
    }

    if($question_id)
    {
      return $this->get_question($question_id);
    }

    return wp_send_json(array('response' => TRUE),200);
  }

  /**
  * Edit question
  *
  * @return array
  */
  public function edit_question()
  {
    $question_id = prsv_input_post('question_id');

    if($question_id > 0)
    {
      $this->post_type_model->update_question($question_id, prsv_input_post());
    }

    return $this->get_question($question_id);
  }

  /**
  * Add an answer to a question
  */
  public function add_answer()
  {
    $answer_id = $this->post_type_model->add_answer(prsv_input_post('question_id'), prsv_input_post());

    return $this->get_answer($answer_id);
  }

  /**
  * Add an answer to a question
  */
  public function add_answer_value()
  {
    $answer_value_id = $this->post_type_model->add_answer_value(prsv_input_post('question_id'));

    return $this->get_answer_value($answer_value_id);
  }

  /**
  * Delete an answer
  */
  public function delete_answer()
  {
    $res = $this->post_type_model->delete_answer(prsv_input_post('answer_id'));

    return wp_send_json(array('status' => $res ? 'OK' : 'KO'), $res ? 200 : 404);
  }

  /**
  * Delete an answer value
  */
  public function delete_answer_value()
  {
    $res = $this->post_type_model->delete_answer_value(prsv_input_post('answer_value_id'));

    return wp_send_json(array('status' => $res ? 'OK' : 'KO'), $res ? 200 : 404);
  }

  /**
  * Delete an answer
  */
  public function delete_question()
  {
    $res = $this->post_type_model->delete_question(prsv_input_post('question_id'));

    return wp_send_json(array('status' => $res ? 'OK' : 'KO'), $res ? 200 : 404);
  }

  /**
  * Delete a logic condition
  */
  public function delete_logic_condition()
  {
    $res = $this->post_type_model->delete_logic_condition(prsv_input_post('logic_condition_id'));

    return wp_send_json(array('status' => $res ? 'OK' : 'KO'), $res ? 200 : 404);
  }


  /**
  * Delete an answer
  */
  public function copy_survey()
  {
    $ID      = prsv_input_post('ID');

    $new_ID  = $this->post_type_model->copy_survey($ID);

    $href    = $new_ID ? admin_url('post.php?post='.$new_ID.'&action=edit') : false;
    
    if($new_ID)
    {
        $post_meta  = prsv_post_meta_get_all($ID);
        $this->save_post_meta($new_ID, $post_meta);
    }

    return wp_send_json(array('ID' => $new_ID, 'href' => $href, 'message' => __($new_ID ? 'Survey copied successfull' : 'Cannot copy this survey, please try again', 'perfect-survey')), $new_ID ? 200 : 404);
  }

  /**
  * Delete an answer
  */
  public function copy_question()
  {
    $question_id      = prsv_input_post('question_id');

    $new_question_id  = $this->post_type_model->copy_question($question_id);
    $html             = '';

    if($new_question_id)
    {
      $question    = $this->post_type_model->get_question($new_question_id);
      if($question)
      {
        $question_type =  $this->post_type_model->get_question_type($question['type']);
        $html          =  prsv_resource_render_backend('questions_types/'.$question['type'], array(
          'question'      => $question,
          'question_type' => $question_type
        ));
      }
    }

    return wp_send_json(array('question_id' => $new_question_id, 'html' => $html), $question ? 200 : 404);
  }

  /**
  * Add new post ps type
  *
  * @param int $post_id post id
  *
  * @return boolean
  */
  public function add_post($post_id)
  {
    $post = get_post($post_id);

    if($post->post_type != PRSV_POST_TYPE)
    {
      return false;
    }

    if($post->post_status == 'auto-draft')
    {
      //add defaults post meta
      $this->save_post_meta($post_id, prsv_get('post_type_meta')->get_defaults_post_meta());
    }

    return true;
  }

  public function add_logic_condition()
  {
    $logic_condition_id = $this->post_type_model->add_logic_condition(prsv_input_post('ID'));

    return $this->get_logic_condition($logic_condition_id);
  }

  /**
  * Delete all post data
  *
  * @param type $post_id post ID
  *
  * @return bool
  */
  public function delete_post($post_id)
  {
    $post = get_post($post_id);

    if($post->post_type != PRSV_POST_TYPE)
    {
      return false;
    }

    $questions = $this->post_type_model->get_questions($post_id);

    $this->post_type_model->delete_meta_fields($post_id);

    if($questions)
    {
      foreach($questions as $question)
      {
        $this->post_type_model->delete_question($question['question_id']);

        $this->post_type_model->delete_answers($question['question_id']);

        $this->post_type_model->delete_answers_values($question['question_id']);

        $this->post_type_model->delete_question_data($question['question_id']);
      }
    }

    return true;
  }

  /**
  * Save post options
  *
  * @param int $post_id current post id
  *
  * @return boolean
  */
  public function save_post($post_id)
  {
    if(!$this->check_save_post_type($post_id))
    {
      return false;
    }


    // Save questions

    $questions = prsv_input_post('ps_questions');

    if($questions)
    {
      $this->save_questions($questions);
    }

    // Save answers

    $answers = prsv_input_post('ps_answers');

    if($answers)
    {
      $this->save_answers($answers);
    }

    $answers_values = prsv_input_post('ps_answers_values');

    if($answers_values)
    {
      $this->save_answers_values($answers_values);
    }


    // Save logic conditions

    $logic_conditions = prsv_input_post('ps_logic_conditions');

    if($logic_conditions)
    {
      $this->save_logic_conditions($logic_conditions);
    }

    // Save options

    $post_meta = prsv_input_post('ps_post_meta');

    if($post_meta)
    {
      $this->save_post_meta($post_id,$post_meta);
    }

    return true;
  }

  public function edit_logic_condition($logic_condition_id = null)
  {
    $post_logic_conditions = prsv_input_post('ps_logic_conditions');
    $logic_condition_id    = $logic_condition_id ? $logic_condition_id : prsv_input_post('logic_condition_id');
    $logic_condition_data  = $post_logic_conditions[$logic_condition_id];

    $res = $this->post_type_model->update_logic_condition($logic_condition_id, $logic_condition_data) ? 1 : 0;
    return $this->get_logic_condition($logic_condition_id);
  }


  public function save_logic_conditions(array $logic_conditions)
  {
    $tot_update = 0;

    if(!empty($logic_conditions))
    {
      foreach($logic_conditions as $logic_condition_id => $data)
      {
        $tot_update+= $this->post_type_model->update_logic_condition($logic_condition_id, $data) ? 1 : 0;
      }
    }

    return $tot_update;
  }

  public function save_questions(array $questions)
  {
    $tot_update = 0;

    if(!empty($questions))
    {
      foreach($questions as $question_id => $data)
      {
        if(!isset($data['required'])){
          $data['required'] = 0;
        }

        $tot_update+= $this->post_type_model->update_question($question_id, $data) ? 1 : 0;
      }
    }

    return $tot_update;
  }

  public function save_answers(array $answers)
  {
    $tot_update = 0;

    if(!empty($answers))
    {
      foreach($answers as $answer_id => $data)
      {
        $tot_update+= $this->post_type_model->update_answer($answer_id, $data) ? 1 : 0;
      }
    }

    return $tot_update;
  }

  public function save_answers_values(array $answers_values)
  {
    $tot_update = 0;

    if(!empty($answers_values))
    {
      foreach($answers_values as $answer_value_id => $data)
      {
        $this->post_type_model->update_answer_value($answer_value_id, $data);
      }
    }

    return $tot_update;
  }


  public function save_post_meta($ID, array $post_meta)
  {
    $tot_update = 0;

    foreach($post_meta as $name => $value)
    {
      $meta_value = trim($value);

      $post_meta_config = prsv_get('post_type_meta')->get_all_post_meta_fields();

      if(!prsv_empty_str($meta_value) || (empty($meta_value) && !empty($post_meta_config[$name]['allowempty']))) {
        $tot_update+= update_post_meta($ID, $name, $meta_value ? $meta_value : null) ? 1 : 0;
      } elseif (prsv_empty_str($meta_value)) {
        $tot_update+= delete_post_meta($ID, $name) ? 1 : 0;
      }
    }

    return $tot_update;
  }

  public function restore_meta()
  {
    $meta = prsv_input_post('meta');
    $ID   = prsv_input_post('ID');

    $all_meta  = prsv_get('post_type_meta')->get_all_post_meta_fields();
    $res = delete_post_meta($ID, $meta);
    $res = add_post_meta($ID, $meta, $all_meta[$meta]['input']['value']);

    return wp_send_json(array('response' => $res),200);
  }

  /**
  * Save question user answers
  */
  public function save_question_data()
  {
    $post_questions = prsv_input_post('ps_questions');
    $ID             = prsv_input_post('ID');


    $ps_questions = prsv_get_post_type_model()->get_questions($ID);

    foreach($post_questions as $question_id => $post_data)
    {
      $question = prsv_get_post_type_model()->get_question($question_id);

      if(!empty($post_data))
      {
        switch($question['type'])
        {
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_RATING:
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_SLIDER:
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_MATRIX:
          foreach($post_data as $answer_id => $answer_value_id)
          {
            $this->post_type_model->add_question_data(array(
              'question_id'       => $question_id,
              'answer_id'         => $answer_id,
              'answer_value_id'   => $answer_value_id[0]
            ));
          }
          break;
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_IMAGE:
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_SINGLE_CHOICE:
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_MULTIPLE_CHOICE:
          foreach($post_data as $answer_id)
          {
            $this->post_type_model->add_question_data(array(
              'question_id' => $question_id,
              'answer_id'   => $answer_id,
            ));
          }
          break;
          case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_TEXT:
          foreach($post_data as $answer_id => $value)
          {
            $this->post_type_model->add_question_data(array(
              'question_id' => $question_id,
              'answer_id'   => $answer_id,
              'value' => $value
            ));
          }
          break;
        }
      }
      else
      {
        $this->post_type_model->add_question_data(array(
          'question_id' => $question_id,
          'answer_id'   => null,
          'value'       => null
        ));
      }
    }

    $post_meta = prsv_post_meta_get_all($ID);

    if($post_meta['ps_question_submit_complete'] == 'one')
    {
      $question_id_to = false;

      $post_data           = reset($post_questions);
      $current_question_id = key($post_questions);
      $logic_conditions    = $this->post_type_model->get_logic_condition_by_question($current_question_id);
      $question            = prsv_get_post_type_model()->get_question($current_question_id);

      switch($question['type'])
      {
        case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_IMAGE:
        case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_SINGLE_CHOICE:
        case PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_MULTIPLE_CHOICE:
        foreach($logic_conditions as $logic_condition)
        {
          foreach($post_data as $answer_id)
          {
            switch($logic_condition['type'])
            {
              case PerfectSurveyPostTypeModel::PS_LOGIC_CONDITION_TYPE_EQ:
              if($logic_condition['answer_id'] == $answer_id)
              {
                $question_id_to = $logic_condition['question_id_to'];
              }
              break;
              case PerfectSurveyPostTypeModel::PS_LOGIC_CONDITION_TYPE_DIFF:
              if($logic_condition['answer_id'] != $answer_id)
              {
                $question_id_to = $logic_condition['question_id_to'];
              }
              break;
            }
          }
        }

        if($question_id_to !== false)
        {
          $questions_between = $this->post_type_model->get_questions_between($current_question_id, $question_id_to);

          foreach($questions_between as $key => $question)
          {
            $this->post_type_model->add_question_data(array(
              'question_id' => $question['question_id'],
              'answer_id'   => null,
              'value'       => null
            ));
          }
        }

       

        break;
      }
    }

    $total_questions          = $this->post_type_model->count_questions($ID);
    $total_questions_answered = $this->post_type_model->count_questions($ID, true);
    
    $finish                   = false;

    if($total_questions == $total_questions_answered && $post_meta['ps_multiple_submit'] == 'multiple_submit_on')
    {
       $finish = true;
    }

    return wp_send_json(array('response' => true,'total_questions' => (int) $total_questions,'total_questions_answered' => (int) $total_questions_answered,'finish' => $finish), 200);
  }


  public function save_activation()
  {
    $data = prsv_input_post('ps_activation_data');

    $this->post_type_model->update_activation_data($data);
  }

  public function save_global_setting()
  {
    update_option(PRSV_GLOBAL_OPTION, prsv_input_post());
  }

  /**
  * Export all data into CSV
  */
  public function download_csv()
  {

    if(is_user_logged_in()) {

      $ID      = prsv_input_get('id', 0);
      $filters = prsv_input_get('filters', array());

      if(!$ID)
      {
        wp_die('Survey ID not valid!');
      }

      $survey_data_exists = $this->post_type_model->get_survey_data($ID,$filters, 1, 0);

      if(empty($survey_data_exists))
      {
        wp_die(__('<p style="margin:0 auto; padding: 10px; font-family: sans-serif; max-width: 400px; text-align: center; border: 1px solid #f4f4f4; color: #999">No statistics data available for this survey!</p>'));
      }

      header("Pragma: public", true);
      header("Expires: 0", true);
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0", true);
      header("Cache-Control: private", true);
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename="survey-data-'.date("YmdHi").'.csv";');
      header("Content-Transfer-Encoding: binary", true);

      $f = fopen('php://memory','w');

      $limit  = 1000;
      $offset = 0;

      while($survey_data = $this->post_type_model->get_survey_data($ID,$filters, $limit, $offset))
      {
        if($offset == 0)
        {
          fputcsv($f, array_keys($survey_data[0]), ';', '"');
        }

        foreach ($survey_data as $line) {
          fputcsv($f, array_values($line), ';', '"');
        }
        $offset+=$limit;
      }

      fseek($f,0);
      fpassthru($f);
      exit;

    } else {

      global $wp_query;
      $wp_query->set_404();
      status_header( 404 );
      get_template_part( 404 ); exit();

    }
  }


  public function delete_stats()
  {
    $ID      = prsv_input_get('id', 0);

    $this->post_type_model->delete_all_questions_data($ID);
    wp_redirect(admin_url( '/edit.php?post_type='.PRSV_PLUGIN_CODE.'&id='.$ID.'&page=single_statistic&message=All data deleted successfull' ));
  }
}
