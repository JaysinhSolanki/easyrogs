<?php
  require_once("adminsecurity.php");

	$case_id			=	$_POST['case_id'];

/**
* Edit Case
**/
$attorney_id		=	$_POST['attorney_id'];
$makememberofmyteam	=	$_POST['makememberofmyteam'];
if($attorney_id > 0) 
{
	$attorneyDetails	=	$AdminDAO->getrows("attorney","*", "id = :attorney_id", array("attorney_id"=>$attorney_id));
	$attorneyDetail		=	$attorneyDetails[0];
}
?>
<input type="hidden" name="attorney_id" id="attorney_id" value="<?php  echo $attorney_id?>" />
<div class="modal-header" style="padding: 15px;">
<h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter Team Member's Details</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
  <span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<div class="form-group">
<label for="caseteam_attr_name">Name:</label>
<input type="text" name="caseteam_attr_name" class="form-control" id="caseteam_attr_name" value="<?php echo $attorneyDetail['attorney_name']; ?>">
</div>
<div class="form-group">
<label for="caseteam_attr_email">Email:</label>
<input type="text" name="caseteam_attr_email" class="form-control" id="caseteam_attr_email" value="<?php echo $attorneyDetail['attorney_email']; ?>">
</div>
<?php 
/**
* When we add team membet from profile
**/
if($case_id > 0 && $_SESSION['groupid'] == 3)
{
?>
<div class="">
<label><input type="checkbox" name="makememberofmyteam" id="makememberofmyteam" <?php if($makememberofmyteam == 1){echo "checked";} ?>> Make member of my team?</label>
</div>
<?php
}
?>
</div>
<div class="modal-footer">
<button type="button" onclick="addNewCaseTeamAttorney('<?php echo $case_id; ?>')" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>

<i id="msgAttrCaseTeam" style="color:red"></i>
</div>