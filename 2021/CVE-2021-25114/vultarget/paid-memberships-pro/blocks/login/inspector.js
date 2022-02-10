/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { PanelBody, SelectControl, ToggleControl } = wp.components;
const { InspectorControls } = wp.blockEditor;

/**
 * Create an Inspector Controls wrapper Component
 */
export default class Inspector extends Component {
	constructor() {
		super(...arguments);
	}

	render() {
		const { attributes, setAttributes } = this.props;
		const {
			display_if_logged_in,
			show_menu,
			show_logout_link,
			location,
		} = attributes;
		return (
			<InspectorControls>
				<PanelBody>
					<ToggleControl
						label={__("Display 'Welcome' content when logged in.", "paid-memberships-pro")}
						checked={display_if_logged_in}
						onChange={(value) => {
							this.props.setAttributes({
								display_if_logged_in: value,
							});
						}}
					/>
					<ToggleControl
						label={__("Display the 'Log In Widget' menu.", "paid-memberships-pro")}
						help={__("Assign the menu under Appearance > Menus.")}
						checked={show_menu}
						onChange={(value) => {
							this.props.setAttributes({
								show_menu: value,
							});
						}}
					/>
					<ToggleControl
						label={__("Display a 'Log Out' link.", "paid-memberships-pro")}
						checked={show_logout_link}
						onChange={(value) => {
							this.props.setAttributes({
								show_logout_link: value,
							});
						}}
					/>
				</PanelBody>
			</InspectorControls>
		);
	}
}
