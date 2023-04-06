<?php
class CaseModel extends BaseModel
{
  function __construct($dbConfig = null)
  {
    parent::__construct($dbConfig);

    $this->queryTemplates = array_merge($this->queryTemplates, [
      'removeClient'       => 'DELETE 
                                 FROM sides_clients
                                 WHERE sides_clients.side_id IN ( 
                                   SELECT id FROM sides WHERE sides.case_id = :case_id
                                 ) AND sides_clients.client_id = :client_id',
      'removeUser' => 'DELETE 
                         FROM sides_users
                         WHERE sides_users.side_id IN ( 
                           SELECT id FROM sides WHERE sides.case_id = :case_id
                         ) AND sides_users.system_addressbook_id = :user_id',

      'getByUser' => 'SELECT c.*
                        FROM cases AS c
                          INNER JOIN sides AS s
                            ON c.id = s.case_id
                          LEFT JOIN sides_users AS su
                            ON su.side_id = s.id
                        WHERE (su.system_addressbook_id = :user_id AND su.active = 1 )
                              OR s.primary_attorney_id = :user_id
                        GROUP BY c.id
                        ORDER BY c.case_title ASC',

      'getAllClients' => 'SELECT c.*
                            FROM clients AS c
                              INNER JOIN sides_clients AS sc
                                ON c.id = sc.client_id
                              INNER JOIN sides AS s
                                ON s.id = sc.side_id
                            WHERE s.case_id = :case_id',

      'getActvCases' => 'SELECT c.* FROM cases AS c 
                          INNER JOIN sides AS s 
                          ON s.case_id = c.id 
                          WHERE s.case_id = :case_id
                          AND c.is_deleted = 0',

      'getActvUserInCase' => 'SELECT COUNT(*) AS `count`
                         FROM sides_users
                         INNER JOIN sides ON sides_users.side_id = sides.id
                         WHERE ( sides_users.system_addressbook_id = :user_id 
                         OR sides.primary_attorney_id = :user_id)
                         AND sides.case_id = :case_id 
                         AND sides.is_deleted = 0',

      'userInCase' => 'SELECT COUNT(*) AS `count`
                         FROM sides_users
                           INNER JOIN sides ON sides_users.side_id = sides.id
                         WHERE ( sides_users.system_addressbook_id = :user_id 
                                 OR sides.primary_attorney_id = :user_id
                               ) AND sides.case_id = :case_id',

      'search' => 'SELECT cases.id, cases.case_title, cases.case_number, cases.county_name
                     FROM cases
                       INNER JOIN sides ON (sides.case_id = cases.id)
                       INNER JOIN sides_clients ON (sides_clients.side_id = sides.id)
                     WHERE LOWER(case_title) LIKE :query
                           OR normalized_number LIKE :case_number
                     GROUP BY cases.id
                     ORDER BY cases.id DESC
                     LIMIT 10',

      'getActvCaseByUser' => 'SELECT c.*
       FROM cases AS c
         INNER JOIN sides AS s
           ON c.id = s.case_id
         LEFT JOIN sides_users AS su
           ON su.side_id = s.id
       WHERE (su.system_addressbook_id = :user_id AND su.active = 1 AND su.is_deleted = 1 AND s.is_deleted = 0)
             OR s.primary_attorney_id = :user_id AND s.is_deleted = 0
       GROUP BY c.id
       ORDER BY c.case_title ASC',

    ]);
    $this->sides = new Side();
    $this->users = new User();
  }

  function find($caseId)
  {
    return $this->getBy('cases', ['id' => $caseId], 1);
  }

  function findByUID($uid)
  {
    return $this->getBy('cases', ['uid' => $uid], 1);
  }

  function setSideMasterHead($side, $masterHead)
  {
    $this->sides->updateSide($side['id'], ['masterhead' => $masterHead]);
  }

  function setSideLetterhead($side, $letterhead)
  {
    $this->sides->updateSide($side['id'], ['letterhead' => $letterhead]);
  }
  function setSideheaderHeight($side, $header_height)
  {
    $this->sides->updateSide($side['id'], ['header_height' => $header_height]);
  }
  function setSideFooterHeight($side, $footer_height)
  {
    $this->sides->updateSide($side['id'], ['footer_height' => $footer_height]);
  }

  function setSideAttorney($side, $attorneyId, $updateTeam = true)
  {
    $attorney = $this->users->find($attorneyId);

    if ($updateTeam) {
      $this->sides->addAttorneyTeam($side['id'], $attorneyId);
    }
    $this->sides->updateSide($side['id'], [
      'primary_attorney_id' => $attorneyId,
      'masterhead'          => $attorney['masterhead']
    ]);
  }

