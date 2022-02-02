	$.fbuilder.typeList.push(
		{
			id:"ftextareads",
			name:"Text Area DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'ftextareads' ] = function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'ftextareads' ].prototype,
		$.fbuilder.controls[ 'ftextarea' ].prototype,
		{
			ftype:"ftextareads",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'ftextarea' ].prototype.editItemEvents.call(this);					
				}		
		}
	);