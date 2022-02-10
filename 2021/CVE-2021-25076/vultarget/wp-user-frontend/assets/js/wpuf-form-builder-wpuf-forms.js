;(function($) {
    'use strict';

    /**
     * Only proceed if current page is a 'Post Forms' form builder page
     */
    if (!$('#wpuf-form-builder.wpuf-form-builder-post').length) {
        return;
    }

    window.wpuf_forms_mixin_root = {
        data: function () {
            return {
                validation_error_msg: wpuf_form_builder.i18n.any_of_three_needed
            };
        },

        methods: {
            // wpuf_forms must have either 'post_title', 'post_content' or 'post_excerpt'
            // field template
            validate_form_before_submit: function () {
                var is_valid = false;

                _.each(this.form_fields, function (form_field) {
                    if (_.indexOf(['post_title', 'post_content', 'post_excerpt'], form_field.template) >= 0) {
                        is_valid = true;
                        return;
                    }

                    // check in column field
                    if (form_field.template === 'column_field' ) {
                        var innerColumnFields = form_field.inner_fields;

                        for (const columnFields in innerColumnFields) {
                            if (innerColumnFields.hasOwnProperty(columnFields)) {
                                var columnFieldIndex = 0;

                                //console.log(innerColumnFields[columnFields].length);
                                while (columnFieldIndex < innerColumnFields[columnFields].length) {
                                    if (_.indexOf(['post_title', 'post_content', 'post_excerpt'], innerColumnFields[columnFields][columnFieldIndex].template) >= 0) {
                                        is_valid = true;
                                        return;
                                    }
                                    columnFieldIndex++;
                                }
                            }
                        }
                    }
                });

                return is_valid;
            }
        }
    };

    window.wpuf_forms_mixin_builder_stage = {
        data: function () {
            return {
                label_type: 'left',
                post_form_settings: {
                    submit_text: '',
                    draft_post: false,
                }
            };
        },

        mounted: function () {
            var self = this;

            // submit button text
            this.post_form_settings.submit_text = $('[name="wpuf_settings[submit_text]"]').val();

            $('[name="wpuf_settings[submit_text]"]').on('change', function () {
                self.post_form_settings.submit_text = $(this).val();
            });

            $('[name="wpuf_settings[label_position]"]').on('change', function () {
                self.label_type = $(this).val();
            });

            $('[name="wpuf_settings[label_position]"]').trigger('change');

            // draft post text
            this.post_form_settings.draft_post = $('[type="checkbox"][name="wpuf_settings[draft_post]"]').is(':checked') ? true : false;
            $('[type="checkbox"][name="wpuf_settings[draft_post]"]').on('change', function () {
                self.post_form_settings.draft_post = $(this).is(':checked') ? true : false;
            });

            // set taxonomies according to selected post type
            var post_type_dropdown = $('select[name="wpuf_settings[post_type]"]'),
                post_type          = post_type_dropdown.val();

            this.set_taxonomies(post_type);

            post_type_dropdown.on('change', function () {
                self.set_taxonomies($(this).val());
            });
        },

        methods: {
            set_taxonomies: function (post_type) {
                var self       = this,
                    taxonomies = wpuf_form_builder.wp_post_types[post_type],
                    tax_names  = taxonomies ? Object.keys(taxonomies) : [];

                self.$store.commit('set_panel_section_fields', {
                    id: 'taxonomies',
                    fields: tax_names
                });

                // Bind jquery ui draggable. But first destory any previous binding
                Vue.nextTick(function () {
                    var buttons = $('#panel-form-field-buttons-taxonomies .button');

                    buttons.each(function () {
                        if ($(this).draggable('instance')) {
                            $(this).draggable('destroy');
                        }
                    });

                    buttons.draggable({
                        connectToSortable: '#form-preview-stage .wpuf-form,  .wpuf-column-inner-fields .wpuf-column-fields-sortable-list',
                        helper: 'clone',
                        revert: 'invalid',
                        cancel: '.button-faded',
                    }).disableSelection();
                });
            },

            // executed in 'builder-stage' component by 'is_template_available' method
            is_post_tags_template_available: function () {
                return true;
            },

            // executed in 'builder-stage' component by 'is_template_available' method
            is_taxonomy_template_available: function (field) {
                return this.field_settings[field.name] ? true : false;
            }
        }
    };

    window.wpuf_forms_mixin_field_options = {
        methods: {
            form_field_post_tags_title: function () {
                return this.$store.state.field_settings.post_tag.title;
            },

            form_field_taxonomy_title: function (form_field) {
                return this.$store.state.field_settings[form_field.name].title;
            },

            settings_post_tags: function () {
                return this.$store.state.field_settings.post_tag.settings;
            },

            settings_taxonomy: function (form_field) {
                return this.$store.state.field_settings[form_field.name].settings;
            }
        }
    };
})(jQuery);
