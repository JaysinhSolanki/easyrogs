<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
//dump($_POST);
$servereddate	=	$_POST['servereddate'];
$servereddate	=	str_replace("-","/",$servereddate);
$servereddate	=	date("Y-m-d",strtotime($servereddate));
$extensiondays	=	$_POST['extensiondays'];
$no_of_court_days	=	0;


//Add default 30 days extension 
$expected_duedate	=	date('Y-m-d', strtotime($servereddate. ' + 30 days'));

//Add extension on the basis of extension dropdown
if($extensiondays == 1)
{
	$duedate	=	$expected_duedate;
}
else if($extensiondays == 2)
{
	$duedate			=	date('Y-m-d', strtotime($expected_duedate. ' + 1 days'));
}
else if($extensiondays == 3)
{
	$duedate			=	date('Y-m-d', strtotime($expected_duedate. ' + 5 days'));
}
else if($extensiondays == 4)
{
	$duedate			=	date('Y-m-d', strtotime($expected_duedate. ' + 10 days'));
}
else if($extensiondays == 5)
{
	$duedate			=	date('Y-m-d', strtotime($expected_duedate. ' + 20 days'));
}
$holidays		=	$AdminDAO->getrows('holidays',"date");		
foreach($holidays as $holiday)
{
	$holidaysArray[]	=	date($dateformate,strtotime($holiday['date']));
}
//echo "Serve:{$servereddate}_____Due={$duedate}_________Response Due Date=";
//echo "Due Date: ".$duedate;
//echo "<br>";
//echo "Extention Days:".$extensiondays;
//echo "<br>";
//echo "Days:".$no_of_court_days;
//dump($holidaysArray);
$response_due_date	=	findWorkingDay($duedate,$extensiondays,$holidaysArray,$no_of_court_days);
?>
 