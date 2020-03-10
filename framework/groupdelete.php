<?php
require_once("adminsecurity.php");
$AdminDAO->deleterows('system_groups'," pkgroupid IN($ids)");
?>