<?php
require_once("adminsecurity.php");
$id		=	$_REQUEST['id'];
if($id!=-1) 
{
	$forms				=	$AdminDAO->getrows("system_label,system_form","formtitle,formtitlehebrew,pklabelid,fieldname,fkformid,label,labelhebrew,sortorder","pkformid='$id' AND pkformid = fkformid");
	$formtitle			=	$forms[0]['formtitle'];
 	$formtitlehebrew 	=	$forms[0]['formtitlehebrew'];//updated...1/16/2015 exit;
}
?>
<script language="javascript">
$(document).ready(function(){
document.getElementById('formtitle').focus(); 				 
});
var newId = (function() {
    var id = 1;
    return function() {
        return id++;
    };
}());
function addanotherfield()
{
	$("#fromfiledclone").clone().appendTo("#tofieldclone").attr('id', newId()).addClass("fieldclass");
}
function addanotheraction()
{
	$("#fromactionclone").clone().appendTo("#toactionclone").attr('id', newId()).addClass("actionclass");
}
function deleteformlabel(e,labelid)
{
	if(confirm("Are you sure to delete?"))
	{ 
	 $(e).parents(".fieldclass").remove();
	 if(labelid>0)
	 {
	 	$.post("deleteformlabel.php?labelid="+labelid);
	 }
	return;
	}
}
function deleteaction(e,actionid)
{
	if(confirm("Are you sure to delete?"))
	{ 
	 $(e).parents(".actionclass").remove();
	 if(actionid>0)
	 {
	 	$.post("deleteaction.php?actionid="+actionid);
	 }
	return;
	}
}
</script>
<script src="js/jquery.form.js"></script> 
<style>
.icon-remove
{
	cursor:pointer;
}
</style>
<div id="loaditemscript"> </div>

<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <?php 
                if($id=='-1')
                {echo "Add Form";}
                else
                {echo "Edit Form >> $screenname";}
    ?>	
            </div>
            <div class="panel-body">
            <form id="formfrm" class="form form-horizontal" >
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
    
            <div class="form-group">
                <label class="col-sm-2 control-label">Form Title (English) </label>
            <div class="col-sm-10">
                <input type="text" placeholder="Form Title (English)" class="form-control m-b"  name="formtitle" id="formtitle" value="<?php echo $formtitle; ?>">
            </div>
            </div>
            
            <?php /*?><div class="form-group">
                <label class="col-sm-2 control-label">Form Title (German) </label>
                <div class="col-sm-10">
                    <input type="text" placeholder="Form Title (German)" class="form-control m-b"  name="formtitlehebrew" id="formtitlehebrew" value="<?php echo $formtitlehebrew; ?>">
                </div>
            </div><?php */?>
            
            
            <h3>Fields</h3>
            
            <span id="tofieldclone">
            
             <?php
            foreach($forms as $form)
            {
                $pklabelid			=	$form['pklabelid'];
                $fieldlabel			=	$form['label'];
                $fieldlabelherbew	=	$form['labelhebrew'];
                $fieldname			=	$form['fieldname'];
                $sortorder			=	$form['sortorder'];
                
            ?>
                <span id="<?php echo $pkfieldid; ?>"  class="fieldclass" ><i class="icon-remove bigger-110" style="float:right; margin-right:550px" onClick="deleteformlabel(this,<?php echo $pklabelid; ?>); return false" ></i>
                    <input name="pklabelid[]" class="text" value="<?php echo $pklabelid; ?>" type="hidden" >
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Field Label(English) </label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="Field Label(English)" class="form-control m-b"  name="fieldlabel[]" value="<?php echo $fieldlabel;?>">
                    </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Field Label(German) </label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="Field Label()" class="form-control m-b"  name="fieldlabelhebrew[]" value="<?php echo $fieldlabelherbew; ?>">
                    </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Field Name </label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="Field Name" class="form-control m-b"  name="fieldname[]" value="<?php echo $fieldname; ?>">
                    </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort Order </label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="Sort Order" class="form-control m-b"  name="sortorder[]" value="<?php echo $sortorder; ?>">
                    </div>
                    </div>
                    
                   <div class="hr-line-dashed"></div>
               </span>
            <?php
            }
            ?>
            </span>
            <span id="fromfiledclone">
                <i class="icon-remove bigger-110" style="float:right; margin-right:550px" onClick="deleteformlabel(this,0); return false" ></i>
                
                <div class="form-group">
                        <label class="col-sm-2 control-label">Field Label(English) </label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="Field Label(English)" class="form-control m-b"  name="fieldlabel[]" value="">
                    </div>
                </div>
                
                
                <div class="form-group">
                        <label class="col-sm-2 control-label">Field Label(German) </label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="Field Label()" class="form-control m-b"  name="fieldlabelhebrew[]" value="">
                    </div>
                </div>
                    
                <div class="form-group">
                    <label class="col-sm-2 control-label">Field Name </label>
                <div class="col-sm-10">
                    <input type="text" placeholder="Field Name" class="form-control m-b"  name="fieldname[]" value="">
                </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Sort Order </label>
                <div class="col-sm-10">
                    <input type="text" placeholder="Sort Order" class="form-control m-b"  name="sortorder[]" value="">
                </div>
                </div>
                
                <div class="hr-line-dashed"></div>
            </span>
            <a href="javascript:;" onclick="addanotherfield();">Add Another</a>
             <div class="hr-line-dashed"></div>
    <?php
        buttons('insertform.php','formfrm','maindiv','main.php?pkscreenid=73',0)
    ?>
                    
                </form>
            </div>
        </div>
    </div>
</div>