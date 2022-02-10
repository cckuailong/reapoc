if (typeof wdi_front == 'undefined') {
  wdi_front = {
    type: 'not_declared'
  };
}

wdi_front.detectEvent = function () {
  var e = 'click';
  var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
  if ( isMobile ) {
    e = 'touchend';
  }
  return e;
}

var wdi_error_show = false;
var wdi_error_init = false;

wdi_front.escape_tags = function (text) {
  var lt = /</g,
    gt = />/g,
    ap = /'/g,
    ic = /"/g;
  if(typeof text == 'undefined'){
    text = '';
  }
  text = text.toString().replace(lt, "&lt;").replace(gt, "&gt;").replace(ap, "&#39;").replace(ic, "&#34;");

  return text;
}

wdi_front.show_alert = function ( message, response, wdi_current_feed ) {
  wdi_current_feed = jQuery('#wdi_feed_' + wdi_current_feed.feed_row.wdi_feed_counter);
  if ( typeof wdi_current_feed != 'undefined' ) {
    wdi_error_show = true;
    wdi_current_feed.find('.wdi_spinner').remove();
    var wdi_js_error = wdi_current_feed.find('.wdi_js_error');
    var wdi_token_error = wdi_current_feed.find('.wdi_token_error');
    if ( response != false && ( (typeof response.meta !== 'undefined' && response.meta.error == true && response.meta.error_type === 'OAuthException' ) || (typeof response.error !== 'undefined' && response.error.type === 'OAuthException') ) ) {
      wdi_current_feed.find('.wdi_single_user').remove();
      wdi_token_error.removeClass('wdi_hidden');
      if ( wdi_front_messages.wdi_token_error_flag != '1' ) {
        jQuery.ajax({
          type: "POST",
          url: wdi_url.ajax_url,
          dataType: 'json',
          data: {
            action: 'wdi_token_flag',
            wdi_token_flag_nonce: wdi_front_messages.wdi_token_flag_nonce,
          },
          success: function ( data ) {
          }
        });
      }
    }
    else if ( typeof response.error !== 'undefined' && typeof response.error.message !== 'undefined') {
      wdi_js_error.html(response.error.message);
      wdi_current_feed.find('.wdi_single_user').remove();
      wdi_js_error.removeClass('wdi_js_error');
      wdi_js_error.addClass('wdi_js_error_no_animate');
      jQuery('.wdi_js_error_no_animate').show();
    }

    if ( wdi_front_messages.show_alerts ) {
      // alert(message);
    }
    else {
      console.log('%c' + message, "color:#cc0000;");
    }
  }
  wdi_error_show = true;
}

wdi_front.globalInit = function () {

  var num = wdi_front['feed_counter'];

  var init_feed_counter = 0;
  if (typeof wdi_ajax.ajax_response != "undefined") {
    var init_feed_counter = wdi_feed_counter_init.wdi_feed_counter_init;
  }

  for (var i = init_feed_counter; i <= num; i++) {

    if(jQuery('#wdi_feed_' + i).length === 0) { //conflict with Yoast SEO, Page Builder by SiteOrigin
      continue;
    }

    var currentFeed = new WDIFeed(window['wdi_feed_' + i]);

    /*initializing instagram object which will handle all instagram api requests*/
    currentFeed.instagram = new WDIInstagram();
    /**
     * this object will be passed to filtering function of currentFeed.instagram as second parameter
     * @type {Object}
     */
    currentFeed.instagram.filterArguments = {
      feed: currentFeed
    };

    currentFeed.instagram.addToken(currentFeed['feed_row']['access_token']);

    wdi_front.access_token = currentFeed['feed_row']['access_token'];

    currentFeed.dataLoaded = 0;
    currentFeed.dataStorageRaw = []; //stores all getted data from instagram api
    currentFeed.dataStorage = []; //stores all avialable data
    currentFeed.dataStorageList = []; //?
    currentFeed.allResponseLength = 0; //?
    //number of instagram objects which has been got by single request
    currentFeed.currentResponseLength = 0;
    //temprorary usersData which is uses in case when getted data is smaller then needed
    currentFeed.temproraryUsersData = [];
    currentFeed.removedUsers = 0;
    /*flag for indicating that not all images are loaded yet*/
    currentFeed.nowLoadingImages = true;
    currentFeed.imageIndex = 0; //index for image indexes
    currentFeed.resIndex = 0; //responsive indexes used for pagination
    currentFeed.currentPage = 1; //pagination page number
    currentFeed.currentPageLoadMore = 0; //pagination page number
    currentFeed.userSortFlags = []; //array for descripbing user based filter options
    currentFeed.customFilterChanged = false; //flag to notice filter change, onclick on username

    /**
     * This variable describes after how many requests program will stop searching for content
     * this number is very important and should not be set too high, because when feed has conditional filter
     * and filtered items are rare then the program will recursively request new photos and will filter them
     * if no image was fount it will go into infinite loop if feed images are "infinite" ( very huge number )
     * and if requests count in 1 hour exeed 5000 instagram will block access token for one hour
     *
     * @type {Number}
     */
    currentFeed.maxConditionalFiltersRequestCount = 10;

    /**
     * This variable shows us how many times program has been recursively called,
     * it changes it value within filtering function, and resets itself to 0 when feed is being displayed
     *
     * @type {Number}
     */
    currentFeed.instagramRequestCounter = 0;

    /**
     * flag: false initially, becomes true after first request, no matter if there is response or not
     * */
    currentFeed.mediaRequestsDone = false;

    /**
     * This array stores data from each request,
     * it is used to determine and remove duplicate photos caused by multiple hashtags
     * it is resetted to its inital [] value after displaying feed
     *
     * @type {Array}
     */
    currentFeed.conditionalFilterBuffer = [];
    currentFeed.stopInfiniteScrollFlag = false;

    if (currentFeed.feed_row.feed_type == 'masonry') {
      currentFeed.displayedData = [];
    }

    //if pagination is on then set pagination parameters
    if (currentFeed.feed_row.feed_display_view == 'pagination') {
      currentFeed.feed_row.resort_after_load_more = 0;
      if (currentFeed.feed_row.feed_type != 'image_browser') {
        currentFeed.feed_row.load_more_number = parseInt(currentFeed.feed_row.pagination_per_page_number);
        currentFeed.feed_row.number_of_photos = currentFeed.allResponseLength;
      } else {
        currentFeed.feed_row.number_of_photos = 1 + parseInt(currentFeed.feed_row.image_browser_preload_number);
        currentFeed.feed_row.load_more_number = parseInt(currentFeed.feed_row.image_browser_load_number);
      }
      currentFeed.freeSpaces = (Math.floor(currentFeed.feed_row.pagination_per_page_number / currentFeed.feed_row.number_of_columns) + 1) * currentFeed.feed_row.number_of_columns - currentFeed.feed_row.pagination_per_page_number;
    } else {
      currentFeed.freeSpaces = 0;
    }
    //initializing function for lightbox
    currentFeed.galleryBox = function (image_id) {
      wdi_spider_createpopup(wdi_url.ajax_url + '?gallery_id=' + this.feed_row['id'] + '&image_id=' + image_id, this.feed_row.wdi_feed_counter, this.feed_row['lightbox_width'], this.feed_row['lightbox_height'], 1, 'testpopup', 5, this,image_id);
    }
    //calling responive javascript
    wdi_responsive.columnControl(currentFeed);

    //if feed type is masonry then trigger resize event  for building proper column layout
    if (currentFeed.feed_row.feed_type == 'masonry') {
      jQuery(window).trigger('resize');
    }

    wdi_front.bindEvents(currentFeed);
    window['wdi_feed_' + i] = currentFeed;

    //initializing each feed
    wdi_front.init(currentFeed);
  } //endfor
}

wdi_front.init = function (currentFeed) {
  jQuery('.wdi_js_error').hide();
  //some varables used in code
  currentFeed.photoCounter = currentFeed.feed_row["number_of_photos"];

  if (currentFeed.feed_row.liked_feed == 'liked') {
    currentFeed.feed_users = ['self'];
    // do nothing,
  }
  else {
    if (wdi_front.isJsonString(currentFeed.feed_row.feed_users)) {
      /**
       * Contains username and user_id of each user
       * @type {[Array}
       */
      currentFeed.feed_users = JSON.parse(currentFeed.feed_row.feed_users);

      /**
       * Check if feed user has no id for some reason then update user
       * and after updating them initialize feed
       */
    }
    else {
      wdi_front.show_alert(wdi_front_messages.invalid_users_format, false ,currentFeed);
      return;
    }
  }
  var all_tags = [];
  var feed_user = [];
  var feed_user_tags = [];
  if (typeof window["wdi_all_tags"] !== "undefined") {
    all_tags = window["wdi_all_tags"];
  }

  for (var k =0; k < currentFeed.feed_users.length; k++) {
    if (currentFeed.feed_users[k].username[0] === "#" && typeof currentFeed.feed_users[k].tag_id !== "undefined") {
      all_tags[currentFeed.feed_users[k].tag_id] = currentFeed.feed_users[k];
      feed_user_tags[k] = currentFeed.feed_users[k];
    }
    else {
      feed_user[0] = currentFeed.feed_users[k];
    }
  }
  window["wdi_all_tags"] = all_tags;
  currentFeed.feed_users = ( typeof feed_user_tags !== 'undefined' && !wdi_front.isEmpty(feed_user_tags) ) ? feed_user_tags : feed_user;

  var feedResolution = wdi_front.getFeedItemResolution(currentFeed);
  currentFeed.feedImageResolution = feedResolution.image;
  currentFeed.feedVideoResolution = feedResolution.video;
  currentFeed.dataCount = currentFeed.feed_users.length;  // 1 in case of self feed
  for ( var i = 0; i < currentFeed.dataCount; i++ ) {
     wdi_front.instagramRequest(i, currentFeed);
  }
  if (currentFeed.feed_row["number_of_photos"] > 0) {
    wdi_front.ajaxLoader(currentFeed);
  }
  // setting feed name
  if (currentFeed['feed_row']['display_header'] === '1') {
    wdi_front.show('header', currentFeed);
  }
  if (currentFeed['feed_row']['show_usernames'] === '1') {
    wdi_front.show('users', currentFeed);
  }
}

wdi_front.getFeedItemResolution = function (currentFeed) {
  var defaultResolution = {
    "image": "standard_resolution",
    "video": "standard_resolution"
  };

  if (currentFeed.feed_row.feed_resolution === "thumbnail") {
    return {"image": "thumbnail", "video": "low_bandwidth"};
  } else if (currentFeed.feed_row.feed_resolution === "low") {
    return {"image": "low_resolution", "video": "low_resolution"};
  } else if (currentFeed.feed_row.feed_resolution === "standard") {
    return {"image": "standard_resolution", "video": "standard_resolution"};
  }

  var container = jQuery("#wdi_feed_" + currentFeed.feed_row.wdi_feed_counter).find('.wdi_feed_wrapper');
  container.append('<div class="wdi_feed_item" id="wdi_feed_item_example"></div>');

  wdi_responsive.columnControl(currentFeed, 1);
  var attr = container.attr('wdi-res').split('wdi_col_');
  container.find('#wdi_feed_item_example').remove();

  if(attr.length !== 2){
    return defaultResolution;
  }

  var itemsCount = parseInt(attr[1]);
  if(itemsCount <= 0){
    return defaultResolution;
  }

  var size = (container.width() / itemsCount) - 10;
  var resolution = defaultResolution;

  if(size <= 150){
    resolution.image = "thumbnail";
    resolution.video = "low_bandwidth";
  }
  else if(size > 150 && size <= 320){
    resolution.image = "low_resolution";
    resolution.video = "low_resolution";
  }
  else{
    resolution.image = "standard_resolution";
    resolution.video = "standard_resolution";
  }

  return resolution;
};

/**
 * Checks if given string is JSON string
 * @param  {String}  str [string to check]
 * @return {Boolean}     [true or false]
 */
wdi_front.isJsonString = function (str) {
  try {
    JSON.parse(str);
  } catch (e) {
    return false;
  }
  return true;
}

/**
 * Makes an ajax request for given user from feed_users array
 * if response is ok then calls saveUserData function
 * if liked media to show, feed user is self
 * @param  {Number} id          [index of user in current_feed.feed_users array]
 * @param  {Object} currentFeed
 */
