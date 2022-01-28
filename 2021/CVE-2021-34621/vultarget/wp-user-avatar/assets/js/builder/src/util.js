import $ from 'jquery';

export default {
    addField(fieldType, fieldCategory, collection, position) {
        let footerModel = collection.findWhere({fieldType: "footer"});
        let instance = 'pp_form_builder_' + fieldCategory + '_fields';
        position = typeof position !== 'undefined' ? position : collection.indexOf(footerModel);

        const model = _.extend(window[instance][fieldType], {fieldType: fieldType});

        collection.add(model, {at: position, sort: false});
    },

    save_changes(collection) {
        $('#pp-form-builder-fields-settings').val(JSON.stringify(collection.toJSON()));
        $('.pp_edit_form form').submit();
    },
}