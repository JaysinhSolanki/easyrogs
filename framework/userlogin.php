<?php
session_start();
require_once("../settings.php"); 
include_once("../library/classes/AdminDAO.php");	 
include_once("../library/classes/functions.php");	 
//include_once("../library/classes/filter.php");	 
include_once("../library/classes/login.class.php"); 
//include_once("../library/classes/error.php");  
if($_GET['outside'] == 1) 
{
?>
    <script type="text/javascript">
		window.location.href =	"<?php echo FRAMEWORK_URL; ?>signout.php";
    </script>
<?php
} 


if(!empty($_SESSION['addressbookid']))
{
?>
    <script type="text/javascript">
		window.location.href =	"index.php";
    </script>
<?php
} 
//echo "cookies".$_COOKIE['rememberme'];
$AdminDAO		=	new AdminDAO();
$Login			=	new Login($AdminDAO);
if(!empty($_POST))
{
	$email			=	$_POST['email'];
	$pass			=	$_POST['pass'];
	//$province		=	$_POST['province'];
	$rememberme		=	$_POST['rememberme'];
	$module			=	1;//$_POST['module'];
}
if(!empty($_GET))
{
	if($_GET['loggedout']==1)
	{
		setcookie("rememberme", "",time()-3600);
	}
}
if(isset($_COOKIE['rememberme']) && $_COOKIE['rememberme'] != '')
{
	$uid	=	$_COOKIE['rememberme'];
	$row	=	$AdminDAO->getrows("system_addressbook","*","uid=:uid", array(":uid"=>$uid));
	if(@count($row)>0)
	{
		
		$password	=	$row[0]['password'];
		$email		=	$row[0]['email'];
		$result		=	$Login->loginprocess($email,$password,$type);
		if($result	== 1)
		{
			$_SESSION['module']		=	$module;
			$_SESSION['language']	=	$_POST['language'];
			//$_SESSION['province']	=	$province;
			//echo 2222;
			header("Location: ../application/index.php");
			exit;
		}
	}
}

if(sizeof($_POST)>0)
{
	$result	=	$Login->loginprocess($email,$pass,1);
	
	if($result	== 1)
	{
		//$_SESSION['province']	=	$province;
		$_SESSION['module']		=	$module;
		if($rememberme==1)
		{
			setcookie('rememberme',$_SESSION['uid'], time()+(86400 * 30 *30), "/");
		}
		else
		{
			setcookie("rememberme","", time()-3600);
		}
		header("Location: ../application/index.php");
		exit;
	}
	else
	{
		//$error	=	$Error->display($result,1);
		$errors	=	msg($result,1); 

	}
}
require_once("head.php");
?>
<link rel="stylesheet" href="/system/assets/videopopup.css" />
<script type="text/javascript" src="/system/assets/videopopup.js"></script>
<script type="text/javascript">
        jQuery(function ($) {
           $('#vidBox').VideoPopUp({
            	backgroundColor: "#17212a",
            	opener: "video_introduction",
                maxweight: "340",
                idvideo: "v1",
                pausevideo : true
            });
        });
</script>

<body class="blank">
<?php
	//require_once("splashscreen.php");
?>
<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3 class="mylogo f48"><?php echo $systemmaintitle; ?></h3>
                <small>Log in to your account!</small>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form action="userlogin.php" id="loginForm" method="post">
                            <div class="form-group">
                                <label class="control-label" for="username">Username</label>
                                <input type="text" placeholder="example@gmail.com" title="Please enter you username" required="" value="" name="email" id="username" class="form-control" autocomplete="off">
                                <!--<span class="help-block small">Your unique username to</span>-->
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Please enter your password" placeholder="******" required="" value="" name="pass" id="password" class="form-control">
                              <!--  <span class="help-block small">Your strong password</span>-->
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" class="i-checks" checked name="rememberme" value="1">
								<span style="vertical-align:middle;">Remember password</span>
                                <p class="help-block small" style="margin-left:26px">Recommended only if this is your personal computer.</p>
                            </div>
                            <button class="ladda-button btn btn-success btn-block" data-style="zoom-in">
                            	<span class="ladda-label">Login</span><span class="ladda-spinner"></span>
                            </button>
                            
                            <a  href="<?php echo DOMAIN;?>signup.php" style="float:left; margin-top:5px;">Sign up here</a>
                            <a  href="<?php echo $_SESSION['framework_url'];?>forgotpassword.php" style="float:right; margin-top:5px;">Forgot Password?</a>
                        </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="hpanel">
                <div class="panel-bodys">
                    <a href="javascript:void(0)" id="video_introduction" class="ladda-button btn btn-intro btn-block">New to EasyRogs? <br />Please watch this short introduction</a>
                </div>
                <div id="vidBox" style="display: none;">
                    <div id="videCont">
                		<video id="v1" loop controls>
                            <source src="https://www.easyrogs.com/system/application/demo.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
	require_once("../jsinclude.php");
	
	if(sizeof(json_decode($errors)) > 0)
	{
	?>
		<script type="text/javascript">
		//alert(132);
		//msg(1111111111111111111111111111);
        msg('<?php echo $errors;?>');
        </script>
    <?php
	}
?>
</body>
</html>
<?php
//ob_end_flush();
?>