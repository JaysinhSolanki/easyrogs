<?php

  class HttpResponse {
    static $die = true;

    const TYPE_ERROR   = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO    = 'info';
    const TYPE_SUCCESS = 'success';

    const ERROR_INTERNAL             = '500: Internal Error';
    const ERROR_NOT_IMPLEMENTED      = '501: Not Implemented';
    const ERROR_BAD_GATEWAY          = '502: Bad Gateway';
    const ERROR_SERVICE_UNAVAILABLE  = '503: Service Unavailable';
    const ERROR_GATEWAY_TIMEOUT      = '504: Gateway Timeout';
    const ERROR_INSUFFICIENT_STORAGE = '507: Insufficient Storage';

    static function send($code, $type, $message, $extra) {
      header('Content-Type: application/json');
      $response = array_merge($extra, [
        'type' => $type,
        'msg'  => $message
      ]);
      HttpResponse::sendPayload($code, $response);
    }

    static function sendPayload($code, $payload) {
      http_response_code($code);
      if ( self::$die ) {
        die(json_encode($payload));
      }
      else {
        echo(json_encode($payload));
      }
    }

    static function malformed($message = 'Malformed Request', $extra = []) {
      self::send(400, self::TYPE_ERROR, $message, $extra);
    }

    static function unprocessable($message = 'Unprocessable Entity', $extra = []) {
      self::send(422, self::TYPE_ERROR, $message, $extra);
    }

    static function notFound($message = 'Not Found', $extra = []) {
      self::send(404, self::TYPE_ERROR, $message, $extra);
    }

    static function unauthorized($message = 'Unauthorized.', $extra = []) {
      self::send(403, self::TYPE_ERROR, $message, $extra);
    }

    static function success($message = 'Success!', $extra = []) {
      self::send(200, self::TYPE_SUCCESS, $message, $extra);
    }

    // conflict sends type = warning by default
    static function conflict($message = 'Conflict Found', $type = self::TYPE_ERROR, $extra = []) {
      self::send(409, $type, $message, $extra);
    }

    static function internalError($message, $errorType = self::ERROR_INTERNAL, $extra = []) {
      [$errorCode, $defaultMessage] = explode(':', $errorType, 2);
      self::send($errorCode, TYPE_ERROR, $message ?: trim($defaultMessage) ?: "Internal error", $extra);
    }

    static function successPayload($payload, $code = 200) {
      self::sendPayload($code, $payload);
    }

    static function noContent() {
      http_response_code(204);
      self::$die && die();
    }

    static function paymentRequired($message = 'Payment Required.', $extra = []) {
      self::send(402, self::TYPE_ERROR, $message, $extra);
    }

    static function redirect($location, $code = 302) {
      http_response_code($code);
      header("Location: $location");
      self::$die && die();
    }

  }