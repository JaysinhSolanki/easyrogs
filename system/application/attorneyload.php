<?php
session_start();
require_once("adminsecurity.php");
$fkaddressbookid	=	$_SESSION['addressbookid'];
$loggedin_email		=	$_SESSION['loggedin_email'];
$case_id			=	$_GET['case_id'];
$attorney_type		=	$_GET['attorney_type'];
$caseDetails		=	$AdminDAO->getrows("cases","*", "id = :case_id", array(":case_id"=>$case_id));
$case_owner			=	$caseDetails[0]['attorney_id'];
if(($case_owner	 == $fkaddressbookid) || $attorney_type == 3 || $attorney_type == 1)
{
	$enable			=	1;
	$disabledClass	=	"";
}
else
{
	$enable			=	0;
	$disabledClass	=	"disabled";
}
if($attorney_type == 1)
{
	$attorneys		=	$AdminDAO->getrows("attorney","*", "fkaddressbookid = :fkaddressbookid AND attorney_type = :attorney_type", array(":fkaddressbookid"=>$fkaddressbookid,":attorney_type"=>$attorney_type), "attorney_name", "ASC");
}
else if($attorney_type == 2)
{
/*
	$attorneys			=	$AdminDAO->getrows("attorney",
										"attorney.*", 
										"case_id 		= 	:case_id 	AND 
										attorney_type	=	2 			AND
										attorney_email 	!= 	:owner_email GROUP BY attorney_email", 
										array(":case_id"=>$case_id,":owner_email" => $_SESSION['loggedin_email']),"attorney_name", "ASC");
*/
    $attorneys			=	$AdminDAO->getrows("attorney",
										"attorney.*", 
										"case_id 		= 	:case_id 	AND 
										attorney_type	=	2", 
										array(":case_id"=>$case_id),"attorney_name", "ASC");
}
else if($attorney_type == 3)
{
	$attorneys		=	$AdminDAO->getrows("attorney a ,case_team ct","a.*,ct.id as case_team_id", "ct.attorney_id = a.id AND ct.fkcaseid = :case_id AND ct.is_deleted  = 0 AND a.fkaddressbookid = :fkaddressbookid", array(":case_id"=>$case_id,":fkaddressbookid"=>$fkaddressbookid), "attorney_name", "ASC");
}

if($attorney_type == 3)
{
?>
	<a href="javascript:;" onclick="loadModalCaseTeamFunction(<?php echo $case_id ?>)" class="pull-right btn btn-success btn-small" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Invite New</a>
	<i><b style="color:red">*</b>The invitation will be sent when you click "Save."</i>
<?php
}
?>
<table class="table table-bordered table-hover table-striped" id="table_attornys" >
	<tr>
        <th>Name</th>
        <th>Email</th>
        <?php
        if($attorney_type == 2)
        {
        ?>
            <th>Parties</th>
        <?php
        }
        if($enable == 1)
        {  
        ?>
            <th  width="15%">Action</th> 
        <?php 
        } 
        ?>
	</tr>
	<?php
	if(sizeof($attorneys) > 0)
	{
		foreach($attorneys as $data)
		{
			if($attorney_type == 3)
			{
				$case_team_id	=	$data['case_team_id'];
			}
			else
			{
				$case_team_id	=	0;
			}

			?>
            <tr id="attr_<?php echo $data['id']; ?>">
                <td><?php echo $data['attorney_name']; ?></td>
                <td><?php echo $data['attorney_email']; ?></td>
                <?php
                if($attorney_type == 2)
                {
                    $attorney_id	=	$data['id'];
                    $clientsData	=	$AdminDAO->getrows("clients c,client_attorney ca",
                                                            "c.*", 
                                                            "c.id				=	ca.client_id AND 
                                                            ca.attorney_id 		= 	:attorney_id", 
                                                            array(":attorney_id"=>$attorney_id));
                    
                    ?>
                    <td>
                        <?php 
                        foreach($clientsData as $cData)
                        {
                            echo "".$cData['client_name']."<br>";
                        }
                        ?>
                    </td>
                    <?php
                }
				if($enable == 1)
        		{ 
                ?>
            	<td  width="15%" align="center">
				<?php
                if($attorney_type == 2) //Clients and Service list
                {
                ?>
                	<a href="javascript:;" title="Edit" onclick="loadServiceListModal('<?php echo $case_id; ?>','<?php echo $data['id']; ?>')"><i class="fa fa-edit fa-2x"></i></a>
                <?php
                }
                else if($attorney_type == 1) //Profile Team Members
                {
                ?>
                	<a href="javascript:;" title="Edit" onclick="loadModalCaseTeamFunction('<?php echo $case_id; ?>','<?php echo $data['id']; ?>',1)"><i class="fa fa-edit fa-2x"></i></a>
                <?php
                }
                ?>
                <?php
                if($attorney_type != 3) //Profile Team Members and Clients / Service list
                {
                ?>
                	<a href="javascript:;" title="Delete" onclick="deleteAttorney('<?php echo $data['id']; ?>','<?php echo $attorney_type ?>','<?php echo $case_team_id; ?>','<?php echo $case_id; ?>')"><i class="fa fa-trash fa-2x" style="color:red"></i></a>
                <?php
                }
                else //Case Team
                {
					if($data['attorney_type'] == 1)
					{
						$userteammember	=	1; //User Team Member
					}
					else
					{
						$userteammember	=	2;//Not a user Team Member
					}
                	?>
                	<a href="javascript:;" title="Edit" onclick="loadModalCaseTeamFunction('<?php echo $case_id; ?>','<?php echo $data['id']; ?>','<?php echo $userteammember ?>')"><i class="fa fa-edit fa-2x"></i></a>
                	<a href="javascript:;" title="Delete" onclick="loadmodaldelete('<?php echo $data['id']; ?>','<?php echo $attorney_type ?>','<?php echo $case_team_id; ?>','<?php echo $case_id; ?>','<?php echo $userteammember ?>')"><i class="fa fa-trash fa-2x" style="color:red"></i></a>
                	<?php
                }
                ?>
            </td> 
            	<?php
				}
				?>
			</tr>
			<?php
		}
	}
	else
	{
		?>
		<tr>
			<td colspan="4" align="center">No record found.</td>
		</tr>
		<?php	
    }
    ?>
</table>

