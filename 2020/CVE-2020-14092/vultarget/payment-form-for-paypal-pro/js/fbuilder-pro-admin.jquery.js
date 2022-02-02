var ppp_started=false;
	$.fbuilder[ 'typeList' ] = [];
	$.fbuilder[ 'categoryList' ] = [];
	$.fbuilder[ 'controls' ] = {};
	
	$.fbuilder[ 'preview' ] = function( e )
	{
		var f  = $( e.form );
		f.attr( 'target', 'formpopup' ).attr( 
			'onsubmit', 
			function( f )
			{
				var w = screen.width*0.8,
					h = screen.height*0.7,
					l = screen.width/2 - w/2,
					t = screen.height/2 - h/2,
					new_window = window.open('', 'formpopup', 'resizeable,scrollbars,width='+w+',height='+h+',left='+l+',top='+t);
					
				$( f ).removeAttr( 'onsubmit' );
				new_window.focus();
			} 
		);
		$( '<input type="hidden" name="preview" value="1" />' ).appendTo( f );
		
		f[ 0 ].submit();
		f.attr( 'target', '_self' ).find( 'input[name="preview"]').remove();
	};
	
	$.fbuilder[ 'htmlEncode' ] = function(value)
	{
		value = $('<div/>').text(value).html()
		value = value.replace( /&/g, '&amp;' ).replace(/"/g, "&quot;");
		return value;
	};
	
	$.fbuilder[ 'escape_symbol' ] = function( value ) // Escape the symbols used in regulars expressions
	{
		return value.replace(/([\^\$\-\.\,\[\]\(\)\/\\\*\?\+\!\{\}])/g, "\\$1");
	};
				
	$.fbuilder[ 'parseVal' ] = function( value, thousandSeparator, decimalSymbol )
	{
		if( value == '' ) return 0;
		value += '';
		
		thousandSeparator = new RegExp( $.fbuilder.escape_symbol( ( typeof thousandSeparator == 'undefined' ) ? ',' : thousandSeparator ), 'g' );
		decimalSymbol = new RegExp( $.fbuilder.escape_symbol( ( typeof decimalSymbol == 'undefined' ) ? '.' : decimalSymbol ), 'g' );
		
		var t = value.replace( thousandSeparator, '' ).replace( decimalSymbol, '.' ).replace( /\s/g, '' ),
			p = /[+-]?((\d+(\.\d+)?)|(\.\d+))/.exec( t );
			
		return ( p ) ? p[0]*1 : '"' + value.replace(/'/g, "\\'").replace( /\$/g, '') + '"';
	};
	
    $.fbuilder[ 'showErrorMssg' ] = function( str ) // Display an error message
    {
        $( '.form-builder-error-messages' ).html( '<div class="error-text">' + str + '</div>' );
    };
    
	// fbuilder plugin
	$.fn.fbuilder = function(options){
		var opt = $.extend({},
				{
	   				pub:false,
					identifier:"",
					title:""
				},
				options, true),
			typeList = 	$.fbuilder.typeList,
			categoryList = $.fbuilder.categoryList;
			
		$.fbuilder[ 'getNameByIdFromType' ] = function( id )
			{
				for ( var i = 0, h = typeList.length; i < h; i++ )
				{
					if ( typeList[i].id == id )
					{
						return  typeList[i].name;
					}	
				}		
				return "";
			};
		
		for ( var i=0, h = typeList.length; i < h; i++ )
		{
			var category_id = typeList[ i ].control_category;
			
			if( typeof categoryList[ category_id ]  == 'undefined' )
			{
				categoryList[ category_id ] = { title : '', description : '', typeList : [] };
			}
			else if( typeof categoryList[ category_id ][ 'typeList' ]  == 'undefined' )
			{
				categoryList[ category_id ][ 'typeList' ] = [];
			}
			
			categoryList[ category_id ].typeList.push( i );
		}

		for ( var i in categoryList )
		{
			$("#tabs-1").append('<div style="clear:both;"></div><div>'+categoryList[ i ].title+'</div><hr />');
			if( typeof categoryList[ i ][ 'description' ] != 'undefined' && !/^\s*$/.test( categoryList[ i ][ 'description' ] ) )
			{
				$("#tabs-1").append('<div style="clear:both;"></div><div class="category-description">'+categoryList[ i ].description+'</div>');
			}
			
			if( typeof categoryList[ i ][ 'typeList' ]  != 'undefined' )
			{
				for( var j = 0, k = categoryList[ i ].typeList.length; j < k; j++ )
				{
					var index = categoryList[ i ].typeList[ j ];
					$("#tabs-1").append('<div class="button itemForm width40" id="'+typeList[ index ].id+'">'+typeList[ index ].name+'</div>');
				}
			}	
		}
		
		$("#tabs-1").append('<div class="clearer"></div>');
		$( ".button").button();
		
		// Create a items object
		var items = [],
            fieldsIndex = {},
            calculatedFields = {},
			selected = -3;
			
		$.fbuilder[ 'editItem' ] = function( id ) 
			{		    
				selected = id;
                try 
                { 
                    $('#tabs-2').html( items[id].showAllSettings() ); 
                } catch (e) {}
				items[id].editItemEvents();
			};
			
		$.fbuilder[ 'removeItem' ] = function( index ) 
			{
				if( typeof items[ index ][ 'remove' ] != 'undefined' ) items[ index ][ 'remove' ]();
				items.splice(index,1);
				for ( var i=0, h = items.length; i<h; i++ )
				{
					items[i].index = i;
				}
				selected = -2;
				$('#tabs').tabs("option", "active", 0);
				$.fbuilder.reloadItems();
			};
			
		$.fbuilder[ 'duplicateItem' ] = function( index ) 
			{
				var n = 0;
				for ( var i=0, h = items.length; i<h; i++ )
				{
				   n1 = parseInt( items[i].name.replace( /fieldname/g,"" ) );
				   if (n1>n)
					   n = n1;
				}
				
				if( $( '#field-' + items[ index ].index ).parents( '.fields' ).length )
				{
					var i = $( '#field-' + items[ index ].index ).parents( '.fields' ).attr( 'id' ).replace( 'field-', '' );
					if( typeof items[ i ][ 'duplicateItem' ] != 'undefined' ) items[ i ][ 'duplicateItem' ]( items[ index ].name, 'fieldname'+( n + 1 ) );
				}
				
				items.splice( index*1+1, 0, $.extend( true, {}, items[index], { name:"fieldname"+(n+1) } ) );
				for ( var i=index*1+1, h = items.length; i<h; i++ )
				{
					items[i].index = i;
				}
				
				$('#tabs').tabs("option", "active", 0);
				$.fbuilder.reloadItems();
			}
		
		$.fbuilder[ 'editForm' ] = function() 
			{
				$('#tabs-3').html(theForm.showAllSettings());
				selected = -1;

				$("#fTitle").keyup(function()
				{
					theForm.title = $(this).val();
					$.fbuilder.reloadItems();
				});
				
				$("#fEvalEquations").click(function()
				{
					theForm.evalequations = ($(this).is( ':checked' )) ? 1 : 0;
					$.fbuilder.reloadItems();
				});
				
				$("#fAutocomplete").click(function()
				{
					theForm.autocomplete = ($(this).is( ':checked' )) ? 1 : 0;
					$.fbuilder.reloadItems();
				});
				
				$("#fDescription").keyup(function()
				{
					theForm.description = $(this).val();
					$.fbuilder.reloadItems();
				});
				
				$("#fLayout").change(function()
				{
					theForm.formlayout = $(this).val();
					$.fbuilder.reloadItems();
				});
				
				$("#fTemplate").change(function()
				{
					theForm.formtemplate = $(this).val();
					var template 	= $.fbuilder.showSettings.formTemplateDic[ theForm.formtemplate ],
						thumbnail	= '',
						description = '';

					if( typeof template != 'undefined' )
					{
						if( typeof template[ 'thumbnail' ] != 'undefined' )
						{
							thumbnail = '<img src="' + template[ 'thumbnail' ] + '">';
						}
						if( typeof template[ 'description' ] != 'undefined' )
						{
							description = template[ 'description' ];
						}
					}
					$( '#fTemplateThumbnail' ).html( thumbnail );
					$( '#fTemplateDescription' ).html( description );
					$.fbuilder.reloadItems();
				});
			};
		
		$.fbuilder[ 'reloadItems' ] = function() 
			{
        if (ppp_started){alert('* Note: The Form Builder is read-only in this version.');return;}else {ppp_started=true;}       
				for ( var i=0, h = $.fbuilder.showSettings.formlayoutList.length; i < h; i++ )
				{
					$("#fieldlist"+opt.identifier).removeClass( $.fbuilder.showSettings.formlayoutList[i].id );
				}	
				$("#fieldlist"+opt.identifier).addClass(theForm.formlayout);
				$("#formheader"+opt.identifier).html(theForm.display());
				$("#fieldlist"+opt.identifier).html("");
				if ( parseInt( selected ) == -1 )
				{
					$(".fform").addClass("ui-selected");
				}
				else
				{
					$(".fform").removeClass("ui-selected");
				}
				
                fieldsIndex = {};
                calculatedFields = {};
				for ( var i=0, h = items.length; i < h; i++ )
				{
					items[i].index = i;
                    fieldsIndex[ items[i].name ] = i;
                    if( items[i].ftype == 'fCalculated')
                    {
                        calculatedFields[ items[i].name ] = i;
                    }
					$("#fieldlist"+opt.identifier).append(items[i].display());
					if ( i == selected )
					{
						$("#field"+opt.identifier+"-"+i).addClass("ui-selected");
                        if( $('#tabs').tabs("option", "active") != 1 )
                        {
                            $.fbuilder[ 'editItem' ]( i );
                        }    
					}	
					else
					{
						$("#field"+opt.identifier+"-"+i).removeClass("ui-selected");
					}	
					$("#field"+opt.identifier+"-"+i+" .remove").click(function()
						{
							$.fbuilder[ 'removeItem' ]($(this).parent().attr("id").replace("field"+opt.identifier+"-",""));
						});
						
					$("#field"+opt.identifier+"-"+i+" .copy").click(function()
						{
							$.fbuilder[ 'duplicateItem' ]($(this).parent().attr("id").replace("field"+opt.identifier+"-",""));
						});
						
				}
				for ( var i=0, h = items.length; i < h; i++ )
				{
					if( typeof items[ i ].after_show != 'undefined' ) items[ i ].after_show();
				}
				if (items.length>0)
				{
				    $(".fields").mouseover(function( evt ) 
						{
							$(this).addClass("ui-over");
							evt.stopPropagation();
						})
						.mouseout(function( evt )
						{
							$(this).removeClass("ui-over");
							evt.stopPropagation();
						})
						.click(function( evt )
						{
							evt.stopPropagation();
                            $.fbuilder[ 'editItem' ]($(this).attr("id").replace("field"+opt.identifier+"-",""));
							$( '#fieldlist .ui-selected' ).removeClass("ui-selected");
							$(this).addClass("ui-selected");
							$('#tabs').tabs("option", "active", 1);
                        });
					$(".field").focus(function()
						{
							$(this).blur();
						});	
				}
				
				$(".fform").mouseover(function() 
					{
						$(this).addClass("ui-over");
					})
					.mouseout(function()
					{
						$(this).removeClass("ui-over");
					})
					.click(function( evt ){
                        evt.stopPropagation();
						$('#tabs').tabs("option", "active", 2);
						$.fbuilder.editForm();
						$(this).siblings().removeClass("ui-selected");
						$(this).addClass("ui-selected");
					});
					
				ffunct.saveData("form_structure");
				
				//email list
				var str = "",
					cu_user_email_field = $('#cu_user_email_field').attr("def").split( ',' );
					
				for ( var i=0, h = items.length; i < h; i++ )
				{
					var item = items[ i ];
					if (item.ftype=="femail" || item.ftype=="femailds")
					{
						str += '<option value="'+item.name+'" '+( ( $.inArray( item.name, cu_user_email_field ) != -1 ) ? "selected" : "" )+'>'+item.name+' ('+item.title+')'+'</option>';
					}
				}
				$('#cu_user_email_field').html(str);
				
				// recurrent payments
				var str = "",
					paypal_recurrent_field = $('[name="paypal_recurrent_field"]').attr("def");
					
				for ( var i=0, h = items.length; i < h; i++ )
				{
					var item = items[ i ];
					if (item.ftype=="fradio" || item.ftype=="fdropdown")
					{
						str += '<option value="'+item.name+'" '+( ( item.name == paypal_recurrent_field ) ? "selected" : "" )+'>'+item.name+' ('+item.title+')'+'</option>';
					}
				}
				$('[name="paypal_recurrent_field"]').html(str);
				
				//field list for paypal request
				if (($('#request_cost').length > 0) && ($('#request_cost').is('select')))
				{
					var str = "",
						request_cost = $('#request_cost').attr("def");
						
					for (var i=0, h = items.length; i<h; i++)
					{
						var item = items[ i ];
						str += '<option value="'+item.name+'" '+( ( item.name == request_cost ) ? "selected" : "" )+'>'+item.name+'('+(item.title)+')</option>';
					}	
					$('#request_cost').html(str);
				}
				
				//request amount list
				if ($('#paypal_price_field').length > 0)
				{
					var paypal_price_field = $('#paypal_price_field').attr("def"),
						str = '<option value="" '+( ('' == paypal_price_field ) ? "selected" : "" )+'> ---- No ---- </option>';
						
					for (var i=0, h = items.length; i < h; i++)
					{
						var item = items[ i ];
						str += '<option value="'+item.name+'" '+( ( item.name == paypal_price_field ) ? "selected" : "" )+'>'+(item.title)+'</option>';
					}		
					$('#paypal_price_field').html(str);
				}
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
				display:function()
				{
					return '<div class="fform" id="field"><div class="arrow ui-icon ui-icon-play "></div><h1>'+this.title+'</h1><span>'+this.description+'</span></div>';
				},
				
				showAllSettings:function()
				{
					var layout 	    = '',
						template    = '<option value="">Use default template</option>',
						thumbnail   = '',
						description = '',
						selected    = '';
						
					for ( var i = 0; i< $.fbuilder.showSettings.formlayoutList.length; i++ )
					{
						layout += '<option value="'+$.fbuilder.showSettings.formlayoutList[i].id+'" '+(($.fbuilder.showSettings.formlayoutList[i].id==this.formlayout)?"selected":"")+'>'+$.fbuilder.showSettings.formlayoutList[i].name+'</option>';
					}	

					for ( var i in $.fbuilder.showSettings.formTemplateDic )
					{
						if( /^\s*$/.test( i ) ) break;
						selected = '';
						if( $.fbuilder.showSettings.formTemplateDic[i].prefix==this.formtemplate )
						{
							selected = 'SELECTED';
							if( typeof $.fbuilder.showSettings.formTemplateDic[i].thumbnail != 'undefined' )
							{
								thumbnail = '<img src="'+$.fbuilder.showSettings.formTemplateDic[i].thumbnail+'">'; 
							}
							
							if( typeof $.fbuilder.showSettings.formTemplateDic[i].description != 'undefined' )
							{
								description = $.fbuilder.showSettings.formTemplateDic[i].description; 
							}
						}
						
						template += '<option value="'+$.fbuilder.showSettings.formTemplateDic[i].prefix+'" ' + selected + '>'+$.fbuilder.showSettings.formTemplateDic[i].title+'</option>';
					}	
					return '<div><label>Form Name</label><input class="large" name="fTitle" id="fTitle" value="'+$.fbuilder.htmlEncode(this.title)+'" /></div><div><label>Description</label><textarea class="large" name="fDescription" id="fDescription">'+this.description+'</textarea></div><div><label>Label Placement</label><br /><select name="fLayout" id="fLayout" class="large">'+layout+'</select></div><div><label><input type="checkbox" name="fAutocomplete" id="fAutocomplete" '+( ( this.autocomplete ) ? 'CHECKED' : '' )+' /> Enable autocompletion</label></div>' + ( ( typeof $.fbuilder.controls[ 'fCalculated' ] != 'undefined' ) ? '<div><label><input type="checkbox" name="fEvalEquations" id="fEvalEquations" '+( ( this.evalequations ) ? 'CHECKED' : '' )+' /> Eval dynamically the equations associated to the calculated fields</label></div>' : '' ) + '<div><label>Form Template</label><br /><select name="fTemplate" id="fTemplate" class="large">'+template+'</select></div><br /><div><span id="fTemplateThumbnail" style="float:left;padding-right:10px;">'+thumbnail+'</span><span  id="fTemplateDescription" style="float:left;">'+description+'</span></div>' ;
				}
			}
		);
		
		var theForm = new fform();
		$("#fbuilder").click( function( evt )
            { 
                evt.stopPropagation();
                selected = -2;
                $(".fform").removeClass("ui-selected")
                $( '#fieldlist .ui-selected' ).removeClass("ui-selected");
                $('#tabs').tabs("option", "active", 0);
            } 
        );
        $("#fieldlist"+opt.identifier).sortable(
			{
				'connectWith': '.ui-sortable',
				'items': '.fields',
				'update': function( event, ui )
				{
					var i, h = items.length;
					for( i = 0; i < h; i++ )
					{
						if( ui.item.hasClass( items[ i ].name ) ) break;
					}
						
					if( ui.item.parent().attr( 'id' ) == 'fieldlist' )
					{
						// receive or change order in fieldlist
						var end_pos = parseInt( ui.item.index() );
						items.splice( end_pos, 0,  items.splice( i, 1 )[ 0 ] );
						$.fbuilder.reloadItems();
					}
					else
					{
						// remove
						items = items.concat( items.splice( i, 1 ) );
					}
				}
			}   
		);
		
		$('#tabs').tabs(
			{
				activate: function(event, ui) 
					{
						switch( $(this).tabs( "option", "active" ) )
						{
							case 0:
								$(".fform").removeClass("ui-selected");
							break;
							case 1:
								$(".fform").removeClass("ui-selected");
								if (selected < 0)
								{
								   $('#tabs-2').html('<b>No Field Selected</b><br />Please click on a field in the form preview on the right to change its properties.');
								}
							break;
							case 2:
								$(".fields").removeClass("ui-selected");
								$(".fform").addClass("ui-selected");
								$.fbuilder.editForm();
							break;
						}
					}	   
			}		
		);
		
	    var ffunct = {
	        getFieldsIndex: function() 
			{
			   return fieldsIndex;
		    },
		    getCalculatedFields: function() 
			{
			   return calculatedFields;
		    },
		    getItems: function() 
			{
			   return items;
		    },
		    addItem: function(id) 
			{
			    var obj = eval("new $.fbuilder.controls['"+id+"']();"),
					fBuild = this,
					n = 0;

				obj.init();
			    for (var i=0, h = items.length; i < h; i++)
			    {
		 		    n1 = parseInt(items[i].name.replace(/fieldname/g,""));
	 			    if (n1>n)
					{
					   n = n1;
					}   
 			    }
				n++;
				obj.fBuild = fBuild;
			    $.extend(obj,{name:"fieldname"+n});
                
                if( selected >= 0 )
                {
                    items.splice( (selected)*1+1, 0, obj );
                    if( typeof items[ selected ][ 'addItem' ] != 'undefined' )
                    {
                        items[ selected ][ 'addItem' ]( obj.name );
                    }
                    else
                    {
                        selected++;
                    }
                }
                else
                {
                    selected = items.length;
                    items[selected] = obj;
                }    
				$.fbuilder.reloadItems();
		    },
		    saveData:function(f)
			{
                var itemsStringified   = $.stringifyXX( items ),
                    theFormStringified = $.stringifyXX( theForm ),
                    errorTxt = 'The entered data includes invalid characters. Please, if you are copying and pasting from another platform, be sure the data not include invalid characters.',
                    str;
                
                if( typeof global_varible_save_data != 'undefined' )
                {
                    // If the global_varible_save_data exists clear the form-builder-error-messages
                    $( '.form-builder-error-messages' ).html( '' );
                }
                
                try{
                    if( $.parseJSON( itemsStringified ) != null && $.parseJSON( theFormStringified ) != null )
                    {
                        str = "["+ itemsStringified +",["+ theFormStringified +"]]";
                        $( "#"+f ).val( str );
                        $( "#form_structure_crc" ).val( str.length );
                    }
                    else
                    {
                        $.fbuilder[ 'showErrorMssg' ]( errorTxt );
                    }    
                }
                catch( err )
                {
                    $.fbuilder[ 'showErrorMssg' ]( errorTxt );
                }
                
                global_varible_save_data = true;
		    },
		    loadData:function(form_structure, available_templates)
			{
		        var structure = $.parseJSON( $("#"+form_structure).val() ), // JSON data
					templates = ( typeof available_templates == 'undefined' ) ? null : $.parseJSON( $("#"+available_templates).val() ),
					fBuild = this;

			    if ( structure )
				{
					if (structure.length==2)
					{
						items = [];
						for (var i=0;i<structure[0].length;i++)
						{
						   var obj = eval("new $.fbuilder.controls['"+structure[0][i].ftype+"']();");
						   obj = $.extend( true, {}, obj, structure[0][i] );
						   obj.name = obj.name+opt.identifier;
						   obj.form_identifier = opt.identifier;
						   obj.fBuild = fBuild;
						   items[items.length] = obj;
						}
						theForm = new fform();
						theForm = $.extend(theForm,structure[1][0]);
						$.fbuilder.reloadItems();
					}
				}
				
				if( templates )
				{
					$.fbuilder.showSettings.formTemplateDic = templates;
				}
		    },
		    removeItem: $.fbuilder[ 'removeItem' ],
		    editItem:   $.fbuilder[ 'editItem' ]
	    }
	   
	    this.fBuild = ffunct;
	    return this;
	};

    $.fbuilder[ 'showSettings' ] = {
		sizeList:new Array({id:"small",name:"Small"},{id:"medium",name:"Medium"},{id:"large",name:"Large"}),
		layoutList:new Array({id:"one_column",name:"One Column"},{id:"two_column",name:"Two Column"},{id:"three_column",name:"Three Column"},{id:"side_by_side",name:"Side by Side"}),
		formlayoutList:new Array({id:"top_aligned",name:"Top Aligned"},{id:"left_aligned",name:"Left Aligned"},{id:"right_aligned",name:"Right Aligned"}),
		formTemplateDic: {}, // Form Template dictionary
        showFieldType: function( v )
        {
            return '<label>Field Type: '+$.fbuilder[ 'getNameByIdFromType' ]( v )+'</label><br /><br />';
        },
		showTitle: function(v) 
		{
			return '<label>Field Label</label><textarea class="large" name="sTitle" id="sTitle">'+v+'</textarea>';
		},
		showShortLabel: function( v )
		{
			return '<div><label>Short label (optional) [<a class="helpfbuilder" text="The short label is used at title for the column when exporting the form data to CSV files.\n\nIf the short label is empty then, the field label will be used for the CSV file.">help?</a>] :</label><input class="large" name="sShortlabel" id="sShortlabel" value="'+v+'" /></div>';
		},
		showName: function( v ) 
		{
			return '<div><label>Field name, tag for the message:</label><input readonly="readonly" class="large" name="sNametag" id="sNametag" value="&lt;%'+v+'%&gt;" />'+
				   '<input style="display:none" readonly="readonly" class="large" name="sName" id="sName" value="'+v+'" /></div>';
		},
		showPredefined: function(v,c) 
		{
			return '<div><label>Predefined Value</label><textarea class="large" name="sPredefined" id="sPredefined">'+v+'</textarea><br /><input type="checkbox" name="sPredefinedClick" id="sPredefinedClick" '+((c)?"checked":"")+' value="1" > Use predefined value as placeholder.</div>';
		},
		showEqualTo: function(v,name) 
		{
			return '<div><label>Equal to [<a class="helpfbuilder" text="Use this field to create password confirmation field or email confirmation fields.\n\nSpecify this setting ONLY into the confirmation field, not in the original field.">help?</a>]</label><br /><select class="equalTo" name="sEqualTo" id="sEqualTo" dvalue="'+v+'" dname="'+name+'"></select></div>';
		},
		showRequired: function(v) 
		{
			return '<div><input type="checkbox" name="sRequired" id="sRequired" '+((v)?"checked":"")+'><label>Required</label></div>';
		},
		showSize: function(v) 
		{
			var str = "";
			for (var i=0;i<this.sizeList.length;i++)
			{
				str += '<option value="'+this.sizeList[i].id+'" '+((this.sizeList[i].id==v)?"selected":"")+'>'+this.sizeList[i].name+'</option>';
			}	
			return '<label>Field Size</label><br /><select name="sSize" id="sSize">'+str+'</select>';
		},
		showLayout: function(v) 
		{
			var str = "";
			for (var i=0;i<this.layoutList.length;i++)
			{
				str += '<option value="'+this.layoutList[i].id+'" '+((this.layoutList[i].id==v)?"selected":"")+'>'+this.layoutList[i].name+'</option>';
			}	
			return '<label>Field Layout</label><br /><select name="sLayout" id="sLayout">'+str+'</select>';
		},
		showUserhelp: function(v,c) 
		{
			return '<div><label>Instructions for User</label><textarea class="large" name="sUserhelp" id="sUserhelp">'+v+'</textarea><br /><input type="checkbox" name="sUserhelpTooltip" id="sUserhelpTooltip" '+((c)?"checked":"")+' value="1" > Show as floating tooltip.</div>';
		},
		showCsslayout: function(v) 
		{
			return '<label>Add Css Layout Keywords</label><input class="large" name="sCsslayout" id="sCsslayout" value="'+v+'" />';
		}
	};
	
	$.fbuilder.controls[ 'ffields' ] = function(){};
	$.extend( $.fbuilder.controls[ 'ffields' ].prototype, 
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
			editItemEvents:function()
			{
				$("#sTitle").bind("keyup", {obj: this}, function(e) 
					{
						var str = $(this).val();
						e.data.obj.title = str.replace(/\n/g,"<br />");
						$.fbuilder.reloadItems();
					});
					
				$("#sShortlabel").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.shortlabel = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$("#sPredefined").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.predefined = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$("#sPredefinedClick").bind("click", {obj: this}, function(e) 
					{
						e.data.obj.predefinedClick = $(this).is(':checked');
						$.fbuilder.reloadItems();
					});
					
				$("#sRequired").bind("click", {obj: this}, function(e) 
					{
						e.data.obj.required = $(this).is(':checked');
						$.fbuilder.reloadItems();
					});
					
				$("#sUserhelp").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.userhelp = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$("#sUserhelpTooltip").bind("click", {obj: this}, function(e) 
					{
						e.data.obj.userhelpTooltip = $(this).is(':checked');
						$.fbuilder.reloadItems();
					});
					
				$("#sCsslayout").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.csslayout = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$(".helpfbuilder").click(function()
					{
						alert($(this).attr("text"));
					});
			},
			
			showSpecialData:function()
			{
				if(typeof this.showSpecialDataInstance != 'undefined')
				{
					return this.showSpecialDataInstance();
				}	
				else
				{
					return "";
				}	
			},
			
			showEqualTo:function()
			{
				if(typeof this.equalTo != 'undefined')
				{
					return $.fbuilder.showSettings.showEqualTo(this.equalTo,this.name);
				}	
				else
				{
					return "";
				}	
			},
			
			showPredefined:function()
			{
				if(typeof this.predefined != 'undefined')
				{
					return $.fbuilder.showSettings.showPredefined(this.predefined,this.predefinedClick);
				}	
				else
				{
					return "";
				}	
			},
			
			showRequired:function()
			{
				if(typeof this.required != 'undefined')
				{
					return $.fbuilder.showSettings.showRequired(this.required);
				}	
				else
				{
					return "";
				}	
			},
			
			showSize:function()
			{
				if(typeof this.size != 'undefined')
				{
					return $.fbuilder.showSettings.showSize(this.size);
				}	
				else
				{
					return "";
				}	
			},
			
			showLayout:function()
			{
				if(typeof this.layout != 'undefined')
				{
					return $.fbuilder.showSettings.showLayout(this.layout);
				}	
				else
				{
					return "";
				}	
			},
			
			showRange:function()
			{
				if(typeof this.min != 'undefined')
				{
					return this.showRangeIntance();
				}	
				else
				{
					return "";
				}	
			},
			
			showFormat:function()
			{
				if(typeof this.dformat != 'undefined')
				{
					try 
					{
						return this.showFormatIntance();
					} catch(e) {return "";}
				}	
				else
				{
					return "";
				}	
			},
			
			showChoice:function()
			{
				if(typeof this.choices != 'undefined')
				{
					return this.showChoiceIntance();
				}	
				else
				{
					return "";
				}	
			},
			
			showUserhelp:function()
			{
				return $.fbuilder.showSettings.showUserhelp(this.userhelp,this.userhelpTooltip);
			},
			
			showCsslayout:function()
			{
				return $.fbuilder.showSettings.showCsslayout(this.csslayout);
			},
			
			showAllSettings:function()
			{
				return this.showFieldType()+this.showTitle()+this.showShortLabel()+this.showName()+this.showSize()+this.showLayout()+this.showFormat()+this.showRange()+this.showRequired()+this.showSpecialData()+this.showEqualTo()+this.showPredefined()+this.showChoice()+this.showUserhelp()+this.showCsslayout();
			},
			
			showFieldType:function()
			{
				return $.fbuilder.showSettings.showFieldType(this.ftype);
			},
			
			showTitle:function()
			{
				return $.fbuilder.showSettings.showTitle(this.title);
			},
			
			showName:function()
			{
				return $.fbuilder.showSettings.showName(this.name);
			},
			
			showShortLabel:function()
			{
				return $.fbuilder.showSettings.showShortLabel(this.shortlabel);
			},
			
			display:function()
			{
				return 'Not available yet';
			},
			
			show:function()
			{
				return 'Not available yet';
			}
		}
	);