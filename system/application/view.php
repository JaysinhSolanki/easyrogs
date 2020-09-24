<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

$uid				=	$_GET['id'];
$response_id		=	$_GET['response_id'];
$view				=	$_GET['view'];
$respond			=	$_GET['respond'];
$active_attr_email	=	$_SESSION['loggedin_email'];

$discovery_data = $discoveriesModel->findDetails($uid);
 Side::legacyTranslateCaseData(
	$discovery_data['case_id'],
	$discovery_data,
	$discovery_data['attorney_id'] // !! Will use this attorney's side data
);

$case_title			= $discovery_data['case_title'];
$discovery_id		= $discovery_data['discovery_id'];
$case_number		= $discovery_data['case_number'];
$jurisdiction		= $discovery_data['jurisdiction'];
$judge_name			= $discovery_data['judge_name'];
$county_name		= $discovery_data['county_name'];
$court_address		= $discovery_data['court_address'];
$department			= $discovery_data['department'];
$case_id			= $discovery_data['case_id'];
$form_id			= $discovery_data['form_id'];
$set_number			= $discovery_data['set_number'];
$atorny_name		= $discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
$attorney_id		= $discovery_data['attorney_id'];
$send_date			= $discovery_data['send_date'];
$email				= $discovery_data['email'];
$type				= $discovery_data['type'];
$introduction		= $discovery_data['introduction'];
$propounding		= $discovery_data['propounding'];
$responding			= $discovery_data['responding'];
if( ($view == Discovery::VIEW_RESPONDING) || $respond || $response_id ) { //!!
  $discoveryName = $responsesModel->getTitle($response_id, $discovery_data);
} else {
  $discoveryName = $discoveriesModel->getTitle($discovery_data);
}
?>

<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
            <h3 align="center"><strong><?= $case_title ?></strong></h3></div>
            <div class="panel-body">
            <div class="panel panel-primary">
            <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <span style="font-size:18px; font-weight:600"><?= $discoveryName ?></span>
                </div>
            </div>
            </div>
            <div class="panel-body">
                <div class="text-center">
					<a style="margin-bottom:10px" href="javascript:" onclick="selecttab('45_tab','discoveries.php?pid=<?= $case_id ?>','45');" class="btn btn-danger buttonid">
						<i class="fa fa-close" /> Close
					</a>

					<div class="col-md-12" style="min-height:500px">
						<iframe id="loadIFrame" style="height:800px" src="" frameborder="0" height="100%" width="100%"></iframe>
					</div>
					<div class="text-center">
					<a style="margin-bottom:10px" href="javascript:" onclick="selecttab('45_tab','discoveries.php?pid=<?= $case_id ?>','45');" class="btn btn-danger buttonid">
						<i class="fa fa-close" /> Close
					</a>
				</div>
            </div>
            </div>
            </div>

        </div>
    </div>
</div>
<script>
jQuery( $ => { //debugger;
	$.LoadingOverlay("show");
	$.get( "generatePDF_IFrame.php?id=<?= $uid ?>&downloadORwrite=1&view=<?= $view ?>&active_attr_email=<?= $active_attr_email ?>&response_id=<?= $response_id ?>",
		   data => {
				$("#loadIFrame").attr("src",data);
				setTimeout( _ => {
					$.LoadingOverlay("hide");
				}, 2000);
	} );
} );
</script>
