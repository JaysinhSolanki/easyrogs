<?php

class SessionUser {
  const ATTORNEY_GROUP_ID = 3;
  
  const GROUP_NAMES = [
    1 => 'Developer',
    2 => 'Administrator',
    3 => 'Attorney',
    4 => 'Support'
  ];

  function __construct($addressBookId) {
    $this->addrBookModel = new SystemAddressBook();
    $this->user = $this->addrBookModel->find($addressBookId);
  }

  function searchableGroupIds() { return [3, 4]; } // attorney and support
}