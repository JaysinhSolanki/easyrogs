<?php
require_once("adminsecurity.php");
$id	=	$_POST['id'];
$AdminDAO->deleterows('clients'," id = :id", array("id"=>$id));
$AdminDAO->deleterows('client_attorney'," client_id = :id", array("id"=>$id));