<?php
  require_once __DIR__ . '/../bootstrap.php';
  require_once __DIR__ . '/adminsecurity.php';

  use Stripe\PaymentIntent;
  use Stripe\PaymentMethod;
  use Stripe\Customer;
  
  $discoveryId     = $_GET['id'];
  $paymentMethodId = $_GET['payment_method_id'];
  $saveToSide      = $_GET['save_to_side'];
  $saveToProfile   = $_GET['save_to_profile'];

  $discovery = $discoveriesModel->find($discoveryId);
  $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $discovery['case_id']);
  
  // validate
  if ( !($discovery && $currentSide) ) { HttpResponse::notFound(); }
  
  // if using side's payment method, impersonate owner stripe customer
  if ( $paymentMethodId && $currentSide['payment_method_id'] == $paymentMethodId) {
    $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
    $customer = Customer::retrieve($paymentMethod->customer);
  }
  else {
    $customer = User::getPaymentCustomer($currentUser->user);
  }

  // always create a new payment intent, we dont want users following up
  // previous incomplete intents
  $paymentIntent = PaymentIntent::create([
    'amount'               => SERVE_DISCOVERY_COST,
    'currency'             => PAYMENTS_CURRENCY,
    'setup_future_usage'   => 'on_session',
    'statement_descriptor' => Discovery::statementDescriptor($discovery),
    'customer'             => $saveToProfile ? $customer->id : null,
    'payment_method'       => $paymentMethodId ?: null,
    'metadata'             => [
      'discovery_id'    => $discoveryId,
      'side_id'         => $currentSide['id'],
      'save_to_side'    => $saveToProfile ? $saveToSide : 0, // only allow to save to side if saved to profile
      'save_to_profile' => $saveToProfile,
      'user_id'         => $currentUser->id
    ]
  ]);
  $discoveriesModel->setPaymentIntent($discoveryId, $paymentIntent->id); // update the discovery
  
  HttpResponse::successPayload([
    'client_secret' => $paymentIntent->client_secret
  ]);