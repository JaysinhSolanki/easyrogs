<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@session_start();
/**
* Signout Old session
**/
setcookie('rememberme','', time()+(86400*30), "/");
setcookie("rememberme",'', time()-3600);
@session_destroy();

require_once("../bootstrap.php");
include_once("../includes/classes/login.class.php");
require_once("head.php");
/********************* Load Countries **************************/

include_once("../includes/classes/functions.php");
//$AdminDAO->displayquery = 1;
$states	=	$AdminDAO->getrows('system_state','*',"fkcountryid = :fkcountryid ",array(":fkcountryid"=>254), 'statename', 'ASC');
/********************* Load Countries **************************/
//$packages	=	$AdminDAO->getrows('sub_package','*',"",'packagename', 'ASC');
if(isset($_SESSION['addressbookid']) && $_SESSION['addressbookid']>0)
{
?>
    <script type="text/javascript">
		window.location	=	"index.php";
    </script>
<?php
}
?>
<body class="blank">
<style>
.register-container
{
	max-width:100% !important;	
}
input[type=checkbox]
{
  -webkit-appearance:checkbox !important;
}
</style>
<!-- Simple splash screen-->
<?php
	//require_once("splashscreen.php");
?>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="color-line"></div>
<?php /*?><div class="back-link">
    <a href="userlogin.php" class="btn btn-primary">Back to Login</a>
</div><?php */?>
<div class="register-container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="text-center m-b-md">
                <h3>Registration<?php //dump($_SESSION);?></h3>
                <small>Create your account!</small>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form name="signupform" action="#" method="post" id="signupform">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                	<h3>Login Info</h3>
                                </div>
                                <div class="form-group col-lg-6" style="text-align:right">
                                	<a  href="<?php echo $domain;?>userlogin.php" style="float:right; margin-top:5px;"><h3>Login</h3></a>
                                </div>
                            </div>
						<hr>
                            <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="firstname">First Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                <input type="text" value="" id="firstname" class="form-control" name="firstname" required>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="middlename">Middle Name</label>
                                <input type="text" value="" id="middlename" class="form-control" name="middlename">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="lastname">Last Name</label>
                                <input type="text" value="" id="lastname" class="form-control" name="lastname" required>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="email">Email Address<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                <input type="email" value="" id="email" class="form-control" name="email" required onBlur="loadLoginDetails();">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="password">Password<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                <input type="password" value="" id="password" class="form-control" name="password" onBlur="loadLoginDetails()">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="confirmpassword">Confirm Password<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                <input type="password" value="" id="confirmpassword" class="form-control" name="confirmpassword" onBlur="loadLoginDetails()">
                            </div>
                            
                            <div class="form-group col-lg-6">
                                <label for="lastname">Firm Name</label>
                                <input type="text" value="" id="companyname" class="form-control" name="companyname" required>
                            </div>
                            
                              <div class="form-group col-lg-6">
                                <label for="address">Address</label>
                                <input type="text" value="" id="address" class="form-control" name="address">
                            </div>
                             <div class="form-group col-lg-6">
                                <label for="zipcode">Street</label>
                                <input type="text" value="" id="street" class="form-control" name="street">
                            </div>
                             <div class="form-group col-lg-6">
                                <label for="zipcode">City</label>
                                <input type="text" value="" id="city" class="form-control" name="city">
                            </div>
                             
                           
                           
                            <div class="form-group col-lg-6">
                                <label for="fkstateid">State</label>
                                <select name="fkstateid" class="form-control" id="fkstateid" required>
                                <option value="">Please Select State</option>
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
                            <div class="form-group col-lg-6">
                                <label for="zipcode">Zipcode</label>
                                <input type="text" value="" id="zipcode" class="form-control" name="zipcode">
                            </div>
                             <div class="form-group col-lg-6">
                                <label for="phone">Phone</label>
                                <input type="text" value="" id="phone" class="form-control" name="phone">
                            </div>
                            
                            <div class="form-group col-lg-6">
                                <label for="fkadmittedstateid">Admission State</label>
                                <select name="fkadmittedstateid" class="form-control" id="fkadmittedstateid" required>
                                <option value="">Please Select Admission States</option>
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
                            <div class="form-group col-lg-6">
                                <label for="barnumber">Bar Number</label>
                                <input type="text" value="" id="barnumber" class="form-control" name="barnumber">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="barnumber">Info<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                <textarea class="form-control" id="attorney_info"  name="attorney_info"></textarea>
                            </div>
                             
                            
                            </div>
                           
                            <div class="row">
                               <div class="form-group ">
                                <div class="checkbox">
                                    <input type="checkbox" class="i-checks"  id="agree" name="agree" value="1">
                                    <span style="vertical-align:middle;"><b>I agree to accept service via email.</b></span>
                                </div>
                                </div>
                               
                                
                            </div>
                            
                            <div class="text-center">
                              <?php /*?>  <button class="btn btn-success" id="register" name="register" type="button" onClick="addform()">Register</button><?php */?>
                                <?php
								buttonsave('signupaction.php','signupform',' ','thankyou.php',0)
								?>
                              <?php /*?>  <button class="btn btn-default" id="reset" type="reset">Cancel</button><?php */?>
                                <p>
                                <b id="successmsg" style='color:Green; display:none;'>Thanks...! You have successfully created your account!</b>
                                <b id="errormsg" style='color:Red; display:none;'>Oppppps...! Email already exist.</b>
                                </p>
                            </div>
                            
                        </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="display:none">
    	<form id="loginform" method="post" action="userlogin.php">
        	<input type="hidden" name="email" id="login_email">
            <input type="hidden" name="pass" id="login_pass">
        </form>
    </div>
</div>
<?php
require_once("../jsinclude.php");
?>

<script type="text/javascript">




/***************************************** Validation Form ***********************************/
 

</script>
<script src="vendor/jquery-validation/jquery-1.9.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="vendor/jquery-validation/jquery.maskedinput.js"></script>
<script type="text/javascript">
function loadLoginDetails()
{
	$('#login_email').val($('#email').val());
	$('#login_pass').val($('#password').val());
}
$(function() 
{
	$.mask.definitions['~'] = "[+-]";
	$("#phone").mask("+41 99 999 99 99");
});
</script>

</body>
</html>