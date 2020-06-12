<?php
  
  class Response extends Payable {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }

    function find($id) { return $this->getBy( 'responses', ['id' => $id], 1); }

    public function updateById($id, $fields, $ignore = false) {
      return parent::update('responses', $fields, ['id' => $id], $ignore);
    }

    static function statementDescriptor($response) {
      return "Served Response #$response[id]";
    }

  }

  $responsesModel = new Response();