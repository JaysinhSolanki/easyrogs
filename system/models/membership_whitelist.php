<?php
  class MembershipWhitelist extends BaseModel {
    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        'existsByEmailOrDomain' => 'SELECT COUNT(*) as whitelisted
                                    FROM membership_whitelist
                                    WHERE (expires_at IS NULL OR expires_at > NOW()) AND 
                                          (address = :email OR domain = :domain)'
                                          
      ]);
    }

    function isWhitelisted($user) {
      $query = $this->queryTemplates['existsByEmailOrDomain'];

      $emailParts = explode('@', $user['email']);
      $domain = $emailParts[1];
      
      $row = $this->readQuery($query, [
        'email'  => trim($user['email']),
        'domain' => trim($domain)
      ]);
      
      return $row && $row[0]['whitelisted'] > 0;
    }

  }

  $membershipWhitelist = new MembershipWhitelist();