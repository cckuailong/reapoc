/**
 * Mixin for option fields like
 * field-text, field-text-meta, field-radio etc
 */
wpuf_mixins.option_field_mixin = {
    props: {
        option_field: {
            type: Object,
            default: {}
        },

        editing_form_field: {
            type: Object,
            default: {}
        }
    },

    computed: {
        // show/hide on basis of depenedent settings
        met_dependencies: function () {
            // no 'dependencies' key
            if (!this.option_field.hasOwnProperty('dependencies')) {
                return true;
            }

            var deps = Object.keys(this.option_field.dependencies),
                i    = 0;

            // has 'dependencies' key, but no property is set
            if (!deps.length) {
                return true;
            }

            // check if dependencies met
            for (i = 0; i < deps.length; i++) {
                var required_dep_value  = this.option_field.dependencies[ deps[i] ],
                    editing_field_value = this.editing_form_field[ deps[i] ];

                if (required_dep_value !== editing_field_value) {
                    return false;
                }
            }

            return true;
        }
    },

    methods: {
        update_value: function(property, value) {
            this.$store.commit('update_editing_form_field', {
                editing_field_id: this.editing_form_field.id,
                field_name: property,
                value: value
            });
        },
    }
};
