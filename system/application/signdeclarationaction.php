<?php
@session_start();
require_once("adminsecurity.php");
$discovery_id			=	$_POST['discovery_id'];
$declaration_text		=	$_POST['declaration_text'];
$dec_city				=	$_POST['dec_city'];
$dec_state				=	$_POST['dec_state'];
$declaration_updated_by	=	$_SESSION['addressbookid'];
$declaration_updated_at	=	date("Y-m-d H:i:s");
$declaration_text		=	$declaration_text." <p>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct. Executed at {$dec_city}, {$dec_state}</p>";
//$AdminDAO->displayquery=1;
$fields		=	array('declaration_text','declaration_updated_at','declaration_updated_by','dec_city','dec_state');
$values		=	array($declaration_text,$declaration_updated_at,$declaration_updated_by,$dec_city,$dec_state);
$AdminDAO->updaterow("discoveries",$fields,$values,"id ='$discovery_id'");
?>