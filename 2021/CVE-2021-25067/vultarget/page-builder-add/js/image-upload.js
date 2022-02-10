// Uploading files

(function($){

   var file_frame;
   var buttonID;
  $('.upload_bg').on('click', function( event ){

    try {
      event.preventDefault();

      var id = $(this).data('id');
      var altField = $(this).siblings('.altTextField');
      var titleField = $(this).siblings('.titleTextField');
      // Create the media frame.
      file_frame = wp.media.frames.file_frame = wp.media({
        title: $( this ).data( 'uploader_title' ),
        button: {
          text: $( this ).data( 'uploader_button_text' ),
        },
        multiple: false  // Set to true to allow multiple files to be selected
      });

      // When an image is selected, run a callback.
      file_frame.on( 'select', function() {
        
        // We set multiple to false so only get one image from the uploader
        attachment = file_frame.state().get('selection').first().toJSON();
        $( '.upload_image_button'+id).val( attachment.url );
        $( '.upload_image_button'+id).trigger('change');


        if ( attachment.alt != '' && attachment.alt != ' ' ) {
          $(altField).val(attachment.alt);
          $(altField).trigger('change');
        }

        if ( attachment.title != '' && attachment.title != ' ' ) {
          $(titleField).val(attachment.title);
          $(titleField).trigger('change');
        }
        
        //  $( '.slider_preview_image' ).attr( 'src', attachment.url );
        
       
      });

      // Finally, open the modal
      file_frame.open();

    } catch(e) {
      // statements
      console.log(e);
    }

  });


$('.upload_bg0').on('click', function( event ){

  try {
    
    event.preventDefault();

    var id = $(this).data('id');

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $( this ).data( 'uploader_title' ),
      button: {
        text: $( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
      $( '.upload_image_button'+id).val( attachment.url );

    //  $( '.slider_preview_image' ).attr( 'src', attachment.url );
      
     
    });

    // Finally, open the modal
    file_frame.open();

  } catch(e) {
    // statements
    console.log(e);
  }
    
});


$(document).on('click','.fontUploadBtn', function( event ){

  try {
    
    event.preventDefault();

    var id = $(this).data('id');
    var fontType = $(this).data('fonttype');

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $( this ).data( 'uploader_title' ),
      button: {
        text: $( this ).data( 'uploader_button_text' ),
      },
      multiple: false  
    });

    file_frame.on( 'select', function() {
      
      attachment = file_frame.state().get('selection').first().toJSON();
      $( '.image_location_'+fontType+'_'+id).val( attachment.url );
      
     
    });

    file_frame.open();

  } catch(e) {
    // statements
    console.log(e);
  }
    
});




$('.upload_image_modal').on('click',function( event ){

  try {
    
    event.preventDefault();

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $( this ).data( 'uploader_title' ),
      button: {
        text: $( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();

      $( '.image_attach_url' ).val( attachment.url );

      $( '.image_modal_preview').attr( 'src', attachment.url );

    });

    // Finally, open the modal
    file_frame.open();

  } catch(e) {
    // statements
    console.log(e);
  }
    
});


$(document).on('click','.upload_bg_btn_imageSlider', function( event ){

  try {
    
    event.preventDefault();

    var id = $(this).data('id');
    var altField = $(this).siblings('.altTextField');
    var titleField = $(this).siblings('.titleTextField');
    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $( this ).data( 'uploader_title' ),
      button: {
        text: $( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
      $( '.upload_image_button'+id).val( attachment.url );
      $( '.upload_image_button'+id).trigger('change');


      if ( attachment.alt != '' && attachment.alt != ' ' ) {
        $(altField).val(attachment.alt);
        $(altField).trigger('change');
      }

      if ( attachment.title != '' && attachment.title != ' ' ) {
        $(titleField).val(attachment.title);
        $(titleField).trigger('change');
      }
      
    //  $( '.slider_preview_image' ).attr( 'src', attachment.url );
      
     
    });

    // Finally, open the modal
    file_frame.open();
    
  } catch(e) {
    // statements
    console.log(e);
  }

    
});


})(jQuery);