wdi_front.instagramRequest = function (id, currentFeed) {
  var _this = this,
    feed_users = currentFeed.feed_users;
  if ( typeof feed_users[id] === 'string' && feed_users[id] === 'self' ) { // self liked media
    currentFeed.instagram.getRecentLikedMedia({
      success: function (response) {
        if(typeof response.meta!= "undefined" && typeof response.meta.error_type != "undefined"){
          wdi_front.show_alert(false, response, currentFeed);
        }
        currentFeed.mediaRequestsDone = true;
        response = _this.checkMediaResponse(response, currentFeed);
        if (response != false) {
          _this.saveSelfUserData(response, currentFeed);
        }
      }
    });
  }
  else {
      if ( this.getInputType(feed_users[id]['username']) == 'hashtag' ) {
        if ( this.isJsonString(currentFeed.feed_row.feed_users) ) {
          json_feed_users = JSON.parse(currentFeed.feed_row.feed_users);
          for ( var i in json_feed_users ) {
            if ( json_feed_users[i].username.charAt(0) !== '#' ) {
              user = json_feed_users[i];
            }
          }
        }

        currentFeed.instagram.getTagRecentMedia(this.stripHashtag(feed_users[id]['username']), {
          feed_id: currentFeed.feed_row.id,
          user_id: user.id,
          user_name: user.username,
          success: function (response) {
            if ( ( typeof response.error != 'undefined' && response.error.type != 'undefined' ) || ( typeof response.meta != 'undefined' && response.meta.error == true ) ) {
              currentFeed.dataLoaded = 1;
              wdi_front.show_alert(false, response, currentFeed);
              return false;
            }
            currentFeed.mediaRequestsDone = true;
            response = _this.checkMediaResponse(response, currentFeed);
            currentFeed.dataLoaded = 1;
            if ( response != false ) {
              _this.saveUserData(response, currentFeed.feed_users[id], currentFeed);
            }
          }
        },
        null,
        currentFeed.feed_row.hashtag_top_recent,
        0
      );
    }
    else {
      if ( this.getInputType( feed_users[id]['username']) == 'user' ) {
        currentFeed.instagram.getUserMedia({
          feed_id: currentFeed.feed_row.id,
          user_id: feed_users[id].id,
          user_name: feed_users[id].username,
          success: function (response) {
            if ( typeof response.meta != 'undefined' && typeof response.meta.error == true ) {
              currentFeed.dataLoaded = 1;
              wdi_front.show_alert(false, response, currentFeed);
              return false;
            }
            currentFeed.mediaRequestsDone = true;
            currentFeed.dataLoaded = 1;
            response = _this.checkMediaResponse(response, currentFeed);
            if ( response != false ) {
               _this.saveUserData(response, currentFeed.feed_users[id], currentFeed);
            } else {
              wdi_front.allImagesLoaded(currentFeed);
            }
          }
        }, '', 0);
      }
    }
  }
}

/**
 * Returns true is given string starts with dash ( # )
 * @param  {String}  str
 * @return {Boolean}     [true or false]
 */
wdi_front.isHashtag = function (str)
{
  return (str[0] === '#');
}

/*
 * Saves each user data on seperate index in currentFeed.usersData array
 * And also checks if all data form all users is already avialable if yes it displays feed
 */
wdi_front.saveUserData = function (data, user, currentFeed) {

  data['user_id'] = user.id;
  data['username'] = user.username;

  // checking if user type is hashtag then manually add hashtag to each object, for later use
  // hashtag based filters
  if (data['user_id'][0] === '#') {
    data['data'] = wdi_front.appendRequestHashtag(data['data'], data['user_id']);
  }

  currentFeed.usersData.push(data);
  currentFeed.currentResponseLength = wdi_front.getArrayContentLength(currentFeed.usersData, 'data');
  currentFeed.allResponseLength += currentFeed.currentResponseLength;
  if (currentFeed.dataCount == currentFeed.usersData.length) {
    //if getted objects is not enough then recuest new ones
    if (currentFeed.currentResponseLength < currentFeed.feed_row.number_of_photos && !wdi_front.userHasNoPhoto(currentFeed)) {
      /*here we are calling loadMore function out of recursion cycle, after this initial-keep call
       loadMore will be called with 'initial' recursively until the desired number of photos is reached
       if possible*/
      wdi_front.loadMore('initial-keep', currentFeed);
    }
    else {
      //display feed
      wdi_front.displayFeed(currentFeed);
      //when all data us properly displayed check for any active filters and then apply them
      wdi_front.applyFilters(currentFeed);
      /*removing load more button of feed has finished*/
      if ( !wdi_front.activeUsersCount(currentFeed) ) {
        if (currentFeed.feed_row.feed_display_view == 'load_more_btn') {
          var feed_container = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter);
          feed_container.find('.wdi_load_more').addClass('wdi_hidden');
          feed_container.find('.wdi_spinner').addClass('wdi_hidden');
        }
      }
    }
  }
}

/*
 * Saves self user data on separate index in currentFeed.usersData array
 * And also checks if all data form all users is already avialable if yes it displays feed
 */
wdi_front.saveSelfUserData = function (data, currentFeed) {

  //keep empty for self feed
  data['user_id'] = '';
  data['username'] = '';

  currentFeed.usersData.push(data);
  currentFeed.currentResponseLength = wdi_front.getArrayContentLength(currentFeed.usersData, 'data');
  currentFeed.allResponseLength += currentFeed.currentResponseLength;
  if (currentFeed.dataCount == currentFeed.usersData.length) {
    //if retrieved objects are not enough then request new ones
    if (currentFeed.currentResponseLength < currentFeed.feed_row.number_of_photos && !wdi_front.userHasNoPhoto(currentFeed)) {
      /*here we are calling loadMore function out of recursion cycle, after this initial-keep call
       loadMore will be called with 'initial' recursively until the desired number of photos is reached
       if possible*/

      wdi_front.loadMore('initial-keep', currentFeed);
    }
    else {
      //display feed
      wdi_front.displayFeed(currentFeed);
      //when all data us properly displayed check for any active filters and then apply them
      wdi_front.applyFilters(currentFeed);

      /*removing load more button of feed has finished*/
      if (!wdi_front.activeUsersCount(currentFeed)) {
        if (currentFeed.feed_row.feed_display_view == 'load_more_btn') {
          var feed_container = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter);
          feed_container.find('.wdi_load_more').addClass('wdi_hidden');
          feed_container.find('.wdi_spinner').addClass('wdi_hidden');
        }
      }
    }
  }
}

/**
 * checks weather all feed users have any photos after first time request
 */
wdi_front.userHasNoPhoto = function (currentFeed, cstData) {
  var counter = 0;
  var data = currentFeed.usersData;
  if (typeof cstData != 'undefined') {
    data = cstData;
  }
  for (var i = 0; i < data.length; i++) {
    if(typeof data[i]['pagination'] == 'undefined'){
      data[i]['pagination'] = [];
    }

    if (currentFeed.feed_row.liked_feed === 'liked') {
      if (typeof data[i]['pagination']['next_max_like_id'] == 'undefined') {
        counter++
      }
    }
    else {
      if (typeof data[i]['pagination']['next_max_id'] == 'undefined') {
        counter++
      }
    }

  }
  if (counter == data.length) {
    return 1;
  } else {
    return 0;
  }
}

/*
 *gives each instagram object custom hashtag parameter, which is used for searching image/video
 */
wdi_front.appendRequestHashtag = function (data, hashtag) {
  for (var i = 0; i < data.length; i++) {
    data[i]['wdi_hashtag'] = hashtag;
  }
  return data;
}

/*
 * sorts data based on user choice and displays feed
 * also checks if one request is not enough for displaying all images user wanted
 * it recursively calls wdi_front.loadMore() until the desired number of photos is reached
 */
wdi_front.displayFeed = function ( currentFeed, page_number ) {
  if ( typeof page_number === 'undefined' ) {
    page_number = 1;
  }
  for ( var i = 0; i < currentFeed['usersData'].length; i++ ) {
    currentFeed['dataStorageList'] = currentFeed['usersData'][i]['data'];
    /* Using in parsed lightbox function */
    currentFeed['dataStorage'][i] = currentFeed['usersData'][i]['data'];
  }
  var first_page_img_count = currentFeed['feed_row']['number_of_photos'];
  var load_more_count = currentFeed['feed_row']['load_more_number'];
  var start_index = 0;
  var end_index = first_page_img_count;
  var data = '';

  /* Type of simple pagination */
  if ( currentFeed.feed_row.feed_display_view == 'pagination' ) {
    if ( currentFeed.allResponseLength > 1 ) {
      jQuery('.wdi_pagination').removeClass('wdi_hidden');
    }
    currentFeed.feed_row.number_of_photos = currentFeed.allResponseLength;
    if ( currentFeed.feed_row.feed_type == 'image_browser' ) {
      currentFeed.paginator = parseInt(currentFeed.feed_row.number_of_photos);
    }
    else {
      currentFeed.paginator = Math.ceil(parseInt(currentFeed.feed_row.number_of_photos) / parseInt(load_more_count));
    }
    if ( page_number === 1 ) {
      start_index = 0;
      if ( currentFeed.feed_row.feed_type == 'image_browser' ) {
        end_index = 1;
      }
      else {
        end_index = load_more_count;
      }
      data = currentFeed['dataStorageList'].slice(start_index, end_index);
    }
    else {
      if ( currentFeed.feed_row.feed_type == 'image_browser' ) {
        start_index = (page_number - 1);
        end_index = start_index + 1;
        data = currentFeed['dataStorageList'].slice(start_index, end_index);
      }
      else {
        start_index = (page_number - 1) * load_more_count;
        end_index = start_index + load_more_count;
        data = currentFeed['dataStorageList'].slice(start_index, end_index);
      }
    }
  }
  else {
    if ( typeof currentFeed['already_loaded_count'] !== 'undefined' ) {
      start_index = parseInt(currentFeed['already_loaded_count']);
      end_index = currentFeed['already_loaded_count'] + parseInt(load_more_count);
      data = currentFeed['dataStorageList'].slice(start_index, end_index);
      currentFeed['already_loaded_count'] += data.length;
    }
    else {
      data = currentFeed['dataStorageList'].slice(start_index, end_index);
      currentFeed['already_loaded_count'] = data.length;
    }
  }
  // parsing data for lightbox
  currentFeed.parsedData = wdi_front.parseLighboxData(currentFeed, true);
  // checking feed_type and calling proper rendering functions
  if ( currentFeed.feed_row.feed_type == 'masonry' ) {
    wdi_front.masonryDisplayFeedItems(data, currentFeed);
  }
  if ( currentFeed.feed_row.feed_type == 'thumbnails' || currentFeed.feed_row.feed_type == 'blog_style' || currentFeed.feed_row.feed_type == 'image_browser' ) {
    wdi_front.displayFeedItems(data, currentFeed);
  }
  // checking if display_view is pagination and we are not on the last page then enable
  // last page button
  if ( currentFeed.feed_row.feed_display_view == 'pagination' && currentFeed.currentPage < currentFeed.paginator ) {
    jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter).find('#wdi_last_page').removeClass('wdi_disabled');
  }
  //if there are any missing images in header then replace them with new ones if possible
  wdi_front.updateUsersImages(currentFeed);
}

/**
 * checks if user images in header have empty source or source is missing.png then if it is available data
 * then update source
 * @param  {Object} currentFeed [description]
 */
wdi_front.updateUsersImages = function (currentFeed) {
  var elements = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter).find('.wdi_single_user .wdi_user_img_wrap img');
  elements.each(function ()
  {
    if (jQuery(this).attr('src') == wdi_url.plugin_url + 'images/missing.png' || jQuery(this).attr('src') == '') {
      if (currentFeed.feed_row.liked_feed == 'liked') {
        return;
      }
      for (var j = 0; j < currentFeed.usersData.length; j++) {
        if (currentFeed.usersData[j]['username'] == jQuery(this).parent().parent().find('h3').text()) {
          if (currentFeed.usersData[j]['data'].length != 0) {
            jQuery(this).attr('src', currentFeed.usersData[j]['data'][0]['images']['thumbnail']['url']);
          }
        }
      }
    }
  });
}

