import Backbone from 'backbone';
import wp from 'wp';
import $ from 'jquery';
import FieldSettingsView from "./field-settings";

export default Backbone.View.extend({

    className: 'pp-builder-element',

    template: wp.template('pp-form-builder-field-bar'),

    events: {
        "click .pp-builder-element-expand-button.pp-settings": "reveal_settings",
        "click .pp-builder-element-expand-button.pp-delete": "delete_field",
        "click .pp-builder-element-expand-button.pp-clone": "clone_field"
    },

    reveal_settings(e) {
        e.preventDefault();

        $(e.target).parent('a').blur();
        const fieldType = this.model.get('fieldType');
        let activeSettingsView = new FieldSettingsView({fieldType, model: this.model});
        activeSettingsView.render();
        $('body').append(activeSettingsView.$el);

        var cache = $('.pp-form-control-wpeditor');
        if (cache.length > 0) {
            cache.each(function () {
                var id = $(this).attr('id');
                $('#' + id).pp_wp_editor({mode: 'tmce'});

                tinymce.get(id).on('keyup change undo redo SetContent', function () {
                    this.save();
                });
            });
        }
    },

    delete_field(e) {
        e.preventDefault();
        if (confirm(pp_form_builder.confirm_delete)) {
            $(e.target).parent('a').blur();
            this.remove();
            this.collection.remove(this.model);
        }
    },

    clone_field(e) {
        e.preventDefault();
        $(e.target).parent('a').blur();
        this.collection.add(
            this.model.clone(),
            {
                at: this.collection.indexOf(this.model)
            }
        );
    },

    render() {
        const fieldType = this.model.get('fieldType');
        const fieldTitle = typeof pp_form_builder_combined_fields[fieldType] !== 'undefined' &&
        pp_form_builder_combined_fields[fieldType]["fieldTitle"] !== 'undefined' ? pp_form_builder_combined_fields[fieldType]["fieldTitle"] :
            this.model.get('fieldTitle');

        this.$el.html(this.template({
            fieldType: fieldType,
            fieldTitle: fieldTitle,
            fieldIcon: this.model.get('fieldIcon'),
            fieldBarTitle: this.model.get('fieldBarTitle')
        })).data('modelCID', this.model.cid);
    }
});