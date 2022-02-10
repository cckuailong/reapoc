/**
 * Field template: Custom HTML
 */
Vue.component('form-custom_html', {
    template: '#tmpl-wpuf-form-custom_html',

    mixins: [
        wpuf_mixins.form_field_mixin
    ],

    data: function () {
        return {
            raw_html: '<p>from data</p>'
        };
    }
});
