<div id="wicked-object-folder-pane"<?php if ( ! empty( $classes ) ) echo ' class="' . join( ' ', $classes ) . '"'; ?>>
    <div class="wicked-resizer">
        <div class="wicked-splitter-handle ui-resizable-handle ui-resizable-e ui-resizable-w">
        </div>
    </div>
    <div class="wicked-content">
        <div class="wicked-title"><?php _e( 'Folders', 'wicked-folders' ); ?></div>
        <div class="wicked-toolbar-container"></div>
        <div class="wicked-folder-details-container"></div>
        <div class="wicked-folder-pane-settings-container"></div>
        <?php if ( get_option( 'wicked_folders_show_folder_search', true ) ) : ?>
            <div class="wicked-folder-search-container">
                <div class="wicked-folder-search">
                    <label for="wicked-folder-search-input" class="screen-reader-text">Search folders</label>
                    <input id="wicked-folder-search-input" name="wicked_folder_search" type="text" value="" placeholder="<?php _e( 'Search folders...', 'wicked-folders' ); ?>" />
                </div>
            </div>
        <?php endif; ?>
        <div class="wicked-folder-tree"></div>
        <div class="wicked-navigating-mask"></div>
        <div class="wicked-folder-navigation-error" style="display: none;">
            <div id="wicked-folder-navigation-error">
                <div>
                    <span class="dashicons dashicons-warning"></span>
                    <h1><?php _e( 'Something went wrong', 'wicked-folders' ); ?></h1>
                    <p><?php _e( 'An error occurred while attempting to navigate to the folder.  Please refresh the page.', 'wicked-folders' ); ?></p>
                    <p>
                        <?php
                            echo sprintf(
                                __( "If you continue to have trouble, disable the 'Don't reload page when navigating folders' option on the %s.", 'wicked-folders' ),
                                '<a href="' . admin_url( 'options-general.php?page=wicked_folders_settings' ) . '">' . __( 'settings page', 'wicked-folders' ) . '</a>'
                            );
                        ?>
                    </p>
                    <p class="code wicked-error-text"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="tmpl-wicked-folder-details">
    <header>
        <h2>{{ data.title }}</h2>
        <span class="wicked-spinner"></span>
        <a class="wicked-close" href="#" title="<?php _e( 'Close', 'wicked-folders' ); ?>"><span class="screen-reader-text"><?php _e( 'Close', 'wicked-folders' ); ?></span></a>
    </header>
    <div>
        <div class="wicked-messages wicked-errors"></div>
        <# if ( 'delete' == data.mode ) { #>
            <p>{{ data.deleteFolderConfirmation }}</p>
        <# } else { #>
            <div class="wicked-folder-name">
                <label for="wicked-folder-name" class="screen-reader-text"><?php _e( 'Folder name', 'wicked-folders' ); ?>:</label>
                <input id="wicked-folder-name" type="text" name="wicked_folder_name" placeholder="<?php _e( 'Folder name', 'wicked-folders' ); ?>" value="{{ data.folderName }}" />
            </div>
            <div class="wicked-folder-parent">
                <label for="wicked-folder-parent" class="screen-reader-text"><?php _e( 'Parent folder', 'wicked-folders' ); ?>:</label>
                <div></div>
            </div>
        <# } #>
        <# if ( 'edit' == data.mode ) { #>
            <fieldset>
                <legend>
                    {{ data.cloneFolderLink }}
                    <span class="dashicons dashicons-editor-help" title="{{ data.cloneFolderTooltip }}"></span>
                </legend>
                <p>
                    <label>
                        <input type="checkbox" name="wicked_clone_children" />
                        {{ data.cloneChildFolders }}
                    </label>
                    <span class="dashicons dashicons-editor-help" title="{{ data.cloneChildFoldersTooltip }}"></span>
                </p>
                <p><a class="button wicked-clone-folder" href="#">{{ data.cloneFolderLink }}</a></p>
            </fieldset>
            <p class="wicked-folder-owner">
                <label for="wicked-folder-owner-id">{{ data.ownerLabel }}</label>
                <select id="wicked-folder-owner-id" name="wicked_folder_owner_id">
                    <# if ( data.ownerId ) { #>
                    <option value="{{ data.ownerId }}" selected="selected">{{ data.ownerName }}</option>
                    <# } #>
                </select>
            </p>
        <# } #>
    </div>
    <footer>
        <a class="button wicked-cancel" href="#"><?php _e( 'Cancel', 'wicked-folders' ); ?></a>
        <button class="button-primary wicked-save" type="submit">{{ data.saveButtonLabel }}</button>
    </footer>
