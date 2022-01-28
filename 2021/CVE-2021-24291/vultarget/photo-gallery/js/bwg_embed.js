/*server side analogue is function display_embed in WDWLibraryEmbed*/
/*params
  embed_type: string , one of predefined accepted types
  embed_id: string, id of media in corresponding host, or url if no unique id system is defined for host
  attrs: object with html attributes and values format e.g. {width:'100px', style:"display:inline;"}
*/

function spider_display_embed(embed_type, file_url, embed_id, attrs) {
  var html_to_insert = '';
  switch(embed_type) {
    case 'EMBED_OEMBED_YOUTUBE_VIDEO':
      var oembed_youtube_html ='<iframe ';
      if(embed_id!=''){
        oembed_youtube_html += ' src="' + '//www.youtube.com/embed/'+ embed_id + '?enablejsapi=1&wmode=transparent"';
      }
      for (attr in attrs) {
        if(!(/src/i).test(attr)){
          if(attr != '' && attrs[attr] != ''){
            oembed_youtube_html += ' '+ attr + '="' + attrs[attr] + '"';
          }
        }
      }
      oembed_youtube_html += " ></iframe>";
      html_to_insert += oembed_youtube_html;
            
      break;
    case 'EMBED_OEMBED_VIMEO_VIDEO':
      var oembed_vimeo_html ='<iframe ';
      if(embed_id!=''){
        oembed_vimeo_html += ' src="' + '//player.vimeo.com/video/' + embed_id + '?enablejsapi=1"';
      }
      for (attr in attrs) {
        if(!(/src/i).test(attr)){
          if(attr != '' && attrs[attr] != ''){
            oembed_vimeo_html += ' '+ attr + '="' + attrs[attr] + '"';
          }
        }
      }
      oembed_vimeo_html += " ></iframe>";
      html_to_insert += oembed_vimeo_html;
            
      break;
    case 'EMBED_OEMBED_FLICKR_IMAGE':
        var oembed_flickr_html ='<div ';     
        for (attr in attrs) {
        if(!(/src/i).test(attr)){
          if(attr != '' && attrs[attr] != ''){
            oembed_flickr_html += ' '+ attr + '="'+ attrs[attr] + '"';
          }
        }
      }
        oembed_flickr_html += " >";
        if(embed_id!=''){
        
        oembed_flickr_html += '<img src="'+embed_id+'"'+ 
        ' style="'+
        'max-width:'+'100%'+" !important"+
        '; max-height:'+'100%'+" !important"+
        '; width:'+'auto !important'+
        '; height:'+ 'auto !important' + 
        ';">';
        }

        oembed_flickr_html +="</div>";

        html_to_insert += oembed_flickr_html;
        break;
    case 'EMBED_OEMBED_FLICKR_VIDEO':
        /* code...*/
        break;
    case 'EMBED_OEMBED_INSTAGRAM_VIDEO':
      var oembed_instagram_html = '<div ';
      for ( attr in attrs ) {
        if ( !(/src/i).test(attr) ) {
          if ( attr != '' && attrs[attr] != '' ) {
            oembed_instagram_html += ' ' + attr + '="' + attrs[attr] + '"';
          }
        }
      }
      oembed_instagram_html += " >";
      if ( file_url != '' ) {
        /*oembed_instagram_html += '<iframe src="'+embed_id+'"'+
        ' style="'+
        'max-width:'+'100%'+" !important"+
        '; max-height:'+'100%'+" !important"+
        '; width:'+'auto'+
        '; height:'+ '100%' + " "+
        '; margin:0;"'+
        'frameborder="0" scrolling="no" allowtransparency="false"></iframe>';
        */

        oembed_instagram_html += '<video style="width:auto !important; height:auto !important; max-width:100% !important; max-height:100% !important; margin:0 !important;" controls>' +
          '<source src="' + decodeURIComponent(file_url) +
          '" type="video/mp4"> Your browser does not support the video tag. </video>';
      }
      oembed_instagram_html += "</div>";
      html_to_insert += oembed_instagram_html;
      break;
    case 'EMBED_OEMBED_INSTAGRAM_IMAGE':
      var oembed_instagram_html = '<div ';
      for ( attr in attrs ) {
        if ( !(/src/i).test(attr) ) {
          if ( attr != '' && attrs[attr] != '' ) {
            oembed_instagram_html += ' ' + attr + '="' + attrs[attr] + '"';
          }
        }
      }
      oembed_instagram_html += " >";
      if ( file_url != '' ) {
        oembed_instagram_html += '<img src="' + decodeURIComponent(file_url) + '"' +
          ' style=" ' +
          'max-width:' + '100%' + " !important" +
          '; max-height:' + '100%' + " !important" +
          '; width:' + 'auto' +
          '; height:' + 'auto' +
          ';">';
      }
      oembed_instagram_html += "</div>";
      html_to_insert += oembed_instagram_html;
      break;
    case 'EMBED_OEMBED_INSTAGRAM_POST':
      var oembed_instagram_html = '<div ';
      var id = '';
      for ( attr in attrs ) {
        if ( !(/src/i).test(attr) ) {
          if ( attr != '' && attrs[attr] != '' ) {
            oembed_instagram_html += ' ' + attr + '="' + attrs[attr] + '"';
            if ( attr == 'CLASS' || attr == 'class' || attr == 'Class' ) {
              obj_class = attrs[attr];
            }
          }
        }
      }
      oembed_instagram_html += ">";
      if ( file_url != '' ) {
        oembed_instagram_html += '<div class="inner_instagram_iframe_' + obj_class + '" frameborder="0" scrolling="no" allowtransparency="false" allowfullscreen ' +
          ' style="max-width: 100% !important; max-height: 100% !important; width: 100%; height: 100%; margin:0; vertical-align:middle;">' + atob(file_url) + '</div>';
      }
      oembed_instagram_html += "</div>";
      html_to_insert += oembed_instagram_html;
      break;
	  case 'EMBED_OEMBED_FACEBOOK_IMAGE':
      var oembed_facebook_html ='<span ';	
        for (attr in attrs) {
          if(!(/src/i).test(attr)){
            if(attr != '' && attrs[attr] != ''){
              oembed_facebook_html += ' '+ attr + '="'+ attrs[attr] + '"';
            }
          }
        }
      oembed_facebook_html += " >";
      if(embed_id!=''){
        oembed_facebook_html += '<img src="'+file_url+'"'+
        ' style=" '+
        'max-width:'+'100%'+" !important"+
        '; max-height:'+'100%'+" !important"+
        '; width:'+'auto'+
        '; height:'+ '100%' +
        ';">';
      }
      oembed_facebook_html +="</span>";
      html_to_insert += oembed_facebook_html;
    break; 	
	  case 'EMBED_OEMBED_FACEBOOK_VIDEO':
      var oembed_facebook_video_html ='<div ';     
        for (attr in attrs) {
          if(!(/src/i).test(attr)){
            if(attr != '' && attrs[attr] != ''){
              oembed_facebook_video_html += ' '+ attr + '="'+ attrs[attr] + '"';
            }
          }
        }
      oembed_facebook_video_html += " >";
      if(embed_id!=''){
        oembed_facebook_video_html += '<iframe src="//www.facebook.com/video/embed?video_id='+file_url+'&enablejsapi=1&wmode=transparent"' +
        ' style="'+
        'max-width:'+'100%'+" !important"+
        '; max-height:'+'100%'+" !important"+
        '; width:'+'100%'+
        '; height:'+ '100%' + 
        '; margin:0'+
        '; display:table-cell; vertical-align:middle;"'+
        'frameborder="0" class="bwg_fb_video" scrolling="no" allowtransparency="false" allowfullscreen'+
        '></iframe>';
      }
      oembed_facebook_video_html +="</div>";
      html_to_insert += oembed_facebook_video_html;
    break;  
    case 'EMBED_OEMBED_DAILYMOTION_VIDEO':
      var oembed_dailymotion_html ='<iframe ';
      if(embed_id!=''){
        oembed_dailymotion_html += ' src="' + '//www.dailymotion.com/embed/video/'+embed_id + '?api=postMessage"';
      }
      for (attr in attrs) {
        if(!(/src/i).test(attr)){
          if(attr != '' && attrs[attr] != ''){
            oembed_dailymotion_html += ' '+ attr + '="'+ attrs[attr] + '"';
          }
        }
      }
      oembed_dailymotion_html += " ></iframe>";
      html_to_insert += oembed_dailymotion_html;
            
      break;
    case 'EMBED_OEMBED_IMGUR':
    /*not working yet*/
      var oembed_imgur_html ='<div ';     
        for (attr in attrs) {
        if(!(/src/i).test(attr)){
          if(attr != '' && attrs[attr] != ''){
            oembed_instagram_html += ' '+ attr + '="'+ attrs[attr] + '"';
          }
        }
      }
      oembed_imgur_html += " >";
        if(embed_id!=''){

        oembed_imgur_html += '<img src="'+embed_id+'"'+ 
        ' style="'+
        'max-width:'+'100%'+" !important"+
        '; max-height:'+'100%'+" !important"+
        '; width:'+'auto'+
        '; height:'+ 'auto' + " !important"+
        ';">';
        }
        oembed_imgur_html +="</div>";

        html_to_insert += oembed_imgur_html;

        break;
    case 'EMBED_OEMBED_GOOGLE_PHOTO_IMAGE':
      var oembed_google_photos_html ='<div ';
      for (attr in attrs) {
        if(!(/src/i).test(attr)){
          if(attr != '' && attrs[attr] != ''){
            oembed_google_photos_html += ' '+ attr + '="'+ attrs[attr] + '"';
          }
        }
      }
      oembed_google_photos_html += " >";
      if(embed_id!=''){

        oembed_google_photos_html += '<img src="'+file_url+'"'+
          ' style=" '+
          'max-width:'+'100%'+" !important"+
          '; max-height:'+'100%'+" !important"+
          '; width:'+'auto'+
          '; height:'+ 'auto' +
          ';">';
      }
      oembed_google_photos_html +="</div>";

      html_to_insert += oembed_google_photos_html;

      break;
    default:
      var html = {content: ''};
      jQuery(document).trigger('bwg_display_embed', [html, embed_type, file_url, embed_id, attrs]);
      html_to_insert = html.content;
  }
  
  return html_to_insert

}

/**
 * @param from_popup: optional, true if from bulk embed popup, false(default) if from instagram gallery
 * @return "ok" if adds instagram gallery, false if any error when adding instagram gallery
 */
function bwg_add_instagram_gallery(instagram_access_token, from_popup){
  from_popup = typeof from_popup !== 'undefined' ? from_popup : false;
  /*if bulk_embed action*/
  if (from_popup === true) {
    if (bwg_check_instagram_gallery_input(instagram_access_token, from_popup)){
      return false;
    }
    var whole_post = '0';
    if(jQuery("input[name=popup_instagram_post_gallery]:checked").val() == 1){
      whole_post = '1';
    };
    var instagram_user = encodeURI(jQuery("#popup_instagram_gallery_source").val());
    var autogallery_image_number = encodeURI(jQuery("#popup_instagram_image_number").val());
  }
  else{
    /*check if there is problem with input*/
    if ( bwg_check_instagram_gallery_input(instagram_access_token, from_popup) ) {
      return false;
    }
    if ( !bwg_check_gallery_empty(false, true) ) {
      return false;
    }
    var whole_post = '0';
    if ( jQuery("input[name=instagram_post_gallery]:checked").val() == 1 ) {
      whole_post = '1';
    }
    
    var instagram_user = encodeURI(jQuery("#gallery_source").val()); // @ToDo if content type instagram only then user is undefined.
    var update_flag = jQuery("input[name=update_flag]:checked").val();
    var autogallery_image_number = encodeURI(jQuery("#autogallery_image_number").val());
  }
  jQuery('#bulk_embed').hide();
  jQuery('#loading_div').show();

 /*prepare data for request*/
  var filesValid = [];
  var data = {
    'action': 'addInstagramGallery',
    'instagram_user': instagram_user, // @ToDo instagram user is undefined
    'instagram_access_token': instagram_access_token,
    'whole_post': whole_post,
    'autogallery_image_number':autogallery_image_number,
    'update_flag':update_flag,
    'async':true
  };

   /* get response data. Here we use the server as a proxy, since Cross-Origin Resource Sharing AJAX is forbidden. */
  jQuery.post(ajax_url, data, function(response) {
    if ( response == false ) {
      alert('Error: cannot get response from the server.');
      jQuery('#loading_div').hide();
      if(from_popup){
        jQuery('#bulk_embed').show();
      }
      return false;
    }
    else {
      var index_start = response.indexOf("WD_delimiter_start");
      var index_end = response.indexOf("WD_delimiter_end");
      if(index_start == -1 || index_end == -1){
        jQuery('#loading_div').hide();
        if(from_popup){
          jQuery('#bulk_embed').show();
        }
        return false;
      }

      /*filter out other echoed characters*/
      /*18 is the length of "wd_delimiter_start"*/
      response = response.substring(index_start+18,index_end);
      response_JSON = JSON.parse(response);

      if(!response_JSON ){
        alert('There is some error. Cannot add Instagram gallery.');
        jQuery('#loading_div').hide();
        if(from_popup){
          jQuery('#bulk_embed').show();
        }
        return false;
      }
      else{
        if(response_JSON[0] == 'error'){
          alert('Error: ' + JSON.parse(response)[1]);
          jQuery('#loading_div').hide();
          if(from_popup){
            jQuery('#bulk_embed').show();
          }
          return false;
        }
        else{
          var len = response_JSON.length;
          for (var i=1; i<=len; i++) {
            if(response_JSON[len-i]!= false){
              var item = response_JSON[len-i];
              filesValid.push(item);
            }
          }
          bwg_add_image(filesValid);
          if(!from_popup){
            bwg_gallery_update_flag();
            jQuery('#tr_instagram_gallery_add_button').hide();
          }
          jQuery('#loading_div').hide();
          if(from_popup){
            jQuery('.opacity_bulk_embed').hide();
          }
          return "ok";
        }
      }      
    }/*end of considering all cases*/
  });
}