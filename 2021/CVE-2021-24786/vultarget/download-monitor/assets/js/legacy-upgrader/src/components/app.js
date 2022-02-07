import { h, Component } from 'preact';
import { HashRouter, Route, Redirect } from 'react-router-dom';

// polyfill fetch and promise
import 'whatwg-fetch';
import Promise from 'promise-polyfill';
if (!window.Promise) {
  window.Promise = Promise;
}

import Welcome from './welcome';
import Downloads from './downloads';
import Content from './content';
import Done from './done';

// mock dlm_vars for dev
if(window.dlm_lu_vars == undefined) {
  window.dlm_lu_vars = {
    nonce: 'noncemock',
    assets_path: 'http://lnmp.dev:1337/assets/'
  };
}

export default class App extends Component {

	ROUTES = {
		welcome: () => <Welcome />,
		downloads: () => <Downloads />,
		content: ({match}) => <Content download_amount={match.params.download_amount} />,
		done: ({match}) => <Done download_amount={match.params.download_amount} content_amount={match.params.content_amount} />
	};

	constructor(props) {
		super(props);

		this.state = {
			queue: []
		};
	}

	/**
	<Router onChange={this.handleRoute}>

		<Welcome path="" />
		<Downloads path="/downloads" />
		<Content path="/content/:download_amount" />
		<Done path="/done/:download_amount/:content_amount" />
	</Router>*/

	render() {

		const ROUTES = this.ROUTES;

		return (
			<div id="dlm_legacy_upgrader_app">
				<HashRouter>
					<div>
						<Route path="/welcome" component={ROUTES.welcome} />
						<Route path="/downloads" component={ROUTES.downloads} />
						<Route path="/content/:download_amount" component={ROUTES.content} />
						<Route path="/done/:download_amount/:content_amount" component={ROUTES.done} />
						<Redirect from="/" to="/welcome" />
					</div>
				</HashRouter>

			</div>
		);
	}
}
