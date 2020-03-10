<?php 
require_once("common.php");
@session_start(); 
$_SESSION['fieldid']		=	$_SESSION['fieldid']+1;
$_SESSION['fieldoptions']	=	$_SESSION['fieldoptions']+1;
$uniqueid					=	$_SESSION['fieldid'];
$pkformfieldid				=	$_GET['pkformfieldid'];
$formfields					=	$AdminDAO->getrows("system_formfield","*","pkformfieldid = '$pkformfieldid'");
//dump($formfields);
$formfield					=	$formfields[0];
$type						=	$formfield['fkformfieldtypeid'];
$inputtypes					=	$AdminDAO->getrows('system_formfieldtype',"*",'status=0  ORDER BY formfieldtype');
$listtypes					=	$AdminDAO->getrows("system_listtype","*");
//dump($inputtypes); 
?>
 
<span id="panel-body<?php echo $uniqueid;?>">
    	<div class="hpanel" id="sectionheader<?php echo $formfield['pkformfieldid'];?>">
            <div class="panel-heading hbuilt" style="background-color:#F7F9FA !important;" >
                <label  id="fieldtitle<?php echo $formfield['pkformfieldid'];?>">Field</label>
                <input type="hidden" class="allfields"   id="field_uid<?php echo $formfield['pkformfieldid'];?>" name="field_uid" value="<?php echo $formfield['pkformfieldid'];?>" />
                <div class="panel-tools" >
                    <a class="showhide" onclick="collapsepanel(<?php echo $formfield['pkformfieldid'];?>,'section');" ><i class="fa fa-chevron-up" id="sectionupdown<?php echo $formfield['pkformfieldid'];?>"></i></a>
                </div>
                <div class="panel-tools" >
                    <a href="javascript:;" onclick="deletefieldedit('<?php echo $uniqueid;?>','<?php echo $formfield['pkformfieldid'];?>')" title="Delete field" style="font-size:20px; text-align:right;" class='col-sm-1 col-sm-offset-11 remove_field_body'>&times;</a>
                </div>
                <div class="panel-tools" >
                    <select class="select2"  style="max-width:185px;" onchange="loadfieldtype(this.value,'<?php echo $uniqueid;?>')" name="fieldtype[<?php echo $uniqueid; ?>]" <?php //if($formfield['pkformfieldid']>0){  echo 'disabled="disabled"'; }?>   id="fieldtype<?php echo $uniqueid;?>">
                    <option selected="selected" value=""> Select Input Type </option>
                    <?php
                    foreach($inputtypes as $inputtype)
                    {
                    ?>
                        <option <?php if($formfield['pkformfieldid']>0){  /*echo 'disabled="disabled"'; */}?> <?php if($formfield['fkformfieldtypeid']== $inputtype['pkformfieldtypeid']) echo 'selected="selected"'; ?> value="<?php echo $inputtype['pkformfieldtypeid'];?>"><?php echo $inputtype['formfieldlabel'];?></option>
                    <?php
                    }
                    ?>
                    </select>
                </div>
            </div>
            <div class="panel-body" id="sectionbody<?php echo $formfield['pkformfieldid'];?>">
                <div class="form-group">
                     <label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label><input type="hidden"  class="form-control m-b"  name="pkformfieldid[<?php echo $uniqueid; ?>]" id="pkformfieldid<?php echo $uniqueid;?>" value="<?php echo $formfield['pkformfieldid'];?>" /><input type="hidden"  class="form-control m-b"  name="fieldtype[<?php echo $uniqueid; ?>]" id="fieldtype<?php echo $uniqueid;?>" value="<?php echo $formfield['fkformfieldtypeid'];?>" />
                    <div class="col-sm-4">
                        <input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="<?php echo $formfield['label'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="<?php echo $formfield['fieldname'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="<?php echo $formfield['fieldid'];?>" />
                    </div>
                </div>
                <div class="form-group" id="placeholderid">
                    <label class="col-sm-2 control-label">Display Order</label>
                    <div class="col-sm-4">
                        <input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="<?php echo $formfield['displayorder'];?>" />
                    </div>
                </div>
                <?php
				if($type == 8 || $type==22)
				{
					?>
						
						<div class="form-group"  id="valuefield">
						<label class='col-sm-2 control-label'>Value</label>
						<div class='col-sm-4'>
						<input type='text' placeholder='value' class='form-control m-b'  name='fieldvalue[<?php echo $uniqueid; ?>]' id='fieldvalue<?php echo $uniqueid;?>' value='' />
						</div>  
						</div>
						<div class="form-group" id="placeholderid">
						<label class="col-sm-2 control-label">Display Order</label>
						<div class="col-sm-4">
						<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="<?php echo $formfield['displayorder'];?>" />
						</div>
						</div>
						<div class="form-group ">
						<label class="col-sm-2 control-label">Required<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
						<div class="col-sm-4">
						<label class="radio-inline">
						<input type="radio" value="1" checked="checked" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">Yes
						</label>
						<label class="radio-inline">
						<input type="radio" value="0" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">No
						</label>
						</div>
						</div>
						<div class="form-group" id="placeholderid">
						<label class="col-sm-2 control-label">Status</label>
						<div class="col-sm-4">
						<select placeholder="Enctype" class="form-control m-b"  name="status[<?php echo $uniqueid; ?>]" id="status<?php echo $uniqueid;?>" value="">
						<option value="1">Active</option>
						<option value="2">Inactive</option>
						</select>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Classes</label>
						<div class="col-sm-4">
						<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>"  value="<?php echo $formfield['cssclass'];?>" />
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Instruction</label>
						<div class="col-sm-4">
						<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""><?php echo $formfield['instructions'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Style</label>
						<div class="col-sm-4">
						<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""><?php echo $formfield['style'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Javascript</label>
						<div class="col-sm-4">
						<textarea class="form-control m-b"   placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""><?php echo $formfield['fieldjavascript'];?></textarea>
						</div>
						</div> 
                        <?php 
						$filesetting	=	$AdminDAO->getrows('tblfilesetting',"*","fkformfieldid= '$pkformfieldid'");
						$filetypes		=	$AdminDAO->getrows('tblfiletype ',"*",'status=0  ORDER BY filetypename');
						?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">File Size<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="File Size" class="form-control m-b fieldlable" name="filesize[<?php echo $uniqueid; ?>]" id="filesize<?php echo $uniqueid;?>" value="<?php echo $filesetting[0]['filesize']?>" />
                            </div>
                        </div>
                        <?php 
                        if($type == 22) 
                        {
                            ?>
                            <input type="hidden" name="uploadMultiple[<?php echo $uniqueid; ?>]" id="uploadMultiple<?php echo $uniqueid; ?>" value="1"/>
                            <div class="form-group " >
                                <label class="col-sm-2 control-label">Parallel Uploads<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Parallel uploads" class="form-control m-b fieldlable" name="paralleluploads[<?php echo $uniqueid; ?>]" id="paralleluploads<?php echo $uniqueid;?>" value="<?php echo $filesetting[0]['paralleluploads']?>" />
                                </div>
                            </div>
                            <div class="form-group" >
                                <label class="col-sm-2 control-label">maxFiles<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="maxFiles" class="form-control m-b fieldlable" name="maxFiles[<?php echo $uniqueid; ?>]" id="maxFiles<?php echo $uniqueid;?>" value="<?php echo $filesetting[0]['maxFiles']?>" />
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">File Type<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                            <div class="col-sm-4">
                                <select class="form-control m-b select2"  name="filetype[<?php echo $uniqueid; ?>][]" id="filetype<?php echo $uniqueid;?>"  multiple="multiple">
                                        <?php
                                        foreach($filetypes as $filetype)
                                        {
                                            ?><option <?php if(in_array($filetype['pkfiletypeid'],explode(',',$filesetting[0]['filetype']))) echo 'selected="selected"';?>  value="<?php echo $filetype['pkfiletypeid'];?>"><?php echo $filetype['filetypename'];?></option><?php
                                        }
                                        ?>
                                </select>
                            </div>
                        </div>

					<?php
				}
				else if($type == 27 || $type == 29)
				{ 
					?>
						<div class="form-group" id="placeholderid">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-4">
								<select placeholder="Enctype" class="form-control m-b"  name="status[<?php echo $uniqueid; ?>]" id="status<?php echo $uniqueid;?>" value="">
										<option value="1">Active</option>
										<option value="2">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Classes</label>
							<div class="col-sm-4">
								<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="<?php echo $formfield['cssclass'];?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Style</label>
							<div class="col-sm-4">
								<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""><?php echo $formfield['style'];?></textarea>
							</div>
						</div>
					<?php
				}
				else if($type == 28)
				{ 
					?>
						<div class="form-group" id="placeholderid">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-4">
								<select placeholder="Enctype" class="form-control m-b"  name="status[<?php echo $uniqueid; ?>]" id="status<?php echo $uniqueid;?>" value="">
										<option value="1">Active</option>
										<option value="2">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Classes</label>
							<div class="col-sm-4">
								<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="<?php echo $formfield['cssclass'];?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Style</label>
							<div class="col-sm-4">
								<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""><?php echo $formfield['style'];?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Javascript</label>
							<div class="col-sm-4">
								<textarea class="form-control m-b"  placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""><?php echo $formfield['fieldjavascript'];?></textarea>
							</div>
						</div>   
					<?php
				}
				else if($type == 4 || $type == 5 || $type == 6)
				{
					?>
						<div class="form-group ">
							<label class="col-sm-2 control-label">Required<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
							<div class="col-sm-4">
								<label class="radio-inline">
									<input type="radio" value="1" checked="checked" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">Yes
								</label>
								<label class="radio-inline">
									<input type="radio" value="0" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">No
								</label>
							</div>
						</div>
						<div class="form-group" id="placeholderid">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-4">
								<select placeholder="Enctype" class="form-control m-b"  name="status[<?php echo $uniqueid; ?>]" id="status<?php echo $uniqueid;?>" value="">
										<option value="1">Active</option>
										<option value="2">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Classes</label>
							<div class="col-sm-4">
								<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>"  value="<?php echo $formfield['cssclass'];?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Instruction</label>
							<div class="col-sm-4">
								<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""><?php echo $formfield['instructions'];?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Style</label>
							<div class="col-sm-4">
								<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""><?php echo $formfield['style'];?></textarea>
							</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Javascript</label>
						<div class="col-sm-4">
						<textarea class="form-control m-b"   placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""><?php echo $formfield['fieldjavascript'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-3 col-sm-offset-2 control-label">Label</label>
						<label class="col-sm-7 control-label" style="text-align:left;">Value</label>
						</div>
						<div class="optionsdiv" id="optionsdiv<?php echo $uniqueid; ?>">
						<?php 
						$formfieldoptions			=	$AdminDAO->getrows("system_formfieldoption","*","fkformfieldid = '$pkformfieldid'");
						//dump($formfieldoptions);
						foreach($formfieldoptions as $formfieldoption)
						{
							$fieldoptions=$_SESSION['fieldoptions'];
							$_SESSION['fieldoptions']=$fieldoptions+1;
							$fieldoptions=$_SESSION['fieldoptions'];
						?>
							<div class="" id="deleteoptions<?php echo $uniqueid.$fieldoptions; ?>">
									<div class=" col-sm-offset-2 col-sm-3">
										<input type="text" placeholder="Label" class="form-control m-b fieldlable"   name="fieldlabeloptions[<?php echo $uniqueid; ?>][]" id="fieldlabeloptions<?php echo $uniqueid.$fieldoptions;?>" value="<?php echo $formfieldoption['fieldoptionlabel'];?>" />
									</div>
									<div class="col-sm-2">
										<input type="text" placeholder="Value" class="form-control m-b fieldlable"  name="fieldvalueoptions[<?php echo $uniqueid; ?>][]" id="fieldvalueoptions<?php echo $uniqueid.$fieldoptions;?>" value="<?php echo $formfieldoption['fieldoptionvalue'];?>" />
									</div>
									<a href="javascript:;" onclick="deletefieldoption(<?php echo $uniqueid.$fieldoptions; ?>)" title="Delete field" style="font-size:20px; text-align:left;" class="col-sm-5   fieldoptions">×</a>
									<span class="col-sm-12"></span>
							</div>
						<?php
						}
						?>
						</div>
						<div class="form-group">
							<div class="col-sm-6" style="text-align:right; vertical-align:bottom">
									   <a onclick="loadmorefieldoption(<?php echo $uniqueid; ?>)"><span class="glyphicon glyphicon-plus"></span> load field option</a>
							</div>
						</div> 
					<?php
				}
				else if($type == 33 || $type == 34 || $type == 35)
				{
					?>
						<div class="form-group ">
						<label class="col-sm-2 control-label">Required<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
						<div class="col-sm-4">
						<label class="radio-inline">
						<input type="radio" value="1" checked="checked" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">Yes
						</label>
						<label class="radio-inline">
						<input type="radio" value="0" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">No
						</label>
						</div>
						</div>
						<div class="form-group" id="placeholderid">
						<label class="col-sm-2 control-label">Status</label>
						<div class="col-sm-4">
						<select placeholder="Enctype" class="form-control m-b"  name="status[<?php echo $uniqueid; ?>]" id="status<?php echo $uniqueid;?>" value="">
						<option value="1">Active</option>
						<option value="2">Inactive</option>
						</select>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Classes</label>
						<div class="col-sm-4">
						<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>"  value="<?php echo $formfield['cssclass'];?>" />
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Instruction</label>
						<div class="col-sm-4">
						<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""><?php echo $formfield['instructions'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Style</label>
						<div class="col-sm-4">
						<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""><?php echo $formfield['style'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Javascript</label>
						<div class="col-sm-4">
						<textarea class="form-control m-b"   placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""><?php echo $formfield['fieldjavascript'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Query</label>
						<div class="col-sm-10">
						<textarea class="form-control m-b"  placeholder="Sql Query"  name="sqlquery[<?php echo $uniqueid; ?>]" id="sqlquery<?php echo $uniqueid;?>" value=""><?php echo $formfield['sqlquery'];?></textarea>
						</div>
						</div> 
						<div class="form-group">
						<label class="col-sm-2 control-label">Field value<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
						<div class="col-sm-4">
						<input type="text" placeholder="Field value" class="form-control m-b fieldlable"   name="queryfieldvalue[<?php echo $uniqueid; ?>]" id="queryfieldvalue<?php echo $uniqueid;?>" value="<?php echo $formfield['queryfieldvalue'];?>" />
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Field label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
						<div class="col-sm-4">
						<input type="text" placeholder="Field label" class="form-control m-b fieldlable"   name="queryfieldlabel[<?php echo $uniqueid; ?>]" id="queryfieldlabel<?php echo $uniqueid;?>" value="<?php echo $formfield['queryfieldlabel'];?>" />
						</div>
						</div>   
					<?php
				}
				else
				{
					?>
						
						<div class="form-group"  id="valuefield">
						<label class='col-sm-2 control-label'>Value</label>
						<div class='col-sm-4'>
						<input type='text' placeholder='value' class='form-control m-b'  name='fieldvalue[<?php echo $uniqueid; ?>]' id='fieldvalue<?php echo $uniqueid;?>' value='' />
						</div>  
						</div>
						<div class="form-group" id="placeholderid">
						<label class="col-sm-2 control-label">Display Order</label>
						<div class="col-sm-4">
						<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="<?php echo $formfield['displayorder'];?>" />
						</div>
						</div>
						<div class="form-group ">
						<label class="col-sm-2 control-label">Required<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
						<div class="col-sm-4">
						<label class="radio-inline">
						<input type="radio" value="1" checked="checked" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">Yes
						</label>
						<label class="radio-inline">
						<input type="radio" value="0" name="fieldisrequired[<?php echo $uniqueid; ?>]" id="">No
						</label>
						</div>
						</div>
						<div class="form-group" id="placeholderid">
						<label class="col-sm-2 control-label">Status</label>
						<div class="col-sm-4">
						<select placeholder="Enctype" class="form-control m-b"  name="status[<?php echo $uniqueid; ?>]" id="status<?php echo $uniqueid;?>" value="">
						<option value="1">Active</option>
						<option value="2">Inactive</option>
						</select>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Classes</label>
						<div class="col-sm-4">
						<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>"  value="<?php echo $formfield['cssclass'];?>" />
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Instruction</label>
						<div class="col-sm-4">
						<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""><?php echo $formfield['instructions'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Style</label>
						<div class="col-sm-4">
						<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""><?php echo $formfield['style'];?></textarea>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">Javascript</label>
						<div class="col-sm-4">
						<textarea class="form-control m-b"   placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""><?php echo $formfield['fieldjavascript'];?></textarea>
						</div>
						</div> 
					<?php
				}
				?>
                <?php
				if($type == 37)
				{
					?>
					<div class="form-group">
						<label class="col-sm-2 control-label">Type</label>
						<div class="col-sm-4">
							<select placeholder="Enctype" class="form-control m-b"  name="recordFrom[<?php echo $uniqueid; ?>]" id="recordFrom<?php echo $uniqueid;?>" value="" onchange="LoadTypes(this.value)">
									<option <?php echo $formfield['recordFrom']==1?'selected':'selected';?>  value="1">Query</option>
									<option  <?php echo $formfield['recordFrom']==1?'selected':'selected';?>  value="2">List</option>
							</select>
						</div>
					</div>
					<div class="form-group" id="typequery" <?php echo $formfield['recordFrom']==2?'style="display:none"':'';?> >
						<label class="col-sm-2 control-label">Query</label>
						<div class="col-sm-4">
							<textarea class="form-control m-b"  placeholder="Sql Query"  name="sqlquery[<?php echo $uniqueid; ?>]" id="sqlquery<?php echo $uniqueid;?>" value=""><?php echo $formfield['sqlquery'];?></textarea>
						</div>
					</div> 
					<div class="form-group" id="typelist" <?php echo $formfield['recordFrom']==1?'style="display:none"':'';?> >
						<label class="col-sm-2 control-label">List Type</label>
						<div class="col-sm-4">
							<select placeholder="Enctype" class="form-control m-b"  name="fklisttypeid[<?php echo $uniqueid; ?>]" id="liststype<?php echo $uniqueid;?>" value="">
							<?php
							foreach($listtypes as $listtype)
							{
							?>
							<option <?php echo $formfield['fklisttypeid']==$listtype['pklisttypeid']?'selected':'selected';?> value="<?php echo $listtype['pklisttypeid']; ?>"><?php echo $listtype['listtypename']; ?></option>
							<?php
							}
							?>
							</select>
						</div>
					</div>
					<?php
				}
				?>
            </div>
            <div class="panel-footer" id="sectionfooter<?php echo $formfield['pkformfieldid'];?>">
                 End of <span  id="fieldfooter<?php echo $uniqueid;?>"><?php if($formfield['pkformfieldid']>0){  echo $formfield['label']; }else{ echo "field";} ?></span>
            </div>
    	</div>
    </span>
<script>
$(function(){
	$("#fieldtitle<?php echo $uniqueid;?>").html($("#fieldlabel<?php echo $uniqueid;?>").val());	
	collapsepanel(<?php echo $formfield['pkformfieldid'];?>,'section');
});
</script>