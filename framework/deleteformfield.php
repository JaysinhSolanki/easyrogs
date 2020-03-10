<?php
require_once("common.php");
echo $pkformfieldid=$_POST['pkformfieldid']; 
$AdminDAO->deleterows("system_formfield","pkformfieldid=$pkformfieldid");
$AdminDAO->deleterows("tblfilesetting","fkformfieldid=$pkformfieldid");
$AdminDAO->deleterows("system_formfieldoption","fkformfieldid=$pkformfieldid");

