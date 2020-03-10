<?php
session_start();
require_once("adminsecurity.php");
$case_id	=	$_REQUEST['case_id'];
$attr_id	=	$_REQUEST['attr_id'];
if($attr_id > 0)
{
	$attrDetails	=	$AdminDAO->getrows("attorney","*", "id = :id ", array(":id"=>$attr_id));
	$attrDetail		=	$attrDetails[0];
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
<input type="hidden"  id="editattorney_id" value="<?php echo $attr_id ?>"/>
<label for="caseteam_attr_name">Name</label>
<input type="text" placeholder="Attorney Name" class="form-control m-b attr_names" name="attorney_name" id="attorney_name" value="<?php echo $attrDetail['attorney_name']; ?>">
</div>
<div class="form-group">
<label for="caseteam_attr_email">Email</label>
<input type="text" placeholder="Attorney Email" class="form-control m-b attr_emails"  name="attorney_email" id="attorney_email" value="<?php echo $attrDetail['attorney_email']; ?>">
</div>
<div id="clientsList">

</div>
</div>
<div class="modal-footer">
<i id="msgAttr" style="color:red"></i>
<a class="btn btn-success" href="javascript:;" onclick="addAttorney('<?php echo $case_id; ?>',2)"><i class="fa fa-save"></i> Save</a>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
</div>

<script>
$( document ).ready(function() 
{
	attDropdownFunction('<?php echo $case_id ?>','<?php echo $attr_id ?>');
});
function attDropdownFunction(case_id,attr_id="")
{  
	$.post( "loadattorneydropdown.php",{case_id:case_id,attr_id:attr_id}).done(function( data ) 
	{
		$("#clientsList").html(data);
	});
}
</script>