/**
 * Global mixin
 */
Vue.mixin({
    computed: {
        i18n: function () {
            return wpuf_form_builder.i18n;
        }
    },

    methods: {
        get_random_id: function() {
            var min = 999999,
                max = 9999999999;

            return Math.floor(Math.random() * (max - min + 1)) + min;
        },

        warn: function (settings, callback) {
            settings = $.extend(true, {
                title: '',
                text: '',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d54e21',
                confirmButtonText: this.i18n.ok,
                cancelButtonText: this.i18n.cancel,
            }, settings);

            swal(settings, callback);
        },

        is_failed_to_validate: function (template) {
            var validator = this.field_settings[template] ? this.field_settings[template].validator : false;

            if (validator && validator.callback && !this[validator.callback]()) {
                return true;
            }

            return false;
        },

        has_recaptcha_api_keys: function () {
            return (wpuf_form_builder.recaptcha_site && wpuf_form_builder.recaptcha_secret) ? true : false;
        },

        containsField: function(field_name) {
            var self = this,
                i = 0;

            for (i = 0; i < self.$store.state.form_fields.length; i++) {
                // check if the single instance field exist in normal fields
                if (self.$store.state.form_fields[i].template === field_name) {
                    return true;
                }

                if (self.$store.state.form_fields[i].name === field_name) {
                    return true;
                }

                // check if the single instance field exist in column fields
                if (self.$store.state.form_fields[i].template === 'column_field') {
                    var innerColumnFields = self.$store.state.form_fields[i].inner_fields;

                    for (const columnFields in innerColumnFields) {
                        if (innerColumnFields.hasOwnProperty(columnFields)) {
                            var columnFieldIndex = 0;

                            while (columnFieldIndex < innerColumnFields[columnFields].length) {
                                if (innerColumnFields[columnFields][columnFieldIndex].template === field_name) {
                                    return true;
                                }
                                columnFieldIndex++;
                            }
                        }
                    }
                }

            }

            return false;
        },

        isSingleInstance: function(field_name) {
            let singleInstance = wpuf_single_objects;

            for( let instance of singleInstance ) {
                if ( field_name === instance ) {
                    return true;
                }
            }
            return false;
        }
    }
});
