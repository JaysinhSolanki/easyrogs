<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");
  
  // extract params
  $meetConferController = new MeetConferController();
  $meetConferController->save();