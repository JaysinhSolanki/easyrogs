<?php
@session_start();
require_once( "adminsecurity.php" );
include_once( $_SESSION['library_path'] . "helper.php" );
$form_id          = $_POST['form_id'];
$case_id          = $_POST['case_id'];
$set_number       = $_POST['set_number'];
$question_numbers = $_POST['question_numbers'];
$new_questions    = $_POST['new_questions'];

$user_group   = $_SESSION['groupid'];
$propounding  = $_POST['propounding'];
$responding   = $_POST['responding'];
$discovery_id = $_POST['id'];

$respondingdetails = $AdminDAO->getrows( "clients", "*", "id = :id", [":id" => $responding] );
$responding_name   = $respondingdetails[0]['client_name'];
$responding_email  = $respondingdetails[0]['client_email'];
$responding_type   = $respondingdetails[0]['client_type'];
$responding_role   = $respondingdetails[0]['client_role'];

$propoundingdetails = $AdminDAO->getrows( "clients", "*", "id = :id", [":id" => $propounding] );
$propounding_name   = $propoundingdetails[0]['client_name'];
$propounding_email  = $propoundingdetails[0]['client_email'];
$propounding_type   = $propoundingdetails[0]['client_type'];
$propounding_role   = $propoundingdetails[0]['client_role'];

//$alreadysentdiscoveries		= $AdminDAO->getrows("discoveries","*","case_id = :case_id AND form_id = :form_id AND propounding = :propounding AND responding = :responding",array(":case_id"=>$case_id,":form_id"=>$form_id,":propounding"=>$propounding,":responding"=>$responding));
//$totalalreadysentdiscoveries	= sizeof($alreadysentdiscoveries);
//if Set 1 it's  0 otherwise it's the starting question number minus 1
if( $set_number == 1 ) {
    $pre_totalsentquestions = 0;
} else {
    $pre_totalsentquestions = $question_numbers[0] - 1;
}

