jQuery(document).ready(function ($) {
	$('#add_field').on('click', function () {
		$('#event_span').show();
		var num = $('.clonedInput').length; // how many "duplicatable" input fields we currently have.
		var newNum = new Number(num + 1);   // the numeric ID of the new input field being added.
		// create the new element via clone(), and manipulate it's ID using newNum value.
		var newElem = $('#event' + num).clone().attr('id', 'event' + newNum);
		// insert the new element after the last "duplicatable" input field.
		$('#event' + num).after(newElem);
		// enable the "remove" button.
		$('#del_field').removeAttr('disabled');
		// business rule: you can only add 40 occurrences.
		if ( newNum == 40 ) {
			$('#add_field').attr('disabled', 'disabled');
		}
	});

	$('#del_field').on( 'click', function () {
		var num = $('.clonedInput').length; // how many "duplicatable" input fields we currently have.
		$('#event' + num).remove();	 // remove the last element.
		// enable the "add" button.
		$('#add_field').removeAttr('disabled');
		// if only one element remains, disable the "remove" button.
		if ( num - 1 == 1 ) {
			$('#del_field').attr('disabled', 'disabled');
		}
		$('#event_span').hide();
	});

	$( '#del_field' ).attr('disabled', 'disabled');
	$( '#event_span' ).hide();

	$(".selectall").on( 'click', function () {
		var checked_status = $(this).prop('checked');
		var checkbox_name  = $(this).attr('id');
		$('input[name="' + checkbox_name + '[]"]').each(function () {
			$(this).prop('checked', checked_status);
		});
	});

	$( '.mc-actions input' ).attr( 'disabled', 'disabled' );
	$( '#my-calendar-admin-table input' ).on( 'change', function (e) {
		var checked_status = $(this).prop('checked');
		if ( checked_status ) {
			$( '.mc-actions input' ).removeAttr( 'disabled' );
		} else {
			checkboxes = $( '#my-calendar-admin-table input:checked' );
			if ( checkboxes.length == 0 ) {
				$( '.mc-actions input' ).attr( 'disabled', 'disabled' );
			}
		}
	});

	var publishText = $( 'input[name=save]' ).val();
	$( '#e_approved' ).on( 'change', function (e) {
		var event_status = $(this).val();
		if ( event_status == 0 ) {
			$( 'input[name=save]' ).val( mcAdmin.draftText );
		} else {
			$( 'input[name=save]' ).val( publishText );
		}
	});

	$( '.mc-tabs' ).each( function ( index ) {
		var tabs = $('.mc-tabs .wptab').length;
		var firstItem = window.location.hash;
		if ( ! firstItem ) {
			var firstItem = '#' + $( '.mc-tabs .wptab:nth-of-type(1)' ).attr( 'id' );
		}
		$('.mc-tabs .tabs a[href="' + firstItem + '"]').addClass('active').attr( 'aria-selected', 'true' );
		if ( tabs > 1 ) {
			$( '.mc-tabs .wptab' ).not( firstItem ).attr( 'aria-hidden', 'true' );
			$( '.mc-tabs .wptab' ).removeClass( 'initial-hidden' );
			$( firstItem ).show();
			$( '.mc-tabs .tabs a' ).on( 'click', function (e) {
				e.preventDefault();
				$('.mc-tabs .tabs a').removeClass('active').attr( 'aria-selected', 'false' );
				$(this).addClass('active').attr( 'aria-selected', 'true' );
				var target = $(this).attr('href');
				window.location.hash = target;
				$('.mc-tabs .wptab').not(target).attr( 'aria-hidden', 'true' );
				$(target).removeAttr( 'aria-hidden' ).trigger( 'focus' );
			});
		}
	});

	$( '#mc-generator .custom' ).hide();
	$( '#mc-generator select[name=type]' ).on( 'change', function () {
		var selected = $( this ).val();
		if ( selected == 'custom' ) {
			$( '#mc-generator .custom' ).show();
		} else {
			$( '#mc-generator .custom' ).hide();
		}
	});

	$('#mc-sortable').sortable({
		placeholder: 'mc-ui-state-highlight',
		update: function (event, ui) {
			$('#mc-sortable-update').html( 'Submit form to save changes' );
		}
	});
	$('#mc-sortable .up').on('click', function (e) {
		var parentEls = $( this ).parents().map(function() { return this.tagName; } ).get();
		var parentLi  = $.inArray( 'LI', parentEls );
		if ( 1 == parentLi ) {
			$(this).parents('li').insertBefore($(this).parents('li').prev());
			$( '#mc-sortable li' ).removeClass( 'mc-updated' );
			$(this).parents('li').addClass( 'mc-updated' );
		} else {
			$(this).parents('tr').insertBefore($(this).parents('tr').prev());
			$( '#mc-sortable tr' ).removeClass( 'mc-updated' );
			$(this).parents('tr').addClass( 'mc-updated' );
		}
	});
	$('#mc-sortable .down').on('click', function (e) {
		var parentEls = $( this ).parents().map(function() { return this.tagName; } ).get();
		var parentLi  = $.inArray( 'LI', parentEls );
		if ( 1 == parentLi ) {
			$(this).parents('li').insertAfter($(this).parents('li').next());
			$( '#mc-sortable li' ).removeClass( 'mc-updated' );
			$(this).parents('li').addClass( 'mc-updated' );
		} else {
			$(this).parents('tr').insertAfter($(this).parents('tr').next());
			$( '#mc-sortable tr' ).removeClass( 'mc-updated' );
			$(this).parents('tr').addClass( 'mc-updated' );
		}
	});
});

var mediaPopup = '';
(function ($) {
	"use strict";
	$(function () {
		/**
		 * Clears any existing Media Manager instances
		 *
		 * @author Gabe Shackle <gabe@hereswhatidid.com>
		 * @modified Joe Dolson <plugins@joedolson.com>
		 * @return void
		 */
		function clear_existing() {
			if (typeof mediaPopup !== 'string') {
				mediaPopup.detach();
				mediaPopup = '';
			}
		}

		$('.mc-image-upload')
			.on('click', '.textfield-field', function (e) {
				e.preventDefault();
				var $self = $(this),
					$inpField = $self.parent('.field-holder').find('#e_image'),
					$idField = $self.parent('.field-holder').find('#e_image_id'),
					$displayField = $self.parent('.field-holder').find('.event_image');
				clear_existing();
				mediaPopup = wp.media({
					multiple: false, // add, reset, false.
					title: 'Choose an Uploaded Document',
					button: {
						text: 'Select'
					}
				});

				mediaPopup.on('select', function () {
					var selection = mediaPopup.state().get('selection'),
						id = '',
						img = '',
						height = '',
						width = '';
					if (selection) {
						id = selection.first().attributes.id;
						height = mcAdmin.thumbHeight;
						width = ( ( selection.first().attributes.width ) / ( selection.first().attributes.height ) ) * height;
						img = "<img src='" + selection.first().attributes.url + "' width='" + width + "' height='" + height + "' />";
						$inpField.val(selection.first().attributes.url);
						$idField.val(id);
						$displayField.html(img);
					}
				});
				mediaPopup.open();
			})
	});
})(jQuery);