<!-- pdf-header -->
<style>
.tabela1 {
	width:100% !important;
	overflow:wrap !important;
}
.tabela1 {
	border: 0px solid #A2A9B1;
	border-collapse: collapse;
}
.tabela1 tbody tr td {
	padding: 5px 10px 5px 10px;
	border:0px solid #A2A9B1;
	border-collapse: collapse;
}
.text-center {
    text-align: center;
}
h5 {
    font-size: 1.1em;
}
@page :right {
	footer: htmlpagefooter ;
}
</style>

<table class="tabela1" style="border:none !important">
    <tr>
        <th colspan="2"><h2 align="center"></h2></th>
    </tr>
    <tr>
        <td><?= nl2br($masterhead) ?></td>
    </tr>
    <tr>
    	<td>
        	<br />
            <?= "Attorney for $att_for_client_role" ?><br />
            <?= $att_for_client_name ?><br />
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        	<h2><?= strtoupper ("SUPERIOR COURT OF CALIFORNIA <br />COUNTY OF ".$county_name) ?></h2>
        </td>
    </tr>
    <tr>
    	<td style="border-right:1px solid;border-bottom:1px solid" width="50%">
        	<?= $plaintiff ?>
            <br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plaintiff(s)<br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;vs.<br />
           <?= $defendant ?><br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Defendant(s).
        </td>
        <td style="border-bottom:1px solid">
            Case No. <?= $case_number ?>
            <br /><br />
            <h3 style="font-weight:normal">
<?php
            echo strtoupper($form_name);
            if( !empty($con_Details) ) {
                $conjunction = Discovery::getTitle( $con_Details['con_discovery_name'], $con_Details['con_setnumber'], Discovery::STYLE_AS_IS );
                echo "<br><br><i>Served in conjunction with <br>$conjunction</i>";
            }
?>
            </h3>
        </td>
    </tr>
    <tr>
        <td>PROPOUNDING PARTY:</td>
        <td><?= strtoupper($proponding_name) ?></td>
    </tr>
    <tr>
        <td>RESPONDING PARTY:</td>
        <td><?= strtoupper($responding_name) ?></td>
    </tr>
    <tr>
        <td>SET NO.:</td>
        <td><?= numberTowords(strtoupper($set_number)) ?></td>
    </tr>
