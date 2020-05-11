<?php
	require_once __DIR__ . '/../bootstrap.php';
	require_once("adminsecurity.php");

	$caseId			=	$_POST['case_id'];
	$userId			=	$_POST['attr_id'];
	$selectedClientIds	=	array();

	if (!$side = $sidesModel->getByUserAndCase($currentUser->id, $caseId)) {
		HttpResponse::unauthorized();
	}

	$clients 		 = $casesModel->getAllClients($caseId);
	$userClients = $userId ? $sidesModel->getUserServiceListClients($side, $userId) : [];

	$activeRole = null;
	foreach($userClients as $client)
	{
		$activeRole 			 	 = Client::roleGeneric($client);
	  $selectedClientIds[] = $client['id'];
	}

	$roleClients = [
		Client::ROLE_PLAINTIFF => [],
		Client::ROLE_DEFENDANT => []
	];
	foreach($clients as $client) {
		$roleClients[Client::roleGeneric($client)][] = $client;
	}

?>

<div class="form-group">
	<?php foreach($roleClients as $role => $clients): ?>
		<label><?=$role?>s</label>
		<?php if ($clients): ?>
			<table class='table'>
        <tr>
					<th>Select</th>
					<th>Client</th>
					<th>Role</th>
				</tr>
				<?php foreach($clients as $client): ?>
					<tr for="client_id_<?= $client['id']; ?>">
						<td>
							<div style="margin-top:0px; margin-bottom:0px" class="ui checkbox <?= $role ?>-clients-div <?= $activeRole == $role ? '' : 'disabled' ?>">
								<input 
									type="checkbox" 
									class="<?= $role ?>-clients allclients" 
									value="<?= $client['id'] ?>" 
									name="client_id[]" 
									id="client_id_<?= $client['id'] ?>"
									<?= in_array($client['id'], $selectedClientIds) ? 'checked' : '' ?>
									<?= !$activeRole || $activeRole == $role ? '' : 'disabled' ?>
								/>
								<label>&nbsp;</label>
							</div>
						</td>
						<td valign="middle"><?= $client['client_name']; ?></td>
						<td valign="middle"><?= $client['client_role']; ?></td>
          </tr>					
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			No <?= $role ?> clients attached.
		<?php endif; ?>
	<?php endforeach; ?>
</div>

<script type="text/javascript">
	$('.Plaintiff-clients,.Defendant-clients').off('change').on('change', () => {
    if ($('.Plaintiff-clients:checked').length > 0) 
    {
      $('.Defendant-clients').prop('disabled', true).prop("checked", false);
			$('.Defendant-clients-div').addClass("disabled");
    }
    else
    {
    	$('.Defendant-clients').prop('disabled', false);
			$('.Defendant-clients-div').removeClass("disabled");
		}
		
    if ($('.Defendant-clients:checked').length > 0) 
    {
      $('.Plaintiff-clients').prop('disabled', true).prop("checked", false);
		  $('.Plaintiff-clients-div').addClass("disabled");
    }
    else
    {
    	$('.Plaintiff-clients').prop('disabled', false);
			$('.Plaintiff-clients-div').removeClass("disabled");
    }
	});
</script>

