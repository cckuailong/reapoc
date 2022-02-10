/**
 * Field template: Column Field
 */
const mixins = [
    wpuf_mixins.form_field_mixin
];

if (window.wpuf_forms_mixin_builder_stage) {
    mixins.push(window.wpuf_forms_mixin_builder_stage);
}

if (window.weforms_mixin_builder_stage) {
    mixins.push(window.weforms_mixin_builder_stage);
}

Vue.component('form-column_field', {
    template: '#tmpl-wpuf-form-column_field',

    mixins: mixins,

    data() {
        return{
            columnClasses: ['column-1', 'column-2', 'column-3'] // don't edit class names
        };
    },

    mounted() {
        this.resizeColumns(this.field.columns);

        // bind jquery ui draggable
        var self = this,
            sortableFields = $(self.$el).find('.wpuf-column-inner-fields .wpuf-column-fields-sortable-list'),
            sortableTriggered = 1,
            columnFieldArea = $('.wpuf-field-columns'),
            columnFields = $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields");

        columnFieldArea.mouseenter(function() {
            self.resizeColumns(self.field.columns);
        });


        columnFieldArea.mouseleave(function() {
            columnFields.unbind( "mouseup" );
            columnFields.unbind( "mousemove" );
        });

        // bind jquery ui sortable
        $(sortableFields).sortable({
            placeholder: 'form-preview-stage-dropzone',
            connectWith: sortableFields,
            items: '.column-field-items',
            handle: '.wpuf-column-field-control-buttons .move',
            scroll: true,
            stop: function( event, ui ) {
                var item        = ui.item[0];
                var data        = item.dataset;
                var data_source = data.source;

                if ('panel' === data_source) {
                    var payload = {
                        toIndex: parseInt($(ui.item).index()),
                        field_template: data.formField,
                        to_column: $(this).parent().attr('class').split(' ')[0]
                    };

                    self.add_column_inner_field(payload);

                    // remove button from stage
                    $(this).find('.button.ui-draggable.ui-draggable-handle').remove();
                }
            },
            update: function (e, ui) {
                var item    = ui.item[0],
                    data    = item.dataset,
                    source  = data.source,
                    toIndex = parseInt($(ui.item).index()),
                    payload = {
                        toIndex: toIndex
                    };

                if ( 'column-field-stage' === source) {
                    payload.field_id   = self.field.id;
                    payload.fromIndex  = parseInt(item.attributes['column-field-index'].value);
                    payload.fromColumn = item.attributes['in-column'].value;
                    payload.toColumn   = item.parent().parent().attr('class').split(' ')[0];

                    // when drag field one column to another column, sortable event trigger twice and try to swap field twice.
                    // So the following conditions are needed to check and run swap_column_field_elements commit only once
                    if (payload.fromColumn !== payload.toColumn && sortableTriggered === 1) {
                        sortableTriggered = 0;
                    }else{
                        sortableTriggered++;
                    }

                    if (payload.fromColumn === payload.toColumn) {
                        sortableTriggered = 1;
                    }

                    if (sortableTriggered === 1) {
                        self.$store.commit('swap_column_field_elements', payload);
                    }
                }
            }
        });
    },

    computed: {
        column_fields: function () {
            return this.field.inner_fields;
        },

        innerColumns() {
            return this.field.columns;
        },

        editing_form_id: function () {
            return this.$store.state.editing_field_id;
        },

        field_settings: function () {
            return this.$store.state.field_settings;
        },
    },

    methods: {
        is_template_available: function (field) {
            var template = field.template;

            if (this.field_settings[template]) {
                if (this.is_pro_feature(template)) {
                    return false;
                }

                return true;
            }

            // for example see 'mixin_builder_stage' mixin's 'is_taxonomy_template_available' method
            if (_.isFunction(this['is_' + template + '_template_available'])) {
                return this['is_' + template + '_template_available'].call(this, field);
            }

            return false;
        },

        is_pro_feature: function (template) {
            return (this.field_settings[template] && this.field_settings[template].pro_feature) ? true : false;
        },

        get_field_name: function (template) {
            return this.field_settings[template].title;
        },

        is_full_width: function (template) {
            if (this.field_settings[template] && this.field_settings[template].is_full_width) {
                return true;
            }

            return false;
        },

        is_invisible: function (field) {
            return ( field.recaptcha_type && 'invisible_recaptcha' === field.recaptcha_type ) ? true : false;
        },

        isAllowedInClolumnField: function(field_template) {
            var restrictedFields = ['column_field', 'custom_hidden_field', 'step_start'];

            if ( $.inArray(field_template, restrictedFields) >= 0 ) {
                return true;
            }

            return false;
        },

        add_column_inner_field(data) {
            var payload = {
                toWhichColumnField: this.field.id,
                toWhichColumnFieldMeta: this.field.name,
                toIndex: data.toIndex,
                toWhichColumn: data.to_column
            };

            if (this.isAllowedInClolumnField(data.field_template)) {
                swal({
                    title: "Oops...",
                    text: "You cannot add this field as inner column field"
                });
                return;
            }

            // check if these are already inserted
            if ( this.isSingleInstance( data.field_template ) && this.containsField( data.field_template ) ) {
                swal({
                    title: "Oops...",
                    text: "You already have this field in the form"
                });
                return;
            }

            var field = $.extend(true, {}, this.$store.state.field_settings[data.field_template].field_props),
            form_fields = this.$store.state.form_fields;

            field.id = this.get_random_id();

            if ('yes' === field.is_meta && !field.name && field.label) {
                field.name = field.label.replace(/\W/g, '_').toLowerCase();

                var same_template_fields = form_fields.filter(function (form_field) {
                    return (form_field.template === field.template);
                });

                if (same_template_fields) {
                    field.name += '_' + this.get_random_id();
                }
            }

            payload.field = field;

            // add new form element
            this.$store.commit('add_column_inner_field_element', payload);
        },

        moveFieldsTo(column) {
            var payload = {
                field_id: this.field.id,
                move_to : column,
                inner_fields: this.getInnerFields()
            };

            // clear inner fields & push mergedFields to column-1
            this.$store.commit('move_column_inner_fields', payload);
        },

        getInnerFields() {
            return this.field.inner_fields;
        },

        open_column_field_settings: function(field, index, column) {
            var self = this,
                payload = {
                    field_id: self.field.id,
                    column_field: field,
                    index: index,
                    column: column,
                };
            self.$store.commit('open_column_field_settings', payload);
        },

        clone_column_field: function(field, index, column) {
            var self = this,
                payload = {
                    field_id: self.field.id,
                    column_field_id: field.id,
                    index: index,
                    toColumn: column,
                    new_id: self.get_random_id()
                };

            // check if the field is allowed to duplicate
            if ( self.isSingleInstance( field.template ) ) {
                swal({
                    title: "Oops...",
                    text: "You already have this field in the form"
                });
                return;
            }

            self.$store.commit('clone_column_field_element', payload);
        },

        delete_column_field: function(index, fromColumn) {
            var self = this,
                payload = {
                    field_id: self.field.id,
                    index: index,
                    fromColumn: fromColumn
                };

            swal({
                text: self.i18n.delete_field_warn_msg,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d54e21',
                confirmButtonText: self.i18n.yes_delete_it,
                cancelButtonText: self.i18n.no_cancel_it,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
            }).then(function () {
                self.$store.commit('delete_column_field_element', payload);
            }, function() {

            });
        },

        resizeColumns(columnsNumber) {
            var self = this;

            (function () {
                var columnElement;
                var startOffset;
                var columnField = $(self.$el).parent();
                var total_width = parseInt($(columnField).width());

                Array.prototype.forEach.call(
                    $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields"),

                    function (column) {
                        column.style.position = 'relative';

                        var grip = document.createElement('div');
                        grip.innerHTML = "&nbsp;";
                        grip.style.top = 0;
                        grip.style.right = 0;
                        grip.style.bottom = 0;
                        grip.style.width = '5px';
                        grip.style.position = 'absolute';
                        grip.style.cursor = 'col-resize';
                        grip.addEventListener('mousedown', function (e) {
                            columnElement = column;
                            startOffset = column.offsetWidth - e.pageX;
                        });

                        column.appendChild(grip);
                    });

                $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields").mousemove(function( e ) {
                    if (columnElement) {
                    var currentColumnWidth = startOffset + e.pageX;

                    columnElement.style.width = (100*currentColumnWidth) / total_width + '%';
                    }
                });

                $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields").mouseup(function() {
                    let colOneWidth   = 0,
                        colTwoWidth   = 0,
                        colThreeWidth = 0;

                    if (parseInt(columnsNumber) === 3) {
                        colOneWidth = 100 / columnsNumber;
                        colTwoWidth = 100 / columnsNumber;
                        colThreeWidth = 100 / columnsNumber;
                    } else if (parseInt(columnsNumber) === 2) {
                        colOneWidth = 100 / columnsNumber;
                        colTwoWidth = 100 / columnsNumber;
                        colThreeWidth = 0;
                    } else {
                        colOneWidth = 100;
                        colTwoWidth = 0;
                        colThreeWidth = 0;
                    }

                    self.field.inner_columns_size['column-1'] = colOneWidth + '%';
                    self.field.inner_columns_size['column-2'] = colTwoWidth + '%';
                    self.field.inner_columns_size['column-3'] = colThreeWidth + '%';

                    columnElement = undefined;
                });
            })();
        }
    },

    watch: {
        innerColumns(new_value) {
            var columns = parseInt(new_value),
                columns_size = this.field.inner_columns_size;

            Object.keys(columns_size).forEach(function (column) {
                if (columns === 1) {
                    columns_size[column] = '100%';
                }

                if (columns === 2) {
                    columns_size[column] = '50%';
                }

                if (columns === 3) {
                    columns_size[column] = '33.33%';
                }
            });

            // if columns number reduce to 1 then move other column fields to the first column
            if ( columns === 1 ) {
                this.moveFieldsTo( "column-1" );
            }

            // if columns number reduce to 2 then move column-2 and column-3 fields to the column-2
            if ( columns === 2 ) {
                this.moveFieldsTo( "column-2" );
            }

            this.resizeColumns(columns);
        }
    }
});
