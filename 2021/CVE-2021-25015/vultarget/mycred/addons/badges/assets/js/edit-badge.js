jQuery(function($){

	var myCREDBadgeLevelsWrap     = $( '#mycred-badge-setup .inside' );

	var AddNewLevelButton         = $( '#badges-add-new-level' );
	var AddNewRequirementButton   = $( '#badges-add-new-requirement' );
	var ChangeDefaultImageButton  = $( '#badges-change-default-image' );
	var RemoveDefaultImageButton  = $( '#badges-remove-default-image' );

	var TotalBadgeLevels          = 1;
	var TotalRequirements         = 1;
	var UsedtoCompare             = $( '#badge-requirement-compare a.selected' ).data( 'do' );

	var PointTypeSelect           = $( 'select#badge-select-point-type' );
	var ReferenceSelect           = $( 'select#badge-select-reference' );
	var RequirementTypeSelect     = $( 'select#badge-select-requirement-type' );
	var RequirementCompare        = $( '#badge-requirement-compare a' );

	var LevelImageSelector;
	var DefaultImageSelector;

	/**
	 * Get Badge Requirements Template
	 * Creates a template of the top-level badge's set requirements.
	 * @since 1.7
	 * @version 1.0
	 */
	var mycred_get_badge_requirement_template = function( badgelevel ) {

		var template = '';

		// Loop through each requirement set for the top-level badge
		$( '#mycred-badge-level0 .level-requirements .row-narrow' ).each(function(index){

			// Row
			var required_row_id = $(this).data( 'row' );

			// Point Type
			var required_point_type = $(this).find( 'select.point-type option:selected' );
			if ( required_point_type === undefined || required_point_type == '' )
				required_point_type = '-';
			else
				required_point_type = required_point_type.text();

			// Reference
			var required_reference = $(this).find( 'select.reference option:selected' );
			if ( required_reference === undefined || required_reference == '' )
				required_reference = '-';
			else
				required_reference = required_reference.text();

			//Specific
			var required_reference_specific = $(this).find( '.specific' );
			if( required_reference_specific[0] != undefined ) {
				console.log(required_reference_specific[0].tagName);
				if( required_reference_specific[0].tagName === 'INPUT' ) {
					required_reference_specific = required_reference_specific.val();
				}
				else if( required_reference_specific[0].tagName === 'SELECT' ) {
					var required_reference_specific = $(this).find( 'select.specific option:selected' );
					if ( required_reference === undefined || required_reference == '' )
						required_reference_specific = '-';
					else
						required_reference_specific = required_reference_specific.text();
				}
			}
			else{
				required_reference_specific = '';
			}

			// Amount
			var required_amount = $(this).find( '.form-inline input.form-control' ).val();

			// Requirement type
			var required_type = $(this).find( 'select.req-type option:selected' );
			if ( required_type === undefined || required_type == '' )
				required_type = '-';
			else
				required_type = required_type.text();

			// Comparison
			var compare_to_show = myCREDBadge.compareOR;
			if ( UsedtoCompare == 'AND' )
				compare_to_show = myCREDBadge.compareAND;

			var totalrequirements = $( '#mycred-badge-level0 .level-requirements .row-narrow' ).length;
			if ( totalrequirements > 1 && index+1 == totalrequirements || ( totalrequirements == 1 && index == 0 ) )
				compare_to_show = '';

			// Render fresh requirement template
			template += Mustache.render( BadgeRequirement, {
				level        : badgelevel,
				reqlevel     : required_row_id,
				selectedtype : required_point_type,
				refspecific  : required_reference_specific,
				selectedref  : required_reference,
				reqamount    : required_amount,
				selectedby   : required_type,
				comparelabel : compare_to_show
			});

		});

		return template;

	};

	$(document).ready(function(){

		TotalBadgeLevels  = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
		TotalRequirements = $( '#mycred-badge-setup .level-requirements .row-narrow' ).length;

		console.log( 'Total levels detected: ' + TotalBadgeLevels );

		if ( TotalBadgeLevels > 1 ) {

			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function(index){

				var subrequirementrow = $(this).find( '.level-ref p:last' );
				if ( subrequirementrow !== undefined ) {
					subrequirementrow.empty().text( $( '#mycred-badge-setup input.specific' ).val() );
				}

			});
		}

		// Change Requirement Compare Action
		RequirementCompare.click(function(e){

			e.preventDefault();

			var refselectcompare = $(this);
			refselectcompare.blur();

			if ( refselectcompare.hasClass( 'selected' ) ) return false;

			var currentlyselected = $( '#badge-requirement-compare a.selected' );
			if ( currentlyselected !== undefined )
				currentlyselected.removeClass( 'selected' );

			UsedtoCompare = refselectcompare.data( 'do' );
			$( '#badge-requirement-compare input' ).val( UsedtoCompare );

			refselectcompare.addClass( 'selected' );

			// Make sure there is more then one level
			var numberoflevels = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			if ( numberoflevels == 1 ) return;

			var newrequirementlevel = $( '#mycred-badge-level0 .level-requirements .row-narrow' ).length;

			var compare_to_show = myCREDBadge.compareOR;
			if ( UsedtoCompare == 'AND' )
				compare_to_show = myCREDBadge.compareAND;

			// Loop through all place holders for each level and change the text
			$( '#mycred-badge-setup #badge-levels .level-requirements .level-compare p' ).each(function(index){

				$(this).fadeOut(function(){

					var reqrowid = $(this).data( 'row' );

					if ( newrequirementlevel > 1 && reqrowid+1 == newrequirementlevel )
						$(this).empty().fadeIn();

					else
						$(this).empty().text( compare_to_show ).fadeIn();

				});

			});

		});

		// Add New Requirement Action
		AddNewRequirementButton.click(function(e){

			e.preventDefault();

			console.log( 'Add new requirement clicked' );
			// Prep
			var totallevels         = $( '#mycred-badge-setup #badge-levels .badge-level' );
			var totalrequirements   = $( '#mycred-badge-level0 .level-requirements .row-narrow' );
			var newrequirementlevel = totalrequirements.length;

			// Render a fresh requirement row
			var template = Mustache.render( BadgeNewRequrement, {
				reqlevel  : newrequirementlevel,
				level     : 0,
				reqamount : ''
			});

			// Insert fresh row to the top level
			$( '#mycred-badge-setup #mycred-badge-level0 .level-requirements' ).append( template );

			if ( totallevels.length > 1 ) {

				// Loop through each level and insert a blank requirement row for each one
				totallevels.each(function( index ){

					var currentlevel = $(this).data( 'level' );

					// Skip firt row as we just added one in there
					if ( currentlevel > 0 ) {

						var compare_to_show = myCREDBadge.compareOR;
						if ( UsedtoCompare == 'AND' )
							compare_to_show = myCREDBadge.compareAND;

						// Render a fresh requirement row
						var reqtemplate = Mustache.render( BadgeRequirement, {
							level        : currentlevel,
							reqlevel     : newrequirementlevel,
							reqamount    : '',
							selectedtype : '-',
							selectedref  : '-',
							selectedby   : '-',
							comparelabel : ''
						});

						// Insert fresh row to the requirement list
						$( '#mycred-badge-setup #mycred-badge-level' + currentlevel + ' .level-requirements' ).append( reqtemplate );

						if ( newrequirementlevel > 0 ) {

							for ( var i = newrequirementlevel-1; i > -1; i-- ) {

								$( '#mycred-badge-setup #mycred-badge-level' + currentlevel + ' #level' + currentlevel + 'requirement' + i + ' .level-compare p' ).fadeOut(function(){
									$(this).empty().text( compare_to_show ).fadeIn();
								});

							}

						}

					}

					// Next!

				});

			}

		});

		// Remove Requirement Action
		$( '#mycred-badge-setup' ).on( 'click', 'button.remove-requirement', function(e){

			var rowtoremove = $(this).data( 'req' );

			$( '#level0requirement' + rowtoremove ).slideUp(function(){
				$(this).remove();
			});

			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function( index ){

				var currentlevel = $(this).data( 'level' );

				if ( currentlevel > 0 ) {

					$( '#mycred-badge-setup #level' + currentlevel + 'requirement' + rowtoremove ).remove();

				}

			});

		});

		// Change Point Type Requirement Action
		$( '#mycred-badge-setup' ).on( 'change', 'select.point-type', function(e){

			var typeselectelement = $(this);

			// Make sure something was selected
			var selectedtype = typeselectelement.find( ':selected' );
			if ( selectedtype === undefined ) return;

			// Make sure there is more then one level
			var numberoflevels = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			if ( numberoflevels == 1 ) return;

			var requirementrow = typeselectelement.data( 'row' );

			// Loop through each level
			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function(index){

				var badgelevel = $(this).data( 'level' );
				if ( badgelevel == 0 ) { return true; }

				var subrequirementrow = $(this).find( '#level' + badgelevel + 'requirement' + requirementrow + ' .level-type p' );
				if ( subrequirementrow !== undefined ) {

					subrequirementrow.fadeOut(function(){
						subrequirementrow.empty().text( selectedtype.text() ).fadeIn();
					});

				}

			});

		});

		// Change Reference Requrirement Action
		$( '#mycred-badge-setup' ).on( 'change', 'select.reference', function(e){

			var refselectelement = $(this);

			// Make sure something was selected
			var selectedref = refselectelement.find( ':selected' );
			if ( selectedref === undefined ) return;

			// Make sure there is more then one level
			var numberoflevels = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			if ( numberoflevels == 1 ) return;

			var requirementrow = refselectelement.data( 'row' );

			// Loop through each level
			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function(index){

				var badgelevel = $(this).data( 'level' );
				if ( badgelevel == 0 ) { return true; }

				var subrequirementrow = $(this).find( '#level' + badgelevel + 'requirement' + requirementrow + ' .level-ref p:first' );
				if ( subrequirementrow !== undefined ) {

					subrequirementrow.fadeOut(function(){
						subrequirementrow.empty().text( selectedref.text() ).fadeIn();
						subrequirementrow.next().empty().fadeIn();
					});

				}

			});

		});

		$( '#mycred-badge-setup' ).on( 'blur', 'input.specific', function(e){

			var refselectelement = $(this);

			// Make sure there is more then one level
			var numberoflevels = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			if ( numberoflevels == 1 ) return;

			var requirementrow = refselectelement.data( 'row' );

			// Loop through each level
			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function(index){

				var badgelevel = $(this).data( 'level' );
				if ( badgelevel == 0 ) { return true; }

				var subrequirementrow = $(this).find( '#level' + badgelevel + 'requirement' + requirementrow + ' .level-ref p:last' );
				if ( subrequirementrow !== undefined ) {
					subrequirementrow.fadeOut(function(){
						subrequirementrow.empty().text( refselectelement.val() ).fadeIn();
					});
				}

			});

		});



		$( '#mycred-badge-setup' ).on( 'change', 'select.specific', function(e){

			var refselectelement = $(this);
			var selectedreftype = refselectelement.find( ':selected' );

			// Make sure there is more then one level
			var numberoflevels = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			if ( numberoflevels == 1 ) return;

			var requirementrow = refselectelement.data( 'row' );

			// Loop through each level
			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function(index){

				var badgelevel = $(this).data( 'level' );
				if ( badgelevel == 0 ) { return true; }

				var subrequirementrow = $(this).find( '#level' + badgelevel + 'requirement' + requirementrow + ' .level-ref p:last' );
				if ( subrequirementrow !== undefined ) {
					subrequirementrow.fadeOut(function(){
						subrequirementrow.empty().text( selectedreftype.text() ).fadeIn();
					});
				}

			});

		});

		// Change Requirement Type Action
		$( '#mycred-badge-setup' ).on( 'change', 'select.req-type', function(e){

			var reftypeselectelement = $(this);

			// Make sure something was selected
			var selectedreftype = reftypeselectelement.find( ':selected' );
			if ( selectedreftype === undefined ) return;

			// Make sure there is more then one level
			var numberoflevels = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			if ( numberoflevels == 1 ) return;

			var requirementrow = reftypeselectelement.data( 'row' );

			// Loop through each level
			$( '#mycred-badge-setup #badge-levels .badge-level' ).each(function(index){

				var badgelevel = $(this).data( 'level' );
				if ( badgelevel == 0 ) { return true; }

				var subrequirementrow = $(this).find( '#level' + badgelevel + 'requirement' + requirementrow + ' .level-type-by p' );
				if ( subrequirementrow !== undefined ) {

					subrequirementrow.fadeOut(function(){
						subrequirementrow.empty().text( selectedreftype.text() ).fadeIn();
					});

				}

			});

		});

		// Add New Level Action
		AddNewLevelButton.click(function(e){

			e.preventDefault();

			console.log( 'Add new level button' );
			// Prep
			TotalBadgeLevels  = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
			TotalRequirements = $( '#mycred-badge-setup .level-requirements .row-narrow' ).length;

			// Get the top-level requirement list as a template
			var reqtemplate = mycred_get_badge_requirement_template( TotalBadgeLevels );

			// Render a fresh level
			var template = Mustache.render( BadgeLevel, {
				level        : TotalBadgeLevels,
				levelone     : ( parseInt( TotalBadgeLevels ) + 1 ),
				requirements : reqtemplate
			});

			// Insert fresh row
			$( '#mycred-badge-setup #badge-levels' ).append( template );

		});

		// Set / Change Level Image Action
		$( '#mycred-badge-setup' ).on( 'click', 'button.change-level-image', function(e){

			console.log( 'Change level image button' );

			var button       = $(this);
			var currentlevel = button.data( 'level' );

			LevelImageSelector = wp.media.frames.file_frame = wp.media({
				title    : myCREDBadge.uploadtitle,
				button   : {
					text     : myCREDBadge.uploadbutton
				},
				multiple : false
			});

			// When a file is selected, grab the URL and set it as the text field's value
			LevelImageSelector.on( 'select', function(){

				attachment = LevelImageSelector.state().get('selection').first().toJSON();
				if ( attachment.url != '' ) {

					$( '#mycred-badge-level' + currentlevel + ' .level-image-wrapper' ).fadeOut(function(){

						$( '#mycred-badge-level' + currentlevel + ' .level-image-wrapper' ).empty().removeClass( 'empty dashicons' ).html( '<img src="' + attachment.url + '" alt="Badge level image" \/><input type="hidden" name="mycred_badge[levels][' + currentlevel + '][attachment_id]" value="' + attachment.id + '" \/><input type="hidden" name="mycred_badge[levels][' + currentlevel + '][image]" value="" \/>' ).fadeIn();
						button.text( myCREDBadge.changeimage );

					});

				}

			});

			// Open the uploader dialog
			LevelImageSelector.open();

		});

		// Remove Level Action
		$( '#mycred-badge-setup' ).on( 'click', 'button.remove-badge-level', function(e){

			var leveltoremove = $(this).data( 'level' );
			if ( $( '#mycred-badge-level' + leveltoremove ) === undefined ) return false;

			console.log( 'Remove level button' );

			if ( ! confirm( myCREDBadge.remove ) ) return false;

			$( '#mycred-badge-level' + leveltoremove ).slideUp().remove();
			TotalBadgeLevels--;

		});

		// Change Default Image Action
		ChangeDefaultImageButton.click(function(e){

			e.preventDefault();
			console.log( 'Change default image button' );

			var button = $(this);

			DefaultImageSelector = wp.media.frames.file_frame = wp.media({
				title    : myCREDBadge.uploadtitle,
				button   : {
					text     : myCREDBadge.uploadbutton
				},
				multiple : false
			});

			// When a file is selected, grab the URL and set it as the text field's value
			DefaultImageSelector.on( 'select', function(){

				attachment = DefaultImageSelector.state().get('selection').first().toJSON();
				if ( attachment.url != '' ) {

					$( '#mycred-badge-default .default-image-wrapper' ).fadeOut(function(){

						$( '#mycred-badge-default .default-image-wrapper' ).empty().removeClass( 'empty dashicons' ).html( '<img src="' + attachment.url + '" alt="Badge default image" \/><input type="hidden" name="mycred_badge[main_image]" value="' + attachment.id + '" \/><input type="hidden" name="mycred_badge[main_image_url]" value="" \/>' ).fadeIn();
						button.text( myCREDBadge.changeimage );

						RemoveDefaultImageButton.removeClass('hidden');

					});

				}

			});

			// Open the uploader dialog
			DefaultImageSelector.open();

		});

		$(document).on('change', '.reference', function(){

			var refrence_id = 'mycred_badge_'+$(this).val();
			var ele_name    = $(this).attr('name');
			var row         = $(this).data('row');

			if( window[refrence_id] !== undefined ) {

				totalBadgeLevels  = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
				totalRequirements = $( '#mycred-badge-setup .level-requirements .row-narrow' ).length;

				var template = Mustache.render( window[refrence_id], {
					element_name : ele_name.replace('reference', 'specific'),
					reqlevel     : row
				});
				$(this).parent().next().remove();
				$( this ).closest('.form').append( template );
			}
			else {
				$(this).parent().next().remove();
			}
		});

		RemoveDefaultImageButton.on( 'click', function(e){

			$('.default-image-wrapper input').val('');
			$('.default-image-wrapper img').remove();
			$('.default-image-wrapper').addClass('empty').addClass('dashicons');
			ChangeDefaultImageButton.text( myCREDBadge.setimage );
			$(this).addClass('hidden');

		});

	});

});