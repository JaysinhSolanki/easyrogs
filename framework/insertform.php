<?php
include_once("adminsecurity.php");
include_once("../includes/classes/login.class.php");
$Login		=	new Login($AdminDAO);
$id = $_REQUEST['id'];
//dump($_POST,1);
//dump($_POST);
//exit;  
	 
if(sizeof($_POST)>0)
{
	$formtitle 			= 	filter($_POST['formtitle']);
	$formtitlehebrew 	= 	""; //filter($_POST['formtitlehebrew']);
	$fieldlabel 		= 	$_POST['fieldlabel'];
	$sortorder			= 	$_POST['sortorder'];
	$pklabelid			=	$_POST['pklabelid'];
	$fieldlabelhebrew 	= 	$_POST['fieldlabelhebrew'];
	$language			=	$_SESSION['language'];
	$fieldnamearr		=	$_POST['fieldname'];
	
	
	/*if($language=='english')
	{
		$fklanguageid	=	1;
	}else
	{
		$fklanguageid	=	2;
	}*/
	 
	$formurl			=	filter($_POST['formurl']);
	$displayorder		=	filter($_POST['displayorder']);
	$fkmoduleid			=	$_POST['fkmoduleid'];
	$fksectionid		=	$_POST['fksectionid'];
	$visibility			=	$_POST['visibility'];
	$showtoadmin		=	$_POST['showtoadmin'];
	$showontop			=	$_POST['showontop'];
	$query				=	addslashes($_POST['query']);
	$deletefilename	=	filter($_POST['deletefilename']);
	
	if($formtitle=='')
	{
		msg(48,2);
	}
	if($formtitlehebrew=='')
	{
		msg(49,2);
	}
	/*if($filename=='')
	{
		$msg	.=	"<li>File Name can not be left blank</li>";
	}
	if($formurl=='')
	{
		$msg	.=	"<li>Form URL can not be left blank</li>";
	}
	if($query=='')
	{
		$msg	.=	"<li>SQL can not be left blank</li>";
	}
	if($filename=='')
	{
		$msg	.=	"<li>File Name can not be left blank</li>";
	}
	if($fkmoduleid=='')
	{
		$msg	.=	"<li>Please select a module</li>";
	}
	if($visibility=='')
	{
		$msg	.=	"<li>Please select visibility</li>";
	}
	if($displayorder=='')
	{
		$msg	.=	"<li>Please enter display order</li>";
	}
	
	 */
	/*******************************screen fields****************
	for($screenfiled=0; $screenfield<sizeof($fieldname); $screenfield++)
	{
		//$fieldname 		= 	filter($_POST['fieldname']);
		//$fieldlabel	 	= 	filter($_POST['fieldlabel']);
		//$sortorder		=	filter($_POST['sortorder']);
		
		if($fieldname[$screenfiled]=='')
		{
			$msg	.=	"<li>Field Name can not be left blank</li>";
		}
		if($fieldlabel[$screenfiled]=='')
		{
			$msg	.=	"<li>Field Label can not be left blank</li>";
		}
		if($sortorder[$screenfiled]=='')
		{
			$msg	.=	"<li>Field Sort Order can not be left blank</li>";
		}
	}
	
	**************************form actions**********************/
	
	//exit;
	//for($screenaction=0; $screenaction < sizeof($actionlabel); $screenaction++)
	//{ 
		//$actionlabel 		= 	filter($_POST['actionlabel']);
		//$fkactiontypeid	= 	filter($_POST['fkactiontypeid']);
		//$actionsortorder	= 	filter($_POST['actionsortorder']);
		/*if($fkactiontypeid[$screenaction]==4)
		{
			if($actioncodecustom[$screenaction]=="")
			{
				$msg	.=	"<li>Custom Query can not be left blank</li>";
			}
		}*/
		/*if($actionsortorder[$screenaction]=='')
		{
			$msg	.=	"<li>Action Sort Order can not be left blank</li>";
		}*/
	//}
	/***************************************************************/
	//exit;
	/*if($formtitle)
	{
		$unique = $AdminDAO->isunique('system_label', 'pklabelid', $id, 'screenname', $screenname);
		if($unique=='1')
		{
				echo"Screen with this name <b><u>$screenname</u></b> already exists. Please choose another name.";	
				exit;
		}
	}*/
	
	//To add  Form title
	
	$fields = array('formtitle','formtitlehebrew');
	$values = array($formtitle, $formtitlehebrew);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("system_form",$fields,$values," pkformid='$id' ");
	}
	else
	{
		// this is the add system_form	
		$id = $AdminDAO->insertrow("system_form",$fields,$values);
	}//end of else
	
	/**************************************************************************************************************/
	
	//$AdminDAO->displayquery	=	1;
	/*$fields = array('fklanguageid','fkformid','label','labelhebrew');
	$values = array($fklanguageid, $fkformid,$label ,$labelhebrew);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("system_label",$fields,$values," pklabelid='$id' ");
	}
	else
	{
		$id = $AdminDAO->insertrow("system_label",$fields,$values);
	}//end of else*/
	
	/*$screenrows			=	$AdminDAO->getrows("system_groupscreen","pkgroupscreenid", "fkgroupid='3' AND fkscreenid = '$id'");
	$pkgroupscreenid	=	$screenrows[0]['pkgroupscreenid'];
	if(!$pkgroupscreenid)
	{
		 $AdminDAO->insertrow("system_groupscreen",array('fkgroupid','fkscreenid'),array(3,$id));
	}*/
	
	/*********************insert screen fields*****************************/
	for($screenfield=0; $screenfield<sizeof($fieldlabel); $screenfield++)
	{
		if($fieldlabel[$screenfield]!="")
		{
			$fieldnamelover	=	strtolower($fieldnamearr[$screenfield]);
			$fieldname		=	preg_replace('/\s+/', '', $fieldnamelover);
			$fields 		=	array('fkformid','fieldname','label','labelhebrew','sortorder');
			$values 		=	array($id,$fieldname,$fieldlabel[$screenfield] ,$fieldlabelhebrew[$screenfield],$sortorder[$screenfield]);
			$pklabelids		=	$pklabelid[$screenfield];
			if($pklabelids)//updates records 
			{
				//$postedfields[]	=	$pkfieldid[$screenfield];
				//$AdminDAO->updaterow("system_field",$fields,$values," pkfieldid='$pkfieldids' ");
				$AdminDAO->updaterow("system_label",$fields,$values," pklabelid='$pklabelids' ");
			}
			else
			{
				// this is the add section	
				//$postedfields[] = $AdminDAO->insertrow("system_field",$fields,$values);
				 $AdminDAO->insertrow("system_label",$fields,$values);				
			}//end of else
		}
	}
	
	/*******************insert actions*********************************/
	//dump($fkactiontypeid);
	//for($screenaction=0; $screenaction<sizeof($actionlabel); $screenaction++)
	/*{
		if($actionlabel[$screenaction]!="")
		{
			$actioncodecustom	=	"";
			$selection			=	$selectioncustom[$screenaction];
			$phpfile			=	$phpfilecustom[$screenaction];
			$title				=	$titlecustom[$screenaction];
			$childdiv			=	$childdivcustom[$screenaction];
			if(!$childdiv)
			{
				$childdiv		=	"sugrid";
			}
			$buttonclass		=	$buttonclasscustom[$screenaction];
			$iconclass			=	$iconclasscustom[$screenaction];
			$actionparam		=	$actionparamcustom[$screenaction];
			
			$fields				=	array('actionlabel','acttionlabelherbew','fkactiontypeid','actioncodecustom','fkscreenid','sortorder','selection','phpfile','title','childdiv','buttonclass','iconclass','actionparam');
			if(($phpfile!="")&&($title!="")&&($childdiv!="")&&($buttonclass!="")&&($iconclass!=""))
			{
				$actioncodecustom	=	"<a href='javascript:showpage($selection,document.~form~.checks,'$phpfile','$childdiv','~div~','$actionparam')' title='$title' class='btn btn-mini $buttonclass'><i class='bigger-110 $iconclass'></i></a>";
				$actioncodecustom	=	filter(($actioncodecustom));
			}
			$values				=	array($actionlabel[$screenaction], $acttionlabelherbew[$screenaction],$fkactiontypeid[$screenaction],$actioncodecustom, $id, $actionsortorder[$screenaction], $selection, $phpfile, $title, $childdiv, $buttonclass, $iconclass, $actionparam);
			$pkactionids		=	$pkactionid[$screenaction];
			//dump($values);
			if($pkactionids)//updates records 
			{
				$postedactions[]	=	$pkactionid[$screenaction];
				$AdminDAO->updaterow("system_action",$fields,$values," pkactionid='$pkactionids' ");
			}
			else
			{
				// this is the add section	
				$postedactions[]	=	$AdminDAO->insertrow("system_action",$fields,$values);
			}//end of else
		}
	}*/
	/*******************************************ADD RIGHTS**********************************/
	/*$gfields	=	array('fkgroupid','fkfieldid');
	$gactions	=	array('fkgroupid','fkactionid');
	if(sizeof($postedfields) > 0)
	{
		foreach($postedfields as $fieldz)
		{
			$data	=	array($groupid,$fieldz);
			$AdminDAO->insertrow("system_groupfield",$gfields,$data);
		}
	}
	if(sizeof($postedactions) > 0)
	{
		foreach($postedactions as $actionz)
		{
			$data	=	array($groupid,$actionz);
			$AdminDAO->insertrow("system_groupaction",$gactions,$data);
		}
	}
	*/
	/*****************Refresh Rights for the logged in user******************/
	$userscreens			=	$Login->getscreens($_SESSION['groupid']);//fetching user screens
	$_SESSION['screenids']	=	$userscreens;
	for($s=0;$s<sizeof($userscreens);$s++)
	{
		$userscreenpriviliges					=	$Login->userRights($userscreens[$s],$_SESSION['groupid']);
		$_SESSION['screens'][$userscreens[$s]]	=	$userscreenpriviliges;
	}
	?>
    	<?php /*?><script>
			window.location.reload();
		</script><?php */?>
    <?php
msg(7);
	//header("Location: index.php");
}// end post
?>