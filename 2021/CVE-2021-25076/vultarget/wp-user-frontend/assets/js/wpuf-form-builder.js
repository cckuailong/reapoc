;(function($) {
    'use strict';

    /**
     * Only proceed if current page is a form builder page
     */
    if (!$('#wpuf-form-builder').length) {
        return;
    }

    if (!Array.prototype.hasOwnProperty('swap')) {
        Array.prototype.swap = function (from, to) {
            this.splice(to, 0, this.splice(from, 1)[0]);
        };
    }

    // check if an element is visible in browser viewport
    function is_element_in_viewport (el) {
        if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
        }

        var rect = el.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
        );
    }

    /**
     * Vuex Store data
     */
    var wpuf_form_builder_store = new Vuex.Store({
        state: {
            post: wpuf_form_builder.post,
            form_fields: wpuf_form_builder.form_fields,
            panel_sections: wpuf_form_builder.panel_sections,
            field_settings: wpuf_form_builder.field_settings,
            notifications: wpuf_form_builder.notifications,
            settings: wpuf_form_builder.form_settings,
            current_panel: 'form-fields',
            editing_field_id: 0, // editing form field id
            show_custom_field_tooltip: true,
            index_to_insert: 0,
        },

        mutations: {
            set_form_fields: function (state, form_fields) {
                Vue.set(state, 'form_fields', form_fields);
            },

            set_form_settings: function (state, value) {
                Vue.set(state, 'settings', value);
            },

            // set the current panel
            set_current_panel: function (state, panel) {
                if ('field-options' !== state.current_panel &&
                    'field-options' === panel &&
                    state.form_fields.length
                ) {
                    state.editing_field_id = state.form_fields[0].id;
                }

                state.current_panel = panel;

                // reset editing field id
                if ('form-fields' === panel) {
                    state.editing_field_id = 0;
                }
            },

            // add show property to every panel section
            panel_add_show_prop: function (state) {
                state.panel_sections.map(function (section, index) {
                    if (!section.hasOwnProperty('show')) {
                        Vue.set(state.panel_sections[index], 'show', true);
                    }
                });
            },

            // toggle panel sections
            panel_toggle: function (state, index) {
                state.panel_sections[index].show = !state.panel_sections[index].show;
            },

            // open field settings panel
            open_field_settings: function (state, field_id) {
                var field = state.form_fields.filter(function(item) {
                    return parseInt(field_id) === parseInt(item.id);
                });

                if ('field-options' === state.current_panel && field[0].id === state.editing_field_id) {
                    return;
                }

                if (field.length) {
                    state.editing_field_id = 0;
                    state.current_panel = 'field-options';

                    setTimeout(function () {
                        state.editing_field_id = field[0].id;
                    }, 400);
                }
            },

            update_editing_form_field: function (state, payload) {
                var i = 0;

                for (i = 0; i < state.form_fields.length; i++) {
                    // check if the editing field exist in normal fields
                    if (state.form_fields[i].id === parseInt(payload.editing_field_id)) {
                        if (payload.field_name === 'name'  && ! state.form_fields[i].hasOwnProperty('is_new') ) {
                            continue;
                        } else {
                            state.form_fields[i][payload.field_name] = payload.value;
                        }

                    }

                    // check if the editing field belong to a column field
                    if (state.form_fields[i].template === 'column_field') {
                        var innerColumnFields = state.form_fields[i].inner_fields;

                        for (const columnFields in innerColumnFields) {
                            if (innerColumnFields.hasOwnProperty(columnFields)) {
                                var columnFieldIndex = 0;

                                while (columnFieldIndex < innerColumnFields[columnFields].length) {
                                    if (innerColumnFields[columnFields][columnFieldIndex].id === parseInt(payload.editing_field_id)) {
                                       innerColumnFields[columnFields][columnFieldIndex][payload.field_name] = payload.value;
                                    }
                                    columnFieldIndex++;
                                }
                            }
                        }
                    }
                }
            },

            // add new form field element
            add_form_field_element: function (state, payload) {
                state.form_fields.splice(payload.toIndex, 0, payload.field);
                var sprintf = wp.i18n.sprintf;
                var __ = wp.i18n.__;
                // bring newly added element into viewport
                Vue.nextTick(function () {
                    var el = $('#form-preview-stage .wpuf-form .field-items').eq(payload.toIndex);
                    if ('yes' == payload.field.is_meta && state.show_custom_field_tooltip) {

                        var image_one  = wpuf_assets_url.url + '/images/custom-fields/settings.png';
                        var image_two  = wpuf_assets_url.url + '/images/custom-fields/advance.png';
                        var html       = '<div class="wpuf-custom-field-instruction">';
                            html      += '<div class="step-one">';
                            html      += sprintf( '<p style="font-weight: 400">%s<strong><code>%s</code></strong>%s"</p>', __( 'Navigate through', 'wp-user-frontend' ), __( 'WP-admin > WPUF > Settings > Frontend Posting', 'wp-user-frontend' ), __( '- there you have to check the checkbox: "Show custom field data in the post content area', 'wp-user-frontend' ) );
                            html      += '<img src="'+ image_one +'" alt="settings">';
                            html      += '</div>';
                            html      += '<div class="step-two">';
                            html      += sprintf( '<p style="font-weight: 400">%s<strong>%s</strong>%s</p>', __( 'Edit the custom field inside the post form and on the right side you will see', 'wp-user-frontend' ), __( '"Advanced Options".', 'wp-user-frontend' ), __( ' Expand that, scroll down and you will see "Show data on post" - set this yes.', 'wp-user-frontend' ) );
                            html      += '<img src="' + image_two + '" alt="custom field data">';
                            html      += '</div>';
                            html      += '</div>';
                        swal({
                            title: __( 'Do you want to show custom field data inside your post ?', 'wp-user-frontend' ),
                            html: html,
                            showCancelButton: true,
                            confirmButtonColor: '#d54e21',
                            confirmButtonText: "Don't show again",
                            cancelButtonText: 'Okay',
                            confirmButtonClass: 'btn btn-success',
                            cancelButtonClass: 'btn btn-success',
                            cancelButtonColor: '#007cba'
                        }).then((result) => {
                            if (result) {
                                state.show_custom_field_tooltip = false;
                            } else {

                            }
                        } );
                    }

                    if (el && !is_element_in_viewport(el.get(0))) {
                        $('#builder-stage section').scrollTo(el, 800, {offset: -50});
                    }
                });
            },

            // sorting inside stage
            swap_form_field_elements: function (state, payload) {
                state.form_fields.swap(payload.fromIndex, payload.toIndex);
            },

            clone_form_field_element: function (state, payload) {
                var field = _.find(state.form_fields, function (item) {
                    return parseInt(item.id) === parseInt(payload.field_id);
                });

                var clone = $.extend(true, {}, field),
                    index = parseInt(payload.index) + 1;

                let column_field = state.form_fields.find(function (field) {
                    return field.id === payload.field_id && field.input_type === 'column_field';
                });

                if (column_field){
                    let columns = ['column-1','column-2','column-3'];
                    columns.forEach(function (column) {
                        let inner_field = clone.inner_fields[column];
                        if(inner_field.length){
                            inner_field.forEach(function (field) {
                                field.id     = Math.floor(Math.random() * (9999999999 - 999999 + 1)) + 999999;
                                field.name   = field.name + '_copy';
                                field.is_new = true;
                            });
                        }
                    });
                }

                clone.id     = payload.new_id;
                clone.name   = clone.name + '_copy';
                clone.is_new = true;

                state.form_fields.splice(index, 0, clone);
            },

            // delete a field
            delete_form_field_element: function (state, index) {
                state.current_panel = 'form-fields';
                state.form_fields.splice(index, 1);
            },

            // set fields for a panel section
            set_panel_section_fields: function (state, payload) {
                var section = _.find(state.panel_sections, function (item) {
                    return item.id === payload.id;
                });

                section.fields = payload.fields;
            },

            // notifications
            addNotification: function(state, payload) {
                state.notifications.push(payload);
            },

            deleteNotification: function(state, index) {
                state.notifications.splice(index, 1);
            },

            cloneNotification: function(state, index) {
                var clone = $.extend(true, {}, state.notifications[index]);

                index = parseInt(index) + 1;
                state.notifications.splice(index, 0, clone);
            },

            // update by it's property
            updateNotificationProperty: function(state, payload) {
                state.notifications[payload.index][payload.property] = payload.value;
            },

            updateNotification: function(state, payload) {
                state.notifications[payload.index] = payload.value;
            },

            // add new form field element to column field
            add_column_inner_field_element: function (state, payload) {
                var columnFieldIndex = state.form_fields.findIndex(field => field.id === payload.toWhichColumnField);

                if (state.form_fields[columnFieldIndex].inner_fields[payload.toWhichColumn] === undefined) {
                    state.form_fields[columnFieldIndex].inner_fields[payload.toWhichColumn] = [];
                }

                if (state.form_fields[columnFieldIndex].inner_fields[payload.toWhichColumn] !== undefined) {
                    var innerColumnFields   = state.form_fields[columnFieldIndex].inner_fields[payload.toWhichColumn];

                    if ( innerColumnFields.filter(innerField => innerField.name === payload.field.name).length <= 0 ) {
                        state.form_fields[columnFieldIndex].inner_fields[payload.toWhichColumn].splice(payload.toIndex, 0, payload.field);
                    }
                }
            },

            move_column_inner_fields: function(state, payload) {
                var columnFieldIndex = state.form_fields.findIndex(field => field.id === payload.field_id),
                    innerFields  = payload.inner_fields,
                    mergedFields = [];

                Object.keys(innerFields).forEach(function (column) {
                    // clear column-1, column-2 and column-3 fields if move_to specified column-1
                    // add column-1, column-2 and column-3 fields to mergedFields, later mergedFields will move to column-1 field
                    if (payload.move_to === "column-1") {
                        innerFields[column].forEach(function(field){
                            mergedFields.push(field);
                        });

                        // clear current column inner fields
                        state.form_fields[columnFieldIndex].inner_fields[column].splice(0, innerFields[column].length);
                    }

                    // clear column-2 and column-3 fields if move_to specified column-2
                    // add column-2 and column-3 fields to mergedFields, later mergedFields will move to column-2 field
                    if (payload.move_to === "column-2") {
                        if ( column === "column-2" || column === "column-3" ) {
                            innerFields[column].forEach(function(field){
                                mergedFields.push(field);
                            });

                            // clear current column inner fields
                            state.form_fields[columnFieldIndex].inner_fields[column].splice(0, innerFields[column].length);
                        }
                    }
                });

                // move inner fields to specified column
                if (mergedFields.length !== 0) {
                    mergedFields.forEach(function(field){
                        state.form_fields[columnFieldIndex].inner_fields[payload.move_to].splice(0, 0, field);
                    });
                }
            },

            // sorting inside column field
            swap_column_field_elements: function (state, payload) {
                var columnFieldIndex = state.form_fields.findIndex(field => field.id === payload.field_id),
                    fieldObj         = state.form_fields[columnFieldIndex].inner_fields[payload.fromColumn][payload.fromIndex];

                if( payload.fromColumn !== payload.toColumn) {
                    // add the field object to the target column
                    state.form_fields[columnFieldIndex].inner_fields[payload.toColumn].splice(payload.toIndex, 0, fieldObj);

                    // remove the field index from the source column
                    state.form_fields[columnFieldIndex].inner_fields[payload.fromColumn].splice(payload.fromIndex, 1);
                }else{
                    state.form_fields[columnFieldIndex].inner_fields[payload.toColumn].swap(payload.fromIndex, payload.toIndex);
                }
            },

            // open field settings panel
            open_column_field_settings: function (state, payload) {
                var field = payload.column_field;

                if ('field-options' === state.current_panel && field.id === state.editing_field_id) {
                    return;
                }

                if (field) {
                    state.editing_field_id = 0;
                    state.current_panel = 'field-options';
                    state.editing_field_type = 'column_field';
                    state.editing_column_field_id = payload.field_id;
                    state.edting_field_column = payload.column;
                    state.editing_inner_field_index = payload.index;

                    setTimeout(function () {
                        state.editing_field_id = field.id;
                    }, 400);
                }
            },

            clone_column_field_element: function (state, payload) {
                var columnFieldIndex = state.form_fields.findIndex(field => field.id === payload.field_id);

                var field = _.find(state.form_fields[columnFieldIndex].inner_fields[payload.toColumn], function (item) {
                    return parseInt(item.id) === parseInt(payload.column_field_id);
                });

                var clone = $.extend(true, {}, field),
                    index = parseInt(payload.index) + 1;

                clone.id     = payload.new_id;
                clone.name   = clone.name + '_copy';
                clone.is_new = true;

                state.form_fields[columnFieldIndex].inner_fields[payload.toColumn].splice(index, 0, clone);
            },

            // delete a column field
            delete_column_field_element: function (state, payload) {
                var columnFieldIndex = state.form_fields.findIndex(field => field.id === payload.field_id);

                state.current_panel = 'form-fields';
                state.form_fields[columnFieldIndex].inner_fields[payload.fromColumn].splice(payload.index, 1);
            },


        }
    });

    /**
     * The main form builder vue instance
     */
    new Vue({
        el: '#wpuf-form-builder',

        mixins: wpuf_form_builder_mixins(wpuf_mixins.root),

        store: wpuf_form_builder_store,

        data: {
            is_form_saving: false,
            is_form_saved: false,
            is_form_switcher: false,
            post_title_editing: false,
            isDirty: false
        },

        computed: {
            current_panel: function () {
                return this.$store.state.current_panel;
            },

            post: function () {
                return this.$store.state.post;
            },

            form_fields_count: function () {
                return this.$store.state.form_fields.length;
            },

            form_fields: function () {
                return this.$store.state.form_fields;
            },

            notifications: function() {
                return this.$store.state.notifications;
            },

            settings: function() {
                return this.$store.state.settings;
            }
        },

        watch: {
            form_fields: {
                handler: function() {
                    this.isDirty = true;
                },
                deep: true
            }
        },

        created: function () {
            this.$store.commit('panel_add_show_prop');

            /**
             * This is the event hub we'll use in every
             * component to communicate between them
             */
            wpuf_form_builder.event_hub = new Vue();
        },

        mounted: function () {
            // primary nav tabs and their contents
            this.bind_tab_on_click($('#wpuf-form-builder > fieldset > .nav-tab-wrapper > a'), '#wpuf-form-builder');

            // secondary settings tabs and their contents
            var settings_tabs = $('#wpuf-form-builder-settings .nav-tab'),
                settings_tab_contents = $('#wpuf-form-builder-settings .tab-contents .group');

            settings_tabs.first().addClass('nav-tab-active');
            settings_tab_contents.first().addClass('active');

            this.bind_tab_on_click(settings_tabs, '#wpuf-form-builder-settings');

            var clipboard = new window.Clipboard('.form-id');
            $(".form-id").tooltip();

            var self = this;

            clipboard.on('success', function(e) {
                // Show copied tooltip
                $(e.trigger)
                    .attr('data-original-title', 'Copied!')
                    .tooltip('show');

                // Reset the copied tooltip
                setTimeout(function() {
                    $(e.trigger).tooltip('hide')
                    .attr('data-original-title', self.i18n.copy_shortcode);
                }, 1000);

                e.clearSelection();
            });

            window.onbeforeunload = function () {
                if ( self.isDirty ) {
                    return self.i18n.unsaved_changes;
                }
            };
        },

        methods: {
            // tabs and their contents
            bind_tab_on_click: function (tabs, scope) {
                tabs.on('click', function (e) {
                    e.preventDefault();

                    var button = $(this),
                        tab_contents = $(scope + ' > fieldset > .tab-contents'),
                        group_id = button.attr('href');

                    button.addClass('nav-tab-active').siblings('.nav-tab-active').removeClass('nav-tab-active');

                    tab_contents.children().removeClass('active');
                    $(group_id).addClass('active');
                });
            },

            // switch form
            switch_form: function () {
                this.is_form_switcher = (this.is_form_switcher) ? false : true;
            },

            // set current sidebar panel
            set_current_panel: function (panel) {
                this.$store.commit('set_current_panel', panel);
            },

            // save form builder data
            save_form_builder: function () {
                var self = this;

                if (_.isFunction(this.validate_form_before_submit) && !this.validate_form_before_submit()) {

                    this.warn({
                        text: this.validation_error_msg
                    });

                    return;
                }

                self.is_form_saving = true;
                self.set_current_panel('form-fields');

                var form_id = $('#wpuf-form-builder [name="wpuf_form_id"]').val();

                if ( typeof tinyMCE !== 'undefined' ) {
                    $('textarea[name="wpuf_settings[notification][verification_body]"]').val(tinyMCE.get('wpuf_verification_body_' + form_id).getContent());
                    $('textarea[name="wpuf_settings[notification][welcome_email_body]"]').val(tinyMCE.get('wpuf_welcome_email_body_' + form_id).getContent());
                }

                wp.ajax.send('wpuf_form_builder_save_form', {
                    data: {
                        form_data: $('#wpuf-form-builder').serialize(),
                        form_fields: JSON.stringify(self.form_fields),
                        notifications: JSON.stringify(self.notifications)
                    },

                    success: function (response) {
                        if (response.form_fields) {
                            self.$store.commit('set_form_fields', response.form_fields);
                        }

                        if (response.form_settings) {
                            self.$store.commit('set_form_settings', response.form_settings);
                        }

                        self.is_form_saving = false;
                        self.is_form_saved = true;

                        setTimeout(function(){
                            self.isDirty = false;
                        }, 500);

                        toastr.success(self.i18n.saved_form_data);
                    },

                    error: function () {
                        self.is_form_saving = false;
                    }
                });
            }
        }
    });

    var SettingsTab = {
        init: function() {
            $(function() {
                $('.datepicker').datetimepicker();
                $('.wpuf-ms-color').wpColorPicker();
            });

            $('#wpuf-metabox-settings').on('change', 'select[name="wpuf_settings[redirect_to]"]', this.settingsRedirect);
            $('#wpuf-metabox-settings-update').on('change', 'select[name="wpuf_settings[edit_redirect_to]"]', this.settingsRedirect);
            $('select[name="wpuf_settings[redirect_to]"]').change();
            $('select[name="wpuf_settings[edit_redirect_to]"]').change();

            // Form settings: Payment
            $('#wpuf-metabox-settings-payment').on('change', 'input[type=checkbox][name="wpuf_settings[payment_options]"]', this.settingsPayment);
            $('input[type=checkbox][name="wpuf_settings[payment_options]"]').trigger('change');

            // pay per post
            $('#wpuf-metabox-settings-payment').on('change', 'input[type=checkbox][name="wpuf_settings[enable_pay_per_post]"]', this.settingsPayPerPost);
            $('input[type=checkbox][name="wpuf_settings[enable_pay_per_post]"]').trigger('change');

            // force pack purchase
            $('#wpuf-metabox-settings-payment').on('change', 'input[type=checkbox][name="wpuf_settings[force_pack_purchase]"]', this.settingsForcePack);
            $('input[type=checkbox][name="wpuf_settings[force_pack_purchase]"]').trigger('change');

            // Form settings: Submission Restriction

            // Form settings: Guest post
            $('#wpuf-metabox-submission-restriction').on('change', 'input[type=checkbox][name="wpuf_settings[guest_post]"]', this.settingsGuest);
            $('input[type=checkbox][name="wpuf_settings[guest_post]"]').trigger('change');
            $('#wpuf-metabox-submission-restriction').on('change', 'input[type=checkbox][name="wpuf_settings[role_base]"]', this.settingsRoles);
            $('input[type=checkbox][name="wpuf_settings[role_base]"]').trigger('change');

            // From settings: User details
            $('#wpuf-metabox-submission-restriction').on('change', 'input[type=checkbox][name="wpuf_settings[guest_details]"]', this.settingsGuestDetails);

            // From settings: schedule form
            $('#wpuf-metabox-submission-restriction').on('change', 'input[type=checkbox][name="wpuf_settings[schedule_form]"]', this.settingsRestriction);
            $('input[type=checkbox][name="wpuf_settings[schedule_form]"]').trigger('change');

            // From settings: limit entries
            $('#wpuf-metabox-submission-restriction').on('change', 'input[type=checkbox][name="wpuf_settings[limit_entries]"]', this.settingsLimit);
            $('input[type=checkbox][name="wpuf_settings[limit_entries]"]').trigger('change');

            this.changeMultistepVisibility($('.wpuf_enable_multistep_section :input[type="checkbox"]'));
            var self = this;
            $('.wpuf_enable_multistep_section :input[type="checkbox"]').click(function() {
                self.changeMultistepVisibility($(this));
            });

            this.showRegFormNotificationFields();
            this.integrationsCondFieldsVisibility();

        },

        settingsGuest: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-guest').show();
                table.find('tr.show-if-not-guest').hide();

                $('input[type=checkbox][name="wpuf_settings[guest_details]"]').trigger('change');

            } else {
                table.find('tr.show-if-guest').hide();
                table.find('tr.show-if-not-guest').show();
            }
        },

        settingsRoles: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-roles').show();
            } else {
                table.find('tr.show-if-roles').hide();
            }
        },

        settingsGuestDetails: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-details').show();
            } else {
                table.find('tr.show-if-details').hide();
            }
        },

        settingsPayment: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-payment').show();
                table.find('tr.show-if-force-pack').hide();
            } else {
                table.find('tr.show-if-payment').hide();
                table.find('input[type=checkbox]').removeAttr('checked');
            }
        },

        settingsPayPerPost: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-pay-per-post').show();

            } else {
                table.find('tr.show-if-pay-per-post').hide();

            }
        },

        settingsForcePack: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-force-pack').show();

            } else {
                table.find('tr.show-if-force-pack').hide();

            }
        },

        settingsRestriction: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-schedule').show();
            } else {
                table.find('tr.show-if-schedule').hide();

            }
        },

        settingsLimit: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-limit-entries').show();
            } else {
                table.find('tr.show-if-limit-entries').hide();

            }
        },

        settingsRedirect: function(e) {
            e.preventDefault();

            var $self = $(this),
                $table = $self.closest('table'),
                value = $self.val();

            switch( value ) {
                case 'post':
                    $table.find('tr.wpuf-page-id, tr.wpuf-url, tr.wpuf-same-page').hide();
                    break;

                case 'page':
                    $table.find('tr.wpuf-page-id').show();
                    $table.find('tr.wpuf-same-page').hide();
                    $table.find('tr.wpuf-url').hide();
                    break;

                case 'url':
                    $table.find('tr.wpuf-page-id').hide();
                    $table.find('tr.wpuf-same-page').hide();
                    $table.find('tr.wpuf-url').show();
                    break;

                case 'same':
                    $table.find('tr.wpuf-page-id').hide();
                    $table.find('tr.wpuf-url').hide();
                    $table.find('tr.wpuf-same-page').show();
                    break;
            }
        },

        changeMultistepVisibility: function(target) {
            if (target.is(':checked')) {
                $('.wpuf_multistep_content').show();
            } else {
                $('.wpuf_multistep_content').hide();
            }
        },

        showRegFormNotificationFields: function() {
            var newUserStatus                 = $( "input#wpuf_new_user_status" ),
                emailVerification             = $( "input#notification_type_verification" ),
                welcomeEmail                  = $( "#notification_type_welcome_email" );

            if ( newUserStatus.is(':checked') ) {
                $('#wpuf_pending_user_admin_notification').show();
                $('#wpuf_approved_user_admin_notification').hide();
            } else{
                $('#wpuf_pending_user_admin_notification').hide();
                $('#wpuf_approved_user_admin_notification').show();
            }

            $( newUserStatus ).on( "click", function() {
                $('#wpuf_pending_user_admin_notification').hide();
                $('#wpuf_approved_user_admin_notification').show();

                if ( newUserStatus.is(':checked') ) {
                    $('#wpuf_pending_user_admin_notification').show();
                    $('#wpuf_approved_user_admin_notification').hide();
                }
            });

            if ( emailVerification.is(':checked') ) {
                $('.wpuf-email-verification-settings-fields').show();
                $('.wpuf-welcome-email-settings-fields').hide();
            }

            if ( welcomeEmail.is(':checked') ) {
                $('.wpuf-welcome-email-settings-fields').show();
                $('.wpuf-email-verification-settings-fields').hide();
            }

            $( emailVerification ).on( "click", function() {
                $('.wpuf-email-verification-settings-fields').show();
                $('.wpuf-welcome-email-settings-fields').hide();
            });

            $( welcomeEmail ).on( "click", function() {
                $('.wpuf-welcome-email-settings-fields').show();
                $('.wpuf-email-verification-settings-fields').hide();
            });
        },

        integrationsCondFieldsVisibility: function() {
            var conditional_logic      = $( '.wpuf-integrations-conditional-logic' ),
                cond_fields_container  = $( '.wpuf-integrations-conditional-logic-container' ),
                cond_fields            = $( '.wpuf_available_conditional_fields' ),
                cond_field_options     = $( '.wpuf_selected_conditional_field_options' );

            $( conditional_logic ).on( "click", function(e) {
                $( cond_fields_container ).hide();

                if ( e.target.value === 'yes' ) {
                    $( cond_fields_container ).show();
                }
            });

            $( cond_fields ).on('focus', function(e) {
                var form_fields = wpuf_form_builder.form_fields,
                    options     = '';
                    options     += '<option value="-1">- select -</option>';

                form_fields.forEach(function(field) {
                  if ( field.template === 'radio_field' || field.template === 'checkbox_field' || field.template === 'dropdown_field' ) {
                    options += '<option value="'+field.name+'">'+field.label+'</option>';
                  }
                });
                e.target.innerHTML = options;
            });

            $( cond_fields ).on('change', function(e){
                var form_fields = wpuf_form_builder.form_fields,
                    field_name = e.target.value,
                    field_options  = '';
                    field_options += '<option value="-1">- select -</option>';

                form_fields.forEach(function(field) {
                    if ( field.name === field_name ) {
                        var options = field.options;

                        for (var key in options) {
                            if (options.hasOwnProperty(key)) {
                                field_options += '<option value="'+key+'">'+options[key]+'</option>';
                            }
                        }
                    }
                });

                cond_field_options[0].innerHTML = field_options;
            });
        }
    };

    // on DOM ready
    $(function() {
        resizeBuilderContainer();

        $("#collapse-menu").click(function () {
            resizeBuilderContainer();
        });

        function resizeBuilderContainer() {
            if ($(document.body).hasClass('folded')) {
                $("#wpuf-form-builder").css("width", "calc(100% - 80px)");
            } else {
                $("#wpuf-form-builder").css("width", "calc(100% - 200px)");
            }
        }

        SettingsTab.init();
    });

    // Mobile view menu toggle
    $('#wpuf-form-builder').on('click', '#wpuf-toggle-field-options, #wpuf-toggle-show-form, .control-buttons .fa-pencil, .ui-draggable-handle', function() {
        $('#wpuf-toggle-field-options').toggleClass('hide');
        $('#wpuf-toggle-show-form').toggleClass('show');
        $('#builder-form-fields').toggleClass('show');
    });

    $('#wpuf_settings_posttype').on('change', function() {
        event.preventDefault();
        var post_type =  $(this).val();
        wp.ajax.send('wpuf_form_setting_post', {
            data: {
                post_type: post_type,
                wpuf_form_builder_setting_nonce: wpuf_form_builder.nonce
            },
            success: function (response) {
                $('.wpuf_settings_taxonomy').remove();
                $('.wpuf-post-fromat').after(response.data);
            },
            error: function ( error ) {
                console.log(error);
            }
        });
    });

})(jQuery);
