<?php
@session_start();
require_once("adminsecurity.php");
$id				=	$_GET['id'];
$view			=	$_GET['view'];
if($view == 1)
{
	$css	=	"disabled";
}
else
{
	$css	=	"";
}

/***************************************
		Query For Header Data
****************************************/	
$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
											'c.case_title 	as case_title,
											c.case_number 	as case_number,
											c.jurisdiction 	as jurisdiction,
											c.judge_name 	as judge_name,
											c.county_name 	as county_name,
											c.plaintiff 	,
											c.defendant 	,
											d.submit_date,
											d.send_date,
											c.court_address as court_address,
											c.department 	as department, 
											d.case_id 		as case_id,
											d.id 			as discovery_id,
											d.form_id 		as form_id,
											d.set_number 	as set_number,
											f.form_name	 	as form_name,
											f.short_form_name as short_form_name,
											a.firstname 	as atorny_fname,
											a.lastname 		as atorny_lname',
											
											"d.id 			= :id AND  
											d.case_id 		= c.id AND  
											d.form_id		= f.id AND
											d.attorney_id 	= a.pkaddressbookid",
											array(":id"=>$id));


$discovery_data		=	$discoveryDetails[0];
$case_title			=	$discovery_data['plaintiff']?> V <?php echo $discovery_data['defendant'];
$discovery_id		=	$discovery_data['discovery_id'];
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
$submit_date		=	$discovery_data['submit_date'];
$send_date			=	$discovery_data['send_date'];
/***************************************
	Query For Forms 1,2,5 Questions 
****************************************/
//$AdminDAO->displayquery=1;
$mainQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',
										'dq.answer			as 	answer,
										dq.answer_detail	as 	answer_detail,
										dq.answered_at		as 	answer_time,
										dq.id 				as 	discovery_question_id,
										q.id 				as 	question_id,
										q.question_type_id 	as 	question_type_id,
										q.question_title 	as 	question_title,
										q.question_number 	as 	question_number,
										q.sub_part 			as 	sub_part,
										q.is_pre_defined 	as 	is_pre_defined',
				
										"dq.discovery_id 	= 	:discovery_id AND  
										q.id 				= 	dq.question_id  AND
										q.sub_part 			= 	''",
										array(":discovery_id"=>$discovery_id));											
