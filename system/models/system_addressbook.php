<?php
  class SystemAddressBook extends BaseModel {
    const TABLE = 'system_addressbook';

    const PUBLISHABLE_KEYS = [
      'pkaddressbookid', 'firstname', 'middlename', 
      'lastname', 'email', 'address', 'barnumber'
    ];

    const SEARCH_FIELDS = [
      'firstname', 'middlename', 'lastname', 'email', 'barnumber'
    ];

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
      ]);
    }

    static function publishable($items) {
      $items = is_array($items) ? $items : [$items];
      
      foreach($items as &$item) {
        foreach($item as $key => $value) {
          if ( !in_array($key, self::PUBLISHABLE_KEYS) ) {
            unset($item[$key]);
          }
        }
      }
      return $items;
    }

    function getByEmail($email) {
      return $this->getBy(self::TABLE, ['email' => $email], 1);
    }

    function find($id) {
      return $this->getBy(self::TABLE, ['pkaddressbookid' => $id], 1);
    }

    function create($fieldsMapping) {
      $id = $this->insert(self::TABLE, array_merge([
        'username'           => '',
        'contactperson'      => '',
        'street'             => '',
        'fkadmittedstateid'  => 0,
        'barnumber'          => '',
        'cityname'           => '',
        'iscustomer'         => 0,
        'organizationnumber' => '',
        'uploadfile'         => '',
        'agentpercentage'    => '',
        'agentshortcode'     => '',
        'isblocked'          => 0,
        'designation'        => '',
        'companyname'        => '',
        'attorney_info'      => '',
        'isclosed'           => 0,
        'orignal_email'      => $fieldsMapping['email'], // email is required
        'accountid'          => '',
        'updated_at'         => date('Y-m-d H:i:s'),
        'updated_by'         => 0
      ], $fieldsMapping));
      return $this->getBy(self::TABLE, ['pkaddressbookid' => $id], 1);
    }

    function searchInGroups($groupIds, $term, $fields = self::SEARCH_FIELDS) {
      return $this->searchBy(
        self::TABLE, 
        $term, 
        $fields, 
        ['fkgroupid' => $groupIds]
      );
    }

  }