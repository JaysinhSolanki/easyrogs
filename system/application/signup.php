l<?php
@session_start();
//Destroy session if user already logged in
setcookie('rememberme','', time()+(86400*30), "/");
setcookie("rememberme",'', time()-3600); 
@session_destroy();

require_once("../bootstrap.php");
require_once(SYSTEMPATH."library/classes/AdminDAO.php");
$AdminDAO	=	new AdminDAO();	
include_once(SYSTEMPATH."library/classes/login.class.php");
include_once(SYSTEMPATH."library/classes/error.php");
require_once(FRAMEWORK_PATH."head.php");
include_once(SYSTEMPATH."library/classes/functions.php");

$states	=	$AdminDAO->getrows('system_state','*',"fkcountryid = :fkcountryid ",array(":fkcountryid"=>254), 'statename', 'ASC');
$groups	=	$AdminDAO->getrows('system_groups','*',"pkgroupid IN (3,4)");
$uid	=	$_GET['uid'];
if($uid != "")
{
	$userDetails	=	$AdminDAO->getrows('attorney a,invitations i','*',"i.uid = :uid AND a.id = i.attorney_id",array(":uid"=>$uid));
	$error			=	0;
	if(!empty($userDetails))
	{
		$userDetail			=	$userDetails[0];
		$status				=	$userDetail['status'];
		$attorney_email		=	$userDetail['attorney_email'];
		if($status != 1 )
		{
			$message	=	"You have already signed up with this email {$attorney_email}.";
			$error		=	1;
		}
	}
	else
	{
		$message	=	"URL is invalid or expired.";
		$error		=	1;
	}
	if($error == 1 )
	{
		?>
        <div class="container">
        <div class="jumbotron text-xs-center" style="margin-top:200px">
        <h1 class="display-3 text-center">Sorry!</h1>
        <p class="lead  text-center"><?php echo $message ?></p>
        <p class="text-center">Email <a href="mailto:support@EasyRogs.com">support@EasyRogs.com</a> if you need assistance.</p>
        </div>
        </div>
        <?php
		exit;
	}
	//$newsignup = '';
}
else
{
	//$newsignup = 1;
}
$newsignup = 1;
?>
<body class="blank">
<style>
.register-container
{
	max-width:100% !important;	
}
.required:after { content:"*"; }
</style>
<!-- Simple splash screen-->
<?php
	//require_once("splashscreen.php");
