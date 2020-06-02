<?php
  require_once __DIR__ . '/../bootstrap.php';
  require_once __DIR__ . '/adminsecurity.php';

  use Stripe\PaymentMethod;

  $caseId = $_GET['case_id'];

  $customer = User::getPaymentCustomer($currentUser->user);

  $paymentMethods = [];

  if ( $caseId ) {
    $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $caseId);

    if( $currentSide && $currentSide['payment_method_id']) {
      $sidePaymentMethod = $stripe->paymentMethods->retrieve($currentSide['payment_method_id'], []);

      $paymentMethods[] = [
        'type'        => 'side',
        'default'     => true,
        'id'          => $sidePaymentMethod->id,
        'name' => sprintf("%s ending %s (Team's Card)",
          strtoupper($sidePaymentMethod->card->brand),
          $sidePaymentMethod->card->last4
        )
      ];
    }
  }

  $customerPaymentMethods = $stripe->paymentMethods->all([
    'customer' => $customer->id,
    'type'     => 'card'
  ]);

  if ($customerPaymentMethods) {
    foreach($customerPaymentMethods->data as $paymentMethod) {
      if ( $paymentMethod->id != $sidePaymentMethod->id ) {
        $paymentMethods[] = [
          'type'    => 'customer',
          'default' => $sidePaymentMethod ? false : true,
          'id'      => $paymentMethod->id,
          'name'    => sprintf("%s ending %s (Personal Card)",
            strtoupper($paymentMethod->card->brand),
            $paymentMethod->card->last4
          )
        ];
      }
    }
  }

  HttpResponse::successPayload($paymentMethods);