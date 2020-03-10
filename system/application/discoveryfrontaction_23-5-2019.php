<?php
@session_start();

include_once("../library/classes/AdminDAO.php");
$AdminDAO		=	new AdminDAO();
include_once("../library/classes/functions.php");
include_once("../library/helper.php");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(0);
$updated_by			=	$_SESSION['addressbookid'];
$form_id			=	$_POST['form_id'];
$case_id			=	$_POST['case_id'];
//$discovery_uid		=	$_POST['uid'];
$have_main_question	=	$_POST['have_main_question'];
$answers			=	$_POST['answer'];
$subanswers			=	$_POST['subanswer'];
$uid				=	$_POST['uid'];
$introduction		=	$_POST['introduction'];
$discovery_verification			=	$_POST['discovery_verification'];
$discovery_verification_state	=	$_POST['discovery_verification_state'];
$discovery_verification_city	=	$_POST['discovery_verification_city'];
$case				=	$AdminDAO->getrows("cases","*","id='$case_id'");
$form				=	$AdminDAO->getrows("forms","*","id='$form_id'");
/*if($_GET['q']==1)
{
	foreach($answers as $key=> $answer)
	{
		if(in_array($form_id,array(1,2)))
		{
			if(@$have_main_question[$key]=='2' && !is_array($answer) && trim($answer) == "")
			{
				msg(318,2);
			}
		}
		elseif(!is_array($answer) && trim($answer) == "")
		{
			msg(318,2);
		}
	}
	foreach($subanswers as  $key=>$subanswer)
	{
		foreach($subanswer as  $key1=>$subanswer1)
		{
			if(trim($subanswers[$key][$key1]) == "")
			{
				msg(318,2);
			}
		}
	}
	foreach($subanswers as  $key=>$subanswer)
	{
		if(!is_array($subanswer) && trim($subanswer) == "")
		{
			msg(318,2);
		}
		
	}
}*/

