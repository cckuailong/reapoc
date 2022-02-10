jQuery.noConflict();
(function ($) {
    var WRAPPER = $('#oxi-addons-preview-data').attr('template-wrapper');
    $(".oxi-addons-tabs-ul li:first").addClass("active");
    $(".oxi-addons-tabs-content-tabs:first").addClass("active");
    $(".oxi-addons-tabs-ul li").click(function () {
        if ($(this).hasClass('active')) {
            $(".oxi-addons-tabs-ul li").removeClass("active");
            var activeTab = $(this).attr("ref");
            $(activeTab).removeClass("active");
        } else {
            $(".oxi-addons-tabs-ul li").removeClass("active");
            $(this).toggleClass("active");
            $(".oxi-addons-tabs-content-tabs").removeClass("active");
            var activeTab = $(this).attr("ref");
            $(activeTab).addClass("active");
        }
    });
    $("#oxi-addons-setting-reload").click(function () {
        location.reload();
    });
    $(".oxi-head").click(function () {
        var self = $(this).parent();
        self.toggleClass("oxi-admin-head-d-none");
    });
    $(".shortcode-addons-templates-right-panel-heading").click(function () {
        var self = $(this).parent();
        self.toggleClass("oxi-admin-head-d-none");
    });
    $("#oxi-addons-form-submit").submit(function (e) {
        e.preventDefault();
        return false;
    });
    $("#shortcode-addons-style-change-submit").submit(function (e) {
        e.preventDefault();
        return false;
    });
    $("#shortcode-addons-name-change-submit").submit(function (e) {
        e.preventDefault();
        return false;
    });
    $("#shortcode-addons-name-change-submit").submit(function (e) {
        e.preventDefault();
        return false;
    });
    $("#shortcode-addons-template-modal-form").submit(function (e) {
        e.preventDefault();
        return false;
    });
    $('.shortcode-control-type-select .shortcode-addons-select-input').each(function (e) {
        if (!$(this).parents('.shortcode-addons-form-repeater-store').length) {
            $(this).select2({width: '100%'});
        }
    });
    $('.shortcode-form-control').each(function (e) {
        if ($(this).hasClass('shortcode-addons-form-responsive-tab')) {
            $(this).addClass('shortcode-addons-responsive-display-none');
        } else if ($(this).hasClass('shortcode-addons-form-responsive-mobile')) {
            $(this).addClass('shortcode-addons-responsive-display-none');
        }
    });
    $(document.body).on("click", ".shortcode-form-responsive-switcher-desktop", function () {
        $("#oxi-addons-form-submit").toggleClass('shortcode-responsive-switchers-open');
        $("#oxi-template-modal-form").toggleClass('shortcode-responsive-switchers-open');
        $(".shortcode-form-responsive-switcher-tablet").removeClass('active');
        $(".shortcode-form-responsive-switcher-mobile").removeClass('active');
        $(".shortcode-addons-form-responsive-laptop").removeClass('shortcode-addons-responsive-display-none');
        $(".shortcode-addons-form-responsive-tab").addClass('shortcode-addons-responsive-display-none');
        $(".shortcode-addons-form-responsive-mobile").addClass('shortcode-addons-responsive-display-none');
    });
    $(document.body).on("click", ".shortcode-form-responsive-switcher-tablet", function () {
        $(".shortcode-form-responsive-switcher-tablet").addClass('active');
        $(".shortcode-form-responsive-switcher-mobile").removeClass('active');
        $(".shortcode-addons-form-responsive-laptop").addClass('shortcode-addons-responsive-display-none');
        $(".shortcode-addons-form-responsive-tab").removeClass('shortcode-addons-responsive-display-none');
        $(".shortcode-addons-form-responsive-mobile").addClass('shortcode-addons-responsive-display-none');
    });
    $(document.body).on("click", ".shortcode-form-responsive-switcher-mobile", function () {
        $(".shortcode-form-responsive-switcher-tablet").removeClass('active');
        $(".shortcode-form-responsive-switcher-mobile").addClass('active');
        $(".shortcode-addons-form-responsive-laptop").addClass('shortcode-addons-responsive-display-none');
        $(".shortcode-addons-form-responsive-tab").addClass('shortcode-addons-responsive-display-none');
        $(".shortcode-addons-form-responsive-mobile").removeClass('shortcode-addons-responsive-display-none');
    });
    $.fn.uncheckableRadio = function () {
        var $root = this;
        $root.each(function () {
            var $radio = $(this);
            if ($radio.prop('checked')) {
                $radio.data('checked', true);
            } else {
                $radio.data('checked', false);
            }

            $radio.click(function () {
                var $this = $(this);
                if ($this.data('checked')) {
                    $this.prop('checked', false);
                    $this.data('checked', false);
                    $this.trigger('change');
                } else {
                    $this.data('checked', true);
                    $this.closest('form').find('[name="' + $this.prop('name') + '"]').not($this).data('checked', false);
                }
            });
        });
        return $root;
    };
    $('.shortcode-addons-form-toggle [type=radio]').uncheckableRadio();
    function PopoverActiveDeactive($_This){
        $(".shortcode-form-control").not($_This.parents()).removeClass('popover-active');
        $_This.closest(".shortcode-form-control").toggleClass('popover-active');
        event.stopPropagation();
    }
    $(document.body).on("click", ".shortcode-form-control-content-popover .shortcode-form-control-input-wrapper", function (event) {
        PopoverActiveDeactive($(this));
    });
    $(document.body).on("click", ".shortcode-form-control-input-link", function (event) {
        PopoverActiveDeactive($(this));
        event.stopPropagation();
    });


    $('.shortcode-control-type-control-tabs .shortcode-control-type-control-tab-child:first-child').addClass('shortcode-control-tab-active');
    $('.shortcode-control-type-control-tabs .shortcode-form-control-tabs-content:first-child').removeClass('shortcode-control-tab-close');
    $(document.body).on("click", ".shortcode-control-type-control-tab-child", function () {
        $(this).siblings().removeClass("shortcode-control-tab-active");
        $(this).addClass('shortcode-control-tab-active');
        var index = $(this).index();
        $(this).parent().parent('.shortcode-form-control-content-tabs').next().children('.shortcode-form-control-tabs-content').addClass('shortcode-control-tab-close');
        $(this).parent().parent('.shortcode-form-control-content-tabs').next().children('.shortcode-form-control-tabs-content:eq(' + index + ')').removeClass('shortcode-control-tab-close');
       
    });

    (function ($) {
        setTimeout(function () {
            var data = 'body#tinymce.wp-editor{font-family:Arial,Helvetica,sans-serif!important}body#tinymce.wp-editor p{font-size:14px!important}body#tinymce.wp-editor h1{font-size:1.475em}body#tinymce.wp-editor h2{font-size:1.065em}body#tinymce.wp-editor h3{font-size:1.065em}body#tinymce.wp-editor h4{font-size:.9}body#tinymce.wp-editor h5{font-size:.75}body#tinymce.wp-editor h6{font-size:.65em}body#tinymce.wp-editor .gallery-caption,body#tinymce.wp-editor figcaption{font-size:.65em}body#tinymce.wp-editor .editor-post-title__block .editor-post-title__input{font-size:1.77em}body#tinymce.wp-editor .editor-default-block-appender .editor-default-block-appender__content{font-size:14px}body#tinymce.wp-editor .wp-block-paragraph.has-drop-cap:not(:focus)::first-letter{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif;font-size:2.12em;margin:0 .15em 0 0}.wp-block-cover .wp-block-cover-text,.wp-block-cover h2{font-size:1.07em;padding-left:.7rem;padding-right:.7rem}.wp-block-gallery .blocks-gallery-image figcaption,.wp-block-gallery .blocks-gallery-item figcaption,.wp-block-gallery .gallery-item .gallery-caption{font-size:.65em}.wp-block-button .wp-block-button__link{line-height:1.8;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif;font-size:.7em;font-weight:700}.wp-block-quote.is-large p,.wp-block-quote.is-style-large p{font-size:1.07em;margin-bottom:.4em;margin-top:.4em}.wp-block-quote .wp-block-quote__citation,.wp-block-quote cite,.wp-block-quote footer{font-size:.65em}.wp-block[data-type="core/pullquote"] blockquote>.block-library-pullquote__content .editor-rich-text__tinymce[data-is-empty=true]::before,.wp-block[data-type="core/pullquote"] blockquote>.editor-rich-text p,.wp-block[data-type="core/pullquote"] p,.wp-block[data-type="core/pullquote"][data-align=left] blockquote>.block-library-pullquote__content .editor-rich-text__tinymce[data-is-empty=true]::before,.wp-block[data-type="core/pullquote"][data-align=left] blockquote>.editor-rich-text p,.wp-block[data-type="core/pullquote"][data-align=left] p,.wp-block[data-type="core/pullquote"][data-align=right] blockquote>.block-library-pullquote__content .editor-rich-text__tinymce[data-is-empty=true]::before,.wp-block[data-type="core/pullquote"][data-align=right] blockquote>.editor-rich-text p,.wp-block[data-type="core/pullquote"][data-align=right] p{font-size:1.067em;margin-bottom:.4em;margin-top:.4em}.wp-block[data-type="core/pullquote"] .wp-block-pullquote__citation,.wp-block[data-type="core/pullquote"][data-align=left] .wp-block-pullquote__citation,.wp-block[data-type="core/pullquote"][data-align=right] .wp-block-pullquote__citation{font-size:.65em}.wp-block-file .wp-block-file__button{font-size:.75em}.wp-block-separator.is-style-dots:before{font-size:1.067em;letter-spacing:calc(2 * .63rem);padding-left:calc(2 * .63rem)}.wp-block-categories li,.wp-block-latest-posts li,ul.wp-block-archives li{font-size:calc(14px * 1.125);padding-bottom:.6rem}';
            var head = $(".mce-container-body iframe").contents().find("head");
            var css = '<style type="text/css">' + data + '</style>';
            $(head).append(css);
        }, 2000);
    })($);

    $("#oxi-addons-list-data-modal-open").on("click", function () {
        $("#oxi-addons-list-data-modal").modal("show");
        $('#oxi-template-modal-form').trigger("reset");
        $('#oxi-template-modal-submit').html('Submit');
        $('#shortcode-addons-template-modal-form *:checkbox').each(function (i, e) {
            if ($(this).attr('ckdflt') === 'true') {
                $(this).attr('checked', true);
            } else {
                $(this).attr('checked', false);
            }
        });
        $('#shortcode-addons-template-modal-form *:radio').each(function (i, e) {
            if ($(this).attr('ckdflt') === 'true') {
                $(this).attr('checked', true);
            } else {
                $(this).attr('checked', false);
            }
        });
        $('#shortcode-addons-template-modal-form .shortcode-addons-media-control-image-load').each(function (i, e) {
            $(this).attr('style', $(this).attr('ckdflt'));
        });
        $('#shortcodeitemid').val("");
        $("[data-condition]").each(function (index, value) {
            $(this).addClass('shortcode-addons-form-conditionize');
        });
       
        $('.shortcode-addons-form-conditionize').conditionize();
    });
    $("[data-condition]").each(function (index, value) {
        $(this).addClass('shortcode-addons-form-conditionize');
    });


})(jQuery);

