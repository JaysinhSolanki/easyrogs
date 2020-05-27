{assign var="overwriteSalutation" value=true}
{assign var="overwriteAction" value=true}
{block name=action}
  <div class="cta">
    <a href="{$actionUrl}" style="background-color: #187204; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
      Join
    </a> &nbsp;&nbsp;
    <a href="{ROOTURL}system/application/demo.mp4" style="background-color: #3498db; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
      Watch Demo
    </a>
  </div>
{/block}

{extends file="emails/layout.tpl"}

{block name=body}
  Counsel,
  <br/>
  <br/>
  <p> You are hereby served with {$propoundingName}'s {$discoveryName} (attached).</p>
	<p> If you're not already using <i>EasyRogs</i>, click the button below for a complimentary membership. To prevent the spread of the Corona virus within the legal community, EasyRogs is free through May 31, 2020.</p>
  <p><i>EasyRogs</i> makes Discovery Easy, Saves Time & Money, and Protects our Health.</p>
  <br />
	<b>___________</b>
  <br/>
  <br /> 
	{$masterhead|nl2br}
{/block}
