<?php
require_once("adminsecurity.php");
$addressbookid	=	$_SESSION['addressbookid'];
$users			=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid	=	'$addressbookid'");
$user			=	$users[0];
$userimage		=	$user['userimage'];	
if($userimage == "")
{
	$userimage	=	"gumptech-logo.png";	
}
?>
<a href="index.php">
	<img src="uploads/profile/<?php echo $userimage;?>" class="img-circle m-b" alt="logo">
</a>