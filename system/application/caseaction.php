<?php
require_once("adminsecurity.php");
$owner				=	$_REQUEST['owner'];
$allow_reminders	=	0;
if($owner == 1)
{
	if($case_title == "")
	{
		msg(301,2);
	}
	if($jurisdiction == "")
	{
		//msg(306,2);
	}
	if($case_number == "")
	{
		//msg(304,2);
	}	
	/*if($filed == "")
	{
		msg(327,2);
	}
	if($trial == "")
	{
		msg(328,2);
	}
	if($discovery_cutoff == "")
	{
		msg(329,2);
	}*/
	if($plaintiff == "")
	{
		msg(312,2);
	}
	if($defendant == "")
	{
		msg(313,2);
	}
	/*if($caseowner == 1)
	{
		if($case_attorney == "")
		{
			msg(335,2);
		}
		if(trim($masterhead) == "")
		{
			msg(336,2);
		}
	}*/
	if($uid == "")
	{
		//Generate UID for cases
		$uid			=	$AdminDAO->generateuid('cases');
	}
	/**
	* Setting Cookies for Attorney County
	**/
	setcookie("ER_ATTORNEY_COUNTY",$county_name,time()+31556926 ,'/');
	
	/**
	* Add current active user as attorney with this case so we show them cases 
	**/
	$casedetails	=	$AdminDAO->getrows("cases","is_draft","id = :id",array("id"=>$id));
	if($casedetails[0]['is_draft'] == 1)
	{
		$AdminDAO->insertrow("attorneys_cases",array("case_id", "attorney_id"),array($id, $_SESSION['addressbookid']));
	}
	
	if($trial != "")
	{
		$trial	=	dateformat($trial,2);
		$trial	=	date("Y-m-d",strtotime($trial));
	}
	if($filed != "")
	{
		$filed	=	dateformat($filed,2);
		$filed	=	date("Y-m-d",strtotime($filed));
	}
	if($discovery_cutoff != "")
	{
		$discovery_cutoff	=	dateformat($discovery_cutoff,2);
		$discovery_cutoff	=	date("Y-m-d",strtotime($discovery_cutoff));
	}
	$fields	=	array('jurisdiction','county_name','case_number','judge_name','department','case_title','plaintiff','defendant','court_address','uid','is_draft','allow_reminders','discovery_cutoff','trial','date_filed');
	$values	=	array($jurisdiction,$county_name,$case_number,$judge_name,$department,$case_title,$plaintiff,$defendant,'--',$uid,0,$allow_reminders,$discovery_cutoff,$trial,$filed);
	if($caseowner == 1)
	{
		$fields[]	=	"attorney_id";
		$values[]	=	$_SESSION['addressbookid'];
		$fields[]	=	"case_attorney";
		$values[]	=	$_SESSION['addressbookid'];//$case_attorney;
		/*
		$fields[]	=	"masterhead";
		$values[]	=	$masterhead;
		*/
	}
	$AdminDAO->updaterow("cases",$fields,$values," id = :id", array("id"=>$id));
}
/********************************************
* Send invitation emails to case team members.
********************************************/

/**
* Get Sender Details
**/
$senderDetails	=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid = :pkaddressbookid", array(":pkaddressbookid"=>$_SESSION['addressbookid']));
$senderDetail	=	$senderDetails[0]; 
$senderEmail	=	$senderDetail['email'];
$senderPhone	=	$senderDetail['phone'];
//$senderName		=	$senderDetail['firstname']." ".$senderDetail['lastname'];
$senderName		=	$senderDetail['firstname'];
$senderFirm		=	$senderDetail['companyname'];

if($senderDetail['middlename'] != "")
{
	$senderName		.=	" ".$senderDetail['middlename'];
}
$senderName		.=	" ".$senderDetail['lastname'];	
$senderAddress	=	makeaddress($_SESSION['addressbookid'],1);//$senderDetail['address'].", ".$senderDetail['cityname'].", ".$senderDetail['street'];

