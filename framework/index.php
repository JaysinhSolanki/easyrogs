<?php
session_start();
//print_r($_SESSION);
//exit;
if(!empty($_SESSION['addressbookid']))
{
	header("Location: ../application/index.php");
}
else
{
	header("Location: userlogin.php");
}
exit;