	$.fbuilder.typeList.push(
		{
			id:"fCommentArea",
			name:"Instruct. Text",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fCommentArea' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fCommentArea' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Comments here",
			ftype:"fCommentArea",
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