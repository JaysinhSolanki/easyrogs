<?php
require_once("adminsecurity.php");
$id	=	$_GET['id'];
$AdminDAO->deleterows('questions',"id	=	'$id'");
$AdminDAO->deleterows('discovery_questions',"question_id	=	'$id'");