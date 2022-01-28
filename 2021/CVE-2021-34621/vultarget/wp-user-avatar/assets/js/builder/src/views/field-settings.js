import Backbone from 'backbone';
import wp from 'wp';
import $ from 'jquery';

export default Backbone.View.extend({

    className: 'pp-form-buider-settings-popup-container',

    iconPickerTemplate: wp.template('pp-form-builder-material-icon'),

    events: {
        //"click": "cancel_settings_outside",
        "click .pp-form-cancel-btn": "cancel_settings",
        "click .pp-form-buider-settings-popup-tab-menu": "switch_tab",
        "click .pp-form-save-btn": "save_changes",
        "click .pp-form-control-icon-picker": "icon_picker",
        "keyup input[name=label].pp-form-control": "make_label_placeholder",
    },

    initialize(options) {
        if (typeof options.fieldType === 'undefined') throw new Error('Field Type required');
        this.options = options;
        _.bindAll(this, 'switch_tab');
        _.bindAll(this, 'save_changes');
        _.bindAll(this, 'icon_picker');
    },

    icon_picker(e) {
        e.preventDefault();

        const _this = this;
        const origin_button = $(e.target);

        const instance = _this.jboxInstance = new jBox('Modal', {
            title: $('#pp-form-material-icon-picker-tmpl-title').html(),
            maxWidth: 600,
            zIndex: 999999999,
            addClass: 'pp-icon-picker-jbox-wrap',
            overlayClass: 'pp-icon-picker-jbox-overlay',
            animation: 'zoomIn',
            content: $('#pp-form-material-icon-picker-tmpl'),
            onOpen: function () {
                let hidden_input = $('input.pp-form-control', origin_button.parent());
                let hidden_input_val = hidden_input.val();

                const cache = $('#pp-form-material-icon-picker-tmpl');
                cache.find('.pp-form-material-icon-wrap').removeClass('pp-active');

                if (hidden_input_val !== "") {
                    cache.find('.pp-form-material-icon-wrap[data-material-icon="' + hidden_input_val + '"]').addClass('pp-active');
                }

                $('.pp-form-material-icon-wrap').click(function () {
                    let icon = $(this).data('material-icon');
                    origin_button.html(_this.iconPickerTemplate({icon}));
                    hidden_input.val(icon);
                    _this.jboxInstance.close();
                });
            }
        });

        instance.open();
    },

    save_changes(e) {
        e.preventDefault();
        let _this = this;

        $('.pp-form-control').each(function () {
            let key = $(this).attr('name'),
                value = $(this).val();
            if ($(this).attr('type') === 'checkbox') {
                value = this.checked;
            }

            if (typeof key === 'undefined') return;

            _this.options.model.set(key, value);
        });

        if ($('#label', this.$el).length > 0) {
            _this.options.model.set('fieldBarTitle', $('#label', this.$el).val());
        } else {
            _this.options.model.set('fieldBarTitle', $('#placeholder', this.$el).val());
        }

        _this.remove();
    },

    switch_tab(e) {
        e.preventDefault();
        $('.pp-form-buider-settings-popup-tab-menu', this.$el).removeClass('active');
        $(e.target).addClass('active');

        $('.pp-form-buider-settings-popup-tab-content', this.$el).hide();
        $($(e.target).attr('href')).show();
    },

    make_label_placeholder(e) {
        e.preventDefault();
        var cache = $('input[name=placeholder]', this.$el);
        if (this.$el.data('make_label_placeholder_flag') !== true) {
            this.$el.data('make_label_placeholder_flag', cache.val() === '');
        }

        if (this.$el.data('make_label_placeholder_flag') === true) {
            cache.val($(e.target).val());
        }
    },

    cancel_settings_outside(e) {
        if (e.target == this.el) {
            this.remove();
        }
    },

    cancel_settings(e) {
        e.preventDefault();
        this.remove();
    },

    render() {
        let fieldType = this.options.fieldType.indexOf('reg-cpf') !== -1 ? 'reg-cpf' : this.options.fieldType;
        fieldType = fieldType.indexOf('edit-profile-cpf') !== -1 ? 'edit-profile-cpf' : fieldType;
        let definedFieldType = this.options.model.get('definedFieldType');

        if (typeof definedFieldType !== 'undefined') {
            fieldType = fieldType + '-' + definedFieldType;
        }

        const tmpl = wp.template('pp-form-builder-popup-settings-' + fieldType);
        this.$el.html(tmpl(this.options.model.toJSON()));
        $('.pp-form-buider-settings-popup-tab-menu', this.$el).eq(0).addClass('active');
        $('.pp-form-buider-settings-popup-tab-content', this.$el).hide().eq(0).show();
    }
});