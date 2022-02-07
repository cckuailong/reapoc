import { h, Component } from 'preact';
import { Link } from 'preact-router';
import style from './style.less';

export default class Done extends Component {

	// gets called when this route is navigated to
	componentDidMount() {
		// mark upgrade as completed
		fetch( ajaxurl + "?action=dlm_lu_mark_upgrade_done&nonce="+window.dlm_lu_vars.nonce, {
			method: 'GET',
			credentials: 'include'
		} ).then( ( r ) => {
			if ( r.status == 200 ) {
				return r.json();
			}

			throw "AJAX API OFFLINE";
		} ).then( ( j ) => {
			return;
		} ).catch( ( e ) => {
			console.log( e );
			return;
		} );
	}

	render() {
		return (
			<div class={style.welcome}>
				<h2>Upgrade Done</h2>
				<p><strong>{this.props.download_amount}</strong> downloads have been upgraded.</p>
				<p><strong>{this.props.content_amount}</strong> posts/pages have been upgraded.</p>
			</div>
		);
	}
}
