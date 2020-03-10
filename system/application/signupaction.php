<?php
@session_start();
require_once("../settings.php");
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
if(@sizeof($_POST) > 0)
{
	extract($_POST);
}
$invited_uid	=	$_POST['uid']; 

if(@$fkgroupid == 1)
{
	if($barnumber == "")
	{ 
		msg(330,2);  
	}
	$fkgroupid = 3;
	$_SESSION['groupid']	=	3;
}
else if(@$fkgroupid == "")
{
	$fkgroupid = 4;
	$_SESSION['groupid']	=	4;
}
if($firstname == "")
{
	msg(283,2);
}
if($lastname == "")
{
	msg(285,2);
}
if($email ==	"")
{
	msg(286,2);
}

if(!empty($_SESSION['addressbookid']))
{
	$validusers			=	$AdminDAO->getrows("system_addressbook","pkaddressbookid","email	=	:email AND pkaddressbookid != :addressbookid ", array(":email"=>$email, ":addressbookid"=>$_SESSION['addressbookid']));
}
else
{
	$validusers			=	$AdminDAO->getrows("system_addressbook","pkaddressbookid","email	=	:email  ", array(":email"=>$email));

}
$isuserid			=	$validusers[0]['pkaddressbookid'];
if($isuserid > 0)
{
	msg(284,2);
}

//
//if($companyname=="")
//{
//	msg(288,2); 
//}
//if($address=="")
//{
//	msg(289,2);
//}
//
//if($street=="")
//{
//	msg(290,2);
//}
//if($city=="")
//{
//	msg(291,2);
//}
//if($fkstateid=="")
//{
//	msg(292,2);
//}
//if($zipcode=="")
//{
//	msg(293,2);
//}
//if($phone=="")
//{
//	msg(294,2);
//}
//
//if($fkadmittedstateid=="")
//{
//	msg(295,2);
//}
//
//
//if(trim($attorney_info)	==	"")
//{
//	msg(319,2);
//}

