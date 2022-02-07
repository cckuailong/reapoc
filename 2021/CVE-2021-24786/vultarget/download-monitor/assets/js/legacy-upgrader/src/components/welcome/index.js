import { h, Component } from 'preact';
import { Link } from 'react-router-dom';
import style from './style.less';

export default class Welcome extends Component {
	render() {
		return (
			<div class={style.welcome}>
				<h2>Welcome</h2>
				<p>Before upgrading your downloads, we'll first scan your database to find your legacy downloads. We will put all found legacy downloads in a queue which you can review before the actual upgrading begins.</p>
				<p><strong>PLEASE NOTE: Although thoroughly tested, this process will modify and move your download data.  Backup your database before you continue.</strong></p>
				<p><Link to="/downloads" class="button button-primary button-large">I have backed up my database, let's go</Link></p>
			</div>
		);
	}
}
