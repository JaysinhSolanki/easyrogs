<?php
include("adminsecurity.php");
$sectionid = $_GET['id'];
if($sectionid!=-1)
{
	$sectiondata		=	$AdminDAO->getrows("system_section","pksectionid,sectionname,sectionnamehebrew,sectionicon,status,sortorder","pksectionid='$sectionid'");
	$sectionname		=	$sectiondata[0]['sectionname'];
	$sectionnamehebrew	=	$sectiondata[0]['sectionnamehebrew'];
	//$sectionicon		=	$sectiondata[0]['sectionicon'];
	$status				=	$sectiondata[0]['status'];
	$sortorder			=	$sectiondata[0]['sortorder'];
}
/****************************************************************************/
?>
<div id="sectiondiv">
	<div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <?php 
				 if($sectionid == '-1')
				{
					print"Add New Section";
				}
				else
				{
					print"Edit"; if($sectionid > 0){ echo $sectionname;}
				}
            	?>
            </div>
            <div class="panel-body">
            <form name="sectionform" id="sectionform" onSubmit="addform(); return false;" class="form form-horizontal" >
            <input type=hidden name="sectionid" value = <?php echo $sectionid?> />
    
            <div class="form-group">
                <label class="col-sm-2 control-label">Section Name (English) </label>
            <div class="col-sm-8">
                <input type="text" placeholder="Section Name (English)" class="form-control m-b"  name="sectionname" id="sectionname" value="<?php echo $sectionname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}">
            </div>
            </div>
            
            <?php /*?><div class="form-group">
                <label class="col-sm-2 control-label">Section Name (French) </label>
            <div class="col-sm-8">
                <input type="text" placeholder="Section Name (French)" class="form-control m-b"  name="sectionnamehebrew" id="sectionnamehebrew" value="<?php echo $sectionnamehebrew; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}">
            </div>
            </div><?php */?>
            
           <!-- <div class="form-group">
                <label class="col-sm-2 control-label">Section Icon </label>
            <div class="col-sm-8">
                <input type="text" placeholder="Section Icon" class="form-control m-b"  name="sectionicon" id="sectionicon" value="<?php //echo $sectionicon; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}">
            </div>
            </div>-->
            
            <div class="form-group">
                <label class="col-sm-2 control-label">Sort Order </label>
                <div class="col-sm-8">
                    <input type="text" placeholder="Sort Order" class="form-control m-b"  name="sortorder" id="sortorder" value="<?php echo $sortorder; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label">Status </label>
                <div class="col-sm-8">
                   <select name="status" id="status" class="form-control m-b">
                        <option value="1" <?php if($status==1){ echo "selected=\"selected\"";}?>>Active</option>
                        <option value="0" <?php if($status==0 && $sectionid!=-1){ echo "selected=\"selected\"";}?>>Inactive</option>
					</select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-10">
            	<?php
        		 buttons('insertsection.php','sectionform','maindiv','main.php?pkscreenid=3',0)
    			?>
                </div>
            </div>    
        </form>
    </div>
  </div>
	</div>
</div>
<script src="../js/jquery.form.js"></script>
<script language="javascript">
document.sectionform.sectionname.focus();
loading('Loading Form...');
</script>