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
      echo "[$this->className] $msg \n";
    }

    protected function logError($msg) {
      error_log("[$this->className] $msg");
      $this->log("ERROR: $msg");
      return false;
    }

  }