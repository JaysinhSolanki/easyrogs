<?php
@session_start();
require_once("adminsecurity.php");
$id				=	$_POST['id'];
$case_id		=	$_POST['case_id'];
$form_id		=	$_POST['form_id'];
$propounding	=	$_POST['propounding'];
$responding		=	$_POST['responding'];
$set_number		=	$_POST['set_number'];
//$AdminDAO->displayquery=1;
if($id>0)
{
	$result			=	$AdminDAO->getrows(
											"discoveries"
											,
											"
											question_number_start_from as question_number
											"
											,
											"
											discoveries.id	=	:id	
											LIMIT 0,1
											"
											,
											array(
													':id' 			=>	$id,
												  )
											);
echo ((int)$result[0]['question_number']);
}
else
{
	$result			=	$AdminDAO->getrows(
											"discoveries,questions"
											,
											"
											discoveries.id as discovery_id,
											question_number
											"
											,
											"
											questions.discovery_id	=	discoveries.id	AND
											discoveries.case_id 	= 	:case_id 		AND
											discoveries.form_id 	= 	:form_id 		AND 
											discoveries.propounding = 	:propounding 	AND 
											discoveries.responding 	= 	:responding   
											ORDER BY discoveries.id DESC, questions.id DESC
											LIMIT 0,1
											"
											,
											array(
													':case_id' 		=>	$case_id,
													':form_id' 		=> 	$form_id,
													':propounding' 	=>	$propounding,
													':responding' 	=> 	$responding
												  )
											);
	if(in_array($form_id,array(3,4,5)) && $set_number == 1)
	{
		echo 1;
	}
	else
	{
		//dump($result);
		echo ((int)$result[0]['question_number'])+1;	
	}
	
}
