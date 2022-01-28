var g_cfsCurrTour = null
,	g_cfsTourOpenedWithTab = false
,	g_cfsAdminTourDissmissed = false;
jQuery(document).ready(function(){
	setTimeout(function(){
		if(typeof(cfsAdminTourData) !== 'undefined' && cfsAdminTourData.tour) {
			jQuery('body').append( cfsAdminTourData.html );
			cfsAdminTourData._$ = jQuery('#supsystic-admin-tour');
			for(var tourId in cfsAdminTourData.tour) {
				if(cfsAdminTourData.tour[ tourId ].points) {
					for(var pointId in cfsAdminTourData.tour[ tourId ].points) {
						_cfsOpenPointer(tourId, pointId);
						break;	// Open only first one
					}
				}
			}
			for(var tourId in cfsAdminTourData.tour) {
				if(cfsAdminTourData.tour[ tourId ].points) {
					for(var pointId in cfsAdminTourData.tour[ tourId ].points) {
						if(cfsAdminTourData.tour[ tourId ].points[ pointId ].sub_tab) {
							var subTab = cfsAdminTourData.tour[ tourId ].points[ pointId ].sub_tab;
							jQuery('a[href="'+ subTab+ '"]')
								.data('tourId', tourId)
								.data('pointId', pointId);
							var tabChangeEvt = str_replace(subTab, '#', '')+ '_tabSwitch';
							jQuery(document).bind(tabChangeEvt, function(event, selector) {
								if(!g_cfsTourOpenedWithTab && !g_cfsAdminTourDissmissed) {
									var $clickTab = jQuery('a[href="'+ selector+ '"]');
									_cfsOpenPointer($clickTab.data('tourId'), $clickTab.data('pointId'));
								}
							});
						}
					}
				}
			}
		}
	}, 500);
});

function _cfsOpenPointerAndFormTab(tourId, pointId, tab) {
	g_cfsTourOpenedWithTab = true;
	jQuery('#cfsFormEditTabs').wpTabs('activate', tab);
	_cfsOpenPointer(tourId, pointId);
	g_cfsTourOpenedWithTab = false;
}
function _cfsOpenPointer(tourId, pointId) {
	var pointer = cfsAdminTourData.tour[ tourId ].points[ pointId ];
	var $content = cfsAdminTourData._$.find('#supsystic-'+ tourId+ '-'+ pointId);
	if(!jQuery(pointer.target) || !jQuery(pointer.target).length)
		return;
	if(g_cfsCurrTour) {
		_cfsTourSendNext(g_cfsCurrTour._tourId, g_cfsCurrTour._pointId);
		g_cfsCurrTour.element.pointer('close');
		g_cfsCurrTour = null;
	}
	if(pointer.sub_tab && jQuery('#cfsFormEditTabs').wpTabs('getActiveTab') != pointer.sub_tab) {
		return;
	}
	var options = jQuery.extend( pointer.options, {
		content: $content.find('.supsystic-tour-content').html()
	,	pointerClass: 'wp-pointer supsystic-pointer'
	,	close: function() {
			//console.log('closed');
		}
	,	buttons: function(event, t) {
			g_cfsCurrTour = t;
			g_cfsCurrTour._tourId = tourId;
			g_cfsCurrTour._pointId = pointId;
			var $btnsShell = $content.find('.supsystic-tour-btns')
			,	$closeBtn = $btnsShell.find('.close')
			,	$finishBtn = $btnsShell.find('.supsystic-tour-finish-btn');

			if($finishBtn && $finishBtn.length) {
				$finishBtn.click(function(e){
					e.preventDefault();
					jQuery.sendFormCfs({
						msgElID: 'noMessages'
					,	data: {mod: 'supsystic_promo', action: 'addTourFinish', tourId: tourId, pointId: pointId}
					});
					g_cfsCurrTour.element.pointer('close');
				});
			}
			if($closeBtn && $closeBtn.length) {
				$closeBtn.bind( 'click.pointer', function(e) {
					e.preventDefault();
					jQuery.sendFormCfs({
						msgElID: 'noMessages'
					,	data: {mod: 'supsystic_promo', action: 'closeTour', tourId: tourId, pointId: pointId}
					});
					t.element.pointer('close');
					g_cfsAdminTourDissmissed = true;
				});
			}
			return $btnsShell;
		}
	});
	jQuery(pointer.target).pointer( options ).pointer('open');
	var minTop = 10
	,	pointerTop = parseInt(g_cfsCurrTour.pointer.css('top'));
	if(!isNaN(pointerTop) && pointerTop < minTop) {
		g_cfsCurrTour.pointer.css('top', minTop+ 'px');
	}
}
function _cfsTourSendNext(tourId, pointId) {
	jQuery.sendFormCfs({
		msgElID: 'noMessages'
	,	data: {mod: 'supsystic_promo', action: 'addTourStep', tourId: tourId, pointId: pointId}
	});
}