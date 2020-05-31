<?php
@session_start();
//Destroy session if user already logged in
setcookie('rememberme','', time()+(86400*30), "/");
setcookie("rememberme",'', time()-3600); 
@session_destroy();

require_once("../bootstrap.php");
require_once(SYSTEMPATH."application/ctxhelp_header.php"); 
include_once(SYSTEMPATH."library/classes/login.class.php");
//include_once(SYSTEMPATH."library/classes/error.php"); // commented out because it doesn't exist 
include_once(SYSTEMPATH."library/classes/functions.php");

$states	= $AdminDAO->getrows('system_state','*',"fkcountryid = :fkcountryid ",array(":fkcountryid"=>254), 'statename', 'ASC');
$groups	= $AdminDAO->getrows('system_groups','*',"pkgroupid IN (3,4)");
$uid	= $_GET['uid'];

if($uid) {
  $userDetails = $AdminDAO->getrows('system_addressbook u, invitations i','*',"i.uid = :uid AND u.pkaddressbookid = i.attorney_id",array(":uid"=>$uid));
  $error = 0;
  if(!empty($userDetails)) {
		$userDetail	    =	$userDetails[0];
		$status			    =	$userDetail['status'];
		$attorney_email	=	$userDetail['email'];
		if($status != 1) {
			$message	=	"You have already signed up with this email {$attorney_email}.";
			$error		=	1;
		}
	}
	else {
		$message	=	"URL is invalid or expired.";
		$error		=	1;
	}
	if( $error == 1 ) {
?>
        <div class="container">
        <div class="jumbotron text-xs-center" style="margin-top:200px">
        <h1 class="display-3 text-center">Sorry!</h1>
        <p class="lead  text-center"><?= $message ?></p>
        <p class="text-center">Email <a href="mailto:support@EasyRogs.com">support@EasyRogs.com</a> if you need assistance.</p>
        </div>
        </div>
<?php
		exit;
	}
}

$newsignup = 1;
?>
  <style type="text/css">
    .register-container { max-width:100% !important; }
    .required:after { content:"*"; }
    .panel-body p { line-height:25px; } 
    .panel-body h4 { margin-top: 15px; margin-bottom: 15px; }
  </style>

<?php
include_once(SYSTEMPATH.'body.php');
?>
</div>
    <!--[if lt IE 7]>
    <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div class="register-container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="text-center m-b-md">
            <!--h2 class="mylogo f48"><?= $systemmaintitle; ?></h2-->
            <h3>Create your account</h3>
          </div>
          <div class="hpanel">
            <div class="panel-body">
              <form name="profileform" action="#" method="post" id="profileform" enctype="multipart/form-data">
                <input type="hidden" name="uid" value="<?= $uid ?>" />
                <input type="hidden" name="newsignup" value="<?= $newsignup ?>" />
                
                <div class="row">
                  <div class="col-md-6">
                    <h3>Registration</h3>
                  </div>
                  <?php if(!$uid): ?>
                  <div class="col-md-6 text-right">
                    <div class="back-link">
                      <a href="<?= $domain;?>userlogin.php" class="btn btn-primary">Back to Login</a>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
                
                <hr/>
                <div class="row">
                  <div class="col-md-2">
                    <label><input name="fkgroupid" onClick="barnoFunction()" id="fkgroupid" type="checkbox" value="1" /> Attorney?</label>
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
                
                <br/>
                <div class="row">
                  <div class="col-md-2">
                    <label>Name</label>
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="text"  id="firstname" class="form-control" name="firstname" required placeholder="First" />
                    <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-3">
                    <input type="text"  id="middlename" class="form-control" name="middlename" placeholder="Middle" />
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="text" id="lastname" class="form-control" name="lastname" required placeholder="Last" />
                    <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-1"></div>
                </div>
                
                <br/>
                <div class="row">
                  <div class="col-md-2">
                    <label>Firm</label>
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="text"  id="companyname" class="form-control" name="companyname" required placeholder="Name" />
                  </div>
                  <div class="col-md-3">
                    <input type="text"  id="address" class="form-control" name="address" placeholder="Street" />
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="text"  id="street" class="form-control" name="street"  placeholder="Suite" />
                  </div>
                  <div class="col-md-1"></div>
                </div>
                
                <br/>
                <div class="row">
                  <div class="col-md-2"></div>
                  <div class="col-md-3">
                    <input type="text"  id="city" class="form-control" name="city" placeholder="City" />
                  </div>
                  <div class="col-md-3">
                    <select name="fkstateid" class="form-control" id="fkstateid" required>
                      <option value="">State</option>
                      <?php foreach($states as $state): ?>
                        <option value="<?= $state['pkstateid']; ?>"><?= $state['statename']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input type="text" id="zipcode" class="form-control" name="zipcode"  placeholder="Zip Code" />
                  </div>
                  <div class="col-md-1"></div>
                </div>
                
                <br/>
                <div class="row">
                  <div class="col-md-2">
                    <label>Phone</label>
                  </div>
                  <div class="col-md-3">
                    <input type="text"  id="phone" class="form-control" name="phone" placeholder="Phone" />
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="email" value="<?= $attorney_email ?>" id="email" class="form-control" name="email" placeholder="Email" />
                    <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-3">
                    <div id="confirmBtn">
                      <a href="javascript:;" class="btn btn-primary" onClick="verifyEmail()">Verify Email</a>
                    </div>
                  </div>
                  <div class="col-md-1"></div>
                </div>
                
                <br/>
                <div class="row">
                <div class="col-md-2">
                    <label>Password</label>
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="password" value="" id="password" class="form-control" name="password" placeholder="Password" />
                    <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-3" style="visibility:hidden;display:flex" id="verification-code-input">
                    <input type="text" value="" id="verification_code" class="form-control" name="verification_code"  placeholder="Enter verification code" />
                    <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-4">
                    <div style="color:red; display:none" id="verification_msg"></div>
                  </div>
                </div>
                
                <br/>
                <div class="row">
                  <div class="col-md-2">
                    <label>Confirm Password</label>
                  </div>
                  <div class="col-md-3" style="display:flex">
                    <input type="password" value="" id="confirmpassword" class="form-control" name="confirmpassword" placeholder="Confirm Password" />
                    <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-1"></div>
                </div>
                
                <br/>
                <div class="row">
                  <?php include_once("termsofservice.php"); ?>
                  <div class="form-group col-lg-12">
                    <label>
                      <input name="termsofservices" id="termsofservices" type="checkbox" value="1" /><span style="font-weight:normal;padding-left: 8px;">I agree to EasyRogs' <a href="javascipt:;" target="_blank">Terms of Service</a></span><span style="color: red;">*</span>
                    </label>
                  </div>
                  <div class="form-group col-lg-12" id="savebtns">
                    <?php buttonsave('signupaction.php','profileform','wrapper','get-cases.php?pkscreenid=44',0); ?>
                    <a href="userlogin.php" class="btn btn-danger buttonid"><i class="fa fa-close"></i>Cancel</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php require_once("../jsinclude.php"); ?>
    <script type="text/javascript" src="/system/assets/sections/signup.js"></script>
<script type="text/javascript">
$( _ => {
	ctxUpdate({ id: -2, pkscreenid: -2, url: 'signup.php', } );
});
</script>
    
  </body>
</html>