</script>

<script type="text/template" id="tmpl-wicked-folder-pane-settings">
    <header>
        <h2><?php _e( 'Settings', 'wicked-folders' ); ?></h2>
        <span class="wicked-spinner"></span>
        <a class="wicked-close" href="#" title="<?php _e( 'Close', 'wicked-folders' ); ?>"><span class="screen-reader-text"><?php _e( 'Close', 'wicked-folders' ); ?></span></a>
    </header>
    <div>
        <div class="wicked-field">
            <div class="wicked-field-label">
                <?php _e( 'Organization mode:', 'wicked-folders' ); ?>
                <span class="dashicons dashicons-editor-help" title="<?php _e( "Controls what happens when you drag and drop folders. Use 'Normal' to arrange your folder hierarchy by dragging and dropping folders into other folders. Use 'Sort' to change the order of the folders.", 'wicked-folders' ); ?>"></span>
            </div>
            <div class="wicked-field-options">
                <div>
                    <label>
                        <input type="radio" name="wicked_organization_mode" value="organize" <# if ( 'organize' == data.mode ) { #>checked<# } #> />
                        <?php _e( 'Normal', 'wicked-folders' ); ?>
                    </label>
                </div>
                <div>
                    <label>
                        <input type="radio" name="wicked_organization_mode" value="sort" <# if ( 'sort' == data.mode ) { #>checked<# } #> />
                        <?php _e( 'Sort', 'wicked-folders' ); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="wicked-field">
            <div class="wicked-field-label">
                <?php _e( 'Folder sort order:', 'wicked-folders' ); ?>
                <span class="dashicons dashicons-editor-help" title="<?php _e( "Controls how your folders are sorted. Select 'Custom' to display the folders in the specific order you specify.", 'wicked-folders' ); ?>"></span>
            </div>
            <div class="wicked-field-options">
                <div>
                    <label>
                        <input type="radio" name="wicked_sort_mode" value="alpha" <# if ( 'alpha' == data.sortMode ) { #>checked<# } #> />
                        <?php _e( 'Alphabetical', 'wicked-folders' ); ?>
                    </label>
                </div>
                <div>
                    <label>
                        <input type="radio" name="wicked_sort_mode" value="custom" <# if ( 'custom' == data.sortMode ) { #>checked<# } #> />
                        <?php _e( 'Custom', 'wicked-folders' ); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-wicked-post-drag-details">
    <div class="items">
        <div class="title">
            <?php _e( 'Move', 'wicked-folders' ); ?> {{ data.count }}
            <# if ( 1 == data.count ) { #>
                <?php _e( 'Item', 'wicked-folders' ); ?>
            <# } else { #>
                <?php _e( 'Items', 'wicked-folders' ); ?>
            <# } #>
        </div>
        <# if ( data.enableCopy ) { #>
            <?php _e( 'Hold SHIFT key to copy items to folder', 'wicked-folders' ); ?>
        <# } #>
    </div>
</script>

<script type="text/template" id="tmpl-wicked-folders-notification">
    <div class="wicked-notification-message">
        <div class="wicked-notification-title">{{ data.title }}</div>
        {{ data.message }}
    </div>
    <# if ( data.dismissible ) { #>
        <button class="wicked-dismiss" type="button">
            <span class="screen-reader-text">Close</span>
        </button>
    <# } #>