/**
* Update profile Case
**/
if(!empty($_SESSION['addressbookid']) && $invited_uid == "" && $newsignup == '')//update profile of the user
{
	/**
	* Check Email already Exists
	**/
	if($_SESSION['loggedin_email'] != $email)
	{
		if($verification_code == "")
		{
			msg(333,2);
		}
		else if($verification_code != $_SESSION['verification_code'])
		{
			msg(334,2);
		}
	}
	
	$fields					=	array('email','firstname','middlename','lastname','companyname','address','street','cityname','fkstateid','zip','phone','fkadmittedstateid','barnumber','fkcountryid','fkgroupid');
	$values					=	array($email,$firstname,$middlename,$lastname,$companyname,$address,$street,$city,$fkstateid,$zipcode,$phone,$fkadmittedstateid,$barnumber,254,$fkgroupid);
	$AdminDAO->updaterow('system_addressbook',$fields,$values," pkaddressbookid = {$_SESSION['addressbookid']}");
	$_SESSION['groupid']		=	$fkgroupid;
	$_SESSION['loggedin_email']	=	$email;
	$redirectme	=	"index.php";
	msg(30);
}
else
{
	
	if($password != $confirmpassword || $password=="" || $confirmpassword=="")
	{
		msg(287,2);
	}
	if(@$termsofservices == "")
	{
		msg(331,2);
	}
	if($newsignup != "")
	{
		if($verification_code == "")
		{
			msg(333,2);
		}
		
		else if($verification_code != $_SESSION['verification_code'])
		{
			msg(334,2);
		}
		
	}
	$uid				=	$AdminDAO->generateuid('system_addressbook');
	$ipaddress			=	$_SERVER['REMOTE_ADDR'];
	$todaydatetime		=	date("Y-m-d H:i:s");
	/**
	* Save Data in addressbook table
	**/
	$fields			=	array('firstname','middlename','lastname','email','password','companyname','address','street','cityname','fkstateid','zip','phone','fkadmittedstateid','barnumber','fkcountryid','signupdate','signupip','fkgroupid','uid','emailverified');
	$values			=	array($firstname,$middlename,$lastname,$email,$password,$companyname,$address,$street,$city,$fkstateid,$zipcode,$phone,$fkadmittedstateid,$barnumber,254,$todaydatetime,$ipaddress,$fkgroupid,$uid,1);
	
	$id				=	$AdminDAO->insertrow("system_addressbook",$fields,$values);
	/**
	* If User comes from invited link 
	**/
	if($invited_uid != "")
	{
		$fields			=	array("status");
		$values			=	array(2);
		$AdminDAO->updaterow("invitations",$fields,$values,"uid = '$invited_uid'");
		
		/**
		*Get case_id from invitations and attorney table
		*Attach user with the case
		**/
		$getAttorneyDetails		=	$AdminDAO->getrows("invitations i,attorney a","case_id","i.uid	=	:uid  AND i.attorney_id = a.id", array(":uid"=>$invited_uid));
		$case_id				=	$getAttorneyDetails[0]['case_id'];
		$fields1				=	array("attorney_id","case_id");
		$values1				=	array($id,$case_id);
		$AdminDAO->insertrow("attorneys_cases",$fields1,$values1);
	}
	/**
	* If User comes as a new user signup
	**/
	else if($newsignup != "")
	{
		$getAllAttornies		=	$AdminDAO->getrows("attorney","*","attorney_type IN (1,2,3) AND attorney_email = '$email'");
		foreach($getAllAttornies as  $attr_data)
		{
			$casesArray		=	array();	
			$attorney_type	=	$attr_data['attorney_type'];
			$attorney_id	=	$attr_data['id'];
			/**
			* If service list attorey
			**/
			if($attorney_type == 2)
			{
				$getAllCases		=	$AdminDAO->getrows("client_attorney","*","attorney_id = :attorney_id",array("attorney_id"=>$attorney_id));	
				foreach($getAllCases as $case_data)
				{
					
					$case_id		=	$case_data['case_id'];
					$casesArray[]	=	$case_id;
				}
			}
			/**
			* If Case team attorney
			**/
			else
			{
				$getAllCases		=	$AdminDAO->getrows("case_team","*","attorney_id = :attorney_id AND is_deleted = 0",array("attorney_id"=>$attorney_id));	
				foreach($getAllCases as $case_data)
				{
					$case_id		=	$case_data['fkcaseid'];
					$casesArray[]	=	$case_id;
				}
			}
			/**
			* Attach cases with that specific attorney
			**/
			if(!empty($casesArray))
			{
				foreach($casesArray as $caseid)
				{
					/**
					* Check if this attorney is already attached with this case in case_attorney table
					**/
					$checkAttrAlreadyAttached		=	$AdminDAO->getrows("attorneys_cases ac, system_addressbook sa","*","ac.attorney_id = sa.pkaddressbookid AND sa.email = :email AND ac.case_id = :case_id",array("email"=>$email,"case_id"=>$caseid));	
					if(sizeof($checkAttrAlreadyAttached) == 0)
					{
						$fields1		=	array("attorney_id","case_id");
						$values1		=	array($id,$caseid);
						$AdminDAO->insertrow("attorneys_cases",$fields1,$values1);	
					}
				}	
			}
			
			/**
			* Update invitation links status = used if available against this attorney in invitation table
			**/
			$fields			=	array("status");
			$values			=	array(2);
			$AdminDAO->updaterow("invitations",$fields,$values,"attorney_id = '$attorney_id'");

		}
	}
	/**
	* Logged In Current User 
	**/
	$_SESSION['addressbookid']	=	$id;
	$_SESSION['name']			=	$firstname." ".$lastname;
	$_SESSION['groupid']		=	$fkgroupid;
	$_SESSION['groupname']		=	"Attorney";
	$_SESSION['loggedin_email']	=	$email;
	$_SESSION['uid']			=	$uid;
	$redirectme	=	"index.php";
	$_SESSION['verification_code']	=	"";
	msg(32);
}
