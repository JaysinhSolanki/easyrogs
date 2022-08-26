<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

$respond		  = $_POST['respond'];
$discovery_id	= $_POST['id'];
$response_id	= $_POST['response_id'];
$loggedin_email = $_SESSION['loggedin_email'];

// TODO: when this script is refactored we can probably produce a more generic solution than using a switch
// handle payables with backward comp...

$id       = $_POST['id']; //!! TODO why both $id and $discovery_id ??
$itemType = @$_POST['item_type'];

$payableType = $itemType ?? ($respond ? Payable::ITEM_TYPE_RESPONSE : Payable::ITEM_TYPE_DISCOVERY);
switch($payableType) {
  case Payable::ITEM_TYPE_DISCOVERY:
    $payable       = $discoveriesModel;
    $payableItemId = $discovery_id;
  break;

  case Payable::ITEM_TYPE_RESPONSE:
    $payable       = $responsesModel;
    $payableItemId = $response_id;
  break;

  case Payable::ITEM_TYPE_MEET_CONFER:
    $discovery_id = null;

    $payable       = $meetConferModel;
    $payableItemId = $id;

    $mc = $meetConferModel->find($id);
    $response  = $responsesModel->find($mc['response_id']);
    $discovery = $discoveriesModel->find($response['fkdiscoveryid']);
      //!! TODO so $id and $response['fkdiscoveryid'] should match, right?
    $case_id   = $discovery['case_id'];
  break;
}

if( $discovery_id ) {
  $discovery_data = $discoveriesModel->findDetails($discovery_id);
    //!! TODO $discovery_data & $discovery, something here should be consolidated!
  Side::legacyTranslateCaseData($discovery_data['case_id'], $discovery_data);
	$uid							= $discovery_data['uid'];
	$case_uid					= $discovery_data['case_uid'];
	$discovery_type		= $discovery_data['type'];
	$case_id			    = $discovery_data['case_id'];
	$case_title			  = $discovery_data['case_title'];
	$case_number		  = $discovery_data['case_number'];
	$county_name		  = $discovery_data['county_name'];
	$is_send			    = $discovery_data['is_send'];
	$set_number			  = $discovery_data['set_number'];
	$form_name			  = $discovery_data['form_name'];
	$form_id 			    = $discovery_data['form_id'];
	$propounding		  = $discovery_data['propounding'];
	$responding			  = $discovery_data['responding'];
	$discovery_id		  = $discovery_data['discovery_id'];
	$attr_id			    = $discovery_data['attr_id'];
}

//Responding Party
$respondingdetails	= $AdminDAO->getrows("clients","*",
                            "id = :id",
                            array(":id"=>$responding));
$responding_name		= $respondingdetails[0]['client_name'];
$responding_email		= $respondingdetails[0]['client_email'];
$responding_type		= $respondingdetails[0]['client_type'];
$responding_role		= $respondingdetails[0]['client_role'];

//Sender Details
$attr_id = $_SESSION['addressbookid'];
$senderDetails	= $AdminDAO->getrows("system_addressbook","*",
                        "pkaddressbookid = :id",
                        array(":id"=>$attr_id));

$senderDetail	 = $senderDetails[0];
$senderEmail	 = $senderDetail['email'];
$senderPhone	 = $senderDetail['phone'];
$senderName		 = $senderDetail['firstname']." ".$senderDetail['lastname'];
$system_address  = $AdminDAO->getrows("system_addressbook,system_state", "*",
                      "pkaddressbookid = :id AND fkstateid = pkstateid",
                      array(":id"=>$attr_id));
$result_address = $system_address[0];

$senderAddress = makeaddress($attr_id);
$getstate		   = $result_address['statename'];

$currentSide     = $sidesModel->getByUserAndCase($currentUser->id, $case_id);
$serviceList     = $sidesModel->getServiceList( $currentSide );
$primaryAttorney = $sidesModel->getPrimaryAttorney($currentSide['id']);

$discovery_name = $respond
                    ? $responsesModel->getTitle( $response_id, $discovery_data )
                    : $discoveriesModel->getTitle( $discovery_data );

?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr"><!-- POSpopup -->
<head>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<style>
 .tabela
	{
		width:100% !important;
    border: 1px solid #A2A9B1;
   border-collapse: collapse;
   line-height:25px;
	} 
   .tabela tbody tr th{
   background: #999;
   color: white;
   font-weight: bold;
   font-size: 13pt;
   text-align:center !important
   }

td, th {
    padding: 5px;
}
   .tabela tbody tr td, .tabela tbody tr th {
   border: 1px solid #A2A9B1;
   border-collapse: collapse;
    line-height:25px;
   } 


   
