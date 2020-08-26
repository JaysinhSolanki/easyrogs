{assign var="overwriteAction" value=true}
{block name=action}
  <div class="cta">
    <a href="{$actionUrl}" style="background-color: #187204; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
      Join
    </a> &nbsp; &nbsp;
    <a href="{ROOTURL}system/application/demo.mp4" style="background-color: #3498db; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
      Watch Demo
    </a>
  </div>
{/block}

{extends file="emails/layout.tpl"}

{block name=body}
  <p>Dear {$name},</p>
  <br/>
  <p>{$senderName} &lt;<a href="mailto:{$senderEmail}">{$senderEmail}</a>&gt; from {$senderFirm} has invited you to join <b>{$caseName}</b> in EasyRogs (membership is complimentary).
  Click a button below to accept or watch a short demonstration.</p>
  <p><i>EasyRogs</i> makes Discovery Easy, Saves Time & Money, and Protects our Health.</p>
{/block}