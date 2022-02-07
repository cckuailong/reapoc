import { h, Component } from 'preact';
import style from './style';

export default class QueueItem extends Component {

	render() {
		var listClass = (this.props.item.done?style.done:style.todo);

		return (
			<li class={listClass}>Legacy Download ID: {this.props.item.id}</li>
		);
	}
}