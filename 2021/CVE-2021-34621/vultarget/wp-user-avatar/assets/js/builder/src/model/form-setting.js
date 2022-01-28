import Backbone from 'backbone';

export default Backbone.Model.extend({
    initialize() {
        this.on('change:fieldBarTitle', function (model, title) {
            this.trigger('pp_fieldBarTitle_changed');
        });
    }
});