wdi_front.checkLoaded = function (currentFeed) {
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var feed_container = jQuery('#wdi_feed_' + wdi_feed_counter);
  /* if there are images which can be loaded */
  if( currentFeed['dataStorageList'].length > (currentFeed['already_loaded_count']) ) {
    feed_container.find('.wdi_load_more').removeClass('wdi_hidden');
    feed_container.find('.wdi_spinner').addClass('wdi_hidden');
  } else {
    feed_container.find('.wdi_load_more').addClass('wdi_hidden');
    feed_container.find('.wdi_spinner').addClass('wdi_hidden');
  }
  setTimeout(function () {
    feed_container.find('.wdi_ajax_loading').addClass('wdi_hidden');
    feed_container.find(".wdi_page_loading").addClass("wdi_hidden");
    feed_container.find('.wdi_front_overlay').addClass('wdi_hidden');
  }, 500);
}

/**
 * Displays data in masonry layout
 * @param  {Object} data        data to be displayed
 * @param  {Object} currentFeed
 */
wdi_front.masonryDisplayFeedItems = function ( data, currentFeed ) {
  var masonryColEnds = [];
  var masonryColumns = [];
  if ( jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper").length == 0 ) {
    //no feed in DOM, ignore
    return;
  }
  jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' .wdi_masonry_column').each(function () {
    //if resorte after load more is on then reset columns on every load more
    if ( currentFeed.feed_row.resort_after_load_more == 1 ) {
      jQuery(this).html('');
      currentFeed.imageIndex = 0;
    }
    //if custom filter is set or changed then reset masonry columns
    if ( currentFeed.customFilterChanged == true ) {
      jQuery(this).html('');
      currentFeed.imageIndex = 0;
    }
    //check if pagination is enabled then each page should have resetted colEnds
    //else give previous colEnds
    if ( currentFeed.feed_row.feed_display_view == 'pagination' ) {
      masonryColEnds.push(0);
    }
    else {
      masonryColEnds.push(jQuery(this).height());
    }
    masonryColumns.push(jQuery(this));
  });
  //if custom filter is set or changed then reset masonry columns
  if ( currentFeed.customFilterChanged == true ) {
    currentFeed.customFilterChanged = false;
  }
  //loop for displaying items
  for ( var i = 0; i < data.length; i++ ) {
    if ( typeof data[i]['videos'] === 'object' ) {
      if ( data[i]['videos'].standard_resolution == null ) {
        continue;
      }
    }
    currentFeed.displayedData.push(data[i]);
    /*carousel feature*/
    var wdi_media_type = "";
    if ( typeof data[i]["wdi_hashtag"] != "undefined" ) {
      wdi_media_type = data[i]["wdi_hashtag"];
    }
    if ( data[i]['type'] == 'image' ) {
      var photoTemplate = wdi_front.getPhotoTemplate(currentFeed, wdi_media_type);
    }
    else if ( data[i].hasOwnProperty('videos') || data[i]['type'] == 'video' ) {
      var photoTemplate = wdi_front.getVideoTemplate(currentFeed, wdi_media_type);
    }
    else {
      var photoTemplate = wdi_front.getSliderTemplate(currentFeed, wdi_media_type);
    }
    var rawItem = data[i];
    var item = wdi_front.createObject(rawItem, currentFeed);
    var html = photoTemplate(item);
    //find column with minumum height and append to it new object
    var shortCol = wdi_front.array_min(masonryColEnds);
    var imageResolution = wdi_front.getImageResolution(data[i]);
    masonryColumns[shortCol['index']].html(masonryColumns[shortCol['index']].html() + html);
    masonryColEnds[shortCol['index']] += masonryColumns[shortCol['index']].width() * imageResolution;
    currentFeed.imageIndex++;
    //changing responsive indexes for pagination
    if ( currentFeed.feed_row.feed_display_view == 'pagination' ) {
      if ( (i + 1) % currentFeed.feed_row.pagination_per_page_number === 0 ) {
        currentFeed.resIndex += currentFeed.freeSpaces + 1;
      }
      else {
        currentFeed.resIndex++;
      }
    }
  }
  //binding onload event for ajax loader
  currentFeed.wdi_loadedImages = 0;
  var columnFlag = false;
  currentFeed.wdi_load_count = i;
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var feed_wrapper = jQuery('#wdi_feed_' + wdi_feed_counter + ' .wdi_img').on('load', function () {
    currentFeed.wdi_loadedImages++;
    //calls wdi_responsive.columnControl() which calculates column number on page
    //and gives feed_wrapper proper column class
    if ( columnFlag === false ) {
      wdi_responsive.columnControl(currentFeed, 1);
      columnFlag = true;
    }
  });
  wdi_front.checkLoaded(currentFeed);
  //checking if pagination next button was clicked then change page
  if ( currentFeed.paginatorNextFlag == true ) {
    wdi_front.updatePagination(currentFeed, 'next');
  }
  //check if load more done successfully then set infinite scroll flag to false
  currentFeed.infiniteScrollFlag = false;
}

/*
 * Calcuates image resolution
 */
wdi_front.getImageResolution = function (data) {
  if ( data.type === 'image' ) {
    var originalHeight = data['images']['standard_resolution']['height'];
    var originalWidth = data['images']['standard_resolution']['width'];
  }
  else if ( data.type === 'video' ) {
    var originalHeight = data['videos']['standard_resolution']['height'];
    var originalWidth = data['videos']['standard_resolution']['width'];
  }
  else {
    var originalHeight = data['carousel_media'][0]['images'] ? data['carousel_media'][0]['images']['standard_resolution']['height'] : data['carousel_media'][0]['videos']['standard_resolution']['height'];
    var originalWidth = data['carousel_media'][0]['images'] ? data['carousel_media'][0]['images']['standard_resolution']['width'] : data['carousel_media'][0]['videos']['standard_resolution']['width'];
  }

  var resolution = originalHeight / originalWidth;
  return resolution;
}

/*
 * Calculates data count on global Storage and if custom storage provied
 * it adds custom storage data count to golbals data count and returns length of all storages
 */
wdi_front.getDataLength = function (currentFeed, customStorage) {

  var length = 0;
  if (typeof customStorage === 'undefined') {
    for (var j = 0; j < currentFeed.dataStorage.length; j++) {
      length += currentFeed.dataStorage[j].length;
    }
  } else {
    for (var j = 0; j < customStorage.length; j++) {
      length += customStorage[j].length;
    }
  }

  return length;
}

wdi_front.getArrayContentLength = function (array, data) {
  var sum = 0;
  for (var i = 0; i < array.length; i++) {
    if (array[i]['finished'] == 'finished' || (typeof array[i]['error'] !== 'undefined')) {
      continue;
    }
    sum += array[i][data].length;
  }
  return sum;
}

/**
 * Displays data in thumbnail layout
 * @param  {Object} data        data to be displayed
 * @param  {Object} currentFeed
 */
wdi_front.displayFeedItems = function ( data, currentFeed ) {
  if ( jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper").length == 0 ) {
    // no feed in DOM, ignore
    return;
  }
  // gets ready data, gets data template, and appens it into feed_wrapper
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var feed_wrapper = jQuery('#wdi_feed_' + wdi_feed_counter + ' .wdi_feed_wrapper');
  /*
     // if feed display view is set to pagination then check if the current page has not enough photos to be a complete page then
     // --currentPage so that after loading new images we stay on the same page and see new images which will be located in that page
     // also do the same thing when recievied data has lenght equal to zero
    if (currentFeed.feed_row.feed_display_view == 'pagination') {
      var local_load_more_number  = currentFeed.feed_row.load_more_number;
      if(currentFeed.feed_row.feed_type == 'image_browser') {
        local_load_more_number = 1;
      }
    }
  */
  for ( var i = 0; i < data.length; i++ ) {
    if ( typeof data[i] == 'undefined' ) {
      return;
    }
    if ( typeof data[i]['videos'] === 'object' ) {
      if ( data[i]['videos'].standard_resolution == null ) {
        continue;
      }
    }
    var wdi_media_type = '';
    if ( typeof data[i]['wdi_hashtag'] != 'undefined' ) {
      wdi_media_type = data[i]['wdi_hashtag'];
    }
    if ( data[i]['type'] == 'image' ) {
      var photoTemplate = wdi_front.getPhotoTemplate(currentFeed, wdi_media_type);
    }
    else if ( data[i].hasOwnProperty('videos') ) {
      var photoTemplate = wdi_front.getVideoTemplate(currentFeed, wdi_media_type);
    }
    else {
      var photoTemplate = wdi_front.getSliderTemplate(currentFeed, wdi_media_type);
    }
    var rawItem = data[i];
    var item = wdi_front.createObject(rawItem, currentFeed);
    var html = '';
    // undefined when carousel media not defined
    if ( typeof item !== 'undefined' ) {
      html = photoTemplate(item);
    }
    feed_wrapper.html(feed_wrapper.html() + html);
    currentFeed.imageIndex++;
    // changing responsive indexes for pagination
    if ( currentFeed.feed_row.feed_display_view == 'pagination' ) {
      if ( (i + 1) % currentFeed.feed_row.pagination_per_page_number === 0 ) {
        currentFeed.resIndex += currentFeed.freeSpaces + 1;
      }
      else {
        currentFeed.resIndex++;
      }
    }
  }
  wdi_front.checkLoaded(currentFeed);
}

wdi_front.checkFeedFinished = function (currentFeed) {
  for (var i = 0; i < currentFeed.usersData.length; i++) {
    if (typeof currentFeed.usersData[i]['finished'] == 'undefined') {
      return false;
    }
  }
  return true;
}


/*
 * returns json object for inserting photo template
 */
wdi_front.createObject = function (obj, currentFeed) {
  var caption = (obj['caption'] != null) ? obj['caption']['text'] : '&nbsp';
  switch (obj['type']) {
    case 'image':
      var image_url = obj.images[currentFeed.feedImageResolution].url;
      var videoUrl = undefined;
      var thumb_url = obj.hasOwnProperty('thumbnail') ? obj['thumbnail'] : wdi_url.plugin_url + "images/missing.png";
      break;
    case 'video':
      var image_url = undefined;
      var videoUrl = obj.hasOwnProperty('videos') ? obj['videos'][currentFeed.feedVideoResolution]['url'] : wdi_url.plugin_url + "images/video_missing.png";
      var thumb_url = obj.hasOwnProperty('thumbnail') ? obj['thumbnail'] : wdi_url.plugin_url + "images/video_missing.png";
      break;
    case 'carousel':
      if( obj.carousel_media.length === 0 ){
          var image_url = wdi_url.plugin_url + "images/missing.png";
          var videoUrl = undefined;
          var thumb_url = wdi_url.plugin_url + "images/missing.png";
      } else {
          switch (obj.carousel_media[0].type) {
            case 'image':
              var image_url = obj.carousel_media[0].images[currentFeed.feedImageResolution].url;
              var videoUrl = undefined;
              var thumb_url = obj.hasOwnProperty('thumbnail') ? obj['thumbnail'] : wdi_url.plugin_url + "images/missing.png";
              break;
            case 'video':
              var image_url = undefined;
              var videoUrl = obj.carousel_media[0].videos[currentFeed.feedVideoResolution].url;
              var thumb_url = obj.hasOwnProperty('thumbnail') ? obj['thumbnail'] : wdi_url.plugin_url + "images/video_missing.png";
              break;
            default:
              var image_url = wdi_url.plugin_url + "images/missing.png";
              var videoUrl = undefined;
              var thumb_url = wdi_url.plugin_url + "images/missing.png";
          }
      }
      break;
    default:
      var image_url = wdi_url.plugin_url + "images/missing.png";
      var videoUrl = undefined;
      var thumb_url = wdi_url.plugin_url + "images/missing.png";
  }

  var imageIndex = currentFeed.imageIndex;

  var wdi_shape = 'square';
  if ( obj.type === 'image' ) {
    var media_standard_h = obj['images']['standard_resolution']['height'];
    var media_standard_w = obj['images']['standard_resolution']['width'];
  }
  else if ( obj.type === 'video' ) {
    var media_standard_h = obj['videos']['standard_resolution']['height'] ;
    var media_standard_w = obj['videos']['standard_resolution']['width'] ;
  }
  else {
    var carousel_media_type = ( typeof obj['carousel_media'][0]['images'] !== 'undefined' ) ? 'images' : 'videos';
    var media_standard_h = obj['carousel_media'][0][carousel_media_type]['standard_resolution']['height'];
    var media_standard_w = obj['carousel_media'][0][carousel_media_type]['standard_resolution']['width'];
  }
  if(media_standard_h > media_standard_w){
    wdi_shape = 'portrait';
  }
  else if(media_standard_h < media_standard_w){
    wdi_shape = 'landscape';
  }
  var obj_user_name = obj['user']['username'];
  if(obj_user_name === ""){
    obj_user_name = "no_user";
  }
  var igMediaObject = {
    'id': obj['id'],
    'thumb_url': thumb_url,
    'caption': wdi_front.escape_tags(caption),
    'image_url': image_url,
    'likes': ( typeof obj['likes']['count'] !== 'undefined' ) ? obj['likes']['count'] : obj['likes'],
    'comments': ( typeof obj['comments']['count'] !== 'undefined' ) ? obj['comments']['count'] : obj['comments'],
    'wdi_index': imageIndex,
    'wdi_res_index': currentFeed.resIndex,
    'wdi_media_user': obj_user_name,
    'link': obj['link'],
    'video_url': videoUrl,
    'wdi_username': obj_user_name,
    'wdi_shape': wdi_shape
  };
  return igMediaObject;
}


