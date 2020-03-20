<?php
/************************* Send Email *************************/
require(__DIR__."/phpmailer/src/PHPMailer.php");
require(__DIR__."/phpmailer/src/Exception.php");
function send_email($to=array(),$subject="Testing Email",$bodyhtml,$fromemail="service@easyrogs.com",$fromname="EasyRogs Service",$emailtype=1,$cc=array(),$bcc=array(),$docsArray=array())
{
	$fromname		=	"EasyRogs Service";
	$fromemail		=	"service@easyrogs.com";
	$bcc			=	array("easyrogs@gmail.com");
	if($emailtype == 3)
	{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .=	"From: $fromname <$fromemail>\r\n";
		$headers .= "Reply-To: $fromemail\r\n";
		$headers .= "Return-Path: $fromemail\r\n";
		if(sizeof($to)>0)
		{
/*
			foreach($to as $t)
			{
				if(mail($t, $subject, $bodyhtml, $headers));//{echo "success".$t.$bodyhtml;}else {echo 'error';}
			}
*/
			$tos = implode(",", $to);
			if(mail($tos, $subject, $bodyhtml, $headers));//{echo "success".$t.$bodyhtml;}else {echo 'error';}
		}
	}
	/*else if($emailtype == 2)
	{
		//$SENDGRID_API_KEY	=	"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";
		$SENDGRID_API_KEY	=	"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";
		
		require(__DIR__."/sendgrid-php/sendgrid-php.php");
		//require 'vendor/autoload.php'; // If you're using Composer (recommended)
		
		$email = new \SendGrid\Mail\Mail(); 
		$email->setFrom($fromemail, $fromname);
		$email->setSubject($subject);
		
		$email->addContent("text/html", $bodyhtml);
		$sendgrid = new \SendGrid($SENDGRID_API_KEY);
		if(!empty($to))
		{
			foreach($to as $t)
			{
				$email->addTo($t);
			}
		}
		if(!empty($bcc))
		{
			foreach($bcc as $bc)
			{
				$email->addBcc($bc);
			}
		}
		if(!empty($cc))
		{
			foreach($cc as $c)
			{
				$email->addCc($c);
			}
		}
		if(!empty($docsArray))
		{
			foreach($docsArray as $doc)
			{
				   
			}
		}
		
		try 
		{
			$response = $sendgrid->send($email);
			echo "<pre>";
			print $response->statusCode() . "\n";
			print_r($response->headers());
			print $response->body() . "\n";
			echo "</pre>";   
		} 
		catch (Exception $e) 
		{
			echo 'Caught exception: '. $e->getMessage() ."\n";
		}
		
	}	*/
	else if($emailtype == 1)
	{
		$mail = new PHPMailer;
		$mail->isHTML(TRUE);
		$mail->setFrom($fromemail, $fromname);
		$mail->Subject  = $subject;
		$mail->Body     = $bodyhtml;
		if(!empty($cc))
		{
			foreach($cc as $c)
			{
				$mail->AddCC($c);
			}
		}
		
		if(!empty($bcc))
		{
			foreach($bcc as $bc)
			{
				$mail->AddBCC($bc);
			}
		}
		if(!empty($docsArray))
		{
			foreach($docsArray as $attachment)
			{
				$mail->addAttachment($attachment['path'],$attachment['filename']);         // Add attachments       
			}
		}
		
		if(!empty($to))
		{
			foreach($to as $t)
			{
				$mail->addAddress($t);
			}
			$mail->send();
		}
	} 
}//end of send_email()

