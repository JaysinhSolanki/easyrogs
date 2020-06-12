<?php
require_once(__DIR__ . "/../system/bootstrap.php");
include_once(__DIR__ . "/../system/library/classes/functions.php");
include_once(__DIR__ . "/../system/library/classes/login.class.php");
if($_GET['outside'] == 1) {
?>
    <script type="text/javascript">
		window.location.href =	"<?php echo FRAMEWORK_URL; ?>signout.php";
    </script>
<?php
} 

if(!empty($_SESSION['addressbookid'])) {
?>
    <script type="text/javascript">
		window.location.href =	<?= ROOTURL; ?>"system/application/index.php";
    </script>
<?php
} 
//echo "cookies".$_COOKIE['rememberme'];
$Login			=	new Login($AdminDAO);
if(!empty($_POST))
{
	$email			=	$_POST['email'];
	$pass			=	$_POST['pass'];
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
			header("Location: " .ROOTURL. "system/application/index.php");
			exit;
		}
	}
}

if(sizeof($_POST)>0)
{
	$result	=	$Login->loginprocess($email,$pass,1);
	
	if($result	== 1)
	{
		$_SESSION['module']		=	$module;
		if($rememberme==1)
		{
			setcookie('rememberme',$_SESSION['uid'], time()+(86400 * 30 *30), "/");
		}
		else
		{
			setcookie("rememberme","", time()-3600);
		}
		header("Location: " .ROOTURL. "system/application/index.php");
		// TODO Add analytics event login 
		// trackEvent('login', { event_category: 'account', event_label: <?= $email ?->, });
		exit;
	}
	else
	{
		//$error	=	$Error->display($result,1);
		$errors	=	msg($result,1); 

	}
}
require_once(SYSTEMPATH."application/ctxhelp_header.php"); 
?>
<link rel="stylesheet" href="<?= ROOTURL ?>system/assets/videopopup.css" />
<script type="text/javascript" src="<?= ROOTURL ?>system/assets/videopopup.js"></script>
<script type="text/javascript">
        jQuery( $ => {
           $('#vidBox').VideoPopUp({
            	backgroundColor: "#17212a",
            	opener: "video_introduction",
                maxweight: "340",
                idvideo: "video-demo",
                pausevideo : true
            });
        });
</script>

<?php
//require_once("splashscreen.php");
?>
</div><!-- 💣 In case there's some `<div>` without its closing tag somewhere -->
<div style="width: 100vw;">
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
                            <button class="ladda-button btn btn-info btn-block" data-style="zoom-in">
                            	<span class="ladda-label">Login</span><span class="ladda-spinner"></span>
                            </button>
                            
                            <a  href="<?php echo $_SESSION['framework_url'];?>forgotpassword.php" style="float:right; margin-top:5px;">Forgot Password?</a>
                        </form>
                </div>
            </div>
        </div>
		<style>
		#info-panel {
			padding: 12px 0 0 ; clear: both;
			text-align: center; font-size: 1.2em;
			color: white; background-color: #3498db !important;
		}
		#info-panel:hover, #info-panel > .btn-info:hover {
			background-color: #3498db !important;
		}
		#info-panel .actions {
			display: flex; justify-content: space-around; align-items: stretch;
			padding: 0.5em; 
		}
		#info-panel .actions>span {
			flex-grow: 0; align-self: baseline; margin: auto 0.5em; font-
		}
		#info-panel a {
			display: table-cell; width: 45%; padding: auto 1em; 
			flex-grow: 1; align-self: baseline; 
		}
		</style>
        <div class="">
            <div id="info-panel" class="hpanel" style="">
				<div style="/*margin-bottom:-0.4em;*/">New to EasyRogs?</div>
                <div class="actions" style="width: 100%;">
                    <a id="video_introduction" href="javascript:;" class="ladda-button btn btn-info col-md-6" style="">
					  Watch our Demo
					</a>
					<span> or </span> 
                    <a id="faq" href="javascript:;" class="ladda-button btn btn-info" style="" onclick="showFAQ(); ">
					  Peruse our FAQs
					</a>
                </div>
                <div id="vidBox" style="display: none;">
                    <div id="videCont">
                		<video id="video-demo" preload="none" x-autoplay controls style="
																				position: fixed;
																				top: 0; left: 0;
																				max-width: 100vw; max-height: 100vh; 
																				height: auto;
																			"
								data-src="demo.mp4">
                            <source src="<?= ROOTURL ?>system/application/demo.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>

			<p style="clear: both; margin: -0.5em 0 0.5em; text-align: center; font-size:1.4em;"> or </p>

            <div class="hpanel btn-success" style="text-align: center; ">
				<button class="ladda-button btn btn-success btn-block" style="padding-bottom: 25px;">
					<a  href="<?php echo DOMAIN;?>signup.php" style="color: white;">
						<div style="font-size: 2.2em;">Join</div>
						<div style="margin-top:0.5em">Membership is complementary</div>
					</a>
				</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php
	if( $errors && sizeof(json_decode($errors)) > 0 ) {
?>
		<script type="text/javascript">
        msg('<?= $errors ?>');
        </script>
<?php
	}
?>
</body>
</html>
<script type="text/javascript">
	$( _ => {
		$('#loginForm .i-checks').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue'
		})

		ctxUpdate({ id: -1, pkscreenid: -1, url: 'userlogin.php', } );
	});
</script>
<?php
ob_end_flush();
?>
