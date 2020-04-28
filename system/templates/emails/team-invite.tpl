{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$name},</p>
  <br/>
  {$senderName} &lt;<a href="mailto:{$senderEmail}">{$senderEmail}</a>&gt; from {$senderFirm} has invited you to join its team in EasyRogs.
  <br/>
  <br/>
  Click the button below to do so. Membership is complimentary.
{/block}