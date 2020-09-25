<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$loggedin_email = @$_SESSION['loggedin_email'];
if( $loggedin_email != 'jeff@jeffschwartzlaw.com' ) {
	  HttpResponse::unauthorized();
}

class DBx extends BaseModel { }
$db = new DBx();

$kbItems = $kbModel->all();

foreach( $kbItems ?: [] as $item ) {
   $targets = preg_split( '[\s,]', $item['notes'] );
}
