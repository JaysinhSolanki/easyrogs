<?php
  class MeetConferController extends BaseController {
    private $mc = [];

    const REQUIRED_FIELDS = [
      'masterhead', 'subject', 'attorney_masterhead', 'intro',
      'arguments', 'conclusion', 'signature', 'response_id'
    ];

    function show() {
      global $smarty, $responsesModel, $discoveriesModel,
             $sidesModel, $currentUser, $usersModel, $meetConferModel;
      
      $responseId = @$_GET['response_id'];
      
      if (!$responseId) { return HttpResponse::malformed(); }
      if (!$response = $responsesModel->find($responseId)) {
        return HttpResponse::notFound('Response not found');
      }

      $mc               = $meetConferModel->findByResponseId($responseId);
      $discoveryId      = $response['fkdiscoveryid'];
      $discovery        = $discoveriesModel->find($discoveryId);
      $respondingUserId = $discovery['responding'];
      $caseId           = $discovery['case_id'];

      $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);
      if (!$currentSide) {
        return HttpResponse::unauthorized('User is not on this case.');
      }
      $opposingSide = $sidesModel->getByUserAndCase($respondingUserId, $caseId);
      if ($currentSide == $opposingSide) {
        return HttpResponse::conflict('Opposing and current side are the same.');
      }

      if (!$attorney = $sidesModel->getPrimaryAttorney($currentSide['id'])) {
        $attorney = $currentUser->id;
      }
      if (!$opposingAttorney = $sidesModel->getPrimaryAttorney($opposingSide['id'])) {
        $opposingAttorney = $usersModel->find($respondingUserId);
      }

      $smarty->assign([
        'mc'                  => $mc,
        'attorney'            => $attorney,
        'opposingAttorney'    => $opposingAttorney,
        'currentUser'         => $currentUser->user,
        'discoveryTitle'      => Discovery::getTitle($discovery['discovery_name'], $discovery['set_number']),
        'discovery'           => $discovery,
        'questions'           => $response['questions'],
        'response'            => $response,
        'side'                => $currentSide,
        'masterhead'          => trim(($mc ? $mc['masterhead']          : $currentSide['masterhead'])),
        'attorney_masterhead' => trim(($mc ? $mc['attorney_masterhead'] : $opposingAttorney['masterhead'])),
      ]);
      $smarty->display('meet_confer/show.tpl');
    }

    function save() { global $meetConferModel;
      $id = @$_POST['id'];

      if (!$this->readAndValidateMCData()) {
        return HttpResponse::malformed('All fields are required and at least one M&C argument.');
      }

      try {
        $mc = $meetConferModel->upsert($id, $this->mc);
      }
      catch(Exception $e) {
        $this->logger->error('MEET_CONFER_CONTROLLER_SAVE Unable to upsert M&C: ' . $e->getMessage());
        return HttpResponse::unprocessable('Sorry, we were unable to save the M&C Letter, please check your input.');
      }

      return HttpResponse::successPayload($mc);
    }

    // PDF document download (USES CACHE)
    function pdf() { global $meetConferModel;
      if (!$id = @$_GET['id']) { return HttpResponse::malformed('ID is required'); }
      
      if( !$pdfFilePath = $meetConferModel->generatePDF($id)) {
        return HttpResponse::notFound();
      }
      
      header("Content-Type: application/octet-stream");
      header('Content-Disposition: attachment; filename="easyrogs-meet-and-confer-' . $id . '.pdf"');
      header("Content-Length: " . filesize($pdfFilePath));
      readfile($pdfFilePath);
    }

    function serve() { 
      global $currentUser, $meetConferModel, $responsesModel, 
             $discoveriesModel, $sidesModel, $usersModel;
      
      if (!$id = @$_POST['id']) { return HttpResponse::malformed('ID is required.'); }
      if (!$mc = $meetConferModel->find($id)) { return HttpResponse::notFound(); }
      if (!$pdfFilePath = $meetConferModel->generatePDF($id, false)) {
        return HttpResponse::unprocessable('Sorry, we were unable to find or create the PDF attachment. If the problem persist, please contact us.');
      }
      
      $response        = $responsesModel->find($mc['response_id']);
      $discovery       = $discoveriesModel->find($response['fkdiscoveryid']);
      $currentSide     = $sidesModel->getByUserAndCase($currentUser->id, $discovery['case_id']);
      $primaryAttorney = $sidesModel->getPrimaryAttorney($currentSide['id']);
          
      if ($meetConferModel->isPaid($mc['id']) || User::hasCredits($primaryAttorney)) {
        try {
          DiscoveryMailer::meetConfer($mc, [[
            'path'     => $pdfFilePath,
            'filename' => 'Meet & Confer Letter.pdf'
          ]]);
         
          $meetConferModel->updateById($id, [
            'served'    => true,
            'served_at' => date('Y-m-d H:i:s')
          ]);

          if ( User::hasCredits($primaryAttorney) ) {
            $usersModel->redeemCredits($primaryAttorney);
          }
  
          return HttpResponse::success();
        }
        catch( Exception $e) {
          $this->logger->error('MEET_CONFER_CONTROLLER_SERVE Unable to serve: ' . $e->getMessage());
          return HttpResponse::unprocessable('Sorry, we were unable to serve the M&C Letter at this time. If the problem persist, please contact us.');
        }
      }
      else {
        return HttpResponse::paymentRequired();
      }
    }

    private function readAndValidateMCData() {
      $valid = true;
      foreach(self::REQUIRED_FIELDS as $field) {
        if (!$valid = $valid && @$_POST[$field]) break;
        $this->mc[$field] = is_array($_POST[$field]) ? $_POST[$field] : trim($_POST[$field]);
      }
      return $valid;
    }
  }