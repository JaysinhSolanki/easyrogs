<?php
require_once("adminsecurity.php");
$addressbookid	=	$_SESSION['addressbookid'];
$code			=	$_POST['idata'];
if($code == '')
{
	msg(90,2);	
}
if(isset($_POST['idata']))
{
	list($type, $code) = explode(';', $code);
    list(, $code)      = explode(',', $code);
    $code = base64_decode($code);
	$filename		=	time().".png";
	$dirfilename	=	'uploads/profile/'.$filename;
    $filemoved		=	file_put_contents($dirfilename, $code);
	if($filemoved)
	{
		// $_SESSION['session_profilename']	=	$filename;
		$fields		=	array('userimage');//,'fax'
		$values		=	array($filename);//,$userfax
		$AdminDAO->updaterow('system_addressbook',$fields,$values," pkaddressbookid = '$addressbookid'");
		$loaddiv		=	"loadpicture";
		$loadpageurl	=	"changeprofilepictureload.php";
		msg(93);
	}
	//echo $filename;
}
?>