/**
* FUNCTION FOR GENERATE PDF
**/
function pdf($filename="",$footertext="",$downloadORwrite='')
{
	global $html;
	if($html=="")
	{
		echo "Please provide HTML to PDF function";
		exit;
	}
	
	//echo $html; exit;
	if(phpversion()>= '7')
	{
		require_once($_SESSION['library_path'].'pdf/mpdf/7/vendor/autoload.php');
		$mpdfConfig = array(
				'mode' => 'utf-8', 
				'format' => 'A4',    // format - A4, for example, default ''
				'default_font_size' => 0,     // font size - default 0
				'default_font' => '',    // default font family
				'margin_left' => 10,    	// 15 margin_left
				'margin_right' => 10,    	// 15 margin right
				'margin_top' => 15,     // 16 margin top
				'margin_bottom' => 8,    	// margin bottom
				'margin_header' => 0,     // 9 margin header
				'margin_footer' => 10,     // 9 margin footer
				'orientation' => 'P'  	// L - landscape, P - portrait
			);
		$mpdf = new \Mpdf\Mpdf($mpdfConfig);
	}
	else
	{
		require_once($_SESSION['library_path'].'pdf/mpdf/610/mpdf.php');     
		$mpdf=new mPDF();
		//$mode='', $format='', $font_size='', $font='', $margin_left=3, $margin_right=3, $margin_top=3, $margin_bottom=3, $margin_header='', $margin_footer=6, $orientation=''
		
	}
	
	$mpdf->setAutoTopMargin = 'stretch'; 
	$mpdf->setAutoBottomMargin = 'stretch';
	
	if($footertext != "")
	{
		$mpdf->SetFooter($footertext); 	
	}

	$mpdf->WriteHTML($html);
	
	if($filename!="")
	{
		if($downloadORwrite != 1)
		{
			$mpdf->Output($filename,"d");	
		}
		else
		{
			$mpdf->Output($filename,"F");
		}
		
	}
	else
	{
		$mpdf->Output();
	}
}
/**
* FUNCTION FOR DATE FORMAT
**/
function dateformat($date,$type=1) 
{
	if($date == "0000-00-00" || $date == "")
	{
		$date = "";
	}
	else
	{
		if($type == 1)
		{
			$date	=	date("n/j/Y",strtotime($date));
			$date	=	str_replace("/","-",$date);
		}
		else
		{
			$date	=	str_replace("-","/",$date); 
			$date	=	date("n/j/Y",strtotime($date));
		}
	}
	
	return $date;
}
/**
* FUNCTION FOR EMAILS LOG
**/
function emaillog($discovery_id,$loggedin_id,$email_subject,$send_from,$to_values=array(),$email_salutation,$email_body,$bcc_values=array(),$cc_values=array(),$sender_type,$receiver_type,$sending_script )
{
	global $AdminDAO;
	$send_to 	= 	implode(',', $to_values);
	$email_bcc = 	implode(',', $bcc_values);
	$email_cc 	= 	implode(',', $cc_values);
	
	$fields	=	array('discovery_id','loggedin_id','email_subject','send_from','send_to','email_salutation','email_body','email_bcc','email_cc','sender_type','receiver_type','sending_script');
	$values	=	array($discovery_id,$loggedin_id,$email_subject,$send_from,$send_to,$email_salutation,$email_body,$email_bcc,$email_cc,$sender_type,$receiver_type,$sending_script );
	$AdminDAO->insertrow("email_log",$fields,$values);	
}
/**
* FUNCTION FOR ADDRESS MAKE
**/
function makeaddress($pkaddressbookid,$issplit =0)
{
	global $AdminDAO;
	$results			=	$AdminDAO->getrows("system_addressbook,system_state","*","pkaddressbookid = :id AND fkstateid = pkstateid",array(":id"=>$pkaddressbookid));	
	$data				=	$results[0];
	if($issplit == 1)
	{
		$addbreak	=	"<br>";
	}
	else
	{
		$addbreak	=	", ";
	}
	$address			=	$data['address'].", ".$data['street'].$addbreak.$data['cityname'].", ".$data['statecode']." ".$data['zip'];
	return $address;
}
/**
* FUNCTION FOR GENERATE FINAL RESPONSE
**/
function finalResponseGenerate($objection,$answer)
{
	$transitiontext	=	"However, in the spirit of cooperation and without waiving any objection, respondent responds: ";
	
	if($objection == "" && $answer == "")
	{
		$finalResponse = "";
	}
	else
	{
		if($answer == "")
		{
			$finalResponse = $objection;
		}
		else if($objection == "")
		{
			$finalResponse = $answer;
		}
		else
		{
			if(!in_array(substr($objection, -1),array('!','.','?')))
			{
				$objection	=	$objection.".";
			}
			$finalResponse = $objection." ".$transitiontext." ".$answer;
		}
		if(substr($finalResponse, -1) != ".")
		{
			$finalResponse = $finalResponse.".";
		}
	}
	echo htmlspecialchars($finalResponse);
}
/**
* FUNCTION FOR INSTRUCTION
**/
function instruction($id)
{
	global $AdminDAO;
	$results			=	$AdminDAO->getrows("instructions","*","pkinstructionid = :id",array(":id"=>$id));	
	$data				=	$results[0];
	$placement			=	$data['placement'];
	$title				=	$data['title'];
	if($placement == "")
	{
		$placement = "top";
	}
	//$placement	=	$placement."-show";
	if($title == "")
	{
		$title = "No title found.";
	}
	echo '<a href="#"><i style="font-size:16px" data-placement="'.$placement.'" data-toggle="tooltip" title="'.$title.'" class="fa fa-info-circle tooltipshow" aria-hidden="true"></i></a>';
	
}
/**
* FUNCTION FOR GETTING ANSWER OF DEPENDENT PARENT QUESTION
**/
function getAnswerOfDependentParentQuestion($discovery_id,$questoin_id,$response_id)  
{
	global $AdminDAO;
	$mainQuestions	=	$AdminDAO->getrows('discovery_questions dq,questions q,response_questions rq',
										'rq.answer			as 	answer,
										rq.answer_detail	as 	answer_detail,
										rq.answered_at		as 	answer_time',
				
										"
										q.id 							= 	dq.question_id  AND
										q.id							=	'$questoin_id'	AND
										rq.fkdiscovery_question_id 		=	dq.id			AND
										rq.fkresponse_id				=	'$response_id'	AND
										dq.discovery_id 				= 	'$discovery_id' AND
										(
											q.sub_part 		= 	'' OR 
											q.sub_part IS NULL OR 
											have_main_question	IN (0,2)
											
										)
										GROUP BY q.id
										ORDER BY display_order, q.id 
										"
									  );
		if(!empty($mainQuestions))
		{
			$data				=	$mainQuestions[0];
			$answer				=	$data['answer'];
			if($answer == "")
			{
				$answer = "No";
			}
		}
		return $answer;
}
/**
* FUNCTION FOR DATE IS WEEKEND OR NOT
**/
function isWeekend($date) 
{
    return (date('N', strtotime($date)) >= 6);
}
/**
* FUNCTION FOR GETTING WORKING DATE
**/
function findWorkingDay($duedate,$extensiondays,$holidaysArray,$no_of_court_days=0)
{
	global $dateformate;
	if(in_array($duedate,$holidaysArray) || isWeekend($duedate))
	{
		//echo 1;
		$duedate	=	date('Y-m-d', strtotime($duedate. ' + 1 days'));
		findWorkingDay($duedate,$extensiondays,$holidaysArray,$no_of_court_days);
	}
	else
	{
		$no_of_court_days++;
		if($extensiondays == 2 && $no_of_court_days < 2)
		{	//echo 2;
			$duedate	=	date('Y-m-d', strtotime($duedate. ' + 1 days'));
			findWorkingDay($duedate,$extensiondays,$holidaysArray,$no_of_court_days);
		}
		else
		{
			//echo 3;
			$duedate	=	date($dateformate,strtotime($duedate));
			$duedate	=	str_replace("/","-",$duedate);
			echo  $duedate;
			//exit;
		}
	}
}
?>