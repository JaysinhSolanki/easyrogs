<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

$id         = @$_GET['id'];
$supp       = @$_GET['supp'];
$case_id    = $_GET['pid'];
$type       = $_GET['type'] ?: Discovery::TYPE_EXTERNAL;

/**
* Check that logged in user is the owner of case
**/
// $checkOwnerCase = $AdminDAO->getrows( "cases", "*",
//                                       "attorney_id = :fkaddressbookid AND id = :case_id",
//                                       array( "fkaddressbookid" => $_SESSION['addressbookid'], "case_id" => $case_id ) );

/**
* Check logged in user is case service list or not
**/
// $isServiceListMember = $AdminDAO->getrows(
//     'attorney a, client_attorney ca',
//     "*,ca.id as client_attorney_id",
//     "a.attorney_email = :attorney_email AND
//     a.id 				= ca.attorney_id AND
//     ca.case_id 			= :case_id ",
//     array( 'attorney_email'=>$_SESSION['loggedin_email'], 'case_id'=>$case_id )
// );

$forms = $AdminDAO->getrows('forms', "*");

$cases = $AdminDAO->getrows(
                            "cases c,system_addressbook a",
                            "c.*,
                            c.case_title 	as case_title,
                            c.case_number 	as case_number,
                            c.jurisdiction 	as jurisdiction,
                            c.judge_name 	as judge_name,
                            c.county_name 	as county_name,
                            c.court_address as court_address,
                            c.department 	as department,
                            a.firstname 	as atorny_fname,
                            a.lastname 		as atorny_lname,
                            a.cityname 		as atorny_city",

                            "id 			= :case_id AND
                            c.attorney_id 	= a.pkaddressbookid",

                            array( 'case_id'=>$case_id )
                        );
Side::legacyTranslateCaseData($case_id, $cases);

$case               = $cases[0];
$case_title         = $case['case_title'];
$case_number        = $case['case_number'];
$jurisdiction       = $case['jurisdiction'];
$judge_name         = $case['judge_name'];
$county_name        = $case['county_name'];
$court_address      = $case['court_address'];
$department         = $case['department'];
$set_number         = $case['set_number'];
$atorny_name        = $case['atorny_fname']." ".$case['atorny_lname'];
$atorny_city        = $currentUser->user['cityname']; //$case['atorny_city'];
$uid                = "addnew";
$conjunction_with   = 0;
$doctype            = 1;
if( $id ) {
    $discoveries            = $AdminDAO->getrows( 'discoveries', "*",
                                                  "id = :id ", array('id'=>$id) );
    $discovery              = $discoveries[0];
    $type                   = $discovery['type'];
    $conjunction_setnumber  = $discovery['conjunction_setnumber'];
    $uid                    = $discovery['uid'];
    $form_id                = $discovery['form_id'];
    $dicsovery_creator      = $discovery['attorney_id'];
    $proponding             = $discovery['proponding'];
    $proponding_attorney    = $discovery['proponding_attorney'];

    // IF CURRENT USER IS NOT THE USER WHO CREATE THIS DISCOVERY THEN IN SROGS & RFAS
    // WE ONLY SHOW THEM UPLOADED DOCUMENTS HE CANNOT DELETE THEM.
    if ($_SESSION['addressbookid'] != $dicsovery_creator) {
        $doctype = 0;
    }
}

/****************************************
	Manage Proponding  and Responding Clients
****************************************/

$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $case_id);

$ownClients   = $sidesModel->getClients($currentSide['id']);
$otherClients = $sidesModel->getOtherClients($currentSide['id'], $case_id);

// DEPRECATION ROW (sides) ---------------------------------------------------
// if(empty($checkOwnerCase) && !empty($isServiceListMember))
// {
// 	$client_attorney_id	=	array();
// 	if(!empty($isServiceListMember))
// 	{
// 		foreach($isServiceListMember as $isServiceListMemberData)
// 		{
// 			$client_attorney_id[]	=	$isServiceListMemberData['client_attorney_id'];
// 		}
// 	}

// 	$sl_attorney_id	=	implode(",",$client_attorney_id);


// 	$clientsData	=	$AdminDAO->getrows("clients c,client_attorney ca",
// 													"c.*",
// 													"c.id		=	ca.client_id AND
// 													ca.id 		IN ($sl_attorney_id)",
// 													array());

