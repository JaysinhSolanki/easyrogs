<?php
  use Stripe\PaymentIntent;

  abstract class Payable extends BaseModel implements PayableInterface {

    const ITEM_TYPE_RESPONSE  = 'response';
    const ITEM_TYPE_DISCOVERY = 'discovery';

    public function isPaid($payableId) {
      global $logger, $currentUser;
      
      try {
        foreach(PAYMENT_WHITELIST as $whitelistedEmail) {
          if (preg_match($whitelistedEmail, $currentUser->user['email'])) {
            return true;
          }
        }
      } catch( Exception $e ) {
        $logger->error( ["Exception while processing PAYMENT_WHITELIST", $e] );
      }

      $payableItem = $this->find($payableId);
      if ($payableItem['payment_intent_id']) {
        $intent = PaymentIntent::retrieve($payableItem['payment_intent_id']);
        return $intent->status === 'succeeded';
      }

      return false;
    }

    public function setPaymentIntent($itemId, $paymentIntentId) {
      return $this->updateById($itemId, ['payment_intent_id' => $paymentIntentId]);
    }
    
  }
  