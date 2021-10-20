<?php
require_once __DIR__ . '/../bootstrap.php';

require_once("adminsecurity.php");
$uid				=	$_GET['id'];
$response_id		=	$_GET['response_id'];

$view				=	$_GET['view'] ?: Discovery::VIEW_RESPONDING;
//$respond			=	$_GET['respond'] ?: 0;
$downloadORwrite	=	$_GET['downloadORwrite'];
$active_attr_email	=	$_GET['active_attr_email'];

// $discovery_data = $discoveries->findDetails($uid);
// Side::legacyTranslateCaseData($discovery_data['case_id'], $discovery_data);

// $case_title		= $discovery_data['case_title'];
// $discovery_id	= $discovery_data['discovery_id'];
// $case_number		= $discovery_data['case_number'];
// $jurisdiction	= $discovery_data['jurisdiction'];
// $judge_name		= $discovery_data['judge_name'];
// $county_name		= $discovery_data['county_name'];
// $court_address	= $discovery_data['court_address'];
// $department		= $discovery_data['department'];
// $case_id			= $discovery_data['case_id'];
// $form_id			= $discovery_data['form_id'];
// $set_number		= $discovery_data['set_number'];
// $atorny_name		= $discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
// $attorney_id		= $discovery_data['attorney_id'];
// $form_name		= $discovery_data['form_name']." [SET ".$set_number."]";
// $short_form_name	= $discovery_data['short_form_name'];
// $send_date		= $discovery_data['send_date'];
// $email			= $discovery_data['email'];
// $instructions	= $discovery_data['discovery_instrunctions'];
// $type			= $discovery_data['type'];
// $introduction	= $discovery_data['introduction'];
// $propounding		= $discovery_data['propounding'];
// $responding		= $discovery_data['responding'];

// //Responding Party
// $respondingdetails = $AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
// $responding_name = $respondingdetails[0]['client_name'];
// $responding_email = $respondingdetails[0]['client_email'];
// $responding_type = $respondingdetails[0]['client_type'];
// $responding_role = $respondingdetails[0]['client_role'];

// //Propondoing Party
// $propondingdetails = $AdminDAO->getrows("clients","*","id = :id",array(":id"=>$propounding));
// $proponding_name	= $propondingdetails[0]['client_name'];
// $proponding_email = $propondingdetails[0]['client_email'];
// $proponding_type	= $propondingdetails[0]['client_type'];
// $proponding_role	= $propondingdetails[0]['client_role'];

// $discoveryName = ( $response_id || $respond )
// 						? $responsesModel->getTitle($response_id, $discovery_data)
// 						: $discoveriesModel->getTitle($discovery_data);

// $PDFname = strtoupper($discoveryName).".pdf";
// //if($type == 2)
// $fileName = SYSTEMPATH."uploads/documents/".$uid."/".$PDFname;
// $useCache = !in_array($_ENV['APP_ENV'], ['dev', 'local', 'development']);
// if( !$useCache || !file_exists($fileName)) {
// 	 $ch = curl_init();
// 	 curl_setopt($ch, CURLOPT_URL,DOMAIN."makepdf.php?id=".$uid."&downloadORwrite=0&view={$view}&active_attr_email={$active_attr_email}&response_id={$response_id}");
// 	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	 $server_output = curl_exec($ch);
// 	 curl_close ($ch);
// }
// $urlPDF	=	UPLOAD_URL."documents/".$uid."/".$PDFname;

//FROM PDFjs
echo $_SESSION['framework_url']."pdfjs/web/viewer.php?url=".DOMAIN."makepdf.php?id=$uid&view=$view&active_attr_email=$active_attr_email&response_id=$response_id";
//FROM GOOGLE
//echo "https://docs.google.com/viewerng/viewer?url={$urlPDF}&embedded=true";
