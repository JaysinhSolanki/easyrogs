<?php

  class DiscoveryMailer extends BaseMailer  {
    const CLIENT_VERIFICATION_SUBJECT = '%s - Verification Issue';
    const CLIENT_RESPONSE_SUBJECT     = '%s - Response Request';
    const CLIENT_RESPONDED_SUBJECT    = '%s - Client Response';
    const PROPOUND_SUBJECT            = '%s';

    static function clientVerification($discovery) {
      global $smarty, $discoveriesModel, $usersModel, $clientsModel, $logger, $casesModel;

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
      if( !$case = $casesModel->find($discovery['case_id']) ) {
        return $logger->error("$logContext Case (id: $discovery[case_id]) not found. Params: $logParams");
      }
      
      $smarty->assign([
        'name'       => $client['client_name'],
        'actionUrl'  => DOMAIN . "discoveryfront.php?uid=$discovery[uid]",        
        'actionText' => 'Verify'
      ]);
      $body = $smarty->fetch('emails/discovery-client-verification.tpl');
      $subject = sprintf(self::CLIENT_VERIFICATION_SUBJECT, $case['case_title']);
      $to = $client['client_email'];

      self::sendEmail($to, $subject, $body, User::getFullName($attorney), $attorney['email']);
    }

    static function clientResponse($discovery, $actionUser) {
      global $smarty, $discoveriesModel, $usersModel, $clientsModel, $logger, $casesModel;

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
      if( !$case = $casesModel->find($discovery['case_id']) ) {
        return $logger->error("$logContext Case (id: $discovery[case_id]) not found. Params: $logParams");
      }
      
      $smarty->assign([
        'name'        => $client['client_name'],
        'senderEmail' => $actionUser['email'],
        'senderPhone' => $actionUser['phone'],
        'masterhead'  => $usersModel->getMasterHead($actionUser),
        'actionUrl'   => DOMAIN . "discoveryfront.php?uid=$discovery[uid]",
        'actionText'  => 'Respond Now'
      ]);
      $body = $smarty->fetch('emails/discovery-client-response.tpl');
      $subject = sprintf(self::CLIENT_RESPONSE_SUBJECT, $case['case_title']);
      $to = $client['client_email'];

      self::sendEmail($to, $subject, $body, User::getFullName($actionUser), $actionUser['email']);
    }

    static function clientResponded($discovery) {
      global $smarty, $discoveriesModel, $clientsModel, $sidesModel, $casesModel, $logger;

      $logContext = 'DISCOVERY_MAILER_CLIENT_RESPONDED';
      $logParams  = json_encode(['discovery' => $discovery]);

      $discovery = is_array($discovery) ? $discovery : $discoveriesModel->find($discovery);
      if ( !$discovery ) {
        return $logger->error("$logContext Discovery not found. Params: $logParams");
      }
      if( !$client = $clientsModel->find($discovery['responding']) ) {
        return $logger->error("$logContext Client (id: $discovery[responding]) not found. Params: $logParams");
      }
      if( !$case = $casesModel->find($discovery['case_id']) ) {
        return $logger->error("$logContext Case (id: $discovery[case_id]) not found. Params: $logParams");
      }
      if( !$side = $sidesModel->getByClientAndCase($client['id'], $discovery['case_id']) ) {
        return $logger->error("$logContext Side Not Found (client: $client[id], case: $discovery[case_id]) not found. Params: $logParams");
      }
      
      $smarty->assign([
        'clientName'    => $client['client_name'],
        'discoveryName' => $discovery['discovery_name'],
        'setNumber'     => $discovery['set_number']
      ]);
      $subject = sprintf(self::CLIENT_RESPONDED_SUBJECT, $case['case_title']);
      $body    = $smarty->fetch('emails/discovery-client-responded.tpl');

      $users = $sidesModel->getUsers($side['id']);
      foreach($users as $user) {
        $client['client_email']
          ? self::sendEmail($user['email'], $subject, $body, $client['client_name'], $client['client_email'])
          : self::sendEmail($user['email'], $subject, $body);
      }
    }

    static function propound($discovery, $actionUser, $isResponse, $attachments) {
      global $smarty, $discoveriesModel, $clientsModel, $usersModel, 
             $casesModel, $logger, $invitationsModel;

      $logContext = 'DISCOVERY_MAILER_PROPOUND';
      $logParams  = json_encode([
        'discovery'  => $discovery,  'actionUser' => $actionUser, 
        'isResponse' => $isResponse, 'attachments' => $attachments
      ]);

      $discovery = is_array($discovery) ? $discovery : $discoveriesModel->find($discovery);
      if ( !$discovery ) {
        return $logger->error("$logContext Discovery not found. Params: $logParams");
      }
      $actionUser = is_array($actionUser) ? $actionUser : $usersModel->find($actionUser);
      if ( !$actionUser ) {
        return $logger->error("$logContext Action User not found. Params: $logParams");
      }
      if( !$case = $casesModel->find($discovery['case_id']) ) {
        return $logger->error("$logContext Case (id: $discovery[case_id]) not found. Params: $logParams");
      }
      if( !$propounding = $clientsModel->find($discovery['propounding']) ) {
        return $logger->error("$logContext Propounding Client (id: $discovery[propounding]) not found. Params: $logParams");
      }
      if ( !$serviceList = $casesModel->getServiceList($discovery['case_id']) ) {
        return $logger->warn("$logContext No service list found for Case (id: $discovery[case_id]). Params: $logParams");
      }

      $subject = sprintf(self::PROPOUND_SUBJECT, $case['case_title']);
      $discoveryName = $isResponse 
                        ? "RESPONSE TO $discovery[discovery_name]" 
                        : $discovery['discovery_name'];
      $discoveryName .= " [Set " . numberTowords($discovery['set_number']) . "]";
      
      $smarty->assign([
        'masterhead'      => $usersModel->getMasterHead($actionUser),
        'propoundingName' => $propounding['client_name'],
        'discoveryName'   => str_replace("set", "Set", ucwords(strtolower($discoveryName)))
      ]);

      foreach($serviceList as $user) {
        if ($user['pkaddressbookid'] == $actionUser['pkaddressbookid']) { continue; }
        
        $isActive = User::isActive($user);
        if ( !$isActive ) {
          $invitation = $invitationsModel->create($user['pkaddressbookid']);
        }
        
        $smarty->assign([
          'name'        => User::getFullName($user),
          'actionUrl'   => $isActive ? DOMAIN : DOMAIN . "signup.php?uid=$invitation[uid]",
          'actionText'  => $isActive ? 'Go to EasyRogs' : 'Signup to EasyRogs'
        ]);
        $body = $smarty->fetch('emails/discovery-propound.tpl');
        $to   = $user['email'];

        self::sendEmail($to, $subject, $body, User::getFullName($actionUser), $actionUser['email'], $attachments);
      }
    }
  }