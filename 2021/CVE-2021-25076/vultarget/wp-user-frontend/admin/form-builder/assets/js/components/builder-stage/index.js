Vue.component('builder-stage', {
    template: '#tmpl-wpuf-builder-stage',

    mixins: wpuf_form_builder_mixins(wpuf_mixins.builder_stage).concat(wpuf_mixins.add_form_field),

    computed: {
        form_fields: function () {
            return this.$store.state.form_fields;
        },

        field_settings: function () {
            return this.$store.state.field_settings;
        },

        hidden_fields: function () {
            return this.$store.state.form_fields.filter(function (item) {
                return 'custom_hidden_field' === item.template;
            });
        },

        editing_form_id: function () {
            return this.$store.state.editing_field_id;
        },

        pro_link: function () {
            return wpuf_form_builder.pro_link;
        }
    },

    mounted: function () {
        var self = this,
            in_column_field = false;

        // bind jquery ui sortable
        $('#form-preview-stage .wpuf-form.sortable-list').sortable({
            placeholder: 'form-preview-stage-dropzone',
            items: '.field-items',
            handle: '.control-buttons .move',
            scroll: true,
            over: function() {
                in_column_field = false;

                // if the field drop in column field, then stop field rendering in the builder stage
                $(".wpuf-column-inner-fields" ).on( "drop", function(event) {
                    var targetColumn = event.currentTarget.classList,
                        isColumnExist = $.inArray(".wpuf-column-inner-fields", targetColumn);

                    if ( isColumnExist ) {
                        in_column_field = true;
                    }
                } );
            },
            update: function (e, ui) {
                var item    = ui.item[0],
                    data    = item.dataset,
                    source  = data.source,
                    toIndex = parseInt($(ui.item).index()),
                    payload = {
                        toIndex: toIndex
                    };

                if ('panel' === source) {
                    // add new form element
                    self.$store.state.index_to_insert = parseInt(toIndex);

                    if ( ! in_column_field ) {
                        var field_template  = ui.item[0].dataset.formField;
                        self.add_form_field(field_template);
                    }

                    // remove button from stage
                    $(this).find('.button.ui-draggable.ui-draggable-handle').remove();

                } else if ('stage' === source) {
                    payload.fromIndex = parseInt(data.index);

                    self.$store.commit('swap_form_field_elements', payload);
                }

            }
        });
    },

    methods: {

        open_field_settings: function(field_id) {
            this.$store.commit('open_field_settings', field_id);
        },

        clone_field: function(field_id, index) {
            var payload = {
                field_id: field_id,
                index: index,
                new_id: this.get_random_id()
            };

            // single instance checking
            var field = _.find(this.$store.state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(payload.field_id);
            });

            // check if these are already inserted
            if ( this.isSingleInstance( field.template ) && this.containsField( field.template ) ) {
                swal({
                    title: "Oops...",
                    text: "You already have this field in the form"
                });
                return;
            }

            this.$store.commit('clone_form_field_element', payload);
        },

        delete_field: function(index) {
            var self = this;

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
                self.$store.commit('delete_form_field_element', index);
            }, function() {

            });
        },

        delete_hidden_field: function (field_id) {
            var i = 0;

            for (i = 0; i < this.form_fields.length; i++) {
                if (parseInt(field_id) === parseInt(this.form_fields[i].id)) {
                    this.delete_field(i);
                }
            }
        },

        is_pro_feature: function (template) {
            return (this.field_settings[template] && this.field_settings[template].pro_feature) ? true : false;
        },

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

        is_full_width: function (template) {
            if (this.field_settings[template] && this.field_settings[template].is_full_width) {
                return true;
            }

            return false;
        },

        is_invisible: function (field) {
            return ( field.recaptcha_type && 'invisible_recaptcha' === field.recaptcha_type ) ? true : false;
        },

        get_field_name: function (template) {
            return this.field_settings[template].title;
        }
    }
});
