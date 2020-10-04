<?php

use Stripe\Discount;

class Response extends Payable {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        'getQAs' => 'SELECT
                       rq.fkresponse_id      AS response_id,
                       q.id 			           AS question_id,
                       d.form_id             AS form_id,
                       dq.discovery_id       AS discovery_id,
                       rq.answer 		         AS answer,
                       rq.answer_detail      AS answer_detail,
                       rq.objection          AS objection,
                       rq.final_response     AS final_response,
                       dq.extra_text         AS extra_text,
                       q.question_type_id 	 AS question_type_id,
                       q.question_title   	 AS question_title,
                       q.question_number  	 AS question_number,
                       q.display_order	   	 AS display_order,
                       q.depends_on_question AS depends_on_question,
                       q.have_main_question  AS have_main_question,
                       q.sub_part			       AS sub_part
                     FROM response_questions AS rq
                       INNER JOIN discovery_questions AS dq ON (rq.fkdiscovery_question_id = dq.id)
                       INNER JOIN questions AS q ON (dq.question_id = q.id)
                       INNER JOIN responses AS r ON (rq.fkresponse_id = r.id)
                       INNER JOIN discoveries AS d ON (r.fkdiscoveryid = d.id)
                     WHERE r.id = :response_id
                     ORDER BY display_order ASC, question_number ASC, sub_part ASC',
        'getByUID' => 'SELECT
                          *
                        FROM
                          discoveries d, responses r
                        WHERE
                          d.uid = :uid AND
                          d.id  = r.fkdiscoveryid
                        ORDER BY r.id DESC
                      '
      ]);
    }

    const PREFIX_RESPONSE = 'Response to ';

    function find($id, $includeQAs = true) {
      $response = $this->getBy( 'responses', ['id' => $id], 1);

      if ($includeQAs) {
        $response['questions'] = $this->getQAStruct($id);
      }

      return $response;
    }

    function getByDiscovery($id) {
      if( strlen($id) >= 16 ) { // is it an UID?
        $query = $this->queryTemplates['getByUID'];
        return $this->readQuery( $query, ['uid' => $id] );
      }
      return $this->getBy( 'responses', ['fkdiscoveryid' => $id] );
    }

    public function updateById($id, $fields, $ignore = false) {
      return parent::update('responses', $fields, ['id' => $id], $ignore);
    }

    static function statementDescriptor($response) {
      return "Served Response #$response[id]";
    }

    /**
     * // TODO: Work in progress...
     * A handy function to obtain an easily renderable structure describing
     * an snapshot of the current QAs for a given response, allegedly honoring
     * all of ER's response form view generation rules.
     *
     * Given a response id, this function will return a hash in the form:
     * {
     *   <question_number>:
     *     response_id: int,
     *     question_id: int,
     *     form_id: int,
     *     discovery_id: int,
     *     answer: string,
     *     answer_detail: string,
     *     objection: string,
     *     final_response: string,
     *     extra_text: string,
     *     question_type_id: string,
     *     question_title: string,
     *     question_number: string,
     *     display_order: int,
     *     depends_on_question: int,
     *     have_main_question: int,
     *     sub_part: string,
     *     sub_questions: [
     *       {
     *          response_id: int,
     *          question_id: int,
     *          form_id: int,
     *          discovery_id: int,
     *          answer: string,
     *          answer_detail: string,
     *          objection: string,
     *          final_response: string,
     *          extra_text: string,
     *          question_type_id: string,
     *          question_title: string,
     *          question_number: string,
     *          display_order: int,
     *          depends_on_question: int,
     *          have_main_question: int,
     *          sub_part: string
     *       },
     *       ... other sub questions ...
     *       NOTE: Some fields may not be available on subquestions, see $this->getRPDSQASubQuestion
     *     ]
     *   },
     *   <other-question-number>: {
     *     ...
     *   }
     * }
     *
     */
    function getQAStruct($responseId) {
      $query = $this->queryTemplates['getQAs'];

      if( isset($this->qaStruct) ) return $this->qaStruct;

      $rows     = $this->readQuery($query, ['response_id' => $responseId]);
      $qaRows   = [];
      foreach($rows as $row) { $qaRows[$row['question_id']] = $row; };

      $qaStruct = [];
      foreach($qaRows as $questionId => &$qaRow) {
        $questionNum = $qaRow['question_number'];

        if ($qaRow['sub_part']) { // is a sub question?
          if (!$parentQA = @$qaStruct[$questionNum]) {
            $this->logger->debug(['RESPONSE_GET_QA_STRUCT', array_keys($qaStruct)]);
            $this->logger->warn("RESPONSE_GET_QA_STRUCT Expected parent question not yet accessible in QA Struct. Question Number: $questionNum");
            continue;
          }

          if ($this->shouldRenderQASubQuestions($parentQA)) {
            $qaRow['is_objection'] = trim($qaRow['objection']) ||
                                     strtolower(substr(trim($qaRow['answer']), 0, 8)) == 'objection';
            $qaStruct[$questionNum]['sub_questions'][] = $qaRow;
          }
        }
        else { // is top question
          $dependableQuestionId = $qaRow['depends_on_question'];
          if ($dependableQuestionId && !$dependableQA = @$qaRows[$dependableQuestionId]) {
            $this->logger->error("RESPONSE_GET_QA_STRUCT Expected dependable question not accessible for Question ID: $questionId");
            continue;
          }

          if (!$dependableQuestionId || $this->shouldRenderQADependantQuestions($dependableQA)) {
            // TODO: DRY
            $qaRow['is_objection'] = trim($qaRow['objection']) ||
                                     strtolower(substr(trim($qaRow['answer']), 0, 8)) == 'objection';

            $qaStruct[$questionNum] = array_merge(
              $qaStruct[$questionNum] ?? ['sub_questions' => []],
              $qaRow
            );
          }

          if( Discovery::isRDPSForm($qaRow['form_id'])) {
            if ( $rpdsSubQuestions = $this->getRPDSQASubQuestion($qaRow) ) {
              $qaStruct[$questionNum]['sub_questions'][] = $rpdsSubQuestions;
            }
          }
        }
      }

      return $this->qaStruct = $qaStruct;
    }

    private function shouldRenderQASubQuestions($qa) {
      return (
        false // TODO: add other logic for forms and question types here
        ||
        $qa['question_type_id'] == QuestionType::RADIO && $qa['answer'] == 'Yes'
        ||
        ($qa['question_type_id'] == QuestionType::TEXT && trim($qa['answer']))
      );
    }

    // NOTE:
    // At the moment this method returns the same as shouldRenderQASubQuestions,
    // more logic to come maybe?
    private function shouldRenderQADependantQuestions($dependableQA) {
      return $this->shouldRenderQASubQuestions($dependableQA);
    }

    private function getRPDSQASubQuestion($qaRow) {
      switch( $qaRow['answer'] ) {
        case Discovery::RPDS_ANSWER_NONE:
        case Discovery::RPDS_ANSWER_HAVE_DOCS:
        case Discovery::RPDS_ANSWER_DOCS_NEVER_EXISTED:
          return [];
        break;

        case Discovery::RPDS_ANSWER_DOCS_DESTROYED:
        case Discovery::RPDS_ANSWER_DOCS_NO_ACCESS:
          return array_merge($qaRow, [
            'sub_part'         => 'a',
            'question_type_id' => QuestionType::TEXT,
            'question_title'   => Discovery::RPDS_DETAIL_QUESTION,
            'answer'           => $qaRow['answer_detail']
          ]);
        break;
      }
    }

    public function isAnyServed($responses) {
        foreach($responses as $response) {
          if( $response['isserved'] ) {
            return true;
          }
        }
        return false;
    }

    public function asResponse($response, $includeQAs = true) {
      assert( !empty($response), "A proper response was expected here, \$response=".json_encode($response) );
        if( !is_array($response) ) {
          $response = $this->find($response);
        }
        assert( !empty($response) && !empty($response['id']), "A proper response was expected here, \$response=".json_encode($response) );
      return $response;
    }

    public function getTitle($response, $discovery = null, $isSupplAmended = false) { global $logger, $discoveriesModel;

      $result = '';
      if( $response ) { // get from Response
        $response = $this->asResponse($response, false);
        $result = $response['responsename'];
      }

      if( !$result || $isSupplAmended ) { // compose from Discovery
        assert( !empty($discovery), "A discovery needs to be specified, \$discovery=".json_encode($discovery) );
        $discovery = $discoveriesModel->asDiscovery($discovery);
        $result = self::PREFIX_RESPONSE. $discoveriesModel->getTitle($discovery);
      }

      if( $isSupplAmended ) {
        $count = $this->countBy('responses', ['fkdiscoveryid' => $discovery['id']]);

        $result = numToOrdinalWord( $count +1 ) ." ". Discovery::PREFIX_SUPP_AMENDED." ". $result;
      }

      $logger->debug("Response->getTitle:
                        \$discovery=".(is_array($discovery)?$discovery['discovery_name']:@$discovery).",
                        \$response=". (is_array($response)?$response['responsename']:@$response).",
                        \$result=$result" );
      return $result;
    }

  }

  $responsesModel = new Response();