/*
 * Template for all feed items which have type=image
 */
wdi_front.getPhotoTemplate = function (currentFeed , type) {
  var customClass = '';
  var pagination = '';
  var onclick = '';
  var overlayCustomClass = '';
  var thumbClass = 'tenweb-i-arrows-out';
  var showUsernameOnThumb = '';
  var sourceAttr = 'src';
  if (currentFeed.feed_row.feed_type == 'blog_style' || currentFeed.feed_row.feed_type == 'image_browser') {
    thumbClass = '';
  }


  if (currentFeed.feed_row.show_username_on_thumb == '1' && currentFeed.data.length && currentFeed.data[0].user.username !== "") {
    showUsernameOnThumb = '<span class="wdi_media_user">@<%= wdi_username%></span>';
  }

  //checking if caption is opend by default then add wdi_full_caption class
  //only in masonry
  if (currentFeed.feed_row.show_full_description == 1 && currentFeed.feed_row.feed_type == 'masonry') {
    customClass += ' wdi_full_caption';
  }

  var onclickevent = "";
  if (currentFeed.feed_row.feed_type !== "blog_style") {
    if (currentFeed.feed_row.feed_type == 'masonry') {
      onclickevent = "wdi_responsive.showMasonryCaption(jQuery(this)," + currentFeed.feed_row.wdi_feed_counter + ");"
    } else {
      onclickevent = "wdi_responsive.showCaption(jQuery(this)," + currentFeed.feed_row.wdi_feed_counter + ");";
    }
  }

  //creating onclick string for different options
  switch (currentFeed.feed_row.feed_item_onclick) {
    case 'lightbox':
    {
      onclick = "onclick=wdi_feed_" + currentFeed.feed_row.wdi_feed_counter + ".galleryBox('<%=id%>')";
      break;
    }
    case 'instagram':
    {
      onclick = 'onclick="window.open (\'<%= link%>\',\'_blank\')"';
      overlayCustomClass = 'wdi_hover_off';
      thumbClass = '';
      break;
    }
    case 'custom_redirect':
    {
      onclick = 'onclick="window.open (\'' + currentFeed.feed_row.redirect_url + '\',\'_self\')"';
      overlayCustomClass = 'wdi_hover_off';
      thumbClass = '';
      break;
    }
    case 'none':
    {
      onclick = '';
      overlayCustomClass = 'wdi_cursor_off wdi_hover_off';
      thumbClass = '';
    }
  }
  var wdi_shape_class = "<%= wdi_shape == 'square' ? 'wdi_shape_square' : (wdi_shape == 'portrait' ? 'wdi_shape_portrait' : (wdi_shape == 'landscape' ? 'wdi_shape_landscape' : 'wdi_shape_square') ) %>";
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var source = '<div class="wdi_feed_item ' + customClass + '"  wdi_index=<%= wdi_index%>  wdi_res_index=<%= wdi_res_index%> wdi_media_user=<%= wdi_media_user%> ' + pagination + ' wdi_type="image" id="wdi_' + wdi_feed_counter + '_<%=id%>">' +
    '<div class="wdi_photo_wrap">' +
    '<div class="wdi_photo_wrap_inner">' +
    '<div class="wdi_photo_img ' + wdi_shape_class + '">' +
    '<img class="wdi_img" ' + sourceAttr + '="<%=thumb_url%>" alt="feed_image" onerror="wdi_front.brokenImageHandler(this);">' +
    '<div class="wdi_photo_overlay ' + overlayCustomClass + '" >' + showUsernameOnThumb +
    '<div class="wdi_thumb_icon" ' + onclick + ' style="display:table;width:100%;height:100%;">' +
    '<div style="display:table-cell;vertical-align:middle;text-align:center;color:white;">' +
    '<i class="tenweb-i ' + thumbClass + '"></i>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';
  var imageIndex = currentFeed['imageIndex'];
  if (currentFeed['feed_row']['show_likes'] === '1' || currentFeed['feed_row']['show_comments'] === '1' || currentFeed['feed_row']['show_description'] === '1') {
    source += '<div class="wdi_photo_meta">';
    var likes_count = 0;
    var comments_count = 0;
    if ( typeof currentFeed['dataStorageList'][imageIndex] !== 'undefined' ) {
      if ( typeof currentFeed['dataStorageList'][imageIndex]['likes'] !== 'undefined' ) {
        if ( typeof currentFeed['dataStorageList'][imageIndex]['likes']['count'] !== 'undefined' ) {
          likes_count = currentFeed['dataStorageList'][imageIndex]['likes']['count'];
        }
        else {
          likes_count = currentFeed['dataStorageList'][imageIndex]['likes'];
        }
      }
      if ( typeof currentFeed['dataStorageList'][imageIndex]['comments'] !== 'undefined' ) {
        if ( typeof currentFeed['dataStorageList'][imageIndex]['comments']['count'] !== 'undefined' ) {
          comments_count = currentFeed['dataStorageList'][imageIndex]['comments']['count'];
        }
        else {
          comments_count = currentFeed['dataStorageList'][imageIndex]['comments'];
        }
      }
    }
    if ( currentFeed['feed_row']['show_likes'] === '1' && likes_count !== 0 ) {
      source += '<div class="wdi_thumb_likes"><i class="tenweb-i tenweb-i-heart-o">&nbsp;<%= likes%></i></div>';
    }
    if ( currentFeed['feed_row']['show_comments'] === '1' && comments_count !== 0 ) {
      source += '<div class="wdi_thumb_comments"><i class="tenweb-i tenweb-i-comment-square">&nbsp;<%= comments%></i></div>';
    }
    source += '<div class="wdi_clear"></div>';
    if (currentFeed['feed_row']['show_description'] === '1') {
      source += '<div class="wdi_photo_title" onclick=' + onclickevent + ' >' +
        '<%=caption%>' +
        '</div>';
    }
    source += '</div>';
  }

  source += '</div>';
  var template = _.template(source);
  return template;
}

/*
 * Template for all feed items which have type=image
 */
wdi_front.getSliderTemplate = function (currentFeed, type) {
  var customClass = '';
  var pagination = '';
  var onclick = '';
  var overlayCustomClass = '';
  var thumbClass = 'tenweb-i-clone';
  var showUsernameOnThumb = '';
  var sourceAttr = 'src';
  if (currentFeed.feed_row.feed_type == 'blog_style' || currentFeed.feed_row.feed_type == 'image_browser') {
    thumbClass = '';
  }

  if (currentFeed.feed_row.show_username_on_thumb == '1' && currentFeed.data.length && currentFeed.data[0].user.username !== "") {
    showUsernameOnThumb = '<span class="wdi_media_user">@<%= wdi_username%></span>';
  }

  //checking if caption is opend by default then add wdi_full_caption class
  //only in masonry
  if (currentFeed.feed_row.show_full_description == 1 && currentFeed.feed_row.feed_type == 'masonry') {
    customClass += ' wdi_full_caption';
  }

  var onclickevent = "";
  if (currentFeed.feed_row.feed_type !== "blog_style") {
    if (currentFeed.feed_row.feed_type == 'masonry') {
      onclickevent = "wdi_responsive.showMasonryCaption(jQuery(this)," + currentFeed.feed_row.wdi_feed_counter + ");"
    } else {
      onclickevent = "wdi_responsive.showCaption(jQuery(this)," + currentFeed.feed_row.wdi_feed_counter + ");";
    }
  }
  //creating onclick string for different options
  switch (currentFeed.feed_row.feed_item_onclick) {
    case 'lightbox':
    {
      onclick = "onclick=wdi_feed_" + currentFeed.feed_row.wdi_feed_counter + ".galleryBox('<%=id%>')";
      break;
    }
    case 'instagram':
    {
      onclick = 'onclick="window.open (\'<%= link%>\',\'_blank\')"';
      overlayCustomClass = 'wdi_hover_off';
      thumbClass = 'tenweb-i-clone';
      break;
    }
    case 'custom_redirect':
    {
      onclick = 'onclick="window.open (\'' + currentFeed.feed_row.redirect_url + '\',\'_self\')"';
      overlayCustomClass = 'wdi_hover_off';
      thumbClass = '';
      break;
    }
    case 'none':
    {
      onclick = '';
      overlayCustomClass = 'wdi_cursor_off wdi_hover_off';
      thumbClass = '';
    }
  }

  var wdi_shape_class = "<%= wdi_shape == 'square' ? 'wdi_shape_square' : (wdi_shape == 'portrait' ? 'wdi_shape_portrait' : (wdi_shape == 'landscape' ? 'wdi_shape_landscape' : 'wdi_shape_square') ) %>";
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var source = '<div class="wdi_feed_item ' + customClass + '"  wdi_index=<%= wdi_index%>  wdi_res_index=<%= wdi_res_index%> wdi_media_user=<%= wdi_media_user%> ' + pagination + ' wdi_type="slideshow" id="wdi_' + wdi_feed_counter + '_<%=id%>">' +
    '<div class="wdi_photo_wrap">' +
    '<div class="wdi_photo_wrap_inner">' +
    '<div class="wdi_photo_img ' + wdi_shape_class + '">' +
    '<img class="wdi_img" ' + sourceAttr + '="<%=thumb_url%>" alt="feed_image" onerror="wdi_front.brokenImageHandler(this);">' +
    '<div class="wdi_photo_overlay ' + overlayCustomClass + '" >' + showUsernameOnThumb +
    '<div class="wdi_thumb_icon" ' + onclick + ' style="display:table;width:100%;height:100%;">' +
    '<div style="display:table-cell;vertical-align:middle;text-align:center;color:white;">' +
    '<i class="tenweb-i ' + thumbClass + '"></i>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';
  var imageIndex = currentFeed['imageIndex'];
  if (currentFeed['feed_row']['show_likes'] === '1' || currentFeed['feed_row']['show_comments'] === '1' || currentFeed['feed_row']['show_description'] === '1') {
    source += '<div class="wdi_photo_meta">';
    var likes_count = 0;
    var comments_count = 0;
    if ( typeof currentFeed['dataStorageList'][imageIndex] !== 'undefined' ) {
      if ( typeof currentFeed['dataStorageList'][imageIndex]['likes'] !== 'undefined' ) {
        if ( typeof currentFeed['dataStorageList'][imageIndex]['likes']['count'] !== 'undefined' ) {
          likes_count = currentFeed['dataStorageList'][imageIndex]['likes']['count'];
        }
        else {
          likes_count = currentFeed['dataStorageList'][imageIndex]['likes'];
        }
      }
      if ( typeof currentFeed['dataStorageList'][imageIndex]['comments'] !== 'undefined' ) {
        if ( typeof currentFeed['dataStorageList'][imageIndex]['comments']['count'] !== 'undefined' ) {
          comments_count = currentFeed['dataStorageList'][imageIndex]['comments']['count'];
        }
        else {
          comments_count = currentFeed['dataStorageList'][imageIndex]['comments'];
        }
      }
    }
    if ( currentFeed['feed_row']['show_likes'] === '1' && likes_count !== 0 ) {
      source += '<div class="wdi_thumb_likes"><i class="tenweb-i tenweb-i-heart-o">&nbsp;<%= likes%></i></div>';
    }
    if ( currentFeed['feed_row']['show_comments'] === '1' && comments_count !== 0 ) {
      source += '<div class="wdi_thumb_comments"><i class="tenweb-i tenweb-i-comment-square">&nbsp;<%= comments%></i></div>';
    }
    source += '<div class="wdi_clear"></div>';
    if ( currentFeed['feed_row']['show_description'] === '1' ) {
      source += '<div class="wdi_photo_title" onclick=' + onclickevent + ' >' +
        '<%=caption%>' +
        '</div>';
    }
    source += '</div>';
  }

  source += '</div>';
  var template = _.template(source);
  return template;
}

