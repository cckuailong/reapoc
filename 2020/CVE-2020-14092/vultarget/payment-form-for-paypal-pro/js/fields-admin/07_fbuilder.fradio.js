		$.fbuilder.typeList.push(
			{
				id:"fradio",
				name:"Radio Buttons",
				control_category:1
			}
		);
		$.fbuilder.controls[ 'fradio' ] = function(){};
		$.extend(
			$.fbuilder.controls[ 'fradio' ].prototype,
			$.fbuilder.controls[ 'ffields' ].prototype,
			{
				title:"Select a Choice",
				ftype:"fradio",
				layout:"one_column",
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