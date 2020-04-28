<?php

@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");

$isemail			=	$_GET['isemail'];
$incidentoption		=	$_POST['type'];
$incidentoption		=	$_POST['incidentoption'];
$incidenttext		=	$_POST['incidenttext'];
$personnames2		=	$_POST['personnames2'];
$personnames1		=	$_POST['personnames1'];
$discovery_name		=	$_POST['discovery_name'];
$client_notes		=	$_POST['client_notes'];
$form_id			=	$_POST['form_id'];
$supp				=	$_POST['supp'];
$proponding_attorney	=	$_POST['proponding_attorney'];

if($supp == 1)
{
	$parentid	=	$id;
	$id 		= 	0;
	$gParentid	=	 getGrandParent($parentid);
}
$instruction		=	html_entity_decode($_POST['instruction']);

/**
* GET GRAND PARENT ID
**/
function getGrandParent($did)
{
	global	$AdminDAO;
	$getDiscoveryDetails	=	$AdminDAO->getrows("discoveries","parentid","id = '$did'");	
	$parentid				=	$getDiscoveryDetails[0]['parentid'];
	if($parentid == 0)
	{
		return $did;
	}
	else
	{
		return getGrandParent($parentid);
	}
}

/************************************************
* Make Instruction structure
*************************************************

$instruction_html	=	$_POST['instruction_html'];

$checkedimg			=	'<img src="../uploads/icons/checkbox_checked_small.png" width="15px">';
$uncheckedimg		=	'<img src="../uploads/icons/checkbox_empty_small.png" width="15px">';
$incidenttext1		=	"&nbsp;&nbsp;(1) INCIDENT Includes the circumstances and events surrounding the alleged accident, injury, or other occurrence or breach of contract giving rise to this action or proceeding.";


if($incidentoption == 1)
{
	$incidenttext2		=	"&nbsp;&nbsp;(2) INCIDENT means (insert your definition here or on a separate, attached sheet labeled 'Sec. 4(a)(2)'):";
	$option1			=	$checkedimg.$incidenttext1;
	$option2			=	$uncheckedimg.$incidenttext2;
}
else if($incidentoption == 2)
{
	$incidenttext2		=	"&nbsp;&nbsp;(2) $incidenttext";
	$option1			=	$uncheckedimg.$incidenttext1;
	$option2			=	$checkedimg.$incidenttext2;
	
	
}

$html 				= 	preg_replace('/<div class=\"checkbox_replace1\">.*<\/div>/',$option1,$instruction_html);
$html 				= 	preg_replace('/<div class=\"checkbox_replace2\">.*<\/div>/',$option2,$html);
$instruction 		= 	preg_replace('/<div class=\"remove_incidenttext\">.*<\/div>/','',$html);

************************************************
* 	Make Instruction structure end
*************************************************/

if(trim($case_id) == '')
{
	msg(295,2);	 
}
if(trim($discovery_name) == '')
{
	msg(326,2);	 
}
if(trim($form_id) == '' && $id == '0')
{
	msg(296,2);	
}
if(trim($set_number)=='')
{
	msg(298,2);	
}
if(trim($propounding)=='')
{
	msg(314,2);	
}
elseif($isemail==1)
{
	$clients			=	$AdminDAO->getrows('clients',"*","id= :id",array('id'=>$responding));
}
if(trim($responding)=='')
{
	msg(315,2);	
}
if($propounding == $responding)
{
	msg(320,2);	
}

if(!empty($in_conjunction) && $in_conjunction == 1)
{
	$alreadyConjunctionWhere	=	"";
	if($id != "")
	{
		$alreadyConjunctionWhere	=	" AND id != $id";
	}
	$alreadyConjunction			=	$AdminDAO->getrows('discoveries',"*",
														"propounding			= 	:propounding AND
														responding 				=	:responding AND
														case_id					=	:case_id AND
														interogatory_type		=	:interogatory_type AND
														conjunction_setnumber 	= 	:conjunction_setnumber $alreadyConjunctionWhere",
														array
														(
															'responding'			=>	$responding,
															'case_id'				=>	$case_id,
															'interogatory_type'		=>	$interogatory_type,
															'conjunction_setnumber'	=>	$conjunction_setnumber,
															'propounding'			=>	$propounding
														));
	if(sizeof($alreadyConjunction) > 0)
	{
		msg(332,2);	
	}
}
function responding_uid($propounding_uid)
{
	global	$AdminDAO;
	$r_uid				=	$AdminDAO->discoveryuid();
	if($propounding_uid == $r_uid)
	{
		responding_uid($propounding_uid);
	}
	else
	{
		return $r_uid;
	}
}

if($form_id != "")
{
	$formdetails		=	$AdminDAO->getrows("forms","*","id = '$form_id'");	
}

$attorney_id	=	$_SESSION['addressbookid'];
$datetime		=	date("Y-m-d H:i:s");
if(@$_GET['serve'] == 1 && $type == 1)
{
	$served		=	$datetime;
}
/**
* If due date is null and serve date is not null then add due date on the basis of default extention days.
**/

