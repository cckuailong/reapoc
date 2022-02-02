	$.fbuilder.controls[ 'datasource' ] = function(){
		this.list = {
			'database' : {
				title : 'Database',
				databaseData: {
					host: '',
					user: '',
					pass: '',
					database: ''
				},
				queryData : {
					active: 'structure',
					query: '',
					value: '',
					text: '',
					table: '',
					where: '',
					orderby: '',
					limit: ''
				},
				show : function( type ) // Type can be 'pair' or 'single', for 'pair' are shown options for text and value, for 'single' is shown only the option for value
					{
						var str = '<div>Database Connection</div>';
						str += '<div><label>Host:<label><input class="large" name="sHost" id="sHost" value="' + this.databaseData.host + '" /></div>';
						str += '<div><label>Username:<label><input class="large" name="sUser" id="sUser" value="' + this.databaseData.user + '" /></div>';
						str += '<div><label>Password:<label><input class="large" name="sPass" id="sPass" value="' + this.databaseData.pass + '" /></div>';
						str += '<div><label>Database:<label><input class="large" name="sDatabase" id="sDatabase" value="' + this.databaseData.database + '" /></div>';
						str += '<div><input type="button" class="button" name="sTestConnection" id="sTestConnection" value="Test Connection" style="float:right;margin:5px 0;" /></div>';
						str += '<div style="clear:both;"></div>';
						
						str += '<div><label><input type="radio" name="sQueryType" id="sQueryType" value="structure" ' + ( ( this.queryData.active == 'structure' ) ? 'CHECKED' : '' ) + ' /> Query Structure</label><label><input type="radio" name="sQueryType" id="sQueryType" value="query" ' + ( ( this.queryData.active == 'query' ) ? 'CHECKED' : '' ) + ' /> Custom Query</label></div>';
						
						str += '<div id="databaseQueryData_structure" class="queryType" style="display:' + ( ( this.queryData.active == 'structure' ) ? 'block' : 'none' ) + ';" >';
						str += '<div><label>Column for values:<label><input class="large" name="sQueryValue" id="sQueryValue" value="' + this.queryData.value + '" /></div>';
						if( type == 'pair' )
						{
							str += '<div><label>Column for texts:<label><input class="large" name="sQueryText" id="sQueryText" value="' + this.queryData.text + '" /></div>';
						}	
						str += '<div><label>Table name:<label><input class="large" name="sQueryTable" id="sQueryTable" value="' + this.queryData.table + '" /></div>';
						str += '<div><label>Condition:<label><input class="large" name="sQueryWhere" id="sQueryWhere" value="' + this.queryData.where + '" /></div>';
						str += '<div><label>Order by:<label><input class="large" name="sQueryOrderBy" id="sQueryOrderBy" value="' + this.queryData.orderby + '" /></div>';
						str += '<div><label>Limit:<label><input class="large" name="sQueryLimit" id="sQueryLimit" value="' + this.queryData.limit + '" /></div>';
						str += '</div>';
						
						str += '<div id="databaseQueryData_query" class="queryType" style="display:' + ( ( this.queryData.active == 'query' ) ? 'block' : 'none' ) + ';" >';
						str += '<div><label>Type the query:<label><input class="large" name="sCustomQuery" id="sCustomQuery" value="' + this.queryData.query + '" /></div>';
						str += '</div>';
						
						str += '<div><input type="button" class="button" name="sTestQuery" id="sTestQuery" value="Test Query" style="float:right;margin:5px 0;" /></div>';
						str += '<div style="clear:both;"></div>';
						
						return str;
					},
				events : function()
					{
						$( '#sHost' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.databaseData.host = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sUser' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.databaseData.user = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sPass' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.databaseData.pass = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sDatabase' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.databaseData.database = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '[name="sQueryType"]' ).bind( "click", { obj: this }, function( e )
							{
								$( '.queryType' ).hide();
								$( '#databaseQueryData_'+e.target.value ).show();
								e.data.obj.queryData.active = $.trim( $(this).val() );
							});
						$( '#sQueryValue' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.value = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sQueryText' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.text = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sQueryTable' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.table = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sQueryWhere' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.where = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sQueryOrderBy' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.orderby = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sQueryLimit' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.limit = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sCustomQuery' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.queryData.query = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sTestConnection' ).bind( 'click', { obj: this }, function( e )
							{
								var form_url = $( this ).parents( 'form' ).attr( 'action' );
								$.ajax(
									{
										url : form_url,
										cache : false,
										data : $.extend( { cffaction: 'test_db_connection' }, e.data.obj.databaseData ),
										success : function( data ){
											alert( data );
										}
									}
								);
							});
						$( '#sTestQuery' ).bind( 'click', { obj: this }, function( e )
							{
								var form_url = $( this ).parents( 'form' ).attr( 'action' );
								$.ajax(
									{
										url : form_url,
										cache : false,
										data : $.extend( { cffaction: 'test_db_query' }, e.data.obj.databaseData, e.data.obj.queryData ),
										success : function( data ){
											alert( data );
										}
									}
								);
							});	
					}
			},
			'csv' : {
				title : 'CSV',
				csvData : {
					text : 0,
					value : 0,
					file : '',
					fields : [],
					headline : false,
					delimiter : 'tabulator',
					character : ',',
					rows : []
				},
				show : function()
					{
					
						var str = '<div>CSV Import</div>',
							optionsTexts  = '',
							optionsValues = '';
						for( var index in this.csvData.fields )
						{
							optionsTexts += '<option value="' + index + '" ' + ( ( index == this.csvData.text ) ? 'SELECTED' : '' ) + ' >' + this.csvData.fields[ index ] + '</option>';
							optionsValues += '<option value="' + index + '" ' + ( ( index == this.csvData.value ) ? 'SELECTED' : '' ) + ' >' + this.csvData.fields[ index ] + '</option>';
						}
						
						str += '<div><label>Select CSV file:<label><input type="file" class="large" name="sCSVLocation" id="sCSVLocation" value="' + this.csvData.file + '" /></div>';
						str += '<div><label>Use headline: <input type="checkbox" name="sCSVUseHeadline" id="sCSVUseHeadline" ' + ( ( this.csvData.headline ) ? 'CHECKED' : '' ) + ' /><label></div>';
						str += '<div><label>Delimiter:</label>&nbsp;&nbsp;<label><input type="radio" name="sCSVDelimiter" id="sCSVDelimiter" value="tabulator" ' + ( ( this.csvData.delimiter == 'tabulator' ) ? 'CHECKED' : '' ) + ' /> Tabulator<label>&nbsp;&nbsp;<label><input type="radio" name="sCSVDelimiter" id="sCSVDelimiter" value="character" ' + ( ( this.csvData.delimiter == 'character' ) ? 'CHECKED' : '' ) + ' /> Character <input type="text" class="small" name="sCSVCharacter" id="sCSVCharacter" value="' + this.csvData.character + '" /><label></div>';
						str += '<div><input type="button" class="button" name="sCSVImport" id="sCSVImport" value="Import CSV" style="float:right;margin:5px 0;" /></div>';
						str += '<div><label>Select column for texts:<label><select class="large" name="sCSVTexts" id="sCSVTexts">' + optionsTexts + '</select></div>';
						str += '<div><label>Select column for values:<label><select class="large" name="sCSVValues" id="sCSVValues">' + optionsValues + '</select></div>';
						str += '<div style="clear:both;"></div>';
						
						return str;
					},
				events : function()
					{
						$( '#sCSVUseHeadline' ).bind( 'click', { obj: this}, function( e )
							{
								e.data.obj.csvData.headline = $( this ).is( ':checked' );
								$.fbuilder.reloadItems();
							});
						$( '#sCSVTexts' ).bind( 'change', { obj: this}, function( e )
							{
								e.data.obj.csvData.text = $( this ).val();
								$.fbuilder.reloadItems();
							});
						$( '#sCSVValues' ).bind( 'change', { obj: this}, function( e )
							{
								e.data.obj.csvData.value = $( this ).val();
								$.fbuilder.reloadItems();
							});
						$( '#sCSVImport' ).bind( 'click', { obj: this}, function( e )
							{
								e.data.obj.csvData.fields = [];
								e.data.obj.csvData.rows = [];
								e.data.obj.csvData.text = 0;
								e.data.obj.csvData.value = 0;
								e.data.obj.csvData.file = '';
								
								var obj = {
									config : {
										header : e.data.obj.csvData.headline,
										dynamicTyping : false,
										delimiter : ( ( e.data.obj.csvData.delimiter != 'tabulator' ) ? e.data.obj.csvData.character : '' ),
										preview : 0
									},
									complete : function( results, file, inputElem, event )
										{
											function setFields( c )
											{
												for ( var i = 0; i < c; i++ )
												{
													e.data.obj.csvData.fields.push( 'Field_' + i );
												}
											};

											if( results.errors.length == 0 )
											{
												if( typeof results.results.fields != 'undefined' )
												{
													e.data.obj.csvData.fields = results.results.fields;
													e.data.obj.csvData.text = e.data.obj.csvData.value = results.results.fields[ 0 ];
												}
												else if( typeof results.results.rows != 'undefined' )
												{
													if( results.results.rows.length )
													{
														setFields( results.results.rows[ 0 ].length );
													}	
												}
												else if( typeof results.results != 'undefined' )
												{
													setFields( results.results[ 0 ].length );
												}
												
												e.data.obj.csvData.text = e.data.obj.csvData.value = 0;
												e.data.obj.csvData.rows = ( typeof results.results.rows != 'undefined' ) ? results.results.rows : results.results;
												e.data.obj.csvData.file = $( '#sCSVLocation' ).val();
												$.fbuilder.reloadItems();
												$( '#datasourceSettings' ).html( e.data.obj.show() );
												e.data.obj.events();
											}
											else
											{
												alert( 'Error, checks the CSV file structure' );
											}	
										}
								};
								$( '#sCSVLocation' ).parse( obj );
							});
					}
			},
			'posttype' : {
				title : 'Post Type',
				posttypeData:{
					posttype : '',
					value 	 : 'ID',
					text 	 : 'post_title',
					last	 : '',
					id 		 : ''
				},
				loadPostTypes : function()
					{
						var me = this,
							e  = $( '#sPostType' ), 
							form_url = e.parents( 'form' ).attr( 'action' );
							
						$.ajax(
							{
								dataType : 'json',
								url : form_url,
								cache : false,
								data : { cffaction: 'get_post_types' },
								success : function( data ){
									var opt = '',
										v,
										selected = ( me.posttypeData.posttype != '' ) ? me.posttypeData.posttype : Object.keys( data )[ 0 ];
									
									for( var index in data )
									{
										opt += '<option value="' + index + '" ' + ( ( index == selected ) ? 'SELECTED' : '') + ' >' + data[ index ] + '</option>';
									}
									
									e.html( opt ).change();
								}
							}
						);
					},		
				show : function( type ) // Type can be 'pair' or 'single', for 'pair' are shown options for text and value, for 'single' is shown only the option for value
					{
						var str = '<div>Select Post Type</div>',
							columns = [ 'ID', 'post_title', 'post_excerpt', 'post_content' ],
							optionsValues = '',
							optionsTexts = '';
						
						for( var i in columns )	
						{
							optionsValues += '<option value="' + columns[ i ] + '" ' + ( ( this.posttypeData.value == columns[ i ] ) ? 'SELECTED' : '' ) + ' >' + columns[ i ] + '</option>';
							optionsTexts += '<option value="' + columns[ i ] + '" ' + ( ( this.posttypeData.text == columns[ i ] ) ? 'SELECTED' : '' ) + ' >' + columns[ i ] + '</option>';
						}
						
						str += '<div><label>Post Type:<label><select class="large" name="sPostType" id="sPostType"></select></div>';
						str += '<div><label>Attribute for values:<label><select class="large" name="sPostTypeValue" id="sPostTypeValue">' + optionsValues + '</select></div>';
						if( type == 'pair' )
						{
							this.posttypeData.id = '';
							str += '<div><label>Attribute for texts:<label><select class="large" name="sPostTypeText" id="sPostTypeText">' + optionsTexts + '</select></div>';
							str += '<div><label>Display the last:<label><input type="text" class="large" name="sPostTypeLast" id="sPostTypeLast" value="' + this.posttypeData.last + '" /></div>';
						}
						else
						{
							str += '<div><label>Type a post ID:<label><input class="large" name="sPostId" id="sPostId" type="text" value="' + this.posttypeData.id + '" /></div>';
						}
						str += '<div style="clear:both;"></div>';
						
						return str;
					},
				events : function()
					{
						$( '#sPostType' ).bind( 'change', { obj: this }, function( e )
							{
								e.data.obj.posttypeData.posttype = $( this ).val();
								$.fbuilder.reloadItems();
							});
						$( '#sPostTypeText' ).bind( 'change', { obj: this }, function( e )
							{
								e.data.obj.posttypeData.text = $( this ).val();
								$.fbuilder.reloadItems();
							});	
						$( '#sPostTypeValue' ).bind( 'change', { obj: this }, function( e )
							{
								e.data.obj.posttypeData.value = $( this ).val();
								$.fbuilder.reloadItems();
							});	
						$( '#sPostTypeLast' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.posttypeData.last = $.trim( $( this ).val() );
								$.fbuilder.reloadItems();
							});	
						$( '#sPostId' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.posttypeData.id = $.trim( $( this ).val() );
								$.fbuilder.reloadItems();
							});	
						this.loadPostTypes();	
					}
			},
			'taxonomy' : {
				title : 'Taxonomy',
				taxonomyData:{
					taxonomy : '',
					value 	 : 'term_id',
					text 	 : 'name',
					id 		 : '',
					slug 	 : ''
				},
				loadTaxonomies : function()
					{
						var me = this,
							e  = $( '#sTaxonomy' ), 
							form_url = e.parents( 'form' ).attr( 'action' );
							
						$.ajax(
							{
								dataType : 'json',
								url : form_url,
								cache : false,
								data : { cffaction: 'get_available_taxonomies' },
								success : function( data ){
									var opt = '',
										v,
										selected = ( me.taxonomyData.taxonomy != '' ) ? me.taxonomyData.taxonomy : Object.keys( data )[ 0 ];
									
									for( var index in data )
									{
										opt += '<option value="' + index + '" ' + ( ( index == selected ) ? 'SELECTED' : '') + ' >' + data[ index ].labels.name + '</option>';
									}
									
									e.html( opt ).change();	
								}
							}
						);
					},
				show : function( type ) // Type can be 'pair' or 'single', for 'pair' are shown options for text and value, for 'single' is shown only the option for value
					{
						var str = '<div>Select Taxonomy</div>',
							columns = [ 'term_id', 'name', 'slug' ],
							optionsValues = '',
							optionsTexts = '';
							
						str += '<div><label>Taxonomy:<label><select class="large" name="sTaxonomy" id="sTaxonomy"></select></div>';
						for( var i in columns )	
						{
							optionsValues += '<option value="' + columns[ i ] + '" ' + ( ( this.taxonomyData.value == columns[ i ] ) ? 'SELECTED' : '' ) + ' >' + columns[ i ] + '</option>';
							optionsTexts += '<option value="' + columns[ i ] + '" ' + ( ( this.taxonomyData.text == columns[ i ] ) ? 'SELECTED' : '' ) + ' >' + columns[ i ] + '</option>';
						}
						
						str += '<div><label>Attribute for values:<label><select class="large" name="sTaxonomyValue" id="sTaxonomyValue">' + optionsValues + '</select></div>';
						if( type == 'pair' )
						{
							this.taxonomyData.id = '';
							this.taxonomyData.slug = '';
							str += '<div><label>Attribute for texts:<label><select class="large" name="sTaxonomyText" id="sTaxonomyText">' + optionsTexts + '</select></div>';
						}
						else
						{
							str += '<div><label>Type a term ID:<label><input class="large" name="sTermId" id="sTermId" type="text" value="' + this.taxonomyData.id + '" /></div>';
							str += '<div><label>or type a term slug:<label><input class="large" name="sTermSlug" id="sTermSlug" type="text" value="' + this.taxonomyData.slug + '" /></div>';
						}
						str += '<div style="clear:both;"></div>';
						
						return str;
					},
				events : function()
					{
						$( '#sTaxonomy' ).bind( 'change', { obj: this }, function( e )
							{
								e.data.obj.taxonomyData.taxonomy = $( this ).val();
								$.fbuilder.reloadItems();
							});
						$( '#sTaxonomyText' ).bind( 'change', { obj: this }, function( e )
							{
								e.data.obj.taxonomyData.text = $( this ).val();
								$.fbuilder.reloadItems();
							});
						$( '#sTaxonomyValue' ).bind( 'change', { obj: this }, function( e )
							{
								e.data.obj.taxonomyData.value = $( this ).val();
								$.fbuilder.reloadItems();
							});
						$( '#sTermId' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.taxonomyData.id = $.trim( $( this ).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sTermSlug' ).bind( 'keyup change', { obj: this }, function( e )
							{
								e.data.obj.taxonomyData.slug = $.trim( $( this ).val() );
								$.fbuilder.reloadItems();
							});
						
						this.loadTaxonomies();	
					}
			},
			'user' : {
				title : 'User Data',
				userData : {
					logged  : false,
					text 	: 'user_nicename',
					value 	: 'ID',
					id 		: '',
					login 	: ''
				},
				show : function( type ) // Type can be 'pair' or 'single', for 'pair' are shown options for text and value, for 'single' is shown only the option for value
					{
						var str = '<div>Display for Users</div>',
							columns = [ 'ID', 'user_login', 'user_nicename', 'display_name', 'user_email' ],
							optionsValues = '',
							optionsTexts = '';
						
						for( var i in columns )	
						{
							optionsValues += '<option value="' + columns[ i ] + '" ' + ( ( this.userData.value == columns[ i ] ) ? 'SELECTED' : '' ) + ' >' + columns[ i ] + '</option>';
							optionsTexts += '<option value="' + columns[ i ] + '" ' + ( ( this.userData.text == columns[ i ] ) ? 'SELECTED' : '' ) + ' >' + columns[ i ] + '</option>';
						}
						
						str += '<div><label>Attribute for values:<label><select class="large" name="sUserValue" id="sUserValue">' + optionsValues + '</select></div>';
						if( type == 'pair' )
						{
							this.userData.logged = false;
							this.userData.id = '';
							this.userData.login = '';
							str += '<div><label>Attribute for texts:<label><select class="large" name="sUserText" id="sUserText">' + optionsTexts + '</select></div>';
						}
						else
						{
							this.userData.text = '';
							str += '<div><label>Display data of logged user:<label><input name="sUserLogged" id="sUserLogged" type="checkbox" ' + ( ( this.userData.logged ) ? 'CHECKED' : '' ) + ' /></div>';
							str += '<div><label> or display data of user ID:<label><input class="large" name="sUserId" id="sUserId" type="text" value="' + this.userData.id + '" ' + ( ( this.userData.logged ) ? 'DISABLED' : '' ) + ' /></div>';
							str += '<div><label> or display data of user with user login:<label><input class="large" name="sUserLogin" id="sUserLogin" type="text" value="' + this.userData.login + '" ' + ( ( this.userData.logged ) ? 'DISABLED' : '' ) + ' /></div>';
						}
						str += '<div style="clear:both;"></div>';
						
						return str;
					},
				events : function()
					{
						$( '#sUserValue' ).bind( 'change', { obj : this }, function( e )
							{
								e.data.obj.userData.value = $(this).val();
								$.fbuilder.reloadItems();
							});
						$( '#sUserText' ).bind( 'change', { obj : this }, function( e )
							{
								e.data.obj.userData.text = $(this).val();
								$.fbuilder.reloadItems();
							});
						$( '#sUserLogged' ).bind( 'click', { obj : this }, function( e )
							{
								var isChecked = $(this).is( ':checked' );
								e.data.obj.userData.logged = isChecked;
								$.fbuilder.reloadItems();
								$( '#sUserId' ).attr( 'disabled',  isChecked );
								$( '#sUserLogin' ).attr( 'disabled',  isChecked );
							});
						$( '#sUserId' ).bind( 'keyup change', { obj : this }, function( e )
							{
								e.data.obj.userData.id = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
						$( '#sUserLogin' ).bind( 'keyup change', { obj : this }, function( e )
							{
								e.data.obj.userData.login = $.trim( $(this).val() );
								$.fbuilder.reloadItems();
							});
					}
			}
		};
	};
	
	$.fbuilder.controls[ 'datasource' ].prototype = {
		isDataSource:true,
		active : '',
		editItemEventsDS : function()
			{
				for( var index in this.list )
				{
					this.list[ index ].events();
				}
				
				$( '#sDataSource' ).bind( 'change', { obj: this }, function( e )
					{
						e.data.obj.active = $(this).val();
						$.fbuilder.editItem( e.data.obj.index  );
						$.fbuilder.reloadItems();
					});
			},
			
		showDataSource : function( list, type )
			{
				if( this.active == '' )
				{
					this.active = list[ 0 ];
				}
				
				var str = '<div style="border:1px dashed #888;padding:5px;position:relative;"><label>Define Datasource</label><div><select class="large" name="sDataSource" id="sDataSource">';
				for( var i in list )
				{
					str += '<option value="' + list[ i ] + '" ' + ( ( list[ i ] == this.active ) ? 'SELECTED' : '' ) + ' >' + this.list[ list[ i ] ].title + '</option>';
				}
				str += '</select></div><div id="datasourceSettings">' + this.list[ this.active ].show( type ) + '</div></div>';
				return str;
			}
	};	