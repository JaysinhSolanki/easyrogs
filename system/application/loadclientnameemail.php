<?php
require_once("adminsecurity.php");
$c_id				=	$_REQUEST['c_id'];
$clientDetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$c_id));
$client_name		=	$clientDetails[0]['client_name'];
$client_email		=	$clientDetails[0]['client_email'];
$client_type		=	$clientDetails[0]['client_type'];
$client_role		=	$clientDetails[0]['client_role'];
//echo json_encode(array("name"=>$client_name,"email"=>$client_email));
$message	=	$client_name;
if($client_email != "")
{
	$message	.=	"<br>".$client_email;
}
echo "<span style='text-align:center'>".$message."</span>";