if($served != "" )
{
	$served				=	str_replace("-","/",$served);
	$served				=	date("Y-m-d",strtotime($served));
	$extensiondays		=	2;
	$no_of_court_days	=	0;
	
	//Add default 30 days extension 
	$expected_duedate	=	date('Y-m-d', strtotime($served. ' + 30 days'));
	
	if($extensiondays == 2)
	{
		$duedate			=	date('Y-m-d', strtotime($expected_duedate. ' + 1 days'));
	}
	
	$holidays		=	$AdminDAO->getrows('holidays',"date");		
	foreach($holidays as $holiday)
	{
		$holidaysArray[]	=	date($dateformate,strtotime($holiday['date']));
	}
	
	ob_start();
	findWorkingDay($duedate,$extensiondays,$holidaysArray,$no_of_court_days);
	$response_due_date	 = ob_get_clean();
}

if($type == 2)
{
	if($due != "")
	{
		$due	=	dateformat($due,2);
		$due	=	date("Y-m-d",strtotime($due));
	}
	else
	{
		if($response_due_date != "")
		{
			$due	=	dateformat($response_due_date,2);
			$due	=	date("Y-m-d",strtotime($due));
		}
		else
		{
			$due 	= "";
		}
	}
}
else if(@$_GET['serve'] == 1 && $type == 1)
{
	$due	=	dateformat($response_due_date,2);
	$due	=	date("Y-m-d",strtotime($due));
}

if($served != "")
{
	$served	=	dateformat($served,2);
	$served	=	date("Y-m-d",strtotime($served));
}

if($id > 0 )
{
	$fields		=	array('case_id','discovery_name','set_number','propounding','responding','question_number_start_from','discovery_introduction','incidenttext','incidentoption','personnames2','personnames1');
	$values		=	array($case_id,$discovery_name,(int)$set_number,$propounding,$responding,$question_number_start_from,$discovery_introduction,$incidenttext,$incidentoption,$personnames2,$personnames1);
	$fields[]	=	"served";
	$values[]	=	$served;
	$fields[]	=	"due";
	$values[]	=	$due;
	if($type == 2)
	{
		$fields[]	=	"proponding_attorney";
		$values[]	=	$proponding_attorney;
	}
	if($form_id == 4)
	{
		$fields[]	=	"in_conjunction";
		$values[]	=	$in_conjunction;
		$fields[]	=	"conjunction_setnumber";
		$values[]	=	$conjunction_setnumber;
		$fields[]	=	"interogatory_type";
		$values[]	=	$interogatory_type;
	}
	if(!in_array($form_id,array(1,2))) //FROGS AND FROGSE IN EXTERNAL CASE
	{
		$fields[]	=	"discovery_instrunctions";
		$values[]	=	$instruction;
	}
	$AdminDAO->updaterow("discoveries",$fields,$values,"id ='$id'");
	
	$selectedids	=	array();
	if(!empty($is_selected) && @count($is_selected)>0)
	{
		foreach($is_selected as $key=>$val)
		{
			if($val==1)
			{
				$selectedids[]	= $questions[$key];	
			}
		}
		$AdminDAO->deleterows('discovery_questions',"discovery_id ='$id' AND question_id NOT IN  ( '".implode("','",$selectedids)."')");
	} 
	if(!empty($selectedids) && $id>0)
	{
		foreach($selectedids as $thisrow)
		{
			$isexist	=	$AdminDAO->getrows('discovery_questions',"*","discovery_id	= :discovery_id AND question_id	= :question_id",array('discovery_id'=>$id,'question_id'=>$thisrow));
			if(@count($isexist)==0)
			{
				$fields	=	array('question_id','discovery_id');
				$values	=	array($thisrow,$id);
				if(isset($extra_text[$thisrow]))
				{
					$fields[]	=	"extra_text";
					$values[]	=	$extra_text[$thisrow];
				}
				$AdminDAO->insertrow("discovery_questions",$fields,$values);
			}
			else if(isset($extra_text[$thisrow]))
			{
				$discoveryquestionid	=	$isexist[0]['id'];
				$fields					=	array('extra_text');
				$values					=	array($extra_text[$thisrow]);
				$AdminDAO->updaterow("discovery_questions",$fields,$values,"id='$discoveryquestionid'");
			}
		}
	}
} 
else 
{
	
	$uid		=	$AdminDAO->generateuid('discoveries');
	$fields		=	array('uid','type','discovery_name','case_id','form_id','attorney_id','set_number','propounding','responding','question_number_start_from','incidenttext','incidentoption','personnames2','personnames1');
	$values		=	array($uid,$type,$discovery_name,$case_id,(int)$form_id,$attorney_id,(int)$set_number,$propounding,$responding,$question_number_start_from,$incidenttext,(int)$incidentoption,$personnames2,$personnames1);
	if($type == 2)
	{
		$fields[]	=	"proponding_attorney";
		$values[]	=	$proponding_attorney;
	}
	if($supp == 1)
	{
		$fields[]	=	"grand_parent_id";
		$values[]	=	$gParentid;
		$fields[]	=	"parentid";
		$values[]	=	$parentid;
		if($served != "")
		{
			$fields[]	=	"served";
			$values[]	=	$served;
		}
		if($due != "")
		{
			$fields[]	=	"due";
			$values[]	=	$due;
		}
	}
	else 
	{
		$fields[]	=	"served";
		$values[]	=	$served;
		$fields[]	=	"due";
		$values[]	=	$due;
	}
	if($form_id == 4)
	{
		$fields[]	=	"in_conjunction";
		$values[]	=	$in_conjunction;
		$fields[]	=	"conjunction_setnumber";
		$values[]	=	$conjunction_setnumber;
		$fields[]	=	"interogatory_type";
		$values[]	=	$interogatory_type;
	}
	if(!in_array($form_id,array(1,2))) //FROGS AND FROGSE IN EXTERNAL CASE
	{
		$fields[]	=	"discovery_instrunctions";
		$values[]	=	$instruction;
	}
	$id	= $AdminDAO->insertrow("discoveries",$fields,$values);

	if(!empty($questions))
	{
		foreach($questions as $key=>$thisrow)
		{
			if($is_selected[$key]==1 && $id>0)
			{
				
				$fields	=	array('question_id','discovery_id');
				$values	=	array($thisrow,$id);
				if(isset($extra_text[$thisrow]))
				{
					$fields[]	=	"extra_text";
					$values[]	=	$extra_text[$thisrow];
				}
				$AdminDAO->insertrow("discovery_questions",$fields,$values);
			}
		}	
	}
}
 
