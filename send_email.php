<?php

///////////////////////////////////////////////////////
/// @ CUSTOM MAIL FUNCTION
/// @ $sub for email subject
/// @ $bod for email body
///////////////////////////////////////////////////////

function sendReminder ($sub, $bod) {
    
include "reminder/function_mail.php";

$to = array(
	'email@email.com' => 'Email Monitoring',
);


$from = array(
	'email@email.com' => 'Email Monitoring',
);


$subject = $sub;
$body = $bod;

send_email($subject,$body,$from,$to,$cc=null,$bcc=null,$files=null);

}


?>
