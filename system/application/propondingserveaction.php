<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
$discovery_id		=	$_POST['discovery_id'];
$posstate			=	$_POST['posstate']; 
$pos_text			=	$_POST['pos_text'];
$poscity				=	$_POST['poscity'];
$posaddress			=	$_POST['posaddress'];
$respond				=	$_POST['respond'];
$discovery_type		=	$_POST['discovery_type'];
$response_id			=	$_POST['response_id'];

$pos_updated_by		=	$_SESSION['addressbookid'];
$pos_updated_at		=	date("Y-m-d H:i:s");
$replace_text		=	'<span style="display:none" id="signtime"></span>';
$pos_updated_at_date	=	date("n/j/Y",strtotime($pos_updated_at));
$pos_updated_at_time	=	str_replace(array('am','pm'),array('a.m','p.m'),date("g:i a",strtotime($pos_updated_at)));
$replace_with		=	"<i>Electronically Served at ".$pos_updated_at_date." ".$pos_updated_at_time.". Pacific Time.</i>";
$pos_text			=	str_replace($replace_text,$replace_with,$pos_text);

if($discovery_type == 1) //If served discovery is external then we save POS details with discovery because in external case we serve discovery.
{
	$served		=	date("Y-m-d H:i:s");
	if($served != "" )
	{
		$served				=	str_replace("-","/",$served);
		$served				=	date("Y-m-d",strtotime($served));
		$extensiondays		=	2;
		$no_of_court_days	=	0;
		
		//Add default 30 days extension 
		$expected_duedate	=	date('Y-m-d', strtotime($served. ' + 30 days'));
		
		if($extensiondays == 2)
		{
			$duedate			=	date('Y-m-d', strtotime($expected_duedate. ' + 1 days'));
		}
		
		$holidays		=	$AdminDAO->getrows('holidays',"date");		
		foreach($holidays as $holiday)
		{
			$holidaysArray[]	=	date($dateformate,strtotime($holiday['date']));
		}
		
		ob_start();
		findWorkingDay($duedate,$extensiondays,$holidaysArray,$no_of_court_days);
		$response_due_date	 = ob_get_clean();
	}
	if($served != "")
	{
		$served	=	dateformat($served,2);
		$served	=	date("Y-m-d",strtotime($served)); 
	}
	$due	=	dateformat($response_due_date,2);
	$due	=	date("Y-m-d",strtotime($due));
	
	$fields				=	array('pos_state','pos_city','pos_text','pos_updated_at','pos_updated_by','is_served','served','due');
	$values				=	array($posstate,$poscity, $pos_text,$pos_updated_at,$pos_updated_by,1,$served,$due);
	$AdminDAO->updaterow("discoveries",$fields,$values,"id ='$discovery_id'");
	
	$fields				=	array('isserved','servedate');
	$values				=	array(1,$pos_updated_at);
	$AdminDAO->updaterow("responses",$fields,$values,"id = '$response_id'");
	
}
else if($discovery_type == 2) // If served discovery is internal then we save POS details with response because in internal case we serve discovery response.
{
	$fields				=	array('posstate','poscity','postext','posupdated_at','posupdated_by','isserved','servedate');
	$values				=	array($posstate,$poscity, $pos_text,$pos_updated_at,$pos_updated_by,1,$pos_updated_at);
	$AdminDAO->updaterow("responses",$fields,$values,"id = '$response_id'");
}

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
												a.middlename 	as atorny_mname,
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
	
	
	$discovery_data		=	$discoveryDetails[0];
	//dump($discoveryDetails,1);
	$uid				=	$discovery_data['uid'];
	$case_uid			=	$discovery_data['case_uid'];
	
	
	$case_id			=	$discovery_data['case_id'];
	$form_name			=	$discovery_data['form_name'];
	$case_title			=	$discovery_data['case_title'];
	$is_send			=	$discovery_data['is_send'];
	$set_number			=	$discovery_data['set_number'];
	$discovery_name		=	$discovery_data['discovery_name']." [Set ".numberTowords( $set_number )."]";
	$atorny_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_mname']." ".$discovery_data['atorny_lname'];
	$atorny_address		=	$discovery_data['atorny_address'];
	$atorny_city		=	$discovery_data['cityname'];
	$atorny_firm		=	$discovery_data['atorny_firm'];
	$atorny_email		=	$discovery_data['email'];
	$propounding		=	$discovery_data['propounding'];
	$responding			=	$discovery_data['responding'];	
	
	/*if($discovery_type == 1 && $respond == 0) 
	{
		  
		//Create a response for external discovery because this discovery is served. Now responding party attorneys show this on front end.
		 
		$response_name		=	"RESPONSE TO $discovery_name";
		$fields_responses	=	array("responsename","fkdiscoveryid","isserved","servedate","submitted_by");
		$values_responses	=	array($response_name,$discovery_id,1,date("Y-m-d H:i:s"),$_SESSION['addressbookid']);
		$AdminDAO->insertrow("responses",$fields_responses,$values_responses);
	}*/
	
	$loggedin_email		=	$_SESSION['loggedin_email'];
	$servicelists		=	$AdminDAO->getrows("attorney","*", "case_id = :case_id  AND attorney_type = :attorney_type AND attorney_email != '$loggedin_email'", array(":case_id"=>$case_id,":attorney_type"=>2));
	
	$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
	$responding_name		=	$respondingdetails[0]['client_name'];
	$responding_email		=	$respondingdetails[0]['client_email'];
	$responding_type		=	$respondingdetails[0]['client_type'];
	$responding_role		=	$respondingdetails[0]['client_role'];
	
	//Propondoing Party
	$propondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$propounding));
	$proponding_name		=	$propondingdetails[0]['client_name'];
	$proponding_email		=	$propondingdetails[0]['client_email'];
	$proponding_type		=	$propondingdetails[0]['client_type'];
	$proponding_role		=	$propondingdetails[0]['client_role'];
	
	
	//Create PDF of this discovery
	if($respond == 1)
	{
		$view = 0;
		$discovery_name	=	"RESPONSE TO $discovery_name";
	}
	else
	{
		$view = 1;
	}
	
	$active_attr_email	=	$_SESSION['loggedin_email'];
	//echo DOMAIN."makepdf.php?id=".$uid."&downloadORwrite=1&view={$view}&active_attr_email={$active_attr_email}";
	//echo "<br>";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,DOMAIN."makepdf.php?id=".$uid."&downloadORwrite=1&view={$view}&active_attr_email={$active_attr_email}&response_id={$response_id}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close ($ch);
	
	//Other Documents
	$discoveryDocuments		=	$AdminDAO->getrows("documents","*","discovery_id = :discovery_id", array(":discovery_id"=>$discovery_id));
	$docsArray				=	array();
	if(!empty($discoveryDocuments))
	{
		foreach($discoveryDocuments as $discoveryDocument)
		{
			$path			=	$_SESSION['system_path']."uploads/documents/".$discoveryDocument['document_file_name'];
			$filename		=	$discoveryDocument['document_file_name'];
			$docsArray[]	=	array("path" => $path, "filename" => $filename);	
		}
	} 
	//Attach discovery Document
	$filename		=	strtoupper($discovery_name).".pdf";
	///echo "<br>";
	$path			=	$_SESSION['system_path']."uploads/documents/".$uid."/".$filename;
	$docsArray[]	=	array("path" => $path, "filename" => $filename);
	
	//Sender Details
	$senderDetails	=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid = :pkaddressbookid", array(":pkaddressbookid"=>$_SESSION['addressbookid']));
	$senderDetail	=	$senderDetails[0];
	$senderEmail	=	$senderDetail['email'];
	$senderPhone	=	$senderDetail['phone'];
	$senderName		=	$senderDetail['firstname'];
	$senderFirm		=	$senderDetail['companyname'];
	if($senderDetail['middlename'] != "")
	{
		$senderName		.=	" ".$senderDetail['middlename'];
	}
	$senderName		.=	" ".$senderDetail['lastname'];
	$senderAddress	=	makeaddress($_SESSION['addressbookid'],1);//$senderDetail['address'].", ".$senderDetail['cityname'].", ".$senderDetail['street'];
	
	//dump($servicelists);
	//exit;
	foreach($servicelists as $list)
	{
		$attorney_name	=	$list['attorney_name'];
		$attorney_email	=	$list['attorney_email'];
		$attorney_id	=	$list['id'];
		/**
		* If attorney is not the member of EasyRogs then send invite to him
		**/
		$alreadyEasyRogsMember	=	$AdminDAO->getrows("system_addressbook","*", "email = :email ", array(":email"=>$attorney_email));
	
		if(sizeof($alreadyEasyRogsMember) == 0)
		{
			$invitation_uid	=	$AdminDAO->generateuid('invitations');
			$emailURL		=	DOMAIN."signup.php?uid=".$invitation_uid;
			$fields			=	array("attorney_id","status",'link','uid');
			$values			=	array($attorney_id,1,$emailURL,$invitation_uid);
			$AdminDAO->insertrow("invitations",$fields,$values);
			
		}
		else 
		{
			$emailURL			=	DOMAIN;
		}	
		$emailURL			=	"<a href='{$emailURL}'>{$emailURL}</a>";
		ob_start();
		?>
		<p>
        <?php echo $list['attorney_name']?>,<br />
		You are hereby served with <?php echo $proponding_name."'s ". str_replace(["set", "For", "Of"], ["Set", "for", "of"], ucwords(strtolower($discovery_name)) ); ?> (attached).<br />
        If you're not already using <i>EasyRogs</i>, click <?php echo $emailURL ?> for a complimentary membership. Plus, the Service Charge is waived through April 30, 2020. <br /><br /><i>EasyRogs</i> makes Discovery Easy, Saves Time & Money, and Protects our Environment. 
        <br />   
        --
		<?php echo $senderName."<br>".$senderFirm."<br>".$senderAddress."<br>".$senderPhone."<br>"; ?><a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a>
		<br>
        All rights reserved &copy; <?php echo date('Y') ?> EasyRogs. U.S. Patent Pending
		</p>
		<?php
		$html = ob_get_contents(); 
		ob_clean();
		$html	=	nl2br($html);
		$senderName		=	$senderName;
		$senderEmail	=	$senderEmail;
		//if($attorney_email == "developers@gumption.pk")
		//{
		//	$attorney_email	=	"gumptiondevelopers@yahoo.com";
		//}
		send_email(array($attorney_email),$case_title,$html,$senderEmail,$senderName,1,array(),array('easyrogs@gmail.com'),$docsArray);	
	}
}
$jsonArray	=	array(
						"messagetype"	 	=> 	2,
						"pkerrorid" 		=> 	7,
						"loadpageurl" 		=> 	"discoveries.php?pid=".$case_id."&pkscreenid=45",
						"loaddivname" 		=> 	"screenfrmdiv",
						"messagetext"		=>	"Data has been served successfully."
						);

echo json_encode($jsonArray);
?>