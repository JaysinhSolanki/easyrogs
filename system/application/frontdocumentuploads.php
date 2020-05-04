<?php
@session_start();
require_once("../bootstrap.php");
include_once("../library/classes/functions.php");

$rp_uid				=	$_REQUEST['rp_uid'];
$olddocuments		=	$_SESSION['documents'][$rp_uid];
if(sizeof($olddocuments) > 0)
{
	foreach($olddocuments as $data)
	{
		$doc_purpose	=	$data['doc_purpose'];
		$doc_name		=	$data['doc_name'];
		$doc_path		=	$data['doc_path'];
		$status			=	$data['status'];
		if($doc_name != "")
		{
			$documents[$rp_uid][]	=	array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>$status);		
		}
	}
}
else
{
	$documents[$rp_uid] = array();
}
$doc_purpose	=	$_POST['doc_purpose'];
$target_dir 	= 	SYSTEMPATH."uploads/documents/";
$doc_name		=	basename($_FILES["myfile"]["name"]);
$doc_path 		=	$target_dir . $doc_name;
if (move_uploaded_file($_FILES["myfile"]["tmp_name"], $doc_path)) 
{
	$documents[$rp_uid][]	=	array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>0);
} 
$_SESSION['documents']	=	$documents;
/*echo "<pre>";
print_r($_SESSION['documents']);
echo "</pre>";*/
?>

