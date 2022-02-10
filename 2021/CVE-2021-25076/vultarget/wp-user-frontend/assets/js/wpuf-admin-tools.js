jQuery(function($){
    Vue.component( 'wpuf-form-uploader', {
        template: '#wpuf-import-form-template',
        data: function() {
            return {
                fileFrame: null,
                isBusy: false,
            };
        },

        methods: {
            openImageManager: function() {
                var vm = this;

                if (vm.fileFrame) {
                    vm.fileFrame.open();
                    return;
                }

                var fileStatesOptions = {
                    library: wp.media.query(),
                    multiple: false, // set it true for multiple image
                    title: wpuf_admin_tools.i18n.wpuf_import_forms,
                    priority: 20,
                    filterable: 'uploaded'
                };

                var options = {};

                fileStatesOptions = $.extend(true, fileStatesOptions, options.fileStatesOptions);

                var fileStates = [
                    new wp.media.controller.Library(fileStatesOptions)
                ];

                var mediaOptions = {
                    title: wpuf_admin_tools.i18n.wpuf_import_forms,
                    library: {
                        type: ''
                    },
                    button: {
                        text: wpuf_admin_tools.i18n.add_json_file,
                    },
                    multiple: false
                };

                mediaOptions = $.extend(true, mediaOptions, options.mediaOptions);
                mediaOptions.states = fileStates;

                vm.fileFrame = wp.media(mediaOptions);

                vm.fileFrame.on('select', function() {
                    var selectedFiles = [];
                    var selection = vm.fileFrame.state().get('selection');

                    var selectedFiles = selection.map(function(attachment) {
                        return attachment.toJSON();
                    });

                    var selectedFile = selectedFiles[0];

                    vm.importForms(selectedFile.id);
                });

                vm.fileFrame.on('ready', function() {
                    vm.fileFrame.uploader.options.uploader.params = {
                        type: 'wpuf-form-uploader'
                    };
                });

                vm.fileFrame.open();
            },

            importForms: function ( fileId ) {
                var vm = this;

                vm.isBusy = true;

                $.ajax( {
                    url: wpuf_admin_tools.url.ajax,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        action: 'wpuf_import_forms',
                        _wpnonce: wpuf_admin_tools.nonce,
                        file_id: fileId,
                    }
                } ).done( function ( response ) {
                    if ( response.data && response.data.message ) {
                        alert( response.data.message );
                    }
                } ).fail( function ( jqXHR ) {
                    if ( jqXHR.responseJSON && jqXHR.responseJSON.data ) {
                        var message = jqXHR.responseJSON.data[0].message;
                        alert( message );
                    } else {
                        alert( wpuf_admin_tools.i18n.could_not_import_forms );
                    }
                } ).always( function () {
                    vm.isBusy = false;
                } );
            },
        }
    } );

    new Vue( {
        el: '#wpuf-import-form',
    } );
});
