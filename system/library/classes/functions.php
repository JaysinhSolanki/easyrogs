<?php
$decimalplaces	=	2;
$currency		=	'$';
$mdateformat	=	'%a %d %b %Y';//mysql date format
$mdatetimeformat	=	'%a %d %b %Y | %H:%i:%s ';//mysql date format
//'$mdateformat'
//date_default_timezone_set("America/Los_Angeles");
/* 
define(IMGPATH,$domain.'images/');
define(JS,$domain.'includes/js/');
define(CSS,$domain.'includes/css/');*/
//echo "<br>This is function file<br>";

function numToWord($num) // Added by JS 3/8/20
{
    $this_word = array('One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen','Twenty','Twenty-One', 'Twenty-Two', 'Twenty-Three','Twenty-Four','Twenty-Five');
    if($num <= 26)
    {
        $num--;
        $num = $this_word[$num];
    }
    return $num;
}

function custom_msg($arr)
{
	$msgarray		=	array("msgno"=>$arr['pksysmessageid'],"type"=>$arr['msgtype'],"msg"=>$arr['sysmessage']);
	echo json_encode($msgarray);
	exit;
}
function msgprint($arr)
{
	//$msgarray		=	array("type"=>$arr['msgtype'],"msg"=>$arr['sysmessage']);
	$msgarray		=	$arr['sysmessage'];
	if($msgarray)
	{
		echo json_encode($msgarray);
	}
	else
	{
		echo 11;
	}
	exit;
}
function successmsg($msg)
{
	$msgarray		=	array("success"=>'1',"msg"=>$msg);
	echo json_encode($msgarray);
	exit;
}
function errormsg($msg)
{
	$msgarray		=	array("success"=>'0',"msg"=>$msg);
	echo json_encode($msgarray);
	exit;
}
function valid($field,$label)
{
	if(empty($field))
	{
		msg(array("msgtype"=>0,"sysmessage"=>"$label cannot be left blank."));
		//errormsg("$label cannot be left blank.");
	}
}
function validnumber($field,$label)
{
 if(!is_numeric($field))
 {
  msg(array("msgtype"=>0,"sysmessage"=>"$label must be numbers only, no symbols or comas."));
 }
}
function validemail($field,$label)
{
	if(filter_var($field,FILTER_VALIDATE_EMAIL) === false)
	{
		msg(array("msgtype"=>0,"sysmessage"=>"$label is not a valid email address."));
		//errormsg("$label is not a valid email address.");
	}
}
function validpassword($field1,$field2,$label1,$label2)
{
	valid($field1,$label1);
	valid($field2,$label2);
	if($field1 != $field2)
	{
		msg(array("msgtype"=>0,"sysmessage"=>"The $label1 and $label2 do not match."));//The "Password" and "Confirm Password" do not match.
		//errormsg("$label1 and $label2 are not same.");
	}
}
function validpasswordlimit($field1,$label1)
{
	
    if ((strlen($field1) < 8)) {//(strlen(preg_replace('/[^A-Za-z]/', '', $field1)) < 1) || (preg_match('/\d/', $field1) < 1) || should contain alphabets and numbers and its
        msg(array("msgtype" => 0, "sysmessage" => "$label1 length should be 8 characters at least."));
    }
}
/***************************************************************************/
function msg($errorid,$return = 0,$markers=array(),$replacements=array())
{
	global $AdminDAO,$redirectme,$loaddiv,$loadpageurl;
	//$AdminDAO 		=	new AdminDAO();
	$errors			=	$AdminDAO->getrows("system_errors","*"," pkerrorid = '$errorid'");
	
	$pkerrorid		=	$errors[0]["pkerrorid"];
	$errormsg		=	$errors[0]["errormsg"];
	$errormsgother	=	$errors[0]["errormsgother"];
	if(sizeof($markers) > 0)
	{
		$errormsg		=	str_replace($markers,$replacements,$errors[0]["errormsg"]);
		$errormsgother	=	str_replace($markers,$replacements,$errors[0]["errormsgother"]);
	}
	$errortype		=	$errors[0]["errortype"];
	$msg_array		=	array("pkerrorid"=>$pkerrorid,"messagetype"=>$errortype, "messagetext"=>$errormsg);
	if(!empty($redirectme))
	{
		$msg_array["redirectme"]	=	$redirectme;
	}
	if(!empty($loaddiv))
	{
		$msg_array["loaddivname"]	=	$loaddiv;
	}
	if(!empty($loadpageurl))
	{
		$msg_array["loadpageurl"]	=	$loadpageurl;
	}
	
	$json			=	json_encode($msg_array);
	
	if($return==0)
	{
		echo $json;
	}
	else if($return==1)
	{
		return $json;
	}
	else 
	{
		echo $json;
		exit;
	}
}
function validate($field,$label,$message="")
{
	if(empty($field))
	{
		if($message)
		{
			echo $message;
		}
		else
		{
			echo "Please provide $label";
		}
		exit;
	}
}
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
	function filterRequest()
	{
		//array_walk_recursive($_REQUEST,$this->filter);
		foreach($_REQUEST as $key=>$val)
		{
			$ob	=	@json_decode($val);
			if($ob !== null) // JSON valid then don't filter it out...
			{
				continue;
			}
			else if(is_array($val))
			{
				foreach($val as $k=>$v)
				{
					if(is_array($v))
					{
						
						foreach($v as $k1=>$v1)
						{
							//echo "$v1 ...";
							$v[$k1]	=	$v1;//$this->filter($v1);
						}
						$val[$k]	=	$v;
					}
					else
					{
						$val[$k]	=	filter($v);
					}
				}
				$_REQUEST[$key]	=	$val;
				//print_r($val);
			}
			else
			{
				$_REQUEST[$key]	=	filter($val);
			}
		}
		
		foreach($_POST as $key=>$val)
		{
			$ob	=	@json_decode($val);
			if($ob !== null) // JSON valid then don't filter it out...
			{
				continue;
			}
			else if(is_array($val))
			{
				foreach($val as $k=>$v)
				{
					if(is_array($v))
					{
						
						foreach($v as $k1=>$v1)
						{
							//echo "$v1 ...";
							$v[$k1]	=	$v1;//$this->filter($v1);
						}
						$val[$k]	=	$v;
					}
					else
					{
						$val[$k]	=	filter($v);
					}
				}
				$_POST[$key]	=	$val;
				//print_r($val);
			}
			else
			{
				$_POST[$key]	=	filter($val);
			}
		}
		
		foreach($_GET as $key=>$val)
		{
			$ob	=	json_decode($val);
			if($ob !== null) // JSON valid then don't filter it out...
			{
				continue;
			}
			else if(is_array($val))
			{
				foreach($val as $k=>$v)
				{
					if(is_array($v))
					{
						
						foreach($v as $k1=>$v1)
						{
							//echo "$v1 ...";
							$v[$k1]	=	$v1;//$this->filter($v1);
						}
						$val[$k]	=	$v;
					}
					else
					{
						$val[$k]	=	filter($v);
					}
				}
				$_GET[$key]	=	$val;
				//print_r($val);
			}
			else
			{
				$_GET[$key]	=	filter($val);
			}
		}
	}
