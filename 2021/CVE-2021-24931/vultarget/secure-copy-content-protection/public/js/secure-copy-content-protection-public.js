(function ($) {
	String.prototype.isValidEmail = function () {
        let reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return reg.test(String(this).toLowerCase());
    };
    
    $(document).find("form[class='ays_sb_form']").submit(function() {
        $(this).submit(function() {
            return false;
        });
        return true;
    });

    $(document).on('change', 'input.ays_sccp_sb_email', function () {
    	var val = $(this).val();
    	$(this).removeClass('ays_red_border');
    	$(this).removeClass('ays_green_border');

		var sub_inp = $(this).parent().find('input[type="submit"]');

		var valid = val.isValidEmail();
    	if (valid) {
            $(this).addClass('ays_green_border').removeAttr('title');
        }
    	
    });

    $(document).on('click', 'input.ays_sccp_sb_sbm', function () {
    	var val = $(this).parent().parent().find('input.ays_sccp_sb_email').val();
    	var inp = $(this).parent().parent().find('input.ays_sccp_sb_email');
    	inp.removeClass('ays_red_border');
    	inp.removeClass('ays_green_border');
		var valid = val.isValidEmail();
    	if (!valid) {
            inp.addClass('ays_red_border').attr('title', 'This field is not valid!');
            inp.addClass('ays_poll_shake');
            setTimeout(() => {                
                inp.removeClass('ays_poll_shake');                
            }, 1000);
            return false;
        } else {
            inp.addClass('ays_green_border').removeAttr('title');
        }
    });
})(jQuery);
