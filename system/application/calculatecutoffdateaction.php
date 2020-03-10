<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");

//dump($_POST);
$trail_date	=	str_replace("-","/",$_POST['trail_date']);
$trail_date	=	date('Y-m-d',strtotime($trail_date));
$cutoffdate	=	date("Y-m-d", strtotime($trail_date. ' - 30 days'));
function isWeekend1($date) 
{
	$date	=	date('Y-m-d',strtotime($date));
    return (date('N', strtotime($date)) >= 6);
}

$holidays		=	$AdminDAO->getrows('holidays',"date");		
foreach($holidays as $holiday)
{
	$holidaysArray[]	=	date($dateformate,strtotime($holiday['date']));
}

function findWorkingDay1($date,$holidaysArray)
{
	global $dateformate;
	if(in_array($date,$holidaysArray) || isWeekend1($date))
	{
		$date	=	date($dateformate, strtotime($date. ' + 1 days'));
		findWorkingDay1($date,$holidaysArray);
	}
	else
	{
		$date	=	date($dateformate,strtotime($date)); 
		$date	=	str_replace("/","-",$date);
		echo $date;
		exit;
	}
}
$discovery_cutoff_date	=	findWorkingDay1($cutoffdate,$holidaysArray);
 ?>