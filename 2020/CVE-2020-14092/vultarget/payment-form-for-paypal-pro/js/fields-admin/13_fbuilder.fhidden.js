	$.fbuilder.typeList.push(
		{
			id:"fhidden",
			name:"Hidden",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fhidden' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fhidden' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Hidden",
			ftype:"fhidden",
			predefined:"",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{ 				    
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
				}			
	});