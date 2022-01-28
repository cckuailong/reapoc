import Backbone from 'backbone';
import $ from 'jquery';
import wp from 'wp';
import _ from 'underscore';
import Util from '../util';

export default Backbone.View.extend({

    el: '.pp-form-builder-sidebar-wrap',

    template: wp.template('pp-form-builder-sidebar-fields-block'),

    events: {
        "click .pp-draggable-field": "add_field",
        "click .pp-form-save-changes": "save_changes",
    },

    initialize() {
        this.listenTo(this.collection, 'add', this.disable_active_standard_fields);
        this.listenTo(this.collection, 'remove', this.enable_inactive_standard_fields);
    },

    initDraggable() {
        this.$el.find(".pp-draggable-field").draggable({
            connectToSortable: "#pp-form-builder",
            helper: 'clone',
            revert: 'invalid',
            delay: 10,
        });
    },

    add_field(e) {
        e.preventDefault();
        if ($(e.target).parent('.pp-draggable-field').hasClass('ui-draggable-disabled')) return false;

        let fieldType = $(e.target).data('field-type');
        let fieldCategory = $(e.target).data('field-category');

        Util.addField(fieldType, fieldCategory, this.collection);
    },

    save_changes(e) {
        e.preventDefault();
        Util.save_changes(this.collection);
    },

    disable_active_standard_fields() {
        this.collection.each(function (model) {

            const fieldType = model.get('fieldType');
            // we need to allow profile custom field bar to be included multiple times.
            if (_.contains(pp_form_builder_fields_multiple_addition, fieldType)) return;

            $('[data-field-type="' + fieldType + '"]', '#pp-form-builder-standard-fields').attr('disabled', 'disabled').parent('.pp-draggable-field').draggable('disable');
            $('[data-field-type="' + fieldType + '"]', '#pp-form-builder-defined-fields').attr('disabled', 'disabled').parent('.pp-draggable-field').draggable('disable');
        });
    },

    enable_inactive_standard_fields(model) {
        const fieldType = model.get('fieldType');
        // we need to allow profile custom field bar to be included multiple times.
        if (_.contains(pp_form_builder_fields_multiple_addition, fieldType)) return;

        $('[data-field-type="' + fieldType + '"]', '#pp-form-builder-standard-fields').removeAttr('disabled', 'disabled').parent('.pp-draggable-field').draggable('enable');
        $('[data-field-type="' + fieldType + '"]', '#pp-form-builder-defined-fields').removeAttr('disabled', 'disabled').parent('.pp-draggable-field').draggable('enable');
    },

    render() {

        let rendered_html = [
            this.template({
                fieldsBlockType: 'standard',
                fields: pp_form_builder_standard_fields
            }),
        ];

        if (_.isEmpty(pp_form_builder_defined_fields) === false) {
            rendered_html.push(
                this.template({
                    fieldsBlockType: 'defined',
                    fields: pp_form_builder_defined_fields
                })
            );
        }

        if (_.isEmpty(pp_form_builder_wc_billing_fields) === false) {
            rendered_html.push(
                this.template({
                    fieldsBlockType: 'wc_billing',
                    fields: pp_form_builder_wc_billing_fields
                })
            );
        }

        if (_.isEmpty(pp_form_builder_wc_shipping_fields) === false) {
            rendered_html.push(
                this.template({
                    fieldsBlockType: 'wc_shipping',
                    fields: pp_form_builder_wc_shipping_fields
                })
            );
        }

        // Since shortcode builder doesn't have extra fields, let's comment it out.
        // if (_.isEmpty(pp_form_builder_extra_fields) === false) {
        //     rendered_html.push(
        //         this.template({
        //             fieldsBlockType: 'extra',
        //             fields: pp_form_builder_extra_fields
        //         })
        //     );
        // }

        this.$el.find('#pp-form-builder-sidebar-fields-block').html(rendered_html);

        this.initDraggable();

        this.disable_active_standard_fields();
    }
});