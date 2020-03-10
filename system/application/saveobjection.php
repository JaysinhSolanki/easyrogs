<?php
require_once("adminsecurity.php");
$id			=	$_POST['discovery_question_id'];
$objection	=	$_POST['objection'];
$fields		=	array("objection");
$values		=	array($objection);
$AdminDAO->updaterow("discovery_questions",$fields,$values," id = :id", array("id"=>$id));
echo "success";