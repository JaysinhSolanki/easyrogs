<?php
  class CaseJoinRequestEmailTask extends BaseTask implements Qutee\TaskInterface {

    public function run() {
      global $sidesModel, $smarty, $usersModel, $casesModel;

      $userId = $this->data['user_id'];
      $caseId = $this->data['case_id'];
      
      if ( !$requestor = $usersModel->find($userId) ) { 
        return $this->logError( "Requestor User not found ($userId)." ); 
      }
      if ( !$case = $casesModel->find($caseId) ) {
        return $this->logError( "Case not found ($caseId)." );
      }
      if ( !$side = $sidesModel->getByUserAndCase($userId, $caseId) ) {
        return $this->logError( "Side not found (user: $userId, case: $caseId)." );
      }

      $this->log( "Will run with User: $userId, Case: $caseId" );

      $smarty->assign([
        'requestorName'  => User::getFullName($requestor),
        'requestorEmail' => $requestor['email'],
        'requestorFirm'  => $requestor['companyname'],
        'caseName'       => $case['case_title'],
      ]);
      $subject = "Join Request - $case[case_title]";

      if ($sideUsers = $sidesModel->getAllUsers($side['id'])) {
        foreach($sideUsers as $recipient) {
          if ($recipient['pkaddressbookid'] != $userId) { // not requesting user
            $token = "$case[uid]-$requestor[uid]-$recipient[uid]"; // TODO: use JWT
            $smarty->assign([
              'recipientName' => User::getFullName($recipient),
              'grantUrl'      => ROOTURL . "system/application/get-grant-join-case.php?token=$token",
              'denyUrl'       => ROOTURL . "system/application/get-deny-join-case.php?token=$token"
            ]);

            $body    = $smarty->fetch('emails/case-join-request.tpl');
            $toEmail = $recipient['email'];
            
            $this->log("Will send email to <$toEmail>");

            send_email($toEmail, $subject, $body);	
          }
        }
      }

      $this->log('DONE!');
    }

  }