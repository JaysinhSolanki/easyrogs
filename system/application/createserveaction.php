<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
$discovery_id		=	$_POST['discovery_id'];
$posstate			=	$_POST['posstate'];
$pos_text			=	$_POST['pos_text'];
$poscity			=	$_POST['poscity'];
$pos_updated_by		=	$_SESSION['addressbookid'];
$pos_updated_at		=	date("Y-m-d H:i:s");
$posDataArray		=	array("discovery_id" => $discovery_id, "pos_text" =>$pos_text, "pos_state" =>$posstate, "pos_city" =>$poscity, "pos_updated_at" =>$pos_updated_at, "pos_updated_by" =>$pos_updated_by);

$_SESSION['posdataarray']	=	$posDataArray;