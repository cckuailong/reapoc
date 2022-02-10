<?php 
class LFB_Extension{

function getFormData($formid,$formData,$savedb){

	$email = (isset($formData['email']))?$formData['email']:'';
    $name  = (isset($formData['name']))?$formData['name']:'';

	if($email!=''){
	$this->mailchimMailSend($formid,$email,$name,$savedb);
	}
}

function mailchimMailSend($formid,$email,$name,$savedb){

	$api  = $savedb->lfb_get_ext_data($formid,1);
	$listId = isset($api[0]->ext_map)?$api[0]->ext_map:'';
	$active = isset($api[0]->active)?$api[0]->active:'';

	if($listId!='' && $active==1){
		$apiKey = isset($api[0]->ext_api)?$api[0]->ext_api:'';
		$this->lfb_send_mailchimp_data($listId,$email,$name,$apiKey);
	} else {
		return;
	}
}

function lfb_send_mailchimp_data($listId,$email,$name,$apiKey){
    $data = array(
        'email'     => $email,
        'status'    => 'subscribed',
        'firstname' => $name,
    );

   // $listId = $get_list[0]->id;

    $memberId = md5(strtolower($data['email']));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
   $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

    $json = json_encode(array(
        'email_address' => $data['email'],
        'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
        'merge_fields'  => array(
            'FNAME'     => $data['firstname'],
        )
    ));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
	//print_r($httpCode);

}

}


?>