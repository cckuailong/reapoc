const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { SelectControl, PanelBody, ServerSideRender, Disabled } = wp.components;
const { InspectorControls } = wp.editor;
const { locationsEnabled, locations } = rtb_blocks;

registerBlockType( 'restaurant-reservations/booking-form', {
	title: __( 'Booking Form', 'restaurant-reservations' ),
	icon: 'calendar',
	category: 'rtb-blocks',
	attributes: {
		location: {
			type: 'number',
			default: 0
		}
	},
	supports: {
		html: false,
		reusable: false,
		multiple: false,
	},
	edit( { attributes, setAttributes } ) {
		const { location } = attributes;

		return (
			<div>
				{locationsEnabled ? (
					<InspectorControls>
						 <PanelBody>
							<SelectControl
								label={ __( 'Location' ) }
								value={ location }
								onChange={ ( location ) => setAttributes( { location: parseInt( location, 10 ) } ) }
								options={ locations }
							/>
						</PanelBody>
					</InspectorControls>
				) : '' }
				<Disabled>
					<ServerSideRender block="restaurant-reservations/booking-form" attributes={ attributes } />
				</Disabled>
			</div>
		);
	},
	save() {
		return null;
	},
} );
