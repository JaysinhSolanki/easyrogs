<?php
  class CaseJoinRequestGrantedEmailTask extends BaseTask implements Qutee\TaskInterface {

    public function run() {
      global $smarty, $usersModel, $casesModel;

      $userId       = $this->data['user_id'];
      $caseId       = $this->data['case_id'];
      $actionUserId = $this->data['action_user_id'];

      if ( !$actionUser = $usersModel->find($actionUserId) ) { 
        $this->log( "WARNING: Action User not found ($userId)." ); 
      }
      if ( !$requestor = $usersModel->find($userId) ) { 
        return $this->logError( "Requestor User not found ($userId)." ); 
      }
      if ( !$case = $casesModel->find($caseId) ) {
        return $this->logError( "Case not found ($caseId)." );
      }
      
      $this->log( "Will run with User: $userId, Case: $caseId, Action User: $actionUserId" );      
      
      $smarty->assign([
        'requestorName'  => User::getFullName($requestor),
        'actionUserName' => $actionUser ? User::getFullName($actionUser) : 'a team member',
        'caseName'       => $case['case_title']
      ]);
      $body    = $smarty->fetch('emails/case-join-request-granted.tpl');
      $subject = "Join Request Granted - $case[case_title]";
      $toEmail = $requestor['email'];
      
      $this->log("Will send email to <$toEmail>");

      send_email($toEmail, $subject, $body);

      $this->log('DONE!');
    }

  }