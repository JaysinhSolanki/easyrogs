<?php
@session_start();
include_once("../settings.php");
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
include_once("../library/helper.php");
$discovery_uid	=	$_GET['uid'];
$case_uid		=	$_GET['case_uid'];
$res_attr_uid	=	$_GET['att_uid'];

//Add these values in session 
$_SESSION['responded_case_uid']			=	$case_uid;
$_SESSION['responded_attrorney_uid']	=	$res_attr_uid;
$_SESSION['responded_discovery_uid']	=	$discovery_uid;

if($_SESSION['addressbookid'] != "")
{
	/**************************************************
	*START::If Responded Attornery comes from serve link
	***************************************************/
	$addressbookid	=	$_SESSION['addressbookid'];
	//Add user details to case_attorney and attorney table
	$case_uid		=	@$_SESSION['responded_case_uid'];
	$res_attr_uid	=	@$_SESSION['responded_attrorney_uid'];
	$discovery_uid	=	@$_SESSION['responded_discovery_uid'];
	
	if($res_attr_uid != "")
	{
		$attrfields		=	array('fkaddressbookid');
		$attrvalues		=	array($addressbookid);
		$AdminDAO->updaterow("attorney",$attrfields,$attrvalues,"uid = :uid",array("uid"=>$res_attr_uid));
		
		//Get case id form case_uid
		$getCaseDetails	=	$AdminDAO->getrows("cases","id","uid = '$case_uid'");
		$case_id		=	$getCaseDetails[0]['id'];
		
		//Check already attached with case or not
		$checkAlreadyExists	=	$AdminDAO->getrows("attorneys_cases","id","case_id = '$case_id' AND attorney_id = '$addressbookid'");
		if(sizeof($checkAlreadyExists) == 0)
		{
			$attrcase_fields		=	array('case_id','attorney_id');
			$attrcase_values		=	array($case_id,$_SESSION['addressbookid']);
			$AdminDAO->insertrow("attorneys_cases",$attrcase_fields,$attrcase_values);
		}
	
		$_SESSION['responded_case_uid']			=	'';
		$_SESSION['responded_attrorney_uid']	=	'';
		$_SESSION['responded_discovery_uid']	=	'';
	}
	/***********************************************
	*END::If Responded Attornery comes from serve link
	************************************************/
}
header("Location: userlogin.php");