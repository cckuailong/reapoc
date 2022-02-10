(function($){

    PPFields = {

        _cbVal: [],
        _preview: [],

        /**
		 * Initializes the events.
		 *
		 * @since 1.0
		 * @access private
		 * @method _init
		 */
        _init: function()
        {
            FLBuilder.addHook('settings-form-init', function() {
                PPFields._initRadioFields();
                PPFields._initCheckboxFields();
                PPFields._initToggleFields();
                PPFields._initSwitchFields();
                PPFields._initMultitextFields();
                PPFields._initDatepickerFields();
                PPFields._settingsCloseEsc();
            });
            PPFields._bindEvents();
        },

        /**
		 * Binds the events to fields.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bindEvents
		 */
        _bindEvents: function()
        {
            /* Radio Input Fields */
			$('body').delegate('.fl-builder-settings-fields .pp-field-radio', 'change', PPFields._settingsRadioChanged);
			$('body').delegate('.fl-builder-settings-fields .pp-field-checkbox', 'change', PPFields._settingsCheckboxChanged);
			$('body').delegate('.fl-builder-settings-fields .pp-field-toggle', 'change', PPFields._settingsToggleChanged);
            $('body').delegate('.fl-builder-settings-fields .pp-field-multitext', 'keyup', PPFields._settingsMultitextChanged);
            $('body').delegate('.fl-builder-settings-fields .pp-multitext.fa-desktop', 'click', PPFields._settingsMultitextToggle);
            $('body').delegate('.fl-builder-settings-fields .pp-multitext-wrap .pp-multitext-responsive-toggle span', 'click', PPFields._settingsMultitextToggleResponsive);
            $('body').delegate('.fl-builder-settings-fields .pp-switch-button', 'click', PPFields._settingsSwitchClick);
            $('body').delegate('.fl-builder-settings-fields .pp-field-switch', 'change', PPFields._settingsSwitchChanged);

        },

        /* Radio Input Fields
		----------------------------------------------------------*/

        /**
		 * Initializes radio input fields for a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initRadioFields
		 */
        _initRadioFields: function()
        {
            $('.fl-builder-settings:visible').find('.fl-builder-settings-fields input[type="radio"]').trigger('change');
            $('.fl-builder-settings:visible').find('.fl-builder-settings-fields input[type="radio"]:checked').parent().addClass('selected');
        },

        /**
		 * Callback for when a settings form radio input has been changed.
		 * If toggle data is present, other fields will be toggled
		 * when this field changes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsRadioChanged
		 */
        _settingsRadioChanged: function()
        {
            var input   = $(this),
                control = input.attr('name'),
                field   = $('input[data-name="'+control+'"]'),
                toggle  = field.attr('data-toggle'),
                hide    = field.attr('data-hide'),
                trigger = field.attr('data-trigger'),
                val     = field.val(),
                i       = 0,
                k       = 0;

            // Add selected class to the label.
            $('.pp-label.'+control).removeClass('selected');
            if(input.is(':checked')) {
                input.parent().addClass('selected');
                field.val(input.val());
                val = input.val();
            }

            // TOGGLE sections, fields or tabs.
			if(typeof toggle !== 'undefined') {

				toggle = JSON.parse(toggle);

				for(i in toggle) {
					PPFields._settingsFieldToggle(toggle[i].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[i].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[i].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}

				if(typeof toggle[val] !== 'undefined') {
					PPFields._settingsFieldToggle(toggle[val].fields, 'show', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[val].sections, 'show', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[val].tabs, 'show', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}

            // HIDE sections, fields or tabs.
			if(typeof hide !== 'undefined') {

				hide = JSON.parse(hide);

				if(typeof hide[val] !== 'undefined') {
					PPFields._settingsFieldToggle(hide[val].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(hide[val].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(hide[val].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}

            // TRIGGER select inputs.
			if(typeof trigger !== 'undefined') {

				trigger = JSON.parse(trigger);

				if(typeof trigger[val] !== 'undefined') {
					if(typeof trigger[val].fields !== 'undefined') {
						for(i = 0; i < trigger[val].fields.length; i++) {
							$('#fl-field-' + trigger[val].fields[i]).find('input[type="radio"]').trigger('change');
						}
					}
				}
			}
        },

        /* Checkbox Input Fields
		----------------------------------------------------------*/

        /**
		 * Initializes checkbox input fields for a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initCheckboxFields
		 */
        _initCheckboxFields: function()
        {
            $('.fl-builder-settings:visible').find('.fl-builder-settings-fields input[type="checkbox"]').trigger('change');
            $('.fl-builder-settings:visible').find('.fl-builder-settings-fields input[type="checkbox"]:checked').parent().addClass('selected');
        },

        /**
		 * Callback for when a settings form checkbox input has been changed.
		 * If toggle data is present, other fields will be toggled
		 * when this field changes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsCheckboxChanged
		 */
        _settingsCheckboxChanged: function()
        {
            var input    = $(this),
                control  = input.attr('data-name'),
                field    = $('input[name="'+control+'"]'),
                toggle   = field.attr('data-toggle'),
                hide     = field.attr('data-hide'),
                trigger  = field.attr('data-trigger'),
                val      = parseInt(input.val()),
                i        = 0,
                k        = 0;

            // Add or Remove selected class to the label and update values.
            if(input.is(':checked')) {
                input.parent().addClass('selected');
                PPFields._cbVal.push(val);
                field.val('['+PPFields._cbVal+']');
                val = PPFields._cbVal;
            } else {
                input.parent().removeClass('selected');
                PPFields._cbVal.splice(PPFields._cbVal.indexOf(val), 1);
                field.val('['+PPFields._cbVal+']');
                val = PPFields._cbVal;
            }

            // TOGGLE sections, fields or tabs.
			if(typeof toggle !== 'undefined') {

				toggle = JSON.parse(toggle);

				for(i in toggle) {
					PPFields._settingsFieldToggle(toggle[i].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[i].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[i].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}
                for(k = 0; k < val.length; k++) {
    				if(typeof toggle[val[k]] !== 'undefined') {
    					PPFields._settingsFieldToggle(toggle[val[k]].fields, 'show', '#fl-field-');
    					PPFields._settingsFieldToggle(toggle[val[k]].sections, 'show', '#fl-builder-settings-section-');
    					PPFields._settingsFieldToggle(toggle[val[k]].tabs, 'show', 'a[href*=fl-builder-settings-tab-', ']');
    				}
                }
			}

            // HIDE sections, fields or tabs.
			if(typeof hide !== 'undefined') {

				hide = JSON.parse(hide);

                for(k = 0; k < val.length; k++) {
    				if(typeof hide[k] !== 'undefined') {
    					PPFields._settingsFieldToggle(hide[val[k]].fields, 'hide', '#fl-field-');
    					PPFields._settingsFieldToggle(hide[val[k]].sections, 'hide', '#fl-builder-settings-section-');
    					PPFields._settingsFieldToggle(hide[val[k]].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
    				}
                }
			}

            // TRIGGER select inputs.
			if(typeof trigger !== 'undefined') {

				trigger = JSON.parse(trigger);

                for(k = 0; k < val.length; k++) {
    				if(typeof trigger[val[k]] !== 'undefined') {
    					if(typeof trigger[val[k]].fields !== 'undefined') {
    						for(i = 0; i < trigger[val[k]].fields.length; i++) {
    							$('#fl-field-' + trigger[val[k]].fields[i]).find('input[type="checkbox"]').trigger('change');
    						}
    					}
    				}
                }
			}
        },

        /* Toggle Input Fields
		----------------------------------------------------------*/

        /**
		 * Initializes checkbox input fields for a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initCheckboxFields
		 */
        _initToggleFields: function()
        {
            $('.fl-builder-settings:visible').find('.fl-builder-settings-fields .pp-toggle input[type="checkbox"]').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if($(this).val() === 'disabled') {
                    $(this).val('enabled').parent().removeClass('disabled').addClass('enabled');
                } else {
                    $(this).val('disabled').parent().removeClass('enabled').addClass('disabled');
                }
                $(this).trigger('change');
            }).trigger('change');
        },

        /**
		 * Callback for when a settings form toggle field has been changed.
		 * If toggle data is present, other fields will be toggled
		 * when this field changes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsToggleChanged
		 */
        _settingsToggleChanged: function()
        {
            var input   = $(this),
                toggle  = input.attr('data-toggle'),
                hide    = input.attr('data-hide'),
                trigger = input.attr('data-trigger'),
                val     = input.val(),
                i       = 0,
                k       = 0;

            input.prop('checked', true);

            // TOGGLE sections, fields or tabs.
			if(typeof toggle !== 'undefined') {

				toggle = JSON.parse(toggle);

				for(i in toggle) {
					PPFields._settingsFieldToggle(toggle[i].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[i].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[i].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}

				if(typeof toggle[val] !== 'undefined') {
					PPFields._settingsFieldToggle(toggle[val].fields, 'show', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[val].sections, 'show', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[val].tabs, 'show', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}

            // HIDE sections, fields or tabs.
			if(typeof hide !== 'undefined') {

				hide = JSON.parse(hide);

				if(typeof hide[val] !== 'undefined') {
					PPFields._settingsFieldToggle(hide[val].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(hide[val].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(hide[val].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}

            // TRIGGER select inputs.
			if(typeof trigger !== 'undefined') {

				trigger = JSON.parse(trigger);

				if(typeof trigger[val] !== 'undefined') {
					if(typeof trigger[val].fields !== 'undefined') {
						for(i = 0; i < trigger[val].fields.length; i++) {
							$('#fl-field-' + trigger[val].fields[i]).find('input[type="checkbox"]').trigger('change');
						}
					}
				}
			}
        },

        _initMultitextFields: function() {
            if ( typeof $.fn.tipTip === 'function' ) {
                $('.fl-builder-settings .pp-tip:not(.fa-desktop)').tipTip({defaultPosition: 'top'});
            }
        },

        /**
		 * Callback for when a settings form multitext field has been changed.
		 * If multitext preview data is present, style will be appended to body.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsMultitextChanged
		 */
        _settingsMultitextChanged: function()
        {
            var input   = $(this),
                preview = input.parent().attr('data-pp-preview');

            PPFields._initFieldCSSPreview( input.parent() );

			if(typeof preview !== 'undefined') {

				preview = JSON.parse(preview);

                if(typeof preview.selector !== 'undefined' && typeof preview.property !== 'undefined') {
                    var unit = typeof preview.unit !== 'undefined' ? preview.unit : '';
                    var css = '<style class="pp-module-preview">';
                    css += '.fl-node-' + FLBuilder.preview.nodeId + ' ' + preview.selector + ' { ' + preview.property + ': ' + input.val() + unit + '; } ';
                    css += '</style>';
                    $('body').append(css);
                }
            }

        },

        _settingsMultitextToggle: function(e) {
            if($(e.target).hasClass('pp-field-multitext')) {
                return;
            }
            $(this).siblings().toggle();
        },

        _settingsMultitextToggleResponsive: function(e)
        {
            var toggle  = $(e.target),
                target  = toggle.data('field-target'),
                mode    = target,
                field   = toggle.parents('.pp-multitext-wrap').find('.pp-multitext .pp-field-multitext-' + target);

            toggle.parents('.pp-multitext-wrap').find('.pp-multitext .pp-field-multitext').hide();
            field.show();

            if (toggle.hasClass('pp-multitext-default')) {
                toggle.parent().find('.pp-multitext-medium').show();
            }
            if (toggle.hasClass('pp-multitext-medium')) {
                toggle.parent().find('.pp-multitext-small').show();
            }
            if (toggle.hasClass('pp-multitext-small')) {
                toggle.parent().find('.pp-multitext-default').show();
            }
            toggle.hide();

            if (mode === 'small') {
                mode = 'responsive';
            }

            FLBuilderResponsiveEditing._switchToAndScroll(mode);
        },

        _settingsSwitchClick: function()
        {
            var val = $(this).data('value');
            var preview = $(this).parents('tr.fl-field').data('preview');

            $(this).parent().find('.pp-field-switch').val(val).trigger('change');
            $(this).parent().find('span').removeClass('pp-switch-active');
            $(this).addClass('pp-switch-active');
            if(typeof preview !== 'undefined') {
				if ( preview.type !== 'none' ) {
					if(preview.type !== 'css') {
						setTimeout(function() {
						   FLBuilder.preview.preview();
						}, 100);
					} else {
						PPFields._initFieldCSSPreview( $(this).parents('tr.fl-field') );
					}
				}
            }
        },

        _initSwitchFields: function() {
            $('.fl-builder-settings:visible').find('.fl-builder-settings-fields .pp-field-switch').trigger('change');
        },

        _disableSwitchFields: function(key, val) {
            $('.fl-builder-settings:visible').find('.fl-builder-col-settings .fl-field-'+key+' .pp-switch-button[data-value="'+val+'"]').addClass('pp-switch-disable');
        },

        /**
		 * Callback for when a settings form switch field has been changed.
		 * If switch preview data is present, style will be appended to body.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsSwitchChanged
		 */
        _settingsSwitchChanged: function()
        {
            var input   = $(this),
                toggle  = input.attr('data-toggle'),
                hide    = input.attr('data-hide'),
                trigger = input.attr('data-trigger'),
                val     = input.val(),
                i       = 0,
                k       = 0;

            // TOGGLE sections, fields or tabs.
			if(typeof toggle !== 'undefined') {

				toggle = JSON.parse(toggle);

				for(i in toggle) {
					PPFields._settingsFieldToggle(toggle[i].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[i].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[i].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}

				if(typeof toggle[val] !== 'undefined') {
					PPFields._settingsFieldToggle(toggle[val].fields, 'show', '#fl-field-');
					PPFields._settingsFieldToggle(toggle[val].sections, 'show', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(toggle[val].tabs, 'show', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}

			// HIDE sections, fields or tabs.
			if(typeof hide !== 'undefined') {

				hide = JSON.parse(hide);

				if(typeof hide[val] !== 'undefined') {
					PPFields._settingsFieldToggle(hide[val].fields, 'hide', '#fl-field-');
					PPFields._settingsFieldToggle(hide[val].sections, 'hide', '#fl-builder-settings-section-');
					PPFields._settingsFieldToggle(hide[val].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}

			// TRIGGER select inputs.
			if(typeof trigger !== 'undefined') {

				trigger = JSON.parse(trigger);

				if(typeof trigger[val] !== 'undefined') {
					if(typeof trigger[val].fields !== 'undefined') {
						for(i = 0; i < trigger[val].fields.length; i++) {
							$('#fl-field-' + trigger[val].fields[i]).find('select').trigger('change');
						}
					}
				}
			}

        },

        /**
		 * @since 1.0
		 * @access private
		 * @method _settingsFieldToggle
		 * @param {Array} inputArray
		 * @param {Function} func
		 * @param {String} prefix
		 * @param {String} suffix
		 */
		_settingsFieldToggle: function(inputArray, func, prefix, suffix)
		{
			var i = 0;

			suffix = 'undefined' == typeof suffix ? '' : suffix;

			if(typeof inputArray !== 'undefined') {
				for( ; i < inputArray.length; i++) {
					$(prefix + inputArray[i] + suffix)[func]();
				}
			}
		},

        /* Datepicker Input Fields
		----------------------------------------------------------*/

        /**
		 * Initializes datepicker input fields for a settings form.
		 *
		 * @since 1.2.4
		 * @access private
		 * @method _initDatepickerFields
		 */
        _initDatepickerFields: function()
        {
            $('body').delegate('.pp-field-datepicker', 'click', function(){
                var dateFormat = $(this).data('format');
                if (!$(this).hasClass("hasDatepicker")) {
                    $(this).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat : dateFormat,
                        beforeShow: function() {
                            $('#ui-datepicker-div').addClass('pp-datepicker');
                        }
                    });
                    $(this).datepicker("show");
                }
           });
        },

        /**
         * Close setting form lightbox by pressing Esc key.
         *
		 * @since 1.0.7
		 * @access private
		 * @method _settingsCloseEsc
		 */
        _settingsCloseEsc: function() {
            $(document).keyup(function(e) {
                if(27 == e.which && $('.fl-builder-settings-cancel').length !== 0) {
                    $('.fl-builder-settings-cancel').trigger('click');
                }
            });
        },


        /**
		 * Initializes CSS previews for a node.
		 *
		 * @since 1.0.9
		 * @method _initFieldCSSPreview
		 * @param {Object} field A field object.
		 */
		_initFieldCSSPreview: function( field )
		{
			var preview = field.data( 'preview' ),
				i 		= null;

            if ( 'undefined' === typeof preview ) {
                return;
            }

			if ( 'undefined' !== typeof preview.rules ) {
				for ( i in preview.rules ) {
					PPFields._initFieldCSSPreviewCallback( field, preview.rules[ i ] );
				}
			}
			else {
				PPFields._initFieldCSSPreviewCallback( field, preview );
			}
		},

        /**
		 * Initializes CSS preview callbacks for a field.
		 *
		 * @since 1.0.9
		 * @method _initFieldCSSPreviewCallback
		 * @param {Object} field A field object.
		 * @param {Object} preview The preview data object.
		 */
		_initFieldCSSPreviewCallback: function( field, preview )
		{
			PPFields._previewCSS( preview, field );
		},

        /**
		 * Updates the CSS rule for a preview.
		 *
		 * @since 1.0.9
		 * @method _previewCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} e An event object.
		 */
		_previewCSS: function(preview, field)
		{
			var selector = FLBuilder.preview._getPreviewSelector( FLBuilder.preview.classes.node, preview.selector ),
				property = preview.property,
				unit     = typeof preview.unit == 'undefined' ? '' : preview.unit,
				value    = field.find('input').val();

			if(unit == '%') {
				value = parseInt(value)/100;
			}
			else {
				value += unit;
			}

			FLBuilder.preview.updateCSSRule(selector, property, value);
		}

    };

    PPFields._init();

})(jQuery);
