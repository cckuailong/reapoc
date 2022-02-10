;(function( window, $, undefined ){

    window.wickedfolders = window.wickedfolders || { views: {}, models: {}, collections: {}, util: {} };

    wickedfolders.util = {
        updateUrlParam: function( url, param, value ){
            if ( -1 == url.indexOf( param + '=' ) ) {
                url += -1 == url.indexOf( '?' ) ? '?' : '&';
                url += param + '=' + value;
            } else {
                var pattern = new RegExp( param + '=([^&]*)', 'gi' );
                url = url.replace( pattern, param + '=' + value );
            }
            if ( false === value || '' == value ) {
                var pattern = new RegExp( param + '=([^&]*)', 'gi' );
                url = url.replace( pattern, '' );
            }
            return url;
        },

        isRtl: function(){
            if ( _.isUndefined( window.isRtl ) ) {
                return jQuery( 'body' ).hasClass( 'rtl' );
            } else {
                return isRtl;
            }
        },

        templateSettings: {
            evaluate:    /<#([\s\S]+?)#>/g,
            interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
            escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
            variable:    'data'
        }
    };

    wickedfolders.models.Model = Backbone.Model.extend();

    wickedfolders.models.Folder = wickedfolders.models.Model.extend({
        defaults: {
            parent:         'root',
            postType:       false,
            taxonomy:       false,
            posts:          false,
            type:           'Wicked_Folders_Term_Folder',
            lazy:           false,
            loading:        false,
            itemCount:      0,
            showItemCount:  true,
            order:          0,
            editable:       true,
            deletable:      true,
            assignable:     true,
            ownerId:        0,
            ownerName:      ''
        },

        initialize: function(){

            if ( ! this.get( 'posts' ) ) {
                this.set( 'posts', new wickedfolders.collections.Posts() );
            }

        },

        parse: function( response, options ){
            return _.omit( response, 'posts' );
        },

        url: function(){

            // NOTE: the _methodOverride parameter is being used an an alternative
            // to overriding the sync function

            var id = this.id ? this.id : '',
                taxonomy = this.get( 'taxonomy' ),
                action = this.get( '_actionOverride' ) || 'wicked_folders_save_folder',
                methodOverride = this.get( '_methodOverride' ) || false;

            // We need the ID and taxonomy attributes when deleting
            var url = wickedFoldersSettings.ajaxURL + '?action=' + action + '&id=' + id + '&taxonomy=' + taxonomy;

            // Assume we're updating if we have an ID
            if ( false === methodOverride && id ) methodOverride = 'PUT';

            if ( false !== methodOverride ) url += '&_method_override=' + methodOverride;

            return url;
        },

        cloneFolder: function( options ){
            var options = options || {};

            _.defaults( options, {
                cloneChildren:  false,
                parent:         this.get( 'parent' )
            } );

            return $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':           'wicked_folders_clone_folder',
                        'id':               this.id,
                        'post_type':        this.get( 'postType' ),
                        'clone_children':   options.cloneChildren,
                        'parent':           options.parent
                    },
                    method: 'POST',
                    dataType: 'json'
                }
            );
        },

        hasChildren: function() {
            var id = this.get( 'id' );

            var children = _.filter( this.collection.models, function( folder ){
                return folder.get( 'parent' ) == id;
            } );

            return children.length > 0;
        }
    });

    wickedfolders.models.Post = wickedfolders.models.Model.extend({
        defaults: {
            id:   false,
            name: false,
            type: false
        },

        url: function(){
            return wickedFoldersSettings.ajaxURL + '?action=wicked_folders_save_post';
        }

    });

    wickedfolders.models.FolderTreeState = wickedfolders.models.Model.extend({
        defaults: {
            selected:           '0',
            expanded:           [ '0' ],
            checked:            [],
            readOnly:           false,
            showFolderContents: false,
            search:             ''
        },

        addExpanded: function( id ){

            var expanded = _.clone( this.get( 'expanded' ) );

            expanded.push( id );

            this.set( 'expanded', _.uniq( expanded ) );

        },

        toggleExpanded: function( id ){

            var expanded = _.clone( this.get( 'expanded' ) );

            if ( -1 == expanded.indexOf( id ) ) {
                expanded.push( id );
            } else {
                expanded = _.without( expanded, id );
            }

            this.set( 'expanded', expanded );

        },

        toggleChecked: function( id ){

            var checked = _.clone( this.get( 'checked' ) ) || [];

            if ( -1 == checked.indexOf( id ) ) {
                checked.push( id );
            } else {
                checked = _.without( checked, id );
            }

            this.set( 'checked', checked );

        }

    });

    wickedfolders.models.Notification = wickedfolders.models.Model.extend({
        defaults: {
            title:          'SUCCESS',
            message:        '',
            dismissible:    true,
            dismissed:      false,
            autoDismiss:    true,
            delay:          3000
        },

        initialize: function(){
            var model = this;

            this.on( 'change:dismissed', this.dismiss, this );

            if ( this.get( 'autoDismiss' ) ) {
                setTimeout( function(){
                    model.set( 'dismissed', true );
                }, this.get( 'delay' ) )
            }
        },

        dismiss: function(){
            var model = this;

            setTimeout( function(){
                model.destroy();
            }, 500 );
        }
    });

    wickedfolders.collections.Posts = Backbone.Collection.extend({
        model: wickedfolders.models.Post,
    });

    wickedfolders.collections.Folders = Backbone.Collection.extend({
        model: wickedfolders.models.Folder,

        sortMode: 'custom',

        taxonomy: 'wf_page_folders',

        children: function( parent ) {
            var folders = new wickedfolders.collections.Folders();
            this.each( function( folder ) {
                if ( folder.get( 'parent' ) == parent ) {
                    folders.add( folder );
                }
            });
            return folders;
        },

        ancestors: function( id ) {

            var ancestors = new wickedfolders.collections.Folders(),
                folder = this.get( id );

            // Disable the collection's comparator; we want the folders to be in
            // the order they're added
            ancestors.comparator = false;

            if ( folder ) {
                var parent = this.findWhere( { id: folder.get( 'parent' ) } );
                if ( parent ) {
                    ancestors.add( parent );
                    var parentAncestors = this.ancestors( parent.id );
                    parentAncestors.each( function( ancestor ){
                        ancestors.add( ancestor );
                    } );
                }
            }

            return ancestors;

        },

        ancestorIds: function( id ){
            return this.ancestors( id ).pluck( 'id' );
        },

        descendants: function( id ) {
            var descendants = new wickedfolders.collections.Folders(),
                folder = this.get( id );

            if ( folder ) {
                var children = this.where( { parent: folder.id } );

                _.each( children, function( child ) {
                    descendants.add( child );

                    var childDescendants = this.descendants( child.id );

                    childDescendants.each( function( descendant ){
                        descendants.add( descendant );
                    } );
                }, this );
            }

            return descendants;
        },

        descendantIds: function( id ){
            return this.descendants( id ).pluck( 'id' );
        },

        comparator: function( a, b ){
            var aOrder = parseInt( a.get( 'order' ) ),
                bOrder = parseInt( b.get( 'order' ) ),
                aName = a.get( 'name' ).toUpperCase(),
                bName = b.get( 'name' ).toUpperCase(),
                aParent = a.get( 'parent' ),
                bParent = b.get( 'parent' );

            if ( _.isNaN( aOrder ) ) aOrder = 0;
            if ( _.isNaN( bOrder ) ) bOrder = 0;

            // Always sort root folders by their sort order
            if ( 'root' == aParent || 'root' == bParent ) {
                if ( 'root' == aParent && 'root' == bParent ) {
                    return aOrder < bOrder ? -1 : 1;
                }

                if ( 'root' == aParent ) {
                    bOrder = 1;
                }

                if ( 'root' == bParent ) {
                    aOrder = 1;
                }

                return aOrder < bOrder ? -1 : 1;
            }

            // If the order is the same for both folders, sort by name
            if ( 'alpha' == this.sortMode || aOrder == bOrder ) {
                if ( aName == bName ) return 0;

                return aName < bName ? -1 : 1;
            } else {
                if(Number.isNaN(aOrder)||Number.isNaN(bOrder)) {
                    console.log('diff');
                    //console.log(aName +'|'+bName);
                    console.log(aOrder +'|'+bOrder);
                    console.log(a);
                    console.log(b);
                }

            }

            return aOrder < bOrder ? -1 : 1;
        },

        saveOrder: function(){
            var folders = [];

            this.each( function( folder ){
                folders.push( {
                    id:         folder.id,
                    order:      folder.get( 'order' ),
                    taxonomy:   folder.get( 'taxonomy' )
                } );
            } );

            $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':   'wicked_folders_save_folder_order',
                        'folders':  folders
                    },
                    method: 'POST',
                    dataType: 'json'
                }
            );
        },

        url: function(){
            return wickedFoldersSettings.ajaxURL + '?action=wicked_folders_fetch_folders&taxonomy=' + this.taxonomy;
        }
    });

    wickedfolders.collections.Notifications = Backbone.Collection.extend({
        model: wickedfolders.models.Notification,
    });

    wickedfolders.views.View = Backbone.View.extend({
        constructor: function( options ) {
            if ( this.options ) {
                options = _.extend( {} , _.result( this, 'options' ), options );
            }
            this.options = options;
            Backbone.View.prototype.constructor.apply( this, arguments );
        }
    });

    wickedfolders.views.FolderSelect = wickedfolders.views.View.extend({
        tagName:    'select',
        events:     {
            'change': 'changed'
        },

        attributes: function(){
            return {
                'id':   'wicked-folder-parent',
                'name': this.options.name
            }
        },

        constructor: function( options ){

            _.defaults( options, {
                selected:           '0',
                name:               'wicked_folder_parent',
                defaultText:        wickedFoldersL10n.folderSelectDefault,
                hideUneditable:     false,
                hideUnassignable:   false
            } );

            wickedfolders.views.View.prototype.constructor.apply( this, arguments );

        },

        initialize: function(){
            this.listenTo( this.collection, 'add remove reset change', this.render );
        },

        render: function(){
            var parent = wp.hooks.applyFilters( 'wickedFolders.folderSelectRenderParent', '0' );
            var id = this.options.selected;

            this.$el.empty();
            this.$el.append( '<option value="0">' + this.options.defaultText + '</option>' );

            this.renderOptions( parent );

            // Make sure the option still exists
            if ( ! this.$( '[value="' + id + '"]' ).length ) {
                this.$el.val( '0' );
            }

            return this;
        },

        renderOptions: function( parent, depth ){

            if ( ! parent ) parent = 'root';
            if ( typeof depth == 'undefined' ) depth = 0;

            var view = this,
                space = '&nbsp;&nbsp;&nbsp;';

            this.collection.each( function( folder ) {
                if ( view.options.hideUneditable && ! folder.get( 'editable' ) ) return;
                if ( view.options.hideUnassignable && ! folder.get( 'assignable' ) ) return;

                if ( folder.get( 'parent' ) == parent ) {
                    var option = $( '<option />' );
                    option.attr( 'value', folder.id );
                    option.html( repeat( space, depth ) + folder.get( 'name' ) );
                    if ( view.options.selected == folder.id ) {
                        option.prop( 'selected', true );
                    }
                    view.$el.append( option );
                    view.renderOptions( folder.id, ( depth + 1 ) );
                }
            });

            function repeat( s, n ){
                var _s = s;
                if ( n < 1 ) return '';
                for ( i = 0; i < n - 1; i++ ) {
                    _s += s;
                }
                return _s;
            }
        },

        changed: function(){
            this.options.selected = this.$el.val();
        }

    });

    wickedfolders.views.FolderPath = wickedfolders.views.View.extend({
        tagName:    'ul',
        className:  'wicked-folder-path',

        initialize: function(){

            this.collection.on( 'change:name', this.render, this );

        },

        render: function() {

            var selected = this.options.selected || '0',
                selectedFolder = this.collection.get( selected ),
                ancestors = this.collection.ancestors( selected ),
                view = this;

            view.$el.empty();

            ancestors.chain().reverse().each( function( folder ){
                var a = $( '<a class="wicked-folder" href="#" />' ),
                    li = $( '<li />' );

                a.html( folder.get( 'name') );
                li.attr( 'data-folder-id', folder.id );
                li.append( a );
                view.$el.append( li );
            } );

            var li = $( '<li />' );
            li.attr( 'data-folder-id', selectedFolder.id );
            li.html( selectedFolder.get( 'name') );
            view.$el.append( li );

        }

    });

    wickedfolders.views.FolderTree = wickedfolders.views.View.extend({
        tagName:    'div',
        className:  'wicked-folder-tree',

        events: {
            'change [type="checkbox"]': 'toggleCheckbox',
            'click .wicked-toggle':     'toggleBranch',
            'click .wicked-folder':     'clickFolder',
        },

        initialize: function( options ) {

            _.defaults( options, {
                model:          false,
                showCheckboxes: false,
                expandAll:      false, // Overrides 'expanded' folder tree state and makes all folders expanded
                showItemCount:  false
            } );

            if ( ! options.model ) {
                this.model = new wickedfolders.models.FolderTreeState();
            }

            this.model.on( 'change:selected', this.changeSelected, this );
            this.model.on( 'change:expanded', this.render, this );
            this.model.on( 'change:checked', this.render, this );
            this.model.on( 'change:search', this.render, this );

        },

        clickFolder: function( e ){

            var id = $( e.currentTarget ).parent().attr( 'data-folder-id' );

            if ( ! this.options.showCheckboxes ) {
                this.model.set( 'selected', id );
                this.model.addExpanded( id );
            }

        },

        changeSelected: function(){

            var selected = this.model.get( 'selected' );

            this.$( 'li' ).removeClass( 'wicked-selected' );

            this.$( 'li[data-folder-id="' + selected + '"]' ).addClass( 'wicked-selected' );

        },

        toggleBranch: function( e ) {
            var id = $( e.currentTarget ).parent().attr( 'data-folder-id' ),
                search = this.model.get( 'search' );

            // Don't allow the branch to be toggled during a search
            if ( search.length ) return;

            this.model.toggleExpanded( id );
        },

        toggleCheckbox: function( e ){
            this.model.toggleChecked( $( e.currentTarget ).val() );

        },

        render: function() {
            // Build the tree
            var branch = this.branch( 'root' );

            branch.$el.addClass( 'wicked-tree' );

            this.$el.html( branch.el );

            return this;
        },

        branch: function( parent ) {
            if ( ! parent ) parent = 'root';

            var FoldersCollection = wickedfolders.collections.Folders,
                FolderLeaf = wickedfolders.views.FolderTreeLeaf,
                PostLeaf = wickedfolders.views.PostTreeLeaf,
                Folder = wickedfolders.views.FolderTreeFolder,
                FolderTreePost = wickedfolders.views.FolderTreePost,
                TreeBranch = wickedfolders.views.FolderTreeBranch,
                TreeBranchToggle = wickedfolders.views.FolderTreeBranchToggle,
                view = this,
                selected = this.model.get( 'selected' ),
                expanded = this.model.get( 'expanded' ),
                checked = this.model.get( 'checked' ) || [],
                readOnly = this.model.get( 'readOnly' ),
                search = this.model.get( 'search' ),
                showCheckboxes = this.options.showCheckboxes,
                showItemCount = this.options.showItemCount,
                expandAll = this.options.expandAll,
                branch;

            var branch = new TreeBranch({ collection: new FoldersCollection() });

            this.collection.each( function( folder ) {
                if ( folder.get( 'parent' ) == parent ) {


                    var folderName      = folder.get( 'name' );
                    var isFolderChecked = -1 != checked.indexOf( folder.id );
                    var leaf            = new FolderLeaf({ model: folder });
                    var folderView      = new Folder({
                        model:          folder,
                        tagName:        showCheckboxes ? 'label' : 'a',
                        showCheckbox:   showCheckboxes,
                        readOnly:       readOnly,
                        checked:        isFolderChecked,
                        showItemCount:  showItemCount
                    });
                    var toggle          = new TreeBranchToggle();
                    var childBranch     = view.branch( folder.id );

                    if ( _.contains( expanded, folder.id ) || expandAll ) {
                        leaf.$el.addClass( 'wicked-expanded' );
                    }

                    if ( selected == folder.id ) {
                        leaf.$el.addClass( 'wicked-selected' );
                    }

                    if ( -1 != checked.indexOf( folder.id ) ) {
                        //folderView.$( 'input[type="checkbox"]' ).prop( 'checked', true );
                        leaf.$( 'input[type="checkbox"]' ).prop( 'checked', true );
                    }

                    leaf.$el.append( toggle.el );
                    leaf.$el.append( folderView.el );

                    if ( childBranch.collection.length || ( view.model.get( 'showFolderContents' ) && folder.get( 'posts' ).length ) ) {
                        leaf.$el.append( childBranch.el );
                    }

                    if ( view.model.get( 'showFolderContents' ) ) {
                        folder.get( 'posts' ).each( function( post ){
                            var postLeaf = new PostLeaf({ model: post });
                            var postView = new FolderTreePost({ model: post });
                            postLeaf.$el.append( postView.el );
                            childBranch.$el.append( postLeaf.el );
                        } );
                    }

                    // Return the branch without adding the current leaf if the
                    // current folder or any of its children don't match the
                    // search term
                    if ( search ) {
                        if ( -1 == folderName.toUpperCase().indexOf( search.toUpperCase() ) ) {
                            leaf.$el.addClass( 'wicked-no-match' );

                            if ( 0 == childBranch.collection.length ) {
                                return branch;
                            } else {
                                leaf.$el.addClass( 'wicked-expanded' );
                            }
                        } else {
                            leaf.$el.addClass( 'wicked-match' );
                            leaf.$el.addClass( 'wicked-expanded' );
                        }
                    }

                    branch.collection.add( folder );

                    branch.$el.append( leaf.el );
                }
            });

            return branch;
        },

        /**
         * Expands the tree to the selected folder.
         */
        expandToSelected: function(){
            var selected = this.model.get( 'selected' ),
                expanded = _.clone( this.model.get( 'expanded' ) ),
                ancestors = this.collection.ancestorIds( selected );

            this.model.set( 'expanded', _.union( expanded, ancestors ) );
        }

    });

    wickedfolders.views.FolderTreeBranch = wickedfolders.views.View.extend({
        tagName: 	'ul',
        initialize: function() {
            //this.render();
        },
        render: function() {
            var FolderView = wickedfolders.views.FolderTreeFolder;
            var view = this;
            this.collection.each( function( folder ) {
                var folderView = new FolderView({ model: folder });
                view.$el.append( folderView.el );
            });
        }
    });

    wickedfolders.views.FolderTreeLeaf = wickedfolders.views.View.extend({
        tagName: 	'li',
        className: 	function() {
            var classes = 'wicked-tree-leaf wicked-folder-leaf';

            if ( 'Wicked_Folders_Term_Folder' == this.model.get('type') && this.model.get( 'editable' ) ) {
                classes += ' wicked-movable';
            }

            if ( this.model.get( 'editable' ) ) {
                classes += ' editable';
            }

            if ( this.model.get( 'assignable' ) ) {
                classes += ' assignable';
            }

            if ( this.model.hasChildren() ) {
                classes += ' has-children';
            }

            return classes;

        },

        initialize: function( options ) {
            /*
            _.defaults( options, {
                showCheckbox:   false,
                readOnly:       false,
            } );

            if ( ! this.$( '.wicked-checkbox' ).length && this.options.showCheckbox ) {
                this.$el.prepend( '<span class="wicked-checkbox"><input type="checkbox" name="wicked_folder[]" value="' + this.model.id + '" /></span>' );
            }

            this.$( '.wicked-checkbox input' ).prop( 'disabled', this.options.readOnly );
            */
            this.render();
        },

        attributes: function() {
            return {
                'data-folder-id': this.model.id,
                'data-item-count': this.model.get( 'itemCount' )
            }
        },

        render: function() {
        }
    });

    wickedfolders.views.PostTreeLeaf = wickedfolders.views.View.extend({
        tagName: 	'li',
        className: 	'wicked-tree-leaf wicked-post-leaf',
        attributes: function() {
            return {
                'data-post-id': this.model.id
            }
        },
    });

    wickedfolders.views.FolderTreeBranchToggle = wickedfolders.views.View.extend({
        tagName: 	'a',
        className: 	'wicked-toggle',
        attributes: {
            href: '#'
        },
        events: {
            'click': 'click'
        },
        click: function( e ){
            e.preventDefault();
        }
    });

    wickedfolders.views.FolderTreeFolder = wickedfolders.views.View.extend({
        tagName: 	'a',
        className: 	'wicked-folder',
        attributes: function(){
            var atts = {};
            if ( 'a' == this.tagName ) atts['href'] = '#';
            return atts;
        },

        events: {
            'click': 'click'
        },

        initialize: function( options ) {

            _.defaults( options, {
                showCheckbox:   false,
                readOnly:       false,
                checked:        false,
                showItemCount:  false
            } );

            this.render();

            this.model.on( 'change:name change:itemCount', this.render, this );

            this.model.on( 'change:loading', function( folder ){
                if ( true == folder.get( 'loading' ) ) {
                    this.$el.addClass( 'wicked-loading' );
                } else {
                    this.$el.removeClass( 'wicked-loading' );
                }
            }, this );

        },

        click: function( e ){
            if ( 'a' == this.tagName ) e.preventDefault();
        },

        render: function() {
            var itemCount = this.model.get( 'itemCount' );

            this.$el.html( this.model.get( 'name' ) );
            //this.$el.append( '<span class="wicked-folder-name">' + this.model.get( 'name' ) + '</span>' );
            if ( ! this.$( '.wicked-icon' ).length ) {
                this.$el.prepend( '<span class="wicked-icon" />' );
            }
            if ( ! this.$( '.wicked-checkbox' ).length && this.options.showCheckbox ) {
                this.$el.prepend( '<span class="wicked-checkbox"><input type="checkbox" name="wicked_folder[]" value="' + this.model.id + '" /></span>' );
            }
            this.$( '.wicked-checkbox input' ).prop( 'checked', this.options.checked );
            this.$( '.wicked-checkbox input' ).prop( 'disabled', this.options.readOnly || ! this.model.get( 'assignable' ) );

            // Only show item count if the item count is enabled for the folder
            // as well as the view itself
            if ( this.model.get( 'showItemCount' ) && this.options.showItemCount ) {
                this.$el.append( '<span class="wicked-count">' + itemCount + '</span>' );
            }
        }

    });

    wickedfolders.views.FolderTreePost = wickedfolders.views.View.extend({
        tagName: 	'a',
        className: 	'wicked-post',
        attributes: {
            href: '#'
        },

        events: {
            'click': 'click'
        },

        initialize: function() {

            this.render();

        },

        click: function( e ){
            e.preventDefault();
        },

        render: function() {
            this.$el.html( this.model.get( 'name' ) );
        }

    });

    wickedfolders.models.FolderBrowserController = wickedfolders.models.Model.extend({

        _saveStateTimer: null,

        defaults: {
            id:                     1,
            folders:                false,
            folder:                 false,
            expanded:               [ '0' ],
            postType:               false,
            taxonomy:               false,
            loading:                false,
            treePaneWidth:          400,
            showContentsInTreeView: false,
            hideAssignedItems:      true,
            orderby:                'title',
            order:                  'asc'
        },

        initialize: function(){

            this.on( 'change', this.saveState, this );

        },

        saveState: function(){

            var model = this;

            clearTimeout( this._saveStateTimer );

            // Wait a second before saving in case another action triggers
            // a save
            this._saveStateTimer = setTimeout( function(){
                model.save();
            }, 1000 );

        },

        moveObject: function( objectType, objectId, destinationObjectId, sourceFolderId, copy ) {

            // TODO: probably a better way to handle all of this...

            var model = this;

            $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':                   'wicked_folders_move_object',
                        //'nonce': WickedFolderSettings.moveObjectNonce,
                        'object_type':              objectType,
                        'object_id':                objectId,
                        'destination_object_id':    destinationObjectId,
                        'source_folder_id':         copy ? false : sourceFolderId,
                        'post_type':                model.get( 'postType' )
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function( data ) {
                        model.updateItemCounts( data.folders );
                    },
                    error: function( data ) {
                    }
                }
            );

        },

        unassignFolders: function( objectId ) {
            var model = this;

            $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':       'wicked_folders_unassign_folders',
                        'object_id':    objectId,
                        'taxonomy':     model.get( 'taxonomy' )
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function( data ) {
                        model.updateItemCounts( data.folders );
                    },
                    error: function( data ) {
                    }
                }
            );
        },

        url: function(){
            return wickedFoldersSettings.ajaxURL + '?action=wicked_folders_save_state';
        },

        updateItemCounts: function( folders ){
            var model = this;

            _.each( folders, function( folder ){
                var _folder = model.get( 'folders' ).get( folder.id );

                if ( 'undefined' != typeof _folder ) {
                    _folder.set( 'itemCount', folder.itemCount );
                }
            });
        }
    });

    wickedfolders.models.ObjectFolderPaneController = wickedfolders.models.Model.extend({
        defaults: {
            id:                     1,
            folders:                false,
            folder:                 false,  // The currently active folder object (not folder ID)
            expanded:               [ '0' ],
            postType:               false,
            taxonomy:               false,
            loading:                false,
            orderby:                'title',
            order:                  'asc',
            isFolderPaneVisible:    true,
            treePaneWidth:          292,
            lang:                   false,
            organizationMode:       'organize',
            sortMode:               'custom',
            showItemCount:          true,
            enableCreate:           true,
            enableAssign:           true
        },

        initialize: function(){

            var view = this;

            view.saveDebounced = _.debounce( this.save, 1000 )

            this.on( 'change', function(){
                view.saveDebounced();
            }, this );

        },

        url: function(){
            return wickedFoldersSettings.ajaxURL + '?action=wicked_folders_save_state';
        }

    });

    wickedfolders.models.FolderState = wickedfolders.models.Model.extend({
        defaults: {
            sortColumn:     'title',
            needsUpdate:    true,
            content:        '',
            selected:       []
        }
    });

    wickedfolders.views.FolderDialog = wickedfolders.views.View.extend({

        folderSelectView: false,

        events: {
            'click .wicked-popup-mask':             'onClose',
            'click .wicked-popup-close':            'onClose',
            'click .wicked-cancel':                 'onClose',
            'keyup [name="wicked_folder_name"]':    'setSaveButtonState',
            'blur [name="wicked_folder_name"]':     'setSaveButtonState',
            'submit form':                          'save'
        },

        initialize: function( options ){

            _.defaults( options, {
                mode: 'edit'
            } );

            this.folderSelectView = new wickedfolders.views.FolderSelect({
                collection:     this.collection,
                selected:       this.model.get( 'parent' ),
                defaultText:    '&mdash; ' + wickedFoldersL10n.folderSelectDefault + ' &mdash;'
            });

            this.setFolder( this.model );

        },

        setFolder: function( folder ){

            this.model = folder;

            this.model.on( 'request', function(){
                this.$( '.wicked-save' ).prop( 'disabled', true );
            }, this );

        },

        render: function(){

            var view = this,
                template = _.template( $( '#tmpl-wicked-folder-dialog' ).html() ),
                mode = this.options.mode,
                saveButtonLabel = wickedFoldersL10n.save,
                title = wickedFoldersL10n.editFolderLink;

            if ( 'add' == mode ) {
                title = wickedFoldersL10n.addNewFolderLink;
            }

            if ( 'delete' == mode ) {
                title           = wickedFoldersL10n.deleteFolderLink;
                saveButtonLabel = wickedFoldersL10n.delete;
            }

            template = template({
                mode:                       mode,
                dialogTitle:                title,
                folderName:                 this.model.get( 'name' ),
                saveButtonLabel:            saveButtonLabel,
                deleteFolderConfirmation:   wickedFoldersL10n.deleteFolderConfirmation
            });

            this.folderSelectView.options.selected = this.model.get( 'parent' );

            this.$el.html( template );
            //this.$( '.wicked-folder-parent' ).html( this.folderSelectView.render() );
            this.$( '.wicked-folder-parent' ).html( this.folderSelectView.render().el );

            this.setSaveButtonState();

        },

        onClose: function( e ){

            e.preventDefault();

            this.close();

        },

        open: function(){

            this.$( '.wicked-popup-mask' ).show();
            this.$( '.wicked-popup' ).show();

            if ( 'delete' != this.options.mode ) {
                this.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();
            }

        },

        close: function(){

            this.$( '.wicked-popup-mask' ).hide();
            this.$( '.wicked-popup' ).hide();

        },

        save: function( e ){

            var view = this;

            e.preventDefault();

            if ( 'delete' == this.options.mode ) {
                this.model.set( '_methodOverride', 'DELETE' );
                this.model.destroy( {
                    success: function(){
                        view.close();
                    }
                } );

            } else {

                this.model.set( {
                    name:   this.$( '[name="wicked_folder_name"]' ).val(),
                    parent: this.$( '[name="wicked_folder_parent"]' ).val()
                } );

                this.model.save( {}, {
                    success: function( model, response, options ){
                        view.setSaveButtonState();
                        view.close();
                        if ( 'add' == view.options.mode ) {
                            view.collection.add( view.model );
                        }
                    },
                    error: function( model, response, options ){
                        view.$( '.wicked-errors' ).text( response.responseJSON.message ).show();
                        view.setSaveButtonState();
                    }
                } );

            }

        },

        setSaveButtonState: function(){

            var disabled = false;

            if ( 'delete' != this.options.mode ) {
                if ( this.$( '[name="wicked_folder_name"]' ).val().length < 1 ) {
                    disabled = true;
                }
            }

            this.$( '.wicked-save' ).prop( 'disabled', disabled );

        }

    });

    wickedfolders.views.FolderBrowser = wickedfolders.views.View.extend({
        folderDialogView:   false,
        folderTreeView:     false,
        folderStates:       false,

        events: {
            'click .wicked-folder':                     'clickFolder',
            'click .wicked-add-new-folder':             'addNewFolder',
            'click .wicked-edit-folder':                'editFolder',
            'click .wicked-clone-folder':               'cloneFolder',
            'click .wicked-delete-folder':              'deleteFolder',
            'click .wicked-expand-all':                 'expandAllFolders',
            'click .wicked-collapse-all':               'collapseAllFolders',
            'change .check-column [type="checkbox"]':   'togglePostSelection',
            'change #wicked-hide-assigned':             'toggleHideAssigned',
            'submit form':                              'formSubmitted'
        },

        initialize: function(){

            var view = this,
                folderStates = new Backbone.Collection(),
                expanded = this.model.get( 'expanded' ),
                folder = this.model.get( 'folder' );

            // Make sure the selected folder's ancestors are expanded upon
            // initialization to ensure the selected folder is visible in the
            // tree
            var ancestors = this.folders().ancestors( folder.id );

            ancestors.each( function( ancestor ){
                expanded.push( ancestor.id );
            } );

            expanded = _.uniq( expanded );

            // Initialize folder tree view
            var folderTree = new wickedfolders.views.FolderTree({
                collection: this.model.get( 'folders' ),
                model:      new wickedfolders.models.FolderTreeState({
                    selected:           folder.id,
                    expanded:           expanded,
                    showFolderContents: this.model.get( 'showContentsInTreeView' ),
                })
            });

            this.folderTreeView = folderTree;

            // Set up folder states
            this.folders().each( function( folder ){
                folderStates.add( new wickedfolders.models.FolderState({
                    id:             folder.id,
                    needsUpdate:    folder.id != view.folder().id
                }) );
                folder.get( 'posts' ).on( 'add', view.folderPostAddedOrRemoved, view );
                folder.get( 'posts' ).on( 'remove', view.folderPostAddedOrRemoved, view );
            });

            // Initialize the current folder's content
            folderStates.get( view.folder().id ).set( 'content', this.$( '.wicked-folder-contents-pane' ).html() );

            this.folderStates = folderStates;

            // Bind events
            this.folderStates.on( 'change:selected', this.updateFolderStateSelections, this );
            this.folders().on( 'add', this.folderAdded, this );
            this.folders().on( 'remove', this.folderRemoved, this );
            this.folders().on( 'change:parent', this.folderParentChanged, this );
            //this.folders().get( 'posts' ).on( 'add', this.folderPostAddedOrRemoved, this );
            //this.folders().get( 'posts' ).on( 'remove', this.folderPostAddedOrRemoved, this );
            this.model.on( 'change:folder', this.folderChanged, this );
            this.model.on( 'change:loading', this.setLoadingMask, this );
            this.model.on( 'moveObjectStart', this.onMoveObjectStart, this );
            this.folderTreeView.model.on( 'change:expanded', this.changeExpanded, this );

            this.folderDialogView = new wickedfolders.views.FolderDialog({
                model:      this.folder(),
                collection: this.folders()
            });

            this.render();

        },

        /**
         * Returns the folder currently being viewed.
         */
        folder: function(){
            return this.model.get( 'folder' );
        },

        /**
         * Returns the view's folders collection.
         */
        folders: function(){
            return this.model.get( 'folders' );
        },

        /**
         * Returns the view's folder states.
         */
        folderState: function() {
            return this.folderStates.get( this.folder().id );
        },

        changeExpanded: function() {

            // Keep the controller's expanded state in sync with the folder
            // tree's state
            this.model.set( 'expanded', this.folderTreeView.model.get( 'expanded' ) );

        },

        /**
         * Change handler for post row select checkboxes.
         */
        togglePostSelection: function( e ){

            var checkbox = $( e.currentTarget ),
                row = checkbox.parents( 'tr' ),
                table = this.$( '.wicked-folder-contents-pane .wp-list-table' ),
                ids = _.clone( this.folderState().get( 'selected' ) );

            if ( -1 == checkbox.attr( 'id' ).indexOf( 'select-all' ) ) {
                id = row.attr( 'id' ).substring( 5 );
                if ( checkbox.prop( 'checked' ) ) {
                    ids.push( id );
                } else {
                    ids = _.without( ids, id );
                }
            } else {
                // Handle select all checkboxes
                if ( checkbox.prop( 'checked' ) ) {
                    table.find( 'tbody tr' ).each( function( index, row ){
                        ids.push( $( row ).find( '.check-column [type="checkbox"]' ).val() );
                    } );
                } else {
                    ids = [];
                }
            }

            this.folderState().set( 'selected', ids );

        },

        toggleHideAssigned: function( e ){

            this.model.set( 'hideAssignedItems', $( e.currentTarget ).prop( 'checked' ) );

            this.folderState().set( 'needsUpdate', true ),
            this.folderChanged();

        },

        updateFolderStateSelections: function() {

            var selected = this.folderState().get( 'selected' ),
                disableMoveMultiple = this.folderState().get( 'selected' ).length < 1,
                table = this.$( '.wicked-folder-contents-pane .wp-list-table' ),
                isMedia = table.hasClass( 'media' ),
                view = this;

            // Clear out items from the move multiple container
            this.$( '.wicked-move-multiple .wicked-items' ).empty();

            // Reset all to unchecked
            table.find( 'tbody .check-column [type="checkbox"]' ).prop( 'checked', false );

            _.each( selected, function( id ){
                table.find( 'tbody .check-column [type="checkbox"][value="' + id + '"]' ).prop( 'checked', true );
            } );

            checkedRows = this.$( '.wicked-folder-contents-pane tbody .check-column [type="checkbox"]:checked' ).parents( 'tr' )

            table.find( 'tbody .check-column [type="checkbox"]' ).each( function( index, checkbox ){

                var checkbox = $( checkbox ),
                    id = checkbox.val(),
                    row = checkbox.parents( 'tr' );

                // Get the post's name
                if ( isMedia ) {
                    var name = $( row ).find( '.title a' ).eq( 0 ).text().trim();
                } else {
                    var name = $( row ).find( '.row-title' ).text().trim();
                }

                if ( checkbox.prop( 'checked' ) ) {

                    // Append a div with the post's name to the move multiple
                    // container so that the post names are displayed when moving
                    // multiple posts
                    _.each( checkedRows, function( row ){
                        $( row ).find( '.wicked-move-multiple .wicked-items' ).append( '<div data-object-id="' + id + '">' + name + '</div>' );
                    });
                    table.find( 'thead .wicked-move-multiple .wicked-items' ).append( '<div data-object-id="' + id + '">' + name + '</div>' );
                } else {
                    view.$( '.wicked-move-multiple[data-object-id="' + id + '"] .wicked-items' ).append( '<div data-object-id="' + id + '">' + name + '</div>' );
                }

            } );

            view.$( 'th .wicked-move-multiple' ).draggable( 'option', 'disabled', disableMoveMultiple );

        },

        clickFolder: function( e ) {

            var id = $( e.currentTarget ).parent().attr( 'data-folder-id' ),
                folder = this.model.get( 'folders' ).get( id );

            this.model.set( 'folder', folder );

        },

        folderPostAddedOrRemoved: function( post ){

            this.renderFolderTree();

        },

        folderAdded: function( folder ){

            this.folderStates.add( new wickedfolders.models.FolderState({
                id:             folder.id,
                needsUpdate:    true
            }) );

            this.renderFolderTree();

        },

        folderRemoved: function( folder ){

            var parent = this.folders().get( folder.get( 'parent' ) );

            // If the current folder was removed, switch to the parent folder
            if ( this.folder().id == folder.id ) {
                this.model.set( 'folder', parent );
            }

            // Move the deleted folder's children to it's parent.  Note: the
            // backend takes care of updating the children's parent when the
            // folder is removed so keep this silent
            var children = this.folders().where( { parent: folder.id } );

            _.each( children, function( child ){
                child.set( 'parent', parent.id, { silent: true } );
            } );

            this.renderFolderTree();

        },

        folderChanged: function(){

            var view = this,
                id = this.model.get( 'folder' ).get( 'id' ),
                folderType = this.model.get( 'folder' ).get( 'type' ),
                folderState = this.folderStates.get( id ),
                router = this.model.get( 'router' ),
                s = this.$( '#post-search-input' ).val(),
                postType = this.model.get( 'postType' );

            this.$el.attr( 'data-folder', id );
            this.$( '[name="folder"]' ).val( id );

            this.renderFolderPath();
            this.renderActions();
            this.folderTreeView.model.set( 'selected', id );

            if ( folderState.get( 'needsUpdate' ) ) {

                this.model.set( 'loading', true );

                // Prevent hide assigned checkbox from triggering additional loads
                this.$( '#wicked-hide-assigned' ).prop( 'disabled', true );

                /*
                , {
                    post_type:  view.model.get( 'postType' ),
                    page:       'page_' + view.model.get( 'postType' ) + '_folders',
                    folder:     id
                }*/
                var url = 'edit.php?page=wf_' + view.model.get( 'postType' ) + '_folders&folder=' + id + '&folder_type=' + folderType;
                // post_type parameter in URL causes conflicts in WordPress for
                // 'post' types
                if ( 'post' != postType ) url += '&post_type=' + postType;

                url += '&hide_assigned=' + ( this.model.get( 'hideAssignedItems' ) ? 1 : 0 );

                if ( s ) url += '&s=' + s;

                $.get( url, function( data ){
                    data = $( '<div />' ).append( data );
                    folderState.set( 'content', $( data ).find( '#wicked-folder-browser .wicked-folder-contents-pane' ).html() );
                    folderState.set( 'needsUpdate', false );
                    view.renderFolderContent();
                    view.model.set( 'loading', false );

                    // Kludgy fix to fix the sort order when it is set differently
                    // on the server side
                    if ( view.$( '.column-wicked_sort' ).hasClass( 'sorted' ) ) {
                        view.model.set( 'orderby', 'wicked_folder_order' );
                        if ( view.$( '.column-wicked_sort' ).hasClass( 'asc' ) ) {
                            view.model.set( 'order', 'asc' );
                        } else {
                            view.model.set( 'order', 'desc' );
                        }
                    }

                    // Re-enable hide assigned checkbox
                    view.$( '#wicked-hide-assigned' ).prop( 'disabled', false );

                } );
                /*
                $.get( ajaxurl, {
                    action:     'wicked_folders_get_contents',
                    post_type:  view.model.get( 'postType' ),
                    paged:      folderState.get( 'page' ),
                    orderby:    folderState.get( 'orderby' ),
                    order:      folderState.get( 'order' ),
                    folder:     id,
                    screen:     view.model.get( 'screen' ),
                    items_per_page: view.model.get( 'itemsPerPage' )
                }, function( content ){
                    folderState.set( 'content', content );
                    folderState.set( 'needsUpdate', false );
                    view.renderFolderContent();
                    view.model.set( 'loading', false );
                } );
                */
            } else {
                view.renderFolderContent();
            }

        },

        folderParentChanged: function( folder ){

            folder.save();

            this.renderFolderPath();
            this.renderFolderTree();

        },

        render: function() {

            var view = this;

            //this.$el.html( _.template( $( '#tmpl-wicked-folder-browser' ).html() ) );

            this.renderActions();
            this.renderFolderPath();
            this.renderFolderTree();
            this.renderFolderContent();
            this.renderFolderDialog();

            this.$( '.wicked-folder-tree-pane' ).resizable( {
                resizeHeight:   false,
                handles:        'e',
                minWidth:       200,
                containment:    this.$el,
                stop:           function() {
                    var width = view.$( '.wicked-folder-tree-pane' ).width();
                    view.model.set( 'treePaneWidth', width );
                }
            } );

        },

        renderFolderContent: function(){

            var view = this,
                folder = this.folder();

            view.saveSortOrderDebounced = _.debounce( this.saveSortOrder, 500 )

            this.$( '.wicked-folder-contents-pane' ).html( this.folderState().get( 'content' ) );

            // Move pagination
            this.$( '.wicked-head .tablenav' ).remove();
            this.$( '.wicked-foot .tablenav' ).remove();

            this.$( '.wicked-folder-contents-pane .tablenav.top' ).appendTo( this.$( '.wicked-head .wicked-lower-row .wicked-right' ) );
            this.$( '.wicked-folder-contents-pane .tablenav.bottom' ).appendTo( this.$( '.wicked-foot .wicked-right' ) );

            // Enable/disable search column toggle as necessary
            // TODO: remember previous sort column visibility selection
            if ( 'Wicked_Folders_Term_Folder' == folder.get( 'type' ) && '0' != folder.id && ! this.model.get('isSearch') ) {
                $( '#screen-meta #adv-settings #wicked_sort-hide' ).prop( 'checked', true ).prop( 'disabled', false );
            } else {
                $( '#screen-meta #adv-settings #wicked_sort-hide' ).prop( 'checked', false ).prop( 'disabled', true );
            }

            // Preserve hidden columns
            $( '#screen-meta #adv-settings [type="checkbox"]' ).each( function( index, item ){
                var column = $( item ).val();
                if ( $( item ).prop( 'checked' ) ) {
                    view.$( '.wicked-folder-contents-pane .column-' + column ).removeClass( 'hidden' );
                } else {
                    view.$( '.wicked-folder-contents-pane .column-' + column ).addClass( 'hidden' );
                }
            } );

            // Fix colspan for 'no items found' row
            var columnCount = view.$( 'thead > tr > th, thead > tr > td' ).length,
                hiddenColumnCount = $( '#screen-meta #adv-settings [type="checkbox"]' ).not( ':checked' ).length;

            view.$( '.colspanchange' ).attr( 'colspan', columnCount - hiddenColumnCount );

            /*
            // Preserve orderby
            this.$( '.wicked-folder-contents-pane th.sorted' ).addClass( 'sortable' );
            this.$( '.wicked-folder-contents-pane th' ).removeClass( 'sorted' );
            this.$( '.wicked-folder-contents-pane th.column-' + this.folderState().get( 'sortColumn' ) )
                .removeClass( 'desc' )
                .removeClass( 'asc' )
                .addClass( 'sorted' )
                .addClass(this.folderState().get( 'order' ) );
                */

            this.$( '.wicked-folder-contents-pane .wicked-move-multiple' ).draggable( {
                revert:         'invalid',
                helper:         'clone',
                containment:    '#wicked-folder-browser .wicked-panes',
                appendTo:       '#wicked-folder-browser'
            } );

            // Only make term folders sortable
            if ( 'Wicked_Folders_Term_Folder' == folder.get( 'type' ) && '0' != folder.id && ! this.model.get('isSearch') ) {
                this.$( '.wicked-folder-contents-pane' ).sortable( {
                    items:          'tr',
                    revert:         'invalid',
                    handle:         'a.wicked-sort',
                    containment:    '#wicked-folder-browser .wicked-folder-contents-pane tbody',
                    placeholder:    'sortable-placeholder',
                    tolerance:      'pointer',
                    delay:          100,
                    helper:         function( e, ui ){
                        // Thanks to https://paulund.co.uk/fixed-width-sortable-tables
                        // for the snippet to maintain column widths of dragged
                        // table row
                        ui.children().each( function() {
                            $( this ).width( $( this ).width() );
                        } );
                        return ui;
                    },
                    start:          function( e, ui ){
                        // Hide columns in the placeholder row that have been
                        // hidden in the table using the toggles in Screen Options
                        ui.placeholder.children().each( function( index ){
                            if ( view.$( 'thead > tr > *' ).eq( index ).hasClass( 'hidden' ) ) {
                                $( this ).css( 'display', 'none' );
                            }
                        } );
                        return ui;
                    },
                    stop:           function( e, ui ) {
                        view.saveSortOrderDebounced();
                    }
                } );
            }

            this.updateFolderStateSelections();

        },

        renderFolderTree: function(){

            var view = this;

            // Use setElement so that events are re-bound
            this.folderTreeView.setElement( this.$( '.wicked-folder-tree' ) ).render();

            this.$( '.wicked-folder-tree-pane' ).width( this.model.get( 'treePaneWidth' ) );

            this.$( '.wicked-folder-leaf.wicked-movable' ).not( '[data-folder-id="0"]' ).draggable( {
                revert: 'invalid',
                helper: 'clone'
            } );

            this.$( '.wicked-post-leaf' ).draggable( {
                revert: 'invalid',
                helper: 'clone'
            } );

            this.$( '.wicked-tree [data-folder-id="0"] .wicked-folder' ).droppable( {
                hoverClass: 'wicked-drop-hover',
                accept: function( draggable ){

                    var destinationFolderId = $( this ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' ),
                        folder = view.folder(),
                        accept = false;

                    if ( draggable.hasClass( 'wicked-folder' ) || draggable.hasClass( 'wicked-folder-leaf' ) || draggable.hasClass( 'wicked-post-leaf' ) || draggable.hasClass( 'wicked-move-multiple' ) || ( draggable.is( 'tr' ) && -1 != draggable.attr( 'id' ).indexOf( 'post-' ) ) ) {
                        accept = true;
                    }

                    if ( draggable.is( 'tr' ) && -1 != draggable.attr( 'id' ).indexOf( 'post-' ) ) {
                        // Don't allow posts to be moved to the folder they're already in
                        if ( destinationFolderId == folder.id ) {
                            accept = false;
                        }
                        // For now, don't allow posts to be dragged to 'all folders'
                        if ( destinationFolderId == 0 ) {
                            accept = false;
                        }
                    }

                    if ( draggable.hasClass( 'wicked-folder-leaf' ) ) {
                        var parent = draggable.parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );
                        // Don't allow folders to be moved to the folder they're already in
                        if ( destinationFolderId == parent ) {
                            accept = false;
                        }
                    }

                    if ( draggable.hasClass( 'wicked-post-leaf' ) ) {
                        var parent = draggable.parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );
                        // Don't allow posts to be moved to the folder they're already in
                        if ( destinationFolderId == parent ) {
                            accept = false;
                        }
                        // For now, don't allow posts to be dragged to 'all folders'
                        if ( destinationFolderId == 0 ) {
                            accept = false;
                        }
                    }

                    if ( draggable.hasClass( 'wicked-move-multiple' ) ) {
                        // Don't allow posts to be moved to the folder they're already in
                        if ( destinationFolderId == folder.id ) {
                            accept = false;
                        }
                        if ( destinationFolderId == 0 ) {
                            accept = false;
                        }
                    }

                    return accept;

                },
                tolerance: 'pointer',
                drop: function( e, ui ) {

                    // TODO: clean this up

                    var destinationFolderId = $( this ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );

                    if ( ui.draggable.is( 'tr' ) && -1 != ui.draggable.attr( 'id' ).indexOf( 'post-' ) ) {

                        var objectId = $( ui.draggable ).attr( 'id' ).substring( 5 );

                        view.model.moveObject( 'post', objectId, destinationFolderId, view.folder().id );
                        view.folderStates.get( view.folder().id ).set( 'needsUpdate', true );
                        view.folderStates.get( destinationFolderId ).set( 'needsUpdate', true );
                        view.removePostRows( objectId );

                    } else if ( ui.draggable.hasClass( 'wicked-post-leaf' ) ) {

                        var objectId    = $( ui.draggable ).attr( 'data-post-id' );
                        sourceFolderId  = $( ui.draggable ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );

                        view.model.moveObject( 'post', objectId, destinationFolderId, sourceFolderId );
                        view.folderStates.get( sourceFolderId ).set( 'needsUpdate', true );
                        view.folderStates.get( destinationFolderId ).set( 'needsUpdate', true );
                        view.removePostRows( objectId );

                        var destinationFolderPosts = view.folders().get( destinationFolderId ).get( 'posts' ),
                            sourceFolderPosts = view.folders().get( sourceFolderId ).get( 'posts' ),
                            post = sourceFolderPosts.get( objectId );

                        sourceFolderPosts.remove( post );
                        destinationFolderPosts.add( post );


                    } else if ( ui.draggable.hasClass( 'wicked-move-multiple' ) ) {

                        var objectIds = [];

                        ui.draggable.find( '.wicked-items div' ).each( function( index, item ){
                            objectIds.push( $( item ).attr( 'data-object-id' ) );
                        });

                        view.model.moveObject( 'post', objectIds, destinationFolderId, view.folder().id );
                        view.folderStates.get( view.folder().id ).set( 'needsUpdate', true );
                        view.folderStates.get( destinationFolderId ).set( 'needsUpdate', true );
                        view.removePostRows( objectIds );

                    } else {

                        var objectId = $( ui.draggable ).attr( 'data-folder-id' );
                        view.folders().get( objectId ).set( 'parent', destinationFolderId );

                    }

                }
            });

        },

        renderFolderPath: function(){

            var controller = this.model,
                folders = this.model.get( 'folders' ),
                folder = this.model.get( 'folder' );

            var folderPath = new wickedfolders.views.FolderPath({
                collection: folders,
                selected:   folder.id
            });

            folderPath.render();

            this.$( '.wicked-folder-path-pane' ).html( folderPath.el );

        },

        renderFolderDialog: function(){

            this.folderDialogView.setElement( $( '#wicked-folder-dialog-container') ).render();

        },

        renderActions: function(){

            var type = this.folder().get( 'type' );

            this.$( '.wicked-folder-browser-actions' ).empty();

            jQuery( '#wp-admin-bar-wicked-folders-edit-folder' ).hide();
            jQuery( '#wp-admin-bar-wicked-folders-clone-folder' ).hide();
            jQuery( '#wp-admin-bar-wicked-folders-delete-folder' ).hide();

            var ul = $( '<ul class="subsubsub">' );
            ul.append( '<li><a class="wicked-add-new-folder" href="#">' + wickedFoldersL10n.addNewFolderLink + '</a>|</li>' );

            if ( '0' != this.folder().id && 'Wicked_Folders_Term_Folder' == type ) {
                ul.append( '<li><a class="wicked-edit-folder" href="#">' + wickedFoldersL10n.editFolderLink + '</a>|</li>' );
                ul.append( '<li><a class="wicked-clone-folder" href="#">' + wickedFoldersL10n.cloneFolderLink + '</a><span class="dashicons dashicons-editor-help" title="' + wickedFoldersL10n.cloneFolderTooltip + '"></span>|</li>' );
                ul.append( '<li><a class="wicked-delete-folder" href="#">' + wickedFoldersL10n.deleteFolderLink + '</a>|</li>' );

                jQuery( '#wp-admin-bar-wicked-folders-edit-folder' ).show();
                jQuery( '#wp-admin-bar-wicked-folders-clone-folder' ).show();
                jQuery( '#wp-admin-bar-wicked-folders-delete-folder' ).show();

            }

            ul.append( '<li><a class="wicked-expand-all" href="#">' + wickedFoldersL10n.expandAllFoldersLink + '</a>|</li>' );
            ul.append( '<li><a class="wicked-collapse-all" href="#">' + wickedFoldersL10n.collapseAllFoldersLink + '</a></li>' );

            if ( '0' == this.folder().id ) {
                ul.append( '<li>| <input id="wicked-hide-assigned" name="hide_assigned" type="checkbox" value="1" /><label for="wicked-hide-assigned">' + wickedFoldersL10n.hideAssignedItems + '</label> <span class="dashicons dashicons-editor-help" title="' + wickedFoldersL10n.hideAssignedItemsTooltip + '"></span></li>' );
            }

            this.$( '.wicked-folder-browser-actions' ).append( ul );

            this.$( '#wicked-hide-assigned' ).prop( 'checked', this.model.get( 'hideAssignedItems' ) );

        },

        addNewFolder: function( e ){

            e.preventDefault();

            this.folderDialogView.options.mode = 'add';
            this.folderDialogView.setFolder( new wickedfolders.models.Folder({
                parent:     this.folder().id,
                postType:   this.model.get( 'postType' ),
                taxonomy:   this.model.get( 'taxonomy' )
            }) );
            this.folderDialogView.render();
            this.folderDialogView.open();

        },

        editFolder: function( e ){

            e.preventDefault();

            this.folderDialogView.options.mode = 'edit';
            this.folderDialogView.setFolder( this.folder() );
            this.folderDialogView.render();
            this.folderDialogView.open();

        },

        cloneFolder: function( e ){
            e.preventDefault();

            var view = this

            this.model.set( 'loading', true );

            this.folder().cloneFolder()
                .done( function( folders ){
                    _.each( folders, function( folder ){
                        view.folders().add( folder );
                    } );

                    view.model.set( 'loading', false );
                } )
                .fail( function( error ){
                    view.model.set( 'loading', false );
                });
        },

        deleteFolder: function( e ){

            e.preventDefault();

            this.folderDialogView.options.mode = 'delete';
            this.folderDialogView.setFolder( this.folder() );
            this.folderDialogView.render();
            this.folderDialogView.open();

        },

        expandAllFolders: function( e ){

            e.preventDefault();

            var ids = this.folders().pluck( 'id' );

            this.folderTreeView.model.set( 'expanded', ids );

        },

        collapseAllFolders: function( e ){

            e.preventDefault();

            this.folderTreeView.model.set( 'expanded', [ '0' ] );

        },

        setLoadingMask: function(){
            if ( this.model.get( 'loading' ) ) {
                this.$( '.wicked-body' ).addClass( 'wicked-loading-mask' );
            } else {
                this.$( '.wicked-body' ).removeClass( 'wicked-loading-mask' );
            }
        },

        removePostRows: function( postId ){

            var view = this,
                ids = _.isArray( postId ) ? postId : [ postId ];

            _.each( ids, function( id ){
                view.$( '.wicked-folder-contents-pane tr[id="post-' + id + '"]' ).fadeOut( 500, function(){
                    $( this ).remove();
                });
            } );

        },

        formSubmitted: function( e ) {

            // Remove inputs like _wpnonce and _wp_http_referer
            this.$( 'input[name^="_"]' ).remove();

            // Reset current page field when form is submitted. This fixes the
            // issue of being on the wrong page when searching and is how
            // WordPress appears to handle the issue (see wp-admin/js/common.js)
            if ( this.$( 'input.current-page' ).length ) {
                if ( this.model.get( 'pageNumber' ) == this.$( 'input.current-page' ) ) {
                    this.$( 'input.current-page' ).val( '1' );
                }
            }

        },

        saveSortOrder: function() {

            var view = this;

            var ids = _.map( this.$( '.wicked-folder-contents-pane tbody tr' ), function( item ){
                return $( item ).find( '.check-column [type="checkbox"]' ).val();
            });

            $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':           'wicked_folders_save_sort_order',
                        'folder_id':        this.folder().id,
                        'screen':           this.model.get( 'screen' ),
                        'items_per_page':   this.model.get( 'itemsPerPage' ),
                        'page_number':      this.model.get( 'pageNumber' ),
                        'taxonomy':         this.model.get( 'taxonomy' ),
                        'post_type':        this.model.get( 'postType' ),
                        'order':            this.model.get( 'order' ),
                        'orderby':          this.model.get( 'orderby' ),
                        'object_ids':       ids,
                    },
                    method: 'POST',
                    dataType: 'json',
                    complete: function( data ) {
                        view.model.set( 'orderby', 'wicked_folder_order' );
                        view.model.set( 'order', 'asc' );

                        // Bug with posts table...sorted column doesn't have
                        // sortable class which breaks layout when sorted class
                        // is removed
                        view.$( '.wicked-folder-contents-pane th.sorted' ).addClass( 'sortable' );
                        view.$( '.wicked-folder-contents-pane th' ).removeClass( 'sorted' );
                        view.$( '.wicked-folder-contents-pane th.column-wicked_sort' ).addClass( 'sorted' ).removeClass( 'desc' ).addClass( 'asc' );

                        view.$( '.wicked-folder-contents-pane th.column-title' ).removeClass( 'asc' ).addClass( 'desc' );
                        view.$( '.wicked-folder-contents-pane th.column-date' ).removeClass( 'desc' ).addClass( 'asc' );

                        // Reset title column link
                        var $titleLink = view.$( '.wicked-folder-contents-pane th.column-title a' );
                        $titleLink.attr( 'href', $titleLink.attr( 'href' ).replace( 'order=desc', 'order=asc' ) );

                        // Reset date column link
                        var $dateLink = view.$( '.wicked-folder-contents-pane th.column-date a' );
                        $dateLink.attr( 'href', $dateLink.attr( 'href' ).replace( 'order=asc', 'order=desc' ) );

                        // Update sort column link
                        var $sortLink = view.$( '.wicked-folder-contents-pane th.column-wicked_sort a' );
                        $sortLink.attr( 'href', $sortLink.attr( 'href' ).replace( 'order=asc', 'order=desc' ) );

                        // Update pagination links
                        view.$( '.pagination-links a' ).each( function( index, link ){
                            var url = $( link ).attr( 'href' );
                            url = wickedfolders.util.updateUrlParam( url, 'order', 'asc' );
                            url = wickedfolders.util.updateUrlParam( url, 'orderby', 'wicked_folder_order' );
                            $( link ).attr( 'href', url );
                        } );

                        // Append/update orderby and order hidden fields
                        if ( ! view.$( 'input[name="orderby"]' ).length ) {
                            view.$( 'form' ).append( '<input type="hidden" name="orderby" value="wicked_folder_order" />' );
                        } else {
                            view.$( 'input[name="orderby"]' ).val( 'wicked_folder_order' );
                        }

                        if ( ! view.$( 'input[name="order"]' ).length ) {
                            view.$( 'form' ).append( '<input type="hidden" name="order" value="asc" />' );
                        } else {
                            view.$( 'input[name="order"]' ).val( 'asc' );
                        }

                        // Update the cached copy of the folder's content so the
                        // order changes are preserved
                        view.folderState().set( 'content', view.$( '.wicked-folder-contents-pane' ).html() );

                        view.model.trigger( 'sortOrderChanged' );

                    },
                    error: function( data ) {
                    }
                }
            );

        }

    });

    wickedfolders.views.ObjectFolderPaneToolbar = wickedfolders.views.View.extend({
        tagName: 	'ul',
        className: 	'wicked-folder-pane-toolbar',
        events: {
            'click a': 						'clickLink',
            'click .wicked-add-folder': 	'addFolder',
            'click .wicked-edit-folder': 	'editFolder',
            'click .wicked-delete-folder': 	'deleteFolder',
            'click .wicked-toggle-all': 	'toggleAll',
            'click .wicked-pane-settings': 	'editSettings'
        },

        initialize: function(){

            var l10n = wickedFoldersL10n;

            this.$el.append( '<li><a class="wicked-add-folder" href="#" title="' + l10n.addNewFolderLink + '"><span class="screen-reader-text">' + l10n.addNewFolderLink + '</span></a></li>' );
            this.$el.append( '<li><a class="wicked-edit-folder" href="#" title="' + l10n.editFolderLink + '"><span class="screen-reader-text">' + l10n.editFolderLink + '</span></a></li>' );
            this.$el.append( '<li><a class="wicked-delete-folder" href="#" title="' + l10n.deleteFolderLink + '"><span class="screen-reader-text">' + l10n.deleteFolderLink + '</span></a></li>' );
            this.$el.append( '<li><a class="wicked-toggle-all wicked-expand-all" href="#" title="' + l10n.expandAllFoldersLink + '"><span class="screen-reader-text">' + l10n.expandAllFoldersLink + '</span></a></li>' );
            this.$el.append( '<li><a class="wicked-pane-settings" href="#" title="' + l10n.settings + '"><span class="screen-reader-text">' + l10n.settings + '</span></a></li>' );

            this.options.pane.model.on( 'change:selected', this.onFolderChanged, this );

            this.onFolderChanged();

        },

        clickLink: function( e ){
            e.preventDefault();
        },

        addFolder: function(){
            this.options.pane.addFolder();
        },

        editFolder: function( e ){
            if ( $( e.currentTarget ).hasClass( 'wicked-disabled' ) ) return;
            this.options.pane.editFolder();
        },

        deleteFolder: function( e ){
            if ( $( e.currentTarget ).hasClass( 'wicked-disabled' ) ) return;
            this.options.pane.deleteFolder();
        },

        editSettings: function() {
            this.options.pane.editPaneSettings();
        },

        expandAll: function(){
            this.options.pane.expandAll();
        },

        collapseAll: function(){
            this.options.pane.collapseAll();
        },

        toggleAll: function( e ){
            var $el = $( e.currentTarget ),
                l10n = wickedFoldersL10n;

            if ( $el.hasClass( 'wicked-expand-all' ) ) {
                $el.removeClass( 'wicked-expand-all' );
                $el.addClass( 'wicked-collapse-all' );
                $el.attr( 'title', l10n.collapseAllFoldersLink );
                $el.find( '.screen-reader-text' ).text( l10n.collapseAllFoldersLink );

                this.expandAll();
            } else {
                $el.addClass( 'wicked-expand-all' );
                $el.removeClass( 'wicked-collapse-all' );
                $el.attr( 'title', l10n.expandAllFoldersLink );
                $el.find( '.screen-reader-text' ).text( l10n.expandAllFoldersLink );

                this.collapseAll();
            }
        },

        onFolderChanged: function(){

            var id = this.options.pane.model.get( 'folder' ),
                folder = this.collection.get( id );

            // TODO: fix folder tree state model so a selected folder always
            // exists
            if ( _.isUndefined( folder ) ) folder = new wickedfolders.models.Folder();

            this.$( 'a' ).removeClass( 'wicked-disabled' );

            if ( 'Wicked_Folders_Term_Folder' != folder.get( 'type' ) ) {
                this.$( '.wicked-edit-folder' ).addClass( 'wicked-disabled' );
                this.$( '.wicked-delete-folder' ).addClass( 'wicked-disabled' );
            }

            if ( ! this.options.pane.model.get( 'enableCreate' ) ) {
                this.$( '.wicked-add-folder' ).addClass( 'wicked-disabled' );
            }

            if ( ! folder.get( 'editable' ) ) {
                this.$( '.wicked-edit-folder' ).addClass( 'wicked-disabled' );
            }

            if ( ! folder.get( 'deletable' ) ) {
                this.$( '.wicked-delete-folder' ).addClass( 'wicked-disabled' );
            }
        }

    });

    wickedfolders.views.FolderDetails = wickedfolders.views.View.extend({
        className: 'wicked-folder-pane-panel wicked-folder-details',
        events: {
            'keyup input':                  'keyup',
            'keydown input':                'keydown',
            'blur input':                   'setSaveButtonState',
            'click .wicked-save':           'save',
            'click .wicked-delete':         'delete',
            'click .wicked-cancel':         'cancel',
            'click .wicked-close': 	        'cancel',
            'click .wicked-clone-folder':   'cloneFolder'
        },

        initialize: function(){

            this.template = _.template( $( '#tmpl-wicked-folder-details' ).html(), wickedfolders.util.templateSettings );

            _.defaults( this.options, {
                mode: 'add'
            } );

            /*
            if ( _.isUndefined( this.model ) ) {
                this.model = new wickedfolders.models.Folder({
                    postType: 'page',
                    taxonomy: 'wf_page_folders'
                });
            }
            */

            this.folderSelect = new wickedfolders.views.FolderSelect({
                collection:     this.collection,
                selected:       this.model.get( 'parent' ),
                hideUneditable: true
            });

            this.listenTo( this.model, 'change:parent', this.folderParentChanged );

        },

        remove: function(){
            this.folderSelect.remove();
            wickedfolders.views.View.prototype.remove.apply(this, arguments);
        },

        render: function(){

            var mode = this.options.mode,
                title = wickedFoldersL10n.editFolderLink,
                saveButtonLabel = wickedFoldersL10n.save;

            if ( 'add' == mode ) {
                title = wickedFoldersL10n.addNewFolderLink;
            }

            if ( 'delete' == mode ) {
                title           = wickedFoldersL10n.deleteFolderLink;
                saveButtonLabel = wickedFoldersL10n.delete;
            }

            var html = this.template({
                mode: 						this.options.mode,
                title: 						title,
                folderName:                 this.model.get( 'name' ),
                ownerId:                    this.model.get( 'ownerId' ),
                ownerName:                  this.model.get( 'ownerName' ),
                saveButtonLabel:            saveButtonLabel,
                deleteFolderConfirmation: 	wickedFoldersL10n.deleteFolderConfirmation,
                cloneFolderLink: 	        wickedFoldersL10n.cloneFolderLink,
                cloneFolderTooltip:         wickedFoldersL10n.cloneFolderTooltip,
                cloneChildFolders:          wickedFoldersL10n.cloneChildFolders,
                cloneChildFoldersTooltip:   wickedFoldersL10n.cloneChildFoldersTooltip,
                ownerLabel:                 wickedFoldersL10n.owner
            });

            this.folderSelect.options.selected = this.model.get( 'parent' );

            this.$el.html( html );

            this.$( '.wicked-folder-parent > div' ).html( this.folderSelect.render().el );

            this.setSaveButtonState();

            this.renderFolderOwner();

            return this;
        },

        renderFolderOwner: function(){
            var page    = 1;
            var perPage = 25;
            var term    = '';

            this.$( '#wicked-folder-owner-id' ).select2({
                width: '100%',
                ajax: {
                    url: wickedFoldersSettings.restURL + 'wp/v2/users',
                    dataType: 'json',
                    cache: true,
                    data: function( params ){
                        if ( term != params.term ) {
                            term = params.term;
                            page = 1;
                        }

                        return {
                            per_page: perPage,
                            search: params.term,
                            page: page,
                            wf_include_users_without_posts: true
                        };
                    },
                    transport: function( params, success, failure ){
                        var readHeaders = function( data, textStatus, jqXHR ) {
                            var more    = false;
                            var total   = parseInt( jqXHR.getResponseHeader( 'X-WP-Total' ) ) || 0;
                            var fetched = page * perPage;

                            if ( total > fetched ) {
                                page++;
                                more = true;
                            }

                            return {
                                results: $.map( data, function( item ){
                                    return {
                                        id:     item.id,
                                        text:   item.name
                                    }
                                } ),
                                pagination: {
                                    more: more
                                }
                            };
                        };

                        var request = $.ajax( params );
                        request.then( readHeaders ).then( success );
                        request.fail( failure );
                    }
                }
            });
        },

        keyup: function( e ){
            // Escape button
            if ( 27 == e.which ) this.$el.hide();
            this.setSaveButtonState();
        },

        keydown: function( e ) {
            // Enter key
            if ( 13 == e.which && this.$( '[name="wicked_folder_name"]' ).val().length > 0 ) {
                this.save();
            }
        },

        cancel: function( e ) {
            e.preventDefault();
            this.$el.hide();
        },

        save: function(){

            var view = this,
                parent = this.model.get( 'parent' ),
                parentFolder = this.collection.get( parent ),
                originalFolder = this.model.clone();

            view.clearMessages();
            view.setBusy( true );

            if ( 'delete' == this.options.mode ) {
                //this.model.set( '_actionOverride', 'wicked_folders_delete_folder' );
                this.model.set( '_methodOverride', 'DELETE' );
                this.model.destroy( {
                    wait: true,
                    success: function( model, response, options ){
                        // Move the deleted folder's children to it's parent
                        var children = view.collection.where( { parent: model.id } );

                        if ( children.length ) {
                            _.each( children, function( child ){
                                // Keep silent to prevent unnecessary re-renders
                                // by views monitoring the collection
                                child.set( 'parent', parent, { silent: true } );
                            } );
                            // Trigger an event so that views monitoring the
                            // collection will re-render
                            view.collection.trigger( 'remove', model, {} );
                        }
                        view.setBusy( false );
                        view.$el.hide();

                        view.options.pane.model.set( 'folder', parentFolder );
                    },
                    error: function( model, response, options ){
                        view.setErrorMessage( response.responseJSON.message );
                        view.setSaveButtonState();
                        view.setBusy( false );
                    }
                } );
            } else {
                view.model.set( {
                    name:       this.$( '[name="wicked_folder_name"]' ).val(),
                    parent:     this.$( '[name="wicked_folder_parent"]' ).val(),
                    ownerId:    this.$( '[name="wicked_folder_owner_id"]' ).val(),
                    ownerName:  this.$( '[name="wicked_folder_owner_id"] option:selected' ).text()
                } );
                this.model.save( {}, {
                    success: function( model, response, options ){
                        if ( 'add' == view.options.mode ) {
                            view.collection.add( model );
                            view.model = new wickedfolders.models.Folder({
                                postType: 	model.get( 'postType' ),
                                taxonomy: 	model.get( 'taxonomy' ),
                                parent:		model.get( 'parent' )
                            });
                            view.render();
                            // TODO: l10n
                            view.flashMessage( 'Folder added.' );
                            view.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();
                        }
                        view.setSaveButtonState();
                        view.setBusy( false );
                        //if ( 'edit' == view.options.mode && view.options.controller.state().frame.options.modal ) {
                        if ( 'edit' == view.options.mode ) {
                            view.$el.hide();
                        }
                    },
                    error: function( model, response, options ){
                        view.setErrorMessage( response.responseJSON.message );
                        view.setSaveButtonState();
                        view.setBusy( false );

                        // Revert model to previous values
                        view.model.set( {
                            name:       originalFolder.get( 'name' ),
                            parent:     originalFolder.get( 'parent' ),
                            ownerId:    originalFolder.get( 'ownerId' ),
                            ownerName:  originalFolder.get( 'ownerName' )
                        } );
                    }
                } );
            }

        },

        setSaveButtonState: function(){

            var disabled = false;

            if ( 'delete' != this.options.mode ) {
                if ( this.$( '[name="wicked_folder_name"]' ).val().length < 1 ) {
                    disabled = true;
                }
            }

            this.$( '.wicked-save' ).prop( 'disabled', disabled );

        },

        setBusy: function( isBusy ){
            if ( isBusy ) {
                this.$( '.wicked-spinner' ).css( 'display', 'inline-block' );
                this.$( '[name="wicked_folder_name"]' ).prop( 'disabled', true );
                this.$( '[name="wicked_folder_parent"]' ).prop( 'disabled', true );
                this.$( '.wicked-save' ).prop( 'disabled', true );
            } else {
                this.$( '.wicked-spinner' ).hide();
                this.$( '[name="wicked_folder_name"]' ).prop( 'disabled', false );
                this.$( '[name="wicked_folder_parent"]' ).prop( 'disabled', false );
                this.setSaveButtonState();
            }
        },

        clearMessages: function(){
            this.$( '.wicked-messages' ).removeClass( 'wicked-errors wicked-success' ).empty().hide();
        },

        setErrorMessage: function( message ){
            this.$( '.wicked-messages' ).addClass( 'wicked-errors' ).text( message ).show();
        },

        flashMessage: function( message ){
            var view = this;
            this.$( '.wicked-messages' ).addClass( 'wicked-success' ).text( message ).show();
            setTimeout( function(){
                view.$( '.wicked-messages' ).fadeOut();
            }, 1000 );
        },

        folderParentChanged: function( folder ){
            // Model change event will trigger folder select view to re-render
            // so just update the view's selected option
            this.folderSelect.options.selected = this.model.get( 'parent' );
        },

        cloneFolder: function( e ){
            e.preventDefault();

            var view = this,
                options = {
                    cloneChildren: this.$( '[name="wicked_clone_children"]' ).prop( 'checked' )
                };

            view.clearMessages();
            view.setBusy( true );

            this.model.cloneFolder( options )
                .done( function( folders ){
                    _.each( folders, function( folder ){
                        view.collection.add( folder );
                    } );

                    view.setBusy( false );
                    view.flashMessage( wickedFoldersL10n.cloneFolderSuccess );
                } )
                .fail( function( error ){
                    view.setErrorMessage( error.responseText );
                    view.setBusy( false );
                });
        }

    });

    wickedfolders.views.FolderPaneSettings = wickedfolders.views.View.extend({
        className: 'wicked-folder-pane-panel wicked-folder-pane-settings',
        events: {
            'click .wicked-close': 'cancel',
            'change [name="wicked_organization_mode"]': 'changeOrganizationMode',
            'change [name="wicked_sort_mode"]':         'changeSortMode'
        },

        initialize: function(){
            this.template = _.template( $( '#tmpl-wicked-folder-pane-settings' ).html(), wickedfolders.util.templateSettings );

            this.options.pane.model.on( 'change:organizationMode change:sortMode', this.render, this );
        },

        cancel: function( e ){
            e.preventDefault();

            this.$el.hide();
        },

        changeOrganizationMode: function( e ){
            var value = $( e.currentTarget ).val();

            // Update the folder pane controller's organization mode
            this.options.pane.model.set( 'organizationMode', value );
        },

        changeSortMode: function( e ){
            var value = $( e.currentTarget ).val();

            // Update the folder pane controller's organization mode
            this.options.pane.model.set( 'sortMode', value );
        },

        render: function(){
            var html = this.template({
                mode: this.options.pane.model.get( 'organizationMode' ),
                sortMode: this.options.pane.model.get( 'sortMode' )
            });

            this.$el.html( html );

            return this;
        }
    });

    wickedfolders.views.PostDragDetails = wickedfolders.views.View.extend({
        className: 'wicked-drag-details',

        initialize: function(){
            this.template = _.template( $( '#tmpl-wicked-post-drag-details' ).html(), wickedfolders.util.templateSettings );

            _.defaults( this.options, {
                enableCopy: true
            } );
        },

        render: function(){
            this.$el.html( this.template({
                count:      this.collection.length,
                posts:      this.collection,
                enableCopy: this.options.enableCopy
            }) );

            return this;
        }
    });

    wickedfolders.views.NotificationCenter = wickedfolders.views.View.extend({
        tagName:    'div',

        className:  'wicked-folders-notifications',

        initialize: function(){
            this.collection.on( 'add remove', this.render, this );
        },

        render: function(){
            var view = this;

            this.$el.empty();

            this.collection.each( function( notification ){
                var notificationView = new wickedfolders.views.Notification({
                    model: notification
                });

                view.$el.append( notificationView.el );
            } );

            return this;
        }
    });

    wickedfolders.views.Notification = wickedfolders.views.View.extend({
        className: 'wicked-folders-notification',

        events: {
            'click .wicked-dismiss': 'dismiss'
        },

        initialize: function(){
            this.template = _.template( $( '#tmpl-wicked-folders-notification' ).html(), wickedfolders.util.templateSettings );

            this.model.on( 'change', this.render, this );

            this.render();
        },

        dismiss: function(){
            this.model.set( 'dismissed', true );
        },

        render: function(){
            var notification = this.model;

            if ( notification.get( 'dismissed' ) ) {
                this.$el.addClass( 'dismissed' );
            }

            this.$el.html( this.template({
                title:          notification.get( 'title' ),
                message:        notification.get( 'message' ),
                dismissible:    notification.get( 'dismissible' ),
                dismissed:      notification.get( 'dismissed' ),
            }) );

            return this;
        }
    });

    wickedfolders.views.ObjectFolderPane = wickedfolders.views.View.extend({
        folderTreeView: false,
        toolbar: false,

        events: {
            'click .wicked-toggle':                 'toggleBranch',
            'keyup [name="wicked_folder_search"]':  'search'
        },

        initialize: function() {
            var view = this;

            this.toolbar = new wickedfolders.views.ObjectFolderPaneToolbar({
                pane:       this,
                collection: this.folders()
            });

            this.createFolderDetails();
            this.createBreadcrumbs();
            this.createNotificationCenter();

            this.$( '.wicked-toolbar-container' ).append( this.toolbar.render().el );

            // Initialize folder tree view
            var folderTree = new wickedfolders.views.FolderTree({
                showItemCount:  this.model.get( 'showItemCount' ),
                collection:     this.model.get( 'folders' ),
                model:          new wickedfolders.models.FolderTreeState({
                    selected: this.model.get( 'folder' ).id,
                    expanded: _.uniq( this.model.get( 'expanded' ) )
                })
            });

            this.folderTreeView = folderTree;

            // Un-bind folder tree model events that will cause the tree to
            // re-render as we want to control the rendering here
            this.folderTreeView.model.off( 'change:expanded' );
            this.folderTreeView.model.off( 'change:checked' );

            this.folderTreeView.model.on( 'change:expanded', this.renderFolderTree, this );
            this.folderTreeView.model.on( 'change:selected', function( folderTreeState ){
                var folderId = folderTreeState.get( 'selected' );

                view.model.set( 'folder', view.folders().get( folderId ) );
            }, this );

            this.model.on( 'change:loading', this.loadingChanged, this );
            this.model.on( 'change:folder', this.folderChanged, this );
            this.model.on( 'change:organizationMode', this.renderFolderTree, this );
            this.model.on( 'change:sortMode', function(){
                this.folders().sortMode = this.model.get( 'sortMode' );
                this.folders().sort();
                this.renderFolderTree();
            }, this );
            this.folders().on( 'change:parent', this.folderParentChanged, this );
            this.folders().on( 'add remove', this.renderFolderTree, this );
            this.folderTreeView.model.on( 'change:expanded', this.changeExpanded, this );

            this.makePostsDraggable();
            this.render();
            this.updateSelectAllDraggable();
            this.folderTreeView.expandToSelected();
            this.bindInlineEdit();

            this.updateSearch = _.debounce( function( search ){
                // Set silently to avoid rendering twice
                view.folderTreeView.model.set( 'search', search, { silent: true } );

                view.renderFolderTree();
            }, 750 );

            $( '.wp-list-table .check-column [type="checkbox"]' ).change( function(){
                view.updateSelectAllDraggable();
            } );

            // Make posts draggable again after quick edit
            $( document ).ajaxSuccess( function( event, xhr, settings, data ) {
                if ( settings.hasOwnProperty( 'data' ) ) {
                    if ( settings.data.indexOf( 'action=inline-save' ) != -1 ) {
                        view.makePostsDraggable();
                    }
                }
            } );
        },

        createNotificationCenter: function() {
            this.notifications = new wickedfolders.collections.Notifications();

            var notificationCenter = new wickedfolders.views.NotificationCenter( {
                collection: this.notifications
            } );

            this.$el.append( notificationCenter.render().el );
        },

        createBreadcrumbs: function() {
            if ( ! wickedFoldersSettings.showBreadcrumbs ) return;

            var view = this;

            $( '#wpbody-content .wp-list-table' ).before( '<div class="wicked-folders-breadcrumbs"><span class="wicked-folders-label">' + wickedFoldersL10n.folder + ':</span> <span class="wicked-folders-container"></span></div>');

            var folderPath = new wickedfolders.views.FolderPath({
                collection: this.folders(),
                selected:   this.folder().id
            });

            folderPath.render();

            $( '#wpbody-content .wicked-folders-breadcrumbs > .wicked-folders-container' ).html( folderPath.el );
            $( '#wpbody-content .wicked-folders-breadcrumbs' ).on( 'click', 'a', function( e ){
                var folderId = $( this ).parent().attr( 'data-folder-id' ),
                    folder = view.folders().get( folderId );

                view.model.set( 'folder', folder );

                return false;
            });
        },

        createFolderDetails: function( args ){

            var id = this.folder(),
                folder = this.folders().get( id )
                args = args || {},
                visible = false;

            _.defaults( args, {
                pane:       this,
                collection:	this.folders(),
                model:		folder
            } );

            if ( ! _.isUndefined( this.folderDetails ) ) {
                visible = this.folderDetails.$el.is( ':visible' );
                this.folderDetails.remove();
            }

            this.folderDetails = new wickedfolders.views.FolderDetails( args );

            this.$( '.wicked-folder-details-container' ).append( this.folderDetails.render().el );

            if ( ! visible ) this.folderDetails.$el.hide();

        },

        render: function() {

            this.renderFolderTree();
            this.makeResizable();

        },

        setWidth: function( width ) {
            this.$( '.wicked-content' ).css( 'width', width - 12 + 'px' );
            this.$( '.wicked-resizer' ).css( 'width', width + 'px' );

            if ( wickedfolders.util.isRtl() ) {
                $( '#wpcontent' ).css( 'paddingRight', width + 11 + 'px' );
                $( '#wpfooter' ).css( 'right', width - 6 + 'px' );
            } else {
                $( '#wpcontent' ).css( 'paddingLeft', width + 11 + 'px' );
                $( '#wpfooter' ).css( 'left', width - 6 + 'px' );
            }
        },

        makeResizable: function() {
            var view = this;

            this.$( '.wicked-resizer' ).resizable( {
                resizeHeight:   false,
                handles:        'e',
                minWidth:       150,
                containment:    $( '#wpcontent' ),
                resize:         function( e, ui ) {
                    view.setWidth( ui.size.width );
                },
                stop:           function( e, ui ) {
                    view.model.set( 'treePaneWidth', ui.size.width );
                }
            } );

            if ( wickedfolders.util.isRtl() ) {
                // Risizing to left is not working...disable for now
                //this.$( '.wicked-resizer' ).resizable( 'option', 'handles', 'w' );
                this.$( '.wicked-resizer' ).resizable( 'disable' );
            }
        },

        makePostsDraggable: function() {
            var view = this,
                isMedia = $( '.wp-list-table' ).hasClass( 'media' ),
                isWooCommerceOrders = $( 'body' ).hasClass( 'post-type-shop_order' ),
                folder = this.folder();

            $( 'body.post-type-shop_order .wp-list-table .column-wicked_move' ).click( function( e ){
                return false;
            });

            $( '.wp-list-table .wicked-move-multiple' ).draggable( {
                revert:         'invalid',
                containment:    '#wpwrap',
                //appendTo:       '#wicked-folder-browser'
                helper: function( e ){
                    var posts = new Backbone.Collection(),
                        row = $( e.currentTarget ).parents( 'tr' ),
                        checkbox = row.find( '.check-column [type="checkbox"]' );

                    // Move all checked items if the item being draggeed is
                    // checked (or if the drag icon in the table header is being
                    // used; otherwise, only move the one item
                    if ( checkbox.prop( 'checked' ) || 'on' == checkbox.val() ) {
                        posts = view.getSelectedPosts();
                    } else {
                        if ( isMedia ) {
                            var title = $( row ).find( '.title a' ).eq( 0 ).text().trim();
                        } else if ( isWooCommerceOrders ) {
                            var title = $( row ).find( '.order-view' ).text().trim();
                        } else {
                            var title = $( row ).find( '.wicked-item' ).text().trim();
                        }
                        posts.add( new wickedfolders.models.Post({
                            //id:     checkbox.val(),
                            id:     $( row ).find( '.wicked-item' ).attr( 'data-object-id' ),
                            title:  title
                        }) );
                    }

                    var dragger = new wickedfolders.views.PostDragDetails({
                        collection: posts,
                        enableCopy: 'unassigned_dynamic_folder' != folder.id
                    });

                    view.model.set( 'postsToMove', posts, { silent: true } );

                    return dragger.render().el;
                },
                start: function( e, ui ){
                    view.$( '.wicked-tree' ).addClass( 'highlight-assignable' );
                },
                stop: function( e, ui ){
                    view.$( '.wicked-tree' ).removeClass( 'highlight-assignable' );
                }
            } );

            if ( wickedfolders.util.isRtl() ) {
                $( '.wp-list-table .wicked-move-multiple' ).draggable( 'option', {
                    cursorAt: {
                        top: 10,
                        right: 10
                    }
                } );
            }

        },

        /**
         * Returns the folder object (not ID) of the folder currently being viewed.
         */
        folder: function(){
            return this.model.get( 'folder' );
        },

        /**
         * Returns the view's folders collection.
         */
        folders: function(){
            return this.model.get( 'folders' );
        },

        renderFolderTree: function() {
            var view = this,
                mode = this.model.get( 'organizationMode' );

            // Use setElement so that events are re-bound
            this.folderTreeView.setElement( this.$( '.wicked-folder-tree' ) ).render();

            if ( 'organize' == mode ) {
                this.$( '.wicked-folder-leaf.wicked-movable' ).not( '[data-folder-id="0"]' ).draggable( {
                    revert: 'invalid',
                    helper: 'clone',
                    start: function( e, ui ){
                        var folderId = $( this ).attr( 'data-folder-id' ),
                            folder = view.folders().get( folderId ),
                            parentId = folder.get( 'parent' );

                        view.$( '.wicked-tree' ).addClass( 'highlight-editable' );

                        if ( 0 != parentId ) {
                            view.$( '[data-folder-id="0"]' ).addClass( 'editable' );
                        }
                    },
                    stop: function( e, ui ){
                        view.$( '.wicked-tree' ).removeClass( 'highlight-editable' );
                        view.$( '[data-folder-id="0"]' ).removeClass( 'editable' );
                    }
                } );

                if ( wickedfolders.util.isRtl() ) {
                    this.$( '.wicked-folder-leaf.wicked-movable' ).not( '[data-folder-id="0"]' ).draggable( 'option', 'cursorAt', {
                        top: 0,
                        right: 40
                    } );
                }
            }

            this.$( '.wicked-tree [data-folder-id="0"] .wicked-folder, .wicked-tree [data-folder-id="unassigned_dynamic_folder"] .wicked-folder, [data-folder-id^="dynamic_root"] > ul > [data-folder-id^="dynamic_term"] .wicked-folder' ).droppable( {
                hoverClass: 'wicked-drop-hover',
                accept: function( draggable ){

                    var destinationFolderId = $( this ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' ),
                        destinationFolder = view.folders().get( destinationFolderId ),
                        draggedFolderId = draggable.attr( 'data-folder-id' ),
                        draggedFolder = view.folders().get( draggedFolderId ),
                        folder = view.folder(),
                        accept = false;

                    if ( draggable.hasClass( 'wicked-folder' ) || draggable.hasClass( 'wicked-folder-leaf' ) || draggable.hasClass( 'wicked-post-leaf' ) || draggable.hasClass( 'wicked-move-multiple' ) ) {
                        accept = true;
                    }

                    if ( draggable.hasClass( 'wicked-folder-leaf' ) ) {
                        var parent = draggable.parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );
                        // Don't allow folders to be moved to the folder they're already in
                        if ( destinationFolderId == parent ) {
                            accept = false;
                        }
                        // Don't allow folders to be dragged to the 'Unassigned' dynamic folder
                        if ( destinationFolderId == 'unassigned_dynamic_folder' ) {
                            accept = false;
                        }

                        // Don't allow folders to be dropped when not in organize mode
                        if ( 'organize' != mode ) {
                            accept = false;
                        }

                        // Don't allow dropping if either folder isn't editable
                        if ( 0 != destinationFolderId ) {
                            if ( ! destinationFolder.get( 'editable' ) || ! draggedFolder.get( 'editable' ) ) {
                                accept = false;
                            }
                        }
                    }

                    if ( draggable.hasClass( 'wicked-move-multiple' ) ) {
                        // Don't allow posts to be moved to the folder they're already in
                        if ( destinationFolderId == folder.id ) {
                            accept = false;
                        }

                        // Don't allow items to be moved to 'All Folders'
                        if ( destinationFolderId == 0 ) {
                            accept = false;
                        }

                        // Make sure target folder allows assignment
                        if ( ! destinationFolder.get( 'assignable' ) ) {
                            accept = false;
                        }
                    }

                    return accept;

                },
                tolerance: 'pointer',
                drop: function( e, ui ) {

                    // TODO: clean this up

                    var destinationFolderId = $( this ).parents( 'li' ).eq( 0 ).attr( 'data-folder-id' );

                    if ( ui.draggable.hasClass( 'wicked-move-multiple' ) ) {

                        var posts = view.model.get( 'postsToMove' ),
                            objectIds = posts.pluck( 'id' ),
                            controller = new wickedfolders.models.FolderBrowserController(),
                            taxonomy = view.folder().get('taxonomy' ),
                            folder = view.folder(),
                            destinationFolder = view.folders().get( destinationFolderId ),
                            sourceFolderId = view.folder().id,
                            assignable = folder.get( 'assignable' ),
                            copy = e.shiftKey || ! assignable,
                            message = ( copy ? 'Copied' : 'Moved' ) + ' ' + ( 1 == objectIds.length ? 'item' : objectIds.length + ' items' ) + ' to folder.';


                        if ( 'Wicked_Folders_Term_Dynamic_Folder' == destinationFolder.get( 'type' ) ) {
                            destinationFolderId = destinationFolder.get( 'termId' );
                        }

                        if ( 'Wicked_Folders_Term_Dynamic_Folder' == folder.get( 'type' ) ) {
                            sourceFolderId = folder.get( 'termId' );
                        }

                        controller.set( 'folders', view.folders(), { silent: true } );
                        controller.set( 'postType', view.model.get('postType'), { silent: true } );
                        controller.set( 'taxonomy', taxonomy, { silent: true } );

                        if ( destinationFolderId == 0 || destinationFolderId == 'unassigned_dynamic_folder' ) {
                            if ( ! confirm( wickedFoldersL10n.confirmUnassign ) ) {
                                return false;
                            }

                            controller.unassignFolders( objectIds );

                            if ( folder.get( 'type' ) == 'Wicked_Folders_Term_Folder' || folder.get( 'type' ) == 'Wicked_Folders_Term_Dynamic_Folder' ) {
                                // Only animate removing posts when in a term folder
                                view.removePostRows( objectIds );
                            } else {
                                _.each( objectIds, function( id ) {
                                    // ...otherwise, try to update the folders column
                                    $( '.wp-list-table [id="post-' + id + '"] td.taxonomy-' + taxonomy ).html( '<span aria-hidden="true">—</span><span class="screen-reader-text">No categories</span>' );
                                } );
                            }

                            view.notifications.add( new wickedfolders.models.Notification({
                                title:      'Success',
                                message:    'Unassigned ' + ( 1 == objectIds.length ? 'item' : 'items' ) + ' from folders.'
                            }) );
                        } else {
                            controller.moveObject( 'post', objectIds, destinationFolderId, sourceFolderId, copy );

                            if ( '0' == folder.get( 'id' ) && ! e.shiftKey ) {
                                view.removePostRows( objectIds );
                            } else if ( 'unassigned_dynamic_folder' == folder.get( 'id' ) ) {
                                view.removePostRows( objectIds );
                            } else if ( ! copy ) {
                                view.removePostRows( objectIds );
                            }

                            view.notifications.add( new wickedfolders.models.Notification({
                                title:      'Success',
                                message:    message
                            }) );
                        }

                        view.model.get( 'postsToMove' ).reset( null, { silent: true } );

                    } else {

                        var folderId = $( ui.draggable ).attr( 'data-folder-id' );
                        view.folders().get( folderId ).set( 'parent', destinationFolderId );

                    }

                }
            });

            if ( 'sort' == mode ) {
                this.$( '.wicked-tree [data-folder-id="0"] ul' ).sortable({
                    items: '> li',
                    stop: function( e, ui ){
                        var items = ui.item.parent().find( '> li' )
                            folders = view.folders(),
                            changedFolders = new wickedfolders.collections.Folders();

                            var controller = new wickedfolders.models.FolderBrowserController();

                        items.each( function( index, item ){
                            var folderId = $( item ).attr( 'data-folder-id' ),
                                folder = folders.get( folderId );

                            folder.set( 'order', index );

                            changedFolders.add( folder );
                        });

                        view.model.set( 'sortMode', 'custom' );

                        changedFolders.saveOrder();

                        view.folders().sort();
                    }
                });
            }
        },

        toggleBranch: function( e ) {
            var id = $( e.currentTarget ).parent().attr( 'data-folder-id' ),
                expanded = ! $( e.currentTarget ).parent().hasClass( 'wicked-expanded' );

            // Don't load child folders if the folder is being collapsed
            if ( expanded ) return;

            this.loadLazyFolder( id );
        },

        loadLazyFolder: function( folderId ) {
            var folders = this.folders(),
                folder = folders.get( folderId ),
                view = this;

            // Load folder's sub folders
            if ( true == folder.get( 'lazy' ) ) {

                folder.set( 'loading', true );

                $.ajax(
                    ajaxurl,
                    {
                        data: {
                            'action':       'wicked_folders_get_child_folders',
                            'folder_id':    folder.get( 'id' ),
                            'folder_type':  folder.get( 'type' ),
                            'post_type':    folder.get( 'postType' ),
                            'taxonomy':     folder.get( 'taxonomy' )
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function( folderData ) {
                            // Add child folders to collection
                            _.each( folderData, function( folder ){
                                folders.add( new wickedfolders.models.Folder({
                                    id:             folder.id,
                                    parent:         folder.parent,
                                    name:           folder.name,
                                    postType:       folder.postType,
                                    taxonomy:       folder.taxonomy,
                                    type:           folder.type,
                                    lazy:           folder.lazy,
                                    order:          folder.order,
                                    showItemCount:  folder.showItemCount,
                                    itemCount:      folder.itemCount
                                }) );
                            });

                            folder.set( 'loading', false );
                            folder.set( 'lazy', false );

                            view.renderFolderTree();
                        },
                        error: function( data ) {
                            // TODO: nicer error message
                            alert( wickedFoldersL10n.errorFetchingChildFolders );
                            folder.set( 'loading', false );
                        }
                    }
                );
            }
        },

        folderChanged: function() {
            var $ = jQuery,
                view = this,
                folder = this.folder(),
                id = folder.id,
                type = folder.get( 'type' ),
                param = 'wicked_' + this.model.get( 'postType' ) + '_folder_filter',
                url = document.location.href;

            // TODO: Controller is using both 'selected' and 'folder' to determine the current folder
            // The toolbar relies on the 'selected' property so we need to set
            // this also
            this.model.set( 'selected', id );

            // Tell the folder tree controller that we've changed folders
            this.folderTreeView.model.set( 'selected', id );

            // Strip fragment from URL
            if ( url.indexOf( '#' ) != -1 ) {
                url = url.substring( 0, url.indexOf( '#' ) );
            }

            url = wickedfolders.util.updateUrlParam( url, param, id );
            url = wickedfolders.util.updateUrlParam( url, 'paged', 1 );
            url = wickedfolders.util.updateUrlParam( url, 'folder_type', type );

            // Remove WordPress filter params
            url = wickedfolders.util.updateUrlParam( url, 'taxonomy', false );
            url = wickedfolders.util.updateUrlParam( url, 'term', false );
            url = wickedfolders.util.updateUrlParam( url, 'wf_' + this.model.get( 'postType' ) + '_folders', false );

            if ( wickedFoldersSettings.enableAjaxNav ) {
                view.model.set( 'loading', true );

                $.ajax( url, {
                    success: function( data ){
                        var scriptCount = wickedFoldersSettings.afterAjaxScripts.length,
                            scriptsLoaded = 0;

                        $( '#wpbody-content' ).html( $( data ).find( '#wpbody-content').html() );

                        view.makePostsDraggable();

                        view.createBreadcrumbs();

                        // Re-initalize various WordPress functionality
                        if ( window.inlineEditPost && 0 == scriptCount ) window.inlineEditPost.init();
                        if ( window.screenMeta ) window.screenMeta.init();
                        if ( window.columns ) window.columns.init();

                        view.initWordPressHelpPanelTabs();
                        view.initWordPressAdminNotices();
                        view.initGravityFormsScripts();

                        // Load any necessary scripts to re-activate JavaScript
                        // functionality tied to document load/ready
                        _.each( wickedFoldersSettings.afterAjaxScripts, function( script ) {
                            $.getScript( script, function(){
                                scriptsLoaded++;

                                if ( scriptsLoaded == scriptCount ) {
                                    if ( window.inlineEditPost ) window.inlineEditPost.init();
                                }
                            } );
                        } );

                        // Can be a little confusing if we don't scroll back to the top
                        $( window ).scrollTop( 0 );

                        view.model.set( 'loading', false );

                        $( 'body' ).trigger( 'wickedfolders:ajaxNavigationDone' );
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        $( window ).scrollTop( 0 );

                        view.$( '.wicked-folder-navigation-error .wicked-error-text' ).text( errorThrown );

                        $( '#wpbody' ).html( '' ).html( view.$( '.wicked-folder-navigation-error' ).html() );

                        if ( window.console ) {
                            console.warn( wickedFoldersL10n.navigationFailure );
                            console.log( jqXHR );
                            console.log( textStatus );
                            console.log( errorThrown );
                        }

                        view.model.set( 'loading', false );

                        $( 'body' ).trigger( 'wickedfolders:ajaxNavigationDone' );
                    }
                } );

                view.loadLazyFolder( id );
            } else {
                document.location = url;
            }

            this.updateSelectAllDraggable();

            return false;
        },

        folderParentChanged: function( folder ){
            folder.save();
            this.renderFolderTree();
        },

        changeExpanded: function() {
            // Keep the controller's expanded state in sync with the folder
            // tree's state
            this.model.set( 'expanded', this.folderTreeView.model.get( 'expanded' ) );
        },

        addFolder: function(){
            // TODO: disable add folder button instead while in add mode
            // Don't do anything if the folder details view is already in
            // add mode and visible
            if ( 'add' == this.folderDetails.options.mode && this.folderDetails.$el.is( ':visible' ) ) return;

            var id = this.model.get( 'folder' ),
                folder = this.folders().get( id ),
                parent = '0';

            if ( 'Wicked_Folders_Term_Folder' == folder.get( 'type' ) ) {
                parent = folder.id;
            }

            this.createFolderDetails({
                mode: 	'add',
                model: 	new wickedfolders.models.Folder({
                    postType:       this.model.get( 'postType' ),
                    taxonomy:       this.model.get( 'taxonomy' ),
                    showItemCount:  this.model.get( 'showItemCount' ),
                    parent:         parent
                })
            });

            this.folderDetails.$el.show();
            this.folderDetails.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();

            if ( ! _.isUndefined( this.folderPaneSettings ) ) {
                this.folderPaneSettings.$el.hide();
            }
        },

        editFolder: function(){

            this.createFolderDetails({
                mode: 'edit'
            });

            this.folderDetails.$el.show();
            this.folderDetails.$( '[name="wicked_folder_name"]' ).get( 0 ).focus();

            if ( ! _.isUndefined( this.folderPaneSettings ) ) {
                this.folderPaneSettings.$el.hide();
            }
        },

        deleteFolder: function(){
            this.createFolderDetails({
                mode: 'delete'
            });
            this.folderDetails.$el.show();

            if ( ! _.isUndefined( this.folderPaneSettings ) ) {
                this.folderPaneSettings.$el.hide();
            }
        },

        editPaneSettings: function(){
            var visible = false;

            if ( ! _.isUndefined( this.folderPaneSettings ) ) {
                visible = this.folderPaneSettings.$el.is( ':visible' );
                this.folderPaneSettings.remove();
            }

            this.folderPaneSettings = new wickedfolders.views.FolderPaneSettings({
                pane: this
            });

            this.$( '.wicked-folder-pane-settings-container' ).append( this.folderPaneSettings.render().el );

            //if ( ! visible ) this.folderPaneSettings.$el.hide();
            if ( ! _.isUndefined( this.folderDetails ) ) {
                this.folderDetails.$el.hide();
            }
        },

        expandAll: function(){
            var ids = this.folders().pluck( 'id' );
            this.folderTreeView.model.set( 'expanded', ids );
        },

        collapseAll: function(){
            this.folderTreeView.model.set( 'expanded', [ '0' ] );
        },

        getSelectedPosts: function(){
            var posts = new Backbone.Collection(),
                isMedia = $( '.wp-list-table' ).hasClass( 'media' ),
                isWooCommerceOrders = $( 'body' ).hasClass( 'post-type-shop_order' );

            $( '.wp-list-table tbody [type="checkbox"]' ).each( function( index, checkbox ){
                var checkbox = $( checkbox ),
                    //id = checkbox.val(),
                    row = checkbox.parents( 'tr' ),
                    id = row.find( '.wicked-item' ).attr( 'data-object-id' );

                // Get the post's title
                if ( isMedia ) {
                    var title = $( row ).find( '.title a' ).eq( 0 ).text().trim();
                } else if ( isWooCommerceOrders ) {
                    var title = $( row ).find( '.order-view' ).text().trim();
                } else {
                    var title = $( row ).find( '.wicked-item' ).text().trim();
                }

                if ( checkbox.prop( 'checked' ) ) {
                    posts.add( new wickedfolders.models.Post({
                        id:     id,
                        title:  title
                    }));
                }
            } );

            return posts;

        },

        removePostRows: function( postId ){
            var view = this,
                ids = _.isArray( postId ) ? postId : [ postId ];

            _.each( ids, function( id ){
                // Posts
                $( '.wp-list-table tr[id="post-' + id + '"]' ).fadeOut( 500, function(){
                    $( this ).remove();
                });

                // Users
                $( '.wp-list-table tr[id="user-' + id + '"]' ).fadeOut( 500, function(){
                    $( this ).remove();
                });

                // Plugins
                var pluginSlug = $( '.wp-list-table.plugins .wicked-item[data-object-id="' + id + '"]' ).closest( 'tr' ).attr( 'data-slug' );

                $( '.wp-list-table.plugins tr[data-slug="' + pluginSlug + '"]' ).fadeOut( 500, function(){
                    $( this ).remove();
                });

                // Gravity Forms forms
                $( '.wp-list-table.toplevel_page_gf_edit_forms .wicked-item[data-object-id="' + id + '"]' ).closest( 'tr' ).fadeOut( 500, function(){
                    $( this ).remove();
                });

                // Gravity Forms entries
                $( '.wp-list-table.gf_entries tr[data-id="' + id + '"]' ).fadeOut( 500, function(){
                    $( this ).remove();
                });

                // TablePress tables
                $( '.wp-list-table.tablepress-all-tables tr[data-wf-post-id="' + id + '"]' ).fadeOut( 500, function(){
                    $( this ).remove();
                });
            } );
        },

        search: function( e ){
            search = $( e.currentTarget ).val().trim();

            this.updateSearch( search );
        },

        updateSelectAllDraggable: function(){
            var posts = this.getSelectedPosts(),
                disableMoveMultiple = posts.length < 1

            // We'll get an error if we try to set a draggable option and
            // draggable hasn't been initalized
            if ( $( '.wp-list-table th .wicked-move-multiple' ).hasClass( 'ui-draggable' ) ) {
                $( '.wp-list-table th .wicked-move-multiple' ).draggable( 'option', 'disabled', disableMoveMultiple );
            }
        },

        loadingChanged: function() {
            if ( this.model.get( 'loading' ) ) {
                this.$( '.wicked-navigating-mask' ).show();
            } else {
                this.$( '.wicked-navigating-mask' ).hide();
            }
        },

        bindInlineEdit: function() {
            var folders = this.folders(),
                taxonomy = this.model.get( 'taxonomy' ),
                selector = '.inline-edit-row .' + taxonomy + '-checklist';

            $( '#wpbody' ).on( 'click', 'button.editinline', function(){
                // Disable any folders that aren't assignable
                folders.each( function( folder ){
                    if ( ! folder.get( 'assignable' ) ) {
                        $( selector ).find( 'input[value="' + folder.id + '"]').prop( 'disabled', true );
                    }
                } );
            } );
        },

        /**
         * @see /wp-admin/js/common.js
         */
        initWordPressHelpPanelTabs: function() {
            var $ = jQuery;

            $('.contextual-help-tabs').delegate('a', 'click', function(e) {
                var link = $(this),
                    panel;

                e.preventDefault();

                // Don't do anything if the click is for the tab already showing.
                if ( link.is('.active a') )
                    return false;

                // Links
                $('.contextual-help-tabs .active').removeClass('active');
                link.parent('li').addClass('active');

                panel = $( link.attr('href') );

                // Panels
                $('.help-tab-content').not( panel ).removeClass('active').hide();
                panel.addClass('active').show();
            });
        },

        /**
         * @see /wp-admin/js/common.js
         */
        initWordPressAdminNotices: function() {
            var $ = jQuery,
                $headerEnd = $( '.wp-header-end' );

            if ( ! $headerEnd.length ) {
                $headerEnd = $( '.wrap h1, .wrap h2' ).first();
            }
            $( 'div.updated, div.error, div.notice' ).not( '.inline, .below-h2' ).insertAfter( $headerEnd );
        },

        initGravityFormsScripts: function() {
            // Tell Gravity Forms page has been loaded so that form
            // toggle still works after AJAX nav
            window.gfPageLoaded = true;

            // Copied from gravityforms/form_edit.php
            $('.gf_form_action_has_submenu').hover(function(){
                var l = jQuery(this).offset().left;
                jQuery(this).find('.gf_submenu')
                    .toggle()
                    .offset({ left: l });
            }, function(){
                jQuery(this).find('.gf_submenu').hide();
            });
        }

    });

})( window, jQuery, undefined );
