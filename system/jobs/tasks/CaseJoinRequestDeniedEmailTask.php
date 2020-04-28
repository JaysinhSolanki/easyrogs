<?php
  class CaseJoinRequestDeniedEmailTask extends BaseTask implements Qutee\TaskInterface {
    
    public function run() {
      global $usersModel, $casesModel;

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

      CaseMailer::deniedRequest($requestor, $case, $actionUser);

      $this->log('DONE!');
    }

  }