<?php
@session_start();
include_once(__DIR__ . "/../bootstrap.php");
include_once(__DIR__ . "/../library/classes/functions.php");
$type         = $_GET['type'];
$discovery_id = $_GET['id'];
$form_id      = $_GET['form_id'];
$case_id      = $_GET['case_id'];
$viewonly     = @$_GET['viewonly'];
if (!$viewonly) {
    $viewonly = 0;
}

if ($discovery_id) {
    $discoveries      = $AdminDAO->getrows(
        'discoveries',
        "*",
        "id	= :id ",
        ['id' => $discovery_id]
    );
    $discovery        = $discoveries[0];
    $incidentoption   = $discovery['incidentoption'];
    $incidenttext     = $discovery['incidenttext'];
    $instruction_text = html_entity_decode($discovery['discovery_instrunctions']);
}

//Attorney Details
$attorneyDetails = $AdminDAO->getrows(
    "system_addressbook",
    "*",
    "pkaddressbookid = :pkaddressbookid",
    [":pkaddressbookid" => $_SESSION['addressbookid']]
);
$attorneyDetail  = $attorneyDetails[0];
$attorneyEmail   = $attorneyDetail['email'];
$attorneyPhone   = $attorneyDetail['phone'];
$attorneyName    = $attorneyDetail['firstname'] . " " . $attorneyDetail['lastname'];

$sides                  = new Side();
$currentSide            = $sides->getByUserAndCase($currentUser->id, $case_id);
$primaryAttorney        = $sides->getPrimaryAttorney($currentSide['id']);
$primaryAttorneyFirm    = $primaryAttorney['companyname'];
$primaryAttorneyAddress = makeaddress($primaryAttorney['pkaddressbookid']);

