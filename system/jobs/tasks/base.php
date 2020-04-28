<?php
  class BaseTask {

    function __construct()
    {
      $this->className = get_class($this);
    }

    public function setData(array $data) {
      $this->data = $data;
    }

    protected function log($msg) {
      global $logger;

      $message = "[$this->className] $msg";

      $logger->info($message);
      echo "$message \n";

      return true;
    }

    protected function logError($msg) {
      global $logger;
      
      $message = "[$this->className] $msg";

      $logger->error($message);
      echo "ERROR: $message \n";
      
      return false;
    }

  }