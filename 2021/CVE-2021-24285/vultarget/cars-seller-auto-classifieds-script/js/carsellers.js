

function check_availability(id)
{       
         
         jQuery('#postid').val(id);
         bootbox
                .dialog({
                    title: 'Request Information',
                    message: jQuery('#send_request'),
                    show: false // We will show it manually later
                })
                .on('shown.bs.modal', function() {
                    jQuery('#send_request')
                        .show()                             // Show the login form
                        .formValidation('resetForm'); // Reset form
                })
                .on('hide.bs.modal', function(e) {
                    // Bootbox will remove the modal (including the body which contains the login form)
                    // after hiding the modal
                    // Therefor, we need to backup the form
                    jQuery('#send_request').hide().appendTo('body');
                })
                .modal('show').find("div.modal-dialog").addClass("lead-form-container1");
        jQuery('div.modal-header').addClass("lead-title");
         var buttont=jQuery('.lead-form-container1 .lead-title button');
         jQuery('.lead-form-container1 .lead-title button').remove();
        jQuery('.lead-form-container1 .lead-title').append(buttont);
        
}


jQuery(document).on('ready',function ($) {
    
    jQuery('#send_request').formValidation({
            framework: 'bootstrap',
           
            fields: {
                FirstName: {
                    validators: {
                        notEmpty: {
                            message: 'First name is required'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z\s]+$/,
                            message: 'First name can only consist of alphabetical characters'
                        }
                    }
                },
                LastName: {
                    validators: {
                        notEmpty: {
                            message: 'Last name is required'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z\s]+$/,
                            message: 'Last name can only consist of alphabetical characters'
                        }
                    }
                },
                message: {
                    validators: {
                        notEmpty: {
                            message: 'Message is required'
                        }
                    }
                },
                EmailAddress: {
                    validators: {
                        notEmpty: {
                            message: 'Email address is required'
                        },
                        emailAddress: {
                            message: 'Email address is not valid'
                        }
                    }
                },
                PhoneNumber: {
                    validators: {
                        
                        integer: {
                            message: 'Phone Number is not valid',
                         
                        }
                    }
                }
                
            }
        })
        .on('success.form.fv', function(e) {
            // Save the form data via an Ajax request
            e.preventDefault();

            var $form = jQuery(e.target),
                id    = $form.find('[name="id"]').val();

            // The url and method might be different in your application
            jQuery.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                                action: 'send_avaibility_request_carseller',
                                formdata: $form.serialize()
                            },
                beforeSend: function () {
                                $form.parents('.bootbox').modal('hide');
                                jQuery("#element").show();                      
                            jQuery("#element").LoadingOverlay('show');

                            },
            }).success(function(response) {
                jQuery("#element").hide();                      
                jQuery("#element").LoadingOverlay('hide');
//                    bootbox.alert(response);
                    var obj = jQuery.parseJSON(response);
                            
                    jQuery('#send_request input,#send_request textarea ').val('');
                    
                    bootbox.dialog({
                        message: obj.message,
                        title: obj.title,
                       
                    }).find("div.modal-dialog").addClass("lead-form-container1");
        jQuery('div.modal-header').addClass("lead-title");
        




            });
        });
});



function hideform()
{
	jQuery('body').unblock();
}


function showfloorplan()
{
    if(jQuery('#modal-properties').length || jQuery('#modal-floorplans').length)
    {
    jQuery('body').block({message: jQuery('#dlp-modal'), css: {
            padding: '15px', 
            backgroundColor: 'transparent', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            top: '1px', 
                left: '', 
                right: '100px', 
                border: 'none',
                  position: 'absolute', 
            color: '#fff',left:'155px', width:'45%', cursor:'default' },
         overlayCSS: {cursor:'default', top: '1px',},});

        jQuery('#dlp-modal').parent().css('top','5%');
    }
    if(jQuery('#modal-properties').length)
    {
        jQuery('#modal-floorplans').hide();
        jQuery('#modal-properties').show();
    }
    else if(jQuery('#modal-floorplans').length)
    {
       jQuery('#modal-floorplans').show(); 
    }
    
}
function showfloorplan_button(planname)
{
    jQuery('#modal-properties').hide();

    jQuery('#modal-floorplans').hide();
    jQuery('#dlp-modal .lightbox .modal-tabs li').removeClass('active');
    jQuery('#dlp-modal .lightbox .modal-tabs li.'+planname).addClass('active');
    jQuery('#'+planname).show();
    
}








