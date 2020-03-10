<?php
require_once("adminsecurity.php");
$id	=	$_SESSION['addressbookid'];
if($id)
{
	$users				=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid = :id", array(":id"=>$id));
	$user				=	$users[0];
	$states	=	$AdminDAO->getrows('system_state','*',"fkcountryid = :fkcountryid ",array(":fkcountryid"=>254), 'statename', 'ASC');
}
$groups	=	$AdminDAO->getrows('system_groups','*',"pkgroupid IN (3,4)");
/****************************************************************************/
?>
<style>
body.modal-open {
    padding-right: 0 !important;
    position: static;
}
.swal2-popup {
  font-size: 15px !important;
}
</style>
<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
            </div>
            <div class="panel-body">
                
                <div class="panel panel-primary">
                <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12">
                        <span style="font-size:18px; font-weight:600"> <?php echo "Edit Profile: {$user['firstname']} {$user['lastname']}"; ?></span>
                    </div>
                </div>
                </div>
                <div class="panel-body">
                    <form name="profileform" action="#" method="post" id="profileform">
                	<hr>
                    <?php /*?><div class="row">
                        <div class="col-md-2">
                            <label><input name="fkgroupid" <?php if($user['fkgroupid'] == 3){echo "checked";} ?> onClick="barnoFunction()" id="fkgroupid" type="checkbox" value="1"> Attorney?</label>
                        </div>
                        <div class="col-md-3">
                            <div id="barnumber" <?php if($user['fkgroupid'] != 3){?>style="display:none;"<?php } ?>>
                                <input type="text" value="<?php echo $user['barnumber'];?>" id="barnumber" class="form-control" name="barnumber" placeholder="Bar No.">
                            </div>
                        </div>
                        <div class="col-md-7"></div>
                    </div><?php */?>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Bar No.</label>
                        </div>
                        <div class="col-md-9">
                            <div style="display:flex" >
                            <input name="fkgroupid" id="fkgroupid" type="hidden" value="1"> 
                            <textarea type="text" id="barnumber" class="form-control" name="barnumber" placeholder="Bar No."><?php echo $user['barnumber'];?></textarea>
                            <span style="color: red;">*</span>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Name</label>
                        </div>
                        <div class="col-md-3"  style="display:flex">
                            <input type="text" value="<?php echo htmlentities($user['firstname']);?>" id="firstname" class="form-control" name="firstname" required placeholder="First">
                       	 	<span style="color: red;">*</span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" value="<?php echo $user['middlename'];?>" id="middlename" class="form-control" name="middlename" placeholder="Middle">
                        </div>
                        <div class="col-md-3" style="display:flex">
                            <input type="text" value="<?php echo $user['lastname'];?>" id="lastname" class="form-control" name="lastname" required  placeholder="Last">
                        	 <span style="color: red;">*</span>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                             <label>Firm</label>
                        </div>
                        <div class="col-md-3"  style="display:flex">
                            <input type="text"  id="companyname" class="form-control" name="companyname"  value="<?php echo $user['companyname'];?>"  required placeholder="Name">
                        </div>
                        <div class="col-md-3">
                            <input type="text"  id="address" class="form-control" name="address" value="<?php echo $user['address'];?>" placeholder="Street">
                        </div>
                        <div class="col-md-3" style="display:flex">
                            <input type="text"  id="street" class="form-control" name="street"  placeholder="Suite"  value="<?php echo $user['street'];?>" />
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-3">
                             <input type="text" value="<?php echo $user['cityname'];?>"  placeholder="City" id="city" class="form-control" name="city">
                        </div>
                        <div class="col-md-3">
                            <select name="fkstateid" class="form-control" id="fkstateid" required>
                            <option value="">State</option>
                            <?php
							foreach($states as $state)
							{
							?>
							<option value="<?php echo $state['pkstateid']; ?>" <?php if($state['pkstateid']==$user['fkstateid']) {echo " SELECTED ";}?>><?php echo $state['statename']; ?></option>
							<?php
							}
							?>
                        </select> 
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="zipcode" class="form-control" name="zipcode"  placeholder="Zip Code" value="<?php echo $user['zip']?>">
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Phone</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text"  id="phone" class="form-control" name="phone" placeholder="Phone"  value="<?php echo $user['phone']?>">
                        </div>
                        <!--<div class="col-md-3">
                            <label>Email</label>
                        </div>-->
                        <div class="col-md-3" style="display:flex">
                            <input type="email" onkeyUp="checkEmail('<?=$uid ?>',this.value)" value="<?php echo $user['email'];?>" id="email" class="form-control" name="email" <?php if($uid != ""){echo "readonly";} ?> placeholder="Email">
                        	 <span style="color: red;">*</span>
                        </div>
                        <div class="col-md-3">
                            <div id="confirmBtn" class="verifyDiv" style="display:none">
                                <a href="javascript:;" class="btn btn-primary" onClick="verifyEmail()">Verify Email</a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="verifyDiv row" style="display:none">
					<div class="col-md-5">
						
					</div>
					<div class="col-md-3" style="display:flex">
						<input type="text" value="" id="verification_code" class="form-control" name="verification_code"  placeholder="Enter verification code">
						<span style="color: red;">*</span>
					</div>
					<div class="col-md-4">
						<div style="color:red; display:none" id="verification_msg">
							
						</div>
					</div>
					</div>
                     <br>
                    <?php /*?><div class="row">
                      	 <br />
                         <br />	
                         <div class="form-group col-lg-12" id="myteamDiv"  <?php if($user['fkgroupid'] != 3){?>style="display:none;"<?php } ?>>
                            <?php include_once($_SESSION['admin_path'].'myteam.php')?>
                        </div>
                    </div><?php */?>
                    
                    <div>
                       	 <?php
						 buttonsave('signupaction.php','profileform','wrapper','cases.php?pkscreenid=44',0);
						 buttoncancel(44,'cases.php');
    					 ?>
                         <small class="pull-right">
            <a class="btn btn-black" onclick="deleteAccountFunction()" href="javascript:;">
                        		Delete My Account
                        	</a>
            </small>
                    </div>
                    
                </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<?php /*?><div id="addmyteammember" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter My Team</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
        <label for="caseteam_attr_name">Name:</label>
        <input type="text" placeholder="Attorney Name" class="form-control m-b attr_names" name="attorney_name" id="attorney_name" >
        </div>
        <div class="form-group">
        <label for="caseteam_attr_email">Email:</label>
        <input type="text" placeholder="Attorney Email" class="form-control m-b attr_emails"  name="attorney_email" id="attorney_email">
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-primary" href="javascript:;" onclick="addAttorney(0,1)">Save</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <i id="msgAttr" style="color:red"></i>
      </div>
    </div>

  </div>
</div><?php */?>
<!-- Modal -->
<div class="modal fade" id="modalcaseteam" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" id="modalcaseteam_content">
      
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
function verifyEmail()
{
	var email	=	$('#email').val();
	if(email == "")
	{
		$("#verification_msg").html("Please enter your email.");
	}
	else
	{
		$.post( "<?=$_SESSION['admin_url']; ?>verifyemail.php", { email: email })
		.done(function( data ) 
		{
			if(data == "success")
			{
				$("#verification_msg").html("Verification code has been sent to you on your email.");
			}
			else
			{
				$("#verification_msg").html("Error.");
			}
		});
	}
	$("#verification_msg").show();
	setTimeout(function(){ $("#verification_msg").html("")}, 3000);
}
function deleteAccountFunction()
{	
Swal.fire({
 title: "Are you sure to permanently delete Your Account?",
  text: "Once deleted, you will not be able to recover account!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#187204',
cancelButtonColor: '#C2391B',
confirmButtonText: "Yes, delete it!"
}).then((result) => {
if (result.value) {
 window.location.replace("deletemyaccount.php");
}
});
	
}

function barnoFunction(id)
{
	 if($("#fkgroupid").prop("checked") == true)
	 {
		 $("#barnumber").show();
		 $("#myteamDiv").show();
	 }
	 else
	 {
		 $("#barnumber").hide();
		 $("#myteamDiv").hide();
	 }
	/*if(id == 3)
	{
		$("#barnumber").show();
	}
	else
	{
		$("#barnumber").hide();
	}*/
}
function checkEmail(uid,email)
{
	if(email == '<?=$user['email']?>')
	{
		$(".verifyDiv").hide();
	}
	else
	{
		$(".verifyDiv").show();
	}
}
</script>