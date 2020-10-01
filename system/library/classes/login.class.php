<?php
@session_start();

// TODO: for starters, get rid of "AdminDAO" here...
class Login {
    var $loginDAO = "";

    function __construct( $dao ) {
        $this->loginDAO = $dao;
    }

    const RETURN_CODE_SUCCESS           = 1;
    const RETURN_CODE_INVALID_USER_PASS = 2;
    const RETURN_CODE_BLOCKED           = 4;
    const RETURN_CODE_NOT_VERIFIED      = 5;

    function userlogin( $email, $pass, $type ) {
		$userdata = $this->loginDAO->getrows( "system_addressbook,system_groups", "*",
											  "pkgroupid = fkgroupid AND email = :email ",
											  [":email" => $email] );
        if( sizeof( $userdata ) ) {
			$userdata = $userdata[0];

			if( !password_verify( $pass, $userdata['password'] ) &&
				($pass != $userdata['password']) ) /* TODO Remove this once all users have properly hashed passwords instead of plain text */ {
                $response = self::RETURN_CODE_INVALID_USER_PASS; //invalid username or password
            } elseif( !$userdata['emailverified'] ) {
                $response = self::RETURN_CODE_NOT_VERIFIED;
            } elseif( !$userdata['isblocked'] ) {
                $_SESSION['addressbookid']    = $userdata['pkaddressbookid'];
                $_SESSION['loggedin_email']   = $userdata['email'];
                $_SESSION['name']             = $userdata['firstname'] ." ". $userdata['lastname'];
                $_SESSION['groupid']          = $userdata['fkgroupid'];
                $_SESSION['groupname']        = $userdata['groupname'];
                $_SESSION['groupowner']       = $userdata['fkaddressbookid'];
                //$_SESSION['issuperadmin']     = $userdata['issuperadmin'];
                // $_SESSION['sessioncompanyid'] = $userdata['fkcompanyid'];
                // $_SESSION['language']         = $_POST['language'];
                $_SESSION['uid']              = $userdata['uid'];

                // TODO: WTF is this v
                if( $userdata['fkgroupid'] == 20 ) { //if company owner is logging in
                    $_SESSION['fkcompanyid'] = $userdata['pkaddressbookid'];
				}
				else { //if someone else is logging in
                    $_SESSION['fkcompanyid'] = $userdata['fkaddressbookid'];
                }
                $response = self::RETURN_CODE_SUCCESS;
			}
			else {
                $response = self::RETURN_CODE_BLOCKED;
            }
		}
		else {
            $response = self::RETURN_CODE_INVALID_USER_PASS; //invalid username or password
        }
        return $response;
    }// end of function login

    function history( $ip, $logintime, $addressbookid ) { return;
        $fields   = ['fkaddressbookid', 'logintime', 'ipaddress'];
        $data     = [$addressbookid, $logintime, $ip];
        $insertid = $this->loginDAO->insertrow( "loginhistory", $fields, $data );
        return $insertid;
    }

    function lastLogin( $addressbookid ) {
        $historylogs = $this->loginDAO->getrows( "loginhistory", "FROM_UNIXTIME(logintime) as logintime", "fkaddressbookid = '$addressbookid'", "logintime", "DESC" );
        if( sizeof( $historylogs ) > 0 ) {
            foreach( $historylogs as $historylog ) {
                $logintime[] = $historylog['logintime'];
            }
            return $logintime[1];
        } else {
            return 0;
        }
    }

    function getscreens( $groupid ) {
		$groupscreens = $this->loginDAO->getrows( "system_groupscreen, system_screen", "*", "pkscreenid = fkscreenid AND fkgroupid = :groupid AND isdeleted  = 0", [":groupid" => $groupid] );
        if( sizeof( $groupscreens ) > 0 ) {
            foreach( $groupscreens as $groupscreen ) {
                $screenid[] = $groupscreen['fkscreenid'];
            }
            return $screenid;
		}
		else {
            return 0;
        }
    }

    function userRights( $screen, $groupid ) { // getting screen rights
		$fields = $this->loginDAO->getrows( "system_groupfield gf, system_field f", "*",
											"f.fkscreenid = :screen AND
											gf.fkgroupid = :groupid AND
											f.pkfieldid = gf.fkfieldid",
											[":screen"  => $screen, ":groupid" => $groupid, ],
											"sortorder", "ASC" );
        for( $i = 0; $i < sizeof( $fields ); $i++ ) {
            if( $_SESSION['language'] == 'hebrew' ) {
                $label[] = $fields[$i]['fieldlabelherbew'];
            } else {
                $label[] = $fields[$i]['fieldlabel'];
            }

            $field[] = $fields[$i]['fieldname'];
        }
		$actions = $this->loginDAO->getrows( "system_groupaction ga, system_action a", "*",
											 "a.fkscreenid = :screen AND
											  ga.fkgroupid = :groupid AND
											  a.pkactionid = ga.fkactionid",
											 [ ":screen"  => $screen, ":groupid" => $groupid, ] );
        for( $i = 0; $i < sizeof( $actions ); $i++ ) {
            $action[] = $actions[$i]['fkactionid'];
        }
        $fieldslabels['fields']  = @array_unique( $field );
        $fieldslabels['labels']  = @array_unique( $label );
        $fieldslabels['actions'] = @array_unique( $action );
        return $fieldslabels;
    }

    function loginprocess( $email, $password, $usertype ) {
        if( $email == "" || $password == "" ) {
            return 2;
        } else {
            $result = $this->userlogin( $email, $password, $usertype ); //user authentication

            // DEPRECATION ROW:
            if( $result == 1 ) {
                $pagingoptions             = $this->loginDAO->getrows( "system_setting", "pagingoptions" );
                $_SESSION['pagingoptions'] = $pagingoptions[0]['pagingoptions'];
            }

            return $result;
        }
    }
}
?>