{extends file="layouts/embedded.tpl"}

{assign var="editable"   value={!$mc.served}}
{assign var="title"      value="Meet & Confer Letter"}
{assign var="subtitle"   value="Response to $discoveryTitle"}
{assign var="caseTitle"  value={$side.case_title}}
{assign var="caseNumber" value={$side.case_number}}
{assign var="subject"    value={$mc.subject|default: "$caseTitle, Case No. $caseNumber"}}

{block name=panel_body}
  <form class="er-mc-body {if $editable}editable{/if}" >
    <input type="hidden" name="response_id" value="{$response.id}" />
    <input type="hidden" name="id" value="{$mc.id}" />

    {if $editable}
      <textarea name="masterhead" class="er-mc-masterhead">{$masterhead}</textarea>
    {else}
      <div class="er-mc-masterhead">{$masterhead|nl2br}</div>
    {/if}

    {if $mc.served_at}
      <div class="er-mc-date">{date('F d, Y', strtotime($mc.served_at))}</div>
    {else}
      <div class="er-mc-date">{date('F d, Y')}</div>
    {/if}
    
    {if $editable}
      <textarea name="attorney_masterhead" class="er-mc-attorney-masterhead" placeholder="Recipient letterhead...">{$attorney_masterhead}</textarea>
    {else}
      <div class="er-mc-attorney-masterhead">{$attorney_masterhead|nl2br}</div>
    {/if}

    <div class="er-mc-subject">Re: {$subject}</div>
    <input type="hidden" name="subject" value="{$subject}"/>
    
    {if $editable}
<textarea name="intro" class="er-mc-intro">{if $mc}{trim($mc.intro)}{else}
Dear {$opposingAttorney.firstname|default: 'counsel'},

This letter shall serve as a good faith attempt to meet and confer, under the Code of Civil Procedure, regarding your client responses to our {$discoveryTitle}, served on {$discovery.served}.
{/if}
</textarea>
    {else}
      <div class="er-mc-intro">{trim($mc.intro)|nl2br}</div>
    {/if}

    <div class="er-mc-responses">
      {foreach item=question from=$questions key=questionNumber}
        <div class="er-mc-question-wrapper">
          {assign var="visible" value={!$mc || $mc.arguments[$question.question_id]}}
          {if $editable}
            <a class="pull-right er-mc-toggle-question btn btn-xs btn-default {if $visible}active{/if}" data-toggle="collapse" data-question-id="{$question.question_id}" data-target="#question-{$question.question_id}">Reply</a>
          {/if}
          {if $editable || $visible}
            <div class="er-mc-response-question-number {if $editable}er-mc-toggle-question{/if}" {if $editable}data-target="#question-{$question.question_id}" data-toggle="collapse"{/if}>
              <div class="heading">No. {$questionNumber}</div>
            </div>
          {/if}
          <div class="er-mc-response-question collapse {if $visible}in{else}out{/if}" aria-collapsed="true" id="question-{$question.question_id}">
            <div class="er-mc-response-main-question">
              <div class="heading">Request:</div>
              <p class="er-mc-question-title">
                {$question.question_title}
              </p>
            </div>
            <div class="er-mc-response-main-question-answer">
              <div class="heading">Response:</div>
              {if $question.objection}
                <p class="er-mc-answer">
                  {$question.objection}
                </p>
              {else}
                <p class="er-mc-answer">
                  {$question.answer|default: 'Not provided'}
                </p>            
                {foreach item=subQuestion from=$question.sub_questions}
                  <p class="er-mc-response-sub-question">
                    {$subQuestion.sub_part}) {$subQuestion.question_title}
                  </p>
                  <p class="er-mc-response-sub-question-answer">
                    {$subQuestion.answer}
                  </p>
                {/foreach}
              {/if}          
            </div>
            <div class="er-mc-meet-confer">
              <div class="heading">M&C:</div>
              {if $editable}
                <textarea name="arguments[{$question.question_id}]" {if !$visible}disabled{/if} class="er-mc-meet-confer-body" id="er-mc-text-{$question.question_id}" placeholder="Enter your Argument...">{trim($mc.arguments[$question.question_id].body)}</textarea>
              {else}  
                <div class="er-mc-meet-confer-body" id="er-mc-text-{$question.question_id}" >
                  {trim($mc.arguments[$question.question_id].body)}
                </div>
              {/if}
            </div>
          </div>
        </div>
      {/foreach}
    </div>

    <div class="er-mc-conclusion">
      <div class="heading">Demand to Agree to Provide Complete Answers Without Further Objections</div>
      {if $editable}
<textarea name="conclusion" class="er-mc-conclusion-body">{if $mc}{trim($mc.conclusion)}{else}
Your unmeritorious objections and refusals to respond constitute a “misuse of the discovery process.” Code Civ. Proc., § 2023.010. This is grounds for monetary sanctions including reasonable expenses and attorney’s fees. Code Civ. Proc., § 2023.030(a).
{/if}
</textarea>  
      {else}
        <div class="er-mc-conclusion-body">{trim($mc.conclusion)|nl2br}</div>
      {/if}
    </div>
    
    {if $editable}
<textarea name="signature" class="er-mc-signature">{if $mc}{trim($mc.signature)}{else}
Sincerely,

{$attorney.companyname}


s/______________________

{User::getFullName($attorney)}
Signed Electronically
Cal. Rules of Court, rule 2.257{/if}
</textarea>
    {else}
      <div class="er-mc-signature">{trim($mc.signature)|nl2br}</div>
    {/if}

  </form>
{/block}

{block name=main_bottom}
  <div class="row">
    <div class="col-lg-12 er-mc-action-bar">
      <div class="container">
        <a class="er-mc-cancel-button btn btn-sm btn-default">&laquo; Back</a>
        <div class="pull-right">
          {if $editable}
            <button type="button" class="btn btn-success er-mc-save-button"><i class="fa fa-save"></i> Save Draft</button>
            <button type="button" class="btn btn-purple  er-mc-serve-button"><i class="fa fa-share"></i> Serve </button>
          {else}
            <a href="meet-confer-pdf.php?id={$mc.id}" class="btn btn-primary er-mc-download-button"><i class="fa fa-download"></i> Download PDF</a>
          {/if}
        </div>  
      </div>
    </div>  
  </div>  
{/block}

{block name=css_dependencies}
  <link rel="stylesheet" href="{$ASSETS_URL}sections/meet_confer.css" />
{/block}

{block name=js_dependencies}
  <script>
    mcId     = {$mc.id|default: 'null'}
    mcFormId = {$discovery.form_id}
    mcCaseId = {$discovery.case_id}
  </script>
  <script src="{$ASSETS_URL}sections/meet_confer.js"></script>
{/block}