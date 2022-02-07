const {Component} = wp.element;

import apiFetch from '@wordpress/api-fetch';
import React from 'react';
import Select from 'react-select';

export default class DownloadInput extends Component {

	constructor( props ) {
		super( props );
		this.state = { downloads: [] };
	}

	componentDidMount() {
		apiFetch( { url: dlmBlocks.ajax_getDownloads } ).then( results => {
			this.setState({downloads: results });
		} );
	}

	render() {
		const valueFromId = (opts, id) => opts.find(o => o.value === id);

		return (
			<div>
				<Select
					value={valueFromId( this.state.downloads, this.props.selectedDownloadId )}
					onChange={(selectedOption) =>  this.props.onChange(selectedOption.value)}
					options={this.state.downloads}
					isSearchable="true"
				 />
			</div>
		);
	}

}
