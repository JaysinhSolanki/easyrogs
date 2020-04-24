{assign var="overwriteAction" value=true}
{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$recipientName},</p>
  <br/>
  <b>{$requestorName}</b> &lt;<a href="mailto: {$requestorEmail}">{$requestorEmail}</a>&gt; from {$requestorFirm} has asked to join <b>{$caseName}</b>.
  <br/>
  <br/>
  You can use the buttons below to grant or deny the request.
{/block}

{block name=action}
  <a href="{$grantUrl}" style="background-color: #187204; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
    Grant
  </a>
  <a href="{$denyUrl}" style="background-color: red; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
    Deny
  </a>
{/block}