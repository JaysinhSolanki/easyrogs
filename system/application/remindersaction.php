<?php
@session_start();
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
include_once("../library/helper.php");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);

/*
 *	GET ALL THE CASES WITH REMINDERS ENABLE
 */
$cases		=	$AdminDAO->getrows("cases","*","allow_reminders = 1");	

//remindersemail(1,40);
//exit;
foreach($cases as $case)
{
	$case_id		=	$case['id'];
	$case_title		=	$case['case_title'];
	$case_number	=	$case['case_number'];
	/*
	 *	GET ALL DISCOVERIES OF THAT CASE
	 */
	$discoveries	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
											'c.case_title 	as case_title,
											c.case_number 	as case_number,
											c.jurisdiction 	as jurisdiction,
											c.judge_name 	as judge_name,
											c.county_name 	as county_name,
											c.court_address as court_address,
											c.department 	as department, 
											d.*,
											d.id as discovery_id,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											a.firstname 	as atorny_fname,
											a.lastname 		as atorny_lname,
											a.email			as atorny_email,
											a.address		as atorny_address,
											a.cityname,
											a.street,
											a.zip,
											a.phone,
											a.fkstateid,
											a.companyname	as atorny_firm,
											a.attorney_info',
											"
											c.id			=	:case_id AND
											d.case_id 		= 	c.id AND  
											d.form_id		= 	f.id AND
											d.attorney_id 	= 	a.pkaddressbookid",
											array(":case_id"=>$case_id)
										);

	if(sizeof($discoveries) > 0)
	{
		foreach($discoveries as $discovery)
		{
			$discovery_id		=	$discovery['discovery_id'];
			$discovery_uid		=	$discovery['uid'];
			$due_date			=	$discovery['due'];
			$is_submitted		=	$discovery['is_submitted'];
			$submit_date		=	$discovery['submit_date'];
			$send_date			=	$discovery['send_date'];
			$is_send			=	$discovery['is_send'];
			$is_opened			=	$discovery['is_opened'];
			$open_date			=	$discovery['open_date'];
			$current_date		=	date("Y-m-d");
			$responding			=	$discovery['responding'];
			$discovery_name		=	$discovery['discovery_name']." [".$discovery['set_number']."]";
			$atorny_name		=	$discovery['atorny_fname']." ".$discovery_data['atorny_lname'];
			$atorny_email		=	$discovery['atorny_email'];
			$atorny_address		=	$discovery['atorny_address'];
			$atorny_city		=	$discovery['cityname'];
			$atorny_zip			=	$discovery['zip'];
			$atorny_street		=	$discovery['street'];
			$atorny_phone		=	$discovery['phone'];
			$fkstateid			=	$discovery['fkstateid'];
			$atorny_firm		=	$discovery['atorny_firm'];
			$getState			=	$AdminDAO->getrows("system_state","*","pkstateid = :id",array(":id"=>$fkstateid));
			$atorny_state		=	$getState[0]['statename'];
			$atorny_state_short	=	$getState[0]['statecode'];
			$admin_link			=	"<a href='".DOMAIN."'>".DOMAIN."</a>";
			$front_link			=	"<a href='".DOMAIN."discoveryfront.php?uid=".$discovery_uid."'>".DOMAIN."discoveryfront.php?uid=".$discovery_uid."</a>";	
			
			/********************************************************************************************************
												 7 days before the Response is due.
			*********************************************************************************************************/
			if(dateDiffInDays($due_date,$current_date) == 7)
			{
				$toemails	=	array();
				$pkemailid	=	1;
				/*
				*Attorney Details
				*/
					$atorney_name	=	$discovery['atorny_fname']." ".$discovery['atorny_lname'];
					$atorny_email	=	$discovery['atorny_email'];
					if (filter_var($atorny_email, FILTER_VALIDATE_EMAIL)) 
					{
						$toemails[]		=	$atorny_email;
					}
					//echo "<br>Discovery:$discovery_id<br>";
					//echo "<br>$atorney_name===$atorny_email<br>";
				/*
				*Case Team Details
				*/
					$caseteam		=	$AdminDAO->getrows("attorney a ,case_team ct","a.*,ct.id as case_team_id", "ct.attorney_id = a.id AND ct.fkcaseid = :case_id AND ct.is_deleted  = 0", array(":case_id"=>$case_id), "attorney_name", "ASC");
					//dump($caseteam);
					foreach($caseteam as $team)
					{
						$case_team_email		=	$team['attorney_email'];
						$case_team_name		=	$team['attorney_name'];
						if (filter_var($case_team_email, FILTER_VALIDATE_EMAIL)) 
						{
							$toemails[]		=	$case_team_email;
						}
					}
				/*
				*Responding Party Details
				*/
					$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
					$respondingdetail		=	$respondingdetails[0];
					$responding_name		=	$respondingdetail['client_name'];
					$responding_email		=	$respondingdetail['client_email'];
					if (filter_var($responding_email, FILTER_VALIDATE_EMAIL)) 
					{
						$toemails[]		=	$responding_email;
					}
					//echo "<br>$responding_name===$responding_email<br>";
					//echo "<br>==============================================<br>";
					//dump($toemails);
				
				/*
				* Get email body detals from table
				*/
				
				$emailDetails		=	$AdminDAO->getrows("system_email","*","pkemailid = :id",array(":id"=>$pkemailid));
				//dump($emailDetails);
				$emailDetail		=	$emailDetails[0];
				$bodyhtml			=	$emailDetail['bodyhtml'];
				$sender_details	=	"<br>$atorny_name<br>$atorny_firm<br>$atorny_address, $atorny_street<br>$atorny_city, $atorny_state_short $atorny_zip<br>$atorny_phone<br>$atorny_email";
				$bodyhtml		=	str_replace("~discovery_name~",$discovery_name,$bodyhtml);
				$bodyhtml		=	str_replace("~admin_link~",$admin_link,$bodyhtml);
				$bodyhtml		=	str_replace("~sender_details~",$sender_details,$bodyhtml);
			}
			/******************************************************************************************************
									5 days before the Party's Response is due to her Attorney.
			******************************************************************************************************/
			if(dateDiffInDays($due_date,$current_date) == 5 && $is_submitted == 0)
			{
				$toemails	=	array();
				$pkemailid	=	2;
				
				/*
				*Responding Party Details
				*/
					$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
					$respondingdetail		=	$respondingdetails[0];
					$responding_name		=	$respondingdetail['client_name'];
					$responding_email		=	$respondingdetail['client_email'];
					if (filter_var($responding_email, FILTER_VALIDATE_EMAIL)) 
					{
						$toemails[]		=	$responding_email;
					}
					//echo "<br>$responding_name===$responding_email<br>";
					//echo "<br>==============================================<br>";
					//dump($toemails);
				
				/*
				* Get email body detals from table
				*/
				
				$emailDetails		=	$AdminDAO->getrows("system_email","*","pkemailid = :id",array(":id"=>$pkemailid));
				$emailDetail		=	$emailDetails[0];
				$bodyhtml			=	$emailDetail['bodyhtml'];
				
				$sender_details		=	"<br>$atorny_name<br>$atorny_firm<br>$atorny_address, $atorny_street<br>$atorny_city, $atorny_state_short $atorny_zip<br>$atorny_phone<br>$atorny_email";
				$bodyhtml			=	str_replace("~discovery_name~",$discovery_name,$bodyhtml);
				$bodyhtml			=	str_replace("~attorney_email~",$atorny_email,$bodyhtml);
				$bodyhtml			=	str_replace("~attorney_phone~",$atorny_phone,$bodyhtml);
				$bodyhtml			=	str_replace("~front_link~",$front_link,$bodyhtml);
				$bodyhtml			=	str_replace("~sender_details~",$sender_details,$bodyhtml);
			}
			
			/******************************************************************************************************
			5 days after the Attorney sent the Discovery, if the Party hasn't looked at it, we send the Party a reminder. 
			******************************************************************************************************/
			if($is_send == 1)
			{
				$FivedaysAfterSendtoClient	=	date('Y-m-d', strtotime($Date. ' + 5 days')); 
				if((strtotime($current_date) == strtotime($FivedaysAfterSendtoClient)) && ($is_submitted == 0))
				{
					$toemails	=	array();
					$pkemailid	=	3;
					
					/*
					*Responding Party Details
					*/
						$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
						$respondingdetail		=	$respondingdetails[0];
						$responding_name		=	$respondingdetail['client_name'];
						$responding_email		=	$respondingdetail['client_email'];
						if (filter_var($responding_email, FILTER_VALIDATE_EMAIL)) 
						{
							$toemails[]		=	$responding_email;
						} 
						//echo "<br>$responding_name===$responding_email<br>";
						//echo "<br>==============================================<br>";
						//dump($toemails);
					
					/*
					* Get email body detals from table
					*/
					
					$emailDetails		=	$AdminDAO->getrows("system_email","*","pkemailid = :id",array(":id"=>$pkemailid));
					$emailDetail		=	$emailDetails[0];
					$bodyhtml			=	$emailDetail['bodyhtml'];
					
					$sender_details		=	"<br>$atorny_name<br>$atorny_firm<br>$atorny_address, $atorny_street<br>$atorny_city, $atorny_state_short $atorny_zip<br>$atorny_phone<br>$atorny_email";
					$bodyhtml			=	str_replace("~discovery_name~",$discovery_name,$bodyhtml);
					$bodyhtml			=	str_replace("~attorney_email~",$atorny_email,$bodyhtml);
					if($due_date != "")
					{
						$dateSentense		=	"It's due back to your attorney by ".dateformat($due_date).".";
						
					}
					else
					{
						$dateSentense		=	"";
					}
					$bodyhtml			=	str_replace("~due_date~",$dateSentense,$bodyhtml);
					$bodyhtml			=	str_replace("~attorney_phone~",$atorny_phone,$bodyhtml);
					$bodyhtml			=	str_replace("~front_link~",$front_link,$bodyhtml);
					$bodyhtml			=	str_replace("~sender_details~",$sender_details,$bodyhtml);
				}	
			}
			
			$toemails[]			=	"gumptiondevelopers@gmail.com";
			/*
			* Send Email Function Call
			*/
			$email_body			=	$bodyhtml;
			$email_subject		=	$case_title;
			$email_bcc			=	array("easyrogs@gmail.com");
			$email_to			=	$toemails;
			//$email_from_name	=	"EasyRogs";
			//$email_from_email	=	"easyrogs@gmail.com";
			$email_from_name	=	"EasyRogs Service";
			$email_from_email	=	"service@easyrogs.com";
			$email_type			=	1;
			$email_cc			=	array();
			
			//echo "<br>$email_subject<br>";
			//echo "<br>$email_body<br>";
			//echo "<br>".dump($email_bcc)."<br>";
			//echo "<br>".dump($email_to)."<br>";
			//echo "<br>==============================================<br>";
			if(sizeof($email_to) > 0)
			{
				send_email($email_to,$email_subject,$bodyhtml,$email_from_email,$email_from_name,$email_type,$email_cc,$email_bcc);	
			}
		}
	}
}



/*
 *	FUNCTION FOR CALCULATING DAYS BETWEEN TWO DATES
 */
function dateDiffInDays($date1, $date2)  
{ 
    $diff = strtotime($date2) - strtotime($date1);   
    return abs(round($diff / 86400)); 
} 

