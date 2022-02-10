(function($) {
  window.IbtanaModal = window.IbtanaModal || {};
  IbtanaModal.Box = (function() {
    var selectors = {
      box:      '#ibtanaBoxModal',
      closeBtn: '#ibtanaBoxModal .ive-close-button',
      mainBtn:  '#ibtanaBoxModal .ive-demo-main-btn',
      input:    '#ibtanaBoxModal input',
      anchor:   '#ibtanaBoxModal a'
    };

    function getHtml( title = 'anything', subTitle = 'anything', showInput, showLink ) {
      var box_modal = `<div id="ibtanaBoxModal" class="ive-plugin-popup" style="z-index: 999999;">
        <div class="ive-admin-modal" style="height: 50%;">
          <button class="ive-close-button">×</button>
          <div class="ive-demo-step-container">
            <div class="ive-current-step">

              <div class="ive-demo-child ive-demo-step ive-demo-step-0 active">
                <h2>` + title + `</h2>
                <p>` + subTitle + `</p>`;
                if ( showInput ) {
                  box_modal += `<div class="ive-checkbox-container">
                    <input type="text" placeholder="ibtana template" name="template_name" value="ibtana template" style="width: 100%;">
                  </div>`;
                }
                if ( showLink ) {
                  box_modal += `
                  <style>
                  .ive-checkbox-container:not(.activated):hover {
                    color: unset !important;
                  }
                  .ive-checkbox-container {
                    display: unset !important;
                  }
                  .ive-checkbox-container a {
                    color: #0e8ecc !important;
                  }
                  </style>
                  <div class="ive-checkbox-container">
                    You can view your saved templates in the templates modal, if you want to have a look then <a>click here</a>.
                  </div>`;
                }
              box_modal += `</div>
            </div>
            <div class="ive-demo-step-controls">
              <button class="ive-demo-btn ive-demo-main-btn">OK</button>
            </div>
          </div>
        </div>
      </div>`;

      return box_modal;
    }


    function setup( title, subtitle, showInput, showLink, callback ) {
      var boxHtml = getHtml( title, subtitle, showInput, showLink );
      jQuery( document.body ).append( boxHtml );
      attachEvents( callback );
    }

    function attachEvents( callback ) {
      jQuery( selectors.closeBtn ).on( 'click', function() {
        jQuery( selectors.box ).remove();
      } );
      jQuery( selectors.input ).on( 'click mouseenter mouseleave keypress keydown keyup', function() {
        if ( !jQuery( selectors.input ).val() ) {
          jQuery( selectors.mainBtn ).prop( 'disabled', true );
        } else {
          jQuery( selectors.mainBtn ).prop( 'disabled', false );
        }
      } );
      jQuery( selectors.mainBtn ).on( 'click', function() {
        callback( jQuery( selectors.input ).val() );
        jQuery( selectors.box ).remove();
      } );
      jQuery( selectors.anchor ).on( 'click', function() {
        jQuery( '.modal_btn_svg_icon' ).trigger( 'click' );
        jQuery( '[data-tab-head="SavedTemplates"]' ).trigger( 'click' );
        jQuery( selectors.box ).remove();
      } );
      jQuery( selectors.box ).show();
    }

    return {
      setup: setup
    };
  })();

  function ajaxPost( endpoint, data_post, callback ) {
    $('.ibtana--modal--loader').show();
    jQuery.ajax({
      method:       "POST",
      url:          endpoint,
      data:         JSON.stringify(data_post),
      dataType:     'json',
      contentType:  'application/json',
    }).done(function( data ) {
      $('.ibtana--modal--loader').hide();
      callback( data );
    });
  }

  function ibtana_visual_editor_show_hide_modal_button() {
    var togglebtn = document.querySelector( ".components-panel__body-toggle" );
    if (togglebtn !== null) {
      var isbtntrue = togglebtn.getAttribute("aria-expanded");
      if (document.getElementById("ibtana-modal-btn") !== null) {
        if (isbtntrue == 'false') {
          $( '#ibtana-modal-btn' ).closest("div").hide();
        }else{
          $( '#ibtana-modal-btn' ).closest("div").show();
        }
      }
      if (document.getElementById("ive-save-template-btn") !== null) {
        if (isbtntrue == 'false') {
          $( '#ive-save-template-btn' ).closest("div").hide();
        }else{
          $( '#ive-save-template-btn' ).closest("div").show();
        }
        if ( wp.data.select( "core/editor" ).getEditedPostContent() === "" ) {
          $( '#ive-save-template-btn' ).closest("div").hide();
        }
      }
    }
  }

  function ibtana_visual_editor_AppendOpenModalBtn() {
    var myspan = $('.edit-post-post-status');
    if( myspan.length ) {

      if ( !jQuery( '#ibtana-modal-btns-wrap' ).length ) {

        myspan.append(
          `<div id="ibtana-modal-btns-wrap"></div>`
        );

        var is_pro_active = false;
        if ( ( typeof iepaGlobal === "object" ) && ( ibtana_visual_editor_modal_js.post_type == "product" ) ) {
          if ( iepaGlobal.hasOwnProperty( 'iepa_license' ) ) {
            if ( iepaGlobal.iepa_license.hasOwnProperty( 'license_status' ) ) {
              if ( iepaGlobal.iepa_license.license_status === true ) {
                is_pro_active = true;
              }
            }
          }
        }


        if ( !is_pro_active && ( ibtana_visual_editor_modal_js.post_type === "product" ) ) {
          var get_pro_permalink = ibtana_visual_editor_modal_js.adminUrl + "admin.php?page=ibtana-visual-editor-addons";
          if ( ibtana_visual_editor_modal_js.hasOwnProperty( 'get_pro_permalink' ) ) {
            get_pro_permalink = ibtana_visual_editor_modal_js.get_pro_permalink;
          }
          jQuery(
            `<div class="components-panel__row">
              <p id="iepa_product_metabox_license_top" class="iepa_product_metabox_license">
                Get pre-built premium product page templates using <strong>Ibtana - Ecommerce Product Addons.</strong>
                <br>
                <a class="button" href="` + get_pro_permalink + `" target="_blank">Upgrade To Pro!</a>
              </p>
            </div>`
          ).prependTo( '#ibtana-modal-btns-wrap' );
        }


        if ( !jQuery( '#ibtana-modal-btns-wrap #ive_go_pro_metabox_p' ).length ) {
          // vw themes buy now ajax
          $.post(
            ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
            {
              "theme_text_domain":  ibtana_visual_editor_modal_js.themedomain
            }, function ( data ) {

              if( !jQuery( '#ibtana-modal-btns-wrap #ive_go_pro_metabox_p' ).length ) {

                if ( !data.data.is_found ) {
                  // $( '#ive_go_pro_template_metabox' ).hide();
                  // Sirat Logic Start


                  $.post(
                    ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
                    {
                      "theme_text_domain":  ibtana_visual_editor_modal_js.ive_active_vw_theme_text_domain
                    }, function ( data ) {

                      if ( data.data.is_found ) {

                        if ( data.data.is_found.name == "Sirat" ) {

                          if ( ibtana_visual_editor_modal_js.post_type != "product" ) {

                            $(
                              `<div class="components-panel__row">
                                <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                                  <strong>Get Sirat Pro At Just $40.</strong>
                                  <br>
                                  <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                                </p>
                              </div>`
                            ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(1)' ) );

                          } else {

                            if ( !is_pro_active ) {

                              $(
                                `<div class="components-panel__row">
                                  <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                                    <strong>Get Sirat Pro At Just $40.</strong>
                                    <br>
                                    <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                                  </p>
                                </div>`
                              ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(2)' ) );

                            } else {

                              $(
                                `<div class="components-panel__row">
                                  <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                                    <strong>Get Sirat Pro At Just $40.</strong>
                                    <br>
                                    <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                                  </p>
                                </div>`
                              ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(1)' ) );

                            }

                          }

                        } else {

                          var vw_pro_theme_name = 'Premium Features';
        									if ( data.data.is_found.hasOwnProperty( 'parent_theme_template_data' ) ) {
        										if ( data.data.is_found.parent_theme_template_data.hasOwnProperty( 'name' ) ) {
        											vw_pro_theme_name = data.data.is_found.parent_theme_template_data.name;
        										}
        									}

                          if ( ibtana_visual_editor_modal_js.post_type != "product" ) {

                            $(
                              `<div class="components-panel__row">
                                <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                                  <strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
                                  <br>
                                  <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                                </p>
                              </div>`
                            ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(1)' ) );

                          } else {

                            if ( !is_pro_active ) {

                              $(
                                `<div class="components-panel__row">
                                  <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                                    <strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
                                    <br>
                                    <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                                  </p>
                                </div>`
                              ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(2)' ) );

                            } else {

                              $(
                                `<div class="components-panel__row">
                                  <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                                    <strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
                                    <br>
                                    <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                                  </p>
                                </div>`
                              ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(1)' ) );

                            }

                          }

                        }






                      }





                    }
                  );



                  // Sirat Logic END

                } else {
                  // If premium theme is installed
                  if ( ibtana_visual_editor_modal_js.custom_text_domain != "" ) {

                    if ( ibtana_visual_editor_modal_js.post_type != "product" ) {

                      $(
                        `<div class="components-panel__row">
                          <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                            Get all our <strong>160+ Premium Themes</strong> worth $9440 With Our <strong>WP Theme Bundle</strong> in just <strong>$99.</strong>
                            <br>
                            <a class="ive_go_pro_metabox_a1 button" href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" target="_blank">Buy Now!</a>
                          </p>
                        </div>`
                      ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(1)' ) );

                    } else {

                      if ( !is_pro_active ) {

                        $(
                          `<div class="components-panel__row">
                            <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                              Get all our <strong>160+ Premium Themes</strong> worth $9440 With Our <strong>WP Theme Bundle</strong> in just <strong>$99.</strong>
                              <br>
                              <a class="ive_go_pro_metabox_a1 button" href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" target="_blank">Buy Now!</a>
                            </p>
                          </div>`
                        ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(2)' ) );

                      } else if ( ibtana_visual_editor_modal_js.ive_add_on_keys.hasOwnProperty( 'ibtana_ecommerce_product_addons_license_key' ) ) {

                        $(
                          `<div class="components-panel__row">
                            <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                              Get all our <strong>160+ Premium Themes</strong> worth $9440 With Our <strong>WP Theme Bundle</strong> in just <strong>$99.</strong>
                              <br>
                              <a class="ive_go_pro_metabox_a1 button" href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" target="_blank">Buy Now!</a>
                            </p>
                          </div>`
                        ).prependTo( '#ibtana-modal-btns-wrap' );

                      }

                    }

                  }

                  // if free theme is installed
                  else {


                    var vw_pro_theme_name = 'Premium Features';
                    if ( data.data.is_found.hasOwnProperty( 'parent_theme_template_data' ) ) {
                      if ( data.data.is_found.parent_theme_template_data.hasOwnProperty( 'name' ) ) {
                        vw_pro_theme_name = data.data.is_found.parent_theme_template_data.name;
                      }
                    }
                    if ( ibtana_visual_editor_modal_js.post_type != "product" ) {

                      $(
                        `<div class="components-panel__row">
                          <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                            <strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
                            <br>
                            <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                          </p>
                        </div>`
                      ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(1)' ) );

                    } else {


                      if ( !is_pro_active ) {

                        $(
                          `<div class="components-panel__row">
                            <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                              <strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
                              <br>
                              <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                            </p>
                          </div>`
                        ).insertAfter( $( '#ibtana-modal-btns-wrap > div:nth-child(2)' ) );

                      } else if ( ibtana_visual_editor_modal_js.ive_add_on_keys.hasOwnProperty( 'ibtana_ecommerce_product_addons_license_key' ) ) {

                        $(
                          `<div class="components-panel__row">
                            <p id="ive_go_pro_metabox_p" class="ive_go_pro_metabox_p">
                              <strong>Get ` + vw_pro_theme_name + ` At Just $40.</strong>
                              <br>
                              <a class="ive_go_pro_metabox_a2 button" href="` + data.data.is_found.permalink + `" target="_blank">Upgrade To Pro!</a>
                            </p>
                          </div>`
                        ).prependTo( '#ibtana-modal-btns-wrap' );

                      }

                    }

                  }

                }

              }
            }
          );
        }



        jQuery(
          `<div class="components-panel__row">
            <button id="ibtana-modal-btn" class="btn btn-success" type="button">
              Ibtana Blocks Templates
            </button>
          </div>`
        ).prependTo(
          '#ibtana-modal-btns-wrap'
        );

        jQuery( '#ibtana-modal-btns-wrap' ).append(
          `<div class="components-panel__row">
              <button id="ive-save-template-btn" class="btn">Save as template</button>
            </div>`
        );

        $( '#ive-save-template-btn' ).on('click', function() {
          // var name = prompt( 'What would you like to call this template?' );
          IbtanaModal.Box.setup( 'Save Template', 'What would you like to call this template?', true, false, function( name ) {
            if ( name ) {
              jQuery( '#ive-save-template-btn' ).closest("div").hide();
              jQuery( '#ive-save-template-btn' ).addClass( 'ive_is-busy' );
              jQuery.post(
                ibtana_visual_editor_modal_js.adminAjax + '?action=ive_ajax_save_template', {
                  title: name,
                  post_type: ibtana_visual_editor_modal_js.post_type,
                  tpl: wp.data.select( "core/editor" ).getEditedPostContent(),
                  wpnonce: ibtana_visual_editor_modal_js.wpnonce
                }, function( resp ) {
                  if ( resp.status == true ) {
                    // alert( resp.msg );
                    IbtanaModal.Box.setup( 'Success', resp.msg, false, true, function( name ) {});
                    $( '.ive_trial_notice strong' ).text(
                      resp.saved_templates + `/` + resp.save_templates_limit + ` Saves Remaining`
                    );
                    if ( !resp.is_add_on_providing_template_limit ) {
                      $( '.ive_trial_notice_right' ).css( 'display', 'inline-block' );
                    } else {
                      $( '.ive_trial_notice_right' ).hide();
                    }
                  } else {
                    // alert( resp.msg );
                    IbtanaModal.Box.setup( 'Notice', resp.msg, false, false, function( name ) {});
                  }
                  jQuery( '#ive-save-template-btn' ).closest("div").show();
                  jQuery( '#ive-save-template-btn' ).removeClass( 'ive_is-busy' );
                }
              );
            }
          });

        } );

      }

      // if ( !jQuery( '#ive-save-template-btn' ).length ) {
      //   myspan.append(
      //     `<div class="components-panel__row">
      //       <button id="ive-save-template-btn" class="btn">Save as template</button>
      //     </div>`
      //   );
      // }

    }
  }

  wp.data.subscribe( () => {

    // if ( jQuery('button.components-button.editor-post-trash').length ) {
    //   jQuery( 'button.components-button.editor-post-trash' ).remove();
    // }

    appendButton();

    if ( wp.data.select( "core/editor" ).getEditedPostContent() == "" ) {
      $( '#ive-save-template-btn' ).closest("div").hide();
    } else {
      $( '#ive-save-template-btn' ).closest("div").show();
    }
  });

  function appendButton() {



    if (!$('.modal_btn_svg_icon').length) {
      var modal_btn_svg_icon = `<div class="ive-editor-btns-wrap"><div class="modal_btn_svg_icon"><svg id="Layer_1" data-name="Layer 1" width="24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24"><defs><style>.cls-1-ive-editor-btns-wrap{fill:#fff;}.cls-2-ive-editor-btns-wrap{fill:url(#linear-gradient);}</style><linearGradient id="linear-gradient" x1="12" y1="4.56" x2="12" y2="19.93" gradientUnits="userSpaceOnUse"><stop offset="0.03" stop-color="#6ccef5"/><stop offset="0.96" stop-color="#1689c8"/></linearGradient></defs><circle class="cls-1-ive-editor-btns-wrap" cx="12" cy="12" r="12"/><path class="cls-2-ive-editor-btns-wrap" d="M11.44,6.49A1.64,1.64,0,0,0,9.83,4.88H6.47a1.53,1.53,0,0,0-1.35.79,3.64,3.64,0,0,0-.25.65V10a.14.14,0,0,1,0,.06A1.63,1.63,0,0,0,6.5,11.44H9.82a1.63,1.63,0,0,0,1.62-1.61C11.45,8.72,11.45,7.6,11.44,6.49ZM10.33,8.16V9.72a.54.54,0,0,1-.61.61H6.59A.55.55,0,0,1,6,9.73V6.59a.55.55,0,0,1,.6-.6H9.74a.54.54,0,0,1,.59.59Zm1.11,6a1.64,1.64,0,0,0-1.61-1.61H6.47a1.53,1.53,0,0,0-1.34.78,3,3,0,0,0-.26.67v3.67a3.29,3.29,0,0,0,.23.62,1.57,1.57,0,0,0,1.15.81l.07,0H10l.07,0a1.65,1.65,0,0,0,1.38-1.6C11.45,16.4,11.45,15.28,11.44,14.17ZM10.33,17.4a.57.57,0,0,1-.61.62H6.6A.57.57,0,0,1,6,17.4V14.3a.57.57,0,0,1,.64-.63H9.71a.57.57,0,0,1,.62.63Zm8.79-3.23a1.65,1.65,0,0,0-1.6-1.61H14.16a1.65,1.65,0,0,0-1.6,1.61c0,1.12,0,2.23,0,3.34a1.59,1.59,0,0,0,.66,1.28,1.87,1.87,0,0,0,.81.34h3.62l.08,0a1.66,1.66,0,0,0,1.39-1.6C19.13,16.4,19.14,15.29,19.12,14.17ZM18,17.4a.54.54,0,0,1-.62.61h-3.1a.54.54,0,0,1-.62-.62V14.28a.54.54,0,0,1,.61-.61h3.13a.54.54,0,0,1,.6.61Zm1.12-9.23V6.62a1.68,1.68,0,0,0-1.75-1.75H14.29a1.68,1.68,0,0,0-1.74,1.73v3.1a1.69,1.69,0,0,0,1.74,1.74H17.4a1.69,1.69,0,0,0,1.73-1.73ZM18,9.71a.56.56,0,0,1-.62.62h-3.1a.56.56,0,0,1-.62-.61V6.61A.56.56,0,0,1,14.3,6h3.09a.56.56,0,0,1,.62.62Z"/></svg><span class="modal-btn-svg-text-span">Templates</span></div></div>`;
      $('.edit-post-header__toolbar').append(modal_btn_svg_icon);
      // Remove flex:grow CSS
      const innerToolbar = document.querySelector( '.components-accessible-toolbar.edit-post-header-toolbar' );
      if ( innerToolbar ) {
        innerToolbar.style.flexGrow = 0;
      }
    }

  }

  window.onclick = function(event) {
    var myUpcomingModal = document.getElementById("myUpcomingModal");
    if (event.target == myUpcomingModal) {
      myUpcomingModal.style.display = "none";
    }
    if(!document.querySelector("#ibtana-modal-btns-wrap")) {
      ibtana_visual_editor_AppendOpenModalBtn();
    }
    ibtana_visual_editor_show_hide_modal_button();
  }

  window.onload = function() {
    var active_theme = ibtana_visual_editor_modal_js.active_theme_text_domain;

    var ibtana_license_api_endpoint = ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT;

    var svgButtonInterval = setInterval(ibtana_visual_editor_setSVGButton, 1000);
    function ibtana_visual_editor_setSVGButton() {
      if ($('.edit-post-header__toolbar').length !== 0) {
        ibtana_visual_editor_AppendOpenModalBtn();
        ibtana_visual_editor_show_hide_modal_button();
        clearInterval(svgButtonInterval);
      }
    }

    var qtModal = document.createElement("div");
    qtModal.setAttribute("id", "myUpcomingModal");
    qtModal.setAttribute("class", "UpcomingModal");

    var themedomain = ibtana_visual_editor_modal_js.themedomain;
    var theme_slug = themedomain.replaceAll("-", "_");
    var adminUrl = ibtana_visual_editor_modal_js.adminUrl;
    var page_id = ibtana_visual_editor_modal_js.page_id;


    var html = `<div class="UpcomingModal-content"><span class="CloseUpcomingModal">×</span>
    	<div class="content-modal">
        <div class="ibtana-modal-head">
      		<div class="ibtana-row">
            <div class="ibtana-modal-logo">
              <h2>
                <img src="`+ibtana_visual_editor_modal_js.plugin_url+`/dist/images/admin-wizard/adminIcon.png">
                VW Themes
              </h2>
            </div>
        		<div class="ive-tab-parent-head">
              <ul>
                <li>
                  <button class="ive-tablinks active" data-tab-head="Templates">
                    <span class="dashicons dashicons-text-page"></span>Templates
                  </button>
                </li>
                <li>
                  <button class="ive-tablinks" data-tab-head="SavedTemplates">
                    <span class="dashicons dashicons-admin-page"></span>Saved Templates
                  </button>
                </li>
                <li>
                  <button class="ive-tablinks" data-tab-head="Components">
                    <span class="dashicons dashicons-align-wide"></span>Components
                  </button>
                </li>
              </ul>
        		</div>
          </div>
        </div>

        <div class="modal-content-reload-svg">
          <button id="reload--modal--contents">
            <span class="dashicons dashicons-update-alt"></span>
          </button>
          <input type="text" class="search-text" placeholder="Search for names..">
        </div>

        <div class="template-buy-banner">
          <span>Get All Our Premium Themes In Our WP Theme Bundle</span>
          <a href="`+ibtana_visual_editor_modal_js.IBTANA_THEME_URL+`premium/theme-bundle/" target="_blank">BUY NOW</a>
        </div>



    		<div id="Templates" class="tabcontent">

    			<div class="inner-tab-content">
            <ul>
              <li class="theme-tab-list-two active" data-template="free-template" data-template-type="wordpress"><span>Free</span></li>
              <li class="theme-tab-list-two" data-template="premium-template"><span>Premium</span></li>
            </ul>
          </div>


          <div id="free-template" class="ibtana-theme-block">
            <div class="sub-category-wrapper">
              <div class="ibtana-column-one sub-cats">

              </div>
              <div class="ibtana-column-two">
                <div class="ibtana-row themes-box-wrap">

                </div>
                <div class="load-more-wrapper">
                  <button class="button load-more-btn">Load More...</button>
                </div>
              </div>
            </div>
          </div>


          <div id="premium-template" class="ibtana-theme-block" data-template-div="template">

            <div class="sub-category-wrapper">
              <div class="ibtana-column-one sub-cats">

              </div>
              <div class="ibtana-column-two">
                <div class="ibtana-row themes-box-wrap">

                </div>
              </div>
            </div>
          </div>
    		</div>


        <div id="InnerPages" class="tabcontent">
          <div class="inner-tab-content">
            <button class="button back-to-templates">
              <span class="dashicons dashicons-arrow-left-alt"></span>
            </button>
          </div>

          <div class="ibtana-theme-block">
            <div class="sub-category-wrapper">
              <div class="ibtana-column-one sub-cats">

              </div>
              <div class="ibtana-column-two">
                <div class="ibtana-row themes-box-wrap">

                </div>
              </div>
            </div>
          </div>

        </div>


        <div id="SavedTemplates" class="tabcontent" style="display:none;">

          <div class="inner-tab-content">

            <div class="ive_trial_notice">
              <div>
                <div class="components-notice is-info">
                  <div class="components-notice__content">
                    <strong>` +
                      ibtana_visual_editor_modal_js.save_templates_limit_info.saved_templates +
                      `/` + ibtana_visual_editor_modal_js.save_templates_limit_info.save_templates_limit +
                      ` Saves Remaining
                    </strong>
                  </div>
                </div>
              </div>
            </div>


            <div class="ive_trial_notice_right">
              <div>
                <div class="components-notice is-info">
                  <div class="components-notice__content">
                    <strong>For this feature Upgrade to Pro</strong>
                    <a target="_blank" type="button" class="components-button is-primary is-small has-text has-icon" aria-label="Upgrade to Pro">
                      Get Pro
                    </a>
                  </div>
                </div>
              </div>
            </div>


            <ul>
              <li class="theme-tab-list-two active" data-template-type="ibtana_page_template">
                <span>Page Templates</span>
              </li>
            </ul>

          </div>


          <div class="ibtana-theme-block">
            <div class="sub-category-wrapper">
              <div class="ibtana-column-two ibtana-column-full">
                <div class="ibtana-row themes-box-wrap">

                </div>
                <div class="load-more-wrapper" style="display:none;">
                  <button class="button load-more-btn">Load More...</button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div id="Components" class="tabcontent">
          <div class="inner-tab-content">
          </div>

          <div class="ibtana-theme-block">
            <div class="sub-category-wrapper">
              <div class="ibtana-column-one sub-cats">

              </div>
              <div class="ibtana-column-two">
                <div class="ibtana-row themes-box-wrap">

                </div>

                <div class="load-more-wrapper">
                  <button class="button load-more-btn">Load More...</button>
                </div>

              </div>
            </div>
          </div>

        </div>

        <div id="fullSizeModal" class="tabcontent" style="display:none;">
          <div id="fullSizeModalMainWindow">
            <span class="ive-fm-collapse-btn dashicons dashicons-admin-collapse"></span>
            <div class="ive-full-modal-import-sidebar">
              <div class="ive-fm-btns">
                <span class="ive-fm-close dashicons dashicons-no-alt"></span>
                <span class="ive-fm-prev dashicons dashicons-arrow-left-alt2"></span>
                <span class="ive-fm-next dashicons dashicons-arrow-right-alt2"></span>
              </div>
              <div class="ive-fm-import-btn-wrap">
                <a id="ive-fm-import-template" href="javascript:void(0);">Import</a>
              </div>

              <div class="ive-fm-sidebar-content">
              	<a href="" class="ive-fm-go-pro-btn" target="_blank" style="display:none;">Go Pro</a>

                <div class="ive-pp-scrollable">
                  <h4 class="ive-template-name">Template Name</h4>
                	<div class="ive-fm-template-img">
                		<img src="" style="display:none;">
                	</div>
                	<div class="ive-fm-template-text" style="display:none;">
                		<p>description</p>
                	</div>
                  <div class="ive-bundle-text"></div>
                </div>

              </div>

              <div class="ive-fm-view-icons">
              	<ul>
              		<li class="ive-fm-desk-view active"><span class="ive-fm-view-icon dashicons dashicons-desktop"></span></li>
              		<li class="ive-fm-tab-view"><span class="ive-fm-view-icon dashicons dashicons-tablet"></span></li>
              		<li class="ive-fm-mob-view"><span class="ive-fm-view-icon dashicons dashicons-smartphone"></span></li>
              	</ul>
              </div>

            </div>

            <div class="ive-full-modal-iframe-wrap">
              <iframe width="100%" height="100%"></iframe>
            </div>
          </div>
        </div>

        <div class="ive-plugin-popup">
          <div class="ive-admin-modal">
            <button class="ive-close-button">×</button>
            <div class="ive-demo-step-container">
              <div class="ive-current-step">

                <div class="ive-demo-child ive-demo-step ive-demo-step-0 active">
                  <h2>Install Base Theme</h2>
                  <p>We strongly recommend to install the base theme.</p>
                  <div class="ive-checkbox-container">
                    Install Base Theme
                    <span class="ive-checkbox active">
                      <svg width="10" height="8" viewBox="0 0 11.2 9.1">
                        <polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
                      </svg>
                    </span>
                  </div>
                </div>

                <div class="ive-demo-plugins ive-demo-step ive-demo-step-1">
                  <h2>Install & Activate Plugins</h2>
                  <p>The following plugins are required for this template in order to work properly. Ignore if already installed.</p>
                  <div class="ive-checkbox-container activated">
                    Elementor
                    <span class="ive-checkbox active">
                      <svg width="10" height="8" viewBox="0 0 11.2 9.1">
                        <polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
                      </svg>
                    </span>
                  </div>
                  <div class="ive-checkbox-container">
                    Gutenberg
                    <span class="ive-checkbox active">
                      <svg width="10" height="8" viewBox="0 0 11.2 9.1">
                        <polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
                      </svg>
                    </span>
                  </div>
                </div>

                <div class="ive-demo-template ive-demo-step ive-demo-step-2">
                  <h2>Import Content</h2>
                  <p>This will import the template.</p>
                </div>

                <div class="ive-demo-install ive-demo-step ive-demo-step-3">
                  <h2>Installing...</h2>
                  <p>Please be patient and don't refresh this page, the import process may take a while, this also depends on your server.</p>
                  <div class="ive-progress-info">Required plugins<span>10%</span></div>
                  <div class="ive-installer-progress"><div></div></div>
                </div>

              </div>
              <div class="ive-demo-step-controls">
                <button class="ive-demo-btn ive-demo-back-btn">Back</button>
                <ul class="ive-steps-pills">
                  <li class="active">1</li>
                  <li class="">2</li>
                  <li class="">3</li>
                </ul>
                <button class="ive-demo-btn ive-demo-main-btn">Next</button>
              </div>
            </div>
          </div>
        </div>

    	</div>

    </div>
    <div class="ibtana--modal--loader">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
      <circle cx="50" cy="50" fill="none" stroke="#44a745" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
      </circle>
      </svg>
    </div>`;
    document.querySelector('body').appendChild(qtModal);
    qtModal.innerHTML = html;


    if ( ibtana_visual_editor_modal_js.post_type == "page" ) {
      get_modal_contents();
    }

    function get_ibtana_visual_editor_defaults() {
      $.ajax({
        method: "POST",
        url: ibtana_license_api_endpoint + "get_ibtana_visual_editor_defaults",
        // data: JSON.stringify(data_post),
        dataType: 'json',
        contentType: 'application/json',
      }).done(function( data ) {

        if ( data.data.hasOwnProperty('get_pro_permalink') ) {

          ibtana_visual_editor_modal_js.get_pro_permalink = data.data.get_pro_permalink;
          $( '.ive_trial_notice_right a' ).attr( 'href', data.data.get_pro_permalink );
          $( '#iepa_product_metabox_license_top a' ).attr( 'href', data.data.get_pro_permalink );
        }

        if ( data.data.hasOwnProperty( 'save_template_limit' ) ) {

          jQuery.post(
            ibtana_visual_editor_modal_js.adminAjax, {
              action:   'set_default_save_template_limit_info',
              save_template_limit:  data.data.save_template_limit,
              wpnonce: ibtana_visual_editor_modal_js.wpnonce
            },
            function( save_template_limit_info ) {

            }
          );
        }

        if ( data.data.hasOwnProperty( 'modal_banner_message' ) ) {
          if ( data.data.modal_banner_message != '' ) {
            $( '#myUpcomingModal .template-buy-banner' ).html( data.data.modal_banner_message );
          }
        }
      });
    }
    get_ibtana_visual_editor_defaults();


    function get_all__pages_list_by_template_type( search_key, next_page_number, will_clear = 1, template_type, product_category = null ) {


      var data_post = {
        "domain":             ibtana_visual_editor_modal_js.site_url,
        "limit":              9,
        "start":              next_page_number,
        "search":             search_key,
        "template_type":      template_type,
        "api_request":        'modal'
      };

      if ( product_category ) {
        data_post.is_premium  = product_category;
      }

      ajaxPost( ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + 'get_client_pages_list_by_template_type', data_post, function( data ) {

        // jQuery( '#free-template .sub-cats' ).hide();

        // Free and premium sub tabs
        $( '#free-template .sub-cats' ).empty();
        var data_sub_tabs = data.sub_tabs;

        for (var i = 0; i < data_sub_tabs.length; i++) {
          var data_sub_tab = data_sub_tabs[i];



          var data_sub_tab_name = 'Free';
          if ( data_sub_tab.is_premium == 1 ) {
            data_sub_tab_name = 'Premium';
          }



          if ( product_category && ( data_sub_tab.is_premium == product_category ) ) {
            $( '#free-template .sub-cats' ).append(
              `<button class="sub-cat-button active" data-product-category="` + data_sub_tab.is_premium + `">
                ` + data_sub_tab_name + `
                <span class="badge badge-info">` + data_sub_tab.template_count + `</span>
              </button>`
            );
          } else {
            $( '#free-template .sub-cats' ).append(
              `<button class="sub-cat-button" data-product-category="` + data_sub_tab.is_premium + `">
                ` + data_sub_tab_name + `
                <span class="badge badge-info">` + data_sub_tab.template_count + `</span>
              </button>`
            );
          }

        }
        // Free and premium sub tabs ends here


        if ( data.next_page_number ) {
          jQuery( '#free-template .load-more-btn' ).attr( 'data-next-page-number', data.next_page_number );
          jQuery( '#free-template .load-more-btn' ).show();
        } else {
          jQuery( '#free-template .load-more-btn' ).hide();
        }

        if ( will_clear === 1 ) {
          jQuery( '#free-template .ibtana-row.themes-box-wrap' ).empty();
        }

        var is_premium_theme_key_valid = data.is_key_valid;
        var template_with_inner_pages = data.data;

        // jQuery('#free-template .ibtana-row.themes-box-wrap').parent().addClass('ibtana-column-full');

        for ( var k = 0; k < template_with_inner_pages.length; k++ ) {
          var template_or_inner_page  = template_with_inner_pages[k];
          var template_or_inner_page_is_premium = parseInt(template_or_inner_page.is_premium);
          var premium_badge = ``;
          if ( template_or_inner_page_is_premium ) {
            premium_badge = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 76.65 100.86"><defs><style>.cls-1{fill:#1689c8;}.cls-2{font-size:25.18px;fill:#fff;font-family:Lato-Black, Lato;font-weight:800;}.cls-3{letter-spacing:-0.02em;}</style><linearGradient id="linear-gradient" x1="38.3" y1="4.1" x2="37.36" y2="184.18" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#330f48"/><stop offset="0.05" stop-color="#35134b"/><stop offset="0.28" stop-color="#3c1f53"/><stop offset="0.5" stop-color="#3e2356"/></linearGradient></defs><g id="Layer_2" data-name="Layer 2"><g id="Ñëîé_1" data-name="Ñëîé 1"><path class="cls-1" d="M76.65,0H0c.57,1.11,1,2,1.21,2.66a28.73,28.73,0,0,1,2.2,10.25V15.3h0v85.41c4-3.95,7.9-6.47,11.85-10.42l12,10.57,11.08-9.65,11.07,9.65,12-10.57c4,3.95,7.9,6.47,11.85,10.42V15.3h0c0-.79,0-1.59,0-2.38a28.73,28.73,0,0,1,2.2-10.25C75.69,2.05,76.08,1.12,76.65,0Z"/><text class="cls-2" transform="translate(12.17 59.06)">P<tspan class="cls-3" x="16.06" y="0">R</tspan><tspan x="32.18" y="0">O</tspan></text></g></g></svg>`;
          }
          jQuery( '#free-template .ibtana-row.themes-box-wrap' ).append(
            `<div class="ibtana-column-three ibtana--card" data-page-type="` + template_or_inner_page.page_type + `">
              <div class="blog-content-inner">
                `+premium_badge+`
                <div class="blog-content-img-inner free-content-inner">
                  <img class="blog-content-inner-image" src="` + template_or_inner_page.image + `">
                </div>
                <h2>`+template_or_inner_page.name+`</h2>
                <a class="blog-content-btn-inner preview-template" ive-template-text-domain="` + template_or_inner_page.domain + `" ive-template-type="` + template_or_inner_page.template_type + `" ive-is-premium="`+template_or_inner_page.is_premium+`" ive-template-slug="`+template_or_inner_page.slug+`">
                  PREVIEW
                  <span class="dashicons dashicons-welcome-view-site">
                  </span>
                </a>
              </div>
            </div>`
          );
        }

        if ( !template_with_inner_pages.length ) {
          $( '#free-template .ibtana-row.themes-box-wrap' ).append(
            '<h3 class="ive-coming-soon">No Results Found...</h3>'
          );
        }


      });
    }


    // On click free premium template tab
    $('#Templates').on( 'click', '.theme-tab-list-two', function() {
      $('.search-text').val('');
      var theme = $(this).attr('data-template');
      $('#Templates .theme-tab-list-two').removeClass('active');
      $(this).addClass('active');
      var mainTabId = $(this).closest('.tabcontent').attr('id');
      $('#' + mainTabId).find('.ibtana-theme-block').hide();
      $('#Templates').find('#'+theme).show();


      if ( $( this ).attr( 'data-template-type' ) !== undefined ) {
        if ( $(this).attr('data-template-type') == 'wordpress' ) {
          get_templates_list();
        } else {
          var data_template_type = $(this).attr('data-template-type');
          get_all__pages_list_by_template_type( '', 1, 1, data_template_type );
        }
      }
    });
    // On click free premium template tab END

    // On Click InnerPages Inner Tabs
    $('#InnerPages').on('click', '.theme-tab-list-two', function() {
      $('#InnerPages .theme-tab-list-two').removeClass('active');
      $(this).addClass('active');
      var inner_tab_name = $(this).attr('data-template-tab');
      $('#InnerPages .ibtana-theme-block').hide();
      $('#InnerPages .ibtana-theme-block[data-template-div="'+inner_tab_name+'"]').show();
    });
    // On Click InnerPages Inner Tabs END

    $('.ive-tablinks').on('click',function() {
      var mainTab = $(this).attr('data-tab-head');
      $('.ive-tablinks').removeClass('active');
      $(this).addClass('active');

      $('.tabcontent').hide();
      $('#'+mainTab).show();

      $('.search-text').val('');
      $( '#myUpcomingModal .sub-cat-button.active' ).removeClass( 'active' );

      if ( 'SavedTemplates' === mainTab ) {
        get_saved_ibtana_templates_by_terms();
      } else if ( 'Components' === mainTab ) {
        get_component_list();
      }
    });

    // Show Modal
    $(document.body).on('click', '#ibtana-modal-btn, .modal_btn_svg_icon', function() {
      $('#myUpcomingModal').show();
      if ( ( ibtana_visual_editor_modal_js.post_type === "product" ) && ( typeof iepaGlobal === "object" ) ) {
        jQuery( '[data-tab-head="Templates"]' ).trigger( 'click' );
        jQuery( '[data-template-type="woocommerce"]' ).trigger( 'click' );
      }
    });
    // Show Modal END

    // Hide modal
    $(document.body).on('click', '.CloseUpcomingModal', function() {
      $('#myUpcomingModal').hide();
    });
    // Hide modal END

    // On click subcategory
    $('#premium-template .sub-cats').on('click', '.sub-cat-button', function() {
      $('.sub-category-wrapper .sub-cat-button').removeClass('active');
      $(this).addClass("active");
      if ($(this).index() === 0) {
        $('#premium-template .ibtana-row.themes-box-wrap [data-id]').show();
      } else {
        var data_ids = $(this).attr('data-ids');
        var id_arr = data_ids.split(',');
        $('#premium-template .ibtana-row.themes-box-wrap [data-id]').hide();
        for (var i = 0; i < id_arr.length; i++) {
          var single_id = id_arr[i];
          $('#premium-template .ibtana-row.themes-box-wrap [data-id="'+single_id+'"]').show();
        }
      }
    });
    // On click subcategory END


    function get_saved_ibtana_templates_by_terms() {
      $('.ibtana--modal--loader').show();

      var data_to_send = {
        action:             'ive_get_saved_ibtana_templates_by_terms',
        wpnonce:            ibtana_visual_editor_modal_js.wpnonce
      };

      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax, data_to_send, function( saved_ibtana_templates ) {

          $('.ibtana--modal--loader').hide();

          // After Ajax Call
          $( '#SavedTemplates .inner-tab-content ul' ).empty();
          jQuery( '#SavedTemplates .ibtana-row.themes-box-wrap' ).empty();

          var ibtana_templates_response = saved_ibtana_templates.ibtana_templates_response;


          var ibtana_terms             = ibtana_templates_response.ibtana_terms;

          if ( ibtana_terms.length ) {
            for (var i = 0; i < ibtana_terms.length; i++) {
              var ibtana_term  = ibtana_terms[i];

              var ibtana_term_slug  = ibtana_term.slug.replace( /-/g, '_' );
              var sub_tab_heading   = ibtana_term.slug.replace( /-/g, ' ' ).replace( /ibtana /g, '' ).replace( /template/g, 'templates' );

              if ( i == 0 ) {
                $( '#SavedTemplates .inner-tab-content ul' ).append(
                  `<li class="theme-tab-list-two active" data-template-type="` + ibtana_term_slug + `">
                    <span>` + sub_tab_heading + `</span>
                  </li>`
                );
              } else {
                $( '#SavedTemplates .inner-tab-content ul' ).append(
                  `<li class="theme-tab-list-two" data-template-type="` + ibtana_term_slug + `">
                    <span>` + sub_tab_heading + `</span>
                  </li>`
                );
              }
            }
            jQuery( '#SavedTemplates .inner-tab-content ul' ).show();
          } else {
            jQuery( '#SavedTemplates .inner-tab-content ul' ).hide();
          }


          var is_iepa_license_activated = false;
          if ( typeof iepaGlobal != "undefined" ) {
            if ( iepaGlobal.iepa_license ) {
              if ( iepaGlobal.iepa_license.hasOwnProperty( 'license_status' ) ) {
                if ( iepaGlobal.iepa_license.license_status === true ) {
                  is_iepa_license_activated = true;
                }
              }
            }
          }


          // ibtana_products loop
          var ibtana_posts            = ibtana_templates_response.ibtana_posts;
          for ( var k = 0; k < ibtana_posts.length; k++ ) {
            var single_ibtana_template  = ibtana_posts[k];
            var single_ibtana_template_html = `<div class="ibtana-column-four ibtana--card">
              <div class="blog-content-inner">`;
                if ( is_iepa_license_activated ) {
                  single_ibtana_template_html += ` <a class="delete_saved_ibtana_template" post-id="` + single_ibtana_template.ID + `">
                    <span class="dashicons dashicons-dismiss">
                  </a>`;
                }
                single_ibtana_template_html += `<div class="blog-content-img-inner free-content-inner">
                  <img class="blog-content-inner-image" src="`+ibtana_visual_editor_modal_js.placeholder_image+`">
                </div>
                <h2>` + single_ibtana_template.post_title + `</h2>
                <a class="blog-content-btn-inner import_saved_ibtana_template" data-post-id="`+ single_ibtana_template.ID +`">
                  IMPORT
                  <span class="dashicons dashicons-welcome-view-site">
                  </span>
                </a>
                <a class="blog-content-btn-inner export_saved_ibtana_template" data-post-id="`+ single_ibtana_template.ID +`">
                  EXPORT
                  <span class="dashicons dashicons-database-export">
                  </span>
                </a>
                <a class="blog-content-btn-inner" href="` + ibtana_visual_editor_modal_js.adminUrl + `post.php?post=` + single_ibtana_template.ID + `&action=edit" target="_blank">
                  EDIT
                  <span class="dashicons dashicons-edit-page">
                  </span>
                </a>
              </div>
            </div>`;
            jQuery( '#SavedTemplates .ibtana-row.themes-box-wrap' ).append(
              single_ibtana_template_html
            );
          }

          if ( !ibtana_posts.length ) {
            $( '#SavedTemplates .ibtana-row.themes-box-wrap' ).append(
              '<h3 class="ive-coming-soon">No Result Found...</h3>'
            );
          }

          $( '.ive_trial_notice strong' ).text(
            ibtana_templates_response.saved_templates + `/` + ibtana_templates_response.save_templates_limit + ` Saves Remaining`
          );

          if ( !ibtana_templates_response.is_add_on_providing_template_limit ) {
            $( '.ive_trial_notice_right' ).css( 'display', 'inline-block' );
          } else {
            $( '.ive_trial_notice_right' ).hide();
          }

        }
      );
    }

    function ive_get_saved_ibtana_templates_by_term_slug( data_template_type ) {
      $('.ibtana--modal--loader').show();

      var data_to_send = {
        action:             'ive_get_saved_ibtana_templates_by_term_slug',
        term_slug:          data_template_type,
        wpnonce:            ibtana_visual_editor_modal_js.wpnonce
      };


      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax, data_to_send, function( saved_ibtana_templates_posts ) {

          $('.ibtana--modal--loader').hide();

          // After Ajax Call
          jQuery( '#SavedTemplates .ibtana-row.themes-box-wrap' ).empty();

          var ibtana_templates_response = saved_ibtana_templates_posts.ibtana_templates_response;


          var is_iepa_license_activated = false;
          if ( typeof iepaGlobal != "undefined" ) {
            if ( iepaGlobal.iepa_license ) {
        			if ( iepaGlobal.iepa_license.hasOwnProperty( 'license_status' ) ) {
        				if ( iepaGlobal.iepa_license.license_status === true ) {
        					is_iepa_license_activated = true;
        				}
        			}
        		}
          }


          for ( var k = 0; k < ibtana_templates_response.length; k++ ) {
            var single_ibtana_template  = ibtana_templates_response[k];
            var single_ibtana_template_html = `<div class="ibtana-column-four ibtana--card">
              <div class="blog-content-inner">`;
                if ( is_iepa_license_activated ) {
                  single_ibtana_template_html += ` <a class="delete_saved_ibtana_template" post-id="` + single_ibtana_template.ID + `">
                    <span class="dashicons dashicons-dismiss">
                  </a>`;
                }
                single_ibtana_template_html += `<div class="blog-content-img-inner free-content-inner">
                  <img class="blog-content-inner-image" src="`+ibtana_visual_editor_modal_js.placeholder_image+`">
                </div>
                <h2>` + single_ibtana_template.post_title + `</h2>
                <a class="blog-content-btn-inner import_saved_ibtana_template" data-post-id="`+ single_ibtana_template.ID +`">
                  IMPORT
                  <span class="dashicons dashicons-welcome-view-site">
                  </span>
                </a>
                <a class="blog-content-btn-inner export_saved_ibtana_template" data-post-id="`+ single_ibtana_template.ID +`">
                  EXPORT
                  <span class="dashicons dashicons-database-export">
                  </span>
                </a>
                <a class="blog-content-btn-inner" href="` + ibtana_visual_editor_modal_js.adminUrl + `post.php?post=` + single_ibtana_template.ID + `&action=edit" target="_blank">
                  EDIT
                  <span class="dashicons dashicons-edit-page">
                  </span>
                </a>
              </div>
            </div>`;
            jQuery( '#SavedTemplates .ibtana-row.themes-box-wrap' ).append(
              single_ibtana_template_html
            );
          }

          if ( !ibtana_templates_response.length ) {
            $( '#SavedTemplates .ibtana-row.themes-box-wrap' ).append(
              '<h3 class="ive-coming-soon">No Results Found...</h3>'
            );
          }

        }
      );
    }



    $( '#SavedTemplates .inner-tab-content ul' ).on( 'click', 'li[data-template-type]', function() {

      $( '#SavedTemplates .inner-tab-content ul li[data-template-type]' ).removeClass( 'active' );
      $( this ).addClass( 'active' );
      var data_template_type  = $( this ).attr( 'data-template-type' ).replace( /_/g, '-' );

      ive_get_saved_ibtana_templates_by_term_slug( data_template_type );
    } );


    $( '#SavedTemplates' ).on( 'click', '.import_saved_ibtana_template', function() {
      var post_id = $( this ).attr('data-post-id');
      $('.ibtana--modal--loader').show();
      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax, {
          action:   'ive_import_saved_single_ibtana_template',
          post_id:  post_id,
          page_id:  ibtana_visual_editor_modal_js.page_id,
          wpnonce:  ibtana_visual_editor_modal_js.wpnonce
        }, function( ive_saved_ibtana_template ) {
          if ( ive_saved_ibtana_template.status === false ) {
            // alert( ive_saved_ibtana_template.msg );
            IbtanaModal.Box.setup( 'Notice', ive_saved_ibtana_template.msg, false, false, function( name ) {});
            $('.ibtana--modal--loader').hide();
          } else {
            location.reload( true );
          }
        }
      );
    } );

    $( '#SavedTemplates' ).on( 'click', '.export_saved_ibtana_template', function() {
      var post_id = $( this ).attr('data-post-id');
      $('.ibtana--modal--loader').show();
      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax, {
          action:   'ive_export_saved_single_ibtana_template',
          post_id:  post_id,
          wpnonce:  ibtana_visual_editor_modal_js.wpnonce
        }, function( ive_saved_ibtana_template ) {

          if ( ive_saved_ibtana_template.status === false ) {
            // alert( ive_saved_ibtana_template.msg );
            IbtanaModal.Box.setup( 'Notice', ive_saved_ibtana_template.msg, false, false, function( name ) {});
          } else {
            var element = document.createElement('a');
            element.setAttribute(
              'href',
              'data:text/plain;charset=utf-8,' + encodeURIComponent( ive_saved_ibtana_template.post_content )
            );
            element.setAttribute( 'download', Math.random().toString(36).substr(2, 9) + '.txt' );
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
          }
          $('.ibtana--modal--loader').hide();

        }
      );
    });


    $( '#SavedTemplates' ).on( 'click', '.delete_saved_ibtana_template', function() {
      var $this_card  = $(this);
      var post_id = $( this ).attr('post-id');
      $('.ibtana--modal--loader').show();
      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax, {
          action:   'ive_delete_saved_single_ibtana_template',
          post_id:  post_id,
          wpnonce:  ibtana_visual_editor_modal_js.wpnonce
        }, function( ive_saved_ibtana_template ) {
          if ( ive_saved_ibtana_template.status === false ) {
            IbtanaModal.Box.setup( 'Notice', ive_saved_ibtana_template.msg, false, false, function( name ) {});
          } else {
            $( '.ive_trial_notice strong' ).text(
              ive_saved_ibtana_template.saved_templates + `/` + ive_saved_ibtana_template.save_templates_limit + ` Saves Remaining`
            );
            if ( !ive_saved_ibtana_template.is_add_on_providing_template_limit ) {
              $( '.ive_trial_notice_right' ).css( 'display', 'inline-block' );
            } else {
              $( '.ive_trial_notice_right' ).hide();
            }
            IbtanaModal.Box.setup( 'Success', ive_saved_ibtana_template.msg, false, false, function( name ) {});
            $this_card.closest( '.ibtana--card' ).remove();
          }
          $('.ibtana--modal--loader').hide();
        }
      );
    });



    $('#reload--modal--contents').on('click', function() {
      $('.search-text').val('');
      $( '#myUpcomingModal .sub-cat-button.active' ).removeClass( 'active' );

      if ( 'Templates' == get_ibtana_modal_main_tab() ) {
        if ( jQuery('#Templates .theme-tab-list-two.active').attr('data-template-type') === undefined ) {
          get_modal_contents();
        } else {
          jQuery( '#' + jQuery('.ive-tablinks.active').attr( 'data-tab-head' ) + ' .theme-tab-list-two.active' ).trigger('click');
        }
      } else if ( 'SavedTemplates' == get_ibtana_modal_main_tab() ) {
        get_saved_ibtana_templates_by_terms();
      } else if ( 'Components' == get_ibtana_modal_main_tab() ) {
        get_component_list();
      }
    });

    function get_templates_list( search_key = '', next_page_number = 1, will_clear = 1, template_type = 'wordpress', pro_cat = null ) {
      var data_post = {
        "theme_license_key":  ibtana_visual_editor_modal_js.admin_user_ibtana_license_key,
        "domain":             ibtana_visual_editor_modal_js.site_url,
        "theme_text_domain":  ibtana_visual_editor_modal_js.active_theme_text_domain,
        "limit":              9,
        "start":              next_page_number,
        "search":             search_key,
        "template_type":      template_type,
        "product_category":   pro_cat,
        "api_request":        'modal'
      };

      if ( ibtana_visual_editor_modal_js.custom_text_domain != "" ) {
        data_post.theme_text_domain = ibtana_visual_editor_modal_js.custom_text_domain;
      }

      ajaxPost( ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + 'get_client_template_list_product_cats', data_post, function( data ) {

        // Check if the product categories are created or not
        jQuery( '#free-template .sub-cats' ).show();
        var data_product_categories = data.product_categories;

        if ( ibtana_visual_editor_modal_js.are_product_categories_created === undefined ) {
          var previous_active_product_category = jQuery( '#free-template .sub-cats .sub-cat-button.active' ).attr( 'data-product-category' );

          $( '#free-template .sub-cats' ).empty();
          for (var i = 0; i < data_product_categories.length; i++) {
            var data_product_category = data_product_categories[i];

            if ( pro_cat && previous_active_product_category && ( previous_active_product_category == data_product_category.term_id ) ) {
              $( '#free-template .sub-cats' ).append(
                `<button class="sub-cat-button active" data-product-category="` + data_product_category.term_id + `">
                  ` + data_product_category.name + `
                  <span class="badge badge-info">` + data_product_category.product_category_tags_count + `</span>
                </button>`
              );
            } else {
              $( '#free-template .sub-cats' ).append(
                `<button class="sub-cat-button" data-product-category="` + data_product_category.term_id + `">
                  ` + data_product_category.name + `
                  <span class="badge badge-info">` + data_product_category.product_category_tags_count + `</span>
                </button>`
              );
            }

          }
          ibtana_visual_editor_modal_js.are_product_categories_created = true;
        }
        // Check if the product categories are created or not END

      });

      ajaxPost( ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + 'get_client_template_list_new', data_post, function( data ) {

        // Check if the tabs are already appended START
        var tabs  = data.tabs;

        if ( ibtana_visual_editor_modal_js.are_tabs_created === undefined ) {
          for (var i = 0; i < tabs.length; i++) {
            var tab  = tabs[i];
            if ( tab.option != 'wordpress' ) {
              jQuery( '#Templates .inner-tab-content ul' ).append(
                `<li class="theme-tab-list-two" data-template="free-template" data-template-type="`+tab.option+`">
                  <span>`+tab.display_string+`</span>
                </li>`
              );
            }
          }
          ibtana_visual_editor_modal_js.are_tabs_created  = true;
        }
        // Check if the tabs are already appended END

        // Check the post type and if it is a product then hide free and premium tabs.
        if ( "product" == ibtana_visual_editor_modal_js.post_type ) {
          jQuery( '#Templates .inner-tab-content ul .theme-tab-list-two' ).hide();
          jQuery( '#Templates .inner-tab-content ul .theme-tab-list-two[data-template-type="woocommerce"]' ).show();
          jQuery( '[data-template-type="woocommerce"]' ).trigger( 'click' );
          return;
        } else if ( "page" == ibtana_visual_editor_modal_js.post_type ) {
          jQuery( '#Templates .inner-tab-content ul .theme-tab-list-two[data-template-type="woocommerce"]' ).hide();
        }



        // // Check if the product categories are created or not
        // jQuery( '#free-template .sub-cats' ).show();
        // var data_product_categories = data.product_categories;
        // console.log( 'data', data );
        //
        // if ( ibtana_visual_editor_modal_js.are_product_categories_created === undefined ) {
        //   var previous_active_product_category = jQuery( '#free-template .sub-cats .sub-cat-button.active' ).attr( 'data-product-category' );
        //
        //   $( '#free-template .sub-cats' ).empty();
        //   for (var i = 0; i < data_product_categories.length; i++) {
        //     var data_product_category = data_product_categories[i];
        //
        //     if ( pro_cat && previous_active_product_category && ( previous_active_product_category == data_product_category.term_id ) ) {
        //       $( '#free-template .sub-cats' ).append(
        //         `<button class="sub-cat-button active" data-product-category="` + data_product_category.term_id + `">
        //           ` + data_product_category.name + `
        //           <span class="badge badge-info">` + data_product_category.product_category_tags_count + `</span>
        //         </button>`
        //       );
        //     } else {
        //       $( '#free-template .sub-cats' ).append(
        //         `<button class="sub-cat-button" data-product-category="` + data_product_category.term_id + `">
        //           ` + data_product_category.name + `
        //           <span class="badge badge-info">` + data_product_category.product_category_tags_count + `</span>
        //         </button>`
        //       );
        //     }
        //
        //   }
        //   ibtana_visual_editor_modal_js.are_product_categories_created = true;
        // }
        // // Check if the product categories are created or not END


        if ( will_clear ) {
          $( '#free-template .ibtana-row.themes-box-wrap' ).empty();
        }

        jQuery('#free-template .ibtana-row.themes-box-wrap').parent().removeClass('ibtana-column-full');

        var active_theme_data = data.active_theme_data;
        if ( data.active_theme_data ) {
          jQuery( '#free-template .ibtana-row.themes-box-wrap' ).append(
            `<div class="ibtana-column-three ibtana--card card-theme-active">
              <div class="blog-content-inner">
                <div class="blog-content-img-inner free-content-inner">
                  <img class="blog-content-inner-image" src="` + active_theme_data.image + `">
                </div>
                <h2>`+active_theme_data.name+`</h2>
                <a class="blog-content-btn-inner show-inner-pages" data-template-parent-reference="` + active_theme_data.parent_reference + `" data-text-domain="` + active_theme_data.domain + `" data-theme-slug="`+ active_theme_data.slug +`">
                  VIEW
                  <span class="dashicons dashicons-welcome-view-site">
                  </span>
                </a>
              </div>
            </div>`
          );
        }

        var free_data = data.data;
        if ( free_data ) {
          for (var i = 0; i < free_data.length; i++) {
            var free_data_single = free_data[i];

            var free_card_content = ``;
            // if (active_theme === free_data_single.domain) {
            //   free_card_content += `<div class="ibtana-column-three ibtana--card card-theme-active">`;
            // } else {
              free_card_content += `<div class="ibtana-column-three ibtana--card">`;
            // }

            free_card_content += `<div class="blog-content-inner">
                <div class="blog-content-img-inner free-content-inner">
                  <img class="blog-content-inner-image" src="` + free_data_single.image + `">
                </div>
                <h2>`+free_data_single.name+`</h2>
                <a class="blog-content-btn-inner show-inner-pages" data-template-parent-reference="` + free_data_single.parent_reference + `" data-text-domain="` + free_data_single.domain + `" data-theme-slug="`+ free_data_single.slug +`">
                  VIEW
                  <span class="dashicons dashicons-welcome-view-site">
                  </span>
                </a>
              </div>
            </div>`;
            // if (active_theme === free_data_single.domain) {
            //   $(free_card_content).prependTo('#free-template .ibtana-row.themes-box-wrap');
            // } else {
              $( '#free-template .ibtana-row.themes-box-wrap' ).append(free_card_content);
            // }
          }
        }
        // Free cards END

        // Load more button next page number START
        if ( data.next_page_number ) {
          jQuery( '#free-template .load-more-btn' ).attr( 'data-next-page-number', data.next_page_number );
          jQuery( '#free-template .load-more-btn' ).show();
        } else {
          jQuery( '#free-template .load-more-btn' ).hide();
        }
        // Load more button next page number END

      });
    }
    get_templates_list();


    function get_component_list( search_key = '', next_page_number = 1, will_clear = 1, template_type = '' ) {
      var data_post = {
        "limit":              9,
        "start":              next_page_number,
        "search":             search_key,
        "component_type":     template_type
      };

      ajaxPost( ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + 'get_client_component_list', data_post, function( data ) {

        // Check if the product categories are created or not
        jQuery( '#Components .sub-cats' ).show();
        var data_component_types = data.component_types;


        if ( ibtana_visual_editor_modal_js.are_component_categories_created === undefined ) {
          var previously_active_component_type = jQuery( '#Components .sub-cats .sub-cat-button.active' ).attr( 'data-product-category' );


          $( '#Components .sub-cats' ).empty();
          for (var i = 0; i < data_component_types.length; i++) {
            var data_component_type = data_component_types[i];

            if ( previously_active_component_type && ( previously_active_component_type == data_component_type.option ) ) {
              $( '#Components .sub-cats' ).append(
                `<button class="sub-cat-button active" data-product-category="` + data_component_type.option + `">
                  ` + data_component_type.display_string + `
                  <span class="badge badge-info">` + data_component_type.component_count + `</span>
                </button>`
              );
            } else {
              $( '#Components .sub-cats' ).append(
                `<button class="sub-cat-button" data-product-category="` + data_component_type.option + `">
                  ` + data_component_type.display_string + `
                  <span class="badge badge-info">` + data_component_type.component_count + `</span>
                </button>`
              );
            }

          }
          ibtana_visual_editor_modal_js.are_component_categories_created = true;
        }
        // Check if the product categories are created or not END


        if ( will_clear ) {
          $( '#Components .ibtana-row.themes-box-wrap' ).empty();
        }



        var free_data = data.data;
        if ( free_data ) {
          for ( var i = 0; i < free_data.length; i++ ) {
            var free_data_single = free_data[i];

            var component_is_premium = parseInt( free_data_single.plan_type );
            var premium_badge = ``;
            if ( component_is_premium ) {
              premium_badge = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 76.65 100.86"><defs><style>.cls-1{fill:#1689c8;}.cls-2{font-size:25.18px;fill:#fff;font-family:Lato-Black, Lato;font-weight:800;}.cls-3{letter-spacing:-0.02em;}</style><linearGradient id="linear-gradient" x1="38.3" y1="4.1" x2="37.36" y2="184.18" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#330f48"/><stop offset="0.05" stop-color="#35134b"/><stop offset="0.28" stop-color="#3c1f53"/><stop offset="0.5" stop-color="#3e2356"/></linearGradient></defs><g id="Layer_2" data-name="Layer 2"><g id="Ñëîé_1" data-name="Ñëîé 1"><path class="cls-1" d="M76.65,0H0c.57,1.11,1,2,1.21,2.66a28.73,28.73,0,0,1,2.2,10.25V15.3h0v85.41c4-3.95,7.9-6.47,11.85-10.42l12,10.57,11.08-9.65,11.07,9.65,12-10.57c4,3.95,7.9,6.47,11.85,10.42V15.3h0c0-.79,0-1.59,0-2.38a28.73,28.73,0,0,1,2.2-10.25C75.69,2.05,76.08,1.12,76.65,0Z"/><text class="cls-2" transform="translate(12.17 59.06)">P<tspan class="cls-3" x="16.06" y="0">R</tspan><tspan x="32.18" y="0">O</tspan></text></g></g></svg>`;
            }

            var free_card_content = ``;
            free_card_content += `
            <div class="ibtana-column-three ibtana--card">
              <div class="blog-content-inner">
                `+premium_badge+`
                <div class="blog-content-img-inner free-content-inner">
                  <img class="blog-content-inner-image" src="` + free_data_single.image_path + `">
                </div>
                <h2>` + free_data_single.name + `</h2>
                <a class="blog-content-btn-inner preview-template" ive-component-type="` + free_data_single.component_type + `" ive-is-premium="` + free_data_single.plan_type + `" ive-template-slug="` + free_data_single.slug + `">
                  PREVIEW
                  <span class="dashicons dashicons-welcome-view-site">
                  </span>
                </a>
              </div>
            </div>`;
            $( '#Components .ibtana-row.themes-box-wrap' ).append( free_card_content );
          }
        }
        // Free cards END

        // Load more button next page number START
        if ( data.next_page_number ) {
          jQuery( '#Components .load-more-btn' ).attr( 'data-next-page-number', data.next_page_number );
          jQuery( '#Components .load-more-btn' ).show();
        } else {
          jQuery( '#Components .load-more-btn' ).hide();
        }
        // Load more button next page number END

      });
    }
    get_component_list();

    function get_ibtana_modal_main_tab() {
      var main_tab = jQuery( '#myUpcomingModal .ive-tablinks.active' ).attr( 'data-tab-head' );
      return main_tab;
    }

    // Search text
    $( '.search-text' ).on('input', function() {

      var search_keyword = $(this).val().toLowerCase().trim();

      if ( 'Templates' == get_ibtana_modal_main_tab() ) {
        if ( jQuery('#Templates .inner-tab-content li.active').attr('data-template-type') === undefined ) {
          var active_sub_cat = $('#premium-template .sub-cat-button.active');
          var visible_wrapper = $('.content-modal .ibtana-row.themes-box-wrap:visible');
          if (active_sub_cat.length != 0) {
            var sub_cat_pro_ids = active_sub_cat.attr('data-ids');
            var sub_cat_arr_ids = sub_cat_pro_ids.split(',');
            $('#premium-template [data-id]').hide();
            for (var i = 0; i < sub_cat_arr_ids.length; i++) {
              var sub_cat_pro_id = sub_cat_arr_ids[i];
              var pro_card = $('#premium-template [data-id='+sub_cat_pro_id+']');
              var pro_card_text = pro_card.find('h2').text().toLowerCase();
              if (pro_card_text.indexOf(search_keyword) !== -1) {
                pro_card.show();
              }
            }
          } else {
            visible_wrapper.find('.ibtana--card').hide();
            var pro_cards = visible_wrapper.find('.ibtana--card');
            $.each(pro_cards, function(key, pro_card) {
              pro_card_text = $(pro_card).find('h2').text().toLowerCase();
              if (pro_card_text.indexOf(search_keyword) !== -1) {
                $(pro_card).show();
              }
            });
          }

        } else {
          var data_template_type = $('#Templates .theme-tab-list-two.active').attr('data-template-type');
          var product_category = jQuery('#free-template .sub-cat-button.active').attr('data-product-category');
          if ( !product_category ) {
            product_category = null;
          }
          if ( data_template_type == 'wordpress' ) {
            get_templates_list(
              search_keyword,
              1,
              1,
              'wordpress',
              product_category
            );
          } else {
            get_all__pages_list_by_template_type( search_keyword, 1, 1, data_template_type, product_category );
          }
        }


      } else if ( 'Components' == get_ibtana_modal_main_tab() ) {
        var component_type  = $( '#Components .sub-cat-button.active' ).attr( 'data-product-category' );
        get_component_list( search_keyword, 1, 1, component_type );
      }




    });
    // Search text END

    $( '#free-template' ).on( 'click', '.sub-cat-button', function() {
      $( '#free-template .sub-cat-button' ).removeClass( 'active' );
      $( this ).addClass( 'active' );
      var product_category  = $( this ).attr( 'data-product-category' );
      var search_keyword = $('.search-text').val().toLowerCase().trim();

      var data_template_type = $('#Templates .theme-tab-list-two.active').attr('data-template-type');

      if ( data_template_type == 'wordpress' ) {
        get_templates_list(
          search_keyword,
          1,
          1,
          'wordpress',
          product_category
        );
      } else {
        get_all__pages_list_by_template_type( search_keyword, 1, 1, data_template_type, product_category );
      }
    });

    $( '#Components' ).on( 'click', '.sub-cat-button', function() {
      $( '#Components .sub-cat-button' ).removeClass( 'active' );
      $( this ).addClass( 'active' );
      var component_type  = $( this ).attr( 'data-product-category' );
      var search_keyword = $('.search-text').val().toLowerCase().trim();

      get_component_list( search_keyword, 1, 1, component_type );
    } );

    jQuery( '#free-template .load-more-btn' ).click(function() {
      var page_no = parseInt( jQuery(this).attr( 'data-next-page-number' ) );
      var search_keyword = $('.search-text').val().toLowerCase().trim();

      var data_template_type = $('#Templates .theme-tab-list-two.active').attr('data-template-type');
      var product_category = jQuery('#free-template .sub-cat-button.active').attr('data-product-category');
      if ( !product_category ) {
        product_category = null;
      }

      if ( data_template_type == 'wordpress' ) {
        get_templates_list(
          search_keyword,
          page_no,
          0,
          'wordpress',
          product_category
        );
      } else {
        get_all__pages_list_by_template_type( search_keyword, page_no, 0, data_template_type, product_category );
      }
    });

    jQuery( '#Components .load-more-btn' ).click( function() {
      var page_no = parseInt( jQuery(this).attr( 'data-next-page-number' ) );
      var search_keyword = $('.search-text').val().toLowerCase().trim();

      var component_type = jQuery( '#Components .sub-cat-button.active' ).attr( 'data-product-category' );
      if ( !component_type ) {
        component_type = '';
      }

      get_component_list(
        search_keyword,
        page_no,
        0,
        component_type
      );

    } );

    function get_inner_pages_list( parent_reference ) {
      var data_post_inner = {
        parent_reference:   parent_reference,
        domain:             ibtana_visual_editor_modal_js.site_url,
        theme_license_key:  ibtana_visual_editor_modal_js.admin_user_ibtana_license_key,
        theme_text_domain:  ibtana_visual_editor_modal_js.themedomain
      };

      ajaxPost(
        ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT+'get_client_inner_pages_list',
        data_post_inner,
        function( data ) {

          // Create page types
          var page_types  = data.page_types;
          jQuery('#InnerPages .sub-cats').empty();
          for (var i = 0; i < page_types.length; i++) {
            var page_type = page_types[i];
            if ( page_type.page_type == 'template' ) {
              jQuery(
                `<button class="sub-cat-button" data-page-type="`+page_type.page_type+`">
                  `+page_type.display_string+`
                  <span class="badge badge-info">`+page_type.count+`</span>
                </button>`
              ).prependTo( '#InnerPages .sub-cats' );
            } else {
              jQuery('#InnerPages .sub-cats').append(
                `<button class="sub-cat-button" data-page-type="`+page_type.page_type+`">
                  `+page_type.display_string+`
                  <span class="badge badge-info">`+page_type.count+`</span>
                </button>`
              );
            }
          }
          // End of page types


          var is_premium_theme_key_valid = data.is_key_valid;
          var template_with_inner_pages = data.data;
          jQuery( '#InnerPages .ibtana-row.themes-box-wrap' ).empty();
          for ( var k = 0; k < template_with_inner_pages.length; k++ ) {
            var template_or_inner_page  = template_with_inner_pages[k];
            var template_or_inner_page_is_premium = parseInt(template_or_inner_page.is_premium);
            var premium_badge = ``;
            if ( template_or_inner_page_is_premium ) {
              premium_badge = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 76.65 100.86"><defs><style>.cls-1{fill:#1689c8;}.cls-2{font-size:25.18px;fill:#fff;font-family:Lato-Black, Lato;font-weight:800;}.cls-3{letter-spacing:-0.02em;}</style><linearGradient id="linear-gradient" x1="38.3" y1="4.1" x2="37.36" y2="184.18" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#330f48"/><stop offset="0.05" stop-color="#35134b"/><stop offset="0.28" stop-color="#3c1f53"/><stop offset="0.5" stop-color="#3e2356"/></linearGradient></defs><g id="Layer_2" data-name="Layer 2"><g id="Ñëîé_1" data-name="Ñëîé 1"><path class="cls-1" d="M76.65,0H0c.57,1.11,1,2,1.21,2.66a28.73,28.73,0,0,1,2.2,10.25V15.3h0v85.41c4-3.95,7.9-6.47,11.85-10.42l12,10.57,11.08-9.65,11.07,9.65,12-10.57c4,3.95,7.9,6.47,11.85,10.42V15.3h0c0-.79,0-1.59,0-2.38a28.73,28.73,0,0,1,2.2-10.25C75.69,2.05,76.08,1.12,76.65,0Z"/><text class="cls-2" transform="translate(12.17 59.06)">P<tspan class="cls-3" x="16.06" y="0">R</tspan><tspan x="32.18" y="0">O</tspan></text></g></g></svg>`;
            }
            jQuery( '#InnerPages .ibtana-row.themes-box-wrap' ).append(
              `<div class="ibtana-column-three ibtana--card" data-page-type="` + template_or_inner_page.page_type + `">
                <div class="blog-content-inner">
                  `+premium_badge+`
                  <div class="blog-content-img-inner free-content-inner">
                    <img class="blog-content-inner-image" src="` + template_or_inner_page.image + `">
                  </div>
                  <h2>`+template_or_inner_page.name+`</h2>
                  <a class="blog-content-btn-inner preview-template" ive-is-premium-theme-key-valid="`+is_premium_theme_key_valid+`" ive-template-text-domain="` + template_or_inner_page.domain + `" ive-template-type="` + template_or_inner_page.template_type + `" ive-is-premium="`+template_or_inner_page.is_premium+`" ive-template-slug="`+template_or_inner_page.slug+`">
                    PREVIEW
                    <span class="dashicons dashicons-welcome-view-site">
                    </span>
                  </a>
                </div>
              </div>`
            );
          }
          jQuery( '#InnerPages .sub-cats button[data-page-type]:first' ).trigger( 'click' );
        }
      );

    }

    $( '#InnerPages .sub-cats' ).on( 'click', 'button[data-page-type]', function() {
      var $this           = jQuery( this );
      $( '#InnerPages .sub-cats button[data-page-type]' ).removeClass( 'active' );
      $this.addClass( 'active' );
      var data_page_type  = $this.attr( 'data-page-type' );
      if ( !data_page_type ) {
        jQuery( '#InnerPages .ibtana-row.themes-box-wrap .ibtana--card' ).show();
      } else {
        jQuery( '#InnerPages .ibtana-row.themes-box-wrap .ibtana--card' ).hide();
        jQuery( '#InnerPages .ibtana-row.themes-box-wrap .ibtana--card[data-page-type="'+data_page_type+'"]' ).show();
      }
    });

    $( '#free-template' ).on( 'click', '.show-inner-pages', function() {
      var data_template_parent_reference = $(this).attr('data-template-parent-reference');

      $( '.back-to-templates' ).attr( 'data_template_parent_reference', data_template_parent_reference );

      $( '.tabcontent' ).hide();
      $( '.modal-content-reload-svg' ).hide();
      $( '#InnerPages' ).show();
      get_inner_pages_list(data_template_parent_reference);
    });

    $( '.back-to-templates' ).on( 'click', function() {
      $( '.tabcontent' ).hide();
      $( '#Templates' ).show();
      $( '.modal-content-reload-svg' ).show();

      var data_template_parent_reference = jQuery( '.back-to-templates' ).attr( 'data_template_parent_reference' );

      if ( data_template_parent_reference ) {

        jQuery( '.UpcomingModal-content' ).animate(
          {
            scrollTop: jQuery( '#free-template .show-inner-pages[data-template-parent-reference="'+data_template_parent_reference+'"]' ).closest( '.ibtana--card' ).offset().top
          },
          500
        );
      }
    });

    $( '#free-template, #InnerPages' ).on( 'click', '.preview-template', function() {
      $( '#fullSizeModal' ).show();
      ibtana_visual_editor_setup_preview_popup( $( this ) );
    } );

    $( '#Components' ).on( 'click', '.preview-template', function() {
      $( '#fullSizeModal' ).show();
      ibtana_visual_editor_setup_component_preview_popup( $( this ) );
    } );

    /* --------- Responsive Template View --------- */
    jQuery( '.ive-fm-desk-view, .ive-fm-tab-view, .ive-fm-mob-view' ).on('click', function() {
      $( '.ive-fm-view-icons li' ).removeClass( 'active' );
      $( this ).addClass( 'active' );
      if ( $(this).hasClass('ive-fm-desk-view') ) {
        jQuery('.ive-full-modal-iframe-wrap iframe').css("width", "100%");
      } else if ( $(this).hasClass('ive-fm-tab-view') ) {
        jQuery('.ive-full-modal-iframe-wrap iframe').css("width", "772px");
      } else if ( $(this).hasClass('ive-fm-mob-view') ) {
        jQuery('.ive-full-modal-iframe-wrap iframe').css("width", "356px");
      }
    });

    $( '.ive-fm-collapse-btn' ).on( 'click', function() {
      if ( !$(this).hasClass('ive-fm-btn-rotate') ) {
        $( this ).addClass( 'ive-fm-btn-rotate' );
        $( '.ive-full-modal-import-sidebar' ).addClass( 'collapse' );
        $( '.ive-full-modal-iframe-wrap' ).addClass( 'fullwidth' );
      } else {
        $( this ).removeClass( 'ive-fm-btn-rotate' );
        $( '.ive-full-modal-import-sidebar' ).removeClass( 'collapse' );
        $( '.ive-full-modal-iframe-wrap' ).removeClass( 'fullwidth' );
      }
    });

    $( '.ive-fm-close' ).on( 'click', function() {
      jQuery( '.ive-full-modal-iframe-wrap iframe' ).attr( 'src', '' );
      $( '#fullSizeModal' ).hide();
    });

    function ibtana_visual_editor_setup_preview_popup( $this ) {


      jQuery( '.ive-fm-import-btn-wrap a' ).hide();
      jQuery( '.ive-fm-go-pro-btn' ).hide();
      jQuery( '.ive-fm-sidebar-content .ive-template-name' ).hide();
      jQuery( '.ive-fm-template-img img' ).hide();
      jQuery( '.ive-fm-template-text' ).hide();
      jQuery( '.ive-bundle-text' ).hide();
      jQuery( '.ive-pp-scrollable .ive-required-plugin' ).remove();


      var is_demo_premium_template  = parseInt( jQuery($this).attr('ive-is-premium') );
      var ive_template_type         = jQuery($this).attr( 'ive-template-type' );
      var demo_slug                 = jQuery($this).attr( 'ive-template-slug' );

      jQuery( '.ibtana--modal--loader' ).show();

      var data_to_send  = {
        site_url:       ibtana_visual_editor_modal_js.site_url,
        template_slug:  demo_slug
      };

      if ( is_demo_premium_template == 1 ) {

        if ( ive_template_type == 'wordpress' ) {
          data_to_send.text_domain    = ibtana_visual_editor_modal_js.themedomain;
          data_to_send.license_key    = ibtana_visual_editor_modal_js.admin_user_ibtana_license_key;
          data_to_send.template_type  = ive_template_type;
        } else if ( ive_template_type == 'woocommerce' ) {
          if ( ibtana_visual_editor_modal_js.ive_add_on_keys ) {
            if ( ibtana_visual_editor_modal_js.ive_add_on_keys.hasOwnProperty( 'ibtana_ecommerce_product_addons_license_key' ) ) {
              if ( ibtana_visual_editor_modal_js.ive_add_on_keys.ibtana_ecommerce_product_addons_license_key.hasOwnProperty( 'license_key' ) ) {
                data_to_send.text_domain    = "ibtana-ecommerce-product-addons";
                data_to_send.license_key    = ibtana_visual_editor_modal_js.ive_add_on_keys.ibtana_ecommerce_product_addons_license_key.license_key;
                data_to_send.template_type  = ive_template_type;
              }
            }
          }
        }
      }

      jQuery.ajax({
        method: "POST",
        url: ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + "get_client_page_info_for_import",
        data: JSON.stringify(data_to_send),
        dataType: 'json',
        contentType: 'application/json',
      }).done( function( data ) {
        jQuery( '.ibtana--modal--loader' ).hide();

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-content-type', 'template' );

        var current_theme         = ibtana_visual_editor_modal_js.custom_text_domain;
        var demo_url              = data.data.demo_url;
        var demo_image            = data.data.image;
        var demo_title            = data.data.name;
        var demo_permalink        = data.data.permalink;
        var template_text_domain  = data.data.domain;
        var demo_description      = data.data.description;
        var data_template_type    = data.data.template_type;

        var is_premium__key_valid  = data.is_key_valid;

        jQuery( '.ive-fm-import-btn-wrap a' ).removeClass( 'ive-install-plugin' );
        jQuery( '.ive-fm-sidebar-content .ive-required-plugin' ).remove();

        if ( is_demo_premium_template === 1 ) {
          jQuery('.ive-fm-import-btn-wrap a').text( 'Premium Import' );
          jQuery('.ive-fm-import-btn-wrap a').attr( 'ive-is-premium', 1 );
        } else {
          jQuery('.ive-fm-import-btn-wrap a').text( 'Free Import' );
          jQuery('.ive-fm-import-btn-wrap a').attr( 'ive-is-premium', 0 );


          var unavailable_plugins = 0;

          // If it is a product page
          if ( data_template_type == 'woocommerce' ) {

            var required_plugins_html = ``;

            // Check if the WooCommerce is active
            if ( !Boolean( parseInt( ibtana_visual_editor_modal_js.is_woocommerce_available ) ) ) {
              ++unavailable_plugins;
              required_plugins_html += `<div data-slug="woocommerce" data-file="woocommerce.php">
                                          <span class="dashicons dashicons-no-alt"></span>WooCommerce
                                        </div>`;
            } else {
              required_plugins_html += `<div><span class="dashicons dashicons-yes"></span>WooCommerce</div>`;
            }

            // Check if the woo addon is active.
            if ( !ibtana_visual_editor_modal_js.ive_add_on_keys.hasOwnProperty( 'ibtana_ecommerce_product_addons_license_key' ) ) {
              ++unavailable_plugins;
              required_plugins_html += `<div data-slug="ibtana-ecommerce-product-addons" data-file="plugin.php">
                                          <span class="dashicons dashicons-no-alt"></span>Ibtana - Ecommerce Product Addons
                                        </div>`;
            } else {
              required_plugins_html += `<div><span class="dashicons dashicons-yes"></span>Ibtana - Ecommerce Product Addons</div>`;
            }

            if ( unavailable_plugins ) {
              jQuery( '.ive-fm-import-btn-wrap a' ).text( 'Install & Activate Plugin' );
              jQuery( '.ive-fm-import-btn-wrap a' ).addClass( 'ive-install-plugin' );
            }
            jQuery( '.ive-fm-sidebar-content .ive-pp-scrollable' ).append(
              `<div class="ive-required-plugin">
                <p>Required Plugins</p>
                ` + required_plugins_html + `
              </div>`
            );
          }

        }

        var ive_template_page_type    = data.data.page_type;
        var ive_template_text_domain  = jQuery($this).attr( 'ive-template-text-domain' );

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-type', ive_template_type );

        if ( ive_template_type == 'wordpress' ) {
          if( is_demo_premium_template == 1 && is_premium__key_valid == 1 && current_theme == ive_template_text_domain ) {
            jQuery('.ive-fm-import-btn-wrap a').css( 'display', 'block' );
          } else if( !is_demo_premium_template || is_demo_premium_template == 0 ) {
            jQuery('.ive-fm-import-btn-wrap a').css( 'display', 'block' );
          } else {
            jQuery('.ive-fm-import-btn-wrap a').hide();
          }
        } else {
          // Condition for the other template types.
          if ( ( is_demo_premium_template == 1 ) && ( is_premium__key_valid == 1 ) ) {
            jQuery('.ive-fm-import-btn-wrap a').css( 'display', 'block' );
          } else if ( is_demo_premium_template == 0 ) {
            jQuery('.ive-fm-import-btn-wrap a').css( 'display', 'block' );
          } else {
            jQuery('.ive-fm-import-btn-wrap a').hide();
          }
        }

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-page-type', ive_template_page_type );
        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-page-title', demo_title );
        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-text-domain', ive_template_text_domain );

        jQuery( '.ive-fm-template-img img' ).show();
        jQuery( '.ive-fm-template-img img' ).attr( 'src', demo_image );

        jQuery( '.ive-fm-sidebar-content .ive-template-name' ).show();
        jQuery( '.ive-fm-sidebar-content .ive-template-name' ).text( demo_title );

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-slug', demo_slug );
        jQuery( '.ive-full-modal-iframe-wrap iframe' ).attr( 'src', demo_url );



        jQuery( '.ive-fm-template-text' ).show();
        jQuery( '.ive-fm-template-text p' ).text( demo_description );

        if ( data.data.hasOwnProperty( 'bundle_text_message' ) ) {
          if ( data.data.bundle_text_message != "" ) {
            jQuery( '.ive-bundle-text' ).html( data.data.bundle_text_message );
            jQuery( '.ive-bundle-text' ).show();
          } else {
            jQuery( '.ive-bundle-text' ).hide();
          }
        } else {
          jQuery( '.ive-bundle-text' ).hide();
        }

        jQuery( '.ive-fm-go-pro-btn' ).show();


        if (
          ( jQuery( '.card-theme-active a[data-text-domain]' ).attr('data-text-domain') == jQuery('#InnerPages .ibtana--card a[ive-is-premium-theme-key-valid]').attr('ive-template-text-domain') ) &&
          ( jQuery('#InnerPages .ibtana--card a[ive-is-premium-theme-key-valid]').attr('ive-is-premium-theme-key-valid') == "1" ) &&
          data_template_type == 'wordpress'
        ) {
          jQuery( '.ive-fm-go-pro-btn' ).attr(
            'href', "https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true"
          );
          jQuery( '.ive-fm-go-pro-btn' ).text( 'Upgrade To Bundle' );
        } else {
          jQuery( '.ive-fm-go-pro-btn' ).attr( 'href', demo_permalink );
          jQuery( '.ive-fm-go-pro-btn' ).text( 'Go Pro' );
        }




        // Template Base Theme Condition in step popup
        jQuery( '.ive-demo-child .ive-checkbox-container' ).remove();
        if ( data_template_type != 'wordpress' ) {
          jQuery( '.ive-demo-child p' ).text( 'No base theme installation is required!' );
        } else {
          jQuery( '.ive-demo-child p' ).text( 'We strongly recommend to install the base theme.' );
          jQuery( '.ive-demo-child' ).append(
            `<div class="ive-checkbox-container">
              Install Base Theme
              <span class="ive-checkbox active">
                <svg width="10" height="8" viewBox="0 0 11.2 9.1">
                  <polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
                </svg>
              </span>
            </div>`
          );
        }

        // Setup Plugins in step popup
        var template_plugins = data.data.template_plugins;
        jQuery( '.ive-demo-plugins' ).find( '.ive-checkbox-container' ).remove();
        if ( !template_plugins.length ) {
          jQuery( '.ive-demo-plugins p' ).text( 'No plugin installation is required!' );
        } else {
          // Append plugin data to the step popup
          jQuery( '.ive-demo-plugins p' ).text( 'The following plugins are required for this template in order to work properly. Ignore if already installed.' );
          for (var i = 0; i < template_plugins.length; i++) {
            var template_plugin = template_plugins[i];
            jQuery('.ive-demo-plugins').append(
              `<div class="ive-checkbox-container" ive-plugin-text-domain="` + template_plugin.plugin_text_domain + `" ive-plugin-main-file="` + template_plugin.plugin_main_file + `" ive-plugin-url="` + template_plugin.plugin_url + `">
                ` + template_plugin.plugin_title + `
                <span class="ive-checkbox active">
                  <svg width="10" height="8" viewBox="0 0 11.2 9.1">
                    <polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
                  </svg>
                </span>
              </div>`
            );
          }
        }

      });
    }

    function ibtana_visual_editor_setup_component_preview_popup( $this ) {

      var is_demo_premium_template  = parseInt( jQuery($this).attr( 'ive-is-premium' ) );


      var ive_template_type         = jQuery( $this ).attr( 'ive-component-type' );
      var demo_slug                 = jQuery( $this ).attr('ive-template-slug');

      jQuery( '.ibtana--modal--loader' ).show();

      var data_to_send  = {
        site_url:       ibtana_visual_editor_modal_js.site_url,
        component_slug:  demo_slug
      };

      if ( is_demo_premium_template == 1 ) {

        // if ( ive_template_type == 'wordpress' ) {
        //   data_to_send.text_domain    = ibtana_visual_editor_modal_js.themedomain;
        //   data_to_send.license_key    = ibtana_visual_editor_modal_js.admin_user_ibtana_license_key;
        //   data_to_send.template_type  = ive_template_type;
        // } else if ( ive_template_type == 'woocommerce' ) {
          if ( ibtana_visual_editor_modal_js.ive_add_on_keys ) {
            if ( ibtana_visual_editor_modal_js.ive_add_on_keys.hasOwnProperty( 'ibtana_ecommerce_product_addons_license_key' ) ) {
              if ( ibtana_visual_editor_modal_js.ive_add_on_keys.ibtana_ecommerce_product_addons_license_key.hasOwnProperty( 'license_key' ) ) {
                data_to_send.text_domain    = "ibtana-ecommerce-product-addons";
                data_to_send.license_key    = ibtana_visual_editor_modal_js.ive_add_on_keys.ibtana_ecommerce_product_addons_license_key.license_key;
                data_to_send.component_type  = ive_template_type;
              }
            }
          }
        // }
      }

      jQuery.ajax( {
        method: "POST",
        url: ibtana_visual_editor_modal_js.IBTANA_LICENSE_API_ENDPOINT + "get_client_component_info_for_import",
        data: JSON.stringify(data_to_send),
        dataType: 'json',
        contentType: 'application/json',
      } ).done( function( data ) {
        jQuery( '.ibtana--modal--loader' ).hide();

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-content-type', 'component' );

        var demo_url              = data.data.demo_url;
        var demo_image            = data.data.image_path;
        var demo_title            = data.data.name;
        var demo_permalink        = data.data.permalink;
        var demo_description      = data.data.description;
        var data_template_type    = data.data.template_type;

        var is_premium__key_valid  = data.is_key_valid;

        jQuery( '.ive-fm-import-btn-wrap a' ).removeClass( 'ive-install-plugin' );
        jQuery( '.ive-fm-sidebar-content .ive-required-plugin' ).remove();

        if ( is_demo_premium_template === 1 ) {
          jQuery('.ive-fm-import-btn-wrap a').text( 'Premium Import' );
          jQuery('.ive-fm-import-btn-wrap a').attr( 'ive-is-premium', 1 );
        } else {
          jQuery('.ive-fm-import-btn-wrap a').text( 'Free Import' );
          jQuery('.ive-fm-import-btn-wrap a').attr( 'ive-is-premium', 0 );
        }

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-type', ive_template_type );


        if ( ( is_demo_premium_template == 1 ) && ( is_premium__key_valid == 1 ) ) {
          jQuery('.ive-fm-import-btn-wrap a').css( 'display', 'block' );
        } else if( !is_demo_premium_template || is_demo_premium_template == 0 ) {
          jQuery('.ive-fm-import-btn-wrap a').css( 'display', 'block' );
        } else {
          jQuery('.ive-fm-import-btn-wrap a').hide();
        }

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-page-title', demo_title );

        jQuery( '.ive-fm-template-img img' ).show();
        jQuery( '.ive-fm-template-img img' ).attr( 'src', demo_image );

        jQuery( '.ive-fm-sidebar-content .ive-template-name' ).show();
        jQuery( '.ive-fm-sidebar-content .ive-template-name' ).text( demo_title );

        jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-slug', demo_slug );
        jQuery( '.ive-full-modal-iframe-wrap iframe' ).attr( 'src', demo_url );


        jQuery( '.ive-fm-template-text' ).show();
        jQuery( '.ive-fm-template-text p' ).text( demo_description );

        if ( data.data.hasOwnProperty( 'bundle_text_message' ) ) {
          if ( data.data.bundle_text_message != "" ) {
            jQuery( '.ive-bundle-text' ).html( data.data.bundle_text_message );
            jQuery( '.ive-bundle-text' ).show();
          } else {
            jQuery( '.ive-bundle-text' ).hide();
          }
        } else {
          jQuery( '.ive-bundle-text' ).hide();
        }

        jQuery( '.ive-fm-go-pro-btn' ).show();
        jQuery( '.ive-fm-go-pro-btn' ).attr( 'href', demo_permalink );


        // Setup Plugins in step popup
        if ( typeof data.data.template_plugins != "undefined" ) {
          var template_plugins = data.data.template_plugins;
          jQuery( '.ive-demo-plugins' ).find( '.ive-checkbox-container' ).remove();
          if ( !template_plugins.length ) {
            jQuery( '.ive-demo-plugins p' ).text( 'No plugin installation is required!' );
          } else {
            // Append plugin data to the step popup
            jQuery( '.ive-demo-plugins p' ).text( 'The following plugins are required for this template in order to work properly. Ignore if already installed.' );
            for (var i = 0; i < template_plugins.length; i++) {
              var template_plugin = template_plugins[i];
              jQuery('.ive-demo-plugins').append(
                `<div class="ive-checkbox-container" ive-plugin-text-domain="` + template_plugin.plugin_text_domain + `" ive-plugin-main-file="` + template_plugin.plugin_main_file + `" ive-plugin-url="` + template_plugin.plugin_url + `">
                  ` + template_plugin.plugin_title + `
                  <span class="ive-checkbox active">
                    <svg width="10" height="8" viewBox="0 0 11.2 9.1">
                      <polyline class="check" points="1.2,4.8 4.4,7.9 9.9,1.2 "></polyline>
                    </svg>
                  </span>
                </div>`
              );
            }
          }
        }



      });
    }

    jQuery( '.ive-fm-prev, .ive-fm-next' ).on( 'click', function() {

      if ( jQuery(this).hasClass('ive-fm-arrow-disabled') ) {
        return;
      }

      var current_template_slug = jQuery( '.ive-fm-import-btn-wrap a' ).attr( 'ive-template-slug' );


      var $current_cards_row = $('.ibtana-row.themes-box-wrap:visible');


      var $current_preview_btn_card = $current_cards_row.find( '.preview-template[ive-template-slug="'+current_template_slug+'"]' ).closest( '.ibtana--card' );

      var current_card_index  = $current_preview_btn_card.index();

      var next_or_prev_card_index = null;
      var next_or_prev_card_index_after_one_card = null;

      if ( jQuery(this).hasClass( 'ive-fm-prev' ) ) {
        next_or_prev_card_index = current_card_index - 1;

        // Code to check if next or previous after one card is available or not.
        next_or_prev_card_index_after_one_card  =  next_or_prev_card_index - 1;
      } else if ( jQuery(this).hasClass( 'ive-fm-next' ) ) {
        next_or_prev_card_index = current_card_index + 1;

        // Code to check if next or previous after one card is available or not.
        next_or_prev_card_index_after_one_card  =  next_or_prev_card_index + 1;
      }

      var $next_or_prev_card = $current_cards_row.find( '.ibtana--card' ).eq( next_or_prev_card_index );
      var $next_or_prev_card_btn = $next_or_prev_card.find( '.preview-template[ive-template-slug]' );
      ibtana_visual_editor_setup_preview_popup( $next_or_prev_card_btn );

      // Code to check if next or previous after one card is available or not.
      jQuery( '.ive-preview-close-btn .prev' ).removeClass( 'ive-fm-arrow-disabled' );
      jQuery( '.ive-preview-close-btn .next' ).removeClass( 'ive-fm-arrow-disabled' );
      if ( ( next_or_prev_card_index_after_one_card < 0 ) || $current_cards_row.find( '.ibtana--card' ).eq( next_or_prev_card_index_after_one_card ).length == 0 ) {
        jQuery( this ).addClass( 'ive-fm-arrow-disabled' );
      }
    });

    function ibtana_visual_editor_importThemeTemplateJson( $this ) {



      var free_template_slug  = $this.attr( 'ive-template-slug' );
      var is_pro_or_free      = parseInt( $this.attr( 'ive-is-premium' ) );
      var temp_type           = $this.attr( 'ive-template-type' );
      var page_type           = $this.attr( 'ive-template-page-type' );
      var ive_page_title      = $this.attr( 'ive-template-page-title' );
      var ive_template_text_domain = $this.attr( 'ive-template-text-domain' );


      var demo_action = '';
      var params = {
        action:               'ibtana_visual_editor_setup_free_demo',
        slug:                 free_template_slug,
        temp_type:            temp_type,
        page_type:            page_type,
        page_title:           ive_page_title,
        wpnonce:              ibtana_visual_editor_modal_js.wpnonce,
        is_pro_or_free:       is_pro_or_free,
        page_id:              ibtana_visual_editor_modal_js.page_id,
        ive_template_text_domain: ive_template_text_domain
      };

      if ( $this.attr( 'data-variable-product' ) ) {
        params.is_variable_product =  true;
      }

      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax,
        params,
        function( response ) {
          if ( response.home_page_url != "" ) {
            location.href = response.home_page_url;
          }
        }
      );
    }

    function ibtana_visual_editor_importComponentJson( $this ) {

      var free_template_slug  = $this.attr( 'ive-template-slug' );
      var is_pro_or_free      = parseInt( $this.attr( 'ive-is-premium' ) );
      var ive_component_title = $this.attr( 'ive-template-page-title' );


      var demo_action = '';
      var params = {
        action:               'ibtana_visual_editor_insert_component',
        slug:                 free_template_slug,
        wpnonce:              ibtana_visual_editor_modal_js.wpnonce,
        is_pro_or_free:       is_pro_or_free,
        page_id:              ibtana_visual_editor_modal_js.page_id
      };

      jQuery.post(
        ibtana_visual_editor_modal_js.adminAjax,
        params,
        function( response ) {
          if ( response.home_page_url != "" ) {
            location.href = response.home_page_url;
          }
        }
      );
    }

    $( '#ive-fm-import-template' ).on( 'click', function(e) {
      e.preventDefault();

      var $this = $( this );

      if ( 'template' == $this.attr( 'ive-content-type' ) ) {
        if ( $(this).hasClass('ive-install-plugin') ) {

          var plugin_text_domains_arr = [];
          var ive_required_plugins_divs = document.querySelectorAll('.ive-required-plugin div[data-slug]');
          for (var i = 0; i < ive_required_plugins_divs.length; i++) {
            plugin_text_domains_arr.push( {
              slug: jQuery( ive_required_plugins_divs[i] ).attr( 'data-slug' ),
              file: jQuery( ive_required_plugins_divs[i] ).attr( 'data-file' ),
            } );
          }

          ive_install_and_activate_plugin_from_wp( plugin_text_domains_arr, function() {
            if ( !jQuery( '.ive-required-plugin span.dashicons-no-alt' ).length && !jQuery( '.ive-required-plugin span.dashicons-update' ).length ) {

              ibtana_visual_editor_modal_js.ive_add_on_keys.ibtana_ecommerce_product_addons_license_key = false;
              ibtana_visual_editor_modal_js.is_woocommerce_available  = "1";

              $this.removeClass( 'ive-install-plugin' );
              jQuery( '.ive-fm-import-btn-wrap a' ).text( 'Free Import' );
              jQuery('.ibtana--modal--loader').hide();
              display_step_popup( $this );
            }
          } );
        } else {
          display_step_popup( $this );
        }
      } else if ( 'component' == $this.attr( 'ive-content-type' ) ) {

        ibtana_visual_editor_importComponentJson( $this );

      }




    });

    function display_step_popup( $this ) {

      // finally start the step popup
      var ive_template_text_domain = $this.attr( 'ive-template-text-domain' );


      jQuery( '.ive-demo-child .ive-checkbox-container' ).attr( 'ive-template-text-domain', ive_template_text_domain );
      // Check if the theme is activated
      if ( ( ive_template_text_domain == ibtana_visual_editor_modal_js.active_theme_text_domain ) || ( ive_template_text_domain == ibtana_visual_editor_modal_js.custom_text_domain ) ) {
        jQuery( '.ive-demo-child .ive-checkbox-container' ).addClass( 'activated' );
      }

      activate_first_step_in_step_popup();
      $( '.ive-plugin-popup' ).show();
    }



    function ive_install_and_activate_plugin_from_wp( plugin_text_domains, callback ) {
      jQuery('.ibtana--modal--loader').show();
      jQuery('.ive-fm-import-btn-wrap a').text( 'Installing...' );

      var plugin_text_domains_length = plugin_text_domains.length;

      for ( var i = 0; i < plugin_text_domains.length; i++ ) {

        var required_plugin_text_domain = plugin_text_domains[i].slug;
        var required_plugin_main_file   = plugin_text_domains[i].file;

        jQuery( '.ive-required-plugin div[data-slug="' + required_plugin_text_domain + '"] .dashicons' ).removeClass( 'dashicons-no-alt' ).addClass( 'dashicons-update' );

        var data_to_post = {
          action:             'ive-check-plugin-exists',
          plugin_text_domain: required_plugin_text_domain,
          main_plugin_file:   required_plugin_main_file,
          wpnonce:            ibtana_visual_editor_modal_js.wpnonce
        };


        jQuery.ajax({
          url:    ibtana_visual_editor_modal_js.adminAjax,
          type:   'post',
          data:   data_to_post,
          async:  false
        }).done( function( response ) {

            if ( response.data.install_status == true ) {
              // only activate the plugin
              jQuery('.ive-fm-import-btn-wrap a').text( 'Activating...' );
              jQuery.post(
                ibtana_visual_editor_modal_js.adminAjax,
                {
                  'action'        : 'ibtana_visual_editor_activate_plugin',
                  'ive-addon-slug': response.data.plugin_path,
                  'wpnonce':        ibtana_visual_editor_modal_js.wpnonce,
                },
                function() {
                  jQuery( '.ive-required-plugin div[data-slug="' + response.data.plugin_slug + '"] .dashicons' ).removeClass( 'dashicons-update' ).addClass( 'dashicons-yes' );
                  callback();
                }
              );

            } else {
              // install and activate the plugin
              wp.updates.installPlugin({
                  slug:     response.data.plugin_slug,
                  success:  function(data) {
                    jQuery('.ive-fm-import-btn-wrap a').text( 'Activating...' );
                    // now activate
                    jQuery.post(
                      ibtana_visual_editor_modal_js.adminAjax,
                      {
                        'action':         'ibtana_visual_editor_activate_plugin',
                        'ive-addon-slug': response.data.plugin_path,
                        'wpnonce':        ibtana_visual_editor_modal_js.wpnonce,
                      },
                      function() {
                        jQuery( '.ive-required-plugin div[data-slug="' + response.data.plugin_slug + '"] .dashicons' ).removeClass( 'dashicons-update' ).addClass( 'dashicons-yes' );
                        callback();
                      }
                    );
                  },
                  error: function(data) {
                    jQuery( '.ive-fm-import-btn-wrap a' ).text( 'Try Again' );
                    jQuery('.ibtana--modal--loader').hide();
                  },
              });
            }
          });

      }
    }


    $( '.ive-demo-step-container' ).on( 'click', '.ive-checkbox-container', function() {
      if ( $( this ).hasClass( 'activated' ) ) { return; }
      if ( $( this ).find( '.ive-checkbox' ).hasClass( 'active' ) ) {
        $( this ).find( '.ive-checkbox' ).removeClass( 'active' );
      } else {
        $( this ).find( '.ive-checkbox' ).addClass( 'active' );
      }
    });

    $( '.ive-close-button' ).on( 'click', function() {
      $('.ive-plugin-popup').hide();
    });

    function activate_first_step_in_step_popup() {
      $( '.ive-current-step .ive-demo-step' ).removeClass( 'active' );
      $( '.ive-current-step .ive-demo-step-0' ).addClass( 'active' );
      $( '.ive-steps-pills li' ).removeClass( 'active' );
      $( '.ive-steps-pills li:first' ).addClass( 'active' );
      $( '.ive-demo-back-btn' ).hide();
      $( '.ive-demo-main-btn' ).text( 'Next' );
      $( '.ive-demo-main-btn' ).show();
      $( '.ive-steps-pills' ).show();
      $( '.ive-close-button' ).show();
    }

    $( '.ive-demo-btn' ).on( 'click', function() {
      var $this_btn = $( this );

      var current_step_index = jQuery( '.ive-current-step .ive-demo-step.active' ).index();
      if ( $this_btn.hasClass( 'ive-demo-main-btn' ) ) {
        ++current_step_index;
      } else if ( $this_btn.hasClass( 'ive-demo-back-btn' ) ) {
        --current_step_index;
      }
      $( '.ive-current-step .ive-demo-step' ).removeClass( 'active' );
      $( '.ive-current-step .ive-demo-step-' + current_step_index ).addClass( 'active' );
      $( '.ive-steps-pills li' ).removeClass( 'active' );
      $( '.ive-steps-pills li' ).eq( current_step_index ).addClass( 'active' );

      // Back Button Show Hide
      if ( current_step_index != 0 ) {
        $( '.ive-demo-back-btn' ).show();
      } else {
        $( '.ive-demo-back-btn' ).hide();
      }

      if ( current_step_index == 2 ) {
        $( '.ive-demo-main-btn' ).text( 'Install & Import' );
      } else {
        $( '.ive-demo-main-btn' ).text( 'Next' );
      }

      if ( current_step_index != 3 ) {
        $( '.ive-demo-main-btn' ).show();
      } else {
        $( '.ive-demo-main-btn' ).hide();
        $( '.ive-demo-back-btn' ).hide();
        $( '.ive-steps-pills' ).hide();
        $( '.ive-close-button' ).hide();
        install_theme_and_plugins_using_ajax();
      }
    });

    function install_theme_and_plugins_using_ajax() {

      var total_progress_count = 0;

      // Check if the base theme is selected
      var theme_text_domain = '';
      if ( $( '.ive-demo-child .ive-checkbox-container:not(.activated) .ive-checkbox' ).hasClass('active') ) {
        // Get the theme name
        theme_text_domain = $('.ive-demo-child .ive-checkbox-container').attr('ive-template-text-domain');
        ++total_progress_count;
      }

      // Check if the plugins are selected
      var plugins_array = [];
      var plugin_checked_boxes = jQuery('.ive-demo-plugins .ive-checkbox-container .ive-checkbox.active');
      $.each( plugin_checked_boxes, function( index, plugin_checked_box ) {
        var $parent_div = jQuery(this).closest('.ive-checkbox-container');
        var plugin_text_domain = $parent_div.attr( 'ive-plugin-text-domain' );
        var plugin_main_file = $parent_div.attr( 'ive-plugin-main-file' );
        var ive_plugin_url = $parent_div.attr( 'ive-plugin-url' );
        plugins_array.push({
          plugin_text_domain: plugin_text_domain,
          plugin_main_file: plugin_main_file,
          plugin_url: ive_plugin_url
        });
        ++total_progress_count;
      });

      jQuery( '#ive-fm-import-template' ).removeAttr( 'data-variable-product' );

      set_installation_progress_status();

      if ( total_progress_count === 0 ) {
        set_installation_progress_status( 100 );
        ibtana_visual_editor_importThemeTemplateJson( jQuery('#ive-fm-import-template') );
      } else {
        if ( theme_text_domain != '' ) {
          install_or_activate_theme( theme_text_domain, function() {
            --total_progress_count;
            if ( total_progress_count === 0 ) {
              set_installation_progress_status( 100 );
              ibtana_visual_editor_importThemeTemplateJson( jQuery('#ive-fm-import-template') );
            }
            for (var i = 0; i < plugins_array.length; i++) {
              var plugin_single = plugins_array[i];
              install_or_activate_plugin( plugin_single, function( result ) {

                --total_progress_count;

                if ( total_progress_count == 0 ) {
                  set_installation_progress_status( 100 );
                  ibtana_visual_editor_importThemeTemplateJson( jQuery('#ive-fm-import-template') );
                }
              });
            }
          });
        } else {
          for (var i = 0; i < plugins_array.length; i++) {
            var plugin_single = plugins_array[i];
            install_or_activate_plugin( plugin_single, function( result ) {

              --total_progress_count;

              if ( total_progress_count == 0 ) {
                set_installation_progress_status( 100 );
                ibtana_visual_editor_importThemeTemplateJson( jQuery('#ive-fm-import-template') );
              }
            });
          }
        }
      }

    }

    function install_or_activate_plugin( plugin_details, callback ) {

      if ( plugin_details.plugin_text_domain == 'woo-variation-swatches' ) {
        jQuery( '#ive-fm-import-template' ).attr( 'data-variable-product', 1 );
      }

      jQuery.ajax({
        url:   ibtana_visual_editor_modal_js.adminAjax,
        type:  "POST",
        data: {
          "action"         : "ive_install_and_activate_plugin",
          "plugin_details" : plugin_details,
          "wpnonce"         : ibtana_visual_editor_modal_js.wpnonce,
        },
        async:  false
      }).done(function ( result ) {
        callback( result );
      });
    }

    function install_or_activate_theme( ive_template_text_domain, callback ) {
      jQuery.ajax({
        url:   ibtana_visual_editor_modal_js.adminAjax,
        type:  "POST",
        data: {
          "action"  : "ive-get-installed-theme",
          "slug"    : ive_template_text_domain,
          "wpnonce" : ibtana_visual_editor_modal_js.wpnonce,
        },
      }).done(function (result) {
        if( result.success ) {
          if ( result.data.install_status === true ) {
            // Theme is already installed and ready to active

            // Activation Script START
            setTimeout( function() {
              jQuery.ajax({
                url:   ibtana_visual_editor_modal_js.adminAjax,
                type:  "POST",
                data: {
                  "action" : "ive-theme-activate",
                  "slug"   : ive_template_text_domain,
                  "wpnonce": ibtana_visual_editor_modal_js.wpnonce,
                },
              }).done(function (result) {
                if( result.success ) {
                  ibtana_visual_editor_modal_js.active_theme_text_domain = ive_template_text_domain;
                  // return
                  callback();
                }
              });
            }, 1200 );
            // Activation Script END

          } else {
            // Theme is need to be downloaded and installed.
            wp.updates.installTheme( {
              slug:    ive_template_text_domain
            }).then(function(e) {
              // Activation Script START
              setTimeout( function() {
                jQuery.ajax({
                  url:   ibtana_visual_editor_modal_js.adminAjax,
                  type:  "POST",
                  data: {
                    "action" : "ive-theme-activate",
                    "slug"   : ive_template_text_domain,
                    "wpnonce": ibtana_visual_editor_modal_js.wpnonce,
                  },
                }).done(function (result) {
                  if( result.success ) {
                    ibtana_visual_editor_modal_js.active_theme_text_domain = ive_template_text_domain;
                    // return
                    callback()
                  }
                });
              }, 1200 );
              // Activation Script END
            });
          }
        }
      });
    }

    var progress_interval;
    function set_installation_progress_status( progress = 1 ) {
      if ( progress >= 100 ) {
        clearInterval( progress_interval );
        jQuery( '.ive-demo-install' ).attr( 'data-progress', 100 );
        jQuery( '.ive-demo-install span' ).text( '100%' );
        jQuery( '.ive-demo-install .ive-installer-progress div' ).css( 'width', '100%' );
      } else {
        progress_interval = setInterval( do_progress, 1000 );
      }
      function do_progress() {
        ++progress;
        jQuery( '.ive-demo-install' ).attr( 'data-progress', progress );
        jQuery( '.ive-demo-install span' ).text( progress + '%' );
        jQuery( '.ive-demo-install .ive-installer-progress div' ).css( 'width', progress + '%' );
      }
    }

    function get_modal_contents() {
      var data_post = {
        "active_theme_text_domain": active_theme,
        "custom_text_domain": ibtana_visual_editor_modal_js.custom_text_domain
      };

      $('.ibtana--modal--loader').show();
      $( ".content-modal" ).addClass( "ive-content-modal-show" );
      $.ajax({
        method: "POST",
        url: ibtana_license_api_endpoint + "get_modal_contents",
        data: JSON.stringify(data_post),
        dataType: 'json',
        contentType: 'application/json',
      }).done(function( data ) {

        var theme_text_domains_obj = data.data.theme_text_domains;

        var is_ibtana_theme = false;
        $.each(theme_text_domains_obj, function( key, ibtana_theme ) {
          if (ibtana_theme === active_theme) {
            is_ibtana_theme = true;
          }
        });

        var is_key_valid = data.data.is_key_valid;

        $('.ibtana--modal--loader').hide();
        $( ".content-modal" ).removeClass( "ive-content-modal-show" );


        if (!is_key_valid) {
          if ('sub' in data.data) {
            var subcategories_data = data.data.sub;
            var sub_cat_html = ``;
            for (var i = 0; i < subcategories_data.length; i++) {
              var subcategory_data = subcategories_data[i];
              var product_ids = subcategory_data.product_ids;
              sub_cat_html += `<button class="sub-cat-button" data-ids="`+product_ids+`">`+subcategory_data.name+` <span class="badge badge-info">`+product_ids.length+`</span></button>`;
            }
            $('#premium-template .sub-cats').empty();
            $('#premium-template .sub-cats').append(sub_cat_html);
          }
          var premium_data = data.data.products;
          $('#premium-template .ibtana-row.themes-box-wrap').empty();
          for (var i = 0; i < premium_data.length; i++) {
            var premium_product = premium_data[i];
            var paid_card_content = `<div class="ibtana-column-three ibtana--card" data-id="`+premium_product.id+`">
                                      <div class="blog-content-inner">
                                        <div class="blog-content-img-inner">
                                          <img class="blog-content-inner-image" src="`+premium_product.image+`">
                                        </div>
                                        <h2>`+premium_product.title+`</h2>`;
            if (themedomain == premium_product.domain) {
              var href = adminUrl+'themes.php?page='+theme_slug+'_guide&tab=gutenberg_import&page_id='+page_id;
              paid_card_content += `<a href="`+href+`" class="blog-content-btn-inner">Get Started</a>`;
            } else {
              paid_card_content += `<a href="`+premium_product.permalink+`" target="_blank" class="blog-content-btn-inner">Buy Now</a>
                                    <a href="`+premium_product.demo_url+`" target="_blank" class="blog-content-btn-inner">Demo</a>
                                  </div>
                                </div>`;

            }
            $('#premium-template .ibtana-row.themes-box-wrap').append(paid_card_content);
          }
          if (!data.data.inner_page.length) {
            jQuery('button[data-tab-head="InnerPages"]').hide();
          }
        } else {
          var premium_data = data.data.premium;
          $('#premium-template .ibtana-row.themes-box-wrap').empty();
          for (var i = 0; i < premium_data.length; i++) {
            var premium_product = premium_data[i];
            var card_content = ``;
            if (active_theme === premium_product.domain) {
              card_content = `<div class="ibtana-column-four ibtana--card card-theme-active">`;
              card_content += `<div class="blog-content-inner">
                                      <div class="blog-content-img-inner">
                                        <img class="blog-content-inner-image" src="`+premium_product.image+`">
                                      </div>
                                      <h2>`+premium_product.name+`</h2>
                                      <a class="import_premium blog-content-btn-inner" data-theme-slug="`+ premium_product.slug +`">IMPORT<span class="dashicons dashicons-download"></span></a>
                                    </div>
                                  </div>`;
              $('#premium-template .ibtana-row.themes-box-wrap').append(card_content);
            } else {
              card_content = `<div class="ibtana-column-four ibtana--card">`;
              card_content += `<div class="blog-content-inner">
                                      <div class="blog-content-img-inner">
                                        <img class="blog-content-inner-image" src="`+premium_product.image+`">
                                      </div>
                                      <h2>`+premium_product.name+`</h2>
                                      <a href="`+premium_product.permalink+`" target="_blank" class="blog-content-btn-inner" data-theme-slug="`+ premium_product.slug +`">Buy Now<span class="dashicons dashicons-download"></span></a>
                                    </div>
                                  </div>`;
              $('#premium-template .ibtana-row.themes-box-wrap').append(card_content);
            }

          }
          if ((0==premium_data.length) && (0==$('#premium-template .ive-coming-soon').length)) {
            $('#premium-template .ibtana-row.themes-box-wrap').append(
              '<h3 class="ive-coming-soon">Coming Soon...</h3>'
            );
          }

          // Inner Pages
          var inner_page_object = data.data.inner_page;
          if (!jQuery.isEmptyObject(inner_page_object)) {
            var inner_pages_sub_cats = inner_page_object.inner_pages_sub_cats;
            $('#InnerPages .inner-tab-content ul').empty();
            $('#InnerPages .inner-pages-divs-wrapper').empty();
            for (var i = 0; i < inner_pages_sub_cats.length; i++) {
              var inner_pages_sub_cat = inner_pages_sub_cats[i];
              var _inner_pages_sub_cat = inner_pages_sub_cat.replace('_', ' ');
              if (i === 0) {
                $('#InnerPages .inner-tab-content ul').append('<li class="theme-tab-list-two active" data-template-tab="'+inner_pages_sub_cat+'"><span>'+_inner_pages_sub_cat+'</span></li>');
                $('#InnerPages .inner-pages-divs-wrapper').append(
                  `<div class="ibtana-theme-block" data-template-div="`+inner_pages_sub_cat+`">
                    <div class="ibtana-row themes-box-wrap">
                    </div>
                  </div>`
                );
              } else {
                $('#InnerPages .inner-tab-content ul').append('<li class="theme-tab-list-two" data-template-tab="'+inner_pages_sub_cat+'"><span>'+_inner_pages_sub_cat+'</span></li>');
                $('#InnerPages .inner-pages-divs-wrapper').append(
                  `<div class="ibtana-theme-block" data-template-div="`+inner_pages_sub_cat+`" style="display:none;">
                    <div class="ibtana-row themes-box-wrap">
                    </div>
                  </div>`
                );
              }
            }
          }
          // Inner Pages END
        }
      });
    }

  }
})(jQuery);
