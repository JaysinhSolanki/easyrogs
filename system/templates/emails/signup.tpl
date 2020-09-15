{assign var="overwriteAction" value=true}
{extends file="emails/layout.tpl"}

{block name=body}
  <p>Greetings,</p>
  <br/>
  <strong>Welcome to EasyRogs!</strong>
  <br/>
  <br/>
  Please click the button below to complete the signup process and login to your account.
  <br/>
{/block}


{block name=action}
  <div class="cta">
    <a href="{$signupUrl}" style="background-color: #187204; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
      Complete Signup
    </a>
  </div>
{/block}