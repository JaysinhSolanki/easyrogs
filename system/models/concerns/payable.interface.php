<?php

  interface PayableInterface {
    function find($payableId);
    function updateById($payableId, $fields, $ignore = false);

    function isPaid($payableId);
    function setPaymentIntent($payableId, $paymentIntentId);

    static function statementDescriptor($payable);
  }