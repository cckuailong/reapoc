
/**
* Fetch form questions data
*
* @param int question_id   question_id
*
* @returns object
*/
function ps_question_form_data(question_id)
{
  var querySelector       = '#survey_question_'+question_id+' *';
  var questionsFormFields = jQuery(querySelector).serializeArray();
  var formData            =  ps_form_array_to_object(questionsFormFields);

  formData['question_id'] = question_id;
  return formData;
}

/**
* Fetch form answers data
*
* @param int answer_id   answer id
*
* @returns object
*/
function ps_answer_form_data(answer_id)
{
  var querySelector       = "[name*='ps_answers["+answer_id+"]']";
  var answersFormFields   = jQuery(querySelector).serializeArray();
  var formData            =  ps_form_array_to_object(answersFormFields);

  formData['answer_id'] = answer_id;
  return formData;
}

/**
* Fetch logic condition data
*
* @param int logic_condition_id logic_condition_id
*
* @returns object
*/
function ps_logic_condition_form_data(logic_condition_id)
{
  var querySelector            = '#survey_logic_condition_'+logic_condition_id+' *';
  var logicConditionFormFields = jQuery(querySelector).serializeArray();
  var formData                 =  ps_form_array_to_object(logicConditionFormFields);

  formData['logic_condition_id'] = logic_condition_id;
  return formData;
}

/**
* Transform form data array as form data object
*
* @param {array} formArrayData
*
* @returns {object}
*/
function ps_form_array_to_object(formArrayData)
{
  var formData = {};
  for (var i = 0; i < formArrayData.length; i++){
    formData[formArrayData[i]['name']] = formArrayData[i]['value'];
  }
  return formData;
}

/**
* Send ajax request GET to PsSurveyPostTypeAjax
*
* @param string action
* @param object data
* @param function onSuccess
* @param function onError
*
* @returns {jqXHR}
*/
function ps_ajax_get(action, data, onSuccess, onError)
{
  var args = (arguments.length === 1 ? [arguments[0]] : Array.apply(null, arguments));
  args.unshift('GET');
  return ps_ajax.apply(this,args);
}

/**
* Send ajax request POST to PsSurveyPostTypeAjax
*
* @param string action
* @param object data
* @param function onSuccess
* @param function onError
*
* @returns {jqXHR}
*/
function ps_ajax_post(action, data,  onSuccess, onError)
{
  var args = (arguments.length === 1 ? [arguments[0]] : Array.apply(null, arguments));
  args.unshift('POST');
  return ps_ajax.apply(this, args);
}


/**
* Send ajax request to PsSurveyPostTypeAjax
*
* @param string method
* @param string action
* @param object data
* @param function onSuccess
* @param function onError
*
* @returns {jqXHR}
*/
function ps_ajax(method, action, data, onSuccess, onError)
{
  var method    = typeof method    == 'undefined' ? 'GET'     : method;
  var onSuccess = typeof onSuccess == 'function'  ? onSuccess : function(data){};
  var onError   = typeof onError   == 'function'  ? onError   : function(data){};
  var data      = typeof data      == 'undefined' ? {}        : data;

  data['action'] = action;

  //console.log('ps ajax ['+method+'] url: '+ajaxurl+' data: ',data);

  return jQuery.ajax({
    url: ajaxurl,
    method: method,
    data: data,
    success: function(response){
      //console.log('ps ajax response success: ',response);
      onSuccess(response);
    },
    error: function(response){
      //console.log('ps ajax response error: ',response);
      onError(response);
    },
    dataType: 'json'
  });
}
