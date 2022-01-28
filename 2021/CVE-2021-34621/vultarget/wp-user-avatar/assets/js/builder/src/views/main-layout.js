import Backbone from 'backbone';
import FieldBarView from './field-bar';
import $ from 'jquery';
import Util from '../util';

export default Backbone.View.extend({

    el: '#pp-form-builder',

    initialize() {

        _.bindAll(this, 'update_sort_index');
        _.bindAll(this, 'addFieldOnDrop');

        this.listenTo(this.collection, 'add', this.render);
        this.listenTo(this.collection, 'remove', this.render);
        this.listenTo(this.collection, 'pp_fieldBarTitle_changed', this.render);
    },

    initSortable() {
        this.$el.sortable({
            items: ".pp-builder-element",
            cursor: "move",
            placeholder: "pp-form-builder-drag-bg",
            update: this.update_sort_index,
            receive: this.addFieldOnDrop
        });
    },

    addFieldOnDrop(event, ui) {

        let position, previousFieldBarModelCID;

        if (ui.helper.prev().length === 0 || typeof ui.helper.prev().data('modelCID') === 'undefined') {
            position = 0;
        }
        else {
            previousFieldBarModelCID = ui.helper.prev().data('modelCID');
            position = this.collection.get(previousFieldBarModelCID).get('sortID');
        }

        let fieldType = ui.helper.find('a').data('field-type');
        let fieldCategory = ui.helper.find('a').data('field-category');

        Util.addField(fieldType, fieldCategory, this.collection, position);

        this.update_sort_index();

        // remove the redundant helper/clone representing the draggable.
        ui.helper.remove();
    },

    update_sort_index() {
        let collection = this.collection;
        $('.pp-builder-form-content .pp-builder-element').each(function (index) {
            index++;
            let cid = $(this).data('modelCID');
            collection.get(cid).set('sortID', index);
        });

        collection.sort();
    },

    render() {

        let formSettings = this.collection;

        let output = [];
        formSettings.each(function (formSetting) {
            const field = new FieldBarView({
                model: formSetting,
                collection: formSettings
            });
            field.render();
            output.push(field.el);
        });

        this.$el.find('.pp-form-builder-body .pp-builder-form-content').html(output);

        this.initSortable();
    }
});