<?php
  class KB extends BaseModel {
    const AREA_OBJECTIONS        = 0;
    const AREA_OBJECTION_KILLERS = 1;
    const AREA_DEFINITIONS       = 2;
    
    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }
    function find($id) { return $this->getBy('kb', ['id' => $id], 1); }

    function getByAreaId($areaId) {
      return $this->getBy('kb', ['area_id' => $areaId] );
    }
  }

  $kbModel = new KB();
