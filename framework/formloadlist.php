<?php
require_once("formclass.php");
$parentfieldid	=	$_POST['fieldid'];
$fkformfieldid	=	$_POST['fkformfieldid'];
$selectedvalue	=	$_POST['selectedvalue'];
$formfields	=	$AdminDAO->getrows("system_formfield","*","pkformfieldid	=	'$fkformfieldid'");
$GAF->makeFields($formfields,$parentfieldid);
require_once("formgridjs.php");