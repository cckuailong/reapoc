/**
 * External Dependencies
 */
import { Modal, MenuGroup, MenuItemsChoice, Button, Notice, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Input, Select } from '@moderntribe/common/elements';
import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import './style.pcss';

export default class MoveModal extends PureComponent {
	static propTypes = {
		hasSelectedPost: PropTypes.bool.isRequired,
		hideModal: PropTypes.func.isRequired,
		initialize: PropTypes.func.isRequired,
		isFetchingPosts: PropTypes.bool.isRequired,
		isModalSubmitting: PropTypes.bool.isRequired,
		onPostSelect: PropTypes.func.isRequired,
		onPostTypeChange: PropTypes.func.isRequired,
		onSearchChange: PropTypes.func.isRequired,
		onSubmit: PropTypes.func.isRequired,
		postOptions: PropTypes.arrayOf( PropTypes.object ),
		postTypeOptions: PropTypes.arrayOf( PropTypes.object ),
		postTypeOptionValue: PropTypes.object,
		postValue: PropTypes.string.isRequired,
		search: PropTypes.string.isRequired,
		title: PropTypes.string.isRequired,
	}

	static defaultProps = {
		title: __( 'Move Ticket Types', 'event-tickets' ),
	}

	componentDidMount() {
		this.props.initialize();
	}

	renderPostTypes = () => {
		if ( this.props.isFetchingPosts ) {
			return <Spinner />;
		}

		return (
			this.props.postOptions.length
				? (
					<MenuGroup>
						<MenuItemsChoice
							choices={ this.props.postOptions }
							value={ this.props.postValue }
							onSelect={ this.props.onPostSelect }
						/>
					</MenuGroup>
				)
				: (
					<Notice
						isDismissible={ false }
						status="warning"
					>
						{
							__( 'No posts found', 'event-tickets' )
						}
					</Notice>
				)

		);
	}

	render() {
		return (
			<Modal
				title={ this.props.title }
				onRequestClose={ this.props.hideModal }
				className="tribe-editor__tickets__move-modal"
			>
				<label htmlFor="post_type">
					{ __( 'You can optionally focus on a specific post type:', 'event-tickets' ) }
				</label>
				<Select
					id="post_type"
					options={ this.props.postTypeOptions }
					onChange={ this.props.onPostTypeChange }
					value={ this.props.postTypeOptionValue }
				/>

				<label htmlFor="search">
					{ __(
						'You can also enter keywords to help find the target event by title or description',
						'event-tickets',
					) }
				</label>
				<Input
					id="search"
					type="text"
					onChange={ this.props.onSearchChange }
					value={ this.props.search }
				/>

				<label>
					{ __( 'Select the post you wish to move the ticket type to:', 'event-tickets' ) }
				</label>
				{ this.renderPostTypes() }

				<footer>
					<Button
						isLarge
						isPrimary
						isBusy={ this.props.isModalSubmitting }
						disabled={ ! this.props.hasSelectedPost || this.props.isFetchingPosts }
						onClick={ this.props.onSubmit }
					>
						{ __( 'Finish!', 'event-tickets' ) }
					</Button>
				</footer>
			</Modal>
		);
	}
}
