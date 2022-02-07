const {Component} = wp.element;
const {__, setLocaleData} = wp.i18n;

import apiFetch from '@wordpress/api-fetch';
import React from 'react';
import Select from 'react-select';

export default class VersionInput extends Component {

	constructor( props ) {
		super( props );
		this.state = { versions: [], currentDownloadId: props.download_id };
	}

	componentDidMount() {
		this.fetchVersions(this.props.downloadId);
	}

	componentDidUpdate() {
		this.fetchVersions(this.props.downloadId);
	}

	fetchVersions( downloadId) {
		if( typeof downloadId !== 'undefined' && downloadId != this.state.currentDownloadId ) {
			apiFetch( { url: dlmBlocks.ajax_getVersions + "&download_id=" + downloadId } ).then( results => {
				results.unshift({value: 0, label: __('Latest version', 'download-monitor')});
				this.setState({versions: results,  currentDownloadId: downloadId});
			} );
		}

	}

	render() {

		const valueFromId = (opts, id) => opts.find(o => o.value === id);

		return (
			<div>
				<Select
					value={valueFromId( this.state.versions, this.props.selectedVersionId )}
					onChange={(selectedOption) =>  this.props.onChange(selectedOption.value)}
					options={this.state.versions}
					isSearchable="true"
					isDisabled={(typeof this.props.downloadId === 'undefined')}
				 />
			</div>
		);
	}

}
