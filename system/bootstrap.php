<?php
  // 3rd Party
  require_once(__DIR__ . '/../vendor/autoload.php');

  // settings
  $dotEnv = new Dotenv\Dotenv(__DIR__ . '/..');
  $dotEnv->load();
  require_once(__DIR__ . "/settings.php");
  
  // logging
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set("log_errors", 1);
  ini_set("error_log", LOGS_DIR . "/errors.log");
  
  require_once __DIR__ . '/library/classes/logger.class.php';
  $logger = new Logger();
  //$logger->info("Logging is available now..");

  // lib
  require_once __DIR__ . '/library/classes/httpresponse.php';
  require_once __DIR__ . '/library/classes/AdminDAO.php';
  require_once __DIR__ . '/library/helper.php';  

  // templates
  $smarty = new Smarty();
  $smarty->template_dir = __DIR__ . '/templates';
  $smarty->compile_dir  = __DIR__ . '/../tmp/templates_c';

  // models
  require_once(__DIR__ . '/models/index.php');

  // mailing
  require_once __DIR__ . '/mailers/index.php';

  // globals (LEGACY)
  date_default_timezone_set("America/Los_Angeles");
  $dateformate				    =	'n/j/Y'; 
  $systemmaintitle			  =	"EasyRogs";
  $systemmaindescription	=	"EasyRogs is an electronic discovery system. It allows attorneys and their support staff to create and Serve Discovery instantly, easily, and inexpensively. No paper, toner, envelopes, or postage. It also allows attorneys and their clients to collaborate on Discovery Responses. ";
  $screensnotincludes			=	"";
  $AdminDAO 	            = new AdminDAO;
  
  // jobs
  $queuePersistor = new Qutee\Persistor\Pdo();
  $queuePersistor->setOptions([
    'dsn'        => "mysql:dbname=" . DBNAME . ";host=" . DBHOST,
    'username'   => DBUSER,
    'password'   => DBPASS,
    'table_name' => 'jobs_queue'
  ]);
  $jobsQueue = new Qutee\Queue();
  $jobsQueue->setPersistor($queuePersistor);
  
  // session
  @session_start();
  @set_time_limit(0);
  ini_set('session.bug_compat_warn', 0);
  ini_set('session.bug_compat_42', 0);
  ini_set('max_input_vars', 25000);
  
  $_SESSION['upload_url']		  =	UPLOAD_URL;
  $_SESSION['admin_url']		  =	DOMAIN;
  $_SESSION['admin_path']		  =	"{$_SESSION['system_path']}application/"; 
  $_SESSION['system_path']	  =	SYSTEMPATH;
  $_SESSION['library_path']	  =	"{$_SESSION['system_path']}library/";
  $_SESSION['framework_path']	=	FRAMEWORK_PATH;
  $_SESSION['framework_url']	=	FRAMEWORK_URL;

  require_once(__DIR__ . '/library/classes/sessionuser.php');
  $currentUser = isset($_SESSION['addressbookid']) 
                 ? new SessionUser($_SESSION['addressbookid']) 
                 : null;