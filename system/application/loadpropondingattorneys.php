<?php
session_start();
require_once("adminsecurity.php");
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
$case_id		=	$_GET['case_id'];
$client_id		=	$_GET['client_id'];
$selected_id	=	@$_GET['selected_id'];
$where			=	"";		
$attorneyDetails	=	$AdminDAO->getrows("attorney,client_attorney",
														"attorney.*", 
														"attorney.id = client_attorney.attorney_id AND 
														client_id = :client_id", 
														array(":client_id"=>$client_id), 
														"attorney_name", "ASC");
?>  
<select  name="proponding_attorney" id="proponding_attorney"  class="form-control m-b">                          	
<?php
foreach($attorneyDetails as $data)
{
	?>
	<option 
	<?php 
	if(($data['id']==$selected_id))
	{
		echo "selected";
	}
	?>
	value="<?php echo $data['id']?>"><?php echo $data['attorney_name']." (". $data['attorney_email'].")";?></option>
	<?php
}
?>
</select>