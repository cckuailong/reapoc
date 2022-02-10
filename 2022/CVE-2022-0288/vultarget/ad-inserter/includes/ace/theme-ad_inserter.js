ace.define("ace/theme/ad_inserter",["require","exports","module","ace/lib/dom"], function(require, exports, module) {

exports.isDark = true;
exports.cssClass = "ace-ad-inserter";
exports.cssText = ".ace-ad-inserter .ace_gutter {\
background: #272727;\
color: #CCC\
}\
.ace-ad-inserter .ace_print-margin {\
width: 1px;\
background: #272727\
}\
.ace-ad-inserter {\
background-color: #2D2D2D;\
color: #DDDDDD\
}\
.ace-ad-inserter .ace_constant.ace_other,\
.ace-ad-inserter .ace_cursor {\
color: #CCCCCC\
}\
.ace-ad-inserter .ace_marker-layer .ace_selection {\
background: #515151\
}\
.ace-ad-inserter.ace_multiselect .ace_selection.ace_start {\
box-shadow: 0 0 3px 0px #2D2D2D;\
border-radius: 2px\
}\
.ace-ad-inserter .ace_marker-layer .ace_step {\
background: rgb(102, 82, 0)\
}\
.ace-ad-inserter .ace_marker-layer .ace_bracket {\
margin: -1px 0 0 -1px;\
border: 1px solid #FF00FF\
}\
.ace-ad-inserter .ace_stack {\
background: rgb(66, 90, 44)\
}\
.ace-ad-inserter .ace_marker-layer .ace_active-line {\
background: #393939\
}\
.ace-ad-inserter .ace_gutter-active-line {\
background-color: #393939\
}\
.ace-ad-inserter .ace_marker-layer .ace_selected-word {\
border: 1px solid #FFBBBB\
}\
.ace-ad-inserter .ace_invisible {\
color: #6A6A6A\
}\
.ace-ad-inserter .ace_keyword,\
.ace-ad-inserter .ace_meta,\
.ace-ad-inserter .ace_storage,\
.ace-ad-inserter .ace_storage.ace_type,\
.ace-ad-inserter .ace_support.ace_type {\
color: #00FF00\
}\
.ace-ad-inserter .ace_paren,\
.ace-ad-inserter .ace_keyword.ace_operator,\
.ace-ad-inserter .ace_punctuation.ace_operator {\
color: #FF4444\
}\
.ace-ad-inserter .ace_constant.ace_character,\
.ace-ad-inserter .ace_constant.ace_numeric,\
.ace-ad-inserter .ace_keyword.ace_other.ace_unit {\
color: #FFFF00\
}\
.ace-ad-inserter .ace_constant.ace_language {\
color: #00ff00\
}\
.ace-ad-inserter .ace_invalid {\
color: #CDCDCD;\
background-color: #F2777A\
}\
.ace-ad-inserter .ace_invalid.ace_deprecated {\
color: #CDCDCD;\
background-color: #CC99CC\
}\
.ace-ad-inserter .ace_fold {\
background-color: #6699CC;\
border-color: #CCCCCC\
}\
.ace-ad-inserter .ace_support.ace_function,\
.ace-ad-inserter .ace_variable {\
color: #00FF00\
}\
.ace-ad-inserter .ace_meta.ace_tag,\
.ace-ad-inserter .ace_support.ace_class,\
.ace-ad-inserter .ace_support.ace_type {\
color: #00FF00\
}\
.ace-ad-inserter .ace_heading,\
.ace-ad-inserter .ace_markup.ace_heading,\
.ace-ad-inserter .ace_string {\
color: #FFFFFF\
}\
.ace-ad-inserter .ace_comment {\
color: #DDDDDD\
}\
.ace-ad-inserter .ace_entity.ace_name.ace_function,\
.ace_entity.ace_name.ace_function,\
.ace-ad-inserter .ace_support.ace_constant,\
.ace-ad-inserter .ace_identifier,\
.ace-ad-inserter .ace_variable {\
color: #66FFFF\
}\
.ace-ad-inserter .ace_variable.ace_language {\
color: #FF88FF\
}\
.ace-ad-inserter .ace_entity.ace_name.ace_tag,\
.ace-ad-inserter .ace_entity.ace_other.ace_attribute-name {\
color: #fffF80\
}\
.ace-ad-inserter .ace_support.ace_php_tag {\
color: #ff8888\
}\
//.ace-ad-inserter .ace_paren.ace_adinserter {\
//color: #FF4444;\
//border-left: 1px solid #BBBBFF;\
//border-top: 1px solid #BBBBFF;\
//border-bottom: 1px solid #BBBBFF;\
//border-radius: 3px;\
//}\
.ace-ad-inserter .ace_shortcode {\
color: #ff8888\
}\
.ace-ad-inserter .ace_shortcode.ace_adinserter {\
color: #ff88ff;\
//border-top: 1px solid #BBBBFF;\
//border-bottom: 1px solid #BBBBFF;\
}\
.ace-ad-inserter .ace_shortcode.ace_ai-attribute {\
color: #80ff80;\
//border: 1px solid #FFBBBB;\
//border-radius: 3px;\
}\
.ace-ad-inserter .ace_data {\
color: #e0b6ff\
}\
.ace-ad-inserter .ace_indent-guide {\
background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAEklEQVQImWPQ09NrYAgMjP4PAAtGAwchHMyAAAAAAElFTkSuQmCC) right repeat-y\
}";

var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass);
});