/*
 * Template for all feed items which have type=video
 */
wdi_front.getVideoTemplate = function (currentFeed, type) {
  var customClass = '';
  var pagination = '';
  var thumbClass = 'tenweb-i-play';
  var onclick = '';
  var overlayCustomClass = '';
  var sourceAttr = 'src';;
  var showUsernameOnThumb = '';

  if (currentFeed.feed_row.show_username_on_thumb == '1' && currentFeed.data.length && currentFeed.data[0].user.username !== "") {
    showUsernameOnThumb = '<span class="wdi_media_user">@<%= wdi_username%></span>';
  }

  //checking if caption is opend by default then add wdi_full_caption class
  //only in masonry
  if (currentFeed.feed_row.show_full_description == 1 && currentFeed.feed_row.feed_type == 'masonry') {
    customClass += ' wdi_full_caption';
  }

  var onclickevent = "";
  if (currentFeed.feed_row.feed_type !== "blog_style") {
    if (currentFeed.feed_row.feed_type == 'masonry') {
      onclickevent = "wdi_responsive.showMasonryCaption(jQuery(this)," + currentFeed.feed_row.wdi_feed_counter + ");"
    } else {
      onclickevent = "wdi_responsive.showCaption(jQuery(this)," + currentFeed.feed_row.wdi_feed_counter + ");";
    }
  }

  //creating onclick string for different options
  switch (currentFeed.feed_row.feed_item_onclick) {
    case 'lightbox':
    {
      onclick = "onclick=wdi_feed_" + currentFeed.feed_row.wdi_feed_counter + ".galleryBox('<%=id%>')";
      break;
    }
    case 'instagram':
    {
      onclick = 'onclick="window.open (\'<%= link%>\',\'_blank\')"';
      overlayCustomClass = 'wdi_hover_off';
      thumbClass = 'tenweb-i-play';
      break;
    }
    case 'custom_redirect':
    {
      onclick = 'onclick="window.open (\'' + currentFeed.feed_row.redirect_url + '\',\'_self\')"';
      overlayCustomClass = 'wdi_hover_off';
      thumbClass = '';
      break;
    }
    case 'none':
    {
      overlayCustomClass = 'wdi_cursor_off wdi_hover_off';
      thumbClass = '';
      if (currentFeed.feed_row.feed_type == 'blog_style' || currentFeed.feed_row.feed_type == 'image_browser') {
        onclick = "onclick=wdi_front.replaceToVideo('<%= video_url%>','<%= wdi_index%>'," + currentFeed.feed_row.wdi_feed_counter + ")";
        overlayCustomClass = '';
        thumbClass = 'tenweb-i-play';
      }
    }
  }
  var wdi_shape_class = "<%= wdi_shape == 'square' ? 'wdi_shape_square' : (wdi_shape == 'portrait' ? 'wdi_shape_portrait' : (wdi_shape == 'landscape' ? 'wdi_shape_landscape' : 'wdi_shape_square') ) %>";
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var source = '<div class="wdi_feed_item ' + customClass + '"  wdi_index=<%= wdi_index%> wdi_res_index=<%= wdi_res_index%> wdi_media_user=<%= wdi_media_user%> ' + pagination + ' wdi_type="image" id="wdi_' + wdi_feed_counter + '_<%=id%>">' +
    '<div class="wdi_photo_wrap">' +
    '<div class="wdi_photo_wrap_inner">' +
    '<div class="wdi_photo_img ' +wdi_shape_class + '">' +
    '<img class="wdi_img" ' + sourceAttr + '="<%=thumb_url%>" alt="feed_image" onerror="wdi_front.brokenImageHandler(this);">' +
    '<div class="wdi_photo_overlay ' + overlayCustomClass + '" ' + onclick + '>' + showUsernameOnThumb +
    '<div class="wdi_thumb_icon" style="display:table;width:100%;height:100%;">' +
    '<div style="display:table-cell;vertical-align:middle;text-align:center;color:white;">' +
    '<i class="tenweb-i ' + thumbClass + '"></i>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';
  var imageIndex = currentFeed['imageIndex'];
  if (currentFeed['feed_row']['show_likes'] === '1' || currentFeed['feed_row']['show_comments'] === '1' || currentFeed['feed_row']['show_description'] === '1') {
    source += '<div class="wdi_photo_meta">';
    var likes_count = 0;
    var comments_count = 0;
    if ( typeof currentFeed['dataStorageList'][imageIndex] !== 'undefined' ) {
      if ( typeof currentFeed['dataStorageList'][imageIndex]['likes'] !== 'undefined' ) {
        if ( typeof currentFeed['dataStorageList'][imageIndex]['likes']['count'] !== 'undefined' ) {
          likes_count = currentFeed['dataStorageList'][imageIndex]['likes']['count'];
        }
        else {
          likes_count = currentFeed['dataStorageList'][imageIndex]['likes'];
        }
      }
      if ( typeof currentFeed['dataStorageList'][imageIndex]['comments'] !== 'undefined' ) {
        if ( typeof currentFeed['dataStorageList'][imageIndex]['comments']['count'] !== 'undefined' ) {
          comments_count = currentFeed['dataStorageList'][imageIndex]['comments']['count'];
        }
        else {
          comments_count = currentFeed['dataStorageList'][imageIndex]['comments'];
        }
      }
    }
    if ( currentFeed['feed_row']['show_likes'] === '1' && likes_count !== 0 ) {
      source += '<div class="wdi_thumb_likes"><i class="tenweb-i tenweb-i-heart-o">&nbsp;<%= likes%></i></div>';
    }
    if ( currentFeed['feed_row']['show_comments'] === '1' && comments_count !== 0 ) {
      source += '<div class="wdi_thumb_comments"><i class="tenweb-i tenweb-i-comment-square">&nbsp;<%= comments%></i></div>';
    }
    source += '<div class="wdi_clear"></div>';
    if (currentFeed['feed_row']['show_description'] === '1') {
      source += '<div class="wdi_photo_title" onclick=' + onclickevent + ' >' +
        '<%=caption%>' +
        '</div>';
    }
    source += '</div>';
  }
  source += '</div>';
  var template = _.template(source);
  return template;
}

wdi_front.replaceToVideo = function (url, index, feed_counter) {

  overlayHtml = "<video style='width:auto !important; height:auto !important; max-width:100% !important; max-height:100% !important; margin:0 !important;' controls=''>" +
    "<source src='" + url + "' type='video/mp4'>" +
    "Your browser does not support the video tag. </video>";

  jQuery('#wdi_feed_' + feed_counter + ' [wdi_index="' + index + '"] .wdi_photo_wrap_inner').html(overlayHtml);
  jQuery('#wdi_feed_' + feed_counter + ' [wdi_index="' + index + '"] .wdi_photo_wrap_inner video').get(0).play();
}

wdi_front.bindEvents = function (currentFeed) {

  if (jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper").length == 0) {
    //no feed in DOM, ignore
    return;
  }
  if (currentFeed.feed_row.feed_display_view == 'load_more_btn') {
    //binding load more event
    jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' .wdi_load_more_container').on(wdi_front.clickOrTouch, function () {
      jQuery(document).find('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' .wdi_load_more').addClass('wdi_hidden');
      jQuery(document).find('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' .wdi_spinner').removeClass('wdi_hidden');

      //do the actual load more operation
      setTimeout(function () {
        wdi_front.loadMore(jQuery(this).find('.wdi_load_more_wrap'), currentFeed);
      },1000)
    });
  }

  if ( currentFeed.feed_row.feed_display_view == 'pagination' ) {
    //binding pagination events
    jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' #wdi_next').on(wdi_front.clickOrTouch, function () {
      var wdi_current_page = parseInt(jQuery(this).parents('.wdi_pagination_container').find('#wdi_current_page').text()) + 1;
      if( parseInt(wdi_current_page) > currentFeed.paginator ) {
        return;
      }
      currentFeed.currentPage = parseInt(wdi_current_page);
      wdi_front.changePage(jQuery(this), currentFeed);
    });
    jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' #wdi_prev').on(wdi_front.clickOrTouch, function (){
      var wdi_current_page = parseInt(jQuery(this).parents('.wdi_pagination_container').find('#wdi_current_page').text()) - 1;
      if( parseInt(wdi_current_page) <= 0 ) {
        return;
      }
      currentFeed.currentPage = parseInt(wdi_current_page);
      wdi_front.changePage(jQuery(this), currentFeed);
    });
    jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' #wdi_last_page').on(wdi_front.clickOrTouch, function ()
    {
      currentFeed.currentPage = currentFeed.paginator;
      wdi_front.changePage(jQuery(this), currentFeed);
    });
    jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' #wdi_first_page').on(wdi_front.clickOrTouch, function ()
    {
      currentFeed.currentPage = 1;
      wdi_front.changePage(jQuery(this), currentFeed);
    });
    // setting pagiantion flags
    currentFeed.paginatorNextFlag = false;
  }
  if (currentFeed.feed_row.feed_display_view == 'infinite_scroll') {
    //binding infinite scroll Events
    jQuery(window).on('scroll', function ()
    {
      wdi_front.infiniteScroll(currentFeed);
    });
    //infinite scroll flags
    currentFeed.infiniteScrollFlag = false;
  }
}

wdi_front.infiniteScroll = function (currentFeed) {

  if ((jQuery(window).scrollTop() + jQuery(window).height() - 100) >= jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' #wdi_infinite_scroll').offset().top) {
    if ((currentFeed['dataStorageList'].length > (currentFeed['already_loaded_count']) || typeof currentFeed['already_loaded_count'] === 'undefined')) {
      currentFeed.infiniteScrollFlag = true;
      wdi_front.loadMore(jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' #wdi_infinite_scroll'), currentFeed);
    } else
      wdi_front.allImagesLoaded(currentFeed);

  }
}

wdi_front.changePage = function ( btn, currentFeed ) {
  new_page_number = currentFeed.currentPage;
  if ( new_page_number > 1 ) {
    btn.parents('.wdi_pagination').find("#wdi_first_page").removeClass("wdi_disabled");
  }
  else if ( new_page_number === 1 ) {
    btn.parents('.wdi_pagination').find("#wdi_first_page").addClass("wdi_disabled");
  }
  if ( new_page_number == parseInt(currentFeed.paginator) ) {
    btn.parents('.wdi_pagination').find("#wdi_last_page").addClass("wdi_disabled");
  }
  else if ( new_page_number < parseInt(currentFeed.paginator) ) {
    btn.parents('.wdi_pagination').find("#wdi_last_page").removeClass("wdi_disabled");
  }
  if ( currentFeed.feed_row.feed_type == 'masonry' ) {
    btn.parent().parent().parent().find(".wdi_feed_wrapper .wdi_masonry_column").empty();
  }
  else {
    btn.parent().parent().parent().find(".wdi_feed_wrapper").empty();
  }
  btn.parent().find("#wdi_current_page").empty().text(new_page_number);

  var feed_container = btn.closest(".wdi_feed_container");
      feed_container.parents('.wdi_feed_main_container').find(".wdi_front_overlay").removeClass("wdi_hidden");
      feed_container.parents('.wdi_feed_main_container').find(".wdi_page_loading").removeClass("wdi_hidden");
      wdi_front.displayFeed(currentFeed, new_page_number);
}

