	$.fbuilder.typeList.push(
		{
			id:"fSectionBreak",
			name:"Section break",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fSectionBreak' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fSectionBreak' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Section Break",
			ftype:"fSectionBreak",
			userhelp:"A description of the section goes here.",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{ 				    
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
				}
	});