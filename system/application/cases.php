<?php
@session_start();
require_once("adminsecurity.php");
$loggedin_email		=	$_SESSION['loggedin_email'];

/**************************************************
*START::If Responded Attornery comes from serve link
***************************************************/
$addressbookid	=	$_SESSION['addressbookid'];
//Add user details to case_attorney and attorney table
$case_uid		=	@$_SESSION['responded_case_uid'];
$res_attr_uid	=	@$_SESSION['responded_attrorney_uid'];
$discovery_uid	=	@$_SESSION['responded_discovery_uid'];

if($res_attr_uid != "")
{
	$attrfields		=	array('fkaddressbookid');
	$attrvalues		=	array($addressbookid);
	$AdminDAO->updaterow("attorney",$attrfields,$attrvalues,"uid = :uid",array("uid"=>$res_attr_uid));

	//Get case id form case_uid
	$getCaseDetails	=	$AdminDAO->getrows("cases","id","uid = '$case_uid'");
	$case_id		=	$getCaseDetails[0]['id'];

	//Check already attached with case or not
	$checkAlreadyExists	=	$AdminDAO->getrows("attorneys_cases","id","case_id = '$case_id' AND attorney_id = '$addressbookid'");
	if(sizeof($checkAlreadyExists) == 0)
	{
		$attrcase_fields		=	array('case_id','attorney_id');
		$attrcase_values		=	array($case_id,$_SESSION['addressbookid']);
		$AdminDAO->insertrow("attorneys_cases",$attrcase_fields,$attrcase_values);
	}

	$_SESSION['responded_case_uid']			=	'';
	$_SESSION['responded_attrorney_uid']	=	'';
	$_SESSION['responded_discovery_uid']	=	'';
}
/***********************************************
*END::If Responded Attornery comes from serve link
************************************************/

?>
<div id="screenfrmdiv" style="display: block;">

<div id="create-case-error-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="existing-case-modal-header" style="font-size: 22px;">Error</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4>
					To create a case you must be either:
					<br/>
					<br/>
					<ol>
						<li>An attorney, or</li>
						<li>A member of an attorney's team.</li>
					</ol>
					<br/>
					If you work for an attorney, ask them to add you to their Team.
				</h4>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<div id="join-case-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" id="join-case-modal-content">
    	<div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="existing-case-modal-header" style="font-size: 22px;">Join Existing Case</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4 class="join-case-text"></h4>
        <div class="join-case-search join-case-action">
					<select class="case-search" id="join-case-id" ></select>
				</div>
        <br/>
        <div class="form-group join-case-clients join-case-action" style="display: none">
          <label>Request to join representing</label>
          <select name="client" id="join-case-client" class="form-control">
            <option>Select a Party</option>
					</select>
					<input type="hidden" value="" id="join-existing-case-id" />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success join-case-action" id="join-case-btn">Join</button>
        <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<div id="new-user-video-modal" class="modal fade" role="dialog" style="min-width: 95vw; min-height: 95vh;">
  <div class="modal-dialog" style="width: 75%; margin:2rem auto; padding:0;">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" style="text-align:center; font-size: 22px;">Get Started Using EasyRogs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
            <video id="intro-video" preload="none" x-autoplay controls style="max-width:100%;max-height:100%;">
                <source src="<?= ROOTURL ?>system/application/getting_started.mp4" type="video/mp4">
            </video>
        </div>
      </div>
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</a>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading" align="center">
            <h3 align="center"><strong>My Cases</strong></h3>
        </div>
        <div class="panel-body">
        	<div class="panel panel-primary">
            <div class="panel-heading">
            <div class="row">
            	<div class="col-md-4">
							</div>
							<div class="col-md-8" align="right">
								<!-- <a href="javascript:;" class="btn btn-warning join-case-btn" data-toggle="modal" data-target="#join-case-modal"><i class="fa fa-arrow-circle-right"></i> Join a Case</a> -->
								<a href="javascript:;" class="btn btn-success add-new-case-btn" ><i class="fa fa-plus"></i> Add a Case</a>
							</div>
            </div>
            </div>

            <div class="panel-body">
            	<div class="row">
                <div class="col-md-12" style="margin-top:10px">
                    <table class="table table-bordered table-hover" id="datatable">
                        <thead style="background-color: #F7F9FA;">
                            <tr>
                                <th>Case Name</th>
                                <th>Case Number</th>
                                <th>County</th>
                                <th>Trial Date</th>
                                <th width="17%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cases as $case): ?>
                              <?php if (!$case['is_draft']): ?>
                                <tr>
                                  <td><?= $case['case_title']  ?></td>
                                  <td><?= $case['case_number'] ?></td>
                                  <td><?= $case['county_name'] ?></td>
                                  <td>
                                  <?php if($case['trial'] != "0000-00-00") {echo dateformat($case['trial']);} else{echo "-";}?>
                                  </td>
                                  <td style="text-align: right">
                                  <a href="javascript:;" class="btn btn-info" title="Edit case" id="newcase" onclick="javascript: selecttab('46_tab','get-case.php?id=<?= $case['id'];?>','46');"><i class="fa fa-edit"></i> Edit</a>
                                  <a href="javascript:;" class="btn btn-purple"  title="Click to view discoveries" onclick="javascript: selecttab('45_tab','discoveries.php?pid=<?=$case['id'];?>','45');"><i class="fa fa-eye"></i> Discovery</a></td>
                                </tr>
                              <?php endif; ?>                                
                            <?php endforeach; ?>
                        </tbody>
                    </table>


<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<script src="<?= ROOTURL ?>system/assets/sections/cases.js"></script>

<a id="intro-video-replay" href="javascript:;" style="display:none;text-align:center;">No cases open yet. Click here to watch again the intro video.</a>
<script language="javascript">
  function userIntroVideoSeen(success, error) {
    $.post('<?= ROOTURL ?>system/application/post-intro-video-seen.php', {}, success )
      .fail(error);
  }
  function showIntroVideo() {
    $('#new-user-video-modal').modal('show');
    $('video#intro-video')[0].play();
  }
  $('#new-user-video-modal').on('hidden.bs.modal', _ => {
    $('video#intro-video')[0].pause();
    userIntroVideoSeen();
    //$('#new-user-video-modal').modal('hide');
  });
  $().ready( _ => {

<?php
    global $currentUser;
    $autoplayIntroVideo  = ( !empty($currentUser) && $currentUser->user['intro_seen'] != 1 );
    $linktoIntroVideo    = ( count($cases) == 0 );
    if( $autoplayIntroVideo || $linktoIntroVideo ) {
        if( $autoplayIntroVideo ) { echo "
          showIntroVideo();
        "; }
        if( $linktoIntroVideo ) { echo "
          \$('#intro-video-replay')
            .css('display','block')
            .on('click', _ => showIntroVideo() )
        "; }
    }
?>

  })
</script>

                </div>
            </div>
            </div>
            </div>
        </div>
    </div>
</div>
</div>
