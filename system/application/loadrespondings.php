<?php
session_start();
require_once("adminsecurity.php");
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
$case_id		=	$_GET['case_id'];
$client_id		=	$_GET['client_id'];
$responding_id	=	@$_GET['selected_id'];
$where			=	"";		
if($client_id != "")
{
	$getTypeDetails	=	$AdminDAO->getrows("clients","*", "id=:id", array(":id"=>$client_id), "client_name", "ASC");
	$client_type	=	$getTypeDetails[0]['client_type'];	
	if($client_type == 'Others')
	{
		$where	=	" client_type NOT IN ('Others') ";
	}
	else
	{
		$where	=	" client_type NOT IN ('Us','Pro per') "; 
	}
}
$clients	=	$AdminDAO->getrows("clients","*", "case_id=:case_id AND {$where}", array(":case_id"=>$case_id), "client_name", "ASC");
?>
<select  name="responding" id="responding"  class="form-control m-b" onchange="setquestionnumber()">                          	
<?php
foreach($clients as $client)
{
	?>
	<option 
	<?php 
	if(($client['id']==$responding_id))
	{
		echo "selected";
	}
	?>
	value="<?php echo $client['id']?>"><?php echo $client['client_name']." (". $client['client_type'].")";?></option>
	<?php
}
?>
</select>