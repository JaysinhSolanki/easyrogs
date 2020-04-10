<?php
  require_once(__DIR__ . '/../bootstrap.php');
  require_once("adminsecurity.php");

  $term = $_GET['term'];
  $groupId = $_GET['group_id'];
  $addressBookId = $_SESSION['addressbookid'];
  
  // group_id filter
  $groups = $currentUser->searchableGroupIds();
  if ($groupId && in_array($groupId, $groups)) {
    $groups = [$groupId];
  }
  if(empty($groups)) {
    http_response_code(403);
    die(
      json_encode([
        'error' => 'Unauthorized group id'
      ])
    );
  }

  // query
  $usersModel = new User();
  $users = $usersModel->searchInGroups(
    $currentUser->searchableGroupIds(), 
    $term    
  );

  // format response  
  $response = [];
  foreach($users as $user) {
    $type = User::GROUP_NAMES[$user['fkgroupid']];
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