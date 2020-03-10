<?php
include_once("../includes/classes/adminsecurity.php");
exit;
$file = fopen("sub4.csv","r");
$fklevelid				=	 6;
while(! feof($file))
{
	$rows = fgetcsv($file);
	$alarmcode						=	 $rows[0];
	$alarmname						=	 $rows[1];
	$description					=	 $rows[2];
	$assessment						=	 $rows[3];
	$details_comment				=	 $rows[4];
	$sourceofinformationname		=	 $rows[5];
	$responsiblefordatacollection	=	 $rows[6];
	$responsibleforvalidation		=	 $rows[8];
	$category						=	 $rows[9];
	$updatefrequency				=	 $rows[10];
	$updatecriteria					=	 $rows[11];
	$startingdate					=	 $rows[12];
	$dateforachievement				=	 $rows[13];
	$unit							=	 $rows[14];
	$baseline						=	 $rows[15];
	$target							=	 $rows[16];
	$colorassignmentmode			=	 $rows[17];
	$supergreen_a					=	 $rows[18];
	$green_b						=	 $rows[19];
	$yellow_c						=	 $rows[20];
	$red							=	 $rows[21];
	$yellow_e						=	 $rows[22];
	$green_d						=	 $rows[23];
	$red_f							=	 $rows[24];	
	/***********************************************************************************************************************************************************************/
	$alarms							=	$AdminDAO->getrows("tblalarm","*","alarmcode = '$alarmcode'");
	if(count($alarms) > 0)
	{
		$fkalarmid	=	$alarms[0]['pkalarmid'];
	}
	else
	{
		$units				=	$AdminDAO->getrows("tblunit","*","unitname = '$unit'");
		if(count($units) > 0)
		{
			$fkunitid		=	$units[0]['pkunitid'];
		}
		else
		{
			$unitfields		=	array('unitname');
			$unitvalues		=	array($unit);
			//if($unit != "")
			//{
				$fkunitid		=	$AdminDAO->insertrow("tblunit",$unitfields,$unitvalues);	
			//}
		}
		$addressfields		=	array('alarmname','alarmcode','fkunitid');
		$records			=	array($alarmname,$alarmcode,$fkunitid);
		$fkalarmid			=	$AdminDAO->insertrow("tblalarm",$addressfields,$records);	
	}
	/************************************************************************************************************************************************************************/
	$responsiblefordatacollections		=	$AdminDAO->getrows("system_groups","*","groupname = '$responsiblefordatacollection'");
	if(count($responsiblefordatacollections) > 0)
	{
		$responsiblefordatacollection	=	$responsiblefordatacollections[0]['pkgroupid'];
	}
	else
	{
		$responsiblefordatacollectionfields					=	array('groupname');
		$responsiblefordatacollectionvalues					=	array($responsiblefordatacollection);
		//if($responsiblefordatacollection != "")
		//{
			$responsiblefordatacollection						=	$AdminDAO->insertrow("system_groups",$responsiblefordatacollectionfields,$responsiblefordatacollectionvalues);	
		//}
	}
	
	/************************************************************************************************************************************************************************/
	$responsibleforvalidations			=	$AdminDAO->getrows("system_groups","*","groupname = '$responsibleforvalidation'");
	if(count($responsibleforvalidations) > 0)
	{
		$responsibleforvalidation		=	$responsibleforvalidations[0]['pkgroupid'];
	}
	else
	{
		$responsibleforvalidationfields					=	array('groupname');
		$responsibleforvalidationvalues					=	array($responsibleforvalidation);
		//if($responsibleforvalidation != "")
		//{
			$responsibleforvalidation						=	$AdminDAO->insertrow("system_groups",$responsibleforvalidationfields,$responsibleforvalidationvalues);	
		//}
	}
	
	/************************************************************************************************************************************************************************/
	if($category == 'PROGRESS')
	{
		$category		=	'2';
	}
	elseif($category == 'STATUS')
	{
		$category		=	'1';
	}
	/************************************************************************************************************************************************************************/
	if($updatefrequency == 'weekly')
	{
		$fkupdatefrequencyid		=	'1';
	}
	elseif($updatefrequency == 'monthly')
	{
		$fkupdatefrequencyid		=	'2';
	}
	elseif($updatefrequency == 'quarterly')
	{
		$fkupdatefrequencyid		=	'3';
	}
	elseif($updatefrequency == 'yearly')
	{
		$fkupdatefrequencyid		=	'4';
	}
	/************************************************************************************************************************************************************************/
	$updatecriterias			=	$AdminDAO->getrows("tblupdatecriteria","*","updatecriterianame = '$updatecriteria'");
	if(count($updatecriterias) > 0)
	{
		$fkupdatecriteriaid		=	$updatecriterias[0]['pkupdatecriteriaid'];
	}
	else
	{
		$updatecriteriafields					=	array('updatecriterianame');
		$updatecriteriavalues					=	array($updatecriteria);
		//if($updatecriteria != "")
		//{
			$fkupdatecriteriaid						=	$AdminDAO->insertrow("tblupdatecriteria",$updatecriteriafields,$updatecriteriavalues);	
		//}
	}
	/************************************************************************************************************************************************************************/
	$sourceofinformations			=	$AdminDAO->getrows("tblalarmsourceofinformation","*","sourceofinformationname = '$sourceofinformationname'");
	if(count($sourceofinformations) > 0)
	{
		$fkalarmsourceofinformationid		=	$updatecriterias[0]['pkalarmsourceofinformationid'];
	}
	else
	{
		$sourceofinformationnamefields					=	array('sourceofinformationname');
		$sourceofinformationnamevalues					=	array($sourceofinformationname);
		//if($sourceofinformationname != "")
		//{
			$fkalarmsourceofinformationid					=	$AdminDAO->insertrow("tblalarmsourceofinformation",$sourceofinformationnamefields,$sourceofinformationnamevalues);	
		//}
	}
	/************************************************************************************************************************************************************************/
	if($colorassignmentmode == 'Direct')
	{
		$colorassignmentmode		=	'2';
	}
	elseif($colorassignmentmode == 'Inverse')
	{
		$colorassignmentmode		=	'1';
	}
	
	
	$fields1 = array('description','assessment','details_comment','responsiblefordatacollection','responsibleforvalidation','category','fkupdatefrequencyid','fkupdatecriteriaid','startingdate','dateforachievement','baseline','target','fkalarmsourceofinformationid','colorassignmentmode','fklevelid','fkalarmid','supergreen_a','green_b','yellow_c','red','yellow_e','green_d','red_f');
	$records1	= array($description,$assessment,$details_comment,$responsiblefordatacollection,$responsibleforvalidation,$category,$fkupdatefrequencyid,$fkupdatecriteriaid,$startingdate,$dateforachievement,$baseline,$target,$fkalarmsourceofinformationid,$colorassignmentmode,$fklevelid,$fkalarmid,$supergreen_a,$green_b,$yellow_c,$red,$yellow_e,$green_d,$red_f);
	$alarmlevelid			=		$AdminDAO->insertrow("tblalarmlevel",$fields1,$records1);
}
	fclose($file);
?>