/**
 * 10Web plugins Gutenberg integration
 * version 2.0.6
 */
( function ( blocks, element ) {
  registerAllPluginBlocks();

  function registerAllPluginBlocks() {
    var twPluginsData = JSON.parse(tw_obj_translate.blocks);
    for ( var pluginId in window['tw_gb'] ) {
      if ( !window['tw_gb'].hasOwnProperty( pluginId ) ) {
        continue;
      }
      twPluginsData[pluginId] = window['tw_gb'][pluginId];
    }
    if ( !twPluginsData ) {
      return;
    }

    for ( var pluginId in twPluginsData ) {
      if ( !twPluginsData.hasOwnProperty( pluginId ) ) {
        continue;
      }

      if ( !twPluginsData[pluginId].inited ) {
        twPluginsData[pluginId].inited = true;
        registerPluginBlock( blocks, element, pluginId, twPluginsData[pluginId] );
      }
    }
  }

  function registerPluginBlock( blocks, element, pluginId, pluginData ) {
    var el = element.createElement;

    var isPopup = pluginData.isPopup;

    var iconEl = el( 'img', {
      width: pluginData.iconSvg.width,
      height: pluginData.iconSvg.height,
      src: pluginData.iconSvg.src
    } );

    blocks.registerBlockType( pluginId, {
      title: pluginData.title,
      icon: iconEl,
      category: 'common',
      attributes: {
        shortcode: {
          type: 'string'
        },
        popupOpened: {
          type: 'boolean',
          value: true
        },
        notInitial: {
          type: 'boolean'
        },
        shortcode_id: {
          type: 'string'
        }
      },

      edit: function ( props ) {
        if ( !props.attributes.notInitial ) {
          props.setAttributes( {
            notInitial: true,
            popupOpened: true
          } );

          return el( 'p' );
        }

        if ( props.attributes.popupOpened ) {
          if ( isPopup ) {
            return showPopup( props.attributes.shortcode, props.attributes.shortcode_id );
          }
          else {
            return showShortcodeList( props.attributes.shortcode );
          }
        }

        if ( props.attributes.shortcode ) {
          return showShortcode();
        }
        else {
          return showShortcodePlaceholder();
        }

        function showPopup( shortcode, shortcode_id ) {
          var shortcodeCbName = generateUniqueCbName( pluginId );
          /* Store shortcode attribute into a global variable to get it from an iframe. */
          window[shortcodeCbName + '_shortcode'] = shortcode ? shortcode : '';
          window[shortcodeCbName] = function ( shortcode, shortcode_id ) {
            delete window[shortcodeCbName];

            if ( props ) {
              props.setAttributes( { shortcode: shortcode, shortcode_id: shortcode_id, popupOpened: false } );
            }
          };
          props.setAttributes( { popupOpened: true } );
          if (!shortcode_id && undefined != shortcode) {
            var shortcode_extract = shortcode.split(' ');
            for (i = 0; i < shortcode_extract.length; i++) {
              var attributes = shortcode_extract[i].split('=');
              if ('id'== attributes[0]) {
                shortcode_id = attributes[1].replace(/"/g, "");
              }
            }
          }
          jQuery(".edit-post-layout, .edit-post-layout__content").css({"z-index":"99999","overflow":"visible"});
          var elem = el( 'form', { className: 'tw-container' }, el( 'div', { className: 'tw-container-wrap' + (pluginData.containerClass ? ' ' + pluginData.containerClass : '') }, el( 'span', {
            className: "media-modal-close",
            onClick: close
          }, el( "span", { className: "media-modal-icon" } ) ), el( 'iframe', { src: pluginData.data.shortcodeUrl + '&callback=' + shortcodeCbName + '&edit=' + shortcode_id + '&shortcode=' + shortcode} ) ) );
          return elem;
        }

        function showShortcodeList( shortcode ) {
          props.setAttributes( { popupOpened: true } );
          var children = [];
          var shortcodeList = JSON.parse( pluginData.data );
          shortcodeList.inputs.forEach( function ( inputItem ) {
            if ( inputItem.type === 'select' ) {
              children.push( el( 'option', { value: '', dataId: 0 }, tw_obj_translate.empty_item ) );
              if ( inputItem.options.length ) {
                inputItem.options.forEach( function ( optionItem ) {
                  var shortcode = '[' + shortcodeList.shortcode_prefix + ' ' + inputItem.shortcode_attibute_name + '="' + optionItem.id + '"]';
                  children.push(
                    el( 'option', { value: shortcode, dataId: optionItem.id }, optionItem.name )
                  );
                } )
              }
            }
          } );

          if ( shortcodeList.shortcodes ) {
            shortcodeList.shortcodes.forEach( function ( shortcodeItem ) {
              children.push(
                el( 'option', { value: shortcodeItem.shortcode, dataId: shortcodeItem.id }, shortcodeItem.name )
              );
            } );
          }

          return el( 'form', { onSubmit: chooseFromList }, el( 'div', {}, pluginData.titleSelect ), el( 'select', {
            value: shortcode,
            onChange: chooseFromList,
            class: 'tw-gb-select'
          }, children ) );
        }

        function showShortcodePlaceholder() {
          props.setAttributes( { popupOpened: false } );
          return el( 'p', {
            style: {
              'cursor': "pointer"
            },

            onClick: function () {
              props.setAttributes( { popupOpened: true } );
            }.bind( this )
          }, tw_obj_translate.nothing_selected );
        }

        function showShortcode() {
          if(pluginData.title=="Photo Gallery"){
            var iconWidth = 'auto';
            var iconHeight = 'auto';
          }
          else {
            var iconWidth = '36px';
            var iconHeight = '36px';
          }
          return el( 'img', {
            src: pluginData.iconUrl,
            alt: pluginData.title,
            style: {
              'height': iconHeight,
              'width': iconWidth
            },
            onClick: function () {
              props.setAttributes( { popupOpened: true } );
            }.bind( this )
          } );
        }

        function close() {
          jQuery(".edit-post-layout, .edit-post-layout__content").css({"z-index":"0","overflow":"auto"});
          props.setAttributes( { popupOpened: false } );
        }

        function chooseFromList( event, shortcode_id ) {
          var selected = event.target.querySelector( 'option:checked' );
          props.setAttributes( { shortcode: selected.value, shortcode_id: selected.dataId, popupOpened: false } );
          event.preventDefault();
        }
      },

      save: function ( props ) {
        return props.attributes.shortcode;
      }
    } );
  }

  function generateUniqueCbName( pluginId ) {
    return 'wdg_cb_' + pluginId;
  }
} )(
  window.wp.blocks,
  window.wp.element
);
