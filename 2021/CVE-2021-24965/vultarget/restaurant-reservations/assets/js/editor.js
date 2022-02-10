/**
 * Custom fields editor for Custom Fields for Restaurant Reservations
 */
var cffrtb_editor = cffrtb_editor || {};

/**
 * Initialize the editor object after jQuery has loaded
 */
jQuery(document).ready(function ($) {

	/**
	 * jQuery reference for editor panel
	 */
	cffrtb_editor.el = $( '#cffrtb-editor' );

	/**
	 * Show the error modal
	 */
	cffrtb_editor.show_error = function( msg ) {

		var rtb_error_modal = $( '#rtb-error-modal ' );

		rtb_error_modal.find( '.rtb-error-msg' ).html( msg );
		rtb_error_modal.addClass( 'is-visible' );

		$(document).keyup( function(e) {
			if ( e.which == '27' ) {
				cffrtb_editor.hide_error( rtb_error_modal );
			}
		});

		rtb_error_modal.click( function(e) {
			if ( $(e.target).is( rtb_error_modal ) || $(e.target).is( rtb_error_modal.find( 'a.button' ) ) ) {

				e.stopPropagation();
				e.preventDefault();

				cffrtb_editor.hide_error( rtb_error_modal );
			}
		});
	};

	/**
	 * Hide the error modal
	 */
	cffrtb_editor.hide_error = function( el ) {
		el.removeClass( 'is-visible' );
		el.off();
	};

});

/**
 * Initialize the field editor after jQuery has loaded
 */
