<?php
$subscription_id = (int)JRequest::getCmd('x_subscription_id');
// Check to see if we got a valid subscription ID.
// If so, do something with it.
if ($subscription_id)
{
    // Get the response code. 1 is success, 2 is decline, 3 is error
    $response_code = (int) JRequest::getCmd('x_response_code');
    // Get the reason code. 8 is expired card.
	header("HTTP/1.0 200 OK");	
	// Get the response code. 1 is success, 2 is decline, 3 is error
    //$response_code = (int) $_POST['x_response_code'];
    // Get the reason code. 8 is expired card.
    //$reason_code = (int) $_POST['x_response_reason_code'];
    $db = oseDB::instance();
    
}
?>