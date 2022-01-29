"use strict";
var RMCustomTabs = function(options){

    this.rmWidth=0;
    this.container= options.container;
    this.activeTabIndex= options.activeTabIndex || 0;
    this.tabPanels = [];
    this.tabHeadsDOMEle = [];

    this.animation = options.animation || 'none';
    this.accentColor = options.accentColor || '#000';

    this.init();
}

RMCustomTabs.prototype.getActiveTabIndex= function () {
	   return this.activeTabIndex;
};

RMCustomTabs.prototype.setActiveTabByIndex= function (i) {
        if(this.activeTabIndex != i && this.tabPanels.length > i && i >= 0 && this.tabPanels[i] != '__rmt_noop'){
        	jQuery(this.tabHeadsDOMEle[this.activeTabIndex]).removeClass("rmActiveTab");
			jQuery(this.tabHeadsDOMEle[i]).addClass("rmActiveTab");

			this.switchTabWithAnim(jQuery(this.tabPanels[this.activeTabIndex]),
								   jQuery(this.tabPanels[i]));

			this.activeTabIndex = i;
		}
};

RMCustomTabs.prototype.switchTabWithAnim= function (jqele_to_hide, jqele_to_show) {

	switch(this.animation) {
		case 'fade':
			jqele_to_hide.fadeOut(0);
			jqele_to_show.fadeIn(400);
		break;

		case 'slide':
			jqele_to_hide.hide(400);
			jqele_to_show.show(400);
		break;

		default:
			jqele_to_hide.hide();
			jqele_to_show.show();
		break;
	}
};


RMCustomTabs.prototype.init = function () {
	var rmtabs = this;
	var tabContainer = jQuery(this.container);

	if(tabContainer.innerWidth()< 800)
		tabContainer.addClass('rmNarrow');
	else
		tabContainer.addClass('rmWide');
	
	tabContainer.find(".rmtabs_head").each(function(i){
		var thisHead_jqele = jQuery(this);

		thisHead_jqele.addClass('rm-menu-tab');

		thisHead_jqele.hover(function () {
	        jQuery(this).css({'border-left-color': rmtabs.accentColor, 'border-left-style':'solid' });
                }, function () {
                    jQuery(this).css('border-left-color', 'transparent');
                });

		var tc = thisHead_jqele.data("rmt-tabcontent");
		if(typeof tc == "undefined")
			rmtabs.tabPanels.push("#rmtabpanel_"+i);
		else
			rmtabs.tabPanels.push(tc);

		rmtabs.tabHeadsDOMEle.push(this);
		jQuery(rmtabs.tabPanels[i]).addClass("rm-tab-content");
		if(rmtabs.activeTabIndex == i) {
			jQuery(rmtabs.tabPanels[i]).show();
			thisHead_jqele.addClass('rmActiveTab');
		}
		else
			jQuery(rmtabs.tabPanels[i]).hide();

		jQuery(this).click(function(e){					
				rmtabs.setActiveTabByIndex(i);
		})

	});
        tabContainer.show();
	
};
