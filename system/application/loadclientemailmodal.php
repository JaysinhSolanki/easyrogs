<?php
require_once("adminsecurity.php");
$discovery_id		=	$_REQUEST['discovery_id'];
$actiontype			=	$_REQUEST['actiontype'];
if($discovery_id > 0)
{
	$discoveryDetails	=	$AdminDAO->getrows('discoveries','*',"id 	= :id",array(":id"=>$discovery_id));
	$discovery_data		=	$discoveryDetails[0];
	$responding			=	$discovery_data['responding'];
}
else
{
	$responding			=	$_REQUEST['client_id'];
}
$case_id			=	$discovery_data['case_id'];
$respondingdetails	=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
$responding_name	=	$respondingdetails[0]['client_name'];
$responding_email	=	$respondingdetails[0]['client_email'];
$responding_type	=	$respondingdetails[0]['client_type'];
$responding_role	=	$respondingdetails[0]['client_role'];
?>

<form  name="addClientEmailModal" id="addClientEmailModal" class="form form-horizontal" method="post">
<input type="hidden" name="discovery_id" value="<?php echo $discovery_id ?>" id="discovery_id"  /> 
<input type="hidden" name="actiontype" value="<?php echo $actiontype ?>" id="actiontype"  />
<input type="hidden" name="client_id" value="<?php echo $responding ?>" id="client_id"  />
<input type="hidden" name="case_id" value="<?php echo $case_id ?>" id="case_id"  />
<div class="form-group">
    <input type="text"  name="client_email" id="client_email" placeholder="Enter Email" class="form-control m-b" value="">
</div>
<div class="row"> 
	
	<button type="button" onclick="saveclientemail()" class="btn btn-success"><i class="fa fa-share"></i> Save</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button> 
    <i id="msgAddEmailClientModal" style="color:red"></i> 
</div>
</form>