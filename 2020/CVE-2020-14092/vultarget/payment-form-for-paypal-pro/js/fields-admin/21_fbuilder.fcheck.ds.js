	$.fbuilder.typeList.push(
		{
			id:"fcheckds",
			name:"Checkboxes DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'fcheckds' ] = function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'fcheckds' ].prototype,
		$.fbuilder.controls[ 'fcheck' ].prototype,
		{
			ftype:"fcheckds",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'fcheck' ].prototype.editItemEvents.call(this);						
				}
	});