<?php
@session_start();
require_once("adminsecurity.php");
//dump($_POST);
echo "ALOOOO";
exit;
$response_id		=	$_POST['response_id'];
$discovery_id		=	$_POST['discovery_id'];
//$AdminDAO->displayquery=1;
$discoveryDetails	=	$AdminDAO->getrows("discoveries","*","id = :id",array("id"=>$discovery_id));
$discoveryDetail	=	$discoveryDetails[0];
$case_id			=	$discoveryDetail['case_id'];
$uid				=	$discoveryDetail['uid'];
$discoveryname		=	$discoveryDetail['discovery_name']." [ SET ".$discoveryDetail['set_number']."]";
$responseDetails	=	$AdminDAO->getrows("responses","*","fkdiscoveryid = :fkdiscoveryid AND fkresponseid != 0",array(":fkdiscoveryid"=>$discovery_id));
$totalResponses		=	sizeof($responseDetails)+1;
//dump($discoveryDetails);
//dump($responseDetails);
//exit;
$responsename		=	strtoupper(numToOrdinalWord($totalResponses))." RESPONSE TO ".$discoveryname;
$response_name		=	"RESPONSE TO $discovery_name";
$fields_responses	=	array("responsename","fkdiscoveryid","fkresponseid","created_by");
$values_responses	=	array($responsename,$discovery_id,$response_id,$_SESSION['addressbookid']);
$response_id		=	$AdminDAO->insertrow("responses",$fields_responses,$values_responses);

function numToOrdinalWord($num)
{
	$first_word = array('eth','First','Second','Third','Fourth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
	$second_word =array('','','Twenty','Thirty','Forty','Fifty');

	if($num <= 20)
		return $first_word[$num];

	$first_num = substr($num,-1,1);
	$second_num = substr($num,-2,1);

	return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
}
echo json_encode(array("uid"=>$uid,"case_id"=>$case_id,"response_id"=>$response_id));