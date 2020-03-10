<?php
@session_start();
//------------Code of Daily log in user Logout start-----------
//require_once("{$_SESSION['includes_path']}/classes/AdminDAO.php");
/*if($_SESSION['groupid'] == 3)
{
    $what	=	"Admin Logout";
}
elseif($_SESSION['groupid'] == 4)
{
    $what	=	"Supervisor Logout";
}
elseif($_SESSION['groupid'] == 5)
{
    $what	=	"Project Manager Logout";
}
elseif($_SESSION['groupid'] == 7)
{
    $what	=	"Project Manager Logout";
}*/
//$AdminDAO   =   new AdminDAO();
//$AdminDAO->logactivity($what,'','');
//------------Code of Daily log in user Logout start-----------
setcookie('rememberme','', time()+(86400*30), "/");
setcookie("rememberme",'', time()-3600);
$admin_url		=	$_SESSION['admin_url'];
@session_destroy();
header("Location:".$_SESSION['admin_url']."userlogin.php?loggedout=1");
exit;