jQuery(function($) {
    
    var sendform='<form class="form-horizontal row" id="send_request" > <input hidden="" id="postid" name="postid" value=""><div class="col-md-12"> <div class="form-group col-md-5 nopadding"> <input type="text" class="form-control empty-field" id="leadFirstName" name="FirstName" value="" placeholder="First name*" required ></div><div class="form-group col-md-2 nopadding"></div><div class="form-group col-md-5 nopadding"> <input type="text" class="form-control empty-field" id="leadLastName"  name="LastName" value="" placeholder="Last name*" required></div><div class="form-group col-md-5 nopadding"> <input type="email" class="form-control empty-field" id="leadEmailAddress" name="EmailAddress" value="" placeholder="Email address*"  data-error="Email address is invalid"  required ></div><div class="form-group col-md-2 nopadding"></div><div class="form-group col-md-5 nopadding"> <input type="text" class="form-control empty-field" id="leadPhoneNumber"  name="PhoneNumber" value="" placeholder="Phone number"></div><div class="form-group col-md-12 nopadding"> <textarea aria-required="true" id="leadMessage" name="message" required class="form-control empty-field edit-off"  placeholder="Please write your message here." ></textarea></div><div class="form-group col-md-12"><div class="col-md-4"></div><div class="col-md-4"><button type="submit" class="btn btn-default col-md-12 btn-success btn-md"><span aria-hidden="true" class="glyphicon glyphicon-send"></span> &nbsp; Send Request</button></div><div class="col-md-4"></div></div></div></form>';
    $('body').append(sendform);
    $('#send_request').hide();
    

    
    $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
  
  
  
});












jQuery(document).ready(function ($) {

  $('#checkbox').change(function(){
    setInterval(function () {
        moveRight();
    }, 3000);
  });
  
    var slideCount = $('#dlp-modal #floorplan-carousel ul li').length;
    var slideWidth = $('#dlp-modal #floorplan-carousel ul li').width();
    var slideHeight = $('#dlp-modal #floorplan-carousel ul li').height();
    var sliderUlWidth = slideCount * slideWidth;
    
    $('#dlp-modal #floorplan-carousel').css({ width: slideWidth, height: slideHeight });
    
    $('#dlp-modal #floorplan-carousel ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
    
    $('#dlp-modal #floorplan-carousel ul li:last-child').prependTo('#dlp-modal #floorplan-carousel ul');

    function moveLeft() {
        $('#dlp-modal #floorplan-carousel ul').animate({
            left: + slideWidth
        }, 200, function () {
            $('#dlp-modal #floorplan-carousel ul li:last-child').prependTo('#dlp-modal #floorplan-carousel ul');
            $('#dlp-modal #floorplan-carousel ul').css('left', '');
        });
    };

    function moveRight() {
        $('#dlp-modal #floorplan-carousel ul').animate({
            left: - slideWidth
        }, 200, function () {
            $('#dlp-modal #floorplan-carousel ul li:first-child').appendTo('#dlp-modal #floorplan-carousel ul');
            $('#dlp-modal #floorplan-carousel ul').css('left', '');
        });
    };

    $('a.arrow-holder.left').click(function () {
        moveLeft();
    });

    $('a.arrow-holder.right').click(function () {
        moveRight();
    });

}); 




jQuery(document).ready(function ($) {

  $('#checkbox').change(function(){
    setInterval(function () {
        moveRight();
    }, 3000);
  });
  
    var slideCount = $('#dlp-modal #modal-properties ul li').length;
    var slideWidth = $('#dlp-modal #modal-properties ul li').width();
    var slideHeight = $('#dlp-modal #modal-properties ul li').height();
    var sliderUlWidth = slideCount * slideWidth;
    
    $('#dlp-modal #modal-properties').css({ width: slideWidth, height: slideHeight });
    
    $('#dlp-modal #modal-properties ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
    
    $('#dlp-modal #modal-properties ul li:last-child').prependTo('#dlp-modal #modal-properties ul');

    function moveLeft() {
        $('#dlp-modal #modal-properties ul').animate({
            left: + slideWidth
        }, 200, function () {
            $('#dlp-modal #modal-properties ul li:last-child').prependTo('#dlp-modal #modal-properties ul');
            $('#dlp-modal #modal-properties ul').css('left', '');
        });
    };

    function moveRight() {
        $('#dlp-modal #modal-properties ul').animate({
            left: - slideWidth
        }, 200, function () {
            $('#dlp-modal #modal-properties ul li:first-child').appendTo('#dlp-modal #modal-properties ul');
            $('#dlp-modal #modal-properties ul').css('left', '');
        });
    };

    $('a.arrow-holder.left').click(function () {
        moveLeft();
    });

    $('a.arrow-holder.right').click(function () {
        moveRight();
    });

}); 