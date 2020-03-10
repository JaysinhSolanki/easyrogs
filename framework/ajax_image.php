<?php
include_once("adminsecurity.php");
session_start();
$session_id= $_SESSION['addressbookid']; // Session_id
$t_width = 100;	// Maximum thumbnail width
$t_height = 100;	// Maximum thumbnail height
$new_name = "small".$session_id.".jpg"; // Thumbnail image name
$path = "uploads/";
if(isset($_GET['t']) and $_GET['t'] == "ajax")
{
	extract($_GET);
	$ratio = ($t_width/$w); 
	$nw = ceil($w * $ratio);
	$nh = ceil($h * $ratio);
	$nimg = imagecreatetruecolor($nw,$nh);
	$im_src = imagecreatefromjpeg($path.$img);
	imagecopyresampled($nimg,$im_src,0,0,$x1,$y1,$nw,$nh,$w,$h);
	imagejpeg($nimg,$path.$new_name,90);
	mysql_query("UPDATE system_addressbook SET userimage='$new_name' WHERE pkaddressbookid='$session_id'");
	echo $new_name."?".time();
	exit;
}