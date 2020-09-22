<html>
  <head>
    <link rel="stylesheet" href="{$ASSETS_URL}sections/meet_confer.css" />
  </head>
  <body>
    <div class="er-mc-body" >
      <div class="er-mc-masterhead">{$mc.masterhead|nl2br}</div>

      {if $mc.served_at}
        <div class="er-mc-date">{date('F d, Y', strtotime($mc.served_at))}</div>
      {else}
        <div class="er-mc-date">{date('F d, Y')}</div>
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
                <div class="heading">Reply:</div>
                <div class="er-mc-meet-confer-body" id="er-mc-text-{$question.question_id}" >
                  {$mc.arguments[$question.question_id].body}
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