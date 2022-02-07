/**
 * BLOCK: Job Listing Block
 *
 * Registering a SJB block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 *
 * @since 2.8.0
 */

//  Import CSS.
import './editor.scss';
import './style.scss';
const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { createElement } = wp.element;
const { InspectorControls } = wp.blockEditor; //Block inspector wrapper
const { serverSideRender } = wp; //server-side renderer
const { TextControl, SelectControl } = wp.components; //WordPress form inputs

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'cgb/block-sjb-shortcode-block', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'SJB Listing' ), // Block title.
	icon: 'clipboard', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'widgets', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'SJB Job Listing' ),
		__( 'Job Listing' ),
		__( 'SJB Shortcode' ),
	],
	attributes: {
		sjb_layout: {
			type: 'string',
			default: 'list-view',
		},
		numberofposts: {
			type: 'number',
			default: 10,
		},
		order: {
			type: 'string',
			default: 'DESC',
		},
		jobsearch: {
			type: 'boolean',
			default: true,
		},
	},
	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	// eslint-disable-next-line indent
	edit: ( props ) => {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		//Function to update Number of Posts attribute
		function changeNumberofposts( numberofposts ) {
			setAttributes( { numberofposts } );
		}

		//Function to change SJB Layout(list/grid)
		function changeLayout( sjb_layout ) {
			setAttributes( { sjb_layout } );
		}

		//Function to change Job Listing order(ASC/DESC)
		function changeJobListingOrder( order ) {
			setAttributes( { order } );
		}

		//Function to enable/disable Search Box
		function updateSearchBoxView( jobsearch ) {
			setAttributes( { jobsearch } );
		}
		// Creates a <p class='wp-block-cgb-block-sjb-shortcode-block'></p>.
		return createElement( 'div', {}, [
			createElement( serverSideRender, {
				block: 'cgb/block-sjb-shortcode-block',
				attributes: attributes,
			} ),
			//Block inspector
			createElement( InspectorControls, {},
				[
					createElement( TextControl, {
						value: attributes.numberofposts,
						label: __( 'Number of Posts' ),
						onChange: changeNumberofposts,
						type: 'number',
						min: 1,
						step: 1,
					} ),
					createElement( SelectControl, {
						value: attributes.sjb_layout,
						label: __( 'Job Layout' ),
						onChange: changeLayout,
						options: [
							{ value: 'list-view', label: 'List View' },
							{ value: 'grid-view', label: 'Grid View' },
						],
					} ),
					createElement( SelectControl, {
						value: attributes.order,
						label: __( 'Job Listing Order' ),
						onChange: changeJobListingOrder,
						options: [
							{ value: 'DESC', label: 'Descending' },
							{ value: 'ASC', label: 'Ascending' },
						],
					} ),
					createElement( SelectControl, {
						value: attributes.jobsearch,
						label: __( 'Job Search' ),
						onChange: updateSearchBoxView,
						options: [
							{ value: true, label: 'Enable' },
							{ value: false, label: 'Disable' },
						],
					} ),
				]
			),
		] );
	},
	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: ( props ) => {
		return null;
	},
} );
