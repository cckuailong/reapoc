var isPopUpOpened = false;
var wdi_data = [];

function wdi_spider_createpopup(url, current_view, width, height, duration, description, lifetime, currentFeed, image_id) {
    url = url.replace(/&#038;/g, '&');
    if (isPopUpOpened) {
        return
    }
    isPopUpOpened = true;
    if (wdi_spider_hasalreadyreceivedpopup(description) || wdi_spider_isunsupporteduseragent()) {
        return;
    }

    jQuery("html").attr("style", "overflow:hidden !important;");
    jQuery("#wdi_spider_popup_loading_" + current_view).css({
        display: "block"
    });
    jQuery("#wdi_spider_popup_overlay_" + current_view).css({
        display: "block"
    });

    var current_image_index = 0;
    var current_image_row;
    for (var i = 0; i < currentFeed.parsedData.length; i++) {
        if(currentFeed.parsedData[i].id  === image_id){

            current_image_index = i;
            current_image_row = [currentFeed.parsedData[i]];
            break;
        }
    }

    jQuery.ajax({
        type: 'POST',
        url: url,
        dataType: 'text',
        data: {
            action: 'WDIGalleryBox',
            image_rows: JSON.stringify(current_image_row),
            feed_id: currentFeed.feed_row['id'],
            feed_counter: currentFeed.feed_row['wdi_feed_counter'],
            current_image_index: current_image_index,
            image_rows_count: currentFeed.parsedData.length,
            carousel_media_row: JSON.stringify(current_image_row[0].carousel_media)
        },
        success: function (response) {
            var popup = jQuery(
              '<div id="wdi_spider_popup_wrap" class="wdi_spider_popup_wrap wdi_lightbox_theme_' + currentFeed.feed_row.theme_id + '" style="' +
              'width:' + width + 'px;' +
              'height:' + height + 'px;' +
              'margin-top:-' + (height / 2) + 'px;' +
              'margin-left: -' + (width / 2) + 'px; ">' + response +
              '</div>');

            var constructor = new wdi_construct_popup(popup, currentFeed, currentFeed.parsedData, image_id);
            constructor.construct();

            popup.hide().appendTo("body");
            wdi_spider_showpopup(description, lifetime, popup, duration);
            jQuery("#wdi_spider_popup_loading_" + current_view).css({
                display: "none !important;"
            });
        }
    });
}

var wdi_construct_popup = function (popup, currentFeed, image_rows, current_image_id) {

    this.theme_row = {};
    this.construct = function () {
        this.theme_row = window['wdi_theme_' + currentFeed.feed_row.theme_id];
        if (currentFeed.feed_row.popup_enable_filmstrip && currentFeed.feed_row.popup_enable_filmstrip === "1") {
            this.add_filmstrip();
        }
        this.set_wdi_data();
    };

    this.add_filmstrip = function () {

        var filmstrip_direction = 'horizontal';
        if (this.theme_row.lightbox_filmstrip_pos === 'right' || this.theme_row.lightbox_filmstrip_pos === 'left') {
            filmstrip_direction = 'vertical';
        }

        var fa_class_name_1 = (filmstrip_direction === "horizontal") ? 'tenweb-i-angle-left' : 'tenweb-i-angle-up';
        var fa_class_name_2 = (filmstrip_direction === "horizontal") ? 'tenweb-i-angle-right' : 'tenweb-i-angle-down';

        // var thumb_width = 90;
        // var thumb_height = 90;

        var thumbnails_html = "";

        var image_filmstrip_height,image_filmstrip_width;
        if (filmstrip_direction === 'horizontal') {
            image_filmstrip_width = image_filmstrip_height = (typeof currentFeed.feed_row['popup_filmstrip_height'] !== "undefined") ? (currentFeed.feed_row['popup_filmstrip_height']) : '20';
        }
        else {
            image_filmstrip_width = image_filmstrip_height = (typeof currentFeed.feed_row['popup_filmstrip_height'] !== "undefined") ? (currentFeed.feed_row['popup_filmstrip_height']) : '50';
        }

        image_filmstrip_height = image_filmstrip_width = parseInt(image_filmstrip_width);

        for (var i = 0; i < image_rows.length; i++) {
            var image_row = image_rows[i];

            var image_thumb_width,image_thumb_height,scale;
            if (image_row.resolution && image_row.resolution !== '') {
                var resolution_arr = image_row.resolution.split(" ");
                var resolution_w = intval($resolution_arr[0]);
                var resolution_h = intval($resolution_arr[2]);
                if (resolution_w !== 0 && resolution_h !== 0) {
                    scale = Math.max(image_filmstrip_width / resolution_w, image_filmstrip_height / resolution_h);
                    image_thumb_width = resolution_w * scale;
                    image_thumb_height = resolution_h * scale;
                }
                else {
                    image_thumb_width = image_filmstrip_width;
                    image_thumb_height = image_filmstrip_height;
                }
            }
            else {
                image_thumb_width = image_filmstrip_width;
                image_thumb_height = image_filmstrip_height;
            }

            scale = Math.max(image_filmstrip_width / image_thumb_width, image_filmstrip_height / image_thumb_height);
            image_thumb_width *= scale;
            image_thumb_height *= scale;
            var thumb_left = (image_filmstrip_width - image_thumb_width) / 2;
            var thumb_top = (image_filmstrip_height - image_thumb_height) / 2;

            var class_name = "wdi_filmstrip_thumbnail " + ((parseInt(image_row.id) === parseInt(current_image_id)) ? 'wdi_thumb_active' : 'wdi_thumb_deactive');

            var img_style = 'width:' + image_thumb_width + 'px;' +
              'height:'+image_thumb_height+'px;' +
              'margin-left:'+thumb_left+'px;' +
              'margin-top:'+thumb_top+'px;';

            var onclick = 'wdi_change_image(parseInt(jQuery(\'#wdi_current_image_key\').val()), \'' + i + '\', wdi_data)';
            var ontouchend = 'wdi_change_image(parseInt(jQuery(\'#wdi_current_image_key\').val()), \'' + i + '\', wdi_data)';

            switch (image_row.filetype) {
                case 'EMBED_OEMBED_INSTAGRAM_IMAGE':
                    var src = (typeof image_row.images[currentFeed.feedImageResolution] !== 'undefined' && typeof image_row.images[currentFeed.feedImageResolution]['url'] !== "undefined") ? image_row.images[currentFeed.feedImageResolution]['url'] : image_row.thumb_url;
                    var img_html = '<img style="' + img_style + '" class="wdi_filmstrip_thumbnail_img" src="' + src + '" onclick="' + onclick + '" ontouchend="' + ontouchend + '" image_id="' + image_row.id + '" image_key="' + i + '" alt="' + image_row.alt + '" />';
                    break;
                case 'EMBED_OEMBED_INSTAGRAM_VIDEO':
                    var src = (typeof image_row.thumb_url !== 'undefined') ? image_row.thumb_url : 'thumburl';
                    var img_html = '<img style="' + img_style + '" class="wdi_filmstrip_thumbnail_img" src="' + src + '" onclick="' + onclick + '" ontouchend="' + ontouchend + '" image_id="' + image_row.id + '" image_key="' + i + '" alt="' + image_row.alt + '" />';
                    break;
                case 'EMBED_OEMBED_INSTAGRAM_CAROUSEL':
                    if( image_row.carousel_media.length === 0 ){
                        continue;
                    }
                    switch (image_row.carousel_media[0].type) {
                        case 'image':
                            src = image_row.carousel_media[0].images.thumbnail.url;
                            var img_html = '<img style="' + img_style + '" class="wdi_filmstrip_thumbnail_img" src="' + src + '" onclick="' + onclick + '" ontouchend="' + ontouchend + '" image_id="' + image_row.id + '" image_key="' + i + '" alt="' + image_row.alt + '" />';
                            break;
                        case 'video':
                            var src = (typeof image_row.thumb_url !== 'undefined') ? image_row.thumb_url : 'thumburl';
                            var img_html = '<img style="' + img_style + '" class="wdi_filmstrip_thumbnail_img" src="' + src + '" onclick="' + onclick + '" ontouchend="' + ontouchend + '" image_id="' + image_row.id + '" image_key="' + i + '" alt="' + image_row.alt + '" />';
                            break;
                        default:
                            src = wdi_url.plugin_url + "images/missing.png";
                            var img_html = '<img style="' + img_style + '" class="wdi_filmstrip_thumbnail_img" src="' + src + '" onclick="' + onclick + '" ontouchend="' + ontouchend + '" image_id="' + image_row.id + '" image_key="' + i + '" alt="' + image_row.alt + '" />';
                            break;
                    }
            }
            thumbnails_html += '<div id="wdi_filmstrip_thumbnail_' + i + '" class="' + class_name + '">' + img_html + '</div>';


        }

        var html = '' +
          '<div class="wdi_filmstrip_left"><i class="tenweb-i ' + fa_class_name_1 + '"></i></div>' +
          '<div class="wdi_filmstrip">' +
          '<div class="wdi_filmstrip_thumbnails">' +
          thumbnails_html +
          '</div>' +
          '</div>' +
          '<div class="wdi_filmstrip_right"><i class="tenweb-i ' + fa_class_name_2 + '"></i></div>';

        popup.find('.wdi_filmstrip_container').append(html);
    };

    this.set_wdi_data = function () {

        wdi_data = [];

        for (var i = 0; i < image_rows.length; i++) {
            wdi_data[i] = [];
            wdi_data[i]["number"] = i + 1;
            wdi_data[i]["id"] = image_rows[i].id;
            wdi_data[i]["alt"] = image_rows[i].alt;
            wdi_data[i]["description"] = wdi_front.escape_tags(image_rows[i].description);
            wdi_data[i]["username"] = image_rows[i].username;
            wdi_data[i]["profile_picture"] = image_rows[i].profile_picture;
            wdi_data[i]["image_url"] = image_rows[i].image_url;
            wdi_data[i]["thumb_url"] = image_rows[i].thumb_url;
            wdi_data[i]["src"] = typeof image_rows[i].images !== 'undefined' ? image_rows[i].images["standard_resolution"]["url"] : "";
            wdi_data[i]["date"] = image_rows[i].date;
            wdi_data[i]["comment_count"] = image_rows[i].comment_count;
            wdi_data[i]["filetype"] = image_rows[i].filetype;
            wdi_data[i]["filename"] = image_rows[i].filename;
            wdi_data[i]["avg_rating"] = image_rows[i].avg_rating;
            wdi_data[i]["rate"] = image_rows[i].rate;
            wdi_data[i]["rate_count"] = image_rows[i].rate_count;
            wdi_data[i]["hit_count"] = image_rows[i].hit_count;
            wdi_data[i]["comments_data"] = (typeof image_rows[i].comments_data !== "undefined") ? image_rows[i].comments_data : 'null';
            wdi_data[i]["carousel_media"] = (typeof image_rows[i]['carousel_media'] !== "undefined") ? image_rows[i]['carousel_media'] : null;
        }

    };

}

function wdi_spider_showpopup(description, lifetime, popup, duration)
{
    isPopUpOpened = true;
    popup.show();

    wdi_spider_receivedpopup(description, lifetime);
}

function wdi_spider_hasalreadyreceivedpopup(description)
{
    if (document.cookie.indexOf(description) > -1) {
        delete document.cookie[document.cookie.indexOf(description)];
    }
    return false;
}

function wdi_spider_receivedpopup(description, lifetime)
{
    var date = new Date();
    date.setDate(date.getDate() + lifetime);
    document.cookie = description + "=true;expires=" + date.toUTCString() + ";path=/";
    jQuery(".wdi_image_info").mCustomScrollbar({
        autoHideScrollbar: false,
        scrollInertia: 150,
        advanced: {
            updateOnContentResize: true
        }
    });
}

function wdi_spider_isunsupporteduseragent()
{
    return (!window.XMLHttpRequest);
}

function wdi_spider_destroypopup(duration)
{


    if (document.getElementById("wdi_spider_popup_wrap") != null) {
        wdi_comments_manager.popup_destroyed();

        if (typeof jQuery().fullscreen !== 'undefined' && jQuery.isFunction(jQuery().fullscreen)) {
            if (jQuery.fullscreen.isFullScreen()) {
                jQuery.fullscreen.exit();
            }
        }
        setTimeout(function ()
        {
            jQuery(".wdi_spider_popup_wrap").remove();
            jQuery(".wdi_spider_popup_loading").css({
                display: "none"
            });
            jQuery(".wdi_spider_popup_overlay").css({
                display: "none"
            });
            jQuery(document).off("keydown");
            jQuery("html").attr("style", "");
        }, 20);
    }
    isPopUpOpened = false;
    var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
    var viewportmeta = document.querySelector('meta[name="viewport"]');
    if (isMobile && viewportmeta) {
        viewportmeta.content = 'width=device-width, initial-scale=1';
    }
    var scrrr = jQuery(document).scrollTop();
    window.location.hash = "";
    jQuery(document).scrollTop(scrrr);
    if(typeof wdi_playInterval != 'undefined'){
        clearInterval(wdi_playInterval);
    }


}


Object.size = function (obj)
{
    var size = 0,
      key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function wdi_spider_ajax_save(form_id, image_id)
{
    wdi_comments_manager.init(image_id);
    return false;
}

wdi_comments_manager = {
    media_id: '',
    mediaComments: [],
    /*all comments*/
    load_more_count: 10,
    commentCounter: 0,
    /* current comments counter*/
    currentKey: -1,
    /*iamge id*/
    init: function (image_id)
    {
        /*initializing instagram object which will handle all instagram api requests*/
        this.instagram = new WDIInstagram();
        this.instagram.addToken(wdi_front.access_token);


        if (this.currentKey != image_id) {
            this.currentKey = image_id;

            this.reset_comments();
        } else {
            /*open close*/
            /*do nothing*/
        }
    },
    reset_comments: function ()
    {
        jQuery('#wdi_load_more_comments').remove();
        jQuery('#wdi_added_comments').html('');
        //currentImage = wdi_data[this.currentKey];
        this.commentCounter = 0;
        this.media_id = wdi_data[this.currentKey]['id'];

        this.getAjaxComments(this.currentKey);


        //this.showComments(currentImage['comments_data']);


    },
    clear_comments: function ()
    {
        jQuery('#wdi_load_more_comments').remove();
        jQuery('#wdi_added_comments').html('');
        //currentImage = wdi_data[this.currentKey];
        this.commentCounter = 0;
    },
    popup_destroyed: function ()
    {
        this.media_id = '';
        this.mediaComments = [];
        /*all comments*/
        this.commentCounter = 0;
        /* current comments counter**/
        this.currentKey = -1;

    },

    //function for dispaying comments
    showComments: function (comments, count)
    {
        if (Object.size(comments) - this.commentCounter - count < 0 || count === undefined) {
            count = Object.size(comments) - this.commentCounter;
        }
        var counter = this.commentCounter;
        for (i = Object.size(comments) - counter - 1; i >= Object.size(comments) - counter - count; i--) {
            this.commentCounter++;
            var commentText = (comments[i]['text']);
            commentText = wdi_front.escape_tags(commentText);
            commentText = this.filterCommentText(commentText);
            var username = (comments[i]['username']);
            //var profile_picture = (comments[i]['profile_picture']);
            var singleComment = jQuery('<div class="wdi_single_comment"></div>');
            singleComment.append(jQuery('<p class="wdi_comment_header_p"><span class="wdi_comment_header"><a target="_blank" href="//instagram.com/' + username + '">' + username + '</a></span><span class="wdi_comment_date">' + wdi_front.convertUnixDate(comments[i]['timestamp']) + '</span></p>'));
            singleComment.append(jQuery('<div class="wdi_comment_body_p"><span class="wdi_comment_body"><p>' + commentText + '</p></span></div>'));
            jQuery('#wdi_added_comments').prepend(singleComment);
        }
        if (jQuery(".wdi_single_comment").length == 0){
            jQuery("#wdi_added_comments").html('<p class="wdi_no_comment">There are no comments to show</p>');
        }
        this.updateScrollbar();
    },

    //function for updating scrollbar
    updateScrollbar: function ()
    {
        var wdi_comments = jQuery('#wdi_comments');
        var wdi_added_comments = jQuery('#wdi_added_comments');
        //jQuery('#wdi_load_more_comments').remove();
        jQuery('.wdi_comments').attr('class', 'wdi_comments');
        jQuery('.wdi_comments').html('');

        /*restore load more button*/

        // if(jQuery('#wdi_load_more_comments').length===0){
        //    wdi_added_comments.prepend(jQuery('<p id="wdi_load_more_comments" class="wdi_load_more_comments">Load more</p>'));
        //   jQuery('#wdi_load_more_comments').on('click',function(){
        //     wdi_comments_manager.showComments(wdi_comments_manager.mediaComments, wdi_comments_manager.load_more_count);
        //   });
        // }

        jQuery('.wdi_comments').append(wdi_comments);
        jQuery('.wdi_comments').append(wdi_added_comments);

        if (typeof jQuery().mCustomScrollbar !== 'undefined') {
            if (jQuery.isFunction(jQuery().mCustomScrollbar)) {
                jQuery(".wdi_comments").mCustomScrollbar({
                    scrollInertia: 250
                });
            }
        }

        ////
        jQuery('.wdi_comments_close_btn').on('click', wdi_comment);
        //binding click event for loading more commetn by ajax


    },
    //get recent media comments
    getAjaxComments: function () {
        /* Next page url for comments */
        var next = '';
        if (typeof wdi_data[wdi_comments_manager.currentKey]['comments_data']['next'] !== 'undefined') {
            next =wdi_data[wdi_comments_manager.currentKey]['comments_data']['next'];
        }
        this.instagram.getRecentMediaComments(this.media_id, {
            success: function (response)
            {
                if (response == '' || response == undefined || response == null || typeof response['error'] !== 'undefined') {
                    errorMessage = 'Network Error, please try again later :(';
                    console.log('%c' + errorMessage, "color:#cc0000;");
                    jQuery("#wdi_added_comments").html('<p class="wdi_no_comment">Comments are currently unavailable</p>');

                    return;
                }
                if (response['meta']['code'] != 200) {
                    errorMessage = response['meta']['error_message'];
                    console.log('%c' + errorMessage, "color:#cc0000;");
                    jQuery("#wdi_added_comments").html('<p class="wdi_no_comment">Comments are currently unavailable</p>');
                    return;
                }

                if ( typeof wdi_data[wdi_comments_manager.currentKey]['comments_data']['data'] != 'undefined') {
                    wdi_data[wdi_comments_manager.currentKey]['comments_data']['data'] = response['data'].concat(wdi_data[wdi_comments_manager.currentKey]['comments_data']['data']);
                    wdi_data[wdi_comments_manager.currentKey]['comment_count'] += wdi_data[wdi_comments_manager.currentKey]['comments_data']['data'].length;
                } else {
                    wdi_data[wdi_comments_manager.currentKey]['comments_data'] = response;
                    wdi_data[wdi_comments_manager.currentKey]['comment_count'] = wdi_data[wdi_comments_manager.currentKey]['comments_data']['data'].length;
                }
                wdi_data[wdi_comments_manager.currentKey]['comments_data']['next'] = ( typeof response['paging'] !== 'undefined') ? response['paging']['next'] : '';

                //wdi_data[wdi_comments_manager.currentKey]['comments_data']['next'] = ( typeof response['paging'] !== 'undefined') ? response['paging']['next'] : '';
                wdi_comments_manager.mediaComments = response['data'];

                //ttt
                var currentImage = wdi_data[wdi_comments_manager.currentKey];
                //currentImage['comments_data'] = response['data'];

                wdi_comments_manager.showComments(currentImage['comments_data']['data'], wdi_comments_manager.load_more_count);
                wdi_comments_manager.ajax_comments_ready(response['data']);
            }
        },next);
    },
    ajax_comments_ready: function (response)
    {
        this.createLoadMoreAndBindEvent(wdi_comments_manager.currentKey);
    },
    createLoadMoreAndBindEvent: function (cur_image_key)
    {
        if(cur_image_key == '') {
            cur_image_key = wdi_comments_manager.currentKey;
        }
        if(wdi_data[cur_image_key]['comments_data']['next'] !== '' || (wdi_data[cur_image_key]['comments_data']['data'].length > wdi_comments_manager.load_more_count && jQuery(".wdi_single_comment").length < wdi_data[cur_image_key]['comments_data']['data'].length) && jQuery("#wdi_load_more_comments").length == 0) {
            jQuery('#wdi_added_comments').prepend(jQuery('<p id="wdi_load_more_comments" class="wdi_load_more_comments">load more comments</p>'));
        }
        jQuery('.wdi_comment_container #wdi_load_more_comments').on('click', function ()
        {
            if( (wdi_comments_manager.commentCounter + wdi_comments_manager.load_more_count) > wdi_data[cur_image_key]['comments_data']['data'].length && wdi_data[cur_image_key]['comments_data']['next'] != '' ) {
                wdi_comments_manager.getAjaxComments(this.currentKey);
            }
            jQuery(this).remove();
            wdi_comments_manager.showComments(wdi_data[cur_image_key]['comments_data']['data'], wdi_comments_manager.load_more_count);
            wdi_comments_manager.createLoadMoreAndBindEvent(wdi_comments_manager.currentKey);
        });
    },
    /*
     * Filtesrs comment text and makes it instagram like comments
     */
    filterCommentText: function (comment)
    {
        var commentArray = comment.split(' ');
        var commStr = '';
        for (var i = 0; i < commentArray.length; i++) {
            switch (commentArray[i][0]) {
                case '@':
                {
                    commStr += '<a target="blank" class="wdi_comm_text_link" href="//instagram.com/' + commentArray[i].substring(1, commentArray[i].length) + '">' + commentArray[i] + '</a> ';
                    break;
                }
                case '#':
                {
                    commStr += '<a target="blank" class="wdi_comm_text_link" href="//instagram.com/explore/tags/' + commentArray[i].substring(1, commentArray[i].length) + '">' + commentArray[i] + '</a> ';
                    break;
                }
                default:
                {
                    commStr += commentArray[i] + ' ';
                }
            }
        }
        commStr = commStr.substring(0, commStr.length - 1);
        return commStr;
    }


}


// Submit rating.
// function wdi_spider_rate_ajax_save(form_id) {
//   var post_data = {};
//   post_wdi_data["image_id"] = jQuery("#" + form_id + " input[name='image_id']").val();
//   post_wdi_data["rate"] = jQuery("#" + form_id + " input[name='score']").val();
//   post_wdi_data["ajax_task"] = jQuery("#rate_ajax_task").val();
//   jQuery.post(
//     jQuery('#' + form_id).attr('action'),
//     post_data,

//     function (data) {
//       var str = jQuery(data).find('#' + form_id).html();
//       jQuery('#' + form_id).html(str);
//     }
//   ).success(function(jqXHR, textStatus, errorThrown) {
//   });
//   // if (event.preventDefault) {
//     // event.preventDefault();
//   // }
//   // else {
//     // event.returnValue = false;
//   // }
//   return false;
// }

// Set value by ID.
function wdi_spider_set_input_value(input_id, input_value)
{
    if (document.getElementById(input_id)) {
        document.getElementById(input_id).value = input_value;
    }
}

// Submit form by ID.
function wdi_spider_form_submit(event, form_id)
{
    if (document.getElementById(form_id)) {
        document.getElementById(form_id).submit();
    }
    if (event.preventDefault) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    }
}

// Check if required field is empty.
function wdi_spider_check_required(id, name)
{
    if (jQuery('#' + id).val() == '') {
        wdi_front.show_alert(name + '* ' + wdi_objectL10n.wdi_field_required);
        jQuery('#' + id).attr('style', 'border-color: #FF0000;');
        jQuery('#' + id).focus();
        return true;
    } else {
        return false;
    }
}

// Check Email.
function wdi_spider_check_email(id)
{
    if (jQuery('#' + id).val() != '') {
        var email = jQuery('#' + id).val().replace(/^\s+|\s+$/g, '');
        if (email.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
            wdi_front.show_alert(wdi_objectL10n.wdi_mail_validation);
            return true;
        }
        return false;
    }
}

// Refresh captcha.
function wdi_captcha_refresh(id)
{
    if (document.getElementById(id + "_img") && document.getElementById(id + "_input")) {
        srcArr = document.getElementById(id + "_img").src.split("&r=");
        document.getElementById(id + "_img").src = srcArr[0] + '&r=' + Math.floor(Math.random() * 100);
        document.getElementById(id + "_img").style.display = "inline-block";
        document.getElementById(id + "_input").value = "";
    }
}

function wdi_play_pause($this)
{
    var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
    var is_safari = navigator.userAgent.indexOf("Safari") > -1;
    if ((is_chrome)&&(is_safari)) {is_safari=false;}
    if(is_safari){
        return;
    }
    var video = $this.get(0);
    var regex = /firefox/i;
    var firefox = false;
    if (navigator.userAgent.match(regex)) {
        firefox = true;
    }
    if (!firefox) {

        if (!video.paused) {
            video.pause();
        } else {
            video.play();
        }

    }


}


/*server side analogue is function display_embed in WDWLibraryEmbed*/
/*params
 embed_type: string , one of predefined accepted types
 embed_id: string, id of media in corresponding host, or url if no unique id system is defined for host
 attrs: object with html attributes and values format e.g. {width:'100px', style:"display:inline;"}
 */
function wdi_spider_display_embed( embed_type, embed_id, src, attrs, carousel_media ) {
    var html_to_insert = '';
    switch ( embed_type ) {
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
            if ( embed_id != '' ) {
                /*oembed_instagram_html += '<iframe src="'+embed_id+'"'+
                 ' style="'+
                 'max-width:'+'100%'+" !important"+
                 '; max-height:'+'100%'+" !important"+
                 '; width:'+'auto'+
                 '; height:'+ '100%' + " "+
                 '; margin:0;"'+
                 'frameborder="0" scrolling="no" allowtransparency="false"></iframe>';
                 */
                oembed_instagram_html += '<video onclick=\"wdi_play_pause(jQuery(this));\" style="width:auto !important; height:auto !important; max-width:100% !important; max-height:100% !important; margin:0 !important;" controls>' +
                  '<source src="' + src +
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
            if ( embed_id != '' ) {
                oembed_instagram_html += '<img src="' + src + '"' +
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
        case 'EMBED_OEMBED_INSTAGRAM_CAROUSEL':
            var oembed_instagram_html = '<div ';
            for ( attr in attrs ) {
                if ( !(/src/i).test(attr) ) {
                    if ( attr != '' && attrs[attr] != '' ) {
                        oembed_instagram_html += ' ' + attr + '="' + attrs[attr] + '"';
                    }
                }
            }
            oembed_instagram_html += " >";
            for ( var i = 0; i < carousel_media.length; i++ ) {
                if ( carousel_media[i]["type"] == "image" ) {
                    oembed_instagram_html += '<img src="' + carousel_media[i]["images"]["standard_resolution"]["url"] + '"' +
                      ' style="' +
                      'max-width:' + '100%' + " !important" +
                      '; max-height:' + '100%' + " !important" +
                      '; width:' + 'auto !important' +
                      '; height:' + 'auto !important' +
                      ';" data-id="' + i + '" class="carousel_media ' + (i == 0 ? "active" : "") + '">';
                }
                else if ( carousel_media[i]["type"] == "video" ) {
                    if(typeof carousel_media[i]["videos"] !== "undefined" && typeof carousel_media[i]["videos"]["standard_resolution"] !== "undefined" && typeof carousel_media[i]["videos"]["standard_resolution"]["url"] !== "undefined"){
                        src = carousel_media[i]["videos"]["standard_resolution"]["url"];
                    }
                    oembed_instagram_html += '<video onclick="wdi_play_pause(jQuery(this));" style="width:auto !important; height:auto !important; max-width:100% !important; max-height:100% !important; margin:0 !important;" controls data-id="' + i + '" class="carousel_media ' + (i == 0 ? "active" : "") + '">' +
                      '<source src="' + src +
                      '" type="video/mp4"> Your browser does not support the video tag. </video>';
                }
            }
            oembed_instagram_html += "</div>";
            html_to_insert += oembed_instagram_html;
            break;
        default:
            break;
    }
    return html_to_insert;
}
/**
 * @param from_popup: optional, true if from bulk embed popup, false(default) if from instagram gallery
 * @return "ok" if adds instagram gallery, false if any error when adding instagram gallery
 */
/*------------------------------*/

function wdi_testBrowser_cssTransitions() {
    return wdi_testDom('Transition');
}
function wdi_testBrowser_cssTransforms3d() {
    return wdi_testDom('Perspective');
}
function wdi_testDom(prop) {
    /* Browser vendor CSS prefixes.*/
    var browserVendors = ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-'];
    /* Browser vendor DOM prefixes.*/
    var domPrefixes = ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml'];
    var i = domPrefixes.length;
    while (i--) {
        if (typeof document.body.style[domPrefixes[i] + prop] !== 'undefined') {
            return true;
        }
    }
    return false;
}

function wdi_cube(tz, ntx, nty, nrx, nry, wrx, wry, current_image_class, next_image_class, direction) {
    /* If browser does not support 3d transforms/CSS transitions.*/
    if (!wdi_testBrowser_cssTransitions()) {
        return wdi_fallback(current_image_class, next_image_class, direction);
    }
    if (!wdi_testBrowser_cssTransforms3d()) {
        return wdi_fallback3d(current_image_class, next_image_class, direction);
    }
    wdi_trans_in_progress = true;
    /* Set active thumbnail.*/
    jQuery(".wdi_filmstrip_thumbnail").removeClass("wdi_thumb_active").addClass("wdi_thumb_deactive");
    jQuery("#wdi_filmstrip_thumbnail_" + wdi_current_key).removeClass("wdi_thumb_deactive").addClass("wdi_thumb_active");
    jQuery(".wdi_slide_bg").css('perspective', 1000);
    jQuery(current_image_class).css({
        transform : 'translateZ(' + tz + 'px)',
        backfaceVisibility : 'hidden'
    });
    jQuery(next_image_class).css({
        opacity : 1,
        filter: 'Alpha(opacity=100)',
        backfaceVisibility : 'hidden',
        transform : 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY('+ nry +'deg) rotateX('+ nrx +'deg)'
    });
    jQuery(".wdi_slider").css({
        transform: 'translateZ(-' + tz + 'px)',
        transformStyle: 'preserve-3d'
    });
    /* Execution steps.*/
    setTimeout(function () {
        jQuery(".wdi_slider").css({
            transition: 'all ' + wdi_transition_duration + 'ms ease-in-out',
            transform: 'translateZ(-' + tz + 'px) rotateX('+ wrx +'deg) rotateY('+ wry +'deg)'
        });
    }, 20);
    /* After transition.*/
    jQuery(".wdi_slider").one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(wdi_after_trans));
    function wdi_after_trans() {
        jQuery(current_image_class).removeAttr('style');
        jQuery(next_image_class).removeAttr('style');
        jQuery(".wdi_slider").removeAttr('style');
        jQuery(current_image_class).css({'opacity' : 0, filter: 'Alpha(opacity=0)', 'z-index': 1});
        jQuery(next_image_class).css({'opacity' : 1, filter: 'Alpha(opacity=100)', 'z-index' : 2});

        wdi_trans_in_progress = false;
        jQuery(current_image_class).html('');
        if (typeof event_stack !== 'undefined') {
            if (event_stack.length > 0) {
                key = event_stack[0].split("-");
                event_stack.shift();
                wdi_change_image(key[0], key[1], wdi_data, true);
            }
        }
        wdi_change_watermark_container();
    }
}

function wdi_cubeH(current_image_class, next_image_class, direction) {
    /* Set to half of image width.*/
    var dimension = jQuery(current_image_class).width() / 2;
    if (direction == 'right') {
        wdi_cube(dimension, dimension, 0, 0, 90, 0, -90, current_image_class, next_image_class, direction);
    }
    else if (direction == 'left') {
        wdi_cube(dimension, -dimension, 0, 0, -90, 0, 90, current_image_class, next_image_class, direction);
    }
}
function wdi_cubeV(current_image_class, next_image_class, direction) {
    /* Set to half of image height.*/
    var dimension = jQuery(current_image_class).height() / 2;
    /* If next slide.*/
    if (direction == 'right') {
        wdi_cube(dimension, 0, -dimension, 90, 0, -90, 0, current_image_class, next_image_class, direction);
    }
    else if (direction == 'left') {
        wdi_cube(dimension, 0, dimension, -90, 0, 90, 0, current_image_class, next_image_class, direction);
    }
}

/* For browsers that does not support transitions.*/
function wdi_fallback(current_image_class, next_image_class, direction) {
    wdi_fade(current_image_class, next_image_class, direction);
}
/* For browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms).*/
function wdi_fallback3d(current_image_class, next_image_class, direction) {
    wdi_sliceV(current_image_class, next_image_class, direction);
}
function wdi_none(current_image_class, next_image_class, direction) {
    jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
    jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
    /* Set active thumbnail.*/
    jQuery(".wdi_filmstrip_thumbnail").removeClass("wdi_thumb_active").addClass("wdi_thumb_deactive");
    jQuery("#wdi_filmstrip_thumbnail_" + wdi_current_key).removeClass("wdi_thumb_deactive").addClass("wdi_thumb_active");
    wdi_trans_in_progress = false;
    jQuery(current_image_class).html('');
    wdi_change_watermark_container();
}

function wdi_fade(current_image_class, next_image_class, direction) {
    /* Set active thumbnail.*/
    jQuery(".wdi_filmstrip_thumbnail").removeClass("wdi_thumb_active").addClass("wdi_thumb_deactive");
    jQuery("#wdi_filmstrip_thumbnail_" + wdi_current_key).removeClass("wdi_thumb_deactive").addClass("wdi_thumb_active");
    if (wdi_testBrowser_cssTransitions()) {
        jQuery(next_image_class).css('transition', 'opacity ' + wdi_transition_duration + 'ms linear');
        jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
        jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
        wdi_change_watermark_container();
    }
    else {
        jQuery(current_image_class).animate({'opacity' : 0, 'z-index' : 1}, wdi_transition_duration);
        jQuery(next_image_class).animate({
            'opacity' : 1,
            'z-index': 2
        }, {
            duration: wdi_transition_duration,
            complete: function () {

                wdi_trans_in_progress = false;
                jQuery(current_image_class).html('');
                wdi_change_watermark_container(); }
        });
        /* For IE.*/
        jQuery(current_image_class).fadeTo(wdi_transition_duration, 0);
        jQuery(next_image_class).fadeTo(wdi_transition_duration, 1);
    }
}

function wdi_grid(cols, rows, ro, tx, ty, sc, op, current_image_class, next_image_class, direction) {
    /* If browser does not support CSS transitions.*/
    if (!wdi_testBrowser_cssTransitions()) {
        return wdi_fallback(current_image_class, next_image_class, direction);
    }
    wdi_trans_in_progress = true;
    /* Set active thumbnail.*/
    jQuery(".wdi_filmstrip_thumbnail").removeClass("wdi_thumb_active").addClass("wdi_thumb_deactive");
    jQuery("#wdi_filmstrip_thumbnail_" + wdi_current_key).removeClass("wdi_thumb_deactive").addClass("wdi_thumb_active");
    /* The time (in ms) added to/subtracted from the delay total for each new gridlet.*/
    var count = (wdi_transition_duration) / (cols + rows);
    /* Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)*/
    function wdi_gridlet(width, height, top, img_top, left, img_left, src, imgWidth, imgHeight, c, r) {
        var delay = (c + r) * count;
        /* Return a gridlet elem with styles for specific transition.*/
        return jQuery('<span class="wdi_gridlet" />').css({
            display : "block",
            width : width,
            height : height,
            top : top,
            left : left,
            backgroundImage : 'url("' + src + '")',
            backgroundColor: jQuery(".wdi_spider_popup_wrap").css("background-color"),
            /*backgroundColor: 'rgba(0, 0, 0, 0)',*/
            backgroundRepeat: 'no-repeat',
            backgroundPosition : img_left + 'px ' + img_top + 'px',
            backgroundSize : imgWidth + 'px ' + imgHeight + 'px',
            transition : 'all ' + wdi_transition_duration + 'ms ease-in-out ' + delay + 'ms',
            transform : 'none'
        });
    }
    /* Get the current slide's image.*/
    var cur_img = jQuery(current_image_class).find('img');
    /* Create a grid to hold the gridlets.*/
    var grid = jQuery('<span style="display: block;" />').addClass('wdi_grid');
    /* Prepend the grid to the next slide (i.e. so it's above the slide image).*/
    jQuery(current_image_class).prepend(grid);
    /* Vars to calculate positioning/size of gridlets.*/
    var cont = jQuery(".wdi_slide_bg");
    var imgWidth = cur_img.width();
    var imgHeight = cur_img.height();
    var contWidth = cont.width(),
      contHeight = cont.height(),
      colWidth = Math.floor(contWidth / cols),
      rowHeight = Math.floor(contHeight / rows),
      colRemainder = contWidth - (cols * colWidth),
      colAdd = Math.ceil(colRemainder / cols),
      rowRemainder = contHeight - (rows * rowHeight),
      rowAdd = Math.ceil(rowRemainder / rows),
      leftDist = 0,
      img_leftDist = Math.ceil((jQuery(".wdi_slide_bg").width() - cur_img.width()) / 2);
    var imgSrc = typeof cur_img.attr('src')=='undefined' ? '' :cur_img.attr('src');
    /* tx/ty args can be passed as 'auto'/'min-auto' (meaning use slide width/height or negative slide width/height).*/
    tx = tx === 'auto' ? contWidth : tx;
    tx = tx === 'min-auto' ? - contWidth : tx;
    ty = ty === 'auto' ? contHeight : ty;
    ty = ty === 'min-auto' ? - contHeight : ty;
    /* Loop through cols.*/
    for (var i = 0; i < cols; i++) {
        var topDist = 0,
          img_topDst = Math.floor((jQuery(".wdi_slide_bg").height() - cur_img.height()) / 2),
          newColWidth = colWidth;
        /* If imgWidth (px) does not divide cleanly into the specified number of cols, adjust individual col widths to create correct total.*/
        if (colRemainder > 0) {
            var add = colRemainder >= colAdd ? colAdd : colRemainder;
            newColWidth += add;
            colRemainder -= add;
        }
        /* Nested loop to create row gridlets for each col.*/
        for (var j = 0; j < rows; j++)  {
            var newRowHeight = rowHeight,
              newRowRemainder = rowRemainder;
            /* If contHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total.*/
            if (newRowRemainder > 0) {
                add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
                newRowHeight += add;
                newRowRemainder -= add;
            }
            /* Create & append gridlet to grid.*/
            grid.append(wdi_gridlet(newColWidth, newRowHeight, topDist, img_topDst, leftDist, img_leftDist, imgSrc, imgWidth, imgHeight, i, j));
            topDist += newRowHeight;
            img_topDst -= newRowHeight;
        }
        img_leftDist -= newColWidth;
        leftDist += newColWidth;
    }
    /* Set event listener on last gridlet to finish transitioning.*/
    var last_gridlet = grid.children().last();
    /* Show grid & hide the image it replaces.*/
    grid.show();
    cur_img.css('opacity', 0);
    /* Add identifying classes to corner gridlets (useful if applying border radius).*/
    grid.children().first().addClass('rs-top-left');
    grid.children().last().addClass('rs-bottom-right');
    grid.children().eq(rows - 1).addClass('rs-bottom-left');
    grid.children().eq(- rows).addClass('rs-top-right');
    /* Execution steps.*/
    setTimeout(function () {
        grid.children().css({
            opacity: op,
            transform: 'rotate('+ ro +'deg) translateX('+ tx +'px) translateY('+ ty +'px) scale('+ sc +')'
        });
    }, 1);
    jQuery(next_image_class).css('opacity', 1);
    /* After transition.*/
    jQuery(last_gridlet).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(wdi_after_trans));
    function wdi_after_trans() {
        jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
        jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
        cur_img.css('opacity', 1);
        grid.remove();
        wdi_trans_in_progress = false;
        jQuery(current_image_class).html('');
        if (typeof event_stack !== 'undefined') {
            if (event_stack.length > 0) {
                key = event_stack[0].split("-");
                event_stack.shift();
                wdi_change_image(key[0], key[1], wdi_data, true);
            }
        }
        wdi_change_watermark_container();
    }
}
function wdi_sliceH(current_image_class, next_image_class, direction) {
    if (direction == 'right') {
        var translateX = 'min-auto';
    }
    else if (direction == 'left') {
        var translateX = 'auto';
    }
    wdi_grid(1, 8, 0, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
}
function wdi_sliceV(current_image_class, next_image_class, direction) {
    if (direction == 'right') {
        var translateY = 'min-auto';
    }
    else if (direction == 'left') {
        var translateY = 'auto';
    }
    wdi_grid(10, 1, 0, 0, translateY, 1, 0, current_image_class, next_image_class, direction);
}
function wdi_slideV(current_image_class, next_image_class, direction) {
    if (direction == 'right') {
        var translateY = 'auto';
    }
    else if (direction == 'left') {
        var translateY = 'min-auto';
    }
    wdi_grid(1, 1, 0, 0, translateY, 1, 1, current_image_class, next_image_class, direction);
}
function wdi_slideH(current_image_class, next_image_class, direction) {
    if (direction == 'right') {
        var translateX = 'min-auto';
    }
    else if (direction == 'left') {
        var translateX = 'auto';
    }
    wdi_grid(1, 1, 0, translateX, 0, 1, 1, current_image_class, next_image_class, direction);
}
function wdi_scaleOut(current_image_class, next_image_class, direction) {
    wdi_grid(1, 1, 0, 0, 0, 1.5, 0, current_image_class, next_image_class, direction);
}
function wdi_scaleIn(current_image_class, next_image_class, direction) {
    wdi_grid(1, 1, 0, 0, 0, 0.5, 0, current_image_class, next_image_class, direction);
}
function wdi_blockScale(current_image_class, next_image_class, direction) {
    wdi_grid(8, 6, 0, 0, 0, .6, 0, current_image_class, next_image_class, direction);
}
function wdi_kaleidoscope(current_image_class, next_image_class, direction) {
    wdi_grid(10, 8, 0, 0, 0, 1, 0, current_image_class, next_image_class, direction);
}
function wdi_fan(current_image_class, next_image_class, direction) {
    if (direction == 'right') {
        var rotate = 45;
        var translateX = 100;
    }
    else if (direction == 'left') {
        var rotate = -45;
        var translateX = -100;
    }
    wdi_grid(1, 10, rotate, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
}
function wdi_blindV(current_image_class, next_image_class, direction) {
    wdi_grid(1, 8, 0, 0, 0, .7, 0, current_image_class, next_image_class);
}
function wdi_blindH(current_image_class, next_image_class, direction) {
    wdi_grid(10, 1, 0, 0, 0, .7, 0, current_image_class, next_image_class);
}
function wdi_random(current_image_class, next_image_class, direction) {
    var anims = ['sliceH', 'sliceV', 'slideH', 'slideV', 'scaleOut', 'scaleIn', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV'];
    /* Pick a random transition from the anims array.*/
    this["wdi_" + anims[Math.floor(Math.random() * anims.length)]](current_image_class, next_image_class, direction);
}
function wdi_pause_stream(parent){
    jQuery(parent).find('video').each(function(){
        jQuery(this).get(0).pause();
    });
}
function wdi_reset_zoom() {
    var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
    var viewportmeta = document.querySelector('meta[name="viewport"]');
    if (isMobile) {
        if (viewportmeta) {
            viewportmeta.content = 'width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=0';
        }
    }
}