//displays proper images for specific page after pagination buttons click event
wdi_front.updatePagination = function (currentFeed, dir, oldPage) {
  var currentFeedString = '#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'];
  jQuery(currentFeedString + ' [wdi_page="' + currentFeed.currentPage + '"]').each(function ()
  {
    jQuery(this).removeClass('wdi_hidden');
  });
  switch (dir) {
    case 'next':
    {
      var oldPage = currentFeed.currentPage - 1;
      jQuery(currentFeedString + ' .wdi_feed_wrapper').height(jQuery('.wdi_feed_wrapper').height());
      jQuery(currentFeedString + ' [wdi_page="' + oldPage + '"]').each(function ()
      {
        jQuery(this).addClass('wdi_hidden');
      });
      break;
    }
    case 'prev':
    {
      var oldPage = currentFeed.currentPage + 1;
      jQuery(currentFeedString + ' .wdi_feed_wrapper').height(jQuery('.wdi_feed_wrapper').height());
      jQuery(currentFeedString + ' [wdi_page="' + oldPage + '"]').each(function ()
      {
        jQuery(this).addClass('wdi_hidden');
      });
      break;
    }
    case 'custom':
    {
      var oldPage = oldPage;
      if (oldPage != currentFeed.currentPage) {
        jQuery(currentFeedString + ' .wdi_feed_wrapper').height(jQuery('.wdi_feed_wrapper').height());
        jQuery(currentFeedString + ' [wdi_page="' + oldPage + '"]').each(function ()
        {
          jQuery(this).addClass('wdi_hidden');
        });
      }

      break;
    }
  }
  currentFeed.paginatorNextFlag = false;

  jQuery(currentFeedString + ' .wdi_feed_wrapper').css('height', 'auto');
  jQuery(currentFeedString + ' #wdi_current_page').text(currentFeed.currentPage);
}

wdi_front.loadMore = function (button, _currentFeed) {
  var dataCounter = 0;
  if (button != '' && typeof button != 'undefined' && button != 'initial' && button != 'initial-keep') {
    var currentFeed = window[button.parent().parent().parent().parent().attr('id')];
  }
  if ( typeof _currentFeed != 'undefined' ) {
    var currentFeed = _currentFeed;
  }

  wdi_front.ajaxLoader(currentFeed);
  if ( this.isJsonString(currentFeed.feed_row.feed_users) ) {
   json_feed_users = JSON.parse(currentFeed.feed_row.feed_users);
   for ( var i in json_feed_users ) {
     iuser = json_feed_users[i];
     if ( json_feed_users[i].username.charAt(0) !== '#' ) {
       iuser = json_feed_users[i];
     }
   }
  }

 //check if masonry view is on and and feed display type is pagination then
 //close all captions before loading more pages for porper pagination rendering
 if (currentFeed.feed_row.feed_type === 'masonry' && currentFeed.feed_row.feed_display_view == 'pagination') {
   jQuery('#wdi_feed_' + wdi_front.feed_counter + ' .wdi_full_caption').each(function () {
     jQuery(this).find('.wdi_photo_title').trigger(wdi_front.clickOrTouch);
   });
 }

  currentFeed.loadMoreDataCount = currentFeed.feed_users.length;
  wdi_front.displayFeed(currentFeed);
}

/*
* Requests images based on provided pagination url
*/
wdi_front.loadMoreRequest = function (user, next_url, currentFeed, button) {
  /*if there was no initial request, do not allow loadmore request */
  if (!currentFeed.mediaRequestsDone  || next_url == "") {
    return;
  }
  var usersData = currentFeed['usersData'];
  var errorMessage = '';
  /*sometimes (infinitescroll) loadMoreRequest is triggered before feed has any user data */
  var success_function = function (response) {
    if (response === '' || typeof response == 'undefined' || response == null) {
      errorMessage = wdi_front_messages.network_error;
      currentFeed.loadMoreDataCount--;
      wdi_front.show_alert(errorMessage, response, currentFeed);
      return;
    }
    if (typeof response.meta != 'undefined' && typeof response.meta.error_type != 'undefined') {
      wdi_front.show_alert(false, response, currentFeed);
    }
    if (typeof response.meta != 'undefined' && typeof response.meta.code != 'undefined' && response.meta.code != 200) {
      errorMessage = response['meta']['error_message'];
      currentFeed.loadMoreDataCount--;
      wdi_front.show_alert(errorMessage, response, currentFeed);
      return;
    }
    if (user['hashtag']) {
      response['user_id'] = user.hashtag_id;
      response['username'] = user.hashtag;
    } else {
      response['user_id'] = user.user_id;
      response['username'] = user.username;
    }

    for (var i = 0; i < currentFeed['usersData'].length; i++) {
      if (response['user_id'] === currentFeed['usersData'][i]['user_id'] || response['tag_id'] === currentFeed['usersData'][i]['tag_id']) {
        ///mmm!!!
        if (response['user_id'][0] === '#') {
          response['data'] = wdi_front.appendRequestHashtag(response['data'], response['user_id']);
        }
        ////////////////
        /*if button is initial-keep then we will lose currentFeed['usersData'][i]
         for not loosing it we keep it in currentFeed.temproraryUsersData, which value will be
         used later in wdi_front.checkForLoadMoreDone(), in other cases when button is set to
         initial we already keep data in that variable, so we don't deed to keep it again, it will give us duplicate value
         */
        if (button == 'initial-keep') {
          currentFeed.temproraryUsersData[i] = currentFeed.usersData[i];
        }
        currentFeed['usersData'][i] = response;
        if ( typeof currentFeed['dataStorageRaw'][i] === 'undefined') {
          currentFeed['dataStorageRaw'][i] = {data:response['data']};
        } else {
          currentFeed['dataStorageRaw'][i]['data'] = currentFeed['dataStorageRaw'][i]['data'].concat(response['data']);
        }
        currentFeed.loadMoreDataCount--;
      }
    }
    wdi_front.checkForLoadMoreDone(currentFeed, button);
  };
}

wdi_front.checkForLoadMoreDone = function (currentFeed, button) {
  var load_more_number = currentFeed.feed_row['load_more_number'];
  var number_of_photos = currentFeed.feed_row['number_of_photos'];

  if (currentFeed.loadMoreDataCount == 0) {

    currentFeed.temproraryUsersData = wdi_front.mergeData(currentFeed.temproraryUsersData, currentFeed.usersData);
    var gettedDataLength = wdi_front.getArrayContentLength(currentFeed.temproraryUsersData, 'data');
    /*this will happen when we call loadMore first time
     initial-keep is the same as initial except that if loadMore is called
     with initial-keep we store data on currentFeed.temproraryUsersData before checkLoadMoreDone()
     function call*/
    if (button == 'initial-keep') {
      button = 'initial';
    }
    //if button is set to inital load number_of_photos photos
    if (button == 'initial') {

      /*if existing data length is smaller then load_more_number then get more objects until desired number is reached
       also if it is not possible to reach the desired number (this will happen when all users has no more photos) then
       displayFeed()*/
      if (gettedDataLength < number_of_photos && !wdi_front.userHasNoPhoto(currentFeed, currentFeed.temproraryUsersData) && currentFeed.instagramRequestCounter <= currentFeed.maxConditionalFiltersRequestCount) {
        wdi_front.loadMore('initial', currentFeed);
      }
      else {
        currentFeed.usersData = currentFeed.temproraryUsersData;

        wdi_front.displayFeed(currentFeed);
        //when all data us properly displayed check for any active filters and then apply them
        wdi_front.applyFilters(currentFeed);

        //resetting temprorary users data array for the next loadmoer call
        currentFeed.temproraryUsersData = [];
      }
    }
    else {
      //else load load_more_number photos
      //if existing data length is smaller then load_more_number then get more objects until desired number is reached

      if (gettedDataLength < load_more_number && !wdi_front.userHasNoPhoto(currentFeed, currentFeed.temproraryUsersData) && currentFeed.instagramRequestCounter <= currentFeed.maxConditionalFiltersRequestCount) {
        wdi_front.loadMore(undefined, currentFeed);
      }
      else {
        currentFeed.usersData = currentFeed.temproraryUsersData;

        if (!wdi_front.activeUsersCount(currentFeed)) {
          return;
        }
        wdi_front.displayFeed(currentFeed, load_more_number);
        //when all data us properly displayed check for any active filters and then apply them
        wdi_front.applyFilters(currentFeed);

        //resetting temprorary users data array for the next loadmoer call
        currentFeed.temproraryUsersData = [];
      }
    }
  }
}

wdi_front.allDataHasFinished = function (currentFeed) {
  var c = 0;
  for (var j = 0; j < currentFeed.dataStorageRaw.length; j++) {
    if (currentFeed.usersData[j].pagination.next_url == '') {
      c++;
      currentFeed.usersData[j].finished = "finished";
    }
  }
  if (c == currentFeed.dataStorageRaw.length) {
    jQuery('#wdi_feed_' + currentFeed['feed_row']['wdi_feed_counter'] + ' .wdi_load_more').remove();
    return true;
  }
  return false;
}

wdi_front.mergeData = function (array1, array2) {
  for (var i = 0; i < array2.length; i++) {
    if (typeof array1[i] != 'undefined') {
      if (array2[i]['finished'] == 'finished') {
        continue;
      }

      //if user data is finished then dont add duplicate data
      if (typeof array1[i]['pagination']['next_max_id'] == 'undefined' &&
        typeof array1[i]['pagination']['next_max_like_id'] == 'undefined') {
        continue;
      }
      //extend data
      array1[i]['data'] = array1[i]['data'].concat(array2[i]['data']);
      array1[i]['pagination'] = array2[i]['pagination'];
      array1[i]['user_id'] = array2[i]['user_id'];
      array1[i]['username'] = array2[i]['username'];
      array1[i]['meta'] = array2[i]['meta'];
    } else {
      array1.push(array2[i]);
    }
  }
  return array1;
}

//broken image handling
wdi_front.brokenImageHandler = function (source) {
/* @ToDo. remove this function
  var url_params = source.src.split("/p/");
  if(typeof url_params[0] !== "undefined" && typeof url_params[1] !== "undefined" && url_params[0] !== "https://www.instagram.com"){
    var main_url = wdi_baseName(url_params[0]);
    var new_url = main_url+"/p/"+url_params[1];
    source.src = new_url;
  }else{
    source.src = wdi_url.plugin_url + "images/missing.png";
  }
  source.onerror = "";
*/
  return true;
}

function wdi_baseName(str) {
  var base = str.substr(str.lastIndexOf('/'));
  return str.replace(base, "");
}

//ajax loading
wdi_front.ajaxLoader = function (currentFeed) {
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var feed_container = jQuery(document).find('#wdi_feed_' + wdi_feed_counter);
  if (currentFeed.feed_row.feed_display_view == 'load_more_btn') {
    feed_container.find('.wdi_load_more').addClass('wdi_hidden');
    feed_container.find('.wdi_spinner').removeClass('wdi_hidden');
  }
  /////////////////////////////////////////////////////
  if (currentFeed.feed_row.feed_display_view == 'infinite_scroll') {
    var loadingDiv;
    if (feed_container.find('.wdi_ajax_loading').length == 0) {
      loadingDiv = jQuery('<div class="wdi_ajax_loading"><div><div><img class="wdi_load_more_spinner" src="' + wdi_url.plugin_url + 'images/ajax_loader.png"></div></div></div>');
      feed_container.find(".wdi_feed_container").append(loadingDiv);
    } else {
      loadingDiv = feed_container.find('.wdi_ajax_loading');
    }
    loadingDiv.removeClass('wdi_hidden');
  }
  return 1;
}

//if all images loaded then clicking load more causes it's removal
wdi_front.allImagesLoaded = function (currentFeed) {
  var dataLength = wdi_front.getDataLength(currentFeed);
  /*if there was no request for media, we do not know yet of feed data has been finished or not*/
  if(! currentFeed.mediaRequestsDone){
    jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper").remove("wdi_nomedia");
  }
  /* display message if feed contains no image at all */
  if (currentFeed.allResponseLength == 0 && currentFeed.dataLoaded === 1) {
    jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper").append("<p class='wdi_nomedia'>" + wdi_front_messages.feed_nomedia + "</p>");
  }

  //if all images loaded then enable load more button and hide spinner
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var feed_container = jQuery('#wdi_feed_' + wdi_feed_counter);

  if (currentFeed.feed_row.feed_display_view == 'load_more_btn') {
    if(parseInt(currentFeed.allResponseLength) > parseInt(currentFeed.feed_row.number_of_photos)) {
      feed_container.find('.wdi_load_more').removeClass('wdi_hidden');
    }
    feed_container.find('.wdi_spinner').addClass('wdi_hidden');
  }

  if (currentFeed.feed_row.feed_display_view == 'infinite_scroll') {
    jQuery('#wdi_feed_' + currentFeed.feed_row['wdi_feed_counter'] + ' .wdi_ajax_loading').addClass('wdi_hidden');
  }
}