// 	//dump($clientsData);
// 	$myclients		=	array();
// 	foreach($clientsData as $cdata)
// 	{
// 		$myclientIds[]	=	$cdata['id'];
// 		$myclients[]	=	array("client_type"=>$cdata['client_type'],"client_name"=>$cdata['client_name']);
// 		$myclientType	=	$cdata['client_type'];
// 	}
// 	if(!empty($myclientIds))
// 	{
// 		$myclientsIdsList	=	implode(",",$myclientIds);
// 	}
// 	/*if($myclientType == "Others")
// 	{
// 		$otherWhere	=	" client_type IN ('Us','Pro per') AND id NOT IN ($myclientsIdsList) ";
// 	}
// 	else
// 	{
// 		$otherWhere	=	" client_type IN ('Others') AND id NOT IN ($myclientsIdsList) ";
// 	}*/
// 	$otherWhere		=	" id NOT IN ($myclientsIdsList) ";
// 	$ownclients		=	$AdminDAO->getrows("clients","*", "case_id=:case_id AND id IN ($myclientsIdsList)", array(":case_id"=>$case_id), "client_name", "ASC");

// 	$otherclients	=	$AdminDAO->getrows("clients","*", "case_id=:case_id AND $otherWhere ", array(":case_id"=>$case_id), "client_name", "ASC");


// 	if( $type == Discovery::TYPE_EXTERNAL )
// 	{
// 		$propondingClients	=	$ownclients;
// 		$respondingClients	=	$otherclients;

// 	}
// 	else if( $type == Discovery::TYPE_INTERNAL )
// 	{
// 		$propondingClients	=	$otherclients;
// 		$respondingClients	=	$ownclients;
// 	}

// }
// else
// {
// 	$ownclients		=	$AdminDAO->getrows("clients","*", "case_id=:case_id AND client_type IN ('Us','Pro per')", array(":case_id"=>$case_id), "client_name", "ASC");
// 	$otherclients	=	$AdminDAO->getrows("clients","*", "case_id=:case_id AND client_type IN ('Others')", array(":case_id"=>$case_id), "client_name", "ASC");
// ------------------------------------------------

	if( $type == Discovery::TYPE_EXTERNAL ) {
		$propondingClients	=	$ownClients;
		$respondingClients	=	$otherClients;
	}
	else  {
        assert( $type == Discovery::TYPE_INTERNAL );
		$propondingClients	=	$otherClients;
		$respondingClients	=	$ownClients;
	}

// DEPRECATION ROW (sides) ------------------------------------------------
// }
// ------------------------------------------------

/****************************************
	Load Documents Array if FORM_RPDS case
****************************************/
$_SESSION['documents']=array();
if ($id > 0 && in_array($form_id, array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS))) {
    $olddocuments   = $AdminDAO->getrows('documents', "*", "discovery_id = '$id'");
    if( sizeof($olddocuments) ) {
        foreach ($olddocuments as $data) {
            $doc_purpose    = $data['document_notes'];
            $doc_name       = $data['document_file_name'];
            $doc_path       = SYSTEMPATH."uploads/documents/".$data['document_file_name'];
            if( $doc_name ) {
                $documents[$uid][]  = array("doc_name"=>$doc_name,"doc_purpose" => $doc_purpose, "doc_path"=>$doc_path,"status"=>1);
            }
        }
        $_SESSION['documents'] = $documents;
    }
}
?>

<style>
body.modal-open {
    position: static !important;
}
.modal-header .close {
    margin-top: -45px !important;
}
.modal-title {
    font-size: 24px !important;
}
.close {
    font-size: 25px !important;
}
.modal-header {
    padding:10px !important
}
.w-900 {
    width:900px !important
}

.swal2-popup {
  font-size: 15px !important;
}

.question_titlecls {
    height:120px !important;
    font-size:13px !important;
}
.tooltip-inner {
    text-align: center;
}
.q-checkbox {
    padding: 0.5em 2em;
}
</style>
<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading text-center">
                <small>
<?php
                    if( $id ) {
                        if( @$supp == 1 ) {
                            echo "<strong>Supplemental-Amended Discovery</strong>";
                            $discovery['discovery_name']  = "SUPPLEMENTAL-AMENDED ".$discovery['discovery_name'];
                        } else {
                            echo "<strong>Edit Discovery for</strong>";
                        }
                    } else {
                        echo "<strong>Create Discovery for</strong>";
                    }
?>
                    </small>
                <h3 align="center"><strong><?= $case_title ?></strong></h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <form  name="discoveriesform" id="discoveriesform" class="form form-horizontal" method="post">
                     <input type="hidden" name="type" value="<?= $type ?>" id="type"  />
                     <input type="hidden" name="uid" value="<?= $uid ?>">
                     <input type="hidden" name="supp" value="<?= $supp ?>">

                    <div class="form-group">
                        <label class=" col-sm-2 control-label">Form<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <select  name="form_id" id="form_id"
