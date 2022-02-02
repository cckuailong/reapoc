		$.fbuilder.typeList.push(
			{
				id:"fcurrency",
				name:"Currency",
				control_category:1
			}
		);
        $.fbuilder.controls[ 'fcurrency' ] = function(){};
		$.extend(
			$.fbuilder.controls[ 'fcurrency' ].prototype, 
			$.fbuilder.controls[ 'ffields' ].prototype,
			{
				title:"Currency",
				ftype:"fcurrency",
				predefined:"",
				predefinedClick:false,
				required:false,
				size:"small",
				readonly:false,
				currencySymbol:"$",
				currencyText:"USD",
				thousandSeparator:",",
				centSeparator:".",
				min:"",
				max:"",
				formatDynamically:false,
				display:function()
					{
						return '- available only in commercial version of plugin -';
					},
				editItemEvents:function()
					{						
						$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
					}
		});