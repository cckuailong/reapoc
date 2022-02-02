var Children = React.createClass({
	render: function() {
		return (
			<div>
			{ this.props.item.map(function(item) {
				var children = [];
				if( item.children ) {
					for (var i = 0; i < item.children.length; i++) {

						if( item.children[i].scheme == "album" ) {
							icon = "icon-icn_album_01";
							var click = this.props.onClick.bind(this,item.children[i]);
						} else {
							icon = "icon-icn_flight_folder_open3";
							var click = '';
						}

						children.push(<li><i className={icon} onClick={click}></i><a href="javascript:;" onClick={click}>{item.children[i].name}</a></li>);
					}
					var showChildren = <Children item={item.children} />;
				}

				if( item.scheme == "album" ) {
					icon = "icon-icn_album_01";
					var click = this.props.onClick.bind(this,item);
				} else {
					icon = "icon-icn_flight_folder_open3";
					var click = '';
				}

				return (
					<ul>
						<li>
							<i className={icon} onClick={click}></i>
							<a href="javascript:;" onClick={click}>{item.name}</a>


							<ul>
								{children}
							</ul>
						</li>
					</ul>
				);
			}, this)}
			</div>
		);
	}

});

var Folders = React.createClass({
	handleClick: function(item,e) {
		this.setState({
			item: [item]
		});
	},

	handleClick: function(item,e) {
		var children = jQuery("#parent_"+ item.id +">div");
		if (children.is(":visible")) {
			jQuery("#parent_"+ item.id +">i").removeClass("icon-icn_flight_folder_open3");
			jQuery("#parent_"+ item.id +">i").addClass("icon-icn_flight_folder_01");
			children.hide('fast');
		} else {
			jQuery("#parent_"+ item.id +">i").removeClass("icon-icn_flight_folder_01");
			jQuery("#parent_"+ item.id +">i").addClass("icon-icn_flight_folder_open3");
			children.show('fast');
		}
		//e.stopPropagation();
	},

	handleChange: function(e) {
		this.props.onValueChange(e);
	},

    render: function() {
        return (
			<span>
				{ this.props.data.map(function(item, i) {

					if( item.children ) {
						var c = "parent_li";
						var f = "icon-icn_flight_folder_01";
						var click = this.handleClick.bind(this,item);
						var showChildren = <Children item={item.children} onClick={this.handleChange} />;
					} else {
						var c = "";
						var f = "icon-icn_album_01";
						var click = this.props.onValueChange.bind(this,item);
					}

					var id = "parent_"+item.id;

					return (
						<li className={c} id={id}>
							<i className={f} onClick={click}></i>
							<a href="javascript:;" onClick={click}>{item.name}</a>

							{showChildren}
			            </li>
					);
				}, this)}
			</span>
        );
    }

});

var Tree = React.createClass({
	getInitialState: function() {
		return {
			src: args.FBC_URL +"/includes/lib/tree.php?subdomain="+ args.subdomain +"&token="+ args.token,
			data: []
		};
	},

	library: function(e) {
        this.setState({
            album: {
                name: 'Recent Images'
            },
			search: '',
            src: args.FBC_URL +"/includes/lib/get.php?subdomain="+ args.subdomain +"&token="+ args.token +"&limit=30&start=0"
		});
		this.props.onValueChange(e);
		this.props.library(this.state.album);
    },

	componentDidMount: function() {
		var self = this;
		$.ajax({
			url: this.state.src,
			dataType: 'json',
			cache: false
		})
		.done(function(data) {
			self.setState({data: data});
		});
	},

	handleChange: function(e) {
		this.props.onValueChange(e);
	},

    render: function() {
		var icon = args.FBC_URL +"/assets/flight-icon.png";
        return (
			<div className="tree well">
				<ul>
					<li>
						<img id="fbc-icon" src={icon} />
						<a href="javascript:;" onClick={this.library}>Flight Library</a>
					</li>

	                <Folders data={this.state.data} onValueChange={this.handleChange} />
	            </ul>
			</div>
        );
    }
});
