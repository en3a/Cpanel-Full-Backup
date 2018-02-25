<?php
//include phpmailer class file
if (!class_exists("phpmailer")){
	include_once('PHPMailer/class.phpmailer.php');
}
//send email function
function send_email($subject,$body,$from,$to,$cc,$bcc,$files){
	
	$mail = new PHPMailer(true);
	try {
		$mail->IsSMTP(); 
		$mail->SMTPDebug = 0;  
		$mail->SMTPAuth = true;  //use authentication, set to false in case of anonymous login
		//$mail->SMTPSecure = 'ssl'; 
		$mail->Host = 'mail.server.com'; //amtp server to authenticate
		//$mail->Port = 25;
		$mail->Port = 587; //smtp port, can be either 25 or 587
		$mail->Username = 'email@email.com';  //email for authentication
		$mail->Password = 'EmailPassword'; // password for authentication
		$mail->Subject = $subject;
		$mail->IsHTML(true);
		$mail->CharSet="utf-8";
		$mail->MsgHTML($body);
		//$mail->Body = $body;
		//
		//Seat attachment email
		if($files){
			foreach($files as $v_files => $s_files){
				if(substr($s_files,-3) == 'pdf' || substr($s_files,-3) == 'jpg' || substr($s_files,-4) == 'xlsx' || substr($s_files,-3) == 'wav' || substr($s_files,-3) == 'mp3' || substr($s_files,-4) == 'jpeg' || substr($s_files,-3) == 'png'){
					$mail->AddAttachment($v_files,$s_files); 		//attach PDF invoice
				}elseif(substr($s_files,-3) == 'csv'){
					$mail->AddStringAttachment($v_files,$s_files, 'base64', 'text/csv');//attach CSV reports
				}else{
					$mail->AddStringAttachment($v_files,$s_files);	//attach XLS reports
				}
			}
		}
		//Set from email
		foreach($from as $v_from => $s_from){
			$mail->SetFrom($v_from,$s_from);
		}
		//$mail->AddReplyTo($from);
		//Set to email
		if($to){
			foreach($to as $v_to => $s_to) {
				$mail->AddAddress($v_to,$s_to);
			}
		}
		//Set cc email
		if($cc){
			foreach($cc as $v_cc => $s_cc) {
				$mail->AddCC($v_cc,$s_cc);
			}
		}
		//Set bcc email
		if($bcc){
			foreach($bcc as $v_bcc => $s_bcc) {
				$mail->AddBCC($v_bcc,$s_bcc);
			}
		}
		
		$mail->Timeout = 5;
		$mail->Send();
		$mail->ClearAddresses();
		$mail->ClearAttachments();
		$mail->ClearCCs();
		$mail->ClearBCCs();
		
		return 1;
		
	}
	catch (phpmailerException $e) {
		echo $e->errorMessage();
		return 0;
	} 
	catch (Exception $e) {
		return 0;
		echo $e->getMessage();
	}
}

?>