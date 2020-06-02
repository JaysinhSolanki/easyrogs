<?php
  require_once __DIR__ . '/../bootstrap.php';
  require_once __DIR__ . '/adminsecurity.php';

  // create a new Stripe customer if none is associated with the current user
  $customer = User::getPaymentCustomer($currentUser->user);
  
  HttpResponse::successPayload(
    $stripe->setupIntents->create([
      'customer' => $customer->id
    ])
  );