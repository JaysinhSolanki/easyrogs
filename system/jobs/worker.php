<?php
  require_once __DIR__ . '/../bootstrap.php';
  require_once __DIR__ . '/tasks/index.php';

  $worker = new Qutee\Worker;
  $worker->setQueue($jobsQueue);
  
  //while( true ) {
    try {
      $worker->run();
    } catch (Exception $e) {
      error_log($e->getMessage());
    }
  //}