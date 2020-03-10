<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include_once("adminsecurity.php");
include_once($_SESSION['system_path']."library/classes/login.class.php");
$Login		=	new Login($AdminDAO);
$id = $_REQUEST['id'];
//dump($_POST,1);
dump($_POST);
//exit;
$postedactions	=	array();
$postedfields	=	array();

if(sizeof($_POST)>0)
{
	
	$screenname 		= 	$_POST['screenname'];
	$screennamehebrew 	=	"";	//$_POST['screennamehebrew']);
	$filename	 		= 	$_POST['filename'];
	$customfilename	 	= 	$_POST['customfilename'];
	$screentype	 		= 	$_POST['screentype'];
	
	if($screentype==2)
	{
		$filename	=	$customfilename;
	}
	$formurl			=	$_POST['formurl'];
	$displayorder		=	$_POST['displayorder'];
	$fkmoduleid			=	$_POST['fkmoduleid'];
	$fksectionid		=	$_POST['fksectionid'];
	$visibility			=	$_POST['visibility'];
	$showtoadmin		=	$_POST['showtoadmin'];
	$showontop			=	$_POST['showontop'];
	$query				=	$_POST['query'];//addslashes($_POST['query']);
	$deletefilename		=	$_POST['deletefilename'];
	$orderby  			=	$_POST['orderby'];
	//$confirmation		=	$_POST['confirmation'];
	
//	$extraparameter  	=	$_POST['extraparameter'];

	$addbutton  		=	$_POST['addbutton'];
	$editbutton  		=	$_POST['editbutton'];
	$deletebutton  		=	$_POST['deletebutton'];

	$addactionid  		=	$_POST['addactionid'];
	$editactionid  		=	$_POST['editactionid'];
	$deleteactionid  	=	$_POST['deleteactionid'];
	
	
	
	
	
	/////////////////////////////////////////////Filter field/////////////////////////////////////////////////////
	$pkfilterid			=	$_POST['pkfilterid'];
	$fkformfieldtypeid	=	$_POST['fkformfieldtypeid'];
	$filterlabelname	=	$_POST['filterlabelname'];
	$filterfieldname	=	$_POST['filterfieldname'];
	$filtervalue		=	$_POST['filtervalue'];
	$filterlabel		=	$_POST['filterlabel'];
	$filterselect		=	$_POST['filterselect'];
	$filterquery		=	$_POST['filterquery'];
	$filterstatus		=	$_POST['filterstatus'];
	$filtersortorder	=	$_POST['filtersortorder'];
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($screentype==0)
	{
		//msg(229,2);
	}
	if($screenname=='')
	{
		msg(37,2);
	}
	if($filename=='')
	{
		msg(38,2);
	}
	if($screentype==1)
	{
		
		if($formurl=='')
		{
			msg(39,2);
		}
		if($query=='')
		{
			msg(40,2);
		}
		if($filename=='')
		{
			msg(41,2);
		}
		if($fkmoduleid=='')
		{
			msg(42,2);
		}
		if($visibility=='')
		{
			msg(43,2);
		}
		if($displayorder=='')
		{
			msg(44,2);
		}
	}//if NOT custom
	
	/***************************************************************/
	if($msg)
	{
		echo $msg;
		exit;
	}
	//exit;
	if($screenname)
	{
		/*$unique = $AdminDAO->isunique('system_screen', 'pkscreenid', $id, 'screenname', $screenname);
		if($unique=='1')
		{
			msg(45,2);
		}*/
		if($id > 0)
		{
			$wherescreen	=	" AND pkscreenid <> $id ";
		}
		//$AdminDAO->displayquery = 1;
		$screens	=	$AdminDAO->getrows("system_screen", "*", " screenname = '$screenname' AND fksectionid = '$fksectionid' $wherescreen ");
		$pkscreenid =	$screens[0]['pkscreenid'];
		if($pkscreenid > 0)
		{
			msg(45,2);
		}
	}
	//$AdminDAO->displayquery	=	1;
	$fields = array('screenname','screentype','screennamehebrew','url','formurl','deletefilename','displayorder','fkmoduleid','fksectionid','visibility','showtoadmin','showontop','query','orderby','tablename','pkid');
	$values = array($screenname,$screentype,$screennamehebrew, $filename,$formurl,$deletefilename,$displayorder,$fkmoduleid,$fksectionid,$visibility,$showtoadmin,$showontop,$query,$orderby,$tablename,$pkid);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("system_screen",$fields,$values," pkscreenid='$id' ");
		$AdminDAO->deleterows('system_groupfield'," fkgroupid='1' AND fkfieldid IN (SELECT pkfieldid FROM system_field WHERE fkscreenid = '$id') ");
		$AdminDAO->deleterows('system_groupaction'," fkgroupid='1' AND fkactionid IN (SELECT pkactionid FROM system_action WHERE fkscreenid = '$id') ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("system_screen",$fields,$values);
	}//end of else
	$screenrows			=	$AdminDAO->getrows("system_groupscreen","pkgroupscreenid", "fkgroupid='1' AND fkscreenid = '$id'");
	$pkgroupscreenid	=	$screenrows[0]['pkgroupscreenid'];
	if(!$pkgroupscreenid)
	{
		 $AdminDAO->insertrow("system_groupscreen",array('fkgroupid','fkscreenid'),array(1,$id));
	}
	
	//$tablename  		=	$_POST['tablename']);
	//$pkid  				=	$_POST['pkid']);
	/*****************************************************Add/Edit/Delete Action Buttons***************************************************************/
	//$addbutton,$editbutton, $deletebutton
	//$addactionid, $editactionid, $deleteactionid
	if($addbutton==1)
	{
		if($addactionid == 0)
		{
			$fields			=	array('actionlabel','fkactiontypeid','fkscreenid','sortorder','selection','phpfile','title','childdiv','buttonclass','iconclass','actionparam');
			$values			=	array("Add Record",1, $id, 1, 0, $formurl, "Add Record", "sugrid", "gbtn btn btn-mini btn-success", "fa fa-plus", $extraparameter);
			$addactionid	=	$AdminDAO->insertrow("system_action",$fields,$values);
		}
	}
	else
	{
		$AdminDAO->deleterows('system_action',"pkactionid = :addactionid", array("addactionid"=>$addactionid));
		
	}
	
	if($editbutton==1)
	{
		if($editactionid == 0)
		{
			$fields				=	array('actionlabel','fkactiontypeid','fkscreenid','sortorder','selection','phpfile','title','childdiv','buttonclass','iconclass','actionparam');
			$values				=	array("Edit Record",2, $id, 2, 1, $deletefilename, "Edit Record", "sugrid", "gbtn btn btn-mini btn-info", "fa fa-pencil-square-o", $extraparameter);
			$editactionid	=	$AdminDAO->insertrow("system_action",$fields,$values);
		}
	}
	else
	{
		$AdminDAO->deleterows('system_action',"pkactionid = :editactionid", array("editactionid"=>$editactionid));
	}
	
	if($deletebutton==1)
	{
		if($deleteactionid == 0)
		{
			$fields			=	array('actionlabel','fkactiontypeid','fkscreenid','sortorder','selection','phpfile','title','childdiv','buttonclass','iconclass','actionparam');
			$values			=	array("Delete Record",3, $id, 3, 1, $formurl, "Delete Record", "sugrid", "gbtn btn btn-mini btn-danger", "fa fa-trash-o", $extraparameter);
			$deleteactionid	=	$AdminDAO->insertrow("system_action",$fields,$values);
		}
	}
	else
	{
		$AdminDAO->deleterows('system_action',"pkactionid = :deleteactionid", array("deleteactionid"=>$deleteactionid));
	}
	/*****************************************************End of Add/Edit/Delete Action Buttons***************************************************************/
	
	/*********************insert screen fields*****************************/
	for($screenfield=0; $screenfield<sizeof($fieldname); $screenfield++)
	{
		if($fieldname[$screenfield]!="")
		{
			$fieldnametrm	=	preg_replace('/\s+/', '', trim($fieldname[$screenfield]));
			$fields 		=	array('fieldname','fieldlabel','fieldlabelherbew','fkscreenid','sortorder','iseditable','dbfieldname');
			$values 		=	array($fieldnametrm, $fieldlabel[$screenfield],$fieldlabelherbew[$screenfield], $id, $sortorder[$screenfield],$iseditable[$screenfield],$dbfieldname[$screenfield]);
			$pkfieldids		=	$pkfieldid[$screenfield];			
			if($pkfieldids)//updates records 
			{
				$postedfields[]	=	$pkfieldid[$screenfield];
				$AdminDAO->updaterow("system_field",$fields,$values," pkfieldid='$pkfieldids' ");
			}
			else
			{
				// this is the add section	
				$postedfields[] = $AdminDAO->insertrow("system_field",$fields,$values);
			}//end of else
		}
	}
	
	/*********************insert screen Filters*****************************/
	//$AdminDAO->displayquery=1;
	$AdminDAO->deleterows('system_filter',"fkscreenid = '$id'");
	foreach($fkformfieldtypeids as $filterkey => $filtervalues)
	{
			$filteredquery	=	addslashes($filterquery[$filterkey]);
			$fields = array('fkformfieldtypeid','filterlabelname','filterfieldname','filtervalue','filterlabel','filterselect','filterquery','filterstatus','filtersortorder','fkscreenid','updatedon','updatedby');
			$values = array($filtervalues,$filterlabelname[$filterkey],$filterfieldname[$filterkey],$filtervalue[$filterkey], $filterlabel[$filterkey],$filterselect[$filterkey],$filteredquery,$filterstatus[$filterkey],$filtersortorder[$filterkey],$id,date("Y-m-d H:i:s"),$_SESSION['addressbookid']);
			//$AdminDAO->displayquery=1;
			if($filtervalues >0)		
			$AdminDAO->insertrow("system_filter",$fields,$values);
			//$AdminDAO->displayquery=0;		
	}
	/*******************insert actions*********************************/
	//dump($fkactiontypeid);
	for($screenaction=0; $screenaction<sizeof($actionlabel); $screenaction++)
	{
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
			$actionconfirmation	=	$confirmation[$screenaction];
			$fields				=	array('actionlabel','fkactiontypeid','actioncodecustom','fkscreenid','sortorder','selection','phpfile','title','childdiv','buttonclass','iconclass','actionparam','confirmation');
			if(($phpfile!="")&&($title!="")&&($childdiv!="")&&($buttonclass!="")&&($iconclass!=""))
			{
				$actioncodecustom	=	"<a href='javascript:showpage($selection,document.~form~.checks,'$phpfile','$childdiv','~div~','$actionparam')' title='$title' class='btn btn-mini $buttonclass'><i class='bigger-110 $iconclass'></i></a>";
				$actioncodecustom	=	$actioncodecustom;
			}
			$values				=	array($actionlabel[$screenaction],$fkactiontypeid[$screenaction],'', $id, $actionsortorder[$screenaction], $selection, $phpfile, $title, $childdiv, $buttonclass, $iconclass, $actionparam,$actionconfirmation);
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
	}
	/*******************************************ADD RIGHTS**********************************/
	$gfields	=	array('fkgroupid','fkfieldid');
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
	
	/*
	 *Assign these actions to the Developer Group ...
	 */
		if($addbutton==1)
		{
			$data	=	array($groupid,$addactionid);
			$AdminDAO->insertrow("system_groupaction",$gactions,$data);
		}
		if($editbutton==1)
		{
			$data	=	array($groupid,$editactionid);
			$AdminDAO->insertrow("system_groupaction",$gactions,$data);
		}
		if($deletebutton==1)
		{
			$data	=	array($groupid,$deleteactionid);
			$AdminDAO->insertrow("system_groupaction",$gactions,$data);
		}
	
	/*****************Refresh Rights for the logged in user******************/
	$userscreens			=	$Login->getscreens($_SESSION['groupid']);//fetching user screens
	$_SESSION['screenids']	=	$userscreens;
	for($s=0;$s<sizeof($userscreens);$s++)
	{
		$userscreenpriviliges	=	$Login->userRights($userscreens[$s],$_SESSION['groupid']);
		$_SESSION['screens'][$userscreens[$s]]	=	$userscreenpriviliges;
	}
	
	
	msg(7);
	//header("Location: index.php");
}// end post
?>