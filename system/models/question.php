<?php
  class Question extends BaseModel {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [

      ]);
    }

    function find($id) {
      return $this->getBy('questions', ['id' => $id], 1);
    }

  }

  $questionsModel = new Question();