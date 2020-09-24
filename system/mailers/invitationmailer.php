<?php

  class InvitationMailer extends BaseMailer  {
    const CASE_INVITE_SUBJECT = 'Invitation to Join %s';
    const TEAM_INVITE_SUBJECT = "Invitation to Join %s's Team";
    const ACTION_TEXT         = 'Join';

    static function caseInvite($invitee, $sender, $case) {
      global $logger, $smarty, $usersModel, $invitationsModel, $casesModel, $sidesModel;

      if (!$invitee = (is_array($invitee) ? $invitee : $usersModel->findActive($invitee)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Invitee User not found - ' . json_encode($invitee));
      }
      if ( !$sender = (is_array($sender) ? $sender : $usersModel->findInactive($sender)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Sender User not found - ' . json_encode($sender));
      }
      if( !is_array($case) ) { // if $case is just the id, load the real thing
          $case = $casesModel->find($case);
      }
      $caseId = $case['id'];
      if ( !$side = $sidesModel->getByUserAndCase($sender['pkaddressbookid'], $caseId) ) {
        return $logger->error("INVITATION_MAILER_INVITE Side Not Found (user: $sender[pkaddressbookid], case: $caseId)");
      }
      $primaryAttorney = $sidesModel->getPrimaryAttorney($side['id']);
      if ( !is_array($primaryAttorney) ) {
        return $logger->error('LC for this case NOT FOUND - ' . json_encode($side));
      }

      $invitation = $invitationsModel->create($invitee['pkaddressbookid']);
      $smarty->assign([
        'ASSETS_URL'  => ASSETS_URL,
        'name'        => $usersModel->getFullName($invitee),
        'senderName'  => $usersModel->getFullName($sender),
        'senderEmail' => $sender['email'],
        'senderFirm'  => $primaryAttorney['companyname'],
        'caseName'    => $side['case_title'] ?: $case['case_title'],
        'actionUrl'   => $invitation['link'],
        'actionText'  => self::ACTION_TEXT
      ]);
      $body    = $smarty->fetch('emails/case-invite.tpl');
      $to      = $invitee['email'];
      $subject = sprintf(self::CASE_INVITE_SUBJECT, $side['case_title']);

      parent::sendEmail($to, $subject, $body);
    }

    static function teamInvite($invitee, $sender) {
      global $logger, $smarty, $usersModel, $invitationsModel;

      if (!$invitee = (is_array($invitee) ? $invitee : $usersModel->findActive($invitee)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Invitee User not found - ' . print_r($invitee, true));
      }
      if ( !$sender = (is_array($sender) ? $sender : $usersModel->findInactive($sender)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Sender User not found - ' . print_r($sender, true));
      }

      $invitation = $invitationsModel->create($invitee['pkaddressbookid']);
      $smarty->assign([
        'ASSETS_URL'  => ASSETS_URL,
        'name'        => $usersModel->getFullName($invitee),
        'senderName'  => $usersModel->getFullName($sender),
        'senderEmail' => $sender['email'],
        'senderFirm'  => $sender['companyname'],
        'actionUrl'   => $invitation['link'],
        'actionText'  => self::ACTION_TEXT
      ]);
      $body    = $smarty->fetch('emails/team-invite.tpl');
      $to      = $invitee['email'];
      $subject = sprintf(self::TEAM_INVITE_SUBJECT, $usersModel->getFullName($sender));

      parent::sendEmail($to, $subject, $body);
    }
  }