<?php

  class CaseMailer extends BaseMailer  {
    const JOIN_REQUEST_SUBJECT    = '%s - Join Request';
    const GRANTED_REQUEST_SUBJECT = '%s - Join Request Granted';
    const DENIED_REQUEST_SUBJECT  = '%s - Join Request Denied';

    static function joinRequest($requestor, $case) {
      global $sidesModel, $smarty, $usersModel, $casesModel, $logger;
      
      $logContext = 'CASE_MAILER_JOIN_REQUEST';
      $logParams  = json_encode(['requestor' => $requestor, 'case' => $case]);

      $requestor = is_array($requestor) ? $requestor : $usersModel->find($requestor);
      if ( !$requestor ) {
        return $logger->error("$logContext Requestor User not found. Params: $logParams");
      }
      
      $case = is_array($case) ? $case : $casesModel->find($case);
      if ( !$case ) {
        return $logger->error("$logContext Case not found. Params: $logParams");
      }
      
      if ( !$side = $sidesModel->getByUserAndCase($requestor['pkaddressbookid'], $case['id']) ) {
        return $logger->error("$logContext Side not found. Params: $logParams");
      }

      $smarty->assign([
        'requestorName'  => User::getFullName($requestor),
        'requestorEmail' => $requestor['email'],
        'requestorFirm'  => $requestor['companyname'],
        'caseName'       => $case['case_title'],
      ]);

      $subject = sprintf(self::JOIN_REQUEST_SUBJECT, $case['case_title']);

      if ($sideUsers = $sidesModel->getAllUsers($side['id'])) {
        foreach($sideUsers as $recipient) {
          if ($recipient['pkaddressbookid'] != $requestor['pkaddressbookid']) { // not requesting user
            $token = "$case[uid]-$requestor[uid]-$recipient[uid]"; // TODO: use JWT
            $smarty->assign([
              'recipientName' => User::getFullName($recipient),
              'grantUrl'      => ROOTURL . "system/application/get-grant-join-case.php?token=$token",
              'denyUrl'       => ROOTURL . "system/application/get-deny-join-case.php?token=$token"
            ]);

            $body = $smarty->fetch('emails/case-join-request.tpl');
            $to   = $recipient['email'];
            
            parent::sendEmail($to, $subject, $body);
          }
        }
      }
    }

    static function grantedRequest($requestor, $case, $actionUser) {
      global $smarty, $usersModel, $casesModel, $logger;

      $logContext = 'CASE_MAILER_GRANTED_REQUEST';
      $logParams  = json_encode([
        'requestor'  => $requestor, 
        'case'       => $case, 
        'actionUser' => $actionUser
      ]);

      $actionUser = $actionUser ? (is_array($actionUser) ? $actionUser : $usersModel->find($actionUser)) : null;
      if ( !$actionUser ) {
        $logger->warn("$logContext Sending without action user. Params: $logParams");
      }

      $requestor = is_array($requestor) ? $requestor : $usersModel->find($requestor);
      if ( !$requestor ) {
        return $logger->error("$logContext Requestor User not found. Params: $logParams");
      }

      $case = is_array($case) ? $case : $casesModel->find($case);
      if ( !$case ) {
        return $logger->error("$logContext Case not found. Params: $logParams");
      }

      $smarty->assign([
        'requestorName'  => User::getFullName($requestor),
        'actionUserName' => $actionUser ? User::getFullName($actionUser) : 'a team member',
        'caseName'       => $case['case_title']
      ]);
      $body    = $smarty->fetch('emails/case-join-request-granted.tpl');
      $subject = sprintf(self::GRANTED_REQUEST_SUBJECT, $case['case_title']);
      $to      = $requestor['email'];
      
      parent::sendEmail($to, $subject, $body);
    }

    static function deniedRequest($requestor, $case, $actionUser) {
      global $smarty, $usersModel, $casesModel, $logger;

      $logContext = 'CASE_MAILER_DENIED_REQUEST';
      $logParams  = json_encode([
        'requestor'  => $requestor, 
        'case'       => $case, 
        'actionUser' => $actionUser
      ]);

      $actionUser = $actionUser ? (is_array($actionUser) ? $actionUser : $usersModel->find($actionUser)) : null;
      if ( !$actionUser ) {
        $logger->warn("$logContext Sending without action user. Params: $logParams");
      }

      $requestor = is_array($requestor) ? $requestor : $usersModel->find($requestor);
      if ( !$requestor ) {
        return $logger->error("$logContext Requestor User not found. Params: $logParams");
      }

      $case = is_array($case) ? $case : $casesModel->find($case);
      if ( !$case ) {
        return $logger->error("$logContext Case not found. Params: $logParams");
      }

      $smarty->assign([
        'requestorName'  => User::getFullName($requestor),
        'actionUserName' => $actionUser ? User::getFullName($actionUser) : 'a team member',
        'caseName'       => $case['case_title']
      ]);
      $body    = $smarty->fetch('emails/case-join-request-denied.tpl');
      $subject = sprintf(self::DENIED_REQUEST_SUBJECT, $case['case_title']);
      $to      = $requestor['email'];
      
      parent::sendEmail($to, $subject, $body);
    }

  }