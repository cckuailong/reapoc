// import 'promise-polyfill';
// import 'isomorphic-fetch';
import { h, render } from 'preact';
import './style';

let root;
function DLM_LU_init() {
	let App = require('./components/app').default;
	root = render(<App />, document.getElementById("dlm-legacy-upgrade-container"), root);
}

// in development, set up HMR:
if (module.hot) {
	//require('preact/devtools');   // turn this on if you want to enable React DevTools!
	module.hot.accept('./components/app', () => requestAnimationFrame(init) );
}

document.addEventListener("DOMContentLoaded", function(event) {
	DLM_LU_init();
});