</style>
</head>
<div class="row">
	<div class="col-md-12" id="poshtml">
    	<table class="tabela1" style="border:none !important">
          <tbody>
            <tr>
              <td align="center" style="padding-bottom:4px;">
              <h3 style="text-decoration:underline; font-size:15px; display:block;">PROOF OF SERVICE</h3>
              </td>
            </tr>
            <tr>
                <td align="center">
                     <h4 >
                    <?= $case_title ?><br/>
                    <?= "Case no. $case_number" ?>
                    </h4>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <h4>
                         <?= strtoupper ("STATE OF CALIFORNIA, COUNTY OF ".$county_name) ?>
                    </h4>
                </td>
            </tr>
            <tr>
                <td align="justify">
					<div id="pos_18info" style="width:100%;display:flex;flex-direction:row;align-items:stretch;">
						<span id="_1" style="flex-shrink:0;align-self:center;">I am at least 18 years old and not a party to this action. My business address is </span>
						<input style="margin:5px;width:inherit;" type="text" name="pos_address" id="pos_address" placeholder="Enter your address" value="<?= $result_address['address'] .", ". $result_address['street'] .", ".  $result_address['cityname'] .", ". $result_address['statecode'] ." ". $result_address['zip']; ?>" size="180"/><span style="align-self: center;margin-left: -4px;">.</span>
					</div>
					<p id="_2">My electronic service address is <?= $senderEmail ?>.</p>
					<br/>
					On <?= date('F j, Y') ?>, I electronically served <?= $discovery_name ?> upon the following:
                </td>
            </tr>
          </tbody>
		</table>
        <br />
        <table class="tabela" style="border: 1px solid #A2A9B1;">
          <tbody>
          	<tr>
                <th  style="border: 1px solid #A2A9B1;" align="center">Person Served</th>
                <th  style="border: 1px solid #A2A9B1;" align="center">Party Served</th>
                <th  style="border: 1px solid #A2A9B1;" align="center">Email</th>
            </tr>
			<?php foreach($serviceList as $user): ?>
				<?php if ($user['clients']): ?>
          <tr>
							<td  style="border: 1px solid #A2A9B1;" align="left"><?= $user['attorney_name'] ?></td>
              <td  style="border: 1px solid #A2A9B1;" align="left">
					<?php foreach($user['clients'] as $client): ?>
							<?= $client['client_name'] ?>
              <?php echo "</br>" ?>
					<?php endforeach; ?>
          </td>
          <td  style="border: 1px solid #A2A9B1;" align="left"><?= $user['attorney_email'] ?></td>
						</tr>
				<?php endif; ?>
			<?php endforeach; ?>
          </tbody>
		</table>

        <table class="tabela1" style="border:none !important;">
          <tbody>
            <tr>
                <td align="justify">
					<br />
					I declare under penalty of perjury under the laws of the State of California that the above is true and correct. Executed on <?= date('F j, Y') ?> at <span id="citystate"><input type="text" name="pos_city" id="pos_city" placeholder="Enter your city..." value="<?= $senderDetail['cityname'] ?>" required pattern="[A-Za-z]+" />, <input type="text" name="pos_state" id="pos_state" value="<?= $getstate ?>" required pattern="[A-Za-z]+" /></span>. <span style='display:none' id='signtime'></span>
					<br />
					<br />
                </td>
            </tr>
          </tbody>
		</table>
        <table style="border:none !important" width="100%">
          <tbody>
            <tr>
                <td align="left"><?= date('F j, Y') ?></td>
                <td align="right" style="line-height:20px; padding-top:42px">By: <?= $senderName ?><br /> Signed electronically,<br />
                <img src="<?= ASSETS_URL ?>images/court.png" style="width: 18px;padding-right: 3px;">Cal. Rules of Court, rule 2.257</td>
            </tr>
          </tbody>
		</table>
        <br />
        <br />
    </div>
</div>

<form name="formPOS" id="formPOS">
	<input type="hidden" name="discovery_id" value="<?= $discovery_id ?>" />
	<input type="hidden" name="discovery_type" value="<?= $discovery_type ?>" />
	<input type="hidden" name="response_id" value="<?= $response_id ?>" />
	<input type="hidden" name="pos_text" id="pos_text" value="" />
	<input type="hidden" name="posaddress" id="posaddress" value="" />
	<input type="hidden" name="posstate" id="posstate" value="" />
	<input type="hidden" name="poscity" id="poscity" value="" />

	<input type="hidden" name="respond" value="<?= $respond ?>" />

  <div class="row">
		<div class="col-md-12" style="text-align:right">
      <i id="POS_msgdiv" class="POS_msgdiv" style="color:red"></i>
      <i><?= $primaryAttorney['credits'] ?: 'No' ?> credits left.</i>
			<button type="button" class="btn btn-purple" id="pos-pay-and-serve-btn" onclick="payAndServe()">
        <i class="fa fa-share"></i> Pay & Serve
      </button>
			<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
		</div>
	</div>
</form>

