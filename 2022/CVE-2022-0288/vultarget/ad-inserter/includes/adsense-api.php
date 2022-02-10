<?php

//ini_set ('display_errors', 1);
//error_reporting (E_ALL);

//    GPT
//  define ('AI_ADSENSE_CLIENT_ID',     '607114800573.apps.googleusercontent.com');
//  define ('AI_ADSENSE_CLIENT_SECRET', '2muX2P9FHRNtm6BURa49t1z6');
//  define ('AI_ADSENSE_DEVELOPER_KEY', 'AIzaSyCDZtqhLeAp1XM-xS52nzQ7NwnrOH0UE2U');

if (defined ('AI_CI_STRING') /*&& get_option (AI_ADSENSE_OWN_IDS) === false*/) {
  define ('AI_ADSENSE_CLIENT_ID',     base64_decode (AI_CI_STRING));
  define ('AI_ADSENSE_CLIENT_SECRET', base64_decode (AI_CS_STRING));
}
elseif (($adsense_client_ids = get_option (AI_ADSENSE_CLIENT_IDS)) !== false) {
  define ('AI_ADSENSE_CLIENT_ID',     $adsense_client_ids ['ID']);
  define ('AI_ADSENSE_CLIENT_SECRET', $adsense_client_ids ['SECRET']);
}

if (($adsense_auth_code = get_option (AI_ADSENSE_AUTH_CODE)) !== false) {
  define ('AI_ADSENSE_AUTHORIZATION_CODE', $adsense_auth_code);
}

$php_version = explode ('.', PHP_VERSION);
if ($php_version [0] >= 8) {
  // PHP 8
  require_once AD_INSERTER_PLUGIN_DIR.'includes/google-api-8/vendor/autoload.php';
  require_once AD_INSERTER_PLUGIN_DIR.'includes/google-api-8/vendor/google/apiclient-services/src/Adsense.php';
} else {
  require_once AD_INSERTER_PLUGIN_DIR.'includes/google-api/vendor/autoload.php';
  require_once AD_INSERTER_PLUGIN_DIR.'includes/google-api/vendor/google/apiclient-services/src/Adsense.php';
}


class adsense_api {
  protected $apiClient;
  protected $adSenseService;
  protected $publisherID;
  protected $error;

  public function __construct () {
    $this->apiClient = new Google_Client ();

    $this->apiClient->setClientId (AI_ADSENSE_CLIENT_ID);
    $this->apiClient->setClientSecret (AI_ADSENSE_CLIENT_SECRET);
    $this->apiClient->setRedirectUri ('urn:ietf:wg:oauth:2.0:oob');

    $this->apiClient->setScopes (array ('https://www.googleapis.com/auth/adsense.readonly'));
    $this->apiClient->setAccessType ('offline');

    $this->adSenseService = new Google_Service_AdSense ($this->apiClient);
  }

  public function getAuthUrl () {
    $this->apiClient->setApprovalPrompt ('force');

    return ($this->apiClient->createAuthUrl ());
  }


  public function authenticate () {
    $token = $this->getToken ();
    if (isset ($token)) {
      // We already have the token.
      $this->apiClient->setAccessToken ($token);
    } else {
      // Override the scope to use the readonly one
      $this->apiClient->setScopes (array("https://www.googleapis.com/auth/adsense.readonly"));
      // Go get the token
      $this->apiClient->authenticate (AI_ADSENSE_AUTHORIZATION_CODE);
      $this->saveToken ($this->apiClient->getAccessToken ());
    }
  }

  public function getAdSenseService () {
    return $this->adSenseService;
  }

  public function getAdSensePublisherID () {
    return $this->publisherID;
  }

  public function getError () {
    return $this->error;
  }

  public function isTokenValid () {
    $token = $this->getToken ();
    return isset ($token);
  }

  public function refreshToken ($adunit_code_id = '') {
    if ($this->apiClient->getAccessToken () != null) {
      $this->saveToken ($this->apiClient->getAccessToken());
    }
  }

