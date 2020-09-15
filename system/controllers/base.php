<?php
  use \Firebase\JWT\JWT;

  abstract class BaseController {
    function jwtEncodeToken($payload) {
      return JWT::encode($payload, SECRET_KEY);
    }

    function jwtDecodeToken($token) {
      return JWT::decode($token, SECRET_KEY, ['HS256']);
    }
  }