<?php
include_once("../includes/classes/adminsecurity.php");
$random	=	 rand();
?>
<div id="options<?php echo $random; ?>">
    <div class="form-group">
    		<label class="col-sm-2 control-label">Option <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
    	<div class="col-sm-8">
    		<input type="text" placeholder="Attribute Option" class="form-control m-b"  name="option[]" id="option" >
    	</div>
    </div> 
</div>
<div id="addanotheroption<?php echo $random; ?>">    
	<a href="javascript:;"  class="pull-right" onclick="addanotheroption('<?php echo $random; ?>');">Add Another Option</a>
</div>