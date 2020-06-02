<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

	$caseId	  =	$_GET['id'];
	$newCase  = !$caseId;
	$case 	  = $newCase ? $casesModel->getDraft() : $casesModel->find($caseId);
	$caseId		= $case['id'];
	$isDraft  = $case['is_draft'];
	$uid		  = $case['uid'];
	$states 	= $statesModel->getByCountry(Country::UNITED_STATES);
	$counties = $countiesModel->getAll();
	$parties  = $casesModel->getClients($caseId);

	if($isDraft) { $casesModel->cleanupSides($caseId); }

	$side = $newCase ? null : $sides->getByUserAndCase($currentUser->id, $caseId);
	if(!$newCase && !$side) {
	  HttpResponse::unauthorized();
	}
	
	$canDeleteCase = $side && $casesModel->usersCount($caseId) === 1; // current user is the only one left... allow delete case.
?>

<style type="text/css">
	body.modal-open 
	{
			position: static !important;
	}
	.modal-header .close {
			/*margin-top: -45px !important;*/
	}
	.modal-title {
			font-size: 24px !important;
	}
	.close {
			font-size: 25px !important;
	}
	.modal-header
	{
		padding:10px !important
	}
	.swal2-popup {
		font-size: 15px !important;
	}
</style>

<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
	<div class="hpanel">
		<div class="panel-heading text-center">
			<h3><strong><?= $newCase ? 'Add Case' : $side['case_title'] ?></strong></h3>
		</div>
		<div class="panel-body">
			<form  name="clientform" id="clientform" class="form form-horizontal">
				<div class="form-group row">
					<div class="col-md-3"></div>
						<div class="col-md-3" align="left"> 
							<button type="button" class="btn btn-success buttonid save-case-btn" data-style="zoom-in">
							<i class="fa fa-save"></i>
							<span class="ladda-label">Save</span><span class="ladda-spinner"></span></button>
							<?php buttoncancel(44,'get-cases.php'); ?>
						</div>
						<div class="col-md-5" align="right"> 
							<?php if (!$isDraft): ?>
								<?php if($canDeleteCase): ?>
									<a href="javascript:;" class="btn btn-danger" title="Delete case" id="newcase" onclick="javascript: deleteLeaveCases('<?= $caseId; ?>',1);"><i class="fa fa-trash"></i> Delete </a>
								<?php else: ?>
									<a href="javascript:;" class="btn btn-black" title="Leave case" id="newcase" onclick="javascript: deleteLeaveCases('<?= $caseId; ?>',2);"><i class="fa fa-sign-out"></i> Leave Case</a>
								<?php endif; ?>	
							<?php endif; ?>	
						</div>
            </div>
            <input type="hidden" name="jurisdiction" class="form-control" id="jurisdiction" value='CA'>
            
            <div class="row">
          		<div class="col-md-1"></div>
              <div class="col-md-2">
                <label>Number<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
              </div>
              <div class="col-md-3">
                <input type="text" placeholder="Number" class="form-control m-b"  name="case_number" id="case_number" value="<?php echo htmlentities($side['case_number']); ?>" />
              </div>
              <div class="col-md-6"></div>
						</div>
            
            <div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                  <label>Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                  <input type="text" placeholder="Case Name" class="form-control m-b"  name="case_title" id="case_title" value="<?php echo htmlentities($side['case_title']); ?>" />
                </div>
                <div class="col-md-2">
                  <label>County</label>
                </div>
                <div class="col-md-3">
									<?php $currentCounty = $side['county_name'] ? $side['county_name'] : $currentUser->getCounty(); ?>
                  <select name="county_name" class="form-control  m-b" id="county_name" >
										<?php foreach($counties as $county): ?>
											<option value="<?= $county['countyname'];?>" <?= $county['countyname'] == $currentCounty ? "selected" : "" ?>><?php echo $county['countyname'];?></option>
										<?php endforeach; ?>
									</select>
                </div>
                <div class="col-md-1"></div>
            </div>
            
            <div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                   <label class="control-label">Plaintiff<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <textarea placeholder="Plaintiff(s)" class="form-control m-b"  name="plaintiff" id="plaintiff"><?php echo htmlentities($side['plaintiff']); ?></textarea>
                </div>
                <div class="col-md-2">
                   <label class="control-label">Defendant<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <textarea placeholder="Defendant(s)" class="form-control m-b"  name="defendant" id="defendant" ><?php echo htmlentities($side['defendant']); ?></textarea>
                </div>
                <div class="col-md-1"></div>
            </div>
						
						<div class="row" style="margin-bottom:10px">
							<div class="col-md-1"></div>
							<div class="col-md-2">
								<label>Trial<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
							</div>
							<div class="col-md-3">
								<input type="text"  onchange="calculated_discovery_cutoff_date(this.value)" name="trial" id="trial" placeholder="Trial Date" class="form-control m-b datepicker" value="<?php echo $side['trial'] == '0000-00-00'?'':dateformat($side['trial']);?>" data-date-start-date="0d" data-date-end-date="+5y">
							</div>
							<div class="col-md-2">
								<label>Discovery Cutoff<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
							</div>
							<div class="col-md-3">
									<input type="text"  name="discovery_cutoff" id="discovery_cutoff" placeholder="Discovery Cutoff" class="form-control datepicker" value="<?php echo $side['discovery_cutoff']=='0000-00-00'?'':dateformat($side['discovery_cutoff']);?>" data-date-start-date="0d" data-date-end-date="+5y">
								<i class="fa fa-university" aria-hidden="true"></i> Code Civ.Proc., &sect;&sect; 2016.060 <?php  echo instruction(12) ?>, 2024.020 <?php  echo instruction(13) ?>.
							</div>
							<div class="col-md-1"></div>
						</div>
					
						
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-2">
                <label>Lead Counsel<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
              </div>
              <div class="col-md-3">
							  <select class="er-team-attorney-select form-control" name="case_attorney" id="case_attorney" data-value="<?= $currentPrimaryAttorneyId ?>"></select>
							</div>
							<div class="col-md-2">
								<label>Letterhead<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
							</div>
							<div class="col-md-3">
								<textarea name="masterhead" class="form-control" wrap="off" cols="50" rows="10" style="height: 12em; overflow: hidden"><?= $side['masterhead'] /* ?: $currentUser->user['masterhead']*/ ?></textarea>
							</div>
							<div class="col-md-1"></div>
						</div>
						
						<hr />
						 
            <div class="row">
              <div class="col-md-11" style="text-align:right">
                <a href="javascript:;"  class="pull-right btn btn-success btn-small" onclick="addparty('', <?= $caseId ?>)" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
            	</div>
            	<div class="col-md-1"></div>
            </div>

						<div class="row">  
							<div class="col-md-1"></div>
							<div class="col-md-2">
								<label>Parties</label>
							</div>
							<div class="col-md-8" id="loadclients"></div>							
							<div class="col-md-1"></div>
            </div>
						
						<br />
						
						<div class="row">
            	<div class="col-md-11" style="text-align:right">
                <a href="javascript:;"  class="pull-right btn btn-success btn-small" id="add-user-btn" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
            	</div>
            	<div class="col-md-1"></div>
            </div>

            <div class="row">  
							<div class="col-md-1"></div>
							<div class="col-md-2">
								<label>Team</label>
							</div>
							<div class="col-md-8" id="loadusers"></div>
							<div class="col-md-1"></div>
            </div>
						
						<br />
						
						<div id="service-list"></div>
						
            <br />
						
						<div class="container"><div id="sides-container"></div>
            
            <input type="hidden" name="id" value ="<?php echo $caseId;?>" />
            <input type="hidden" name="uid" value ="<?php echo $uid;?>" />
            
            <div class="form-group">
            	<div class="col-sm-offset-3 col-sm-3" align="left">
								<button type="button" class="btn btn-success buttonid save-case-btn" data-style="zoom-in" >
									<i class="fa fa-save"></i>
									<span class="ladda-label">Save</span><span class="ladda-spinner"></span>
								</button>
								<?php buttoncancel(44,'get-cases.php'); ?> 
							</div>
              <div class="col-md-5"  align="right">
								<?php if (!$isDraft): ?>
									<?php if($canDeleteCase): ?>
										<a href="javascript:;" class="btn btn-danger" title="Delete case" id="newcase" onclick="javascript: deleteLeaveCases('<?= $caseId; ?>',1);"><i class="fa fa-trash"></i> Delete </a>
									<?php else: ?>
										<a href="javascript:;" class="btn btn-black" title="Leave case" id="newcase" onclick="javascript: deleteLeaveCases('<?= $caseId; ?>',2);"><i class="fa fa-sign-out"></i> Leave Case</a>
									<?php endif; ?>	
								<?php endif; ?>	
							</div>
            </div>         
					</form>
			  </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalcaseteam" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" id="modalcaseteam_content"></div>
  </div>
