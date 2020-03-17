<?php

define("DBHOST", $_ENV['DB_HOST']);
define("DBUSER", $_ENV['DB_USERNAME']);
define("DBPASS", $_ENV['DB_PASSWORD']);
define("DBNAME", $_ENV['DB_NAME' ]);

define("ROOTURL", "http://easyrogs.local/"); # Update this value!
define("ROOTPATH", "/home/projects/easyrogs/");  # Update this value!

define("SYSTEMPATH",	ROOTPATH."system/");
define("DOMAIN", 		ROOTURL."system/application/");
define("ASSETS_URL", 	ROOTURL."system/assets/");
define("VENDOR_URL", 	ROOTURL."system/assets/vendors/");
define("FRAMEWORK_URL", ROOTURL."framework/");
define("FRAMEWORK_PATH",ROOTPATH."framework/");
define("UPLOAD_URL", 	ROOTURL."system/uploads/");
define("LOGS_DIR", __DIR__ . '/../logs');