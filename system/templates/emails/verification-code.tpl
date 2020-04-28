{extends file="emails/layout.tpl"}

{block name=body}
  <p>Greetings,</p>
  <br/>
  Your verification code is:
  <br/>
  <br/>
  <div style="padding: 5px 10px 5px 10px; background-color: #eee; font-size: 20px; font-weight: bold; display: inline-block">{$code}</div>
{/block}