<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
@session_start();	
require_once("../system/bootstrap.php"); 
if( $_SESSION['addressbookid'] ) {
?>
    <script type="text/javascript">
		window.location	=	<?php echo DOMAIN; ?>;
    </script>
<?php
}
$passworduid	=	$_GET['uid'];
require_once(SYSTEMPATH."application/ctxhelp_header.php"); 
?>
<body class="blank">
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<?php /*?><div class="color-line"></div><?php */?>
<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3><?php echo $systemmaintitle; ?> Reset Password</h3>
                <small>Manage your accounts!</small>
                <?php /*?><strong class="text-danger"><?php //echo "$error";?></strong><?php */?>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form  id="resetpasswordform" method="post" name="resetpasswordform">
                            <div class="form-group">
                                <label class="control-label" for="password">New Password</label>
                                <input type="password" placeholder="" title="Please enter you password"  value="" name="password" id="password" class="form-control" autocomplete="off">
                                <span class="help-block small">Your new password</span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Confirm New Password</label>
                                <input type="password" placeholder="" title="Please enter you password" value="" name="cpassword" id="cpassword" class="form-control" autocomplete="off">
                               <span class="help-block small">Your new confirm password</span>
                                <input type="hidden" name="uid" id="uid" value="<?php echo $passworduid;?>">
                            </div>
                            <button class="btn btn-success btn-block" id="ajaxformsubmit">Submit</button>
                            <a class="btn btn-default btn-block" href="<?php echo DOMAIN;?>signup.php">Register</a>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	require_once($_SESSION['system_path']."jsinclude.php");
?>
<script type="text/javascript">
$("#ajaxformsubmit").click(function(event)
{
	event.preventDefault();
	$.ajax({
			url:'<?php echo FRAMEWORK_URL ?>resetpasswordaction.php',
			type:'POST',
			data:$("#resetpasswordform").serialize(),
			success:function(result){
				 msg(result);
				 var parsed = JSON.parse(result);
				 if(parsed.messagetype == 2)
				 {
					  setTimeout(function(){
					  window.location	=	'<?php echo DOMAIN ?>userlogin.php';
					}, 6000);
					
				 }
			}

	});
 });
</script>
</body>
</html>