</div>

<div id="serviceListModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" id="serviceListModalContent"></div>
  </div>
</div>

<div id="existing-case-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" id="existing-case-modal-content">
    <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="existing-case-modal-header" style="font-size: 22px;">Case Already Exists</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4 class="text-center join-case-text"></h4>
        <br/>
        <div class="form-group join-case-clients">
          <label>Request to join representing</label>
          <select name="client" id="join-existing-case-client" class="form-control">
            <option>Select a Party</option>
					</select>
					<input type="hidden" value="" id="join-existing-case-id" />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="join-case-btn">Join</button>
        <button class="btn btn-warning" id="join-create-case-btn" data-dismiss="modal" ><i class="fa fa-save"></i> Create New Case</a>
				<button class="btn btn-danger"  id="join-close-btn" type="button"  data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
      </div>
    </div>
  </div>
</div>


<div id="partyModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter Party</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
        	<input type="hidden" id="party_client_id" name="party_client_id" value="" />
            <label for="caseteam_attr_name">Name</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
            <input type="text" placeholder="Party Name" class="form-control m-b"  name="client_name" id="client_name">
        </div>
        <div class="form-group">
            <label for="caseteam_attr_email">Role</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
            <select name="clientroles" id="clientroles" class="form-control">
                <option value="">Party Role</option>
                <option value="Plaintiff">Plaintiff</option>
                <option value="Defendant">Defendant</option>
                <option value="Plaintiff and Cross-defendant">Plaintiff and Cross-defendant</option>
                <option value="Defendant and Cross-plaintiff">Defendant and Cross-plaintiff</option>
            </select>
        </div>
        <div class="form-group">
            <label for="caseteam_attr_name">Representation</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
            <select name="clienttypes" id="clienttypes" class="form-control" onchange="addAttryFunction(this.value)">
							<option value="">Who represents this Party?</option>
							<option value="Us">Us</option>
							<option value="Others">Another Attorney</option>
							<option value="Pro per">Pro per</option>
            </select>
        </div>
        <div class="form-group" id="div_attr" style="display:none;">
					<label for="caseteam_attr_name">Attorney:</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
					<select class="er-team-attorney-select form-control" name="primary_attorney_id" id="primary_attorney_id" value="<?= $currentPrimaryAttorneyId ?>"></select>
        </div>
				<div class="form-group" style="display: none" id="div_attr_email">
					<label for="caseteam_attr_name">Email</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
					<input type="text" placeholder="Party Email" class="form-control m-b"  name="client_email" id="client_email" >
				</div>
      </div>

      <div class="modal-footer">
        <a class="btn btn-success" href="javascript:;" onclick="addCaseClient(<?= $caseId; ?>)"><i class="fa fa-save"></i> Save</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
        
        <i id="msgClient" style="color:red"></i>
      </div>
    </div>

  </div>
