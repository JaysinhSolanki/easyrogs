<?php
/************************* Send Email *************************/
require(__DIR__."/phpmailer/src/PHPMailer.php");
require(__DIR__."/phpmailer/src/Exception.php");
function send_email($to = array(), $subject = "Testing Email", $bodyhtml, $fromemail = "service@easyrogs.com", $fromname = "EasyRogs Service", $emailtype = 1, $cc = array(), $bcc = array(), $docsArray = array())
{
    $fromname       =   "EasyRogs Service";
    $fromemail      =   "service@easyrogs.com";
    $bcc            =   array("easyrogs@gmail.com");
    if ($emailtype == 3) {
        $headers  = 'MIME-Version: 1.0' . "\r\n";

        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .=     "From: $fromname <$fromemail>\r\n";
        $headers .= "Reply-To: $fromemail\r\n";
        $headers .= "Return-Path: $fromemail\r\n";
        if (sizeof($to)>0) {
/*
			foreach($to as $t)
			{
				if(mail($t, $subject, $bodyhtml, $headers));//{echo "success".$t.$bodyhtml;}else {echo 'error';}
			}
*/
            $tos = implode(",", $to);
            if (mail($tos, $subject, $bodyhtml, $headers)) {
//{echo "success".$t.$bodyhtml;}else {echo 'error';}
            }
        }
    } /*else if($emailtype == 2)
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
    elseif ($emailtype == 1) {
        $mail = new PHPMailer;
        $mail->isHTML(true);
        $mail->setFrom($fromemail, $fromname);
        $mail->Subject  = $subject;
        $mail->Body     = $bodyhtml;
        if (!empty($cc)) {
            foreach ($cc as $c) {
                $mail->AddCC($c);
            }
        }

        if (!empty($bcc)) {
            foreach ($bcc as $bc) {
                $mail->AddBCC($bc);
            }
        }
        if (!empty($docsArray)) {
            foreach ($docsArray as $attachment) {
                $mail->addAttachment($attachment['path'], $attachment['filename']);         // Add attachments
            }
        }

        if (!empty($to)) {
            foreach ($to as $t) {
                $mail->addAddress($t);
            }
            $mail->send();
        }
    }
}//end of send_email()


function convertYoutube($string) {
    return preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "<br /><iframe src=\"//www.youtube.com/embed/$2\" allowfullscreen width='100%' height='400'></iframe>",
        $string
    );
}

function replaceUrls($string) {
	$url = $string[0];
	if (filter_var($url, FILTER_VALIDATE_URL)) {
	    $images_allowed = array('gif', 'png', 'jpg', 'svg', 'jpeg');
		$ext = pathinfo($string[0], PATHINFO_EXTENSION);
		if (in_array($ext, $images_allowed)) {
		    return '<br /><img src="'. $url .'" alt="." />';
		}
		$video_allowed = array('mp4');
		if (in_array($ext, $video_allowed)) {
		    return '<br /><video style=" width: 100%; " id="v1" loop="" controls=""><source src="'. $url .'" type="video/mp4"></video>';
		}
	}
	return $url;
}

/**
* FUNCTION FOR GENERATE PDF
**/
function pdf($filename = "", $footertext = "", $downloadORwrite = '')
{
	ini_set("pcre.backtrack_limit", "1000000");
    global $html;
    if ($html=="") {
        echo "Please provide HTML to PDF function";
        exit;
    }

    //echo $html; exit;
    if (phpversion()>= '7') {
        require_once($_SESSION['library_path'].'pdf/mpdf/7/vendor/autoload.php');
        $mpdfConfig = array(
                'mode' => 'utf-8',
                'format' => 'A4',    // format - A4, for example, default ''
                'default_font_size' => 0,     // font size - default 0
                'default_font' => '',    // default font family
                'margin_left' => 10,        // 15 margin_left
                'margin_right' => 10,       // 15 margin right
                'margin_top' => 15,     // 16 margin top
                'margin_bottom' => 8,       // margin bottom
                'margin_header' => 0,     // 9 margin header
                'margin_footer' => 10,     // 9 margin footer
                'orientation' => 'P'    // L - landscape, P - portrait
            );
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
    } else {
        require_once($_SESSION['library_path'].'pdf/mpdf/610/mpdf.php');
        $mpdf=new mPDF();
        //$mode='', $format='', $font_size='', $font='', $margin_left=3, $margin_right=3, $margin_top=3, $margin_bottom=3, $margin_header='', $margin_footer=6, $orientation=''
    }

    $mpdf->setAutoTopMargin = 'stretch';
    $mpdf->setAutoBottomMargin = 'stretch';

    if ($footertext != "") {
        $mpdf->SetFooter($footertext);
    }
	$doc = new DOMDocument();
	@$doc->loadHTML($html);
    $mpdf->WriteHTML($doc->saveHTML());
	
    if ($filename!="") {
        if ($downloadORwrite != 1) {
            $mpdf->Output($filename, "D");
        } else {
            $mpdf->Output($filename, "F");
        }
    } else {
        $mpdf->Output();
    }
    ob_end_flush();
}
/**
* FUNCTION FOR DATE FORMAT
**/
function dateformat($date, $type = 1)
{
    if ($date == "0000-00-00" || $date == "") {
        $date = "";
    } else {
        if ($type == 1) {
            $date   =   date("n/j/Y", strtotime($date));
            $date   =   str_replace("/", "-", $date);
        } else {
            $date   =   str_replace("-", "/", $date);
            $date   =   date("n/j/Y", strtotime($date));
        }
    }

    return $date;
}
/**
* FUNCTION FOR EMAILS LOG
**/
function emaillog($discovery_id, $loggedin_id, $email_subject, $send_from, $to_values = array(), $email_salutation, $email_body, $bcc_values = array(), $cc_values = array(), $sender_type, $receiver_type, $sending_script)
{
    global $AdminDAO;
    $send_to    =   implode(',', $to_values);
    $email_bcc =    implode(',', $bcc_values);
    $email_cc   =   implode(',', $cc_values);

    $fields     =   array('discovery_id','loggedin_id','email_subject','send_from','send_to','email_salutation','email_body','email_bcc','email_cc','sender_type','receiver_type','sending_script');
    $values     =   array($discovery_id,$loggedin_id,$email_subject,$send_from,$send_to,$email_salutation,$email_body,$email_bcc,$email_cc,$sender_type,$receiver_type,$sending_script );
    $AdminDAO->insertrow("email_log", $fields, $values);
}
/**
* FUNCTION FOR ADDRESS MAKE
**/
function makeaddress($pkaddressbookid, $issplit = 0)
{
    global $AdminDAO;
    $results            =   $AdminDAO->getrows("system_addressbook,system_state", "*", "pkaddressbookid = :id AND fkstateid = pkstateid", array(":id"=>$pkaddressbookid));
    $data               =   $results[0];
    if ($issplit == 1) {
        $addbreak   =   "<br>";
    } else {
        $addbreak   =   ", ";
    }
    $address            =   $data['address'].", ".$data['street'].$addbreak.$data['cityname'].", ".$data['statecode']." ".$data['zip'];
    return $address;
}
/**
* FUNCTION FOR GENERATE FINAL RESPONSE
**/
function finalResponseGenerate($objection, $answer)
{
    $transitiontext     =   "However, in the spirit of cooperation and without waiving any objection, respondent responds: ";

    if ($objection == "" && $answer == "") {
        $finalResponse = "";
    } else {
        if ($answer == "") {
            $finalResponse = $objection;
        } elseif ($objection == "") {
            $finalResponse = $answer;
        } else {
            if (!in_array(substr($objection, -1), array('!','.','?'))) {
                $objection  =   $objection.".";
            }
            $finalResponse = $objection." ".$transitiontext." ".$answer;
        }
        if (substr($finalResponse, -1) != ".") {
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
    $results            =   $AdminDAO->getrows("instructions", "*", "pkinstructionid = :id", array(":id"=>$id));
    $data               =   $results[0];
    $placement          =   $data['placement'];
    $title              =   $data['title'];
    if ($placement == "") {
        $placement = "top";
    }
    //$placement	=	$placement."-show";
    if ($title == "") {
        $title = "No title found.";
    }
    echo '<a href="#"><i style="font-size:16px" data-placement="'.$placement.'" data-toggle="tooltip" title="'.$title.'" class="fa fa-info-circle tooltipshow" aria-hidden="true"></i></a>';
}
/**
* FUNCTION FOR GETTING ANSWER OF DEPENDENT PARENT QUESTION
**/
function getAnswerOfDependentParentQuestion($discovery_id, $questoin_id, $response_id)
{
    global $AdminDAO;
    $mainQuestions  =   $AdminDAO->getrows(
        'discovery_questions dq,questions q,response_questions rq',
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
    if (!empty($mainQuestions)) {
        $data               =   $mainQuestions[0];
        $answer                 =   $data['answer'];
        if ($answer == "") {
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
function findWorkingDay($duedate, $extensiondays, $holidaysArray, $no_of_court_days = 0)
{
    global $dateformate;
    if (in_array($duedate, $holidaysArray) || isWeekend($duedate)) {
        //echo 1;
        $duedate    =   date('Y-m-d', strtotime($duedate. ' + 1 days'));
        findWorkingDay($duedate, $extensiondays, $holidaysArray, $no_of_court_days);
    } else {
        $no_of_court_days++;
        if ($extensiondays == 2 && $no_of_court_days < 2) {   //echo 2;
            $duedate    =   date('Y-m-d', strtotime($duedate. ' + 1 days'));
            findWorkingDay($duedate, $extensiondays, $holidaysArray, $no_of_court_days);
        } else {
            //echo 3;
            $duedate    =   date($dateformate, strtotime($duedate));
            $duedate    =   str_replace("/", "-", $duedate);
            echo  $duedate;
            //exit;
        }
    }
}


function numberTowords($num)
{

    $ones = array(
    0 =>"ZERO",
    1 => "ONE",
    2 => "TWO",
    3 => "THREE",
    4 => "FOUR",
    5 => "FIVE",
    6 => "SIX",
    7 => "SEVEN",
    8 => "EIGHT",
    9 => "NINE",
    10 => "TEN",
    11 => "ELEVEN",
    12 => "TWELVE",
    13 => "THIRTEEN",
    14 => "FOURTEEN",
    15 => "FIFTEEN",
    16 => "SIXTEEN",
    17 => "SEVENTEEN",
    18 => "EIGHTEEN",
    19 => "NINETEEN",
    "014" => "FOURTEEN"
    );
    $tens = array(
    0 => "ZERO",
    1 => "TEN",
    2 => "TWENTY",
    3 => "THIRTY",
    4 => "FORTY",
    5 => "FIFTY",
    6 => "SIXTY",
    7 => "SEVENTY",
    8 => "EIGHTY",
    9 => "NINETY"
    );
    $hundreds = array(
    "HUNDRED",
    "THOUSAND",
    "MILLION",
    "BILLION",
    "TRILLION",
    "QUARDRILLION"
    ); /*limit t quadrillion */
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr, 1);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {
        while (substr($i, 0, 1)=="0") {
            $i=substr($i, 1, 5);
        }
        if ($i < 20) {
            /* echo "getting:".$i; */
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            if (substr($i, 0, 1)!="0") {
                $rettxt .= $tens[substr($i, 0, 1)];
            }
            if (substr($i, 1, 1)!="0") {
                $rettxt .= " ".$ones[substr($i, 1, 1)];
            }
        } else {
            if (substr($i, 0, 1)!="0") {
                $rettxt .= $ones[substr($i, 0, 1)]." ".$hundreds[0];
            }
            if (substr($i, 1, 1)!="0") {
                $rettxt .= " ".$tens[substr($i, 1, 1)];
            }
            if (substr($i, 2, 1)!="0") {
                $rettxt .= " ".$ones[substr($i, 2, 1)];
            }
        }
        if ($key > 0) {
            $rettxt .= " ".$hundreds[$key]." ";
        }
    }
    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            $rettxt .= $ones[$decnum];
        } elseif ($decnum < 100) {
            $rettxt .= $tens[substr($decnum, 0, 1)];
            $rettxt .= " ".$ones[substr($decnum, 1, 1)];
        }
    }
    return $rettxt;
}