(function($) {
  $(document).ready( function() {
    $('.upload_images_multi_gall').on( 'click', function() {
      // Accepts an optional object hash to override default values.
      var frame = new wp.media.view.MediaFrame.Select({
        // Modal title
        title: 'Add Images To Gallery',

        // Enable/disable multiple select
        multiple: true,

        // Library WordPress query arguments.
        library: {
          order: 'DESC',

          // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
          // 'id', 'post__in', 'menuOrder' ]
          orderby: 'date',

          // mime type. e.g. 'image', 'image/jpeg'
          type: 'image',

          // Searches the attachment title.
          search: null,

          // Attached to a specific post (ID).
          uploadedTo: null
        },

        button: {
          text: 'Add Selected Images'
        }
      });

      var id = $(this).data('id');

      frame.on( 'select', function() {
        selectedImages = frame.state().get('selection').toJSON();

        $.each(selectedImages, function(index, val){

          slideCountA = Math.floor(Math.random() * 10) + index;
          jQuery('.customImageGalleryItems').append(
            
            '<li>'+
                '<h3 class="handleHeader"> Image  <span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> <img src="'+val.url+'" style="width:20px; margin-right:10px; float:right;"> </h3>'+
                '<div  class="accordContentHolder">'+

                    "<label>Select Image :</label>"+
                    '<input id="image_location'+slideCountA+'" type="text" class="gallItemUrl upload_image_button'+slideCountA+'"  name="lpp_add_img_'+slideCountA+'" value="'+val.url+'"  placeholder="Insert Video URL here" style="width:40%;" />'+
                    "<label></label>"+
                    '<input id="image_location'+slideCountA+'" type="button" class="upload_bg_btn_imageSlider" data-id="'+slideCountA+'" value="Select" />'+
                    "<br><br><br><br><hr><br>"+

                    "<label> Image Link : </label>"+
                    "<input type'url' placeholder='' value='' class='gallItemLink' data-optname='gallItems."+index+".gli' >"+
                    "<br><br><br><hr><br>"+

                    "<label> Title : </label>"+
                    "<input type'text' placeholder='This is also alt text of image' value='"+val.title+"' class='gallItemTitle'  >"+
                    "<br><br><br><hr><br>"+

                    "<label> Caption : </label>"+
                    "<textarea class='gallItemCaption'>"+val.caption+"</textarea>"+


                '</div>'+
            '</li>'

          );


        });

        jQuery( '.customImageGalleryItems' ).accordion( "refresh" );

        pageBuilderApp.changedOpType = 'specific';
        pageBuilderApp.changedOpName = 'slideListEdit';

        var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
        jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

      });

      // Fires after the frame markup has been built, but not appended to the DOM.
      // @see wp.media.view.Modal.attach()
      frame.on( 'ready', function() {} );

      // Fires when the frame's $el is appended to its DOM container.
      // @see media.view.Modal.attach()
      frame.on( 'attach', function() {} );

      // Fires when the modal opens (becomes visible).
      // @see media.view.Modal.open()
      frame.on( 'open', function() {} );

      // Fires when the modal closes via the escape key.
      // @see media.view.Modal.close()
      frame.on( 'escape', function() {} );

      // Fires when the modal closes.
      // @see media.view.Modal.close()
      frame.on( 'close', function() {} );

      // Fires when a user has selected attachment(s) and clicked the select button.
      // @see media.view.MediaFrame.Post.mainInsertToolbar()
      frame.on( 'select', function() {
        var selectionCollection = frame.state().get('selection');
      } );

      // Fires when a state activates.
      frame.on( 'activate', function() {} );

      // Fires when a mode is deactivated on a region.
      frame.on( '{region}:deactivate', function() {} );
      // and a more specific event including the mode.
      frame.on( '{region}:deactivate:{mode}', function() {} );

      // Fires when a region is ready for its view to be created.
      frame.on( '{region}:create', function() {} );
      // and a more specific event including the mode.
      frame.on( '{region}:create:{mode}', function() {} );

      // Fires when a region is ready for its view to be rendered.
      frame.on( '{region}:render', function() {} );
      // and a more specific event including the mode.
      frame.on( '{region}:render:{mode}', function() {} );

      // Fires when a new mode is activated (after it has been rendered) on a region.
      frame.on( '{region}:activate', function() {} );
      // and a more specific event including the mode.
      frame.on( '{region}:activate:{mode}', function() {} );

      // Get an object representing the current state.
      frame.state();

      // Get an object representing the previous state.
      frame.lastState();

      // Open the modal.
      frame.open();
    });
  });
})(jQuery);



