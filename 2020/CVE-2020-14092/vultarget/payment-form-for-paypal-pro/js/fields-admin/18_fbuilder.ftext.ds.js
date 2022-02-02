	$.fbuilder.typeList.push(
		{
			id:"ftextds",
			name:"Line Text DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'ftextds' ]=function(){  this.init();  };
	$.extend(
		$.fbuilder.controls[ 'ftextds' ].prototype,
		$.fbuilder.controls[ 'ftext' ].prototype,
		{
			ftype:"ftextds",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'ftext' ].prototype.editItemEvents.call(this);					
				}
		}
	);