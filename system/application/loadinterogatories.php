<?php
@session_start();
require_once("adminsecurity.php");
$type			=	$_GET['type'];
$case_id		=	$_GET['case_id'];
$id				=	$_GET['discoveryid'];
if($id > 0)
{
	$discoveries		=	$AdminDAO->getrows('discoveries',"*","id	= :id ",array('id'=>$id));
	$discovery			=	$discoveries[0];
	$conjunction_with	=	$discovery['conjunction_with'];
	$uid				=	$discovery['uid'];
	$already_where		=	" AND conjunction_with != {$conjunction_with} ";
}
else
{
	$already_where	=	" ";
}
//dump($discoveries);
//exit;
$already_Con_Discoveries	=	$AdminDAO->getrows('discoveries',"conjunction_with","form_id = 4 AND case_id = :case_id $already_where ",array("case_id"=>$case_id));
if(sizeof($already_Con_Discoveries) > 0)
{
	$already_Con_ids	=	array();
	foreach($already_Con_Discoveries as $conjunction_with_ids)
	{
		$already_Con_ids[]		=	$conjunction_with_ids['conjunction_with'];
	}
	$alreadyattached	=	implode(",",$already_Con_ids);
	$where	=	" AND id NOT IN({$alreadyattached}) ";
}
else
{
	$where	=	"";
}


if($type > 0)
{
	if($type == 1)
	{
		$form_id	=	1;
	}
	elseif($type == 2)
	{
		$form_id	=	2;
	}
	//$AdminDAO->displayquery=1;
	$con_discoveries	=	$AdminDAO->getrows('discoveries',"id,discovery_name,form_id","form_id  = '$form_id' AND case_id = :case_id  $where ",array("case_id"=>$case_id));
	//$AdminDAO->displayquery=0;
	foreach($con_discoveries as $data)
	{
	?>
		<option <?php if(@$conjunction_with== $data['id']){echo " SELECTED ";}?> value="<?php echo $data['id'];?>"><?php echo  $data['discovery_name'];?></option>
	<?php
	}
}
else
{
	echo "<option>Select</option>";
}

