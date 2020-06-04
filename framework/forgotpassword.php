<?php
//ob_start();
@session_start();
require_once("../system/bootstrap.php"); 
include_once($_SESSION['library_path']."classes/functions.php");	 
include_once($_SESSION['library_path']."classes/login.class.php"); 

if(isset($_SESSION['addressbookid']) && $_SESSION['addressbookid']>0)
{
?>
    <script type="text/javascript">
		window.location	=	<?php echo DOMAIN; ?>;
    </script>
<?php
}
$email			=	@trim(@$_POST['email']);
$module			=	1;//$_POST['module'];
if(sizeof($_POST)>0)
{
	$_SESSION['userenteremail']	=	$email;
	$emailwhere			=	"email = '$email'";
	$addressbookdata	=	$AdminDAO->getrows('system_addressbook',"*",$emailwhere);
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$errorData	=	msg(123,1);
		
	}
	else if(sizeof($addressbookdata) == 0)
	{
		$errorData	=	msg(129,1);
	}
	else
	{
		// If valid password then send email for reset password
		UserMailer::forgotPassword($addressbookdata[0]);
		
		$redirectme	=	FRAMEWORK_URL."forgotpassword.php";
		$errorData	=	msg(130,1);
		$_SESSION['userenteremail'] = "";
	}
}
require_once(SYSTEMPATH."application/ctxhelp_header.php"); 
include_once(SYSTEMPATH.'body.php');
?>

<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3><?php echo $systemmaintitle; ?> Forgot Password?</h3>
                <small>Manage your accounts!</small>
                
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form action="<?php echo FRAMEWORK_URL ?>forgotpassword.php" id="forgotpasswordform" method="post" name="forgotpasswordform">
                            <div class="form-group">
                                <label class="control-label" for="username">Email</label>
                                <input type="text" placeholder="example@gmail.com" title="Please enter you email" required="" value="<?php echo @$_SESSION['userenteremail'];?>" name="email" id="email" class="form-control" autocomplete="off">
                                <span class="help-block small">Your unique email to</span>
                            </div>
                            <button class="btn btn-success btn-block">Submit</button>
                            <a class="btn btn-default btn-block" href="<?php echo DOMAIN;?>userlogin.php">Login</a>
							<?php /*?><a class="btn btn-default btn-block" href="<?php echo $domain;?>signup.php">Register</a><?php */?> 
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	//$errorData			=	json_encode(array("pkerrorid"=>$pkerrorid,"messagetype"=>$errortype, "messagetext"=>$errormsg));
	//$_SESSION['userenteremail']	=	'';
	if(@$errorData !="")
	{
	?>
		<script type="text/javascript">
		//alert(1);
        	msg('<?php echo @$errorData;?>');
        </script>
    <?php
	}
?>
</body>
</html>
<script type="text/javascript">
	$( _ => {
		ctxUpdate({ id: -1, pkscreenid: -1, url: 'forgotpassword.php', } );
	});
</script>
