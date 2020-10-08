<html>
  <head>
    <link rel="stylesheet" href="{$ASSETS_URL}sections/meet_confer.css" />
  </head>
  <body>
    <div class="er-mc-body" >
      <div class="er-mc-masterhead">{$mc.masterhead|nl2br}</div>

      {if $mc.served_at}
        <div class="er-mc-date">{date('F j, Y', strtotime($mc.served_at))}</div>
      {else}
        <div class="er-mc-date">{date('F j, Y')}</div>
      {/if}

      <br/>

      <div class="er-mc-attorney-masterhead">{$mc.attorney_masterhead|nl2br}</div>
      <div class="er-mc-subject">Re: {$mc.subject}</div>

      <div class="er-mc-intro">{$mc.intro|nl2br}</div>

      <div class="er-mc-responses">
        {foreach item=question from=$questions key=questionNumber}
          {assign var="visible" value={$mc.arguments[$question.question_id]}}
          {if $visible}
            <div class="er-mc-response-question-number">
              <div class="heading">No. {$questionNumber}</div>
            </div>
            <div class="er-mc-response-question">
              <div class="er-mc-response-main-question">
                <div class="heading">Request:</div>
                <p class="er-mc-question-title">
                  {$question.question_title|nl2br}
                </p>
              </div>
              <div class="er-mc-response-main-question-answer">
                <div class="heading">Response:</div>
                {if $question.objection}
                  <p class="er-mc-answer">
                    {$question.objection|nl2br}
                  </p>
                {else}
                  <p class="er-mc-answer">
                    {if trim($question.final_response)}
                      {$question.final_response|nl2br}
                    {else}
                      {$question.answer|default: 'Not provided'|nl2br}
                    {/if}
                  </p>
                  {* DO NOT SHOW SUBQUESTIONS IF final_response IS SET *}
                  {if not trim($question.final_response)}
                    {foreach item=subQuestion from=$question.sub_questions}
                      <p class="er-mc-response-sub-question">
                        {if $subQuestion.sub_part}{$subQuestion.sub_part}) {/if}{$subQuestion.question_title}
                      </p>
                      <p class="er-mc-response-sub-question-answer">
                        {$subQuestion.answer|nl2br}
                      </p>
                    {/foreach}
                  {/if}
                {/if}          
              </div>
              <div class="er-mc-meet-confer">
                <div class="heading">Reply:</div>
                <div class="er-mc-meet-confer-body" id="er-mc-text-{$question.question_id}" >
                  {$mc.arguments[$question.question_id].body|nl2br}
                </div>
              </div>
            </div>
          {/if}
        {/foreach}
      </div>

      <div class="er-mc-conclusion">
        <div class="heading">Demand to Agree to Provide Complete Answers Without Further Objections</div>
        <div class="er-mc-conclusion-body">{$mc.conclusion|nl2br}</div>
      </div>

      <div class="er-mc-signature">{$mc.signature|nl2br}</div>
    </div>
  </body>
</html>