<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $term = $_GET['term'];
  $addressBookId = $_SESSION['addressbookid'];
  
  $usersModel = new SystemAddressBook();
  $users = $usersModel->searchInGroups(
    $currentUser->searchableGroupIds(), 
    $term    
  );
  
  $response = [];
  foreach($users as $user) {
    $type = SessionUser::GROUP_NAMES[$user['fkgroupid']];
    $name = "$user[firstname] $user[lastname]";
    $bar  = $user['barnumber'] ? " - Bar No. $user[barnumber]" : '';
    
    $response[] = [
      'id'   => $user['pkaddressbookid'],
      'text' => "$name ($type$bar)"
    ];
  }

  die(
    json_encode([
      'results' => $response
    ])
  );