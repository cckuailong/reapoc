	$.fbuilder.typeList.push(
		{
			id:"fradiods",
			name:"Radio Btns DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'fradiods' ] = function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'fradiods' ].prototype,
		$.fbuilder.controls[ 'fradio' ].prototype,
		{
			ftype:"fradiods",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'fradio' ].prototype.editItemEvents.call(this);						
				}
		}
	);