  // TODO: refactor this v
  function updateCase($caseId, $fieldsMap)
  {
    $fieldsMap['normalized_number'] = self::normalizeNumber($fieldsMap['case_number']);
    $this->update('cases', $fieldsMap, ['id' => $caseId]);
  }

  // TODO: this and all called functions should probably be moved to Side->updateCaseData
  function updateSide($side, $caseData, $updateTeam)
  {
    $attorneyChanged   = $caseData['case_attorney']
      && $side['primary_attorney_id'] != $caseData['case_attorney'];
    $masterHeadChanged = $caseData['masterhead']
      && $side['masterhead'] != $caseData['masterhead'];

    $letterHeadChanged = !empty($caseData['letterhead']) ? $caseData['letterhead'] : $side['letterhead'];
    $HeaderHeightChanged = !empty($caseData['header_height']) ? $caseData['header_height'] : $side['header_height'];
    $FooterHeightChanged = !empty($caseData['footer_height']) ? $caseData['footer_height'] : $side['footer_height'];
      
    $side_added   = $caseData['case_attorney']
      && $side['primary_attorney_id'] = $caseData['case_attorney'];

    if ($side_added) {
      $this->setSideAttorney($side, $caseData['case_attorney'], $updateTeam);
    }

    if ($attorneyChanged) {
      $this->setSideAttorney($side, $caseData['case_attorney'], $updateTeam);
    }
    if ($masterHeadChanged) {
      $this->setSideMasterHead($side, $caseData['masterhead']);
    }
    if ($letterHeadChanged) {
      $this->setSideLetterhead($side, $caseData['letterhead']);
    }
    if ($HeaderHeightChanged) {
      $this->setSideHeaderHeight($side, $caseData['header_height']);
    }
    if ($FooterHeightChanged) {
      $this->setSideFooterHeight($side, $caseData['footer_height']);
    }
  }

  function removeClient($caseId, $clientId)
  {
    $query = $this->queryTemplates['removeClient'];
    $this->writeQuery($query, [
      'case_id' => $caseId,
      'client_id' => $clientId
    ]);

    $this->sides->cleanupCase($caseId);
  }

  function getClients($caseId)
  {
    global $currentUser;

    $userSide = $this->sides->getByUserAndCase($currentUser->id, $caseId);

    return $this->sides->getClients($userSide['id']);
  }

  function getUsers($caseId)
  {
    global $currentUser;

    $sides = new Side();
    $userSide = $sides->getByUserAndCase($currentUser->id, $caseId);
    $users = $sides->getUsers($userSide['id']);
    $primaryAttorney = $sides->getPrimaryAttorney($userSide['id']);


    if ($primaryAttorney && !User::inCollection($primaryAttorney, $users)) {

      $users[] = array_merge(
        User::publishable($primaryAttorney),
        ['is_primary' => 'true']
      );
    }


    return $users;
  }

  function getActvUsers($caseId)
  {
    global $currentUser;

    $sides = new Side();
    $userSide = $sides->getByUserAndCase($currentUser->id, $caseId);
    $users = $sides->getActvUsers($userSide['id']);

    $primaryAttorney = $sides->getPrimaryAttorney($userSide['id']);


    if ($primaryAttorney && !User::inCollection($primaryAttorney, $users)) {

      $users[] = array_merge(
        User::publishable($primaryAttorney),
        ['is_primary' => 'true']
      );
    } else if ($primaryAttorney && User::inCollection($primaryAttorney, $users)) {
      $usrs = [];
      foreach ($users as $usr) {
        if ($primaryAttorney['pkaddressbookid'] != $usr['pkaddressbookid']) {
          array_push($usrs, $usr);
        }
      }
      $usrs[] = array_merge(
        User::publishable($primaryAttorney),
        ['is_primary' => 'true']
      );
      $users = $usrs;
    }


    return $users;
  }


  function getUsersFlag($caseId)
  {
    global $currentUser;
    $sides = new Side();
    $userSide = $sides->getByUserAndCase($currentUser->id, $caseId);
    $users = $sides->getUsersFlag($userSide['id']);
    $primaryAttorney = $sides->getPrimaryAttorney($userSide['id']);
    if ($primaryAttorney && !User::inCollection($primaryAttorney, $users)) {
      $users[] = array_merge(
        User::publishable($primaryAttorney),
        ['is_primary' => 'true']
      );
    }
    return $users;
  }



