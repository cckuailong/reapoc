(function($) {
  window.addEventListener(
    'load',
    function() {

      // Sirat theme admin notice START
      $.post(
        ive_notice_params.IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
        {
          "theme_text_domain":  ive_notice_params.theme_text_domain
        },
        function ( data ) {

          if ( !data.data.is_found ) {
            $( '#ive-admin-notice-sirat' ).show();
          } else {
            $( '#ive-admin-notice-sirat' ).hide();
          }
        }
      );
      // Sirat theme admin notice END
      // Go back from sirat theme installation START
      if ( location.href.indexOf("ive-sirat-installed=true") >= 0 ) {

        // Select the node that will be observed for mutations
        const targetNode = document.querySelector('.wrap');

        // Options for the observer (which mutations to observe)
        const config = { attributes: true, childList: true, subtree: true };

        // Callback function to execute when mutations are observed
        const callback = function( mutationsList, observer ) {
          // Use traditional 'for loops' for IE 11
          for( const mutation of mutationsList ) {
            if ( mutation.type === 'childList' ) {
            }
            else if (mutation.type === 'attributes') {
            }

            if ( jQuery( '.wrap a[href*="themes.php"]' ).length ) {
              // window.history.back();
              location.href = ive_notice_params.admin_url + 'plugins.php';
            }
          }
        };

        // Create an observer instance linked to the callback function
        const observer = new MutationObserver(callback);

        // Start observing the target node for configured mutations
        observer.observe(targetNode, config);

        // Later, you can stop observing
        // observer.disconnect();

      }
      // Go back from sirat theme installation END



      var data_to_post = {
        action: 'ive_get_admin_notices',
        wpnonce:  ive_notice_params.wpnonce
      };
      jQuery.ajax({
        method:   "POST",
        url:      ive_notice_params.ajax_url,
        data:     data_to_post
      }).done(function( data ) {

        console.log( 'data', data );

        if ( data.success == true ) {

          var ive_admin_notices_res = data.data;

          var show_ive_admin_notices = false;

          for ( var i = 0; i < ive_admin_notices_res.length; i++ ) {
            var ive_admin_notice_data = ive_admin_notices_res[i];

            var ive_show_notice = ive_admin_notice_data.is_ibtana_admin_notice_enabled;

            if ( ive_show_notice ) {

              var ive_admin_notice_id = ive_admin_notice_data.ibtana_admin_notice_unique_id;

              var ive_notice_params_ive_admin_notices = ive_notice_params.ive_admin_notices;

              for ( var j = 0; j < ive_notice_params_ive_admin_notices.length; j++ ) {
                var ive_admin_notice_single = ive_notice_params_ive_admin_notices[j];
                if ( ive_admin_notice_single == ive_admin_notice_id ) {
                  ive_show_notice = false;
                  break;
                }
              }

              if ( ive_show_notice ) {
                show_ive_admin_notices = true;

                if ( ive_admin_notice_data.ibtana_admin_notice_contents != '' ) {

                  if ( ive_admin_notice_data.ibtana_admin_notice_css != '' ) {
                    $( 'head' ).append(
                      `<style>
                        ${ive_admin_notice_data.ibtana_admin_notice_css}
                      </style>`
                    );
                  }

                  $( '#ive-admin-notice' ).append(
                    `<div class="notice" data-ive-admin-notice-id="${ive_admin_notice_id}">
                      <button type="button" class="ive-admin-notice-dismiss"></button>
                      ${ive_admin_notice_data.ibtana_admin_notice_contents}
                    </div>`
                  );

                }
              }

            }
          }

          if ( show_ive_admin_notices ) {

            $( '.notice[data-ive-admin-notice-id]' ).on( 'click', '.ive-admin-notice-dismiss', function() {

              var ive_admin_notice_el = jQuery( this ).closest( '[data-ive-admin-notice-id]' );

              var ive_admin_notice_id = ive_admin_notice_el.attr( 'data-ive-admin-notice-id' );

              jQuery.post(
                ive_notice_params.ajax_url,
                {
                  'action':             'ive_admin_notice_ignore',
                  'ive_admin_notice_id': ive_admin_notice_id,
                  'wpnonce':             ive_notice_params.wpnonce
                },
                function( result ) {
                  ive_admin_notice_el.remove();
                  if ( !jQuery('#ive-admin-notice .notice').length ) {
                    $( '#ive-admin-notice' ).hide();
                  }
                }
              );

            } );

            $( '#ive-admin-notice' ).show();

          }
        }

      });



      // Theme Notice Start
      function premium_Theme_Rating_Notice_Start() {

        var pro_theme_text_domain   = ive_notice_params.pro_theme_text_domain;
        var theme_validation_status = ive_notice_params.theme_validation_status;
        var vw_pro_theme_key        = ive_notice_params.vw_pro_theme_key;
        var date                    = ive_notice_params.date;

        if ( theme_validation_status == "true" && ( pro_theme_text_domain != '' ) && ( vw_pro_theme_key != '' ) ) {

          var data_to_post = {
            action:                 'ive_get_theme_license_activation_duration',
            vw_pro_theme_key:       vw_pro_theme_key,
            pro_theme_text_domain:  pro_theme_text_domain,
            wpnonce:                ive_notice_params.wpnonce
          };
          jQuery.ajax({
            method: "POST",
            url:    ive_notice_params.ajax_url,
            data:   data_to_post
          }).done(function( data ) {

            if ( data.success == true ) {
              if ( data.hasOwnProperty( 'data' ) ) {
                if ( data.data.hasOwnProperty( 'difference' ) && data.data.hasOwnProperty( 'vw_pro_theme_info' ) ) {

                  var date_differences            = data.data.difference;
                  var date_differences_days       = parseInt( date_differences.total_days );

                  var vw_pro_theme_info           = data.data.vw_pro_theme_info;
                  var vw_pro_theme_info_name      = vw_pro_theme_info.name;
                  var vw_pro_theme_info_permalink = vw_pro_theme_info.permalink;

                  $( '#ive-admin-notice' ).append(
                    `<div class="notice notice-info is-dismissible">
                      <p>Loved <strong>${vw_pro_theme_info_name}!</strong> Review us here.</p>
                      <p>
                        <a class="button-secondary" target="_blank" href="${vw_pro_theme_info_permalink}?iva=true">
                          Rate Us &#9733;&#9733;&#9733;&#9733;&#9733;
                        </a>
                      </p>
                      <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                      </button>
                    </div>`
                  );
                  $( '#ive-admin-notice' ).show();

                }
              }
            }
          });

        }
      }
      premium_Theme_Rating_Notice_Start();
      // Theme Notice END


      // Free Theme Notice Start
      function free_theme_notices() {
        if ( ive_notice_params.free_theme_text_domain != "" ) {
          $.post(
            ive_notice_params.IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
            {
              "theme_text_domain":  ive_notice_params.free_theme_text_domain
            },
            function ( data ) {
              if ( data.data.is_found ) {
                var name      = data.data.is_found.name;
                var permalink = data.data.is_found.permalink;
                $( '#ive-admin-notice' ).append(
                  `<div class="notice notice-info is-dismissible">
                    <p>
                      Try Our Premium <strong>${name}</strong> With Extraodnary Features At Just <strong>$36</strong> Use Coupon <strong>"IBPro10".</strong>
                    </p>
                    <p>
                      <a target="_blank" href="${permalink}" class="button button-primary">
                        Buy Now
                      </a>
                    </p>
                    <button type="button" class="notice-dismiss">
                      <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                  </div>`
                );
                $( '#ive-admin-notice' ).show();
              }
            }
          );
        }
      }
      free_theme_notices();
      // Free Theme Notice End


      // Bundle Notice Start
      function bundle_notice_start() {
        if ( ive_notice_params.pro_theme_text_domain != "" ) {
          $.post(
            ive_notice_params.IBTANA_LICENSE_API_ENDPOINT + 'get_client_meta_box_info',
            {
              "theme_text_domain":  ive_notice_params.pro_theme_text_domain
            },
            function ( data ) {

              if ( data.data.is_found ) {
                $( '#ive-admin-notice' ).append(
                  `<div class="notice notice-info is-dismissible">
                    <p>
                      Get all our <strong>160+ Premium Themes</strong> worth $9440 With Our <strong>WP Theme Bundle</strong> in just <strong>$99.</strong>
                    </p>
                    <p>
                      <a target="_blank" href="https://www.vwthemes.com/premium/theme-bundle?iva_bundle=true" class="button button-primary">
                        Buy Now
                      </a>
                    </p>
                    <button type="button" class="notice-dismiss">
                      <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                  </div>`
                );
                $( '#ive-admin-notice' ).show();
              }

            }
          );
        }
      }
      bundle_notice_start();
      // Bundle Notice END

      $( '#ive-admin-notice' ).on( 'click', '.notice-dismiss', function() {
        $(this).closest( '.notice.notice-info.is-dismissible' ).remove();
      });

    },
    false
  );
})( jQuery );
