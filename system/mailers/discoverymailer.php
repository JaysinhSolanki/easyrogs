<?php

  class DiscoveryMailer extends BaseMailer  {
    const CLIENT_VERIFICATION_SUBJECT = '%s - Verification Issue';
    const CLIENT_RESPONSE_SUBJECT     = '%s - Response Request';
    const CLIENT_RESPONDED_SUBJECT    = '%s - Client Response';
    const PROPOUND_SUBJECT            = '%s';
    const MEET_CONFER_SUBJECT         = '%s - Meet & Confer';

    static function clientVerification($discovery) {
      global $logger, $smarty,
              $discoveriesModel,
              $usersModel, $clientsModel, $sidesModel;

      $logContext = 'DISCOVERY_MAILER_CLIENT_VERIFICATION';
      $logParams  = json_encode(['discovery' => $discovery]);

      $discovery = is_array($discovery) ? $discovery : $discoveriesModel->find($discovery);
      if ( !$discovery ) {
        return $logger->error("$logContext Discovery not found. Params: $logParams");
      }
      if ( !$attorney = $usersModel->find($discovery['attorney_id']) ) {
        return $logger->error("$logContext Attorney (id: $discovery[attorney_id]) not found. Params: $logParams");
      }
      if( !$client = $clientsModel->find($discovery['responding']) ) {
        return $logger->error("$logContext Client (id: $discovery[responding]) not found. Params: $logParams");
      }
      if( !$side = $sidesModel->getByClientAndCase($client['id'], $discovery['case_id']) ) {
        return $logger->error("$logContext Side Not Found (client: $client[id], case: $discovery[case_id]) not found. Params: $logParams");
      }

      $smarty->assign([
        'ASSETS_URL' => ASSETS_URL,
        'name'       => $client['client_name'],
        'actionUrl'  => DOMAIN . "discoveryfront.php?uid=$discovery[uid]",
        'actionText' => 'Verify'
      ]);
      $body = $smarty->fetch('emails/discovery-client-verification.tpl');
      $subject = sprintf(self::CLIENT_VERIFICATION_SUBJECT, $side['case_title']);
      $to = $client['client_email'];

      self::sendEmail($to, $subject, $body, $usersModel->getFullName($attorney), $attorney['email']);
    }

    static function clientResponse($discovery, $actionUser, $notes='') {
      global $smarty, $discoveriesModel, $usersModel, $clientsModel, $logger, $sidesModel;

      $logContext = 'DISCOVERY_MAILER_CLIENT_RESPONSE';
      $logParams  = json_encode(['discovery' => $discovery, 'actionUser' => $actionUser]);

      $discovery = is_array($discovery) ? $discovery : $discoveriesModel->find($discovery);
      if ( !$discovery ) {
        return $logger->error("$logContext Discovery not found. Params: $logParams");
      }
      $actionUser = is_array($actionUser) ? $actionUser : $usersModel->find($actionUser);
      if ( !$actionUser ) {
        return $logger->error("$logContext Action User not found. Params: $logParams");
      }
      if( !$client = $clientsModel->find($discovery['responding']) ) {
        return $logger->error("$logContext Client (id: $discovery[responding]) not found. Params: $logParams");
      }
      if( !$side = $sidesModel->getByUserAndCase($actionUser['pkaddressbookid'], $discovery['case_id']) ) {
        return $logger->error("$logContext Side Not Found (client: $client[id], case: $discovery[case_id]) not found. Params: $logParams");
      }

      $smarty->assign([
        'ASSETS_URL'    => ASSETS_URL,
        'name'          => $client['client_name'],
        'discoveryName' => $discoveriesModel->getTitle($discovery),
        'senderEmail'   => $actionUser['email'],
        'senderPhone'   => $actionUser['phone'],
        'masterhead'    => $sidesModel->getMasterHead($side),
        'notes'         => $notes,
        'actionUrl'     => DOMAIN . "discoveryfront.php?uid=$discovery[uid]",
        'actionText'    => 'Respond Now'
      ]);
      $body = $smarty->fetch('emails/discovery-client-response.tpl');
      $subject = sprintf(self::CLIENT_RESPONSE_SUBJECT, $side['case_title']);
      $to = $client['client_email'];
      $logger->debug("$logContext Will send to <$to>. Params: $logParams");
      self::sendEmail($to, $subject, $body, $usersModel->getFullName($actionUser), $actionUser['email']);
    }

    static function clientResponded($discovery,$response) {
      global $logger, $smarty, $usersModel,
              $discoveriesModel, $responsesModel,
              $clientsModel, $sidesModel;

      $logContext = 'DISCOVERY_MAILER_CLIENT_RESPONDED';
      $logParams  = json_encode(['discovery' => $discovery]);

      $discovery = $discoveriesModel->asDiscovery($discovery);
      if ( !$discovery ) {
        return $logger->error("$logContext Discovery not found. Params: $logParams");
      }
      if( !$client = $clientsModel->find($discovery['responding']) ) {
        return $logger->error("$logContext Client (id: $discovery[responding]) not found. Params: $logParams");
      }
      if( !$side = $sidesModel->getByClientAndCase($client['id'], $discovery['case_id']) ) {
        return $logger->error("$logContext Side Not Found (client: $client[id], case: $discovery[case_id]) not found. Params: $logParams");
      }

      $smarty->assign([
        'ASSETS_URL'   => ASSETS_URL,
        'clientName'   => $client['client_name'],
        'responseName' => $responsesModel->getTitle( $response, $discovery )
      ]);
      $subject = sprintf(self::CLIENT_RESPONDED_SUBJECT, $side['case_title']);

      $users = $sidesModel->getUsers($side['id']);
      foreach($users as $user) {
        $smarty->assign('name', $usersModel->getFullName($user));
        $body    = $smarty->fetch('emails/discovery-client-responded.tpl');

        $client['client_email']
          ? self::sendEmail($user['email'], $subject, $body, $client['client_name'], $client['client_email'])
          : self::sendEmail($user['email'], $subject, $body);
      }
    }

    static function propound($discovery, $actionUser, $isResponse, $attachments) {
      global $logger, $smarty,
              $discoveriesModel, $responsesModel,
              $clientsModel, $usersModel, $sidesModel;

      $logContext = 'DISCOVERY_MAILER_PROPOUND';
      $logParams  = json_encode([
        'discovery'  => $discovery,  'actionUser' => $actionUser,
        'isResponse' => $isResponse, 'attachments' => $attachments
      ]);

      $discovery = $discoveriesModel->asDiscovery($discovery);
      if ( !$discovery ) {
        return $logger->error("$logContext Discovery not found. Params: $logParams");
      }
      $actionUser = is_array($actionUser) ? $actionUser : $usersModel->find($actionUser);
      if ( !$actionUser ) {
        return $logger->error("$logContext Action User not found. Params: $logParams");
      }
      if( !$propounding = $clientsModel->find($discovery['propounding']) ) {
        return $logger->error("$logContext Propounding Client (id: $discovery[propounding]) not found. Params: $logParams");
      }
      if ( !$side = $sidesModel->getByUserAndCase($actionUser['pkaddressbookid'], $discovery['case_id']) ) {
        return $logger->error("$logContext Side (user_id: $actionUser[pkaddressbookid], case_id: $discovery[case_id]) not found. Params: $logParams");
      }
      if ( !$serviceList = $sidesModel->getServiceList($side) ) {
        return $logger->warn("$logContext No service list found for Case (id: $discovery[case_id]). Params: $logParams");
      }

      $subject = sprintf(self::PROPOUND_SUBJECT, $side['case_title']);
      $discoveryName = $isResponse
                        ? $responsesModel->getTitle( 0, $discovery )
                        : $discoveriesModel->getTitle( $discovery );

      $body = $smarty->assign([
        'ASSETS_URL'      => ASSETS_URL,
        'masterhead'      => $sidesModel->getMasterHead($side),
        'propoundingName' => $propounding['client_name'],
        'discoveryName'   => $discoveryName,
        'actionUrl'       => DOMAIN,
        'actionText'      => 'Go to AI4Discovery.com'
      ])->fetch('emails/discovery-propound.tpl');

      $to = [];
      foreach($serviceList as $user) {
        if ($user['pkaddressbookid'] == $actionUser['pkaddressbookid']) { continue; }
        $to[] = $user['email'];
      }

      self::sendEmail($to, $subject, $body, $usersModel->getFullName($actionUser), $actionUser['email'], $attachments);
    }

    static function meetConfer($mc, $attachments) {
      global $smarty, $logger, $usersModel,
             $discoveriesModel, $responsesModel,
             $currentUser, $sidesModel;

      $actionUserId     = $currentUser->id;
      $response         = $responsesModel->find($mc['response_id']);
      $discovery        = $discoveriesModel->find($response['fkdiscoveryid']);
      $caseId           = $discovery['case_id'];

      $logContext = 'DISCOVERY_MAILER_MEET_CONFER';
      $logParams  = json_encode([
        'mc'           => $mc,
        '$attachments' => $attachments
      ]);

      if ( !$side = $sidesModel->getByUserAndCase($actionUserId, $caseId) ) {
        return $logger->error("$logContext Side (user_id: $actionUserId, case_id: $caseId) not found. Params: $logParams");
      }
      if ( !$serviceList = $sidesModel->getServiceList($side) ) {
        return $logger->warn("$logContext No service list found for Case (id: $caseId). Params: $logParams");
      }

      $subject = sprintf(self::MEET_CONFER_SUBJECT, $side['case_title']);
      $body = $smarty->fetch('emails/meet-confer.tpl');

      $to = [];
      foreach($serviceList as $user) {
        if ($user['pkaddressbookid'] == $actionUserId) { continue; }
        $to[] = $user['email'];
      }

      self::sendEmail($to, $subject, $body, $usersModel->getFullName($currentUser->user), $currentUser->user['email'], $attachments);
    }
  }
