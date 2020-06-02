<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$discovery_id		= $_POST['discovery_id'];
$pos_text			= $_POST['pos_text'];
$posstate			= $_POST['posstate']; 
$poscity			= $_POST['poscity'];
$posaddress			= $_POST['posaddress'];
$respond			= $_POST['respond'];
$discovery_type		= $_POST['discovery_type'];
$response_id		= $_POST['response_id'];

if (!$discoveriesModel->isPaid($discovery_id)) {
	HttpResponse::paymentRequired();
}

$pos_updated_by			= $_SESSION['addressbookid'];
$pos_updated_at			= date("Y-m-d H:i:s");
$replace_text			= '<span style="display:none" id="signtime"></span>';
$pos_updated_at_date	= date("n/j/Y",strtotime($pos_updated_at));
$pos_updated_at_time	= str_replace(array('am','pm'),array('a.m','p.m'),date("g:i a",strtotime($pos_updated_at)));
$replace_with			= "<i>Electronically Served at ".$pos_updated_at_date." ".$pos_updated_at_time.". Pacific Time.</i>";
$pos_text				= str_replace($replace_text,$replace_with,$pos_text);

if( $discovery_type == Discovery::TYPE_EXTERNAL ) { //If served discovery is external then we save POS details with discovery because in external case we serve discovery.
	// --
	// $served = date("Y-m-d H:i:s");
	// if( $served != "" ) {
	// 	$served				= str_replace( "-", "/", $served );
	// 	$served				= date( "Y-m-d", strtotime($served) );
	// --
	$served             = date("Y-m-d");
	$extensiondays		= 2;
	$no_of_court_days	= 0;
	
	//Add default 30 days extension 
	$expected_duedate = date( 'Y-m-d', strtotime($served. ' + 30 days') );
	
	if( $extensiondays == 2 ) {
		$duedate			= date('Y-m-d', strtotime($expected_duedate. ' + 1 days'));
	}
	
	$holidays = $AdminDAO->getrows('holidays',"date");
	foreach( $holidays as $holiday ) {
		$holidaysArray[] = date( $dateformate, strtotime($holiday['date']) );
	}
	
	ob_start();
	findWorkingDay( $duedate, $extensiondays, $holidaysArray, $no_of_court_days );
	$response_due_date	 = ob_get_clean();

	$served	= dateformat( $served, 2 );
	$served	= date( "Y-m-d", strtotime($served) ); 

	$due	= dateformat( $response_due_date, 2 );
	$due	= date("Y-m-d",strtotime($due));
	
	$fields				= array( 'pos_state', 'pos_city', 'pos_text', 'pos_updated_at', 'pos_updated_by', 'is_served', 'served', 'due', 'is_work_in_progress' );
	$values				= array( $posstate,   $poscity,   $pos_text,  $pos_updated_at,  $pos_updated_by,  1,           $served,  $due,  0 );
	$AdminDAO->updaterow( "discoveries", $fields, $values, "id ='$discovery_id'" );
	
	$fields				= array('isserved','servedate');
	$values				= array(1,$pos_updated_at);
	$AdminDAO->updaterow( "responses", $fields, $values, "id = '$response_id'" );
}
else if( $discovery_type == Discovery::TYPE_INTERNAL ) { // If served discovery is internal then we save POS details with response because in internal case we serve discovery response.
	$fields				= array('posstate','poscity','postext','posupdated_at','posupdated_by','isserved','servedate');
	$values				= array($posstate,$poscity, $pos_text,$pos_updated_at,$pos_updated_by,1,$pos_updated_at);
	$AdminDAO->updaterow( "responses", $fields, $values, "id = '$response_id'" );
}

$discoveryDetails	= $AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
											'c.case_title 	as case_title,
											c.plaintiff,
											c.defendant,
											c.case_number 	as case_number,
											c.jurisdiction 	as jurisdiction,
											c.judge_name 	as judge_name,
											c.county_name 	as county_name,
											c.court_address as court_address,
											c.department 	as department, 
											c.uid 	as case_uid, 
											d.case_id 		as case_id,
											d.id 			as discovery_id, 
											d.uid,
											d.send_date,
											d.propounding,
											d.responding,
											d.served,
											d.discovery_name,
											d.propounding_uid,
											d.responding_uid,
											d.is_send,
											d.form_id 		as form_id,
											d.set_number 	as set_number,
											d.discovery_introduction as introduction,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											a.firstname 	as atorny_fname,
											a.middlename 	as atorny_mname,
											a.lastname 		as atorny_lname,
											a.address		as atorny_address,
											a.cityname,
											a.companyname	as atorny_firm,
											d.attorney_id	as attorney_id,
											a.email,
											a.attorney_info
											',
											"d.id 			= :id AND 
											d.case_id 		= c.id AND  
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
											array(":id"=>$discovery_id)
										); 

