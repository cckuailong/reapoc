;( function( elementor, $, window ) {

	var TemplateLibraryTemplateModel = Backbone.Model.extend({
		defaults: {
			template_id: 0,
			title: '',
			source: '',
			type: '',
			subtype: '',
			author: '',
			thumbnail: '',
			url: '',
			export_link: '',
			tags: []
		}
	});

	var TemplateLibraryCollection = Backbone.Collection.extend({
		model: TemplateLibraryTemplateModel
	});

	var TemplateLibraryInsertTemplateBehavior = Marionette.Behavior.extend({
		ui: {
			insertButton: '.elementor-template-library-template-insert'
		},
		events: {
			'click @ui.insertButton': 'onInsertButtonClick'
		},
		onInsertButtonClick: function onInsertButtonClick() {
			var args = {
				model: this.view.model
			};

			$e.run('powerpack/insert-template', args);
		}
	});

	var TemplateLibraryTemplateView = Marionette.ItemView.extend({
		className: function className() {
			var classes = 'elementor-template-library-template',
				source = this.model.get('source');
			classes += ' elementor-template-library-template-remote';
			classes += ' elementor-template-library-template-' + source;

			if ('powerpack' === source) {
				classes += ' elementor-template-library-template-' + this.model.get('type');
			}

			if (this.model.get('isPro')) {
				classes += ' elementor-template-library-pro-template';
			}

			return classes;
		},
		ui: function ui() {
			return {
				previewButton: '.elementor-template-library-template-preview'
			};
		},
		events: function events() {
			return {
				'click @ui.previewButton': 'onPreviewButtonClick'
			};
		},
		behaviors: {
			insertTemplate: {
				behaviorClass: TemplateLibraryInsertTemplateBehavior
			}
		}
	});

	var TemplateLibraryTemplatePowerPackView = TemplateLibraryTemplateView.extend({
		template: '#tmpl-elementor-template-library-template-pp',
		ui: function ui() {
			return jQuery.extend(TemplateLibraryTemplateView.prototype.ui.apply(this, arguments), {
				favoriteCheckbox: '.elementor-template-library-template-favorite-input'
			});
		},
		events: function events() {
			return jQuery.extend(TemplateLibraryTemplateView.prototype.events.apply(this, arguments), {
				'change @ui.favoriteCheckbox': 'onFavoriteCheckboxChange'
			});
		},
		onPreviewButtonClick: function onPreviewButtonClick() {
			$e.route('powerpack/preview', {
				model: this.model
			});
		},
		onFavoriteCheckboxChange: function onFavoriteCheckboxChange() {
			var isFavorite = this.ui.favoriteCheckbox[0].checked;
			this.model.set('favorite', isFavorite);
			pp_templates_lib.templates.markAsFavorite(this.model, isFavorite);

			if (!isFavorite && pp_templates_lib.templates.getFilter('favorite')) {
				elementor.channels.templates.trigger('filter:change');
			}
		}
	});

	var TemplateLibraryPreviewView = Marionette.ItemView.extend( {
		template: '#tmpl-elementor-template-library-preview',
	
		id: 'elementor-template-library-preview',
	
		ui: {
			iframe: '> iframe',
		},
	
		onRender: function() {
			this.ui.iframe.attr( 'src', this.getOption( 'url' ) );
		},
	} );

	var TemplateLibraryHeaderPreviewView = Marionette.ItemView.extend({
		template: '#tmpl-elementor-template-library-header-preview-pp',
		id: 'elementor-template-library-header-preview',
		behaviors: {
			insertTemplate: {
				behaviorClass: TemplateLibraryInsertTemplateBehavior
			}
		}
	});

	var TemplateLibraryHeaderBackView = Marionette.ItemView.extend( {
		template: '#tmpl-elementor-template-library-header-back',
	
		id: 'elementor-template-library-header-preview-back',
	
		events: {
			click: 'onClick',
		},
	
		onClick: function() {
			$e.routes.restoreState( 'powerpack' );
		},
	} );

	var TemplateLibraryCollectionView = Marionette.CompositeView.extend({
		template: '#tmpl-elementor-template-library-templates-pp',
		id: 'elementor-template-library-templates',
		childViewContainer: '#elementor-template-library-templates-container',
		reorderOnSort: true,
		ui: {
			textFilter: '#elementor-template-library-filter-text',
			selectFilter: '.elementor-template-library-filter-select',
			myFavoritesFilter: '#elementor-template-library-filter-my-favorites',
			orderInputs: '.elementor-template-library-order-input',
			orderLabels: 'label.elementor-template-library-order-label'
		},
		events: {
			'input @ui.textFilter': 'onTextFilterInput',
			'change @ui.selectFilter': 'onSelectFilterChange',
			'change @ui.myFavoritesFilter': 'onMyFavoritesFilterChange',
			'mousedown @ui.orderLabels': 'onOrderLabelsClick'
		},
		comparators: {
			title: function title(model) {
				return model.get('title').toLowerCase();
			},
		},
		getChildView: function getChildView(childModel) {
			return TemplateLibraryTemplatePowerPackView;
		},
		initialize: function initialize() {
			this.listenTo(elementor.channels.templates, 'filter:change', this._renderChildren);
		},
		filter: function filter(childModel) {
			var filterTerms = pp_templates_lib.templates.getFilterTerms(),
				passingFilter = true;
			jQuery.each(filterTerms, function (filterTermName) {
				var filterValue = pp_templates_lib.templates.getFilter(filterTermName);

				if (!filterValue) {
					return;
				}

				if (this.callback) {
					var callbackResult = this.callback.call(childModel, filterValue);

					if (!callbackResult) {
						passingFilter = false;
					}

					return callbackResult;
				}

				var filterResult = filterValue === childModel.get(filterTermName);

				if (!filterResult) {
					passingFilter = false;
				}

				return filterResult;
			});
			return passingFilter;
		},
		order: function order(by, reverseOrder) {
			var comparator = this.comparators[by] || by;

			if (reverseOrder) {
				comparator = this.reverseOrder(comparator);
			}

			this.collection.comparator = comparator;
			this.collection.sort();
		},
		reverseOrder: function reverseOrder(comparator) {
			if ('function' !== typeof comparator) {
				var comparatorValue = comparator;

				comparator = function comparator(model) {
					return model.get(comparatorValue);
				};
			}

			return function (left, right) {
				var l = comparator(left),
					r = comparator(right);

				if (undefined === l) {
					return -1;
				}

				if (undefined === r) {
					return 1;
				}

				if (l < r) {
					return 1;
				}

				if (l > r) {
					return -1;
				}

				return 0;
			};
		},
		addSourceData: function addSourceData() {
			var isEmpty = this.children.isEmpty();
			this.$el.attr('data-template-source', isEmpty ? 'empty' : pp_templates_lib.templates.getFilter('source'));
		},
		setFiltersUI: function setFiltersUI() {
			var $filters = this.$(this.ui.selectFilter);
			$filters.select2({
				placeholder: elementor.translate('category'),
				allowClear: true,
				width: 150,
				dropdownParent: this.$el
			});
		},
		setMasonrySkin: function setMasonrySkin() {
			var masonry = new elementorModules.utils.Masonry({
				container: this.$childViewContainer,
				items: this.$childViewContainer.children()
			});
			this.$childViewContainer.imagesLoaded(masonry.run.bind(masonry));
		},
		toggleFilterClass: function toggleFilterClass() {
			this.$el.toggleClass('elementor-templates-filter-active', !!(pp_templates_lib.templates.getFilter('text') || pp_templates_lib.templates.getFilter('favorite')));
		},
		onRender: function onRender() {
			if ('powerpack' === pp_templates_lib.templates.getFilter('source')) {
				this.setFiltersUI();
			}
		},
		onRenderCollection: function onRenderCollection() {
			this.addSourceData();
			this.toggleFilterClass();

			if ('powerpack' === pp_templates_lib.templates.getFilter('source') && 'page' !== pp_templates_lib.templates.getFilter('type')) {
				this.setMasonrySkin();
			}
		},
		onBeforeRenderEmpty: function onBeforeRenderEmpty() {
			this.addSourceData();
		},
		onTextFilterInput: function onTextFilterInput() {
			pp_templates_lib.templates.setFilter('text', this.ui.textFilter.val());
		},
		onSelectFilterChange: function onSelectFilterChange(event) {
			var $select = jQuery(event.currentTarget),
				filterName = $select.data('elementor-filter');
			pp_templates_lib.templates.setFilter(filterName, $select.val());
		},
		onMyFavoritesFilterChange: function onMyFavoritesFilterChange() {
			pp_templates_lib.templates.setFilter('favorite', this.ui.myFavoritesFilter[0].checked);
		},
		onOrderLabelsClick: function onOrderLabelsClick(event) {
			var $clickedInput = jQuery(event.currentTarget.control),
				toggle;

			if (!$clickedInput[0].checked) {
				toggle = 'asc' !== $clickedInput.data('default-ordering-direction');
			}

			$clickedInput.toggleClass('elementor-template-library-order-reverse', toggle);
			this.order($clickedInput.val(), $clickedInput.hasClass('elementor-template-library-order-reverse'));
		}
	});

	var TemplateLibraryModalLogoView = Marionette.ItemView.extend({
		template: '#tmpl-elementor-templates-modal__header__logo_pp',
		className: function className() {
			var classes = 'elementor-templates-modal__header__logo';
			return classes;
		},
		events: function events() {
			return {
				click: 'onClick'
			};
		},
		templateHelpers: function templateHelpers() {
			return {
				title: this.getOption('title')
			};
		},
		onClick: function onClick() {
			var clickCallback = this.getOption('click');

			if (clickCallback) {
				clickCallback();
			}
		},
	});

	var TemplateLibraryHeaderActionsView = Marionette.ItemView.extend({
		template: '#tmpl-elementor-template-library-header-actions-pp',
		id: 'elementor-template-library-header-actions',
		ui: {
			sync: '#elementor-template-library-header-sync i',
		},
		events: {
			'click @ui.sync': 'onSyncClick',
		},
		onSyncClick: function() {
			var self = this;
			self.ui.sync.addClass('eicon-animation-spin');
			pp_templates_lib.templates.requestLibraryData({
				onUpdate: function onUpdate() {
					self.ui.sync.removeClass('eicon-animation-spin');
					$e.routes.refreshContainer('powerpack');
				},
				forceUpdate: true,
				forceSync: true
			});
		},
	});

	var TemplateLibraryHeaderMenuView = Marionette.ItemView.extend({
		template: '#tmpl-elementor-template-library-header-menu',
		id: 'elementor-template-library-header-menu',
		templateHelpers: function templateHelpers() {
			return {
				tabs: $e.components.get('powerpack').getTabs()
			};
		}
	});

	var TemplateLibraryLayoutView = elementorModules.common.views.modal.Layout.extend({
		getModalOptions: function() {
			return {
				id: 'elementor-template-library-modal',
				effects: {
					show: 'show',
					hide: 'hide'
				},
			};
		},
		getLogoOptions: function() {
			return {
				title: 'PowerPack Templates',
				click: function click() {
					$e.run('powerpack/open', {
						toDefault: true
					});
					return false;
				}
			};
		},
		getTemplateActionButton: function(templateData) {
			var viewId = '#tmpl-elementor-template-library-insert-button';
			if ( templateData.isPro && 'inactive' === templateData.proStatus ) {
				viewId = '#tmpl-elementor-template-library-get-pro-button-pp';
			}
			if ( templateData.isPro && 'license_inactive' === templateData.proStatus ) {
				viewId = '#tmpl-elementor-pro-template-library-activate-license-button-pp';
			}
			//viewId = elementor.hooks.applyFilters('elementor/editor/pp-template-library/template/action-button', viewId, templateData);
			var template = Marionette.TemplateCache.get(viewId);
			return Marionette.Renderer.render(template);
		},
		setHeaderDefaultParts: function() {
			var headerView = this.getHeaderView();
			headerView.tools.show(new TemplateLibraryHeaderActionsView());
			headerView.menuArea.show(new TemplateLibraryHeaderMenuView());
			//headerView.menuArea.reset();
			this.showLogo();
		},
		showLogo: function() {
			this.getHeaderView().logoArea.show( new TemplateLibraryModalLogoView( this.getLogoOptions() ) );
		},
		showTemplatesView: function(templatesCollection) {
			this.modalContent.show(new TemplateLibraryCollectionView({
				collection: templatesCollection
			}));
		},
		showPreviewView: function(templateModel) {
			this.modalContent.show(new TemplateLibraryPreviewView({
				url: templateModel.get('url')
			}));
			var headerView = this.getHeaderView();
			headerView.menuArea.reset();
			headerView.tools.show(new TemplateLibraryHeaderPreviewView({
				model: templateModel
			}));
			headerView.logoArea.show(new TemplateLibraryHeaderBackView());
		},
	});

	var Component = function(ComponentModal) {
		Component.prototype = Object.create(ComponentModal && ComponentModal.prototype, {
			constructor: {
				value: Component,
				writable: true,
				configurable: true
			}
		});
		Object.setPrototypeOf(Component, ComponentModal);
		function Component() {
			return Object.getPrototypeOf(Component).apply(this, arguments);
		}
		var parent = Object.getPrototypeOf( Component.prototype );
		Component.prototype.__construct = function(args) {
			parent.__construct.call( this, args );
			elementor.on( 'document:loaded', this.onDocumentLoaded.bind( this ) );
		};
		Component.prototype.getNamespace = function() {
			return 'powerpack';
		};
		Component.prototype.defaultTabs = function() {
			return {
				'templates/blocks': {
					title: 'Blocks',
					getFilter: function() {
						return {
							source: 'powerpack',
							type: 'block',
							subtype: elementor.config.document.remoteLibrary.category,
						}
					}
				},
				'templates/pages': {
					title: 'Pages',
					filter: {
						source: 'powerpack',
						type: 'page',
					},
				},
			};
		};
		Component.prototype.defaultRoutes = function() {
			return {
				preview: function(args) {
					this.manager.layout.showPreviewView(args.model);
				}
			};
		},
		Component.prototype.defaultCommands = function() {
			return Object.assign( parent.defaultCommands.call( this ), {
				open: this.show,
				'insert-template': this.insertTemplate
			} );
		};
		Component.prototype.defaultShortcuts = function() {
			return {
				open: {
					keys: 'ctrl+shift+p',
				}
			};
		};
		Component.prototype.onDocumentLoaded = function( document ) {
			this.setDefaultRoute( 'templates/blocks' );
			this.maybeOpenLibrary();
		};
		Component.prototype.renderTab = function(tab) {
			var currentTab = this.tabs[tab],
				filter = currentTab.getFilter ? currentTab.getFilter() : currentTab.filter;
			this.manager.setScreen(filter);
		};
		Component.prototype.activateTab = function(tab) {
			$e.routes.saveState('powerpack');
			parent.activateTab.call( this, tab );
		};
		Component.prototype.open = function() {
			parent.open.call( this );

			if ( ! this.manager.layout ) {
				this.manager.layout = this.layout;
			}
	
			this.manager.layout.setHeaderDefaultParts();
	
			return true;
		};
		Component.prototype.close = function() {
			if ( ! parent.close.call( this ) ) {
				return false;
			}
	
			this.manager.modalConfig = {};
	
			return true;
		};
		Component.prototype.show = function( args ) {
			this.manager.modalConfig = args;
	
			if ( args.toDefault || ! $e.routes.restoreState( 'powerpack' ) ) {
				$e.route( this.getDefaultRoute() );
			}
		};
		Component.prototype.insertTemplate = function( args ) {
			var autoImportSettings = false,
				model = args.model,
				self = this;
	
			var withPageSettings = args.withPageSettings || null;
	
			if ( autoImportSettings ) {
				withPageSettings = true;
			}
	
			if ( null === withPageSettings && model.get( 'hasPageSettings' ) ) {
				var insertTemplateHandler = this.getImportSettingsDialog();
	
				insertTemplateHandler.showImportDialog( model );
	
				return;
			}
	
			this.manager.layout.showLoadingView();
	
			this.manager.requestTemplateContent( model.get( 'source' ), model.get( 'template_id' ), {
				data: {
					with_page_settings: withPageSettings,
				},
				success: function( data ) {
					// Clone the `modalConfig.importOptions` because it deleted during the closing.
					var importOptions = jQuery.extend( {}, self.manager.modalConfig.importOptions );
	
					importOptions.withPageSettings = withPageSettings;
	
					// Hide for next open.
					self.manager.layout.hideLoadingView();
	
					self.manager.layout.hideModal();
	
					$e.run( 'document/elements/import', {
						model:model,
						data:data,
						options: importOptions,
					} );
				},
				error: function( data ) {
					self.manager.showErrorDialog( data );
				},
				complete: function() {
					self.manager.layout.hideLoadingView();
				},
			} );

			Component.prototype.getImportSettingsDialog = function() {
				var InsertTemplateHandler = {
					dialog: null,
		
					showImportDialog: function( model ) {
						var dialog = InsertTemplateHandler.getDialog();
		
						dialog.onConfirm = function() {
							$e.run( 'library/insert-template', {
								model:model,
								withPageSettings: true,
							} );
						};
		
						dialog.onCancel = function() {
							$e.run( 'library/insert-template', {
								model:model,
								withPageSettings: false,
							} );
						};
		
						dialog.show();
					},
		
					initDialog: function() {
						InsertTemplateHandler.dialog = elementorCommon.dialogsManager.createWidget( 'confirm', {
							id: 'elementor-insert-template-settings-dialog',
							headerMessage: elementor.translate( 'import_template_dialog_header' ),
							message: elementor.translate( 'import_template_dialog_message' ) + '<br>' + elementor.translate( 'import_template_dialog_message_attention' ),
							strings: {
								confirm: elementor.translate( 'yes' ),
								cancel: elementor.translate( 'no' ),
							},
						} );
					},
		
					getDialog: function() {
						if ( ! InsertTemplateHandler.dialog ) {
							InsertTemplateHandler.initDialog();
						}
		
						return InsertTemplateHandler.dialog;
					},
				};
		
				return InsertTemplateHandler;
			};
		};
		Component.prototype.getTabsWrapperSelector = function() {
			return '#elementor-template-library-header-menu';
		};
		Component.prototype.getModalLayout = function() {
			return TemplateLibraryLayoutView;
		};
		Component.prototype.maybeOpenLibrary = function() {
			if ('#powerpack' === location.hash) {
				$e.run('powerpack/open');
				location.hash = '';
			}
		};

		return Component;
	}(elementorModules.common.ComponentModal);


	var TemplateLibraryManager = function TemplateLibraryManager() {
		var self = this,
			errorDialog,
			config = {},
			templateTypes = {
				page: {},
				section: {}
			},
			filterTerms = {},
			templatesCollection = false;

		this.modalConfig = {};

		var registerDefaultFilterTerms = function() {
			filterTerms = {
				text: {
					callback: function callback(value) {
						value = value.toLowerCase();

						if (this.get('title').toLowerCase().indexOf(value) >= 0) {
							return true;
						}

						return _.any(this.get('tags'), function (tag) {
							return tag.toLowerCase().indexOf(value) >= 0;
						});
					}
				},
				type: {},
				subtype: {},
				favorite: {}
			};
		};

		this.getFilter = function (name) {
			return elementor.channels.templates.request('filter:' + name);
		};

		this.setFilter = function (name, value, silent) {
			elementor.channels.templates.reply('filter:' + name, value);

			if (!silent) {
				elementor.channels.templates.trigger('filter:change');
			}
		};

		this.setScreen = function (args) {
			elementor.channels.templates.stopReplying();
			self.setFilter('source', args.source, true);
			self.setFilter('type', args.type, true);
			self.setFilter('subtype', args.subtype, true);
			self.showTemplates();
		};

		this.getTemplatesCollection = function () {
			return templatesCollection;
		};

		this.getConfig = function (item) {
			if (item) {
				return config[item] ? config[item] : {};
			}

			return config;
		};

		this.getFilterTerms = function (termName) {
			if (termName) {
				return filterTerms[termName];
			}

			return filterTerms;
		};

		this.requestLibraryData = function (options) {
			if (templatesCollection && !options.forceUpdate) {
				if (options.onUpdate) {
					options.onUpdate();
				}

				return;
			}

			if (options.onBeforeUpdate) {
				options.onBeforeUpdate();
			}

			var ajaxOptions = {
				data: {},
				success: function success(data) {
					templatesCollection = new TemplateLibraryCollection(data.templates);

					if (data.config) {
						config = data.config;
					}

					if (options.onUpdate) {
						options.onUpdate();
					}
				}
			};

			if (options.forceSync) {
				ajaxOptions.data.sync = true;
			}

			elementorCommon.ajax.addRequest('pp_get_library_data', ajaxOptions);
		};

		this.loadTemplates = function (_onUpdate) {
			self.requestLibraryData({
				onBeforeUpdate: self.layout.showLoadingView.bind(self.layout),
				onUpdate: function onUpdate() {
					self.layout.hideLoadingView();

					if (_onUpdate) {
						_onUpdate();
					}
				}
			});
		};

		this.showTemplates = function () {
			// The tabs should exist in DOM on loading.
			self.layout.setHeaderDefaultParts();
			self.layout.showModal();
			//self.layout.modal.refreshPosition();
			self.loadTemplates(function () {
				var templatesToShow = self.filterTemplates();
				self.layout.showTemplatesView(new TemplateLibraryCollection(templatesToShow));
			});
		};

		this.filterTemplates = function () {
			//var activeSource = 'powerpack';
			return templatesCollection.filter(function (model) {
				if ('powerpack' !== model.get('source')) {
					return false;
				}

				return 'block' === model.get('type') || 'page' === model.get('type');
			});
		};

		this.requestTemplateContent = function (source, id, ajaxOptions) {
			var options = {
				unique_id: id,
				data: {
					source: source,
					edit_mode: true,
					display: true,
					template_id: id
				}
			};

			if (ajaxOptions) {
				jQuery.extend(true, options, ajaxOptions);
			}

			return elementorCommon.ajax.addRequest('get_template_data', options);
		};

		this.markAsFavorite = function (templateModel, favorite) {
			var options = {
				data: {
					source: templateModel.get('source'),
					template_id: templateModel.get('template_id'),
					favorite: favorite
				}
			};
			return elementorCommon.ajax.addRequest('mark_template_as_favorite', options);
		};

		this.getErrorDialog = function() {
			if ( ! errorDialog ) {
				errorDialog = elementorCommon.dialogsManager.createWidget( 'alert', {
					id: 'elementor-template-library-error-dialog',
					headerMessage: elementor.translate( 'an_error_occurred' ),
				} );
			}
	
			return errorDialog;
		};

		this.showErrorDialog = function( errorMessage ) {
			if ( 'object' === typeof errorMessage ) {
				var message = '';
	
				_.each( errorMessage, function( error ) {
					message += '<div>' + error.message + '.</div>';
				} );
	
				errorMessage = message;
			} else if ( errorMessage ) {
				errorMessage += '.';
			} else {
				errorMessage = '<i>&#60;The error message is empty&#62;</i>';
			}
	
			self.getErrorDialog()
				.setMessage( elementor.translate( 'templates_request_error' ) + '<div id="elementor-template-library-error-info">' + errorMessage + '</div>' )
				.show();
		};

		this.init = function() {
			registerDefaultFilterTerms();
			this.component = $e.components.register(new Component({
				manager: this
			}));
		};
	};

	pp_templates_lib.templates = new TemplateLibraryManager();
	pp_templates_lib.templates.init();

	elementor.on('document:loaded', function() {
		var $previewContents = elementor.$previewContents;
	
		var $templateLibBtnStyle = $( '<style />' ),
			btnStyle = '';
			btnStyle += '.elementor-add-section-area-button.pp-add-template-button { margin-left: 8px; vertical-align: bottom; }';
			btnStyle += '.elementor-add-section-area-button.pp-add-template-button img { height: 40px; }';

		$templateLibBtnStyle.html( btnStyle );

		$previewContents.find('head').append( $templateLibBtnStyle );

		var $templateLibBtn = $('<div />');
		
		$templateLibBtn.addClass( 'elementor-add-section-area-button pp-add-template-button' );
		$templateLibBtn.attr( 'title', 'Add PowerPack Template' );
		$templateLibBtn.html( '<img src="' + pp_templates_lib.logoUrl + '" />' );
		$templateLibBtn.insertAfter( $previewContents.find('.elementor-add-section-area-button.elementor-add-template-button') );
		$templateLibBtn.on( 'click', function() {
			$e.run('powerpack/open');
		});

		$previewContents.find('body').delegate('.elementor-editor-element-add', 'click', function(e) {
			setTimeout(function() {
				var sectionInner = $previewContents.find('body').find('.elementor-add-section-inner');

				if ( sectionInner.length > 0 ) {
					sectionInner.find( '.pp-add-template-button' ).remove();
					$templateLibBtn.off('click').on( 'click', function() {
						$e.run('powerpack/open');
					});
					$templateLibBtn.insertAfter( sectionInner.find('.elementor-add-section-area-button.elementor-add-template-button') );
				}
			}, 100);
		});
	});

} )( elementor, jQuery, window );