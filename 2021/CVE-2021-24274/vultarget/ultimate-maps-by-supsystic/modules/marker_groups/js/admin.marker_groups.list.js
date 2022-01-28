jQuery(document).ready(function(){
	var tblId = 'umsMgrTbl'
	,	tbl = jQuery('#'+ tblId);
	
	if(!mgrTblData || (mgrTblData && mgrTblData.length === 0)) {
		jQuery('#umsMgrTblEmptyMsg').show();
	}
	
	tbl.tree({
		data: umsGetMarkerGroupsTree(),
		autoOpen: true,
		dragAndDrop: true,
		onCreateLi: function(node, $li, is_selected) {
			for(var i = 0; i < mgrTblData.length; i++) {
				if(mgrTblData[i].id == node.id) {
					$li.find('.jqtree-element').append(mgrTblData[i]['actions']);
				}
			}
		},
		onDragStop: function(node, e) {
			var tree = tbl.tree('getTree');

			jQuery.sendFormUms({
				data: {
					mod: 'marker_groups'
				,	action: 'updateMarkerGroups'
				,	current: node.id
				,	parent: node.parent.id
				,	ids: umsGetNodesIdsList(tree.children)
				}
			});
		}
	});
	tbl.on('tree.click',function(e) {
		// Disable single selection
		// The multiple selection functions require that nodes have an id
		e.preventDefault();

		var btns = jQuery('#umsMgrRemoveGroupBtn'),
			action = tbl.tree('isNodeSelected', e.node) ? 'removeFromSelection' : 'addToSelection',
			selected;

		tbl.tree(action, e.node);
		selected = tbl.tree('getSelectedNodes');
		selected.length ? btns.removeAttr('disabled') : btns.attr('disabled', 'disabled');
	});
	jQuery(document).on('mouseover', '#umsMgrTbl .jqtree-element.jqtree_common', function() {
		jQuery(this).find('.supsystic-actions-wrap').show();
	}).on('mouseout', '#umsMgrTbl .jqtree-element.jqtree_common', function() {
		jQuery(this).find('.supsystic-actions-wrap').hide();
	});
	jQuery('#umsMgrRemoveGroupBtn').click(function(){
		var selected = tbl.tree('getSelectedNodes')
		,	listIds = []
		,	mapLabel = ''
		,	confirmMsg;

		for(var i = 0; i < selected.length; i++) {
			mapLabel = !mapLabel ? selected[i].name : mapLabel;
			listIds.push( selected[i].id );
		}
		confirmMsg = listIds.length > 1
			? toeLangUms('Are you sur want to remove '+ listIds.length+ ' marker categories?')
			: toeLangUms('Are you sure want to remove "'+ mapLabel+ '" marker category?');

		if(confirm(confirmMsg)) {
			jQuery.sendFormUms({
				btn: this
			,	data: {mod: 'marker_groups', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						location.reload();
					}
				}
			});
		}
		return false;
	});
	jQuery('#umsMgrClearBtn').click(function(){
		if(confirm(toeLangUms('Clear whole marker categories list?'))) {
			jQuery.sendFormUms({
				btn: this
			,	data: {mod: 'marker_groups', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						location.reload();
					}
				}
			});
		}
		return false;
	});
	jQuery('#umsMgrTblSearchTxt').on('keyup', function() {
		var search = jQuery(this).val();

		tbl.tree('getNodeByCallback', function(node) {
			if(node.name.indexOf(search) != -1) {
				jQuery(node.element).find('.jqtree-element').show();
			} else {
				jQuery(node.element).find('.jqtree-element').hide()
			}
			return false;
		});
	});
});
function umsGetMarkerGroupsTree(parent) {
	parent = typeof parent != 'undefined' ? parent : 0;

	var tree = [];

	if(typeof mgrTblData != 'undefined') {
		for(var i = 0; i < mgrTblData.length; i++) {
			if(mgrTblData[i].parent == parent) {
				var nodes = umsGetMarkerGroupsTree(mgrTblData[i].id);

				tree.push({
					id: mgrTblData[i].id,
					name: mgrTblData[i].title,
					children: nodes.length ? nodes : null
				});
			}
		}
	}
	return tree;
}
function umsGetNodesIdsList(tree, ids) {
	ids = ids ? ids : [];

	for(var i = 0; i < tree.length; i++) {
		ids.push(tree[i].id);
		if(tree[i].children.length) {
			ids = umsGetNodesIdsList(tree[i].children, ids);
		}
	}
	return ids;
}
function umsRemoveMarkerGroupFromTblClick(markerGroupId){
	if(!confirm(toeLangUms('Remove Marker Category?'))) {
		return false;
	}
	if(markerGroupId == ''){
		return false;
	}
	var msgEl = jQuery('#mgrRemoveElemLoader__'+ markerGroupId);

	jQuery.sendFormUms({
		msgElID: msgEl
	,	data: {action: 'remove', mod: 'marker_groups', id: markerGroupId}
	,	onSuccess: function(res) {
			if(!res.error){
				location.reload();
			}
		}
	});
}