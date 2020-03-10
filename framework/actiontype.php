<?php
require_once("adminsecurity.php");
$id		=	$_REQUEST['id'];
if($id!=-1)
{
	$actiontypes		=	$AdminDAO->getrows("system_actiontype","pkactiontypeid,actiontypelabel,actioncode","pkactiontypeid='$id'");
	$pkactiontypeid		=	$actiontypes[0]['pkactiontypeid'];
	$actiontypelabel	=	$actiontypes[0]['actiontypelabel'];
	$actioncode			=	$actiontypes[0]['actioncode'];
	
}
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('screenname').focus();				 
});
function addscreen(id)
{
	options	=	{	
					url : 'actiontypeaction.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#screenfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Screen has been saved.',0,5000);
		jQuery('#maindiv').load('schools.php');
		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            <?php 
			if($id!="-1")
				{ echo "Edit Action Type";}
				else
				{ echo "Add Action Type ".$actiontypelabel;}	
			?>	
        </div>
        <div class="panel-body">

		<form id="screenfrm" method="post" action="insertscreen.php?id=-1" class="form" >

	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="pkactiontypeid" id="pkactiontypeid" value="<?php echo $param?>" />
	
    <div class="form-group">
		<label class="col-sm-2 control-label">Action Type Label <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
    	<input type="text" placeholder="Action Type Label" class="form-control m-b"  name="actiontypelabel" id="actiontypelabel" value="<?php echo $actiontypelabel; ?>" onKeyDown="javascript:if(event.keycode==13){actiontype(); return false;}">
    </div>
	</div>
    
    <div class="form-group">
		<label class="col-sm-2 control-label">Action Code</label>
	<div class="col-sm-10">
    	<textarea placeholder="Action Code" id="actioncode" name="actioncode" class="form-control m-b"><?php echo $actioncode;?></textarea>
    </div>
	</div>
	    
    
    <div class="form-group" >                 
    	<div class="col-sm-10">
    		<button type="button" class="btn btn-success" onclick="addscreen(-1);"> <i class='icon-ok bigger-110'></i>
    			<?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    		</button>
    		<a href="javascript:void(0);" onclick="hidediv('screenfrmdiv');" class="btn btn-danger"> <i class='icon-undo bigger-110'></i> Cancel </a> 
    	</div><!--/row--><!--/row-->
    </div><!--/.span-->
</form>

		</div>
	</div>
</div>
</div>