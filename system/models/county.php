<?php
  class County extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }

    function getByState($stateId) {
      $counties = $this->getBy('system_county', [
        'fkstateid' => $stateId
      ]);
      return uasort($counties, function($c1, $c2) { 
        return $c1['countyname'] < $c2['countyname'] ? -1 : 1; 
      });
    }

    function getAll() {
      return $this->getSorted('system_county', ['countyname' => 'ASC'], 0, PHP_INT_MAX);
    }

  }

  $countiesModel = new County();