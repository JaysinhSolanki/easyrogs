<?php
require_once("adminsecurity.php");
$id					=	$_REQUEST['id'];
$attorney_type		=	$_REQUEST['attorney_type'];
$case_team_id		=	$_REQUEST['case_team_id'];
$case_id			=	$_REQUEST['case_id'];
$is_userteammember	=	$_REQUEST['is_userteammember'];

?>
<?php /*?>
<div class="swal-icon swal-icon--warning">
<span class="swal-icon--warning__body">
  <span class="swal-icon--warning__dot"></span>
</span>
</div>
<div class="swal-title">Are you sure to delete?</div>
<form id="deleteform" name="deleteform">
<input type="hidden" id="id" name="id" value="<?php echo $id ?>"  />
<input type="hidden" id="attorney_type" name="attorney_type" value="<?php echo $attorney_type ?>"  />
<input type="hidden" id="case_team_id" name="case_team_id" value="<?php echo $case_team_id ?>"  />
<input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>"  />
<?php
if($is_userteammember == 1)
{
?>
<div class="form-check form-check-inline text-center">
  <input class="form-check-input" name="deletefromattr_team" type="checkbox" id="deletefromattr_team" value="1">
  <label class="form-check-label swal-text" for="inlineCheckbox1" style="margin-bottom:0px !important">Delete from my Team, too?</label>
</div>
<?php
}
?>
<div class="swal-footer text-center"> 
<div class="swal-button-container">
<button class="swal-button--confirm btn-success" type="button" onclick="modaldeleteaction()" >Yes, delete it!</button>
</div>
<div class="swal-button-container">

<button class="swal-button--cancel btn-danger" data-dismiss="modal">Cancel</button>

</div>
</div>
  
</form>
<?php */?>

  

<div aria-labelledby="swal2-title" aria-describedby="swal2-content" class="swal2-popup swal2-modal swal2-icon-warning swal2-show" tabindex="-1" role="dialog" aria-live="assertive" aria-modal="true" style="display: flex; padding:0px !important">
    <div class="swal2-header">
        <div class="swal2-icon swal2-warning swal2-icon-show" style="display: flex;">
            <div class="swal2-icon-content">!</div>
            </div>
        <h2 class="swal2-title" id="swal2-title" style="display: flex;">Are you sure you want to delete this Case Team Member?</h2>
    </div>
    <div class="swal2-content">
    	<div id="swal2-content" class="swal2-html-container" style="display: block;">You will not be able to undo this action!</div>
    </div>
    <form id="deleteform" name="deleteform">
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>"  />
        <input type="hidden" id="attorney_type" name="attorney_type" value="<?php echo $attorney_type ?>"  />
        <input type="hidden" id="case_team_id" name="case_team_id" value="<?php echo $case_team_id ?>"  />
        <input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>"  />
        <?php
        if($is_userteammember == 1  && $_SESSION['groupid'] == 3)
        {
        ?>
        <div class="form-check form-check-inline text-center">
          <input class="form-check-input" name="deletefromattr_team" type="checkbox" id="deletefromattr_team" value="1">
          <label class="form-check-label swal-text" for="deletefromattr_team" style="margin-bottom:0px !important">Delete from my Team, too?</label>
        </div>
        <?php
        }
        ?>
    </form>
    <div class="swal2-actions">
    	<button type="button" class="swal2-confirm swal2-styled" style="display: inline-block; background-color: rgb(98, 203, 49); border-left-color: rgb(98, 203, 49); border-right-color: rgb(98, 203, 49);" aria-label="" onclick="modaldeleteaction()" >Yes, delete it!</button>
    	<button type="button" data-dismiss="modal" class="swal2-cancel swal2-styled" style="display: inline-block; background-color: rgb(221, 51, 51);" aria-label="">Cancel</button>
    </div>
</div>