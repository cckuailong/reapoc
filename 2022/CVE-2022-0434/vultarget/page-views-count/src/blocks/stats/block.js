/**
 * BLOCK: page-views-count
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
//import './style.scss';
//import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { BlockControls, AlignmentToolbar, InspectorControls } = wp.blockEditor;
const { Placeholder, PanelBody, PanelRow, ToggleControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;
const { Fragment } = wp.element;
const { select } = wp.data;

import IconPageView from './icon.svg';

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
registerBlockType('page-views-count/stats', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __('Page Views', 'page-views-count'), // Block title.
	description: __('Show all time views and views today', 'page-views-count'),
	icon: {
		src: IconPageView,
		foreground: '#24b6f1',
	}, // Block icon, can get from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'a3rev-blocks', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	supports: {
		multiple: false,
	},
	keywords: [
		__('Page Views Count', 'page-views-count'),
		__('Views', 'page-views-count'),
		__('Stats', 'page-views-count'),
	],
	example: {
		attributes: {
			isPreview: true,
		},
	},

	attributes: {
		align: {
			type: 'string',
		},
		postID: {
			type: 'string',
		},
		isDisabled: {
			type: 'boolean',
			default: true,
		},
		/**
		 * For previewing?
		 */
		isPreview: {
			type: 'boolean',
			default: false,
		},
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	edit: ({ attributes, setAttributes }) => {

		if ( attributes.isPreview ) {
			return ( <img
				src={ pvcblock.preview }
				alt={ __( 'Page Views Count Preview', 'page-views-count' ) }
				style={ {
					width: '100%',
					height: 'auto',
				} }
			/> );
		}

		const { align, isDisabled } = attributes;
		const postID = select('core/editor').getCurrentPostId();

		function onChangeAlign(newAlign) {
			setAttributes({ align: newAlign });
		}

		function toggleManualShow(isDisabled) {
			setAttributes({ isDisabled: !isDisabled });
			const activateOption = document.querySelector('#a3_pvc_activated');

			if (isDisabled) {
				activateOption.removeAttribute('checked');
				activateOption.setAttribute('disabled', true);
			} else {
				activateOption.setAttribute('checked', true);
				activateOption.removeAttribute('disabled');
			}
		}

		attributes.postID = postID;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={__('PVC Settings', 'page-views-count')} opened="true">
						<PanelRow>
							<ToggleControl
								label={__('Manual Show', 'page-views-count')}
								help={
									isDisabled
										? __('Using global show', 'page-views-count')
										: __('Using manual show', 'page-views-count')
								}
								checked={!isDisabled}
								onChange={toggleManualShow}
							/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
				{isDisabled ? (
					<Placeholder label={__('Page Views', 'page-views-count')}>
						{__('Need to active from Settings of this block', 'page-views-count')}
					</Placeholder>
				) : (
					<Fragment>
						<BlockControls>
							<AlignmentToolbar value={align} onChange={onChangeAlign} />
						</BlockControls>

						<ServerSideRender block="page-views-count/stats-editor" attributes={attributes} />
					</Fragment>
				)}
			</Fragment>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save() {
		// Rendering in PHP
		return null;
	},
});
