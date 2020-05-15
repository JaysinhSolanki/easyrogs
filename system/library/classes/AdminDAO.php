<?php
/*********************************************************************************
*   Description: This class is for communicating  with db layer,
*   Who/When: July 2010
**********************************************************************************/
//error_reporting(E_ERROR);
//require_once($_SESSION['system_path']."includes/classes/DBManager.php");
//require_once($_SESSION['system_path']."bootstrap.php");
//error_reporting(0);
// start of the class
class AdminDAO
{
	/*************************************getrows()****************************************/
	//@params: NONE
	//Who/When: Gumption Technologies / 15 May 2017
	//@return: MYSQL resultset
	public $displayquery;
	public $query;

	public $dbhost		=	DBHOST;//'localhost';
	public $dbusername	=	DBUSER;//'root';
	public $dbpassword =	DBPASS;
	public $dbname		=	DBNAME;//'gumption_michaelwuest';
	public $dbconn;

	function encrypt($password)
	{
		/*$options = array(
							'cost' => 12,
							'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
					);*/
		return(password_hash($password, PASSWORD_BCRYPT));
	}

	function connect()
	{
		$this->dbconn	=	new PDO("mysql:host=".$this->dbhost.";dbname=".$this->dbname,$this->dbusername, $this->dbpassword);
		$this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	function executeQuery($query)
	{
		if($this->displayquery == 1)
		{
			echo $query;
		}

		$this->connect();

		//$db = new PDO("...");
		$statement = $this->dbconn->prepare($query); //"select id from some_table where name = :name"
		$statement->execute();//array(':name' => "Jimbo")
		return($statement->fetchAll());

	}


	function queryresult($query)
	{
		$this->connect();

		 $this->displayquery($query, $parameters);
		$statement = $this->dbconn->prepare($query);
		$statement->execute();
		$allrows_array	=	$statement->fetchAll();

		$statement = $this->dbconn->prepare("SELECT FOUND_ROWS() as totalrows");
		$statement->execute();
		$totalrows	=	$statement->fetch(PDO::FETCH_ASSOC);

		$_SESSION['totalrows']	=	$totalrows['totalrows'];

		return ($allrows_array);
	}

	function getrows($tbl,$fields, $where='',$parameters=array() ,$sort_index='',$sort_order='',$start='',$limit='')
	{
		$sort		=	"";
		$records	=	"";
		if($sort_index!='' && $sort_order!='')
		{
			$sort=" ORDER BY $sort_index $sort_order ";
		}
		if($limit!='')
		{
			$records=" LIMIT $start , $limit ";
		}

		if($where!='')
		{
			$where=" WHERE $where ";
		}


		$this->query = "SELECT
						$fields
					FROM
						$tbl
						$where
						$sort  $records
					";
		$this->displayquery($this->query, $parameters);
		//echo "<Pre>";
			//print_r($fieldvaluesarray);
		$this->connect();


		if(!$statement = $this->dbconn->prepare($this->query)) //"select id from some_table where name = :name")
		{
			$this->displayquery($this->query, $parameters);
		}
		//echo "statment ...<br>";
		//print_r($statement);
		if(@sizeof($parameters) > 0)
		{
			$statement->execute($parameters);
		}
		else
		{
			$statement->execute();
		}

		$allrows_array	=	$statement->fetchAll(PDO::FETCH_ASSOC);
		/*if(sizeof($allrows_array) == 1 )
		{
			$first	=	$allrows_array[0];
			$first[0]	=	$allrows_array[0];
			return $first;
		}*/
		return ($allrows_array);
	}//end of get rows
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Gumption Technologies / 16 May 2016
	//@return: MYSQL deleting data from selected table
	function deleterows($tbl,$where='', $whereparameters = array())
	{
		$this->connect();
		$parameters	=	array();
		if(sizeof($whereparameters)  > 0 )
		{
			foreach($whereparameters as $wpkey => $wpvalue)
			{
					$parameters[":{$wpkey}"]= $wpvalue;
			}
		}

		$this->query =	" DELETE FROM  $tbl WHERE  $where ";
		$this->displayquery($this->query, $parameters);
		$statement = $this->dbconn->prepare($this->query);
		if($statement->execute($parameters))
		{
			return 1;
		}
		else
		{
			return 0;
		}
		//$this->executeNonQuery($query);
	}//end of deleterows
	/*************************************insertrow()****************************************/
	//@params: NONE
	//Who/When: Gumption Technologies / 17 May 2017
	//@return: MYSQL inserting data into selected table
	function insertrow($table,$field,$value,$orUpdate=false)
	{
		$field[]	=	'updated_at';
		$value[]	=	date("Y-m-d H:i:s");

		$field[]	=	'updated_by';
		$value[]	=	$_SESSION['addressbookid'] ?: 0;
		$this->connect();
		$fieldstr	=	"";
		$valuestr	=	"";
		$parameters	=	array();
		$fieldstr	=	implode(",", $field);
		foreach($field as $k)
		{
			$valuestr	.=	":$k,";
		}
		for($i=0;$i<sizeof($field);++$i)
		{
			$parameters[":{$field[$i]}"]=  $this->filter($value[$i]);
		}
		$valuestr	= trim($valuestr,",");
		for($i=0;$i<sizeof($field);++$i)
		{
			$parameters[":{$field[$i]}"]=  $this->filter($value[$i]);
		}
		$this->query =	"INSERT INTO $table($fieldstr) VALUES($valuestr)";
		if($orUpdate) {
			$keyvalues = '';
			for($i=0;$i<sizeof($field);++$i)
			{
				$keyvalues .=  $field[$i] .'=:'. $field[$i] .",\n";
			}
			$keyvalues = trim($keyvalues,",\n\r");
			$this->query .= " ON DUPLICATE KEY UPDATE $keyvalues";				
		}

		$this->displayquery($this->query, $parameters);

		$statement = $this->dbconn->prepare($this->query);
		$statement->execute($parameters);
		return $this->dbconn->lastInsertId();
	}//end of insertrow
	/*************************************updaterow()****************************************/
	//@params: NONE
	//Who/When: Gumption Technologies / 16 May 2016
	//@return: MYSQL updating data in selected table
	function displayquery($query, $parameters)
	{
		if($this->displayquery > 0)
		{
			echo "<h2><br>=======================Query Start=========================</h2>";
			echo $query;
			echo "<br>==========================Parameters================================<br>";
			echo "<pre>";
			print_r($parameters);
			echo "</pre>";
			echo "<h2><br>=======================Query END=========================</h2>";
		}
	}
	function updaterow($table,$field,$value,$where = "", $whereparameters= array())
	{
		$field[]	=	'updated_at';
		$value[]	=	date("Y-m-d H:i:s");

		$field[]	=	'updated_by';
		$value[]	=	$_SESSION['addressbookid'] ?: 0;
		$this->connect();
		$fieldstr	=	"";
		$valuestr	=	"";
		$parameters		=	array();

		/*
		* UPDATE table SET field1 = :f1, field2=:f2 WHERE field3 > :f3 AND field4 = 2190

		array(":f1"=>Umar, ":f2"=>Kashif, ":f3"=>"2016" )
		*/

		//$fieldstr	=	implode(",", $field);
		foreach($field as $fieldname)
		{
			$fieldstr	.=	"$fieldname = :$fieldname,";
		}
		$fieldstr	= trim($fieldstr,",");


		for($i=0;$i<sizeof($field);++$i)
		{
			$parameters[":{$field[$i]}"]= $value[$i];
		}

		if(sizeof($whereparameters)  > 0 )
		{
			foreach($whereparameters as $wpkey => $wpvalue)
			{
					$parameters[":{$wpkey}"]= $this->filter($wpvalue);
			}
		}

		$this->query	=	"UPDATE $table SET $fieldstr WHERE $where";
		$this->displayquery($this->query, $parameters);
		$statement = $this->dbconn->prepare($this->query);
		if($statement->execute($parameters))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}//end of updaterow
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Gumption Technologies / 16 May 2016
	//@return: MYSQL deleting data from selected table
	function deleterecord($tbl,$pk,$value)
	{
//		echo $tbl.$pk.$value;
		$query = "DELETE
		  				FROM
							$tbl
					 	WHERE
							$pk='$value'
					";
		$pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
		//$this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
		$this->executeNonQuery($query);
		//$this->updatelog($pkqueryloggerid);
		//$allrows_result			=	$this->executeNonQuery($query);
	}//end of deleterows
	/*************************************isunique()****************************************/
	//@params: NONE
	//Who/When: Gumption Technologies / 16 May 2016
	//@return: MYSQL checking unique data for editing purposes
	function isunique($table, $key, $keyid, $field, $data)
	{
		if($keyid > 0)
		{
			//print"------------------------------------------------------------------------<br>";
			$rows 	= 	$this->getrows($table,$field, " $key<>:keyid AND $field=:data", array(":keyid"=>$keyid, ":data"=>$data));
			//print"------------------------------------------------------------------------<br>";
		}
		else
		{
			$rows 	= 	$this->getrows($table,$field, " $field= :data", array(":data"=>$data));
		}

		if($rows)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}//end of isunique

	/***************************/
	function logactivity($what,$oldrecord,$newrecord)
	{
		if(is_array($newrecord))
		{
			$newrecord	=	json_encode($newrecord);
		}

		if(is_array($oldrecord))
		{
			$oldrecord	=	json_encode($oldrecord);
		}
		$datetime	=	date("Y-m-d H:i:s");
		$sessionid	=	$_SESSION['addressbookid'];
		$query = "INSERT INTO
					system_activity
				SET
					fkaddressbookid		=	'$sessionid',
					what				=	'$what',
					activitydatetime	=	'$datetime',
					oldrecord			=	'$oldrecord',
					newrecord			=	'$newrecord'
				";
		$activitylog	=	$this->executeNonQuery($query);
		return $activitylog;
	}//end of updatelog

	/**********************/
	function checkdbfields($section,$table,$fields,$page)
	{
		$sql=" SELECT $fields from $table order by 1 DESC";
		$result = $this->executeQuery($sql);
		$farray	=	explode(',',$fields);
		//print_r($farray);
		$link="";
		$count=0;
		print"<ul>";
		while($allrows_array	=	@mysql_fetch_assoc($result))
		{
			$flag=0;
			for($a=0;$a<count($farray);$a++)
			{
				$res	=	 $allrows_array[$farray[$a]];
				if($res=='' || $res=='0' )
				{

					$flag=1;
					//echo $a.'=>'.$farray[$a].'=='.$res.' : Empty'.'<br>';
				}//end of if

			}//end of for
			if($flag==1)
			{
				$link.="<li><a href=\"Javascript: loadactionitem('".$page."','".$allrows_array[$farray[0]]."')\">This <b>".$allrows_array[$farray[1]]."</b> $section Require Attention</a></li>";
			$count++;
			}//end of flag

		}//end of while
		if($link!='')
		{
			echo $link;
		}
		else
		{
			print"<li> No Action item found in this Section.</li>";
		}
		print"<ul>";
		print"<br><b>Total Items:</b> $count";
	}//end of checkdbfields
	function getprimarykey($table)
	{
		$result = $this->executeQuery("SHOW COLUMNS FROM $table");
		while ($row = mysqli_fetch_assoc($result))
		{
			if($row['Key'] == 'PRI')
			{
				return($row['Field']);
			}
		}//while
	}//getprimarykey

	function logquery($query,$type,$table,$pk,$pkvalue,$fkstoreid,$querytime,$pkqueryloggerid)
	{
		return;
		session_start();
	//	$query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid
		$query		=	addslashes($query);
		$table		=	addslashes($table);
		$pkvalue	=	addslashes($pkvalue);
		$employeeid =	$_SESSION['addressbookid'];
		//$pkvalue	=	mysql_insert_id();
		$queryx		=	 "INSERT INTO `querylogger` SET
		`query` = \"$query\",
		`type` = '$type',
		`table` = '$table',
		`pk` = '$pk',
		`pkvalue` = '$pkvalue',
		`fkstoreid` = '$fkstoreid',
		`querytime` = '$querytime',
		`fkemployeeid` = '$employeeid'
		";
		$this->executeNonQuery($queryx);
	}
	function logquery2db($query,$type,$table,$pk,$pkvalue,$fkstoreid,$querytime,$pkqueryloggerid,$database)
	{
		return;
		session_start();
	//	$query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid
		$query		=	addslashes($query);
		$table		=	addslashes($table);
		$pkvalue	=	addslashes($pkvalue);
		$employeeid =	$_SESSION['addressbookid'];
		//$pkvalue	=	mysql_insert_id();
		$queryx		=	 "INSERT INTO $database.querylogger SET
		`query` = \"$query\",
		`type` = '$type',
		`table` = '$table',
		`pk` = '$pk',
		`pkvalue` = '$pkvalue',
		`fkstoreid` = '$fkstoreid',
		`querytime` = '$querytime',
		`fkemployeeid` = '$employeeid'
		";
		$this->executeNonQuery($queryx);
	}
function dropdown($name,$tblname,$valuefield,$labelfield,$where='',$selected =array(),$multiple=0,$js="")
{
	list($name,$ins)	=	explode(":",$name);
	if($multiple!=0)
	{
		$multiple	=	" multiple = \"multiple\" ";
		$dropdownname	=	$tblname."[]";
	}
	else
	{
		$multiple	=	"";
		$dropdownname	=	$tblname;
	}
	$select	=	ucfirst($name);
	if(strpos($labelfield,"as"))
	{
		list($xtra,$orderby)	=	explode("as",$labelfield);
	}
	else
	{
		$orderby	=	$labelfield;
	}
	$query	=	"SELECT $valuefield,$labelfield FROM $tblname WHERE 1 $where ORDER BY $orderby";
	$res	=	$this->executeQuery($query);
	$width	=	"200px";
	$txt	=	"Select";
	if($ins)
	{
		$width	=	"150px";
		$txt	=	"All";
	}
	$orderby	=	trim($orderby);
	echo "<select name=\"$name\" id=\"$name\" $multiple $js>";
	while($row	=	mysql_fetch_assoc($res))
	{
		echo "<option value=\"$row[$valuefield]\"";
		if(is_array($selected))
		{
			if(in_array($row[$valuefield],$selected))
			{
				echo " selected=\"selected\" ";
			}
		}//if
		echo ">$row[$orderby] </option>";
	}
	echo "</select>";
}//dropdown
function checkbox($name,$tblname,$valuefield,$labelfield,$selected =array(),$multiple=0,$js="")
{
	if($multiple!=0)
	{
		$multiple	=	" multiple = 'multiple' ";
		$dropdownname	=	$tblname."[]";
	}
	else
	{
		$multiple	=	"";
		$dropdownname	=	$tblname;
	}

	$select	=	ucfirst($name);
	$query	=	"SELECT $valuefield,$labelfield FROM $tblname";
	$res	=	$this->executeQuery($query);
	//echo "<select style='width: 265px;' name='$name' id='$name' $multiple $js>";
	while($row	=	mysql_fetch_assoc($res))
	{
		echo "<input type='checkbox' name=$name  value=\"$row[$valuefield]\"";
		if(is_array($selected))
		{
			if(in_array($row[$valuefield],$selected))
			{
				echo " checked='checked' ";
			}
		}//if
		echo ">$row[$labelfield]<br>";
	}
}//checkbox
function radiobuttons($name,$tblname,$valuefield,$labelfield,$selected =array(),$multiple=0,$js="")
{
	if($multiple!=0)
	{
		$multiple	=	" multiple = 'multiple' ";
		$dropdownname	=	$tblname."[]";
	}
	else
	{
		$multiple	=	"";
		$dropdownname	=	$tblname;
	}
	$select	=	ucfirst($name);
	$query	=	"SELECT $valuefield,$labelfield $fields FROM $tblname";
	$res	=	$this->executeQuery($query);
	//echo "<select style='width: 265px;' name='$name' id='$name' $multiple $js>";
	while($row	=	mysql_fetch_assoc($res))
	{
		echo "<input  type='radio' $js name=$name  value=\"$row[$valuefield]\"";
		if(is_array($selected))
		{
			if(in_array($row[$valuefield],$selected))
			{
				echo " checked='checked' ";
			}
		}//if
		echo ">$row[$labelfield]";
		$tr	.=	"<tr bgcolor='#909090' style='color: #FFF;'>
					<td>&nbsp;$row[$labelfield]</td>
					<td>&nbsp;$row[timing]</td>
				</tr>";
	}
}//radiobuttons

 function checkUser($uid, $oauth_provider, $username,$email,$twitter_otoken,$twitter_otoken_secret)
	{
        $query = mysql_query("SELECT * FROM `users` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'") or die(mysql_error());
        $result = mysql_fetch_array($query);
        if (!empty($result)) {
            # User is already present
        } else {
            #user not present. Insert a new Record
            $query = mysql_query("INSERT INTO `users` (oauth_provider, oauth_uid, username,email,twitter_oauth_token,twitter_oauth_token_secret) VALUES ('$oauth_provider', $uid, '$username','$email')") or die(mysql_error());
            $query = mysql_query("SELECT * FROM `users` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
            $result = mysql_fetch_array($query);
            return $result;
        }
        return $result;
    }
	/****************************generate uid********************/
	function generatePassword ($length = 16)
	{

			// start with a blank password
			$password = "";

			// define possible characters - any character in this string can be
			// picked for use in the password, so if you want to put vowels back in
			// or add special characters such as exclamation marks, this is where
			// you should do it
			$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

			// we refer to the length of $possible a few times, so let's grab it now
			$maxlength = strlen($possible);

			// check for length overflow and truncate if necessary
			if ($length > $maxlength) {
			  $length = $maxlength;
			}

			// set up a counter for how many characters are in the password so far
			$i = 0;

			// add random characters to $password until $length is reached
			while ($i < $length) {

			  // pick a random character from the possible ones
			  $char = substr($possible, mt_rand(0, $maxlength-1), 1);

			  // have we already used this character in $password?
			  if (!strstr($password, $char)) {
				// no, so it's OK to add it onto the end of whatever we've already got...
				$password .= $char;
				// ... and increase the counter by one
				$i++;
			  }

			}

			// done!
			return $password;

	}
	function generateuid($tbl, $length	=	16,$field=0) // t = seconds, f = separator
	{


		$uid	=	$this->generatePassword($length);
		if($field)
		{

			 $query	=	"SELECT count(*) as c FROM $tbl WHERE $field =	'$uid'";
		}
		else
		{
					$query	=	"SELECT count(*) as c FROM $tbl WHERE uid =	'$uid'";

		}

		$row	=	$this->queryresult($query);
		if($row['c']>0)
		{
			$uid	=	generateuid($tbl,$length);
		}
		else
		{
			return $uid;
		}
	}
	function discoveryuid($tbl='discoveries', $length	=	16,$field=0) // t = seconds, f = separator
	{
		$uid	=	$this->generatePassword($length);
		$query	=	"SELECT count(*) as c FROM $tbl WHERE propounding_uid =	'$uid' || responding_uid =	'$uid'";
		$row	=	$this->queryresult($query);
		if($row['c']>0)
		{
			$uid	=	generateuid($tbl,$length);
		}
		else
		{
			return $uid;
		}
	}
	function generatecode ($length = 16)
	{
			$password = "";
			$possible = "0123456789";

			$maxlength = strlen($possible);

			if ($length > $maxlength) {
			  $length = $maxlength;
			}

			$i = 0;

			while ($i < $length) {

			  $char = substr($possible, mt_rand(0, $maxlength-1), 1);

			  if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			  }

			}
			return $password;

	}
	function generatecouponcode($tbl, $length	=	16) // t = seconds, f = separator
	{
		$uid	=	$this->generatecode($length);
		$query	=	"SELECT count(*) as c FROM $tbl WHERE code =	'$uid'";
		$row	=	$this->queryresult($query);
		if($row['c']>0)
		{
			$uid	=	generatecouponcode($tbl,$length);
		}
		else
		{
			return $uid;
		}
	}
	function formatcurrency($currency)
	{
		$cur	=	number_format($currency,'2', '.', ',');
		return $cur;
	}
	/*function formatcurrency($currency)
	{
		$cur	=	number_format($currency,'2', '.', ',');
		return $cur;
	}
	*/
	function filter($input)
	{
		 if (get_magic_quotes_gpc()==1)
		 {
		  	return(htmlentities(trim($input),ENT_QUOTES));
		 }
		 else
		 {
		  	return(htmlentities((trim($input)),ENT_QUOTES));
		 }
	}
	function startsWith($haystack, $needle = '@')
	{
		preg_match_all("/(?<!\w)*{$needle}\w+/",$haystack,$matches);
		return $matches;
	}
}//end of class