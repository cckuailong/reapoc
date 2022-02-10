
var adminAjaxUrl = popb_admin_vars_data.page_ajax_url;
var POPB_data_nonce = popb_admin_vars_data.pluginops_nonce;
var P_ID = popb_admin_vars_data.post_id;
var isPremActive = popb_admin_vars_data.isPremActive;

var pageBuilderApp = {};
pageBuilderApp.currentlyEditedColId = '';
pageBuilderApp.currentlyEditedWidgId = '';
pageBuilderApp.currentlyEditedThisCol = '';
pageBuilderApp.currentlyEditedThisRow = '';
pageBuilderApp.animateWidgetId = '';
pageBuilderApp.copiedSecOps = '';
pageBuilderApp.copiedColOps = '';
pageBuilderApp.copiedWidgOps = '';
pageBuilderApp.copiedInlineOps = '';
pageBuilderApp.setFeaturedImageIfEmpty = '';
pageBuilderApp.isColumnAction = false;
pageBuilderApp.undoRedoColDragWidth = false;

if (isPremActive == 'true') {
  pageBuilderApp.premActive = 'true';
}else{
  pageBuilderApp.premActive = 'false';
}


var URLL = adminAjaxUrl + "?action=ulpb_admin_data&page_id="+P_ID+"&POPB_nonce="+POPB_data_nonce;

var PageBuilderAdminImageFolderPath = popb_admin_vars_data.plugin_url + "/images/menu/";

var P_ID = P_ID;

var PageBuilder_Version = popb_admin_vars_data.plugin_version;

var admURL = popb_admin_vars_data.admin_url;

var siteURLpb = popb_admin_vars_data.site_url;

var isPub = popb_admin_vars_data.post_status;

var thisPostType = popb_admin_vars_data.post_type;

var pluginURL  = popb_admin_vars_data.plugin_url;

var admEMail = popb_admin_vars_data.user_email;

var isMCActive = isPremActive;

var isGlobalRowActive = isPremActive;

var POPB_data_nonce = POPB_data_nonce;

var shortCodeRenderWidgetNO = POPB_data_nonce;





pageBuilderApp.tinymce =  {
  wpautop: true ,
  theme: 'modern' ,
  skin: 'lightgray' ,
  language: 'en' ,
  formats: {
    alignleft: [
      {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li' , styles: {textAlign: 'left' }},
      {selector: 'img, table, dl.wp-caption' , classes: 'alignleft' }
    ],
    aligncenter: [
      {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li' , styles: {textAlign: 'center' }},
      {selector: 'img, table, dl.wp-caption' , classes: 'aligncenter' }
    ],
    alignright: [
      {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li' , styles: {textAlign: 'right' }},
      {selector: 'img, table, dl.wp-caption' , classes: 'alignright' }
    ],
    strikethrough: {inline: 'del' }
  },
  relative_urls: false ,
  remove_script_host: false ,
  convert_urls: false ,
  browser_spellcheck: true ,
  fix_list_elements: true ,
  entities: '38, amp, 60, lt, 62, gt ' ,
  entity_encoding: 'raw' ,
  keep_styles: false ,
  paste_webkit_styles: 'font-weight font-style color' ,
  preview_styles: 'font-family font-size font-weight font-style text-decoration text-transform' ,
  tabfocus_elements: ': prev ,: next' ,
  plugins: 'charmap, hr, media, paste, tabfocus, textcolor, fullscreen, wordpress, wpeditimage, wpgallery, wplink, wpdialogs, wpview' ,
  resize: 'vertical' ,
  menubar: false ,
  indent: false ,
  toolbar1: 'bold, italic, strikethrough, bullist, numlist, blockquote, hr, alignleft, aligncenter, alignright,alignjustify, link, wp_adv' ,
  toolbar2: 'formatselect, underline, forecolor,backcolor, pastetext, removeformat, outdent, indent, wp_help' ,
  toolbar3: '' ,
  toolbar4: '' ,
  body_class: 'id post-type-post post-status-publish post-format-standard' ,
  wpeditimage_disable_captions: false ,
  wpeditimage_html5_captions: true,
  height:'380',

};



pageBuilderApp.tinymceHeadingWidget =  {
  wpautop: true ,
  theme: 'modern' ,
  skin: 'lightgray' ,
  language: 'en' ,
  formats: {
    strikethrough: {inline: 'del' }
  },
  relative_urls: false ,
  remove_script_host: false ,
  convert_urls: false ,
  browser_spellcheck: true ,
  fix_list_elements: true ,
  entities: '38, amp, 60, lt, 62, gt ' ,
  entity_encoding: 'raw' ,
  keep_styles: false ,
  paste_webkit_styles: 'font-weight font-style color' ,
  preview_styles: 'font-family font-size font-weight font-style text-decoration text-transform' ,
  tabfocus_elements: ': prev ,: next' ,
  plugins: 'paste, tabfocus, textcolor, wordpress, wplink, wpdialogs, wpview' ,
  resize: 'vertical' ,
  menubar: false ,
  indent: false ,
  toolbar1: 'bold, italic, strikethrough, underline, link, spellchecker,forecolor,backcolor ',
  toolbar2: '' ,
  toolbar3: '' ,
  toolbar4: '' ,
  body_class: 'id post-type-post post-status-publish post-format-standard' ,
  wpeditimage_disable_captions: false ,
  wpeditimage_html5_captions: true,
  height:'125',
  width:'350',
  forced_root_block:false,
};