</div>

<!-- user modal -->
<div id="userModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
		<!-- Modal content-->
    <div class="modal-content">
			<form id="submit-user-form">
				<div class="modal-header" style="padding: 15px;">
					<h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter User</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="user_name">Name</label>
						<input type="text" placeholder="Name" class="form-control m-b"  name="name" id="user_name" required />
					</div>
					<div class="form-group">
							<label for="user_email">Email</label>
							<input type="text" placeholder="Email" class="form-control m-b" name="email" id="user_email" required />
					</div>
					<input type="hidden" id="user_id" name="id" value="" />
					<input type="hidden" id="case_id" name="case_id" value="<?= $caseId ?>" />
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
					<i id="msgClient" style="color:red"></i>
				</div>
			</form>
    </div>
  </div>
</div>


<div id="viewreminders" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Scheduled Reminders</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <label for="caseteam_attr_email">EasyRogs sends the following reminders:</label>
        <ol>
        	<li><b>To the Attorney:</b> a week before the Response is due.</li>
            <li><b>To the Responding Party:</b> 5 days before their answers are due back to the Attorney. And 5 days after the Attorney sent it, if the Party hasn't at least looked at it.</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button> 
      </div>
    </div>

  </div>
</div>
<div class="modal fade" id="general_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="general_modal_title">Calculate Date</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_general_modal_content">
       <div class="text-center"> Loading...</div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:450px !important">
    <div class="modal-content">
  		<div class="modal-body" id="deletemodalcotent">
        
      </div>
    </div>
  </div>
</div>
<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<script src="<?= ROOTURL ?>system/application/custom.js"></script>

<script type="text/javascript">
// TODO: move all this to case.js
// this whole JS code needs to get rid of PHP dependency...
const setMastHead = (attorneyId = null) => {
  const id = attorneyId ? attorneyId : $('#case_attorney').val();
  getAttorney(id, 
    (attorney) => $('textarea[name=masterhead]').html(attorney.masterhead),
    (e) => console.error(e)
  );
}
$(document).on( 'change', '#case_attorney', () => { setMastHead() });


