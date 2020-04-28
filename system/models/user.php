<?php
  class User extends BaseModel {
    const TABLE = 'system_addressbook';

    const PUBLISHABLE_KEYS = [
      'pkaddressbookid', 'firstname', 'middlename', 'lastname', 'email', 
      'address', 'barnumber', 'masterhead', 'fkgroupid', 'side_active'
    ];

    const SEARCH_FIELDS = [
      'firstname', 'middlename', 'lastname', 'email', 'barnumber'
    ];

    const ATTORNEY_GROUP_ID = 3;
    const GROUP_NAMES = [
      1 => 'Developer',
      2 => 'Administrator',
      3 => 'Attorney',
      4 => 'Support'
    ];

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        // This query returns all owner attorneys from all the teams of an user, 
        'getTeamAttorneys' => 'SELECT u.*
                                 FROM teams AS t
                                   INNER JOIN system_addressbook AS u 
                                     ON t.system_addressbook_id = u.pkaddressbookid
                                   WHERE t.id IN ( 
                                           SELECT team_id 
                                           FROM users_teams 
                                           WHERE users_teams.system_addressbook_id = :user_id
                                         ) AND u.fkgroupid = ' . self::ATTORNEY_GROUP_ID,

                             
      ]);
    }

    static function publishable($users) {
      $singleUser = isset($users['pkaddressbookid']);
      $users = $singleUser ? [$users] : $users;
      
      if ( !$users ) { return false; }
      
      foreach($users as &$user) {
        if (is_array($user)) {
          foreach($user as $key => $value) {
            if ( !in_array($key, self::PUBLISHABLE_KEYS) ) {
              unset($user[$key]);
            }
          }
        
          $user['id'] = $user['pkaddressbookid'];
          $user['full_name'] = self::getFullName($user);
          $user['group_name'] = self::GROUP_NAMES[$user['fkgroupid']];
        }
      }
      return $singleUser ? $users[0] : $users;
    }

    function getByEmail($email) {
      return $this->getBy(self::TABLE, ['email' => $email], 1);
    }

    function find($id) {
      return $this->getBy(self::TABLE, ['pkaddressbookid' => $id], 1);
    }

    function findByUID($uid) {
      return $this->getBy(self::TABLE, ['uid' => $uid], 1);
    }

    function findUnverified($id) {
      return $this->getBy(self::TABLE, [
        'pkaddressbookid' => $id,
        'emailverified'   => null
      ], 1);
    }
    function findInactive($id) { return $this->findUnverified($id); }

    function findVerified($id) {
      return $this->getBy(self::TABLE, [
        'pkaddressbookid' => $id,
        'emailverified'   => 1
      ], 1);
    }
    function findActive($id) { return $this->findVerified($id); }

    function findAttorney($id) {
      return $this->getBy(self::TABLE, [
        'pkaddressbookid' => $id,
        'fkgroupid' => self::ATTORNEY_GROUP_ID
      ], 1);
    }

    function create($fieldsMapping) {
      global $currentUser;
      
      $id = $this->insert(self::TABLE, array_merge([
        'uid'                => $this->generateUID('system_addressbook'),
        'username'           => '',
        'contactperson'      => '',
        'street'             => '',
        'fkadmittedstateid'  => 0,
        'barnumber'          => '',
        'cityname'           => '',
        'iscustomer'         => 0,
        'organizationnumber' => '',
        'uploadfile'         => '',
        'agentpercentage'    => '',
        'agentshortcode'     => '',
        'isblocked'          => 0,
        'designation'        => '',
        'companyname'        => '',
        'attorney_info'      => '',
        'isclosed'           => 0,
        'orignal_email'      => $fieldsMapping['email'], // email is required
        'accountid'          => '',
        'updated_at'         => date('Y-m-d H:i:s'),
        'updated_by'         => $currentUser->id,
        'masterhead'         => '',
      ], $fieldsMapping));
      return $this->getBy(self::TABLE, ['pkaddressbookid' => $id], 1);
    }

    function searchInGroups($groupIds, $term, $fields = self::SEARCH_FIELDS) {
      return $this->searchBy(
        self::TABLE, 
        $term, 
        $fields, 
        ['fkgroupid' => $groupIds]
      );
    }

    function getTeamAttorneys($userId) {
      $query = $this->queryTemplates['getTeamAttorneys'];
      $user = $this->find($userId);
      $attorneys = User::publishable($this->readQuery($query, ['user_id' => $userId]));

      if ($user['fkgroupid'] == self::ATTORNEY_GROUP_ID) {
        $exists = in_array($userId, BaseModel::pluckIds($attorneys));
        if (!$exists) {
          $attorneys[] = User::publishable($user);
        }
      }
      return $attorneys;
    }

    function expressFindOrCreate($name, $email, $groupId = null) {
      global $jobsQueue;

      $user = $this->getByEmail($email);
      if (!$user) {
        $nameParts = explode(' ', $name);
        $user = $this->create([
          'firstname' => $nameParts[0], 
          'lastname'  => implode( ' ', array_slice($nameParts,  1) ), 
          'email'     => $email,
          'fkgroupid' => $groupId
        ]);
      }
      return $user;
    }

    function getMasterHead($user, $update = true) {
      if (!is_array($user)) { // assume ID
        $user = $this->find($user);
      }
      $masterhead = $user['masterhead'] 
                    ? $user['masterhead'] 
                    : $this->buildMasterHead($user);
      
      if (!$user['masterhead'] && $update && $masterhead) {
        $this->update('system_addressbook', 
          ['masterhead' => $masterhead], 
          ['pkaddressbookid' => $user['pkaddressbookid']]
        );
      }

      return $masterhead;      
    }

    function buildMasterHead($user) {
      function implodeLine($line) { 
        foreach($line as &$item) { $item = trim($item); }
        return trim(implode(' ', $line), ' ,'); 
      }

      $fullName = self::getFullName($user);
      $state = $this->getBy('system_state', ['pkstateid' => $user['fkstateid']], 1);
      $stateShort	=	$state['statecode'];
    
      $parts = [
        [$fullName, $user['barnumber'] ? "($user[barnumber])" : ''],
        [$user['companyname']],
        [$user['address'] . ", ", $user['street']],
        [$user['cityname'] . ", ", $stateShort, $user['zip']],
        [$user['phone']],
        [$user['email']]
      ];
      
      $lines = array_map('implodeLine', $parts);
      $masterHead = trim( implode( "\n", $lines) );
      return str_replace("\n\n", "\n", $masterHead);
    }

    static function getFullName($user) {
      return trim("$user[firstname] $user[middlename] $user[lastname]");
    }

    function getByAttorneyId($attorneyId, $create = false) {
      $attorney = $this->getBy('attorney', ['id' => $attorneyId], 1);
      $user = null;
      if ( $attorney ) {
        $user = $this->getByEmail($attorney['attorney_email']);
      }
      if( !$user && $create ) {
        $user = $this->expressFindOrCreate(
          $attorney['attorney_name'],
          $attorney['attorney_email'],
          self::ATTORNEY_GROUP_ID
        );
      }
    
      return $user;
    }

    static function inCollection($user, $users, $key = 'pkaddressbookid' ) {
      return parent::inCollection($user, $users, $key);
    }

    static function isAttorney($user) {
      return $user['fkgroupid'] == self::ATTORNEY_GROUP_ID;
    }

    static function isActive($user) {
      return $user['emailverified'] == 1;
    }

  }

  $usersModel = new User();