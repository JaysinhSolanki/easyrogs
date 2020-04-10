<?php
  class Team extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        'getMembers' => 'SELECT sab.*
                         FROM users_teams as ut
                           INNER JOIN system_addressbook AS sab 
                             ON (ut.system_addressbook_id = sab.pkaddressbookid)
                         WHERE ut.team_id = :team_id',
        
        'memberExistsByEmail' => 'SELECT count(*) as `count`
                                  FROM users_teams as ut
                                    INNER JOIN system_addressbook AS sab 
                                      ON (ut.system_addressbook_id = sab.pkaddressbookid)
                                  WHERE ut.team_id = :team_id AND
                                        sab.email = :email'
      ]);
    }

    public function byAddressBookId($addressBookId) {
      $fields = ['system_addressbook_id' => $addressBookId];
      return $this->getOrInsertBy('teams', $fields);
    }

    public function getMembers($teamId) {
      $query = $this->queryTemplates['getMembers'];

      return $this->readQuery($query, ['team_id' => $teamId]);
    }

    public function deleteMember($teamId, $memberId) {
      return $this->deleteBy('users_teams', [
        'team_id'               => $teamId,
        'system_addressbook_id' => $memberId
      ]);
    }

    public function memberExistsByEmail($teamId, $email) {
      $query = $this->queryTemplates['memberExistsByEmail'];

      return $this->readQuery($query, [
        'team_id' => $teamId,
        'email'   => $email
      ])[0]['count'] > 0;
    }

    public function memberExists($teamId, $addressBookId) {
      return $this->existsBy('users_teams', [
        'team_id' => $teamId,
        'system_addressbook_id' => $addressBookId
      ]);
    }

    public function addMember($teamId, $addressBookId) {
      $this->insert('users_teams', [
        'team_id' => $teamId,
        'system_addressbook_id' => $addressBookId
      ], true);
    }

  }

  $teamsModel = new Team();