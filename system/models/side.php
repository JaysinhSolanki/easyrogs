<?php
  use function EasyRogs\_assert as _assert;

  class Side extends BaseModel {
    const CASE_DATA_FIELDS = [
      'case_number', 'case_title', 'plaintiff', 'defendant', 'trial',
      'discovery_cutoff', 'county_name', 'masterhead', 'normalized_number'
    ];

    const ROLE_AGGREGATIONS = [
      [Client::ROLE_PLAINTIFF, Client::ROLE_PLAINTIFF_X_DEFENDANT],
      [Client::ROLE_DEFENDANT, Client::ROLE_DEFENDANT_X_PLAINTIFF],
    ];

    const SAME_SIDE  = "same-side";
    const OTHER_SIDE = "other-side";
    const NOT_FOUND_IN_SIDE = "not-found-in-side";

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

        'getUsers' => 'SELECT u.*, su.active AS side_active
                       FROM system_addressbook AS u
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
                                           AND sc.client_id IN (%1$s)',

        'getServiceList' => 'SELECT a.id AS attorney_id,
                                    a.attorney_name,
                                    a.attorney_email,
                                    u.*
                             FROM system_addressbook AS u
                               INNER JOIN attorney AS a
                                 ON (a.fkaddressbookid = u.pkaddressbookid)
                             WHERE a.side_id = :side_id
                             GROUP BY u.pkaddressbookid',

        'getUserServiceListClients' => 'SELECT c.*
                                        FROM clients AS c
                                          INNER JOIN client_attorney AS ca
                                            ON ca.client_id = c.id
                                          INNER JOIN attorney AS a
                                            ON a.id = ca.attorney_id
                                        WHERE a.side_id = :side_id AND
                                              a.fkaddressbookid = :user_id',

        'getSidesUsersByCase' => 'SELECT s.*, u.*,
                                          u.pkaddressbookid as user_id, s.id as side_id
                                    FROM system_addressbook AS u
                                      INNER JOIN sides_users AS su
                                        ON su.system_addressbook_id = u.pkaddressbookid
                                      INNER JOIN sides AS s
                                        ON s.id = su.side_id
                                    WHERE s.case_id = :case_id AND
                                          su.active = 1',
                                                                
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

    function getSidesUsersByCase($caseId) {
      $query = $this->queryTemplates['getSidesUsersByCase'];
      $result = $this->readQuery($query, [
        'case_id' => $caseId,
      ]);
      _assert( sizeof($result), ['getSidesUsersByCase', $caseId] );
      return $result;
    }

    function createServiceList($newSide) {
      global $casesModel, $usersModel;
      $sides = $casesModel->getSides($newSide['case_id']);
      foreach($sides as $side) {
        if ($side['id'] == $newSide['id']) { continue; }

        $primaryAttorney = $usersModel->find($side['primary_attorney_id']);
        if ($primaryAttorney) {
          $clients = $this->getClients($side['id']);
          $this->updateServiceListForAttorney($newSide, $primaryAttorney, $clients);
        }
      }
    }

    // $attorney - an attorney id OR a hash with system_addressbook data
    // $attorney can be NULL
    function create($clientRole, $caseId, $attorney) {
      global $casesModel;

      // set master head from $attorney if specified
      if ( $attorney ) {
        $users = new User();
        $attorney = is_array($attorney) ? $attorney : $users->find($attorney);
        $attorneyId = $attorney ? $attorney['pkaddressbookid'] : null;
      }

      $sideId = $this->insert('sides', [
        'case_id'             => $caseId,
        'masterhead'          => $attorney ? $attorney['masterhead'] : '',
        'role'                => $clientRole,
        'primary_attorney_id' => $attorneyId
      ]);

      $side = $this->find($sideId);

      // update created side with initial case data
      $case = $casesModel->find($caseId);
      $this->updateCaseData($sideId, $case);

      $this->createServiceList($side);

      return $side;
    }

    function find($id) {
      return $this->getBy('sides', ['id' => $id], 1);
    }

    // $client - a client id OR a hash with client data
    function addClient($sideId, $client, $role = null) {
      $clientId = is_array($client) ? $client['id'] : $client;
      $role = $role ? $role : ( is_array($client) ? $client['role'] : $role);

      $side = $this->find($sideId);
      if ( !$side['role'] && $role) {
        $this->updateSide($sideId, ['role' => $role]);
      }
      $this->insert('sides_clients', [
        'side_id'   => $sideId,
        'client_id' => $clientId
      ], true);

      $this->updateServiceListForPrimaryAttorney($side);
    }

    // $user - user id OR a hash with user data
    function addUser($sideId, $user, $active = true, $checkUniqueSide = true) {
      global $usersModel, $casesModel;

      $user = is_array($user) ? $user : $usersModel->find($user);
      $side = $this->find($sideId);

      if ($checkUniqueSide) {
        $userSide = $this->getByUserAndCase($user['pkaddressbookid'], $side['case_id']);
        if ($userSide && $userSide['id'] != $side['id']) {
          return false;
        }
      }

      $this->insert('sides_users', [
        'side_id'               => $sideId,
        'system_addressbook_id' => $user['pkaddressbookid'],
        'active'                => $active
      ], true);

      if ($active && User::isAttorney($user)) {
        $this->updateServiceLists($side, $user);
        if ( !Side::hasPrimaryAttorney($side) ) {
          $casesModel->setSideAttorney($side, $user['pkaddressbookid'], false);
        }
      }

      return true;
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

    function getAllUsers($sideId) {
      $query = $this->queryTemplates['getUsers'];
      $users = $this->readQuery($query, ['side_id' => $sideId]);

      $primaryAttorney = $this->getPrimaryAttorney($sideId);
      if ($primaryAttorney && !self::inCollection($primaryAttorney, $users, 'systemaddressbookid')) {
        $users[] = $primaryAttorney;
      }
      return $users;
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
      return $this->readQuery($query, ['side_id' => $sideId])[0];
    }

    function cleanupUsers($sideId) {
      return $this->deleteBy('sides_users', ['side_id' => $sideId]);
    }

    function cleanupClients($sideId) {
      return $this->deleteBy('sides_clients', ['side_id' => $sideId]);
      $this->updateServiceListForPrimaryAttorney($sideId);
    }

    function removeUser($sideId, $userId, $alsoRemovePrimaryAttorney = true) {
      $this->deleteBy('sides_users', [
        'side_id' => $sideId,
        'system_addressbook_id' => $userId
      ]);

      if ($alsoRemovePrimaryAttorney) {
        $side = $this->find($sideId);
        if ($side['primary_attorney_id'] === $userId) {
          $this->updateSide($sideId, ['primary_attorney_id' => null]);
        }
      }
    }

    function updateSide($sideId, $fields) {
      return $this->update('sides', $fields, ['id' => $sideId]);
    }

    function addAttorneyTeam($sideId, $attorneyId) {
      global $casesModel;

      $teams = new Team();
      $team = $teams->byAddressBookId($attorneyId);
      $teamMembers = $teams->getMembers($team['id']);

      $side = $this->find($sideId);
      $this->cleanupUsers($sideId);

      foreach($teamMembers as $teamMember) {
        $userId = $teamMember['pkaddressbookid'];
        $caseId = $side['case_id'];
        // check the user is not already in another side of the case.
        if ( !$casesModel->userInCase($caseId, $userId ) ) {
          $this->addUser($sideId, $teamMember);
        }
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
      global $usersModel, $logger;

      if (!is_array($side)) { // assume ID
        $side = $this->find($side);
      }

      $logger->debug(['SIDE_GET_MASTERHEAD Side: ', $side]);
      $masterhead = $side['masterhead'];
      if ( !$masterhead && $side['primary_attorney_id'] ) {
        $logger->debug(['SIDE_GET_MASTERHEAD Updating Side: ', json_encode($side)]);
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

    function activateUser($sideId, $userId) {
      global $usersModel, $casesModel;

      $this->update('sides_users',
        ['active' => true],
        [
          'side_id'               => $sideId,
          'system_addressbook_id' => $userId
        ],
        true
      );

      $user = $usersModel->find($userId);
      $side = $this->find($sideId);

      if (User::isAttorney($user)) {
        if ( !Side::hasPrimaryAttorney($side) ) {
          $casesModel->setSideAttorney($side, $user['pkaddressbookid'], false);
        }
        $this->updateServiceLists($side, $user);
      }
      if (User::isAttorney($user)) {

      }
    }

    // $side: side hash or id
    function updateServiceListForPrimaryAttorney($side) {
      $side = is_array($side) ? $side : $this->find($side);
      if ( !$side['primary_attorney_id'] ) {
        return false;
      }

      $this->updateServiceLists($side, $side['primary_attorney_id']);
    }

    function getServiceList($side) {
      $query = $this->queryTemplates['getServiceList'];

      $side = is_array($side) ? $side : $this->find($side);
      if (!$side) { return null; }

      $serviceList = $this->readQuery($query, ['side_id' => $side['id']]);
      foreach($serviceList as &$user) {
        $attorneyId = $user['attorney_id'];

        $clientIds = self::pluck(
          $this->getBy('client_attorney', ['attorney_id' => $attorneyId]),
          'client_id'
        );

        // TODO: maybe solve this with a join query.
        foreach( $clientIds as $clientId ) {
          $user['clients'][] = $this->getBy('clients', ['id' => $clientId], 1);
        }
      }

      return $serviceList;
    }

    protected function getServiceListAttorney($side, $user, $name = null, $email = null, $create = true) {
      global $currentUser;

      $slAttorney = $this->getBy('attorney', [
        'side_id'         => $side['id'],
        'fkaddressbookid' => $user['pkaddressbookid']
      ], 1);

      if ( !$slAttorney && $create) {
        $slAttorneyId = $this->insert('attorney', [
          'uid'             => $this->generateUID('attorney'),
          'case_id'         => $side['case_id'],
          'attorney_name'   => $name,
          'attorney_email'  => $email,
          'fkaddressbookid' => $user['pkaddressbookid'],
          'attorney_type'   => 2,
          'updated_at'      => date('Y-m-d H:i:s'),
          'updated_by'      => $currentUser->id,
          'side_id'         => $side['id']
        ], true);

        if ( (int)$slAttorneyId ) {
          $slAttorney = $this->getBy('attorney', ['id' => $slAttorneyId], 1);
        }
      }

      return $slAttorney;
    }

    // $side:  side hash or id
    // $attorney: user hash or id
    // $clients: client hash array or ids array
    // TODO: this function is getting fat
    function updateServiceListForAttorney($side, $user, $clients, $name = '', $email = '', $overwrite = true) {
      global $currentUser, $usersModel, $clientsModel;

      $side = is_array($side) ? $side : $this->find($side);
      $user = is_array($user) ? $user : $usersModel->find($user);

      $name  = $name  ?: $usersModel->getFullName($user);
      $email = $email ?: $user['email'];

      if ($slAttorney = $this->getServiceListAttorney($side, $user, $name, $email) ) {
        $this->update('attorney',
          ['attorney_name' => $name, 'attorney_email' => $email],
          ['id' => $slAttorney['id']]
        );

        if ($overwrite) { // delete all clients for attorney service list
          $this->deleteBy('client_attorney', ['attorney_id' => $slAttorney['id']]);
        }

        foreach($clients as $client) {
          $client = is_array($client) ? $client : $clientsModel->find($client);
          if ( $client ) {
            $this->insert('client_attorney', [
              'case_id'     => $side['case_id'],
              'attorney_id' => $slAttorney['id'],
              'client_id'   => $client['id'],
              'updated_at'  => date('Y-m-d H:i:s'),
              'updated_by'  => $currentUser->id
            ], true);
          }
        }
      }
    }

    // adds an SL entry to all other sides with this user and it's side's clients
    function updateServiceLists($userSide, $user) {
      global $casesModel;

      $sides   = $casesModel->getSides($userSide['case_id']);
      $clients = $this->getClients($userSide['id']);

      foreach($sides as $otherSide) { // add the attorney to all other sides service list
        if ($userSide['id'] === $otherSide['id']) { continue; }
        $this->updateServiceListForAttorney($otherSide, $user, $clients);
      }
    }

    function removeFromServiceList($side, $user) {
      global $usersModel;

      $side = is_array($side) ? $side : $this->find($side);
      $user = is_array($user) ? $user : $usersModel->find($user);

      return $this->deleteBy('attorney', [
        'fkaddressbookid' => $user['pkaddressbookid'],
        'side_id'         => $side['id']
      ]);
    }

    function getUserServiceListClients($side, $user) {
      global $usersModel;

      $query = $this->queryTemplates['getUserServiceListClients'];

      $side = is_array($side) ? $side : $this->find($side);
      $user = is_array($user) ? $user : $usersModel->find($user);

      return $this->readQuery($query, [
        'side_id' => $side['id'],
        'user_id' => $user['pkaddressbookid']
      ]);
    }

    function usersCount($side, $role = null) {
      $side = is_array($side) ? $side : $this->find($side);

      $users = $this->getAllUsers($side['id']);

      $count = 0;
      foreach($users as $user) {
        $count += $role === null || $user['fkgroupid'] == $role ? 1 : 0;
      }

      return $count;
    }

    function updateCaseData($sideId, $data) {
      $caseData = [];
      foreach(self::CASE_DATA_FIELDS as $field) {
        $caseData[$field] = $data[$field];
      }
      $caseData['masterhead'] = $caseData['masterhead'] ?? "";

      $caseData['normalized_number'] = CaseModel::normalizeNumber($caseData['case_number']);
      $this->updateSide($sideId, $caseData);
    }

    function setPaymentMethod($sideId, $paymentMethodId) {
      return $this->update('sides',
        ['payment_method_id' => $paymentMethodId],
        ['id' => $sideId]
      );
    }

    static function hasPrimaryAttorney($side) {
      global $sidesModel;

      $side = is_array($side) ? $side : $sidesModel->find($side);

      return !!$side['primary_attorney_id'];
    }

    static function caseData($side, $updateId = true) {
      $case = [];
      foreach(self::CASE_DATA_FIELDS as $field) {
        $case[$field] = $side[$field];
      }

      if ($updateId) {
        $case['id'] = $side['case_id'];
      }

      return $case;
    }

    // LEGACY COMPATIBILITY
    // this function will try to replace all case data fields on any structure
    // to the side version of provided  or current user.
    // DESTRUCTIVE : modifies the data param
    static function legacyTranslateCaseData($caseId, &$data, $userId = null) {
      global $sidesModel, $currentUser;

      $userId = $userId ? $userId : $currentUser->id;
      if (!$userId) return $data;

      $side = $sidesModel->getByUserAndCase($userId, $caseId);
      if (!$side) return $data;

      $isCollection = count(
        array_diff(
          self::CASE_DATA_FIELDS,
          array_keys($data)
        )
      ) == count(self::CASE_DATA_FIELDS);

      $caseData = self::caseData($side, false);
      if ($isCollection) {
        foreach($data as &$entry) {
          $entry = array_merge($entry, $caseData);
        }
      }
      else {
        $data = array_merge($data, $caseData);
      }
    }

  }

  $sidesModel = new Side();