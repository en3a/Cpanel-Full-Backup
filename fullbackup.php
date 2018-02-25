<?php
include "xmlapi.php";
include "config.php";
include "send_email.php";


for ( $row = 0; $row < count( $cpservers ); $row++ ) {

	    // Credentials for cPanel account
		$cpanel_server = $cpservers[$row]['Domain'];
		$cpanel_account   = $cpservers[$row]['CPanel_user']; 
		$cpanel_password  = $cpservers[$row]['CPanel_pass'];

		//remote FTP for transfering Backup Files
		$ftphost = $cpservers[$row]['FTPhost'];
		$ftpacct = $cpservers[$row]['FTPuser']; 
		$ftppass = $cpservers[$row]['FTPpass']; 
		$logs_dir = "/public_html/backups";

		$xmlapi = new xmlapi($cpanel_server);
		$xmlapi->password_auth($cpanel_account,$cpanel_password);
		$xmlapi->set_port('2083');

		$conn_id = ftp_connect($ftphost) or die ("Could not connect to remote FTP");
		$login_result = ftp_login($conn_id, $ftpacct, $ftppass);
        
        //check Remote ftp login
        
        if ((!$conn_id) || (!$login_result)) {
            
            //send email notification
            $subject = "ERROR ON $cpanel_account BACKUP";
            $body = "FTP connection has failed ! <br><br>";
            sendReminder($subject, $body);
            die("FTP connection has failed !");
        }
        
        //change dir
		ftp_chdir($conn_id, $logs_dir);
		$files = ftp_nlist($conn_id, ".");
		foreach ($files as $filename) {
		        $fileCreationTime = ftp_mdtm($conn_id, $filename);
		        $fileAge=time();
		        $fileAge=$fileAge-$fileCreationTime;
		}
		ftp_close($conn_id);

		$api_args = array(
                           'passiveftp',
                           $ftphost,
                           $ftpacct,
                           $ftppass,
                           $email_notify,
                            21,
                            '$logs_dir'
                         );

	$xmlapi->set_output('json');
	print $xmlapi->api1_query($cpanel_account,'Fileman','fullbackup',$api_args);
	unset($xmlapi);
	
	//send custom email notification, for ex sync to cloud
	//or manual operation
	// if you want to disable remove sleep(120) && sendReminder() function
    $subject = "$cpanel_account Cpanel Backup Is Successful";
    $body = "Cpanel for user $cpanel_account has backed up successfully. <br><br>";
    $body .= "Thank you!!";
    sleep(120);
    sendReminder($subject, $body);
	
	//sleep(2) is used in case of multi cpanel backup
	// to give some time and not mess things
	// one after the other
	sleep(2);
	

}
?>
