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

    const STYLE_WORDCAPS  = 'WordCaps';
    const STYLE_ALLCAPS   = 'ALLCAPS';
    const STYLE_LOWERCASE = 'lowercase';
    
    static function getTitle($name, $set_number = null, $syle = self::STYLE_WORDCAPS ) {
      if(isset($set)) {
        $name = $name . " [Set " .numberTowords( $set_number ). "]";
      }
      return str_replace( ["set","For","Of"], ["Set","for","of"], ucwords(strtolower( $name )) );
    }

  }

  $discoveriesModel = new Discovery();