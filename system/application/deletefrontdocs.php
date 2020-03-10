<?php
@session_start();
include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
include_once("../library/helper.php");
$olddocuments		=	$_SESSION['documents'];
/*echo "<pre>";
print_r($_SESSION['documents']);
echo "</pre>";*/
$id		=	$_POST['id'];
$rp_uid = 	$_POST['rp_uid'];
if(sizeof($olddocuments) > 0)
{
	foreach($olddocuments as $values)
	{
		if(sizeof($values) > 0)
		{
			foreach($values as $key => $data)
			{
				$doc_purpose	=	$data['doc_purpose'];
				$doc_name		=	$data['doc_name'];
				$doc_path		=	$data['doc_path'];
				$status			=	$data['status'];
				if($id == $key)
				{
					unlink($doc_path);		
				}
				else
				{
					$documents[$rp_uid][]	=	array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>$status);
				}
			}		
		}
	}
}
else
{
	$documents[$rp_uid][]	=	array();
}
$_SESSION['documents']	=	$documents;