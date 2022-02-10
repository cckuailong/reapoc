ace.define ('ace/mode/ai-html', function (require, exports, module) {

  var oop = require ("ace/lib/oop");
  var HtmlMode = require ("ace/mode/html").Mode;
  var AiHtmlHighlightRules = require ("ace/mode/ai_html_highlight_rules").AiHtmlHighlightRules;

  var Mode = function() {
      this.HighlightRules = AiHtmlHighlightRules;
  };
  oop.inherits (Mode, HtmlMode);

  (function() {}).call(Mode.prototype);

  exports.Mode = Mode;
});


ace.define ('ace/mode/ai_html_highlight_rules', function (require, exports, module) {

  var oop = require("ace/lib/oop");
  var HtmlHighlightRules = require ("ace/mode/html_highlight_rules").HtmlHighlightRules;

  var AiHtmlHighlightRules = function() {
    this.$rules = new HtmlHighlightRules().getRules();
    this.$lang = require ("ace/lib/lang");
    add_ai_highlighting_rules (this, HtmlHighlightRules);
  }

  oop.inherits (AiHtmlHighlightRules, HtmlHighlightRules);
  exports.AiHtmlHighlightRules = AiHtmlHighlightRules;
});

function add_ai_highlighting_rules (highlighter, highlight_rules) {

  highlighter.$ai_shortcodes  = highlighter.$lang.arrayToMap ("adinserter".split ("|"));
  highlighter.$ai_separators1 = highlighter.$lang.arrayToMap ("http|count|check".split ("|"));
  highlighter.$ai_separators2 = highlighter.$lang.arrayToMap ("head|amp".split ("|"));
  highlighter.$ai_separators3 = highlighter.$lang.arrayToMap ("rotate".split ("|"));
  highlighter.$ai_attributes  = highlighter.$lang.arrayToMap ("block|code|name|group|ignore|viewport|debugger|adb|tracking|css|text|selectors|custom-field|random|data|share|time|counter|category|tag|taxonomy|id|url|url-parameter|referrer|client|scheduling|ip-address|country|disable".split ("|"));

  //WP shortcodes
  highlighter.$rules ['start'].unshift (
    {
      token:  function (shortcode_start, shortcode, shortcode_end) {
                highlighter.$ai_shortcode = highlighter.$ai_shortcodes.hasOwnProperty (shortcode.toLowerCase());
                return ["paren", highlighter.$ai_shortcode ? "shortcode.adinserter" : "shortcode"];
              },
      regex:  "(\\[/?)([a-zA-Z][a-zA-Z0-9_-]*)",
      next:   "ai-attributes"
    },
    {
      token: "variable.language",
      regex: "\\|rotate\\||\\|count\\||\\|amp\\|",
    }
  );

  highlighter.embedRules (highlight_rules, "ai-", [
    {
      token: "paren",
      regex: "\\]",
      next: "start"
    }
  ]);

  //AI {#data:#}
  highlighter.$rules ['start'].unshift (
    {
      token:  ["paren", "data", "paren", "string", "paren"],
      regex:  "(\\{\\#)([a-zA-Z][a-zA-Z0-9_-]*)(\\:)?(.*)?(\\#\\})",
    },
  );

  // Add ] to regexp for 'string.unquoted.attribute-value.html'
  var arrayLength = highlighter.$rules ['ai-keyword.operator.attribute-equals.xml'].length;
    for (var i = 0; i < arrayLength; i++) {
      if (highlighter.$rules ['ai-keyword.operator.attribute-equals.xml'][i]['token'] == 'string.unquoted.attribute-value.html')
        highlighter.$rules ['ai-keyword.operator.attribute-equals.xml'][i]['regex'] = "[^<>='\"`\\]\\s]+";
  }

  highlighter.$rules ['ai-attributes'].unshift (
    {
      token: function (attribute) {
               return !highlighter.$ai_shortcode                                           ? "entity.other.attribute-name.xml" :
                      highlighter.$ai_separators1.hasOwnProperty (attribute.toLowerCase()) ? "string" :
                      highlighter.$ai_separators2.hasOwnProperty (attribute.toLowerCase()) ? "identifier" :
                      highlighter.$ai_separators3.hasOwnProperty (attribute.toLowerCase()) ? "variable.language" :
//                      highlighter.$ai_separators4.hasOwnProperty (attribute.toLowerCase()) ? "paren" :
//                      highlighter.$ai_separators5.hasOwnProperty (attribute.toLowerCase()) ? "keyword" :
                      highlighter.$ai_attributes.hasOwnProperty  (attribute.toLowerCase()) ? "entity.other.attribute-name.xml" : "text";
             },
      regex: "[a-zA-Z][-a-zA-Z0-9]*"
    }
  );

//  console.log (highlighter.$rules);
}