<?php
                                if( $id ) { echo "disabled"; }
                                else {
?>
                                onchange="loadformquestions(this.value,'<?= $discovery['id']?>'),callFunction(),loaddocsFunction(this.value)"
<?php
                                }
?>
                                class="form-control m-b" >
                                <option value="">Select form</option>
<?php
                                foreach( $forms as $thisrow ) {
?>
                                    <option <?php if ($thisrow['id']==$discovery['form_id']) {
                                        echo "selected";
}?>
                                        value="<?= $thisrow['id'] ?>"><?= $thisrow['form_name'] ?></option>
<?php
                                }
?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class=" col-sm-2 control-label">Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span> <?php  echo instruction(6) ?></label>
                        <div class="col-sm-3">
                            <input type="text"  name="discovery_name" id="discovery_name" placeholder="Enter name" class="form-control m-b" value="<?php echo $discovery['discovery_name'];?>">
                        </div>
                        <label class=" col-sm-2 control-label">Set Number<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-3">
                            <select  name="set_number" id="set_number"  class="form-control m-b"  onchange="setquestionnumber()">
<?php
                                for( $d=1; $d<=100; $d++ ) {
?>
                                <option <?php if ($discovery['set_number']==$d) {
                                    echo " SELECTED ";
}?> value="<?php echo $d;?>"><?php echo $d;?></option>
<?php
                                }
?>
                            </select>
                        </div>
                    </div>
<?php
                    if (@$discovery['form_id'] == 4 || $id == '') {
?>
                    <div <?= $id ? '' : "style='display:none'" ?> id="in_conjunctionDiv">
                    <div class="row form-group">
                            <label class=" col-sm-2 control-label" style="margin-top: 20px;">In Conjunction with <span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
                            <div class="col-sm-2" style="margin-top: 25px;">
                                <input type="checkbox" onclick="inConjunctionForm()" <?= $discovery['in_conjunction'] ? "checked" : '' ?> value="1" name="in_conjunction" id="in_conjunction">
                                <label for="in_conjunction">Form Interrogatories</label>
                            </div>

                            <div  id="interogatoriesTypeDiv" <?= (!$id || !$discovery['in_conjunction']) ? "style='display:none'" : '' ?>>
                                <div class="col-md-3">
                                <label class="control-label">Type<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                <div>
                                    <select  name="interogatory_type" id="interogatory_type"  class="form-control m-b">
                                        <option value="1" <?= ($discovery['interogatory_type'] == 1) ? "selected" : '' ?>>GENERAL</option>
                                        <option value="2" <?= ($discovery['interogatory_type'] == 2) ? "selected" : '' ?>>EMPLOYMENT</option>
                                    </select>
                                </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Set No.<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                                    <div class="">
                                        <select  name="conjunction_setnumber" id="conjunction_setnumber"  class="form-control m-b" >
<?php
                                            for( $i=1; $i<=50; $i++ ) {
?>
                                            <option value="<?= $i ?>" <?= ($discovery['conjunction_setnumber'] == $i) ? "selected" : '' ?>><?= $i ?></option>
<?php
                                            }
?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>

                    </div>
<?php
                    }
?>
                    <div class="form-group">
                        <label class=" col-sm-2 control-label">Propounder<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-3">
                            <select  name="propounding" id="propounding"  class="form-control m-b" onchange="<?php if( $type == Discovery::TYPE_INTERNAL ) {
?> loadpropondingattorneys('<?= $case_id; ?>',this.value,'<?= @$discovery['proponding_attorney'] ?>'),<?php
} ?>setquestionnumber(),loadrespondings('<?= $case_id; ?>',this.value,'<?= @$discovery['responding'] ?>')">
<?php
                                foreach( $propondingClients as $thisrow ) {
?>
                                    <option <?= $thisrow['id'] == $discovery['propounding'] ? 'selected' : '' ?> value="<?= $thisrow['id'] ?>"><?= $thisrow['client_name'] ?></option>
<?php
                                }
?>
                            </select>
                        </div>
                        <label class=" col-sm-2 control-label">Respondent<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-3" id="loadrespondingsDiv1">
                            <select  name="responding" id="responding"  class="form-control m-b" onchange="setquestionnumber()">
<?php
                                foreach( $respondingClients as $thisrow ) {
?>
                                    <option <?= ($thisrow['id']==$discovery['responding']) ? 'selected' : '' ?> value="<?= $thisrow['id'] ?>"><?= $thisrow['client_name'] ?></option>
<?php
                                }
?>
                            </select>
                        </div>

                    </div>
                    <div class="form-group">
