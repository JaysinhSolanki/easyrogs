{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$requestorName},</p>
  <br/>
  <p>Your request to join <b>{$caseName}</b> has been denied by {$actionUserName}.</p>
{/block}