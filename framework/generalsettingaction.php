<?php
include_once("../includes/classes/adminsecurity.php");
$fromemail		=	$_REQUEST['fromemail'];
if($fromemail == '')
{
	msg(55,2);
}
$target_dir = "../../images/bulkdeal/";
$targetdeal = $target_dir . basename($_FILES["fileToUpload"]["name"]);
if($id != '-1')
{	
	$target_dirr 		= "../../images/clearence/";
	$i	=	1;
	foreach($_FILES['files']['tmp_name'] as $key => $tmp_name )
	{
		if($i == 1)
		{
		
			$messagearray3	=	'';
			$file_name 		=	$_FILES['files']['name'][$key];
			$file_size 		=	$_FILES['files']['size'][$key];
			$file_tmp 		=	$_FILES['files']['tmp_name'][$key];
			$file_type		=	$_FILES['files']['type'][$key];	
			$target_file 	= 	$target_dirr . basename($_FILES["files"]["name"][$key]);
			$ext 			=	pathinfo($file_name, PATHINFO_EXTENSION);
			
			move_uploaded_file($_FILES['files']['tmp_name'][$key], $target_file);
		}
		$i++;
	}
	if($file_name	!=	'')
	{
		$image	=	$file_name;
		
	}
	else
	{
		$image	=	$clearencemainimage;
	}
	
	if($_FILES["fileToUpload"]["name"]	!=	'')
	{
		 $bimage	=	$_FILES["fileToUpload"]["name"];

		  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetdeal)) {

    } 
		
	}
	else
	{
		$bimage	=	$bulkdealimage;
	}
	
	
	$fields				=	array('fromemail','deliveryprice','clearencemainimage','bulkdealimage');
	$values				=	array($fromemail,$deliveryprice,$image,$bimage);	
	$AdminDAO->updaterow("system_setting",$fields,$values," pksettingid	=	'$id' ");
}
msg(7);
?>