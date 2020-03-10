<?php
require_once("adminsecurity.php");
$case_attorney			=	$_REQUEST['case_attorney'];
if(is_numeric($case_attorney))
{
	$attorneyDetails		=	$AdminDAO->getrows("attorney","*", "id = :case_attorney", array("case_attorney"=>$case_attorney));
	$case_attorney_email	=	$attorneyDetails[0]['attorney_email'];
}
else
{
	$case_attorney_email	=	$case_attorney;
}
$getAttorneyDetails		=	$AdminDAO->getrows('system_addressbook',"*","email = :email",array('email'=>$case_attorney_email));

if(!empty($getAttorneyDetails))
{
	$getAttorneyDetail		=	$getAttorneyDetails[0];
	$atorny_name			=	$getAttorneyDetail['firstname']." ".$getAttorneyDetail['middlename']." ".$getAttorneyDetail['lastname'];
	$atorny_email			=	$getAttorneyDetail['email'];
	$atorny_address			=	$getAttorneyDetail['address'];
	$atorny_city			=	$getAttorneyDetail['cityname'];
	$atorny_zip				=	$getAttorneyDetail['zip'];
	$atorny_street			=	$getAttorneyDetail['street'];
	$atorny_phone			=	$getAttorneyDetail['phone'];
	$fkstateid				=	$getAttorneyDetail['fkstateid'];
	$atorny_firm			=	$getAttorneyDetail['companyname'];
	$attorney_info			=	$getAttorneyDetail['attorney_info'];
	$barnumber				=	$getAttorneyDetail['barnumber'];
		
	$getState				=	$AdminDAO->getrows("system_state","*","pkstateid = :id",array(":id"=>$fkstateid));
	$atorny_state			=	$getState[0]['statename'];
	$atorny_state_short		=	$getState[0]['statecode'];	
}
else if(!empty($attorneyDetails))
{
	$attorneyDetail			=	$attorneyDetails[0];
	$atorny_name			=	$attorneyDetail['attorney_name'];
	$atorny_email			=	$attorneyDetail['attorney_email'];
	
	$atorny_address			=	"";
	$atorny_city			=	"";
	$barnumber				=	"";
	$atorny_zip				=	"";
	$atorny_street			=	"";
	$atorny_phone			=	"";
	$fkstateid				=	"";
	$atorny_firm			=	"";
	$attorney_info			=	"";
	$atorny_state			=	"";
	$atorny_state_short		=	"";
}


/*$atorny_name			=	"Name ";
$atorny_email			=	"email@g.com";
$barnumber				=	"1234";
$atorny_address			=	"address";
$atorny_city			=	"city";
$atorny_zip				=	"0000";
$atorny_street			=	"Street";
$atorny_phone			=	"phone";
$fkstateid				=	"";
$atorny_firm			=	"firm";
$attorney_info			=	"info";
$atorny_state			=	"state";
$atorny_state_short		=	"ST";*/
$headText	=	"";
if($atorny_name != "" || $barnumber != "")
{
	$headText	.=	"<NL>";
	if($atorny_name != "")
	{
		$headText	.=	"$atorny_name ";	
	}
	if($barnumber != "")
	{
		$headText	.=	"($barnumber)";		
	}
}
if($atorny_firm != "")
{
	if($atorny_name != "")
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_firm";
}
if($atorny_address != "")
{
	if($atorny_name != "" || $atorny_firm != "")
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_address";
}
if($atorny_street != "")
{
	if($atorny_address != "")
	{
		$headText	.=	", ";
	}
	else
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_street";
}
if($atorny_city != "")
{
	if($atorny_street != "" || $atorny_address != "")
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_city";
}
if($atorny_state_short != "")
{
	if($atorny_city != "")
	{
		$headText	.=	", ";
	}
	else if($atorny_address != "" || $atorny_firm != "" || $atorny_name != "")
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_state_short $atorny_zip";
}
if($atorny_phone != "")
{
	if($atorny_state_short != "" || $atorny_city != "" || $atorny_address != "" || $atorny_firm != "" || $atorny_name != "")
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_phone";
}
if($atorny_email != "")
{
	if(substr($headText, -4) != '<NL>')
	{
		$headText	.=	"<NL>";
	}
	$headText	.=	"$atorny_email";
}
 
$headText	=	str_replace("<NL>","\n",$headText)
?>
<textarea style="width: 383px; height: 135px;resize: none;" placeholder="Masthead " class="form-control m-b"  name="masterhead" id="masterhead"><?php echo html_entity_decode($headText); ?></textarea>
<?php 

//echo $headText;
//echo "<br><br><br><br><br>";
//echo "<br>$atorny_name<br>$atorny_firm<br>$atorny_address, $atorny_street<br>$atorny_city, $atorny_state_short $atorny_zip<br>$atorny_phone<br>$atorny_email";
