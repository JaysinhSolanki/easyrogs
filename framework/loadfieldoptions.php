<?php
@session_start();

$id	 =	$_GET['id'];
$fieldoptions=$_SESSION['fieldoptions'];
$_SESSION['fieldoptions']=$fieldoptions+1;
$fieldoptions=$_SESSION['fieldoptions'];
?>
<div class="" id="deleteoptions<?php echo $id.$fieldoptions; ?>">
		<div class="col-sm-3 col-sm-offset-2">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable"   name="fieldlabeloptions[<?php echo $id; ?>][]" id="fieldlabeloptions<?php echo $id;?>" value="" />
		</div>
        <div class="col-sm-2">
			<input type="text" placeholder="Value" class="form-control m-b fieldlable"  name="fieldvalueoptions[<?php echo $id; ?>][]" id="fieldvalueoptions<?php echo $id;?>" value="" />
		</div>
		<a href="javascript:;" onclick="deletefieldoption(<?php echo $id.$fieldoptions; ?>)" title="Delete field" style="font-size:20px; text-align:left;" class="col-sm-5 fieldoptions">×</a>	
<span class="col-sm-12"></span>
</div>