jQuery(document).ready(function ($) {

	/**
	 * Manage the field editor
	 */
	cffrtb_editor.editor = {

		el:			$( '#cffrtb-field-editor' ),

		option_el:	$( '#cffrtb-field-editor-option' ),

		init:		function() {

			// Store common form references
			this.form = {
				el:			this.el.find( '#cffrtb-field-editor-form' ),
				id:			this.el.find( 'input[name="id"]' ),
				type:		this.el.find( 'input[name="type"]' ),
				subtype:	this.el.find( 'input[name="subtype"]' ),
			};

			// Show field editor option modal
			$( '.add-field' ).click( function(e) {
				e.stopPropagation();
				e.preventDefault();

				cffrtb_editor.editor.show_option();
			});

			// Register click events on options modal
			this.option_el.click( function(e) {
				e.stopPropagation();
				e.preventDefault();

				var target = $( e.target );

				if ( target.hasClass( 'field' ) ) {
					cffrtb_editor.editor.hide_option();
					cffrtb_editor.editor.show_editor();

				} else if ( target.hasClass( 'fieldset' ) ) {
					cffrtb_editor.editor.hide_option();
					cffrtb_editor.editor.show_editor( 'fieldset' );

				} else if ( target.is( cffrtb_editor.editor.option_el ) ) {
					cffrtb_editor.editor.hide_option();
				}
			});

			// Close field editor modal when background is clicked
			this.el.click( function(e) {
				if ( $( e.target ).is( cffrtb_editor.editor.el ) ) {
					cffrtb_editor.editor.hide_editor();
				}
			});

			// Close field editor modal when ESC is keyed
			$(document).keyup( function(e) {
				if ( e.which == '27' ) {
					cffrtb_editor.editor.hide_editor();
				}
			});

			// Form actions
			this.form.el.find( '> .actions' ).on( 'click', function(e) {

				e.stopPropagation();
				e.preventDefault();

				var target = $( e.target );

				// Exit early if the actions are disabled
				if ( typeof target.attr( 'disabled' ) !== 'undefined' ) {
					return;
				}

				// Save field
				if ( target.hasClass( 'save' ) ) {
					cffrtb_editor.editor.save_field();

				// Cancel and close editor
				} else if ( target.hasClass( 'cancel' ) ) {
					cffrtb_editor.editor.hide_editor();
				}
			});

			// Field type selections
			this.form.el.find( '.type .selector' ).on( 'click', function(e) {

				var target = $( e.target );

				if ( target.get(0).tagName != 'A' ) {
					return;
				}

				var level = target.parent().parent();

				if ( target.parent().parent().hasClass( 'types' ) ) {
					cffrtb_editor.editor.set_type( target.data( 'type' ), target );

				} else {
					cffrtb_editor.editor.set_subtype( target.data( 'subtype' ), target );
				}

			});

			// Add an option
			this.el.find( '.settings-panel.options .add a' ).on( 'click', function(e) {
				e.stopPropagation();
				e.preventDefault();
				cffrtb_editor.editor.add_option();
			});

			// Add an option with ENTER key
			this.get_add_option_el().keyup( function(e) {
				if ( e.which == '13' ) {
					e.stopPropagation();
					e.preventDefault();

					cffrtb_editor.editor.add_option();
				}
			});

			// Remove an option
			this.get_options_list_el().on( 'click', function(e) {
				e.stopPropagation();
				e.preventDefault();

				var target = $( e.target );

				if( target.is( 'a, a .dashicons' ) ) {
					cffrtb_editor.editor.remove_option( target.closest( 'li' ) );
				}
			});

			// Make the option list sortable
			this.get_options_list_el().sortable({
				placeholder: 'cffrtb-editor-options-placeholder',
				delay: 250
			});

		},

		/**
		 * Get the add option input element
		 */
		get_add_option_el:	function() {

			if ( typeof this.form.add_option == 'undefined' ) {
				this.form.add_option = this.el.find( '.settings-panel.options .add input' );
			}

			return this.form.add_option;
		},

		/**
		 * Get the options list element
		 */
		get_options_list_el:	function() {

			if ( typeof this.form.options_list == 'undefined' ) {
				this.form.options_list = this.el.find( '.settings-panel.options .options' );
			}

			return this.form.options_list;
		},

		/**
		 * Update the editor values with a new field object
		 */
		update_editor_values:	function( field ) {

			this.form.id.val( field.ID );
			this.set_type( field.type );
			this.set_subtype( field.subtype );
			this.form.el.find( 'input[name="title"]' ).val( field.title );

			if ( field.required ) {
				this.form.el.find( '.required input' ).attr( 'checked', 'checked' );
			}

			if ( field.options && Object.keys( field.options ).length ) {
				var options = '';
				for( var i in field.options ) {
					if ( field.options[i].disabled ) {
						continue;
					}
					options += '<li data-id="' + field.options[i].id + '"><a href="#"><span class="dashicons dashicons-dismiss"></span></a> <span class="value">' + field.options[i].value + '</span></li>';
				}
				this.form.el.find( '.settings-panel.options .options' ).html( options );
			}

			this.el.trigger( 'cffrtb_update_editor_values', field );
		},

		/**
		 * Show the field/fieldset type selection before opening the editor
		 */
		show_option:	function() {

			this.option_el.addClass( 'is-visible' );
			$( 'body' ).addClass( 'rtb-hide-body-scroll' );

		},

		/**
		 * Hide the field/fieldset type selection
		 */
		hide_option:	function() {

			this.option_el.removeClass( 'is-visible' );
			$( 'body' ).removeClass( 'rtb-hide-body-scroll' );

		},

		/**
		 * Show the editor
		 */
		show_editor:	function( mode ) {

			if ( mode === 'edit' ) {
				this.form.el.find( '> .title > h2' ).html( cffrtb_editor.strings.editor_edit_field );
				this.form.el.find( '.actions a.save' ).html( cffrtb_editor.strings.editor_save_field );
			} else if ( mode == 'fieldset' ) {
				this.form.el.find( '> .title > h2' ).html( cffrtb_editor.strings.editor_add_fieldset );
				this.form.el.find( '.actions a.save' ).addClass( 'fieldset' ).html( cffrtb_editor.strings.editor_save_fieldset );
				this.form.el.addClass( mode );
				this.form.type.val( 'fieldset' );
				this.form.subtype.val( 'fieldset' );
			} else {
				this.form.el.find( '> .title > h2' ).html( cffrtb_editor.strings.editor_add_field );
				this.form.el.find( '.actions a.save' ).html( cffrtb_editor.strings.editor_add_field );
			}

			this.el.addClass( 'is-visible' );
			$( 'body' ).addClass( 'rtb-hide-body-scroll' );
		},

		/**
		 * Hide the editor
		 */
		hide_editor:	function() {
			this.el.removeClass( 'is-visible' );
			this.option_el.removeClass( 'is-hidden' );
			this.form.el.addClass( 'is-hidden' );
			this.form.el.removeClass( 'fieldset' );
			this.form.el.find( '.actions a.save' ).removeClass( 'fieldset' );
			$( 'body' ).removeClass( 'rtb-hide-body-scroll' );
			this.form.el.find( 'input, select, textarea' ).not( 'input[type="checkbox"]' ).val( '' );
			this.form.el.find( 'input[type="checkbox"]' ).removeAttr( 'checked' );
			this.get_options_list_el().empty();
			this.set_type( cffrtb_editor.default_type );
			this.set_subtype( cffrtb_editor.default_subtype );
		},

		/**
		 * Set the type and select the new subtype
		 */
		set_type:	function( type, el ) {

			if ( type == this.form.type.val() ) {
				return;
			}

			// Set the value
			this.form.type.val( type );

			// Remove the `current` class from the selection
			this.el.find( '.type .types a' ).removeClass( 'current' );

			// Add the `current` class to this selection
			if ( typeof el == 'undefined' ) {
				el = this.el.find( '.type .types .' + type );
			}
			el.addClass( 'current' );

			// Show the settings if they exist
			this.el.find( '.settings-panel' ).each( function() {
				if ( $(this).hasClass( type ) ) {
					$(this).addClass( 'current' );
				} else {
					$(this).removeClass( 'current' );
				}
			});

			// Show the subtype list
			this.el.find( '.type .subtypes' ).each( function() {
				if ( $(this).hasClass( type ) ) {
					$(this).addClass( 'current' );
				} else {
					$(this).removeClass( 'current' );
				}
			});

			// Trigger a click on the first subtype for this type
			this.el.find( '.subtypes.' + type + ' li' ).first().find( 'a' ).trigger( 'click' );
		},

		/**
		 * Select a subtype
		 */
		set_subtype:	function( subtype, el ) {

			if ( subtype == this.form.subtype.val() ) {
				return;
			}

			// Set the value
			this.form.subtype.val( subtype );

			// Remove the `current` class from the selection
			this.el.find( '.type .subtypes a' ).removeClass( 'current' );

			// Apply the `current` class to the right subtype
			if ( typeof el == 'undefined' ) {
				el = this.el.find( '.type .subtypes .' + subtype );
			}
			el.addClass( 'current' );

		},

		/**
		 * Add an option to the list
		 */
		add_option:		function() {
			var list = this.get_options_list_el();
			var option = this.get_add_option_el();

			this.get_options_list_el().append( '<li><a href="#"><span class="dashicons dashicons-dismiss"></span></a> <span class="value">' + this.get_add_option_el().val() + '</span></li>' );
			this.get_add_option_el().val( '' );

			// Scroll the options list if they have more than 10
			if ( this.get_options_list_el().find( 'li' ).length > 10 ) {
				this.get_options_list_el().addClass( 'scroll' );
			}
		},

		/**
		 * Remove an option from the list
		 */
		remove_option:	function( el ) {

			el.fadeOut( '200', function() {
				$(this).remove();

				// Remove scrollbar if the options list is less than 10 options
				if ( cffrtb_editor.editor.get_options_list_el().find( 'li' ).length <= 10 ) {
					cffrtb_editor.editor.get_options_list_el().removeClass( 'scroll' );
				}
			});
		},

		/**
		 * Disable actions
		 */
		disable_actions: function() {
			this.form.el.find( '.actions' ).addClass( 'working' ).find( '.save, .cancel' ).attr( 'disabled', 'disabled' );
		},

		/**
		 * Enable actions
		 */
		enable_actions: function() {
			this.form.el.find( '.actions' ).removeClass( 'working' ).find( '.save, .cancel' ).removeAttr( 'disabled' );
		},

		/**
		 * Load field
		 */
		load_field:		function( item_slug ) {

			// Don't trigger if we're already saving
			if ( cffrtb_editor.list.get_title_el( item_slug ).hasClass( 'saving' ) ) {
				return;
			}

			var id = cffrtb_editor.list.items[item_slug].el.data( 'id' );
			if ( typeof id == 'undefined' ) {
				return;
			}

			cffrtb_editor.list.disable_sorting();
			cffrtb_editor.list.get_title_el( item_slug ).addClass( 'saving' );
			cffrtb_editor.list.get_title_el( item_slug ).find( '.view .controls' ).prepend( '<span class="load-spinner"></span>' );

			var params = {};

			params.action = 'cffrtb-load-field';
			params.nonce = cffrtb_editor.ajax_nonce;
			params.ID = id;

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {
					cffrtb_editor.editor.update_editor_values( r.data.field );
					cffrtb_editor.editor.show_editor( 'edit' );

				} else {
					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						cffrtb_editor.show_error( r.data.msg );
					}
				}

				// Reset status
				cffrtb_editor.list.enable_sorting();
				cffrtb_editor.list.get_title_el( item_slug ).removeClass( 'saving' );
				cffrtb_editor.list.get_title_el( item_slug ).find( '.view .controls .load-spinner' ).fadeOut( 400, function() { $(this).remove(); });
			});

		},

		/**
		 * Save a field
		 */
		save_field:		function() {

			this.disable_actions();

			var params = {};

			params.action = 'cffrtb-save-field';
			params.nonce = cffrtb_editor.ajax_nonce;
			params.request = 'save_field';
			params.field = {
				ID:			this.form.id.val(),
				type:		this.form.type.val(),
				subtype:	this.form.subtype.val(),
				title:		this.form.el.find( 'input[name="title"]' ).val(),
			};

			if ( this.form.el.find( 'input[name="required"]' ).is( ':checked' ) ) {
				params.field.required = 1;
			} else {
				params.field.required = 0;
			}

			if ( params.field.type == 'options' ) {
				params.field.options = {};
				this.get_options_list_el().find( 'li' ).each( function(i) {

					var id = $(this).data( 'id' );
					if ( typeof id == 'undefined' ) {
						id = 'new-' + Math.random().toString(36).substring(7);
					}

					params.field.options[i] = {
						id: id,
						value: $(this).find( '.value' ).html(),
						order: i
					};
				});
			}

			// Perform some basic validation checks here
			var errors = this.validate_field( params.field );
			if ( typeof errors !== 'undefined' ) {
				cffrtb_editor.show_error( errors );
				this.enable_actions();

				return;
			}

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {
					cffrtb_editor.editor.hide_editor();
					cffrtb_editor.list.add_item( r.data.field, r.data.ID, r.data.is_new_field, r.data.type );

				} else {
					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						console.log( r.data );
						cffrtb_editor.show_error( r.data.msg );
					}
				}

				cffrtb_editor.editor.enable_actions();
			});
		},

		/**
		 * Some quick validation on the field data before sending it off
		 */
		validate_field:		function( field ) {

			if ( field.title.length === 0 || !field.title.trim() ) {
				return cffrtb_editor.strings.field_missing_title;
			}

			if ( field.type == 'options' && field.options.length === 0 ) {
				return cffrtb_editor.strings.field_missing_options;
			}
		}
	};

	/**
	 * Initialize the editor
	 */
	cffrtb_editor.editor.init();

});