$generalQuestions	=	$AdminDAO->getrows('question_admits',"*");
?>
<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading"> Discovery Details </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover table-striped">
                  <tbody>
                     <tr>
                      <th>Case Title</th>
                      <td><?php echo $case['plaintiff']?> V <?php echo $case['defendant']?></td>
                      <th>Case#</th>
                      <td><?php echo $case_number ?></td>
                    </tr>
                     <tr>
                      <th>Jurisdiction</th>
                      <td><?php echo $jurisdiction ?></td>
                      <th>County Name</th>
                      <td><?php echo $county_name ?></td>
                    </tr>
                    <tr>
                      <th>Court Address</th>
                      <td><?php echo $court_address ?></td>
                      <th>Attorny</th>
                      <td><?php echo $atorny_name ?></td>
                    </tr>
                  </tbody>
                </table>
                <table class="table table-bordered table-hover table-striped">
                  <tbody>
                     <tr>
                     <td><?php echo $form_name." <small>".$short_form_name."</small>" ?></td>
                     <td><span style="float:right">Set No:<?php echo $set_number ?></span></td>
                     </tr>
                  </tbody>
                </table>
                <table class="table table-bordered table-hover table-striped">
                    <tbody>
                        <?php
                        if(in_array($form_id,array(1,2)))
                        {
                            
                            foreach($mainQuestions as $data)
                            {
                                $answer 			=	$data['answer'];
                                $answer_time 		=	$data['answer_time'];
                                $question_id 		=	$data['question_id'];
                                $question_type_id 	=	$data['question_type_id'];
                                $question_title 	=	$data['question_title'];
                                $question_number 	=	$data['question_number'];
                                $sub_part 			=	$data['sub_part'];
                                $is_pre_defined 	=	$data['is_pre_defined'];
                                $discovery_question_id	=	$data['discovery_question_id'];
                                if($question_type_id != 1)
                                {
                                    $subQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q',

                                                                                            'dq.answer as answer,
                                                                                            dq.answered_at as answer_time,
                                                                                            dq.id as discovery_question_id,
                                                                                            q.id as question_id,
                                                                                            q.question_type_id as question_type_id,
                                                                                            q.form_id as form_id,
                                                                                            q.question_title as question_title,
                                                                                            q.question_number as question_number,
                                                                                            q.sub_part as sub_part,
                                                                                            q.is_pre_defined as is_pre_defined',
                                                                                            
                                                                                "q.question_number 	= 	:question_number AND  
                                                                                q.id 				= 	 dq.question_id  AND
                                                                                q.sub_part 			!=   '' GROUP BY question_id",
                                                                                array(":question_number"=>$question_number)
                                                                            );
                                   
                                }
                                ?>
                                <tr>
                                    <td><b>Q: No.<?php echo $question_number ?>) </b><?php echo $question_title; ?></td>
                                    <td>
                                    <?php
                                        if($question_type_id == 1)
                                        {
                                            echo $answer; 
                                        }
                                        else if($question_type_id == 2)
                                        {
                                            if($answer == 'Yes'){echo "Yes";} 
                                            if($answer == 'No'){echo "No";}   
                                        }
                                        if($question_type_id != 1)
                                        {
                                            ?>
                                            <table class="table table-bordered table-hover table-striped">
                                                <tbody>
                                                    <?php
                                                    foreach($subQuestions as $data)
                                                    {
                                                        $answer 				=	$data['answer'];
                                                        $answer_time 			=	$data['answer_time'];
                                                        $question_id 			=	$data['question_id'];
                                                        $question_type_id 		=	$data['question_type_id'];
                                                        $form_id 				=	$data['form_id'];
                                                        $question_title 		=	$data['question_title'];
                                                        $question_number 		=	$data['question_number'];
                                                        $sub_part 				=	$data['sub_part'];
                                                        $is_pre_defined 		=	$data['is_pre_defined'];
                                                        $discovery_question_id	=	$data['discovery_question_id'];
                                                        ?>
                                                        <tr>
                                                                <td> <b><?php echo $sub_part ?>) </b><?php echo $question_title ?></td>
                                                                <td><?php echo $answer ?></td>
                                                        </tr>   
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                    ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        else if($form_id == 4)
                        {
							foreach($mainQuestions as $data)
							{
								$answer 				=	$data['answer'];
								$answer_detail 			=	$data['answer_detail'];
								$answer_time 			=	$data['answer_time'];
								$question_id 			=	$data['question_id'];
								$question_type_id 		=	$data['question_type_id'];
								$question_title 		=	$data['question_title'];
								$question_number 		=	$data['question_number'];
								$sub_part 				=	$data['sub_part'];
								$is_pre_defined 		=	$data['is_pre_defined'];
								$discovery_question_id	=	$data['discovery_question_id'];
								?>
								<tr>
									<td><b>Q: No.<?php echo $question_number ?>) </b><?php echo $question_title; ?></td>
                                    <td>
									<?php if($answer == 'Yes'){echo "Yes";} ?>
									<?php if($answer == 'No'){echo "No";} ?> 
                                    <?php
									if($answer == 'No')
									{
										?>
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                            <?php
                                            foreach($generalQuestions as $generalQuestion)
                                            {
                                                $question_admit_id	=	$generalQuestion['id'];
                                                $subQuestionAnswers	=	$AdminDAO->getrows('question_admit_results',"*",":discovery_question_id = discovery_question_id AND :question_admit_id = question_admit_id",array("discovery_question_id" => $discovery_question_id, "question_admit_id" => $question_admit_id));
                                                $subQuestionAnswer	=	$subQuestionAnswers[0]; 
                                                ?>
                                                <tr>
                                                    <td><b><?php echo $generalQuestion['question_no'] ?>) </b><?php echo $generalQuestion['question'] ?></td>
                                                    <td><?php echo $subQuestionAnswer['sub_answer'] ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
									}
                                    ?>
									</td>
								</tr>
								<?php	
							} 
                        }
                        else if(in_array($form_id,array(3,5)))
                        {
                            foreach($mainQuestions as $data)
                            {
                                
                                $answer 			=	$data['answer'];
                                $answer_detail 		=	$data['answer_detail'];
                                $answer_time 		=	$data['answer_time'];
                                $question_id 		=	$data['question_id'];
                                $question_type_id 	=	$data['question_type_id'];
                                $question_title 	=	$data['question_title'];
                                $question_number 	=	$data['question_number'];
                                $sub_part 			=	$data['sub_part'];
                                $is_pre_defined 	=	$data['is_pre_defined'];
                                $discovery_question_id	=	$data['discovery_question_id'];
                                ?>
                                <tr>
                                    <td><b>Q: No.<?php echo $question_number ?>)</b> <?php echo $question_title; ?> </td>
                                    <td>
                                        <?php
                                        if($form_id == 5)
                                        {
                                        ?>
                                        <?php if($answer == "Yes") echo "Yes"; ?>
                                        <?php if($answer == "No, because the document has never existed") echo "No, because the document has never existed"; ?>
                                        <?php if($answer == "No, because the document has been destroyed") echo "No, because the document has been destroyed"; ?>
                                        <?php if($answer == "No, because the document has been lost, misplaced, or stolen, or has never been, or is no longer, in the client's possession, custody, or control") echo "No, because the document has been lost, misplaced, or stolen, or has never been, or is no longer, in the client's possession, custody, or control"; ?>
                                        <?php
                                        }
                                        else if($form_id == 3)
                                        {
                                            echo $answer;
                                        }
                                        ?>
                                        <?php
                                        if($form_id == 5)
                                        {
                                        ?>
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><b>a) </b>Enter the name and address of anyone you believes has the documents.</td>
                                                    <td><?php echo $answer_detail ?></td>
                                                </tr>
                                            </tbody>
                                        </table> 
                                        <?php
                                        }
                                        ?>
                                      </td>
                                </tr>
                                <?php	
                            }												
                        }
                        ?>
                    </tbody>
                </table>
            <div class="text-center">
                <?php buttonsave('discoveryfrontaction.php','discoveryform',' ','thankyou.php',0); ?>
                <a style="float:right !important" target="_blank" href="makepdf.php?id=<?php echo $_GET['id']?>" class="btn btn-success" ><i class="fa fa-eye"></i> PDF</a>
            </div>
            
            </div>
        </div>
    </div>
</div>
<script src="vendor/jquery-validation/jquery-1.9.0.min.js" type="text/javascript" charset="utf-8"></script>
<script src="vendor/jquery-validation/jquery.maskedinput.js"></script>

<script>
$( document ).ready(function() 
{
   
});
function checkFunction(subdivid, option)
{
	if(option == 1)
	{
		$("#subdiv"+subdivid).show();
	}
	else  if(option == 2)
	{
		$("#subdiv"+subdivid).hide();		
	}
}
function checkFunctionForm5(subdivid, option)
{
	if(option == 'Yes')
	{
		$("#subdiv"+subdivid).hide();
	}
	else  
	{
		$("#subdiv"+subdivid).show();		
	}
}

</script>
    					
                   