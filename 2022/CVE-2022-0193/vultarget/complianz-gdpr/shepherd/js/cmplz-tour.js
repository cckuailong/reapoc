jQuery(document).ready(function($) {
	if (!window.Shepherd) return;
	var stepsArr = Object.entries(cmplz_tour.steps);
	var steps = [];
	stepsArr.forEach(([key, value]) => {
		steps[key] = value;
	});
	var prevLink = [];
	var prevText = [];
	var nextLink = [];
	var nextText = [];
	var tour = new Shepherd.Tour();

	initTour();

	if ( typeof window._wpLoadBlockEditor !== 'undefined' ) {
		window._wpLoadBlockEditor.then(function () {
			setTimeout(
				function()
				{
					initTour();
				}, 2000);
		});
	}

	function initTour(){
		for (var key in steps) {
			if (steps.hasOwnProperty(key)) {
				var step = steps[key];
				var stepCount = steps.length;
				if ($(step.attach).length) {
					if (key == 0) {
						prevLink[0] = cmplz_tour.configure_link;
						prevText[0] = cmplz_tour.configure_text;
					} else {
						prevLink[key] = steps[key - 1].link;
						prevText[key] = cmplz_tour.backBtnText;
					}

					if (key < stepCount - 1) {
						nextLink[key] = steps[parseInt(key) + 1].link;
						nextText[key] = cmplz_tour.nextBtnText;
					} else {
						nextLink[key] = nextLink[key-1];
						nextText[key] = cmplz_tour.endTour;
					}

					tour.on('cancel', cancel_tour);
					tour.options.defaults =
						{
							classes: 'shepherd-theme-arrows',
							scrollTo: true,
							scrollToHandler: function (e) {
								if (typeof ($(e).offset()) !== "undefined") {
									$('html, body').animate({
										scrollTop: $(e).offset().top - 200
									}, 1000);
								}
							},
							showCancelLink: true,
							tetherOptions: {
								constraints: [
									{
										to: 'scrollParent',
										attachment: 'together',
										pin: false
									}
								]
							}
						};

					tour.addStep(key, {
						classes: 'shepherd-theme-arrows zrdn-shepherd shepherd-has-cancel-link shepherd-step-' + key,
						attachTo: step.attach + ' ' + step.position,
						title: step.title,
						text: cmplz_tour.html.replace('{content}', step.text),
						buttons: [
							{
								text: prevText[key],
								classes: 'button button-primary',
								action: function () {
									var step_id = $(this).closest('.shepherd-step').data('id');
									if (step_id == 0 ) {
										tour.cancel();
										window.location = prevLink[step_id];
									} else {
										if (prevLink.length > 1 && prevLink[step_id] === steps[step_id].link) {
											if (steps[parseInt(step_id)-1].hasOwnProperty('click') && $(steps[parseInt(step_id)-1].click).length ) {
												$(steps[parseInt(step_id)-1].click).click();
											}
											tour.back();
										} else {
											window.location = prevLink[step_id];
										}
									}
								}
							},
							{
								text: nextText[key],
								action: function () {
									var step_id = $(this).closest('.shepherd-step').data('id');
									if (step_id == stepCount-1 ) {
										tour.cancel();
									} else {
										if (nextLink.length > 1 && nextLink[step_id] === steps[step_id].link) {
											if ( steps[parseInt(step_id)+1].hasOwnProperty('click') && $(steps[parseInt(step_id)+1].click).length ) {
												$(steps[parseInt(step_id)+1].click).click();
											}
											tour.next();
										} else {
											window.location = nextLink[step_id];
										}
									}

								},
								classes: 'button button-primary',
							},
						],

					});
					tour.start();

				}
			}
		}
	}

	/**
	 * Cancel tour
	 */

	function cancel_tour() {
		tour.canceled = true;
		$.ajax({
			type: "POST",
			url: cmplz_tour.ajaxurl,
			dataType: 'json',
			data: ({
				action: 'cmplz_cancel_tour',
				token: cmplz_tour.token,
			})
		});
	};

});