<?php
                        if( $type == Discovery::TYPE_INTERNAL ) {
?>
                            <label class=" col-sm-2 control-label">Attorney<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                            <div class="col-sm-3" id="loadpropondingattorneysDiv">
                                <select name="proponding_attorney" id="proponding_attorney"  class="form-control m-b" /><!--Proponding Attorneys Here--->
                                </select>
                            </div>
<?php
                        }
?>
                    </div>
<?php
                    if( $type == Discovery::TYPE_INTERNAL ) {
?>
                        <div class="form-group">
                            <label class=" col-sm-2 control-label">Served<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
                            <div class="col-sm-6">
                                <input type="text"  name="served" id="served" placeholder="Served Date" class="form-control m-b datepicker" value="<?php echo $discovery['served']=='0000-00-00' || $discovery['served']==''?'':dateformat($discovery['served']);?>" data-date-end-date="0d">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" id="calculateduedatepopup_btn" class="btn btn-info" onclick="calculateduedatepopup();" >
                                <i class="fa fa-calculator" aria-hidden="true"></i> Calculate Due Date <?= instruction(8) ?>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class=" col-sm-2 control-label">Due<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
                            <div class="col-sm-8">
                                <input type="text"  name="due" id="due" placeholder="Due Date" class="form-control m-b datepicker" value="<?php echo $discovery['due']=='0000-00-00' || $discovery['due']==''?'':dateformat($discovery['due']);?>">
                            </div>
                        </div>
<?php
                    }
?>
                    <div class="form-group" id="start_questionid"
<?php
                        if( in_array(@$discovery['form_id'], array(Discovery::FORM_CA_FROGS,Discovery::FORM_CA_FROGSE)) && $id ) {
                            echo "style='display:none'";
                        }
?>>
                        <label class=" col-sm-2 control-label">First Question Number<span class="redstar" style="color:#F00" title="This field is compulsory"></span> <?php  echo instruction(7) ?></label>
                        <div class="col-sm-8">
                            <input type="text" onkeypress="return isNumberKey(event)"  name="question_number_start_from" id="question_number_start_from" onblur="arrangequestionnumber()" placeholder="First Question Number"  min="1" class="form-control m-b" value="<?php echo $discovery['question_number_start_from'];?>">
                        </div>
                    </div>
                    <div id="loadinstructions" class="row"></div><!--Instructions Here--->

                    <div class="row">
                     <div id="loadformquestion"></div><!--Form Question Here--->
                    </div>
                    <input type="hidden" name="instruction_html" id="instruction_html"  />
                    <input type="hidden" name="email_body" id="email_body"  />
                    <input type="hidden" name="email_solicitation" id="email_solicitation"  />

                    <input type="hidden" name="case_id" id="case_id" value ="<?= $case_id ?>" />
                    <input type="hidden" name="id" value ="<?= $id ?>" />
<?php
                    if( $id ) {
?>
                    <input type="hidden" name="form_id" value ="<?php echo $discovery['form_id'];?>" />
<?php
                    }
?>
                    <div class="col-md-2"></div>
                    <div class="col-md-8" id="loaddocs" style="display:none">
                    <hr>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="">
                                <p>
                                    <h3>Upload your documents here</h3>
                                </p>
                            </div>
                            <div id="extraupload"></div>
                            <button type="button" class="btn btn-info" id="extrabutton">
                                <i class="icon-ok bigger-110"></i>
                                <span class="ladda-label">Upload</span><span class="ladda-spinner"></span>
                            </button>
                            <div id="uploadeddocs">

                            </div>
                        </li>
                    </ul>
                </div>
                    <div class="form-group" style="margin-top:20px">
                        <div class="col-sm-offset-2 col-sm-8">
                            <div id="loading" class="loading" style="display:none; position:absolute; color:#F00;"></div>
                            <button type="button" class="btn btn-success buttonid" data-style="zoom-in" onclick="buttonsave();">
                            <i class="icon-ok bigger-110"></i>
                            <span class="ladda-label"><i class="fa fa-save"></i> Save</span><span class="ladda-spinner"></span></button>
