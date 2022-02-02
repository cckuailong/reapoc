	$.fbuilder.controls[ 'fcontainer' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fcontainer' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			fields:[],
			columns:1,
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
					$("#sColumns").bind("change", {obj: this}, function(e) 
						{
							e.data.obj.columns = $(this).val();
							$.fbuilder.reloadItems();
						});
				},
			showShortLabel:function(){ return ''; },
			showUserhelp:function(){ return ''; },
			showSpecialDataInstance: function() 
				{
					var columns = [1,2,3,4],
						cStr = '';
					for( var i = 0, h = columns.length; i < h; i++ )
					{	
						cStr += '<option value="'+columns[ i ]+'" '+( ( this.columns == columns[ i ] ) ? 'SELECTED' : '' )+'>'+columns[ i ]+' column'+( ( i ) ? 's' : '' )+'</option>';
					}	
					return '<div><label>Columns</label><br /><select name="sColumns" id="sColumns">' + cStr + '</select><div class="clearer"><span class="uh">Shown in columns the fields into the container.</span></div></div>';
				},
			remove : function()
				{
					for( var i = this.fields.length - 1, h = 0; i >= h; i-- )
					{
						this.fBuild.removeItem( $( '.'+this.fields[ i ] ).attr( 'id' ).replace( 'field-', '' ) );
					}	
				},
			duplicateItem: function( currentField, newField )	
				{
					for( var i = 0, h = this.fields.length; i < h; i++ )
					{
						if( this.fields[ i ] == currentField )
						{
							this.fields.splice( i+1, 0, newField );
							return;
						}
					}
				},
			addItem: function( newField )
				{
					this.fields.push( newField );
				},
			after_show:function()
				{
					var me  = this,
						e   = $( '#field' + me.form_identifier + '-' + me.index + ' .fcontainer' ),
						tmp = [];

					for( var i = 0, h = me.fields.length; i < h; i++ )
					{
						var f 	= $( '.' + me.fields[ i ] );
						if( f.length )
						{
							f.detach().appendTo( e );
							tmp.push( me.fields[ i ] );
						}
					}
					me.fields = tmp;
					
					e.sortable( 
						{
							'connectWith': '.ui-sortable',
							'items': '.fields',
							'update': function( event, ui )
									{
										var p = ui.item.parents('.fields');
										if( p.length && $(this ).parents( '.fields' ).attr( 'id' ) == p.attr( 'id' ) )
										{
											// receive or or changing the ordering in the fcontainer
											me.fields = [];
											$( event.target ).children( '.fields' )
															 .each( function()
																{
																	me.fields.push( /fieldname\d+/.exec( $(this).attr( 'class' ) )[ 0 ] );
																} );
											$.fbuilder.reloadItems();
										}
										else
										{
											// remove
											me.fields.splice( $.inArray( me.fBuild.getItems()[ ui.item.attr( 'id' ).replace( 'field-', '' ) ].name, me.fields ), 1 );
										}
									}
						}
					);
				}
				
	});