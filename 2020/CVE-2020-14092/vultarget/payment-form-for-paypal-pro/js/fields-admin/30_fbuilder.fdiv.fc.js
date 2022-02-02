	$.fbuilder.typeList.push(
		{
			id:"fdiv",
			name:"Div",
			control_category:10
		}
	);
	$.fbuilder.controls[ 'fdiv' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fdiv' ].prototype,
		$.fbuilder.controls[ 'fcontainer' ].prototype,
		{
			title: 'div', 
			ftype:"fdiv",
			fields:[],
			columns:1,
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'fcontainer' ].prototype.editItemEvents.call(this);
				}
	});