<?php
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
require_once('tcpdf.php');
require_once('fpdf/src/autoload.php');

// create new PDF document
$pdf = new \setasign\Fpdi\TcpdfFpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PACRA');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set some content to print
$html = <<<EOD
<html>
    <head>
    <style>
    body {
    margin: 0;
}

.container{
  padding-right: 0px;
  padding-left: 0px;
}

.contents {
  margin: 0 10px 0 5px;
}
.er-mc-body{
  cursor: default;
  padding: 0.5em;
}

.er-mc-body textarea {
  overflow: auto;
  min-height: 11em;
  border: none;
  font-family: inherit;
  font-size: inherit;
  outline: none;
}

.er-mc-body textarea{
  border-bottom: 1px solid #eee;
  background-image: url(../images/pencil.png);
  background-repeat: no-repeat;
  background-size: 10px;
  background-position: top right;
}

.er-mc-body textarea:hover,
.er-mc-body textarea:focus {
  background-color: rgb(250, 250, 250);
  background-image: none;
}

.er-mc-masterhead{
  text-align: center;
  width: 20em;
  margin: auto;
  display: block;
  font-size: 17px !important;
  font-weight: bold;
  background-color: rgb(250, 250, 250);
  padding: 5px;
  border-radius: 5px;
}

.er-mc-date{
  margin: 1em 0 1em 0;
}

.er-mc-attorney-masterhead{
  width: 20em;
}

.er-mc-subject{
  margin: 1em 0 1em 0;
  font-weight: bold;
}

.er-mc-intro{
  width: 100%;
  margin-bottom: 3em;
}


.er-mc-response-question{
  margin-top: .5em;
}

.er-mc-response-question-number{
  font-size: 16px;
  font-weight: bold;
  display: block;
  border-radius: 3px;
  margin-bottom: 10px;
  text-decoration: underline;
}

.er-mc-toggle-question{
  cursor: pointer;
}

.er-mc-response-main-question .heading,
.er-mc-response-main-question-answer .heading,
.er-mc-meet-confer .heading{
  font-weight: bold;
}

.er-mc-response-sub-question{
  margin-left: 15px;
}

.er-mc-response-sub-question-answer{
  margin-left: 30px;
  font-size: 13px;
}

.er-mc-meet-confer-body{
  width: 100%;
  margin-bottom: 1em;
}

.er-mc-conclusion{
  margin-top: 3em;
  page-break-before: always;
}

.er-mc-conclusion .heading{
  text-align: center;
  font-weight: bold;
  text-decoration: underline;
  font-size: larger;
  margin-bottom: 1em;
}

.er-mc-conclusion-body{
  width: 100%;
  min-height: 260px !important;
}

.er-mc-signature{
  margin-top: 3em;
  width: 100%;
  height: 13em;
}

.er-mc-toggle-question{
  z-index: 0;
  outline: none !important;
}

a.er-mc-toggle-question.active,
a.er-mc-toggle-question.focus,
a.er-mc-toggle-question.active:hover{
  background-color: #fff;
  border-color: #e4e5e7;
  color: #6a6c6f;
}

.er-mc-action-bar {
  position: fixed;
  z-index: 1000;
  bottom: 0px;
  margin: 0;
}

.er-mc-action-bar .container {
    width: calc(100vw - 280px + 4px);
    width: calc(var(--body-width) - 280px + 4px);
    margin: 0;
    padding: 1rem 1rem 1rem 2.5rem;
    background-color: white;
    border: 1px solid #e4e5e7;
}

.footer{
  display: none !important;
}

