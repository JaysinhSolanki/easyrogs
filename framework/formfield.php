<?php 
require_once("common.php");
@session_start(); 
$_SESSION['fieldid'] = $_SESSION['fieldid']+1;
$uniqueid=$_SESSION['fieldid'];
$inputtypes	=	$AdminDAO->getrows('system_formfieldtype',"*",'status=0  ORDER BY formfieldtype');
?>
<div class="hpanel" id="sectionheader<?php echo $uniqueid;?>">
        <div class="panel-heading hbuilt" style="background-color:#F7F9FA !important;" >
        	<input type="hidden" class="allfields"   id="field_uid<?php echo $uniqueid;?>" name="field_uid" value="<?php echo $uniqueid;?>" />
        	<label  id="fieldtitle<?php echo $uniqueid;?>">Field</label>
            <div class="panel-tools" >
                <a class="showhide" onclick="collapsepanel(<?php echo $uniqueid;?>,'section');"><i class="fa fa-chevron-up" id="sectionupdown<?php echo $uniqueid;?>"></i></a>
            </div>
            <div class="panel-tools" >
                <a href='#' onclick="deletefield('<?php echo $uniqueid;?>')" title="Delete field" style="font-size:20px; text-align:right;" class='col-sm-1 col-sm-offset-11 remove_field_body'>&times;</a>
            </div>
            <div class="panel-tools" >
                <select   style="max-width:185px;" onchange="loadfieldtype(this.value,'<?php echo $uniqueid;?>')" name="fieldtype[<?php echo $uniqueid; ?>]" id="fieldtype<?php echo $uniqueid;?>" value="">
                    <option selected="selected" value=""> Select Input Type </option>
                    <?php
                    foreach($inputtypes as $inputtype)
                    {
                    ?>
                    <option <?php if($_GET['fkformfieldtypeid']== $inputtype['pkformfieldtypeid']) echo 'selected="selected"'; ?> value="<?php echo $inputtype['pkformfieldtypeid'];?>"><?php echo $inputtype['formfieldlabel'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="panel-body" id="sectionbody<?php echo $uniqueid;?>">
        <div class="" id="loadfield<?php echo $uniqueid;?>"></div>
        </div>
        <div class="panel-footer" id="sectionfooter<?php echo $uniqueid;?>">
            This is footer for <span  id="fieldfooter<?php echo $uniqueid;?>">field</span>
        </div>
    </div>




























<?php /*?><div class="panel-body" id="panel-body<?php echo $uniqueid;?>"><a href='#' onclick="deletefield('<?php echo $uniqueid;?>')" title="Delete field" style="font-size:20px; text-align:right;" class='col-sm-1 col-sm-offset-11 remove_field_body'>&times;</a>
	<div class="panel-heading hbuilt" style="background-color:#f8f8f8">
                        		<div class="row">
                                    <label class="col-sm-10 control-label" id="fieldtitle<?php echo $uniqueid;?>">Field</label>
                                    <div class="col-sm-2" style="text-align:right">
                                    <select class="form-control m-b"  style="margin-bottom:-10px;max-width:161px;" onchange="loadfieldtype(this.value,'<?php echo $uniqueid;?>')" name="fieldtype[<?php echo $uniqueid; ?>]" id="fieldtype<?php echo $uniqueid;?>" value="">
                                        <option selected="selected" value=""> Select Input Type </option>
                                        <?php
                                        foreach($inputtypes as $inputtype)
                                        {
                                        ?>
                                        <option <?php if($_GET['fkformfieldtypeid']== $inputtype['pkformfieldtypeid']) echo 'selected="selected"'; ?> value="<?php echo $inputtype['pkformfieldtypeid'];?>"><?php echo $inputtype['formfieldtype'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    </div>
                                    </div>
                        
    </div>
    <div class="panel-body" id="loadfield<?php echo $uniqueid;?>"></div>
</div><?php */?>