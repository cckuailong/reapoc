/**
 * Mixin for form fields like
 * form-text_field, form-field_textarea etc
 */
wpuf_mixins.form_field_mixin = {
    props: {
        field: {
            type: Object,
            default: {}
        }
    },

    computed: {
        form_id: function () {
            return this.$store.state.post.ID;
        },

        has_options: function () {
            if (!this.field.hasOwnProperty('options')) {
                return false;
            }

            return !!Object.keys(this.field.options).length;
        }
    },

    methods: {
        class_names: function(type_class) {
            return [
                type_class,
                this.required_class(),
                'wpuf_' + this.field.name + '_' + this.form_id
            ];
        },

        required_class: function () {
            return ('yes' === this.required) ? 'required' : '';
        },

        is_selected: function (label) {
            if (_.isArray(this.field.selected)) {
                if (_.indexOf(this.field.selected, label) >= 0) {
                    return true;
                }

            } else if (label === this.field.selected) {
                return true;
            }

            return false;
        }
    }
};
