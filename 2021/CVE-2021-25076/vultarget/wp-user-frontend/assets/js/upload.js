;(function($) {

    /**
     * Upload handler helper
     *
     * @param string {browse_button} browse_button ID of the pickfile
     * @param string {container} container ID of the wrapper
     * @param int {max} maximum number of file uplaods
     * @param string {type}
     */
    window.WPUF_Uploader = function (browse_button, container, max, type, allowed_type, max_file_size) {
        this.removed_files = [];
        this.container = container;
        this.browse_button = browse_button;
        this.max = max || 1;
        this.count = $('#' + container).find('.wpuf-attachment-list > li').length; //count how many items are there
        this.perFileCount = 0; //file count on each upload
        this.UploadedFiles = 0; //file count on each upload

        //if no element found on the page, bail out
        if( !$('#'+browse_button).length ) {
            return;
        }

        // enable drag option for ordering
        $( "ul.wpuf-attachment-list" ).sortable({
            placeholder: "highlight"
        });
        $( "ul.wpuf-attachment-list" ).disableSelection();

        //instantiate the uploader
        this.uploader = new plupload.Uploader({
            runtimes: 'html5,html4',
            browse_button: browse_button,
            container: container,
            multipart: true,
            multipart_params: {
                action: 'wpuf_upload_file',
                form_id: $( '#' + browse_button ).data('form_id')
            },
            urlstream_upload: true,
            file_data_name: 'wpuf_file',
            max_file_size: max_file_size + 'kb',
            url: wpuf_frontend_upload.plupload.url + '&type=' + type,
            flash_swf_url: wpuf_frontend_upload.flash_swf_url,
            filters: [{
                title: 'Allowed Files',
                extensions: allowed_type
            }]
        });

        //attach event handlers
        this.uploader.bind('Init', $.proxy(this, 'init'));
        this.uploader.bind('FilesAdded', $.proxy(this, 'added'));
        this.uploader.bind('QueueChanged', $.proxy(this, 'upload'));
        this.uploader.bind('UploadProgress', $.proxy(this, 'progress'));
        this.uploader.bind('Error', $.proxy(this, 'error'));
        this.uploader.bind('FileUploaded', $.proxy(this, 'uploaded'));

        this.uploader.init();

        $('#' + container).on('click', 'a.attachment-delete', $.proxy(this.removeAttachment, this));

        return this.uploader;
    };

    WPUF_Uploader.prototype = {

        init: function (up, params) {
            this.showHide();
            $('#' + this.container).prepend('<div class="wpuf-file-warning"></div>');
        },

        showHide: function () {

            if ( this.count >= this.max) {

                if ( this.count > this.max ) {
                    $('#' + this.container + ' .wpuf-file-warning').html( wpuf_frontend_upload.warning );
                } else {
                    $('#' + this.container + ' .wpuf-file-warning').html( wpuf_frontend_upload.warning );
                }

                $('#' + this.container).find('.file-selector').hide();

                return;
            };
            $('#' + this.container + ' .wpuf-file-warning').html( '' );
            $('#' + this.container).find('.file-selector').show();
        },

        added: function (up, files) {
            var $container = $('#' + this.container).find('.wpuf-attachment-upload-filelist');

            this.showHide();

            $.each(files, function(i, file) {
                $(".wpuf-submit-button").attr("disabled", "disabled");

                $container.append(
                    '<div class="upload-item" id="' + file.id + '"><div class="progress progress-striped active"><div class="bar"></div></div><div class="filename original">' +
                    file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                    '</div></div>');
            });

            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        },

        upload: function (uploader) {
            this.count = uploader.files.length - this.removed_files.length ;
            this.showHide();
        },

        progress: function (up, file) {
            var item = $('#' + file.id);

            $('.bar', item).css({ width: file.percent + '%' });
            $('.percent', item).html( file.percent + '%' );
        },

        error: function (up, error) {
            $('#' + this.container).find('#' + error.file.id).remove();

            var msg = '';
            switch (error.code) {
                case -600:
                    msg = wpuf_frontend_upload.plupload.size_error;
                    break;

                case -601:
                    msg = wpuf_frontend_upload.plupload.type_error;
                    break;

                default:
                    msg = 'Error #' + error.code + ': ' + error.message;
                    break;
            }

            alert(msg);

            this.count -= 1;
            this.showHide();
            this.uploader.refresh();
        },

        uploaded: function (up, file, response) {
            try {
                var res = $.parseJSON(response.response);
            }catch (e) {
                var text = true;
            }

            var self = this;

            $('#' + file.id + " b").html("100%");
            $('#' + file.id).remove();

            if( text ) {
                this.perFileCount++;
                this.UploadedFiles++;
                var $container = $('#' + this.container).find('.wpuf-attachment-list');
                $container.append(response.response);

                if ( this.perFileCount > this.max ) {
                    var attach_id = $('.wpuf-image-wrap:last a.attachment-delete',$container).data('attach-id');
                    self.removeExtraAttachment(attach_id);
                    $('.wpuf-image-wrap',$container).last().remove();
                    this.perFileCount--;
                }

            } else {
                alert(res.data.replace( /(<([^>]+)>)/ig, ''));

                up.files.pop();
                this.count -= 1;
                this.showHide();

            }

            var uploaded        = this.UploadedFiles,
                FileProgress    = up.files.length;

            if ( this.count >= this.max ) {
                $('#' + this.container).find('.file-selector').hide();
            }

            if ( FileProgress === uploaded ) {
                if ( typeof grecaptcha !== 'undefined' && $('#g-recaptcha-response').length ) {
                    if ( !grecaptcha.getResponse().length ){
                        return;
                    }
                }

                $(".wpuf-submit-button").removeAttr("disabled");
            }
        },

        removeAttachment: function(e) {
            e.preventDefault();

            var self = this,
            el = $(e.currentTarget);

            swal({
                text: wpuf_frontend_upload.confirmMsg,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d54e21',
                confirmButtonText: wpuf_frontend_upload.delete_it,
                cancelButtonText: wpuf_frontend_upload.cancel_it,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
            }).then(function () {
                var data = {
                    'attach_id' : el.data('attach-id'),
                    'nonce' : wpuf_frontend_upload.nonce,
                    'action' : 'wpuf_file_del'
                };
                self.removed_files.push(data);
                jQuery('#del_attach').val(el.data('attach-id'));
                jQuery.post(wpuf_frontend_upload.ajaxurl, data, function() {
                    self.perFileCount--;
                    el.parent().parent().remove();

                    self.count -= 1;
                    self.showHide();
                    self.uploader.refresh();
                });
            });
        },

        removeExtraAttachment : function( attach_id ) {
            var self = this;

            var data = {
                'attach_id' : attach_id,
                'nonce' : wpuf_frontend_upload.nonce,
                'action' : 'wpuf_file_del'
            };
            this.removed_files.push(data);
            jQuery.post(wpuf_frontend_upload.ajaxurl, data, function() {
                self.count -= 1;
                self.showHide();
                self.uploader.refresh();
            });
        }

    };
})(jQuery);
