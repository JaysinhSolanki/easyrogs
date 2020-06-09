<?php
  
  use Stripe\PaymentIntent;

  class Discovery extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }

    const FORM_FROGS  = 1;
    const FORM_FROGSE = 2;
    const FORM_SROGS  = 3;
    const FORM_RFAS   = 4;
    const FORM_RPDS   = 5;

    const TYPE_EXTERNAL = 1;
    const TYPE_INTERNAL = 2;

    const VIEW_RESPONDING  = 0;
    const VIEW_PROPOUNDING = 1;

    const STYLE_AS_IS     = 'as_IS';
    const STYLE_WORDCAPS  = 'WordCaps';
    const STYLE_ALLCAPS   = 'ALLCAPS';
    const STYLE_LOWERCASE = 'lowercase';

    function find($id) { return $this->getBy( 'discoveries', ['id' => $id], 1); }
    function findByUID($uid) { return $this->getBy( 'discoveries', ['uid' => $uid], 1); }

    public function updateById($id, $fields, $ignore = false ) {
      return parent::update('discoveries', $fields, ['id' => $id], $ignore);
    }

    public function isPaid($discoveryId) {
      global $currentUser;
      
      foreach(PAYMENT_WHITELIST as $whitelistedEmail) {
        if (preg_match($whitelistedEmail, $currentUser->user['email'])) {
          return true;
        }
      }

      $discovery = $this->find($discoveryId);
      if ($discovery['payment_intent_id']) {
        $intent = PaymentIntent::retrieve($discovery['payment_intent_id']);
        $this->logger->info($intent);
        return $intent->status === 'succeeded';
      }
      return false;
    }

    public function setPaymentIntent($discoveryId, $paymentIntentId) {
      return $this->updateById($discoveryId, ['payment_intent_id' => $paymentIntentId]);
    }
    
    static function getTitle($name, $set_number = null, $style = self::STYLE_WORDCAPS ) {
      global $logger;
      $logger->info("getTitle: \$name=$name, \$set=$set_number, \$style=$style" );
      if(isset($set_number)) {
        $name = $name . " [Set " .ucwords(strtolower( numberTowords( $set_number ) )). "]";
      }
      switch( $style ) {
        case self::STYLE_LOWERCASE: 
          $name = strtolower( $name ); break;
        case self::STYLE_WORDCAPS: 
          $name = ucwords(strtolower( $name )); break;
        case self::STYLE_ALLCAPS: 
          $name = strtoupper( $name ); break;
        case self::STYLE_AS_IS: 
      }

      return preg_replace( ["/\bset\b/u","/\bFor\b/u","/\bOf\b/u"], ["Set","for","of"], $name );
    }

    static function statementDescriptor($discovery) {
      return "Served Discovery #$discovery[id]";
    }

  }

  $discoveriesModel = new Discovery();