if (in_array($form_id, [Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE])) { // FROGS & FROGSE in EXTERNAL case
?>


    <div class="">
        <div class="<?= !$viewonly ? "col-sm-offset-2 col-sm-10 col-md-offset-1 col-md-11" : "col-md-12" ?>" style="padding:0">
            <!-- Instructions Section load -->
            <div class="panel panel-default">
                <div class="panel-heading instruction-collapse">
                    <div class="row">
                        <div class="col-sm-2 col-md-4"></div>
                        <div class="col-sm-8 col-md-4 text-center">
                            <h3> Instructions </h3>
                        </div>

                        <div class="col-sm-2 col-md-4">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" class="btn btn-primary pull-right"></a>
                            <?php if ($viewonly && @$currentUser && $currentUser->isAttorney()) { ?>
                                <button type="button" id="btn-objections" class="btn btn-primary btn-sidebar-toggle pull-right hidden" onclick="javascript:toggleKBSidebar(<?= $form_id ?>, ObjectionPanel);">
                                    <!--frogs/e-->
                                    <i class="fa fa-book" /><span>Objections</span>
                                </button>
                                <script>
                                    setTimeout(_ => {
                                        hasItems = $('textarea[name*="objection["]').length > 0;
                                        if (hasItems) {
                                            $("#btn-objections").removeClass("hidden")
                                            toggleKBSidebar(<?= $form_id ?>, ObjectionPanel, hasItems)
                                        }
                                    }, 1000);
                                </script>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php
                        if ($form_id == Discovery::FORM_CA_FROGS) {
                            if ($viewonly) {
                                $checkedimg    = '<img src="../uploads/icons/checkbox_checked_small.png" width="15px">';
                                $uncheckedimg  = '<img src="../uploads/icons/checkbox_empty_small.png" width="15px">';
                                $incidenttext1 = "&nbsp;&nbsp;(1) INCIDENT Includes the circumstances and events surrounding the alleged accident, injury, or other occurrence or breach of contract giving rise to this action or proceeding.";

                                if ($incidentoption == Discovery::INCIDENT_STANDARD) {
                                    $incidenttext2 = "&nbsp;&nbsp;(2) INCIDENT means (insert your definition here or on a separate, attached sheet labeled 'Sec. 4(a)(2)'):";
                                    $option1       = $checkedimg . $incidenttext1;
                                    $option2       = $uncheckedimg . $incidenttext2;
                                } elseif ($incidentoption == Discovery::INCIDENT_CUSTOM) {
                                    $incidenttext2 = "&nbsp;&nbsp;(2) $incidenttext";
                                    $option1       = $uncheckedimg . $incidenttext1;
                                    $option2       = $checkedimg . $incidenttext2;
                                }
                            }
                        ?>
                            <div id="instruction_data">
                                <table class="table" style="border:none !important">
                                    <tr>
                                        <td colspan="2" style="border:none">
                                            <div style="text-align:left">

                                                <h5 class="text-center">Sec. 1. Instructions to All Parties</h5>
                                                <p>(a) Interrogatories are written questions prepared by a party to an action
                                                    that are sent to any other party in the action to be answered under oath.
                                                    The interrogatories below are form interrogatories approved for use in
                                                    civil cases.</p>
                                                <p>(b) For time limitations, requirements for service on other parties, and
                                                    other details, see Code of Civil Procedure section 2030 and the cases
                                                    construing it.</p>
                                                <p>(c) These form interrogatories do not change existing law relating to
                                                    interrogatories nor do they affect an answering party's right to assert any
                                                    privilege or make any objection.</p>

                                                <h5 class="text-center">Sec. 2. Instructions to the Asking Party</h5>
                                                <p>(a) These interrogatories are designed for optional use by parties in
                                                    unlimited civil cases where the amount demanded exceeds $25,000. Separate
                                                    interrogatories, Form Interrogatories --Economic Litigation (form FI-129),
                                                    which have no subparts, are designed for use in limited civil cases where
                                                    the amount demanded is $25,000 or less; however, those interrogatories may
                                                    also be used in unlimited civil cases.</p>
                                                <p>(b) Check the box next to each interrogatory that you want the answering
                                                    party to answer. Use care in choosing those interrogatories that are
                                                    applicable to the case.</p>
                                                <p>(c) You may insert your own definition of INCIDENT in Section 4, but only
                                                    where the action arises from a course of conduct or a series of events
                                                    occurring over a period of time.</p>
                                                <p>(d) The interrogatories in section 16.0, Defendant's Contentions -- Personal
                                                    Injury, should not be used until the defendant has had a reasonable
                                                    opportunity to conduct an investigation or discovery of plaintiff's
                                                    injuries and damages.</p>
                                                <p>(e) Additional interrogatories may be attached.</p>

                                                <h5 class="text-center">Sec. 3. Instructions to the Answering Party</h5>
                                                <p>(a) An answer or other appropriate response must be given to each
                                                    interrogatory checked by the asking party.</p>
                                                <p>(b) As a general rule, within 30 days after you are served with these
                                                    interrogatories, you must serve your responses on the asking party and
                                                    serve copies of your responses on all other parties to the action who have
                                                    appeared. See Code of Civil Procedure section 2030 for details.</p>
                                                <p>(c) Each answer must be as complete and straightforward as the information
                                                    reasonably available to you, including the information possessed by your
                                                    attorneys or agents, permits. If an interrogatory cannot be answered
                                                    completely, answer it to the extent possible.</p>
                                                <p>(d) If you do not have enough personal knowledge to fully answer an
                                                    interrogatory, say so, but make a reasonable and good faith effort to get
                                                    the information by asking other persons or organizations, unless the
                                                    information is equally available to the asking party.</p>
                                                <p>(e) Whenever an interrogatory may be answered by referring to a document,
                                                    the document may be attached as an exhibit to the response and referred to
                                                    in the response. If the document has more than one page, refer to the page
                                                    and section where the answer to the interrogatory can be found.</p>
                                                <p>(f) Whenever an address and telephone number for the same person are
                                                    requested in more than one interrogatory, you are required to furnish them
                                                    in answering only the first interrogatory asking for that information.</p>
                                                <p>(g) If you are asserting a privilege or making an objection to an
                                                    interrogatory, you must specifically assert the privilege or state the
                                                    objection in your written response.</p>
                                                <p>(h) Your answers to these interrogatories must be verified, dated, and
                                                    signed. You may wish to use the following form at the end of your
                                                    answers.</p>
                                                <p>I declare under penalty of perjury under the laws of the State of California
                                                    that the foregoing answers are true and correct.</p>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>

                                            <table class='tabela1' align='center' style='border:none !important; border-spacing: 3em 0; overflow: wrap; margin-top:20px;'>

                                                <tr>
                                                    <td style='width:30px'></td>
                                                    <td align='center' style='border-top: 1px solid black;width:90px'> DATE </td>
                                                    <td style='width:160px'></td>
                                                    <td align='center' style='border-top: 1px solid black;width:140px'> SIGNATURE </td>
                                                    <td style='width:30px'></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>


                                    <tr>
                                        <td colspan="2" style="border:none;">
                                            <h5 class="text-center">Sec. 4. Definitions</h5>
                                            <p>Words in BOLDFACE CAPITALS in these interrogatories are defined as follows:</p>
                                            <p>(a) (Check one of the following):</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border:none;">
                                            <?php
                                            if ($viewonly) {
                                                echo $option1;
                                            } else {
                                            ?>
                                                <div class='checkbox_replace1'>
                                                    <input type="radio" name="incidentoption" value="<?= Discovery::INCIDENT_STANDARD ?>" <?= ($discovery['incidentoption'] == Discovery::INCIDENT_STANDARD) ? " checked " : '' ?> onclick="incidentmeans(<?= Discovery::INCIDENT_STANDARD ?>)" />
                                                    &nbsp;&nbsp;(1) INCIDENT Includes the circumstances and events surrounding
                                                    the alleged accident, injury, or other occurrence or breach of contract
                                                    giving rise to this action or proceeding.
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border:none;">
                                            <?php
                                            if ($viewonly) {
                                                echo $option2;
                                            } else {
                                            ?>
                                                <div class='checkbox_replace2'>
                                                    <input type="radio" name="incidentoption" value="<?= Discovery::INCIDENT_CUSTOM ?>" <?= ($discovery['incidentoption'] == Discovery::INCIDENT_CUSTOM) ? " checked " : '' ?> onclick="incidentmeans(<?= Discovery::INCIDENT_CUSTOM ?>)" />
                                                    &nbsp;&nbsp;(2) INCIDENT means (insert your definition here or on a separate, attached sheet labeled "Sec. 4(a)(2)"):
                                                </div>
                                                <div class='remove_incidenttext'>
                                                    <div id="incidentDiv" <?= ($discovery['incidentoption'] == Discovery::INCIDENT_STANDARD || $discovery['incidentoption'] == "") ? ' style="display:none" ' : '' ?>>
                                                        <textarea class="form-control" rows="5" name="incidenttext" id="incidenttext"><?=
                                                                                                                                        $discovery['incidenttext']
                                                                                                                                        ?></textarea>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border:none;">
                                            <p>(b) YOU OR ANYONE ACTING ON YOUR BEHALF includes you, your agents, your
                                                employees, your insurance companies, their agents, their employees, your
                                                attorneys, your accountants, your investigators, and anyone else acting on your
                                                behalf.</p>
                                            <p>(c) PERSON includes a natural person, firm, association, organization,
                                                partnership, business, trust, limited liability company, corporation, or public
                                                entity.</p>
                                            <p>(d) DOCUMENT means a writing, as defined in Evidence Code section 250, and
                                                includes the original or a copy of handwriting, typewriting, printing,
                                                photostats, photographs, electronically stored information, and every other
                                                means of recording upon any tangible thing and form of communicating or
                                                representation, including letters, words, pictures, sounds, or symbols, or
                                                combinations of them.</p>
                                            <p>(e) HEALTH CARE PROVIDER includes any PERSON referred to in Code of Civil
                                                Procedure section 667.7(e)(3).</p>
                                            <p>(f) ADDRESS means the street address, including the city, state, and zip
                                                code.</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php
                        } elseif ($form_id == Discovery::FORM_CA_FROGSE) {
                            $personnames2 = $discovery['personnames2'];
                            $personnames1 = $discovery['personnames1']
                        ?>
                            <div id="instruction_data">
                                <table class="table">
                                    <tr>
                                        <td colspan="2" style="border:none;">
                                            <div style="text-align:left">

                                                <h5 class="text-center">Sec. 1. Instructions to All Parties</h5>
                                                <p>(a) Interrogatories are written questions prepared by a party to an action
                                                    that are sent to any other party in the action to be answered under oath.
                                                    The interrogatories below are form interrogatories approved for use in
                                                    employment cases.</p>
                                                <p>(b) For time limitations, requirements for service on other parties, and
                                                    other details, see Code of Civil Procedure sections 2030.010-2030.410 and
                                                    the cases construing those sections.</p>
                                                <p>(c) These form interrogatories do not change existing law relating to
                                                    interrogatories nor do they affect an answering party's right to assert any
                                                    privilege or make any objection.</p>

                                                <h5 class="text-center">Sec. 2. Instructions to the Asking Party</h5>
                                                <p>(a) These form interrogatories are designed for optional use by parties in
                                                    employment cases. (Separate sets of interrogatories, Form
                                                    Interrogatories-General (form DISC-001) and Form Interrogatories-Limited
                                                    Civil Cases (Economic Litigation) (form DISC-004) may also be used where
                                                    applicable in employment cases.)</p>
                                                <p>(b) Insert the names of the EMPLOYEE and EMPLOYER to whom these
                                                    interrogatories apply in the definitions in sections 4(d) and (e)
                                                    below.</p>
                                                <p>(c) Check the box next to each interrogatory that you want the answering
                                                    party to answer. Use care in choosing those interrogatories that are
                                                    applicable to the case.</p>
                                                <p>(d) The interrogatories in section 211.0, Loss of Income Interrogatories to
                                                    Employer, should not be used until the employer has had a reasonable
                                                    opportunity to conduct an investigation or discovery of the employee's
                                                    injuries and damages.</p>
                                                <p>(e) Additional interrogatories may be attached.</p>

                                                <h5 class="text-center">Sec. 3. Instructions to the Answering Party</h5>
                                                <p>(a) You must answer or provide another appropriate response to each
                                                    interrogatory that has been checked below.</p>
                                                <p>(b) As a general rule, within 30 days after you are served with these
                                                    interrogatories, you must serve your responses on the asking party and
                                                    serve copies of your responses on all other parties to the action who have
                                                    appeared. See Code of Civil Procedure sections 2030.260-2030.270 for
                                                    details.</p>
                                                <p>(c) Each answer must be as complete and straightforward as the information
                                                    reasonably available to you permits. If an interrogatory cannot be answered
                                                    completely, answer it to the extent possible.</p>
                                                <p>(d) If you do not have enough personal knowledge to fully answer an
                                                    interrogatory, say so but make a reasonable and good faith effort to get
                                                    the information by asking other persons or organizations, unless the
                                                    information is equally available to the asking party.</p>
                                                <p>(e) Whenever an interrogatory may be answered by referring to a document,
                                                    the document may be attached as an exhibit to the response and referred to
                                                    in the response. If the document has more than one page, refer to the page
                                                    and section where the answer to the interrogatory can be found.</p>
                                                <p>(f) Whenever an address and telephone number for the same person are
                                                    requested in more than one interrogatory, you are required to furnish them
                                                    in answering only the first interrogatory asking for that information.</p>
                                                <p>(g) If you are asserting a privilege or making an objection to an
                                                    interrogatory, you must specifically assert the privilege or state the
                                                    objection in your written response.</p>
                                                <p>(h) Your answers to these interrogatories must be verified, dated, and
                                                    signed. You may wish to use the following form at the end of your
                                                    answers:</p>
                                                <p>I declare under penalty of perjury under the laws of the State of California
                                                    that the foregoing answers are true and correct.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>

                                            <table class='tabela1' align='center' style='border:none !important; border-spacing: 3em 0; overflow: wrap; margin-top:20px;'>

                                                <tr>
                                                    <td style='width:30px'></td>
                                                    <td align='center' style='border-top: 1px solid black;width:90px'> DATE </td>
                                                    <td style='width:160px'></td>
                                                    <td align='center' style='border-top: 1px solid black;width:140px'> SIGNATURE </td>
                                                    <td style='width:30px'></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border:none !important">
                                            <h5 class="text-center">Sec. 4. Definitions</h5>
                                            <p>Words in BOLDFACE CAPITALS in these interrogatories are defined as follows:</p>
                                            <p>(a) PERSON includes a natural person, firm, association, organization,
                                                partnership, business, trust, limited liability company, corporation, or public
                                                entity.</p>
                                            <p>(b) YOU OR ANYONE ACTING ON YOUR BEHALF includes you, your agents, your
                                                employees, your insurance companies, their agents, their employees, your
                                                attorneys, your accountants, your investigators, and anyone else acting on your
                                                behalf.</p>
                                            <p>(c) EMPLOYMENT means a relationship in which an EMPLOYEE provides services
                                                requested by or on behalf of an EMPLOYER, other than an independent contractor
                                                relationship.</p>
                                            <?php
                                            if ($viewonly) {
                                                if ($personnames1) {
                                            ?>
                                                    <p>(d) EMPLOYEE means a PERSON who provides services in an EMPLOYMENT
                                                        relationship and who is a party to this lawsuit. For purposes of these
                                                        interrogatories, EMPLOYEE refers to: <?= $personnames1 ?> </p>
                                                <?php
                                                } else {
                                                ?>
                                                    <p>(d) EMPLOYEE means all such PERSONS</p>
                                                <?php
                                                }
                                                if ($personnames2) {
                                                ?>
                                                    <p>(e) EMPLOYER means a PERSON who employs an EMPLOYEE to provide services
                                                        in an EMPLOYMENT relationship and who is a party to this lawsuit. For
                                                        purposes of these interrogatories, EMPLOYER refers
                                                        to <?php echo $personnames2; ?>:</p>
                                                <?php
                                                } else {
                                                ?>
                                                    <p>(d) EMPLOYEE means all such PERSONS</p>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <p>(d) EMPLOYEE means a PERSON who provides services in an EMPLOYMENT
                                                    relationship and who is a party to this lawsuit. For purposes of these
                                                    interrogatories, EMPLOYEE refers to (insert name): </p>
                                                <textarea class="form-control" rows="5" name="personnames1" id="personnames1"><?=
                                                                                                                                $discovery['personnames1']
                                                                                                                                ?></textarea>
                                                <p>(e) EMPLOYER means a PERSON who employs an EMPLOYEE to provide services in
                                                    an EMPLOYMENT relationship and who is a party to this lawsuit. For purposes
                                                    of these interrogatories, EMPLOYER refers to (insert name):</p>
                                                <textarea class="form-control" rows="5" id="personnames2" name="personnames2"><?=
                                                                                                                                $discovery['personnames2'];
                                                                                                                                ?></textarea>
                                            <?php
                                            }
                                            ?>
                                            <p>(f) ADVERSE EMPLOYMENT ACTION means any TERMINATION, suspension, demotion,
                                                reprimand, loss of pay, failure or refusal to hire, failure or refusal to
                                                promote, or other action or failure to act that adversely affects the
                                                EMPLOYEE'S rights or interests and which is alleged in the PLEADINGS.</p>
                                            <p>(g) TERMINATION means the actual or constructive termination of employment and
                                                includes a discharge, firing, layoff, resignation, or completion of the term of
                                                the employment agreement.</p>
                                            <p>(h) PUBLISH means to communicate orally or in writing to anyone other than the
                                                plaintiff. This includes communications by one of the defendant's employees to
                                                others. (<i>Kelly v. General Telephone Co.</i> (1982) 136 Cal.App.3d 278, 284.)
                                            </p>
                                            <p>(i) PLEADINGS means the original or most recent amended version of any
                                                complaint, answer, cross-complaint, or answer to cross-complaint.</p>
                                            <p>(j) BENEFIT means any benefit from an EMPLOYER, including an "employee welfare
                                                benefit plan" or employee pension benefit plan" within the meaning of Title
                                                29 United States Code section 1002(1) or (2) or ERISA.</p>
                                            <p>(k) HEALTH CARE PROVIDER includes any PERSON referred to in Code of Civil
                                                Procedure section 667.7(e)(3).</p>
                                            <p>(l) DOCUMENT means a writing, as defined in Evidence Code section 250,
                                                and includes the original or a copy of handwriting, typewriting, printing,
                                                photostats, photographs, electronically stored information, and every other
                                                means of recording upon any tangible thing and form of communicating or
                                                representation, including letters, words, pictures, sounds, or symbols, or
                                                combinations of them.</p>
                                            <p>(m) ADDRESS means the street address, including the city, state, and zip
                                                code.</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="--row col-sm-12 col-md-12" style="padding:0">
        <button type="button" id="btn-definitions" class="btn btn-primary btn-sidebar-toggle pull-right hidden" onclick="javascript:toggleKBSidebar(<?= $form_id ?>, DefinitionPanel);">
            <i class="fa fa-book" /><span>Definitions</span>
        </button>
    </div>
    <script>
        setTimeout(_ => {
            hasItems = $('textarea[name*="question_titles["]').length > 0
            if (hasItems) {
                $("#btn-definitions").removeClass("hidden")
                toggleKBSidebar(<?= $form_id ?>, DefinitionPanel, hasItems)
            }
        }, 1000);
    </script>
    <?php
    if (!$discovery_id && $type == Discovery::TYPE_EXTERNAL) {
        if ($form_id == Discovery::FORM_CA_SROGS) {
            // [old instructions]
            // <p> Each answer must be as complete and straightforward as the information reasonably available to you, including the
            //     information possessed by your attorneys or agents, permits. If an interrogatory cannot be answered completely, answer it to
            //     the extent possible. If you do not have enough personal knowledge to fully answer an interrogatory, say so, but make a
            //     reasonable and good faith effort to get the information by asking other persons or organizations, unless the information is
            //     equally available to the asking party. Whenever an interrogatory may be answered by referring to a document, the document
            //     may be attached as an exhibit to the response and referred to in the response. If the document has more than one page,
            //     refer to the page and section where the answer to the interrogatory can be found. You may respond by attaching a copy of
            //     the document to your answers to these interrogatories. Whenever an address or telephone number for the same person are
            //     requested in more than one interrogatory, you are required to furnish them in answering only the first interrogatory asking
            //     for that information.</p><p>If you are asserting a privilege or making an objection to an interrogatory, you must
            //     specifically assert the privilege or state the objection in your written response. Your answers to these interrogatories
            //     must be verified, dated, signed, and the original must be included in your response.</p>

            // <h5 class='text-center'> INVALID OBJECTIONS </h5>
            // <p> Calls for a legal conclusion: “An interrogatory is not objectionable because an answer to it involves an opinion or contention that relates to fact
            //     or the application of law to fact, or would be based on information obtained or legal theories developed in anticipation of litigation or in preparation
            //     for trial.” <i>Code Civ.Proc.</i>, § 2030.010, subd. (b). </p>
            // <p> Calls for speculation: This is an objection to the form of the question. Such objections are appropriate only at deposition, not for written discovery.
            //     Rylaarsdam et al., <i>California Practice Guide: Civil Procedure Before Trial</i> (The Rutter Group 2019) ¶ 8:721-8:722. </p>
            // <p> Lack of foundation: Lack, or insufficiency, of foundation is not a valid objection to an interrogatory. <i>Cal. Judges Benchbook Civ. Proc. Discovery</i> (September 2018) § 18.36. </p>
            $instruction_text = "";
            $instruction_info = "
            <p>
                <img src='" . ASSETS_URL . "images/court.png' style='width: 18px;padding-right: 3px;'>
                No preface or instruction is allowed.
                <a href='#'>
                    <i style='font-size:16px;' data-placement='top' data-toggle='tooltip' title='' class='fa fa-info-circle tooltipshow client-btn' aria-hidden='true' data-original-title='
                        Code of Civil Procedure section 2030.060, subdivision (d)<br/>
                        <p style=\"text-align:left;\">No preface or instruction shall be included with a set of interrogatories unless it has been approved under Chapter 17.'>
                    </i>
                </a>
            </p>
            ";
        } else if ($form_id == Discovery::FORM_CA_RFAS) {
            // [old instructions]
            // <p> Pursuant to Code of Civil Procedure section 2030 et seq., propounding party hereby requests that responding party answer the
            //     following Requests for Admission, under oath, within thirty (30) days from the date hereof. </p>

            // <h5 class='text-center'><b> INVALID OBJECTIONS </b></h5>
            // <p> Calls for a legal conclusion: “When a party is served with a request for admission concerning a legal question properly
            //     raised in the pleadings he cannot object simply by asserting that the request calls for a conclusion of law. He should make
            //     the admission if he is able to do so and does not in good faith intend to contest the issue at trial, thereby 'setting at
            //     rest a triable issue.' Otherwise he should set forth in detail the reasons why he cannot truthfully admit or deny the
            //     request.” <i>Burke v. Superior Court</i> (1969) 71 Cal.2d 276, 282, internal citations omitted. See also, <i>Cembrook v. Superior
            //     Court In and For City and County of San Francisco</i> (1961) 56 Cal.2d 423, 429 [“calls for a legal conclusion” is not a valid
            //     objection.]. </p>
            // <p> Calls for speculation: This is an objection to the form of the question. Such objections are appropriate only at deposition,
            //     not for written discovery. Rylaarsdam et al., <i>California Practice Guide: Civil Procedure Before Trial</i> (The Rutter Group
            //     2019) ¶ 8:721-8:722. </p>
            $instruction_text = "
            
            <p style='line-height:40px !important;font-size: 14.7px;dispaly:block'>
            Requests for admission are written requests by a party to an action requiring that any other party to the action either admit or deny, under oath, the truth of certain facts or the genuineness of certain documents.
            For information on timing, the number of admissions a party may request from any other party, service of requests and responses, restriction on the style, format, and scope of requests for admission and responses to requests, and other details, see <i>Code of Civil Procedure</i> sections 94&mdash;95, 1013 and 2033.010&mdash;2033.420 and the case law relating to those sections.
      
            </p>

            <p style='line-height:40px  !important;font-size: 14.7px;dispaly:block'>
            An answering party should consider carefully whether to admit or deny the truth of facts or the genuineness of documents.
            With limited exceptions, an answering party will not be allowed to change an answer to a request for admission.
            There may be penalties if an answering party fails to admit the truth of any fact or the genuineness of any document when requested to do so and the requesting party later proves that the fact is true or that the document is genuine.
            These penalties may include, among other things, payment of the requesting party's attorney's fees incurred in making that proof. 
      
            </p>

            <p style='line-height:40px  !important;font-size: 14.7px;dispaly:block'>
            Unless there is an agreement or a court order providing otherwise, the answering party must respond in writing to requests for admission within 30 days after they are served, or within 5 days after service in an unlawful detainer action.
            There may be significant penalties if an answering party fails to provide a timely written response to each request for admission.
            These penalties may include, among other things, an order that the facts in issue are deemed true or the documents in issue are deemed genuine for purposes of the case. 
            </p>



            <p style='line-height:40px  !important;font-size: 14.7px;dispaly:block'>
            Answers to <i>Requests for Admission</i> must be given under oath. The answering party should use the following language at the end of the responses:
            </p>

                
                <table class='tabela1' style='border:none !important; border-spacing: 3em 0; overflow: wrap;margin-bottom:40px'>
                    <tr>
                        <td  align='center' colspan='2'><em>I declare under penalty of perjury under the laws of the State of California that the foregoing answers are true and correct.</em></td>
                    </tr>
                    </table>
                <table class='tabela1' style='border:none !important; border-spacing: 3em 0; overflow: wrap'>
                
                    <tr>
                    <td style='width:30px'></td>
                    <td align='center' style='border-top: 1px solid black;width:90px'> DATE  </td>
             <td style='width:160px'></td>
                    <td align='center' style='border-top: 1px solid black;width:140px' > SIGNATURE   </td>
                    <td style='width:30px'></td>
                </tr>
                </table>
                <p> These instructions are only a summary and are not intended to provide complete information about requests for admission.
                    This <i>Requests for Admission</i> form does not change existing law relating to requests for admission, nor does it affect an answering party's right to assert any privilege or to make any objection. </p>
       
                    ";
            $instruction_info = "
            <p>
                <img src='" . ASSETS_URL . "images/court.png' style='width: 18px;padding-right: 3px;'>
                These are the only instructions allowed.
                <a href='#'>
                    <i style='font-size:16px;' data-placement='top' data-toggle='tooltip' title='' class='fa fa-info-circle tooltipshow client-btn' aria-hidden='true' data-original-title='
                        Code of Civil Procedure section 2033.060, subdivision (d)<br/>
                        <p style=\"text-align:left;\">Each request for admission shall be full and complete in and of itself. No preface or instruction shall be included with a set of admission requests unless it has been approved under Chapter 17 (commencing with Section 2033.710).</p>'>
                    </i>
                </a>
            </p>
            ";
        } else if ($form_id == Discovery::FORM_CA_RPDS) {
            $instruction_text = "
                <p>DEMAND IS HEREBY MADE UPON YOU, pursuant to Code of Civil Procedure section 2031, et seq. to produce the documents and
                    things described herein at " . $primaryAttorneyFirm . ", " . $primaryAttorneyAddress . " within thirty (30) days of service hereof. Each
                    respondent shall respond separately, under oath, to each item or category of item by any of the following:</p>

                <ol>
                    <li>A statement that you will comply with the demand;</li>
                    <li>A representation that you lack the ability to comply with the demand; or</li>
                    <li>An objection to the demand setting forth, in detail, the nature and factual basis for</li>
                </ol>

                <p> the objection. If objection is made on the basis of a privilege, then you must include a Privilege Log identifying each
                    document being withheld and the basis under which privilege has been asserted.</p>
                <p><b>THE DOCUMENTS DEMANDED HEREIN ARE LIMITED TO THOSE IN THE POSSESSION, CUSTODY OR CONTROL OF RESPONDING PARTY, OR THAT
                    PARTY'S ATTORNEY, INSURERS OR AGENTS</b>. Said documents are relevant to the subject matter of this action, or
                    reasonably calculated to lead to the discovery of admissible evidence in this action.</p>
                <br />

                <h5 class='text-center'><b>DEFINITIONS</b></h5>
                <p>Words in all CAPITALS are defined as follows: YOU or YOUR mean respondent or any of YOUR agents, representatives, and/or
                    affiliated entities or anyone acting or whom YOU know or believe is purporting to act on YOUR behalf or who has acted or
                    whom YOU know or believe is purporting to act on YOUR behalf. IDENTIFY, IDENTITY, or INDENTIFYING mean to provide the name,
                    address, and telephone number. DOCUMENT means a writing, as defined in Evidence Code &sect; 250, and includes the original
                    or a copy of handwriting, typewriting, printing, photocopies, photostating, photographing, and every other means of
                    recording upon any tangible thing and form of communicating or representation, including letters, words, pictures, sounds,
                    or symbols, or combinations of them. COMMUNICATION means any conveyance of information, oral, written, or via gesture, and
                    regardless of whether the information was received or acknowledged. RELATED TO means referencing or referring to in any
                    way, whether expressly or impliedly. POLICIES mean the way YOU run YOUR business, including but not limited to policies,
                    procedures, handbooks, guidelines, and rules. DISCIPLINARY ACTION means an action including but not limited to, a warning
                    or admonishment, whether oral or written, any reduction in rank, seniority, duties, schedule changes, title, benefits, pay,
                    suspension, or termination.</p>

                <h5 class='text-center'><b>INVALID OBJECTIONS</b></h5>
                <p>Calls for a legal conclusion: Although there is no authority directly on point, such requests in both Requests for Admission
                    and Interrogatories are non-objectionable. <i>Burke v. Superior Court</i> (1969) 71 Cal.2d 276, 282 and Code Civ.Proc.,
                    §2030.010(b).</p>
                <p>Calls for speculation: This is an objection to the form of the question. Such objections are appropriate only at deposition,
                    not for written discovery. Rylaarsdam et al., California Practice Guide: Civil Procedure Before Trial (The Rutter Group
                    2019) ¶ 8:721-8:722.</p>
            ";
        }
    }
    if (!$viewonly) {
    ?>
        <div class="form-group" id="instruction_id1" style="clear:both;">
            <label class=" col-sm-2 col-md-1 control-label">Instructions<span class="redstar" style="color:#F00"></span></label>
            <div class="col-sm-10 col-md-11">
                <?=
                $instruction_info
                ?>
                <textarea rows="5" name="instruction" id="instruction" placeholder="Form Instruction" class="form-control m-b"><?=
                                                                                                                                $instruction_text
                                                                                                                                ?></textarea>
            </div>
        <?php
    } else {
        ?>
            <div class="">
                <div class="col-md-12">
                    <!-- Instructions Section load -->
                    <div class="panel panel-default">
                        <div class="panel-heading instruction-collapse">
                            <div class="row">
                                <div class="col-sm-2 col-md-4"></div>
                                <div class="col-sm-8 col-md-4 text-center">
                                    <h3> Instructions </h3>
                                </div>
                                <div class="col-sm-2 col-md-4" style="margin-top: 8px;">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" class="btn btn-primary pull-right"></a>
                                    <?php if (@$currentUser && $currentUser->isAttorney()) { ?>
                                        <button type="button" id="btn-objections" class="btn btn-primary btn-sidebar-toggle pull-right hidden" onclick="javascript:toggleKBSidebar(<?= $form_id ?>, ObjectionPanel);">
                                            <!--rfas/rfps/rdps-->
                                            <i class="fa fa-book" /><span>Objections</span>
                                        </button>
                                        <script>
                                            setTimeout(_ => {
                                                hasItems = $('textarea[name*="objection["]').length > 0;
                                                if (hasItems) {
                                                    $("#btn-objections").removeClass("hidden")
                                                    toggleKBSidebar(<?= $form_id ?>, ObjectionPanel, hasItems)
                                                }
                                            }, 1000);
                                        </script>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in">
                            <div class="panel-body"><?=
                                                    $instruction_text
                                                    ?></div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
    }
}
$forms     = $AdminDAO->getrows('forms', "*");
$formNames = array_map(function ($item) {
    return $item['short_form_name'];
}, $forms);
    ?>
    <script>
        globalThis['discoveryType'] = "<?= $type ?>";
        globalThis['discoveryForm'] = "<?= $form_id ?>";
        globalThis['discoveryFormNames'] = <?= json_encode($formNames, JSON_PRETTY_PRINT) ?>;

        jQuery($ => {
            const $instr = $('#loadinstructions'),
                $form = $instr.parents('form'),
                newFormName = "<?= $formNames[$form_id - 1] ?>"

            $form.removeClass((idx, classes) => {
                return (classes.match(/(^|\s)[-][-]form[-]\S+/g) || []).join(' ')
            })
            $form.addClass(`--form-${newFormName}`)

            $instr.addClass('--loaded')
        });
    </script>