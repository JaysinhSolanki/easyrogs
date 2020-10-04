<?php
  use \Firebase\JWT\JWT;

  abstract class BaseController {
    protected $logger;

    const FORMAT_JSON = 'json';
    const FORMAT_HTML = 'html';

    function __construct() { global $logger;
      $this->logger = $logger;
    }

    function jwtEncodeToken($payload) {
      return JWT::encode($payload, SECRET_KEY);
    }

    function jwtDecodeToken($token) {
      return JWT::decode($token, SECRET_KEY, ['HS256']);
    }
  }