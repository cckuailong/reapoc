jQuery("document").ready(function () {
    elementor.hooks.addAction( 'panel/open_editor/widget/wdi-elementor', function( panel, model, view ) {
        var wdi_el = jQuery('select[data-setting="wdi_feeds"]',window.parent.document);
        wdi_add_edit_link(wdi_el);
    });
    jQuery('body').on('change', 'select[data-setting="wdi_feeds"]',window.parent.document, function (){
        wdi_add_edit_link(jQuery(this));
    });
});

function wdi_add_edit_link(el) {
        var wdi_el = el;
        var wdi_id = wdi_el.val();
        var a_link = wdi_el.closest('.elementor-control-content').find('.elementor-control-field-description').find('a');
        var new_link = 'admin.php?page=wdi_feeds';
        if(wdi_id !== '0'){
            new_link = 'admin.php?page=wdi_feeds&task=edit&current_id='+wdi_el.val();
        }
        a_link.attr( 'href', new_link);
}


