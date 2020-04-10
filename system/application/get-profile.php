<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $users = new User();
  $user = $currentUser->user;
  $user['masterhead'] = $users->getMasterHead($user);

  // LEGACY --------------------------------------------------------------------
  require_once FRAMEWORK_PATH . '/profile.php';