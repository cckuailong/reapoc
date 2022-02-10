if(ppc_features.disabled_panel){
    var disabled_panel = ppc_features.disabled_panel;
    var disabled_panel = disabled_panel.split(',');

    var taxs = ppc_features.taxonomies;
    taxs = taxs.split(',');
    
    taxs.forEach ((tax) => {
        if(disabled_panel.includes("taxonomy-panel-" + tax)){
            wp.data.dispatch('core/edit-post').removeEditorPanel('taxonomy-panel-' + tax); // category / tag / custom taxonomy
        }
    });

    if(disabled_panel.includes("featured-image")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'featured-image' ); // featured image
    }
    if(disabled_panel.includes("post-link")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'post-link' ); // permalink
    }
    if(disabled_panel.includes("page-attributes")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'page-attributes' ); // page attributes
    }
    if(disabled_panel.includes("post-excerpt")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'post-excerpt' ); // Excerpt
    }
    if(disabled_panel.includes("discussion-panel")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'discussion-panel' ); // Discussion
    }
    if(disabled_panel.includes("post-status")){
        wp.data.dispatch( 'core/edit-post').removeEditorPanel( 'post-status' ) ;// Post status
    }
}