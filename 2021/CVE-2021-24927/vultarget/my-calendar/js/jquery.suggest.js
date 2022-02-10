(function ($) {
	$(function () {
		$('.suggest').autocomplete({
				minLength: 3,
				source: function (req, response) {
					$.getJSON(ajaxurl + '?callback=?&action=' + mc_ajax_action.action, req, response);
				},
				select: function (event, ui) {
					var label = $(this).attr('id');
					$( 'input[name=mc_uri_id]' ).val(ui.item.id);
					$( '#mc_uri_id-note' ).html( ' = (' + ui.item.value + ')' );

					return false;
				}
			}
		);
	});
}(jQuery));