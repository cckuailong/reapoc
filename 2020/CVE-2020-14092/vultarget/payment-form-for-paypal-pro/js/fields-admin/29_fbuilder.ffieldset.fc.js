	$.fbuilder.typeList.push(
		{
			id:"ffieldset",
			name:"Fieldset",
			control_category:10
		}
	);
	$.fbuilder.controls[ 'ffieldset' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'ffieldset' ].prototype,
		$.fbuilder.controls[ 'fcontainer' ].prototype,
		{
			title:"Untitled",
			ftype:"ffieldset",
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