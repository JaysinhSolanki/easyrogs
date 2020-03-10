<?php
include_once("adminsecurity.php");
$id = $_REQUEST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$fieldname 		= 	filter($_POST['fieldname']);
	$fieldlabel	 	= 	filter($_POST['fieldlabel']);
	$fkscreenid	 	= 	$_POST['screenid'];
	if($fieldname=='')
	{
		$msg	=	"<li>Field Name can not be left blank</li>";
	}
	if($fieldlabel=='')
	{
		$msg	.=	"<li>Field Label can not be left blank</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields = array('fieldname','fieldlabel','fkscreenid');
	$values = array($fieldname, $fieldlabel, $fkscreenid);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("system_field",$fields,$values," pkfieldid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("system_field",$fields,$values);
	}//end of else
exit;
}// end post
?>