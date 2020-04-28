<?php

  class InvitationMailer extends BaseMailer  {
    const CASE_INVITE_SUBJECT = 'Invitation to Join %s';
    const TEAM_INVITE_SUBJECT = "Invitation to Join %s's Team";
    const ACTION_TEXT         = 'Join';

    static function caseInvite($invitee, $sender, $case) {
      global $usersModel, $casesModel, $logger, $smarty, $invitationsModel;

      if (!$invitee = (is_array($invitee) ? $invitee : $usersModel->findActive($invitee)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Invitee User not found - ' . print_r($invitee, true));
      }
      if ( !$sender = (is_array($sender) ? $sender : $usersModel->findInactive($sender)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Sender User not found - ' . print_r($sender, true));
      }
      if ( !$case = (is_array($case) ? $case : $casesModel->find($case)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Case Not Found -' . print_r($case, true));
      }

      $invitation = $invitationsModel->create($invitee['pkaddressbookid']);
      $smarty->assign([
        'name'        => User::getFullName($invitee),
        'senderName'  => User::getFullName($sender),
        'senderEmail' => $sender['email'],
        'senderFirm'  => $sender['companyname'],
        'caseName'    => $case['case_title'],
        'actionUrl'   => $invitation['link'],
        'actionText'  => self::ACTION_TEXT
      ]);
      $body    = $smarty->fetch('emails/case-invite.tpl');
      $to      = $invitee['email'];
      $subject = sprintf(self::CASE_INVITE_SUBJECT, $case['case_title']);

      parent::sendEmail($to, $subject, $body);
    }

    static function teamInvite($invitee, $sender) {
      global $usersModel, $logger, $smarty, $invitationsModel;

      if (!$invitee = (is_array($invitee) ? $invitee : $usersModel->findActive($invitee)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Invitee User not found - ' . print_r($invitee, true));
      }
      if ( !$sender = (is_array($sender) ? $sender : $usersModel->findInactive($sender)) ) {
        return $logger->error('INVITATION_MAILER_INVITE Sender User not found - ' . print_r($sender, true));
      }

      $invitation = $invitationsModel->create($invitee['pkaddressbookid']);
      $smarty->assign([
        'name'        => User::getFullName($invitee),
        'senderName'  => User::getFullName($sender),
        'senderEmail' => $sender['email'],
        'senderFirm'  => $sender['companyname'],
        'actionUrl'   => $invitation['link'],
        'actionText'  => self::ACTION_TEXT
      ]);
      $body    = $smarty->fetch('emails/team-invite.tpl');
      $to      = $invitee['email'];
      $subject = sprintf(self::TEAM_INVITE_SUBJECT, User::getFullName($sender));

      parent::sendEmail($to, $subject, $body);
    }
  }