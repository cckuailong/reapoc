Vue.component('field-checkbox', {
    template: '#tmpl-wpuf-field-checkbox',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                var value = this.editing_form_field[this.option_field.name];

                if (this.option_field.is_single_opt) {
                    var option = Object.keys(this.option_field.options)[0];

                    if (value === option) {
                        return true;

                    } else {
                        return false;
                    }
                }

                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                if (this.option_field.is_single_opt) {
                    value = value ? Object.keys(this.option_field.options)[0] : '';
                }


                this.$store.commit('update_editing_form_field', {
                    editing_field_id: this.editing_form_field.id,
                    field_name: this.option_field.name,
                    value: value
                });
            }
        }
    }
});