<div id="payment-modal" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" style="font-size: 22px;">Please select a payment method</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<div id="payment-methods">
          <div id="stored-payment-methods"></div>
					<div class="payment-method">
						<div class="form-check" style="cursor: pointer">
              <input type="radio" name="payment_method_id" id="payment_method_id_none" value="" class="form-check-input" checked />
							<label class="form-check-label" for="payment_method_id_none" style="cursor: pointer">Add a new Payment Method</label>
						</div>
					</div>
				</div>
				<form id="payment-form" style="display: none;">
          <br/>
					<div id="card-element">
						<!-- Elements will create input elements here -->
					</div>

					<!-- We'll put the error messages in this element -->
					<div id="card-errors" role="alert"></div>
          <br/>
          <div class="form-check" style="cursor: pointer">
            <input type="checkbox" name="save_to_profile" id="save_to_profile" value="" class="form-check-input" checked />
            <label class="form-check-label" for="save_to_profile" style="cursor: pointer">Save for future payments.</label>
          </div>
				</form>
        <div class="form-check" style="cursor: pointer" id="save-to-side-input">
          <input type="checkbox" name="save_to_side" id="save_to_side" value="" class="form-check-input"/>
          <label class="form-check-label" for="save_to_side" style="cursor: pointer">Allow case team members to use this card.</label>
        </div>
      </div>
      <div class="modal-footer">
        <button id="submit-payment" class="btn btn-success">Pay $<?= round(SERVE_DISCOVERY_COST / 100, 2) ?></button>
        <a href="javascript:;" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</a>
      </div>
    </div>
  </div>
</div>

<script>
  caseId   = <?= $case_id ?>;
  itemId   = <?= $payableItemId ?>;
  itemType = '<?= $payableType ?>';

  signupCredits = <?= SIGNUP_CREDITS ?>;
  userCredits   = <?= $primaryAttorney['credits'] ?: 0 ?>;
  serviceNumber = signupCredits - userCredits + 1;

  pos_state   = $("#pos_state").val();
  pos_city    = $("#pos_city").val();
  pos_address = $("#pos_address").val();

  try {
    stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY ?>');
  } catch( e ) {
    console.error( e );
  }
</script>

<script src="<?= ROOTURL ?>system/assets/payments.js"></script>
<?php if( @$_ENV['PAY_DEMO'] ) { ?>
<script src="<?= ROOTURL ?>system/assets/paymentsDemo.js"></script>
<?php } ?>
<script>
  function payAndServe(callback = servePOS) {
    if ( validate() ) {
      <?php if ($payable->isPaid($payableItemId) || User::hasCredits($primaryAttorney)): ?>
        callback();
      <?php else: ?>
        new ERPayment(itemId, itemType, caseId, callback);
      <?php endif; ?>
    }
  }

  function validate() {
    pos_state   = $("#pos_state").val();
    pos_city    = $("#pos_city").val();
    pos_address = $("#pos_address").val();

    var error = 0;
    var msg = "";
    if( !pos_address.trim() ) {
        error   = 1;
        msg     = "Please enter address.";
    }

    if( !pos_city.trim() ) {
        error   = 1;
        msg     = "Please enter city.";
    }
    if( !pos_state.trim() ) {
        error   = 1;
        msg     = "Please enter state.";
    }
    if( error == 1 ) {
        $(".POS_msgdiv").html(msg);
    }
    return error == 0;
  }

  function creditsText() {
    if (userCredits <= 0) { return ''; }
    if (signupCredits == serviceNumber) { return 'This is your final complimentary service'; }
    return 'This is your ' + stringifyNumber(serviceNumber) + ' complimentary service';
  }

  function servePOS() {
    $.LoadingOverlay("show");

    // we must do this (setAttribute[value]), to properly get their html
    $("#pos_address").attr('value', pos_address);
    $("#pos_state").attr('value', pos_state);
    $("#pos_city").attr('value', pos_city);

    var poshtml = $("#poshtml").clone(),
        _text = $("#pos_18info > #_1").text() +
                $("#pos_18info > input").val() + '. ' +
                $("#pos_18info + #_2").text();

    poshtml.find('#pos_18info').replaceWith( '<p id="pos_18info">' + _text + '</p>' );
    poshtml.find('#pos_18info + #_2').replaceWith( '' );
    poshtml.find("#citystate").replaceWith( pos_city + ", " + pos_state );

    $("#pos_text").val(poshtml.html());
    $("#posaddress").val(pos_address);
    // $("#posstreet").val(pos_street);
    // $("#posstatecode").val(pos_statecode);
    // $("#poszip").val(pos_zip);
    // $("#poscityname").val(pos_cityname);
    $("#posstate").val(pos_state);
    $("#poscity").val(pos_city);
    setTimeout( _ => {
        $.post( "propondingserveaction.php",
                $("#formPOS" )
                    .serialize() )
            .done( data => {
                $('#general_modal').modal('toggle');
                $.LoadingOverlay("hide");
                confirmAction({
                  title: 'Service Complete!',
                  text: creditsText(),
                  icon: 'success',
                  dangerMode: false,
                  buttons: null
                });
                response(data);
                (_ => {
                  const { discoveryFormNames, discoveryForm, } = globalThis;
                  trackEvent('serve', { event_category: 'discovery', event_label: discoveryForm && discoveryFormNames[discoveryForm-1], } );
                })();
            });
    }, 2000 );
  }
</script>
