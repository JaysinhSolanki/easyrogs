<?php
  class KB extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }
    function find($id) { return $this->getBy('kb', ['id' => $id], 1); }

    function getByForm($formId) {
      $states = $this->getBy('system_state', [
        'fkcountryid' => $countryId
      ]);
      return uasort($states, function($s1, $s2) {
        return $s1['statename'] < $s2['statename'] ? -1 : 1;
      });
    }
  }

  $kbModel = new KB();
