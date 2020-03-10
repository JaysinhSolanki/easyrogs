<?php
require_once("adminsecurity.php");
require_once("dbgrid.php");
if(isset($_GET['pkscreenid']))
{
	$_SESSION['screenid']	=	$_GET['pkscreenid'];
	$screenid				=	$_SESSION['screenid'];
}
else
{
	$screenid =	$_SESSION['screenid'];
}  
//echo $screenid."is screen id<br>";



//dump($_SESSION);

$rights	 	=	$userSecurity->getRights($screenid);
//dump($rights);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//dump($fields);
$stractions	=	@implode(',',$actions);
//define(IMGPATH,'../images/');
if(empty($div))
{
	$div		=	'maindiv';
}
$form 				= 	"frm_".$screenid;
//$AdminDAO->displayquery =1;
$screendata			=	$AdminDAO->getrows("system_screen,system_section","*","pkscreenid = :screenid AND pksectionid = fksectionid", array(":screenid"=> $screenid));
$fieldfilters		=	$AdminDAO->getrows("system_filter","*","fkscreenid = :screenid AND filterstatus=:filterstatus", array(":screenid"=> $screenid,":filterstatus"=> 1 )," ORDER BY filtersortorder");
//dump($screendata);
$screenname			=	$screendata[0]['screenname'];
$issystemsection	=	$screendata[0]['issystemsection'];
$formurl			=	$screendata[0]['formurl'];

if($issystemsection == 1)
{
	$formurl	=	$_SESSION['framework_url'].$formurl;
}
//echo "<br>";
//echo $formurl;
if(!is_numeric($screenid))
{
	echo "Sorry but there is some ERROR is SCREEN IDs";
	exit;
}
$dest				=	$_SESSION['framework_url']."main.php?pkscreenid=$screenid";//$screendata[0]['url'];
//$formurl			=	$screendata[0]['formurl'];
$session_adbookid	=	$_SESSION['addressbookid']; 
$sectiondata		=	$AdminDAO->getrows("system_section, system_screen","*"," pksectionid = fksectionid AND pkscreenid = :screenid", array(":screenid"=>$screenid));
$sectionname		=	$sectiondata[0]['sectionname'];
$query				=	stripslashes($screendata[0]['query']);
$id					=	$_GET['id'];
$query				=	str_replace("~addressbookid~", "$addressbookid", $query);
$query				=	str_replace("~case_id~", "$id", $query);
//echo $query;
//exit;
//}
if($_GET['order']=="")
{
	$orderby	=	stripslashes($screendata[0]['orderby']);
}
$deletefilename		=	$screendata[0]['deletefilename'];
if(@sizeof($actions) > 0)
{
	
	$actionsdata	=	$AdminDAO->getrows("system_action,system_actiontype","*","pkactiontypeid = fkactiontypeid AND fkscreenid = :screenid AND pkactionid IN($stractions)", array(":screenid" => $screenid),'sortorder','asc');
	//dump($actionsdata);
	if(strpos($formurl,"?")!==false)
	{
		$formurl.="&pid=$id";
	}
	else
	{
		$formurl.="?pid=$id";
	}
	
	if(strpos($dest,"?")!==false)
	{
		$dest.="&pid=$id";
	}
	else
	{
		$dest.="?pid=$id";
	}
	$actionmarkers			=	array("~dest~","~_SESSION[qs]~","~form~","~formurl~","~div~");
	$actionmreplacements	=	array($dest,$_SESSION['qs'],$form,$formurl,$div);
	
	
	
	//dump($actionmreplacements);
	
	$actiontypesarr	=	array();
	foreach($actionsdata as $ad)
	{
		$phpfile	=	$ad['phpfile'];
			
		if(strpos($phpfile,"?")!==false)
		{
			$phpfile.="&pid=$id";
		}
		else
		{
			$phpfile.="?pid=$id";
		}
		if(in_array($ad['pkactionid'],$actions))
		{
			$selection	=	$ad['selection'];
			
			$childdiv	=	$ad['childdiv'];
			$actionparam=	$ad['actionparam'];
			$title		=	$ad['title'];
			$iconclass		=	$ad['iconclass'];
			if($ad['fkactiontypeid']==4)
			{
				$actioncode	=		"<a 
										href=\"javascript:showpage($selection,document.{$form}.checks,'$phpfile','{$childdiv}','$div','{$actionparam}')\" title='{$title}' class='gbtn btn btn-mini btn-primary }'>
										<i class=' {$iconclass}'></i><span class='bold'>&nbsp;{$title}</span></a>";//$ad['actioncodecustom'];
				
				
				 $navbtn .= $actioncode;// str_replace($actionmarkers,$actionmreplacements,$actioncode);
			}
			else if($ad['fkactiontypeid']==5)
			{
				//echo "<br>--------Hello----<br>";
				//$navbtn .=  str_replace($actionmarkers,$actionmreplacements,$ad['actioncodecustom']);
				//updaterecords(page,div,qs,param)
				//dump($ad);
				if(@sizeof($ad) >0 )
				{
					
					
					
					
					
				$actioncode	=		"<a 			href=\"javascript:showpage(
				$selection,
				document.{$form}.checks,
				'$phpfile',
				'$childdiv',
				'$div',
				'$actionparam'
				
				)\" title='$title' class='gbtn btn btn-mini btn-primary }'><i class=' $iconclass'></i><span class='bold'>&nbsp;$title</span></a>";//$ad['actioncodecustom'];
				
				
				 $navbtn .= $actioncode;
					
				}
			}
			else
			{
				$actiontypesarr[] =	$ad['fkactiontypeid'];
				$navbtn .=  str_replace($actionmarkers,$actionmreplacements,$ad['actioncode']);
			}
		}
	}
}
$systemfilters	=	'';
if(sizeof($fieldfilters) >0)
{
	//dump($filters);
	foreach($fieldfilters as $fieldfilter)
	{
		$filterlabelname	=	$fieldfilter['filterlabelname'];
		$pkformfieldtypeid	=	$fieldfilter['pkformfieldtypeid'];
		$filterlabel		=	$fieldfilter['filterlabel'];
		$filtervalue		=	$fieldfilter['filtervalue'];
		$filterselect		=	$fieldfilter['filterselect'];
		$filterquery		=	$fieldfilter['filterquery'];
		$filterfields		=	$AdminDAO->queryresult($filterquery);
		$selectclass		=	'';
		if($filterselect == '2')
		{
			$selectclass = "multiple='multiple'  class='form-control js-select2'";
		}
		else
		{
			$selectclass = "class='form-control'";
		}
		//dump($filterfields);
		$systemfilters	.="<select $selectclass style='width:160px; height:33px;' name='' id='' ><option value='0'>Select ".$filterlabelname."</option>";
		foreach($filterfields as $filterfield)
		{
			$value	=	$filterfield["$filtervalue"];
			$label	=	$filterfield["$filterlabel"];
         	$systemfilters	.=	"<option value='".$value."'>".$label."</option>";
		}
		$systemfilters	.="</select>";
	}
}
$i=0;
$filterbtn	= '';
if(sizeof($fieldfilters) >0)
{
//$filterbtn	=	'<button class="btn btn-warning btn-mini" type="button" onclick=""><i class="fa fa-search"></i><span class="bold">Filter</span></button>';
}
//$rangeslider	=	'<input id="ex2" type="text" class="span2 slider" value="" data-slider-min="10" data-slider-max="1000" data-slider-step="5" data-slider-value="[250,450]"/>';
$navbtn		=	$navbtn.$filterbtn.$systemfilters.$rangeslider;
//echo $navbtn;
