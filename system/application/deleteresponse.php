<?php
@session_start();
require_once("adminsecurity.php");
$response_id	=	$_POST['response_id'];
//$AdminDAO->displayquery=1;
$AdminDAO->deleterows('responses',"id = :id",array("id"=>$response_id));
$AdminDAO->deleterows('response_questions'," fkresponse_id = :fkresponse_id", array("fkresponse_id"=>$response_id)); 
