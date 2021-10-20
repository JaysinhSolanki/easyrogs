<?php

use function EasyRogs\_assert as _assert;

class Document extends BaseModel {
    const TABLE = 'documents';

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        'getSomething' => '',
      ]);
    }

    function find($id) {
      return $this->getBy(self::TABLE, ['id' => $id], 1);
    }

    function findByDiscovery($discovery) { global $discoveriesModel;

      $discovery = $discoveriesModel->asDiscovery($discovery);
      return $this->getBy(self::TABLE, ['discovery_id' => $discovery['id']]);
    }

    function findByCase($caseId) { global $discoveriesModel;
      return $this->getBy(self::TABLE, ['case_id' => $caseId]);
    }

    function findByAttorney($attorneyId) { global $discoveriesModel;
      return $this->getBy(self::TABLE, ['case_id' => $caseId]);
    }

    function deleteByResponse($response) { global $discoveriesModel, $responsesModel;

      $discovery = $discoveriesModel->asDiscovery($discovery);
      return $this->getBy(self::TABLE, ['discovery_id' => $discovery['id']]);
    }
  }

$docsModel = new Document();

function getDocuments($discovery) { global $discoveriesModel;


}

// $olddocuments = $AdminDAO->getrows('documents', "*", "discovery_id = '$discovery_id' ".($where?:'') );
// $_SESSION['documents'] = array();
//     foreach( $olddocuments ?: [] as $data ) {
//         $doc_purpose = $data['document_notes'];
//         $doc_name    = $data['document_file_name'];
//         $doc_path    = SYSTEMPATH."uploads/documents/".$data['document_file_name'];
//         if( $doc_name != "" ) {
//             $documents[$uid][] = array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>1);
//         }
//     $_SESSION['documents'] = $documents;
// }
