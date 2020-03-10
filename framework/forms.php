<?php session_start();
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(40);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'forms.php';
$div		=	'maindiv';
$form 		= 	"feedbackfrm";
define(IMGPATH,'../images/');
/******************DELETE*****************************/
if($_GET['oper']=='del')
{
	$ids		=	trim($_GET['id'],",");
	$AdminDAO->deleterows('tblsentemail',"pksentemailid IN ($ids)");
}
/***********************************************/
$query	= "SELECT
				pktemplateid,
				title as titleform,
				DATE_FORMAT(creationdate,'%M-%d-%Y %H:%i:%s') as creationdate,
				DATE_FORMAT(lastupdated,'%M-%d-%Y %H:%i:%s') as lastupdated,
				(SELECT username FROM system_addressbook WHERE pkaddressbookid = createdby) as createdby,
				(SELECT username FROM system_addressbook WHERE pkaddressbookid = updatedby) as updatedby,
				(SELECT coursename FROM tblcourse WHERE pkcourseid = fkcourseid) as formcoursename
			FROM
				tblreviewtemplate
			";
			/*(CASE
					WHEN (usertype = 1) THEN 'Delegates'
					WHEN (usertype = 2) THEN 'Booking Persons'
					WHEN (usertype = 3) THEN 'Invoice'
					WHEN (usertype = 4) THEN 'Staff'
					WHEN (usertype = 5) THEN 'Blank'
					ELSE 'Custom'
				  END) usertype,*/
$i=0;
if(@in_array('82',$actions))
{
	$navbtn .= "<a class='button2' id='addaccount' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','form.php','sugrid','$div')\" title='Add Form'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(@in_array('83',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editmenus\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'form.php','sugrid','$div') title=\"Edit Form\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(@in_array('84',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletemenus onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Form\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
/*$navbtn .="	<a class=\"button2\" id=\"editmenus\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'sentemaillog.php','sugrid','$div') title=\"Sent Email Log\">Sent Email Log</span></a>&nbsp;|&nbsp;";
$navbtn .="	<a class=\"button2\" id=\"editmenus\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'urlstats.php','sugrid','$div') title=\"URL Stats\">URL Stats</span></a>&nbsp;|&nbsp;";*/
?>
<div id="menudiv"></div>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Manage Feedback Forms</div>
<!--<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>-->
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby);
?>
</div>