$subanswer		=	$_POST['subanswer'];
foreach($answers as $discovery_question_id => $answer)
{
	$fields	=	array("answer","updated_by");
	$values	=	array($answer,$updated_by);
	////////////////////////////////////////////
	//			IN FROM ID 5 CASE
	///////////////////////////////////////////
	if($form_id == 5)
	{
		$fields[]	=	'answer_detail';
		$values[]	=	$subanswer[$discovery_question_id];
	}
	//$AdminDAO->displayquery=1;
	$AdminDAO->updaterow('discovery_questions',$fields,$values,"id = '$discovery_question_id'");
	//$AdminDAO->displayquery=0;
}
if($form_id == 4 && !empty($subanswer))
{
	$fields1	=	array("discovery_question_id","question_admit_id","sub_answer");
	foreach($subanswer as $discovery_question_id => $subanswerArray)
	{
		$AdminDAO->deleterows('question_admit_results',"discovery_question_id	=	'$discovery_question_id'");
		foreach($subanswerArray as $question_admit_id => $sub_answer)
		{
			if($sub_answer != "")
			{
				$values1	=	array($discovery_question_id,$question_admit_id,$sub_answer);
				$AdminDAO->insertrow("question_admit_results",$fields1,$values1);	
			}
		}
	}
}
if($form_id == 5)
{
	$olddocuments	=	$_SESSION['documents'][$uid];
	/*echo "<pre>";
	print_r($olddocuments);
	echo "</pre>";*/
	
	$getdescovery_details	=	$AdminDAO->getrows("discoveries","*","uid='$uid'");
	$attorney_id			=	$getdescovery_details[0]['attorney_id'];
	$discovery_id			=	$getdescovery_details[0]['id'];
	//$AdminDAO->deleterows('documents',"attorney_id	=	'$attorney_id' AND form_id = '$form_id' AND case_id = '$case_id'");
	$AdminDAO->deleterows('documents',"discovery_id = '$discovery_id'");
	
	if(sizeof($olddocuments) > 0)
	{
		foreach($olddocuments as $data)
		{
			$doc_purpose	=	$data['doc_purpose'];
			$doc_name		=	$data['doc_name'];
			$doc_path		=	$data['doc_path'];
			if($doc_name != "")
			{
				$doc_fields		=	array("form_id",'attorney_id','case_id','document_notes','document_file_name','discovery_id');
				$doc_values		=	array($form_id,$attorney_id,$case_id,$doc_purpose,$doc_name,$discovery_id);
				$AdminDAO->insertrow("documents",$doc_fields,$doc_values);
			}
		}
	}
}
if($_GET['q']==1)
{
	
	$fields				=	array("submit_date",'is_submitted','discovery_verification','verification_state','verification_city');
	$values				=	array(date("Y-m-d G:i:s"),'1',$discovery_verification,$discovery_verification_state,$discovery_verification_city);
	
	$AdminDAO->updaterow('discoveries',$fields,$values,"uid='$uid'");
	/*$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
													'c.case_title 	as case_title,
													c.case_number 	as case_number,
													c.jurisdiction 	as jurisdiction,
													c.judge_name 	as judge_name,
													c.county_name 	as county_name,
													c.court_address as court_address,
													c.department 	as department, 
													d.case_id 		as case_id,
													d.is_opened,
													d.is_submitted,
													d.id 			as discovery_id,
													d.uid,
													d.form_id 		as form_id,
													d.set_number 	as set_number,
													f.form_name	 	as form_name,
													f.short_form_name as short_form_name,
													a.firstname 	as atorny_fname,
													a.lastname 		as atorny_lname,
													a.email',
													
													"(d.responding_uid 			= :uid OR d.propounding_uid = :uid) AND 
													d.case_id 		= c.id AND  
													d.form_id		= f.id AND
													d.attorney_id 	= a.pkaddressbookid",
													array(":uid"=>$uid)
										);


$discovery_data		=	$discoveryDetails[0];
$case_title			=	$discovery_data['case_title'];
$discovery_id		=	$discovery_data['discovery_id'];
$discovery_uid		=	$discovery_data['uid'];
$case_number		=	$discovery_data['case_number'];
$jurisdiction		=	$discovery_data['jurisdiction'];
$judge_name			=	$discovery_data['judge_name'];
$county_name		=	$discovery_data['county_name'];
$court_address		=	$discovery_data['court_address'];
$department			=	$discovery_data['department'];
$case_id			=	$discovery_data['case_id'];
$form_id			=	$discovery_data['form_id'];
$set_number			=	$discovery_data['set_number'];
$atorny_name		=	$discovery_data['atorny_fname']." ".$discovery_data['atorny_lname'];
$form_name			=	$discovery_data['form_name'];
$short_form_name	=	$discovery_data['short_form_name'];
$is_opened			=	$discovery_data['is_opened'];
$is_submitted		=	$discovery_data['is_submitted'];
$email				=	$discovery_data['email'];
ob_start();
?>
<h4><?php echo $form_name." (SET ".$set_number.")" ?> is submitted successfully</h4>  
<table width="100%" border="1" style="border-collapse: collapse;" cellpadding="10px" >
  <tbody>
     <tr>
      <th width="25%">Case Title</th>
      <td  width="25%"><?php echo $case_title ?></td>
      <th  width="25%">Case#</th>
      <td  width="25%"><?php echo $case_number ?></td>
    </tr>
     <tr>
      <th>State</th>
      <td><?php echo $jurisdiction ?></td>
      <th>County/District</th>
      <td><?php echo $county_name ?></td>
    </tr>
    <tr>
      <th>Court Address</th>
      <td><?php echo $court_address ?></td>
      <th>Attorney</th>
      <td><?php echo $atorny_name ?></td>
    </tr>
  </tbody>
</table>
<?php
$html = ob_get_contents();
ob_clean();
$to					=	array($email);
$emailmarker		=	array("customername" => $atorny_name);
send_email($to,"$form_name (SET $set_number )",$html);
*/}

msg(7);