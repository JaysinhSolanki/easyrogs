<?php
@session_start();
include_once("../includes/classes/adminsecurity.php");

$id					=	1;//$_GET['id'];
if($id)
{
	$generalsettings	=	$AdminDAO->getrows('system_setting',"*","pksettingid = 1");
	//echo "<pre>";
	//print_r($generalsettings);
	$generalsetting			=	$generalsettings[0];
	//$fkcurrencyid			=	$generalsetting['fkcurrencyid'];
	$fromemail				=	$generalsetting['fromemail'];
	$deliveryprice			=	$generalsetting['deliveryprice'];
	$clearencemainimage		=	$generalsetting['clearencemainimage'];
	$bulkdealimage			=	$generalsetting['bulkdealimage'];
	//$sendgriduser			=	$generalsetting['sendgriduser'];
	//$sendgridpassword		=	$generalsetting['sendgridpassword'];
}
/************************************* Default Currency************************************/
//$currencies		=	$AdminDAO->getrows('system_currency',"*");
/****************************************************************************/
?>
<link href="css/jquery.filer.css" type="text/css" rel="stylesheet" />
<link href="css/themes/jquery.filer-dragdropbox-theme.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery.filer.min.js?v=1.0.5"></script>
<script type="text/javascript" src="js/custom.js?v=1.0.5"></script>
<script src="js/jquery.form.js"></script>
<title>oldbrandfile</title>

<div id="loaditemscript"> </div>
<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            <?php 
			echo "Edit General Settings";
			?>	
        </div>
<div class="panel-body">        
<form  name="generalsettingform" id="generalsettingform" class="form form-horizontal" enctype="multipart/form-data">
<?php /*?><div class="form-group" >
        <label class="col-sm-2 control-label">Currency <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
        <div class="col-sm-4">
            <select name="fkcurrencyid" id="fkcurrencyid" class="form-control m-b">
                <option value="">Select Currency</option>
                <?php
                foreach($currencies as $currency)
                {
                    $pkcurrencyid	=	$currency['pkcurrencyid'];
                    $currency		=	$currency['currency'];
                ?>
                    <option value="<?php echo $pkcurrencyid;?>" <?php if($fkcurrencyid == $pkcurrencyid){echo "selected=selected";}?>><?php echo $currency;?></option>
                <?php
                }
                ?>
            </select>
        </div>
</div>
<?php */?>
<div class="form-group">
	<label class="col-sm-2 control-label">From Email <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-4">
    	<input type="text" placeholder="From Email" class="form-control m-b"  name="fromemail" id="fromemail" value="<?php echo $fromemail; ?>">
    </div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Delivery Price <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-4">
    	<input type="text" placeholder="Delivery Price" class="form-control m-b"  name="deliveryprice" id="deliveryprice" value="<?php echo $deliveryprice; ?>">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Clearence Sale Main Image <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
    <div class="col-sm-5">
       <input type="file" class="form-control" name="files[]" id="filer_input" multiple="multiple">
       <input type="hidden" class="form-control" name="clearencemainimage" id="clearencemainimage" value="<?php echo $clearencemainimage; ?>">
       </div>
        <div class="col-sm-4">
    <?php 
    if($id)
    {
        ?>
           <img src="../../images/clearence/<?php echo $clearencemainimage; ?>" alt="" width="100%"> 
         <?php
    }
    ?>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">Bulk Deal Sale Main Image <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
    <div class="col-sm-5">
      <input type="file" name="fileToUpload" id="fileToUpload">
       <input type="hidden" class="form-control" name="bulkdealimage" id="bulkdealimage" value="<?php echo $bulkdealimage; ?>">
       </div>
        <div class="col-sm-4">
    <?php 
    if($id)
    {
        ?>
           <img src="../../images/bulkdeal/<?php echo $bulkdealimage; ?>" alt="" width="100%"> 
         <?php
    }
    ?>
    </div>
</div>

<?php /*?><hr />
<h4><?php echo $languagelabels['sendgridcredentials'];?></h4>
<hr />
<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['sendgriduser'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-4">
    	<input type="text" placeholder="<?php echo $languagelabels['sendgriduser'];?>" class="form-control m-b"  name="sendgriduser" id="sendgriduser" value="<?php echo $sendgriduser; ?>">
    </div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['sendgridpassword'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-4">
    	<input type="text" placeholder="<?php echo $languagelabels['sendgridpassword'];?>" class="form-control m-b"  name="sendgridpassword" id="sendgridpassword" value="<?php echo $sendgridpassword; ?>">
    </div>
</div>
<?php */?>
<input type="hidden" name="id" value ="<?php echo $id; ?>" />

<?php
	buttonsave('generalsettingaction.php','generalsettingform','maindiv','main.php?pkscreenid=111',0)
?>          
            </form>
        </div>
    </div>
</div>
</div>
