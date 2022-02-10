(function($){
	var aicpCookies = Cookies.noConflict();
	// Fire the code only of any ad codes exists
	if ( $(".aicp").length > 0 ) {
		//console.log($(".aicp").length);
		if( typeof aicpCookies.get('aicp_click_count') === 'undefined' ) {
			//console.log( "Cookie does not exists. Setting up cont var for the first time." );
			var count = 0;
		} else {
			//console.log( "Cookie already exists so I'm just passing the Cookie value to the variable" );
			var count = aicpCookies.get('aicp_click_count');
		}
		//console.log(count);

		//if the user has already reached the click limit, there is no point of showing the ads
		//just do display none
		if( count > AICP.clickLimit ) {
			$(".aicp").css({ display: "none" });
		} else {
			$(".aicp iframe").iframeTracker({
				blurCallback: function(){
					// Do something when the iframe is clicked
					//console.log( "Iframe Ad Clicked" );
					//console.log( "count: " + count );
					++count; //checking how many times uses click on the ads
					// console.log(count);
					/* Saving this value to the cookie in case the user reloads the page and the counter gets reset */
					aicpCookies.set(
						'aicp_click_count', 
						count, 
						{ 
							expires: ( AICP.clickCounterCookieExp )/24, 
							sameSite: 'strict', 
							secure: location.protocol === 'https:' ? true : false 
						}
					);
					//if the user click on ads for more than 3 times
					if( count >= AICP.clickLimit ) {
						// If the visitor is click bombing, stop showing ads immediately.
						$(".aicp").css({ display: "none" });
						// Now it's AJAX time to handle the data and push it to database
						jQuery.ajax({
							type: 'POST',
							url: AICP.ajaxurl,
							data: {
								"action": "process_data", 
								"nonce": AICP.nonce,
								"ip": AICP.ip,
								"aicp_click_count": count
							},
							success: function( data ){
								console.log( "You are now blocked from seeing ads." );
							},
							error: function(data) {
								console.error("AICP Error Details:", data);
							}
						});
					}
				}
			});
		}
	}
})(jQuery);