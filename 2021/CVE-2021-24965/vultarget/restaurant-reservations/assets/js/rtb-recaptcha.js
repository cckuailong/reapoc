var rtbLoadRecaptcha = function() {
	grecaptcha.render('rtb_recaptcha', {
      'sitekey' : rtb_recaptcha.site_key
    });
}