$caseteam_members	=	$AdminDAO->getrows("case_team,attorney","attorney.*,case_team.id as case_team_id","attorney.id = case_team.attorney_id AND case_team.is_deleted = 0 AND case_team.email_sent = 0 AND case_team.fkcaseid = :fkcaseid", array(":fkcaseid"=>$id));
foreach($caseteam_members as $data)
{
	$attorney_name	=	$data['attorney_name'];
	$attorney_email	=	$data['attorney_email'];
	$attorney_id	=	$data['id'];
	$case_team_id	=	$data['case_team_id'];
	
	/**
	* If attorney is not the member of EasyRogs then send invite to him
	**/
	$alreadyEasyRogsMember	=	$AdminDAO->getrows("system_addressbook","*", "email = :email ", array(":email"=>$attorney_email));

	if(sizeof($alreadyEasyRogsMember) == 0)
	{	
		$invitation_uid	=	$AdminDAO->generateuid('invitations');
		
		$link_url		=	DOMAIN."signup.php?uid=".$invitation_uid;
		$fields			=	array("attorney_id","status",'link','uid');
		$values			=	array($attorney_id,1,$link_url,$invitation_uid);
		$AdminDAO->insertrow("invitations",$fields,$values);
		
		/**
		* Send Email to Attorney to become EasyRogs Member
		**/
		$invite_link		=	"<a href='".$link_url."'>".$link_url."</a>";	
		ob_start();
		?>
		<p><?php echo $attorney_name ?>,<br><br> Click this link to join our team re: <?php echo $case_title; ?>. <br> <?php echo $invite_link; ?>.</p>
		<p>
		--
		<br />
		<?php echo $senderName."<br>".$senderFirm."<br>".$senderAddress."<br>".$senderPhone."<br>"; ?><a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a>
		<br>
        <br />
		All rights reserved &copy; <?php echo date('Y');?> EasyRogs. U.S. Patent Pending.
		</p>
		<?php		
		$html = ob_get_contents(); 
		ob_clean();
		$email_body			=	$html;
		$email_subject		=	"Invitation to join ".$case_title;
		$email_bcc			=	array();
		$email_to			=	array($attorney_email);
		$email_from_name	=	$_SESSION['name'];
		$email_from_email	=	$_SESSION['loggedin_email'];
		$email_type			=	1;
		$email_cc			=	array();
		/*echo "<br>Size===========1111<br>";
		echo "<br>email_to===========$email_to<br>";
		echo "<br>email_subject===========$email_subject<br>";
		echo "<br>email_from_email===========$email_from_email<br>";
		echo "<br>email_from_name===========$email_from_name<br>";
		echo "<br>email_type===========$email_type<br>";*/
		
		send_email($email_to,$email_subject,$email_body,$email_from_email,$email_from_name,$email_type,$email_cc,$email_bcc);	
		
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
		/**
		* Send Email to Attorney related to attachment in case as a case member
		**/
		$invite_link		=	"<a href='".$link_url."'>".$link_url."</a>";	
		ob_start();
		?>
		<p><?php echo $sys_member_name ?>,<br><br> You have been invited to join the team in <?php echo $case_title; ?>. Click on the link below to log into EasyRogs and work on it. <br> <?php echo $invite_link; ?></p>
		<p>
		--
        <br>
		<?php echo $senderName."<br>".$senderFirm."<br>".$senderAddress."<br>".$senderPhone."<br>"; ?><a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a>
		<br>
        <br> 
		All rights reserved &copy; <?php echo date('Y');?> EasyRogs. U.S. Patent Pending
		</p>
		<?php		
		$html = ob_get_contents(); 
		ob_clean();
		$email_body			=	$html;
		$email_subject		=	"Invitation to join ".$case_title;
		$email_bcc			=	array();
		$email_to			=	array($sys_member_email);
		$email_from_name	=	$_SESSION['name'];
		$email_from_email	=	$_SESSION['loggedin_email'];
		$email_type			=	1;
		$email_cc			=	array();
		/*echo "<br>Size===========1111<br>";
		echo "<br>email_to===========$email_to<br>";
		echo "<br>email_subject===========$email_subject<br>";
		echo "<br>email_from_email===========$email_from_email<br>";
		echo "<br>email_from_name===========$email_from_name<br>";
		echo "<br>email_type===========$email_type<br>";*/
		send_email($email_to,$email_subject,$email_body,$email_from_email,$email_from_name,$email_type,$email_cc,$email_bcc);
		
	}
	
	/**
	*  Updated email_sent = 1 in case_team table
	**/
	$fields			=	array("email_sent");
	$values			=	array(1);
	$AdminDAO->updaterow("case_team",$fields,$values," id = :case_team_id", array("case_team_id"=>$case_team_id));
}


msg(7);