@media print {
  body {
    text-align: justify;
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
  }
  p{
    text-align: justify;
    padding: 0px;
    margin: 0px;
  }

  .er-mc-answer,
  .er-mc-question-title{
    margin-bottom: 5px;
  }

}
    </style>
    </head>
    <body>
        <div class="er-mc-body" >
            <div class="er-mc-date">February 28, 2023</div>
            <br/>
            <div class="er-mc-attorney-masterhead">Jeffrey M. Schwartz (254916)<br />
                Schwartz Law, PC<br />
                2343 30th Street<br />
                Santa Monica, CA 90405<br />
                9493104548<br />
                jeff@jeffschwartzlaw.com
            </div>
            <div class="er-mc-subject">Re: Clark v US Framing 1, Case No. 20STCV11392a</div>
            <div class="er-mc-intro">Dear Jeffrey,<br />
                <br />
                This letter shall serve as a good faith attempt to meet and confer, under the Code of Civil Procedure, regarding your client responses to our Requests for Production of Documents [Set Two], served on July 07, 2020.
            </div>
            <div class="er-mc-responses">
                <div class="er-mc-response-question-number">
                    <div class="heading">No. 10</div>
                </div>
                <div class="er-mc-response-question">
                    <div class="er-mc-response-main-question">
                        <div class="heading">Request:</div>
                        <p class="er-mc-question-title">
                            All DOCUMENTS RELATED TO YOUR bases for classifying CLARK as an independent contractor rather than an employee.
                        </p>
                    </div>
                    <div class="er-mc-response-main-question-answer">
                        <div class="heading">Response:</div>
                        <p class="er-mc-answer">
                            This is protected Attorney Work Product. Objection, ambiguous.
                        </p>
                    </div>
                    <div class="er-mc-meet-confer">
                        <div class="heading">Reply:</div>
                        <div class="er-mc-meet-confer-body" id="er-mc-text-3511" >
                            An objection based upon the Attorney Client privilege and/or Work Product requires the respondent to "provide sufficient factual information for other parties to evaluate the merits of that claim, including, if necessary, a privilege log." Code Civ.Proc., § 2031.240(c)(1).
                        </div>
                    </div>
                </div>
            </div>
            <div class="er-mc-conclusion">
                <div class="heading">Demand to Agree to Provide Complete Answers Without Further Objections</div>
                <div class="er-mc-conclusion-body">Your unmeritorious objections and refusals to produce responsive, unprivileged documents constitute a “misuse of the discovery process.” Code Civ. Proc., § 2023.010. This is grounds for monetary sanctions including reasonable expenses and attorney’s fees. Code Civ. Proc., § 2023.030(a).<br />
                    <br />
                    Please notify me by 5:00 p.m. on Tuesday, March 07, 2023 if you will provide full and complete responses to these discovery requests. If so, we can discuss a reasonable time for your supplemental responses along with a commensurate extension of the deadline for a Motion to Compel, if necessary. Please note that you have waived any objections not made in your initial response to the subject discovery and therefore, no new objections may be interposed at this time. Weil & Brown, Cal. Practice Guide: Civil Procedure Before Trial (The Rutter Group 2019) ¶ 8:1476.1, citing Stadish v. Superior Court (1999) 71 Cal.App.4th 1130, 1141.<br />
                    <br />
                    If you do not timely agree to provide full and complete responses, I will be forced to file a Motion to Compel. I hope that will not be necessary.
                </div>
            </div>
            <div class="er-mc-signature">Sincerely,<br />
                <br />
                Schwartz Law, PC<br />
                <br />
                s/______________________<br />
                Jeffrey M. Schwartz<br />
                Signed Electronically<br />
                Cal. Rules of Court, rule 2.257
            </div>
        </div>
    </body>
</html>
EOD;

//Merging of the existing PDF pages to the final PDF
$pageCount = $pdf->setSourceFile('Letterhead-1.pdf');

$pdf->SetFont('helvetica','',10);

for ($i = 1; $i <= $pageCount; $i++) {

    // import our page
    $templateId = $pdf->importPage($i);
    
    $size = $pdf->getTemplateSize($templateId);
    //print_r($size);exit;
    
    // Add our new page
    $pdf->AddPage();

    $pdf->useTemplate($templateId);

    //$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    // output the HTML content
    //$pdf->writeHTML($html, true, false, true, false, '');
    $pdf->writeHTML($html, true, 0, true, true);

    $pdf->Ln();
}

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_065.pdf', 'I');