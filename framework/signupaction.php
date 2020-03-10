<?php
@session_start();
include_once("../includes/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../includes/classes/functions.php");
//require("../includes/classes/helper.php");
//include_once("../includes/classes/filter.php");	
if(@sizeof($_POST) > 0)
{
	extract($_POST);
}
/*//echo "success";
$firstname			=	$_REQUEST['firstname'];
$middlename			=	$_REQUEST['middlename'];
$lastname			=	$_REQUEST['lastname'];
$email				=	$_REQUEST['email'];
$address			=	$_REQUEST['address'];
$password			=	$_REQUEST['password'];
$phone				=	$_REQUEST['phone'];
$fkcountryid		=	$_REQUEST['fkcountryid'];		
$fkstateid			=	$_REQUEST['fkstateid'];
$fkcityid			=	$_REQUEST['fkcityid'];
$zip				=	$_REQUEST['zipcode'];
$fkpackageid		=	$_REQUEST['fkpackageid'];
$pricingtype		=	$_REQUEST['pricingtype'];
$onetime			=	$_REQUEST['onetime'];
$manytime			=	$_REQUEST['manytime'];
$addons				=	$_REQUEST['addons'];*/


/********************************************* User Exists **********************************************/

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


/*if($companyname=="")
{
	msg(288,2);
}
if($address=="")
{
	msg(289,2);
}

if($street=="")
{
	msg(290,2);
}
if($city=="")
{
	msg(291,2);
}
if($fkstateid=="")
{
	msg(292,2);
}
if($zipcode=="")
{
	msg(293,2);
}
if($phone=="")
{
	msg(294,2);
}

if($fkadmittedstateid=="")
{
	msg(295,2);
}

if($barnumber=="")
{
	msg(296,2);
}
if(trim($attorney_info)	==	"")
{
	msg(319,2);
}*/
if(!empty($_SESSION['addressbookid']))//update profile of the user
{
	$fields			=	array('firstname','middlename','lastname','email','companyname','address','street','cityname','fkstateid','zip','phone','fkadmittedstateid','barnumber','fkcountryid');
	$values			=	array($firstname,$middlename,$lastname,$email,$companyname,$address,$street,$city,$fkstateid,$zipcode,$phone,$fkadmittedstateid,$barnumber,254);
	$AdminDAO->updaterow('system_addressbook',$fields,$values," pkaddressbookid = {$_SESSION['addressbookid']}");
	$redirectme	=	"index.php";
}
else
{
	if($password != $confirmpassword || $password=="" || $confirmpassword=="")
	{
		msg(287,2);
	}
	if(empty($agree))
	{
		msg(311,2);
	}
	$uid				=	$AdminDAO->generateuid('system_addressbook');
	$ipaddress			=	$_SERVER['REMOTE_ADDR'];
	$todaydatetime		=	date("Y-m-d H:i:s");
	/********************************************** AddressBook Data *****************************************/
	$fields			=	array('firstname','middlename','lastname','email','password','companyname','address','street','cityname','fkstateid','zip','phone','fkadmittedstateid','barnumber','fkcountryid','signupdate','signupip','fkgroupid','uid','emailverified');
	$values			=	array($firstname,$middlename,$lastname,$email,$password,$companyname,$address,$street,$city,$fkstateid,$zipcode,$phone,$fkadmittedstateid,$barnumber,254,$todaydatetime,$ipaddress,3,$uid,1);
	
	$id				=	$AdminDAO->insertrow("system_addressbook",$fields,$values);
	$redirectme	=	"userlogin.php";
}
msg(7);