function loadServiceListModal(caseId, userId = null, slAttorneyId = null)
{
	$.post( "loadservicelistmodal.php",{case_id: caseId, user_id: userId, sl_attorney_id: slAttorneyId}).done(function(resp)
	{
		$("#serviceListModalContent").html(resp);
		$("#serviceListModal").modal("show");
		$("#msgAttr").html("");
	});
}

function addparty(id='', caseId)
{
	$("#party_client_id").val(0);
	$('#partyModal').modal('show');
	$("#party_client_id").val("");
	$("#client_name").val("");
	$("#clientroles").val("");
	$("#clienttypes").val("");
	$("#client_email").val("");
	$('#div_attr_email').hide();
	if(id>0)
	{
		$.post( "clientsload.php",{id: id, case_id: caseId}).done(function(resp)
		{
			var obj = JSON.parse(resp);
			$("#party_client_id").val(obj.id);
			$("#client_name").val(obj.client_name);
			$("#clientroles").val(obj.client_role);
			$("#clienttypes").val(obj.client_type);
			$("#client_email").val(obj.client_email);
			if(obj.client_type == 'Us' || obj.client_type == 'Pro per')
			{
				$('#div_attr_email').show();
			}
		});
	}
}
function showCaseUser(ev) { debugger;
	$("#user_name").val("");
	$("#user_email").val("");
	$("#user_id").val("");
	$('#userModal').modal('show');

	if( ev ) {
		const data = $(ev.target).parents('td').data();
		if( data && Object.keys(data).length ) {
			$("#user_id").val(data.userId);
			data.caseId 	&& $("#case_id").val(data.caseId); 
			data.userName 	&& $("#user_name").val(data.userName);
			data.userEmail 	&& $("#user_email").val(data.userEmail);
		}
	}
}
function submitCaseUser(ev) {
	ev.preventDefault();
	const formData = new FormData(ev.target);
	const params = Array.from(formData.keys()).reduce(
		(acc, key) => {acc[key] = formData.get(key); return acc; }, {}
	);
	$.post('post-case-user.php', params, response => {
		$('#userModal').modal('hide');
		loadCasePeople(params.case_id);
	}).fail( response => {
		$('#userModal').modal('hide');
		showResponseMessage( JSON.parse(response.responseText) );
	});
}
$( document ).ready(function() 
{
	$('#submit-user-form').on('submit', (e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		const params = Array.from(formData.keys()).reduce(
			(acc, key) => {acc[key] = formData.get(key); return acc; }, {}
		);
		$.post('post-case-user.php', params, (response) => {
			$('#userModal').modal('hide');
			loadCasePeople(params.case_id);
		}).fail((response) => {
			$('#userModal').modal('hide');
			showResponseMessage(JSON.parse(response.responseText));
		});
	});
	$('#add-user-btn').on('click', () => $('#userModal').modal('show') );
	$(document).on('click', '.delete-user-btn', async (e) => {
		const params = $(e.target).parent().data();
		confirm = await confirmAction(); 
		if ( confirm ) {
			$.post('delete-case-user.php', params, (response) => {
				loadCasePeople(params.case_id);
				showResponseMessage(response);				
			}).fail((response) => showResponseMessage(response))
		}
	});

	showreminders(<?php echo $case['allow_reminders']; ?>);
	$('.datepicker').datepicker({format: 'm-d-yyyy',autoclose:true});

	loadCasePeople(<?php echo $caseId; ?>);
});

