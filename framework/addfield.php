<?php
require_once("adminsecurity.php");
$id		=	$_REQUEST['id'];
if($id!=-1)
{
	$fields		=	$AdminDAO->getrows("system_field","*","pkfieldid='$id'");
	$fieldlabel	=	$fields[0]['fieldlabel'];
	$fieldname	=	$fields[0]['fieldname'];
}
$param	=	$_GET['param'];
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('fieldname').focus();				 
});
function addfield(id)
{
	options	=	{	
					url : 'insertfield.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#fieldfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Field has been saved.',0,5000);
		jQuery('#sugrid').load('managefields.php?id=<?php echo $param?>');
		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<script src="js/jquery.form.js"></script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="fieldfrmdiv" style="display: block;">
   <form id="fieldfrm" action="insertfield.php?id=-1" class="form form-horizontal" >
    <div class="page-content">
    <div class="page-header position-relative">
        <h1>
            <?php 
                  if($id!="-1")
                { echo "Edit Field";}
                else
                { echo "Add Field";}	
            ?>
            <small>
                <i class="icon-double-angle-right"></i>
			<?php
            if($id > 0)
            { 
                echo $fieldname;
            }
            ?>
            </small>
        </h1>
    </div><!--/.page-header-->
    <div class="row-fluid">
      <div class="span12">
        <!--PAGE CONTENT BEGINS-->
            <div style="float:right">
                <span class="buttons">
                    <button type="button" class="btn  btn-success" onclick="addfield(-1);"><i class='icon-ok bigger-110'></i> 
                        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                    </button>
                    <a href="javascript:void(0);" onclick="hidediv('fieldfrmdiv');" class="btn  btn-danger"><i class='icon-undo bigger-110'></i> Cancel </a>
                </span>
            </div>
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
            <input type="hidden" name="screenid" id="screenid" value="<?php echo $param?>" />
    
            <div class="control-group">
            	<label class="control-label" for="form-field-1">Field Name</label>
                <div class="controls">
                    <input name="fieldname" id="fieldname" class="text" value="<?php echo $fieldname; ?>" onKeyDown="javascript:if(event.keycode==13){addfield(); return false;}" type="text" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="form-field-1">Field Label</label>
                <div class="controls">
                    <input type="text" class="text" value="<?php echo $fieldlabel;?>" name="fieldlabel" id="fieldlabel" onkeydown="javascript:if(event.keycode==13){addfield(); return false;}"  />
                </div>
            </div>
        <div class="space-4"></div>
        <div class="form-actions" style="text-align:left">
              <button type="button" class="btn btn-success" onclick="addfield(-1);"><i class='icon-ok bigger-110'></i> 
              <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
              </button>
              <a href="javascript:void(0);" onclick="hidediv('fieldfrmdiv');" class="btn btn-danger"><i class='icon-undo bigger-110'></i> Cancel </a> </div><!--/row--><!--/row-->
              </div><!--/.span-->
            </div><!--/.row-fluid-->
        </div>
</form>
</div>