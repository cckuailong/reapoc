jQuery("document").ready(function () {
    elementor.hooks.addAction( 'panel/open_editor/widget/ecwd-elementor', function( panel, model, view ) {
        var ecwd_el = jQuery('select[data-setting="control_calendar"]',window.parent.document);
        ecwd_add_edit_link(ecwd_el);
    });
    jQuery('body').on('change', 'select[data-setting="control_calendar"]',window.parent.document, function (){
        ecwd_add_edit_link(jQuery(this));
    });
});

function ecwd_add_edit_link(el) {
        var ecwd_el = el;
        var ecwd_id = ecwd_el.val();
        var a_link = ecwd_el.closest('.elementor-control-content').find('.elementor-control-field-description').find('a');
        var new_link = 'edit.php?post_type=ecwd_calendar';
        if(ecwd_id !== '0'){
            new_link = 'post.php?post='+ecwd_el.val()+'&action=edit';
        }
        a_link.attr( 'href', new_link);
}