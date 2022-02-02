	$.fbuilder[ 'controls' ] = ( typeof $.fbuilder[ 'controls' ] != 'undefined' ) ? $.fbuilder[ 'controls' ]: {};
	$.fbuilder[ 'forms' ] = ( typeof $.fbuilder[ 'forms' ] != 'undefined' ) ? $.fbuilder[ 'forms' ]: {};
	
	$.fbuilder[ 'htmlEncode' ] = function(value)
	{
		value = $('<div/>').text(value).html()
		value = value.replace( /&/g, '&amp;').replace(/"/g, "&quot;");
		return value;
	};
	
	$.fbuilder[ 'escape_symbol' ] = function( value ) // Escape the symbols used in regulars expressions
	{
		return value.replace(/([\^\$\-\.\,\[\]\(\)\/\\\*\?\+\!\{\}])/g, "\\$1");
	};
	
	$.fbuilder[ 'parseValStr' ] = function( value )
	{
		return '"' + value.replace(/'/g, "\\'").replace( /\$/g, '') + '"';
	};
	
	$.fbuilder[ 'parseVal' ] = function( value, thousandSeparator, decimalSymbol )
	{
		if( value == '' ) return 0;
		value += '';
		
		thousandSeparator = new RegExp( $.fbuilder.escape_symbol( ( typeof thousandSeparator == 'undefined' ) ? ',' : thousandSeparator ), 'g' );
		decimalSymbol = new RegExp( $.fbuilder.escape_symbol( ( typeof decimalSymbol == 'undefined' ) ? '.' : decimalSymbol ), 'g' );
		
		var t = value.replace( thousandSeparator, '' ).replace( decimalSymbol, '.' ).replace( /\s/g, '' ),
			p = /[+-]?((\d+(\.\d+)?)|(\.\d+))/.exec( t );
			
		return ( p ) ? p[0]*1 : $.fbuilder[ 'parseValStr' ]( value );
	};
				
	
	$.fn.fbuilder = function(options){
		var opt = $.extend({},
					{
						pub:false,
						identifier:"",
						title:""
					},options, true);
				
		opt.messages = $.extend({
					previous: "Previous",
					next: "Next",
					pageof: "Page {0} of {0}",
					required: "This field is required.",
					email: "Please enter a valid email address.",
					datemmddyyyy: "Please enter a valid date with this format(mm/dd/yyyy)",
					dateddmmyyyy: "Please enter a valid date with this format(dd/mm/yyyy)",
					number: "Please enter a valid number.",
					digits: "Please enter only digits.",
					maxlength: $.validator.format("Please enter no more than {0} characters"),
                    minlength: $.validator.format("Please enter at least {0} characters."),
                    equalTo: "Please enter the same value again.",
					max: $.validator.format("Please enter a value less than or equal to {0}."),
					min: $.validator.format("Please enter a value greater than or equal to {0}.")
			},opt.messages);
			
		opt.messages.max = $.validator.format(opt.messages.max);
		opt.messages.min = $.validator.format(opt.messages.min);
		
		$.extend($.validator.messages, opt.messages);
		
		var items = [];
		var reloadItemsPublic = function() 
			{
				$("#fieldlist"+opt.identifier).closest( 'form' ).addClass( theForm.formtemplate );
                $("#fieldlist"+opt.identifier).html("").addClass(theForm.formlayout);
				$("#formheader"+opt.identifier).html(theForm.show());
				
				var page = 0;
				$("#fieldlist"+opt.identifier).append('<div class="pb'+page+' pbreak" page="'+page+'"></div>');
				for (var i=0;i<items.length;i++)
				{
					items[i].index = i;
					if (items[i].ftype=="fPageBreak")
					{
						page++;
						$("#fieldlist"+opt.identifier).append('<div class="pb'+page+' pbreak" page="'+page+'"></div>');
					}
					else
					{
						$("#fieldlist"+opt.identifier+" .pb"+page).append(items[i].show());
						if (items[i].predefinedClick)
						{
                            $("#fieldlist"+opt.identifier+" .pb"+page).find("#"+items[i].name).attr("placeholder",items[i].predefined);
                            $("#fieldlist"+opt.identifier+" .pb"+page).find("#"+items[i].name).attr("value","");
                        }
						if (items[i].userhelpTooltip)
						{
							var uh = $("#fieldlist"+opt.identifier+" .pb"+page).find("#"+items[i].name).closest(".fields");
							uh.find(".uh").css("display","none");
							if (uh.find(".uh").text()!="")
							{
								uh.attr("uh",uh.find(".uh").text());
							}	
						}
					}
				}
                
				if (page>0)
				{
                
					$("#fieldlist"+opt.identifier+" .pb"+page).addClass("pbEnd");
					$("#fieldlist"+opt.identifier+" .pbreak").each(function(index) {
						var code = $(this).html();
						var bSubmit = '';
						
						if (index == page)
						{

							if ( $( "#cpcaptchalayer"+opt.identifier ).length && !/^\s*$/.test( $( "#cpcaptchalayer"+opt.identifier ).html() ) )
							{
								code += '<div class="captcha">'+$("#cpcaptchalayer"+opt.identifier).html()+'</div><div class="clearer"></div>';
								$("#cpcaptchalayer"+opt.identifier).html("");
							}
							if ($("#cp_subbtn"+opt.identifier).html())
							{
								bSubmit = '<div class="pbSubmit">'+$("#cp_subbtn"+opt.identifier).html()+'</div>';
							}	
						}
						$(this).html('<fieldset><legend>'+opt.messages.pageof.replace( /\{\s*\d+\s*\}/, (index+1) ).replace( /\{\s*\d+\s*\}/, (page+1) )+'</legend>'+code+'<div class="pbPrevious">'+opt.messages.previous+'</div><div class="pbNext">'+opt.messages.next+'</div>'+bSubmit+'<div class="clearer"></div></fieldset>');
					});
					$( '#fieldlist'+opt.identifier).find(".pbPrevious,.pbNext").bind("click", { 'identifier' : opt.identifier }, function( evt ) {
					    var identifier = evt.data.identifier;
						if (  ($(this).hasClass("pbPrevious")) || (($(this).hasClass("pbNext")) && $(this).parents("form").valid())  )
						{
							var page = parseInt($(this).parents(".pbreak").attr("page"));
							
							(($(this).hasClass("pbPrevious"))?page--:page++);
							$("#fieldlist"+identifier+" .pbreak").css("display","none");
							$("#fieldlist"+identifier+" .pbreak").find(".field").addClass("ignorepb");

							$("#fieldlist"+identifier+" .pb"+page).css("display","block");
							$("#fieldlist"+identifier+" .pb"+page).find(".field").removeClass("ignorepb");
							if ($("#fieldlist"+identifier+" .pb"+page).find(".field").length>0)
							{
								try 
								{
									$("#fieldlist"+identifier+" .pb"+page).find(".field")[0].focus();
								} 
								catch(e){}
							}	
						}
						else
						{
							$(this).parents("form").validate().focusInvalid();
						}	
						return false;
					});
                }
				else
				{
					if ( $( "#cpcaptchalayer"+opt.identifier ).length && !/^\s*$/.test( $( "#cpcaptchalayer"+opt.identifier ).html() ) )
					{
						$("#fieldlist"+opt.identifier+" .pb"+page).append('<div class="captcha">'+$("#cpcaptchalayer"+opt.identifier).html()+'</div>');
						$("#cpcaptchalayer"+opt.identifier).html("");
					}
					if ($("#cp_subbtn"+opt.identifier).html())
					{
						$("#fieldlist"+opt.identifier+" .pb"+page).append('<div class="pbSubmit">'+$("#cp_subbtn"+opt.identifier).html()+'</div>');
					}	
				}
							
                // Set Captcha Event
				$( document ).on( 'click', '#fbuilder .captcha img', function(){ var e = $( this ); e.attr( 'src', e.attr( 'src' ).replace( /&\d+$/, '' ) + '&' + Math.floor( Math.random()*1000 ) ); } );
				
				$( '#fieldlist'+opt.identifier).find(".pbSubmit").bind("click", { 'identifier' : opt.identifier }, function( evt ) 
					{
                        $(this).closest("form").submit();
					});

				if (i>0)
				{
                    theForm.after_show( opt.identifier );
					for (var i=0;i<items.length;i++)
					{
						items[i].after_show();
					}	
					
					$.fbuilder.showHideDep(
						{
							'formIdentifier' : opt.identifier, 
							'throwEvent'	 : true
						}	
					);
					
					$( '#fieldlist'+opt.identifier).find(".depItemSel,.depItem").bind("change", { 'identifier' : opt.identifier }, function( evt ) 
						{
							$.fbuilder.showHideDep(
								{
									'formIdentifier' : evt.data.identifier, 
									'throwEvent'	 : true
								}	
							);
						});
					try 
					{
						$( "#fbuilder"+opt.identifier ).tooltip({show: false,hide:false,tooltipClass:"uh-tooltip",position: { my: "left top", at: "left bottom+5", collision: "none"  },items: "[uh]",content: function (){return $(this).attr("uh");} });
					} catch(e){}
                }
                $("#fieldlist"+opt.identifier+" .pbreak:not(.pb0)").find(".field").addClass("ignorepb");
            };
			
		var fform=function(){};
		$.extend(fform.prototype,
			{
				title:"Untitled Form",
				description:"This is my form. Please fill it out. It's awesome!",
				formlayout:"top_aligned",
				formtemplate:"",
                evalequations:1,
                autocomplete:1,
				show:function(){
                    return '<div class="fform" id="field"><h1>'+this.title+'</h1><span>'+this.description+'</span></div>';
				},
                after_show:function( id ){
                    $( '#cp_calculatedfieldsf_pform'+id ).attr( 'data-evalequations', this.evalequations ).attr( 'autocomplete', ( ( this.autocomplete ) ? 'on' : 'off' ) );
                }
			});
		
		//var theForm = new fform(),
		var theForm,
			ffunct = {
				getItem: function( name )
					{
						for( var i in items )
						{
							if( items[ i ].name == name )
							{
								return items[ i ];
							}
						}
						return false;
					},
				getItems: function() 
					{
					   return items;
					},
				loadData:function(f)
					{
						var d,
							e = $("#"+f);
						
						this.formId = e.parents( 'form' ).attr( 'id' );
						if ( d = $.parseJSON( e.val() ))
						{
						   if (d.length==2)
						   {
							   items = [];
							   for (var i=0;i<d[0].length;i++)
							   {
								   var obj = eval("new $.fbuilder.controls['"+d[0][i].ftype+"']();");
								   obj = $.extend(true, {}, obj,d[0][i]);
								   obj.name = obj.name+opt.identifier;
								   obj.form_identifier = opt.identifier;
								   obj.init();
								   items[items.length] = obj;
							   }
							   theForm = new fform();
							   theForm = $.extend(theForm,d[1][0]);
							   reloadItemsPublic();
						   }
						}

						if( typeof window[ 'cpcff_load_defaults' ] != 'undefined' )
                        {
                            window[ 'cpcff_load_defaults' ]();
                        }
					}
			};

		$.fbuilder[ 'forms' ][ opt.identifier ] = ffunct;
	    this.fBuild = ffunct;
	    return this;
	}; // End fbuilder plugin

	$.fbuilder[ 'showSettings' ] = {
		formlayoutList : [{id:"top_aligned",name:"Top Aligned"},{id:"left_aligned",name:"Left Aligned"},{id:"right_aligned",name:"Right Aligned"}]
	};
	
	$.fbuilder.controls[ 'ffields' ] = function(){};
	$.extend($.fbuilder.controls[ 'ffields' ].prototype, 
		{
				form_identifier:"",
				name:"",
				shortlabel:"",
				index:-1,
				ftype:"",
				userhelp:"",
				userhelpTooltip:false,
				csslayout:"",
				init:function(){},
				show:function()
					{
						return 'Not available yet';
					},
				after_show:function(){},
				val:function(){
					var e = $( "[id='" + this.name + "']:not(.ignore)" );
					if( e.length )
					{
                        return $.fbuilder.parseVal( $.trim( e.val() ) );
					}
					return 0;
				}
		});
	
	$.fbuilder[ 'showHideDep' ] = function( configObj )
		{
			if( typeof configObj[ 'formIdentifier' ] !== 'undefined' )
			{
				var identifier = configObj[ 'formIdentifier' ];
				
				if( typeof  $.fbuilder[ 'forms' ][ identifier ] != 'undefined' )
				{
					var toShow = [],
						toHide = [],
						items = $.fbuilder[ 'forms' ][ identifier ].getItems();
						
					for( var i = 0, h = items.length; i < h; i++ )
					{
						if( typeof items[ i ][ 'showHideDep' ] != 'undefined' )
						{
							items[ i ][ 'showHideDep' ]( toShow, toHide );
						}
					}
					
					if( typeof configObj[ 'throwEvent' ] == 'undefined' || configObj[ 'throwEvent' ] )
					{
						$( document ).trigger( 'showHideDepEvent', $.fbuilder[ 'forms' ][ identifier ][ 'formId' ] );
					}	
				}
			}	
		}; // End showHideDep	
		