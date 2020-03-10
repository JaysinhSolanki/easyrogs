<?php
require_once("adminsecurity.php");
//echo $id			=	$_REQUEST['id'];
$id			=	$_REQUEST['id']; //line added by Ahsan - 06/02/2012 
$fkscreenid	=	$_GET['param'];
if($id!=-1)
{
	$actions			=	$AdminDAO->getrows("system_action","actionlabel,fkactiontypeid,fkscreenid","pkactionid='$id'");
	$actionlabel		=	$actions[0]['actionlabel'];
	$fkscreenid			=	$actions[0]['fkscreenid'];
	$fkactiontypeid		=	$actions[0]['fkactiontypeid'];
	
}
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('actionlabel').focus();				 
});
function addaction(id)
{
	options	=	{	
					url : 'insertaction.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#actionfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Action has been saved.',0,5000);
		jQuery('#sugrid').load('manageactions.php?id='+<?php echo $fkscreenid;?>);
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="actionfrmdiv" style="display: block;"> <br>
<form id="actionfrm"  class="form form-horizontal" >
<div class="page-content">
	<div class="page-header position-relative">
		<h1>
			<?php 
				if($id!="-1")
				{ echo "Edit Action";}
				else
				{ echo "Add Action";}
            ?>
			<small>
				<i class="icon-double-angle-right"></i>
<?php
	if($id > 0)
	{ echo $fieldname;}
	?>
		 	</small>
		</h1>
	</div><!--/.page-header-->
	<div class="row-fluid">
	  <div class="span12">
		<!--PAGE CONTENT BEGINS-->
        <div style="float:right"> <span class="buttons">
        <button type="button" class="btn btn-success" onclick="addaction(-1);"><i class="icon-ok bigger-110"></i>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('actionfrmdiv');" class="btn btn-danger"><i class="icon-undo bigger-110"></i> Cancel </a> </span> </div>
		 <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
     	 <input type="hidden" name="fkscreenid" id="fkscreenid" value="<?php echo $fkscreenid; ?>" />
          <div class="control-group">
			<label class="control-label" for="form-field-1">Action Label</label>
		        <div class="controls">
					<input name="actionlabel" id="actionlabel" class="text" value="<?php echo $actionlabel; ?>" onKeyDown="javascript:if(event.keycode==13){addaction(); return false;}" type="text" >
                </div>
         </div>

         <div class="control-group">
			<label class="control-label" for="form-field-1">Action Type</label>
		        <div class="controls">
					<?php 
					$actiontypes		=	$AdminDAO->getrows("system_actiontype","pkactiontypeid,actiontypelabel");
					?>
                  		<select name="fkactiontypeid" >
					<?php 
					foreach($actiontypes as $actiontype)
					{
					?>                        
                      	  <option value="<?php echo $actiontype['pkactiontypeid'];?>" <?php if($actiontype['pkactiontypeid']==$fkactiontypeid){?> selected="selected" <?php } ?>><?php echo $actiontype['actiontypelabel'];?></option>
                    <?php 
					}
					?>
               	 		</select> 
                </div>
         </div>
          <div class="space-4"></div>
                <div class="form-actions" style="text-align:left">
                  <button type="button" class="btn btn-success" onclick="addaction(-1);"><i class="icon-ok bigger-110"></i>
                  <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                  </button>
                  <a href="javascript:void(0);" onclick="hidediv('actionfrmdiv');" class="btn btn-danger"><i class="icon-undo bigger-110"></i> Cancel </a> </div><!--/row--><!--/row-->
              </div><!--/.span-->
        </div><!--/.row-fluid-->
    </div>
</form>
</div>
<script language="javascript">
	focusfield('actioncode');
</script>