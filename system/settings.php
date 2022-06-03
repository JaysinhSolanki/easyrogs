<?php

define("DBHOST", $_ENV['DB_HOST']);
define("DBUSER", $_ENV['DB_USERNAME']);
define("DBPASS", $_ENV['DB_PASSWORD']);
define("DBNAME", $_ENV['DB_NAME' ]);

define("ROOTURL", "https://www.easyrogs.com/");
define("ROOTPATH", "/home/easyrogs/code/easyrogs/"); 

define("SYSTEMPATH",	ROOTPATH."system/");
define("DOMAIN", 	ROOTURL."system/application/");
define("ASSETS_URL", 	ROOTURL."system/assets/");
define("VENDOR_URL", 	ROOTURL."system/assets/vendors/");
define("FRAMEWORK_URL", ROOTURL."framework/");
define("FRAMEWORK_PATH",ROOTPATH."framework/");
define("UPLOAD_URL", 	ROOTURL."system/uploads/");
define("LOGS_DIR", __DIR__ . '/../logs');
define("DOCS_CACHE_DIR", 	ROOTPATH . "system/docs_cache/");
define("TMP_DIR", ROOTPATH . 'tmp');

// Payments
define('STRIPE_API_KEY',                $_ENV['STRIPE_API_KEY']);
define('STRIPE_PUBLISHABLE_KEY',        $_ENV['STRIPE_PUBLISHABLE_KEY']);
define('STRIPE_WEBHOOK_SIGNING_SECRET', $_ENV['STRIPE_WEBHOOK_SIGNING_SECRET']);

define("SECRET_KEY", $_ENV['SECRET_KEY']);

define('SIGNUP_CREDITS', 3);
define('SERVE_DISCOVERY_COST', 1000); // $10
define('PAYMENTS_CURRENCY',    'usd');
define('PAYMENT_WHITELIST', [] ); // add email regular expressions here, for users that do not require payment

define( "APP_GOOGLE_ANALYTICS_ID", 'UA-168067186-1' );
define( 'ANALYTICS_DISABLED', false);
define( 'PAY_DISABLED', false );
define( 'SMARTSUPP_DISABLED', false);