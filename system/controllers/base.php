<?php
  use \Firebase\JWT\JWT;

  abstract class BaseController {
    protected $logger;

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