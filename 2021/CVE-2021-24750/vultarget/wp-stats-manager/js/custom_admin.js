(function ($) {

        $(document).ready(function(){
			
			/*
			* Remove current row
			*/
			$('body').on('click', '.linkRemoveData', function(e){
				e.preventDefault();
				$(this).parents('tr').remove();
			});
            /**
             * get referral all details on Referral sites section
             */
            $('.linkReferralViewDetails, .linkReferralVisitorDetails, .linkReferralLocationDetails').click(function(e){
                e.preventDefault();
                $('tr[id^=wsmRowChild_]').remove();
                var parentRow=$(this).parents(".wsmReferralRow");
                var childRowId = $(parentRow).attr('id').replace('wsmRowParent_', 'wsmRowChild_');
                var animation = '<div class="wsmSpinner"><div class="wsmRect1"></div><div class="wsmRect2"></div><div class="wsmRect3"></div><div class="wsmRect4"></div><div class="wsmRect5"></div></div>';
                if($("#"+childRowId).length){                    
                    $("#"+childRowId).html(animation);
                }else{
					if( $('.wsmTableContainer').hasClass('wsmContentURLStats') ){
                    	parentRow.after("<tr id='"+childRowId+"'><td  colspan=\"5\">"+animation+"</td></tr>");
					}else if( $('.wsmTableContainer').hasClass('wsmLocationList') ){
                    	parentRow.after("<tr id='"+childRowId+"'><td  colspan=\"6\">"+animation+"</td></tr>");
					}else{
						parentRow.after("<tr id='"+childRowId+"'><td  colspan=\"4\">"+animation+"</td></tr>");
					}
                }  
                $.ajax({
                    type: 'POST',
                    url: wsm_ajaxObject.ajax_url,
                    data: jQuery(this).data('referrak_param'),
                })
                .done(function( strResponse ){
                    $("#"+childRowId).find('td').html( strResponse );
					if( $('.wsmTableContainer').hasClass('wsmContentURLStats') ){
						generate_referral_detail_graph( 'referral_chart', ['First Time Visitor','Visitors', 'Hits'] );
					}else{
						generate_referral_detail_graph( 'referral_chart', ['First Time Visitor','Visitors','Page Views'] );
					}
                });
                        
            });
			if( $('.currenttime').length ){
				startTime();	
			}	        
        });
        
        /**
         * Generate referral-detail graph function
         */
        function generate_referral_detail_graph( $id, $labels ){
            //arrLiveStats.push('');
            //jQuery('#".WSM_PREFIX."_lastDaysChart h2.hndle').html('<span>".sprintf(__('Last %d Days','wp-stats-manager'),$atts['days'])."</span>');
			
            var $chart_panel_id = $id;
            var _bpageViews= $('#'+ $id).data('pageviews');
            var _bvisitors = $('#'+ $id).data('visitors');
            var _bfirstVisitors= $('#'+ $id).data('firsttimevisitors');
            var _bnewVisitor = $('#'+ $id).data('newvisitor');
            var _maxy = $('#'+ $id).data('maxy');			
            var _legendIndex=[];
            var bcolors= $('#'+ $id).data('colors');
            var keyLabels= $labels;
            var _arrLineData=[_bfirstVisitors,_bvisitors,_bpageViews];
            var seriesRenderrer=[{yaxis:'yaxis'},{yaxis:'yaxis'},{yaxis:'yaxis'}, {yaxis:'y2axis'}];
            var $height = $('#'+ $id).data('height');
            var $width = $('#'+ $id).data('width');  
            var $numberTicks = $('#'+ $id).data('totalpageviews');
            var $tDays = $('#'+ $id).data('tdays');
            
            var _bOptions={
                // Tell the plot to stack the bars.
                series:seriesRenderrer,
                seriesColors :bcolors,
                height: $height,
                widht: $width,
                gridPadding:{
                    right:40,
                    left:40,
                    top:20,
                    bottom:60
                },
                captureRightClick: true,
                seriesDefaults: {
                    rendererOptions: {
                        smooth: true
                    }
                },
                axes:{
                    xaxis:{
                        renderer:jQuery.jqplot.DateAxisRenderer,
                        tickRenderer:jQuery.jqplot.CanvasAxisTickRenderer,
                        tickOptions:{
                            formatString:'%a %e %b',
                            fontSize:'10px'
                        },
                        numberTicks: $numberTicks,
                        tickInterval: $tDays +' days'
                    },
                    yaxis:{
                        min:0,
						max: ( parseInt(_maxy) + 200 ),
                        tickOptions: {
                            formatString: '%d'
                        }
                    },
                    y2axis: {
                        padMin: 0,
                        min:0,
                        autoscale:true,
                        tickOptions: {
                            showGridline: false,
                        }
                    }
                },
                legend: {
                    labels :keyLabels,
                    show: true,
                    location: 'nw',
                    placement: 'inside',
                    renderer: jQuery.jqplot.EnhancedLegendRenderer,
                    rendererOptions: {
                        numberRows: 1,
                        seriesToggle:true
                    }
                },
                highlighter: {
                    tooltipLocation: 'ne',
                    useAxesFormatters: true,
                    sizeAdjust: 2.5,
                    show:true,
                    formatString:'%s, %P',
                    tooltipContentEditor : function(str, seriesIndex, pointIndex, plot){
                        return '<span style=\'background:'+ bcolors[seriesIndex] +'\'></span>'+ plot.legend.labels[seriesIndex] + ': ' + plot.data[seriesIndex][pointIndex][1];
                    }
                },
                cursor :{
                    show : false,
                    followMouse : false,
                    useAxesFormatters:false
                }
            };
            
            plot_id = jQuery.jqplot( $chart_panel_id , _arrLineData,_bOptions );
            
            if(typeof wsmMoveLegend!='function'){
                wsmMoveLegend=function(parent){
                    parent.find('.wsmTopChartBar').each(function(){
                        legendDiv=jQuery(this).children('.wsmChartLegend');
                        legendDiv.empty();
                        parent.find('table.jqplot-table-legend').appendTo(legendDiv);
                        legendDiv.children('table.jqplot-table-legend').removeAttr('style');
                    });
                }
            }
            wsmMoveLegend(jQuery('#'+ $chart_panel_id).parents('.postbox'));
            jQuery(window).on('resize',function(){
                plot_id.replot();
                wsmMoveLegend(jQuery('#'+ $chart_panel_id).parents('.postbox'));
            });
            
            return;
            var topButtons=jQuery('#'+ $chart_panel_id).siblings('.wsmTopChartBar').find('.wsmButton');
            topButtons.on('click',function(e){
                e.preventDefault();
                jQuery(this).siblings().removeClass('active');
                bchart=jQuery(this).data('chart');
                nBLabels=keyLabels.slice();
                bSeriesData=_arrLineData.slice();
                switch(bchart){
                    case 'Bounce':
                        //nBLabels.push('".__('Bounce Rate(%)','wp-stats-manager')."');
                        //bSeriesData.push(_bBounce);
                    break;
                    case 'Ppv':
                        nBLabels.push('Page Views Per Visit');
                        bSeriesData.push(_bppv);
                    break;
                    case 'Nvis':
                        nBLabels.push('New Visitors');
                        bSeriesData.push(_bnewVisitor);
                    break;
                    case 'Online':
                        //nBLabels.push('".__('Average Online','wp-stats-manager')."');
                        //bSeriesData.push(_bavgOnline);
                    break;
                    default:
                    break;
                }
                jQuery('#'+ $chart_panel_id).empty();
                _bOptions.legend.labels=nBLabels;
                jQuery(this).addClass('active');
                //console.log(bSeriesData);
                plot_id = jQuery.jqplot($chart_panel_id, bSeriesData,_bOptions);
                wsmMoveLegend(jQuery('#'+ $chart_panel_id).parents('.postbox'));
            });
            jQuery('#'+ $chart_panel_id).parent().find('.".WSM_PREFIX."ChartLegend').on('click','table.jqplot-table-legend tr td', function(event, mode){
                if(mode!='code'){
                   var tI = _legendIndex.indexOf(jQuery(this).index());
                    if(tI==-1){
                        _legendIndex.push(jQuery(this).index());
                    }else{ 
                        _legendIndex.splice(tI, 1);
                    }
                }
            });
        }
		
		function shuffle_color( $id, array) {
			if( jQuery( '.post_stats_graph' ).length  == 0 ){
				return array;	
			}
                        console.log(jQuery( '#'+$id ).length);
                                return jQuery( '#' + $id ).data('colors');
		  var currentIndex = array.length, temporaryValue, randomIndex;

		  // While there remain elements to shuffle...
		  while (0 !== currentIndex) {

		    // Pick a remaining element...
		    randomIndex = Math.floor(Math.random() * currentIndex);
		    currentIndex -= 1;

		    // And swap it with the current element.
		    temporaryValue = array[currentIndex];
		    array[currentIndex] = array[randomIndex];
		    array[randomIndex] = temporaryValue;
		  }

		  return array;
		}
		
		function generate_referral_visitor_info_graph( $id, $content ){
			var colors = ['#4573a7','#aa4644','#89a54e','#806a9b','#3d97af','#d9853c','#91a7ce','#a47c7c','#5cb85c','#74d6fe'];
			colors = shuffle_color( $id, colors );
			jQuery.jqplot.config.enablePlugins = true;
			var data = $content;
			
			 if (data != null && $.isArray(data) != false && data.length > 0 && $.isArray(data[0]) != false && data[0].length > 0)
            {		
				data.map(function(subArr,index){data[index]=[subArr[0],parseInt(subArr[1])]});            
			}
            //data.reverse();
            //console.log(data);
            legend_visible = true;
            if( jQuery( '.post_stats_graph' ).length  ){
                legend_visible = false;
            }
            if (data != null && $.isArray(data) != false && data.length > 0 && $.isArray(data[0]) != false && data[0].length > 0)
            {
				$("#"+$id).html("");
				var piePlot = jQuery.jqplot( $id , [data], {
					seriesColors :colors,
					height: 220,
					textColor: "#ffffff",
					fontFamily:'-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif',
					grid:{
							drawBorder: false,
							drawGridlines: false,
							background: '#ffffff',
							shadow:false
					},
					seriesDefaults: {
							renderer: jQuery.jqplot.PieRenderer,
							rendererOptions: {
							showDataLabels: false,
							padding: 2,
							sliceMargin: 3,
							shadow: false,
							diameter: 180
					},
					pointLabels: { show: true }
				},
					legend: {
						show: legend_visible,
						location :'e',
						marginRight:10,
						border : 'none'
					},
					highlighter: {
						show: true,
						useAxesFormatters: false,
						tooltipFormatString: '%s',
						tooltipContentEditor : function(str, seriesIndex, pointIndex, plot){
							var result = str.split(',');
							var ind = parseInt(result.length)-1;
							result.splice(ind,1);
							var str = result.join();
							return str;
							/*var label = str.split(",");
							console.log(str);
							return label[0];*/
						}
					},
					cursor :{
						show : false,
						followMouse : false,
						useAxesFormatters:false
					}
				});
				
					piePlot.replot();
				
				jQuery(window).on('resize',function(){
					piePlot.replot();
				});
			}
		}
		
		/**
		* Handle visitor stat browser and os wise
		*/
		if( $('.stats_submenu a.active').length ){
			$( $('.stats_submenu a.active').attr('href')).show();
			generate_referral_visitor_info_graph( 'visitor_info_graph', $('.stats_submenu a.active').data('graph') );
			
		}
		/*
		*	Generate country 
		*/
		$(window).load(function(){
			if( $('#country_visitor_info_graph').length ){
				generate_referral_visitor_info_graph( 'country_visitor_info_graph', $('#country_visitor_info_graph').data('graph') );
			}	
			if($(".each_visior_info_graph").length)
			{
				$(".each_visior_info_graph").each(function(){
					generate_referral_visitor_info_graph( $(this).attr('id'), $(this).data('graph') );
				});
			}
			
			
		});
		$('.stats_submenu a').click(function( e ){
			e.preventDefault();
			generate_referral_visitor_info_graph( 'visitor_info_graph', $(this).data('graph') );
			$('.stats_submenu a').removeClass('active');
			$( this ).addClass('active');
			$('.vistor_panel_data').hide();
			$( $( this ).attr('href') ).show();
		})
			
		/*
		* Save ip address for exclusion process
		*/
		$('.save_ipadress').click(function( e ){
			e.preventDefault();
			$('.update_message').removeClass('updated').removeClass('error');
			if( $('#ipadress').val() == '' ){
				$('.update_message').removeClass('updated').addClass('error').html('Please enter I.P. address');
				return false;
			}
            $.ajax({
                type: 'POST',
                url: wsm_ajaxObject.ajax_url,
                data: $('#wsmmainMetboxForm').serialize(),
            })
            .done(function( strResponse ){
				response = $.parseJSON( strResponse );
				if( response.status ){
					$('#ipadress').val('');
					$('#tblIPList').append( response.data );
					$('.update_message').removeClass('error').addClass('updated');
				}else{
					$('.update_message').removeClass('updated').addClass('error');
				}
				$('.update_message').html( response.message ); 
            });	
		});
		
		/*
		* Delete IP address
		*/
		$('#tblIPList').on( 'click', '.deleteIP', function(e){
			e.preventDefault();
			$('.update_message').removeClass('updated').removeClass('error');
			var idaddress = $(this).data('ipaddress');
			var currentRow = $(this).data('row');
			if( !confirm('Are you sure want to delete '+idaddress+' ip address?') ){
				return false;
			}
            $.ajax({
                type: 'POST',
                url: wsm_ajaxObject.ajax_url,
                data: 'action=deleteIpAddress&ip='+ idaddress,
            })
            .done(function( strResponse ){
				response = $.parseJSON( strResponse );
				if( response.status ){
					var count = 1;
					$('#tblIPList #row_'+currentRow).remove();
					if( $('#tblIPList tr[id^=row_]').length ){
						$('#tblIPList tr[id^=row_]').each(function(){
							$(this).find('td').first().html( count );	
							count++;
						});
					}else{
						$('#tblIPList').append('<tr><td align="center" colspan="3">No records found.</td></tr>');
					}
				}
				$('.update_message').removeClass('error').addClass('updated').html( response.message ); 
            });		
		});
		
		/*
		* Enable/Disable status of IP address
		*/
		$('#tblIPList').on( 'click', 'input[type="checkbox"]', function(e){
			$('.update_message').removeClass('updated').removeClass('error');
			var $idaddress = $(this).data('ipaddress');
			var $status = 0;
			if( $(this).is(':checked') ){
				$status = 1;
			}
            $.ajax({
                type: 'POST',
                url: wsm_ajaxObject.ajax_url,
                data: 'action=updateIpAddress&ip='+ $idaddress +'&status='+ $status,
            })
            .done(function( strResponse ){
				response = $.parseJSON( strResponse );
				if( !response.status ){
					$('.update_message').removeClass('updated').addClass('error').html( response.message ); 
				}
            });
		});
		
		/*
		* Drag & Drop metaboxes for WordPress Dashboard
		*/
		
	    $( "#site_dashboard_widget_handler_1, #site_dashboard_widget_handler_2" ).sortable({
			connectWith: ".site_dashboard_widget_handler",
			handle: ".handle",
			create: function( event, ui ) {
				if( $('#site_dashboard_widget_handler_1 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_dashboard_widget_handler_1  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					$('input[name="wsmSiteDashboardNormalWidgets"]').val(widgetName);
				}
				if( $('#site_dashboard_widget_handler_2 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_dashboard_widget_handler_2  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					$('input[name="wsmSiteDashboardSideWidgets"]').val(widgetName);
				}
			},
		    update: function( event, ui ) {
				if( $(ui.item).parents('.site_dashboard_widget_handler').attr('id') == 'site_dashboard_widget_handler_1' ){
					$(ui.item).find('input[type="checkbox"]').attr('name', 'wsmDashboard_widget[normal][]'); 
				}else{
					$(ui.item).find('input[type="checkbox"]').attr('name', 'wsmDashboard_widget[side][]'); 
				}

				if( $('#site_dashboard_widget_handler_1 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_dashboard_widget_handler_1  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					$('input[name="wsmSiteDashboardNormalWidgets"]').val(widgetName);
				}
				if( $('#site_dashboard_widget_handler_2 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_dashboard_widget_handler_2  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					console.log(widgetName);
					$('input[name="wsmSiteDashboardSideWidgets"]').val(widgetName);
				}
		    }
	    }).disableSelection();
		

		
		/*
		* Drag & Drop metaboxes for Plugin Dashboard
		*/
		
	    $( "#site_plugin_widget_handler_1, #site_plugin_widget_handler_2" ).sortable({
			connectWith: ".site_plugin_widget_handler",
			handle: ".handle",
			create: function( event, ui ) {
				if( $('#site_plugin_widget_handler_1 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_plugin_widget_handler_1  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					$('input[name="wsmSitePluginNormalWidgets"]').val(widgetName);
				}
				if( $('#site_plugin_widget_handler_2 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_plugin_widget_handler_2  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					$('input[name="wsmSitePluginSideWidgets"]').val(widgetName);
				}
			},
		    update: function( event, ui ) {
				if( $(ui.item).parents('.site_dashboard_widget_handler').attr('id') == 'site_plugin_widget_handler_1' ){
					$(ui.item).find('input[type="checkbox"]').attr('name', 'wsmPlugin_widget[normal][]'); 
				}else{
					$(ui.item).find('input[type="checkbox"]').attr('name', 'wsmPlugin_widget[side][]'); 
				}

				if( $('#site_plugin_widget_handler_1 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_plugin_widget_handler_1  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					$('input[name="wsmSitePluginNormalWidgets"]').val(widgetName);
				}
				if( $('#site_plugin_widget_handler_2 input').length ){
					var widgetName = '';
					var seperator = '';
					$('#site_plugin_widget_handler_2  input').each(function(){
						widgetName = widgetName + seperator + $(this).val();
						seperator = ', ';
					});
					console.log(widgetName);
					$('input[name="wsmSitePluginSideWidgets"]').val(widgetName);
				}
		    }
	    }).disableSelection();
		
		$('.li-section li > a, .sublist-section li > a').on('click', function(e){
			e.preventDefault();
			var href=$(this).attr('href');
			if(jQuery(this).parents("ul.sublist-section").length)
			{
				var url = jQuery(this).parents("ul.sublist-section").attr('data-url');	
				var curURL = document.location.href
				curURL = curURL.replace(window.location.hash,'');
				if(url==curURL)
				{
					
				}
				else
				{
					document.location=url+href;				
				}
				
			}
			
			$('.li-section li > a').removeClass("active");
			$('.sublist-section li > a').removeClass("active");
			$('.li-section li >a[href="'+href+'"]').addClass('active');
			$('.sublist-section li >a[href="'+href+'"]').addClass('active');
			
			
			//$(this).data('href', $(this).attr('href')).removeAttr('href');
			$('.li-section-table table.form-table').not(this).hide();
			$('.li-section-table table'+href).show();
			$('table#ipexclusion').hide();
			$('.wrap .submit #submit').show();
			$('#tab-li-active').val(href);
			if(href == "#ipexclusion" )
			{
				$('table'+href).show();
				$('.wrap .submit #submit').hide();
				$('.wrap .submit').css("padding","0");
			}
			
			if(href == "#shortcodelist" )
			{
			$('.wrap .submit #submit').hide();
			$('.wrap .submit').css("padding","0");
		}
		});	
	
	
	$(window).load(function(){
		if(window.location.hash) 
		{
			jQuery('.sublist-section li > a[href="'+window.location.hash+'"]').trigger("click");
		}
	});
	
	// Add page link to widgets
	if (typeof wsm_widgets != "undefined" && wsm_widgets ) {
		for (i = 0; i < wsm_widgets.length; i++) {
			if( jQuery('#'+wsm_widgets[i]).length ){
		    	jQuery('#'+wsm_widgets[i]).prepend('<a href="'+wsm_widget_links[i]+'" class="wsm_widget_link"><span class="dashicons dashicons-migrate"></span></a>')
			}
		}
	}
	
	$(".send_test_mail").click(function(e){
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: wsm_ajaxObject.ajax_url,
			data: {
				action : 'send_test_mail',
				report: '1'
			},
			success:function( strResponse ){
				console.log(strResponse);
			}
		});
	});
})(jQuery);

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    jQuery('.currenttime').html(h + ":" + m + ":" + s );
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}
