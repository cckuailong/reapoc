jQuery(window).on('load',function(){
  window.bwgDocumentReady = true;
  if (window.bwgTinymceRendered) {
    jQuery(document).trigger("onUploadImg");
  }
});

(function () {
  var width_window;
  var height_window;
  tinymce.create('tinymce.plugins.bwg_mce', {
    init: function (ed, url) {
      var c = this;
      c.url = url;
      c.editor = ed;
      width_window = jQuery(window).width() + 17;
      height_window = jQuery(window).height();
      ed.addCommand('mcebwg_mce', function () {
        if ( jQuery(".bwg-shortcode-btn:visible").length == 1 ) {
          jQuery('.bwg-shortcode-btn:visible').trigger('click');
          return;
        }
        else {
          ed.windowManager.open({
            file: bwg_admin_ajax,
            width: width_window,
            height: height_window,
            inline: 1,
            title: 'Photo Gallery'
          }, {
            plugin_url: url
          });
          var window = ed.windowManager.windows[ed.windowManager.windows.length - 1],
            $window = window.$el;
          $window.css({
            maxWidth: "100%",
            maxHeight: "100%"
          });
          $window.find(".mce-window-body").css({
            maxWidth: "100%",
            maxHeight: "100%"
          });
          $window.find(".mce-container-body").find("iframe").css({
            width: '1px',
            minWidth: '100%',
          });
          var e = ed.selection.getNode(), d = wp.media.gallery, f;
          if (typeof wp === "undefined" || !wp.media || !wp.media.gallery) {
            return
          }
          if (e.nodeName != "IMG" || ed.dom.getAttrib(e, "class").indexOf("bwg_shortcode") == -1) {
            return
          }
          f = d.edit("[" + ed.dom.getAttrib(e, "title") + "]");
        }
      });
      ed.addButton('bwg_mce', {
        id:'mceu_bwg_shorcode',
        title:'Insert Photo Gallery',
        cmd:'mcebwg_mce',
        image: url + '/images/bwg_edit_but.png'
      });
      ed.onPostRender.add(function(ed, cm) {
         window.bwgTinymceRendered = true;
         if ( window.bwgDocumentReady ) {
            jQuery(document).trigger("onUploadImg");
         }
      });
      ed.onMouseUp.add(function (d, f) {
        if (f.target.nodeName == "IMG" && d.dom.hasClass(f.target, "bwg_shortcode")) {
          var g = tinymce.activeEditor;
          g.wpGalleryBookmark = g.selection.getBookmark("simple");
          g.execCommand("mcebwg_mce");
        }
      });
      ed.onBeforeSetContent.add(function (d, e) {
        e.content = c._do_bwg(e.content)
      });
      ed.onPostProcess.add(function (d, e) {
        if (e.get) {
          e.content = c._get_bwg(e.content)
        }
      })
    },
    _do_bwg:function (ed) {
      return ed.replace(/\[Best_Wordpress_Gallery([^\]]*)\]/g, function (d, c) {
        return '<img src="' + bwg_plugin_url + '/images/tw-gb/shortcode_new.jpg" class="bwg_shortcode mceItem" title="Best_Wordpress_Gallery' + tinymce.DOM.encode(c) + '" />';
      })
    },
    _get_bwg:function (b) {
      function ed(c, d) {
        d = new RegExp(d + '="([^"]+)"', "g").exec(c);
        return d ? tinymce.DOM.decode(d[1]) : "";
      }

      return b.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function (e, d) {
        var c = ed(d, "class");
        if (c.indexOf("bwg_shortcode") != -1) {
          return "<p>[" + tinymce.trim(ed(d, "title")) + "]</p>"
        }
        return e
      })
    }
  });
  tinymce.PluginManager.add('bwg_mce', tinymce.plugins.bwg_mce);
  if ( typeof bwg_set_shortcode_popup_dimensions == "function" ) {
    bwg_set_shortcode_popup_dimensions();
  }
})()