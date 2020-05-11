<?php
  class Country extends BaseModel {
    const UNITED_STATES = 254;

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }

    function getAll() {
      return $this->getSorted('system_country', ['countryname' => 'ASC'], 0, PHP_INT_MAX);
    }
  }

  $countriesModel = new Country();