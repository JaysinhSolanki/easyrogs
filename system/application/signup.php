<?php
  @session_start();
  //Destroy session if user already logged in
  setcookie('rememberme','', time()+(86400*30), "/");
  setcookie("rememberme",'', time()-3600);
  @session_destroy();

  require_once("../bootstrap.php");

  $signup = new SignupController();
  $signup->show();