<?php
  
  class MeetConfer extends Payable {
   
    /**
     * Returns hash in the form:
     * {
     *   <question_id>: {
     *     question_id: int,
     *     meet_confer_id: int,
     *     body: string
     *   },
     *   ...
     * }
     */
    function getArguments($id, $includeQuestion = false) { global $questionsModel;
      $rows = $this->getBy('meet_confer_arguments', ['meet_confer_id' => $id]);
     
      $arguments = [];
      if ($rows) {
        foreach($rows as $row) {
          $questionId = $row['question_id'];
          $arguments[$questionId] = $row;
          
          if($includeQuestion) {
            $arguments[$questionId]['question'] = $questionsModel->find($questionId);
          }
        }
      }
      
      return $arguments;
    }

    function create($fields) {
      $arguments = $fields['arguments'];
      unset($fields['arguments']);

      $mcId = $this->insert('meet_confers', $fields, true);
      $this->updateArguments($mcId, $arguments);

      return $this->find($mcId);
    }

    function findByResponseId($responseId, $includeArguments = true) {
      $mc = $this->getBy('meet_confers', ['response_id' => $responseId], 1);
      if ($mc && $includeArguments) {
        $mc['arguments'] = $this->getArguments($mc['id']);
      }
      return $mc;
    }

    // id can be null
    function upsert($id, $data) {
      if ($id) {
        $mc = $this->updateById($id, $data, true); // UPDATE IGNORE
      }
      else {
        $mc =  $this->create($data);
      }

      return $mc;
    }
    
    // TODO: move this somewhere else
    function generatePDF($id, $useCache = true) { global $smarty, $responsesModel; 
      if (!$mc = $this->find($id)) {
        $this->logger->error("MEET_CONFER_GENERATE_PDF MC not found ($id)");
        return false;
      }
      if (!$response = $responsesModel->find($mc['response_id'])) { 
        $this->logger->error("MEET_CONFER_GENERATE_PDF Response not found. M&C ID: $id, Response ID: $mc[response_id]");
        return false;
      }

      $pdfFileName = "meet-confer-$id.pdf";
      $pdfFilePath = DOCS_CACHE_DIR . $pdfFileName;

      try {
        if (!$useCache || !file_exists($pdfFilePath)) {
          $smarty->assign([
            'mc'        => $mc,
            'questions' => $response['questions']
          ]);
          $html = $smarty->fetch('meet_confer/pdf.tpl');
    
          $mpdf = new \Mpdf\Mpdf(['tempDir' => TMP_DIR]);
          
          $mpdf->SetHTMLFooter("
            <div style='text-align:center'>
              MEET & CONFER RE: " . strtoupper($mc['subject']) . "
              <br/>
              <small>All rights reserved © 2020AI4Discoverys. U.S. Patent Pending</small>
            </div>
          ");
          $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::DEFAULT_MODE);
          $mpdf->output($pdfFilePath, \Mpdf\Output\Destination::FILE);
        }
      }
      catch(Exception $e) {
        $this->logger->error("MEET_CONFER_GENERATE_PDF Generation Error: " . $e->getMessage());
        return false;
      }

      return $pdfFilePath;
    }

    function updateArguments($id, $arguments) {
      $this->deleteBy('meet_confer_arguments', ['meet_confer_id' => $id]);
      foreach($arguments as $questionId => $argument) {
        if (trim($argument)) { // only store non-emtpy arguments
          $this->insert('meet_confer_arguments', [
            'meet_confer_id' => $id,
            'question_id'    => $questionId,
            'body'           => $argument
          ]);
        }
      }
    }

    // Payable
    static function statementDescriptor($mc) {
      return "Served M&C Letter #$mc[id]";
    }

    function find($id, $includeArguments = true) {
      $mc = $this->getBy( 'meet_confers', ['id' => $id], 1); 
      
      if ($includeArguments) { $mc['arguments'] = $this->getArguments($id); }

      return $mc;
    }

    public function updateById($id, $fields, $ignore = false) {
      if (isset($fields['arguments'])) {
        $arguments = $fields['arguments'];
        unset($fields['arguments']);
      }
      
      try {
        parent::update('meet_confers', $fields, ['id' => $id], $ignore);

        if ($arguments) {
          $this->updateArguments($id, $arguments);
        }
      }
      catch(Exception $e) {
        $this->logger->error("MEET_CONFER_UPDATE_BY_ID Unable to update: " . $e->getMessage());
        return false;
      }

      return $this->find($id);
    }
    
  }

  $meetConferModel = new MeetConfer();
