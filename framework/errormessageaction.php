<?php
require_once("adminsecurity.php");
/*$errormsg			=	$_REQUEST['errormsg'];
$errormsgother		=	$_REQUEST['errormsgother'];
$errortype			=	$_REQUEST['errortype'];

$id	=	$_REQUEST['id'];
*/
if($errormsg == "")
{
	msg(76,2);
}
if($errortype == "")
{
	msg(77,2);
}


$fields		=	array('errormsg','errortype');
$values		=	array($errormsg,$errortype);
if($id > 0)
{
	$AdminDAO->updaterow("system_errors",$fields,$values," pkerrorid = '$id'");
}
else
{
	$id	=	$AdminDAO->insertrow("system_errors",$fields,$values);
}
msg(7);
?>