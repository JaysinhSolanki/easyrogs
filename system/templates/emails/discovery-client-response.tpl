{assign var="overwriteSalutation" value=true}
{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$name},</p>
  <br/>
	Please click the button below to respond to <strong>{$discoveryName}</strong> in your case.
  {if $notes}
    <h4 style="display: block !important; text-align: center;">Instructions</h4>
    <div style="white-space: pre-wrap; margin: -1em 0 0; border: 1px solid black; padding: 0.5em;" class="notes-for-client">{$notes}</div>
  {else}
  {/if}
  <br/>
	<p>
    Feel free to email me at <a href="mailto:{$senderEmail}">{$senderEmail}</a>
    {if $senderPhone} or call <a href="tel:{$senderPhone}">{$senderPhone}</a>{/if} if you have any questions.
  </p>
  
	<b>___________</b>
  <br/>
  <br/> 
	{$masterhead|nl2br}
{/block}