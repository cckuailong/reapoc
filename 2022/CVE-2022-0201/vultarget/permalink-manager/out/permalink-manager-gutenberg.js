/*var el = wp.element.createElement;
var registerPlugin = wp.plugins.registerPlugin;
var Text = wp.components.Text;
var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;

function PermalinkManagerURIEditor() {
	const post_ID = wp.data.select("core/editor").getCurrentPostId();

	return el(
		PluginDocumentSettingPanel,
		{
			className: 'pm-urieditor',
			title: 'Permalink Manager',
			id: 'permalink-manager'
		},
		el( 'div',
			{ id: 'permalink-manager' },
			el( 'div',
				{ className: 'inside' },
				'-'
			)
		)
	);
}

registerPlugin( 'pm-urieditor', {
	icon: 'admin-links',
	render: PermalinkManagerURIEditor
} );*/
