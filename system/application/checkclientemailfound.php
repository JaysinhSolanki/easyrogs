<?php
require_once("adminsecurity.php");
$discovery_id		=	$_REQUEST['discovery_id'];

$actiontype			=	$_REQUEST['actiontype'];
if($discovery_id > 0)
{
	$discoveryDetails	=	$AdminDAO->getrows('discoveries','*',"id 	= :id",array(":id"=>$discovery_id));
	$discovery_data		=	$discoveryDetails[0];
	$responding			=	$discovery_data['responding'];
}
else
{
	$responding			=	$_REQUEST['client_id'];
}
$respondingdetails	=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
$responding_name	=	$respondingdetails[0]['client_name'];
$responding_email	=	$respondingdetails[0]['client_email'];
if(trim($responding_email) == "")
{
	$found	=	0;
}
else
{
	$found	=	1;
}
echo json_encode(array("discovery_id"=>$discovery_id,"actiontype"=>$actiontype,"found"=>$found,"client_id"=>$responding));
