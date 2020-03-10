<?php
require_once("adminsecurity.php");
$case_id			=	$_POST['case_id'];
$attr_id			=	$_POST['attr_id'];
$selectedClients	=	array();
if($attr_id > 0) //Edit Case
{
	$clientsData	=	$AdminDAO->getrows("clients c,client_attorney ca",
														"c.id,c.client_role", 
														"c.id		=	ca.client_id AND 
														ca.attorney_id 		= 	:attorney_id", 
														array(":attorney_id"=>$attr_id));
	
	foreach($clientsData as $selectedData)
	{
		if($selectedData['client_role'] == 'Plaintiff' || $selectedData['client_role'] == 'Plaintiff and Cross-defendant')
		{
			$activeCat = 'd';
		}
		else
		{
			$activeCat = 'p';
		}
		$selectedClients[]	=	$selectedData['id'];
	}
}

$attorneys			=	$AdminDAO->getrows("clients","*", "case_id = :case_id ", array(":case_id"=>$case_id), "client_name", "ASC"); //AND client_type ='Others' 
//dump($attorneys);
$arrayClients		=	array();
foreach($attorneys as $data)
{
	if($data['client_role'] == 'Plaintiff' || $data['client_role'] == 'Plaintiff and Cross-defendant')
	{
		$key	=	'p';
	}
	else
	{
		$key 	= 'd';
	} 
	$arrayClients[$key][]	=	array("id" => $data['id'], "client_name" => $data['client_name'],"client_type" => $data['client_type'],"client_role" => $data['client_role']);
}

//dump($arrayClients);
$catTypes	=	array('p','d');
?>
<div class="form-group">
<?php 
foreach($catTypes as $catType)
	{
		if($catType == 'p')
		{
			$noRecordMessage = "No Plaintiff client attached.";
			if(sizeof($arrayClients[$catType]) > 0)
			{
				$title  = "Plaintiffs";
			}
			else
			{
				$title  = "Plaintiff";
			}
		}
		else if($catType == 'd')
		{
			$noRecordMessage = "No Defendant client attached.";
			if(sizeof($arrayClients[$catType]) > 0)
			{
				$title  = "Defendants";
			}
			else
			{
				$title  = "Defendant";
			}
		}
		?>
        <label><?php echo $title ?></label>
        <table class='table'>
        <tr><th>Select</th><th>Client</th><th>Role</th></tr>
        <?php
		if(sizeof($arrayClients[$catType]) > 0)
		{
			foreach($arrayClients[$catType] as $client_data)
			{
				?>
				<tr for="client_id_<?php echo $client_data['id']; ?>">
				<td>
				<div style="margin-top:0px; margin-bottom:0px" class="ui checkbox <?php echo $catType ?>-clients-div <?php if($activeCat == $catType){echo 'disabled';} ?>">
					<input type="checkbox" class="<?php echo $catType ?>-clients allclients" value="<?php echo $client_data['id']; ?>" name="client_id[]" id="client_id_<?php echo $client_data['id']; ?>"
                    <?php
					if(in_array($client_data['id'],$selectedClients))
					{
						echo 'checked';
					}
					if($activeCat == $catType){echo ' disabled ';} ?>
                    >
					<label>&nbsp;</label>
				</div>
				</td>
				<td valign="middle"><?php echo $client_data['client_name']; ?></td>
				<td valign="middle">
					<?php echo $client_data['client_role']; ?>
				</td>
                </tr>
				<?php
			}		
		}
		else
		{
			?>
            <tr><td colspan='3'><?php echo $noRecordMessage; ?></td></tr>
            <?php
		}
		?>
        </table>
        <?php
	}
?>
</div>
<script>
function clientsSelection() 
{
    if ($('.p-clients:checked').length > 0) 
    {
        $('.d-clients').prop('disabled', true).prop("checked", false);
		$('.d-clients-div').addClass("disabled");
    }
    else
    {
    	$('.d-clients').prop('disabled', false);
		$('.d-clients-div').removeClass("disabled");
    }
    if ($('.d-clients:checked').length > 0) 
    {
         $('.p-clients').prop('disabled', true).prop("checked", false);
		 $('.p-clients-div').addClass("disabled");
    }
    else
    {
    	$('.p-clients').prop('disabled', false);
		$('.p-clients-div').removeClass("disabled");
    }
}

$('.p-clients,.d-clients').change(function () 
{
    clientsSelection();
});
</script>

