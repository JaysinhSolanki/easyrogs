<?php
  use Stripe\PaymentIntent;

  abstract class Payable extends BaseModel implements PayableInterface {

    const ITEM_TYPE_RESPONSE    = 'response';
    const ITEM_TYPE_DISCOVERY   = 'discovery';
    const ITEM_TYPE_MEET_CONFER = 'meet_confer';

    public function isPaid($payableId) {
      global $currentUser, $membershipWhitelist;

      if( $_ENV['PAY_DISABLED'] || $membershipWhitelist->isWhitelisted($currentUser->user) ) {
        return true;
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
