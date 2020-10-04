<?php
  class CouponsController extends BaseController {

    function show() { global $couponsModel;
      $code = @$_GET['code'];

      if ( !$code ) { HttpResponse::malformed('Sorry, coupon code is required'); }

      $coupon = $couponsModel->findActiveCoupon($code);

      return $coupon ? 
              HttpResponse::successPayload($coupon) : 
              HttpResponse::unauthorized('Sorry, you entered an invalid coupon, please check your input and try again.');
    }
}