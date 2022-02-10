Vue.component('help-text', {
    template: '#tmpl-wpuf-help-text',

    props: {
        text: {
            type: String,
            default: ''
        },

        placement: {
            type: String,
            default: 'top',
            validator: function (placement) {
                return ['top', 'right', 'bottom', 'left'].indexOf(placement) >= 0;
            }
        }
    },

    mounted: function () {
        $(this.$el).tooltip();
    }
});
