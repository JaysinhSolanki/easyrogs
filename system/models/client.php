<?php

  class Client extends BaseModel {
    const ROLE_PLAINTIFF = 'Plaintiff';
    const ROLE_DEFENDANT = 'Defendant';
    const ROLE_PLAINTIFF_X_DEFENDANT = 'Plaintiff and Cross-defendant';
    const ROLE_DEFENDANT_X_PLAINTIFF = 'Defendant and Cross-plaintiff';

    const CLIENT_TYPE_US = 'Us';
    const CLIENT_TYPE_OTHER = 'Others';
    const CLIENT_TYPE_PRO_PER = 'Pro per';

    const CLIENT_TYPES = [
      self::CLIENT_TYPE_US, 
      self::CLIENT_TYPE_OTHER,
      self::CLIENT_TYPE_PRO_PER
    ];
    
    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge($this->queryTemplates, [
        'belongsToCase' => 'SELECT COUNT(*) AS `count`
                            FROM sides_clients
                              INNER JOIN sides ON sides_clients.side_id = sides.id
                            WHERE sides_clients.client_id = :client_id
                                  AND sides.case_id = :case_id'
      ]);

    }

    function findOrCreateBy($conditions, $fields) {
      global $currentUser;
      return $this->getOrInsertBy('clients', $conditions, array_merge([
          'updated_at' => date('Y-m-d H:i:s'),
          'updated_by' => $currentUser->id
        ], $fields)
      );
    }

    function create($fields) {
      global $currentUser;
      $id = $this->insert('clients', array_merge([
          'updated_at' => date('Y-m-d H:i:s'),
          'updated_by' => $currentUser->id
        ], $fields)
      );
      return $this->find($id);
    }

    function belongsToCase($clientId, $caseId) {
      $query = $this->queryTemplates['belongsToCase'];

      return $this->readQuery($query, [
        'client_id' => $clientId,
        'case_id'   => $caseId
      ])[0]['count'] > 0;
    }

    function getByEmail($email) {
      return $this->getBy('clients', ['email' => $email], 1);
    }

    function find($id) {
      return $this->getBy('clients', ['id' => $id], 1);
    }
    
    function updateClient($clientId, $fields) {
      return $this->update('clients', $fields, ['id' => $clientId]);
    }

    static function roleGeneric($client) {
      return (
  			$client['client_role'] == Client::ROLE_PLAINTIFF || 
	  		$client['client_role'] == Client::ROLE_PLAINTIFF_X_DEFENDANT 
          ? self::ROLE_PLAINTIFF : self::ROLE_DEFENDANT
      );
    }

  }

  $clientsModel = new Client();