<?php
                            //buttonsave('discoveryaction.php','discoveriesform','wrapper','discoveries.php?pkscreenid=45&pid='.$case_id,0);
                            buttoncancel(45, 'discoveries.php?pid='.$case_id.'&iscancel=1');
                            if( $type == Discovery::TYPE_INTERNAL ) {
?>
                                <button type="button" class="btn btn-info buttonid client-btn" data-style="zoom-in" onclick="checkClientEmailFound('<?= $discovery_id ?>',2);"  title="">
                                    <i class="icon-ok bigger-110"></i>
                                        <span class="ladda-label">
                                            Client <i class="fa fa-play" aria-hidden="true" />
                                        </span>
                                        <a href="#"><i style="font-size:16px" data-placement="top" data-toggle="tooltip" title="" class="fa fa-info-circle tooltipshow client-btn" aria-hidden="true" data-original-title=""></i></a>
                                    <span class="ladda-spinner"></span>
                                </button>
<?php
                            } elseif( $type == Discovery::TYPE_EXTERNAL ) {
?>
                                <a href="javascript:;" class="btn btn-purple" onclick="serveFunction()"><i class="fa fa-share"></i> Serve </a>
                                <span id="errorMsg" style="color:red"></span>
<?php
                            }
?>
                        </div>
                    </div>
                </form>
            </div>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="client_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size:16px !important" id="client_modal_title">Send to client</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_client_modal_content">

      </div>
    </div>
  </div>
</div>

<?php
include_once(SYSTEMPATH.'application/client-email-found_modal.php');
include_once(SYSTEMPATH.'application/client_instructions_modal.php');
?>
<link href="<?= VENDOR_URL ?>uploadfile.css" rel="stylesheet">
<script src="<?= VENDOR_URL ?>jquery.uploadfile.min.js"></script>

