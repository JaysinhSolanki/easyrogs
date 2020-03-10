<?php
require_once("adminsecurity.php");
$id	=	$_GET['id'];
if($id != '-1')
{
	$users	=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid	=	'$id'");
	$user		=	$users[0];
	
}

/****************************************************************************/
?>
<script src="js/jquery.form.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$('html, body').animate({ scrollTop: 0 }, 0);
});
$("#checkall").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
<div id="loaditemscript"> </div>
<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            <h4 class="page-headding">
			<?php
			if($id =='-1')
			{
				echo "Add User";
			}
			else
			{ 
				echo "Edit User: ".$user['firstname']." ".$user['lastname'];
			}
			?>	
            </h4>	
        </div>
        <div class="panel-body">
            <form  name="userform" id="userform" class="form form-horizontal">
           
            <div class="form-group">
                <label class="col-sm-2 control-label">First Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                    <input type="text"  name="firstname" id="firstname" placeholder="First Name" value="<?php echo  $user['firstname'];?>" class="form-control m-b"  >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Last Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                    <input type="text"  name="lastname" id="lastname" placeholder="Last Name" value="<?php echo  $user['lastname'];?>" class="form-control m-b"  >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Email<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                    <input type="text"  name="email" id="email" placeholder="Email" value="<?php echo  $user['email'];?>" class="form-control m-b"  >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Password<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                    <input type="password"  name="password" id="password" placeholder="Password" value="<?php echo  $user['password'];?>" class="form-control m-b"  >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Phone<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                    <input type="text"  name="phone" id="phone" placeholder="Phone" value="<?php echo  $user['phone'];?>" class="form-control m-b"  >
                </div>
            </div>
            <?php /*?><div class="form-group">
                <label class="col-sm-2 control-label">Designation<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                    <input type="text"  name="designation" id="designation" placeholder="Designation" value="<?php echo  $user['designation'];?>" class="form-control m-b"  >
                </div>
            </div><?php */?>
           <div class="form-group">
                <label class="col-sm-2 control-label">Group<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                <div class="col-sm-8">
                   <select name="fkgroupid" id="fkgroupid"   class="form-control m-b"  >
                        <option selected="selected" value="">Select Group</option>
						<?php
                        $groups	=	$AdminDAO->getrows('system_groups','*',"pkgroupid != 12 AND pkgroupid != 13"); 
                        foreach($groups as $group)
						{  
                        ?>
                        <option value="<?php echo $group['pkgroupid'];?>" <?php if($group['pkgroupid'] == $user['fkgroupid']){?> selected="selected" <?php } ?> ><?php echo $group['groupname'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                </div>
			<input type="hidden" name="id" value ="<?php echo $id;?>" />
            <div class="row">
            <div class="col-lg-2">
			<?php
			buttons('useraction.php','userform','maindiv','main.php?pkscreenid=14',0)
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

