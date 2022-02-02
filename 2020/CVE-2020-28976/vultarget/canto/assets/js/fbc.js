var FBC = React.createClass({
    getInitialState: function() {
		return {
			album: [{
                name: 'Recent Images'
            }],
            search: '',
            path: args.FBC_URL +"/includes/lib/get.php?subdomain="+ args.subdomain +"&token="+ args.token +"&limit=36&start=0",
            filter: '',
            type: 'library'
		};
	},

    handleChange: function(e) {
        this.setState({
      			album: e,
            path: args.FBC_URL +"/includes/lib/get.php?subdomain="+ args.subdomain +"&album="+ e.id +"&token="+ args.token,
            filter: '',
            search: '',
            type: 'library'
		});
    },

    handleSearch: function(e) {
        this.setState({
            search: e,
            filter: '',
            album: {name: "Recent Images"},
            type: 'search'
		});
    },

    handleFilter: function(e) {
        this.setState({
        	filter: e,
        	type: 'filter'
		});
    },

    toggle: function(e) {
        var tree = jQuery('#fbc-tree');
	    if (tree.is(':visible')){
	        tree.animate({"left":"-250px"}, "fast").hide();
			jQuery('#hideShow>i').addClass('fa-bars');
			jQuery('#hideShow>i').removeClass('fa-close');
			jQuery('#fbc-loop').css({'margin-left':'0px' });
	    } else {
	        tree.animate({"left":"0px"}, "fast").show();
			jQuery('#hideShow>i').removeClass('fa-bars');
			jQuery('#hideShow>i').addClass('fa-close');
			jQuery('#fbc-loop').css({'margin-left':'250px' });
	    }
    },

    library: function(e) {
        this.setState({
            album: {name: "Flight Library"},
            filter: '',
            type: 'library'
		});
    },

    render: function() {
        return (
            <div>
                <div className="searchRow">
                    <a className="btn" id="hideShow" onClick={this.toggle}> <i className="icon-library"></i> Library</a>
                    <Search onValueChange={this.handleSearch} />
                    <Filter onValueChange={this.handleFilter} filter={this.state.filter}/>
                </div>

                <div id="fbc-tree" className="collapse">
                    <Tree onValueChange={this.handleChange} library={this.library} />
                </div>
                <div id="fbc-loop">
                    <FlightImages search={this.state.search} album={this.state.album} path={this.state.path} filter={this.state.filter} type={this.state.type}/>
                </div>
            </div>
        );
    }
});

React.render(<FBC />, document.getElementById('fbc-react') );
