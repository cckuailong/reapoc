var $ = jQuery.noConflict();
$(document).ready(function ($) {
    var currentContainer;
    var currentRowId;
    var currentColumnType;

    $('.icp-auto').iconpicker();
    
    $(document).delegate('#wpdiscuz_form_add_row', 'click', function () {
        wpdiscuzForm.addRow('#wpd-form-sortable-rows');
        wpdFormFieldSortable();
    });

    $(document).delegate('.wpd-form-row-wrap .wpd-form-row-actions .fa-columns', 'click', function () {
        wpdiscuzForm.formTwoColumn($(this));
        wpdFormFieldSortable();
    });

    $(document).delegate('.wpd-form-row-wrap .wpd-form-row-actions .fa-trash-alt', 'click', function () {
         if($(this).parents('.wpd-form-row-wrap').find('.wpd-default-field').length){
            alert(wpdFormAdminOptions.can_not_delete_field);
            return;
        }
        if (confirm(wpdFormAdminOptions.confirm_delete_message)) {
            $(this).parents('.wpd-form-row-wrap').remove();
        }
    });

    $(document).delegate('.wpd-form-add-filed', 'click', function () {
        currentRowId = $(this).parents('.wpd-form-row-wrap').attr('id');
        currentColumnType = 'full';
        if ($(this).parents('.wpd-form-col').hasClass('left-col')) {
            currentColumnType = 'left';
        } else if ($(this).parents('.wpd-form-col').hasClass('right-col')) {
            currentColumnType = 'right';
        }
        currentContainer = $(this).parents('.wpd-form-col').find('.col-body');
        tb_show(wpdFormAdminOptions.wpd_form_fields, ajaxurl + "?action=wpdiscuzCustomFields&width=700&height=400");
        return false;
    });

    $(document).delegate('.wpd-field .fa-trash-alt', 'click', function () {
        if (confirm(wpdFormAdminOptions.confirm_delete_message)) {
            $(this).parents('.wpd-field').remove();
        }
    });

    $(document).delegate('.wpd-field .fa-pencil-alt', 'click', function () {
        $(this).parents('.wpd-field').find('.wpd-field-body').toggle(500);
    });

    $(document).delegate('.wpd-field-button', 'click', function () {
        var fieldType = $(this).attr('id');
        var fieldTitle = $(this).text();
        var defaultField = '0';
        if ($(this).hasClass('wpdDefaultField')) {
            defaultField = 1;
        }
        wpdFieldLoad();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'adminFieldForm',
                fieldType: fieldType,
                row: currentRowId,
                col: currentColumnType,
                defaultField: defaultField
            }
        }).done(function (response) {
            $('#TB_ajaxWindowTitle').text(fieldTitle);
            $('#TB_ajaxContent').html(response);
            $('.icp-auto').iconpicker();
        });
    });

    $(document).delegate('#wpd-add-field-button', 'click', function () {
        var tbForm = $('#TB_ajaxContent_form');
        tbForm.submit(function (event) {
            event.preventDefault();
        });
        if (tbForm[0].checkValidity()) {
            var fieldName = $(this).parents('#TB_ajaxContent').find('.wpd-field-body .wpd-field-name').val();
            var dialogFieldHtml = $(this).parents('#TB_ajaxContent').find('.wpd-field-body');
            var dynamicFiledCont = $('<div class="wpd-col-full wpd-field">' +
                    '<div class="wpd-field-head">' + fieldName +
                    '<div class="wpd-field-actions">' +
                    '<i class="fas fa-pencil-alt" title="' + wpdFormAdminOptions.edit_field + '"></i>|' +
                    '<i class="fas fa-trash-alt" title="' + wpdFormAdminOptions.delete + '"></i>' +
                    '</div></div></div>');
            dialogFieldHtml.hide();
            dialogFieldHtml.appendTo(dynamicFiledCont);
            dynamicFiledCont.appendTo(currentContainer);
            wpdFormFieldSortable();
            tb_remove();
        }
    });


    wpdFormRowSortable();
    wpdFormFieldSortable();

    /*========================= FUNCTIONS ==================================== */

    function wpdFormRowSortable() {
        $('#wpd-form-sortable-rows').sortable({
            containment: '.wpd-form',
            cancel: '.wpd-form-row-body,.fa-columns, .fa-trash-alt',
            update: function (event, ui) {
                ui.item.find('.row_order').val(ui.item.index());
                wpdiscuzForm.rebuildRowsData('#wpd-form-sortable-rows', '.wpd-form-row-wrap', 'wpd-form-row-wrap');
            }
        });
    }

    function wpdFormFieldSortable() {
        $('.col-body').sortable({
            connectWith: '.col-body',
            containment: '.wpd-form',
            cancel: '.fas, .wpd-field-body',
            update: function (event, ui) {
                wpdiscuzForm.afterFieldDrag(ui.item);
            }
        });
    }



    function wpdFieldLoad() {
        $('#TB_ajaxContent').html('<img class="wpdFieldLoad" src="' + wpdFormAdminOptions.loaderImg + '">');
    }
    $(document).delegate('.wpd-advaced-options-title','click', function(){
         $(this).next('.wpd-advaced-options-cont').toggle();
    });
});
//=====================  OBJECT ========================== //

