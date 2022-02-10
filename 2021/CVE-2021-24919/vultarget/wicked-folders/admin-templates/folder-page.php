<div id="wicked-folders-page" class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo esc_html( $post_type_object->labels->singular_name ); ?>
        <?php _e( 'Folders', 'wicked-folders' ); ?>
    </h1>
    <?php
        if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
            /* translators: %s: search keywords */
            printf( ' <span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', get_search_query() );
        }
    ?>
    <hr class="wp-header-end" />
    <div id="wicked-folder-browser" data-folder="<?php echo esc_attr( $active_folder_id ); ?>">
        <form method="get">
            <input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
            <input type="hidden" name="page" value="<?php echo esc_attr( $taxonomy ); ?>" />
            <input type="hidden" name="folder" value="<?php echo esc_attr( $active_folder_id ); ?>" />
            <div class="wicked-head">
                <div class="wicked-upper-row wicked-clearfix">
                    <div class="wicked-right">
                        <?php $wp_list_table->search_box( $search_submit_label, 'post' ); ?>
                    </div>
                </div>
                <div class="wicked-lower-row wicked-clearfix">
                    <div class="wicked-left">
                        <div class="wicked-folder-browser-actions"></div>
                    </div>
                    <div class="wicked-right">
                        <div class="tablenav top">
                            <?php echo esc_html( $wp_list_table->pagination( 'top' ) ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wicked-body">
                <div class="wicked-folder-path-pane"></div>
                <div class="wicked-panes">
                    <div class="wicked-folder-tree-pane" style="width: <?php echo absint( $state->tree_pane_width ); ?>px;">
                        <div class="wicked-folder-tree-wrapper">
                            <div class="wicked-folder-tree"></div>
                        </div>
                        <div class="wicked-splitter-handle ui-resizable-handle ui-resizable-e">
                            <div></div>
                        </div>
                    </div>
                    <div class="wicked-folder-contents-pane">
                        <?php if ( 'dynamic_root' == $active_folder_id ) : ?>
                            <div class="wicked-dynamic-folders-intro">
                                <p class="wicked-icon"><span class="wicked-fa wicked-fa-magic"></span></p>
                                <h1>Dynamic Folders</h1>
                                <p class="wicked-large"><?php _e( 'Dynamic folders are generated on the fly based on your content.  They are useful for finding content based on things like date, author, etc.', 'wicked-folders' ); ?></p>
                                <p><?php echo sprintf( 'Don\'t want to see dynamic folders?  You can turn them off in the %1$ssettings%2$s.', '<a href="' . esc_url( menu_page_url( 'wicked_folders_settings', 0 ) ) . '">', '</a>' ); ?></p>
                            </div>
                        <?php else : ?>
                            <?php $wp_list_table->display(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="wicked-foot wicked-clearfix">
                <div class="wicked-left">
                </div>
                <div class="wicked-right">
                    <div class="tablenav bottom">
                        <?php echo esc_html( $wp_list_table->pagination( 'bottom' ) ); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="wicked-folder-dialog-container"></div>

<script type="text/template" id="tmpl-wicked-folder-dialog">
    <div class="wicked-popup-mask"></div>
    <div class="wicked-popup"><div><div><div><div id="wicked-folder-dialog" class="wicked-folder-dialog wicked-popup-hide">
        <form action="" method="post">
            <div class="wicked-dialog-head">
                <div class="wicked-dialog-title"><%= dialogTitle %></div>
                <a class="wicked-popup-close" href="#"><span class="dashicons dashicons-no"></span></a>
            </div>
            <div class="wicked-dialog-body">
                <div class="wicked-messages wicked-errors"></div>
                <% if ( 'delete' == mode ) { %>
                    <p><%= deleteFolderConfirmation %></p>
                <% } else { %>
                    <p><input type="text" name="wicked_folder_name" placeholder="<?php _e( 'Folder name', 'wicked-folders' ); ?>" value="<%= folderName %>" /></p>
                    <p class="wicked-folder-parent"></p>
                <% } %>
            </div>
            <div class="wicked-dialog-foot">
                <a class="button wicked-cancel" href="#"><?php _e( 'Cancel', 'wicked-folders' ); ?></a>
                <button class="button-primary wicked-save" type="submit"><%= saveButtonLabel %></button>
            </div>
        </form>
    </div></div></div></div>
</script>

<script>
(function( $ ){
    $(function(){

        var FolderBrowserController = wickedfolders.models.FolderBrowserController,
            FolderCollection = wickedfolders.collections.Folders,
            PostCollection = wickedfolders.collections.Posts,
            FolderBrowser = wickedfolders.views.FolderBrowser,
            FolderTree = wickedfolders.views.FolderTree,
            Folder = wickedfolders.models.Folder,
            Post = wickedfolders.models.Post;

        var folders = new FolderCollection(),
            folderData = <?php echo json_encode( $folders ); ?>,
            popping = false,
            treePaneWidth = <?php echo absint( $state->tree_pane_width ); ?>,
            documentHeight = $( document ).height(),
            windowHeight = $( window ).height(),
            adminBarHeight = 32;

        Backbone.emulateHTTP = true;

        _.each( folderData, function( folder ){
            var posts = new PostCollection();
            _.each( folder.posts, function( post ){
                posts.add( new Post({
                    id:     post.id,
                    name:   post.name,
                    type:   post.type
                }) );
            });
            folders.add( new Folder({
                id:         folder.id,
                parent:     folder.parent,
                name:       folder.name,
                postType:   folder.postType,
                taxonomy:   folder.taxonomy,
                type:       folder.type,
                posts:      posts
            }) );
        });

        var controller = new FolderBrowserController({
            expanded:               <?php echo json_encode( array_values( $state->expanded_folders ) ); ?>,
            postType:               '<?php echo esc_attr( $post_type ); ?>',
            taxonomy:               '<?php echo esc_attr( $taxonomy ); ?>',
            folder:                 folders.get( '<?php echo esc_attr( $active_folder_id ); ?>' ),
            folders:                folders,
            screen:                 '<?php echo esc_attr( $screen->id ); ?>',
            nonce:                  '<?php echo wp_create_nonce( 'wicked_folders_save_state' ); ?>',
            treePaneWidth:          <?php echo esc_attr( $state->tree_pane_width ); ?>,
            hideAssignedItems:      <?php echo ( int ) $state->hide_assigned_items; ?>,
            itemsPerPage:           <?php echo esc_attr( $items_per_page ); ?>,
            showContentsInTreeView: <?php echo ( int ) $show_contents_in_tree_view; ?>,
            pageNumber:             <?php echo ( int ) $page_number; ?>,
            itemsPerPage:           <?php echo (int ) $items_per_page; ?>,
            orderby:                '<?php echo esc_attr( $orderby ); ?>',
            order:                  '<?php echo esc_attr( $order ); ?>',
            isSearch:               <?php echo empty( $_GET['s'] ) ? 'false' : 'true'; ?>
        });

        var folderBrowser = new FolderBrowser({
            el:     '#wicked-folder-browser',
            model:  controller
        });

        // Define these variables after the folder browser has been created,
        // otherwise they may be off
        var folderPaneOffset = parseInt( $( '#wicked-folder-browser .wicked-folder-tree-pane' ).offset().top, 10 ),
            folderPaneHeight = parseInt( $( '#wicked-folder-browser .wicked-folder-tree-pane' ).height(), 10 );

        // Listen to folder changes
        controller.on( 'change:folder', function(){

            // Don't push the state if this event was triggered by a popstate
            if ( popping ) {
                popping = false;
            } else {
                if ( window.history && history.pushState ) {
                    var url = 'edit.php?page=' + this.get( 'taxonomy' ) + '&folder=' + this.get( 'folder' ).id + '&folder_type=' + this.get( 'folder' ).get( 'type' );
                    if ( 'post' != this.get( 'postType' ) ) url += '&post_type=' + this.get( 'postType' );
                    var state = {
                        folder: this.get( 'folder' ).id
                    }
                    history.pushState( state, null, url );
                }
            }


        }, controller );

        controller.on( 'change:treePaneWidth', function(){
            // Update global tree pane width variable
            treePaneWidth = this.get( 'treePaneWidth' );
            $( '#wicked-folder-browser .wicked-folder-tree-wrapper' ).css( 'width', treePaneWidth + 'px' );
        }, controller );

        controller.on( 'sortOrderChanged', function(){
            var url = document.location.href,
                orderby = folderBrowser.model.get( 'orderby' ),
                order = folderBrowser.model.get( 'order' );

            // Probably a better way to do this...
            if ( -1 == url.indexOf( 'orderby=' ) ) {
                url += -1 == url.indexOf( '?' ) ? '?' : '&';
                url += 'orderby=' + orderby;
            } else {
                url = url.replace( /orderby=([^&]*)/, 'orderby=' + orderby );
            }

            if ( -1 == url.indexOf( 'order=' ) ) {
                url += -1 == url.indexOf( '?' ) ? '?' : '&';
                url += 'order=' + order;
            } else {
                url = url.replace( /order=([^&]*)/, 'order=' + order );
            }

            window.history.replaceState( {
                url:    url,
                folder: folderBrowser.folder().id
            }, null, url );

        }, controller );

        // Listen for popstate
        $( window ).on( 'popstate', function( e ) {

            if ( 'undefined' != typeof e.originalEvent.state && null !== e.originalEvent.state ) {

                popping = true;

                var state   = e.originalEvent.state;
                var folder  = folders.get( state.folder );

                folderBrowser.model.set( 'folder', folder );

            }
        } );

        $( window ).on( 'scroll', function( e ) {
            var scrollTop = $( window ).scrollTop(),
                top = folderPaneOffset - scrollTop,
                bottom = ( documentHeight - scrollTop - windowHeight - 88 ) * -1;

            bottom = bottom > 0 ? bottom : 0;

            if ( top <= adminBarHeight ) {
                $( '#wicked-folder-browser .wicked-folder-tree-wrapper' ).css({
                    position:   'fixed',
                    top:        adminBarHeight + 'px',
                    bottom:     bottom + 'px',
                    width:      treePaneWidth + 'px',
                    height:     'auto'
                });
            } else {
                $( '#wicked-folder-browser .wicked-folder-tree-wrapper' ).css({
                    position:   'relative',
                    top:        'auto',
                    bottom:     'auto',
                    width:      '100%',
                    height:     '100%'
                });
            }
        } );

        $( window ).on( 'resize', function( e ) {
            recalculateDimensions();
        } );

        // Initialize history
        window.history.replaceState( {
            url:    document.location.href,
            folder: folderBrowser.folder().id
        }, null, null );

        // Bind events to Folders menu in admin toolbar
        $( '#wp-admin-bar-wicked-folders-add-new-folder' ).click( function(){
            folderBrowser.$( '.wicked-folder-browser-actions .wicked-add-new-folder' ).trigger( 'click' );
            return false;
        } );

        $( '#wp-admin-bar-wicked-folders-edit-folder' ).click( function(){
            folderBrowser.$( '.wicked-folder-browser-actions .wicked-edit-folder' ).trigger( 'click' );
            return false;
        } );

        $( '#wp-admin-bar-wicked-folders-clone-folder' ).click( function(){
            folderBrowser.$( '.wicked-folder-browser-actions .wicked-clone-folder' ).trigger( 'click' );
            return false;
        } );

        $( '#wp-admin-bar-wicked-folders-delete-folder' ).click( function(){
            folderBrowser.$( '.wicked-folder-browser-actions .wicked-delete-folder' ).trigger( 'click' );
            return false;
        } );

        $( '#wp-admin-bar-wicked-folders-expand-all' ).click( function(){
            folderBrowser.$( '.wicked-folder-browser-actions .wicked-expand-all' ).trigger( 'click' );
            return false;
        } );

        $( '#wp-admin-bar-wicked-folders-collapse-all' ).click( function(){
            folderBrowser.$( '.wicked-folder-browser-actions .wicked-collapse-all' ).trigger( 'click' );
            return false;
        } );

        /**
         * Ensures that the folder pane width isn't greater than the width of
         * the folder browser.  This can happen if the pane is made wide on a
         * larger screen and then viewed on a smaller screen.
         */
        function resetFolderPaneWidth() {

            var width       = $( '.wicked-folder-tree-pane' ).width();
            var maxWidth    = $( '#wicked-folder-browser' ).width();

            if ( width >= maxWidth ) {
                width = Math.floor( maxWidth * 0.3 );
                $( '.wicked-folder-tree-pane' ).width( width );
            }
        }

        function recalculateDimensions() {

            folderPaneOffset = parseInt( $( '#wicked-folder-browser .wicked-folder-tree-pane' ).offset().top, 10 );
            folderPaneHeight = parseInt( $( '#wicked-folder-browser .wicked-folder-tree-pane' ).height(), 10 );
            // Not sure where this 18 pixels is coming from...
            documentHeight = $( document ).height() - 18;
            windowHeight = $( window ).height();

        }

        resetFolderPaneWidth();

    });
})( jQuery );
</script>