//
// jQuery(document).ready(function($) {
// 	if (!window.Shepherd) return;
//
//
// 	var steps = cmplz_tour.steps;
//
// 	plugins_overview_tour.addStep('cmplz-step-0', {
// 		classes: 'shepherd-theme-arrows cmplz-plugins-overview-tour-container shepherd-has-cancel-link',
// 		attachTo: steps[0]['attach']+' right',
// 		title: steps[0]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[0]['text']),
// 		buttons: [
// 			{
// 				text: cmplz_tour.configure,
// 				classes: 'button button-primary',
// 				action: function() {
// 					cancel_tour();
// 					window.location = cmplz_tour.configure_link;
// 				}
// 			},
// 			{
// 				text: cmplz_tour.startTour,
// 				classes: 'button button-primary',
// 				action: function() {
// 					window.location = steps[0]['link'];
// 				}
// 			},
//
// 		],
// 	});
//
// 	if (steps.hasOwnProperty(1)) {
// 		var license_tour = new Shepherd.Tour();
// 		license_tour.options.defaults = plugins_overview_tour.options.defaults;
// 		license_tour.addStep('cmplz-step-1', {
// 			classes: 'shepherd-theme-arrows cmplz-plugins-overview-tour-container shepherd-has-cancel-link',
// 			attachTo: steps[1]['attach'],
// 			title: steps[1]['title'],
// 			text: cmplz_tour.html.replace('{content}', steps[1]['text']),
// 			buttons: [
// 				{
// 					text: cmplz_tour.nextBtnText,
// 					classes: 'button button-primary',
// 					action: function () {
// 						window.location = steps[1]['link'];
// 					}
// 				},
//
// 			],
// 		});
// 		license_tour.on('cancel', cancel_tour_plus);
//
// 	}
//
// 	dashboard_tour.addStep('cmplz-step-2', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		attachTo: steps[2]['attach']+' right',
// 		title: steps[2]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[2]['text']),
// 		buttons: [
// 			{
// 				text: cmplz_tour.nextBtnText,
// 				classes: 'button button-primary',
// 				action: function() {
// 					window.location = steps[2]['link'];
// 				}
// 			},
//
// 		],
// 	});
// 	wizard_tour.addStep('cmplz-step-3', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		attachTo: steps[3]['attach']+' right',
// 		title: steps[3]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[3]['text']),
// 		buttons: [
// 			{
// 				text: cmplz_tour.backBtnText,
// 				classes: 'button button-primary',
// 				action: function() {
// 					window.location = steps[1]['link'];
// 				}
// 			},
// 			{
// 				text: cmplz_tour.nextBtnText,
// 				action: function() {
// 					window.location = steps[3]['link'];
// 				},
// 				classes: 'button button-primary',
// 			},
// 		],
// 	});
//
// 	cookiebanner_tour.addStep('cmplz-step-4', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		title: steps[4]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[4]['text']),
// 		attachTo: steps[4]['attach'],
// 		buttons: [
// 			{
// 				text: cmplz_tour.backBtnText,
// 				action: function() {
// 					window.location = steps[2]['link'];
// 				},
// 				classes: 'button button-primary',
// 			},
// 			{
// 				text: cmplz_tour.nextBtnText,
// 				action: function() {
// 					window.location = steps[4]['link'];
// 				},
// 				classes: 'button button-primary',
// 			}
// 		],
// 	});
//
// 	integrations_tour.addStep('cmplz-step-5', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		title: steps[5]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[5]['text']),
// 		attachTo: steps[5]['attach'],
// 		buttons: [
// 			{
// 				text: cmplz_tour.backBtnText,
// 				action: function() {
// 					window.location = steps[3]['link'];
// 				},
// 				classes: 'button button-primary',
// 			},
// 			{
// 				text: cmplz_tour.nextBtnText,
// 				action: function() {
// 					window.location = steps[5]['link'];
// 				},
// 				classes: 'button button-primary',
// 			}
// 		],
// 	});
//
// 	settings_tour.addStep('cmplz-step-6', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		title: steps[6]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[6]['text']),
// 		attachTo: steps[6]['attach'],
// 		buttons: [
// 			{
// 				text: cmplz_tour.backBtnText,
// 				action: function() {
// 					window.location = steps[4]['link'];
// 				},
// 				classes: 'button button-primary',
// 			},
// 			{
// 				text: cmplz_tour.nextBtnText,
// 				action: function() {
// 					window.location = steps[6]['link'];
// 				},
// 				classes: 'button button-primary',
// 			}
// 		],
// 	});
//
// 	snapshot_tour.addStep('cmplz-step-7', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		title: steps[7]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[7]['text']),
// 		attachTo: steps[7]['attach'],
// 		buttons: [
// 			{
// 				text: cmplz_tour.backBtnText,
// 				action: function() {
// 					window.location = steps[5]['link'];
// 				},
// 				classes: 'button button-primary',
// 			},
// 			{
// 				text: cmplz_tour.nextBtnText,
// 				action: function() {
// 					window.location = steps[7]['link'];
// 				},
// 				classes: 'button button-primary',
// 			}
// 		],
// 	});
//
// 	finish_tour.addStep('cmplz-step-8', {
// 		classes: 'shepherd-theme-arrows shepherd-has-cancel-link',
// 		title: steps[8]['title'],
// 		text: cmplz_tour.html.replace('{content}', steps[8]['text']),
// 		attachTo: steps[7]['attach'],
// 		buttons: [
// 			{
// 				text: cmplz_tour.backBtnText,
// 				action: function() {
// 					window.location = steps[6]['link'];
// 				},
// 				classes: 'button button-primary',
// 			},
// 			{
// 				text: cmplz_tour.endTour,
// 				action: finish_tour.cancel,
// 				classes: 'button button-primary',
// 			}
// 		],
// 	});
//
// 	dashboard_tour.on('cancel', cancel_tour);
// 	plugins_overview_tour.on('cancel', cancel_tour);
// 	wizard_tour.on('cancel', cancel_tour);
// 	cookiebanner_tour.on('cancel', cancel_tour);
// 	integrations_tour.on('cancel', cancel_tour);
// 	settings_tour.on('cancel', cancel_tour);
// 	snapshot_tour.on('cancel', cancel_tour);
// 	finish_tour.on('cancel', cancel_tour);
//
// 	// start tour when the settings link appears after plugin activation
// 	if ($('.cmplz-settings-link').length) {
// 		plugins_overview_tour.start();
// 	}
// 	if ($('#cmplz_license_key').length) {
// 		license_tour.start();
// 	}
//
// 	if ($('.cmplz-grid-title').length) {
// 		dashboard_tour.start();
// 	}
//
// 	if ($('.cmplz-wizard-menu .cmplz-step-header.active').length) {
// 		wizard_tour.start();
// 	}
//
// 	if ($('#CMPLZ_COOKIEBANNER-general').length) {
// 		cookiebanner_tour.start();
// 	}
//
// 	if ($('#integrations-services').length) {
// 		integrations_tour.start();
// 	}
//
// 	if ($('#settings-general').length) {
// 		settings_tour.start();
// 	}
// 	if ($('#cmplz-cookiestatement-snapshot-filter').length) {
// 		snapshot_tour.start();
// 	}
// 	if ($('.cmplz-step-1').length) {
// 		finish_tour.start();
// 	}
//
// 	/**
// 	 * Cancel tour
// 	 */
//
// 	function cancel_tour() {
// 		// The tour is either finished or [x] was clicked
// 		plugins_overview_tour.canceled = true;
// 		dashboard_tour.canceled = true;
// 		wizard_tour.canceled = true;
// 		snapshot_tour.canceled = true;
// 		settings_tour.canceled = true;
// 		cookiebanner_tour.canceled = true;
// 		integrations_tour.canceled = true;
// 		finish_tour.canceled = true;
//
// 		$.ajax({
// 			type: "POST",
// 			url: cmplz_tour.ajaxurl,
// 			dataType: 'json',
// 			data: ({
// 				action: 'cmplz_cancel_tour',
// 				token: cmplz_tour.token,
// 			})
// 		});
// 	};
//
// 	function cancel_tour_plus() {
// 		license_tour.canceled = true;
//
//
// 		$.ajax({
// 			type: "POST",
// 			url: cmplz_tour.ajaxurl,
// 			dataType: 'json',
// 			data: ({
// 				action: 'cmplz_cancel_tour',
// 				token: cmplz_tour.token,
// 			})
// 		});
// 	};
//
//
// });
