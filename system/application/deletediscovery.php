<?php
@session_start();
require_once("adminsecurity.php");
$discovery_uid = $_POST['discovery_uid'];

$discoverydetails = $AdminDAO->getrows('discoveries',"*","uid = :uid",array(":uid"=>$discovery_uid));

if( sizeof($discoverydetails) ) {
	$case_id		= $discoverydetails[0]['case_id'];
	$form_id		= $discoverydetails[0]['form_id'];
	$discovery_id	= $discoverydetails[0]['id'];
	
	$discoveryresponses = $AdminDAO->getrows('responses',"*","fkdiscoveryid = :fkdiscoveryid",array("fkdiscoveryid"=>$discovery_id));
	if( !empty($discoveryresponses) ) {
		foreach( $discoveryresponses as $response_data ) {
			$response_id = $response_data['id'];
			$AdminDAO->deleterows( 'responses',"id = :id", array("id"=>$response_id) );
			$AdminDAO->deleterows( 'response_questions'," fkresponse_id = :fkresponse_id", array("fkresponse_id"=>$response_id) );
		}
	}
	if( !in_array($form_id,array(1,2)) ) {
		$AdminDAO->deleterows( 'questions'," discovery_id = :discovery_id", array("discovery_id"=>$discovery_id) );
	}
	$AdminDAO->deleterows( 'discovery_questions'," discovery_id = :discovery_id", array("discovery_id"=>$discovery_id) );
	$AdminDAO->deleterows( 'discoveries'," id = :discovery_id", array("discovery_id"=>$discovery_id) );
	echo $case_id;
}