  public function getAdUnits () {
    $adsense_data = array ();

    $this->error = '';

    try {
      $this->authenticate ();

      if ($this->isTokenValid ()) {
        $adsense_service = $this->getAdSenseService ();

        $optParams ['pageSize'] = 20;
        $pageToken = null;
        $optParams ['pageToken'] = $pageToken;

        try {
          $accounts = $adsense_service->accounts->listAccounts ($optParams);

          if (!isset ($accounts) || empty ($accounts)) {
            throw (new Exception ('No valid AdSense account'));
          }

          $aiAccountId = $accounts->accounts [0]['name'];

          if (isset ($aiAccountId)) {
            $account_data = explode ('/', $aiAccountId);
            if (isset ($account_data [1])) {
              $this->publisherID = $account_data [1];
            }
          }

          try {
            $adClients = $adsense_service->accounts_adclients->listAccountsAdclients ($aiAccountId, $optParams);

            if (!isset ($adClients) || empty ($adClients)) {
              throw (new Exception ('No valid AdSense ad client'));
            }

            $aiAdClient = null;
            foreach ($adClients as $adClient) {
              if ($adClient->productCode == 'AFC') {
                $aiAdClient = $adClient;
                break;
              }
            }

            if (!$aiAdClient) throw (new Exception ('No valid AdSense ad client for AFC product'));

            $aiAdClientId = $aiAdClient ['name'];

            try {
              $optParams ['pageSize'] = 50;

              $adsense_adunits = array ();
              $pageToken = null;
              do {
                $optParams['pageToken'] = $pageToken;

                $adsense_adunits_page = $adsense_service->accounts_adclients_adunits->listAccountsAdclientsAdunits ($aiAdClientId, $optParams);

                if (!empty ($adsense_adunits_page ['adUnits'])) {
                  $adsense_adunits = array_merge ($adsense_adunits,  $adsense_adunits_page ['adUnits']);

                  if (isset($adsense_adunits_page ['nextPageToken'])) {
                    $pageToken = $adsense_adunits_page ['nextPageToken'];
                  } else $pageToken = null;
                }

              } while ($pageToken);

              foreach ($adsense_adunits as $adsense_adunit) {
                $name_elements = explode ('/', $adsense_adunit ['name']);
                $adsense_data [] = array (
                  'id'      => $adsense_adunit ['name'],
                  'name'    => $adsense_adunit ['displayName'],
                  'code'    => end ($name_elements),
                  'type'    => $adsense_adunit->contentAdsSettings ['type'],
                  'size'    => str_replace (array ('1x3'), array (''), $adsense_adunit->contentAdsSettings ['size']),
                  'active'  => $adsense_adunit ['state'] == 'ACTIVE',
                );
              }
            } catch (Google_Service_Exception $e ) {
              $adsense_err = $e->getErrors ();
              $this->error = 'List Ad Units Error: ' . strip_tags ($e->getMessage ()) . ' ' . $adsense_err [0]['message'];
            }
          } catch (Google_Service_Exception $e ) {
            $adsense_err = $e->getErrors ();
            $this->error = 'List Ad Clients Error: ' . strip_tags ($e->getMessage ()) . ' ' . $adsense_err [0]['message'];
          }
        } catch (Google_Service_Exception $e ) {
          $adsense_err = $e->getErrors ();
          $this->error = 'List Accounts Error: ' .  strip_tags ($e->getMessage ()) . ' ' . $adsense_err [0]['message'];
        } catch (Exception $e ) {
          $this->error = 'Error: ' . strip_tags ($e->getMessage());
        }
      } else {
        }

    } catch (Exception $e) {
        $this->error = 'AdSense authentication failed: ' . strip_tags ($e->getMessage ());
    }

    if ($this->error != '') return array ();

    return $adsense_data;
  }


  public function getAdCode ($adunit_code_id = '') {
    $adsense_data = '';

    $this->error = '';

    try {
      $this->authenticate ();

      if ($this->isTokenValid ()) {
        $adsense_service = $this->getAdSenseService ();
        try {
          // Ad unit code
          $adsense_adunits_code = $adsense_service->accounts_adclients_adunits->getAdcode ($adunit_code_id);
          $adsense_data = $adsense_adunits_code ['adCode'];
        } catch (Google_Service_Exception $e ) {
          $adsense_err = $e->getErrors ();
          $this->error = 'List Ad Units Error: ' .  $adsense_err [0]['message'];
        } catch (Exception $e ) {
          $this->error = 'Error: ' . strip_tags ($e->getMessage());
        }
      }

    } catch (Exception $e) {
        $this->error = 'AdSense authentication failed: ' . strip_tags ($e->getMessage ());
    }

    if ($this->error != '') return '';

    return $adsense_data;
  }

  private function saveToken ($token) {
    if ($token != null) set_transient (AI_TRANSIENT_ADSENSE_TOKEN, $token);
  }

  private function getToken () {
    $token = get_transient (AI_TRANSIENT_ADSENSE_TOKEN);

    if ($token === false) return null; else return $token;
  }
}

