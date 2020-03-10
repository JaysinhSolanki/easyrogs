<?php
require_once("adminsecurity.php");
if(isset($_GET['pkscreenid']))
{
	$_SESSION['screenid']	=	$_GET['pkscreenid'];
	$screenid				=	$_SESSION['screenid'];
}
else
{
	$screenid =	$_SESSION['screenid'];
}
$screenname	=	" Sections";
include_once("dbgrid.php");
//global $AdminDAO,$userSecurity;
$rights	 	=	$userSecurity->getRights($screenid);
/******************From DATABASE************************************/
//$labels	 	=	$rights['labels'];
//$fields		=	$rights['fields'];
//$rights['actions'];
/*****************************************************************/
$labels = array("ID","Section Name", "Status");
$fields = array("pksectionid","sectionname", "status");
$actions 	=	array(93,94,95);
//*************delete************************
$dest 		= 	'managesections.php';
$div		=	'maindiv';
$form 		= 	"frm1sections";	
//define(IMGPATH,'../images/');
$param		=	$_REQUEST['param'];
$id			=	$_REQUEST['id'];
$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
	$ids	=	explode(",",$delid);
	foreach($ids as $value)
	{
		if($value!='')
		{
			$delcondition =" pksectionid  = '$value' ";
			$AdminDAO->deleterows('system_section',$delcondition,1);
			//updating screens
			$AdminDAO->updaterow("system_screen",array('fksectionid'),array(0),"fksectionid='$value'");
		}
	}
}
$query 		= 	"SELECT
					pksectionid,
					sectionname,
					IF(status=1,'Active','Inactive') status
				FROM
					system_section
				WHERE
					1
				";
$navbtn	=	"";
$sortorder	=	"pksectionid DESC"; // takes field name and field order e.g. brandname DESC
if(in_array('93',$actions))
{
//	print"Hello";
	$navbtn .= "<a class='gbtn btn btn-mini btn-success' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addsection.php','sugrid','$div','','$formtype')\" title='Add Section'>
				<i class='fa fa-plus'></i><span class='bold'>New</span>
			</a>&nbsp;";
}
if(in_array('94',$actions))
{
	$navbtn .="	<a class='gbtn btn btn-mini btn-info' id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addsection.php','sugrid','$div','','$formtype') title=\"Edit Section\">
	
	<i class='fa fa-pencil-square-o'></i><span class='bold'>Edit</span>
	</a>&nbsp;";
}
if(in_array('95',$actions))
{
	$navbtn .="<a class='gbtn btn btn-mini btn-danger' id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Sections\">
	<i class='fa fa-trash-o'></i><span class='bold'>Delete</span>
	</a>&nbsp;";
}
if(in_array('105',$actions))
{
	$navbtn .=" | <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'attachscreen.php','sugrid','$div','','$formtype') title=\"Attach Screens\"><b>Attach Screens</b></a>&nbsp;";
}
?>
<div id="sugrid"></div>
<div id="<?php echo $div;?>">
<div class="col-lg-12" style="">
<?php
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
</div>