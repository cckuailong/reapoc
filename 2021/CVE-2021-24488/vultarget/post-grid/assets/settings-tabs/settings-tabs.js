jQuery(document).ready(function($){

    $( ".settings-tabs-loading").fadeOut();
    $( ".settings-tabs").fadeIn();

    accordion = $( ".settings-tabs .accordion").accordion({
        heightStyle:'content',
        active: 99,
        header: "> div > h3",
        collapsible: true,
    });

    $( ".settings-tabs [colorPicker]").wpColorPicker();


    $( ".settings-tabs .accordion[sortable='true']").sortable({
        axis: "y",
        handle: "h3",
        stop: function( event, ui ) {
            // IE doesn't register the blur when sorting
            // so trigger focusout handlers to remove .ui-state-focus
            ui.item.children( "h3" ).triggerHandler( "focusout" );

            // Refresh accordion to handle new order
            $( this ).accordion( "refresh" );
        }
    })



    $(".settings-tabs .sortable" ).sortable({ handle: ".sort" });


	$(document).on('click','.settings-tabs .tab-nav',function(){

		$(this).parent().parent().children('.tab-navs').children('.tab-nav').removeClass('active');

        $(this).addClass('active');

        id = $(this).attr('data-id');
        $('input[name="tab"], input.current_tab').val(id);


		//console.log('Hello click');
        //console.log(id);

        $(this).parent().parent().children('.tab-content').removeClass('active');
        $(this).parent().parent().children('.tab-content#'+id).addClass('active');

        $(this).parent().parent().children('.settings-tabs-right-panel').children('.right-panel-content').removeClass('active');
        $(this).parent().parent().children('.settings-tabs-right-panel').children('.right-panel-content-'+id).addClass('active');



    })



    $(document).on('click','.settings-tabs .field-media-wrapper .clear ',function(e){

        $(this).parent().children().children('.media-preview').attr('src', '');
        $(this).parent().children().children('.media-title').html('');
        $(this).parent().children('.media-input-value').val('');

        placeholder = $(this).attr('placeholder');
        $(this).parent().children().children('.media-preview').attr('src', placeholder);

    })

    $(document).on('click','.settings-tabs .field-media-wrapper .media-upload',function(e){
        var side_uploader;
        this_ = $(this);
        //alert(target_input);
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (side_uploader) {
            side_uploader.open();
            return;
        }
        //Extend the wp.media object
        side_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        side_uploader.on('select', function() {
            attachment = side_uploader.state().get('selection').first().toJSON();

            attachmentId = attachment.id;

            src_url = attachment.url;
            src_filename = attachment.filename;

            //console.log(attachment);

            $(this_).prev().val(attachmentId);

            $(this_).parent().children('.media-preview-wrap').children('img').attr('src',src_url);
            $(this_).parent().children().children('.media-title').html(src_filename);
        });

        //Open the uploader dialog
        side_uploader.open();

    })



    $(document).on('click','.settings-tabs .field-media-url-wrapper .media-upload',function(e){
        var side_uploader;
        this_ = $(this);
        //alert(target_input);
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (side_uploader) {
            side_uploader.open();
            return;
        }
        //Extend the wp.media object
        side_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        side_uploader.on('select', function() {
            attachment = side_uploader.state().get('selection').first().toJSON();

            attachmentId = attachment.id;
            src_url = attachment.url;
            //console.log(attachment);

            $(this_).prev().val(src_url);

            $(this_).parent().children('.media-preview-wrap').children('img').attr('src',src_url);

        });

        //Open the uploader dialog
        side_uploader.open();

    })


    $(document).on('click','.settings-tabs .field-media-url-wrapper .clear',function(e){
        $(this).parent().children('.media-preview-wrap').children('img').attr('src','');
        $(this).parent().children('input').val('');


    })


    jQuery(document).on('click', '.settings-tabs .input-text-multi-wrapper .add-item',function(){

        dataName = $(this).attr('data-name');
        dataSort = $(this).attr('data-sort');
        dataClone = $(this).attr('data-clone');
        dataPlaceholder = $(this).attr('data-placeholder');

        html = '<div class="item">';
        html += '<input  type="text" name="'+dataName+'" placeholder="'+dataPlaceholder+'" />';

        if(dataClone){
            html += ' <span class="button clone"><i class="far fa-clone"></i></span>';
        }

        if(dataSort){
            html += ' <span class="button sort" ><i class="fas fa-arrows-alt"></i></span>';
        }




        html += ' <span class="button remove" onclick="jQuery(this).parent().remove()"><i class="fas fa-times"></i></span>';
        html += '</div>';


        jQuery(this).parent().children('.field-list').append(html);



    })


    jQuery(document).on("click", ".settings-tabs .field-repeatable-wrapper .collapsible .header .title-text", function() {
        if(jQuery(this).parent().parent().hasClass("active")){
            jQuery(this).parent().parent().removeClass("active");
        }else{
            jQuery(this).parent().parent().addClass("active");
            textarea_to_editor();
        }
    })

    jQuery(document).on("click", ".settings-tabs .field-repeatable-wrapper .add-repeat-field", function() {
        now = jQuery.now();
        add_html = $(this).attr('add_html');

        repeatable_html = add_html.replace(/TIMEINDEX/g, now);

        $(this).parent().children('.repeatable-field-list').append(repeatable_html);

        textarea_to_editor();


    })


    function textarea_to_editor(){

        //textarea = $('.textarea-editor');

        var textarea = document.getElementsByClassName("textarea-editor");

        for (i = 0; i < textarea.length; i++) {

            el_id = textarea[i].id;
            el_attr = textarea[i].getAttribute('editor_enabled');

            //editor_enabled = $(this).attr('editor_enabled');


            //console.log(typeof wp.editor);

            if(el_attr == 'no' && typeof wp.editor != 'undefined'){
                wp.editor.initialize( el_id, {
                    mediaButtons: true,
                    tinymce: {
                        wpautop: true,
                        toolbar1: 'bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen |  wp_adv',
                        toolbar2: 'formatselect alignjustify forecolor | pastetext removeformat charmap table | outdent indent | undo redo | wp_help',

                    },
                    quicktags:    true,
                } );

                textarea[i].setAttribute('editor_enabled','yes')
                //$(this).attr('editor_enabled','yes');
            }



        }

    }

    $(document).on('click','.settings-tabs .textarea-editor',function(){

        id = $(this).attr('id');
        editor_enabled = $(this).attr('editor_enabled');


        //console.log(typeof wp.editor);

        if(editor_enabled == 'no' && typeof wp.editor != 'undefined'){
            wp.editor.initialize( id, {
                mediaButtons: true,
                tinymce: {
                    wpautop: true,
                    toolbar1: 'bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv',
                    toolbar2: 'formatselect alignjustify forecolor | pastetext removeformat charmap table | outdent indent | undo redo | wp_help'
                },
                quicktags:    true,
            } );

            $(this).attr('editor_enabled','yes');
        }

    })

    jQuery(document).on("click", ".settings-tabs .select-reset", function() {

        $(this).prev('select').val('');

    })






    $(document).on('click', '.settings-tabs .expandable .expand', function(){
        if($(this).parent().parent().children('.options').hasClass('active')){
            //$(this).parent().parent().removeClass('active');
            $(this).parent().parent().children('.options').removeClass('active');
        }else {
            //$(this).parent().parent().addClass('active');
            $(this).parent().parent().children('.options').addClass('active');
        }


    })

    // radio-img

    $(document).on("click", ".radio-img label", function () {
        if($(this).hasClass('disabled')){
            return;
        }

        $(this).parent().children("label").removeClass("active");
        $(this).addClass("active");

    })

    $(function() {
        $('.lazy').Lazy();
    });



});