function deleteLeaveCases(case_id,delete_or_leave)
{
	swal( {
		title: (delete_or_leave == 1) ? "Are you sure to delete this case?" : "Are you sure to want to leave this case?",
		text: "You will not be able to undo this action!",
		icon: 'warning',
		dangerMode: true,
		buttons: [true, delete_or_leave == 1 ? "Yes, delete it!" : 'Yes, leave!'],
		// confirmButtonColor: '#187204',
		// cancelButtonColor: '#C2391B',
	})
	.then( result => { 
		if( result ) {
			$.post( "deleteleavecase.php", { case_id: case_id, delete_or_leave: delete_or_leave })
				.done( data => { selecttab('44_tab','get-cases.php','44'); });
		}
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}

function loadCasePeople(case_id) {
	$("#loadclients").load(`get-case-clients.php?format=html&case_id=${case_id}`)
	$("#loadusers").load(`get-case-users.php?format=html&case_id=${case_id}`, _ => {
			$('.tooltipshow').tooltip( { container: 'body' });
		} );
	$("#service-list").load(`get-service-list.php?format=html&case_id=${case_id}`);
	loadSides(case_id);
}

function loadSides(caseId)
{
	<?php if (in_array($_ENV['APP_ENV'], ['dev', 'local', 'development'])): ?>
		$("#sides-container").load(`get-sides.php?case_id=${caseId}&format=html`);
	<?php endif; ?>
}

function addCaseClient(case_id)
{
	var other_attorney_id	= 	{};
	
	var id					=	$("#party_client_id").val();
	var client_name			=	$("#client_name").val();
	var clientroles			=	$("#clientroles").val();
	var clienttypes			=	$("#clienttypes").val();
	var client_email		=	$("#client_email").val();
	var other_attorney_id		=	$("select[name=other_attorney_id]").val();
	var other_attorney_name		=	$("input[name=other_attorney_name]").val();
	var other_attorney_email		=	$("input[name=other_attorney_email]").val();
	var primary_attorney_id		=	$("select[name=primary_attorney_id]").val();
	
	$("#msgClient").html("");
	$.post( "post-case-client.php", {
		id:id, 
		case_id:case_id,
		client_name: client_name, 
		clientroles: clientroles,
		clienttypes:clienttypes,
		client_email:client_email,
		other_attorney_id:other_attorney_id,
		other_attorney_name:  other_attorney_name,
		other_attorney_email: other_attorney_email,
		primary_attorney_id: primary_attorney_id
	}, 'json').done(function( data ) 
	{
		obj = data;
		if(obj.type == "success")
		{
			$('.modal').modal('hide');
			$("#client_name").val('');
			$("#clientroles").val('');
			$("#clienttypes").val('');
			$('#other_attorney_id').val(null).trigger('change');
			$("#client_email").val('');
			loadCasePeople(obj.case_id);
		}
		showResponseMessage(obj)
	}).fail( (e) => {
		const obj = JSON.parse(e.responseText);
		showResponseMessage(obj);
		if (obj.field == 'primary_attorney_id') {
				$("#div_attr").show();
			}
	});
}

function deleteCaseClient(id, case_id) {
	swal({
		title: "Are you sure you want to delete this Party?",
		text: "You will not be able to undo this action!",
		icon: 'warning',
		dangerMode: true,
		buttons: [ true, "Yes, delete it!" ],
		// confirmButtonColor: '#187204',
		// cancelButtonColor: '#C2391B',
	})
	.then( result => { 
		if( result ) {
			$("#client_"+id).remove();
			$.post( "delete-case-client.php", { id: id, case_id: case_id}, () => { loadCasePeople(case_id)} );
		}
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}

function addAttryFunction(type)
{
	$(".disableclass").val("");
	if (type == 'Others' || type == 'Us') {
		$("#bring_team_checkbox").show();
	}
	else {
		$("#bring_team_checkbox").hide();
	}
	if(type == 'Others')
	{
		$("#div_attr_email").hide();
	}
	else if(type == '')
	{
		$("#div_attr").hide();
		$("#div_attr_email").hide();
	}
	else
	{
		$("#div_attr").hide();
		$("#div_attr_email").show();
	}
}

function calculated_discovery_cutoff_date(trail_date)
{
	$.post( "calculatecutoffdateaction.php",{ trail_date: trail_date}).done(function( data ) 
	{
		$("#discovery_cutoff").val(data);
	});
}
function showreminders()
{
	 if($("#allow_reminders").prop("checked") == true)
	 {
		 $(".showreminders").show();
	 }
	 else
	 {
		 $(".showreminders").hide();
	 }
}
function loadmasterhead()
{
	var case_attorney_email	=	$( "#case_attorney option:selected" ).text();
	var case_attorney		=	$( "#case_attorney option:selected" ).val();
	$.post( "loadmasterhead.php",{ case_attorney_email: case_attorney_email,case_attorney:case_attorney}).done(function( data ) 
	{
		$("#masterheadDiv").html(data);
	});
}

  erInviteControl();
  <?php if( $currentPrimaryAttorneyId && !$isDraft ): ?>
    erTeamAttorneySelectControl(<?= $caseId ?>);
  <?php else: ?>
    erTeamAttorneySelectControl(<?= $caseId ?>, setMastHead);
	<?php endif; ?>  	
	
	isDraft = <?= $isDraft ? 'true' : 'false' ?>;
</script>
<script src="<?= ROOTURL ?>system/assets/sections/case.js"></script>