function fdate($date)
{
	$date			=	date("D d M Y",strtotime($date));
	return $date;
}
function numbers($num,$pr=2)
{
	return round($num,$pr);
}
function dump($var,$exit=0)
{
	print"<pre>";	
	print_r($var);
	print"</pre>";
	if($exit!=0)
	{
		exit('System stopped in functions.php on line 29');
	}
	
} 
function formatDateOnly($date)
{
	return (date("D d M Y", strtotime($date)));
} 
function formatDate($date)
{
	return (date("D d M Y H:i:s", strtotime($date)));
} 
function formatdatedb($date)
{
	$time	=	strtotime($date);
	return(date('M jS , Y', $time));
}
function formatdatetimedb($date)
{
	$time	=	strtotime($date);
	return(date('M d, Y h:m A', $time));
}
function formattimedb($time)
{
	$time	=	strtotime($time);
	return(date('h:m A', $time));
}
function formatdatetime($datetime)
{
	$datetime	=	strtotime($datetime);
	return (date('m/d/Y H:i', $datetime));
}
function buttons($action,$form,$div,$file,$place=0)
{
	global $AdminDAO;
	list($exp_data,$pkscreenid)	=	explode('=',$file);
	if($pkscreenid != "")
	{
		$screendata		=	$AdminDAO->getrows('system_screen',"*","pkscreenid = '$pkscreenid'");
		$issystemscreen	=	$screendata[0]['issystemscreen'];
		if($issystemscreen == 1)
		{
			$action	=	@$_SESSION['framework_url'].$action;
			$file	=	@$_SESSION['framework_url'].$file;
		}
	}
	//echo "$action,$form,$div,$file";
	if($place =='1')
	{
		
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
		<div style=\"float:right\">
		<span class=\"buttons\">
			<button type=\"button\" class=\"btn btn-success buttonid\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"fa-save fa\"></i>
				Save
			</button>
			 <a href=\"javascript:void(0);\" onclick=\"hidediv('sugrid');\" class=\"btn btn-danger buttonid\">
				<i class=\"fa fa-close\"></i>
				Cancel
			</a>
			
		  </span><div id='error' class='notice msg'></div>
		</div> 
		
		";
	
	}//if
	else
	{
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
				<div style=\"float:left\">
			<button type=\"button\" class=\"btn btn-success buttonid\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"fa fa-save\"></i>
				Save            </button>
			 <a href=\"javascript:void(0);\" onclick=\"hidediv('sugrid');\" class=\"btn btn-danger buttonid\">
				<i class=\"fa fa-close\"></i>
				Cancel
			</a>
			<div id='error1' class='notice msg'></div>
			</div>
		 
		  ";
	}//else
}
/*function buttoncancel($form,$div,$file)
{
		echo "
			<a href=\"javascript:void(0);\" onclick=\"hidediv('sugrid');\" class=\"btn btn-danger buttonid\">
				<i class=\"icon-remove bigger-110\"></i>
				Cancel
			</a>
		  ";
}*/
function buttonsave($action,$form,$div,$file,$place=0)
{
	//echo "$action,$form,$div,$file";
	if($place =='1')
	{
		
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
		<div style=\"float:right\">
		<span class=\"buttons\">
			<button type=\"button\" class=\"btn btn-success buttonid\" data-style=\"zoom-in\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"fa fa-save\"></i>
				<span class='ladda-label' style='color:green !important;' >Save</span><span class='ladda-spinner'></span>
			</button>
		  </span><div id='error' class='notice msg'></div>
		</div>
		";
	
	}//if
	else
	{
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
				<button type=\"button\"  class=\"btn btn-success buttonid\" data-style=\"zoom-in\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"fa fa-save\"></i>
				<span class='ladda-label'>Save</span><span class='ladda-spinner'></span></button>
		 
		  ";
	}//else
}
function buttonupload($action,$form,$div,$file,$place=0)
{
	//echo "$action,$form,$div,$file";
	if($place =='1')
	{
		
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
		<div style=\"float:right\">
		<span class=\"buttons\">
			<button type=\"button\" class=\"btn btn-success buttonid\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"icon-ok bigger-110\"></i>
				Upload
			</button>
			
		  </span><div id='error' class='notice msg'></div>
		</div> 
		
		";
	
	}//if
	else
	{
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
				<div style=\"float:left\">
			<button type=\"button\" class=\"btn btn-success buttonid\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"icon-ok bigger-110\"></i>
				Upload            </button>
			
			<div id='error1' class='notice msg'></div>
			</div>
		 
		  ";
	}//else
} 
function invoicebuttons($action,$form,$div,$file,$place=0)
{
	//echo "$action,$form,$div,$file";
	if($place =='1')
	{
		
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
		<div style=\"float:right\">
		<span class=\"buttons\">
			<button type=\"button\" class=\"btn btn-success buttonid\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"fa fa-save\"></i>
				Save
			</button>
			 <a href=\"javascript:void(0);\" onclick=\"selecttab('106_tab','invoiceupload.php','106');\" class=\"btn btn-danger buttonid\">
				<i class=\"fa fa-close\"></i>
				Cancel
			</a>
			
		  </span><div id='error' class='notice msg'></div>
		</div> 
		
		";
	
	}//if
	else
	{
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
				<div style=\"float:left\">
			<button type=\"button\" class=\"btn btn-success buttonid\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"fa fa-save\"></i>
				Save            </button>
			 <a href=\"javascript:void(0);\" onclick=\"selecttab('106_tab','invoiceupload.php','106');\" class=\"btn btn-danger buttonid\">
				<i class=\"fa fa-close\"></i>
				Cancel
			</a>
			<div id='error1' class='notice msg'></div>
			</div>
		 
		  ";
	}//else
} 
/*function buttonsave($form,$div,$file)
{
		echo "<div id='loading' class='loading' style='display:none; position:absolute; color:#F00;'></div>
		<div style=\"float:right\">
		<span class=\"buttons\">
			<button type=\"button\" class=\"btn btn-success\" onclick=\"addform('$action','$form','$div','$file');\">
				<i class=\"icon-ok bigger-110\"></i>
				Save
			</button>
			</span><div id='error' class='notice msg'></div>
		</div>";
}
*/
function CompressURL($url) 
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://to.ly/api.php?longurl=".urlencode($url));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  $shorturl = curl_exec ($ch);
  curl_close ($ch);
  return $shorturl;
}
function getlabel($formid)
{
	$langid=$_SESSION['language'];
	global $AdminDAO;
	$and   			=	"fkformid = '$formid' AND pkformid = fkformid";
	$formlabels		=	$AdminDAO->getrows('system_label, system_form',"*",$and);
	if($langid=="english")
	{
		foreach($formlabels as $formlabel)
		{
			$languagelabels[$formlabel['fieldname']] = $formlabel['label'];
		}
	}
	else
	{
		foreach($formlabels as $formlabel)
		{
			$languagelabels[$formlabel['fieldname']] = $formlabel['labelhebrew'];
		}
	}
	return $languagelabels;
} 
/******************************AMNIS*************************************/
$invoicelabelindex	=	array(
							"invoicenumber",
							"invoiceamount",	
							"currency",
							"issuedate",
							"duedate",
							"terms",
							"debitors",
						);
