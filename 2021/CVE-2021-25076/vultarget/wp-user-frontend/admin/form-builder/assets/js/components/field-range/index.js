Vue.component('field-range', {
    template: '#tmpl-wpuf-field-range',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.update_value(this.option_field.name, value);
            }
        },

        minColumn: function () {
            return this.editing_form_field.min_column;
        },

        maxColumn: function () {
            return this.editing_form_field.max_column;
        }
    },

    methods: {
    }
});
