<?php
session_start();
require_once("adminsecurity.php");
$fkaddressbookid		=	$_SESSION['addressbookid'];
$case_id				=	$_GET['case_id'];
$getAlreadyselected		=	$AdminDAO->getrows("case_team","GROUP_CONCAT(attorney_id) as attorney_ids", "fkcaseid = :case_id ", array(":case_id"=>$case_id));
$attorney_ids			=	$getAlreadyselected[0]['attorney_ids'];
$$where		=	"";
if($attorney_ids != "")
{
	$where				=	" AND id NOT IN ($attorney_ids) ";	
}
$attorneys				=	$AdminDAO->getrows("attorney","*", "fkaddressbookid = '$fkaddressbookid' AND attorney_type = 1 {$where}", array(), "attorney_name", "ASC");


?>
<table class="table table-bordered table-hover table-striped" id="table_attornys">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th  width="15%">Action</th>
    </tr>
    <?php
	if(sizeof($attorneys) > 0)
	{
		foreach($attorneys as $data)
		{
		?>
		<tr id="myattr_<?php echo $data['id']; ?>">
			<td><?php echo $data['attorney_name']; ?></td>
			<td><?php echo $data['attorney_email']; ?></td>
            <td  width="15%"><a href="javascript:;" class="btn btn-primary" onclick="addMyAttorneyToCase('<?php echo $data['id']; ?>','<?php echo $case_id; ?>')"> Add to case</a></td> 
		</tr>
		<?php
		}
	}
	else
	{
	?>
    	<tr>
        	<td colspan="3" align="center">No record found.</td>
        </tr>
    <?php	
	}
	?>
</table>
