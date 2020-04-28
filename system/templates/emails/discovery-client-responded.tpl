{extends file="emails/layout.tpl"}

{block name=body}
  <p>Greetings,</p>
	<br/>
  <strong>{$clientName} has returned {$discoveryName} [Set {$setNumber}]</strong>
{/block}