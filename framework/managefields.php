<?php
require_once("adminsecurity.php");
include_once("dbgrid.php");
$screenname	=	" Fields";
//global $AdminDAO,$Component,$userSecurity;
//*************delete************************
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
	$condition="";
	$ids	=	explode(",",$delid);
	foreach($ids as $value)
	{
		if($value!='')
		{
			$delcondition =" pkfieldid  = '$value' ";
			$AdminDAO->deleterows('system_field',$delcondition,1);
		}
	}
	$delid	=	$_GET['param'];
}
/************* DUMMY SET ***************/
$labels = array("ID","Label","Field Name");
$fields = array("pkfieldid","fieldlabel","fieldname");
$dest 	= 	'managefields.php';
$div	=	'sugrid';
$form 	= 	"fieldsfrm";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkfieldid,
				fieldlabel,
				fieldname
			FROM
				system_field
			WHERE
				fkscreenid='$delid'
			";
$navbtn	=	"";
$navbtn .= "<a class='btn btn-mini btn-success' id='addfields' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addfield.php','subgrid','sugrid','$delid')\" title='Add Field'>
				<i class=\"icon-plus bigger-110\"></i>
				</a>&nbsp;";
$navbtn .="	<a class='btn btn-mini btn-info' id=\"editfields\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addfield.php','subgrid','sugrid','$delid') title=\"Edit Field\"><i class=\"icon-edit bigger-110\"></i></a>&nbsp;";
$navbtn .="	<a class='btn btn-mini btn-danger' id=deletenotes onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$delid') title=\"Delete Fields\"><i class=\"icon-trash bigger-110\"></i></a>&nbsp;";
//$navbtn	=	"";
// if
/********** END DUMMY SET ***************/
?>
</head>
<div id="subgrid"></div>
<div id="<?php echo $div;?>">

<?php 
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
