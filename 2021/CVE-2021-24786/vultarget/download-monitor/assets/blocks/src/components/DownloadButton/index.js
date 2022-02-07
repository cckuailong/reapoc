
//import { Component } from '@wordpress/element';
const { Component } = wp.element;

export default class DownloadButton extends Component {

	constructor(props) {
		super(props);

		this.updateHeight = this.updateHeight.bind(this);
		this.getIframeUrl = this.getIframeUrl.bind(this);

		this.state = { calculatedHeight: {
			cacheKey: "",
			height: 100,
		}};
	}

	getIframeUrl() {
		let iframeURL = dlmBlocks.urlButtonPreview;

		if(this.props.download_id != 0) {
			iframeURL += "&download_id=" + this.props.download_id;
		}

		if(this.props.version_id != 0) {
			iframeURL += "&version_id=" + this.props.version_id;
		}

		if(this.props.template != "") {
			iframeURL += "&template=" + this.props.template;
		}

		if(this.props.custom_template != "") {
			iframeURL += "&custom_template=" + this.props.custom_template;
		}

		return iframeURL;
	}

	updateHeight(target) {

		let cacheKey = encodeURI(this.getIframeUrl());

		// check if we need to reset height to new URL
		if(this.state.chacheKey != cacheKey) {
			this.setState({calculatedHeight: {
				cacheKey: cacheKey,
				height: target.contentDocument.getElementById("dlmPreviewContainer").scrollHeight,
			}});
		}
	}

	render() {

		let iframeURL = this.getIframeUrl();
		let frameHeight = this.state.calculatedHeight.height + "px";

		return(
			<div className="dlmPreviewButton">
				<iframe src={iframeURL} width="100%" height={frameHeight} onLoad={(e)=>{this.updateHeight(e.target)}}></iframe>
			</div>
		);
	}

}
