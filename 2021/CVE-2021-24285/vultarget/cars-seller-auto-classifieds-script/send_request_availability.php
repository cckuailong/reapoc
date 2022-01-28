<?php

add_action( 'wp_ajax_nopriv_send_avaibility_request_carseller', 'send_request_availability_carseller' );
add_action( 'wp_ajax_send_avaibility_request_carseller', 'send_request_availability_carseller' );
function send_request_availability_carseller() {
	// echo $formdata=$_POST['formdata'];
    
	if(!empty($_POST['formdata']))
	{
	parse_str($_POST['formdata'], $formdata);
	/*
	Array
(
    [postid] => 60
    [FirstName] => test
    [LastName] => test
    [EmailAddress] => test@test.com
    [moveInDate] => Early July
    [PhoneNumber] => 123
    [message] => I am looking for vacancies that would be available around [7/1/2014]. Could you please contact me at [email] to discuss?
Thank You,
[first name]
    
)*/
  $admin_email=get_option( 'admin_email');

	$title=get_the_title($formdata['postid']);
	$post_meta = get_post_meta( $formdata['postid']);
	
	$permalink = get_permalink( $formdata['postid']);
	global $wpdb;
	$data = array(
        'id' => NULL,
        'carseller_id' => $formdata['postid'],
        'first_name' => $formdata['FirstName'],
        'last_name' => $formdata['LastName'],
        'email' => $formdata['EmailAddress'],
        'phone' => $formdata['PhoneNumber'],
        'message' => $formdata['message'],
        'created' => current_time('mysql', 1)
    );
	$table_name = $wpdb->prefix . "carsellers_requests";
	$wpdb->insert($table_name, (array) $data );

						
	$message='
	<table>
		<tbody>
		<tr><td colspan="2">carseller Name :<b><a href="'.$permalink.'">'.$title.'</a></b></td>
		
		<tr><td>First Name</td><td>'.$formdata['FirstName'].' '.$formdata['LastName'].'</td>
		</tr>
		<tr><td>Email</td><td>'.$formdata['EmailAddress'].'</td>
		</tr>
		
		<tr><td>Phone</td><td>'.$formdata['PhoneNumber'].'</td>
		</tr>
		<tr><td>Message</td><td>'.$formdata['message'].'</td>
		</tr>
		</tbody>
	</table>';
	add_filter( 'wp_mail_content_type', 'set_html_content_type_carseller' );
	$subject='Enquiry for:'.$title;
	$headers[] = 'From: '.$formdata['FirstName'].' '.$formdata['LastName'].' <'.$formdata['EmailAddress'].'>';
	wp_mail( $admin_email, $subject, $message, $headers );


	$messageforcustomer="
		<table>
		<tbody>
		<tr><td>
		Hello ".$formdata['FirstName']."!,
		</td>
		</tr>
		
		</tr>
		<tr><td><br>Thank you for contacting us.</b> Our team will contact you very soon.</td>
		</tr>

		<tr><td>Thanks</td>
		</tr>
		
		</tbody>
	</table>
		";

	$subject='Your Enquiry for:'.$title;
	$headers[] = 'From:  <'.$admin_email.'>';
	wp_mail( $formdata['EmailAddress'], $subject, $messageforcustomer, $headers );	
	// print_r($formdata);
                $result['title']='Requset sent';
		$result['message']='<h3 class="request_received">We have received your query and our team will contact you shortly.</h3>';
                $result['button']='btn-success';
                echo json_encode($result);
		
	}
	else
	{
                $result['title']='Unable to send the requset';
		$result['message']='<h3 class="request_denied">Oops! Something wrong, please try to contact us again .</h3>';
                $result['button']='btn-danger';
                echo json_encode($result);
	}
        
	die;
}
function set_html_content_type_carseller() {

	return 'text/html';
}