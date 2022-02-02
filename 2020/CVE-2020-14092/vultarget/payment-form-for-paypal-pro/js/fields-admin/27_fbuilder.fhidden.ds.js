	$.fbuilder.typeList.push(
		{
			id:"fhiddends",
			name:"Hidden DS",
			control_category:20
		}
	);
	$.fbuilder.controls[ 'fhiddends' ]=function(){ this.init(); };
	$.extend(
		$.fbuilder.controls[ 'fhiddends' ].prototype,
		$.fbuilder.controls[ 'fhidden' ].prototype,
		{
			ftype:"fhiddends",
			display:function()
				{
					return '- available only in commercial version of plugin -';
				},
			editItemEvents:function()
				{
					$.fbuilder.controls[ 'fhidden' ].prototype.editItemEvents.call(this);
					this.editItemEventsDS();
				}
		}
	);