/**
 * Initialize the fields list object after jQuery has loaded
 */
jQuery(document).ready(function ($) {

	/**
	 * Manage the list of fields
	 */
	cffrtb_editor.list = {

		// list element
		el:				cffrtb_editor.el.find( '#cffrtb-list' ),

		// fieldsets and fields.
		// just storing jQuery references to prevent duplicate lookups
		items:			{},

		// flag to disable events during view->edit transition
		in_transition:	false,

		// disabled fields list
		disabled_el:	cffrtb_editor.el.find( '#cffrtb-disabled' ),

		init:		function() {

			// Store jQuery references to prevent duplicate lookups
			this.el.find( '.fieldset, .field' ).each( function() {
				cffrtb_editor.list.items[ $(this).data( 'slug' ) ] = {
					el:	$(this)
				};
			});

			// Clear pre-existing listeners
			this.el.off( 'click keyup' );

			// Register click listeners
			this.el.on( 'click', function( e ) {

				e.stopPropagation();
				e.preventDefault();

				var target = $( e.target );

				// Any click outside one of the field titles
				if ( !target.hasClass( 'title' ) && !target.parents().hasClass( 'title' ) ) {
					cffrtb_editor.list.save_all();
					return;
				}

				var item_slug = target.parents( '.fieldset, .field' ).first().data( 'slug' );

				// Open options panel
				if ( cffrtb_editor.list.is_target( target, 'options' ) ) {
					cffrtb_editor.list.save_all();
					cffrtb_editor.editor.load_field( item_slug );

				// Delete field
				} else if ( cffrtb_editor.list.is_target( target, 'delete' ) ) {
					cffrtb_editor.list.save_all();
					cffrtb_editor.list.delete_item( item_slug );

				// Open label editing panel
				} else if ( !cffrtb_editor.list.is_editing( item_slug ) ) {
					cffrtb_editor.list.show_edit( item_slug );

				// Save label
				} else if ( cffrtb_editor.list.is_target( target, 'save' ) ) {
					cffrtb_editor.list.save_label( item_slug );

				// Give focus to input when editing panel is active
				} else if ( target.hasClass( 'edit' ) ) {
					cffrtb_editor.list.set_focus( item_slug );
				}

			});

			// Save label with ENTER key
			this.el.keyup( function(e) {

				if ( !cffrtb_editor.list.in_transition && e.which == '13' ) {

					var target = $( e.target );

					if ( target.is( 'input:focus' ) ) {

						e.stopPropagation();
						e.preventDefault();

						cffrtb_editor.list.save_label( target.parents( '.fieldset, .field' ).first().data( 'slug' ) );
					}
				}
			});

			// Make the list sortable
			this.el.sortable({
				placeholder: 'cffrtb-list-placeholder',
				delay: 250,
				update: this.sorting_complete
			});
			this.el.find( '.fieldset ul' ).sortable({
				placeholder: 'cffrtb-list-placeholder',
				connectWith: '#cffrtb-list .fieldset ul',
				delay: 250,
				update: this.sorting_complete
			});

			// Clear pre-existing listeners
			this.disabled_el.off( 'click' );

			// Register click events on disabled fields
			this.disabled_el.on( 'click', function(e) {

				e.stopPropagation();
				e.preventDefault();

				var target = $( e.target );

				// Restore field
				if ( cffrtb_editor.list.is_target( target, 'enable' ) ) {
					cffrtb_editor.list.enable_item( target.parents( '.fieldset, .field' ).first() );

				// Open learn more text
				} else if ( cffrtb_editor.list.is_target( target, 'learn-more' ) ) {
					cffrtb_editor.list.disabled_el.find( '.reset .description' ).addClass( 'is-visible' );

				// Revert to default
				} else if ( cffrtb_editor.list.is_target( target, 'reset-all' ) ) {
					cffrtb_editor.list.reset_all();
				}
			});

		},

		/**
		 * Is the field being edited?
		 */
		is_editing: function( item_slug ) {
			return this.get_title_el( item_slug ).hasClass( 'editing' );
		},

		/**
		 * Is the click target opening the field options?
		 */
		is_target: function( target, match ) {
			return target.hasClass( match ) || target.parents().hasClass( match );
		},

		/**
		 * Get the title element for an item
		 */
		get_title_el: function( item_slug ) {

			if ( typeof this.items[item_slug].title == 'undefined' ) {
				this.items[item_slug].title = this.items[item_slug].el.find( '> .title' );
			}

			return this.items[item_slug].title;
		},

		/**
		 * Get the edit element for an item
		 */
		get_edit_el: function( item_slug ) {

			if ( typeof this.items[item_slug].title == 'undefined' ) {
				this.items[item_slug].edit = this.items[item_slug].el.find( '> .title .edit' );
			}

			return this.items[item_slug].edit;
		},

		/**
		 * Get the value of the input field
		 */
		get_input: function( item_slug ) {
			return this.get_input_el( item_slug ).val();
		},

		/**
		 * Get the input element of a field
		 */
		get_input_el: function( item_slug ) {

			if ( typeof this.items[item_slug].input == 'undefined' ) {
				this.items[item_slug].input = this.items[item_slug].el.find( '> .title .edit input' );
			}

			return this.items[item_slug].input;
		},

		/**
		 * Get the fieldset slug an item is attached to
		 */
		get_fieldset: function( item_slug ) {

			if ( this.items[item_slug].el.hasClass( 'fieldset' ) ) {
				return item_slug;
			} else {
				return this.items[item_slug].el.parents( '.fieldset' ).first().data( 'slug' );
			}
		},

		/**
		 * Update the value of the label in the view
		 */
		update_view: function( item_slug ) {

			if ( typeof this.items[item_slug].view_value == 'undefined' ) {
				this.items[item_slug].view_value = this.items[item_slug].el.find( '> .title .view .value' );
			}

			this.items[item_slug].view_value.html( this.get_input( item_slug ) );
		},

		/**
		 * Open an item's edit mode
		 */
		show_edit:	function( item_slug ) {

			// Save and close any other labels being edited
			this.save_all();

			// Set transition flag and timer
			this.in_transition = true;
			setTimeout( this.clear_transition_flag, 600 );

			// Open edit mode for this item
			this.enable_tabbing( item_slug );
			this.get_title_el( item_slug ).addClass( 'editing' );
			this.set_focus( item_slug );
		},

		/**
		 * Focus and select the input field
		 */
		set_focus:	function( item_slug ) {
			this.get_input_el( item_slug ).focus().select();
		},

		/**
		 * Clear the transition flag used when opening the editing panel
		 */
		clear_transition_flag: function() {
			cffrtb_editor.list.in_transition = false;
		},

		/**
		 * Return an item to view mode
		 */
		show_view:	function( item_slug ) {
			this.get_title_el( item_slug ).removeClass( 'editing' );
			this.disable_tabbing( item_slug );
			this.set_focus( item_slug );
		},

		/**
		 * Disable tabbing through a hidden edit interface
		 */
		disable_tabbing: function( item_slug ) {
			this.get_title_el( item_slug ).find(  '> .edit input, > .edit .save' ).attr( 'tabindex', '-1' );
		},

		/**
		 * Enable tabbing through a hidden edit interface
		 */
		enable_tabbing: function( item_slug ) {
			this.get_title_el( item_slug ).find(  '> .edit input, > .edit .save' ).removeAttr( 'tabindex' );
		},

		/**
		 * Disable drag and drop sorting
		 */
		disable_sorting: function() {
			this.el.sortable( 'option', 'disabled', true );
			this.el.find( '.fieldset ul' ).sortable( 'option', 'disabled', true );
		},

		/**
		 * Enable drag and drop sorting
		 */
		enable_sorting: function() {
			this.el.sortable( 'option', 'disabled', false );
			this.el.find( '.fieldset ul' ).sortable( 'option', 'disabled', false );
		},

		/**
		 * Save and close any fields being edited
		 */
		save_all: function() {
			this.el.find( '.title.editing' ).each( function() {
				cffrtb_editor.list.save_label( $(this ).parent().data( 'slug' ) );
			});
		},

		/**
		 * Save an item's label
		 */
		save_label:	function( item_slug ) {

			// Don't trigger if we're already saving
			if ( this.get_title_el( item_slug ).hasClass( 'saving' ) ) {
				return;
			}

			// Indicate status
			this.get_title_el( item_slug ).addClass( 'saving' );
			this.get_input_el( item_slug ).attr( 'disabled', 'disabled' );

			var params = {};

			params.action = 'cffrtb-save-field';
			params.nonce = cffrtb_editor.ajax_nonce;
			params.request = 'save_label';
			params.field = {
				slug:		item_slug,
				title:		this.get_input( item_slug ),
				fieldset:	this.get_fieldset( item_slug )
			};

			if( this.items[item_slug].el.data( 'id' ) ) {
				params.field.ID = this.items[item_slug].el.data( 'id' );
			}

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {
					cffrtb_editor.list.update_view( item_slug );
					cffrtb_editor.list.show_view( item_slug );

				} else {
					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						console.log( r.data );
						cffrtb_editor.show_error( r.data.msg );
					}
				}

				// Reset status
				cffrtb_editor.list.get_title_el( item_slug ).removeClass( 'saving' );
				cffrtb_editor.list.get_input_el( item_slug ).removeAttr( 'disabled' );
			});
		},

		/**
		 * Sorting complete
		 */
		sorting_complete:	function( event, ui ) {
			cffrtb_editor.list.save_sort( $( ui.item.context ) );
		},

		/**
		 * Save the sort order after it's been changed
		 */
		save_sort: function( target ) {

			if ( cffrtb_editor.el.hasClass( 'saving-order' ) ) {
				return;
			}

			// Indicate status
			cffrtb_editor.list.disable_sorting();
			cffrtb_editor.el.addClass( 'saving-order' );
			target.find( '> .title .view .controls' ).prepend( '<span class="load-spinner"></span>' );

			var params = {};

			params.action = 'cffrtb-save-order';
			params.nonce = cffrtb_editor.ajax_nonce;
			params.order = [];

			var i = 0;
			cffrtb_editor.list.el.find( '> li' ).each( function() {
				params.order.push( cffrtb_editor.list.get_item_order_obj( $(this), i ) );
				i++;

				$(this).find( '> ul > li' ).each( function() {
					params.order.push( cffrtb_editor.list.get_item_order_obj( $(this), i ) );
					i++;
				});
			});

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {

				} else {
					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						console.log( r.data );
						cffrtb_editor.show_error( r.data.msg );
					}
				}

				// Reset status
				cffrtb_editor.el.removeClass( 'saving-order' );
				target.find( '> .title .view .controls .load-spinner' ).fadeOut( 400, function() { $(this).remove(); });
				cffrtb_editor.list.enable_sorting();
			});
		},

		/**
		 * Get an object with an elements order and slug/id data
		 */
		get_item_order_obj: function( el, i ) {

			var item = {};

			if ( el.data( 'slug' ) ) {
				item.slug = el.data( 'slug' );
			}

			if ( el.data( 'id' ) ) {
				item.ID = el.data( 'id' );
			}

			item.fieldset = cffrtb_editor.list.get_fieldset( item.slug );

			item.order = i;

			return item;
		},

		/**
		 * Add an item
		 *
		 * Expects to receive an HTML string for the new <li> element
		 */
		add_item:		function( html, ID, is_new_field, type ) {

			if ( is_new_field ) {
				if ( type == 'fieldset' ) {
					this.el.find( '> .fieldset' ).last().after( html );
				} else {
					this.el.find( '> .fieldset' ).last().find( '> .fields' ).append( html );
				}
			} else {
				this.el.find( '.fieldset, .field' ).each( function() {
					if ( $(this).data( 'id' ) == ID ) {
						$(this).html( html ).hide().fadeIn();
					}
				});
			}

			this.init();
			this.save_sort( this.el.find( '.fieldset, .field' ).last() );
		},

		/**
		 * Delete or disable an item from the list of fields
		 */
		delete_item:	function( item_slug ) {

			if ( cffrtb_editor.el.hasClass( 'deleting' ) ) {
				return;
			}

			var item_el = this.items[item_slug].el;
			var is_fieldset = item_el.hasClass( 'fieldset' );

			// Indicate status
			cffrtb_editor.list.disable_sorting();
			cffrtb_editor.el.addClass( 'deleting' );
			item_el.find( '> .title .view .controls' ).prepend( '<span class="load-spinner"></span>' );

			// Can't remove a fieldset with fields
			if ( is_fieldset && item_el.find( '.field' ).length ) {
				cffrtb_editor.show_error( cffrtb_editor.strings.fieldset_not_empty );
				item_el.find( '> .title .view .controls .load-spinner' ).fadeOut( 400, function() { $(this).remove(); });
				cffrtb_editor.el.removeClass( 'deleting' );
				cffrtb_editor.list.enable_sorting();
				return;
			}

			var id = item_el.data( 'id' );

			var params = {};

			params.action = 'cffrtb-delete-field';
			params.nonce = cffrtb_editor.ajax_nonce;

			if ( id ) {
				params.ID = id;
			} else {
				params.slug = item_slug;
				params.fieldset = this.get_fieldset( item_slug );
			}

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {

					item_el.fadeOut( 400, function() { $(this).remove(); });

					if ( typeof r.data !== 'undefined' && typeof r.data.field !== 'undefined' ) {

						if ( !cffrtb_editor.list.disabled_el.find( '.fields' ).length ) {
							cffrtb_editor.list.disabled_el.find( '.no-disabled-fields' ).remove();
							cffrtb_editor.list.disabled_el.find( '.reset' ).before( '<ul class="fields"></ul>' );
						}

						cffrtb_editor.list.disabled_el.find( '.fields' ).append( r.data.field );

						cffrtb_editor.list.disabled_el.find( '.reset' ).addClass( 'is-visible' );
					}

				} else {

					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						console.log( r.data );
						cffrtb_editor.show_error( r.data.msg );
					}

					item_el.find( '> .title .view .controls .load-spinner' ).fadeOut( 400, function() { $(this).remove(); });
				}

				// Reset status
				cffrtb_editor.el.removeClass( 'deleting' );
				cffrtb_editor.list.enable_sorting();
			});
		},

		/**
		 * Enable a field that has been disabled
		 */
		enable_item:		function( field ) {

			if ( field.hasClass( 'enabling' ) ) {
				return;
			}

			// Indicate status
			field.addClass( 'enabling' );
			field.find( '> .title .view .controls' ).prepend( '<span class="load-spinner"></span>' );

			var params = {};

			params.action = 'cffrtb-enable-field';
			params.nonce = cffrtb_editor.ajax_nonce;

			params.slug = field.data( 'slug' );

			if ( field.hasClass( 'fieldset' ) ) {
				params.type = 'fieldset';
			} else {
				params.type = 'field';
			}

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {
					cffrtb_editor.list.add_item( r.data.field, 0, true );
					field.fadeOut( 400, function() { $(this).remove(); });

				} else {

					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						console.log( r.data );
						cffrtb_editor.show_error( r.data.msg );
					}

					field.find( '> .title .view .controls .load-spinner' ).fadeOut( 400, function() { $(this).remove(); });
					field.removeClass( 'enabling' );
				}

				cffrtb_editor.list.enable_sorting();
			});
		},

		/**
		 * Reset all modifications and custom fields created by the
		 * plugin
		 */
		reset_all:			function() {

			this.disabled_el.find( '.reset-all' ).attr( 'disabled', 'disabled' );

			if ( !window.confirm( cffrtb_editor.strings.confirm_reset_all ) ) {
				this.disabled_el.find( '.reset-all' ).removeAttr( 'disabled' );
				return;
			}

			var params = {};

			params.action = 'cffrtb-reset-all';
			params.nonce = cffrtb_editor.ajax_nonce;

			var data = $.param( params );

			$.post( ajaxurl, data, function( r ) {

				if ( r.success ) {

					// Refresh the page so that the new details are visible
					window.location.reload();

				} else {

					if ( typeof r.data === 'undefined' || typeof r.data.error === 'undefined' ) {
						cffrtb_editor.show_error( cffrtb_editor.strings.unknown_error );
					} else {
						console.log( r.data );
						cffrtb_editor.show_error( r.data.msg );
					}

					cffrtb_editor.list.disabled_el.find( '.reset-all' ).removeAttr( 'disabled' );
				}
			});
		}
	};

	/**
	 * Initialize the list
	 */
	cffrtb_editor.list.init();

});

/**
 * Display the pointer admin help tips
 */
jQuery(document).ready(function ($) {

	if ( typeof cffrtb_editor.pointers == 'undefined' || !cffrtb_editor.pointers.length ) {
		return;
	}

	function cffrtb_pointer_show( pointers ) {

		var pointer = pointers.splice( 0, 1 )[0];
		var options = $.extend( pointer.options, {
			close: function() {
				$.post( ajaxurl, {
					pointer: pointer.id,
					action: 'dismiss-wp-pointer'
				}, function() {
					if ( pointers.length ) {
						cffrtb_pointer_show( pointers );
					}
				});
			}
		});

		var target = $( pointer.target );

		if ( !target ) {
			return;
		}

		target.first().pointer( options ).pointer( 'open' );

		$( 'html, body' ).animate({
			scrollTop: target.offset().top - 100
		}, 500);
	}

	cffrtb_pointer_show( cffrtb_editor.pointers );

});
