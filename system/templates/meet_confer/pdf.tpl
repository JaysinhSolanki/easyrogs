    <div class="er-mc-body" >
      {* <div class="er-mc-masterhead">{$mc.masterhead|nl2br}</div> *}
      {* <div class="er-mc-masterhead">{$letterhead|nl2br}</div> *}

      {if $mc.served_at}
        <div class="er-mc-date">{date('F j, Y', strtotime($mc.served_at))}</div>
      {else}
        <div class="er-mc-date">{date('F j, Y')}</div>
      {/if}
      <div class="er-mc-attorney-masterhead">{$mc.attorney_masterhead|nl2br}</div>
      <div class="er-mc-subject"><b>Re: {$mc.subject}</b></div>

      <div class="er-mc-intro">{$mc.intro|nl2br}</div>

      <div class="er-mc-responses">
        {foreach item=question from=$questions key=questionNumber}
          {assign var="visible" value={$mc.arguments[$question.question_id]}}
          {if $visible}<div class="heading number-heading"><b>No. {$questionNumber}</b></div>
            <div class="heading"><b>Request:</b></div>
            <span class="er-mc-question-title">{$question.question_title|nl2br}</span><br>
            <div class="heading"><b>Response:</b></div>
              {if $question.objection}
                <span class="er-mc-answer">{$question.objection|nl2br}</span>
              {else}
                <span class="er-mc-answer">{if trim($question.final_response)}{$question.final_response|nl2br}{else}{$question.answer|default: 'Not provided'|nl2br}{/if}</span>
                {* DO NOT SHOW SUBQUESTIONS IF final_response IS SET *}
                {if not trim($question.final_response)}
                  {foreach item=subQuestion from=$question.sub_questions}
                    <span class="er-mc-response-sub-question">{if $subQuestion.sub_part}{$subQuestion.sub_part}) {/if}{$subQuestion.question_title}</span>
                    <span class="er-mc-response-sub-question-answer">{$subQuestion.answer|nl2br}</span>
                  {/foreach}
                {/if}
              {/if}<br>
              <div class="heading"><b>Reply:</b></div>
              <span class="er-mc-meet-confer-body" id="er-mc-text-{$question.question_id}" >{$mc.arguments[$question.question_id].body|nl2br}</span>
            </div>
          {/if}
        {/foreach}
      </div>

      <div class="er-mc-conclusion">
        <div class="heading"><b>Demand to Agree to Provide Complete Answers Without Further Objections</b></div>
        <div class="er-mc-conclusion-body">{$mc.conclusion|nl2br}</div>
      </div>

      <div class="er-mc-signature">{$mc.signature|nl2br}</div>
    </div>