//Upload documents here
$doc_uid = $_POST['uid'];
if(in_array($form_id,array(4,3)))
{
	$olddocuments			=	isset($_SESSION['documents'][$doc_uid]) ? $_SESSION['documents'][$doc_uid] : [];
	$getdescovery_details	=	$AdminDAO->getrows("discoveries","*","id='$id'");
	$attorney_id			=	$getdescovery_details[0]['attorney_id'];
	$discovery_id			=	$getdescovery_details[0]['id'];
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

if(!empty($new_questions) && @count($new_questions)>0)
{
	foreach($new_questions  as $key=>$thisrow)
	{
		if($id>0)
		{
			if($question_titles[$key] != '')
			{
				if($new_questions[$key] > 0 && $supp != 1)
				{
					$fields	=	array('question_title','question_number');
					$values	=	array($question_titles[$key],$question_numbers[$key]);
					$AdminDAO->updaterow("questions",$fields,$values,"id ='{$new_questions[$key]}'");
				}
				else
				{
					$fields	=	array('discovery_id','question_title','question_number','attorney_id','question_type_id');
					$values	=	array($id,$question_titles[$key],$question_numbers[$key],$attorney_id,2);
					$q_id	= 	$AdminDAO->insertrow("questions",$fields,$values);
					
					$fields	=	array('question_id','discovery_id');
					$values	=	array($q_id,$id);
					$AdminDAO->insertrow("discovery_questions",$fields,$values);
				}	
			}
			else
			{
				if(!in_array($form_id,array(1,2))) //FROGS AND FROGSE IN EXTERNAL CASE
				{
					$delQuestionId	=	 $new_questions[$key];
					$AdminDAO->deleterows('questions',"id	=	'$delQuestionId'");
					$AdminDAO->deleterows('discovery_questions',"question_id	=	'$delQuestionId'");
				}
			}
		}
	}
}

if($isemail==1)
{	
	//Update Send To client details
	if($is_send == 0)	
	{
		$fields	=	array("is_send",'send_date');
		$values	=	array(1,date("Y-m-d H:i:s"));
		$AdminDAO->updaterow('discoveries',$fields,$values,"id='$id'");
	}

	$discovery = $discoveriesModel->find($id);
	DiscoveryMailer::clientResponse($discovery, $currentUser->user);
}

//Update Declaration Text in Create case
$signdeclarationdataarray	=	$_SESSION['signdeclarationdataarray'];

if(!empty($signdeclarationdataarray))
{
	$declaration_text		=	$signdeclarationdataarray['declaration_text'];
	$declaration_updated_at	=	$signdeclarationdataarray['declaration_updated_at'];
	$declaration_updated_by	=	$signdeclarationdataarray['declaration_updated_by'];
	$dec_city	=	$signdeclarationdataarray['dec_city'];
	$dec_state	=	$signdeclarationdataarray['dec_state'];
	$fields		=	array('declaration_text','declaration_updated_at','declaration_updated_by','dec_state','dec_city');
	$values		=	array($declaration_text,$declaration_updated_at,$declaration_updated_by,$dec_state,$dec_city);
	$AdminDAO->updaterow("discoveries",$fields,$values,"id ='$id'");
	$_SESSION['signdeclarationdataarray']	=	array();
}

echo json_encode([
	"pkerrorid"=>"7",
	"messagetype"=>"2",
	"messagetext"=>"Data has been saved successfully.",
	"id" => $id,
	"uid"=>$uid
]);