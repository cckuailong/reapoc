Vue.component('field-option-pro-feature-alert', {
    template: '#tmpl-wpuf-field-option-pro-feature-alert',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        pro_link: function () {
            return wpuf_form_builder.pro_link;
        }
    }
});
