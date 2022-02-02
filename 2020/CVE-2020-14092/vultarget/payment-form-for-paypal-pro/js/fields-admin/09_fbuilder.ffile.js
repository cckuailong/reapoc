	$.fbuilder.typeList.push(
		{
			id:"ffile",
			name:"Upload File",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'ffile' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'ffile' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Untitled",
			ftype:"ffile",
			required:false,
			size:"medium",
			accept:"",
			upload_size:"",
			multiple:false,
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
				}
	});