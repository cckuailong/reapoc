import Backbone from 'backbone';
import $ from 'jquery';

export default Backbone.View.extend({
    el: '#pp-form-builder-metabox',

    events: {
        "click .pp_upload_button": "media_upload",
    },

    initialize() {

        $('.pp-color-field', this.$el).wpColorPicker();
        $('.ppselect2', this.$el).select2();
        this.tabify();

        new jBox('Tooltip', {
            attach: '.pp-form-builder-help-tip',
            maxWidth: 200,
            theme: 'TooltipDark'
        });

        // Makes tooltip close to the color picker field
        $('.form-field .wp-picker-container', this.$el).parent('.pp-field-row-content').css('width', 'auto');
    },

    tabify() {
        $('ul.pp-tabs li a', this.$el).click(function (e) {
            e.preventDefault();
            $('.pp-form-builder_options_panel').hide();
            $('#pp-form-builder-metabox ul.pp-tabs li').removeClass('active');
            $(this).parent().addClass('active');
            $($(this).attr('href')).show();

        }).get(0).click();
    },

    media_upload(e) {

        e.preventDefault();

        let frame, _this = $(e.target);

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media.frames.file_frame = wp.media({
            frame: 'select',
            multiple: false,
            library: {
                type: 'image' // limits the frame to show only images
            },
        });

        frame.on('select', function () {
            let attachment = frame.state().get('selection').first().toJSON();
            _this.parents('.pp_upload_field_container').find('.pp_upload_field').val(attachment.url);

        });

        frame.open();
    }
});