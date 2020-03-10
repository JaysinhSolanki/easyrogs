<?php
require_once("adminsecurity.php");
$client_email		=	$_REQUEST['client_email'];
$actiontype			=	$_REQUEST['actiontype'];
$client_id			=	$_REQUEST['client_id'];
$discovery_id		=	$_REQUEST['discovery_id'];
$case_id			=	$_REQUEST['case_id'];

$fields	=	array('client_email');
$values	=	array($client_email);
$AdminDAO->updaterow("clients",$fields,$values," id = :id", array("id"=>$client_id));

$jsonArray	=	array(
					"actiontype"	 	=> 	$actiontype,
					"discovery_id"	 	=> 	$discovery_id,
					);

echo json_encode($jsonArray);