/*
 * This is a JavaScript Scratchpad.
 *
 * Enter some JavaScript, then Right Click or choose from the Execute Menu:
 * 1. Run to evaluate the selected text (Cmd-R),
 * 2. Inspect to bring up an Object Inspector on the result (Cmd-I), or,
 * 3. Display to insert the result in a comment after the selection. (Cmd-L)
 */

(function($, window) {
    
    var WPUF_Conditional = {
        
        init: function() {
            var form = $('#wpuf-form-editor');
            form.on('click', 'a.wpuf-conditional-plus', this.duplicateRow);
            form.on('click', 'a.wpuf-conditional-minus', this.removeRow);
            form.on('click', 'input[type=radio].wpuf-conditional-enable', this.showHide);
            form.on('change', 'input[type=text][data-type="label"]', this.labelRender );
            form.on('change', 'input[type=text][data-type="option"]', this.optionRender );
            form.on('change', 'input[type=text][data-type="option_value"]', this.optionRender );
            form.on('change', 'select.wpuf-conditional-fields', this.optionChange);
            
            this.getLabel( true );

            //initially get options values array
            this.getOption( true );

            this.labelRender();
            this.optionRender();

        },


        newOptionValue: [],

        prevOptionValue: [],

        prevLabelValue: [],

        newLabelValue: [],

        getOption: function( optionStatus ) {
            var labels = $('#wpuf-form-editor').find('input[data-type="name"]');

            $.each( labels, function(k, label_field ) {
                var label = $(label_field),
                    label_name = label.val();
                if( label_name != '' ) {

                    var label_option = label.closest('.wpuf-form-holder').find('input[data-type="option_value"]');

                    var generate = [];

                    $.each( label_option, function( key, option ) {
                        var field_value = $(option).val();
                        if ( field_value == '' ) {
                            var field_value = $(option).closest('div').find('input[data-type=option]').val();
                        } 
                        generate.push( field_value );
                    });

                    if( optionStatus === true ) {
                        WPUF_Conditional.prevOptionValue[label_name] = generate;
                    }    

                    if( optionStatus === false ) {
                        WPUF_Conditional.newOptionValue[label_name] = generate;
                    } 
                }
            });
        },

        getLabel: function( labelStatus ) {
            var label = $('#wpuf-form-editor').find('input[data-type="name"]'),
                prev_label = WPUF_Conditional.prevLabelValue,
                new_label = WPUF_Conditional.newLabelValue;

            if( labelStatus === true ) {
                prev_label.length = 0;
            }

            if( labelStatus === false ) {
                new_label.length = 0;
            }

            $.each( label, function(k, label_field ) {
                var label = $(label_field),
                    label_name = label.val();
                if( label_name != '' && labelStatus === true ) {
                    prev_label.push( label_name );      
                }

                if( label_name != '' && labelStatus === false ) {
                    new_label.push( label_name );
                }
            });
        },

        labelRender: function() {

            //after change get options values array
            WPUF_Conditional.getLabel( false );

             var options = WPUF_Conditional.fieldDropdown(),
                label = $('#wpuf-form-editor').find('input[data-type="name"]'),
                cond_select_wraps = $('select.wpuf-conditional-fields'),
                wrap = $('.wpuf-form-holder'),
                prev_label = WPUF_Conditional.prevLabelValue,
                new_label = WPUF_Conditional.newLabelValue;

                $.each( wrap, function( key, parent ) {

                    var label_name = $(parent).find('input[data-type="name"]').val(),
                        cond_select_wraps = $(parent).find('select.wpuf-conditional-fields'),
                        new_option = [];

                    $.each( options, function( k, opt_value ) {
                        var opt_val = $( opt_value ).attr('value');

                        if( opt_val !== label_name || label_name == '' ) {
                            new_option.push( opt_value );  
                        }

                    }); 

                    $.each( cond_select_wraps, function( i, select_val) { 
                        var select = $(select_val),
                            select_val = select.val();

                        var index = new_label.indexOf( select_val );

                        if( index == '-1'  ) {
                            var oldindex = prev_label.indexOf( select_val );
                            select.html( new_option.join('') ).val( new_label[oldindex] );
                        } else {
                            select.html( new_option.join('') ).val( select_val );
                        } 
                    });
                });
                


            WPUF_Conditional.getLabel( true );
            WPUF_Conditional.getOption( true );
        },

        optionRender: function() {

            //after change get options values array
            WPUF_Conditional.getOption( false );

            var option = WPUF_Conditional.labelOptionValue,
                cond_select_wraps = $('select.wpuf-conditional-fields');

            $.each( cond_select_wraps, function( i, select_val) {

                var select = $(select_val),
                    select_val = $(select_val).val(),
                    option_generate = WPUF_Conditional.optionDropdown( select_val ),
                    optionDropdown = select.closest('tr').find('select.wpuf-conditional-fields-option'),
                    option_prev_val = optionDropdown.val(),
                    prev_options = WPUF_Conditional.prevOptionValue,
                    new_options = WPUF_Conditional.newOptionValue;

                if( select_val !== '' ) { 

                    var index = new_options[select_val].indexOf( option_prev_val );

                    if( index == '-1') {

                        var oldindex = prev_options[select_val].indexOf( option_prev_val );

                        optionDropdown.html(option_generate).val( new_options[select_val][oldindex] );
                    } else {
                        optionDropdown.html(option_generate).val(option_prev_val);
                    } 
                } else {
                    optionDropdown.html('<option>- select -</option>');
                }
            }); 

            WPUF_Conditional.getOption( true );           
        },

        
        getFields: function() {
            var elements = [],
                form_editor = $('#wpuf-form-editor');
    
            form_editor.find('li.wpuf-conditional').each(function(i, el) {
                el = $(el);

                var label = el.find('input[type=text][data-type="label"]').val(),
                    name = el.find('[data-type="name"]').val();

                elements[i] = {
                    'name': name,
                    'label': label,
                    'options': [],
                    'values': [],
                };

                el.find('.wpuf-options [data-type="option"]').each(function(j, jel){
                    var option_value = $(this).siblings('input[data-type="option_value"]').val();
                    if ( option_value == '') {
                        option_value = $(jel).val(); 
                    }
                    elements[i].options.push( $(jel).val() );
                    elements[i].values.push( option_value );
                });
            });
            
            return elements;
        },
        
        fieldDropdown: function() {

            var fields = this.getFields(),
                dropdown = [];

            dropdown.push('<option value="">- select -</option>');

            for( var field in fields ) {

                var label = fields[field].label; 
                if( label != '') {
                    dropdown.push('<option value="' + fields[field].name + '">' + label + '</option>');
                }
            }

            return dropdown;
        },
        
        optionDropdown: function(fieldName) {
            var fields = this.getFields(),
                dropdown = '<option value="">- select -</option>',
                options = [],
                values =[];

            for(field in fields) {

                if(fields[field].name === fieldName) {
                    options = fields[field].options;
                    values = fields[field].values;
                }
            }

            for( var option in options ) {

                var o = options[option],
                    v = values[option];
                dropdown += '<option  value="' + v + '">' + o + '</option>';
            }

            return dropdown;   
        },
        
        fillFields: function() {
            var options = this.fieldDropdown();
            $('.wpuf-conditional-fields').each(function(i, el) {
                $(el).empty().append(options);
            });
        },
        
        showHide: function() {
           
            var self = $(this),
                parent = self.closest('.wpuf-form-sub-fields').find('.conditional-rules-wrap'),
                val = self.val();

            if ( val === 'yes' ) {
                parent.removeClass('wpuf-hide');
                WPUF_Conditional.labelRender();
                WPUF_Conditional.optionRender();
                
            } else {
                parent.addClass('wpuf-hide');
            }
        },
        
        duplicateRow: function(e) {
            e.preventDefault();
            
            var self = $(this),
                tr = self.closest('tr');
            
            tr.clone().insertAfter(tr);
        },
        
        removeRow: function(e) {
            e.preventDefault();
            
            var table = $(this).closest('table'),
                rows = table.find('tr').length;
                
            if ( rows > 1 ) {
                $(this).closest('tr').remove();
            }
        },
        
        optionChange: function() {
            var self = $(this),
                value = self.val(),
                optionDropdown = self.closest('tr').find('select.wpuf-conditional-fields-option');

            if ( value !== '' ) {
                options = WPUF_Conditional.optionDropdown(value);
                optionDropdown.empty().append(options);
            } else {
                optionDropdown.html('<option>- Select -</option>');
            }
        }
    };
    
    $(function(){
        window.wpuf_conditional = WPUF_Conditional;
        WPUF_Conditional.init();
    });
    
})(jQuery, window);