<?php
  class CaseJoinRequestEmailTask extends BaseTask implements Qutee\TaskInterface {

    public function run() {
      $userId = $this->data['user_id'];
      $caseId = $this->data['case_id'];
      
      $this->log( "Will run with User: $userId, Case: $caseId" );
      
      CaseMailer::joinRequest($userId, $caseId);

      $this->log('DONE!');
    }

  }