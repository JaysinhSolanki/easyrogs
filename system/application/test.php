<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
error_reporting();
$to		=	array("gumptiondevelopers@yahoo.com");
$bcc	=	array();
$acc	=	array();
$docs	=	array("path"=>"/home1/asaamico/public_html/easyrogs/system/uploads/documents/pdkfrjGFRPtNhxmg/SUPPLEMENTAL-AMENDED 2 SPECIAL INTERROGATORIES [SET 1].pdf","filename"=>"SUPPLEMENTAL-AMENDED 2 SPECIAL INTERROGATORIES [SET 1].pdf");
send_email($to,"Testing Email","<h1>Testing Email From Gumption Technologies</h1>","service@easyrogs.com","EasyRogs Service",1,$acc,$bcc,$docs);