<?php
session_start();
$rp_uid				=	$_REQUEST['rp_uid'];
$doctype			=	@$_REQUEST['doctype'];

$olddocuments		=	@$_SESSION['documents'][$rp_uid];
//echo "<pre>";
//print_r($olddocuments);
//echo "</pre>";
//exit;
?>
<br />
<table class="table table-bordered ">
    <tr>
        <th>Document</th>
        <!--<th>Purpose</th>-->
        <th>Status</th>
        <th>Action</th>
    </tr>
<?php
if(!empty($olddocuments))
{
	$i=1;
	foreach($olddocuments as $key =>  $data)
	{
		if(sizeof($data) > 0)
		{
			$doc_purpose	=	$data['doc_purpose'];
			$doc_name		=	$data['doc_name'];
			$doc_path		=	$data['doc_path'];
			$status			=	$data['status'];
			
			?>
			<tr id="row_"<?php echo $key; ?>>
				<td><?php echo $doc_name ?></td>
				<?php /*?><td><?php echo $doc_purpose ?></td><?php */?>
				<td><?php if($status == 1){echo "Saved";}else{echo "Pending";} ?></td>
				<td>
                	<a href="<?php echo $doc_path; ?>" download target="_blank" title="Download document"><i class="fa fa-download fa-2x" style="color:#063"></a></i>
                   <?php
                    if($doctype == 1)
                    {
                    ?>
                    	 <a href="javascript:;" onclick="deleteDoc(<?php echo $key; ?>,'<?php echo $rp_uid	?>')" title="Delete document"><i class="fa fa-trash fa-2x" style="color:#F00"></a></i>
                    <?php
                    }
                    ?>
                </td>
			</tr>
			<?php		
		}
	}
}
else
{
	?>
    <tr>
    	<td colspan="5">
        <div class="alert alert-danger text-center" role="alert">
        	No document found.
        </div>
        </td>
    </tr>
    <?php
}
?>
</table>
<?php
if(!empty($olddocuments) && $doctype == 1)
{
?>
	<p><span style="color:red">*</span>Pending documents will saved on the click of save or serve button.</p>
<?php
}
?>
<br />