<script>
jQuery( $ => {
    setTimeout( _ => loadToolTipForClientBtn(), 500 );
    loadpropondingattorneys('<?= $case_id ?>','<?= @$proponding ?>','<?= @$proponding_attorney ?>');

    var extraObj = $("#extraupload")
            .uploadFile({
                url:"frontdocumentuploads.php",
                fileName:"myfile",
                extraHTML: _ => {
                    var html = "<div><input type='hidden' name='rp_uid' value='<?= $uid ?>' /> <br/>";
                    html += "</div>";
                    return html;
                },
                autoSubmit:false,
                afterUploadAll: obj => {
                    $(".ajax-file-upload-container").html("");
                    loaduploadeddocs();
                }
            });
    $("#extrabutton").click( _ => {
        extraObj.startUpload();
    });

    callFunction();
    $('.datepicker').datepicker({format: 'm-d-yyyy',autoclose:true, startDate: "-5y"});
    $('#served').datepicker({format: 'm-d-yyyy',autoclose:true, startDate: "-5y"})
        .on('changeDate', ev => {
            calculateduedatepopup();
        });
    setTimeout( _ => {
        loadrespondings('<?= $case_id; ?>','<?= @$discovery['proponding']; ?>','<?= @$discovery['responding']; ?>');
    }, 2000);

<?php
    if( $id ) {
?>
        setTimeout( _ => {
	        loadinstructions('<?= $discovery['id'] ?>','<?= $discovery['form_id'] ?>')
	    }, 200);

        loadformquestions('<?= $discovery['form_id'] ?>','<?= $discovery['id'] ?>');
        loaddocsFunction('<?= $discovery['form_id'] ?>');
        loaduploadeddocs();
<?php
    }
?>
    if( !$('#question_number_start_from').val() ) {
        $('#question_number_start_from').val(1);
    }

	autogrowTextareas();

});
function loadpropondingattorneys( case_id, client_id, selected ) {
    if( !client_id ) {
        client_id = $("#propounding").val();
    }
    $("#loadpropondingattorneysDiv")
        .load("loadpropondingattorneys.php?case_id="+case_id+"&client_id="+client_id+"&selected_id="+selected);
}
function loadinstructions(id,form_id) {
    const type = <?= $type ?>;

    $.get( `discoveryloadforminstruction.php?form_id=${form_id}&id=${id}&case_id=<?= $case_id ?>&viewonly=0&type=${type}` )
        .done( resp => { 
            $("#loadinstructions").html( trim(resp) );

            const { discoveryType, discoveryFormNames, discoveryForm,
                    currentPage, } = globalThis,
                suffix = (discoveryForm ? '@' + discoveryFormNames[discoveryForm-1] : '');
            ctxUpdate({ id: `47_${type}${suffix}`, pkscreenid: '47', url: 'discoveryfront.php', } );

            if( $('textarea#instruction').length ) {
                CKEDITOR.replace( 'instruction' );
            }
        } );
}
function loadrespondings(case_id,client_id,responding_id) {
    if( !client_id ) {
        client_id = $("#propounding").val();
    }
    $("#loadrespondingsDiv")
        .load("loadrespondings.php?case_id="+case_id+"&client_id="+client_id+"&selected_id="+responding_id);
}
function callFunction() {
    form_id = $("#form_id").val();

    if( form_id == 1 || form_id == 2 || form_id == "" ) {
        $("#start_questionid").hide();
    }
    else  {
        $("#start_questionid").show();
    }
}
function isNumberKey( evt ) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if( charCode > 31 && (charCode < 48 || charCode > 57) ) {
        return false;
    }
    else {
        return true;
    }
}
function incidentmeans( id ) {
    if( id == 2 ) {
        $("#incidentDiv").show();
    }
    else {
        $("#incidentDiv").hide();
    }
}
function buttonsave() {
    for( instance in CKEDITOR.instances ) {
        CKEDITOR.instances[instance].updateElement();
    }
    //$("#instruction_html").val($("#instruction_data").html());
    var isagree = true;
    setTimeout( _ => {
        $.LoadingOverlay("hide");
        addform( 'discoveryaction.php?save=yes','discoveriesform','wrapper','discoveries.php?pkscreenid=45&pid=<?= $case_id ?>' );
    }, 200);
}
// function buttonsaveandsendpopup() {
//     $.post( "loademailcontentpopup.php",
//             $("#discoveriesform" )
//                 .serialize() )
//         .done( data => {
//             $("#load_client_modal_content").html(data);
//         } );
//     $('#client_modal').modal('toggle');
// }
function buttonsaveandsend( notesElement = '' ) {
    const notes_for_client = notesElement && $(notesElement).val().trimEnd() || "";
    for(instance in CKEDITOR.instances ) {
        CKEDITOR.instances[instance].updateElement();
    }
    var isagree = true;
    setTimeout( _ => {
        addform('discoveryaction.php?isemail=1&notes='+encodeURI(notes_for_client),'discoveriesform','wrapper','discoveries.php?pkscreenid=45&pid=<?= $case_id ?>');
    }, 200);

}
function setquestionnumber() {
    var case_id     = Number( $("#case_id").val() );
    var form_id     = Number( $("#form_id").val() );
    var propounding = Number( $("#propounding").val() );
    var set_number  = Number( $("#set_number").val() );
    var responding  = Number( $("#responding").val() );
    if( case_id  && form_id  && propounding  && responding ) {
        var data =  {
                        id          : '<?= $id ?>',
                        case_id     : case_id,
                        form_id     : form_id,
                        propounding : propounding,
                        responding  : responding,
                        set_number  : set_number
                    };
        $.post( "discoverycheckstartquestionnumber.php", data )
            .done( msg => {
                var ser_number = $("#set_number").val();
                if(msg == 1 &&  ser_number == 1) {
                    $('#question_number_start_from').val(msg);
                    $("#start_questionid").hide();
                }
                else {
                    if(form_id > 2) {
                        $("#start_questionid").show();
                    }
                    $('#question_number_start_from').val(msg);
                }
                arrangequestionnumber();
            });
    }

}
function arrangequestionnumber() {
    setTimeout( _ => {
        $( ".questionscls" ).each( function(index) {
            let question_number_start_from  = $('#question_number_start_from').val();
            if(question_number_start_from == "") {
                question_number_start_from = 1;
                }
            $(this).val( Number(index) + Number(question_number_start_from) );
        })
    }, 200);
}
function loadnewquestion() {
    $.get('discoveryaddformquestion.php')
        .done( resp => {
            $('#addnewquestion').append(resp);
            arrangequestionnumber();
        });

}
function loadformquestions( form_id, id ) {
    if( !id ) {
        $("#discovery_name").val($("#form_id option:selected").html());
    }

    $('#loadformquestion')
      .html("<div class='row'><div class='col-md-2 col-md-offset-2'><img src='../assets/images/ownageLoader/loader4.gif'></div></div><br/>");
    $('#loadinstructions')
      .html("<div class='row'><div class='col-md-2 col-md-offset-2'><img src='../assets/images/ownageLoader/loader4.gif'></div></div><br/>");
    if( form_id )     {
        if( form_id == 4 ) {
            $("#in_conjunctionDiv").show();
        }
        else {
            $("#in_conjunctionDiv").hide();
        }
        loadinstructions(id,form_id);
        $.get("discoveryloadformquestion.php?form_id="+form_id+"&id="+id)
            .done( resp =>{
                $('#loadformquestion').html(resp);
                if( form_id != 2 && form_id !=1 ) {
                    loadnewquestion();
                    setquestionnumber();
                    $("#instruction_id").show();
                    callFunction();
                }
                else  {
                    $("#instruction_id").hide();
                }
            });
    }
    else {
        $("#instruction_id").show();
    }
}
function deletenewquestion(id) {
    swal( {
            title: "Are you sure to delete?",
            text: "You will not be able to undo this action!",
            icon: 'warning',
            dangerMode: true,
            buttons: [true, "Yes, delete it!"],
            // confirmButtonColor: '#187204',
            // cancelButtonColor: '#C2391B',
        } )
    .then( result => {
        if( result ) {
            $("#"+id).remove();
            arrangequestionnumber();
        }
    });
    $( ".swal-button-container:first" ).css( "float", "right" );
}
function deletequestion( id ) {
    swal( {
        title: "Are you sure to permanently delete?",
        text: "You will not be able to undo this action!",
        icon: 'warning',
		dangerMode: true,
        buttons: [true, "Yes, delete it!"],
        // confirmButtonColor: '#187204',
        // cancelButtonColor: '#C2391B',
    } )
    .then( result => {
        if( result ) {
            $.get('discoverydeletenewquestion.php?id='+id)
                .done( resp => {
                    $("#this_" + id).remove();
                    arrangequestionnumber();
                }) ;
        }
    });
}

