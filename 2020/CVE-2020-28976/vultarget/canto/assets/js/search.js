var Keyword = React.createClass({
	getInitialState: function() {
        return {
            keyword: null
        };
    },

	onClick: function(e) {
		React.findDOMNode(this.refs.fbcSearch).value = "";
	},

	onChange: function(e) {
        var keyword = e.target.value;
        this.setState({ keyword: keyword });
    },

	handleSubmit: function(e) {
		React.findDOMNode(this.refs.fbcSearch).value = "";
		this.setState({ keyword: '' });
		e.preventDefault();
	},

    render: function() {
		var submit = this.props.onValueChange.bind(this,this.state.keyword);
        return (
			<form id="searchForm" onSubmit={this.handleSubmit}>
				<input onChange={this.onChange} onClick={this.onClick} value={this.state.keyword} placeholder="Global Search" ref="fbcSearch" />
				<i className="icon-search" onClick={submit}></i>
				<button onClick={submit}></button>
			</form>
        );
    }
});

var Search = React.createClass({
	handleChange: function(e) {
		this.props.onValueChange(e);
	},

    render: function() {
        return (
			<div>
                <Keyword onValueChange={this.handleChange} />
			</div>
        );
    }
});

var Filter = React.createClass({
	getInitialState: function() {
        return {
            filter: this.props.filter
        };
    },	   
	
	componentWillReceiveProps: function(nextProps) {
		if (nextProps.filter != this.state.filter) {
			var value = nextProps.filter;
			if (value == '')
				value = 'all';
			jQuery('[class^=icon-type-]').each(function(){
				jQuery(this).css('opacity', 0.4);
			});
			jQuery('.icon-type-' + value).css('opacity', 1);
			this.setState({
				filter: nextProps.filter
			});
		}
	},
    
	componentDidMount: function() {		
		jQuery('[class^=icon-type-]').each(function(){
			jQuery(this).css('opacity', 0.4);
		});
		jQuery('.icon-type-all').css('opacity', 1);
	},
	
	changeFilterValue: function(value) {
		jQuery('[class^=icon-type-]').each(function(){
			jQuery(this).css('opacity', 0.4);
		});
		jQuery('.icon-type-' + value).css('opacity', 1);
		this.setState({filter: value});
		this.props.onValueChange(value);
	},
	
	displayFilter: function() {
		if (this.state.filter == '' || this.state.filter == 'all')
			return 'All Content Type';
		else {
			var caption = this.state.filter;
			return caption.charAt(0).toUpperCase() + caption.slice(1); 
		}
	},
	
	hideShow: function() {
		var panel = jQuery('#filterHideShowPanel');
	    if (panel.is(':visible')){
	        panel.fadeOut('fast');			
	    } else {
	        panel.fadeIn('fast');			
	    }
	},
	
    render: function() {
    	return (
			<div style={{'margin-top': '5px'}}>
				<a className="btn" id="filterHideShow" onClick={this.hideShow}>	
					<i className="icon-filter" ></i>		
					<span>{this.displayFilter()}</span>		
				</a>	
				<span id="filterHideShowPanel" style={{'display': 'none'}}>
					<i className="icon-type-all" onClick={this.changeFilterValue.bind(this, 'all')}></i>
					<i className="icon-type-image" onClick={this.changeFilterValue.bind(this, 'image')}></i>
					<i className="icon-type-video" onClick={this.changeFilterValue.bind(this, 'video')}></i>
					<i className="icon-type-audio" onClick={this.changeFilterValue.bind(this, 'audio')}></i>
					<i className="icon-type-document" onClick={this.changeFilterValue.bind(this, 'document')}></i>
					<i className="icon-type-presentation" onClick={this.changeFilterValue.bind(this, 'presentation')}></i>
					<i className="icon-type-other" onClick={this.changeFilterValue.bind(this, 'other')}></i>				
				</span>
			</div>
        );
    }
});

