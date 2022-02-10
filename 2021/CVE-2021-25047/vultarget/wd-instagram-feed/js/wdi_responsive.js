/////////////////////Responsive////////////////////
jQuery(document).ready(function ()
{

});

function wdi_responsive()
{
};

/*
 * Calculates current column layout and gives proper column classes
 */
wdi_responsive.columnControl = function (currentFeed, load, customWidth) {
  currentFeed.openRows = [];
  if (load === 1) {
    var wrapper = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper");
    var itemWidth = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_item").css('width') + '';
    var containerWidth = wrapper.first().width();

    if (itemWidth.substr(itemWidth.length - 2, itemWidth.length) == 'px') {
      itemWidth = parseFloat(itemWidth);
    } else {
      itemWidth = 0.01 * containerWidth * parseFloat(itemWidth);
    }

    if (currentFeed.feed_row.feed_type == 'masonry') {
      var itemWidth = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_masonry_column").first().width();
    }

    if (customWidth != undefined) {
      itemWidth = customWidth;
    }
    var currentClass = wrapper.attr('wdi-res');
    var newClass = currentClass.substr(0, 8) + Math.round(containerWidth / itemWidth);
    wrapper.removeClass(currentClass);
    wrapper.attr('wdi-res', newClass);
    wrapper.addClass(newClass);
    //////////////////////////////////////////
    if (currentClass != newClass) {
      var colNum = newClass.substr(8, newClass.length);
      //updating free spaces for pagination view
      if (currentFeed.feed_row.feed_display_view == 'pagination') {
        currentFeed.freeSpaces = (Math.floor(currentFeed.feed_row.pagination_per_page_number / Math.round(containerWidth / itemWidth)) + 1) * Math.round(containerWidth / itemWidth) - currentFeed.feed_row.pagination_per_page_number;
        if (currentFeed.freeSpaces % colNum == 0) {
          currentFeed.freeSpaces = 0;
        }
        //updating pagination indexes for new layout
        currentFeed.resIndex = 0;
        var k = 0;
        jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_feed_item').each(function ()
        {
          jQuery(this).attr('wdi_res_index', currentFeed.resIndex);
          if ((k + 1) % currentFeed.feed_row.pagination_per_page_number === 0) {
            currentFeed.resIndex += currentFeed.freeSpaces + 1;
          } else {
            currentFeed.resIndex++;
          }
          k++;
        });

      }
    }
  }
  else {
    jQuery(window).resize(function () {
      var wrapper = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_wrapper");
      if (wrapper.length == 0) {
        return;
      }
      var itemWidth = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_feed_item").css('width') + '';
      var containerWidth = wrapper.first().width();

      if (itemWidth.substr(itemWidth.length - 2, itemWidth.length) == 'px') {
        itemWidth = parseFloat(itemWidth);
      } else {
        itemWidth = 0.01 * containerWidth * parseFloat(itemWidth);
      }

      var currentClass = wrapper.attr('wdi-res');
      //check if layout was changed then reposition masonry
      if (currentFeed.feed_row.feed_type == 'masonry') {
        var itemWidth = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + " .wdi_masonry_column").first().width();
      }
      if( itemWidth === 0 ) {
        itemWidth = containerWidth;
      }
      var newClass = currentClass.substr(0, 8) + Math.round(containerWidth / itemWidth);
      wrapper.removeClass(currentClass);
      wrapper.attr('wdi-res', newClass);
      wrapper.addClass(newClass);

      //Feed type based configurations
      if (currentFeed.feed_row.feed_type === 'thumbnails') {
        //fixes row which was opened by user
        wdi_responsive.fixRow(currentFeed);
      }
      if (currentFeed.feed_row.feed_type === 'masonry') {
        //checking if layout changed then change masonry columns
        if (currentClass != newClass) {
          wdi_front.ajaxLoader(currentFeed);
          var colNum = newClass.substr(8, newClass.length);

          //clearing wrapper adn adding blank colums
          wrapper.html('');
          var newCols = '';
          for (var i = 0; i < colNum; i++) {
            newCols += '<div class="wdi_masonry_column" wdi_mas_col="' + i + '"></div>';
          }
          newCols += '<div class="wdi_clear">';
          wrapper.html(newCols);

          //resetting index variables
          currentFeed.imageIndex = 0;
          currentFeed.resIndex = 0;

          //inserting content again

          //fix sorting issue;
          var dataToBeDisplayed = currentFeed.displayedData;
          currentFeed.displayedData = [];
          wdi_front.masonryDisplayFeedItems(dataToBeDisplayed, currentFeed);
          wdi_front.applyFilters(currentFeed);
          ///wdi_front.allImagesLoaded(currentFeed);
        }
      }
      //////////////////////////////////////////
      if (currentClass != newClass) {
        var colNum = newClass.substr(8, newClass.length);
        //updating free spaces for pagination view
        if (currentFeed.feed_row.feed_display_view == 'pagination') {
          currentFeed.freeSpaces = (Math.floor(currentFeed.feed_row.number_of_photos / Math.round(containerWidth / itemWidth)) + 1) * Math.round(containerWidth / itemWidth) - currentFeed.feed_row.number_of_photos;
          if (currentFeed.freeSpaces % colNum == 0) {
            currentFeed.freeSpaces = 0;
          }
          //updating pagination indexes for new layout
          currentFeed.resIndex = 0;
          var k = 0;
          jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_feed_item').each(function ()
          {
            jQuery(this).attr('wdi_res_index', currentFeed.resIndex);
            if ((k + 1) % currentFeed.feed_row.pagination_per_page_number === 0) {
              currentFeed.resIndex += currentFeed.freeSpaces + 1;
            } else {
              currentFeed.resIndex++;
            }
            k++;
          });
        }
      }
    });
  }
};
wdi_responsive.bindCaptionEvent = function (imgtitle, currentFeed)
{
  //if(typeof imgtitle == "undefined") return;

  // imgtitle.on('click',function(e){
  // 	wdi_responsive.showCaption(jQuery(this),currentFeed);
  // })
}
wdi_responsive.bindMasonryCaptionEvent = function (imgtitle, currentFeed)
{
  // imgtitle.on('click',function(){
  // 	wdi_responsive.showMasonryCaption(jQuery(this),currentFeed);
  // });
}
wdi_responsive.showCaption = function (caption, currentFeedCounter)
{
  var currentFeed = window["wdi_feed_" + currentFeedCounter]
  var imgItem = caption.parent().parent();
  if (currentFeed.feed_row.feed_display_view === 'pagination') {
    var indexType = 'wdi_res_index';
  } else {
    var indexType = 'wdi_index';
  }
  var imgIndex = imgItem.attr(indexType);
  var colClass = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_feed_wrapper').attr('wdi-res');
  var colNum = parseInt(colClass.substr(8, colClass.length));
  var imgBeforRows = Math.floor(imgIndex / colNum);
  var indexInRow = imgIndex - colNum * imgBeforRows;

  var indexes = [];

  for (var i = 0; i < colNum; i++) {
    var rowIndex = i + (imgBeforRows) * colNum;
    indexes.push(rowIndex);
  }

  if (imgItem.hasClass('wdi_full_caption')) {
    caption.css('white-space', 'nowrap');
    imgItem.removeClass('wdi_full_caption');
  } else {
    caption.css('white-space', 'normal');
    imgItem.addClass('wdi_full_caption');
    imgItem.attr('wdi_scroll_to', imgItem.offset().top - 50);
  }
  imgItem.css('height', 'auto');

  //find maximum height in row
  var maxHeight = 0;
  for (var i = 0; i < indexes.length; i++) {
    var currentItem = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_feed_wrapper [' + indexType + '=' + indexes[i] + ']');
    currentItem.addClass('wdi_row_affected');
    currentItem.css('height', 'auto');
    if (maxHeight < currentItem.height() && currentItem.hasClass('wdi_full_caption')) {
      maxHeight = currentItem.height();
    }
  }

  if (maxHeight == 0) {
    maxHeight = imgItem.height();
  }

  for (var i = 0; i < indexes.length; i++) {
    var currentItem = jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_feed_wrapper [' + indexType + '=' + indexes[i] + ']');
    currentItem.height(maxHeight);
  }
  currentFeed.affectedRow = true;

}
wdi_responsive.fixRow = function (currentFeed) {
  jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_row_affected').each(function ()
  {
    jQuery(this).css('height', 'auto');
    jQuery(this).removeClass('wdi_row_affected');
  });
  if (currentFeed.feed_row.feed_display_view === 'pagination') {
    jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_full_caption').each(function ()
    {
      if (!jQuery(this).hasClass('wdi_hidden')) {
        //triggering two times one time for fixing row second for bringing back old state
        jQuery(this).find('.wdi_photo_title').trigger(wdi_front.clickOrTouch);
        jQuery(this).find('.wdi_photo_title').trigger(wdi_front.clickOrTouch);
      }
    });
  }
  else {
    jQuery('#wdi_feed_' + currentFeed.feed_row.wdi_feed_counter + ' .wdi_full_caption').each(function ()
    {
      //triggering two times one time for fixing row second for bringing back old state
      jQuery(this).find('.wdi_photo_title').trigger(wdi_front.clickOrTouch);
      jQuery(this).find('.wdi_photo_title').trigger(wdi_front.clickOrTouch);
    });
  }
}

wdi_responsive.showMasonryCaption = function (caption, currentFeedCounter) {
  var currentFeed = window["wdi_feed_" + currentFeedCounter];

  var imgItem = caption.parent().parent();

  if (imgItem.hasClass('wdi_full_caption')) {
    caption.css('white-space', 'nowrap');
    imgItem.removeClass('wdi_full_caption');
    //jQuery('body, html').animate({scrollTop:imgItem.attr('wdi_scroll_to')}, '500');
  } else {
    imgItem.attr('wdi_scroll_to', imgItem.offset().top - 50);
    caption.css('white-space', 'normal');
    imgItem.addClass('wdi_full_caption');
  }
}