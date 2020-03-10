<?php
require_once("adminsecurity.php");
$id = $_REQUEST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	
	$pkactiontypeid		=	$_POST['pkactiontypeid'];
	$actiontypelabel	= 	filter($_POST['actiontypelabel']);
	$actioncode			=	$_POST['actioncode'];
	
	
	if($actiontypelabel=='')
	{
		msg(50,2);
	}
	
	if($actioncode=='')
	{
		msg(51,2);
	}
	if($screenname)
	{
		$unique = $AdminDAO->isunique('system_actiontype', 'actioncode', $actioncode, '$actiontypelabel', $actiontypelabel);
		if($unique=='1')
		{
			msg(52,2);
		}
	}
	$fields = array('actiontypelabel','actioncode');
	$values = array($actiontypelabel, $actioncode);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("system_actiontype",$fields,$values," pkactiontypeid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("system_actiontype",$fields,$values);
	}//end of else
	if($id > 0)
	{
		msg(30);
	}
	else
	{
		msg(7);
	}
}// end post
?>