jQuery(".OXIAddonsElementsDeleteSubmit").submit(function () {
    var status = confirm("Do you Want to Deactive this Elements?");
    if (status == false) {
        return false;
    } else {
        return true;
    }
});
jQuery(".oxi-addons-style-delete .btn.btn-danger").on("click", function () {
    var status = confirm("Do you want to Delete this Shortcode? Before delete kindly confirm that you don't use or already replaced this Shortcode. If deleted will never Restored.");
    if (status == false) {
        return false;
    } else {
        return true;
    }
});
jQuery(".btn btn-warning.oxi-addons-addons-style-btn-warning").on("click", function () {
    var status = confirm("Do you Want to Deactive This Layouts?");
    if (status == false) {
        return false;
    } else {
        return true;
    }
});

function oxiequalHeight(group) {
    tallest = 0;
    group.each(function () {
        thisHeight = jQuery(this).height();
        if (thisHeight > tallest) {
            tallest = thisHeight;
        }
    });
    group.height(tallest);
}
setTimeout(function () {
    oxiequalHeight(jQuery(".oxiequalHeight"));
}, 500);


setTimeout(function () {
    jQuery("<style type='text/css'>.oxi-addons-style-left-preview{background: " + jQuery("#shortcode-addons-2-0-preview").val() + "; } </style>").appendTo(".oxi-addons-style-left-preview");
}, 500);

oxiequalHeight(jQuery(".oxiaddonsoxiequalHeight"));

setTimeout(function () {
    if (jQuery(".table").hasClass("oxi_addons_table_data")) {
        jQuery(".oxi_addons_table_data").DataTable({
            "aLengthMenu": [[7, 25, 50, -1], [7, 25, 50, "All"]],
            "initComplete": function (settings, json) {
                jQuery(".oxi-addons-row.table-responsive").css("opacity", "1").animate({height: jQuery(".oxi-addons-row.table-responsive").get(0).scrollHeight}, 1000);
                ;
            }
        });
    }
}, 500);

 jQuery("#shortcode-addons-2-0-color").on("change", function (e) {
        $input = jQuery(this);
        jQuery("<style type='text/css'>.oxi-addons-style-left-preview{background: " + $input.val() + "; } </style>").appendTo(".oxi-addons-style-left-preview");
        jQuery('#shortcode-addons-2-0-preview').val($input.val());
 });