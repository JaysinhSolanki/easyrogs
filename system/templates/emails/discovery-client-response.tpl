{assign var="overwriteSalutation" value=true}
{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$name},</p>
	<br/>
	<strong>Please click the button below to respond to discovery in your case.</strong>
  <br/>
	<p>
    Feel free to email me at <a href="mailto:{$senderEmail}">{$senderEmail}</a>
    {if $senderPhone} or call {$senderPhone}{/if} if you have any questions.
  </p>
  
	<b>___________</b>
  <br/>
  <br/> 
	{$masterhead|nl2br}
{/block}