function checkservedate( val ) {
    if( !val ) {
        $("#calculateduedatepopup_btn").attr('disabled', 'disabled');
    }
    else {
        $("#calculateduedatepopup_btn").removeAttr("disabled");
    }
}
function calculateduedatepopup() {
    $.post( "loadpopupcontent.php",
            $("#discoveriesform" )
                .serialize() )
        .done( data => {
            $("#load_general_modal_content").html(data);
        } );
    $('#general-width').removeClass('w-900');
    $('#general_modal_title').html("Calculate Date");
    $('#general_modal').modal('toggle');
}
function calculatedduedateaction() {
    $.post( "calculateduedateaction.php",
            $("#formduedatecalculation" )
                .serialize() )
        .done( data => {
            $("#due").val(data);
        } );
    setTimeout( _ => {
        $('#general_modal').modal('toggle')
    }, 2000);
}

function serveFunction() { //debugger;
    var type = '<?= $type ?>';
    //START LOADER
    $.LoadingOverlay("show");
    var formid = $("#form_id").val();
    var isagree = true;
    if( formid == <?= Discovery::FORM_CA_FROGS  ?> ||
        formid == <?= Discovery::FORM_CA_FROGSE ?> ||
        formid == <?= Discovery::FORM_CA_RPDS   ?> ) {
        serveFunctionMain();
    }
    else if( formid == <?= Discovery::FORM_CA_SROGS ?> ||
             formid == <?= Discovery::FORM_CA_RFAS  ?> ) {
        $(".questionscls").each( function(index) {
            if( $(this).val() > 35 && $(".question_titlecls:eq("+index+")").val() ) {
                isagree = false;
            }
        } );
        if( /*true ||*/ !isagree && type ==  <?= Discovery::TYPE_EXTERNAL ?> ) {
            PopupForDeclaration(1);
        }
        else {
            serveFunctionMain();
        }
    }
    else {
        $.LoadingOverlay("hide");
        response('{"messagetype":4,"pkerrorid":7,"messagetext":"Please select form."}');
    }
}
function serveFunctionMain( signDAD = 1 ) {
    for( instance in CKEDITOR.instances ) {
        CKEDITOR.instances[instance].updateElement();
    }
    $.post( "discoveryaction.php?serve=0",
            $("#discoveriesform" )
                .serialize() )
        .done( data => {
            setTimeout( _ => {
                var obj = JSON.parse(data);
                if( obj.messagetype == 4 ) {
                    $.LoadingOverlay("hide");
                    msg(data);
                }
                else {
                    //writeDiscoveryPDF(obj.uid);
                    PopupForPOS(obj.id,signDAD);
                }
            }, 1000);
    });
}
function writeDiscoveryPDF( uid ) {
    $.get( "makepdf.php", { id: uid, downloadORwrite: 1,view:0 }).done(function( data ) {});
}
function saveFunction() {
    var type = '<?= $type ?>';
    var formid = $("#form_id").val();
    var isagree = true;
    if( formid == <?= Discovery::FORM_CA_FROGS  ?> ||
        formid == <?= Discovery::FORM_CA_FROGSE ?> ||
        formid == <?= Discovery::FORM_CA_RPDS   ?> ||
        formid == '' ) {
        //PopupForPOS();
        buttonsave();
    }
    else if( formid == <?= Discovery::FORM_CA_SROGS ?> ||
             formid == <?= Discovery::FORM_CA_RFAS  ?> ) {
        $( ".questionscls" )
            .each( function(index) {
                if( $(this).val() > 35 && $(".question_titlecls:eq("+index+")").val() ) {
                    isagree = false;
                }
            });

        setTimeout( _ => {
            if( !isagree && type == <?= Discovery::TYPE_EXTERNAL ?> ) {
                PopupForDeclaration(2);
            }
            else {
                buttonsave();
            }
        }, 1000);
    }
}
function PopupForDeclaration( pos_or_save ) { //pos_or_save: 1: POS, 2: Save
    const _state = '<?= $discovery['dec_state'] ?>',
          _city  = '<?= $discovery['dec_city']  ?: $atorny_city ?>'

    $.post( `loaddeclarationpopupcontent.php?pos_or_save=${pos_or_save}&dec_city=${_city}&dec_state=${_state}`,
            $("#discoveriesform" )
                .serialize() )
        .done( data => {
            $('#general-width').addClass('w-900');
            $('#general_modal_title').html("DECLARATION FOR ADDITIONAL DISCOVERY");
            $("#load_general_modal_content").html(data);
            //END LOADER
            $.LoadingOverlay("hide");
            $('#general_modal').modal('toggle');
        });
}
function signdeclaration( pos_or_save ) {
    for( instance in CKEDITOR.instances ) {
        CKEDITOR.instances[instance].updateElement(); // 🤔🧐
    }
    var dec_state   = $("#dec_state").val();
    var dec_city    = $("#dec_city").val();
    var error       = 0;
    var msg         = "";

    if( !dec_city ) {
        error   = 1;
        msg     = "Please enter city.";
    }
    if( !dec_state ) {
        error   = 1;
        msg     = "Please enter state.";
    }
    if( error == 1 ) {
        $("#DEC_msgdiv").html(msg);
    }
    else {
        $.LoadingOverlay("show");
        $.post( "createsigndeclarationaction.php",
                $("#formdeclaration" )
                    .serialize() )
            .done( data => {
                $('#general_modal').modal('toggle');
                if( pos_or_save == 1 ) {
                    serveFunctionMain();
                }
                else {
                    buttonsave();
                }
            });
    }
}

