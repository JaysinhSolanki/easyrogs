<?php
  require_once __DIR__ . '/../bootstrap.php';

require_once("adminsecurity.php");

$responding	=	$_POST['responding'];
$case_id	=	$_POST['case_id'];
//Sender Details
$senderDetails			=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid = :pkaddressbookid", array(":pkaddressbookid"=>$_SESSION['addressbookid']));
$senderDetail			=	$senderDetails[0];
$senderEmail			=	$senderDetail['email'];
$senderPhone			=	$senderDetail['phone'];
$senderName				=	$senderDetail['firstname']." ".$senderDetail['lastname'];	

$fkstateid				=	$senderDetail['fkstateid'];
$getState				=	$AdminDAO->getrows("system_state","*","pkstateid = :id",array(":id"=>$fkstateid));
$atorny_state			=	$getState[0]['statename'];
$atorny_state_short		=	$getState[0]['statecode'];

$senderAddress			=	makeaddress($_SESSION['addressbookid'], 1);//$senderDetail['address']."<br>Street#".$senderDetail['street'].", ".$senderDetail['cityname'].", ".$atorny_state_short.", ".$senderDetail['zip'];


//Responding Details
$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
$responding_name		=	$respondingdetails[0]['client_name'];
$responding_email		=	$respondingdetails[0]['client_email'];
$responding_type		=	$respondingdetails[0]['client_type'];
$responding_role		=	$respondingdetails[0]['client_role'];

//Email Salutation 
$emaildata				=	$AdminDAO->getrows("email_log","email_salutation","sender_type = 1 AND receiver_type = 2 ORDER BY id DESC LIMIT 1",array());
$email_solicitation		=	$emaildata[0]['email_salutation'];

if($email_solicitation == "")
{
	$email_solicitation = "{$responding_name} ,";
}
//Case Details
$casedetails		=	$AdminDAO->getrows("cases","*","id = :id",array(":id"=>$case_id));

Side::legacyTranslateCaseData($case_id, $casedetails);

$case_title			=	$casedetails[0]['case_title'];
	
$emailURL				=	"~LINK_HERE~";
ob_start();
?>
<h4>Please click on the following link to respond to discovery in your case: <a href='<?php echo $emailURL; ?>'><?php echo $emailURL; ?></a>.</h4> 
<p>Feel free to email me at <a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a><?php if($senderPhone != ""){ echo " or call ".$senderPhone;  }?> if you have any questions.</p>
<p>
<b>___________________</b><br /> 
<?php echo $senderName; ?><br /> 
<?php echo $senderAddress; ?><br />
<a href="mailto:<?php echo $senderEmail;?>"><?php echo $senderEmail;?></a><br />
<?php echo $senderPhone; ?><br />
<br />
<br />
<?php echo "&copy; ".date('Y')." EasyRogs.com"; ?>
</p>
<?php
$html = ob_get_contents(); 
ob_clean();
?>

<script>
$(document).ready(function()
{
	 CKEDITOR.replace( 'email_body_popup' );
});
</script>
<form name="formduedatecalculation" id="formduedatecalculation">
<div class="form-group">
    <label for="email_solicitation" class="col-form-label">Email Salutation:</label>
    <input type="text" name="email_solicitation_popup" id="email_solicitation_popup" placeholder="Add Salutation"  class="form-control m-b" value="<?php echo $email_solicitation; ?>">
</div>
<div class="form-group">
    <label for="email_body" class="col-form-label">Email Body:</label>
    <textarea  rows="10" name="email_body_popup" id="email_body_popup" placeholder="Add email body"  class="form-control m-b"><?php echo ($html); ?></textarea>
</div>
<div class="form-group">
    <button type="button" class="btn btn-danger" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i> Cancel</button>
    <button type="button" class="btn btn-primary" onclick="buttonsaveandsend()"><i class="fa fa-share"></i> Send</button>
</div>
</form>
<script>
// A $( document ).ready() block.
$( document ).ready(function() 
{
   $("#client_modal_title").html("Here's the email notification <?php echo $responding_name; ?> will receive. Add any last-minute instructions and click Send.");
});
</script>