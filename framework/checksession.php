<?php
@session_start();
error_reporting(E_ALL);
if($_SESSION['addressbookid'] == '')
{
	echo "loggedout";
	exit;
}