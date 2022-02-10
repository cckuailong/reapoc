/**
 * BLOCK:Complianz Documents block
 *
 * Registering the Complianz Privacy Suite documents block with Gutenberg.
 */

//  Import CSS.
// import './style.scss';
// import './editor.scss';

import * as api from './utils/api';
//
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { SelectControl } = wp.components;
const { PanelBody, PanelRow } = wp.components;
const { RichText } = wp.editor;
const { Component } = wp.element;
const el = wp.element.createElement;

/**
 *  Set custom Complianz Icon
 */

const iconEl =
    el('svg', { width: 20, height: 20 ,viewBox : "0 0 133.62 133.62"},
        el('path', { d: "M113.63,19.34C100.37,6.51,84.41,0,66.2,0A64.08,64.08,0,0,0,19.36,19.36,64.08,64.08,0,0,0,0,66.2c0,18.25,6.51,34.21,19.34,47.43s28.61,20,46.86,20,34.2-6.72,47.45-20,20-29.21,20-47.45S126.89,32.21,113.63,19.34Zm-2.85,91.44c-12.47,12.46-27.47,18.77-44.58,18.77s-31.89-6.31-43.94-18.75A62.11,62.11,0,0,1,4.07,66.2a60.14,60.14,0,0,1,18.17-44,60.1,60.1,0,0,1,44-18.17c17.12,0,32.12,6.12,44.6,18.19s18.75,26.86,18.75,43.94S123.23,98.32,110.78,110.78Z" } ),
        el('path', { d: "M99.49,30.71a6.6,6.6,0,0,0-9.31,0L40.89,80,35.3,74.41a6.58,6.58,0,0,0-9.31,0l-2.12,2.12a6.6,6.6,0,0,0,0,9.31l9.64,9.64a6.67,6.67,0,0,0,.56.65l2.12,2.12L41,102.8l4-4a8.39,8.39,0,0,0,.65-.56l2.12-2.12a8.39,8.39,0,0,0,.56-.65l53.34-53.34a6.6,6.6,0,0,0,0-9.31Z" } ),
        el('path', { d: "M94.91,86.63H65.15L48.86,102.8H94.91a6.6,6.6,0,0,0,6.58-6.58v-3A6.61,6.61,0,0,0,94.91,86.63Z" } ),
        el('path', { d: "M47.09,45H68.71L85,28.79H47.09a6.6,6.6,0,0,0-6.58,6.58v3A6.6,6.6,0,0,0,47.09,45Z" } ),
    );


    class selectDocument extends Component {
        // Method for setting the initial state.
        static getInitialState(attributes) {
            return {
                documents: [],
                selectedDocument: attributes.selectedDocument,
                customDocument: attributes.customDocument,
                documentSyncStatus : attributes.documentSyncStatus,
                document: {},
                hasDocuments: true,
				preview: false,
			};
        }

        // Constructing our component. With super() we are setting everything to 'this'.
        // Now we can access the attributes with this.props.attributes
        constructor() {
            super(...arguments);
            // Maybe we have a previously selected document. Try to load it.
            this.state = this.constructor.getInitialState(this.props.attributes);

            // Bind so we can use 'this' inside the method.
            this.getDocuments = this.getDocuments.bind(this);
            this.getDocuments();

            this.onChangeSelectDocument = this.onChangeSelectDocument.bind(this);
            this.onChangeSelectDocumentSyncStatus = this.onChangeSelectDocumentSyncStatus.bind(this);
            this.onChangeCustomDocument = this.onChangeCustomDocument.bind(this);
        }

        getDocuments(args = {}) {
            return (api.getDocuments()).then( ( response ) => {
                let documents = response.data;
                if( documents && 0 !== this.state.selectedDocument ) {
                    // If we have a selected document, find that document and add it.
                    const document = documents.find( ( item ) => { return item.id == this.state.selectedDocument } );
                    if (documents.length === 0) {
                        this.setState({hasDocuments: false});

                        this.props.setAttributes({
                            hasDocuments: false,
                        });
                    }

                    // This is the same as { document: document, documents: documents }
                    //this.state.documents = documents;
                    this.setState( { document, documents } );
                } else {
                    //this.state.documents = documents;
                    this.setState({ documents });
                }
            });
        }

        onChangeSelectDocument(value) {
            const document = this.state.documents.find((item) => {
                return item.id === value
            });

            // Set the state
            this.setState({selectedDocument: value, document});

            // Set the attributes
            this.props.setAttributes({
                selectedDocument: value,
            });

        }

        onChangeCustomDocument(value){
            this.setState({customDocument: value});

            // Set the attributes
            this.props.setAttributes({
                customDocument: value,
            });
        }

        onChangeSelectDocumentSyncStatus(value){


            this.setState({documentSyncStatus: value});

            // Set the attributes
            this.props.setAttributes({
                documentSyncStatus: value,
            });

            if (value==='sync'){
                //when sync is turned back on, we reset the customDocument data
                let output = this.state.document.content;

                this.setState({customDocument: output});

                // Set the attributes
                this.props.setAttributes({
                    customDocument: output,
                });

            }
        }



        render() {
            const { className, attributes: {} = {} } = this.props;

            let options = [{value: 0, label: __('Select a document', 'complianz-gdpr')}];
            let output = __('Loading...', 'complianz-gdpr');
            let id = 'document-title';
            let documentSyncStatus = 'sync';
            let document_status_options = [
                {value: 'sync', label: __('Synchronize document with Complianz', 'complianz-gdpr')},
                {value: 'unlink', label: __('Edit document and stop synchronization', 'complianz-gdpr')},
            ];

            if (!this.props.attributes.hasDocuments){
                output = __('No documents found. Please finish the Complianz Privacy Suite wizard to generate documents', 'complianz-gdpr');
                id = 'no-documents';
            }

            //preview
			if (this.props.attributes.preview){
				return(
						<img src={complianz.cmplz_preview} />
				);
			}

            //build options
            if (this.state.documents.length > 0) {
                if (!this.props.isSelected){
                    output = __('Click this block to show the options', 'complianz-gdpr');
                } else {
                    output = __('Select a document type from the dropdownlist', 'complianz-gdpr');
                }
                this.state.documents.forEach((document) => {
                    options.push({value: document.id, label: document.title});
                });
            }

            //load content
            if (this.props.attributes.selectedDocument!==0 && this.state.document && this.state.document.hasOwnProperty('title')) {
                output = this.state.document.content;
                id = this.props.attributes.selectedDocument;
                documentSyncStatus = this.props.attributes.documentSyncStatus;
            }

            let customDocument = output;
            if (this.props.attributes.customDocument.length>0){
                customDocument = this.props.attributes.customDocument;
            }

			if (documentSyncStatus==='sync') {

				return [
                    !!this.props.isSelected && (
                        <InspectorControls key='inspector'>
							<PanelBody title={ __('Document settings', 'complianz-gdpr' ) }initialOpen={ true } >
								<PanelRow>
                            <SelectControl onChange={this.onChangeSelectDocument}
                                           value={this.props.attributes.selectedDocument}
                                           label={__('Select a document', 'complianz-gdpr')}
                                           options={options}/>
								</PanelRow><PanelRow>
                            <SelectControl onChange={this.onChangeSelectDocumentSyncStatus}
                                           value={this.props.attributes.documentSyncStatus}
                                           label={__('Document sync status', 'complianz-gdpr')}
                                           options={document_status_options}/>
								</PanelRow>
							</PanelBody>

                        </InspectorControls>
                    ),

                    <div key={id} className={className} dangerouslySetInnerHTML={{__html: output}}></div>
                ]
            } else {
                return [
                    !!this.props.isSelected && (
                        <InspectorControls key='inspector'>
				<PanelBody title={ __('Document settings', 'complianz-gdpr' ) }initialOpen={ true } >
				<PanelRow>
                            <SelectControl onChange={this.onChangeSelectDocument}
                                           value={this.props.attributes.selectedDocument}
                                           label={__('Select a document', 'complianz-gdpr')}
                                           options={options}/>
				</PanelRow><PanelRow>

                            <SelectControl onChange={this.onChangeSelectDocumentSyncStatus}
                                           value={this.props.attributes.documentSyncStatus}
                                           label={__('Document sync status', 'complianz-gdpr')}
                                           options={document_status_options}/>
								</PanelRow>
							</PanelBody>
                        </InspectorControls>
                    ),

                    <RichText
                        className={className}
                        value={customDocument}
                        autoFocus
                        onChange={this.onChangeCustomDocument}
                    />
                ]
            }
        }

    }

    /**
     * Register: a Gutenberg Block.
     *
     * Registers a new block provided a unique name and an object defining its
     * behavior. Once registered, the block is made editor as an option to any
     * editor interface where blocks are implemented.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/
     * @param  {string}   name     Block name.
     * @param  {Object}   settings Block settings.
     * @return {?WPBlock}          The block, if it has been successfully
     *                             registered; otherwise `undefined`.
     */



    registerBlockType('complianz/document', {
        title: __('Legal document - Complianz', 'complianz-gdpr'), // Block title.
        icon: iconEl, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
        category: 'widgets',
		example: {
			attributes: {
				'preview' : true,
			},
		},
        keywords: [
            __('Privacy Statement', 'complianz-gdpr'),
            __('Cookie Policy', 'complianz-gdpr'),
            __('Disclaimer', 'complianz-gdpr'),
        ],
        //className: 'cmplz-document',
        attributes: {
            documentSyncStatus: {
                type: 'string',
                default: 'sync'
            },
            customDocument: {
                type: 'string',
                default: ''
            },
            hasDocuments: {
                type: 'string',
                default: 'false',
            },
            content: {
                type: 'string',
                source: 'children',
                selector: 'p',
            },
            selectedDocument: {
                type: 'string',
                default: '',
            },
            documents: {
                type: 'array',
            },
            document: {
                type: 'array',
            },
			preview: {
                type: 'boolean',
                default: false,
            }
        },
        /**
         * The edit function describes the structure of your block in the context of the editor.
         * This represents what the editor will render when the block is used.
         *
         * The "edit" property must be a valid function.
         *
         * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
         */

        edit:selectDocument,

        /**
         * The save function defines the way in which the different attributes should be combined
         * into the final markup, which is then serialized by Gutenberg into post_content.
         *
         * The "save" property must be specified and must be a valid function.
         *
         * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
         */

        save: function() {
            // Rendering in PHP
            return null;
        },
    });
