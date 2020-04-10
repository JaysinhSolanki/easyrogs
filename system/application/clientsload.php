<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");
//dump($_SESSION);
$case_id		=	$_REQUEST['case_id'];
$id				=	$_POST['id'];
$addressbookid	=	$_SESSION['addressbookid'];
$caseDetails	=	$AdminDAO->getrows("cases","*", "id = :case_id", array(":case_id"=>$case_id));
$case_owner		=	$caseDetails[0]['attorney_id'];
if($case_owner	 == $addressbookid)
{
	$enable			=	1;
	$disabledClass	=	"";
}
else
{
	$enable			=	0;
	$disabledClass	=	"disabled";
}

$sides = new Side();
$currentSide = $sides->getByUserAndCase($currentUser->id, $case_id);

if(isset($id) && $id>0)
{
	$clients		=	$AdminDAO->getrows("clients","*", "id = :id ", array(":id"=>$id));
	$client = $clients[0];
	$clientSide = $sides->getByClientAndCase($id, $case_id);
	if ( $client['client_type'] != Client::CLIENT_TYPE_PRO_PER) {
		$client['client_type'] = $currentSide['id'] == $clientSide['id'] 
														 ? Client::CLIENT_TYPE_US 
														 : Client::CLIENT_TYPE_OTHER;
	}    
	if ($client['client_type'] === Client::CLIENT_TYPE_OTHER) {
		unset($client['client_email']);
	}
	echo json_encode($client);
}
else
{
	$clients	=	$AdminDAO->getrows("clients","*", "case_id = :case_id ", array(":case_id"=>$case_id), "client_name", "ASC");
	foreach($clients as &$client) {
    if ( $client['client_type'] != Client::CLIENT_TYPE_PRO_PER) {
      $client['client_type'] = $currentSide['id'] == $clientSide['id'] 
                               ? Client::CLIENT_TYPE_US 
                               : Client::CLIENT_TYPE_OTHER;
    }    
		$clientSide = $sides->getByClientAndCase($client['id'], $case_id);
		if ($currentSide['id'] != $clientSide['id']) {
			unset($client['client_email']);
		}
	}
		//dump($clients);
	?>
	<table class="table table-bordered table-hover table-striped" id="table_clients" width="100%">
	<tr>
		<th width="20%">Name</th>
		<th width="20%">Role</th>
		<?php
		if($enable == 1)
		{
		?>
        <th width="20%">Representation</th>
		<?php
		}
		?>
        <th width="10%">Email</th>
		<th width="15%">Attorney</th>
        <?php
		if($enable == 1)
		{
		?>
		<th width="15%">Action</th>
        <?php
		}
		?>
	</tr>
	<?php
	if(sizeof($clients) > 0)
	{
		foreach($clients as $data)
		{
			$client_id			=	$data['id'];
			$attorneyDetails	=	$AdminDAO->getrows("attorney,client_attorney",
										"attorney.*", 
										"attorney.id = client_attorney.attorney_id AND 
										client_id = :client_id AND attorney_email != :owner_email GROUP BY attorney_email", 
										array(":client_id"=>$client_id,":owner_email" => $_SESSION['loggedin_email']), 
										"attorney_name", "ASC");
			$client_attorneys	=	"";
			if(sizeof($attorneyDetails) > 0)
			{
				$client_attorneys	= "";//"<ul style='padding-left: 15px;'>";
				foreach($attorneyDetails as $attorneyDetail)
				{
					$client_attorneys	.=	 "".$attorneyDetail['attorney_name']." (".$attorneyDetail['attorney_email'].") <br>"; 
				}
				//$client_attorneys	.= "</ul>";
			}
			else
			{
				$client_attorneys	=	"-";
			} 
			
			?>
			<tr id="client_<?php echo $data['id']; ?>">
				<td><?php echo $data['client_name']; ?></td>
				<td><?php echo $data['client_role']; ?></td>
				<?php
                if($enable == 1)
                {
                ?>
				<td>
				<?php 
				if($data['client_type'] == "Others")
				{
					echo "Another Attorney";
				}
				else
				{
					echo $data['client_type']; 
				}
				?></td>
				<?php
				}
				?>
                <td>
				<?php 
				
				if( $data['client_email'] != "")
				{
					if($enable == 1 && $data['client_type'] == 'Us') 
					{
						echo $data['client_email'];	
					}
					else
					{
						echo "-";
					}
				}
				else
				{
					echo "-";
				} 
				?>
                </td>
				<td><?php echo $client_attorneys; ?></td>
				<?php
				if($enable == 1)
				{
				?>
                <td>
					<a href="javascript:;" title="Edit" onclick="editCaseClient(<?php echo $data['id']; ?>, <?= $case_id ?>)"><i class="fa fa-edit fa-2x"></i>
						</a>
					<a href="javascript:;" onclick="deleteCaseClient(<?php echo $data['id']; ?>, <?= $case_id ?>)"><i class="fa fa-trash fa-2x" style="color:red"></i></a>
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
				<td colspan="6" align="center">No record found.</td>
			</tr>
		<?php
	}
	?>
    </table>
    <?php
}
?>