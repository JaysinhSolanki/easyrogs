{assign var="overwriteSalutation" value=true}
{extends file="emails/layout.tpl"}

{block name=body}
  Dear {$name},
  <br/>
  <br/>
  <p> You are hereby served with {$propoundingName}'s {$discoveryName} (attached).</p>
	<p> If you're not already using <i>EasyRogs</i>, click the button below for a complimentary membership. Plus, the Service Charge is waived through May 31, 2020.</p>
  <p><i>EasyRogs</i> makes Discovery Easy, Saves Time & Money, and Protects our Environment.</p>
  <br />
	<b>___________</b>
  <br/>
  <br /> 
	{$masterhead|nl2br}
{/block}
