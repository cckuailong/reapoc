	$.fbuilder.typeList.push(
		{
			id:"femailds",
			name:"Email DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'femailds'] = function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'femailds' ].prototype,
		$.fbuilder.controls[ 'femail' ].prototype,
		{
			ftype:"femailds",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'femail' ].prototype.editItemEvents.call(this);					
				}
	});