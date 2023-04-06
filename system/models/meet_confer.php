<?php

// Include the main TCPDF library (search for installation path).
require_once(__DIR__."/../library/tcpdf/tcpdf_include.php");
//require_once(__DIR__."/../library/tcpdf/tcpdf.php");
require_once(__DIR__."/../library/tcpdf/fpdf/src/autoload.php");
  
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
    function generatePDF($id, $useCache = x) { global $smarty, $responsesModel, $usersModel, $currentUser, $clientsModel, $meetConferModel, $discoveriesModel, $sidesModel; 
      if (!$mc = $this->find($id)) {
        $this->logger->error("MEET_CONFER_GENERATE_PDF MC not found ($id)");
        return false;
      }
      if (!$response = $responsesModel->find($mc['response_id'])) { 
        $this->logger->error("MEET_CONFER_GENERATE_PDF Response not found. M&C ID: $id, Response ID: $mc[response_id]");
        return false;
      }

      $rand = rand();
      $pdfFileName = "meet-confer-$id-$rand.pdf";
      $pdfFilePath = DOCS_CACHE_DIR . $pdfFileName;

        $createdUser        =   $usersModel->find($response['created_by']);
        
        $discoveryId        =   $response['fkdiscoveryid'];
        $discovery          =   $discoveriesModel->find($discoveryId);
        $client             =   $clientsModel->find($discovery['responding']);
        $caseId             =   $client['case_id'];
        $primary_case_id    =   $client['primary_attorney_id'];
        $currentSide        =   $sidesModel->getByUserAndCase($currentUser->id, $caseId);
        
        // Get Case Attorney Profile Details
        $attorney_details               =   $usersModel->find($currentSide['primary_attorney_id']);
        $attorney_profile_letterhead    =   $attorney_details['letterhead'];
        $attorney_profile_header_height =   !empty($attorney_details['header_height']) ? (int) $attorney_details['header_height'] : 200;
        $attorney_profile_footer_height =   !empty($attorney_details['footer_height']) ? (int) $attorney_details['footer_height'] : 200;        

        // Get Letterhead PDF Name
        $letter_head                =   ($currentSide['letterhead']) ? $currentSide['letterhead'] : $attorney_profile_letterhead;        
        $header_height                =   ($currentSide['header_height']) ? (int) $currentSide['header_height'] : $attorney_profile_header_height;
        $footer_height                =   ($currentSide['footer_height']) ? (int) $currentSide['footer_height'] : $attorney_profile_footer_height;

        // echo "<pre>";print_r($attorney_details);
        // echo "<pre>";print_r($currentSide);        
        // exit;

        $upload_letterhead_path 	=	__DIR__."/../uploads/profile-letters";

        $case_letter_path 		    =	$upload_letterhead_path."/".$letter_head;

        $pixel          =   0.2645833;
        $header_space   =   $header_height * $pixel;    
        $footer_space   =   $footer_height * $pixel;

        $total_header_space   =    round( ( $header_space / 2 ) + 10 ) ;
        $total_footer_space   =    round( ( $footer_space / 2 ) + 40) ;
       //echo round($total_footer_space);exit;

      try {
            if (!$useCache || !file_exists($pdfFilePath)) {
            $smarty->assign([
                'mc'        => $mc,
                'letterhead' => $createdUser['letterhead'],
                'questions' => $response['questions']
            ]);

            $htmlContent = $smarty->fetch('meet_confer/pdf.tpl');
            //$html = $smarty->fetch('meet_confer/pdf.tpl');
            //echo "<pre>";print_r($htmlContent);exit;

            // Set some content to print
            $html   =   '<html>
                            <head>
                            <style>
                            body{margin:0; line-height: 1;}p{margin:0; padding:0;}span{margin:0; padding:0;display: block;}.number-heading{text-decoration:underline; font-size:16px;}.container{padding-right:0;padding-left:0}.contents{margin:0 10px 0 5px}.er-mc-body{cursor:default;}.er-mc-body textarea{overflow:auto;min-height:11em;border:none;font-family:inherit;font-size:inherit;outline:0;border-bottom:1px solid #eee;background-image:url(../images/pencil.png);background-repeat:no-repeat;background-size:10px;background-position:top right}.er-mc-body textarea:focus,.er-mc-body textarea:hover{background-color:#fafafa;background-image:none}.er-mc-masterhead{text-align:center;width:20em;margin:auto;display:block;font-size:17px!important;font-weight:700;background-color:#fafafa;padding:5px;border-radius:5px}.er-mc-date{}.er-mc-attorney-masterhead{width:20em}.er-mc-subject{margin:1em 0;font-weight:700}.er-mc-intro{width:100%;}.er-mc-response-question{}.er-mc-response-question-number{font-size:16px;font-weight:700;display:block;border-radius:3px;margin-bottom:10px;text-decoration:underline}.er-mc-toggle-question{cursor:pointer;z-index:0;outline:0!important}.er-mc-meet-confer .heading,.er-mc-response-main-question .heading,.er-mc-response-main-question-answer .heading{font-weight:700}.er-mc-response-sub-question{margin-left:15px}.er-mc-response-sub-question-answer{margin-left:30px;font-size:13px}.er-mc-meet-confer-body{width:100%;}.er-mc-conclusion{page-break-before:always}.er-mc-conclusion .heading{text-align:center;font-weight:700;text-decoration:underline;font-size:larger; line-height:22px;}.er-mc-conclusion-body{width:100%;min-height:260px!important}.er-mc-signature{width:100%;height:0}a.er-mc-toggle-question.active,a.er-mc-toggle-question.active:hover,a.er-mc-toggle-question.focus{background-color:#fff;border-color:#e4e5e7;color:#6a6c6f}.er-mc-action-bar{position:fixed;z-index:1000;bottom:0;margin:0}.er-mc-action-bar .container{width:calc(100vw - 280px + 4px);width:calc(var(--body-width) - 280px + 4px);margin:0;background-color:#fff;border:1px solid #e4e5e7}.footer{display:none!important}@media print{body,p,span{text-align:justify}body{font-family:"Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif}p{padding:0;margin:0}.er-mc-answer,.er-mc-question-title{margin-bottom:5px}}
                            </style>
                            </head>
                            <body>'.$htmlContent.'
                            </body></html>';
            //print_r($html);exit;
         
            // if( ( !empty($currentSide['letterhead']) && isset($currentSide['letterhead']) ) || ( !empty($primary_attorney_profile_letterhead) && isset($primary_attorney_profile_letterhead) ) ){
            
            if( file_exists($case_letter_path) && !empty($letter_head) ){
              
                // Get Letterhead PDF Name
                //$letter_head    =   ($currentSide['letterhead']) ? $currentSide['letterhead'] : $primary_attorney_profile_letterhead;

                $letter_head_filepath = __DIR__."/../uploads/profile-letters/".$letter_head;

                // create new PDF document
                $pdf = new \setasign\Fpdi\TcpdfFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('PACRA');

                // remove default header/footer
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT, $total_header_space, PDF_MARGIN_RIGHT, false);

                //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // set font
                $pdf->SetFont('times', '', 12);

                // set auto page breaks
                //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                $pdf->SetAutoPageBreak(TRUE, $total_footer_space);

                //Merging of the existing PDF pages to the final PDF
                $pageCount = $pdf->setSourceFile($letter_head_filepath);
                //echo "<pre>";print_r($pageCount);exit;

                $pdf->SetFont('helvetica','',10);

                // set default font subsetting mode
                $pdf->setFontSubsetting(true);

                for ($i = 1; $i <= $pageCount; $i++) {

                    // import our page
                    $templateId = $pdf->importPage($i);
                    
                    $size = $pdf->getTemplateSize($templateId);
                    //print_r($size);exit;
                    
                    // Add our new page
                    $pdf->AddPage();
                    $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
                    $pdf->setCellPaddings(1, 1, 1, 1);
                    $pdf->setCellMargins(0, 0, 0, 0);

                    // set auto page breaks
                    //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                    //$pdf->SetFillColor(255, 255, 255, 255);
                    //$pdf->SetY(26);
                    //$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'DF', "");

                    $pdf->useTemplate($templateId);

                    // writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
                    $pdf->writeHTML($html, true, 0, true, 0);

                    // set auto page breaks
                    $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

                    // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
                    //$pdf->writeHTMLCell(250, 250, 215.9, 110, $html, 0, 0, 0,true, '', true);
                    //$pdf->writeHTMLCell(202, 0, 0, 70, trim($html), 0, 2, false, true, 'L', false, 1);

                    //$pdf->Ln();
                }
                // Close and output PDF document
                // This method has several options, check the source code documentation for more information.
                $pdf->Output($pdfFilePath, 'F');

            } else {

                $mpdf = new \Mpdf\Mpdf(['tempDir' => TMP_DIR]);

                $html = $smarty->fetch('meet_confer/pdf_without_letterhead.tpl');
            
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
