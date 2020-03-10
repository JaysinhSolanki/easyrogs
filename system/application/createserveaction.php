<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
$discovery_id		=	$_POST['discovery_id'];
$posstate			=	$_POST['posstate'];
$pos_text			=	$_POST['pos_text'];
$poscity			=	$_POST['poscity'];
$pos_updated_by		=	$_SESSION['addressbookid'];
$pos_updated_at		=	date("Y-m-d H:i:s");
$posDataArray		=	array("discovery_id" => $discovery_id, "pos_text" =>$pos_text, "pos_state" =>$posstate, "pos_city" =>$poscity, "pos_updated_at" =>$pos_updated_at, "pos_updated_by" =>$pos_updated_by);

$_SESSION['posdataarray']	=	$posDataArray;
exit; 
/*$fields				=	array('pos_state','pos_city','pos_text','pos_updated_at','pos_updated_by');
$values				=	array($posstate,$poscity, $pos_text,$pos_updated_at,$pos_updated_by);
$AdminDAO->updaterow("discoveries",$fields,$values,"id ='$discovery_id'");

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
												d.is_opened,
												d.is_submitted,
												d.id 			as discovery_id, 
												d.uid,
												d.submit_date,
												d.send_date,
												d.discovery_verification_text,
												d.discovery_verification,
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
												a.attorney_info,
												(CASE WHEN (form_id = 1 OR form_id = 2) 
												 THEN
													  f.form_instructions 
												 ELSE
													  d.discovery_instructions
												 END)
												 as instructions 
												',
												"d.id 			= :id AND 
												d.case_id 		= c.id AND  
												d.form_id		= f.id AND
												d.attorney_id 	= a.pkaddressbookid",
												array(":id"=>$discovery_id)
											);
	
	//dump($discoveryDetails);
	$discovery_data		=	$discoveryDetails[0];
	$uid				=	$discovery_data['uid'];
	$case_uid			=	$discovery_data['case_uid'];
	$discovery_name		=	$discovery_data['discovery_name'];
	
	$case_id			=	$discovery_data['case_id'];
	$form_name			=	$discovery_data['form_name'];
	$case_title			=	$discovery_data['case_title'];
	$is_send			=	$discovery_data['is_send'];
	$set_number			=	$discovery_data['set_number'];
	$atorny_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
	$atorny_address		=	$discovery_data['atorny_address'];
	$atorny_city		=	$discovery_data['cityname'];
	$atorny_firm		=	$discovery_data['atorny_firm'];
	$atorny_email		=	$discovery_data['email'];
	$propounding		=	$discovery_data['propounding'];
	$responding			=	$discovery_data['responding'];
	
	$servicelists	=	$AdminDAO->getrows("attorney a, case_team c","*", "c.fkcaseid = :case_id  AND a.id = c.attorney_id AND is_deleted = 0 ORDER BY a.attorney_name ASC", array(":case_id"=>$case_id));
	
	$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
	$responding_name		=	$respondingdetails[0]['client_name'];
	$responding_email		=	$respondingdetails[0]['client_email'];
	$responding_type		=	$respondingdetails[0]['client_type'];
	$responding_role		=	$respondingdetails[0]['client_role'];
	
	
	//Sender Details
	$senderDetails	=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid = :pkaddressbookid", array(":pkaddressbookid"=>$_SESSION['addressbookid']));
	$senderDetail	=	$senderDetails[0];
	$senderEmail	=	$senderDetail['email'];
	$senderPhone	=	$senderDetail['phone'];
	$senderName		=	$senderDetail['firstname']." ".$senderDetail['lastname'];	
	$senderAddress	=	makeaddress($_SESSION['addressbookid']);//$senderDetail['address'].", ".$senderDetail['cityname'].", ".$senderDetail['street'];
	
	
	foreach($servicelists as $list)
	{
		$emailURL				=	DOMAIN;
		
		$emailURL			=	"<a href='{$emailURL}'>{$emailURL}</a>";	
		$email_body			=	$message_to_client;
		$email_body			=	str_replace("~LINK_HERE~",$emailURL,$email_body);
		ob_start();
		?>
		<p>Hi <?php echo $list['attorney_name']?>,<br />
		You are hereby e-Served with <?php echo $responding_name." ".$form_name; ?> (attached).<br />
		If you prefer hardcopy service, please email <?php echo $senderName ?> at <?php echo $senderEmail ?>.
		To use EasyRogs yourself, click here: <?php echo $emailURL ?>. It's free through August 31, 2019. EasyRogs makes discovery easy, saves time & money, and protects our planet. Give it a try.
		--
		<?php echo $senderName ?><br />
		<?php echo $senderAddress ?><br />
		<?php echo $senderPhone ?><br />
		<?php echo $senderEmail ?><br />
		<br />
		All rights reserved &copy; EasyRogs.
		</p>
		<?php
		$html = ob_get_contents(); 
		ob_clean();
		//echo $html;
		//exit;
		$html	=	nl2br($html);
		$responding_email = "gumptiondevelopers@gmail.com";
		send_email(array($responding_email),$case_title,$html,$senderEmail,"EasyRogs",3,array(),array('easyrogs@gmail.com'));
	}
}*/
?>