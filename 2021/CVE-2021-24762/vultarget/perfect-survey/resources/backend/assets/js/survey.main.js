(function(w,$){

  "use strict";

  $(function(){

    $("body").on('click', '.psrv_tabs_nav li', function() {
      var $tabsClicked = $(this).attr('id'),
          $allTabs = $('.psrv_pager');
          $allTabs.hide();
          $('.'+$tabsClicked).show();
          $('.psrv_tabs_nav li').removeClass('psrv_active_tab_clicked');
          $(this).addClass('psrv_active_tab_clicked');
    });

    $("body").on('click','.ps-meta-restore',function(e){
      e.preventDefault();
      var meta = $(this).data('meta');
      var id   = $(this).data('id') ? $(this).data('id') : w.wp_post.ID;
      ps_ajax_post('restore_meta',{ ID: id,  meta: meta }, function(){
        w.location.reload();
      });
    });

    $('body').on('click','.ps_open_settings',function () {
      $('.ps_settings-modal').hide();
      $('.ps_modal_back').fadeIn();
      $(this).next().fadeIn();
    });

    $('input.survey-question-text.question-validate').blur(function(){
      if(!$(this).val()){
        $(this).addClass("warning");
      } else{
        $(this).removeClass("warning");
      }
    });

    $('body').on('click','.ps_settings_header,.ps_submit_modal, ps_submit_modal input',function (e) {
      e.preventDefault();
      $(this).parent().fadeOut();
      $('.ps_modal_back').fadeOut();
    });

    $('body').on('click','#survey_open_modal',function () {
      $('.survey-select-type').toggleClass('psrv_show_show');
      return false;
    });

    $('body').on('click','.survey-accordionize',function () {
      $(this).next().toggle();
    });

    $('body').on('mouseenter','.survey_sortable-item',function () {
      if(!$(this).data('uiSortable'))
      {
        $(this).sortable({
          placeholder: "survey-placeholder-ui",
          handle: '.survey_move_header',
          forcePlaceholderSize: true,
          stop: function (e, ui) {
            $(ui.item).closest('.survey_question_box').find('textarea').each(function () {
              tinymce.execCommand('mceRemoveEditor', false, $(this).attr('id'));
              tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
            });
          },
          update: function (event, ui) {
            $(this).find(".question-position-field").each(function (i) {
              $(this).val(parseInt(i) + 1);
            });
            $(this).find(".logic-condition-position-field").each(function (i) {
              $(this).val(parseInt(i) + 1);
            });
          }
        });
      }
    });


    var $psQuestionsBox         = $('#post-questions-box');
    var $psLogicConditionBox    = $("#logic-conditions-container");

    $('body').on('mouseenter','.sortable',function () {

      // Return a helper with preserved width of cells
      var fixHelper = function (e, ui) {
        ui.children().each(function () {
          $(this).width($(this).width());
        });
        return ui;
      };

      if(!$(this).data('uiSortable'))
      {
        $(this).sortable({
          placeholder: "survey-placeholder-ui",
          handle: '.survey_move_element',
          helper: fixHelper,
          start: function (e, ui) {
            ui.placeholder.height(ui.item.height());
          },
          update: function (event, ui) {
            $(this).find("input[type=hidden][name*=position]").each(function (i) {
              $(this).val(parseInt(i) + 1);
            });
          },
          forcePlaceholderSize: true

        }).disableSelection();
      }
    });


    $('.color-field').each(function () {
      $(this).wpColorPicker({
        palettes: false
      });
    });

    $('.rotate-cw').click(function () {
      $('.image-editor').cropit('rotateCW');
    });

    $('.rotate-ccw').click(function () {
      $('.image-editor').cropit('rotateCCW');
    });

    $('.export').click(function () {
      var imageData = $('.image-editor').cropit('export');
      w.open(imageData);
    });

    $('.survey-btn-add').each(function () {
      var $btn = $(this);

      $btn.click(function (e) {

        e.preventDefault();

        ps_ajax_post('add_question', {type: $btn.data('psQuestionType'), ID: w.wp_post.ID}, function (response) {
          $psQuestionsBox.append($(response.html));
          $psQuestionsBox.find(".survey_question_header:last").trigger("click");
          $("#survey-empty-questions-box").addClass('hidden');
        });

        $('.psrv_toast_add_question').addClass('psrv_toast_add_question_show');

        setTimeout(function() {
          $('.psrv_toast_add_question').removeClass('psrv_toast_add_question_show');
        }, 1500)

      });
    });

    $('body').on('click', '.btn-delete-answer, .btn-delete-answer-value', function (e) {

      e.preventDefault();
      e.stopImmediatePropagation();

      var $btn = $(this);
      var answer_id = $(this).data('answerId');
      var answer_value_id = $(this).data('answerValueId');
      var confirm_text = $(this).data('confirmText');
      var action = $(this).data('action');

      if (!confirm_text || w.confirm(confirm_text))
      {
        ps_ajax_post(action, {answer_value_id: answer_value_id, answer_id: answer_id}, function (response) {

          var totalAnswers = $btn.closest('table').find("tbody .survey_answer_box").length;
          var multipleAnswers = $btn.data('multipleAnswers');

          if (!multipleAnswers)
          {
            switch (action)
            {
              case 'delete_answer':
              $btn.closest('.survey_question_body').find(".btn-add-answer").show();
              break;
              case 'delete_answer_value':
              $btn.closest('.survey_question_body').find(".btn-add-answer-value").show();
              break;
            }
          }

          $btn.closest('tr').remove();
        });
      }
    });

    $('body').on('click', '.btn-add-answer, .btn-add-answer-value', function (e) {

      e.preventDefault();
      e.stopImmediatePropagation();

      var $btn = $(this);
      var question_id = $btn.data('questionId');
      var action = $btn.data('action');

      var totalAnswers = $btn.closest('table').find("tbody .survey_answer_box").length;
      var multipleAnswers = $btn.data('multipleAnswers');

      if (totalAnswers == 0 || multipleAnswers)
      {
        var questionData = action == 'add_answer' ? {} : ps_question_form_data(question_id);
        questionData['question_id'] = question_id;

        ps_ajax_post(action, questionData, function (response) {
          $btn.closest('table').find("tbody").append($(response.html));
          if (!multipleAnswers)
          {
            $btn.hide();
          }
        });
      }
      else if (!multipleAnswers)
      {
        $btn.hide();
      }
    });

    $('body').on('click', '.survey_question_header .btn-delete-question', function (e) {

      e.preventDefault();
      e.stopImmediatePropagation();
      e.stopPropagation();

      var $btn = $(this);
      var question_id = $btn.data('questionId');
      var confirm_text = $btn.data('confirmText');

      if (!confirm_text || w.confirm(confirm_text))
      {
        ps_ajax_post('delete_question', {question_id: question_id}, function (response) {
          $btn.closest('.survey_question_box').remove();
          if($(".survey_question_box").length == 0)
          {
            $("#survey-empty-questions-box").removeClass('hidden');
          }
        });
      }

      return false;
    });

    $('body').on('click', '.btn-copy-survey', function (e) {

      e.preventDefault();
      e.stopImmediatePropagation();
      e.stopPropagation();

      var $btn = $(this);
      var ID = $btn.data('id');
      var confirm_text = $btn.data('confirmText');

      if (!confirm_text || w.confirm(confirm_text))
      {
        ps_ajax_post('copy_survey', {ID: ID}, function (response) {
          alert(response.message)
          if(response.ID)
          {
            window.location.href=response.href;
          }
        });
      }

      return false;
    });

    $('body').on('click', '.survey_question_header .btn-copy-question', function (e) {

      e.preventDefault();
      e.stopImmediatePropagation();
      e.stopPropagation();

      var $btn = $(this);
      var question_id = $btn.data('questionId');
      var confirm_text = $btn.data('confirmText');

      if (!confirm_text || w.confirm(confirm_text))
      {
        ps_ajax_post('copy_question', {question_id: question_id}, function (response) {
          if(response.question_id > 0)
          {
            $psQuestionsBox.append($(response.html));
          }
          //                    if($(".survey_question_box").length == 0)
          //                    {
          //                        $("#survey-empty-questions-box").removeClass('hidden');
          //                    }
        });
      }

      return false;
    });



    $("body").on('change keyup', '.survey_optional_settings input, .survey_optional_settings select', function (e) {

      var $elm    = $(this);
      var timeout = $elm.data('psAjaxTimeout');

      if(timeout){
        window.clearTimeout(timeout);
      }

      timeout = window.setTimeout(function(){
        var $questionBox = $elm.closest('.survey_question_box');
        var question_id = $questionBox.data('questionId');

        var questionData = ps_question_form_data(question_id);
        ps_ajax_post('edit_question_box', questionData, function (response) {
          $questionBox.find('.survey_question_body').html($(response.html).find('.survey_question_body').html());
          if($('.ps_modal_back:visible').length > 0)
          {
            $questionBox.find('.ps_settings-modal').show();
          }
        });
      },2000);

      $elm.data('psAjaxTimeout',timeout);
    });


    $('body').on('click', '.survey_question_header', function (e) {
      if (!e.isPropagationStopped())
      {
        var $btn = $(this);
        $btn.toggleClass('survey_active_class');
        $btn.next().slideToggle('fast', function(){
          $(this).trigger($btn.is('.survey_active_class') ? "open" : "close");
        });
      }
    });

    $("body").on('keyup', 'input[type=text].survey-question-text', function (e) {
      var $surveyQuestionP = $(this).closest('.survey_question_box').find('.survey_question_p');
      $surveyQuestionP.text($(this).val());
    });

    var wp  = w.wp || undefined;

    if (typeof wp != 'undefined' && wp.media && wp.media.editor)
    {
      $('body').on('click', '.answer-set-image-btn', function (e) {

        e.preventDefault();

        var $btn = $(this);
        var btnId = $btn.attr("id") ?  $btn.attr("id") : 'wp-media-upload-'+(new Date().getTime());
        $btn.attr("id",btnId);
        var $input = $btn.prevAll('.process_custom_images');
        var $inputProperties = $btn.prevAll('.process_custom_images_properties');
        var $inputLoader = $btn.prevAll('.ps_image_loader');
        var $img = $btn.prevAll('img');
        var $imgDiv = $btn.closest('.survey-image-single-block');
        wp.media.editor.send.attachment = function (props, attachment) {

          //console.log('attachment: ',attachment,JSON.stringify(props));

          if ($img.length > 0) {
            $img.attr('src', attachment.url).show();
          }

          if ($imgDiv.length > 0) {
            $imgDiv.css('background-image', 'url(' + attachment.url + ')');
          }

          if ($inputProperties.length > 0) {
            $inputProperties.get(0).value = JSON.stringify(props);
          }

          if ($input.length > 0) {
            $input.val(attachment.id).trigger("change");
          }
          $btn.nextAll('.answer-del-image-btn').removeClass('hidden');
          $inputLoader.addClass('hidden');
        };

        wp.media.editor.open(btnId);
        return false;
      });

      $('body').on('click', '.answer-del-image-btn', function (e) {

        e.preventDefault();

        var $btn = $(this);
        var btnId = $btn.attr("id") ?  $btn.attr("id") : 'wp-media-upload-'+(new Date().getTime());
        var $input = $btn.prevAll('.process_custom_images');
        var $inputProperties = $btn.prevAll('.process_custom_images_properties');
        var $inputLoader = $btn.prevAll('.ps_image_loader');
        var $img = $btn.prevAll('img');
        var $imgDiv = $btn.closest('.survey-image-single-block');
        if ($img.length > 0) {
          $img.attr('src','').hide();
        }

        if ($imgDiv.length > 0) {
          $imgDiv.css('background-image', '');
        }

        if ($inputProperties.length > 0) {
          $inputProperties.get(0).value = '';
        }

        if ($input.length > 0) {
          $input.val('').trigger("change");
        }
        $(this).addClass('hidden');
        return false;
      });
    }


    $('body').on('click', '#survey_add_condition', function (e) {
      e.preventDefault();
      ps_ajax_post('add_logic_condition', { ID: w.wp_post.ID }, function (response) {
        $psLogicConditionBox.append($(response.html));
      });
    });

    $('body').on('click', '#logic-conditions-container .btn-delete-logic-condition', function (e) {

      e.preventDefault();
      e.stopImmediatePropagation();
      e.stopPropagation();

      var $btn = $(this);
      var logic_condition_id = $btn.data('logicConditionId');
      var confirm_text       = $btn.data('confirmText');

      if (!confirm_text || w.confirm(confirm_text))
      {
        ps_ajax_post('delete_logic_condition', {logic_condition_id: logic_condition_id}, function (response) {
          $btn.closest('.survey_logic_condition_row').remove();
        });
      }

      return false;
    });

    $("body").on('change', '.survey_conditional_select', function (e) {

      var $elm    = $(this);
      var timeout = $elm.data('psAjaxTimeout');

      if(timeout){
        window.clearTimeout(timeout);
      }

      timeout = window.setTimeout(function(){
        var $logicConditionBox = $elm.closest('.survey_logic_condition_row');
        var logic_condition_id  = $logicConditionBox.data('logicConditionId');

        var questionData = ps_logic_condition_form_data(logic_condition_id);
        ps_ajax_post('edit_logic_condition', questionData, function (response) {
          $logicConditionBox.replaceWith(response.html);
        });

      },1000);

      $elm.data('psAjaxTimeout',timeout);
    });

    $("form[name=post] input[type=submit]").click(function(e){
      $("body").find(".survey_question_body").each(function(){
        if($(this).find("*[required]").filter(function(){ return $(this).val().length == 0; }).length > 0){
          $(this).closest('.survey_question_box').find(".survey_question_header:not(.survey_active_class)").trigger("click");
        }
      });
    });
  });

})(window,jQuery);
