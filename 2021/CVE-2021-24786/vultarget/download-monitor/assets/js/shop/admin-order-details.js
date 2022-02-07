jQuery( function ( $ ) {

	function handleChangeOrderAction() {
		var button = $("#dlm-order-details-button-change-state");
		var ogLbl = button.html();
		var isWorking = false;
		var successBlock = null;

		button.click(function(){
			if(isWorking) {
				return;
			}
			isWorking = true;
			button.html('...');

			var new_status = $("#dlm-order-details-current-state").val();

			$.post( dlm_strings.ajax_url_change_order_status, {
				status: new_status,
				order_id: dlm_strings.order_id
			}, function ( response ) {
				if ( response.success === true ) {
					button.html(ogLbl);
					isWorking = false;
					if(successBlock === null) {
						successBlock = $("<span>").addClass("dlm-order-details-update-successful").html("✓");
						button.parent().append(successBlock);
						setTimeout(
							function() {
								successBlock.fadeOut(300, function(){
									successBlock.remove();
									successBlock = null;
								});
							}, 300
						);
					}

				}
			} );

		});
	}

	handleChangeOrderAction();

} );