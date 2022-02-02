	$.fbuilder.typeList.push(
		{
			id:"fPageBreak",
			name:"Page break",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fPageBreak' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fPageBreak' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Page Break",
			ftype:"fPageBreak",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{ 				    
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
				}
	});