$invoicelables	=	array(
							"invoicenumber"	=>	"Invoice Number",
							"invoiceamount"	=>	"Invoice Amount",
							"currency"		=>	"Currency",
							"issuedate"		=>	"Issue Date",
							"duedate"		=>	"Due Date",
							"terms"			=>	"Terms",
							"debitors"		=>	"Names",//Debitors
							"category"		=>	"Category"
						);
function columnmatch($matchingarray)
{
	global $AdminDAO;
	$sessioncompanyid		=	$_SESSION['sessioncompanyid'];
	foreach($matchingarray as $marray)
	{
		$marray	=	trim($marray);
		//echo "<br>";
		//$AdminDAO->displayquery = 1;
		$results		=	$AdminDAO->getrows('tblcolumnmatch',"matchingfield","fieldname	=	'$marray' AND fkcompanyid = '$sessioncompanyid'");
		foreach($results as $result)
		{
			$resultarray[$marray][]	=		$result['matchingfield'];
		}
	}
	return $resultarray;
}
/*
********************************** REPORTS ****************************************
********************************** Daily   ****************************************
*/
/*function getDaysc($addDays)
{
    $date		=	date("d.m.Y",time());
    $ddate		=	date( 'Y-m-d', strtotime( $date . ' -1 day' ) );
    $newdate	=	strtotime ( '+'.$addDays.' day' , strtotime ( $ddate ) ) ;
	$newdate[]	=	date ( 'Y-m-d' , $newdate );
    return $newdate;
}*/
function getDays()
{
	for($i=1; $i<= 11; $i++)
	{
		$startdate		=	date("Y-m-d", strtotime("+ $i day" . ' -1 day'));		 
		$dates[]		=	array("startdate"=>$startdate,"enddate" => $startdate);
	}
	return $dates;
}
//$date 		=	date("Y-m-d");
//$days		=	getDays($date);
/*
**********************************  Weekly   ****************************************
*/
function getWeekDates($date)
{
	$dates		=	array();
	$week		= 	date('W', strtotime($date));
	$year		= 	date('Y', strtotime($date));
	$from		=	date("Y-m-d", strtotime("{$year}-W{$week}-1")); //Returns the date of monday in week
	$to			=	date("Y-m-d", strtotime("{$year}-W{$week}-7"));   //Returns the date of sunday in week
	$dates[] 	=	array("startdate"=>$from,"enddate" => $to);
	for($i=1; $i<= 10; $i++)
	{
		$start_counter	=	$i * 7;
		$end_counter	=	$i * 7;
		$start			=	date('Y-m-d', strtotime($from. " + $start_counter days"));
		$end			=	date('Y-m-d', strtotime($to. " + $end_counter days")); 
		$dates[]		=	array("startdate"=>$start,"enddate" => $end);
	}
 return $dates;
}
//$date 		=	date("Y-m-d");
//$weekdays	=	getWeekDates($date);
/*
**********************************  Weekly   ****************************************
*/
function getsalemonths($numberofmonths=12)
{
	$months		=	array();
	$startdate	=	firstday();
	$enddate	=	lastday();
	$month		=	date("m",strtotime($startdate));
	$year		=	date("Y",strtotime($startdate));
	$months[]	=	array("startdate"=>$startdate,"enddate"=>$enddate);
	for($m=1;$m<$numberofmonths;$m++)
	{
		$month	=	$month + 1;
		
		if($month > 12)
		{
			$month	=	1;
			$year	=	$year+1;
		}
		$startdate	=	firstday($month,$year);
		$enddate	=	lastday($month,$year);
		$months[]	= array("startdate"=>$startdate,"enddate"=>$enddate);	
	}
	return $months;
} 
function getmonths($numberofmonths=13)
{
	$months		=	array();
	$startdate	=	firstday();
	$enddate	=	lastday();
	$month		=	date("m",strtotime($startdate));
	$year		=	date("Y",strtotime($startdate));
	$months[]	=	array("startdate"=>$startdate,"enddate"=>$enddate);
	for($m=1;$m<$numberofmonths;$m++)
	{
		$month	=	$month + 1;
		
		if($month > 12)
		{
			$month	=	1;
			$year	=	$year+1;
		}
		$startdate	=	firstday($month,$year);
		$enddate	=	lastday($month,$year);
		$months[]	= array("startdate"=>$startdate,"enddate"=>$enddate);	
	}
	return $months;
}
function lastday($month = '', $year = '') 
{
	if (empty($month)) 
	{
		$month	=	date('m');
	}
	if (empty($year)) 
	{
		$year = date('Y');
	}
	$result	=	strtotime("{$year}-{$month}-01");
	$result	=	strtotime('-1 second', strtotime('+1 month', $result));
	return date('Y-m-d', $result);
}
function firstDay($month = '', $year = '')
{
	if (empty($month)) 
	{
		$month	=	date('m');
	}
	if (empty($year))
	{
		$year	=	date('Y');
	}
	$result	=	strtotime("{$year}-{$month}-01");
	return date('Y-m-d', $result);
}
//$monthdays	=	getmonths();
/******************************************* Planning next Week days ***************************************/
function datesOfNextWeek($date="") 
{
  $dates = array();
  if($date=="")
  {
  	$date = time();                                 // get current date.
  }
  while (date('w', $date += 86400) != 1);         // find the next Monday.
  for ($w = 0; $w < 7; $w++) {                    // get the 7 dates from it. 
    $dates[$w+1] = date('Y-m-d', $date + $w * 86400);
  }
  return $dates;
}
/**************************************Is Image*******************/
function isImage($img)
{
      return (bool)getimagesize($img);
}
function currency($number,$format)
{
	if($format == "")
	{
		$format	=	"kr";
	}
	$amount	=	number_format($number,0);
	return $amount." ".$format;
}
function makepdf($name='test.pdf',$html,$header='',$footer='')
{
	require_once($_SESSION['library_path'].'MPDF/mpdf.php');
	$mpdf=new mPDF('',    // mode - default ''
	 'A4',    // format - A4, for example, default ''
	 0,     // font size - default 0
	 '',    // default font family
	 5,    // margin_left
	 5,    // margin right
	 2,     // margin top
	 2,    // margin bottom
	 9,     // margin header 
	 9,     // margin footer
	 'L');  // L - landscape, P - portrait
	/*$stylesheet1 = file_get_contents('vendor/fontawesome/css/font-awesome.css');
	$stylesheet2 = file_get_contents('vendor/bootstrap/dist/css/bootstrap.css');
	$stylesheet3 = file_get_contents('styles/pdfstyle.css');
	//$stylesheet4 = file_get_contents('styles/reportstyle.css');
	//$stylesheet5 = file_get_contents('js/autocomplete/content/styles.css');
	$stylesheet6 = file_get_contents('styles/pdftablecss.css');
	// The parameter 1 tells that this is css/style only and no body/html/text
	$mpdf->WriteHTML($stylesheet1,1);
	$mpdf->WriteHTML($stylesheet2,1);
	$mpdf->WriteHTML($stylesheet3,1);
	$mpdf->WriteHTML($stylesheet4,1);
	$mpdf->WriteHTML($stylesheet5,1); 
	$mpdf->WriteHTML($stylesheet6,1);*/
	//$mpdf->AddPage('L');
	//$mpdf->SetHeader('Document Title|Center Text|PAGENO');
	//$mpdf->SetFooter('www.gumption.pk');
	//$mpdf->SetWatermarkText('Gumption');
	//$mpdf->showWatermarkText = true;
	//$mpdf->SetWatermarkImage('uploads/profile/logo.png'); 
	//$mpdf->showWatermarkImage = true;
	
	//$content	=  getCompanylogo();
	$content	.=  @$header;
	$content	.=  @$html;
	$content	.=  @$footer;
	//$mpdf->WriteHTML($content);
	//$mpdf->Output($name,'D');
	exit;
}
/************************JEEFF**************************/
function buttoncancel($screenid,$url)
{
	echo "
			<a style='' href=\"javascript:void(0);\" onclick=\"selecttab('{$screenid}_tab','$url','$screenid');\" class=\"btn btn-danger buttonid\">
				<i class=\"fa fa-close\"></i>
				Cancel
			</a> 
		  ";
}

function getDraft($tbl,$pkfield,$savefields=array(),$where='')
{
	global $AdminDAO;
	
	if($where == '')
	{
		$result	=	$AdminDAO->getrows($tbl,$pkfield,"is_draft = '1'");
		if(count($result)>0)
		{
			return  $result[0][$pkfield];
		}
	}
	else
	{
		$where	=	$where." AND is_draft = 1";
		
		$result	=	$AdminDAO->getrows($tbl,$pkfield,$where);
		if(count($result)>0)
		{
			return  $result[0][$pkfield];
		}
	}
	//$AdminDAO->displayquery=1;
	$fields		=	array();
	$values		=	array();
	if(sizeof($savefields) > 0)
	{
		foreach($savefields as $field => $val)
		{
			$fields[]	=	$field;
			$values[]	=	$val;
		}
	}
	/*echo "<pre>";
	print_r($fields);
	print_r($values);
	echo "</pre>";
	exit;*/

	$id	=	$AdminDAO->insertrow($tbl,$fields,$values);
	return $id;
	exit;
}