</table>
<?php
    if( $view != 1 ) {
?>
<table class="tabela1" style="border:none !important;overflow: wrap">
    <tr>
        <td colspan="2">
        Responding party submits the following responses to propounding party, pursuant to Code of Civil Procedure section 2031.010.
        <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><h3><u>PRELIMINARY STATEMENT</u></h3></td>
    </tr>
</table>
<p style="line-height:25px">This responding party has not completed its investigation or discovery of the facts of this case and is not yet prepared for trial. The answers contained herein are based upon the information presently available, and specifically known, to this responding party and disclose only those contentions that presently occur to such party. It is anticipated that further discovery, independent investigation, legal research, and analysis will supply additional facts, modify known facts, and establish entirely new factual or legal contentions that may lead to substantial additions and modifications to the contentions set forth herein.</p>
<?php
    }
    else {
        if( trim($instructions) || !in_array($form_id,array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS)) ) {
?>
    <table class="tabela1" style="border:none !important;overflow: wrap">
        <tr>
            <td colspan="2" align="center"><h3><u>INSTRUCTIONS</u></h3></td>
        </tr>
    </table>
<?php
        }
	if( in_array($form_id,array(Discovery::FORM_CA_SROGS, Discovery::FORM_CA_RFAS, Discovery::FORM_CA_RPDS)) ) {
?>
    <div> <?= html_entity_decode($instructions) ?> </div>
<?php
	}
	else {
		if( $form_id == Discovery::FORM_CA_FROGS ) {
			$checkedimg			=	'<img src="../uploads/icons/checkbox_checked_small.png" width="15px">';
			$uncheckedimg		=	'<img src="../uploads/icons/checkbox_empty_small.png" width="15px">';
			$incidenttext1		=	"&nbsp;&nbsp;(1) INCIDENT Includes the circumstances and events surrounding the alleged accident, injury, or other occurrence or breach of contract giving rise to this action or proceeding.";

			if( $incidentoption == 1 ) {
				$incidenttext2		=	"&nbsp;&nbsp;(2) INCIDENT means (insert your definition here or on a separate, attached sheet labeled 'Sec. 4(a)(2)'):";
				$option1			=	$checkedimg.$incidenttext1;
				$option2			=	$uncheckedimg.$incidenttext2;
			}
			else if( $incidentoption == 2 ) {
				$incidenttext2		=	"&nbsp;&nbsp;(2) $incidenttext";
				$option1			=	$uncheckedimg.$incidenttext1;
				$option2			=	$checkedimg.$incidenttext2;
			}
?>
            <h4 class="text-center">Sec. 1. Instructions to All Parties</h4>
            <p>(a) Interrogatories are written questions prepared by a party to an action that are sent to any other party in the action to be answered under oath. The interrogatories below are form interrogatories approved for use in civil cases.</p>

            <p>(b) For time limitations, requirements for service on other parties, and other details, see Code of Civil Procedure section 2030 and the cases construing it.</p>
            <p>(c) These form interrogatories do not change existing law relating to interrogatories nor do they affect an answering party's right to assert any privilege or make any objection.</p>
            <h4 class="text-center">Sec. 2. Instructions to the Asking Party</h4>
            <p>(a) These interrogatories are designed for optional use by parties in unlimited civil cases where the amount demanded exceeds $25,000. Separate interrogatories, Form Interrogatories --Economic Litigation (form FI-129), which have no subparts, are designed for use in limited civil cases where the amount demanded is $25,000 or less; however, those interrogatories may also be used in unlimited civil cases.</p>

            <p>(b) Check the box next to each interrogatory that you want the answering party to answer. Use care in choosing those interrogatories that are applicable to the case.</p>
            <p>(c) You may insert your own definition of INCIDENT in Section 4, but only where the action arises from a course of conduct or a series of events occurring over a period of time.</p>
            <p>(d) The interrogatories in section 16.0, Defendant's Contentions -- Personal Injury, should not be used until the defendant has had a reasonable opportunity to conduct an investigation or discovery of plaintiff's injuries and damages.</p>
            <p>(e) Additional interrogatories may be attached.</p>
            <h4 class="text-center">Sec. 3. Instructions to the Answering Party</h4>
            <p>(a) An answer or other appropriate response must be given to each interrogatory checked by the asking party.</p>
            <p>(b) As a general rule, within 30 days after you are served with these interrogatories, you must serve your responses on the asking party and serve copies of your responses on all other parties to the action who have appeared. See Code of Civil Procedure section 2030 for details.</p>
            <p>(c) Each answer must be as complete and straightforward as the information reasonably available to you, including the information possessed by your attorneys or agents, permits. If an interrogatory cannot be answered completely, answer it to the extent possible.</p>
            <p>(d) If you do not have enough personal knowledge to fully answer an interrogatory, say so, but make a reasonable and good faith effort to get the information by asking other persons or organizations, unless the information is equally available to the asking party.</p>
            <p>(e) Whenever an interrogatory may be answered by referring to a document, the document may be attached as an exhibit to the response and referred to in the response. If the document has more than one page, refer to the page and section where the answer to the interrogatory can be found.</p>
            <p>(f) Whenever an address and telephone number for the same person are requested in more than one interrogatory, you are required to furnish them in answering only the first interrogatory asking for that information.</p>
            <p>(g) If you are asserting a privilege or making an objection to an interrogatory, you must specifically assert the privilege or state the objection in your written response.</p><p>(h) Your answers to these interrogatories must be verified, dated, and signed. You may wish to use the following form at the end of your answers.</p>
            <p>I declare under penalty of perjury under the laws of the State of California that the foregoing answers are true and correct.</p>
            <table class="tabela1" style="border:none !important;overflow: wrap">
                <tr>
                    <td  align="center">(DATE)</td>
                    <td  align="center">(SIGNATURE)</td>
                </tr>
            </table>

            <h4 class="text-center">Sec. 4. Definitions</h4>
            <p>Words in BOLDFACE CAPITALS in these interrogatories are defined as follows:</p>
            <p>(a) (Check one of the following):</p>
            <p><?= $option1 ?></p>
            <p><?= $option2 ?></p>

            <p>(b) YOU OR ANYONE ACTING ON YOUR BEHALF includes you, your agents, your employees, your insurance companies, their agents, their employees, your attorneys, your accountants, your investigators, and anyone else acting on your behalf.</p>
            <p>(c) PERSON includes a natural person, firm, association, organization, partnership, business, trust, limited liability company, corporation, or public entity.</p>
            <p>(d) DOCUMENT means a writing, as defined in Evidence Code section 250, and includes the original or a copy of handwriting, typewriting, printing, photostats, photographs, electronically stored information, and every other means of recording upon any tangible thing and form of communicating or representation, including letters, words, pictures, sounds, or symbols, or combinations of them.</p>
            <p>(e) HEALTH CARE PROVIDER includes any PERSON referred to in Code of Civil Procedure section 667.7(e)(3).</p>
            <p>(f) ADDRESS means the street address, including the city, state, and zip code.</p>

            <h5 class="text-center">Sec. 5. INVALID OBJECTIONS</h5>
            <p>Calls for a legal conclusion: “An interrogatory is not objectionable because an answer to it involves an opinion or contention that relates to fact or the application of law to fact, or would be based on information obtained or legal theories developed in anticipation of litigation or in preparation for trial.” Code Civ.Proc., § 2030.010, subd. (b).</p>
            <p>Calls for speculation: This is an objection to the form of the question. Such objections are appropriate only at deposition, not for written discovery. Rylaarsdam et al., California Practice Guide: Civil Procedure Before Trial (The Rutter Group 2019) ¶ 8:721-8:722.</p>
            <p>Lack of foundation: Lack, or insufficiency, of foundation is not a valid objection to an interrogatory. Cal. Judges Benchbook Civ. Proc. Discovery (September 2018) § 18.36.</p>
<?php
		}
		else if( $form_id == Discovery::FORM_CA_FROGSE ) {
?>
		<div style="text-align:left">
            <h4 class="text-center">Sec. 1. Instructions to All Parties</h4>
            <p>(a) Interrogatories are written questions prepared by a party to an action that are sent to any other party in the action to be answered under oath. The interrogatories below are form interrogatories approved for use in employment cases.</p>
            <p>(b) For time limitations, requirements for service on other parties, and other details, see Code of Civil Procedure sections 2030.010-2030.410 and the cases construing those sections.</p>
            <p>(c) These form interrogatories do not change existing law relating to interrogatories nor do they affect an answering party's right to assert any privilege or make any objection.</p>
            <h4 class="text-center">Sec. 2. Instructions to the Asking Party</h4>
            <p>(a) These form interrogatories are designed for optional use by parties in employment cases. (Separate sets of interrogatories, Form Interrogatories-General (form DISC-001) and Form Interrogatories-Limited Civil Cases (Economic Litigation) (form DISC-004) may also be used where applicable in employment cases.)</p>

            <p>(b) Insert the names of the EMPLOYEE and EMPLOYER to whom these interrogatories apply in the definitions in sections 4(d) and (e) below.</p>
            <p>(c) Check the box next to each interrogatory that you want the answering party to answer. Use care in choosing those interrogatories that are applicable to the case.</p>
            <p>(d) The interrogatories in section 211.0, Loss of Income Interrogatories to Employer, should not be used until the employer has had a reasonable opportunity to conduct an investigation or discovery of the employee's injuries and damages.</p>
            <p>(e) Additional interrogatories may be attached.</p>

            <h4 class="text-center">Sec. 3. Instructions to the Answering Party</h4>
            <p>(a) You must answer or provide another appropriate response to each interrogatory that has been checked below.</p>
            <p>(b) As a general rule, within 30 days after you are served with these interrogatories, you must serve your responses on the asking party and serve copies of your responses on all other parties to the action who have appeared. See Code of Civil Procedure sections 2030.260-2030.270 for details.</p>
            <p>(c) Each answer must be as complete and straightforward as the information reasonably available to you permits. If an interrogatory cannot be answered completely, answer it to the extent possible.</p>
            <p>(d) If you do not have enough personal knowledge to fully answer an interrogatory, say so but make a reasonable and good faith effort to get the information by asking other persons or organizations, unless the information is equally available to the asking party.</p>
            <p>(e) Whenever an interrogatory may be answered by referring to a document, the document may be attached as an exhibit to the response and referred to in the response. If the document has more than one page, refer to the page and section where the answer to the interrogatory can be found.</p>
            <p>(f) Whenever an address and telephone number for the same person are requested in more than one interrogatory, you are required to furnish them in answering only the first interrogatory asking for that information.</p>
            <p>(g) If you are asserting a privilege or making an objection to an interrogatory, you must specifically assert the privilege or state the objection in your written response.</p>
            <p>(h) Your answers to these interrogatories must be verified, dated, and signed. You may wish to use the following form at the end of your answers:</p>
            <p>I declare under penalty of perjury under the laws of the State of California that the foregoing answers are true and correct.</p>
            </div>
            <table class="tabela1" style="border:none !important;overflow: wrap">
                <tr>
                    <td  align="center">(DATE)</td>
                    <td  align="center">(SIGNATURE)</td>
                </tr>
            </table>

            <h4 class="text-center">Sec. 4. Definitions</h4>
            <p>Words in BOLDFACE CAPITALS in these interrogatories are defined as follows:</p>
            <p>(a) PERSON includes a natural person, firm, association, organization, partnership, business, trust, limited liability company, corporation, or public entity.</p>
            <p>(b) YOU OR ANYONE ACTING ON YOUR BEHALF includes you, your agents, your employees, your insurance companies, their agents, their employees, your attorneys, your accountants, your investigators, and anyone else acting on your behalf.</p>
            <p>(c) EMPLOYMENT means a relationship in which an EMPLOYEE provides services requested by or on behalf of an EMPLOYER, other than an independent contractor relationship.</p>
<?php
        if( $personnames1 ) {
?>
            <p>(d) EMPLOYEE means a PERSON who provides services in an EMPLOYMENT relationship and who is a party to this lawsuit. For purposes of these interrogatories, EMPLOYEE refers to <?php echo $personnames1; if(substr($personnames1, -1) != '.') { echo "."; } ?> </p>
<?php
        }
        else {
?>
            <p>(d) EMPLOYEE means all such PERSONS</p>
<?php
        }
        if( $personnames2 ) {
?>
            <p>(e) EMPLOYER means a PERSON who employs an EMPLOYEE to provide services in an EMPLOYMENT relationship and who is a party to this lawsuit. For purposes of these interrogatories, EMPLOYER refers to <?php echo $personnames2; if(substr($personnames2, -1) != '.') { echo "."; } ?></p>
<?php
        }
        else {
?>
            <p>(d) EMPLOYEE means all such PERSONS</p>
<?php
        }
?>
        <p>(f) ADVERSE EMPLOYMENT ACTION means any TERMINATION, suspension, demotion, reprimand, loss of pay, failure or refusal to hire, failure or refusal to promote, or other action or failure to act that adversely affects the EMPLOYEE'S rights or interests and which is alleged in the PLEADINGS.</p>
        <p>(g) TERMINATION means the actual or constructive termination of employment and includes a discharge, firing, layoff, resignation, or completion of the term of the employment agreement.</p>
        <p>(h) PUBLISH means to communicate orally or in writing to anyone other than the plaintiff. This includes communications by one of the defendant's employees to others. (Kelly v. General Telephone Co. (1982) 136 Cal.App.3d 278, 284.)</p>
        <p>(i) PLEADINGS means the original or most recent amended version of any complaint, answer, cross-complaint, or answer to cross-complaint.</p>
        <p>(j) BENEFIT means any benefit from an EMPLOYER, including an "employee welfare benefit plan" or employee pension benefit plan" within the meaning of Title 29 United States Code section 1002(1) or (2) or ERISA.</p>
        <p>(k) HEALTH CARE PROVIDER includes any PERSON referred to in Code of Civil Procedure section 667.7(e)(3).</p>
        <p>(l) DOCUMENT means a writing, as defined in Evidence Code section 250, and includes the original or a copy of handwriting, typewriting, printing, photostats, photographs, electronically stored information, and every other means of recording upon any tangible thing and form of communicating or representation, including letters, words, pictures, sounds, or symbols, or combinations of them.</p>
        <p>(m) ADDRESS means the street address, including the city, state, and zip code.</p>
<?php
		}
	}
}
?>