<?php
include_once("adminsecurity.php");
$id = $_REQUEST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$actionlabel 		= 	filter($_POST['actionlabel']);
	$fkactiontypeid	 	= 	filter($_POST['fkactiontypeid']);
	$fkscreenid	 		= 	$_POST['fkscreenid'];
	if($actionlabel=='')
	{
		$msg	=	"<li>Action Name can not be left blank</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields = array('actionlabel','fkactiontypeid','fkscreenid');
	$values = array($actionlabel, $fkactiontypeid, $fkscreenid);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("system_action",$fields,$values," pkactionid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("system_action",$fields,$values);
	}//end of else
exit;
}// end post
?>