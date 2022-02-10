jQuery(window).resize(function() {
  jQuery("body").each(function () {
    window.parent.wdi_thickDims(jQuery(this).data("width"), jQuery(this).data("height"));
  });
});