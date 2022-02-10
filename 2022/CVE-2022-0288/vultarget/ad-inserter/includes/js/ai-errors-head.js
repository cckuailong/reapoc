ai_js_errors = [];
window.onerror = function (errorMsg, url, lineNumber) {
  ai_js_errors.push ([errorMsg, url, lineNumber]);
};
