<?php
require_once("adminsecurity.php");
if($formtitle=="")
{
	msg(236,2);
}
if($formname=="")
{
	msg(237,2);
}
if($formid=="")
{
	msg(238,2);
}
if($formaction=="")
{
	msg(239,2);
}
$formfields	=	array("formtitle","formname","formid","method","formaction","cssclass","enctype","cssinline","javascript","querystring","redirecturl","isajax","gridstyle");			
$formvalues	=	array($formtitle,$formname,$formid,$formmethod,$formaction,$formcssclass,$formenctype,$forminlinecss,$formjs,$formquerystring,$formredirecturl,$isajax,$gridstyle); 

if($pkformid >0)//old form getting updated
{
	$AdminDAO->updaterow("system_form",$formfields,$formvalues,"pkformid=$pkformid");
}
else//new form
{
	$pkformid	=	$AdminDAO->insertrow("system_form",$formfields,$formvalues);
}
//echo "<br>line====> ".__LINE__;
foreach($fieldtype as $key=>$values)
{
	//echo "<br>line====> ".__LINE__;
	$fieldfields		=	array("fkformid","label","fieldname","fieldid","fkformfieldtypeid","cssclass","displayorder","instructions","isrequired","fieldjavascript","fieldplaceholder","style","status","sqlquery","queryfieldvalue","queryfieldlabel","fklisttypeid","recordFrom");
	$fieldvalues		=	array($pkformid,$fieldlabel[$key],$fieldname[$key],$fieldid[$key],$fieldtype[$key],$fieldclass[$key],$fielddisplayorder[$key],$fieldinstruction[$key],$fieldisrequired[$key],$fieldjs[$key],$fieldplaceholder[$key],$fieldstyle[$key],$status[$key],$sqlquery[$key],$queryfieldvalue[$key],$queryfieldlabel[$key],$fklisttypeid[$key],$recordFrom[$key]);
	
	if($pkformfieldid[$key] > 0)//existing fidld
	{
		$pkformfld			=	$pkformfieldid[$key];
		$AdminDAO->deleterows("system_formfieldoption","fkformfieldid=$pkformfld");
		$AdminDAO->updaterow("system_formfield",$fieldfields,$fieldvalues,"pkformfieldid = '$pkformfld'");
		if($fieldtype[$key] == 4 || $fieldtype[$key] == 5 || $fieldtype[$key] == 6)
		{
			foreach($fieldvalueoptions[$key] as $fieldkey=>$fieldvalues)
			{
				$fieldfieldoptionarray		=	array("fieldoptionvalue","fieldoptionlabel","fkformfieldid");
				$fieldvalueoptionarray		=	array($fieldvalueoptions[$key][$fieldkey],$fieldlabeloptions[$key][$fieldkey],$pkformfld);
				$AdminDAO->insertrow("system_formfieldoption",$fieldfieldoptionarray,$fieldvalueoptionarray);
			}
		}	
	}
	else // new field
	{
		$fkformfieldid		=	$AdminDAO->insertrow("system_formfield",$fieldfields,$fieldvalues,"");
		if($fieldtype[$key] == 4 || $fieldtype[$key] == 5 || $fieldtype[$key] == 6)
		{
			foreach($fieldvalueoptions[$key] as $fieldkey=>$fieldvalues)
			{
				$fieldfieldoptionarray		=	array("fieldoptionvalue","fieldoptionlabel","fkformfieldid");
				$fieldvalueoptionarray		=	array($fieldvalueoptions[$key][$fieldkey],$fieldlabeloptions[$key][$fieldkey],$fkformfieldid);
				$AdminDAO->insertrow("system_formfieldoption",$fieldfieldoptionarray,$fieldvalueoptionarray);
			}
		}
		if($fieldtype[$key] == 8 || $fieldtype[$key] == 22)
		{
				$AdminDAO->deleterows("tblfilesetting","fkformfieldid=$fkformfieldid");
				$fieldfieldoptionarray		=	array("fkformfieldid","filetype","filesize","paralleluploads","maxFiles","uploadMultiple");
				$fieldvalueoptionarray		=	array($fkformfieldid,implode(",",$filetype[$key]),$filesize[$key],$paralleluploads[$key],$maxFiles[$key],$uploadMultiple[$key]);
				$AdminDAO->insertrow("tblfilesetting",$fieldfieldoptionarray,$fieldvalueoptionarray);
		}			
	}
}
msg(30);