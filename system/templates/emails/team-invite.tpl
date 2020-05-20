{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$name},</p>
  <br/>
  {$senderName} &lt;<a href="mailto:{$senderEmail}">{$senderEmail}</a>&gt; from {$senderFirm} has invited you to join its team in EasyRogs.
  <br/>
  <br/>
  <p>Click the button below to do so. Membership is complimentary.</p>
  <p><i>EasyRogs</i> makes Discovery Easy, Saves Time & Money, and Protects our Health.</p>
{/block}