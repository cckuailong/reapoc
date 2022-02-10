ace.define("ace/theme/disabled",["require","exports","module","ace/lib/dom"], function(require, exports, module) {

exports.isDark = true;
exports.cssClass = "ace-disabled";
exports.cssText = ".ace-disabled .ace_gutter {\
background: #272727;\
color: #CCC\
}\
.ace-disabled .ace_print-margin {\
width: 1px;\
background: #272727\
}\
.ace-disabled {\
background-color: #FFFFFF;\
color: #222222\
}\
.ace-disabled .ace_constant.ace_other,\
.ace-disabled .ace_cursor {\
color: #333333\
}\
.ace-disabled .ace_marker-layer .ace_selection {\
background: #DDDDDD\
}\
.ace-disabled.ace_multiselect .ace_selection.ace_start {\
box-shadow: 0 0 3px 0px #2D2D2D;\
border-radius: 2px\
}\
.ace-disabled .ace_marker-layer .ace_step {\
background: rgb(102, 82, 0)\
}\
.ace-disabled .ace_marker-layer .ace_bracket {\
margin: -1px 0 0 -1px;\
border: 1px solid #FF00FF\
}\
.ace-tomorrow-night-bright .ace_stack {\
background: rgb(66, 90, 44)\
}\
.ace-disabled .ace_marker-layer .ace_active-line {\
background: #393939\
}\
.ace-disabled .ace_gutter-active-line {\
background-color: #393939\
}\
.ace-disabled .ace_marker-layer .ace_selected-word {\
border: 1px solid #FFBBBB\
}\
.ace-disabled .ace_invisible {\
color: #6A6A6A\
}";

var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass);
});
