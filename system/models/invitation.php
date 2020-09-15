<?php
  class Invitation extends BaseModel {
    const STATUS_NEW  = 1;
    const STATUS_USED = 2;

    function __construct( $dbConfig = null )
    {
      parent::__construct( $dbConfig );

      $this->queryTemplates = array_merge( $this->queryTemplates, [
        'getInvitationUser' => 'SELECT u.*
                                FROM system_addressbook AS u
                                  INNER JOIN invitations AS i ON (u.pkaddressbookid = i.attorney_id)
                                WHERE i.uid = :uid
                                      AND status = 1'
      ]);
    }

    function create($userId) {
      global $currentUser;

      $uid = $this->generateUID('invitations');
      $id = $this->insert('invitations', [
        'uid'         => $uid,
        'attorney_id' => $userId,
        'status'      => self::STATUS_NEW,
        'link'        => DOMAIN . "signup.php?uid=$uid",
        'updated_at'  => date('Y-m-d H:i:s'),
        'updated_by'  => $currentUser->id,
      ]);

      return $this->getBy('invitations', ['id' => $id], 1);
    }

    function redeem($invitationUID) {
      return $this->update('invitations',
        ['status' => self::STATUS_USED],
        ['uid' => "'$invitationUID'"]
      );
    }

    function getInvitationUser($invitationUID) {
      $query = $this->queryTemplates['getInvitationUser'];
      return $this->readQuery($query, ['uid' => $invitationUID])[0];
    }

  }

  $invitationsModel = new Invitation();