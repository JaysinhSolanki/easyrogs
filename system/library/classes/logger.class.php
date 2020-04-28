<?php
  class Logger
  {
    const DEBUG      = 'DEBUG';
    const INFO       = 'INFO';
    const WARN       = 'WARN';
    const ERROR      = 'ERROR';
    const LOG_LEVELS = [self::DEBUG, self::INFO, self::WARN, self::ERROR];

    private $files = [];
    private $logsDir;

    function __construct( $logsDir = LOGS_DIR )
    {
      $this->logsDir = $logsDir;
      $this->files['ALL'] = fopen( $logsDir . '/all.log', 'a+' );
    }

    function __destruct()
    {
      foreach($this->files as $file) {
        fclose($file);
      }
    }

    protected function log($message, $level)
    {
      $time = date('Y-m-d H:i:s');

      $level = trim($level);
      if (in_array($level, self::LOG_LEVELS)) {
        $fp = $this->files[$level] = $this->files[$level]
              ? $this->files[$level] 
              : fopen($this->logsDir . '/' . strtolower($level) . '.log', 'a+');
      }

      if ( is_array( $message ) || is_object( $message ) ) {
        $message = print_r( $message, true );
      }

      if ($fp) { fwrite($fp, "$time $message \n"); }
      fwrite( $this->files['ALL'], "$time [$level] $message \n" );
    }

    public function debug($message) { $this->log($message, self::DEBUG); }
    public function info($message)  { $this->log($message, self::INFO); }
    public function warn($message)  { $this->log($message, self::WARN); }
    public function error($message) { $this->log($message, self::ERROR); }    
  }