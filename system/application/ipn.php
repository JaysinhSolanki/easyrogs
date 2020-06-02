<?php
  require_once __DIR__ . '/../bootstrap.php';

  use Stripe\Event;
  use Stripe\Exception\SignatureVerificationException;

  $payload   = @json_decode(@file_get_contents('php://input'), true);
  $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
  
  try {
    $event = Event::constructFrom($payload, $sigHeader, STRIPE_WEBHOOK_SIGNING_SECRET);
  } catch(\UnexpectedValueException $e) { // Invalid payload
    HttpResponse::malformed();
  } catch(SignatureVerificationException $e) { // Invalid signature
    HttpResponse::malformed();
  }

  // Handle the event
  switch ($event->type) {
    case 'payment_intent.succeeded': 
      // payment succeeded, save the payment method to the side if requested
      $paymentIntent = $event->data->object;
      if ($paymentIntent->metadata->save_to_side) {
        $sidesModel->setPaymentMethod(
          $paymentIntent->metadata->side_id, 
          $paymentIntent->payment_method
        );
      }
    break;
  }

  HttpResponse::success();