(function ($) {
    /* global tinymce */
    /* global wpdObject */
    tinymce.PluginManager.add('wpDiscuz', function (ed, url) {
        if (ed.id === "content") {
            ed.addButton('wpDiscuz', {
                image: wpdObject.image,
                tooltip: wpdObject.tooltip,
                onclick: function () {
                    var w = $(window).width();
                    var dialogWidth = 600;
                    var W = (dialogWidth < w) ? dialogWidth : w;
                    $('#wpd-inline-question').val('');
                    var text = tinymce.activeEditor.selection.getContent();
                    $('#wpd-inline-content').html(text ? text : '<span class="wpd-text-error">' + wpdObject.no_text_selected + '</span>');
                    tb_show(wpdObject.popup_title, '#TB_inline?width=' + W + '&height=400&inlineId=wpdiscuz_feedback_dialog');
                }
            });
        }
    });

    $(document).delegate('#wpd-put-shortcode', 'mousedown', function () {
        var question = $('#wpd-inline-question').val();
        var shortcode = '[' + wpdObject.shortcode + ' id="' + Math.random().toString(36).substr(2, 10) + '" question="' + (question ? $('<div>' + question + '</div>').text() : wpdObject.leave_feebdack) + '" opened="' + $('[name=wpd-inline-type]:checked').val() + '"]';
        shortcode += tinymce.activeEditor.selection.getContent();
        shortcode += '[/' + wpdObject.shortcode + ']';
        tinymce.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tb_remove();
    });
})(jQuery);