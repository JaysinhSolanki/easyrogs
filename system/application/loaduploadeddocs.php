<?php
session_start();
$rp_uid  = $_REQUEST['rp_uid'];
$doctype = @$_REQUEST['doctype'];

$olddocuments = @$_SESSION['documents'][$rp_uid];
?>
	<br />
	<table class="table table-bordered ">
		<tr>
			<th>Document</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
<?php
    if( !empty( $olddocuments ) ) {
        $i = 1;
        foreach( $olddocuments as $key => $data ) {
            if( sizeof( $data ) ) {
                $doc_purpose = $data['doc_purpose'];
                $doc_name    = $data['doc_name'];
                $doc_path    = $data['doc_path'];
                $status      = $data['status'];
?>
                <tr id="row_"<?= $key ?>>
                    <td><?= $doc_name ?></td>
                    <td><?= ( $status == 1 ) ? "Saved" : "Pending" ?></td>
                    <td>
                        <a href="<?= $doc_path ?>" download target="_blank" title="Download document">
							<i class="fa fa-download fa-2x" style="color:#063" />
						</a>
<?php
                        if( $doctype == 1 ) {
?>
                            <a href="javascript:;" onclick="deleteDoc(<?= $key ?>,'<?= $rp_uid ?>')" title="Delete document">
								<i class="fa fa-trash fa-2x" style="color:#F00" />
							</a>
<?php
                        }
?>
                    </td>
                </tr>
<?php
            }
        }
    } else {
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
if( !empty( $olddocuments ) && $doctype == 1 ) {
?>
    <p><span style="color:red">*</span> Pending documents will be saved with your responses.</p>
<?php
}
?>
<br />