//Count total number of questions sent this time
$totalquestions = 0;
foreach( $question_titles as $qdata ) {
    if( $qdata ) {
        $totalquestions = $totalquestions + 1;
    }
}
if( $form_id == Discovery::FORM_CA_SROGS ) {
    $decelarationtext = "<p>I, {$_SESSION['name']}, declare:</p>
		<ol>
			<li>I am the attorney of record for the {$propounding_role} in this matter.</li> 
			<li>I am propounding to {$responding_role} the foregoing set of Interrogatories.</li>
			<li>This set of Interrogatories will cause the total number of specially prepared Interrogatories propounded to the party to whom they are directed to exceed the number of Specially Prepared Interrogatories permitted by Code of Civil Procedure section 2030.030.</li>
			<li>I have previously propounded a total of {$pre_totalsentquestions} Interrogatories to this party.</li>
			<li>This set of Interrogatories contains a total of {$totalquestions} Interrogatories.</li>
			<li>I am familiar with the issues and the previous discovery conducted by all of the parties in this action.</li>
			<li>I have personally examined each of the questions in this set of Interrogatories.</li>
			<li>This number of questions is warranted under Code of Civil Procedure section 2030.040(a) because of the complexity and quantity of the existing and potential issues in this particular case.</li>
			<li>None of the questions in this set of Interrogatories is being propounded for any improper purpose, such as to harass the party, or the attorney for the party, to whom it is directed, or to cause unnecessary delay or needless increase in the cost of litigation.</li>
		</ol>";
} 
elseif( $form_id == Discovery::FORM_CA_RFAS ) {
    $decelarationtext = "<p>I, {$_SESSION['name']}, declare:</p>
		<ol>
			<li>I am the attorney of record for the {$propounding_role} in this matter.</li>
			<li>I am propounding to {$responding_role} the foregoing set of Requests for Admission.</li>
			<li>This set of Requests for Admission will cause the total number of Requests for Admission propounded to the party to whom they are directed to exceed the number of Requests permitted by Code of Civil Procedure section 2033.030.</li>
			<li>I have previously propounded a total of {$pre_totalsentquestions} Requests for Admission to this party.</li>
			<li>This set of Requests for Admission contains a total of {$totalquestions} Requests for Admission.</li>
			<li>I am familiar with the issues and the previous discovery conducted by all of the parties in this action.</li>
			<li>I have personally examined each of the questions in this set of Requests for Admission.</li>
			<li>This number of requests is warranted under Code of Civil Procedure section 2033.040(a) because of the complexity and quantity of the existing and potential issues in this case.</li>
			<li>None of the requests in this set of Requests for Admission is being propounded for any improper purpose, such as to harass the party, or the attorney for the party, to whom it is directed, or to cause unnecessary delay or needless increase in the cost of litigation.</li>
		</ol>";
}
/*elseif( $form_id == Discovery::FORM_CA_RPDS ) {
	$decelarationtext = "<p>I, {$_SESSION['name']}, declare:</p>
		<ol>
			<li>I am the attorney of record for the {$propounding_role} in this matter.</li>
			<li>I am propounding to {$responding_role} the foregoing set of Requests for Production of Documents.</li>
			<li>This set of Requests for Production of Documents will cause the total number of Requests for Production of Documents propounded to the party to whom they are directed to exceed the number of Requests permitted by Code of Civil Procedure section 2033.030.</li>
			<li>I have previously propounded a total of {$pre_totalsentquestions} Requests for Production of Documents to this party.</li>
			<li>This set of Requests for Production of Documents contains a total of {$totalquestions} Requests.</li>
			<li>I am familiar with the issues and the previous discovery conducted by all of the parties in this action.</li>
			<li>I have personally examined each of the questions in this set of Requests for Production of Documents.</li>
			<li>This number of requests is warranted under Code of Civil Procedure section 2033.040(a) because of the complexity and quantity of the existing and potential issues in this case.</li>
			<li>None of the requests in this set of Requests for Production of Documents is being propounded for any improper purpose, such as to harass the party, or the attorney for the party, to whom it is directed, or to cause unnecessary delay or needless increase in the cost of litigation.</li>
		</ol>";
}*/
if( $user_group != 3 ) {
?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger" role="alert">
				Sorry, an attorney must sign a Declaration for Additional Discovery. <br />
				<i class="fa fa-university" aria-hidden="true"></i> Code Civ.Proc., 2033.030 <?= instruction( 17 ) ?>, 2033.050 <?= instruction( 18 ) ?>.
			</div>
		</div>
		<div class="col-md-12" style="text-align:right">
			<a type="button" class="btn btn-warning tooltipshow" onclick="serveFunctionMain(0);" data-placement="top"
					data-toggle="tooltip" data-original-title="Serve without signing DAD.">
				<i class="fa fa-fast-forward" aria-hidden="true"></i> Skip
			</a>
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
        </div>
    </div>

<?php
} 
else {
?>

	<form name="formdeclaration" id="formdeclaration">
		<input type="hidden" name="discovery_id" value="<?= $discovery_id ?>" />
		<div class="form-group">
			<label for="recipient-name" class="col-form-label"></label>
			<textarea class="form-control" rows="60" name="declaration_text" id="declaration_text"><?=
				$decelarationtext;
            ?></textarea>
            <br />
            <p>	I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct.<br>
				Executed on <?= date( 'F j, Y' ) ?> at 
				<span id="citystate">
					<input type="text" name="dec_city" id="dec_city" placeholder="Enter your city..." />, 
					<input type="text" name="dec_state" id="dec_state" value="California" />
				</span>.</br>
			</p>
		</div>

		<div class="row">
			<div class="col-md-6" style="text-align:left">
				<img src="<?= ASSETS_URL ?>images/court.png" style="width: 18px;padding-right: 3px;">

<?php // added by JS 3/3/20
				if( $form_id == Discovery::FORM_CA_SROGS ) {
					echo "Code Civ.Proc., &sect; 2030.210 ";
					echo instruction( 11 );
				} elseif( $form_id == Discovery::FORM_CA_RFAS ) {
					echo "Code Civ.Proc., &sect;&sect; 2033.040 ";
					echo instruction( 9 );
					echo ", 2033.050 ";
					echo instruction( 10 );
				}
?>
				</p></div>
			<div class="col-md-6" style="text-align:right">
				By: <?= $_SESSION['name'] ?>
				<br />
				Signed electronically,<br><img src="<?= ASSETS_URL ?>images/court.png" style="width: 18px;padding-right: 3px;">Cal.
				Rules of Court, rule 2.257
			</div>
		</div>
		<br />
		<br />
		<div class="row">
			<div class="col-md-8" style="text-align:right">
				<i id="DEC_msgdiv" style="color:red"></i>
			</div>
			<div class="col-md-4" style="text-align:right">
				<button type="button" class="btn btn-primary" onclick="signdeclaration('<?= $_REQUEST['pos_or_save'] ?>')">
					<i class="fa fa-pencil "></i> Sign
                </button>
                <a type="button" class="btn btn-warning tooltipshow" onclick="serveFunctionMain(0);" 
						data-placement="top" data-toggle="tooltip" data-original-title="Serve without signing DAD.">
					<i class="fa fa-fast-forward" aria-hidden="true"></i> Skip 
				</a>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
					<i class="fa fa-close"></i> Cancel
				</button>
			</div>
		</div>
	</form>
<?php
}
?>

<script>
jQuery( $ => {
	CKEDITOR.replace( "declaration_text", { height: 350 } );
} )
</script>
