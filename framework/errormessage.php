<?php
require_once("adminsecurity.php");
$id	=	$_GET['id'];
if($id)
{
	$errormessages		=	$AdminDAO->getrows('system_errors',"*","pkerrorid	=	'$id'");
	$errormessage		=	$errormessages[0];
	$errormsg			=	$errormessage['errormsg'];
	//$errormsgother			=	$errormessage['errormsgother'];
	
	$errortype			=	$errormessage['errortype'];
}



?>
<style>
.radiobtnmargin
{
	margin-top:-25px !important;	
}
</style>
<?php /*?><script src="js/jquery.form.js"></script>
<div id="loaditemscript"> </div><?php */?>

<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            <?php 
			if($id=='-1')
			{echo "Add Error Message";}
			else
			{echo "Edit Error Message >> $errormsg";}
			?>	
        </div>
        <div class="panel-body">
            <form  name="errormessafeform" id="errormessafeform" class="form form-horizontal">

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>

<input type="hidden" name="issuperadmin" id="issuperadmin" value="<?php echo $issuperadmin; ?>" />
<div class="form-group">
	<label class="col-sm-2 control-label"><?php //echo $languagelabels['errormsg'];?>Error Message <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8">
    	<input type="text" placeholder="<?php //echo $languagelabels['errormsg'];?>Error Message" class="form-control m-b"  name="errormsg" id="errormsg" value="<?php echo $errormsg; ?>">
    </div>
</div>

<?php /*?><div class="form-group">
	<label class="col-sm-2 control-label"><?php //echo $languagelabels['errormsg'];?>Error Message(G) <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8">
    	<input type="text" placeholder="<?php //echo $languagelabels['errormsg'];?>Error Message" class="form-control m-b"  name="errormsgother" id="errormsgother" value="<?php echo $errormsgother; ?>">
    </div>
</div><?php */?>

<div class="form-group">
	<label class="col-sm-2 control-label">Error Type <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8" id="cities">
		<select name="errortype" id="errortype" class="form-control m-b">
        	<option value="">Select Error Type</option>
            <option value="1" <?php if($errortype == '1'){echo "selected=selected";}?>>Info</option>
            <option value="2" <?php if($errortype == '2'){echo "selected=selected";}?>>Success</option>
            <option value="3" <?php if($errortype == '3'){echo "selected=selected";}?>>Warning</option>
            <option value="4" <?php if($errortype == '4'){echo "selected=selected";}?>>Error</option>
        </select>
    </div>
</div>


<style>
.fieldaligncenter
{
	text-align:center !important;	
}
</style>

<input type="hidden" name="passhidden" id="passhidden" value="<?php echo base64_encode($password); ?>" />
<input type="hidden" name="id" value ="<?php echo $id;?>" />
<input type="hidden" name="addressbookid" value="<?php echo $addressbookid;?>" />
                  
<?php
	buttons('errormessageaction.php','errormessafeform','maindiv','main.php?pkscreenid=10',0)
?>          
            </form>
        </div>
    </div>
</div>
</div>
<script language="javascript" type="text/javascript">
$(document).ready(function()
{
	showdaysfield("<?php echo $isfreetrial; ?>");	
})
function showdaysfield(id)
{
	if(id == 1)
	{
		$("#showdaysfield").show();	
	}
	else
	{
		$("#showdaysfield").hide();
	}
}

function defaultaddonchecked(addonid)
{
	$("#manytimefkaddonids"+addonid).prop("checked", true);	
}
function selecteddefaultaddon(did,aid)
{
	var ischecked	=	document.getElementById("manytimefkaddonids"+aid).checked;
	if(ischecked == true)
	{
		$('#defaultaddon').val(did);
	}
	else
	{
		$('#defaultaddon').val("");
	}
}
/*function fetchaddondetails(pacakgeadons)
{
	$("#loadaddons").load("loadpackageaddons.php?addonis="+pacakgeadons);
}*/


	/*document.getElementById('fname').focus();
      
    loading('Loading Form...');*/
//    $(".chzn-select").chosen();
  //  $(".chzn-select-deselect").chosen({allow_single_deselect: true});
</script>
