<?php
require_once("adminsecurity.php");
@session_start();


/***************************************************
*	Get all non selected my team attorneys
****************************************************/

$fkaddressbookid		=	$_SESSION['addressbookid'];
$case_id				=	$_POST['case_id'];
//$AdminDAO->displayquery=1;
$getAlreadyselected		=	$AdminDAO->getrows("case_team","GROUP_CONCAT(attorney_id) as attorney_ids", "fkcaseid = :case_id", array(":case_id"=>$case_id)); 
$attorney_ids			=	$getAlreadyselected[0]['attorney_ids'];
$where		=	"";
if($attorney_ids != "")
{
	$where		=	" AND id NOT IN ($attorney_ids) ";	
}
$attorneys		=	$AdminDAO->getrows("attorney","*", "fkaddressbookid = '$fkaddressbookid' AND attorney_type = 1 {$where}", array(), "attorney_name", "ASC");
//dump($attorneys);
$fields			=	array("fkcaseid","attorney_id");
//exit;
/***************************************************
*			Attach them to case
****************************************************/
/*dump($attorneys);
exit;*/
if($_SESSION['groupid'] == 3)
{
	foreach($attorneys as $attorney)
	{
		$attorney_id	=	$attorney['id'];
		$attorney_email	=	$attorney['attorney_email'];
		$attorney_name	=	$attorney['attorney_name'];
		$values			=	array($case_id,$attorney_id);
		$AdminDAO->insertrow("case_team",$fields,$values);
		
		/**
		*Update attorney case id. Because when these attornies are created at that time caseid is 0 (e.g from profile page)
		**/
		$AdminDAO->updaterow("attorney",array('case_id'),array($case_id),"id = '$attorney_id'");
		
		/**
		*Check this attorney is the member of EasyRogs Team?
		**/
		/*$alreadyEasyRogsMember	=	$AdminDAO->getrows("system_addressbook","*", "email = :email ", array(":email"=>$attorney_email));
		if(sizeof($alreadyEasyRogsMember) == 0)
		{	
			$invitation_uid	=	$AdminDAO->generateuid('invitations');
			
			$link_url					=	DOMAIN."signup.php?uid=".$invitation_uid;
			$fields_invitation			=	array("attorney_id","status",'link','uid');
			$values_invitation			=	array($attorney_id,0,$link_url,$invitation_uid);
			$AdminDAO->insertrow("invitations",$fields_invitation,$values_invitation);
			
			
			 //Send Email to Attorney to become EasyRogs Member
			
			$invite_link		=	"<a href='".$link_url."'>".$link_url."</a>";	
			ob_start();
			?>
			<p>Hi <?php echo $attorney_name ?>,<br><br> Click on the link below to join our litigation team for case. <br> <?php echo $invite_link; ?></p>
			<p>
			--
			<?php echo $senderName."<br>".$senderAddress."<br>".$senderPhone."<br>"; ?><a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a>
			<br>
			All rights reserved &copy; <?php echo date('Y') ?> EasyRogs.
			</p>
			<?php		
			$html = ob_get_contents(); 
			ob_clean();
			$email_body			=	$html;
			$email_subject		=	"Invitation to Become EasyRogs Member";
			$email_bcc			=	array();
			$email_to			=	array($attorney_email);
			$email_from_name	=	$_SESSION['name'];//"EasyRogs";
			$email_from_email	=	$_SESSION['loggedin_email'];//"easyrogs@gmail.com";
			$email_type			=	1;
			$email_cc			=	array();
			
			if (filter_var($attorney_email, FILTER_VALIDATE_EMAIL)) 
			{
				
				send_email($email_to,$email_subject,$email_body,$email_from_email,$email_from_name,$email_type,$email_cc,$email_bcc);	
			}
			
		}
		else
		{
			$sys_memberdetails	=	$alreadyEasyRogsMember[0];
			$sys_member_id		=	$sys_memberdetails['pkaddressbookid'];
			$sys_member_email	=	$attorney_email;//$sys_memberdetails['email'];
			$sys_member_name	=	$attorney_name;//$sys_memberdetails['firstname']." ".$sys_memberdetails['lastname'];
				
			
			$link_url			=	DOMAIN."userlogin.php?outside=1";
			$fields				=	array("attorney_id","case_id");
			$values				=	array($sys_member_id,$case_id);
			$AdminDAO->insertrow("attorneys_cases",$fields,$values);
			
			// Send Email to Attorney related to attachment in case as a case member
			
			$invite_link		=	"<a href='".$link_url."'>".$link_url."</a>";	
			
			ob_start();
			?>
			<p>Hi <?php echo $sys_member_name ?>,<br><br> You are the member of new case team. Click on the link below and login to EasyRogs system and start work on it. <br> <?php echo $invite_link; ?></p>
			<p>
			--
			<?php echo $senderName."<br>".$senderAddress."<br>".$senderPhone."<br>"; ?><a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a>
			<br>
			All rights reserved &copy; <?php echo date('Y') ?> EasyRogs.
			</p>
			<?php		
			$html = ob_get_contents(); 
			ob_clean();
			$email_body			=	$html;
			
			//$email_body			=	"Hi {$sys_member_name},<br> You are the member of new case team. Click on the link below and login to EasyRogs system and start work on it.<br>".$invite_link;
			
			
			$email_subject		=	"Join a case team.";
			$email_bcc			=	array();
			$email_to			=	array($sys_member_email);
			$email_from_name	=	$_SESSION['name']; //"EasyRogs";
			$email_from_email	=	$_SESSION['loggedin_email'];//"easyrogs@gmail.com";
			$email_type			=	1;
			$email_cc			=	array();
			
			send_email($email_to,$email_subject,$email_body,$email_from_email,$email_from_name,$email_type,$email_cc,$email_bcc);
			
		}*/
		
		
	}
}
$msg			=	"Added successfully.";
$type			=	'success';

$jsonArray	=	array("type"=>$type,"msg"=>$msg,"case_id"=>$case_id);
echo json_encode($jsonArray);


