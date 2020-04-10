<?php
  class Side extends BaseModel {
    const ROLE_PLAINTIFF = 'Plaintiff';
    const ROLE_DEFENDANT = 'Defendant';
    const ROLE_PLAINTIFF_X_DEFENDANT = 'Plaintiff and Cross-defendant';
    const ROLE_DEFENDANT_X_PLAINTIFF = 'Defendant and Cross-plaintiff';

    const ROLE_AGGREGATIONS = [
      [self::ROLE_PLAINTIFF, self::ROLE_PLAINTIFF_X_DEFENDANT],
      [self::ROLE_DEFENDANT, self::ROLE_DEFENDANT_X_PLAINTIFF],
    ];

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        'getByUserAndCase' => 'SELECT s.*
                               FROM sides AS s
                                    LEFT JOIN sides_users AS su
                                      ON s.id = su.side_id
                               WHERE s.case_id = :case_id 
                                     AND ( s.primary_attorney_id = :user_id  
                                           OR su.system_addressbook_id = :user_id )',
        
        'getByClientAndCase' => 'SELECT s.*
                                   FROM sides AS s
                                     LEFT JOIN sides_clients AS sc
                                       ON s.id = sc.side_id
                                 WHERE s.case_id = :case_id 
                                       AND sc.client_id = :client_id',
        
        'getUsers' => 'SELECT u.* 
                       FROM system_addressbook as u
                            INNER JOIN sides_users AS su 
                              ON su.system_addressbook_id = u.pkaddressbookid
                            INNER JOIN sides AS s
                              ON s.id = su.side_id
                       WHERE s.id = :side_id',

        'getClients' => 'SELECT c.* 
                         FROM clients as c
                             INNER JOIN sides_clients AS sc
                               ON sc.client_id = c.id
                             INNER JOIN sides AS s
                               ON s.id = sc.side_id
                         WHERE s.id = :side_id',

        'getOtherClients' => 'SELECT c.* 
                              FROM clients as c
                                  INNER JOIN sides_clients AS sc
                                    ON sc.client_id = c.id
                                  INNER JOIN sides AS s
                                    ON s.id = sc.side_id
                              WHERE s.id != :side_id 
                                    AND s.case_id = :case_id',

        'getPrimaryAttorney' => 'SELECT u.* 
                                 FROM system_addressbook as u
                                      INNER JOIN sides AS s
                                        ON s.primary_attorney_id = u.pkaddressbookid
                                 WHERE s.id = :side_id',
        'cleanupCase' => 'DELETE
                          FROM sides
                          WHERE case_id = :case_id
                                AND primary_attorney_id IS NULL
                                AND id NOT IN (
                                  SELECT side_id FROM sides_users
                                )
                                AND id NOT IN (
                                  SELECT side_id FROM sides_clients
                                )',
        'getBulkByCaseClientIds' => 'SELECT s.*
                                     FROM sides AS s
                                       INNER JOIN sides_clients AS sc
                                         ON ( sc.side_id = s.id )
                                     WHERE s.case_id = :case_id 
                                           AND sc.client_id IN (%1$s)'
      ]);
    }

    static function isRoleAggregable($role1, $role2) {
      $role1 = is_array($role1) ? $role1['role'] : $role1;
      $role2 = is_array($role2) ? $role2['role'] : $role2;

      $aggregable = $role1 === null || $role2 === null;
      foreach(self::ROLE_AGGREGATIONS as $roleAggregation) {
        $aggregable = $aggregable || ( in_array($role1, $roleAggregation)
                                       && in_array($role2, $roleAggregation) );
        if ($aggregable) break;
      }
      return $aggregable;
    }

    function getByUserAndCase($userId, $caseId, $asPrimary = false) {
      $query = $this->queryTemplates['getByUserAndCase'];
      if ($asPrimary) {
        return $this->getBy('sides', [
          'primary_attorney_id' => $userId,
          'case_id'             => $caseId
        ], 1);
      }
      else {
        $data = $this->readQuery($query, [
          'case_id' => $caseId, 
          'user_id' => $userId
        ])[0];
        return $data;
      }
    }

    function getByClientAndCase($clientId, $caseId) {
      $query = $this->queryTemplates['getByClientAndCase'];
      return $this->readQuery($query, [
        'case_id' => $caseId, 
        'client_id' => $clientId
      ])[0];
    }
    
    
    // $attorney - an attorney id OR a hash with system_addressbook data
    // $attorney can be NULL 
    function create($clientRole, $caseId, $attorney) {
      // set master head from primary attorney if specified
      if ( $attorney ) {
        $users = new User();
        $attorney = is_array($attorney) ? $attorney : $users->find($attorney);
        $attorneyId = $attorney ? $attorney['pkaddressbookid'] : null;
      }

      $id = $this->insert('sides', [
        'case_id'             => $caseId,
        'masterhead'          => $attorney ? $attorney['masterhead'] : '',
        'role'                => $clientRole,
        'primary_attorney_id' => $attorneyId
      ], true);
      return $this->getBy('sides', ['id' => $id], 1);
    }
    
    function find($id) {
      $this->getBy('sides', ['id' => $id])[0];
    }

    // $client - a client id OR a hash with client data
    function addClient($sideId, $client, $role = null) {
      $clientId = is_array($client) ? $client['id'] : $client;
      $role = $role ? $role : ( is_array($client) ? $client['role'] : $role);

      $side = $this->find($sideId);
      if ( !$side['role'] && $role) {
        $this->updateSide($sideId, ['role' => $role]);
      }
      return $this->insert('sides_clients', [
        'side_id'   => $sideId,
        'client_id' => $clientId        
      ], true);
    }

    // $user - user id OR a hash with user data
    function addUser($sideId, $user) {
      $userId = is_array($user) ? $user['pkaddressbookid'] : $user;
      return $this->insert('sides_users', [
        'side_id' => $sideId,
        'system_addressbook_id' => $userId
      ], true);
    }

    function byCaseId($caseId) {
      return $this->getBy('sides', ['case_id' => $caseId]);
    }

    function userIsMember($sideId, $userId) {
      $isPrimaryAttorney = $this->existsBy('sides', [
        'id' => $sideId,
        'primary_attorney_id' => $userId
      ]);
      $isMember = $this->existsBy('sides_users', [
        'side_id' => $sideId,
        'system_addressbook_id' => $userId
      ]);
      return $isPrimaryAttorney || $isMember;
    }

    function getUsers($sideId) {
      $query = $this->queryTemplates['getUsers'];
      return User::publishable(
        $this->readQuery($query, ['side_id' => $sideId])
      );
    }

    function getClients($sideId) {
      $query = $this->queryTemplates['getClients'];
      return $this->readQuery($query, ['side_id' => $sideId]);
    }

    function getOtherClients($sideId, $caseId) {
      $query = $this->queryTemplates['getOtherClients'];
      return $this->readQuery($query, [
        'side_id' => $sideId,
        'case_id' => $caseId
      ]);
    }

    function getPrimaryAttorney($sideId) {
      $query = $this->queryTemplates['getPrimaryAttorney'];
      return $this->readQuery($query, ['side_id' => $sideId], 1)[0];
    }

    function cleanupUsers($sideId) {
      return $this->deleteBy('sides_users', ['side_id' => $sideId]);
    }

    function cleanupClients($sideId) {
      return $this->deleteBy('sides_clients', ['side_id' => $sideId]);
    }

    function removeUser($sideId, $userId) {
      return $this->deleteBy('sides_users', [
        'side_id' => $sideId,
        'system_addressbook_id' => $userId
      ]);
    }

    function updateSide($sideId, $fields) {
      return $this->update('sides', $fields, ['id' => $sideId]);
    }

    function addAttorneyTeam($sideId, $attorneyId) {
      $teams = new Team();
      $team = $teams->byAddressBookId($attorneyId);
      $teamMembers = $teams->getMembers($team['id']);
      
      $this->cleanupUsers($sideId);
      
      foreach($teamMembers as $teamMember) {
        $this->addUser($sideId, $teamMember);
      }
    }

    function cleanupCase($caseId) {
      $query = $this->queryTemplates['cleanupCase'];
      return $this->writeQuery($query, ['case_id' => $caseId]);
    }

    function getBulkByCaseClientIds($caseId, $clientIds) {
      $query = $this->queryTemplates['getBulkByCaseClientIds'];
      return $this->readQuery($query, 
        ['case_id' => $caseId],  
        ['client_ids' => implode(',', $clientIds)]
      );
    }

    function isMergeable($mainSide, $withSide) {
      $mainUserIds = self::pluck($this->getUsers($mainSide['id']), 'pkaddressbookid');
      $withUserIds = self::pluck($this->getUsers($withSide['id']), 'pkaddressbookid');
      
      if ($mainSide['primary_attorney_id']) {
        $mainUserIds[] = $mainSide['primary_attorney_id'];
      }

      // Sides are role aggregable AND the second side doesnt have primary
      // attorney set AND (doesnt have users OR they are already on the main side)
      return $mainSide['id'] === $withSide['id'] 
             || ( self::isRoleAggregable($mainSide, $withSide) &&
                  !$withSide['primary_attorney_id'] &&  
                  (count($withUserIds) === 0 ||
                   array_diff($withUserIds, $mainUserIds) === 0));
    }

    function moveUsers($fromSide, $toSide) {
      if ( $fromSide['id'] != $toSide['id'] ) {
        $fromUsers = $this->getUsers($fromSide['id']);
        $this->cleanupUsers($fromSide['id']);
        foreach( $fromUsers as $user ) {
          $this->addUser($toSide['id'], $user);
        }
      }
    }

    function moveClients($fromSide, $toSide) {
      if ( $fromSide['id'] != $toSide['id'] ) {
        $fromClients = $this->getClients($fromSide['id']);
        $this->cleanupClients($fromSide['id']);
        foreach( $fromClients as $client ) {
          $this->addClient($toSide['id'], $client);
        }
      }
    }

    function mergeClientSides($caseId, $clientIds, $mainSide = null) {
      function checkMergeable ($carry, $item) { // $carry = [mainSide, mergeable, sidesModel]
        $sides = $carry[2];
        return [$carry[0], $carry[1] && $sides->isMergeable($carry[0], $item), $sides];
      }
      
      $clientSides = $this->getBulkByCaseClientIds($caseId, $clientIds);
      if ($clientSides) {
        if ( !$mainSide ) {
          $mainSide = $mainSide ? $mainSide : $clientSides[0]; // if no side provided merge with the first client side
          $clientSides = array_slice($clientSides, 1);
        }

        $mergeable = array_reduce(
          $clientSides, 'checkMergeable', [$mainSide, true, $this]
        )[1];
        
        if ( $mergeable ) {
          foreach( $clientSides as $clientSide ) {
            $this->moveUsers($clientSide, $mainSide);
            $this->moveClients($clientSide, $mainSide);
            if ($mainSide['id'] != $clientSide['id']) {
              $this->deleteBy('sides', ['id' => $clientSide['id']]);
            }
          }
          return $mainSide;
        }
        else return false;
      }
      else return false;
    }

    function getMasterHead($side, $update = true) {
      global $usersModel;
      
      if (!is_array($side)) { // assume ID
        $side = $this->find($side);
      }

      $masterhead = $side['masterhead'];
      if ( !$masterhead && $side['primary_attorney_id'] ) {
        $masterhead = $usersModel->getMasterHead($side['primary_attorney_id']);
      }
      
      if (!$side['masterhead'] && $update && $masterhead) {
        $this->update('sides', 
          ['masterhead' => $masterhead],
          ['id' => $side['id']]
        );
      }

      return $masterhead;      
    }

  }

  $sidesModel = new Side();