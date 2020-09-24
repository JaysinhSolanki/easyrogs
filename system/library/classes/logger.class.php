<?php

  namespace EasyRogs;
  class Logger
  {
    const DEBUG      = 'DEBUG';
    const INFO       = 'INFO';
    const WARN       = 'WARN';
    const ERROR      = 'ERROR';
    const LOG_LEVELS = [self::DEBUG, self::INFO, self::WARN, self::ERROR];

    private $files = [];
    private $logsDir;

    public $stdOut = false;

    function __construct( $logsDir = LOGS_DIR, $stdOut = false )
    {
      $this->logsDir = $logsDir;
      $this->stdOut = $stdOut;
      $this->files['ALL'] = fopen( $logsDir . '/all.log', 'a+' );
    }

    function __destruct()
    {
      foreach($this->files as $file) {
        fclose($file);
      }
    }

    static function toString( $value ) {
      if ( is_array( $value ) || is_object( $value ) ) {
        return json_encode( $value, JSON_PRETTY_PRINT+JSON_UNESCAPED_LINE_TERMINATORS+JSON_UNESCAPED_SLASHES );
      }
      return $value;
    }

    protected function log_text($message, $level, $printBacktrace = false) {
      $message = Logger::toString($message);

      if ($printBacktrace) {
        $e = new Exception();
        $message .= "\n ---------------- \n" .
                    $e->getTraceAsString()   .
                    "\n ----------------";
      }
      return $message;
    }


    protected function log($message, $level, $printBacktrace = false)
    {
      $time = date('Y-m-d H:i:s');
      $level = trim($level);


      if (in_array($level, self::LOG_LEVELS)) {
        $fp = $this->files[$level] = $this->files[$level]
              ? $this->files[$level]
              : fopen($this->logsDir . '/' . strtolower($level) . '.log', 'a+');
      }


      $message = $this->log_text( $message, $level, $printBacktrace );
      if ($fp) { fwrite($fp, "$time $message \n"); }
      fwrite( $this->files['ALL'], "$time [$level] $message \n" );


      if ($this->stdOut) { echo "$time [$level] $message \n"; }
    }

    public function info($message,  $printBacktrace = false) { $this->log($message, self::INFO,  $printBacktrace); }
    public function warn($message,  $printBacktrace = false) { $this->log($message, self::WARN,  $printBacktrace); }
    public function error($message, $printBacktrace = false) { $this->log($message, self::ERROR, $printBacktrace); }
    public function debug($message, $printBacktrace = false) {
      if ($_ENV['APP_ENV'] != 'prod') {
        $this->log($message, self::DEBUG, $printBacktrace);
      }
    }

    public function browser_log($message, $alert = "", $level = self::INFO, $printBacktrace = false)
    {
      $message = $this->log_text($message, $level, $printBacktrace);
      $code = '';
      if( $alert ) {
        $code .= "window.alert(`". Logger::toString($alert) ."`);\n\r";
      }
      if( $message ) {
        $code .= "console.log(`". Logger::toString($message) ."`);\n\r"; // TODO select console.?? by $level
      }
      echo "<script>$code</script>";
    }
}