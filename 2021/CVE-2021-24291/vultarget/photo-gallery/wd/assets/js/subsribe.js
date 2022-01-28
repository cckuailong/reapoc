jQuery(document).on("ready", function () {
  jQuery(".permissions").on("click", function () {
    jQuery(this).toggleClass("active");
    jQuery(".list").slideToggle("fast");
    return false;
  });
  jQuery(".allow_and_continue, .skip").on("click", function () {
    var url = jQuery(this).attr("href");
    if (url) {
      jQuery(".allow_and_continue, .skip").css("opacity", "0.5");
      jQuery(".allow_and_continue, .skip").attr("disabled", "disabled");
      jQuery(".allow_and_continue, .skip").removeAttr("href");
      jQuery(".wd_loader").css("display", "inline-block");
      window.location = url;
    }
  });
});