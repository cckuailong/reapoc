Vue.component('field-multiselect', {
    template: '#tmpl-wpuf-field-multiselect',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                if ( ! value ) {
                    value = [];
                }

                this.$store.commit('update_editing_form_field', {
                    editing_field_id: this.editing_form_field.id,
                    field_name: this.option_field.name,
                    value: value
                });
            }
        }
    },

    mounted: function () {
        this.bind_selectize();
    },

    methods: {
        bind_selectize: function () {
            var self = this;

            $(this.$el).find('.term-list-selector').selectize({}).on('change', function () {
                var data = $(this).val();

                self.value = data;
            });
        },
    },

});
