<?php
$screenname	=	" Actions";
require_once("adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
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
				$delcondition =" pkactionid  = '$value' ";
				$AdminDAO->deleterows('action',$delcondition,1);
			}
		}
		$delid	=	$_GET['param'];
}
/************* DUMMY SET ***************/
$labels = array("ID","Label");
$fields = array("pkactionid","actionlabel");
$dest 	= 	'manageactions.php';
$div	=	'sugrid';
$form 	= 	"actionsfrm";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkactionid,
				actionlabel,
				actioncodecustom
			FROM
				system_action
			WHERE
				fkscreenid='$delid'
			";
$navbtn	=	"";
$navbtn .= "<a class='btn btn-mini btn-success' id='addactions' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addaction.php','subgrid','sugrid','$delid')\" title='Add Action'>
				<i class='icon-plus bigger-110'></i>
				</a>&nbsp;";
$navbtn .="	<a class=\"btn btn-mini btn-info\" id=\"editactions\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addaction.php','subgrid','sugrid','$delid') title=\"Edit Action\"><i class='icon-edit bigger-110'></i></a>&nbsp;";
$navbtn .="	<a class=\"btn btn-mini btn-danger\" id=deleteactions onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$delid') title=\"Delete Actions\"><i class='icon-trash bigger-110'></i></a>&nbsp;";
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
</div>