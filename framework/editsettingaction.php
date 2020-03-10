<?php
include_once("../includes/classes/adminsecurity.php");
extract($_POST);
$oldrecord	=	$AdminDAO->getrows('system_setting',"*","pksettingid =	'$pksettingid'");

$fields		=	array('managercreationemail','manageractivationemail','supervisorcreationemail','supervisoractivationemail','customercreationemail','customeractivationemail','newprojectemailtomanger','newprojectemailtosupervisor','newprojectemailtocustomer','newprojectemailtosupervisor','newprojectemailtocustomer','managerchangedemail','supervisorchangedemail','evaluationemailtocustomer','evaluationemailtocustomerf','evaluationemailtocustomercontact','evaluationemailtoresource','evaluationemailtoresourcef','dailylogemailtocustomer','dailylogemailtocustomercontact','dailylogemailtoresource','displayupcomingtaskdays');
$data		=	array($managercreationemail,$manageractivationemail,$supervisorcreationemail,$supervisoractivationemail,$customercreationemail,$customeractivationemail,$newprojectemailtomanger,$newprojectemailtosupervisor,$newprojectemailtocustomer,$newprojectemailtosupervisor,$newprojectemailtocustomer,$managerchangedemail,$supervisorchangedemail,$evaluationemailtocustomer,$evaluationemailtocustomerf,$evaluationemailtocustomercontact,$evaluationemailtoresource,$evaluationemailtoresourcef,$dailylogemailtocustomer,$dailylogemailtocustomercontact,$dailylogemailtoresource,$displayupcomingtaskdays);
$AdminDAO->updaterow('system_setting',$fields,$data," pksettingid = '$pksettingid'");

$oldrecord	=	$AdminDAO->getrows('system_setting',"*","pksettingid =	'$pksettingid'");
$what		=	'Updated Notification Setting.';
$AdminDAO->logactivity($what,$oldrecord,$newrecord);
?>