?>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<style>.panel-body p{ line-height:25px; } .panel-body h4{
    margin-top: 15px;
    margin-bottom: 15px;
} </style>
<div class="color-line"></div>
<div class="register-container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="text-center m-b-md">
           		<h2><?php echo $systemmaintitle; ?></h2>
                <h3></h3>
                <small>Create your account</small>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form name="profileform" action="#" method="post" id="profileform" enctype="multipart/form-data">
                        <input type="hidden" name="uid" value="<?php echo $uid ?>">
                        <input type="hidden" name="newsignup" value="<?php echo $newsignup ?>">
                        <?php 
						if($uid != "")
						{
						/*?>
                        <input type="hidden" name="fkgroupid" value="3">
                        <?php*/
						}
						 ?>
                        <div class="row">
                        	<div class="col-md-6">
                            	<h3>Registration</h3>
                            </div>
							<?php 
                            if($uid == "")
                            {
                            ?>
                            <div class="col-md-6 text-right">
                            	<div class="back-link">
                                	<a href="<?php echo $domain;?>userlogin.php" class="btn btn-primary">Back to Login</a>
                                </div>
                            </div>
                            <?php
							}
							?>
                        </div>
						<hr>
                        <div class="row">
                        	<div class="col-md-2">
                            	<label><input name="fkgroupid" onClick="barnoFunction()" id="fkgroupid" type="checkbox" value="1"> Attorney?</label>
                            </div>
                            <div class="col-md-3">
                                <div id="barnumber" style="display:none;">
                                	<div style="display:flex" >
                                	<input type="c" id="barnumber" class="form-control" name="barnumber" placeholder="Bar No." maxlength="15" />
                                    <span style="color: red;">*</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7"></div>
                        </div>
                        <?php /*?><div class="row">
                        	<div class="col-md-2">
                            	<label>Bar No.</label>
                            </div>
                            <div class="col-md-9">
                                <div style="display:flex" >
                                <input name="fkgroupid" id="fkgroupid" type="hidden" value="1"> 
                                <textarea type="text" id="barnumber" class="form-control" name="barnumber" placeholder="Bar No."></textarea>
                                <span style="color: red;">*</span>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div><?php */?>
                        <br>
                        <div class="row">
                        	<div class="col-md-2">
                            	<label>Name</label>
                            </div>
                            <div class="col-md-3" style="display:flex">
                            	<input type="text"  id="firstname" class="form-control" name="firstname" required placeholder="First">
                                <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-3">
                            	<input type="text"  id="middlename" class="form-control" name="middlename" placeholder="Middle">
                            </div>
                            <div class="col-md-3" style="display:flex">
                            	<input type="text" id="lastname" class="form-control" name="lastname" required placeholder="Last">
                                <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <br>
                        <div class="row">
                        	<div class="col-md-2">
                            	<label>Firm</label>
                            </div>
                            <div class="col-md-3" style="display:flex">
                            	<input type="text"  id="companyname" class="form-control" name="companyname" required placeholder="Name">
                            </div>
                            <div class="col-md-3">
                            	<input type="text"  id="address" class="form-control" name="address" placeholder="Street">
                            </div>
                            <div class="col-md-3" style="display:flex">
                            	<input type="text"  id="street" class="form-control" name="street"  placeholder="Suite">
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <br>
                        <div class="row">
                        	<div class="col-md-2">
                            </div>
                            <div class="col-md-3">
                                 <input type="text"  id="city" class="form-control" name="city" placeholder="City">
                            </div>
                            <div class="col-md-3">
                            	<select name="fkstateid" class="form-control" id="fkstateid" required>
                                <option value="">State</option>
                                <?php
								foreach($states as $state)
								{
								?>
                                <option value="<?php echo $state['pkstateid']; ?>"><?php echo $state['statename']; ?></option>
                                <?php
								}
								?>
                            </select> 
                            </div>
                            <div class="col-md-3">
                            	<input type="text" id="zipcode" class="form-control" name="zipcode"  placeholder="Zip Code">
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <br>
                        <div class="row">
                        	<div class="col-md-2">
                            	<label>Phone</label>
                            </div>
                            <div class="col-md-3">
                            	<input type="text"  id="phone" class="form-control" name="phone" placeholder="Phone">
                            </div>
                            <div class="col-md-3" style="display:flex">
                            	<input type="email" value="<?php echo $userDetail['attorney_email'];?>" id="email" class="form-control" name="email" <?php //if($uid != ""){echo "readonly";} ?> placeholder="Email">
                                <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-3">
								<?php
                                //if($uid == "")
                                {
                                ?>
                                <div id="confirmBtn">
                                	<a href="javascript:;" class="btn btn-primary" onClick="verifyEmail()">Verify Email</a>
                            	</div>
								<?php
								}
								?>
                            </div>
                            <div class="col-md-1">
                            	
                            </div>
                            
                        </div>
						<?php
                       // if($uid == "")
                        {
                        ?>
                        <br>
                        <div class="row">
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
                        <?php
                        }
                        ?>
                        <br>
                        <div class="row">
                        	<div class="col-md-2">
                            	<label>Password</label>
                            </div>
                            <div class="col-md-3" style="display:flex">
                            	<input type="password" value="" id="password" class="form-control" name="password" placeholder="Password">
                                <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-3">
                            	<label>Confirm Password</label>
                            </div>
                            <div class="col-md-3" style="display:flex">
								<input type="password" value="" id="confirmpassword" class="form-control" name="confirmpassword" placeholder="Confirm Password">
                                <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <br>
                        <div class="row">
                        <?php 
						include_once("termsofservice.php"); 
						?>
                        <div class="form-group col-lg-12">
                            <label><input name="termsofservices" id="termsofservices" type="checkbox" value="1"><span style="font-weight:normal;padding-left: 8px;">I agree to EasyRogs' <a href="javascipt:;" target="_blank">Terms of Service</a></span><span style="color: red;">*</span></label>
                        </div>
                        
                        <div class="form-group col-lg-12" id="savebtns">
							<?php
                            buttonsave('signupaction.php','profileform','wrapper','get-cases.php?pkscreenid=44',0);
                            ?>
                            <a style="" href="userlogin.php" class="btn btn-danger buttonid">
                            <i class="fa fa-close"></i>
                            Cancel
                            </a>
                        </div>
                    	</div>
                            
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../jsinclude.php");
?>
<script type="text/javascript">
function verifyEmail()
{
	var email	=	$('#email').val();
	if(email == "")
	{
		$("#verification_msg").html("Please enter your email.");
	}
	else
	{
		$.post( "verifyemail.php", { email: email })
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
function barnoFunction(id)
{
	if($("#fkgroupid").prop("checked") == true)
	{
	 $("#barnumber").show();
	}
	else
	{
	 $("#barnumber").hide();
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
function planbtnftn(id)
{
	
	$(".packageschkbox").attr("checked", false);
	$("#pkpackageid"+id).attr("checked", true);
	$("addclass").addClass("active");
	//$('.hpanel plan-box hred').find('div').addClass('active');
	$('.plandivs').removeClass('active');
	$('#maindiv'+id).addClass('active');
}

</script>
<script type="text/javascript">

function changestateftn(id)
{
	$('#loadstates').load('loadstatessignup.php?fkcountryid='+id);
}
function changecityftn(id)
{
	$('#loadcities').load("loadcitiessignup.php?fkstateid="+id);
}
$('input.packageschkbox').on('change', function() {
	 $("#package-error").hide();
    $('input.packageschkbox').not(this).prop('checked', false);
	
});

/***************************************** Validation Form ***********************************/
  $("#signupform").validate({
            rules: {
                firstname: {
                    required: true,
                    minlength: 3
                },
                lastname: {
                    required: true,
                    minlength: 3
                },
                email: {
					required: true,
					email: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
				phone: {
                    required: true
                },
				/*mobile: {
                    required: true
                },*/
				address: {
                    required: true
                },
				/*fkcountryid: {
                    required: true
                },*/
				fkstateid: {
                    required: true
                },
				zipcode: {
                    required: true
                }
            },
            messages: {
				firstname: {
                    required: "Please enter your first name",
                    minlength: "Please enter alteast 3 characters)"
                },
				lastname: {
                    required: "Please enter your last name",
                    minlength: "Please enter alteast 3 characters)"
                },
				email: {
                    required: "Please enter your email address",
                    email: "Please enter valid email address)"
                },
				password: {
                    required: "Please enter your phone number",
                    minlength: "Please enter atleast 6 characters)"
                },
                phone: {
                    required: "Please enter your phone number"
                },
				/*mobile: {
                    required: "Please enter your mobile number"
                },*/
				address: {
                    required: "Please enter your address"
                },
				 /*fkcountryid: {
                    required: "Please select your country."
                },*/
				fkstateid: {
                    required: "Please select your state."
                },
				zipcode: {
                    required: "Please enter your zipcode."
                }
				
            },
            submitHandler: function(form) 
			{
                //form.submit();
				   //var data = $(form).serialize();
				     var formData = new FormData(form);
       			  $.ajax({
						type: 'POST',
						url: 'signupaction.php',
						data:formData,
						cache:false,
						contentType: false,
						processData: false,
						success: function(data) {
							//alert(data);
							var result			=	JSON.parse(data);
							var msg				=	result.msg;
							//var packageurl		=	decodeURIComponent(result.packagelink);
							//var packageuid		=	result.packageuid;
							var membershipuid	=	result.membershipuniqueid;
							//alert(decodeURIComponent(result.packagelink));
							//return;
							if(msg == 'alreadyuser')
							{
								$("#errormsg").show();
								$("#errormsg").html("Email already exists.");
								$("#errormsg").delay(2000).fadeOut();		
							}
							else if(msg == "success")
							{
								$("#successmsg").show();
									setTimeout(function()
									{
										$("#successmsg").delay(1000).fadeOut();	
										window.location.href= "userlogin.php";	
										//var packageurl	=	$(".packageschkbox:checked").attr('data-url');
										/*if(packageurl != "")
										{
								   			window.location.href= packageurl+"?membershipuid="+membershipuid;
										}
										else
										{
											window.location.href= "userlogin.php";	
										}*/
									},5000);
							}
						},
						error: function(data) {
									$("#errormsg").show();
									$("#errormsg").delay(2000).fadeOut();	
						}
						});
            }
        });
</script>
<?php /*?><script src="vendor/jquery-validation/jquery-1.9.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../assets/vendor/jquery-validation/jquery.maskedinput.js"></script><?php */?>

<script type="text/javascript">

$(function() 
{
	changestateftn(254);
	//$.mask.definitions['~'] = "[+-]";
	//$("#phone").mask("+1-999-999-9999");
	$(".selectsearch").select2();
});


</script>

</body>
</html>