<?php
require_once("adminsecurity.php");
$languagelabels		=   getlabel(10);
$sessiongroupid		=	$_SESSION['groupid'];
$ab_id				=	$_SESSION['addressbookid'];
$logincheckuser		=	$AdminDAO->getrows('system_addressbook,system_groups',"pkaddressbookid","fkgroupid	=	3 AND pkaddressbookid = '$ab_id'");
$issuperadmin		=	$logincheckuser[0]['pkaddressbookid'];
$name				=	$_SESSION['name'];
$id					=	$_GET['id'];
if(!$id)
{
	$id	=	$_SESSION['addressbookid'];
}
if($id)
{
	$users				=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid = '$id'");
	$user				=	$users[0];
	$firstname			=	$user['firstname'];
	$lastname			=	$user['lastname'];
}
/****************************************************************************/
?>
<script type="text/javascript">
$(document).ready(function(){
	$('html, body').animate({ scrollTop: 0 }, 0);
})
</script>
<div id="loaditemscript"> </div>
<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            
            <h4 class="page-headding">
			<?php 
			echo "Change Password: $firstname $lastname";
			?>	
            </h4>	
        </div>
        <div class="panel-body">
<form  name="passwordchange" id="passwordchange" class="form form-horizontal">
<input type="hidden" name="issuperadmin" id="issuperadmin" value="<?php echo $issuperadmin; ?>" />
<div class="form-group">
	<label class="col-sm-2 control-label">Old Password<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8">
    	<input type="password" placeholder="Old Password" class="form-control m-b"  name="oldpassword" id="oldpassword" value="" autocomplete='off'>
    </div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">New Password<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8">
    	<input type="password" placeholder="New Password" class="form-control m-b"  name="newpassword" id="newpassword" value="" autocomplete='off'>
    </div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Confirm New Password<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8">
    	<input type="password" placeholder="Confirm New Password" class="form-control m-b"  name="newpasswordconfirm" id="newpasswordconfirm" value="" autocomplete='off'>
    </div>
</div>
<input type="hidden" name="passhidden" id="passhidden" value="<?php echo base64_encode($password); ?>" />
<input type="hidden" name="id" value ="<?php echo $id;?>" />
<input type="hidden" name="addressbookid" value="<?php echo $addressbookid;?>" /> 
<div class="row">
            <div class="col-lg-2">
			<?php
			buttonsave('passwordchangeaction.php','passwordchange',' ','main.php?pkscreenid=99',0)
			?> 
            </div>
            <div class="col-lg-2 col-lg-offset-8">
            <span class="redstar" style="color:#F00" title="This field is compulsory">*</span> Required fields.
            </div>
            </div>        
            </form>
        </div>
    </div>
</div>
</div>
