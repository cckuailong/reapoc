ai_document_write = document.write;

document.write = function (content) {
 if (document.readyState == 'interactive') {
    console.error ('document.write called after page load: ', content);
    if (typeof ai_js_errors != 'undefined') {
      ai_js_errors.push (['document.write called after page load', content, 0]);
    }
    return;
  }
  ai_document_write.call (document, content);
};
