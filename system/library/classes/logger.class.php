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
    const PROBLEM_REPORTS = [self::WARN, self::ERROR];

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

    function clearLogs() { // Reset logs, useful when debugging

      // Safety checks:
      $caller = debug_backtrace();
      if( $_ENV['APP_ENV'] == 'prod' ) {
        $logger->warn( ['Using clearLogs() in production? NO-NO-NO!!', $caller] );
        return;
      }
      if( !$this->logsDir ) { // be safe!
        $logger->warn( ['clearLogs() but $this->logsDir is not set', $this, $caller] );
        return;
      }

      $files = glob( $this->logsDir ."/*.log" );
      foreach( $files as $file ) {
        if( is_file($file) )
          unlink($file);
      }
    }

    static function getCallstack( $e ) {
      $result = "\n ---------------- \n" .
                $e->getTraceAsString()   .
                "\n ----------------";
      $result = \preg_replace( "/^#\d+ .*classes\/logger\.class\.php\(.*$/m", '', $result); // remove references to this library
      $result = \str_replace( "\n\n", "\n", $result); // remove empty lines
      return $result;
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
        $e = new Exception();  //!! TODO could also use debug_backtrace() directly
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
      if( in_array($level, self::PROBLEM_REPORTS ) ) $time = "\n\n* $time"; // or any separator

      if( $fp ) { fwrite($fp, "$time $message \n"); }
      if( $level != self::ERROR && $level != self::DEBUG ) { fwrite( $this->files['ALL'], "$time [$level] $message \n" ); }

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
  $text = Logger::toString($e);
  if( $_ENV['APP_ENV'] == 'local' ) $logger->browser_log( $text, $text );
  die();
}
function error_logger($errNo, $msg, $file, $line, $vars = []) { global $logger;
  if( !error_reporting() ) return false; // `@code` silenced by developer, respect that

    if( !(error_reporting() & $errNo) ) {
      //return false;
  }

  if( in_array($msg, ["session_start(): A session had already been started - ignoring", ])) {
    return false;
  }
  if( preg_match( '/.*'.preg_quote("/system/library/pdf/mpdf/7/vendor/mpdf/mpdf/src/Mpdf",'/').'/i', $file ) ||
      preg_match( '/.*'.preg_quote("xdebug://"                                           ,'/').'/i', $file ) ) {
    return false;
  }

  switch( $errNo ) {
    case E_CORE_ERROR: case E_ERROR: case E_USER_ERROR: case E_RECOVERABLE_ERROR:
      $text = Logger::toString(["\n\n$file:$line", $msg,
                              ( $_ENV['APP_ENV'] == 'local' ) ? $vars : null ]);
      $logger->error( $text, true );
      if( $_ENV['APP_ENV'] == 'local' ) $logger->browser_log( $text, $text );
      die();
    case E_NOTICE: case E_USER_NOTICE:
    case E_CORE_WARNING: case E_WARNING: case E_USER_WARNING:
    case E_DEPRECATED: case E_USER_DEPRECATED:
      $logger->warn( [ "$file:$line($errNo)", $msg], true );
      break;
    default:
      $logger->info( [ "$file:$line($errNo)", $msg], true );
  }
}

function _assert( $test, $etc = null ) { global $logger;

  if( $_ENV['APP_ENV'] == 'local' ) {
    $idx = 0;
    if( \is_array($test) ) {
      foreach( $test as $value ) {
        if( !$value ) break;
        $idx++;
      }
      if( $idx == sizeof($test) ) return true;
    } else {
      if( !!$test ) return true;
    }

    $caller = debug_backtrace()[1];
    $info = ["assertion failed at ". $caller['file'].":".$caller['line'], $caller['args'], $etc ];
    $logger->error( $info, true );
    // Check if JSON or HTML, act accordingly
    // getallheaders()['Accept] apache_response_headers()
    $logger->browser_log( $info, $info );
    return false;
  }
  return "NOT CHECKED"; // it's a truthy value anyways. TODO: think this
}

