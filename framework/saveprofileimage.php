<?php
$code		=	$_POST['idata'];
if(isset($_POST['idata']))
{
	list($type, $code) = explode(';', $code);
    list(, $code)      = explode(',', $code);
    $code = base64_decode($code);
	$filename	=	'users/'.time().".png";
    file_put_contents($filename, $code);
	
	//echo $filename;
}
?>