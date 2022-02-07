import {h, Component} from 'preact';
import style from './style.less';
import QueueItem from './QueueItem';
import { Link } from 'react-router-dom';

export default class Content extends Component {

	state = {
		checked: false,
		items: [],
		upgrading: false
	};

	constructor(props) {
		super(props);

		this.startUpgrade = this.startUpgrade.bind(this);
		this.upgradeNext = this.upgradeNext.bind(this);
		this.upgradeItem = this.upgradeItem.bind(this);
	}

	// gets called when this route is navigated to
	componentDidMount() {
		fetch( ajaxurl + "?action=dlm_lu_get_content_queue&nonce="+window.dlm_lu_vars.nonce, {
			method: 'GET',
			credentials: 'include'
		} ).then( ( r ) => {
			if ( r.status == 200 ) {
				return r.json();
			}

			throw "AJAX API OFFLINE";
		} ).then( ( j ) => {
			var items = [];
			for ( var i = 0; i < j.length; i ++ ) {
				items.push( {id: j[i], done: false} );
			}
			this.setState( {checked: true, items: items} );
			return;
		} ).catch( ( e ) => {
			console.log( e );
			return;
		} );
	}

	// gets called just before navigating away from the route
	componentWillUnmount() {
		// todo clear queue
	}

	upgradeNext() {
		var upgradeDone = true;
		for( var i = 0; i < this.state.items.length; i++ ) {
			if( this.state.items[i].done === false ) {
				upgradeDone = false;
				this.upgradeItem( this.state.items[i] );
				break;
			}
		}

		if( upgradeDone ) {
			window.location.hash = "/done/"+this.props.download_amount+"/"+this.state.items.length;
		}
	}

	upgradeItem( item ) {
		fetch( ajaxurl + "?action=dlm_lu_upgrade_content&content_id="+item.id+"&nonce="+window.dlm_lu_vars.nonce, {
			method: 'GET',
			credentials: 'include'
		} ).then( ( r ) => {
			if ( r.status == 200 ) {
				return r.json();
			}

			throw "AJAX API OFFLINE";
		} ).then( ( j ) => {
			console.log( j );
			item.done = true;
			this.forceUpdate();
			this.upgradeNext();
			return;
		} ).catch( ( e ) => {
			console.log( e );
			return;
		} );
	}

	startUpgrade() {
		// check if we're upgrading
		if( this.state.upgrading ) {
			return;
		}

		// set we're upgrading
		this.setState( {upgrading: true} );

		// upgrade next download
		this.upgradeNext();
	}

	render() {

		var loadingImg = window.dlm_lu_vars.assets_path + "loading.gif";

		if ( this.state.checked == false ) {
			return (
				<div class={style.queue}>
					<h2>Posts/Pages Queue</h2>
					<p><strong>{this.props.download_amount}</strong> downloads have been upgraded.</p>
					<p>We're currently building the posts/pages queue, please wait.</p>
				</div>
			);
		}

		if ( this.state.items.length == 0 ) {
			var linkURL = "/done/"+this.props.download_amount+"/0";
			return (
				<div class={style.queue}>
					<p><strong>{this.props.download_amount}</strong> downloads have been upgraded.</p>
					<p>No posts/pages found that require upgrading.</p>
					<Link to={linkURL} class="button button-primary button-large">Continue to Post/Page upgrade</Link>
			</div>
			);
		}

		return (
			<div class={style.queue}>
				<h2>Posts/Pages Queue</h2>

				{this.state.upgrading &&
					<p class={style.upgrading_notice}><img src={loadingImg} />Currently upgrading your downloads, please wait...</p>
				}

				<p><strong>{this.props.download_amount}</strong> downloads have been upgraded.</p>

				<p>The following posts/pages items have been found that need upgrading:</p>

				{this.state.items.length > 0 &&
				 <ul>
					 {this.state.items.map( ( o, i ) => <QueueItem item={o}/> )}
				 </ul>
				}

				<a href="javascript:;" class="button button-primary button-large" onClick={() => this.startUpgrade()}>Upgrade Content Items</a>

			</div>
		);
	}
}
