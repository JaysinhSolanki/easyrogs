<?php
  class State extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }
    function find($id) { return $this->getBy('system_state', ['pkstateid' => $id], 1); }

    function getByCountry($countryId) {
      $states = $this->getBy('system_state', [
        'fkcountryid' => $countryId
      ]);
      return uasort($states, function($s1, $s2) { 
        return $s1['statename'] < $s2['statename'] ? -1 : 1; 
      });
    }
  }

  $statesModel = new State();