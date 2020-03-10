<?php
require_once("common.php"); 
//@session_start();
$type			=	$_GET['type']; 
$uniqueid		=	$_SESSION['fieldid'];
$fieldoptions	=	$_SESSION['fieldoptions'];
$listtypes		=	$AdminDAO->getrows("system_listtype","*");
if($type == 8 || $type == 22)
{
	require_once("common.php");
	$filetypes	=	$AdminDAO->getrows('tblfiletype ',"*",'status=0  ORDER BY filetypename');
	?>
    <div class="form-group">
		<label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group"  id="valuefield">
	  <label class='col-sm-2 control-label'>Value</label>
		<div class='col-sm-4'>
			<input type='text' placeholder='value' class='form-control m-b'  name='fieldvalue[<?php echo $uniqueid; ?>]' id='fieldvalue<?php echo $uniqueid;?>' value='' />
		</div>  
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Placeholder</label>
		<div class="col-sm-4">
			<input  placeholder="Placeholder " class="form-control m-b"  name="fieldplaceholder[<?php echo $uniqueid; ?>]" id="fieldplaceholder<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Display Order</label>
		<div class="col-sm-4">
			<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="" />
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
			<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Instruction</label>
		<div class="col-sm-4">
			<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Style</label>
		<div class="col-sm-4">
			<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Javascript</label>
		<div class="col-sm-4">
			<textarea class="form-control m-b"  placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
    <div class="form-group">
		<label class="col-sm-2 control-label">File Size<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="File Size" class="form-control m-b fieldlable" name="filesize[<?php echo $uniqueid; ?>]" id="filesize<?php echo $uniqueid;?>" value="" />
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
                <input type="text" placeholder="Parallel uploads" class="form-control m-b fieldlable" name="paralleluploads[<?php echo $uniqueid; ?>]" id="paralleluploads<?php echo $uniqueid;?>" value="" />
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label">maxFiles<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
            <div class="col-sm-4">
                <input type="text" placeholder="maxFiles" class="form-control m-b fieldlable" name="maxFiles[<?php echo $uniqueid; ?>]" id="maxFiles<?php echo $uniqueid;?>" />
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
						?><option value="<?php echo $filetype['pkfiletypeid'];?>"><?php echo $filetype['filetypename'];?></option><?php
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
	<div class="form-group">
		<label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Display Order</label>
		<div class="col-sm-4">
			<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="" />
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
			<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Style</label>
		<div class="col-sm-4">
			<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<?php
}
else if($type == 28)
{ 
	?>
	<div class="form-group">
		<label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Display Order</label>
		<div class="col-sm-4">
			<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="" />
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
			<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Style</label>
		<div class="col-sm-4">
			<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Javascript</label>
		<div class="col-sm-4">
			<textarea class="form-control m-b"  placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>   
	<?php
}
else if($type == 4 || $type == 5 || $type == 6)
{
	?>
    <div class="form-group">
		<label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group" id="">
		<label class="col-sm-2 control-label">Display Order</label>
		<div class="col-sm-4">
			<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="" />
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
			<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="">
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Instruction</label>
		<div class="col-sm-4">
			<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Style</label>
		<div class="col-sm-4">
			<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Javascript</label>
		<div class="col-sm-4">
			<textarea class="form-control m-b"  placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
    <div class="form-group">
    	<label class="col-sm-3 col-sm-offset-2 control-label">Label</label>
        <label class="col-sm-7 control-label" style="text-align:left;">Value</label>
    </div>
    <div class="optionsdiv" id="optionsdiv<?php echo $uniqueid; ?>">
        <div class="" id="deleteoptions<?php echo $uniqueid.$fieldoptions; ?>">
                <div class=" col-sm-offset-2 col-sm-3">
                    <input type="text" placeholder="Label" class="form-control m-b fieldlable"   name="fieldlabeloptions[<?php echo $uniqueid; ?>][]" id="fieldlabeloptions<?php echo $uniqueid.$fieldoptions;?>" value="" />
                </div>
                <div class="col-sm-2">
                    <input type="text" placeholder="Value" class="form-control m-b fieldlable"  name="fieldvalueoptions[<?php echo $uniqueid; ?>][]" id="fieldvalueoptions<?php echo $uniqueid.$fieldoptions;?>" value="" />
                </div>
                <a href="javascript:;" onclick="deletefieldoption(<?php echo $uniqueid.$fieldoptions; ?>)" title="Delete field" style="font-size:20px; text-align:left;" class="col-sm-5   fieldoptions">×</a>
                <span class="col-sm-12"></span>
        </div>
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
    <div class="form-group">
		<label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Display Order</label>
		<div class="col-sm-4">
			<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="" />
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
			<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="">
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Instruction</label>
		<div class="col-sm-4">
			<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Style</label>
		<div class="col-sm-4">
			<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Javascript</label>
		<div class="col-sm-4">
			<textarea class="form-control m-b"  placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
    <div class="form-group">
		<label class="col-sm-2 control-label">Query</label>
		<div class="col-sm-10">
			<textarea class="form-control m-b"  placeholder="Sql Query"  name="sqlquery[<?php echo $uniqueid; ?>]" id="sqlquery<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div> 
    <div class="form-group">
		<label class="col-sm-2 control-label">Field value<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Field value" class="form-control m-b fieldlable"   name="queryfieldvalue[<?php echo $uniqueid; ?>]" id="queryfieldvalue<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
    <div class="form-group">
		<label class="col-sm-2 control-label">Field label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Field label" class="form-control m-b fieldlable"   name="queryfieldlabel[<?php echo $uniqueid; ?>]" id="queryfieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div> 
    <?php
}
else
{
	?>
    <div class="form-group">
		<label class="col-sm-2 control-label">Label<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Label" class="form-control m-b fieldlable" onkeyup="fieldtitlefucntion('<?php echo $uniqueid;?>',this.value)"  name="fieldlabel[<?php echo $uniqueid; ?>]" id="fieldlabel<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Name" class="form-control m-b"  name="fieldname[<?php echo $uniqueid; ?>]" id="fieldname<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Id<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
		<div class="col-sm-4">
			<input type="text" placeholder="Id" class="form-control m-b"  name="fieldid[<?php echo $uniqueid; ?>]" id="fieldid<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group"  id="valuefield">
	  <label class='col-sm-2 control-label'>Value</label>
		<div class='col-sm-4'>
			<input type='text' placeholder='value' class='form-control m-b'  name='fieldvalue[<?php echo $uniqueid; ?>]' id='fieldvalue<?php echo $uniqueid;?>' value='' />
		</div>  
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Placeholder</label>
		<div class="col-sm-4">
			<input  placeholder="Placeholder " class="form-control m-b"  name="fieldplaceholder[<?php echo $uniqueid; ?>]" id="fieldplaceholder<?php echo $uniqueid;?>" value="" />
		</div>
	</div>
	<div class="form-group" id="placeholderid">
		<label class="col-sm-2 control-label">Display Order</label>
		<div class="col-sm-4">
			<input  placeholder="Display Order " class="form-control m-b"  name="fielddisplayorder[<?php echo $uniqueid; ?>]" id="fielddisplayorder<?php echo $uniqueid;?>" value="" />
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
			<input type="text" class="form-control m-b" placeholder="Classes"   name="fieldclass[<?php echo $uniqueid; ?>]" id="fieldclass<?php echo $uniqueid;?>" value="">
		</div>
	</div>
	 <div class="form-group">
		<label class="col-sm-2 control-label">Instruction</label>
		<div class="col-sm-4">
			<textarea  placeholder="Instruction " class="form-control m-b"  name="fieldinstruction[<?php echo $uniqueid; ?>]" id="fieldinstruction<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Style</label>
		<div class="col-sm-4">
			<textarea  placeholder="Style " class="form-control m-b"  name="fieldstyle[<?php echo $uniqueid; ?>]" id="fieldstyle<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Javascript</label>
		<div class="col-sm-4">
			<textarea class="form-control m-b"  placeholder="Javascript"  name="fieldjs[<?php echo $uniqueid; ?>]" id="fieldjs<?php echo $uniqueid;?>" value=""></textarea>
		</div>
	</div> 
    <?php
	if($type == 37)
	{
		?>
		<div class="form-group">
			<label class="col-sm-2 control-label">Type</label>
			<div class="col-sm-4">
				<select placeholder="Enctype" class="form-control m-b"  name="recordFrom[<?php echo $uniqueid; ?>]" id="recordFrom<?php echo $uniqueid;?>" value="" onchange="LoadTypes(this.value)">
						<option value="1">Query</option>
						<option value="2">List</option>
				</select>
			</div>
		</div>
		<div class="form-group" id="typequery">
			<label class="col-sm-2 control-label">Query</label>
			<div class="col-sm-4">
				<textarea class="form-control m-b"  placeholder="Sql Query"  name="sqlquery[<?php echo $uniqueid; ?>]" id="sqlquery<?php echo $uniqueid;?>" value=""></textarea>
			</div>
		</div> 
		<div class="form-group" id="typelist" style="display:none">
			<label class="col-sm-2 control-label">List Type</label>
			<div class="col-sm-4">
				<select placeholder="Enctype" class="form-control m-b"  name="fklisttypeid[<?php echo $uniqueid; ?>]" id="liststype<?php echo $uniqueid;?>" value="">
				<?php
				foreach($listtypes as $listtype)
				{
				?>
				<option value="<?php echo $listtype['pklisttypeid']; ?>"><?php echo $listtype['listtypename']; ?></option>
				<?php
				}
				?>
				</select>
			</div>
		</div>
		<?php
	}
}
?>
<div class="form-group">
    <label class="col-sm-2 control-label">Show<span class='redstar' style='color:#F00' title='This field is compulsory'></span></label>
    <div class="col-sm-4">
    	<div class="col-sm-4">
			<label class="checkbox-inline">
				<input type="checkbox" name="showbydefault[<?php echo $uniqueid; ?>]" id="showbydefault<?php echo $uniqueid;?>" checked="checked" value="1"> 
			</label>
			
		</div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">is Depend On</label>
    <div class="col-sm-4">
        <select placeholder="Enctype" class="form-control m-b"  name="isdependent[<?php echo $uniqueid; ?>]" id="isdependent<?php echo $uniqueid;?>" value="" onchange="Dependson(this.value,'<?php echo $uniqueid;?>')">
                <option value="0">No</option>
                <option value="1">Yes</option>
        </select>
    </div>
</div>
<div class="form-group dependent_<?php echo $uniqueid;?>" style="display:none">
    <label class="col-sm-2 control-label">Depend on<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
    <div class="col-sm-4">
        <select class="form-control m-b fieldlable" name="dependentfield[<?php echo $uniqueid; ?>]" id="dependentfield<?php echo $uniqueid;?>" >
        </select>
    </div>
</div>
<div class="form-group dependent_<?php echo $uniqueid;?>" style="display:none">
    <label class="col-sm-2 control-label">Event<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
    <div class="col-sm-4">
        <input type="text" placeholder="Event" class="form-control m-b fieldlable" name="dependentfieldevent[<?php echo $uniqueid; ?>]" id="dependentfieldevent<?php echo $uniqueid;?>" value="" />
    </div>
</div>