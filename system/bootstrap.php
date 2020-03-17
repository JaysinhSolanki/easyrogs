<?php
  // 3rd Party
  require_once(__DIR__ . '/../vendor/autoload.php');

  // settings
  $dotEnv = new Dotenv\Dotenv(__DIR__ . '/..');
  $dotEnv->load();
  require_once(__DIR__ . "/settings.php");
  
  // globals
  date_default_timezone_set("America/Los_Angeles");
  $dateformate				    =	'n/j/Y'; 
  $systemmaintitle			  =	"EasyRogs";
  $systemmaindescription	=	"EasyRogs is an electronic discovery system. It allows attorneys and their support staff to create and Serve Discovery instantly, easily, and inexpensively. No paper, toner, envelopes, or postage. It also allows attorneys and their clients to collaborate on Discovery Responses. ";
  $screensnotincludes			=	"";

  // Logging
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set("log_errors", 1);
  ini_set("error_log", LOGS_DIR . "/errors.log");

  // session
  @session_start();
  @set_time_limit(0);
  ini_set('session.bug_compat_warn', 0);
  ini_set('session.bug_compat_42', 0);
  ini_set('max_input_vars', 25000);
  $_SESSION['upload_url']		=	UPLOAD_URL;
  $_SESSION['admin_url']		=	DOMAIN;
  $_SESSION['admin_path']		=	"{$_SESSION['system_path']}application/"; 
  $_SESSION['system_path']	=	SYSTEMPATH;
  
  $_SESSION['library_path']	=	"{$_SESSION['system_path']}library/";
  $_SESSION['framework_path']	=	FRAMEWORK_PATH;
  $_SESSION['framework_url']	=	FRAMEWORK_URL;