	$.fbuilder.typeList.push(
		{
			id:"fPhoneds",
			name:"Phone DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'fPhoneds' ] = function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'fPhoneds' ].prototype,
		$.fbuilder.controls[ 'fPhone' ].prototype,
		{
			ftype:"fPhoneds",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'fPhone' ].prototype.editItemEvents.call(this);	
				}
		}
	);