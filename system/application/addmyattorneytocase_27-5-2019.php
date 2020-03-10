<?php
require_once("adminsecurity.php");
$case_id		=	$_POST['case_id'];
$attorney_id	=	$_POST['attorney_id'];

$fields			=	array("fkcaseid","attorney_id");
$values			=	array($case_id,$attorney_id);
$AdminDAO->insertrow("case_team",$fields,$values);
$msg			=	"Added successfully.";
$type			=	'success';

$jsonArray	=	array("type"=>$type,"msg"=>$msg,"case_id"=>$case_id);
echo json_encode($jsonArray);


