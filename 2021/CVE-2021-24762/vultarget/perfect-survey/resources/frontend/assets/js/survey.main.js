(function($){

  "use strict";

  $(document).ready(function($){

    // Add limit to textarea counter
    $("textarea.survey_textarea").keyup(function(){
      var lenght = $(this).val().length;
      $(this).next().text((1000 - lenght) + " / 1000");
    });

    $("body").on("click",".survey-rating",function(e){
      $(this).closest('li').prevAll('li').find('.survey-rating').addClass('selected');
      $(this).closest('li').nextAll('li').find('.survey-rating').removeClass('selected');
      $(this).addClass('selected');
    });

    $("body").on("click",".ps-select-answer option", function(e){

      var $select = $(this).closest('select');

      if($select.attr("multiple"))
      {
        if($(this).val().length > 0)
        {
          $select.find("option").filter(function(){ return !$(this).val(); }).prop('selected',false);
        }
        else if(!$(this).val())
        {
          $select.find("option").prop('selected',false);
          $(this).prop('selected',true);
        }
      }
    });

    $("body").on("click",".survey_submit_btn", function(e){

      e.preventDefault();

      var $survey  = $(this).closest(".survey-container");
      var errors   = ps_validate_survey($survey);
      var metadata = $survey.data('metadata');

      if(errors.length > 0)
      {
            swal({
              title: "Ops!",
              text: "• "+errors.join('\n • '),
            });
            return false;
      }

      var submit_mode = metadata['ps_question_submit_complete'];

      var postDataArray = $survey.find(".survey_question_box:visible *").serializeArray();

      $survey.find(".survey_question_container:not(.hidden_ps_questions_com) .survey_question_box").each(function(){
        if(!$(this).data("validationRequired"))
        {
          if($(this).find("*").serializeArray().length == 0)
          {
            postDataArray.push({
              name: 'ps_questions['+$(this).data('questionId')+']',
              value: 0
            });
          }
        }
      });

      var postData      = ps_form_array_to_object(postDataArray);
      postData['ID']    = $survey.data('id');

      ps_ajax_post("save_question_data",postData, function(response){

        var partial_message          = 'ps_success_message_one';
        var complete_message         = 'ps_success_message_complete';
        var set_complete_messages    = 'ps_success_message_complete_on';
        var set_one_messages         = 'ps_success_message_one_on';

        if(typeof metadata[complete_message] != 'undefined' && response.total_questions_answered == response.total_questions)
        {
          if(typeof metadata[complete_message] == 'string' && metadata[complete_message].length > 0 && metadata[set_complete_messages] == 'message_complete_on')
          {
                swal({
                  text: metadata[complete_message],
                }).then(confirm);
          }
          else
          {
               confirm();
          }

          function confirm()
          {
                if(typeof metadata['ps_success_page_link'] != 'undefined' && metadata.ps_success_page_link.length > 0)
                {
                  window.location.href=metadata.ps_success_page_link;
                }
                else
                {
                  typeof metadata['ps_question_submit_type'] == 'undefined' ||  metadata['ps_question_submit_type'] != 'ajax'  ? window.location.reload() : ps_survey_reload($survey);
                }
          }
        }

        if(typeof metadata[partial_message] != 'undefined' && response.total_questions_answered < response.total_questions)
        {
            if(typeof metadata[partial_message] == 'string' && metadata[partial_message].length > 0 && metadata[set_one_messages] == 'message_one_on')
            {
              swal({

                text: metadata[partial_message],

              }).then(confirm);
            }
            else
            {
                confirm();
            }

            function confirm()
            {
                typeof metadata['ps_question_submit_type'] == 'undefined' ||  metadata['ps_question_submit_type'] != 'ajax' ? window.location.reload() : ps_survey_reload($survey);
            }
        }
      });
    });

    $("body").on('mouseenter','.ps-datepicker', function(){
      if(typeof $.fn.datepicker == 'function' && typeof $(this).data('datepicker') == 'undefined')
      {
        $(this).datepicker({
          beforeShow: function() {
            $(this).datepicker("widget").addClass("ps_ui_customize_survey");
          },
          changeMonth: true,
          changeYear: true,
          yearRange: '-100:+100',
        });
      }
    });

    $("body").on('click', '.ps_sfe_toglger', function(e) {
      $(this).toggleClass('ps_sfe_toglger_opener');
      $(this).next().toggle();
    })
  });

})(jQuery);