//shows different parts of the feed based user choice
wdi_front.show = function (name, currentFeed) {
  var wdi_feed_counter = currentFeed.feed_row['wdi_feed_counter'];
  var feed_container = jQuery('#wdi_feed_' + wdi_feed_counter + ' .wdi_feed_container');
  var _this = this;
  switch (name) {
    case 'header': {
      show_header();
      break;
    }
    case 'users': {
      /* @ToDo API Changes 2020 */
      /* show_users(currentFeed); */
      break;
    }
  }

  function show_header() {
    var templateData = {
      'feed_thumb': currentFeed['feed_row']['feed_thumb'],
      'feed_name': currentFeed['feed_row']['feed_name'],
    };

    var headerTemplate = wdi_front.getHeaderTemplate(),
      html = headerTemplate(templateData),
      containerHtml = feed_container.find('.wdi_feed_header').html();
    feed_container.find('.wdi_feed_header').html(containerHtml + html);
  }
}

wdi_front.getUserTemplate = function (currentFeed, username) {
  var usersCount = currentFeed.dataCount,
    instagramLink, instagramLinkOnClick, js;

  switch (username[0]) {
    case '#':
    {
      instagramLink = '//instagram.com/explore/tags/' + username.substr(1, username.length);
      break;
    }
    default:
    {
      instagramLink = '//instagram.com/' + username;
      break;
    }
  }
  js = 'window.open("' + instagramLink + '","_blank")';
  instagramLinkOnClick = "onclick='" + js + "'";

  var source = '<div class="wdi_single_user" user_index="<%=user_index%>">' +
    '<div class="wdi_header_user_text <%=hashtagClass%>">' +

    '<div class="wdi_user_img_wrap">' +
    '<img onerror="wdi_front.brokenImageHandler(this);" src="<%= user_img_url%>">';
  if (usersCount > 1) {
    source += '<div  title="' + wdi_front_messages.filter_title + '" class="wdi_filter_overlay">' +
      '<div  class="wdi_filter_icon">' +
      '<span onclick="wdi_front.addFilter(<%=user_index%>,<%=feed_counter%>);" class="tenweb-i tenweb-i-filter"></span>' +
      '</div>' +
      '</div>';
  }
  source += '</div>';
  source += '<h3 ' + instagramLinkOnClick + '><%= user_name%></h3>';

  if (username[0] !== '#') {
    if (currentFeed.feed_row.follow_on_instagram_btn == '1') {
      source += '<div class="wdi_user_controls">' +
        '<div class="wdi_follow_btn" onclick="window.open(\'//instagram.com/<%= user_name%>\',\'_blank\')"><span> '+ wdi_front_messages.follow + '</span></div>' +
        '</div>';
    }
    source += '<div class="wdi_media_info">' +
      '<p class="wdi_posts"><span class="tenweb-i tenweb-i-camera-retro"></span><%= counts.media%></p>' +
      '<p class="wdi_followers"><span class="tenweb-i tenweb-i-user"></span><%= counts.followed_by%></p>' +
      '</div>';
  } else {
    source += '<div class="wdi_user_controls">' +
      '</div>' +
      '<div class="wdi_media_info">' +
      '<p class="wdi_posts"><span class="tenweb-i tenweb-i-camera-retro"></span><%= counts.media%></p>' +
      '<p class="wdi_followers"><span></span></p>' +
      '</div>';
  }
  source += '<div class="wdi_clear"></div>';

  if (usersCount == 1 && username[0] !== '#' && currentFeed.feed_row.display_user_info == '1') {
    source += '<div class="wdi_bio"><%= bio%></div>';
    source += '<div class="wdi_website"><a target="_blank" href="<%= website_url%>" ><%= website%></a></div>';
  }

  source += '</div>' +
    '</div>';

  var template = _.template(source);
  return template;
}

wdi_front.getHeaderTemplate = function () {
  var source = '<div class="wdi_header_wrapper">' +
    '<div class="wdi_header_img_wrap">' +
    '<img src="<%=feed_thumb%>">' +
    '</div>' +
    '<div class="wdi_header_text"><%=feed_name%></div>' +
    '<div class="wdi_clear">' +
    '</div>';
  var template = _.template(source);
  return template;
}

//sets user filter to true and applys filter to feed
wdi_front.addFilter = function (index, feed_counter) {
  var currentFeed = window['wdi_feed_' + feed_counter];
  var usersCount = currentFeed.dataCount;
  if (usersCount < 2) {
    return;
  }

  if (currentFeed.nowLoadingImages != false) {
    return;
  } else {

    var userDiv = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + '_users [user_index="' + index + '"]');
    userDiv.find('.wdi_filter_overlay').toggleClass('wdi_filter_active_bg');
    userDiv.find('.wdi_header_user_text h3').toggleClass('wdi_filter_active_col');
    userDiv.find('.wdi_media_info').toggleClass('wdi_filter_active_col');
    userDiv.find('.wdi_follow_btn').toggleClass('wdi_filter_active_col');

    currentFeed.customFilterChanged = true;
    //setting filter flag to true
    if (currentFeed.userSortFlags[index]['flag'] == false) {
      currentFeed.userSortFlags[index]['flag'] = true;
    } else {
      currentFeed.userSortFlags[index]['flag'] = false;
    }
    //getting active filter count
    var activeFilterCount = 0;
    for (var j = 0; j < currentFeed.userSortFlags.length; j++) {
      if (currentFeed.userSortFlags[j]['flag'] == true) {
        activeFilterCount++;
      }
    }

    if (currentFeed.feed_row.feed_display_view == 'pagination') {
      //reset responsive indexes because number of feed images may change after using filter
      currentFeed.resIndex = 0;
    }

    //applying filters
    if (activeFilterCount != 0) {
      wdi_front.filterData(currentFeed);
      wdi_front.displayFeed(currentFeed);
    } else {
      currentFeed.customFilteredData = currentFeed.dataStorageList;
      wdi_front.displayFeed(currentFeed);
    }

    if (currentFeed.feed_row.feed_display_view == 'pagination') {
      //reset paginator because while filtering images become more or less so pages also become more or less
      currentFeed.paginator = Math.ceil((currentFeed.imageIndex) / parseInt(currentFeed.feed_row.pagination_per_page_number));
      //setting current page as the last loaded page when filter is active
      currentFeed.currentPage = currentFeed.paginator; //pagination page number
      //when feed is displayed we are by default in the first page
      //so we are navigating from page 1 to current page using custom navigation method
      wdi_front.updatePagination(currentFeed, 'custom', 1);

      jQuery('#wdi_first_page').removeClass('wdi_disabled');
      jQuery('#wdi_last_page').addClass('wdi_disabled');
    }
  }
}

wdi_front.filterData = function (currentFeed) {
  var users = currentFeed.userSortFlags;
  currentFeed.customFilteredData = [];
  for (var i = 0; i < currentFeed.dataStorageList.length; i++) {
    for (var j = 0; j < users.length; j++) {
      if (((typeof currentFeed.dataStorageList[i]['user']['id'] != "undefined" && currentFeed.dataStorageList[i]['user']['id'] == users[j]['id']) || currentFeed.dataStorageList[i]['wdi_hashtag'] == users[j]['name']) && users[j]['flag'] == true) {
        currentFeed.customFilteredData.push(currentFeed.dataStorageList[i]);
      }

    }
  }

}

wdi_front.applyFilters = function (currentFeed) {
  for (var i = 0; i < currentFeed.userSortFlags.length; i++) {
    if (currentFeed.userSortFlags[i]['flag'] == true) {
      var userDiv = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + '[user_index="' + i + '"]');
      wdi_front.addFilter(i, currentFeed.feed_row.wdi_feed_counter);
      wdi_front.addFilter(i, currentFeed.feed_row.wdi_feed_counter);
    }
  }
}

//gets data Count from global storage
wdi_front.getImgCount = function (currentFeed) {
  var dataStorage = currentFeed.dataStorage;
  var count = 0;
  for (var i = 0; i < dataStorage.length; i++) {
    count += dataStorage[i].length;
  }
  return count;
}

// parses image data for lightbox popup
wdi_front.parseLighboxData = function (currentFeed, filterFlag) {
  var dataStorage = currentFeed.dataStorage;
  var data = [];

  var popupData = [];
  var obj = {};

  //if filterFlag is true, it means that some filter for frontend content is enabled so give
  //lightbox only those images which are visible at that moment else give all avialable
  for (var i = 0; i < dataStorage.length; i++) {
    for (var j = 0; j < dataStorage[i].length; j++) {
      data.push(dataStorage[i][j]);
    }
  }
  for (i = 0; i < data.length; i++) {
    if( typeof data[i] === 'undefined' ) {
      continue;
    }
    var thumb_url = (typeof data[i] !== 'undefined' && typeof data[i]['media_url'] !== 'undefined') ? data[i]['media_url'] : wdi_url.plugin_url + 'images/video_missing.png';
    var thumb_url = data[i]['thumbnail'];
    //todo what is this??????
    if ( typeof data[i] !== 'undefined' && typeof data[i]['media_url'] === 'undefined' ) {
      if ( data[i]['type'] === 'carousel' ) {
        var carousel_media = data[i]['carousel_media'][0];
        if ( typeof carousel_media !== 'undefined' && typeof carousel_media['images'] !== 'undefined' ) {
          thumb_url = carousel_media['thumbnail'];
        }
        else if (  typeof carousel_media !== 'undefined' && typeof carousel_media['videos'] !== 'undefined' ) {
          thumb_url = carousel_media['thumbnail'];
        }
      }
    }
    var comment_count = 0;
    var comments = ( typeof data[i]['comments']['count'] !== 'undefined' ) ? data[i]['comments']['count'] : data[i]['comments'];
    if( typeof data[i] !== 'undefined' && typeof comments !== "undefined" ) {
      comment_count = comments;
    }
    obj = {
      'alt': '',
      'avg_rating': '',
      'comment_count': comment_count,
      'date': wdi_front.convertUnixDate(data[i]['created_time']),
      'description': wdi_front.getDescription((typeof data[i]['caption'] !== 'undefined' && data[i]['caption'] !== null) ? wdi_front.escape_tags(data[i]['caption']['text']) : ''),
      'filename': wdi_front.getFileName(data[i]),
      'filetype': wdi_front.getFileType(data[i]),
      'hit_count': '0',
      'id': data[i]['id'],
      'image_url': data[i]['link'],
      'number': 0,
      'rate': '',
      'rate_count': '0',
      'username': (typeof data[i]['user'] !== 'undefined') ? data[i]['user']['username'] : '',
      'profile_picture': (typeof data[i]['user'] !== 'undefined') ? data[i]['user']['profile_picture'] : '',
      'thumb_url': thumb_url,
      'comments_data': (typeof data[i]['comments'] !== 'undefined') ? data[i]['comments']['data'] : '',
      'images': data[i]['images'] ? data[i]['images'] : data[i]['videos'],
      'carousel_media': (typeof data[i]['carousel_media'] !== "undefined") ? data[i]['carousel_media'] : null
    }
    popupData.push(obj);
  }
  return popupData;
}

wdi_front.convertUnixDate = function (date) {
  var utcSeconds = new Date(date).getTime() / 1000;
  var newDate = new Date(0);
  newDate.setUTCSeconds(utcSeconds);
  var str = newDate.getFullYear() + '-' + (newDate.getMonth()+1) + '-' + newDate.getDate();
  str += ' ' + newDate.getHours() + ':' + newDate.getMinutes();
  return str;
}

wdi_front.getDescription = function (desc) {
  desc = desc.replace(/\r?\n|\r/g, ' ');

  return desc;
}

/**
 * use this data for lightbox
 * **/

