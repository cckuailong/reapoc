(function(w,$){

    "use strinct";

    /**
     * Decode HTML to string
     *
     * @param {string} value
     *
     * @returns {string}
     */
    w.ps_html_decode = function(value)
    {
        return $("<textarea/>").html(value).text();
    }

    /**
     * Encode string to HTML
     *
     * @param {string} value
     *
     * @returns {string}
     */
    w.ps_html_encode = function(value)
    {
        return $('<textarea/>').text(value).html();
    }

    /**
     * Validate survey form
     *
     * @param {$} $surveyContainer
     *
     * @returns {Array|ps_validate_survey.errors}
     */
    w.ps_validate_survey = function($surveyContainer)
    {
        var $questions = $surveyContainer.find(".survey_question_box:visible");
        var errors = [];

        $questions.each(function(i){

           var required = $(this).data('validationRequired');

           if(required)
           {
               var inputs = $(this).find("*").serializeArray();

               var not_checked_inputs = $(this).find('input[type=checkbox]:not(:checked),input[type=radio]:not(:checked)').map(function() {
                                                return {"name": this.name, "value": ''}
                                        }).get();

               if(not_checked_inputs.length > 0)
               {
                    for(var l = 0;l<not_checked_inputs.length;l++)
                    {
                        var found = false;
                        for(var k = 0;k<inputs.length; k++)
                        {
                            if(inputs[k].name == not_checked_inputs[l].name)
                            {
                                found = true;
                            }
                        }

                        if(!found)
                        {
                            inputs.push(not_checked_inputs[l]);
                        }
                    }
               }

               if(inputs.length == 0)
               {
                   errors.push(ps_html_decode($(this).data('validationMessage')));
               }
               else
               {
                   for(var i = 0;i<inputs.length;i++)
                   {
                       if($.trim(inputs[i].value).length == 0)
                       {
                          errors.push(ps_html_decode($(this).data('validationMessage')));
                       }
                   }
               }
           }
        });

        return $.unique(errors);
    }

    /**
     * Transform form data array as form data object
     *
     * @param {array} formArrayData
     *
     * @returns {object}
     */
    w.ps_form_array_to_object = function(formArrayData)
    {
        var formData = {};

        for (var i = 0; i < formArrayData.length; i++)
        {
           var value = formArrayData[i]['value'];

           if(typeof formData[formArrayData[i]['name']] != 'undefined')
           {
               var curr_value = formData[formArrayData[i]['name']];
               if(typeof curr_value != 'object'){
                   curr_value = [curr_value];
               }
               curr_value.push(value);
               value = curr_value;
           }

           formData[formArrayData[i]['name']] = value;
        }
        return formData;
    }

    /**
     * Reload survey box
     *
     * @param {string} value
     *
     * @returns {string}
     */
    w.ps_survey_reload = function($surveyContainer)
    {
        ps_ajax_get('get_survey', { ID:  $surveyContainer.data('id') } ,function(response){
            $surveyContainer.replaceWith(response.html);
        });
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
    w.ps_ajax_get = function(action, data, onSuccess, onError)
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
    w.ps_ajax_post = function(action, data,  onSuccess, onError)
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
    w.ps_ajax = function(method, action, data, onSuccess, onError)
    {
        var method    = typeof method    == 'undefined' ? 'GET'     : method;
        var onSuccess = typeof onSuccess == 'function'  ? onSuccess : function(data){};
        var onError   = typeof onError   == 'function'  ? onError   : function(data){};
        var data      = typeof data      == 'undefined' ? {}        : data;

        data['action'] = action;

        //console.log('ps ajax ['+method+'] url: '+ajaxurl+' data: ',data);

        return $.ajax({
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

})(window,jQuery);