function inConjunctionForm() {
    if( $("#in_conjunction").prop('checked') ) {
        $("#interogatoriesTypeDiv").show();
    }
    else {
        $("#interogatoriesTypeDiv").hide();
    }
}
function loaddocsFunction( form_id ) {
    if( form_id == 3 || form_id == 4 ) {
        $("#loaddocs").show();
    }
    else {
        $("#loaddocs").hide();
    }
}
function loaduploadeddocs() {
    var rp_uid  = '<?= $uid ?>';
    var doctype = '<?= $doctype ?>';
    $("#uploadeddocs")
        .load("loaduploadeddocs.php?rp_uid="+rp_uid+"&doctype="+doctype);
}
function deleteDoc( id, rp_uid ) {
    $.post( "deletefrontdocs.php",
            { id: id,rp_uid:rp_uid } )
        .done( data => {
            loaduploadeddocs();
        });
}
function PopupForPOS( discovery_id, signDAD = 1 ) {
    $("#load_general_modal_content").html('');
    $.post( "loadpospopupcontent.php", { id:discovery_id, respond:0 } )
        .done( data => {
            $("#load_general_modal_content").html(data);
        });
    $('#general_modal_title').html("PROOF OF ELECTRONIC SERVICE");
    $('#general-width').addClass('w-900');
    setTimeout( _ => {
        //END LOADER
        $.LoadingOverlay("hide");
        if( signDAD == 1 ) {
            $('#general_modal').modal('toggle');
        }
    }, 2000 );
}
function addrow( rowid ) {
    $.get( "<?= DOMAIN ?>discoveryaddformquestion.php?totalrows=1", data => {
        $('#'+rowid).before(data);
        arrangequestionnumber();
    });
}
function checkClientEmailFound( discovery_id, actiontype ) {
    var client_id = $("#responding").val();
    $.post( "checkclientemailfound.php", { client_id, discovery_id, actiontype } )
        .done( data => {
            var obj = JSON.parse(data);
            if( obj.found  ) {
                if( actiontype == 2 ) {
                    $('#client-instructions-send').on('click', _ => buttonsaveandsend('#notes_for_client') );
                    $('#discovery-client-instructions_modal').modal('show');
                } else {
                    buttonsaveandsend();
                }
            }
            else {
                callclientemailmodal( obj.discovery_id, obj.actiontype, obj.client_id );
            }
        });
}
function saveclientemail() {
    var client_email= $("#client_email").val();
    $("#msgAddEmailClientModal").html("");
    if( client_email )     {
        $.post( "saveclientemailaction.php", $("#addClientEmailModal").serialize() )
            .done( data => {
                var obj = JSON.parse(data);
                $('#client-email-found_modal').modal('show');
                buttonsaveandsend();
            });
    }
    else {
        $("#msgAddEmailClientModal").html("Please enter client email.");
    }
}
</script>
