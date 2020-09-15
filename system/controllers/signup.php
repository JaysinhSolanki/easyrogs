<?php

  class SignupController extends BaseController {
    
    function show() { global $smarty, $invitationsModel;
      $uid = @$_GET['uid'];

      if ($uid && $user = $invitationsModel->getInvitationUser($uid)) {
        if ($user['emailverified']) {
          $smarty->assign([
            'notifications' => [
              [
                "colorClass" => 'warning',
                'message'    => "You have already signed up with this email ($user[email])",
                'action' => [
                  'text' => 'Login &raquo;',
                  'url'  => 'userlogin.php'
                ]
              ]
            ]
          ]);
        }
        else {
          $smarty->assign(array_merge(['invitation_uid' => $uid], $user));
        }
      }
      else if ($uid) {
        $smarty->assign([
          'notifications' => [
            ['message' => "URL is invalid or expired.", "colorClass" => 'danger']
          ]
        ]);
      }
    
      $smarty->display('signup.tpl');
    }

    function start() { global $usersModel;
      $isAttorney    = @trim($_POST['is_attorney']);
      $barNumber     = @trim($_POST['barnumber']);
      $firstName     = @trim($_POST['firstname']);
      $lastName      = @trim($_POST['lastname']);
      $email         = @trim($_POST['email']);
      $password      = @trim($_POST['password']);
      $terms         = @trim($_POST['terms']);
      $invitationUID = @trim($_POST['invitation_uid']);
    
      $valid = (!$isAttorney || ($isAttorney && $barNumber)) && $firstName && 
               $lastName && $email && $password && $terms;
      if (!$valid) { 
        return HttpResponse::malformed('Please fill the required fields.'); 
      }
    
      $userExists = $usersModel->existsBy(User::TABLE, ['email' => $email, 'emailverified' => true]);
      if ($userExists) {
        return HttpResponse::send(409, HttpResponse::TYPE_ERROR, 'Email address already in use', [
          '_color_class' => 'info',
          '_action' => [
              'text' => 'Recover your password &raquo;',
              'url'  => FRAMEWORK_URL . 'forgotpassword.php'
            ]
        ]);
      }
    
      $token = $this->jwtEncodeToken([
        'is_attorney'    => $isAttorney,
        'barnumber'      => $barNumber,
        'firstname'      => $firstName,
        'lastname'       => $lastName,
        'email'          => $email,
        'password'       => $password,
        'terms'          => $terms,
        'invitation_uid' => $invitationUID
      ]);
    
      UserMailer::signup($email, $token);
    
      return HttpResponse::success();
    }

    function finish() { global $usersModel, $invitationsModel, $logger;
      $token = @$_GET['t'];

      try { $payload = $this->jwtDecodeToken($token); }
      catch(Exception $e){ return HttpResponse::redirect('signup.php'); } // the token is invalid, redirect to signup
      
      $userData = [
        'fkgroupid'     => $payload->is_attorney ? User::ATTORNEY_GROUP_ID : User::SUPPORT_GROUP_ID,
        'barnumber'     => $payload->barnumber,
        'firstname'     => $payload->firstname,
        'lastname'      => $payload->lastname,
        'email'         => $payload->email,
        'password'      => password_hash($payload->password,PASSWORD_DEFAULT),
        'fkcountryid'   => Country::UNITED_STATES,
        'signupdate'    => date('Y-m-d H:i:s'),
        'signupip'      => $_SERVER['REMOTE_ADDR'],
        'emailverified' => 1,
        'username'      => $payload->email,
        'credits'       => SIGNUP_CREDITS
      ];
      
      $user = $usersModel->getByEmail($payload->email);
      if ($user && $user['emailverified'] == 0) {
        $usersModel->update('system_addressbook', $userData, ['pkaddressbookid' => $user['pkaddressbookid']]);
      } else {
        try {
          $user = $usersModel->create($userData);
        }
        catch(Exception $e){
          $logger->warn("FINISH_SIGNUP Unable to create user for (" . $e->getMessage() . "). Payload: " . json_encode($payload));
          return HttpResponse::redirect(FRAMEWORK_URL . 'forgotpassword.php');
        }
      }

      if ($payload->invitation_uid) {
        $invitationsModel->redeem($payload->invitation_uid);
      }

      return SessionUser::login($user, $payload->password);
    }

  }