<?php
include_once("../includes/classes/adminsecurity.php");
 
	$AdminDAO->deleterows('system_form'," pkformid IN($ids)");//$AdminDAO->deleterows('tblfaq'," pkfaqid IN($ids)");
	$AdminDAO->deleterows('system_label'," fkformid IN($ids)");
?>