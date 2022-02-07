const {Component} = wp.element;

import React from 'react';
import Select from 'react-select';

export default class TemplateInput extends Component {

	constructor( props ) {
		super( props );
		this.state = { templates: [] };
	}

	componentDidMount() {
		this.setState( {templates: JSON.parse(this.props.templatesStr) } );
	}

	render() {
		const valueFromId = (opts, id) => opts.find(o => o.value === id);

		return (
			<div>
				<Select
					value={valueFromId( this.state.templates, this.props.selectedTemplate )}
					onChange={(selectedOption) =>  this.props.onChange(selectedOption.value)}
					options={this.state.templates}
					isSearchable="true"
				 />
			</div>
		);
	}

}
