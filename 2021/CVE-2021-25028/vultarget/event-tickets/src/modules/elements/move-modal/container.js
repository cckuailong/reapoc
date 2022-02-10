/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import { withStore } from '@moderntribe/common/hoc';
import * as selectors from '@moderntribe/tickets/data/shared/move/selectors';
import { INITIALIZE_MODAL, SUBMIT_MODAL } from '@moderntribe/tickets/data/shared/move/types';
import { hideModal, setModalData } from '@moderntribe/tickets/data/shared/move/actions';
import Template from './template';

const mapStateToProps = ( state ) => ( {
	hasSelectedPost: selectors.hasSelectedPost( state ),
	isFetchingPosts: selectors.isFetchingPosts( state ),
	isFetchingPostTypes: selectors.isFetchingPostTypes( state ),
	isModalSubmitting: selectors.isModalSubmitting( state ),
	postOptions: selectors.getPostOptions( state ),
	postTypeOptions: selectors.getPostTypeOptions( state ),
	postTypeOptionValue: selectors.getPostTypeOptionValue( state ),
	postValue: selectors.getModalTarget( state ),
	search: selectors.getModalSearch( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	initialize: () => dispatch( { type: INITIALIZE_MODAL } ),
	hideModal: () => dispatch( hideModal() ),
	onSearchChange: ( e ) => dispatch( setModalData( { search_terms: e.target.value } ) ),
	onPostTypeChange: ( option ) => dispatch( setModalData( { post_type: option.value } ) ),
	onPostSelect: ( value ) => dispatch( setModalData( { target_post_id: value } ) ),
	onSubmit: () => dispatch( { type: SUBMIT_MODAL } ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
)( Template );

