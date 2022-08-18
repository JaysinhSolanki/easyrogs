<?php
 
define("DBHOST", 'localhost');
define("DBUSER", 'root');
define("DBPASS", '');
define("DBNAME", 'easyrogs_prod');

define("ROOTURL", 'http://localhost/easyrogs/');
define("ROOTPATH",'C:/xampp/htdocs/easyrogs/'); 

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
define('STRIPE_API_KEY',              'sk_live_TzHCSxDGfOZaabcvshK93jpo00MUHeebpt');
define('STRIPE_PUBLISHABLE_KEY',      'pk_live_pX6vFTs3fFjjqkZ8lY5IggWl001I1qlWh3');
define('STRIPE_WEBHOOK_SIGNING_SECRET', 'whsec_xljT2SocEC1QsfNGYBGQQE20K3Whrg4R');

define("SECRET_KEY", 'KGHI&^JH767hug78&hgjhjas7u&5HN65hjJHI-JKYUTUHGHNGHJ%6gh.fy6565tghghjfg545337678hjfgFG');

define('SIGNUP_CREDITS', 3);
define('SERVE_DISCOVERY_COST', 1000); // $10
define('PAYMENTS_CURRENCY',    'usd');
define('PAYMENT_WHITELIST', [] ); // add email regular expressions here, for users that do not require payment

define( "APP_GOOGLE_ANALYTICS_ID", 'UA-168067186-1' );
define( 'ANALYTICS_DISABLED', false);
define( 'PAY_DISABLED', false );
define( 'SMARTSUPP_DISABLED', false);
