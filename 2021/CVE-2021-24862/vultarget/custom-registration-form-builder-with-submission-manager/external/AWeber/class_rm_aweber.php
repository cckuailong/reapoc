<?php
if (!class_exists('AWeberAPI'))
    require_once('aweber_api.php');
// Replace with the keys of your application
// NEVER SHARE OR DISTRIBUTE YOUR APPLICATIONS'S KEYS!

class RM_AWeber
{
    private $consumerKey;
    private $consumerSecret; 
    private $accessToken ;
    private $accessTokenSecret ;
    private $aweber ;
    private $account ;
    public function __construct($options=null)
    {
        $this->consumerKey=$options['aw_consumer_key'];
        $this->consumerSecret=$options['aw_consumer_secret'];
        $this->accessToken=$options['aw_access_key'];
        $this->accessTokenSecret=$options['aw_access_secret'];
        $this->aweber = new AWeberAPI($this->consumerKey, $this->consumerSecret);
        $this->aweber->adapter->debug = false;
        $this->account = $this->aweber->getAccount($this->accessToken, $this->accessTokenSecret);
        
    }
# set this to true to view the actual api request and response
       
    
     public function fetch_list()
     {
          $lists = $this->account->lists->data['entries'] ;
          
          return $lists;
     }
     public function add_contact($contacts,$list)
     {
         $params['name']="";
                $listURL = "/accounts/{$this->account->data['id']}/lists/{$list}";
                $list = $this->account->loadFromUrl($listURL);
                 
                    try {
               
                # create a subscriber
                if($contacts['email']){
                    $params = array(
                    'email' => $contacts['email']);
                }
                
                if(isset($contacts['first_name'])){
                    $params['name'] = $contacts['first_name'];
                }
                
                if(isset($contacts['last_name'])){
                    $params['name'] = $params['name'].'  '.$contacts['last_name'];
                }
                
              
                $subscribers = $list->subscribers;
                $new_subscriber = $subscribers->create($params);
            } 
            catch(AWeberAPIException $exc) {
                //print_r($exc); die;
            }

         return true;
     }

}

?>