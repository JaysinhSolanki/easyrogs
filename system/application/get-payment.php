<?php
  require_once __DIR__ . '/../bootstrap.php';
  require_once __DIR__ . '/adminsecurity.php';

  use Stripe\PaymentIntent;
  use Stripe\PaymentMethod;
  use Stripe\Customer;

  $itemId          = $_GET['id'];
  $itemType        = $_GET['type'];
  $paymentMethodId = $_GET['payment_method_id'];
  $saveToSide      = $_GET['save_to_side'];
  $saveToProfile   = $_GET['save_to_profile'];

  switch($itemType) {
    case Payable::ITEM_TYPE_DISCOVERY:
      $itemModel   = $discoveriesModel;
      $item        = $itemModel->find($itemId);
      $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $item['case_id']);
    break;
    case Payable::ITEM_TYPE_RESPONSE:
      $itemModel   = $responsesModel;
      $item        = $itemModel->find($itemId);
      $discovery   = $discoveriesModel->find($item['fkdiscoveryid']);
      $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $discovery['case_id']);
    break;

    case Payable::ITEM_TYPE_MEET_CONFER:
      $itemModel   = $meetConferModel;
      $item        = $itemModel->find($itemId);
      $response    = $responsesModel->find($item['response_id']);
      $discovery   = $discoveriesModel->find($response['fkdiscoveryid']);
      $currentSide = $sidesModel->getByUserAndCase($currentUser->id, $discovery['case_id']);
    break;

    default:
      HttpResponse::notFound();
    break;
  }

  // validate
  if ( !($item && $currentSide) ) { HttpResponse::notFound(); }

  // if using side's payment method, impersonate owner stripe customer
  if ( $paymentMethodId && $currentSide['payment_method_id'] == $paymentMethodId) {
    $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
    $customer = Customer::retrieve($paymentMethod->customer);
  }
  else {
    $customer = User::getPaymentCustomer($currentUser->user);
  }

  try {
    // always create a new payment intent, we dont want users following up
    // previous incomplete attempts
    $paymentIntent = PaymentIntent::create([
      'amount'               => SERVE_DISCOVERY_COST, // TODO: YAGNI. implement various item costs
      'currency'             => PAYMENTS_CURRENCY,
      'setup_future_usage'   => 'on_session',
      'statement_descriptor' => $itemModel::statementDescriptor($item),
      'customer'             => $saveToProfile   ? $customer->id : null,
      'payment_method'       => $paymentMethodId ?: null,
      'metadata'             => [
        'item_id'         => $itemId,
        'item_type'       => $itemType,
        'side_id'         => $currentSide['id'],
        'save_to_side'    => $saveToProfile ? $saveToSide : 0, // only allow to save to side if saved to profile
        'save_to_profile' => $saveToProfile,
        'user_id'         => $currentUser->id
      ]
    ]);
    $itemModel->setPaymentIntent($itemId, $paymentIntent->id);
  }
  catch( Exception $e ) {
    error_log( $e->getMessage() );
  }

  HttpResponse::successPayload([
    'client_secret' => $paymentIntent->client_secret
  ]);