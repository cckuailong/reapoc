/**
 * Field template: Recaptcha
 */
Vue.component('form-recaptcha', {
    template: '#tmpl-wpuf-form-recaptcha',

    mixins: [
        wpuf_mixins.form_field_mixin
    ],

    computed: {
        has_recaptcha_api_keys: function () {
            return (wpuf_form_builder.recaptcha_site && wpuf_form_builder.recaptcha_secret) ? true : false;
        },

        no_api_keys_msg: function () {
            return wpuf_form_builder.field_settings.recaptcha.validator.msg;
        }
    }
});
