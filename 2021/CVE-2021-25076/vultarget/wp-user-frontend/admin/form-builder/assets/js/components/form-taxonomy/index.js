/**
 * Field template: taxonomy
 */
Vue.component('form-taxonomy', {
    template: '#tmpl-wpuf-form-taxonomy',

    mixins: [
        wpuf_mixins.form_field_mixin
    ],

    computed: {
        terms: function () {
            var i;

            for (i in wpuf_form_builder.wp_post_types) {
                var taxonomies = wpuf_form_builder.wp_post_types[i];

                if (taxonomies.hasOwnProperty(this.field.name)) {
                    var tax_field = taxonomies[this.field.name];

                    if (tax_field.terms) {
                        return tax_field.terms;
                    }
                }
            }

            return [];
        },

        sorted_terms: function () {
            var self  = this;
            var terms = $.extend(true, [], this.terms);

            // selection type and terms
            if (this.field.exclude_type && this.field.exclude) {
                var filter_ids = [];

                if ( this.field.exclude.length > 0 ) {
                    filter_ids = this.field.exclude.map(function (id) {
                        id = id.trim();
                        id = parseInt(id);
                        return id;
                    }).filter(function (id) {
                        return isFinite(id);
                    });
                }

                terms = terms.filter(function (term) {

                    switch(self.field.exclude_type) {
                        case 'exclude':
                            return _.indexOf(filter_ids, term.term_id) < 0;

                        case 'include':
                            return _.indexOf(filter_ids, term.term_id) >= 0;

                        case 'child_of':
                            return _.indexOf(filter_ids, parseInt(term.parent)) >= 0;
                    }
                });
            }

            // order
            terms = _.sortBy(terms, function (term) {
                return term[self.field.orderby];
            });

            if ('DESC' === this.field.order) {
                terms = terms.reverse();
            }

            var parent_terms = terms.filter(function (term) {
                return !term.parent;
            });

            parent_terms.map(function (parent) {
                parent.children = self.get_child_terms(parent.term_id, terms);
            });

            return parent_terms.length ? parent_terms : terms;
        }
    },

    methods: {
        get_child_terms: function (parent_id, terms) {
            var self = this;

            var child_terms = terms.filter(function (term) {
                return parseInt(term.parent) === parseInt(parent_id);
            });

            child_terms.map(function (child) {
                child.children = self.get_child_terms(child.term_id, terms);
            });

            return child_terms;
        },

        get_term_dropdown_options: function () {
            var self    = this,
                options = '';

            if ( this.field.type === 'select' ) {
                options = '<option value="">' + this.field.first + '</option>';
            }

            _.each(self.sorted_terms, function (term) {
                options += self.get_term_dropdown_options_children(term, 0);
            });

            return options;
        },

        get_term_dropdown_options_children: function (term, level) {
            var self   = this,
                option = '';

            var indent = '',
                i = 0;

            for (i = 0; i < level; i++) {
                indent += '&nbsp;&nbsp;';
            }

            option += '<option value="' + term.id + '">' + indent + term.name + '</option>';

            if (term.children.length) {
                _.each(term.children, function (child_term) {
                    option += self.get_term_dropdown_options_children(child_term, (level + 1));
                });
            }

            return option;
        },

        get_term_checklist: function () {
            var self      = this,
                checklist = '';

            checklist += '<ul class="wpuf-category-checklist">';

            _.each(this.sorted_terms, function (term) {
                checklist += self.get_term_checklist_li(term);
            });

            checklist += '</ul>';

            return checklist;
        },

        get_term_checklist_li: function (term) {
            var self = this,
                li   = '';

            li += '<li><label class="selectit"><input type="checkbox"> ' + term.name + '</label></li>';

            if (term.children.length) {
                li += '<ul class="children">';

                _.each(term.children, function (child_term) {
                    li += self.get_term_checklist_li(child_term);
                });

                li += '</ul>';
            }

            return li;
        },

        get_term_checklist_inline: function () {
            var self      = this,
                checklist = '';

            _.each(this.sorted_terms, function (term) {
                checklist += self.get_term_checklist_li_inline(term);
            });

            return checklist;
        },

        get_term_checklist_li_inline: function (term) {
            var self = this,
                li_inline   = '';

            li_inline += '<label class="wpuf-checkbox-inline"><input type="checkbox"> ' + term.name + '</label>';

            if (term.children.length) {
                _.each(term.children, function (child_term) {
                    li_inline += self.get_term_checklist_li_inline(child_term);
                });
            }

            return li_inline;
        }
    }
});
