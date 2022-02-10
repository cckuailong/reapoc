/**
 * External dependencies
 */
import React, { createRef, PureComponent } from 'react';
import PropTypes from 'prop-types';

/**
 * Wordpress dependencies
 */
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { LabelWithModal } from '@moderntribe/common/elements';
import './style.pcss';

class AttendeesRegistration extends PureComponent {
	static propTypes = {
		helperText: PropTypes.string.isRequired,
		iframeURL: PropTypes.string.isRequired,
		isDisabled: PropTypes.bool.isRequired,
		isModalOpen: PropTypes.bool.isRequired,
		label: PropTypes.string.isRequired,
		linkText: PropTypes.string.isRequired,
		modalTitle: PropTypes.string.isRequired,
		onClick: PropTypes.func.isRequired,
		onClose: PropTypes.func.isRequired,
		onIframeLoad: PropTypes.func.isRequired,
		showHelperText: PropTypes.bool.isRequired,
	};

	constructor( props ) {
		super( props );
		this.iframe = createRef();
	}

	render() {
		const {
			helperText,
			iframeURL,
			isDisabled,
			isModalOpen,
			label,
			linkText,
			modalTitle,
			onClick,
			onClose,
			onIframeLoad,
			showHelperText,
			...restProps
		} = this.props;

		const modalContent = (
			<div className="tribe-editor__attendee-registration__modal-content">
				<iframe
					className="tribe-editor__attendee-registration__modal-iframe"
					onLoad={ () => onIframeLoad( this.iframe.current ) }
					ref={ this.iframe }
					src={ iframeURL }
					title={ __( 'Attendee registration', 'event-tickets' ) }
				>
				</iframe>
				<div className="tribe-editor__attendee-registration__modal-overlay">
					<Spinner />
				</div>
			</div>
		);

		return (
			<div className="tribe-editor__attendee-registration">
				<LabelWithModal
					className="tribe-editor__attendee-registration__label-with-modal"
					isOpen={ isModalOpen }
					label={ label }
					modalButtonDisabled={ isDisabled }
					modalButtonLabel={ linkText }
					modalClassName="tribe-editor__attendee-registration__modal"
					modalContent={ modalContent }
					modalTitle={ modalTitle }
					onClick={ onClick }
					onClose={ onClose }
					{ ...restProps }
				/>
				{ showHelperText && (
					<span className="tribe-editor__attendee-registration__helper-text">
						{ helperText }
					</span>
				) }
			</div>
		);
	}
}

export default AttendeesRegistration;
