/**
 * Sidebar field options panel
 */
Vue.component('field-options', {
    template: '#tmpl-wpuf-field-options',

    mixins: wpuf_form_builder_mixins(wpuf_mixins.field_options),

    data: function() {
        return {
            show_basic_settings: true,
            show_advanced_settings: false,
            show_quiz_settings: false
        };
    },

    computed: {
        editing_field_id: function () {
            this.show_basic_settings = true;
            this.show_advanced_settings = false;
            this.show_quiz_settings = false;

            return parseInt(this.$store.state.editing_field_id);
        },

        editing_form_field: function () {
            var self = this,
                i = 0;

            for (i = 0; i < self.$store.state.form_fields.length; i++) {
                // check if the editing field exist in normal fields
                if (self.$store.state.form_fields[i].id === parseInt(self.editing_field_id)) {
                    return self.$store.state.form_fields[i];
                }

                // check if the editing field belong to column field
                if (self.$store.state.form_fields[i].template === 'column_field') {
                    var innerColumnFields = self.$store.state.form_fields[i].inner_fields;

                    for (const columnFields in innerColumnFields) {
                        if (innerColumnFields.hasOwnProperty(columnFields)) {
                            var columnFieldIndex = 0;

                            while (columnFieldIndex < innerColumnFields[columnFields].length) {
                                if (innerColumnFields[columnFields][columnFieldIndex].id === self.editing_field_id) {
                                    return innerColumnFields[columnFields][columnFieldIndex];
                                }
                                columnFieldIndex++;
                            }
                        }
                    }
                }

            }
        },

        settings: function() {
            var settings = [],
                template = this.editing_form_field.template;

            if (_.isFunction(this['settings_' + template])) {
                settings = this['settings_' + template].call(this, this.editing_form_field);
            } else {
                settings = this.$store.state.field_settings[template].settings;
            }

            return _.sortBy(settings, function (item) {
                return parseInt(item.priority);
            });
        },

        basic_settings: function () {
            return this.settings.filter(function (item) {
                return 'basic' === item.section;
            });
        },

        advanced_settings: function () {
            return this.settings.filter(function (item) {
                return 'advanced' === item.section;
            });
        },

        quiz_settings: function () {
            return this.settings.filter(function (item) {
                return 'quiz' === item.section;
            });
        },

        form_field_type_title: function() {
            var template = this.editing_form_field.template;

            if (_.isFunction(this['form_field_' + template + '_title'])) {
                return this['form_field_' + template + '_title'].call(this, this.editing_form_field);
            }

            return this.$store.state.field_settings[template].title;
        },

        form_settings: function () {
            return this.$store.state.settings;
        }
    },

    watch: {
        form_settings: function () {
            return this.$store.state.settings;
        }
    }
});
