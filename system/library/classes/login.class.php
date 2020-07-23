<?php
@session_start();

class Login {
    var $loginDAO = "";

    function __construct( $dao ) {
        $this->loginDAO = $dao;
    }

    function userlogin( $email, $pass, $type ) {
		$userdata = $this->loginDAO->getrows( "system_addressbook,system_groups", "*", 
											  "pkgroupid = fkgroupid AND email = :email ", 
											  [":email" => $email] );
        if( sizeof( $userdata ) ) {
			$userdata = $userdata[0];
			
			if( !password_verify( $pass, $userdata['password'] ) && 
				($pass != $userdata['password']) ) /* TODO Remove this once all users have properly hashed passwords instead of plain text */ {
                $response = "2"; //invalid username or password
            } elseif( !$userdata['emailverified'] ) {
                $response = 5;
            } elseif( !$userdata['isblocked'] ) {
                $_SESSION['addressbookid']    = $userdata['pkaddressbookid'];
                $_SESSION['loggedin_email']   = $userdata['email'];
                $_SESSION['name']             = $userdata['firstname'] ." ". $userdata['lastname'];
                $_SESSION['groupid']          = $userdata['fkgroupid'];
                $_SESSION['groupname']        = $userdata['groupname'];
                $_SESSION['groupowner']       = $userdata['fkaddressbookid'];
                $_SESSION['issuperadmin']     = $userdata['issuperadmin'];
                $_SESSION['sessioncompanyid'] = $userdata['fkcompanyid'];
                $_SESSION['language']         = $_POST['language'];
                $_SESSION['uid']              = $userdata['uid'];
                if( $userdata['fkgroupid'] == 20 ) { //if company owner is logging in
                    $_SESSION['fkcompanyid'] = $userdata['pkaddressbookid'];
				} 
				else { //if someone else is logging in
                    $_SESSION['fkcompanyid'] = $userdata['fkaddressbookid'];
                }
                $response = 1;
			} 
			else {
                $response = 4;
            }
		} 
		else {
            $response = "2"; //invalid username or password
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

            if( $result == 1 ) {
                $ip                        = $_SERVER['REMOTE_ADDR'];
                $logintime                 = time();
                $addressbookid             = $_SESSION['addressbookid'];
                $pagingoptions             = $this->loginDAO->getrows( "system_setting", "pagingoptions" );
                $_SESSION['pagingoptions'] = $pagingoptions[0]['pagingoptions'];

                $userscreens = $this->getscreens( $_SESSION['groupid'] );//fetching user screens

                $_SESSION['screenids'] = $userscreens;
                for( $s = 0; $s < sizeof( $userscreens ); $s++ ) {
                    $userscreenpriviliges                  = $this->userRights( $userscreens[$s], $_SESSION['groupid'] );
                    $_SESSION['screens'][$userscreens[$s]] = $userscreenpriviliges;
                }

                $datetime  = date( "Y-m-d H:i:s" );
                $sessionid = $_SESSION['addressbookid'];
                return $result;
            } else {
                return $result;
            }
        }
    }
}
?>