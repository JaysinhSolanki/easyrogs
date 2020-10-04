<?php
  class Coupon extends BaseModel {
    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, []);
    }

    function redeem($code) {
      if (!$coupon = $this->findActiveCoupon($code)) {
        $this->logger->warning("Trying to redeem INVALID coupon: $code");
        return false;
      }

      $this->update('coupons',
        ['uses' => $coupon['uses'] + 1],
        ['code' => "'$code'"]
      );
      
      return true;
    }

    // returns a coupon or `false` if the coupon is invalid
    function findActiveCoupon($code) {
      $coupon = $this->getBy('coupons', ['code' => $code], 1);

      $active = $coupon && 
                $coupon['active'] && 
                (!$coupon['max_uses']   || $coupon['uses'] < $coupon['max_uses']) &&
                (!$coupon['expires_at'] || strtotime($coupon['expires_at']) > time());

      return ( $active ? $coupon : $active);
    }

  }

  $couponsModel = new Coupon();