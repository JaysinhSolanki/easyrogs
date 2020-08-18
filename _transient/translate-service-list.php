<?php 
  require_once __DIR__ . '/../system/bootstrap.php';

  /**
   * THIS SCRIPT WILL:
   * 1. UPDATE ALL OUTDATED SIDES WITH THE CURRENT CASE'S DATA
   * 2. UPDATE ATTORNEYS FK ADDRESSBOOKID TO POINT TO THE CORRECT USER BY EMAIL
   * 3. MIGRATE THE OLD SERVICE LISTS RELATIONSHIPS TO THE NEW SIDES IMPLEMENTATION.
   */

   $logContext = 'TRANSLATE_SERVICE_LIST';
   $logger = new EasyRogs\Logger(LOGS_DIR, true);
 
   $logger->info("$logContext Starting...");

   $logger->info("$logContext Updating sides case data...");
   $rowsAffected = $sidesModel->writeQuery("
     UPDATE IGNORE sides AS s
       INNER JOIN cases AS c ON c.id = s.case_id
     SET s.case_number = c.case_number,
         s.case_title = c.case_title,
         s.plaintiff = c.plaintiff,
         s.defendant = c.defendant,
         s.trial = c.trial,
         s.discovery_cutoff = c.discovery_cutoff, 
         s.normalized_number = c.normalized_number
     WHERE s.case_number IS NULL
    ");

   $logger->info("$logContext Updating attorney fkaddressbook column...");
   $rowsAffected = $sidesModel->writeQuery("
      UPDATE attorney AS a
        INNER JOIN system_addressbook AS u ON u.email = a.attorney_email
      SET a.fkaddressbookid = u.pkaddressbookid
   ");

   $slAttorneys = $usersModel->getBy('attorney', ['side_id' => null]);
   
   $logger->info("$logContext Found " . count($slAttorneys) . " SL attorneys.");

   foreach($slAttorneys as $slAttorney) {
    $userId = $slAttorney['fkaddressbookid'];
    $caseId = $slAttorney['case_id'];

    $logger->info('----------');
    $logger->info("$logContext Processing SL Attorney:$slAttorney[id] ($slAttorney[attorney_name]), User:$userId, Case:$caseId");

    $clientIds = BaseModel::pluck(
      $clientsModel->getBy('client_attorney', ['attorney_id' => $slAttorney['id']]),
      'client_id'
    );

    $logger->info("$logContext\tFound " . count($clientIds) . " Associated Client IDs: " . json_encode($clientIds));

    $logger->info("$logContext\tWill cleanup SL Clients for Attorney:$slAttorney[id] ($slAttorney[attorney_name]) on Case:$caseId");
    $usersModel->deleteBy('attorney', ['id' => $slAttorney['id']]);

    if ( !$clientIds ) { continue; }

    $sides = $sidesModel->byCaseId($caseId);
    $logger->info("$logContext\tFound " . count($sides) . " Case Sides.");

    foreach($sides as $side) {
      $logger->info("$logContext\t\tProcessing Side:$side[id]");

      $newAttorneyId = $usersModel->insert('attorney', array_merge(
        $slAttorney, [
          'id'      => null,
          'uid'     => $usersModel->generateUID('attorney'),
          'side_id' => $side['id'],
          'updated_by'  => 0,
        ]
      ));
      
      $logger->info("$logContext\t\t\tCreated new SL Attorney:$newAttorneyId for Side:$side[id]");
      $logger->info("$logContext\t\t\tAdding SL clients");
      
      foreach( $clientIds as $clientId ) {
        $clientsModel->insert('client_attorney', [
          'case_id'     => $caseId,
          'attorney_id' => $newAttorneyId,
          'client_id'   => $clientId,
          'updated_by'  => 0,
        ]);

        $logger->info("$logContext\t\t\tAdded SL client: " . json_encode(
          ['case_id' => $caseId,'attorney_id' => $newAttorneyId,'client_id' => $clientId]
        ));
      }

    }    
   }
