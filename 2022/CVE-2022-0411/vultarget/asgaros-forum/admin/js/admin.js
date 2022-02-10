// Prevent replacing Font Awesome icons with SVG icons.
window.FontAwesomeConfig = {
    autoReplaceSvg: false
};

(function($) {
    $(document).ready(function() {
        var editor = null;

        if (wp && wp.codeEditor) {
            if ($('textarea[data-code-editor-mode]').length) {
                wp.codeEditor.defaultSettings.codemirror.mode = $('textarea[data-code-editor-mode]').attr('data-code-editor-mode');
                var editor = wp.codeEditor.initialize($('textarea[data-code-editor-mode]'));
            }
        }

        // Settings-tabs toggle.
        $('#af-options #settings-tabs li').click(function() {
            // Get slug.
            var tab = $(this).attr('data-slug');

            // Set slug.
            $('#af-options input[name=selected_tab]').val(tab);

            // Change highlighted tab.
            $('#af-options #settings-wrapper #settings-tabs li').removeClass('active-tab');
            $('#af-options #settings-wrapper #settings-tabs li[data-slug='+tab+']').addClass('active-tab');

            // Hide all tabs.
            $('#af-options #settings-wrapper .tab').css('display', 'none');

            // Show selected tab.
            $('#af-options #settings-wrapper #tab-'+tab).css('display', 'block');
        });

        // Adding color picker
        $('.color-picker').wpColorPicker();

        // Show/hide color pickers
        $('select[name="theme"]').change(function() {
            if ($('select[name="theme"]').val() === 'default') {
                $('#af-options .custom-color-selector').show();
            } else {
                $('#af-options .custom-color-selector').hide();
            }
        });

        // Show/hide options.
        $('.show_hide_initiator').change(function() {
            var hideClass = $(this).attr('data-hide-class');

            if (this.checked) {
                $('#af-options .'+hideClass).show();
            } else {
                $('#af-options .'+hideClass).hide();
            }
        });

        // Create/edit category dialog.
        $('.category-editor-link').click(function() {
            resetEditor();

            var category_id         = $(this).attr('data-value-id');
            var category_name       = '';
            var category_access     = 'everyone';
            var category_order      = '1';
            var category_usergroups = '';

            if (category_id !== 'new') {
                category_name       = $('#category_'+category_id+'_name').val();
                category_access     = $('#category_'+category_id+'_access').val();
                category_order      = $('#category_'+category_id+'_order').val();
                category_usergroups = $('#category_'+category_id+'_usergroups').val().split(',');
            }

            $('#category-editor input[name=category_id]').val(category_id);
            $('#category-editor input[name=category_name]').val(category_name);

            $('#category-editor select[name=category_access] option').each(function() {
                if ($(this).val() == category_access) {
                    $(this).prop('selected', true);
                }
            });

            $('#category-editor input[name=category_order]').val(category_order);

            $('#usergroups-editor input[type=checkbox]').each(function() {
                if (jQuery.inArray($(this).val(), category_usergroups) != -1) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });

            setEditorTitle(this);
            showEditorInstance('#category-editor');
            $('#category-editor input[name=category_name]').focus();
        });

        // Create/edit forum dialog.
        $('.forum-editor-link').click(function() {
            resetEditor();

            var forum_id                = $(this).attr('data-value-id');
            var forum_category          = $(this).attr('data-value-category');
            var forum_parent_forum      = $(this).attr('data-value-parent-forum');
            var forum_name              = '';
            var forum_description       = '';
            var forum_icon              = 'fas fa-comments';
            var forum_status            = 'normal';
            var forum_order             = '1';
            var forum_count_subforums   = '0';

            if (forum_id !== 'new') {
                forum_name              = $('#forum_'+forum_id+'_name').val();
                forum_description       = $('#forum_'+forum_id+'_description').val();
                forum_icon              = $('#forum_'+forum_id+'_icon').val();
                forum_status            = $('#forum_'+forum_id+'_status').val();
                forum_order             = $('#forum_'+forum_id+'_order').val();
                forum_count_subforums   = $('#forum_'+forum_id+'_count_subforums').val();
            }

            // Create parent-dropdown.
            $('#forum_parent').empty();

            if (forum_count_subforums == 0) {
                var cloned_select = $('#hidden-data #data-forum-parent-two-level').html();
                $('#forum-editor select[name=forum_parent]').html(cloned_select);

                // Remove itself from list because its not possible to assign a forum to itself.
                $('#forum-editor option[value='+forum_category+'_'+forum_id+']').remove();
            } else {
                var cloned_select = $('#hidden-data #data-forum-parent-one-level').html();
                $('#forum-editor select[name=forum_parent]').html(cloned_select);
            }

            // Select parent element.
            $('#forum-editor select[name=forum_parent] option').each(function() {
                if ($(this).val() == forum_category+'_'+forum_parent_forum) {
                    $(this).prop('selected', true);
                }
            });

            // Apply values.
            $('#forum-editor input[name=forum_id]').val(forum_id);
            $('#forum-editor input[name=forum_category]').val(forum_category);
            $('#forum-editor input[name=forum_parent_forum]').val(forum_parent_forum);
            $('#forum-editor input[name=forum_name]').val(forum_name);
            $('#forum-editor input[name=forum_description]').val(forum_description);
            $('#forum-editor input[name=forum_icon]').val(forum_icon);

            $('#forum-editor select[name=forum_status] option').each(function() {
                if ($(this).val() == forum_status) {
                    $(this).prop('selected', true);
                }
            });

            $('#forum-editor input[name=forum_order]').val(forum_order);

            setEditorTitle(this);
            showEditorInstance('#forum-editor');
            $('#forum-editor input[name=forum_name]').focus();
        });

        // Create/edit usergroup category dialog.
        $('.usergroup-category-editor-link').click(function() {
            resetEditor();

            var usergroup_category_id   = $(this).attr('data-value-id');
            var usergroup_category_name = '';

            if (usergroup_category_id !== 'new') {
                usergroup_category_name = $('#usergroup_category_'+usergroup_category_id+'_name').val();
            }

            $('#usergroup-category-editor input[name=usergroup_category_id]').val(usergroup_category_id);
            $('#usergroup-category-editor input[name=usergroup_category_name]').val(usergroup_category_name);

            setEditorTitle(this);
            showEditorInstance('#usergroup-category-editor');
            $('#usergroup-category-editor input[name=usergroup_category_name]').focus();
        });

        // Create/edit usergroup dialog.
        $('.usergroup-editor-link').click(function() {
            resetEditor();

            var usergroup_id            = $(this).attr('data-value-id');
            var usergroup_category      = $(this).attr('data-value-category');
            var usergroup_name          = '';
            var usergroup_color         = '#444444';
            var usergroup_visibility    = '';
            var usergroup_auto_add      = '';
            var usergroup_icon          = '';

            if (usergroup_id !== 'new') {
                usergroup_name          = $('#usergroup_'+usergroup_id+'_name').val();
                usergroup_color         = $('#usergroup_'+usergroup_id+'_color').val();
                usergroup_visibility    = $('#usergroup_'+usergroup_id+'_visibility').val();
                usergroup_auto_add      = $('#usergroup_'+usergroup_id+'_auto_add').val();
                usergroup_icon          = $('#usergroup_'+usergroup_id+'_icon').val();
            }

            $('#usergroup-editor input[name=usergroup_id]').val(usergroup_id);
            $('#usergroup-editor input[name=usergroup_category]').val(usergroup_category);
            $('#usergroup-editor input[name=usergroup_name]').val(usergroup_name);
            $('#usergroup-editor input[name=usergroup_color]').val(usergroup_color);
            $('#usergroup-editor input[name=usergroup_color]').wpColorPicker('color', usergroup_color);

            if (usergroup_visibility === 'hidden') {
                $('#usergroup-editor input[name=usergroup_visibility]').prop('checked', true);
            } else {
                $('#usergroup-editor input[name=usergroup_visibility]').prop('checked', false);
            }

            if (usergroup_auto_add === 'yes') {
                $('#usergroup-editor input[name=usergroup_auto_add]').prop('checked', true);
            } else {
                $('#usergroup-editor input[name=usergroup_auto_add]').prop('checked', false);
            }

            $('#usergroup-editor input[name=usergroup_icon]').val(usergroup_icon);

            setEditorTitle(this);
            showEditorInstance('#usergroup-editor');
            $('#usergroup-editor input[name=usergroup_name]').focus();
        });

        // Create/edit ad dialog.
        $('.ad-editor-link').click(function() {
            resetEditor();

            var ad_id           = $(this).attr('data-value-id');
            var ad_name         = '';
            var ad_code         = '';
            var ad_active       = '1';
            var ad_locations    = '';

            if (ad_id !== 'new') {
                ad_name         = $('#ad_'+ad_id+'_name').val();
                ad_code         = $('#ad_'+ad_id+'_code').val();
                ad_active       = $('#ad_'+ad_id+'_active').val();
                ad_locations    = $('#ad_'+ad_id+'_locations').val().split(',');
            }

            $('#ad-editor input[name=ad_id]').val(ad_id);
            $('#ad-editor input[name=ad_name]').val(ad_name);
            $('#ad-editor textarea[name=ad_code]').text(ad_code);

            if (ad_active == '1') {
                $('#ad-editor input[name=ad_active]').prop('checked', true);
            } else {
                $('#ad-editor input[name=ad_active]').prop('checked', false);
            }

            $('#ad-editor #locations-editor input[type=checkbox]').each(function() {
                if (jQuery.inArray($(this).val(), ad_locations) != -1) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });

            setEditorTitle(this);
            showEditorInstance('#ad-editor');

            // Try to update CodeMirror content. The content must get updated
            // after the editor-instance is visible again. Otherwise the refresh
            // is not working.
            if (editor) {
                editor.codemirror.doc.setValue(ad_code);
                editor.codemirror.refresh();
            }

            $('#ad-editor input[name=ad_name]').focus();
        });

        // Delete category dialog.
        $('.category-delete-link').click(function() {
            resetEditor();

            var elementID = $(this).attr('data-value-id');
            $('#category-delete input[name=category-id]').val(elementID);

            setEditorTitle(this);
            showEditorInstance('#category-delete');
        });

        // Delete forum dialog.
        $('.forum-delete-link').click(function() {
            resetEditor();

            var forum_id        = $(this).attr('data-value-id');
            var forum_category  = $(this).attr('data-value-category');

            $('#forum-delete input[name=forum-id]').val(forum_id);
            $('#forum-delete input[name=forum-category]').val(forum_category);

            setEditorTitle(this);
            showEditorInstance('#forum-delete');
        });

        // Delete usergroup dialog.
        $('.usergroup-delete-link').click(function() {
            resetEditor();

            var elementID = $(this).attr('data-value-id');
            $('#usergroup-delete input[name=usergroup-id]').val(elementID);

            setEditorTitle(this);
            showEditorInstance('#usergroup-delete');
        });

        // Delete usergroup category dialog.
        $('.usergroup-category-delete-link').click(function() {
            resetEditor();

            var elementID = $(this).attr('data-value-id');
            $('#usergroup-category-delete input[name=usergroup-category-id]').val(elementID);

            setEditorTitle(this);
            showEditorInstance('#usergroup-category-delete');
        });

        // Delete ad dialog.
        $('.ad-delete-link').click(function() {
            resetEditor();

            var elementID = $(this).attr('data-value-id');
            $('#ad-delete input[name=ad_id]').val(elementID);

            setEditorTitle(this);
            showEditorInstance('#ad-delete');
        });

        $('#editor-container .button-cancel').click(function() {
            resetEditor();
        })

        function setEditorTitle(objectElement) {
            var editor_title = $(objectElement).attr('data-value-editor-title');

            $('#editor-container .settings-header').html(editor_title);
        }

        function resetEditor() {
            $('#editor-container').hide();
            $('.editor-instance').hide();
        }

        function showEditorInstance(instanceName) {
            $(instanceName).show();
            $('#editor-container').show();
        }
    });
})(jQuery);
