/**
 * Admin JS functions
 */
(function ($)
{

  $("input[name='ecwd_set_default']").on('change',function(){
    var ecwd_calendar_id = $(this).data("calendar_id");
    console.log(ecwd_calendar_id);
    jQuery.ajax({
      type: 'POST',
      url: ecwd.ajaxurl,
      data: {
        action: 'ecwd_set_default_calendar',
        nonce: ecwd.ajaxnonce,
        id: ecwd_calendar_id
      },
      success: function (response) {

      }
    });
  });


  $("#ecwd_category_color").ecolorpicker();

  $('#ecwd_event_repeat_dont_repeat_radio').click(function ()
  {
    $("#ecwd_repeat_until").hide();
  });


  $("#ecwd_category_color, .ecwd_colour").ecolorpicker({
    displayIndicator: false,
    displayPointer: false,
    transparentColor: true
  });

  $("#ecwd_category_color, .ecwd_colour").on("change.color", function (event, color)
  {
    $(this).css('background-color', color);
  });

  //events custom fields js
  var allHiddens = $('#ecwd_event_repeats_div .hidden'),
    radios = $('.ecwd_event_repeat_event_radio, .ecwd_event_repeat_list_radio');
  var checked_el = $('.ecwd_event_repeat_event_radio:checked,  .ecwd_event_repeat_list_radio:checked');
  show_fields(checked_el);

  $('.ecwd_event_repeat_event_radio').click(function (e)
  {
    show_fields($(this));
  });


  function show_fields(el)
  {
    allHiddens.attr('class', 'hidden');
    if (el.attr('id') != 'ecwd_event_repeat_dont_repeat_radio') {
      $("#ecwd_repeat_until").show();
    }
    else {
      $("#ecwd_repeat_until").hide();
    }
    $('#ecwd_' + el.val()).removeClass('hidden');
    if ($('#ecwd_event_repeat_how_label_' + el.val()).length > 0) {
      $('#ecwd_daily').removeClass('hidden');
      $('#ecwd_event_repeat_how_label_' + el.val()).removeClass('hidden');
    }
    $('#ecwd_repeat_until').removeClass('hidden');
  }

  if ($(".ecwd_event_repeat_choose").prop('checked')) {
    $(".select_to_enable_disable").prop('disabled', true);
  } else {
    $(".ecwd_event_repeat_on_the").prop('disabled', true);
  }

  $("#ecwd_event_repeat_dont_repeat_radio").click(function ()
  {
    $("#ecwd_repeat_until").attr('class', 'hidden');
  });

  $(".ecwd_event_repeat_choose").click(function ()
  {
    $(".ecwd_event_repeat_on_the").prop('disabled', false);
    $(".select_to_enable_disable").prop('disabled', true);
  });

  if ($(".ecwd_event_repeat_list_radio").prop('checked')) {

    $(".ecwd_event_repeat_on_the").prop('disabled', true);
    $(".select_to_enable_disable").prop('disabled', false);
  }
  if ($("#ecwd_event_repeat_year_on_days_2").prop('checked')) {
    $(".ecwd_event_repeat_on_the").prop('disabled', true);
    $(".select_to_enable_disable").prop('disabled', false);
    $(".ecwd_event_year_month").prop('disabled', true);
  }
  $(".ecwd_event_repeat_list_radio").click(function ()
  {
    $(".ecwd_event_repeat_on_the").prop('disabled', true);
    $(".select_to_enable_disable").prop('disabled', false);
  });

  //	on adding event
  $("#ecwd_repeat_event_monthly").click(function ()
  {
    $(".ecwd_event_repeat_choose").prop('checked', true);
    $(".ecwd_event_repeat_on_the").prop('disabled', false);
    $(".select_to_enable_disable").prop('disabled', true);
  });

  //event validations
  if ($("#ecwd_event_meta").length > 0) {
    $("#post").submit(function (e)
    {
      var dateTo = Date.parse($("#ecwd_event_date_to").val().replace("am", " am").replace("pm", " pm").replace("AM", " AM").replace("PM", " PM")),
        dateFrom = Date.parse($("#ecwd_event_date_from").val().replace("am", " am").replace("pm", " pm").replace("AM", " AM").replace("PM", " PM"));
      if (dateFrom == '' || isNaN(dateFrom) || isNaN(dateTo) || dateTo == '') {
        alert('Please set the event dates');
        e.preventDefault();
        return false;
      }
      if (dateFrom && !dateTo) {
        alert('Please set the end date');
        e.preventDefault();
        return false;
      }
      if (dateTo < dateFrom) {
        alert('Date to must be greater or equal to Date from');
        e.preventDefault();
        return false;
      }
      if ($('input[name="ecwd_event_repeat_event"]').length > 0) {
        var repeat = $('input[name="ecwd_event_repeat_event"]:checked').val();
        var until = Date.parse($('#ecwd_event_repeat_until_input').val());
        if (repeat !== 'no_repeat') {
          if (until == '' || isNaN(until)) {
            alert('Please set the repeat until date');
            e.preventDefault();
            return false;
          }
          if (!isNaN(dateFrom) && !isNaN(until) && until <= dateFrom) {
            alert('Repeat until date must be greater than Date from');
            e.preventDefault();
            return false;
          }
        }
      }

      if($("#ecwd_event_venue").val() == 'new'){
        alert('Please save your new venue before publishing the event.');
        e.preventDefault();
        return false;
      }

    });
  }

  //calendar validations, etc
  if ($("#publish").attr('value') == 'Publish') {
    $("#ecwd_calendar_12_hour_time_format_NO").prop('checked', true);
  }

  if ($('#map-canvas').length > 0) {
    loadScript();
  }


  var wordpress_ver = ecwd_admin_params.version, upload_button;

  $(".ecwd_upload_image_button").click(function (event)
  {
    upload_button = $(this);
    var frame;
    if (wordpress_ver >= "3.5") {
      event.preventDefault();
      if (frame) {
        frame.open();
        return;
      }
      frame = wp.media();
      frame.on("select", function ()
      {
        // Grab the selected attachment.
        var attachment = frame.state().get("selection").first();
        frame.close();
        if (upload_button.parent().prev().children().hasClass("tax_list")) {
          upload_button.parent().prev().children().val(attachment.attributes.url);
          upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
        }
        else
          $("#ecwd_taxonomy_image").val(attachment.attributes.url);
      });
      frame.open();
    }
    else {
      tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
      return false;
    }
  });

  $(".ecwd_remove_image_button").click(function ()
  {
    $("#ecwd_taxonomy_image").val("");
    $(this).parent().siblings(".title").children("img").attr("src", "' . Z_IMAGE_PLACEHOLDER . '");
    $(".inline-edit-col :input[name=\'ecwd_taxonomy_image\']").val("");
    return false;
  });

  if (wordpress_ver < "3.5") {
    window.send_to_editor = function (html)
    {
      imgurl = $("img", html).attr("src");
      if (upload_button.parent().prev().children().hasClass("tax_list")) {
        upload_button.parent().prev().children().val(imgurl);
        upload_button.parent().prev().prev().children().attr("src", imgurl);
      }
      else
        $("#ecwd_taxonomy_image").val(imgurl);
      tb_remove();
    }
  }

  $("body").on("click", '.editinline', function ()
  {
    var tax_id = $(this).parents("tr").attr("id").substr(4);
    var thumb = $("#tag-" + tax_id + " .thumb img").attr("src");
    if (thumb != "' . Z_IMAGE_PLACEHOLDER . '") {
      $(".inline-edit-col :input[name=\'ecwd_taxonomy_image\']").val(thumb);
    } else {
      $(".inline-edit-col :input[name=\'ecwd_taxonomy_image\']").val("");
    }
    $(".inline-edit-col .title img").attr("src", thumb);
    return false;
  });
  ////////////Calendar add/remove events/////////////
  $(document).on('click', '.ecwd-events .ecwd-calendar-event-delete', function ()
  {
    if (confirm('Sure?')) {
      var clicked_el = this;
      var element = $(this).closest('.ecwd-calendar-event');
      var event_id = $(element).find('input').val();
      var calendar_id = $('#post_ID').val();
      $.post(ecwd_admin_params.ajaxurl, {
        action: 'manage_calendar_events',
        ecwd_event_id: event_id,
        ecwd_calendar_id: calendar_id,
        ecwd_action: 'delete'
      }).done(function (data)
      {
        res = JSON.parse(data);
        if (res.status == 'ok') {
          if($(".ecwd_events_popup_button").length === 0){
            var data_new_event_url = $(clicked_el).closest(".ecwd-events").data("new_event_url");
            $(".ecwd_events_popup_button").remove();
            $(".ecwd-calendar-event-add").html('<a class="ecwd_events_popup_button" data-new_event_url="'+data_new_event_url+'" href="#ecwd_event_list_popup">Select Events from the list</a><a class="ecwd_events_popup_button" data-new_event_url="\'+data_new_event_url+\'" href="#ecwd_event_list_popup"><span class="add_event_plus">+</span></a>');
            var ecwd_events_popup_button = $('.ecwd_events_popup_button');
            if(ecwd_events_popup_button.length>0){
              ecwd_events_popup_button.magnificPopup({
                type:'inline',
                callbacks: {

                }
              });
            }
          }
          $(clicked_el).removeClass('ecwd-calendar-event-delete');
          $(clicked_el).addClass('ecwd-calendar-event-add');
          $(clicked_el).text('+');
          $(element).find('.ecwd-calendar-event-edit').addClass('hidden');
          $(element).remove().appendTo('.ecwd-excluded-events');
          $(element).find('input').attr('name', 'ecwd-calendar-excluded-event-id[]');
        }
      });
    }
  });

  $(document).on('click', '.ecwd-excluded-events .ecwd-calendar-event-add, #ecwd_add_event_to_calendar .ecwd-calendar-event-add', function ()
  {
    var clicked_el = this;
    var element = $(this).closest('.ecwd-calendar-event');
    var calendar_id = $('#post_ID').val();
    var event_id = $(element).find('input').val();
    $.post(ecwd_admin_params.ajaxurl, {
      action: 'manage_calendar_events',
      ecwd_event_id: event_id,
      ecwd_calendar_id: calendar_id,
      ecwd_action: 'add'

    }).done(function (data)
    {
      res = JSON.parse(data);
      if (res.status == 'ok') {
        $(clicked_el).addClass('ecwd-calendar-event-delete');
        $(clicked_el).removeClass('ecwd-calendar-event-add');
        $(clicked_el).text('x');
        $(element).find('input').addClass('ecwd-calendar-event-id');
        $(element).find('.ecwd-calendar-event-edit').removeClass('hidden');
        $(element).remove().appendTo('.ecwd-events');
      }
    });

  });
  ////////////////////////////////////////////////////////

  ////////////Calendar selectable add events/////////////
  $(document).on('click', '.event_cal_add .event_cal_add_close', function (e)
  {
    $('.event_cal_add').hide();
  });
  $(document).on('mouseup', '.day-with-date', function (e)
  {
    var position = $(this).position();
    $('.event_cal_add').css({"margin": "0 auto", "left": position.left});
  });

  $('body').on('mouseenter', '.ecwd_calendar_container', function ()
  {
    $(this).selectable({
      filter: ".day-with-date",
      start: function ()
      {
        $('.event_cal_add').hide();
        $('#add_event_to_cal').show();
        $('.ecwd_notification, .ecwd_error').empty();
        $('#ecwd_event_name').val('');
      },
      stop: function ()
      {

        var result = $("#select-result").empty();

        var position = $('.ui-selected').last().find('.day-number').position();
        var start_day = parseInt($('.ui-selected').first().find('.day-number').text());
        var end_day = parseInt($('.ui-selected').last().find('.day-number').text());
        var start_date = $('.ui-selected').first().attr('data-date');
        var end_date = $('.ui-selected').last().attr('data-date');
        if (start_day) {
          if (start_day == end_day) {
            $('.ecwd-dates').text(start_date);
            $('#ecwd_event_date_from').val(start_date);
            $('#ecwd_event_date_to').val(start_date);
          }
          if (end_day > start_day) {
            $('.ecwd-dates').text(start_date + ' - ' + end_date);
            $('#ecwd_event_date_from').val(start_date);
            $('#ecwd_event_date_to').val(end_date);

          }
          if (start_day > end_day) {
            $('.ecwd-dates').text(end_date + ' - ' + start_date);
            $('#ecwd_event_date_from').val(end_date);
            $('#ecwd_event_date_to').val(start_date);
          }
          $('.event_cal_add').removeClass('hidden');
          $('.event_cal_add').show();
          setTimeout(function ()
          {
            $('#ecwd_event_name').focus();
          }, 1);
          $('#ecwd-modal-preview').animate({
            scrollTop: $(".event_cal_add").position().top
          }, 1000);
        }
      }
    });
  });
  $(document).on('click', '#add_event_to_cal', function ()
  {
    var start_date = $('#ecwd_event_date_from').val();
    var end_date = $('#ecwd_event_date_to').val();
    var name = $('#ecwd_event_name').val();
    if (name.length > 0) {
      var calendar_id = $('#post_ID').val();
      $.post(ecwd_admin_params.ajaxurl, {
        action: 'add_calendar_event',
        ecwd_calendar_id: calendar_id,
        ecwd_event_name: name,
        ecwd_event_date_from: start_date,
        ecwd_event_date_to: end_date
      }).done(function (data)
      {
        res = JSON.parse(data);
        if (res.status == 'success') {
          $('#add_event_to_cal').hide();
          $('.ecwd_notification').html('Event \'' + name + '\' has been saved.  <a href="?post=' + res.data.event_id + '&action=edit" target="_blank">Edit details</a>');
        }
      });
    } else {
      $('#ecwd_event_name').focus();
      $('.ecwd_error').html(ecwd_admin_translation.enter_event_name);
    }
  });
  //////////////////////////////////////////

  //////////////Theme tabs//////////////////

  if (typeof(localStorage.currentItem) !== "undefined") {
    var current_item = localStorage.currentItem;
    $("#ecwd-tabs > div").css("display", "none");
    $(current_item).css("display", "block");
    $("#ecwd-tabs .ecwd-tabs li").removeClass("ui-state-active");
    $('#ecwd-tabs .ecwd-tabs li a[href="' + current_item + '"]').parent().addClass("ui-state-active");
  } else {
    $('#general').css("display", "block");
    $('#ecwd-tabs .ecwd-tabs li:first-child').addClass("ui-state-active");
  }

  $(".ecwd-tabs li a").each(function (indx, element)
  {
    $(element).click(function ()
    {
      if (typeof(Storage) !== "undefined") {
        localStorage.currentItem = $(element).attr("href");
      }
      $("#ecwd-tabs > div").css("display", "none");
      $(localStorage.currentItem).css("display", "block");
      $('#ecwd-tabs .ecwd-tabs li').removeClass("ui-state-active");
      $(element).parent().addClass("ui-state-active");
    });
  });


/*  $('.ecwd_add_event_to_calendar').ecwd_popup({
    button: $('.ecwd_events_popup_button'),
    title: ecwd_admin_translation.event_list,
    container_class: 'ecwd_add_event_calendar'
  });*/
  $('#ecwd_preview_add_event_popup').ecwd_popup({
    button: $('#ecwd_preview_add_event'),
    title: ecwd_admin_translation.calendar,
    body_class: "ecwd-modal",
    container_class: 'ecwd_preview_calendar'
  });

  if ($("#ecwd-settings-content").length == 1) {
    var color = "rgba(51,51,51,.5)";
    $('.ecwd_disabled_option').each(function ()
    {
      $(this).closest("tr").find("th").css("color", color);

      $(this).closest("td").find("select").prop("disabled", true);
      $(this).closest("td").find("input").prop("disabled", true);

      $(this).closest("td").find("select").attr("name", '');
      $(this).closest("td").find("input").attr("name", '');

      $(this).closest("td").find("label").css("color", color);
      $(this).closest("td").find(".description").css("color", color);
      $(this).closest("td").find(".ecwd_disabled_text").css("color", color);
    });
  }

  var ecwd_venue_meta_box = $("#ecwd_venue_meta");
  if (ecwd_venue_meta_box.length == 0) {
    ecwd_venue_meta_box = $("#ecwd_event_venue_meta .ecwd_event_venue_map_content");
  }

  if (ecwd_venue_meta_box.length > 0) {
    var venue_meta_controller = new ecwd_venue_meta_controller();
    venue_meta_controller.init(ecwd_venue_meta_box);
  }

  if (jQuery('#ecwd_event_venue').length > 0) {
    var ecwd_event_venue_controller = new ecwd_event_venue_controller();
    ecwd_event_venue_controller.init(jQuery('#ecwd_event_venue'));
  }

  function ecwd_event_venue_controller()
  {

    this.$event_venue = null;
    this.$infoTable = null;
    this.$formTable = null;
    this.$addVenueButtonContainer = null;
    this.$editVenueButtonContainer = null;
    this.$mapTable = null;
    this.$location = null;
    this.$latLongContainer = null;
    this.$showMapContainer = null;
    this.$showMapCheckbox = null;
    this.$addVenueSpinner = null;

    this.init = function (event_venue)
    {
      this.$event_venue = event_venue;

      this.$infoTable = jQuery('.ecwd_event_venue_info_content');
      this.$formTable = jQuery('.ecwd_event_venue_form_content');
      this.$mapTable = jQuery('.ecwd_event_venue_map_content');

      this.$latLongContainer = this.$mapTable.find('.ecwd_event_venue_lat_long');
      this.$location = jQuery('#ecwd_event_location');
      this.$showMapContainer = jQuery('.ecwd_venue_show_map_checkbox_container');
      this.$showMapCheckbox = jQuery('#ecwd_venue_show_map');

      this.$editVenueButtonContainer = jQuery('.ecwd_event_venue_edit_link_container');
      this.$addVenueButtonContainer = jQuery('.ecwd_event_venue_add_button_container');

      this.$addVenueSpinner = this.$addVenueButtonContainer.find('.spinner');

      this.onChangeVenue();
      this.addNewVenue();

      this.$event_venue.trigger('change');
    };

    this.addNewVenue = function ()
    {
      var _this = this;

      var newVenueFields = {
        title: _this.$formTable.find('.ecwd_event_venue_title_field'),
        content: _this.$formTable.find('.ecwd_event_venue_content_field'),
        phone: _this.$formTable.find('.ecwd_event_venue_phone_field'),
        website: _this.$formTable.find('.ecwd_event_venue_website_field'),
        showMap: _this.$showMapCheckbox,
        location: _this.$location,
        latLng: _this.$mapTable.find('#ecwd_lat_long'),
        zoom: _this.$mapTable.find('#ecwd_map_zoom'),
      };

      this.$addVenueButtonContainer.on('click', function (e)
      {
        e.preventDefault();

        if (_this.$event_venue.val() !== "new") {
          return false;
        }

        if (newVenueFields.title.val() == "") {
          alert('Venue title is required');
          return false;
        }

        var post_data = {
          ecwd_venue_title: newVenueFields.title.val(),
          ecwd_venue_content: newVenueFields.content.val(),
          post_type: 'ecwd_venue',
          ecwd_event_location: newVenueFields.location.val(),
          ecwd_venue_meta_phone: newVenueFields.phone.val(),
          ecwd_venue_meta_website: newVenueFields.website.val(),
          ecwd_venue_show_map: (newVenueFields.showMap.is(':checked')) ? '1' : 'no',
          ecwd_lat_long: newVenueFields.latLng.val(),
          ecwd_map_zoom: newVenueFields.zoom.val()
        };

        for (var i in newVenueFields) {
          newVenueFields[i].prop('disabled', true);
        }
        _this.$addVenueButtonContainer.prop('disabled', true);
        _this.$latLongContainer.find('input').prop('disabled', true);

        _this.$addVenueSpinner.addClass('is-active');

        var response = add_new_post(post_data);

        ecwd_venues[response.venue_data.id] = response.venue_data;

        if (response.success == true) {
          var option = "<option value='" + response.venue_data.id + "'>" + response.venue_data.post_title + "</option>";
          _this.$event_venue.find('optgroup').prepend(option);
          _this.$event_venue.val(response.venue_data.id);
        } else {
          _this.$event_venue.val('0');
        }
        _this.$event_venue.trigger('change');


        for (var i in newVenueFields) {
          newVenueFields[i].prop('disabled', false);

          if (i == "ecwd_event_location" || i == "ecwd_venue_show_map") {
            continue;
          } else
            if (i == "ecwd_map_zoom") {
              newVenueFields[i].val('17');
            } else {
              newVenueFields[i].val('');
            }

        }
        _this.$addVenueButtonContainer.prop('disabled', false);
        _this.$latLongContainer.find('input').prop('disabled', false);
        _this.$addVenueSpinner.removeClass('is-active');

        jQuery('html, body').animate({
          scrollTop: jQuery("#ecwd_event_venue_meta").offset().top
        }, 1000);

        return false;
      });
    };

    this.onChangeVenue = function ()
    {
      var _this = this;
      this.$event_venue.change(function (e)
      {
        var selectValue = $(this).val();

        if (selectValue == "0") {
          _this.noneVenue();
          return;
        }

        if (selectValue == 'new') {
          _this.newVenueForm();
          return;
        }

        if (parseInt(selectValue) > 0) {

          _this.changeVenueInfo(parseInt(selectValue));

        }


      });
    };

    this.noneVenue = function ()
    {
      this.$infoTable.addClass('ecwd_hidden');
      this.$formTable.addClass('ecwd_hidden');
      this.$editVenueButtonContainer.addClass('ecwd_hidden');
      this.$addVenueButtonContainer.addClass('ecwd_hidden');
      this.$location.addClass('ecwd_hidden');
      this.$mapTable.find('.ecwd_google_map').addClass('ecwd-hide-map');
      this.$mapTable.find('th, td').addClass('ecwd-hide-map-td');
      this.$mapTable.find('.ecwd_venue_meta_decription').addClass('ecwd_hidden');
    };

    this.newVenueForm = function ()
    {

      if (typeof google !== "undefined" && typeof map !== "undefined") {
        var myLatlng = new google.maps.LatLng(parseFloat(40.712784), parseFloat(-74.005941));

        deleteMarkers();
        addMarker(myLatlng, true);

        map.setCenter(myLatlng);
        map.setZoom(17);
        $('#ecwd_map_zoom').val(17);

        this.$latLongContainer.removeClass('ecwd_hidden');
      }

      this.$mapTable.find('.ecwd_venue_meta_decription').removeClass('ecwd_hidden');

      this.$infoTable.addClass('ecwd_hidden');
      this.$editVenueButtonContainer.addClass('ecwd_hidden');

      this.$formTable.removeClass('ecwd_hidden');
      this.$addVenueButtonContainer.removeClass('ecwd_hidden');

      this.$location.removeClass('ecwd_hidden');
      this.$showMapContainer.removeClass('ecwd_hidden');
      this.$mapTable.find('th, td').removeClass('ecwd-hide-map-td');

      this.$showMapCheckbox.prop('checked', false);
      this.$showMapCheckbox.trigger('change');

    };

    this.changeVenueInfo = function (venueID)
    {

      this.$formTable.addClass('ecwd_hidden');
      this.$addVenueButtonContainer.addClass('ecwd_hidden');

      this.$infoTable.removeClass('ecwd_hidden');
      this.$editVenueButtonContainer.removeClass('ecwd_hidden');

      this.$mapTable.find('.ecwd_venue_meta_decription').removeClass('ecwd_hidden');

      var venue = ecwd_venues[venueID];

      this.$infoTable.find('.ecwd_venue_address_info').text((venue.ecwd_venue_location != "") ? venue.ecwd_venue_location : ecwd_admin_translation.none);
      this.$infoTable.find('.ecwd_venue_phone_info').text((venue.ecwd_venue_meta_phone != "") ? venue.ecwd_venue_meta_phone : ecwd_admin_translation.none);
      this.$infoTable.find('.ecwd_venue_website_info').text((venue.ecwd_venue_meta_website != "") ? venue.ecwd_venue_meta_website : ecwd_admin_translation.none);
      this.$editVenueButtonContainer.find('.ecwd_edit_venue_link').attr('href', venue.edit_link);

      this.$location.addClass('ecwd_hidden');
      this.$latLongContainer.addClass('ecwd_hidden');
      this.$showMapContainer.addClass('ecwd_hidden');

      if (venue.ecwd_venue_show_map == '1') {

        if (typeof google !== "undefined" && typeof map !== "undefined") {

          var zoom = (venue.ecwd_map_zoom != "") ? parseInt(venue.ecwd_map_zoom) : 17;

          var lat_long = venue.ecwd_venue_lat_long.split(',');
          if (lat_long.length !== 2) {
            lat_long[0] = 40.7127837;
            lat_long[1] = -74.00594130000002;
          }
          var myLatlng = new google.maps.LatLng(parseFloat(lat_long[0]), parseFloat(lat_long[1]));

          deleteMarkers();
          addMarker(myLatlng, false);

          map.setCenter(myLatlng);
          map.setZoom(zoom);

        }

        this.$mapTable.find('th, td').removeClass('ecwd-hide-map-td');
        this.$mapTable.find('.ecwd_google_map').removeClass('ecwd-hide-map');
      } else {
        this.$mapTable.find('.ecwd_google_map').addClass('ecwd-hide-map');
        this.$mapTable.find('th, td').addClass('ecwd-hide-map-td');

      }
    };

  }


  function ecwd_venue_meta_controller()
  {

    this.$container = null;
    this.$map_container = null;
    this.$description = null;
    var _this = this;

    this.init = function ($container)
    {
      this.$container = $container;
      this.$map_container = this.$container.find('.ecwd_google_map');
      this.$ecwd_venue_show_map = this.$container.find('#ecwd_venue_show_map');
      this.$description = this.$container.find('.ecwd_venue_meta_decription');


      this.$ecwd_venue_show_map.on("change", function ()
      {

        if ($(this).is(':checked')) {

          if (_this.$map_container.length > 0) {
            _this.$map_container.removeClass('ecwd-hide-map');
          } else {

          }

          _this.$description.removeClass('ecwd_hidden');
        } else {

          if (_this.$map_container.length > 0) {
            _this.$map_container.addClass('ecwd-hide-map');
          } else {

          }
          _this.$description.addClass('ecwd_hidden');
        }
      });

    }

  }

  if ($('.ecwd-add_organizer-container').length > 0) {
    add_organizer_form_event_page();
  }

  function add_organizer_form_event_page()
  {
    var container = $('.ecwd-add_organizer-container');
    var form = $('.ecwd-add_organizer-container').find('.ecwd-add-organizer-form');

    container.find('a.ecwd-add-organizer').on('click', function (e)
    {
      e.preventDefault();

      if (form.is(":visible")) {
        form.hide();
      } else {
        form.show();
      }

      return false;
    });

    form.find('.ecwd-add-organizer-save').on('click', function (e)
    {
      e.preventDefault();

      var spinner = container.find('.spinner');
      var title = form.find('#ecwd-add-organizer-title');

      if (title.val() == "") {
        alert("Organizer title is required");
        return false;
      }

      var content = form.find('#ecwd-add-organizer-content');
      var phone = form.find('#ecwd_organizer_meta_phone');
      var website = form.find('#ecwd_organizer_meta_website');


      var post_data = {
        title: title.val(),
        content: content.val(),
        post_type: 'ecwd_organizer',
        metas: {
          phone: phone.val(),
          website: website.val()
        }
      };

      title.prop('disabled', true);
      content.prop('disabled', true);
      phone.prop('disabled', true);
      website.prop('disabled', true);
      jQuery(this).prop('disabled', true);
      spinner.addClass('is-active');

      var response = add_new_post(post_data);

      if (response.success == true) {

        var template = form.find('.ecwd-organizer-template').html();

        template = template.replace(new RegExp('{organizer_id}', 'gi'), response.id);
        template = template.replace(new RegExp('{organizer_title}', 'gi'), response.title);

        container.closest('#ecwd-display-options-wrap').find('.ecwd-meta-control').prepend(template);

      }

      title.val("");
      content.val("");
      phone.val("");
      website.val("");

      title.prop('disabled', false);
      content.prop('disabled', false);
      phone.prop('disabled', false);
      website.prop('disabled', false);
      jQuery(this).prop('disabled', false);
      spinner.removeClass('is-active');

      form.hide();
      return false;
    });

  }

  function add_new_post(post_data)
  {

    var response = null;

    $.ajax({
      url: ecwd.ajaxurl,
      type: "POST",
      dataType: 'json',
      async: false,
      data: {
        action: 'ecwd_add_post',
        nonce: ecwd.ajaxnonce,
        post_data: post_data,
      },
      success: function (data)
      {
        response = data;
      },
      error: function (data)
      {
        response = null;
      }
    });

    return response;
  }

    jQuery('#ecwd_reset_settings_button').on('click', function (e) {
        e.preventDefault();

        jQuery('#ecwd_reset_settings_form').submit();

        return false;
    });


}(jQuery));