  // TODO: solve with query
  function usersCount($case, $role = null)
  {
    $caseId = is_array($case) ? $case['id'] : $case;
    $sides = $this->sides->byCaseId($caseId);
    $count = 0;
    foreach ($sides as $side) {
      $count += $this->sides->usersCount($side, $role);
    }
    return $count;
  }

  function getByUser($userId)
  {
    $query = $this->queryTemplates['getByUser'];
    return $this->readQuery($query, ['user_id' => $userId]);
  }

  function getActvCaseByUser($userId)
  {
    $query = $this->queryTemplates['getActvCaseByUser'];
    return $this->readQuery($query, ['user_id' => $userId]);
  }

  function getActvCases($caseId)
  {
    $query = $this->queryTemplates['getActvCases'];
    return $this->readQuery($query, ['case_id' => $caseId]);
  }

  function getAllClients($caseId)
  {
    $query = $this->queryTemplates['getAllClients'];
    return $this->readQuery($query, ['case_id' => $caseId]);
  }

  function getActvUserInCase($caseId, $userId)
  {
    $query = $this->queryTemplates['getActvUserInCase'];

    return $this->readQuery($query, [
      'user_id' => $userId,
      'case_id' => $caseId
    ])[0]['count'] > 0;
  }

  function removeUser($caseId, $userId, $cleanupSides = false)
  {
    $query = $this->queryTemplates['removeUser'];
    $this->writeQuery($query, [
      'case_id' => $caseId,
      'user_id' => $userId
    ]);
    if ($cleanupSides) {
      $this->sides->cleanupCase($caseId);
    }
  }

  function getByNumber($number, $requireClients = true)
  {
    $number = self::normalizeNumber($number);
    $sides = $this->getBy('sides', ['normalized_number' => $number]);

    $case = null;

    if ($requireClients) {
      foreach ($sides as $side) {
        if ($this->getActvCases($side['case_id'])) {
          $case = Side::caseData($side);
          break;
        };
      }
    } elseif ($sides) {
      $case = $this->find($sides[0]['case_id']);
    }
    return $case;
  }

  function userInCase($caseId, $userId)
  {
    $query = $this->queryTemplates['userInCase'];

    return $this->readQuery($query, [
      'user_id' => $userId,
      'case_id' => $caseId
    ])[0]['count'] > 0;
  }

  function search($term)
  {
    $query = $this->queryTemplates['search'];
    return $this->readQuery($query, [
      'query'       => "%" . strtolower($term) . "%",
      'case_number' => "%" . self::normalizeNumber($term) . "%"
    ]);
  }

  // WARNING!! with force = true this will delete all sides and associated data from a case,
  // by default it only does the cleanup if the case is a draft.
  function cleanupSides($case, $force = false)
  {
    $case = is_array($case) ? $case : $this->find($case);
    if ($case['is_draft'] || $force) {
      $this->deleteBy('sides', ['case_id' => $case['id']]);
    }
  }

  function getSides($caseId)
  {
    return $this->getBy('sides', ['case_id' => $caseId]);
  }

  function getDraft()
  {
    global $currentUser;

    $case = $this->getBy('cases', [
      'attorney_id' => $currentUser->id,
      'is_draft'    => 1
    ], 1);

    if (!$case) {
      $caseId = $this->insert('cases', [
        'attorney_id'     => $currentUser->id,
        'is_draft'        => 1,
        'allow_reminders' => 1,
        'uid'             => $this->generateUID('cases')
      ]);
      $case = $this->find($caseId);
    }
    return $case;
  }

  static function normalizeNumber($number)
  {
    return strtolower(preg_replace('/[^A-Za-z0-9]/', '', $number));
  }

  // Update Lettercase Field
  function updateLetterhead($caseId, $fieldsMap)
  {
      $this->update(
          'cases',
          ['letterhead' => $fieldsMap],
          ['id' => $caseId]
        );
  }

    // Update Header Height Field
    function updateHeaderHeight($caseId, $fieldsMap)
    {
        $this->update(
            'cases',
            ['header_height' => $fieldsMap],
            ['id' => $caseId]
          );
    }

      // Update Footer Height Field
  function updateFooterHeight($caseId, $fieldsMap)
  {
      $this->update(
          'cases',
          ['footer_height' => $fieldsMap],
          ['id' => $caseId]
        );
  }
}

$casesModel = new CaseModel();
