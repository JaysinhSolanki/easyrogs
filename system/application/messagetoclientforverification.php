<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");

$actiontype			=	$_POST['actiontype'];
$discovery_id		=	$_POST['discovery_id'];

 
//if($isemail==1)
{
	
	
	$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
												'c.case_title 	as case_title,
												c.plaintiff,
												c.defendant,
												c.case_number 	as case_number,
												c.jurisdiction 	as jurisdiction,
												c.judge_name 	as judge_name,
												c.county_name 	as county_name,
												c.court_address as court_address,
												c.department 	as department, 
												c.uid 	as case_uid, 
												d.case_id 		as case_id,
												d.id 			as discovery_id, 
												d.uid,
												d.send_date,
												d.propounding,
												d.responding,
												d.served,
												d.discovery_name,
												d.propounding_uid,
												d.responding_uid,
												d.is_send,
												d.form_id 		as form_id,
												d.set_number 	as set_number,
												d.discovery_introduction as introduction,
												f.form_name	 	as form_name,
												f.short_form_name as short_form_name,
												a.firstname 	as atorny_fname,
												a.lastname 		as atorny_lname,
												a.address		as atorny_address,
												a.cityname,
												a.companyname	as atorny_firm,
												d.attorney_id	as attorney_id,
												a.email,
												a.attorney_info 
												',
												"d.id 			= :id AND 
												d.case_id 		= c.id AND  
												d.form_id		= f.id AND
												d.attorney_id 	= a.pkaddressbookid",
												array(":id"=>$discovery_id)
											);
	
	//$AdminDAO->displayquery=0;
	 
	$discovery_data		=	$discoveryDetails[0];
	$uid				=	$discovery_data['uid'];
	$case_uid			=	$discovery_data['case_uid'];
	$discovery_name		=	$discovery_data['discovery_name'];
	
	$case_title			=	$discovery_data['case_title'];
	$case_id			=	$discovery_data['case_id'];
	$is_send			=	$discovery_data['is_send'];
	$set_number			=	$discovery_data['set_number'];
	$atorny_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
	$atorny_address		=	$discovery_data['atorny_address'];
	$atorny_city		=	$discovery_data['cityname'];
	$atorny_firm		=	$discovery_data['atorny_firm'];
	$atorny_email		=	$discovery_data['email'];
	$propounding		=	$discovery_data['propounding'];
	$responding			=	$discovery_data['responding'];
	
	
	$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
	$responding_name		=	$respondingdetails[0]['client_name'];
	$responding_email		=	$respondingdetails[0]['client_email'];
	$responding_type		=	$respondingdetails[0]['client_type'];
	$responding_role		=	$respondingdetails[0]['client_role'];
	$emailURL				=	DOMAIN."discoveryfront.php?uid=".$uid;
	
	//Sender Details
	$senderDetails	=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid = :pkaddressbookid", array(":pkaddressbookid"=>$_SESSION['addressbookid']));
	
	$senderDetail	=	$senderDetails[0];
	$senderEmail	=	$senderDetail['email'];
	$senderPhone	=	$senderDetail['phone'];
	$senderFirm		=	$senderDetail['companyname'];
	$senderName		=	$senderDetail['firstname']." ".$senderDetail['lastname'];	
	$senderAddress	=	makeaddress($_SESSION['addressbookid'], 1);
	
	
	if($actiontype == 1) //Send message to client for verification
	{
		$message_to_client	=	"Hi,<br>You have not verify the discovery SPECIAL INTERROGATORIES. Please click on the link below and verify your discovery.<br>~LINK_HERE~.";//$_POST['message_to_client'];
		$emailURL			=	"<a href='{$emailURL}'>{$emailURL}</a>";	
		$email_body			=	$message_to_client;
		$email_body			=	str_replace("~LINK_HERE~",$emailURL,$email_body);
		ob_start();
		echo nl2br($email_body);
		echo "<p>All rights reserved &copy; ".date('Y')." EasyRogs. U.S. Patent Pending<p>";
		$html = ob_get_contents(); 
		ob_clean();
		//echo $html;
		//exit;
		//$responding_email = "gumptiondevelopers@gmail.com";
		$sender_name	=	$atorny_name;
		$sender_email	=	$atorny_email;
		send_email(array($responding_email),"Verification Issue",$html,$sender_email,$sender_name,1,array(),array());
		//echo "success";	
	}
	else if($actiontype == 2) //Send discovery to client for response
	{
		
		ob_start();
		?>
		<h4>Please click on the following link to respond to discovery in your case: <a href='<?php echo $emailURL; ?>'><?php echo $emailURL; ?></a>.</h4> 
		<p>Feel free to email me at <a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a><?php if($senderPhone != ""){ echo " or call ".$senderPhone;  }?> if you have any questions.</p>
		<p>
		<b>--</b><br /> 
		<?php echo $senderName; ?><br /><?php echo $senderFirm	; ?><br />  
		<?php echo $senderAddress; ?><br />
		<a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a><br />
		<?php echo $senderPhone; ?><br />
		<?php echo "<p>All rights reserved &copy; ".date('Y')." EasyRogs. U.S. Patent Pending<p>"; ?>
        
		</p>
		<?php 
		$html = ob_get_contents(); 
		ob_clean();
		
		//$responding_email = "gumptiondevelopers@yahoo.com"; 
		
		$sender_name	=	$atorny_name;
		$sender_email	=	$atorny_email;
		send_email(array($responding_email),"EasyRogs",$html,$sender_email,$sender_name,1,array(),array());
	}
}
$jsonArray	=	array(
					"messagetype"	 	=> 	2,
					"pkerrorid" 		=> 	7,
					"loadpageurl" 		=> 	"discoveries.php?pid=".$case_id."&pkscreenid=45",
					"loaddivname" 		=> 	"screenfrmdiv",
					"messagetext"		=>	"Email has been sent successfully."
					);
//dump($jsonArray);
echo json_encode($jsonArray);
?>