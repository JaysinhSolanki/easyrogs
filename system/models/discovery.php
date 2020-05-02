<?php
  
  class Discovery extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }

    function find($id) { return $this->getBy( 'discoveries', ['id' => $id], 1); }
    function findByUID($uid) { return $this->getBy( 'discoveries', ['uid' => $uid], 1); }

    const STYLE_AS_IS     = 'as_IS';
    const STYLE_WORDCAPS  = 'WordCaps';
    const STYLE_ALLCAPS   = 'ALLCAPS';
    const STYLE_LOWERCASE = 'lowercase';
    
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

  }

  $discoveriesModel = new Discovery();