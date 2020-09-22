{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$name},</p>
  <br/>
  We received a request to recover your password. If it wasn't you ignore this email.
  <br/>
  <br/>
  Click the button below to set a new password.
  <br/>
{/block}