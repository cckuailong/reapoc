	$.fbuilder.typeList.push(
		{
			id:"fcheck",
			name:"Checkboxes",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fcheck' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fcheck' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Check All That Apply",
			ftype:"fcheck",
			layout:"one_column",
			required:false,
			showDep:false,
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
				}
	});