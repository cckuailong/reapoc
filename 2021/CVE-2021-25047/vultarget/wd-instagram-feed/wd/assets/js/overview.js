jQuery(window).resize(function () {
	tenWebOverviewResize();
});
jQuery(document).ready(function () {
	tenWebOverviewResize();
});

function tenWebOverviewResize(){
	if(jQuery(".tenweb_header_right").length){
		if (matchMedia('only screen and (max-width: 840px)').matches) {
		   jQuery(".tenweb_header_right").after(jQuery(".tenweb_header_right .header_text"));
		} else{
		   jQuery(".tenweb_header_right .button").before(jQuery(".tenweb_overview .inline-block.header_text"));
		}
	}
}