$discovery_data	= $discoveryDetails[0];

Side::legacyTranslateCaseData($discovery_data['case_id'], $discovery_data);

$uid				= $discovery_data['uid'];
$case_uid			= $discovery_data['case_uid'];
$case_id			= $discovery_data['case_id'];
$form_name			= $discovery_data['form_name'];
$case_title			= $discovery_data['case_title'];
$is_send			= $discovery_data['is_send'];
$set_number			= $discovery_data['set_number'];
$discovery_name		= $discovery_data['discovery_name']." [Set ".numberTowords( $set_number )."]";
$atorny_name		= $discovery_data['atorny_fname']." ".$discovery_data['atorny_mname']." ".$discovery_data['atorny_lname'];
$atorny_address		= $discovery_data['atorny_address'];
$atorny_city		= $discovery_data['cityname'];
$atorny_firm		= $discovery_data['atorny_firm'];
$atorny_email		= $discovery_data['email'];
$propounding		= $discovery_data['propounding'];
$responding			= $discovery_data['responding'];	

$loggedin_email		= $_SESSION['loggedin_email'];
$servicelists		= $AdminDAO->getrows("attorney","*", "case_id = :case_id  AND attorney_type = :attorney_type AND attorney_email != '$loggedin_email'", array(":case_id"=>$case_id,":attorney_type"=>2));

$respondingdetails		= $AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
$responding_name		= $respondingdetails[0]['client_name'];
$responding_email		= $respondingdetails[0]['client_email'];
$responding_type		= $respondingdetails[0]['client_type'];
$responding_role		= $respondingdetails[0]['client_role'];

//Propondoing Party
$propondingdetails		= $AdminDAO->getrows("clients","*","id = :id",array(":id"=>$propounding));
$proponding_name		= $propondingdetails[0]['client_name'];
$proponding_email		= $propondingdetails[0]['client_email'];
$proponding_type		= $propondingdetails[0]['client_type'];
$proponding_role		= $propondingdetails[0]['client_role'];


// Create PDF of this discovery
if( $respond == 1 ) {
	$view = Discovery::VIEW_RESPONDING;
	$discovery_name	= "RESPONSE TO $discovery_name";
}
else {
	$view = Discovery::VIEW_PROPOUNDING;
}

$active_attr_email	= $_SESSION['loggedin_email'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,DOMAIN."makepdf.php?id=".$uid."&downloadORwrite=1&view={$view}&active_attr_email={$active_attr_email}&response_id={$response_id}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);

//Other Documents
$discoveryDocuments		= $AdminDAO->getrows("documents","*","discovery_id = :discovery_id", array(":discovery_id"=>$discovery_id));
$docsArray				= array();
if( !empty($discoveryDocuments) ) {
	foreach( $discoveryDocuments as $discoveryDocument ) {
		$path			= $_SESSION['system_path']."uploads/documents/".$discoveryDocument['document_file_name'];
		$filename		= $discoveryDocument['document_file_name'];
		$docsArray[]	= array("path" => $path, "filename" => $filename);	
	}
} 

//Attach discovery Document
$filename		= strtoupper($discovery_name).".pdf";
$path			= $_SESSION['system_path']."uploads/documents/".$uid."/".$filename;
$docsArray[]	= array("path" => $path, "filename" => $filename);

$discovery = $discoveriesModel->find($discovery_id);
DiscoveryMailer::propound( $discovery, $currentUser->user, $respond, $docsArray );

$jsonArray	= array(
						"messagetype"	 	=> 	2,
						"pkerrorid" 		=> 	7,
						"loadpageurl" 		=> 	"discoveries.php?pid=$discovery[case_id]&pkscreenid=45",
						"loaddivname" 		=> 	"screenfrmdiv",
						"messagetext"		=>	"Data has been served successfully."
						);

echo json_encode($jsonArray);
?>