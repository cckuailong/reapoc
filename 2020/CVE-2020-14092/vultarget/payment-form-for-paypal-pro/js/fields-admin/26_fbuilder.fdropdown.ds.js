	$.fbuilder.typeList.push(
		{
			id:"fdropdownds",
			name:"Dropdown DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'fdropdownds' ] = function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'fdropdownds' ].prototype,
		$.fbuilder.controls[ 'fdropdown' ].prototype,
		{
			ftype:"fdropdownds",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'fdropdown' ].prototype.editItemEvents.call(this);						
				}
		}
	);