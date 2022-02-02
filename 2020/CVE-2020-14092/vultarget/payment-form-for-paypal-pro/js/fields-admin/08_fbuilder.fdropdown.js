	$.fbuilder.typeList.push(
		{
			id:"fdropdown",
			name:"Dropdown",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fdropdown' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fdropdown' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Select a Choice",
			ftype:"fdropdown",
			size:"medium",
			required:false,
			choiceSelected:"",
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