var map;
var markers = [];
var geocoder;


var venue_metas_container = null;
if (jQuery('.ecwd-venue-meta-fields').length > 0) {
  venue_metas_container = jQuery('.ecwd-venue-meta-fields');
}

function initialize()
{
  geocoder = new google.maps.Geocoder();

  var lat_long = document.getElementById('ecwd_lat_long').value.split(',');
  var lat_long_available = false;
  if (lat_long[0]) {
    var myLatlng = new google.maps.LatLng(parseFloat(lat_long[0]), parseFloat(lat_long[1]));
    lat_long_available = true;
  } else {
    var myLatlng = new google.maps.LatLng(40.7127837, -74.00594130000002);
  }
  var ecwd_zoom = parseInt(document.getElementById('ecwd_map_zoom').value);
  var ecwd_marker = parseInt(document.getElementById('ecwd_marker').value);

  var mapOptions = {
    zoom: ecwd_zoom,
    center: myLatlng,
    scrollwheel: false
  };

  if (ecwd_admin_params.gmap_style !== "") {
    mapOptions.styles = JSON.parse(ecwd_admin_params.gmap_style);
  }

  map = new google.maps.Map(document.getElementById('map-canvas'),
    mapOptions);

  if (!lat_long_available && navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position)
    {
      initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
      map.setCenter(initialLocation);
    });
  }

  var ecwd_typing_timer = null;
  var $inputs = jQuery('#ecwd_longitude, #ecwd_latitude');

  $inputs.on('keyup', function ()
  {
    clearTimeout(ecwd_typing_timer);
    ecwd_typing_timer = setTimeout(function ()
    {
      var latlng = new google.maps.LatLng(jQuery('#ecwd_latitude').val(), jQuery('#ecwd_longitude').val());
      deleteMarkers();
      geocodePosition(latlng);
      addMarker(latlng);
      map.setCenter(latlng);
    }, 1000);
  });

  $inputs.on('keydown', function ()
  {
    clearTimeout(ecwd_typing_timer);
  });


  var input = document.getElementById('ecwd_event_location');

  var types = document.getElementById('type-selector');
  if (venue_metas_container == null) {
    // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  }
  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

  var infowindow = new google.maps.InfoWindow();
  var address_marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29),
    draggable: true
  });
  markers.push(address_marker);

  google.maps.event.addListener(autocomplete, 'place_changed', function ()
  {
    infowindow.close();
    address_marker.setVisible(false);

    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(ecwd_zoom);
    }

    deleteMarkers();
    geocodePosition(place.geometry.location);
    address_marker = addMarker(place.geometry.location);

    var lat_long_val = place.geometry.location.toString().replace(')', '').replace('(', '');
    document.getElementById('ecwd_lat_long').value = lat_long_val;
    var lat_long_data = lat_long_val.split(',');
    if (lat_long_data.length == 2) {
      document.getElementById('ecwd_latitude').value = lat_long_data[0];
      document.getElementById('ecwd_longitude').value = lat_long_data[1];
    }
    //marker.setIcon(/** @type {google.maps.Icon} */({
    //    url: place.icon,
    //    size: new google.maps.Size(71, 71),
    //    origin: new google.maps.Point(0, 0),
    //    anchor: new google.maps.Point(17, 34),
    //    scaledSize: new google.maps.Size(35, 35)
    //}));
    address_marker.setPosition(place.geometry.location);
    address_marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }

    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, address_marker);
  });

  google.maps.event.addListener(map, 'click', function (event)
  {

    if (map.ecwd_draggable_marker == false) {
      return;
    }


    deleteMarkers();
    geocodePosition(event.latLng);
    addMarker(event.latLng);
  });

  google.maps.event.addListener(address_marker, 'dragend', function ()
  {

    if (map.ecwd_draggable_marker == false) {
      return;
    }


    setMarkerPosition(address_marker);
    geocodePosition(address_marker.getPosition());
  });
  google.maps.event.addListener(map, 'zoom_changed', function ()
  {
    jQuery('#ecwd_map_zoom').val(map.getZoom());
  });

  if (ecwd_marker == 1) {

    var draggable = true;
    var ecwd_event_venue_select = jQuery('#ecwd_event_venue');
    if (ecwd_event_venue_select.length > 0 && ecwd_event_venue_select.val() != '0' && ecwd_event_venue_select.val() != 'new') {
      draggable = false;
    }


    var infowindow = new google.maps.InfoWindow();
    addMarker(myLatlng, draggable);
    var loc = document.getElementById('ecwd_event_location').value;

  }
}

