<?php
@session_start();
class Login
{
	var $loginDAO	=	"";
	function Login($dao)
	{
		$this->loginDAO	=	$dao;
	}
	function userlogin($email,$pass,$type)
	{
		//$userdata		=	$this->loginDAO->getrows("system_addressbook,system_groups","*","pkgroupid = fkgroupid AND email = '$email' AND password = '$pass'");
		//$this->loginDAO->displayquery = 1;
		$userdata		=	$this->loginDAO->getrows("system_addressbook,system_groups","*","pkgroupid = fkgroupid AND email = :email ", array(":email"=>$email));
		
		if(sizeof($userdata)>0)
		{
			//echo $pass."===".$userdata[0]['password'];
			//exit;
			if ($pass !== $userdata[0]['password'])//(!password_verify($pass, $userdata[0]['password']))
			{
			  $response	=	"2"; //invalid username or password
			}
			else if($userdata[0]['emailverified']==0)
			{
				$response	=	5;
			}
			else if($userdata[0]['isblocked']==0)
			{
				$_SESSION['addressbookid']	=	$userdata[0]['pkaddressbookid'];
				$_SESSION['loggedin_email']	=	$userdata[0]['email'];
				$_SESSION['name']			=	$userdata[0]['firstname']." ".$userdata[0]['lastname'];
				$_SESSION['groupid']		=	$userdata[0]['fkgroupid'];
				$_SESSION['groupname']		=	$userdata[0]['groupname'];
				$_SESSION['groupowner']		=	$userdata[0]['fkaddressbookid'];
				$_SESSION['issuperadmin']	=	$userdata[0]['issuperadmin'];
				$_SESSION['sessioncompanyid']=	$userdata[0]['fkcompanyid'];
				$_SESSION['language']		=	$_POST['language'];
				$_SESSION['uid']			=	$userdata[0]['uid'];
				if($userdata[0]['fkgroupid']==20)//if company owner is logging in
				{
					$_SESSION['fkcompanyid']	=	$userdata[0]['pkaddressbookid'];
				}
				else//if someone else is logging in
				{
					$_SESSION['fkcompanyid']	=	$userdata[0]['fkaddressbookid'];
				}
				$response	=	1;
			}
			else
			{
				$response	=	4;
			}
		}
		else
		{
			$response	=	"2"; //invalid username or password
		}
		//echo "<pre>";
		//print_r($_SESSION);
		//exit;
		return $response;
	}// end of function login
	function history($ip,$logintime,$addressbookid)
	{
			return;
			$fields			=	array('fkaddressbookid','logintime','ipaddress');
			$data			=	array($addressbookid,$logintime,$ip);
			$insertid		=	$this->loginDAO->insertrow("loginhistory",$fields,$data);
			return $insertid;
	}
	function lastLogin($addressbookid)
	{
		$historylogs	=	$this->loginDAO->getrows("loginhistory","FROM_UNIXTIME(logintime) as logintime","fkaddressbookid = '$addressbookid'", "logintime", "DESC");
		if(sizeof($historylogs)>0)
		{
			foreach($historylogs as $historylog)
			{
				$logintime[]	=	$historylog['logintime'];
			}
			return $logintime[1];
		}
		else
		{
			return 0;
		}
	}
	function getscreens($groupid)
	{
		
		$groupscreens	=	$this->loginDAO->getrows("system_groupscreen, system_screen","*","pkscreenid = fkscreenid AND fkgroupid = :groupid AND isdeleted  = 0", array(":groupid"=>$groupid));
		//print_r($groupscreens);
		//exit;
		if(sizeof($groupscreens)>0)
		{
			foreach($groupscreens	as $groupscreen)
			{
				$screenid[] =	$groupscreen['fkscreenid'];
			}
			return $screenid;
		}
		else
		{
			return 0;
		}
	}
	function userRights($screen,$groupid)//getting screen rights
	{
		$fields	=	$this->loginDAO->getrows("system_groupfield gf, system_field f","*"," f.fkscreenid = :screen AND gf.fkgroupid = :groupid AND f.pkfieldid = gf.fkfieldid",array(":screen"=>$screen, ":groupid"=>$groupid),"sortorder","ASC");
		for($i=0;$i<sizeof($fields);$i++)
		{
			if($_SESSION['language']=='hebrew')
			{
				$label[]	=	$fields[$i]['fieldlabelherbew'];
			}else
			{
				$label[]	=	$fields[$i]['fieldlabel'];
			}
			
			$field[]	=	$fields[$i]['fieldname'];
		}
		$actions	=	$this->loginDAO->getrows("system_groupaction ga, system_action a","*"," a.fkscreenid = :screen AND ga.fkgroupid = :groupid AND a.pkactionid = ga.fkactionid",array(":screen"=>$screen, ":groupid"=>$groupid));
		for($i=0;$i<sizeof($actions);$i++)
		{
			$action[]	=	$actions[$i]['fkactionid'];
		}
		$fieldslabels['fields']		=	@array_unique($field);
		$fieldslabels['labels']		=	@array_unique($label);
		$fieldslabels['actions']	=	@array_unique($action);
		return $fieldslabels;
	}
	function loginprocess($email, $password, $usertype)
	{
		
		if($email == "" || $password == "")
		{
			return 2;
		}
		else
		{
			$result	=	$this->userlogin($email, $password, $usertype); //user authentication
			
			if($result == 1)
			{
				$ip							=	$_SERVER['REMOTE_ADDR'];
				$logintime					=	time();
				$addressbookid				=	$_SESSION['addressbookid'];
				$pagingoptions				=	$this->loginDAO->getrows("system_setting","pagingoptions");
				$_SESSION['pagingoptions']	=	$pagingoptions[0]['pagingoptions'];
				
				//$historyid				=	$this->history($ip,$logintime,$addressbookid); //inserting login history
				//$lastlogintime			=	$this->lastlogin($addressbookid); //getting last login time
				//$_SESSION['lastlogin']	=	$lastlogintime;
				$userscreens			=	$this->getscreens($_SESSION['groupid']);//fetching user screens
				
				
				$_SESSION['screenids']	=	$userscreens;
				for($s=0;$s<sizeof($userscreens);$s++)
				{
					$userscreenpriviliges	=	$this->userRights($userscreens[$s],$_SESSION['groupid']);
					$_SESSION['screens'][$userscreens[$s]]	=	$userscreenpriviliges;
				}
				//	print_r($_SESSION);
				//exit;
							//------------Code of Daily log in user login start-----------
				
				$datetime	=	date("Y-m-d H:i:s");
				$sessionid	=	$_SESSION['addressbookid'];
			/*	$query = "INSERT INTO
					system_activity
				SET
					fkaddressbookid		=	'$sessionid',
					what				=	'$what',
					activitydatetime	=	'$datetime',
					oldrecord			=	'',
					newrecord			=	''
				";
				//mysql_query($query);
				insertrow(,$field,$value);
				$this->loginDAO->executeQuery($query);
				//------------Code of Daily log in user login start-----------*/
				return $result;
			}
			else
			{
				return $result;
			}
		}
	}// end of function loginManager
}// end of class Login
?>