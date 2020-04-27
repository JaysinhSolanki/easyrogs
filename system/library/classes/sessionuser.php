<?php

class SessionUser {
  function __construct($addressBookId) {
    $this->users = new User();
    $this->teams = new Team();
    $this->user = $this->users->find($addressBookId);
    $this->id = $addressBookId;
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

  function permissions() {
    return [
      'cases' => [
        'create' => $this->canCreateCase()
      ]
    ];
  }
}