// Add a marker to the map and push to the array.
function addMarker(location, draggable)
{
  if (typeof draggable == "undefined") {
    draggable = draggable;
  }

  map.ecwd_draggable_marker = draggable;

  var marker = new google.maps.Marker({
    position: location,
    map: map,
    draggable: true
  });
  markers.push(marker);
  setMarkerPosition(marker);
  google.maps.event.addListener(marker, 'dragend', function (event)
  {

    if (map.ecwd_draggable_marker === false) {
      return;
    }


    setMarkerPosition(marker);
    geocodePosition(marker.getPosition());
  });
  return marker;
}

// Sets the map on all markers in the array.
function setAllMap(map)
{
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers()
{
  setAllMap(null);
}

// Shows any markers currently in the array.
function showMarkers()
{
  setAllMap(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers()
{
  clearMarkers();
  markers = [];
}

function setMarkerPosition(marker)
{
  var lat_long_val = marker.getPosition().toUrlValue();
  document.getElementById('ecwd_lat_long').value = lat_long_val;
  var lat_long_data = lat_long_val.split(',');
  if (lat_long_data.length == 2) {
    document.getElementById('ecwd_latitude').value = lat_long_data[0];
    document.getElementById('ecwd_longitude').value = lat_long_data[1];
  }
}

function geocodePosition(pos)
{
  geocoder.geocode({
    latLng: pos
  }, function (responses)
  {
    if (responses && responses.length > 0) {
      updateMarkerAddress(responses[0].formatted_address);
    } else {
      updateMarkerAddress('Cannot determine address at this location.');
    }
  });
}
function updateMarkerAddress(address)
{
  document.getElementById('ecwd_event_location').value = address;

}

function loadScript()
{
  if (ecwd.gmap_key == "") {
    return;
  }
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp' +
    '&callback=initialize&libraries=places&key=' + ecwd.gmap_key;
  document.body.appendChild(script);
}


var ecwd_table;
var ecwd_past_events_table;


jQuery(document).ready(function(){
  var ecwd_events_popup_button = jQuery('.ecwd_events_popup_button');
  if(ecwd_events_popup_button.length>0){
    ecwd_events_popup_button.magnificPopup({
      type:'inline',
      callbacks: {

      }
    });
  }

    var ecwd_delete_past_events = jQuery(".ecwd_delete_past_events a");
    if(ecwd_delete_past_events.length>0){
        ecwd_delete_past_events.magnificPopup({
            type:'inline',
            callbacks: {

            }
        });
    }

  jQuery('#ecwd_ask_question').parent().attr('target','_blank');


});

var $ = jQuery;
$('body').on('click', '.ecwd_events_popup_button', function (){
  if(typeof ecwd_table !== "undefined"){
    ecwd_table.destroy();
  }
  $(".ecwd_event_table").remove();
  ecwd_get_events();
});
function ecwd_get_events() {
  if(ecwdServerVars.calendar_id != "" && typeof ecwdServerVars.calendar_id != "undefined"){
    var calendar_id = ecwdServerVars.calendar_id;
  }else{
    var calendar_id = jQuery("#post_ID").val();
  }
  var url = ecwdServerVars.rest_route+'excluded_event';
  var request_url = ecwd_updateQueryStringParameter(url, 'nonce', ecwdServerVars.ecwdRestNonce);
  request_url = ecwd_updateQueryStringParameter(request_url, 'calendar_id', calendar_id);
  $(".ecwd_event_list_popup_loader").css({
    'display':'block'
  });
  $(".ecwd_add_events").addClass('ecwd_add_events_button');
  $.ajax({
    url: request_url,
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      $(".ecwd_event_list_popup_loader").css({
        'display':'none'
      });
      $(".ecwd_add_events").removeClass('ecwd_add_events_button');
      var ecwd_event_list = "";
      $.each(data.data, function (key, value ) {
        ecwd_event_list+="<tr data-id='"+value.id+"' data-title='"+value.title+"'><td></td><td>"+value.title+"</td><td>"+value.from+"</td><td>"+value.end+"</td></tr>";
      });
      var ecwd_event_table = '' +
        '<table class="ecwd_event_table display" style="width:100%">' +
        '<thead>' +
        '<th><input type="checkbox" name="select_all" value="1" id="ecwd-select-all"></th>'+
        '<th>Title</th>' +
        '<th>Start date</th>' +
        '<th>End date</th>' +
        '</thead>' +
        '<tbody>' +
        ecwd_event_list+
        '</tbody>' +
        '<tfoot>' +

        '</tfoot>' +
        '<th></th>'+
        '<th>Title</th>' +
        '<th>Start date</th>' +
        '<th>End date</th>' +
        '</tfoot>' +
        '</table>';



      $(".ecwd_event_table").remove();
      $("#ecwd_event_list_popup").prepend(ecwd_event_table);
      ecwd_table = $('.ecwd_event_table').DataTable({
        'columnDefs': [{
          'targets': 0,
          'searchable':false,
          'orderable':false,
          'className': 'dt-body-center',
          'render': function (data, type, full, meta){
            return '<input type="checkbox" name="id[]"  value="">';
          }
        }],
        'order': [[1, 'asc']]
      });

      $('body').on('click', '#ecwd-select-all', function (){
        var rows = ecwd_table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
      });


      $('.ecwd_event_table tbody').on('change', 'input[type="checkbox"]', function(){
        if(!this.checked){
          $('#ecwd-select-all').prop('checked', false);
        }
      });
    },
    error: function() {

    },
    beforeSend: setHeader
  });

  function setHeader(xhr) {
    xhr.setRequestHeader('X-WP-Nonce', ecwdServerVars.wpRestNonce);
  }
}

$('body').on('click','.ecwd_add_events',function (e) {
  e.preventDefault();
  $('#ecwd_event_list_popup').magnificPopup('close');
  var ecwd_event_data = [];


  ecwd_table.$('input[type="checkbox"]').each(function(){
    if(this.checked){
      var main_tr = this.closest("tr");
      var event_id = $(main_tr).data("id");
      var event_title = $(main_tr).data("title");


      ecwd_event_data.push({
        event_id:event_id,
      });
      var ecwd_added_event = '<span class="ecwd-calendar-event"> <span>'+ECWDescapeHtml(event_title)+'</span>\n' +
        '                                <input type="hidden" name="ecwd-calendar-event-id[]" value="'+event_id+'">\n' +
        '                                <span class="ecwd-calendar-event-edit"><a href="post.php?post=21&amp;action=edit" target="_blank">e</a></span>\n' +
        '                                <span class="ecwd-calendar-event-delete">x</span>\n' +
        '                            </span>';
      $("#ecwd_calendar_meta table .ecwd-events").append(ecwd_added_event);
    }
  });
  ecwd_ajax_add_events(ecwd_event_data);
});

function ecwd_ajax_add_events(ecwd_event_data ) {
  var calendar_id = $('#post_ID').val();
  var url = ecwdServerVars.rest_route+'add_event';
  var request_url = ecwd_updateQueryStringParameter(url, 'nonce', ecwdServerVars.ecwdRestNonce);

  $.ajax({
    url: request_url,
    type: 'POST',
    dataType: 'json',
    data: {calendar_id:calendar_id, ecwd_data:ecwd_event_data ,nonce: ecwdServerVars.ecwdRestNonce },
    success: function(data) {
      if(data.success){
        ecwd_table.destroy();
        if(data.free_events_count == 0){
          var new_post_url = $(".ecwd_events_popup_button").data('new_event_url');
          $(".ecwd_events_popup_button").remove();
          $(".ecwd-calendar-event-add").html('<a href="'+new_post_url+'" target="_blank">Create more events</a><a href="\'+new_post_url+\'" target="_blank"><span class="add_event_plus">+</span></a></span>');
        }
      }
    },
    error: function() {},
    beforeSend: setHeader
  });
  function setHeader(xhr) {
    xhr.setRequestHeader('X-WP-Nonce', ecwdServerVars.wpRestNonce);
  }
}
function ecwd_updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}




$('body').on('click', '.ecwd_delete_past_events a', function (){
  if(typeof ecwd_past_events_table !== "undefined"){
    ecwd_past_events_table.destroy();
  }
  $(".ecwd_past_event_table").remove();
  ecwd_get_past_events();
});
function ecwd_get_past_events() {
  var url = ecwdServerVars.rest_route+'past_event';
  var request_url = ecwd_updateQueryStringParameter(url, 'nonce', ecwdServerVars.ecwdRestNonce);
  $(".ecwd_past_event_list_popup_loader").css({
    'display':'block'
  });
  $(".ecwd_past_events_delete_button").addClass('ecwd_delete_events_button');
  $.ajax({
    url: request_url,
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      $(".ecwd_past_event_list_popup_loader").css({
        'display':'none'
      });
      $(".ecwd_past_events_delete_button").removeClass('ecwd_delete_events_button');
      var ecwd_event_list = "";
      $.each(data.data, function (key, value ) {
        ecwd_event_list+="<tr data-id='"+value.id+"' data-title='"+value.title+"'><td></td><td>"+value.title+"</td><td>"+value.from+"</td><td>"+value.end+"</td></tr>";
      });
      var ecwd_event_table = '' +
        '<table class="ecwd_past_event_table display" style="width:100%">' +
        '<thead>' +
        '<th><input type="checkbox" name="select_all" value="1" id="ecwd-select-all"></th>'+
        '<th>Title</th>' +
        '<th>Start date</th>' +
        '<th>End date</th>' +
        '</thead>' +
        '<tbody>' +
        ecwd_event_list+
        '</tbody>' +
        '<tfoot>' +

        '</tfoot>' +
        '<th></th>'+
        '<th>Title</th>' +
        '<th>Start date</th>' +
        '<th>End date</th>' +
        '</tfoot>' +
        '</table>';



      $(".ecwd_event_table").remove();
      $(".ecwd_popup_notice").remove();
      $(".ecwd_popup_title").remove();
      $("#ecwd_past_event_list_popup").prepend(ecwd_event_table);
      $("#ecwd_past_event_list_popup").prepend("<h4 class='ecwd_popup_notice'>Recurring events are excluded from this list</h4>");
      $("#ecwd_past_event_list_popup").prepend("<h3 class='ecwd_popup_title'>Delete past events</h3>");
      ecwd_past_events_table = $('.ecwd_past_event_table').DataTable({
        'columnDefs': [{
          'targets': 0,
          'searchable':false,
          'orderable':false,
          'className': 'dt-body-center',
          'render': function (data, type, full, meta){
            return '<input type="checkbox" name="id[]"  value="">';
          }
        }],
        'order': [[1, 'asc']]
      });

      $('body').on('click', '#ecwd-select-all', function (){
        var rows = ecwd_past_events_table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
      });


      $('.ecwd_event_table tbody').on('change', 'input[type="checkbox"]', function(){
        if(!this.checked){
          $('#ecwd-select-all').prop('checked', false);
        }
      });
    },
    error: function() {

    },
    beforeSend: setHeader
  });

  function setHeader(xhr) {
    xhr.setRequestHeader('X-WP-Nonce', ecwdServerVars.wpRestNonce);
  }
}
$('body').on('click','.ecwd_past_events_delete_button',function (e) {
  e.preventDefault();
  $('#ecwd_past_event_list_popup').magnificPopup('close');
  var ecwd_past_event_data = [];


  ecwd_past_events_table.$('input[type="checkbox"]').each(function(){
    if(this.checked){
      var main_tr = this.closest("tr");
      var event_id = $(main_tr).data("id");
      ecwd_past_event_data.push(
        event_id
      );
    }
  });
  if(ecwd_past_event_data.length>0){
    ecwd_ajax_delete_events(ecwd_past_event_data);
  }
});

function ecwd_ajax_delete_events(data) {
  var url = ecwdServerVars.rest_route+'delete_event';
  var request_url = ecwd_updateQueryStringParameter(url, 'nonce', ecwdServerVars.ecwdRestNonce);
  $.ajax({
    url: request_url,
    type: 'POST',
    dataType: 'json',
    data: { events_id:data ,nonce: ecwdServerVars.ecwdRestNonce },
    success: function(data) {
      if(data.success){

      }
    },
    error: function() {},
    beforeSend: setHeader
  });
  function setHeader(xhr) {
    xhr.setRequestHeader('X-WP-Nonce', ecwdServerVars.wpRestNonce);
  }

}

function ECWDescapeHtml(str) {
    return str.replace(/[&<>"'\/]/g, function (s) {
        var entityMap = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': '&quot;',
            "'": '&#39;',
            "/": '&#x2F;'
        };

        return entityMap[s];
    });
}

