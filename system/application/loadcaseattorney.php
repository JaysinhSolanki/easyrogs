<?php
require_once("adminsecurity.php");
$fkaddressbookid	=	$_SESSION['addressbookid'];
$case_id			=	$_REQUEST['case_id'];
$attorneylist		=	array();
$added				=	array();
$where				=	"";

$caseDetails		=	$AdminDAO->getrows('cases',"*","id = :id",array("id"=>$case_id));
$case_attorney		=	$caseDetails[0]['case_attorney'];
if($_SESSION['groupid'] == 3) 
{
	$attorneylist[]	=	array("id"=>$_SESSION['loggedin_email'],"attorney_name"=>$_SESSION['name'],"attorney_email"=>$_SESSION['loggedin_email']);
	if($case_attorney == "" || $case_attorney == '0')
	{
		$case_attorney	=	$_SESSION['loggedin_email'];
	}
	
	//Profile Attorneys
	/*$myattorneys		=	$AdminDAO->getrows("attorney","*", "fkaddressbookid = :fkaddressbookid AND attorney_type = 1", array(":fkaddressbookid"=>$fkaddressbookid), "attorney_name", "ASC");
	foreach($myattorneys as $attorney)
	{
		$added[]		=	$attorney['id'];
		$attorneylist[]	=	array("id"=>$attorney['id'],"attorney_name"=>$attorney['attorney_name'],"attorney_email"=>$attorney['attorney_email']);
	}
	if(!empty($added))
	{
		$attorney_ids		=	implode(",",$added);
		$where				=	" AND a.id NOT IN ($attorney_ids) ";	
	}*/
}

//Get owners in which logged in user is as a profile team member
$myowners		=	$AdminDAO->getrows("attorney,system_addressbook","*", "fkaddressbookid = pkaddressbookid AND attorney_email = :attorney_email AND attorney_type = 1", array(":attorney_email"=>$_SESSION['loggedin_email']));
if(!empty($myowners))
{
	foreach($myowners as $owner)
	{
		$attorneylist[]	=	array("id"=>$owner['email'],"attorney_name"=>$owner['firstname']." ".$owner['lastname'],"attorney_email"=>$owner['email']);
	}
}

//Case Team Attorneys
/*$caseteamattornys		=	$AdminDAO->getrows("attorney a ,case_team ct","a.*,ct.id as case_team_id", "ct.attorney_id = a.id AND ct.fkcaseid = :case_id AND ct.is_deleted  = 0 AND a.fkaddressbookid = :fkaddressbookid $where	", array(":case_id"=>$case_id,":fkaddressbookid"=>$fkaddressbookid), "attorney_name", "ASC");
foreach($caseteamattornys as $attorney)
{
	$attorneylist[]	=	array("id"=>$attorney['id'],"attorney_name"=>$attorney['attorney_name'],"attorney_email"=>$attorney['attorney_email']);
}*/

$keys = array_column($attorneylist, 'attorney_name');
array_multisort($keys, SORT_ASC, $attorneylist);
echo "<option value=''>Select Attorney</option>";
foreach($attorneylist as $attorney)
{
?>
	<option value="<?=$attorney['id']?>" <?php if(trim($case_attorney) == trim($attorney['id'])){echo "selected";} ?>><?=$attorney['attorney_name']." (".$attorney['attorney_email'].")";?></option>
<?php
}