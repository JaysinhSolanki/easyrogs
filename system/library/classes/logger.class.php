<?php

namespace EasyRogs;

  use \Exception;
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

    static function getCallstack( $e ) {
      return  "\n ---------------- \n" .
              $e->getTraceAsString()   .
              "\n ----------------";
    }

    static function toString( $value ) {
      if ( is_subclass_of($value,'Throwable') ) {
        $result =                                          "\n\n".
                  $value->getFile().":".$value->getLine() ."\n\n".
                  $value->getCode()                         ."\n".
                  $value->getMessage()                      ."\n".
                  self::getCallstack($value);
        return $result;
      }
      if ( is_array( $value ) || is_object( $value ) ) {
        $result = json_encode( $value, JSON_PRETTY_PRINT+JSON_UNESCAPED_LINE_TERMINATORS+JSON_UNESCAPED_SLASHES );
        if( !$result ) {
          $result = "// using var_export due to Logger.JSON_Error:". \json_last_error() .":". \json_last_error_msg() .
                    "\n". var_export($value, true);
        }
        return $result;
      }
      return $value;
    }

    protected function log_text($message, $level, $printBacktrace = false) {
      $message = Logger::toString($message);

      if ($printBacktrace) {
        $e = new Exception();
        $message .= self::getCallstack($e);
      }
      return $message;
    }

    protected function log($message, $level, $printBacktrace = false)
    {
      $time = date('Y-m-d H:i:s');
      $level = trim($level);

      if (in_array($level, self::LOG_LEVELS)) {
        $fp = isset($this->files[$level]) ? $this->files[$level] :
                  fopen($this->logsDir . '/' . strtolower($level) . '.log', 'a+');
        $this->files[$level] = $fp;
      }


      $message = $this->log_text( $message, $level, $printBacktrace );
      if( $fp ) { fwrite($fp, "$time $message \n"); }
      if( $level != self::ERROR ) { fwrite( $this->files['ALL'], "$time [$level] $message \n" ); }

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

function exception_logger($e) { global $logger;
  $logger->error( $e );
  die();
}
function error_logger($errNo, $msg, $file, $line, $vars) { global $logger;
  if( !(error_reporting() & $errNo) ) {
      //return false;
  }

  if( in_array($msg, ["session_start(): A session had already been started - ignoring", ])) {
    return false;
  }
  if( preg_match( '/.*'.preg_quote("/system/library/pdf/mpdf/7/vendor/mpdf/mpdf/src/Mpdf",'/').'/i', $file )) {
    return false;
  }

  switch( $errNo ) {
    case E_USER_ERROR:
      $logger->error( [ "\n\n$file:$line", "$msg",
                        ( $_ENV['APP_ENV'] == 'local' ) ? $vars : null ], true );
      die();
    case E_USER_WARNING:
      $logger->warn( [ "$file:$line", "$msg"], true );
      break;
    case E_USER_NOTICE:
    default:
      $logger->info( [ "$file:$line", "$msg"], true );
  }
}
