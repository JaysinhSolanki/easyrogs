<?php
@session_start();
@set_time_limit(0);
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);
ini_set('max_input_vars', 25000);

date_default_timezone_set("America/Los_Angeles");
$dateformate				=	'n/j/Y'; 
$systemmaintitle			=	"EasyRogs";
$systemmaindescription		=	"EasyRogs is an electronic discovery system. It allows attorneys and their support staff to create and Serve Discovery instantly, easily, and inexpensively. No paper, toner, envelopes, or postage. It also allows attorneys and their clients to collaborate on Discovery Responses. ";
$screensnotincludes			=	"";

error_reporting(0); 
define("DBHOST", "localhost");
define("DBUSER", "easyrogs");
define("DBPASS", "");
define("DBNAME", "easyrogs");


$project_folder_name	=	"system";
define("ROOTURL", "https://www.easyrogs.com/");


define("ROOTPATH", "/home/easyrogs/public_html/"); 


define("SYSTEMPATH",	ROOTPATH."{$project_folder_name}/");
define("DOMAIN", 		ROOTURL."{$project_folder_name}/application/");
define("ASSETS_URL", 	ROOTURL."{$project_folder_name}/assets/");
define("VENDOR_URL", 	ROOTURL."{$project_folder_name}/assets/vendors/");
define("FRAMEWORK_URL", ROOTURL."framework/");
define("FRAMEWORK_PATH",ROOTPATH."framework/");
define("UPLOAD_URL", 	ROOTURL."{$project_folder_name}/uploads/");

$_SESSION['upload_url']		=	UPLOAD_URL;
$_SESSION['admin_url']		=	DOMAIN;
$_SESSION['admin_path']		=	"{$_SESSION['system_path']}application/"; 
$_SESSION['system_path']	=	SYSTEMPATH;

$_SESSION['library_path']	=	"{$_SESSION['system_path']}library/";
$_SESSION['framework_path']	=	FRAMEWORK_PATH;
$_SESSION['framework_url']	=	FRAMEWORK_URL;
