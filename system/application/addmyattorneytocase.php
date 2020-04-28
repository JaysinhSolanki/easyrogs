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

$fields			=	array("fkcaseid","attorney_id");


/***************************************************
*			Attach them to case
****************************************************/

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
	}
}
$msg			=	"Added successfully.";
$type			=	'success';

$jsonArray	=	array("type"=>$type,"msg"=>$msg,"case_id"=>$case_id);
echo json_encode($jsonArray);


