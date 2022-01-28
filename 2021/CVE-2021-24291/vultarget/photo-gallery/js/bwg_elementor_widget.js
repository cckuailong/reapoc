jQuery(function() {
    elementor.hooks.addAction( 'panel/open_editor/widget/bwg-elementor', function( panel, model, view ) {
        var bwg_view_type = jQuery('select[data-setting="bwg_gallery_view_type"]',window.parent.document);
        var bwg_group_view_type = jQuery('select[data-setting="bwg_gallery_group_view_type"]',window.parent.document);
        var bwg_galleries = jQuery('select[data-setting="bwg_galleries"]',window.parent.document);
        var bwg_tags = jQuery('select[data-setting="bwg_tags"]',window.parent.document);
        var bwg_theme = jQuery('select[data-setting="bwg_theme"]',window.parent.document);
        var bwg_gallery_group = jQuery('select[data-setting="bwg_gallery_group"]',window.parent.document);

        bwg_add_edit_link(bwg_view_type, 'view_type');
        bwg_add_edit_link(bwg_group_view_type, 'group_view_type');
        bwg_add_edit_link(bwg_galleries, 'galleries');
        bwg_add_edit_link(bwg_tags, 'tag');
        bwg_add_edit_link(bwg_theme, 'theme');
        bwg_add_edit_link(bwg_gallery_group, 'group');
    });
    jQuery('body').on('change', 'select[data-setting="bwg_gallery_view_type"]',window.parent.document, function (){
        bwg_add_edit_link(jQuery(this), 'view_type');
    });
    jQuery('body').on('change', 'select[data-setting="bwg_gallery_group_view_type"]',window.parent.document, function (){
        bwg_add_edit_link(jQuery(this), 'group_view_type');
    });
    jQuery('body').on('change', 'select[data-setting="bwg_galleries"]',window.parent.document, function (){
        bwg_add_edit_link(jQuery(this), 'galleries');
    });
    jQuery('body').on('change', 'select[data-setting="bwg_tags"]',window.parent.document, function (){
        bwg_add_edit_link(jQuery(this), 'tag');
    });
    jQuery('body').on('change', 'select[data-setting="bwg_theme"]',window.parent.document, function (){
        bwg_add_edit_link(jQuery(this), 'theme');
    });
    jQuery('body').on('change', 'select[data-setting="bwg_gallery_group"]',window.parent.document, function (){
        bwg_add_edit_link(jQuery(this), 'group');
    });
});

function bwg_add_edit_link(el , type) {
    var bwg_el = el;
    var bwg_id = bwg_el.val();
    var a_link = bwg_el.closest('.elementor-control-content').find('.elementor-control-field-description').find('a');
    var link = "";
    var edit_link = "";
    if(type === "view_type"){
        link = "admin.php?page=options_bwg&active_tab=1&gallery_type=thumbnails";
        edit_link = "admin.php?page=options_bwg&active_tab=1&gallery_type={1}";
    }
    else if (type === "group_view_type"){
        link = "admin.php?page=options_bwg&active_tab=2&album_type=album_extended_preview"
        edit_link = "admin.php?page=options_bwg&active_tab=2&album_type={1}"
    }
    else if(type === "galleries"){
        link = "admin.php?page=galleries_bwg";
        edit_link = "admin.php?page=galleries_bwg&task=edit&current_id={1}";
    }else if(type === "tag"){
        link = "edit-tags.php?taxonomy=bwg_tag";
        edit_link = "term.php?taxonomy=bwg_tag&tag_ID={1}";
    }else if(type === "theme"){
        link = "admin.php?page=themes_bwg";
        edit_link = "admin.php?page=themes_bwg&task=edit&current_id={1}";
    }else if(type === "group"){
        link = "admin.php?page=albums_bwg";
        edit_link = "admin.php?page=albums_bwg&task=edit&current_id={1}";
    }
    if(bwg_id !== '0'){
        link = edit_link.replace("{1}", bwg_id);
    }
    a_link.attr( 'href', link);
}