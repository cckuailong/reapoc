var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
	TextControl = wp.components.TextControl,
	InspectorControls = wp.editor.InspectorControls;

registerBlockType( 'ultimate-faqs/ewd-ufaq-display-faq-block', {
	title: 'Display FAQs',
	icon: 'editor-help',
	category: 'ewd-ufaq-blocks',
	attributes: {
		post_count: { type: 'string' },
		include_category: { type: 'string' },
		exclude_category: { type: 'string' },
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push(
			el( InspectorControls, {},
				el( TextControl, {
					label: 'Number of FAQs',
					value: props.attributes.post_count,
					onChange: ( value ) => { props.setAttributes( { post_count: value } ); },
				} ),
				el( TextControl, {
					label: 'Include Category',
					value: props.attributes.include_category,
					onChange: ( value ) => { props.setAttributes( { include_category: value } ); },
				} ),
				el( TextControl, {
					label: 'Exclude Category',
					value: props.attributes.exclude_category,
					onChange: ( value ) => { props.setAttributes( { exclude_category: value } ); },
				} )
			),
		);
		returnString.push( el( 'div', { class: 'ewd-ufaq-admin-block ewd-ufaq-admin-block-display-faqs' }, 'Display FAQs Block' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );

registerBlockType( 'ultimate-faqs/ewd-ufaq-search-block', {
	title: 'Search FAQs',
	icon: 'editor-help',
	category: 'ewd-ufaq-blocks',
	attributes: {
		include_category: { type: 'string' },
		exclude_category: { type: 'string' },
		show_on_load: { type: 'string' },
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push(
			el( InspectorControls, {},
				el( TextControl, {
					label: 'Include Category',
					value: props.attributes.include_category,
					onChange: ( value ) => { props.setAttributes( { include_category: value } ); },
				} ),
				el( TextControl, {
					label: 'Exclude Category',
					value: props.attributes.exclude_category,
					onChange: ( value ) => { props.setAttributes( { exclude_category: value } ); },
				} ),
				el( TextControl, {
					label: 'Show all FAQs on Page Load? (Yes or No)',
					value: props.attributes.show_on_load,
					onChange: ( value ) => { props.setAttributes( { show_on_load: value } ); },
				} )
			),
		);
		returnString.push( el( 'div', { class: 'ewd-ufaq-admin-block ewd-ufaq-admin-block-search-faqs' }, 'Search FAQs Block' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );

registerBlockType( 'ultimate-faqs/ewd-ufaq-submit-faq-block', {
	title: 'Submit FAQ',
	icon: 'editor-help',
	category: 'ewd-ufaq-blocks',
	attributes: {
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push( el( 'div', { class: 'ewd-ufaq-admin-block ewd-ufaq-admin-block-submit-faq' }, 'Submit Question Block' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );

registerBlockType( 'ultimate-faqs/ewd-ufaq-recent-faqs-block', {
	title: 'Recent FAQs',
	icon: 'editor-help',
	category: 'ewd-ufaq-blocks',
	attributes: {
		post_count: { type: 'string' },
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push(
			el( InspectorControls, {},
				el( TextControl, {
					label: 'Number of FAQs',
					value: props.attributes.post_count,
					onChange: ( value ) => { props.setAttributes( { post_count: value } ); },
				} )
			),
		);
		returnString.push( el( 'div', { class: 'ewd-ufaq-admin-block ewd-ufaq-admin-block-recent-faqs' }, 'Recent FAQs Block' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );

registerBlockType( 'ultimate-faqs/ewd-ufaq-popular-faqs-block', {
	title: 'Popular FAQs',
	icon: 'editor-help',
	category: 'ewd-ufaq-blocks',
	attributes: {
		post_count: { type: 'string' },
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push(
			el( InspectorControls, {},
				el( TextControl, {
					label: 'Number of FAQs',
					value: props.attributes.post_count,
					onChange: ( value ) => { props.setAttributes( { post_count: value } ); },
				} )
			),
		);
		returnString.push( el( 'div', { class: 'ewd-ufaq-admin-block ewd-ufaq-admin-block-popular-faqs' }, 'Popular FAQs Block' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );


