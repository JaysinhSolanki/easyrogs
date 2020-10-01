<?php

class Discovery extends Payable {

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        // TODO temporary measure, needed until the old code is refactored
        'getDetails' => "SELECT
                            d.uid, d.id,
                            (SELECT d.uid) AS discovery_uid,
                            (SELECT d.id) AS discovery_id,
                            d.case_id, c.uid AS case_uid,
                            d.propounding,     d.responding,
                            d.propounding_uid, d.responding_uid,
                            c.case_title, c.case_number,
                            c.plaintiff, c.defendant,
                            c.judge_name, c.jurisdiction, c.court_address, a.cityname, c.county_name,
                            c.department,
                            d.is_served, d.served,
                            d.is_send,   d.send_date,
                            d.type,
                            d.form_id,
                            f.form_name, f.short_form_name,
                            d.discovery_name, d.set_number,
                            d.discovery_introduction as introduction,
                            a.email, a.phone,
                            a.attorney_info,
                            a.firstname, a.middlename, a.lastname,
                            TRIM(
                              REPLACE( CONCAT(
                                COALESCE( a.firstname,  '' ), ' ',
                                COALESCE( a.middlename, '' ), ' ',
                                COALESCE( a.lastname,   '' )
                              ), '  ', ' ' )
                            ) AS attorney,
                            a.address		  as atorny_address,
                            a.companyname	as atorny_firm,
                            d.attorney_id	as attorney_id
                          FROM
                            discoveries d, cases c, system_addressbook a, forms f
                          WHERE
                            d.id          = :id AND
                            d.case_id     = c.id AND
                            d.form_id     = f.id AND
                            d.attorney_id = a.pkaddressbookid
                        ",

        'getSuppAmended' => "SELECT
                            d.id, d.uid,
                            (SELECT d.uid) AS discovery_uid,
                            (SELECT d.id ) AS discovery_id,
                            d.propounding_uid, d.responding_uid,
                            d.propounding,     d.responding,
                            d.is_served, d.served,
                            d.due,
                            c.case_title,
                            d.attorney_id as creator_id,
                            TRIM(
                              REPLACE( CONCAT(
                                COALESCE( a.firstname,  '' ), ' ',
                                COALESCE( a.middlename, '' ), ' ',
                                COALESCE( a.lastname,   '' )
                              ), '  ', ' ' )
                            ) AS attorney,
                            (SELECT attorney) AS creator,
                            d.discovery_name, d.set_number,
                            f.form_name, f.short_form_name,
                            d.form_id,
                            d.type,
                            IF( send_date='0000-00-00 00:00:00', '-', send_date ) send_date
                          FROM
                            discoveries d, cases c, system_addressbook a, forms f
                          WHERE
                            d.grand_parent_id	= :id AND
                            c.id              = d.case_id AND
                            f.id              = d.form_id AND
                            a.pkaddressbookid = d.attorney_id
                          ",

        'getByCase' => "SELECT
                            d.id, d.uid,
                            (SELECT d.uid) AS discovery_uid,
                            (SELECT d.id) AS discovery_id,
                            d.propounding, d.responding,
                            d.propounding_uid, d.responding_uid,
                            d.is_served, d.served,
                            d.due,
                            c.case_title,
                            d.attorney_id as creator_id,
                            TRIM(
                              REPLACE( CONCAT(
                                COALESCE( a.firstname,  '' ), ' ',
                                COALESCE( a.middlename, '' ), ' ',
                                COALESCE( a.lastname,   '' )
                              ), '  ', ' ' )
                            ) AS attorney,
                            (SELECT attorney) AS creator,
                            d.discovery_name, d.set_number,
                            form_name, short_form_name,
                            form_id,
                            d.type,
                            IF(send_date='0000-00-00 00:00:00', '-', send_date) send_date
                          FROM
                            discoveries d, cases c, system_addressbook a, forms f
                          WHERE
                            c.id                  = d.case_id AND
                            f.id                  = d.form_id AND
                            pkaddressbookid       = d.attorney_id AND
                            d.parentid	          = 0 AND
                            d.is_work_in_progress	= 0 AND
                            c.id 				          = :case_id
                          ORDER BY discovery_name ASC
                          ",
      ]);
    }

    const FORM_FED_FROGS  = 11;
    const FORM_FED_FROGSE = 12;
    const FORM_FED_SROGS  = 13;
    const FORM_FED_RFAS   = 14;
    const FORM_FED_RPDS   = 15;

    const FORM_CA_FROGS   = 01;
    const FORM_CA_FROGSE  = 02;
    const FORM_CA_SROGS   = 03;
    const FORM_CA_RFAS    = 04;
    const FORM_CA_RPDS    = 05;

    const TYPE_EXTERNAL = 1;
    const TYPE_INTERNAL = 2;

    const VIEW_RESPONDING  = 0;
    const VIEW_PROPOUNDING = 1;

    const STYLE_AS_IS     = 'as_IS';
    const STYLE_WORDCAPS  = 'WordCaps';
    const STYLE_ALLCAPS   = 'ALLCAPS';
    const STYLE_LOWERCASE = 'lowercase';

    const PREFIX_SUPP_AMENDED = 'Supplemental/Amended ';

    // TODO: we need to figure this out, the whole forms engine won't hold...
    // For this specifically I think we need a better handling of the `dropdown` question type.
    const RPDS_ANSWER_NONE               = 'Select Your Response';
    const RPDS_ANSWER_HAVE_DOCS          = 'I have responsive documents';
    const RPDS_ANSWER_DOCS_NEVER_EXISTED = 'Responsive documents have never existed';
    const RPDS_ANSWER_DOCS_DESTROYED     = 'Responsive documents were destroyed';
    const RPDS_ANSWER_DOCS_NO_ACCESS     = 'Responsive documents were lost, misplaced, stolen, or I lack access to them';
    const RPDS_DETAIL_QUESTION           = 'Enter the name and address of anyone you believe has the documents.';

    const RPDS_FORMS = [self::FORM_CA_RPDS, self::FORM_FED_RPDS];

    function find($id) { return $this->getBy( 'discoveries', ['id' => $id], 1); }
    function findByUID($uid) { return $this->getBy( 'discoveries', ['uid' => $uid], 1); }
    function findDetails($id) { return $this->getDetails( $id, 1); }

    function getDetails( $id, $limit = null ) { global $logger;

      if( strlen($id) >= 16 ) {
        $id = $this->findByUID($id)['id'];
      }
      $query = $this->queryTemplates['getDetails'] . ( $limit ? " LIMIT $limit" : '' );
      $result = $this->readQuery( $query, ['id' => $id] );
      if( $result && $limit && $limit == 1 ) {
        $result = $result[0];
      }
      //$logger->browser_log(null, $result );
      return $result;
    }

    public function updateById($id, $fields, $ignore = false ) {
      return parent::update('discoveries', $fields, ['id' => $id], $ignore);
    }

    static function composeTitle($name, $set_number = null, $style = self::STYLE_WORDCAPS ) { global $logger;

      // $logger->info("getTitle: \$name=$name, \$set=$set_number, \$style=$style" );
      if( isset($set_number) ) {
        $name = $name . " [Set " .ucwords(strtolower( numberTowords( $set_number ) )). "]";
      }
      switch( $style ) {
        case self::STYLE_LOWERCASE:
          $name = strtolower( $name ); break;
        case self::STYLE_WORDCAPS:
          $name = ucwords(strtolower( $name )); break;
        case self::STYLE_ALLCAPS:
          $name = strtoupper( $name ); break;
        case self::STYLE_AS_IS:
      }

      return preg_replace( ["/\bset\b/u","/\bFor\b/u","/\bOf\b/u"], ["Set","for","of"], $name );
    }

    static function statementDescriptor($discovery) {
      return "Served Discovery #$discovery[id]";
    }

    static function isRDPSForm($formId) {
      return in_array($formId, self::RPDS_FORMS);
    }

    public function asDiscovery($discovery) {
      assert( !empty($discovery), "A proper discovery was expected here, \$discovery=".json_encode($discovery) );
      if( !is_array($discovery) ) {
        $discovery = ( strlen($discovery) >= 16 ) ? $this->findByUID($discovery) : $this->find($discovery);
      }
      assert( !empty($discovery['id']) && !empty($discovery['uid']), "A proper discovery was expected here, \$discovery=".json_encode($discovery) );
      assert( !!$discovery['id'], "A proper discovery was expected here, \$discovery=".json_encode($discovery) );
      return $discovery;
    }

    public function getByUserAndCase($user, $case, $limit = null ) { global $logger;

      $query = $this->queryTemplates['getByCase'] . ( $limit ? " LIMIT $limit" : '' );
      $result = $this->readQuery( $query, ['case_id' => $case] );
      if( $result && $limit && $limit == 1 ) {
        $result = $result[0];
      }
      //$logger->browser_log(null, $result );
      return $result;
    }

    public function getSuppAmended($discovery, $limit = null ) { global $logger;

      $id = is_array($discovery) ?  $discovery['id'] : $discovery;

      $query = $this->queryTemplates['getSuppAmended'] . ( $limit ? " LIMIT $limit" : '' );
      $result = $this->readQuery( $query, ['id' => $id] );
      if( $result && $limit && $limit == 1 ) {
        $result = $result[0];
      }
      //$logger->browser_log(null, $result );
      return $result;
    }

    public function getSet($discovery) {
      $discovery = $this->asDiscovery($discovery);
      return self::composeTitle( '', $discovery['set_number'] );
    }

    public function getTitle($discovery, $isSupplAmended = false) { global $logger;

      $discovery = $this->asDiscovery($discovery);
      $id = $discovery['id'];

      $set  = $discovery['set_number'];
      $name = $discovery['discovery_name'] ?: $discovery_data['form_name'];
      $name = preg_replace( '/^(.*?)(?:\s*\[Set [0-9a-zA-Z]+\])*$/i', '$1', $name ); // Remove the ` [Set nn]` part if already present
      $result = self::composeTitle( $name, $set, self::STYLE_AS_IS );
      $count = 0;

      if( $isSupplAmended ) {
        assert( $id, "!!" );
        // $query = $this->queryTemplates['getSuppAmendedCountById'];
        // $count = $this->readQuery( $query, ['id' => $discovery['id']] )[0]['COUNT'];
        $count = $this->countBy('discoveries', ['parentid' => $id]);

        $result = preg_replace( '/^(?:[A-Za-z]+\s+'.preg_quote(Discovery::PREFIX_SUPP_AMENDED,'/').'\s*)*(.*)$/i', '$1', $result );
          // Remove the `Nth Supplemental/Amended ` part if already present
        $result = numToOrdinalWord( $count +1 ) ." ". Discovery::PREFIX_SUPP_AMENDED . $result;
      }
      $logger->debug("Discovery->getTitle: \$name=$name,
                          \$set=$set,
                          \$count=".@$count.",
                          \$result=$result" );
      return $result;
    }
}

$discoveriesModel = new Discovery();