var wpdiscuzForm = {
    /**
     * @param {string} selector Container where should be added row
     */
    addRow: function (selector) {
        $(selector).append('<div  class="wpd-form-row-wrap">' +
                '<input class="column_type" type="hidden" value="full"/>' +
                '<input class="row_order" type="hidden" name=""/>' +
                '<div class="wpd-form-row-head">' +
                '<div class="wpd-form-row-actions">' +
                '<i class="fas fa-columns" title="' + wpdFormAdminOptions.two_column + '"></i>|' +
                '<i class="fas fa-trash-alt" title="' + wpdFormAdminOptions.delete + '"></i>|' +
                '<i class="fas fa-arrows-alt" title="' + wpdFormAdminOptions.move + '"></i>' +
                '</div></div>' +
                '<div  class="wpd-form-row">' +
                '<div class="wpd-form-row-body">' +
                '<div class="full-col wpd-form-col">' +
                '<div class="col-body"></div>' +
                '<div class="wpd-form-add-filed">' +
                '<i class="fas fa-plus" title="' + wpdFormAdminOptions.add_field + '">' +
                '</div></div>' +
                '</div></div></div>');
        wpdiscuzForm.rebuildRowsData(selector, '.wpd-form-row-wrap', 'wpd-form-row-wrap');

    },
    rebuildRowsData: function (container, selector, idPrefix) {
        $(container + ' ' + selector).each(function (index, element) {
            var dynamicId = idPrefix + '_' + index;
            $(this).attr('id', dynamicId);
            $(this).find('.column_type').attr('name', wpdFormAdminOptions.wpdiscuz_form_structure + '[' + dynamicId + '][column_type]');
            $(this).find('.row_order').attr('name', wpdFormAdminOptions.wpdiscuz_form_structure + '[' + dynamicId + '][row_order]');
            $(this).find('.row_order').attr('value', index);
            $('input , textarea', $(this)).each(function () {
                var oldRowName = $(this).attr('name');
                if (oldRowName) {
                    var newRowName = oldRowName.replace(new RegExp(wpdFormAdminOptions.wpdiscuz_form_structure + '\\[[^\\]]+\\]' ,'g'), wpdFormAdminOptions.wpdiscuz_form_structure  + '[' + dynamicId + ']');
                    $(this).attr('name', newRowName);
                }
            });
        });
    },
    formTwoColumn: function (obj) {
        var rowWrap = obj.parents('.wpd-form-row-wrap');
        var row = rowWrap.find('.wpd-form-row>.wpd-form-row-body');
        var colNewName = 'full';
        var colNewType = 'full';
        if (row.hasClass('two-col')) {
            var rigthColumnHtml = $('.right-col>.col-body', row).html();
            var leftColumn = $('.left-col', row);
            $('.right-col', row).remove();
            $('.col-body', leftColumn).append(rigthColumnHtml);
            leftColumn.removeClass('left-col');
            leftColumn.addClass('full-col');
        } else {
            var fullColumn = $('.full-col', row);
            fullColumn.removeClass('full-col');
            fullColumn.addClass('left-col');
            fullColumn.after('<div class="right-col wpd-form-col">' +
                    '<div class="col-body"></div>' +
                    '<div class="wpd-form-add-filed">' +
                    '<i class="fas fa-plus" title="' + wpdFormAdminOptions.add_field + '">' +
                    '</div></div>');
            colNewName = 'left';
            colNewType = 'two';
        }
        obj.toggleClass("wpd-form-columns-two");
        row.toggleClass("two-col");
        rowWrap.find('.column_type').val(colNewType);
        wpdiscuzForm.replaceColName(colNewName, rowWrap.attr('id'), row);
    },
    replaceColName: function (colNewName, parentRowId, obj) {
        $('input , textarea', obj).each(function () {
            var oldRowName = $(this).attr('name');
            if (oldRowName) {
                var newRowName = oldRowName.replace(new RegExp(wpdFormAdminOptions.wpdiscuz_form_structure + '\\[[^\\]]+\\]\\[[^\\]]+\\]' ,'g'), wpdFormAdminOptions.wpdiscuz_form_structure + '[' + parentRowId + '][' + colNewName + ']');
                $(this).attr('name', newRowName);
            }
        });
    },
    afterFieldDrag: function (obj) {
        var parentRowId = obj.parents('.wpd-form-row-wrap').attr('id');
        var parentColName = 'full';
        if (obj.parents('.wpd-form-col').hasClass('left-col')) {
            parentColName = 'left';
        } else if (obj.parents('.wpd-form-col').hasClass('right-col')) {
            parentColName = 'right';
        }
        wpdiscuzForm.replaceColName(parentColName, parentRowId, obj);
    }
};