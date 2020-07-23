{extends file="emails/layout.tpl"}

{block name=body}
	<br/>
  <p>Greetings {$name},</p>
  <strong>{$clientName} has returned {$responseName}</strong>
{/block}