wdi_front.getFileName = function (data) {
  if( typeof data !== 'undefined' ) {
    var link = data['link'];
    var type = data['type'];
    /*if pure video, not carousel*/
    if (type === 'video' && data.hasOwnProperty('videos') && data['videos']['standard_resolution'] != null) {
      return data['videos']['standard_resolution']['url'];
    }
    else {
      if ( typeof link !== 'undefined' ) {
        var linkFragments = link.split('/');
        return linkFragments[linkFragments.length - 2];
      }
      return '';
    }
  }
}

wdi_front.getFileType = function (data) {
  /*if pure video, not carousel*/
  //@ToDo old if (data['type'] == 'video' && data.hasOwnProperty('videos')) {
  if (data['type'] == 'video' && data.hasOwnProperty('videos')) {
    return "EMBED_OEMBED_INSTAGRAM_VIDEO";
  } else if(data['type'] == 'carousel' && data.hasOwnProperty('carousel_media')) {
    return "EMBED_OEMBED_INSTAGRAM_CAROUSEL";
  } else {
    return "EMBED_OEMBED_INSTAGRAM_IMAGE";
  }
}

wdi_front.array_max = function (array) {
  var max = array[0];
  var minIndex = 0;
  for (var i = 1; i < array.length; i++) {
    if (max < array[i]) {
      max = array[i];
      minIndex = i;
    }
  }
  return {
    'value': max,
    'index': minIndex
  };
}

wdi_front.array_min = function (array) {
  var min = array[0];
  var minIndex = 0;
  for (var i = 1; i < array.length; i++) {
    if (min > array[i]) {
      min = array[i];
      minIndex = i;
    }
  }
  return {
    'value': min,
    'index': minIndex
  };
}

/*
 * Returns users count whose feed is not finished
 */
wdi_front.activeUsersCount = function (currentFeed) {
  var counter = 0;
  for (var i = 0; i < currentFeed.usersData.length; i++) {
    if (currentFeed.usersData[i].finished != 'finished') {
      counter++;
    }
  }

  return counter;
}

/**
 * Return response if it is valid else returns boolean false
 * @param  {Object} response [instagram API response]
 * @return {Object or Boolean}          [false: if invalid response, object: if valid]
 */
wdi_front.checkMediaResponse = function (response, currentFeed) {
  if (response == '' || typeof response == 'undefined' || response == null || typeof response.error !== 'undefined') {
    errorMessage = wdi_front_messages.connection_error;
    wdi_front.show_alert(errorMessage, response, currentFeed);
    return false;
  }
  if ( response != '' && typeof response != 'undefined' && response != null && typeof response['meta'] !== "undefined" && response['meta']['code'] != 200) {
    errorMessage = response['meta']['error_message'];
    wdi_front.show_alert(errorMessage, response, currentFeed);
    return false;
  }

  return response;
}

/**
 * Removes # from string if it is first char
 * @param  {String} hashtag
 * @return {String}
 */
wdi_front.stripHashtag = function (hashtag) {
  switch (hashtag[0]) {
    case '#':
    {
      return hashtag.substr(1, hashtag.length);
      break;
    }
    default:
    {
      return hashtag;
      break;
    }
  }
}

/**
 * Returns type of given input
 * @param  {String} input [this is username or hashtag]
 * @return {String}       [input type]
 */
wdi_front.getInputType = function (input) {
  switch (input[0]) {
    case '#':
    {
      return 'hashtag';
      break;
    }
    case '%':
    {
      return 'location';
      break;
    }
    default:
    {
      return 'user';
      break;
    }
  }
}

/**
 * Makes a regex search of a given word returns true if symbol before and after word is space
 * or word is in the beggining or in the end of string
 * @param  {String} captionText [String where search needs to be done]
 * @param  {String} searchkey   [word or phrazee to search]
 * @return {Boolean}
 */
wdi_front.regexpTestCaption = function (captionText, searchkey) {
  var flag1 = false,
    flag2 = false,
    matchIndexes = [],
    escKey = searchkey.replace(/[-[\]{}()*+?.,\\^$|]/g, "\\$&"),
    regexp1 = new RegExp("(?:^|\\s)" + escKey + "(?:^|\\s)"),
    regexp2 = new RegExp("(?:^|\\s)" + escKey, 'g');
  if (regexp1.exec(captionText) != null) {
    flag1 = true;
  }

  while (( match = regexp2.exec(captionText) ) != null) {
    //if (match.index == captionText.length - searchkey.length - 1) {
    flag2 = true;
    //}
  }

  if (flag1 == true || flag2 == true) {
    return true;
  } else {
    return false;
  }

}

/**
 * replaces single new-lines with space
 * if multiple new lines are following each other then replaces all newlines with single space
 * @param  {String} string [input string]
 * @return {String}        [output string]
 */
wdi_front.replaceNewLines = function (string) {
  var delimeter = "vUkCJvN2ps3t",
    matchIndexes = [],
    regexp;
  string = string.replace(/\r?\n|\r/g, delimeter);

  regexp = new RegExp(delimeter, 'g');
  while (( match = regexp.exec(string) ) != null) {
    matchIndexes.push(match.index);
  }

  var pieces = string.split(delimeter);
  var foundFlag = 0;

  for (var i = 0; i < pieces.length; i++) {

    if (pieces[i] == '') {
      foundFlag++;
    } else {
      foundFlag = 0;
    }

    if (foundFlag > 0) {
      pieces.splice(i, 1);
      foundFlag--;
      i--;
    }

  }
  string = pieces.join(' ');
  return string;
}

wdi_front.isEmptyObject = function (obj) {
  for (var prop in obj) {
    if (obj.hasOwnProperty(prop))
      return false;
  }
  return true
}

wdi_front.isEmpty = function (str) {
  return (!str || 0 === str.length);
}

var WDIFeed = function (obj) {
  this['data'] = obj['data'];
  this['dataCount'] = obj['dataCount'];
  this['feed_row'] = obj['feed_row'];
  this['usersData'] = obj['usersData'];
  _this = this;

  this.set_images_loading_flag = function (_this)
  {
    window.addEventListener('load', function ()
    {
      _this.nowLoadingImages = false;
    });
  }

  this.set_images_loading_flag(_this);
};

WDIFeed.prototype.mediaExists = function (media, array) {

  for (var i = 0; i < array.length; i++) {
    if (media['id'] == array[i]['id']) {
      return true;
    }
  }
  return false;
}

/**
 * gets id of media from url, this id is not the one which comes with api request
 * @param  {String} url [media url]
 * @return {String}
 */
WDIFeed.prototype.getIdFromUrl = function (url) {
  var url_parts = url.split('/'),
    id = false;
  for (var i = 0; i < url_parts.length; i++) {
    if (url_parts[i] == 'p') {
      if (typeof url_parts[i + 1] != 'undefined') {
        id = url_parts[i + 1];
        break;
      }
    }
  }
  ;
  return id;
}

/**
 * Iterates throught response data and remove duplicate media
 * @param  {Object} response [Instagram API request]
 * @return {Object}          [response]
 */
WDIFeed.prototype.avoidDuplicateMedia = function (response) {
  var data = response['data'],
    uniqueData = [],
    returnObject = {};
  if (typeof data == "undefined") {
    data = [];
  }

  for (var i = 0; i < data.length; i++) {
    if (!this.mediaExists(data[i], this.dataStorageList) && !this.mediaExists(data[i], uniqueData) && !this.mediaExists(data[i], this.conditionalFilterBuffer)) {
      uniqueData.push(data[i]);
    }
  }

  this.conditionalFilterBuffer = this.conditionalFilterBuffer.concat(uniqueData);

  returnObject = {
    data: uniqueData,
    meta: response['meta'],
    pagination: response['pagination']
  }

  return returnObject;

}

/* stores data from objects array into global variable */
WDIFeed.prototype.storeRawData = function (objects, variable) {
  var _this = this;
  if (typeof this[variable] == "object" && typeof this[variable].length == "number") {
    //checks if in golbal storage user already exisit then it adds new data to user old data
    //else it simple puches new user with it's data to global storage
    for (var i = 0; i < objects.length; i++) {


      var hash_id = "";
      if (wdi_front.isHashtag(objects[i].user_id)) {
        if(typeof objects[i].pagination.cursors !== "undefined") {
          hash_id = objects[i].pagination.cursors.after;
        }
      }
      else
      if (_this.feed_row.liked_feed == 'liked') {
        hash_id = objects[i].pagination.next_max_like_id;
        if (typeof hash_id == "undefined") {
          hash_id = "";
        }
      }
      else {

        /*strange bug sometimes happening in instagram API when user feed pagination is null*/
        if (objects[i].pagination == null) {
          objects[i].pagination = [];
        }

        hash_id = objects[i].pagination.next_max_id;
        if (typeof hash_id == "undefined") {
          hash_id = "";
        }


      }

      if (typeof this[variable][i] == "undefined") {
        this[variable].push({
          data: objects[i].data,
          index: 0,
          locked: false,
          hash_id: hash_id,
          usersDataFinished: false,
          userId: objects[i].user_id,
          length: function ()
          {
            return this.data.length - this.index;
          },
          getData: function (num)
          {
            var data = this.data.slice(this.index, this.index + num);
            this.index += Math.min(num, this.length());

            if (this.index == this.data.length && this.locked == true && this.usersDataFinished == false) {

              for (var j = 0; j < _this.usersData.length; j++) {
                if (_this.usersData[j]['user_id'] == this.userId) {
                  this.usersDataFinished = true;
                  break;
                }
              }
            }
            return data;
          }
        });
      } else {
        if (this[variable][i].locked == false) {

          if (hash_id != this[variable][i].hash_id) {
            this[variable][i].data = this[variable][i].data.concat(objects[i].data);
            this[variable][i].hash_id = hash_id;
          } else {
            this[variable][i].locked = true;

          }
        }

      }
    }
  }

}

wdi_front.updateUsersIfNecessary = function (currentFeed) {
  var users = currentFeed.feed_users;
  var ifUpdateNecessary = false;

  for (var i = 0; i < users.length; i++) {
    if ("#" == users[i].username.substr(0, 1)) {
      users[i].id = users[i].username;
      continue;
    }
    if ("" == users[i].id || 'username' == users[i].id) {

      ifUpdateNecessary = true;
      currentFeed.instagram.searchForUsersByName(users[i].username, {
        success: function (res)
        {

          if(typeof res.meta!= "undefined" && typeof res.meta.error_type != "undefined"){
            wdi_front.show_alert(false, res, currentFeed);
          }
          if (res.meta.code == 200 && res.data.length > 0) {

            var found = false;

            for (var k = 0; k < res.data.length; k++) {
              if (res.data[k].username == res.args.username) {
                found = true;
                break;
              }
            }

            if (found) {
              for (var j = 0; j < users.length; j++) {
                if (res.data[k].username == users[j].username) {
                  users[j].id = res.data[k].id;
                }
              }
            }


          }

          var noid_user_left = false;
          for (var m = 0; m < users.length; m++) {
            if (users[m].id == "" || users[m].id == "username") {
              noid_user_left = true;
              break;
            }
          }
          if (!noid_user_left) {
            currentFeed.feed_row.feed_users = JSON.stringify(users);
            wdi_front.init(currentFeed);
          }

        },
        username: users[i].username
      });
    }
  }

  return ifUpdateNecessary;
}

if (typeof wdi_ajax.ajax_response != "undefined") {
  jQuery(document).one('ajaxStop', function ()
  {
    if (wdi_front['type'] != 'not_declared') {

      wdi_front.clickOrTouch = wdi_front.detectEvent();
      //initializing all feeds in the page
      wdi_front.globalInit();
    } else {
      return;
    }
  });


}
else {
  jQuery(document).ready(function () {
    if (wdi_front['type'] != 'not_declared') {
      wdi_front.clickOrTouch = wdi_front.detectEvent();
      //initializing all feeds in the page
      wdi_front.globalInit();
    } else {
      return;
    }
  });
}

jQuery(document).ready(function () {
  setTimeout(function(){
    if(wdi_front_messages.show_alerts === '1' && jQuery('.wdi_check_fontawesome .tenweb-i-instagram').prop("tagName") !== 'I'){
      console.log('Font Awesome is not loaded properly. Please ask for support https://wordpress.org/support/plugin/wd-instagram-feed/');
    }
  }, 2000);
});

function wdi_extractHostname(url) {
  if(typeof url === "undefined" || url===""){
    return "";
  }
  var result = url.replace(/(^\w+:|^)\/\//, '');

  return result;
}