</script>

<script>

var wickedFolderPane;

(function( $ ){
    $(function(){

        var FolderBrowserController = wickedfolders.models.FolderBrowserController,
            FolderCollection = wickedfolders.collections.Folders,
            ObjectFolderPaneController = wickedfolders.models.ObjectFolderPaneController,
            ObjectFolderPane = wickedfolders.views.ObjectFolderPane,
            FolderTree = wickedfolders.views.FolderTree,
            Folder = wickedfolders.models.Folder

        var folders = new FolderCollection(),
            folderData = <?php echo json_encode( $folders ); ?>;

        folders.sortMode = '<?php echo esc_attr( $state->sort_mode ); ?>';

        Backbone.emulateHTTP = true;

        _.each( folderData, function( folder ){
            folders.add( new Folder({
                id:             folder.id,
                parent:         folder.parent,
                name:           folder.name,
                postType:       folder.postType,
                taxonomy:       folder.taxonomy,
                type:           folder.type,
                lazy:           folder.lazy,
                order:          folder.order,
                itemCount:      folder.itemCount,
                showItemCount:  <?php echo $show_item_counts ? 'folder.showItemCount' : 'false'; ?>,
                ownerId:        folder.ownerId,
                ownerName:      folder.ownerName,
                editable:       folder.editable,
                deletable:      folder.deletable,
                assignable:     folder.assignable,
                termId:         'Wicked_Folders_Term_Dynamic_Folder' == folder.type ? folder.termId : false
            }) );
        });

        var activeFolder = folders.get( '<?php echo ( int ) $active_folder_id; ?>' );

        // In case we can't find the specified folder, fallback to the root folder
        if ( ! activeFolder ) activeFolder = folders.get( '0' );

        var controller = new ObjectFolderPaneController({
            expanded:               <?php echo json_encode( array_values( $state->expanded_folders ) ); ?>,
            postType:               '<?php echo esc_attr( $post_type ); ?>',
            taxonomy:               '<?php echo esc_attr( $taxonomy ); ?>',
            folder:                 activeFolder,
            folders:                folders,
            screen:                 '<?php echo esc_attr( $screen->id ); ?>',
            nonce:                  '<?php echo wp_create_nonce( 'wicked_folders_save_state' ); ?>',
            treePaneWidth:          <?php echo ( int ) $state->tree_pane_width; ?>,
            //hideAssignedItems:      <?php echo ( int ) $state->hide_assigned_items; ?>,
            isSearch:               <?php echo empty( $_GET['s'] ) ? 'false' : 'true'; ?>,
            isFolderPaneVisible:    <?php echo $state->is_folder_pane_visible ? 'true' : 'false'; ?>,
            sortMode:               '<?php echo esc_attr( $state->sort_mode ); ?>',
            showItemCount:          <?php echo $show_item_counts ? 'true' : 'false'; ?>,
            lang:                   <?php echo $lang ? "'{$lang}'" : 'false'; ?>,
            enableCreate:           <?php echo $enable_create ? 'true' : 'false'; ?>
        });

        var pane = new ObjectFolderPane({
            el:     '#wicked-object-folder-pane',
            model:  controller
        });

        $( 'body' ).on( 'wickedfolders:toggleFolderPane', function( e, visible ){
            if ( visible ) {
                pane.model.set( 'isFolderPaneVisible', true );
                pane.setWidth( pane.model.get( 'treePaneWidth' ) );
            } else {
                $( '#wpcontent' ).css( 'paddingLeft', '' );
                $( '#wpcontent' ).css( 'paddingRight', '' );

                pane.model.set( 'isFolderPaneVisible', false );
            }
        } );

        wickedFolderPane = pane;
    });
})( jQuery );
</script>
