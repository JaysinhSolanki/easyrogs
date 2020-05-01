<?php
@session_start();
error_reporting(E_ALL);
if( !isset($_SESSION) || empty($_SESSION['addressbookid']) )
{
	echo "loggedout";
	exit;
}
