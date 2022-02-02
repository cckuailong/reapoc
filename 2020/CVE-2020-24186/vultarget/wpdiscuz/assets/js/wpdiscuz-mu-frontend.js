jQuery(document).ready(function ($) {

    $(document).delegate('#wpdcom .wmu-upload-wrap', 'click', function () {
        $('.wpd-form-foot', $(this).parents('.wpd_comm_form')).slideDown(parseInt(wpdiscuzAjaxObj.enableDropAnimation) ? 500 : 0);
    });

    $(document).delegate('.wmu-add-files', 'change', function () {
        var btn = $(this);
        var form = btn.parents('.wpd_comm_form');
        var files = btn[0].files ? btn[0].files : [];
        if (files.length) {
            wmuUploadFiles(btn, form, files);
        }
    });

    function wmuUploadFiles(btn, form, files) {
        var data = new FormData();
        data.append('action', 'wmuUploadFiles');
        data.append('wmu_nonce', wpdiscuzAjaxObj.wmuSecurity);
        data.append('wmuAttachmentsData', $('.wmu-attachments-data', form).val());
        var size = 0;
        $.each(files, function (i, file) {
            size += file.size;
            data.append(wpdiscuzAjaxObj.wmuInput + '[' + i + ']', file);
        });
        if (size > parseInt(wpdiscuzAjaxObj.wmuMaxFileSize)) {
            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj.wmuPhraseMaxFileSize, 'error', 3000);
        } else if (size > parseInt(wpdiscuzAjaxObj.wmuPostMaxSize)) {
            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj.wmuPhrasePostMaxSize, 'error', 3000);
        } else {
            wpdiscuzAjaxObj.getAjaxObj(true, true, data)
                    .done(function (r) {
                        if (r.success) {
                            $('.wmu-attached-data-info', form).remove();
                            $('.wmu-add-files', form).after(r.data.attachmentsHtml);
                            if (r.data.tooltip) {
                                $('.wmu-upload-wrap').attr('wpd-tooltip', r.data.tooltip);
                            }
                            wmuDisplayPreviews(form, r);
                            if (r.data.errors) {
                                wpdiscuzAjaxObj.setCommentMessage(r.data.errors, 'error', 3000);
                                console.log(r.data.errors);
                            }
                        } else {
                            if (r.data.errorCode) {
                                wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data.errorCode], 'error', 3000);
                            } else if (r.data.error) {
                                wpdiscuzAjaxObj.setCommentMessage(r.data.error, 'error', 3000);
                            }
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        }
    }

    function wmuDisplayPreviews(form, r) {
        $.each(r.data.previewsData, function (key, fileList) {
            $('.wmu-action-wrap .wmu-' + key + '-tab', form).html('');
            $.each(fileList, function (index, fileData) {
                var pId = fileData.id;
                var pIcon = '';
                var pFullname = fileData.fullname;
                var pShortname = fileData.shortname;
                if (key == wpdiscuzAjaxObj.wmuKeyImages) {
                    pIcon = fileData.url;
                    pShortname = '';
                } else if (key == wpdiscuzAjaxObj.wmuKeyVideos) {
                    pIcon = wpdiscuzAjaxObj.wmuIconVideo;
                } else if (key == wpdiscuzAjaxObj.wmuKeyFiles) {
                    pIcon = wpdiscuzAjaxObj.wmuIconFile;
                }
                var previewTemplate = '<div class="wmu-preview [PREVIEW_TYPE_CLASS]" title="[PREVIEW_TITLE]" data-wmu-type="[PREVIEW_TYPE]" data-wmu-attachment="[PREVIEW_ID]"><div class="wmu-preview-remove"><img class="wmu-preview-img" src="[PREVIEW_ICON]"><div class="wmu-file-name">[PREVIEW_FILENAME]</div><div class="wmu-delete">&nbsp;</div></div></div>';
                previewTemplate = previewTemplate.replace('[PREVIEW_TYPE_CLASS]', 'wmu-preview-' + key);
                previewTemplate = previewTemplate.replace('[PREVIEW_TITLE]', pFullname);
                previewTemplate = previewTemplate.replace('[PREVIEW_TYPE]', key);
                previewTemplate = previewTemplate.replace('[PREVIEW_ID]', pId);
                previewTemplate = previewTemplate.replace('[PREVIEW_ICON]', pIcon);
                previewTemplate = previewTemplate.replace('[PREVIEW_FILENAME]', pShortname);
                $('.wmu-action-wrap .wmu-' + key + '-tab', form).removeClass('wmu-hide').append(previewTemplate);
            });
        });
    }

    $(document).delegate('.wmu-attachment-delete', 'click', function (e) {
        if (confirm(wpdiscuzAjaxObj.wmuPhraseConfirmDelete)) {
            var btn = $(this);
            var attachmentId = btn.data('wmu-attachment');
            var data = new FormData();
            data.append('action', 'wmuDeleteAttachment');
            data.append('attachmentId', attachmentId);
            wpdiscuzAjaxObj.getAjaxObj(true, true, data)
                    .done(function (r) {
                        if (r.success) {
                            var parent = $('.wmu-attachment-' + attachmentId).parents('.wmu-comment-attachments');
                            $('.wmu-attachment-' + attachmentId).remove();
                            if (!$('.wmu-attached-images *', parent).length) {
                                $('.wmu-attached-images', parent).remove();
                            }
                            if (!$('.wmu-attached-videos *', parent).length) {
                                $('.wmu-attached-videos', parent).remove();
                            }
                            if (!$('.wmu-attached-files *', parent).length) {
                                $('.wmu-attached-files', parent).remove();
                            }
                        } else {
                            if (r.data.errorCode) {
                                wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data.errorCode], 'error', 3000);
                            } else if (r.data.error) {
                                wpdiscuzAjaxObj.setCommentMessage(r.data.error, 'error', 3000);
                            }
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        } else {
            console.log('canceled');
        }
    });
    /**
     * ajax request
     * remove preview from frontend (html) and backend (db data)
     */
    $(document).delegate('.wmu-preview', 'click', function () {
        var btn = $(this);
        var form = btn.parents('.wpd_comm_form');
        var type = btn.data('wmu-type');
        var id = btn.data('wmu-attachment');
        var data = new FormData();
        data.append('action', 'wmuRemoveAttachmentPreview');
        data.append('attachmentId', id);
        data.append('wmuAttachmentsData', $('.wmu-attachments-data', form).val());
        wpdiscuzAjaxObj.getAjaxObj(true, true, data)
                .done(function (r) {
                    if (r.success) {
                        btn.remove();
                        var tabs = $('.wmu-tabs', form);
                        $.each(tabs, function (i, tab) {
                            if ($('.wmu-preview', tab).length) {
                                $(tab).removeClass('wmu-hide');
                            } else {
                                $(tab).addClass('wmu-hide');
                            }
                        });
                        $('.wmu-attached-data-info', form).remove();
                        $('.wmu-add-files', form).after(r.data.attachmentsHtml);
                        if (r.data.tooltip) {
                            $('.wmu-upload-wrap').attr('wpd-tooltip', r.data.tooltip);
                        }
                    } else {
                        if (r.data.errorCode) {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data.errorCode], 'error', 3000);
                        } else if (r.data.error) {
                            wpdiscuzAjaxObj.setCommentMessage(r.data.error, 'error', 3000);
                        }
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });

    if (parseInt(wpdiscuzAjaxObj.wmuIsLightbox)) {
        function wmuAddLightBox() {
            $(".wmu-lightbox").colorbox({
                maxHeight: "95%",
                maxWidth: "95%",
                rel: 'wmu-lightbox',
                fixed: true
            });
        }
        wmuAddLightBox();
        wpdiscuzAjaxObj.wmuAddLightBox = wmuAddLightBox;
    }

    wpdiscuzAjaxObj.wmuHideAll = function (r, wcForm) {
        if (typeof r === 'object') {
            if (r.success) {
                $('.wmu-tabs', wcForm).addClass('wmu-hide');
                $('.wmu-preview', wcForm).remove();
                $('.wmu-attached-data-info', wcForm).remove();
            } else {
                console.log(r.data);
            }
        } else {
            console.log(r);
        }
    }

});