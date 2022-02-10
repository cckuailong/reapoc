$ = jQuery.noConflict();

var wpbs_unsaved_changes = false;
var wpbs_form_submitting = false;

jQuery(function ($) {

    if(!$("#wpbs-form-builder").length) return;

    var wpbs_form_builder = new WPBS_Form_Builder(wpbs_form_data, wpbs_available_field_types, wpbs_available_field_types_options, wpbs_languages);

    /**
     * Set the form_submitting variable.
     * 
     */
    $(".wpbs-save-form").click(function(){
        wpbs_form_submitting = true;
    })

    /**
     * Set the unsaved_changes variable.
     * 
     */
    $(".wpbs-wrap-edit-form").on('change keyup', 'input, select, textarea', function(){
        wpbs_unsaved_changes = true;
    })

    /**
	 * Add new fields to the form builder
	 *
	 */
    $(".wpbs-form-builder-add-form-fields a").click(function (e) {
        e.preventDefault();
        // Add new field
        var form_field = $.extend(true, {}, wpbs_available_field_types[$(this).data('field-type')]);

        form_field['id'] = $("#wpbs_form_field_id_index").val();
        $("#wpbs_form_field_id_index").val( parseInt(form_field['id']) + 1  );
        wpbs_form_builder.form_data.push(form_field);
        wpbs_form_builder.render();

        // Close all fields
        $("#wpbs-form-builder .form-field").removeClass('open').find(".form-field-content").hide();

        // Open last field
        $("#wpbs-form-builder .form-field").last().addClass('open').find(".form-field-content").show();

        // Show notice on notification pages
        wpbs_show_form_changed_notice();
    })

    /**
	 * Update form object when interacting with fields
	 *
	 */
    $("#wpbs-form-builder").on('keyup change', 'input, textarea', function () {
        field = $(this);
        eq = field.parents('.form-field').index();

        if(!field.data('language')){
            return;
        }

        if(typeof wpbs_form_builder.form_data[eq]['values'][field.data('language')] === 'undefined'){
            wpbs_form_builder.form_data[eq]['values'][field.data('language')] = [];
        }

        if (field.attr('type') == 'checkbox') {
            wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')] = field.prop('checked') == true ? 'on' : '';
        } else {
            if(field.data('key') == 'options' || field.data('key') == 'options_pricing'){
                if(typeof wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')] === 'undefined'){
                    wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')] = [];
                }
                wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')][field.parent().index()] = field.val();
            } else {
                wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')] = field.val();
            }
        }
    })

    /**
     * Join pricing fields into one.
     * 
     */
    $("#wpbs-form-builder").on('keyup change', '.form-field-pricing-fields', function () {
        $parent = $(this).parent();
        $parent.find('input').eq(2).val( $parent.find('.price').val() + '|' +   $parent.find('.value').val()).trigger('keyup')
    })
    

    /**
     * Duplicate the label option into the field header
     */
    $("#wpbs-form-builder").on('keyup', 'input[data-key="label"][data-language="default"]', function () {
        field = $(this);
        field_value = field.val() ? field.val() : field.parents('.form-field').find('.form-field-header-label').data('field-type');
        field.parents('.form-field').find('.form-field-header-label').html( field_value );
    })

    /**
     * Remove fields button
     */
    $("#wpbs-form-builder").on('click', '.form-field-remove', function (e) {
        e.preventDefault();

        if( ! confirm( "Are you sure you want to remove this field?" ) )
            return false;

        field = $(this);
        eq = field.parents('.form-field').index();
        wpbs_form_builder.form_data = wpbs_form_builder.form_data.filter(function (item, index) {
            return index !== eq
        })
        wpbs_form_builder.render();

        // Show notice on notification pages
        wpbs_show_form_changed_notice();
    })

    /**
     * Make fields collapsable
     */
    $("#wpbs-form-builder").on('click', '.form-field-header', function (e) {
        e.preventDefault();

        field = $(this);
        field.parents('.form-field').toggleClass('open').find(".form-field-content").slideToggle();
    })

    /**
     * Field options accordion
     */
    $("#wpbs-form-builder").on('click', '.form-field-accordion-open', function (e) {
        e.preventDefault();

        accordion = $(this);
        accordion.parents('.form-field-accordion').toggleClass('open').find(".form-field-accordion-inner").slideToggle();

        if(accordion.parents('.form-field-accordion').find('.form-field-tabs').length){
            accordion.parents('.form-field-accordion').find('.form-field-tabs .form-field-tabs-navigation a').first().trigger('click');
        }
    })

    /**
     * Field options tabs
     */
    $("#wpbs-form-builder").on('click', '.form-field-tabs-navigation a', function (e) {
        e.preventDefault();
        var tab = $(this);

        tab.parents('.form-field-tabs').find(".form-field-tab").hide();
        tab.parents('.form-field-tabs').find(".form-field-tabs-navigation a").removeClass('active');

        tab.addClass('active');
        $(tab.data('tab')).show();
    })

    

    /**
     * Add option to dropdown, radio, checkboxes, etc. fields
     */
    $("#wpbs-form-builder").on('click', '.form-field-add-option', function (e) {
        e.preventDefault();
        options = $(this).parents('.form-field-options');
        $field_option = options.find('.form-field-option-placeholder').clone();
        $field_option.removeClass('form-field-option-placeholder').find('input[data-name]').attr('name', options.find('input[data-name]').data('name'))
        $field_option.appendTo(options.find('.form-field-options-inner-fields'));
    })

    /**
     * Remove dropdown, radio, checkboxes, etc. option fields
     */
    $("#wpbs-form-builder").on('click', '.form-field-option-field-remove', function (e) {
        e.preventDefault();

        if (!confirm("Are you sure you want to remove this option?"))
            return false;
        
        var field = $(this).siblings('input[data-key]');
        var field_parent = field.parents('.form-field-options');
        var eq = field.parents('.form-field').index();

        // Remove the field.
        $(this).parent().remove();

        // Rebuild field options.
        wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')] = [];

        field_parent.find('.form-field-options-inner-fields input[data-key]').each(function(){
            var option_field = $(this);
            wpbs_form_builder.form_data[eq]['values'][field.data('language')][field.data('key')][option_field.parent().index()] = option_field.val();
        })
       
    })

    

    /**
     * Make fields sortable
     */
    $('#wpbs-form-builder').sortable({
        handle: '.form-field-sort',
        placeholder: 'form-field-placeholder',
        containment: '#wpcontent',
        update: function () {
            form_sorted = [];
            $("#wpbs-form-builder .form-field").each(function (i) {
                new_position = $(this).data('order');
                form_sorted[i] = wpbs_form_builder.form_data[new_position];
            })
            wpbs_form_builder.form_data = form_sorted;
            wpbs_form_builder.render();
        }
    });

    /**
     * Select all text when clicking on an email tag
     */
    $(".wpbs-wrap-edit-form").on('click', '.wpbs-email-tag div', function (e) {
        node = $(this)[0];
        if (document.body.createTextRange) {
            const range = document.body.createTextRange();
            range.moveToElementText(node);
            range.select();
        } else if (window.getSelection) {
            const selection = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(node);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    });


    /**
     * Toggle confirmation messages
     */
    $(".wpbs-wrap-edit-form").on('change', '#form_confirmation_type', function(){
        $(this).parents('.wpbs-tab').find(".wpbs-confirmation-type").hide();
        $(".wpbs-confirmation-type-" + $(this).val()).show();
    })

    /**
     * Show a warning on the notifications page if the form has changed
     */
    function wpbs_show_form_changed_notice(){
        $(".wpbs-form-changed-notice").show();
    }

    // Build the form.
    wpbs_form_builder.render();

});

class WPBS_Form_Builder {
    constructor(form_data, available_field_types, available_field_types_options, languages){
        this.wrapper = document.getElementById('wpbs-form-builder');
        this.output;
        this.form_data = form_data;
        this.available_field_types = available_field_types;
        this.available_field_types_options = available_field_types_options;
        this.languages = languages;
    }

    build() {
        this.output = '';
        if(this.form_data.length){
            this.fields();
            $(".wpbs-start-building").hide();
        } else {
            $(".wpbs-start-building").show();
        }
    }
    /**
     * Build the Fields
     */
    fields(){
        this.form_data.forEach(function (field, i) {
            this.field(field, i);
        }.bind(this))
    }

    /**
     * Build each individual field
     * 
     * @param field 
     * @param i 
     */
    field(field, i){
        this.output += '<div class="form-field form-field-type-' + field.type + '" data-order="' + i + '">';
            this.output += '<div class="form-field-inner">';
                this.field_header(field);
                this.output += '<div class="form-field-content"><div class="form-field-content-inner">';
                    this.output += '<input type="hidden" name="form_fields[' + i + '][type]" value="' + field.type + '" />';
                    this.output += '<input type="hidden" name="form_fields[' + i + '][id]" value="' + field.id + '" />';
                    this.field_options(field, i);
                    this.field_translation_options(field, i);
                this.output += '</div></div>';
            this.output += '</div>';
        this.output += '</div>';
    }

    /**
     * Build the header for a field
     * 
     * @param  field 
     */
    field_header(field){
        var field_nice_name = field.type.replace('_', ' ');
        var label = (typeof field['values']['default'] !== 'undefined' && typeof field['values']['default']['label'] !== 'undefined' && field['values']['default']['label'] != '') ? field['values']['default']['label'] : '<span><span>' + field_nice_name + '</span> Field</span>';
        


        this.output += '<div class="form-field-header">';
            this.output += '<div class="form-field-header-buttons">';
                this.output += '<span class="form-field-id">ID:'+field.id+'</span>';
                this.output += '<a href="#" class="form-field-remove"><i class="wpbs-icon-close"></i></a>';
                this.output += '<a href="#" class="form-field-sort"><i class="wpbs-icon-sort"></i></a>';
                this.output += '<a href="#" class="form-field-toggle"><i class="wpbs-icon-down-arrow"></i></a>';
            this.output += '</div>';

            this.output += '<p><i class="wpbs-icon-'+field.type+'"></i> <span data-field-type="<span><span>' + field_nice_name + '</span> Field</span>" class="form-field-header-label">'+label+'</span></p>';
        this.output += '</div>';
    }

    /**
     * Build the field options
     * 
     * @param field 
     * @param i 
     * @param language 
     */
    field_options(field, i, language = 'default'){

        // Primary Fields
        if(this.available_field_types[field.type].supports.primary){
            this.available_field_types[field.type].supports.primary.forEach(function (option) {
                this.field_option(option, i, language)
            }.bind(this))
        }

        if(language == 'default' && this.available_field_types[field.type].supports.secondary){
            this.output += '<div class="form-field-accordion">';
                this.output += '<div class="form-field-accordion-header">';
                    this.output += '<a href="#" class="form-field-accordion-open">Advanced Options <i class="wpbs-icon-down-arrow"></i></a>';
                this.output += '</div>';
                this.output += '<div class="form-field-accordion-inner">';
        }

        //Secondary Fields
        if(this.available_field_types[field.type].supports.secondary){
            this.available_field_types[field.type].supports.secondary.forEach(function (option) {
                this.field_option(option, i, language)
            }.bind(this))
        }

        if(language == 'default' && this.available_field_types[field.type].supports.secondary){
            this.output += '</div>';
        this.output += '</div>';
        }
        
    }

    /**
     * Build the translatable options
     * 
     * @param field 
     * @param i 
     */
    field_translation_options(field, i){
        
        // If there are no languages, we skip this section
        if(!this.available_field_types[field.type].languages) return;

        this.output += '<div class="form-field-accordion">';
            this.output += '<div class="form-field-accordion-header">';
                this.output += '<a href="#" class="form-field-accordion-open">Translations <i class="wpbs-icon-down-arrow"></i></a>';
            this.output += '</div>';
            this.output += '<div class="form-field-accordion-inner">';

                this.output += '<div class="form-field-tabs">';
                    this.output += '<div class="form-field-tabs-navigation">';
                        this.available_field_types[field.type].languages.forEach(function (language, j) {
                            this.output += '<a href="#" data-tab="#form-field-tab-'+language+'-'+i+'-'+j+'"><img src="'+wpbs_localized_data.wpbs_plugins_dir_url+'/assets/img/flags/'+language+'.png" />'+this.languages[language]+'</a>';
                        }.bind(this))
                    this.output += '</div>';

                    this.output += '<div class="form-field-tabs-inner">';

                        this.available_field_types[field.type].languages.forEach(function (language, j) {
                            this.output += '<div id="form-field-tab-'+language+'-'+i+'-'+j+'" class="form-field-tab form-field-translation form-field-translation-'+language+'">';
                                this.field_options(field, i, language);
                            this.output += '</div>';
                        }.bind(this))

                    this.output += '</div>';
                this.output += '</div>';

            this.output += '</div>';
        this.output += '</div>';
    }

    /**
     * Build the field option
     * 
     * @param field 
     * @param i 
     */
    field_option(option, i, language = 'default'){
        
        // If we build translation options and the field does not support translation, we skip it
        if(language != 'default' && this.available_field_types_options[option].translatable == false) return;

        var key = this.available_field_types_options[option].key;
        var label = this.available_field_types_options[option].label;
        var input = typeof this.available_field_types_options[option].input !== 'undefined' ? this.available_field_types_options[option].input : 'text';
        var options = typeof this.available_field_types_options[option].options !== 'undefined' ? this.available_field_types_options[option].options : false;

        this.output += '<div class="form-field-row form-field-row-type-'+key+'">';

            switch(key){
                case 'required':
                case 'hide_label':
                    this.field_option_type_checkbox(key, label, input, language, i);
                    break;
                case 'options':
                    this.field_option_type_options(key, label, input, language, i);
                    break;
                case 'options_pricing':
                    this.field_option_type_options_pricing(key, label, input, language, i);
                    break;
                case 'pricing_type':
                    this.field_option_type_pricing_type(key, label, input, language, options, i);
                    break;
                default:
                    if(key.indexOf('notice_') === -1){
                        this.field_option_type_default(key, label, input, language, i);
                    } else {
                        this.field_option_type_notice(key, label);
                    }
            }

        this.output += '</div>';
    }

    
    /**
     * Build the field option inputs based on type
     * 
     * @param key 
     * @param label 
     * @param language 
     * @param i 
     */
    field_option_type_default(key, label, input, language, i){
        var value = (typeof this.form_data[i]['values'][language] !== 'undefined' && typeof this.form_data[i]['values'][language][key] !== 'undefined') ? this.form_data[i]['values'][language][key] : '';
        if(input == 'textarea'){
            this.output += '<label for="form-field-'+ i +'-'+ key +'">' + label + '</label><textarea id="form-field-'+ i +'-'+ key +'" type="text" data-language="' + language + '" data-key="' + key + '" name="form_fields[' + i + '][values][' + language + '][' + key + ']">' + value + '</textarea>';
        } else {
            this.output += '<label for="form-field-'+ i +'-'+ key +'">' + label + '</label><input id="form-field-'+ i +'-'+ key +'" type="text" data-language="' + language + '" data-key="' + key + '" value="' + value + '" name="form_fields[' + i + '][values][' + language + '][' + key + ']" />';
        }
        
    }

    field_option_type_pricing_type(key, label, input, language, options, i){
        var value = (typeof this.form_data[i]['values'][language] !== 'undefined' && typeof this.form_data[i]['values'][language][key] !== 'undefined') ? this.form_data[i]['values'][language][key] : '';
        this.output += '<label for="form-field-'+ i +'-'+ key +'">' + label + '</label><select id="form-field-'+ i +'-'+ key +'" type="text" data-language="' + language + '" data-key="' + key + '" value="' + value + '" name="form_fields[' + i + '][values][' + language + '][' + key + ']">';
        for(var option in options){
            var selected = (value == option) ? 'selected' : '';
            this.output += '<option value="'+option+'" '+selected+'>'+options[option]+'</option>';
        }
        this.output += '</select>';
    }

    field_option_type_notice(key, label){
        this.output += '<div class="wpbs-page-notice notice-error"><p>' + label + '</p></div>';
    }

    field_option_type_checkbox(key, label, input, language, i){
        var value = (typeof this.form_data[i]['values'][language][key] !== 'undefined' && this.form_data[i]['values'][language][key] == 'on') ? 'checked' : '';
        this.output += '<label for="form-field-'+ i +'-'+ key +'">' + label + '</label><label class="wpbs-checkbox-switch" for="form-field-'+ i +'-'+ key +'"><input id="form-field-'+ i +'-'+ key +'" type="checkbox" data-language="' + language + '" data-key="' + key + '" ' + value + ' name="form_fields[' + i + '][values][' + language + '][' + key + ']" /><div class="wpbs-checkbox-slider"></div></label>';
    }


    field_option_type_options(key, label, input, language, i){
        this.output += '<div class="form-field-options">';
            this.output += '<label>'+label+'</label>';
            this.output += '<div class="form-field-options-inner">';

                this.output += '<div class="form-field-option-placeholder"><input type="text" data-name="form_fields[' + i + '][values][' + language + '][' + key + '][]" data-language="' + language + '" data-key="' + key + '" /><a href="#" class="form-field-option-field-remove"><i class="wpbs-icon-close"></i></a></div>';

                this.output += '<div class="form-field-options-inner-fields">';
                    if (typeof this.form_data[i]['values'][language] !== 'undefined' && typeof this.form_data[i]['values'][language][key] !== 'undefined') {
                        this.form_data[i]['values'][language][key].forEach(function (value) {
                            if (value)
                                this.output += '<div><input type="text" data-language="' + language + '" data-key="' + key + '" name="form_fields[' + i + '][values][' + language + '][' + key + '][]" value="' + value + '" /><a href="#" class="form-field-option-field-remove"><i class="wpbs-icon-close"></i></a></div>';
                        }.bind(this))
                    }

                this.output += '</div>';

                this.output += '<a href="#" class="form-field-add-option button button-secondary">Add Option</a>';

            this.output += '</div>';

        this.output += '</div>';
    }

    field_option_type_options_pricing(key, label, input, language, i){
        this.output += '<div class="form-field-options">';
            this.output += '<label>'+label+'</label>';
            this.output += '<div class="form-field-options-inner">';

                this.output += '<div class="form-field-option-placeholder"><input type="number" class="form-field-pricing-fields price" /><input type="text" class="form-field-pricing-fields value" /><input type="hidden" data-name="form_fields[' + i + '][values][' + language + '][' + key + '][]" data-language="' + language + '" data-key="' + key + '" /><a href="#" class="form-field-option-field-remove"><i class="wpbs-icon-close"></i></a></div>';

                this.output += '<div class="form-field-options-inner-heading"><span>Price</span><span>Name</span></div>';
                this.output += '<div class="form-field-options-inner-fields">';
                    if (typeof this.form_data[i]['values'][language] !== 'undefined' && typeof this.form_data[i]['values'][language][key] !== 'undefined') {
                        this.form_data[i]['values'][language][key].forEach(function (value) {
                            if (value)
                                var values = value.split('|');
                                this.output += '<div><input type="number" class="form-field-pricing-fields price" value="'+values[0]+'" /><input type="text" class="form-field-pricing-fields value" value="'+values[1]+'" /><input type="hidden" data-language="' + language + '" data-key="' + key + '" name="form_fields[' + i + '][values][' + language + '][' + key + '][]" value="' + value + '" /><a href="#" class="form-field-option-field-remove"><i class="wpbs-icon-close"></i></a></div>';
                        }.bind(this))
                    }

                this.output += '</div>';

                this.output += '<a href="#" class="form-field-add-option button button-secondary">Add Option</a>';

            this.output += '</div>';

        this.output += '</div>';
    }

    /**
     * Render the form
     */
    render(){
        this.build();
        this.wrapper.innerHTML = this.output;
    }
}