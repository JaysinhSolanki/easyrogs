<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");
	$caseId				=	$_REQUEST['case_id'];
	$userId				=	$_REQUEST['user_id'];
	$slAttorneyId	=	$_REQUEST['sl_attorney_id'];
	$userId				=	$_REQUEST['user_id'];

	if($slAttorneyId) {
		$slAttorney	=	$usersModel->getSlAttorney($slAttorneyId);
	}
	if($slAttorneyId) {
		$slAttorney	=	$usersModel->getSlAttorney($slAttorneyId);
	}

?>
<div class="modal-header" style="padding: 15px;">
<h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter Service List</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
  <span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body"> 
<div class="form-group">  
<input type="hidden"  id="sl_attorney_id" value="<?= $slAttorneyId ?>"/>
<input type="hidden"  id="user_id" value="<?= $userId ?>"/>
<label for="caseteam_attr_name">Name</label>
<input type="text" placeholder="Attorney Name" class="form-control m-b attr_names" name="attorney_name" id="attorney_name" value="<?= $slAttorney['attorney_name'] ?>">
</div>
<div class="form-group">
<label for="caseteam_attr_email">Email</label>
<input type="text" placeholder="Attorney Email" class="form-control m-b attr_emails"  name="attorney_email" id="attorney_email" value="<?= $slAttorney['attorney_email'] ?>">
</div>
<div id="clientsList">

</div>
</div>
<div class="modal-footer">
<i id="msgAttr" style="color:red"></i>
<a class="btn btn-success" href="javascript:;" onclick="addAttorney('<?= $caseId; ?>',2)"><i class="fa fa-save"></i> Save</a>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
</div>

<script>
$( document ).ready(function() 
{
	attDropdownFunction('<?= $caseId ?>','<?= $userId ?>');
});
function attDropdownFunction(case_id,attr_id="")
{  
	$.post( "loadattorneydropdown.php",{case_id:case_id,attr_id:attr_id}).done(function( data ) 
	{
		$("#clientsList").html(data);
	});
}
</script>