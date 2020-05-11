<?php

class SessionUser {
  function __construct($userId) {
    $this->users = new User();
    $this->teams = new Team();
    $this->user = $this->users->find($userId);
    $this->id = $userId;
  }

  function searchableGroupIds() { return [3, 4]; } // attorney and support
  
  function getTeamAttorneys() {
    return $this->users->getTeamAttorneys($this->id);
  }

  function isAttorney() {
    return $this->user['fkgroupid'] == User::ATTORNEY_GROUP_ID;
  }

  function canCreateCase() {
    return !!($this->isAttorney() || $this->getTeamAttorneys());
  }

  function getCounty() {
    return $_COOKIE['ER_ATTORNEY_COUNTY'];
  }

  function setCounty($county) {
    setcookie("ER_ATTORNEY_COUNTY", $county, time() + 31556926 ,'/');
  }

  function permissions() {
    return [
      'cases' => [
        'create' => $this->canCreateCase()
      ]
    ];
  }

  function getSide($case) {
    global $sidesModel;

    $caseId = is_array($case) ? $case['id'] : $case;
    
    return $sidesModel->getByUserAndCase($this->id, $caseId);
  }
}