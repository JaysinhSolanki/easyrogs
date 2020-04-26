<?php

  class Logger
  {
    const DEBUG   = 'DEBUG';
    const INFO    = 'INFO';
    const WARN    = 'WARN';
    const ERROR   = 'ERROR';

    protected $fpAllLogs;
    protected $fpDebugLogs;
    protected $fpInfoLogs;
    protected $fpWarningLogs;
    protected $fpErrorLogs;

    function __construct( $logsDir = LOGS_DIR )
    {
      $this->fpAllLogs       = fopen( $logsDir . '/all.log',       'a+' );
      $this->fpDebugLogs     = fopen( $logsDir . '/debug.log',     'a+' );
      $this->fpInfoLogs      = fopen( $logsDir . '/info.log',      'a+' );
      $this->fpWarningLogs   = fopen( $logsDir . '/warning.log',   'a+' );
      $this->fpErrorLogs     = fopen( $logsDir . '/error.log',     'a+' );
    }

    function __destruct()
    {
      fclose( $this->fpAllLogs );
      fclose( $this->fpDebugLogs );
      fclose( $this->fpInfoLogs );
      fclose( $this->fpWarningLogs );
      fclose( $this->fpErrorLogs );
    }

    public function log($message, $level = self::DEBUG)
    {
      $time = date('Y-m-d H:i:s');

      switch( $level )
      {
        case self::DEBUG: $fpContextLogs = $this->fpDebugLogs; break;
        case self::INFO:  $fpContextLogs = $this->fpInfoLogs; break;
        case self::WARN:  $fpContextLogs = $this->fpWarningLogs; break;
        case self::ERROR: $fpContextLogs = $this->fpErrorLogs; break;
      }

      if ( is_array( $message ) || is_object( $message ) ) {
        $message = print_r( $message, true );
      }        

      fwrite( $fpContextLogs,   "$time $message \n" );
      fwrite( $this->fpAllLogs, "$time [$level] $message \n" );
    }

  }

?>